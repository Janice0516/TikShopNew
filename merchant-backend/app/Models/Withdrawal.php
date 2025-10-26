<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'withdrawal_number',
        'amount',
        'fee',
        'actual_amount',
        'method',
        'payment_info',
        'status',
        'admin_notes',
        'failure_reason',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'payment_info' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * 关联商家
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    /**
     * 关联处理人
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * 关联财务记录
     */
    public function financeRecords(): HasMany
    {
        return $this->hasMany(FinanceRecord::class);
    }

    /**
     * 获取提现方式文本
     */
    public function getMethodTextAttribute()
    {
        $methodMap = [
            'bank_transfer' => '银行转账',
            'alipay' => '支付宝',
            'wechat' => '微信支付',
            'paypal' => 'PayPal',
        ];

        return $methodMap[$this->method] ?? '未知方式';
    }

    /**
     * 获取状态文本
     */
    public function getStatusTextAttribute()
    {
        $statusMap = [
            'pending' => '待处理',
            'processing' => '处理中',
            'completed' => '已完成',
            'failed' => '失败',
            'cancelled' => '已取消',
        ];

        return $statusMap[$this->status] ?? '未知状态';
    }

    /**
     * 获取状态颜色
     */
    public function getStatusColorAttribute()
    {
        $colorMap = [
            'pending' => 'yellow',
            'processing' => 'blue',
            'completed' => 'green',
            'failed' => 'red',
            'cancelled' => 'gray',
        ];

        return $colorMap[$this->status] ?? 'gray';
    }

    /**
     * 按商家筛选
     */
    public function scopeForMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }

    /**
     * 按状态筛选
     */
    public function scopeByStatus($query, $status)
    {
        if (empty($status)) {
            return $query;
        }
        
        return $query->where('status', $status);
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
     * 生成提现单号
     */
    public static function generateWithdrawalNumber()
    {
        do {
            $withdrawalNumber = 'WD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('withdrawal_number', $withdrawalNumber)->exists());
        
        return $withdrawalNumber;
    }

    /**
     * 创建提现申请
     */
    public static function createWithdrawal($merchantId, $amount, $method, $paymentInfo)
    {
        // 计算手续费（假设2%）
        $fee = $amount * 0.02;
        $actualAmount = $amount - $fee;
        
        return self::create([
            'merchant_id' => $merchantId,
            'withdrawal_number' => self::generateWithdrawalNumber(),
            'amount' => $amount,
            'fee' => $fee,
            'actual_amount' => $actualAmount,
            'method' => $method,
            'payment_info' => $paymentInfo,
            'status' => 'pending',
        ]);
    }

    /**
     * 处理提现申请
     */
    public function process($status, $processedBy, $notes = null, $failureReason = null)
    {
        $this->status = $status;
        $this->processed_by = $processedBy;
        $this->processed_at = now();
        
        if ($notes) {
            $this->admin_notes = $notes;
        }
        
        if ($failureReason) {
            $this->failure_reason = $failureReason;
        }
        
        $this->save();
        
        // 如果提现成功，创建财务记录
        if ($status === 'completed') {
            FinanceRecord::createRecord(
                $this->merchant_id,
                'withdrawal',
                -$this->amount, // 负数表示支出
                "提现申请 #{$this->withdrawal_number}",
                null,
                $this->id
            );
        }
    }

    /**
     * 获取提现统计
     */
    public static function getStatsForMerchant($merchantId, $period = '30')
    {
        $startDate = now()->subDays($period);
        
        $query = self::forMerchant($merchantId)->where('created_at', '>=', $startDate);
        
        return [
            'total_withdrawals' => $query->count(),
            'total_amount' => $query->sum('amount'),
            'completed_amount' => $query->where('status', 'completed')->sum('amount'),
            'pending_amount' => $query->where('status', 'pending')->sum('amount'),
            'failed_amount' => $query->where('status', 'failed')->sum('amount'),
        ];
    }
}