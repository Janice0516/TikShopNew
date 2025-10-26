<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'overall_score',
        'service_score',
        'quality_score',
        'shipping_score',
        'communication_score',
        'rating_level',
        'rating_text',
        'total_reviews',
        'positive_reviews',
        'neutral_reviews',
        'negative_reviews',
        'total_orders',
        'completed_orders',
        'cancelled_orders',
        'refund_orders',
        'avg_response_time',
        'avg_shipping_time',
        'avg_delivery_time',
        'total_revenue',
        'payment_delays',
        'refund_rate',
        'is_verified',
        'is_premium',
        'badges',
    ];

    protected $casts = [
        'overall_score' => 'decimal:1',
        'service_score' => 'decimal:1',
        'quality_score' => 'decimal:1',
        'shipping_score' => 'decimal:1',
        'communication_score' => 'decimal:1',
        'total_revenue' => 'decimal:2',
        'badges' => 'array',
        'is_verified' => 'boolean',
        'is_premium' => 'boolean',
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
     * 按评级等级筛选
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('rating_level', $level);
    }

    /**
     * 按认证状态筛选
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * 按高级商家筛选
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * 获取或创建信用评级
     */
    public static function getOrCreateForMerchant($merchantId)
    {
        $creditRating = self::forMerchant($merchantId)->first();
        
        if (!$creditRating) {
            $creditRating = self::create([
                'merchant_id' => $merchantId,
                'overall_score' => 75.0, // 默认分数
                'service_score' => 75.0,
                'quality_score' => 75.0,
                'shipping_score' => 75.0,
                'communication_score' => 75.0,
                'rating_level' => 'B',
                'rating_text' => '良好',
                'is_verified' => false,
                'is_premium' => false,
                'badges' => [],
            ]);
        }
        
        return $creditRating;
    }

    /**
     * 更新信用评级
     */
    public function updateRating()
    {
        // 计算各项评分
        $this->service_score = $this->calculateServiceScore();
        $this->quality_score = $this->calculateQualityScore();
        $this->shipping_score = $this->calculateShippingScore();
        $this->communication_score = $this->calculateCommunicationScore();
        
        // 计算总分
        $this->overall_score = ($this->service_score + $this->quality_score + $this->shipping_score + $this->communication_score) / 4;
        
        // 更新评级等级
        $this->updateRatingLevel();
        
        // 更新徽章
        $this->updateBadges();
        
        $this->save();
    }

    /**
     * 计算服务评分
     */
    private function calculateServiceScore()
    {
        if ($this->total_reviews == 0) return 75.0;
        
        $positiveRate = ($this->positive_reviews / $this->total_reviews) * 100;
        $negativeRate = ($this->negative_reviews / $this->total_reviews) * 100;
        
        // 基础分数 + 好评率加分 - 差评率扣分
        $score = 60 + ($positiveRate * 0.3) - ($negativeRate * 0.5);
        
        return max(0, min(100, $score));
    }

    /**
     * 计算商品质量评分
     */
    private function calculateQualityScore()
    {
        if ($this->total_orders == 0) return 75.0;
        
        $refundRate = $this->refund_rate;
        $qualityScore = 80 - ($refundRate * 0.8); // 退款率越高，质量分数越低
        
        return max(0, min(100, $qualityScore));
    }

    /**
     * 计算配送评分
     */
    private function calculateShippingScore()
    {
        if ($this->avg_shipping_time == 0) return 75.0;
        
        // 基于平均发货时间和配送时间计算
        $shippingScore = 90 - ($this->avg_shipping_time * 2) - ($this->avg_delivery_time * 1.5);
        
        return max(0, min(100, $shippingScore));
    }

    /**
     * 计算沟通评分
     */
    private function calculateCommunicationScore()
    {
        if ($this->avg_response_time == 0) return 75.0;
        
        // 基于平均响应时间计算
        $communicationScore = 90 - ($this->avg_response_time / 10);
        
        return max(0, min(100, $communicationScore));
    }

    /**
     * 更新评级等级
     */
    private function updateRatingLevel()
    {
        $score = $this->overall_score;
        
        if ($score >= 95) {
            $this->rating_level = 'A+';
            $this->rating_text = '优秀';
        } elseif ($score >= 90) {
            $this->rating_level = 'A';
            $this->rating_text = '优秀';
        } elseif ($score >= 85) {
            $this->rating_level = 'B+';
            $this->rating_text = '良好';
        } elseif ($score >= 80) {
            $this->rating_level = 'B';
            $this->rating_text = '良好';
        } elseif ($score >= 75) {
            $this->rating_level = 'C+';
            $this->rating_text = '普通';
        } elseif ($score >= 70) {
            $this->rating_level = 'C';
            $this->rating_text = '普通';
        } else {
            $this->rating_level = 'D';
            $this->rating_text = '较差';
        }
    }

    /**
     * 更新徽章
     */
    private function updateBadges()
    {
        $badges = [];
        
        // 基于各种条件添加徽章
        if ($this->total_orders >= 100) {
            $badges[] = 'experienced';
        }
        
        if ($this->overall_score >= 90) {
            $badges[] = 'excellent';
        }
        
        if ($this->refund_rate <= 5) {
            $badges[] = 'reliable';
        }
        
        if ($this->avg_response_time <= 30) {
            $badges[] = 'responsive';
        }
        
        if ($this->is_verified) {
            $badges[] = 'verified';
        }
        
        if ($this->is_premium) {
            $badges[] = 'premium';
        }
        
        $this->badges = $badges;
    }

    /**
     * 获取评级颜色
     */
    public function getRatingColorAttribute()
    {
        return match($this->rating_level) {
            'A+', 'A' => 'green',
            'B+', 'B' => 'blue',
            'C+', 'C' => 'yellow',
            'D' => 'red',
            default => 'gray'
        };
    }

    /**
     * 获取徽章文本
     */
    public function getBadgeTextsAttribute()
    {
        $badgeTexts = [
            'experienced' => '经验丰富',
            'excellent' => '优秀商家',
            'reliable' => '值得信赖',
            'responsive' => '响应迅速',
            'verified' => '认证商家',
            'premium' => '高级商家',
        ];
        
        return array_map(fn($badge) => $badgeTexts[$badge] ?? $badge, $this->badges ?? []);
    }

    /**
     * 获取评级趋势
     */
    public function getRatingTrend($days = 30)
    {
        // 这里可以实现评级趋势分析
        // 暂时返回模拟数据
        return [
            'trend' => 'up', // up, down, stable
            'change' => 2.5, // 变化幅度
            'period' => $days,
        ];
    }
}