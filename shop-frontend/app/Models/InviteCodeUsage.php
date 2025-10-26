<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InviteCodeUsage extends Model
{
    protected $fillable = [
        'invite_code_id',
        'user_id',
        'user_type',
        'ip_address',
        'user_agent',
        'reward_amount',
        'reward_type',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'reward_amount' => 'decimal:2',
        ];
    }

    /**
     * 获取邀请码
     */
    public function inviteCode(): BelongsTo
    {
        return $this->belongsTo(InviteCode::class);
    }

    /**
     * 获取使用者
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 作用域：按状态筛选
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 作用域：按用户类型筛选
     */
    public function scopeUserType($query, $userType)
    {
        return $query->where('user_type', $userType);
    }

    /**
     * 作用域：按日期范围筛选
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
