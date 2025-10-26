<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Withdrawal extends Model
{
    protected $fillable = [
        'withdrawal_number',
        'merchant_id',
        'amount',
        'fee',
        'actual_amount',
        'withdrawal_method',
        'account_name',
        'account_number',
        'bank_name',
        'bank_branch',
        'status',
        'rejection_reason',
        'processed_by',
        'processed_at',
        'admin_notes',
        'merchant_notes',
        'attachments',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'fee' => 'decimal:2',
            'actual_amount' => 'decimal:2',
            'processed_at' => 'datetime',
            'attachments' => 'array',
        ];
    }

    /**
     * 获取商家
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    /**
     * 获取处理人
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'processed_by');
    }

    /**
     * 获取操作日志
     */
    public function logs(): HasMany
    {
        return $this->hasMany(WithdrawalLog::class);
    }

    /**
     * 作用域：按状态筛选
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 作用域：按商家筛选
     */
    public function scopeMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }

    /**
     * 作用域：按提现方式筛选
     */
    public function scopeMethod($query, $method)
    {
        return $query->where('withdrawal_method', $method);
    }

    /**
     * 作用域：搜索
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('withdrawal_number', 'like', "%{$keyword}%")
              ->orWhere('account_name', 'like', "%{$keyword}%")
              ->orWhere('account_number', 'like', "%{$keyword}%")
              ->orWhereHas('merchant', function ($merchantQuery) use ($keyword) {
                  $merchantQuery->where('name', 'like', "%{$keyword}%")
                               ->orWhere('email', 'like', "%{$keyword}%");
              });
        });
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
     * 生成提现单号
     */
    public static function generateWithdrawalNumber(): string
    {
        do {
            $number = 'WD' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('withdrawal_number', $number)->exists());

        return $number;
    }

    /**
     * 更新状态并记录日志
     */
    public function updateStatus($newStatus, $operatorId = null, $operatorType = 'admin', $description = null, $data = null)
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => $newStatus,
            'processed_by' => $operatorId,
            'processed_at' => now(),
        ]);

        // 记录操作日志
        $this->logs()->create([
            'action' => $newStatus,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'description' => $description,
            'operator_id' => $operatorId,
            'operator_type' => $operatorType,
            'data' => $data,
        ]);
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
            'rejected' => '已拒绝',
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
            'rejected' => 'red',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    /**
     * 获取提现方式标签
     */
    public function getMethodLabelAttribute(): string
    {
        return match($this->withdrawal_method) {
            'bank' => '银行转账',
            'alipay' => '支付宝',
            'wechat' => '微信支付',
            default => '未知',
        };
    }

    /**
     * 检查是否可以处理
     */
    public function canProcess(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * 检查是否可以拒绝
     */
    public function canReject(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * 检查是否可以取消
     */
    public function canCancel(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }
}
