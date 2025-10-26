<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'cost_price',
        'image',
        'category',
        'brand',
        'rating',
        'sales',
        'stock',
        'sku',
        'specifications',
        'images',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'rating' => 'decimal:2',
        'specifications' => 'array',
        'images' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * 获取商品的主要图片
     */
    public function getMainImageAttribute()
    {
        if ($this->image) {
            return $this->image;
        }
        
        if ($this->images && is_array($this->images) && count($this->images) > 0) {
            return $this->images[0];
        }
        
        return 'https://via.placeholder.com/300x200?text=No+Image';
    }

    /**
     * 获取利润率
     */
    public function getProfitMarginAttribute()
    {
        if ($this->cost_price > 0) {
            return round((($this->price - $this->cost_price) / $this->cost_price) * 100, 2);
        }
        return 0;
    }

    /**
     * 获取建议售价（加价20%）
     */
    public function getSuggestedPriceAttribute()
    {
        return round($this->price * 1.2, 2);
    }

    /**
     * 搜索商品
     */
    public function scopeSearch($query, $keyword)
    {
        if (empty($keyword)) {
            return $query;
        }
        
        return $query->where(function($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%")
              ->orWhere('category', 'like', "%{$keyword}%")
              ->orWhere('brand', 'like', "%{$keyword}%")
              ->orWhere('sku', 'like', "%{$keyword}%");
        });
    }

    /**
     * 按分类筛选
     */
    public function scopeByCategory($query, $category)
    {
        if (empty($category)) {
            return $query;
        }
        
        return $query->where('category', $category);
    }

    /**
     * 按品牌筛选
     */
    public function scopeByBrand($query, $brand)
    {
        if (empty($brand)) {
            return $query;
        }
        
        return $query->where('brand', $brand);
    }

    /**
     * 只显示活跃商品
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 按评分排序
     */
    public function scopeOrderByRating($query, $direction = 'desc')
    {
        return $query->orderBy('rating', $direction);
    }

    /**
     * 按销量排序
     */
    public function scopeOrderBySales($query, $direction = 'desc')
    {
        return $query->orderBy('sales', $direction);
    }

    /**
     * 按价格排序
     */
    public function scopeOrderByPrice($query, $direction = 'asc')
    {
        return $query->orderBy('price', $direction);
    }
}