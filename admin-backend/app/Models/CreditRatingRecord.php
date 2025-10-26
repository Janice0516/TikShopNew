<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditRatingRecord extends Model
{
    protected $fillable = [
        'credit_rating_id',
        'order_id',
        'customer_id',
        'overall_score',
        'service_score',
        'quality_score',
        'delivery_score',
        'communication_score',
        'review_type',
        'review_content',
        'review_images',
        'is_anonymous',
        'is_verified',
        'verified_by',
        'verified_at',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'overall_score' => 'decimal:2',
            'service_score' => 'decimal:2',
            'quality_score' => 'decimal:2',
            'delivery_score' => 'decimal:2',
            'communication_score' => 'decimal:2',
            'review_images' => 'array',
            'is_anonymous' => 'boolean',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * 获取信用评级
     */
    public function creditRating(): BelongsTo
    {
        return $this->belongsTo(CreditRating::class);
    }

    /**
     * 获取订单
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 获取客户
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * 获取验证人
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'verified_by');
    }

    /**
     * 作用域：按评价类型筛选
     */
    public function scopeReviewType($query, $type)
    {
        return $query->where('review_type', $type);
    }

    /**
     * 作用域：已验证的评价
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * 作用域：按客户筛选
     */
    public function scopeCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * 作用域：按日期范围筛选
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * 获取评价类型标签
     */
    public function getReviewTypeLabelAttribute(): string
    {
        return match($this->review_type) {
            'positive' => '好评',
            'neutral' => '中评',
            'negative' => '差评',
            default => '未知',
        };
    }

    /**
     * 获取评价类型颜色
     */
    public function getReviewTypeColorAttribute(): string
    {
        return match($this->review_type) {
            'positive' => 'green',
            'neutral' => 'yellow',
            'negative' => 'red',
            default => 'gray',
        };
    }

    /**
     * 计算平均评分
     */
    public function getAverageScoreAttribute(): float
    {
        return round(($this->service_score + $this->quality_score + $this->delivery_score + $this->communication_score) / 4, 2);
    }
}
