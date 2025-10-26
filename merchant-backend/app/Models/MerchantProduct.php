<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'platform_product_id',
        'name',
        'description',
        'sale_price',
        'cost_price',
        'sku',
        'image',
        'images',
        'category',
        'brand',
        'stock',
        'min_stock',
        'is_active',
        'is_featured',
        'specifications',
        'notes',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'images' => 'array',
        'specifications' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * 关联商家
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    /**
     * 关联平台商品
     */
    public function platformProduct(): BelongsTo
    {
        return $this->belongsTo(PlatformProduct::class);
    }

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
        if ($this->cost_price && $this->cost_price > 0) {
            return round((($this->sale_price - $this->cost_price) / $this->cost_price) * 100, 2);
        }
        return 0;
    }

    /**
     * 获取利润金额
     */
    public function getProfitAmountAttribute()
    {
        if ($this->cost_price && $this->cost_price > 0) {
            return round($this->sale_price - $this->cost_price, 2);
        }
        return 0;
    }

    /**
     * 检查库存状态
     */
    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) {
            return 'out_of_stock';
        } elseif ($this->stock <= $this->min_stock) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    /**
     * 获取库存状态文本
     */
    public function getStockStatusTextAttribute()
    {
        switch ($this->stock_status) {
            case 'out_of_stock':
                return '缺货';
            case 'low_stock':
                return '库存不足';
            default:
                return '有库存';
        }
    }

    /**
     * 获取库存状态颜色
     */
    public function getStockStatusColorAttribute()
    {
        switch ($this->stock_status) {
            case 'out_of_stock':
                return 'text-red-600';
            case 'low_stock':
                return 'text-yellow-600';
            default:
                return 'text-green-600';
        }
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
              ->orWhere('sku', 'like', "%{$keyword}%")
              ->orWhere('category', 'like', "%{$keyword}%")
              ->orWhere('brand', 'like', "%{$keyword}%");
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
     * 按状态筛选
     */
    public function scopeByStatus($query, $status)
    {
        switch ($status) {
            case 'active':
                return $query->where('is_active', true);
            case 'inactive':
                return $query->where('is_active', false);
            case 'featured':
                return $query->where('is_featured', true);
            case 'low_stock':
                return $query->whereRaw('stock <= min_stock');
            case 'out_of_stock':
                return $query->where('stock', 0);
            default:
                return $query;
        }
    }

    /**
     * 只显示活跃商品
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 按价格排序
     */
    public function scopeOrderByPrice($query, $direction = 'asc')
    {
        return $query->orderBy('sale_price', $direction);
    }

    /**
     * 按库存排序
     */
    public function scopeOrderByStock($query, $direction = 'desc')
    {
        return $query->orderBy('stock', $direction);
    }

    /**
     * 按创建时间排序
     */
    public function scopeOrderByCreated($query, $direction = 'desc')
    {
        return $query->orderBy('created_at', $direction);
    }

    /**
     * 从平台商品创建商家商品
     */
    public static function createFromPlatformProduct($platformProduct, $merchantId, $salePrice = null)
    {
        $salePrice = $salePrice ?? $platformProduct->suggested_price;
        
        return self::create([
            'merchant_id' => $merchantId,
            'platform_product_id' => $platformProduct->id,
            'name' => $platformProduct->name,
            'description' => $platformProduct->description,
            'sale_price' => $salePrice,
            'cost_price' => $platformProduct->cost_price,
            'sku' => 'MP-' . $platformProduct->sku . '-' . $merchantId,
            'image' => $platformProduct->image,
            'images' => $platformProduct->images,
            'category' => $platformProduct->category,
            'brand' => $platformProduct->brand,
            'stock' => 0, // 初始库存为0，需要商家手动设置
            'min_stock' => 5, // 默认最低库存
            'specifications' => $platformProduct->specifications,
        ]);
    }
}