<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_sn',
        'merchant_id',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_city',
        'shipping_postal_code',
        'status',
        'payment_status',
        'shipping_status',
        'payment_method',
        'subtotal',
        'shipping_fee',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'notes',
        'tracking_number',
        'shipping_company',
        'shipping_info',
        'paid_at',
        'shipped_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'shipping_info' => 'array',
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    /**
     * 获取商家
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    /**
     * 获取客户
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * 获取订单商品
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * 作用域：按商家筛选
     */
    public function scopeForMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }

    /**
     * 作用域：按状态筛选
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 作用域：按支付状态筛选
     */
    public function scopePaymentStatus($query, $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    /**
     * 作用域：搜索
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('order_number', 'like', "%{$keyword}%")
              ->orWhere('customer_name', 'like', "%{$keyword}%")
              ->orWhere('customer_email', 'like', "%{$keyword}%")
              ->orWhere('customer_phone', 'like', "%{$keyword}%");
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
     * 获取状态标签
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => '待确认',
            'confirmed' => '已确认',
            'shipped' => '已发货',
            'delivered' => '已送达',
            'cancelled' => '已取消',
            'returned' => '已退货',
            default => '未知',
        };
    }

    /**
     * 获取状态标签
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => '待处理',
            'confirmed' => '已确认',
            'processing' => '处理中',
            'shipped' => '已发货',
            'delivered' => '已送达',
            'completed' => '已完成',
            'cancelled' => '已取消',
            'refunded' => '已退款',
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
            'confirmed' => 'blue',
            'processing' => 'purple',
            'shipped' => 'purple',
            'delivered' => 'green',
            'completed' => 'green',
            'cancelled' => 'red',
            'refunded' => 'blue',
            default => 'gray',
        };
    }

    /**
     * 获取支付状态标签
     */
    public function getPaymentStatusLabel(): string
    {
        return match($this->payment_status) {
            'pending' => '待支付',
            'paid' => '已支付',
            'failed' => '支付失败',
            'refunded' => '已退款',
            default => '未知',
        };
    }

    /**
     * 获取物流状态标签
     */
    public function getShippingStatusLabel(): string
    {
        return match($this->shipping_status) {
            'pending' => '待发货',
            'shipped' => '已发货',
            'in_transit' => '运输中',
            'delivered' => '已送达',
            'returned' => '已退回',
            default => '未知',
        };
    }

    /**
     * 获取支付状态颜色
     */
    public function getPaymentStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'yellow',
            'paid' => 'green',
            'failed' => 'red',
            'refunded' => 'blue',
            default => 'gray',
        };
    }

    /**
     * 生成订单号
     */
    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
