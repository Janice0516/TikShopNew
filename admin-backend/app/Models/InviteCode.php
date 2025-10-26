<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InviteCode extends Model
{
    protected $fillable = [
        'code',
        'type',
        'name',
        'description',
        'creator_id',
        'merchant_id',
        'max_uses',
        'used_count',
        'reward_amount',
        'reward_type',
        'discount_percent',
        'discount_amount',
        'start_date',
        'end_date',
        'is_active',
        'conditions',
    ];

    protected function casts(): array
    {
        return [
            'reward_amount' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
            'conditions' => 'array',
        ];
    }

    /**
     * 获取创建者
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'creator_id');
    }

    /**
     * 获取关联商家
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    /**
     * 获取使用记录
     */
    public function usages(): HasMany
    {
        return $this->hasMany(InviteCodeUsage::class);
    }

    /**
     * 作用域：按类型筛选
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * 作用域：激活的邀请码
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 作用域：按商家筛选
     */
    public function scopeMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }

    /**
     * 作用域：搜索
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('code', 'like', "%{$keyword}%")
              ->orWhere('name', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
        });
    }

    /**
     * 作用域：有效的邀请码（未过期且未达到使用上限）
     */
    public function scopeValid($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('max_uses')
                  ->orWhereRaw('used_count < max_uses');
            });
    }

    /**
     * 检查邀请码是否有效
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // 检查日期范围
        if ($this->start_date && $this->start_date > now()) {
            return false;
        }

        if ($this->end_date && $this->end_date < now()) {
            return false;
        }

        // 检查使用次数
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * 使用邀请码
     */
    public function useBy($userId, $userType = 'merchant', $ipAddress = null, $userAgent = null)
    {
        if (!$this->isValid()) {
            return false;
        }

        // 检查是否已经使用过
        $existingUsage = $this->usages()
            ->where('user_id', $userId)
            ->where('user_type', $userType)
            ->first();

        if ($existingUsage) {
            return false;
        }

        // 创建使用记录
        $usage = $this->usages()->create([
            'user_id' => $userId,
            'user_type' => $userType,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'reward_amount' => $this->reward_amount,
            'reward_type' => $this->reward_type,
            'status' => 'success',
        ]);

        // 更新使用次数
        $this->increment('used_count');

        return $usage;
    }

    /**
     * 生成邀请码
     */
    public static function generateCode($length = 8): string
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * 获取状态标签
     */
    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_active) {
            return '已停用';
        }

        if (!$this->isValid()) {
            return '已失效';
        }

        return '有效';
    }

    /**
     * 获取状态颜色
     */
    public function getStatusColorAttribute(): string
    {
        if (!$this->is_active) {
            return 'red';
        }

        if (!$this->isValid()) {
            return 'yellow';
        }

        return 'green';
    }

    /**
     * 获取使用率
     */
    public function getUsageRateAttribute(): float
    {
        if (!$this->max_uses) {
            return 0;
        }

        return round(($this->used_count / $this->max_uses) * 100, 2);
    }
}
