<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    protected $fillable = [
        'user_id',
        'merchant_name',
        'username',
        'contact_name',
        'contact_phone',
        'shop_name',
        'invite_code',
        'status',
        'balance',
        'frozen_amount',
        'settings',
        'business_info',
        'verified_at',
        'last_login_at',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'frozen_amount' => 'decimal:2',
            'settings' => 'array',
            'business_info' => 'array',
            'verified_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * 获取关联用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取商家商品
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'merchant_id', 'user_id');
    }

    /**
     * 获取商家订单
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'merchant_id', 'user_id');
    }

    /**
     * 作用域：按状态筛选
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 作用域：已认证
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * 作用域：未认证
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('verified_at');
    }

    /**
     * 作用域：搜索
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('merchant_name', 'like', "%{$keyword}%")
              ->orWhere('username', 'like', "%{$keyword}%")
              ->orWhere('shop_name', 'like', "%{$keyword}%")
              ->orWhere('contact_name', 'like', "%{$keyword}%")
              ->orWhere('invite_code', 'like', "%{$keyword}%");
        });
    }

    /**
     * 获取状态标签
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => '正常',
            'inactive' => '停用',
            'suspended' => '暂停',
            default => '未知',
        };
    }

    /**
     * 获取状态颜色
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'red',
            'suspended' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * 获取可用余额
     */
    public function getAvailableBalanceAttribute(): float
    {
        return $this->balance - $this->frozen_amount;
    }

    /**
     * 是否已认证
     */
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }
}
