<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitorData extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'website',
        'market_share',
        'avg_price',
        'min_price',
        'max_price',
        'traffic_rank',
        'monthly_visitors',
        'conversion_rate',
        'conversion_trend',
        'customer_rating',
        'review_count',
        'growth_rate',
        'growth_trend',
        'strengths',
        'weaknesses',
        'features',
        'target_audience',
        'marketing_channels',
        'technology_stack',
        'founded_year',
        'employee_count',
        'revenue',
        'funding',
        'locations',
        'specializations',
        'competitive_advantages',
        'recent_activities',
        'market_position',
        'pricing_strategy',
        'customer_service',
        'innovation_score',
        'digital_presence',
        'social_media_followers',
        'last_updated',
        'data_source',
        'metadata',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'market_share' => 'decimal:2',
        'avg_price' => 'decimal:2',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'monthly_visitors' => 'integer',
        'conversion_rate' => 'decimal:2',
        'customer_rating' => 'decimal:2',
        'review_count' => 'integer',
        'growth_rate' => 'decimal:2',
        'strengths' => 'array',
        'weaknesses' => 'array',
        'features' => 'array',
        'target_audience' => 'array',
        'marketing_channels' => 'array',
        'technology_stack' => 'array',
        'employee_count' => 'integer',
        'revenue' => 'decimal:2',
        'funding' => 'decimal:2',
        'locations' => 'array',
        'specializations' => 'array',
        'competitive_advantages' => 'array',
        'recent_activities' => 'array',
        'innovation_score' => 'decimal:2',
        'digital_presence' => 'array',
        'social_media_followers' => 'array',
        'last_updated' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function marketTrends(): HasMany
    {
        return $this->hasMany(MarketTrend::class);
    }

    public function scopeByMarketPosition($query, $position)
    {
        return $query->where('market_position', $position);
    }

    public function scopeByPricingStrategy($query, $strategy)
    {
        return $query->where('pricing_strategy', $strategy);
    }

    public function scopeBySpecialization($query, $specialization)
    {
        return $query->whereJsonContains('specializations', $specialization);
    }

    public function scopeHighGrowth($query, $threshold = 10)
    {
        return $query->where('growth_rate', '>=', $threshold);
    }

    public function scopeHighRating($query, $threshold = 4.0)
    {
        return $query->where('customer_rating', '>=', $threshold);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('last_updated', '>', now()->subDays($days));
    }

    public function isMarketLeader()
    {
        return $this->market_position === 'leader';
    }

    public function isMarketChallenger()
    {
        return $this->market_position === 'challenger';
    }

    public function isMarketFollower()
    {
        return $this->market_position === 'follower';
    }

    public function isMarketNicher()
    {
        return $this->market_position === 'nicher';
    }

    public function isPremiumPricing()
    {
        return $this->pricing_strategy === 'premium';
    }

    public function isValuePricing()
    {
        return $this->pricing_strategy === 'value';
    }

    public function isEconomyPricing()
    {
        return $this->pricing_strategy === 'economy';
    }

    public function isGrowing()
    {
        return $this->growth_rate > 0;
    }

    public function isHighlyRated()
    {
        return $this->customer_rating >= 4.0;
    }

    public function getMarketPositionLabel()
    {
        return match($this->market_position) {
            'leader' => 'Market Leader',
            'challenger' => 'Market Challenger',
            'follower' => 'Market Follower',
            'nicher' => 'Market Nicher',
            default => 'Unknown'
        };
    }

    public function getPricingStrategyLabel()
    {
        return match($this->pricing_strategy) {
            'premium' => 'Premium Pricing',
            'value' => 'Value Pricing',
            'economy' => 'Economy Pricing',
            'competitive' => 'Competitive Pricing',
            default => 'Unknown'
        };
    }

    public function getGrowthTrendLabel()
    {
        return match($this->growth_trend) {
            'accelerating' => 'Accelerating',
            'stable' => 'Stable',
            'decelerating' => 'Decelerating',
            'declining' => 'Declining',
            default => 'Unknown'
        };
    }

    public function getConversionTrendLabel()
    {
        return match($this->conversion_trend) {
            'improving' => 'Improving',
            'stable' => 'Stable',
            'declining' => 'Declining',
            default => 'Unknown'
        };
    }

    public function getPriceRange()
    {
        if ($this->min_price && $this->max_price) {
            return number_format($this->min_price, 2) . ' - ' . number_format($this->max_price, 2);
        }
        
        return number_format($this->avg_price, 2);
    }

    public function getAge()
    {
        return $this->founded_year ? now()->year - $this->founded_year : null;
    }

    public function getAgeCategory()
    {
        $age = $this->getAge();
        
        if ($age === null) return 'unknown';
        if ($age <= 2) return 'startup';
        if ($age <= 5) return 'young';
        if ($age <= 10) return 'established';
        if ($age <= 20) return 'mature';
        return 'legacy';
    }

    public function getSizeCategory()
    {
        if ($this->employee_count === null) return 'unknown';
        if ($this->employee_count <= 10) return 'micro';
        if ($this->employee_count <= 50) return 'small';
        if ($this->employee_count <= 250) return 'medium';
        if ($this->employee_count <= 1000) return 'large';
        return 'enterprise';
    }

    public function getRevenueCategory()
    {
        if ($this->revenue === null) return 'unknown';
        if ($this->revenue <= 1000000) return 'startup';
        if ($this->revenue <= 10000000) return 'small';
        if ($this->revenue <= 100000000) return 'medium';
        if ($this->revenue <= 1000000000) return 'large';
        return 'enterprise';
    }

    public function getCompetitiveScore()
    {
        $score = 0;
        
        // Market share (30%)
        $score += min(30, $this->market_share * 3);
        
        // Growth rate (20%)
        $score += min(20, max(0, $this->growth_rate) * 2);
        
        // Customer rating (20%)
        $score += min(20, $this->customer_rating * 4);
        
        // Innovation score (15%)
        $score += min(15, $this->innovation_score * 1.5);
        
        // Digital presence (15%)
        $digitalScore = $this->calculateDigitalPresenceScore();
        $score += min(15, $digitalScore * 0.15);
        
        return min(100, $score);
    }

    private function calculateDigitalPresenceScore()
    {
        $score = 0;
        
        if ($this->website) $score += 20;
        if ($this->monthly_visitors > 10000) $score += 20;
        if ($this->customer_rating >= 4.0) $score += 20;
        if (count($this->social_media_followers ?? []) > 0) $score += 20;
        if ($this->digital_presence['has_mobile_app'] ?? false) $score += 20;
        
        return $score;
    }

    public function getThreatLevel()
    {
        $score = $this->getCompetitiveScore();
        
        if ($score >= 80) return 'very_high';
        if ($score >= 60) return 'high';
        if ($score >= 40) return 'medium';
        if ($score >= 20) return 'low';
        return 'very_low';
    }

    public function getOpportunityLevel()
    {
        $score = $this->getCompetitiveScore();
        
        if ($score <= 20) return 'very_high';
        if ($score <= 40) return 'high';
        if ($score <= 60) return 'medium';
        if ($score <= 80) return 'low';
        return 'very_low';
    }

    public function getStrengthsSummary()
    {
        return implode(', ', array_slice($this->strengths ?? [], 0, 3));
    }

    public function getWeaknessesSummary()
    {
        return implode(', ', array_slice($this->weaknesses ?? [], 0, 3));
    }

    public function getSpecializationsSummary()
    {
        return implode(', ', array_slice($this->specializations ?? [], 0, 3));
    }

    public function getRecentActivitiesSummary()
    {
        return implode(', ', array_slice($this->recent_activities ?? [], 0, 3));
    }

    public function getMarketingChannelsSummary()
    {
        return implode(', ', array_keys($this->marketing_channels ?? []));
    }

    public function getTechnologyStackSummary()
    {
        return implode(', ', array_slice($this->technology_stack ?? [], 0, 3));
    }

    public function getSocialMediaSummary()
    {
        $followers = $this->social_media_followers ?? [];
        $total = array_sum($followers);
        
        if ($total > 1000000) return '1M+ followers';
        if ($total > 100000) return '100K+ followers';
        if ($total > 10000) return '10K+ followers';
        if ($total > 1000) return '1K+ followers';
        
        return count($followers) . ' platforms';
    }

    public function getCompetitiveAdvantagesSummary()
    {
        return implode(', ', array_slice($this->competitive_advantages ?? [], 0, 3));
    }

    public function getTargetAudienceSummary()
    {
        $audience = $this->target_audience ?? [];
        
        $summary = [];
        if (isset($audience['age_range'])) $summary[] = $audience['age_range'];
        if (isset($audience['income_level'])) $summary[] = $audience['income_level'];
        if (isset($audience['geography'])) $summary[] = $audience['geography'];
        
        return implode(', ', $summary);
    }

    public function compareWith($otherCompetitor)
    {
        return [
            'market_share_diff' => $this->market_share - $otherCompetitor->market_share,
            'price_diff' => $this->avg_price - $otherCompetitor->avg_price,
            'rating_diff' => $this->customer_rating - $otherCompetitor->customer_rating,
            'growth_diff' => $this->growth_rate - $otherCompetitor->growth_rate,
            'conversion_diff' => $this->conversion_rate - $otherCompetitor->conversion_rate,
            'score_diff' => $this->getCompetitiveScore() - $otherCompetitor->getCompetitiveScore(),
            'better_performer' => $this->getCompetitiveScore() > $otherCompetitor->getCompetitiveScore() ? $this->name : $otherCompetitor->name
        ];
    }

    public function generateReport()
    {
        return [
            'competitor_info' => [
                'name' => $this->name,
                'website' => $this->website,
                'founded_year' => $this->founded_year,
                'age' => $this->getAge(),
                'age_category' => $this->getAgeCategory(),
                'employee_count' => $this->employee_count,
                'size_category' => $this->getSizeCategory(),
                'revenue' => $this->revenue,
                'revenue_category' => $this->getRevenueCategory(),
                'locations' => $this->locations,
                'last_updated' => $this->last_updated?->format('Y-m-d H:i:s')
            ],
            'market_position' => [
                'position' => $this->market_position,
                'position_label' => $this->getMarketPositionLabel(),
                'market_share' => $this->market_share,
                'growth_rate' => $this->growth_rate,
                'growth_trend' => $this->growth_trend,
                'growth_trend_label' => $this->getGrowthTrendLabel(),
                'competitive_score' => $this->getCompetitiveScore(),
                'threat_level' => $this->getThreatLevel(),
                'opportunity_level' => $this->getOpportunityLevel()
            ],
            'pricing' => [
                'strategy' => $this->pricing_strategy,
                'strategy_label' => $this->getPricingStrategyLabel(),
                'avg_price' => $this->avg_price,
                'min_price' => $this->min_price,
                'max_price' => $this->max_price,
                'price_range' => $this->getPriceRange()
            ],
            'performance' => [
                'traffic_rank' => $this->traffic_rank,
                'monthly_visitors' => $this->monthly_visitors,
                'conversion_rate' => $this->conversion_rate,
                'conversion_trend' => $this->conversion_trend,
                'conversion_trend_label' => $this->getConversionTrendLabel(),
                'customer_rating' => $this->customer_rating,
                'review_count' => $this->review_count
            ],
            'offerings' => [
                'specializations' => $this->specializations,
                'specializations_summary' => $this->getSpecializationsSummary(),
                'features' => $this->features,
                'competitive_advantages' => $this->competitive_advantages,
                'competitive_advantages_summary' => $this->getCompetitiveAdvantagesSummary()
            ],
            'strengths_weaknesses' => [
                'strengths' => $this->strengths,
                'strengths_summary' => $this->getStrengthsSummary(),
                'weaknesses' => $this->weaknesses,
                'weaknesses_summary' => $this->getWeaknessesSummary()
            ],
            'digital_presence' => [
                'website' => $this->website,
                'social_media_followers' => $this->social_media_followers,
                'social_media_summary' => $this->getSocialMediaSummary(),
                'digital_presence_data' => $this->digital_presence,
                'innovation_score' => $this->innovation_score
            ],
            'targeting' => [
                'target_audience' => $this->target_audience,
                'target_audience_summary' => $this->getTargetAudienceSummary(),
                'marketing_channels' => $this->marketing_channels,
                'marketing_channels_summary' => $this->getMarketingChannelsSummary()
            ],
            'activities' => [
                'recent_activities' => $this->recent_activities,
                'recent_activities_summary' => $this->getRecentActivitiesSummary()
            ],
            'technology' => [
                'technology_stack' => $this->technology_stack,
                'technology_stack_summary' => $this->getTechnologyStackSummary()
            ],
            'metadata' => $this->metadata,
            'generated_at' => now()->toDateString()
        ];
    }

    public function exportToJson()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'website' => $this->website,
            'market_share' => $this->market_share,
            'avg_price' => $this->avg_price,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'traffic_rank' => $this->traffic_rank,
            'monthly_visitors' => $this->monthly_visitors,
            'conversion_rate' => $this->conversion_rate,
            'conversion_trend' => $this->conversion_trend,
            'customer_rating' => $this->customer_rating,
            'review_count' => $this->review_count,
            'growth_rate' => $this->growth_rate,
            'growth_trend' => $this->growth_trend,
            'strengths' => $this->strengths,
            'weaknesses' => $this->weaknesses,
            'features' => $this->features,
            'target_audience' => $this->target_audience,
            'marketing_channels' => $this->marketing_channels,
            'technology_stack' => $this->technology_stack,
            'founded_year' => $this->founded_year,
            'employee_count' => $this->employee_count,
            'revenue' => $this->revenue,
            'funding' => $this->funding,
            'locations' => $this->locations,
            'specializations' => $this->specializations,
            'competitive_advantages' => $this->competitive_advantages,
            'recent_activities' => $this->recent_activities,
            'market_position' => $this->market_position,
            'pricing_strategy' => $this->pricing_strategy,
            'customer_service' => $this->customer_service,
            'innovation_score' => $this->innovation_score,
            'digital_presence' => $this->digital_presence,
            'social_media_followers' => $this->social_media_followers,
            'last_updated' => $this->last_updated,
            'data_source' => $this->data_source,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
