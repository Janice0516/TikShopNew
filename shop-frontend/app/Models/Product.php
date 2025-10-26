<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'sku',
        'price',
        'cost_price',
        'markup_price',
        'suggest_price',
        'stock',
        'status',
        'images',
        'variants',
        'category_id',
        'merchant_id',
        'weight',
        'brand',
        'specifications',
        'is_featured',
        'sort_order',
        'views_count',
        'sales_count',
        'rating',
    ];

    /**
     * 隐藏敏感的商业信息字段
     */
    protected $hidden = [
        'cost_price',
        'suggest_price',
        'merchant_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'markup_price' => 'decimal:2',
            'suggest_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'images' => 'array',
            'variants' => 'array',
            'specifications' => 'array',
            'is_featured' => 'boolean',
            'views_count' => 'integer',
            'sales_count' => 'integer',
            'rating' => 'decimal:1',
        ];
    }

    /**
     * 获取商品分类
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 获取商家
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    /**
     * 获取商品变体
     */
    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * 获取商品图片
     */
    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * 作用域：按状态筛选
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 作用域：按分类筛选
     */
    public function scopeCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * 作用域：按商家筛选
     */
    public function scopeMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }

    /**
     * 作用域：推荐商品
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * 作用域：搜索
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('sku', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
        });
    }

    /**
     * 获取状态标签
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => '上架',
            'inactive' => '下架',
            'draft' => '草稿',
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
            'draft' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * 获取商城端显示价格（优先使用markup_price）
     */
    public function getDisplayPriceAttribute(): float
    {
        return $this->markup_price ?? $this->price;
    }

    /**
     * 获取商家利润
     */
    public function getMerchantProfitAttribute(): float
    {
        $displayPrice = $this->getDisplayPriceAttribute();
        return $displayPrice - $this->cost_price;
    }

    /**
     * 获取利润率
     */
    public function getProfitMarginAttribute(): float
    {
        $displayPrice = $this->getDisplayPriceAttribute();
        if ($displayPrice <= 0) return 0;
        return (($displayPrice - $this->cost_price) / $displayPrice) * 100;
    }
}
