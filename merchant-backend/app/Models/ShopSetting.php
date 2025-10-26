<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'shop_name',
        'shop_slug',
        'shop_description',
        'shop_logo',
        'shop_banner',
        'contact_email',
        'contact_phone',
        'contact_address',
        'contact_city',
        'contact_state',
        'contact_country',
        'contact_zip',
        'website_url',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'youtube_url',
        'business_hours',
        'timezone',
        'currency',
        'language',
        'free_shipping_threshold',
        'default_shipping_fee',
        'shipping_zones',
        'return_policy',
        'privacy_policy',
        'terms_of_service',
        'is_active',
        'is_featured',
        'allow_reviews',
        'show_contact_info',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'business_hours' => 'array',
        'shipping_zones' => 'array',
        'free_shipping_threshold' => 'decimal:2',
        'default_shipping_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'allow_reviews' => 'boolean',
        'show_contact_info' => 'boolean',
    ];

    /**
     * 关联商家
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
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
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 按推荐筛选
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * 生成店铺slug
     */
    public static function generateSlug($shopName, $merchantId)
    {
        $baseSlug = \Str::slug($shopName);
        $slug = $baseSlug;
        $counter = 1;
        
        while (self::where('shop_slug', $slug)->where('merchant_id', '!=', $merchantId)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * 获取或创建店铺设置
     */
    public static function getOrCreateForMerchant($merchantId)
    {
        $shopSetting = self::forMerchant($merchantId)->first();
        
        if (!$shopSetting) {
            $merchant = User::find($merchantId);
            $merchantProfile = $merchant->merchantProfile ?? null;
            
            $shopSetting = self::create([
                'merchant_id' => $merchantId,
                'shop_name' => $merchantProfile->shop_name ?? $merchantProfile->merchant_name ?? '我的店铺',
                'shop_slug' => self::generateSlug($merchantProfile->shop_name ?? $merchantProfile->merchant_name ?? 'my-shop', $merchantId),
                'shop_description' => '欢迎来到我的店铺！',
                'contact_email' => $merchant->email,
                'contact_phone' => $merchantProfile->contact_phone ?? null,
                'timezone' => 'Asia/Kuala_Lumpur',
                'currency' => 'MYR',
                'language' => 'zh',
                'is_active' => true,
                'allow_reviews' => true,
                'show_contact_info' => true,
            ]);
        }
        
        return $shopSetting;
    }

    /**
     * 更新店铺设置
     */
    public function updateSettings($data)
    {
        // 如果店铺名称改变，更新slug
        if (isset($data['shop_name']) && $data['shop_name'] !== $this->shop_name) {
            $data['shop_slug'] = self::generateSlug($data['shop_name'], $this->merchant_id);
        }
        
        $this->update($data);
        return $this;
    }

    /**
     * 获取完整的联系地址
     */
    public function getFullAddressAttribute()
    {
        $addressParts = array_filter([
            $this->contact_address,
            $this->contact_city,
            $this->contact_state,
            $this->contact_zip,
            $this->contact_country,
        ]);
        
        return implode(', ', $addressParts);
    }

    /**
     * 获取营业时间文本
     */
    public function getBusinessHoursTextAttribute()
    {
        if (!$this->business_hours) {
            return '24/7 营业';
        }
        
        $hours = $this->business_hours;
        $text = [];
        
        $days = [
            'monday' => '周一',
            'tuesday' => '周二',
            'wednesday' => '周三',
            'thursday' => '周四',
            'friday' => '周五',
            'saturday' => '周六',
            'sunday' => '周日',
        ];
        
        foreach ($days as $day => $dayName) {
            if (isset($hours[$day]) && $hours[$day]['enabled']) {
                $text[] = $dayName . ': ' . $hours[$day]['open'] . ' - ' . $hours[$day]['close'];
            }
        }
        
        return implode('<br>', $text);
    }

    /**
     * 检查是否在营业时间内
     */
    public function isOpenNow()
    {
        if (!$this->business_hours) {
            return true; // 24/7营业
        }
        
        $now = now($this->timezone);
        $currentDay = strtolower($now->format('l')); // monday, tuesday, etc.
        
        if (!isset($this->business_hours[$currentDay]) || !$this->business_hours[$currentDay]['enabled']) {
            return false;
        }
        
        $hours = $this->business_hours[$currentDay];
        $currentTime = $now->format('H:i');
        
        return $currentTime >= $hours['open'] && $currentTime <= $hours['close'];
    }

    /**
     * 获取配送费用
     */
    public function getShippingFee($orderAmount = 0)
    {
        // 如果订单金额达到免运费门槛
        if ($this->free_shipping_threshold && $orderAmount >= $this->free_shipping_threshold) {
            return 0;
        }
        
        return $this->default_shipping_fee;
    }
}