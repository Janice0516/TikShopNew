<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'transaction_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'order_id',
        'withdrawal_id',
        'description',
        'notes',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    /**
     * 关联商家
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    /**
     * 关联订单
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 关联提现记录
     */
    public function withdrawal(): BelongsTo
    {
        return $this->belongsTo(Withdrawal::class);
    }

    /**
     * 获取交易类型文本
     */
    public function getTypeTextAttribute()
    {
        $typeMap = [
            'income' => '收入',
            'expense' => '支出',
            'withdrawal' => '提现',
            'refund' => '退款',
            'commission' => '佣金',
            'bonus' => '奖金',
        ];

        return $typeMap[$this->type] ?? '未知类型';
    }

    /**
     * 获取状态文本
     */
    public function getStatusTextAttribute()
    {
        $statusMap = [
            'pending' => '处理中',
            'completed' => '已完成',
            'failed' => '失败',
            'cancelled' => '已取消',
        ];

        return $statusMap[$this->status] ?? '未知状态';
    }

    /**
     * 按商家筛选
     */
    public function scopeForMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }

    /**
     * 按类型筛选
     */
    public function scopeByType($query, $type)
    {
        if (empty($type)) {
            return $query;
        }
        
        return $query->where('type', $type);
    }

    /**
     * 按日期范围筛选
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
        
        return $query;
    }

    /**
     * 生成交易ID
     */
    public static function generateTransactionId()
    {
        do {
            $transactionId = 'TXN-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));
        } while (self::where('transaction_id', $transactionId)->exists());
        
        return $transactionId;
    }

    /**
     * 创建财务记录
     */
    public static function createRecord($merchantId, $type, $amount, $description, $orderId = null, $withdrawalId = null)
    {
        // 获取当前余额
        $lastRecord = self::forMerchant($merchantId)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->first();
        
        $balanceBefore = $lastRecord ? $lastRecord->balance_after : 0;
        $balanceAfter = $balanceBefore + $amount;
        
        return self::create([
            'merchant_id' => $merchantId,
            'transaction_id' => self::generateTransactionId(),
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'order_id' => $orderId,
            'withdrawal_id' => $withdrawalId,
            'description' => $description,
            'status' => 'completed',
        ]);
    }

    /**
     * 获取商家余额
     */
    public static function getMerchantBalance($merchantId)
    {
        $lastRecord = self::forMerchant($merchantId)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->first();
        
        return $lastRecord ? $lastRecord->balance_after : 0;
    }

    /**
     * 获取财务统计
     */
    public static function getStatsForMerchant($merchantId, $period = '30')
    {
        $startDate = now()->subDays($period);
        
        $query = self::forMerchant($merchantId)->where('created_at', '>=', $startDate);
        
        return [
            'total_income' => $query->where('type', 'income')->where('status', 'completed')->sum('amount'),
            'total_expense' => abs($query->where('type', 'expense')->where('status', 'completed')->sum('amount')),
            'total_withdrawals' => abs($query->where('type', 'withdrawal')->where('status', 'completed')->sum('amount')),
            'current_balance' => self::getMerchantBalance($merchantId),
            'transaction_count' => $query->where('status', 'completed')->count(),
        ];
    }
}