<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'merchant_product_id',
        'product_name',
        'product_sku',
        'product_image',
        'unit_price',
        'quantity',
        'total_price',
        'product_specifications',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'product_specifications' => 'array',
    ];

    /**
     * 关联订单
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 关联商家商品
     */
    public function merchantProduct(): BelongsTo
    {
        return $this->belongsTo(MerchantProduct::class);
    }

    /**
     * 计算总价
     */
    public function calculateTotalPrice()
    {
        $this->total_price = $this->unit_price * $this->quantity;
        return $this->total_price;
    }
}