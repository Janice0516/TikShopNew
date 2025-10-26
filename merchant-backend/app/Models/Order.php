<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'merchant_id',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'status',
        'subtotal',
        'shipping_fee',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'payment_status',
        'payment_method',
        'payment_reference',
        'paid_at',
        'shipping_address',
        'shipping_method',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'notes',
        'customer_notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipping_address' => 'array',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * 关联商家
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    /**
     * 关联客户
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * 关联订单商品
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * 关联财务记录
     */
    public function financeRecords(): HasMany
    {
        return $this->hasMany(FinanceRecord::class);
    }

    /**
     * 获取订单状态文本
     */
    public function getStatusTextAttribute()
    {
        $statusMap = [
            'pending' => '待处理',
            'confirmed' => '已确认',
            'processing' => '处理中',
            'shipped' => '已发货',
            'delivered' => '已送达',
            'cancelled' => '已取消',
            'refunded' => '已退款',
        ];

        return $statusMap[$this->status] ?? '未知状态';
    }

    /**
     * 获取支付状态文本
     */
    public function getPaymentStatusTextAttribute()
    {
        $statusMap = [
            'pending' => '待支付',
            'paid' => '已支付',
            'failed' => '支付失败',
            'refunded' => '已退款',
        ];

        return $statusMap[$this->payment_status] ?? '未知状态';
    }

    /**
     * 获取状态颜色
     */
    public function getStatusColorAttribute()
    {
        $colorMap = [
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'processing' => 'indigo',
            'shipped' => 'purple',
            'delivered' => 'green',
            'cancelled' => 'red',
            'refunded' => 'gray',
        ];

        return $colorMap[$this->status] ?? 'gray';
    }

    /**
     * 按商家筛选
     */
    public function scopeForMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }

    /**
     * 按状态筛选
     */
    public function scopeByStatus($query, $status)
    {
        if (empty($status)) {
            return $query;
        }
        
        return $query->where('status', $status);
    }

    /**
     * 按支付状态筛选
     */
    public function scopeByPaymentStatus($query, $paymentStatus)
    {
        if (empty($paymentStatus)) {
            return $query;
        }
        
        return $query->where('payment_status', $paymentStatus);
    }

    /**
     * 按日期范围筛选
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
        
        return $query;
    }

    /**
     * 搜索订单
     */
    public function scopeSearch($query, $keyword)
    {
        if (empty($keyword)) {
            return $query;
        }
        
        return $query->where(function($q) use ($keyword) {
            $q->where('order_number', 'like', "%{$keyword}%")
              ->orWhere('customer_name', 'like', "%{$keyword}%")
              ->orWhere('customer_email', 'like', "%{$keyword}%")
              ->orWhere('customer_phone', 'like', "%{$keyword}%");
        });
    }

    /**
     * 生成订单号
     */
    public static function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('order_number', $orderNumber)->exists());
        
        return $orderNumber;
    }

    /**
     * 更新订单状态
     */
    public function updateStatus($status, $notes = null)
    {
        $this->status = $status;
        
        if ($notes) {
            $this->notes = $notes;
        }
        
        // 设置相关时间戳
        switch ($status) {
            case 'shipped':
                $this->shipped_at = now();
                break;
            case 'delivered':
                $this->delivered_at = now();
                break;
        }
        
        $this->save();
    }

    /**
     * 计算订单统计
     */
    public static function getStatsForMerchant($merchantId, $period = '30')
    {
        $startDate = now()->subDays($period);
        
        $query = self::forMerchant($merchantId)->where('created_at', '>=', $startDate);
        
        return [
            'total_orders' => $query->count(),
            'total_revenue' => $query->where('payment_status', 'paid')->sum('total_amount'),
            'pending_orders' => $query->where('status', 'pending')->count(),
            'shipped_orders' => $query->where('status', 'shipped')->count(),
            'delivered_orders' => $query->where('status', 'delivered')->count(),
        ];
    }
}