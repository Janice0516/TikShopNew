<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RechargeRequest extends Model
{
    protected $fillable = [
        'recharge_number',
        'user_id',
        'user_type',
        'amount',
        'payment_method',
        'payment_account',
        'receipt_account',
        'bank_name',
        'bank_branch',
        'transaction_id',
        'status',
        'rejection_reason',
        'processed_by',
        'processed_at',
        'admin_notes',
        'user_notes',
        'attachments',
        'payment_proof',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'processed_at' => 'datetime',
            'attachments' => 'array',
            'payment_proof' => 'array',
        ];
    }

    /**
     * 获取用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
        return $this->hasMany(RechargeLog::class);
    }

    /**
     * 作用域：按状态筛选
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 作用域：按用户筛选
     */
    public function scopeUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * 作用域：按用户类型筛选
     */
    public function scopeUserType($query, $userType)
    {
        return $query->where('user_type', $userType);
    }

    /**
     * 作用域：按支付方式筛选
     */
    public function scopePaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * 作用域：搜索
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('recharge_number', 'like', "%{$keyword}%")
              ->orWhere('transaction_id', 'like', "%{$keyword}%")
              ->orWhere('payment_account', 'like', "%{$keyword}%")
              ->orWhereHas('user', function ($userQuery) use ($keyword) {
                  $userQuery->where('name', 'like', "%{$keyword}%")
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
     * 生成充值单号
     */
    public static function generateRechargeNumber(): string
    {
        do {
            $number = 'RC' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('recharge_number', $number)->exists());

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
            'pending' => '待审核',
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
     * 获取支付方式标签
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'bank' => '银行转账',
            'alipay' => '支付宝',
            'wechat' => '微信支付',
            'online' => '在线支付',
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

    /**
     * 检查是否可以完成
     */
    public function canComplete(): bool
    {
        return $this->status === 'processing';
    }
}
