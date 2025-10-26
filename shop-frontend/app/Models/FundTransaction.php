<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundTransaction extends Model
{
    protected $fillable = [
        'transaction_number',
        'from_account_id',
        'to_account_id',
        'amount',
        'transaction_type',
        'status',
        'currency',
        'description',
        'reference_type',
        'reference_id',
        'operator_id',
        'operator_type',
        'metadata',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'metadata' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * 获取转出账户
     */
    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(FundAccount::class, 'from_account_id');
    }

    /**
     * 获取转入账户
     */
    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(FundAccount::class, 'to_account_id');
    }

    /**
     * 获取操作人
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'operator_id');
    }

    /**
     * 作用域：按交易类型筛选
     */
    public function scopeTransactionType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * 作用域：按状态筛选
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 作用域：按账户筛选
     */
    public function scopeAccount($query, $accountId)
    {
        return $query->where('from_account_id', $accountId)
                   ->orWhere('to_account_id', $accountId);
    }

    /**
     * 作用域：按日期范围筛选
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * 作用域：按金额范围筛选
     */
    public function scopeAmountRange($query, $minAmount, $maxAmount)
    {
        return $query->whereBetween('amount', [$minAmount, $maxAmount]);
    }

    /**
     * 作用域：搜索
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('transaction_number', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
        });
    }

    /**
     * 生成交易单号
     */
    public static function generateTransactionNumber(): string
    {
        do {
            $number = 'TXN' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('transaction_number', $number)->exists());

        return $number;
    }

    /**
     * 获取交易类型标签
     */
    public function getTransactionTypeLabelAttribute(): string
    {
        return match($this->transaction_type) {
            'recharge' => '充值',
            'withdrawal' => '提现',
            'payment' => '支付',
            'refund' => '退款',
            'commission' => '佣金',
            'fee' => '手续费',
            'transfer' => '转账',
            default => '未知',
        };
    }

    /**
     * 获取状态标签
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => '待处理',
            'processing' => '处理中',
            'completed' => '已完成',
            'failed' => '失败',
            'cancelled' => '已取消',
            default => '未知',
        };
    }

    /**
     * 获取状态颜色
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'processing' => 'blue',
            'completed' => 'green',
            'failed' => 'red',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    /**
     * 执行交易
     */
    public function execute()
    {
        if ($this->status !== 'pending') {
            return false;
        }

        try {
            \DB::beginTransaction();

            // 更新状态为处理中
            $this->update(['status' => 'processing']);

            // 如果有转出账户，减少余额
            if ($this->from_account_id) {
                $fromAccount = $this->fromAccount;
                if (!$fromAccount->deductBalance($this->amount)) {
                    throw new \Exception('转出账户余额不足');
                }
            }

            // 如果有转入账户，增加余额
            if ($this->to_account_id) {
                $toAccount = $this->toAccount;
                $toAccount->addBalance($this->amount);
            }

            // 更新交易状态为已完成
            $this->update([
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            \DB::commit();
            return true;

        } catch (\Exception $e) {
            \DB::rollback();
            
            $this->update([
                'status' => 'failed',
                'metadata' => array_merge($this->metadata ?? [], ['error' => $e->getMessage()]),
            ]);

            return false;
        }
    }

    /**
     * 取消交易
     */
    public function cancel()
    {
        if (!in_array($this->status, ['pending', 'processing'])) {
            return false;
        }

        $this->update([
            'status' => 'cancelled',
            'processed_at' => now(),
        ]);

        return true;
    }
}
