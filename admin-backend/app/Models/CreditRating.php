<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditRating extends Model
{
    protected $fillable = [
        'merchant_id',
        'overall_score',
        'service_score',
        'quality_score',
        'delivery_score',
        'communication_score',
        'total_reviews',
        'positive_reviews',
        'neutral_reviews',
        'negative_reviews',
        'rating_level',
        'rating_summary',
        'improvement_suggestions',
        'rating_history',
        'last_updated',
    ];

    protected function casts(): array
    {
        return [
            'overall_score' => 'decimal:2',
            'service_score' => 'decimal:2',
            'quality_score' => 'decimal:2',
            'delivery_score' => 'decimal:2',
            'communication_score' => 'decimal:2',
            'improvement_suggestions' => 'array',
            'rating_history' => 'array',
            'last_updated' => 'datetime',
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
     * 获取评级记录
     */
    public function records(): HasMany
    {
        return $this->hasMany(CreditRatingRecord::class);
    }

    /**
     * 作用域：按评级等级筛选
     */
    public function scopeRatingLevel($query, $level)
    {
        return $query->where('rating_level', $level);
    }

    /**
     * 作用域：按评分范围筛选
     */
    public function scopeScoreRange($query, $minScore, $maxScore)
    {
        return $query->whereBetween('overall_score', [$minScore, $maxScore]);
    }

    /**
     * 作用域：按商家筛选
     */
    public function scopeMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }

    /**
     * 作用域：搜索
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->whereHas('merchant', function ($merchantQuery) use ($keyword) {
            $merchantQuery->where('name', 'like', "%{$keyword}%")
                         ->orWhere('email', 'like', "%{$keyword}%");
        });
    }

    /**
     * 更新评分
     */
    public function updateRating()
    {
        $records = $this->records()->where('is_verified', true);
        
        if ($records->count() === 0) {
            return;
        }

        $this->overall_score = $records->avg('overall_score');
        $this->service_score = $records->avg('service_score');
        $this->quality_score = $records->avg('quality_score');
        $this->delivery_score = $records->avg('delivery_score');
        $this->communication_score = $records->avg('communication_score');
        
        $this->total_reviews = $records->count();
        $this->positive_reviews = $records->where('review_type', 'positive')->count();
        $this->neutral_reviews = $records->where('review_type', 'neutral')->count();
        $this->negative_reviews = $records->where('review_type', 'negative')->count();
        
        $this->rating_level = $this->calculateRatingLevel($this->overall_score);
        $this->last_updated = now();
        
        $this->save();
    }

    /**
     * 计算评级等级
     */
    public function calculateRatingLevel($score): string
    {
        if ($score >= 4.8) return 'A+';
        if ($score >= 4.5) return 'A';
        if ($score >= 4.2) return 'B+';
        if ($score >= 3.8) return 'B';
        if ($score >= 3.5) return 'C+';
        if ($score >= 3.0) return 'C';
        return 'D';
    }

    /**
     * 获取评级等级标签
     */
    public function getRatingLevelLabelAttribute(): string
    {
        return match($this->rating_level) {
            'A+' => '优秀+',
            'A' => '优秀',
            'B+' => '良好+',
            'B' => '良好',
            'C+' => '一般+',
            'C' => '一般',
            'D' => '较差',
            default => '未知',
        };
    }

    /**
     * 获取评级等级颜色
     */
    public function getRatingLevelColorAttribute(): string
    {
        return match($this->rating_level) {
            'A+', 'A' => 'green',
            'B+', 'B' => 'blue',
            'C+', 'C' => 'yellow',
            'D' => 'red',
            default => 'gray',
        };
    }

    /**
     * 获取好评率
     */
    public function getPositiveRateAttribute(): float
    {
        if ($this->total_reviews === 0) {
            return 0;
        }
        return round(($this->positive_reviews / $this->total_reviews) * 100, 2);
    }

    /**
     * 获取差评率
     */
    public function getNegativeRateAttribute(): float
    {
        if ($this->total_reviews === 0) {
            return 0;
        }
        return round(($this->negative_reviews / $this->total_reviews) * 100, 2);
    }

    /**
     * 生成改进建议
     */
    public function generateImprovementSuggestions(): array
    {
        $suggestions = [];
        
        if ($this->service_score < 4.0) {
            $suggestions[] = '提升客户服务质量，加强客服培训';
        }
        
        if ($this->quality_score < 4.0) {
            $suggestions[] = '改善商品质量，严格把控商品标准';
        }
        
        if ($this->delivery_score < 4.0) {
            $suggestions[] = '优化配送流程，提高配送效率';
        }
        
        if ($this->communication_score < 4.0) {
            $suggestions[] = '加强与客户的沟通，及时回复客户问题';
        }
        
        if ($this->negative_reviews > $this->positive_reviews) {
            $suggestions[] = '重点关注差评问题，制定改进计划';
        }
        
        return $suggestions;
    }

    /**
     * 获取或创建商家的信用评级
     */
    public static function getOrCreateForMerchant($merchantId): self
    {
        return self::firstOrCreate(
            ['merchant_id' => $merchantId],
            [
                'overall_score' => 0,
                'service_score' => 0,
                'quality_score' => 0,
                'delivery_score' => 0,
                'communication_score' => 0,
                'rating_level' => 'C',
            ]
        );
    }
}
