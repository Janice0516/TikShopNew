<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeaturedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'sort_order',
        'custom_title',
        'custom_price',
        'custom_original_price',
        'custom_rating',
        'custom_sales_count',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'custom_price' => 'decimal:2',
            'custom_original_price' => 'decimal:2',
            'custom_rating' => 'decimal:1',
            'custom_sales_count' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * 关联商品
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 获取显示标题
     */
    public function getDisplayTitleAttribute(): string
    {
        return $this->custom_title ?: $this->product->name;
    }

    /**
     * 获取显示价格
     */
    public function getDisplayPriceAttribute(): float
    {
        return $this->custom_price ?: $this->product->price;
    }

    /**
     * 获取显示原价
     */
    public function getDisplayOriginalPriceAttribute(): ?float
    {
        return $this->custom_original_price ?: $this->product->original_price;
    }

    /**
     * 获取显示评分
     */
    public function getDisplayRatingAttribute(): float
    {
        return $this->custom_rating ?: $this->product->rating;
    }

    /**
     * 获取显示销量
     */
    public function getDisplaySalesCountAttribute(): int
    {
        return $this->custom_sales_count ?: $this->product->sales_count;
    }

    /**
     * 获取类型标签
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'savings' => 'Savings for you',
            'top_deals' => 'Top deals for you',
            default => '未知',
        };
    }

    /**
     * 获取状态标签
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? '启用' : '禁用';
    }

    /**
     * 获取状态颜色
     */
    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'green' : 'red';
    }
}
