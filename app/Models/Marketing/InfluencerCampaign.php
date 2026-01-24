<?php

namespace App\Models\Marketing;

use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InfluencerCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'title',
        'description',
        'campaign_type',
        'status',
        'campaign_objectives',
        'target_audience',
        'platforms',
        'content_requirements',
        'influencer_requirements',
        'budget_details',
        'timeline',
        'deliverables',
        'legal_requirements',
        'campaign_assets',
        'measurement_kpis',
        'total_influencers',
        'total_budget',
        'total_spent',
        'total_content_pieces',
        'total_reach',
        'total_engagement',
        'total_conversions',
        'average_engagement_rate',
        'conversion_rate',
        'return_on_investment',
        'launched_at',
        'completed_at',
        'paused_at',
        'resumed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'campaign_objectives' => 'array',
        'target_audience' => 'array',
        'platforms' => 'array',
        'content_requirements' => 'array',
        'influencer_requirements' => 'array',
        'budget_details' => 'array',
        'timeline' => 'array',
        'deliverables' => 'array',
        'legal_requirements' => 'array',
        'campaign_assets' => 'array',
        'measurement_kpis' => 'array',
        'total_budget' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'average_engagement_rate' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
        'return_on_investment' => 'decimal:2',
        'launched_at' => 'datetime',
        'completed_at' => 'datetime',
        'paused_at' => 'datetime',
        'resumed_at' => 'datetime',
        'deliverables.content_approval' => 'boolean',
        'deliverables.performance_reports' => 'boolean',
        'deliverables.usage_rights' => 'boolean',
        'deliverables.exclusivity_period' => 'integer',
        'deliverables.content_ownership' => 'boolean',
        'legal_requirements.contract_required' => 'boolean',
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaused($query)
    {
        return $query->where('status', 'paused');
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('campaign_type', $type);
    }

    public function scopeByPlatform($query, $platform)
    {
        return $query->whereJsonContains('platforms', $platform);
    }

    public function scopeWithHighBudget($query)
    {
        return $query->where('total_budget', '>=', 10000);
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'active')
                    ->where('launched_at', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('completed_at')
                          ->orWhere('completed_at', '>', now());
                    });
    }

    // Methods
    public function launch()
    {
        $this->update([
            'status' => 'active',
            'launched_at' => now(),
        ]);
    }

    public function pause()
    {
        $this->update([
            'status' => 'paused',
            'paused_at' => now(),
        ]);
    }

    public function resume()
    {
        $this->update([
            'status' => 'active',
            'resumed_at' => now(),
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function calculateMetrics()
    {
        if ($this->total_reach > 0) {
            $this->average_engagement_rate = ($this->total_engagement / $this->total_reach) * 100;
        }

        if ($this->total_engagement > 0) {
            $this->conversion_rate = ($this->total_conversions / $this->total_engagement) * 100;
        }

        if ($this->total_spent > 0) {
            // Mock ROI calculation - in real implementation this would be based on actual revenue
            $revenue = $this->total_conversions * 5000; // Assuming 5,000 per conversion
            $this->return_on_investment = (($revenue - $this->total_spent) / $this->total_spent) * 100;
        }

        $this->save();
    }

    public function getBudgetUtilizationAttribute()
    {
        return $this->total_budget > 0 
            ? ($this->total_spent / $this->total_budget) * 100 
            : 0;
    }

    public function getRemainingBudgetAttribute()
    {
        return $this->total_budget - $this->total_spent;
    }

    public function isOverBudget()
    {
        return $this->total_spent > $this->total_budget;
    }

    public function getDurationAttribute()
    {
        $start = $this->timeline['start_date'] ?? null;
        $end = $this->timeline['end_date'] ?? null;
        
        if ($start && $end) {
            return \Carbon\Carbon::parse($start)->diffInDays(\Carbon\Carbon::parse($end));
        }
        
        return null;
    }

    public function getDaysRemainingAttribute()
    {
        $end = $this->timeline['end_date'] ?? null;
        
        if ($end && \Carbon\Carbon::parse($end) > now()) {
            return now()->diffInDays(\Carbon\Carbon::parse($end));
        }
        
        return 0;
    }

    public function getTypeDisplayNameAttribute()
    {
        return match($this->campaign_type) {
            'property_promotion' => 'ترويج العقار',
            'neighborhood_showcase' => 'عرض الحي',
            'lifestyle_content' => 'محتوى نمط الحياة',
            'event_coverage' => 'تغطية الفعاليات',
            'testimonial' => 'شهادة العملاء',
            'comparison' => 'مقارنة',
            default => $this->campaign_type,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'active' => 'green',
            'completed' => 'gray',
            'paused' => 'orange',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getPlatformListAttribute()
    {
        return implode(', ', array_map(function($platform) {
            return match($platform) {
                'instagram' => 'Instagram',
                'youtube' => 'YouTube',
                'tiktok' => 'TikTok',
                'twitter' => 'Twitter',
                'facebook' => 'Facebook',
                'linkedin' => 'LinkedIn',
                'snapchat' => 'Snapchat',
                default => $platform,
            };
        }, $this->platforms ?? []));
    }

    public function getInfluencerTierDistributionAttribute()
    {
        // Mock distribution - in real implementation this would track actual influencer tiers
        return [
            'nano' => rand(10, 30),
            'micro' => rand(30, 50),
            'macro' => rand(15, 35),
            'mega' => rand(5, 15),
        ];
    }

    public function getContentPerformanceAttribute()
    {
        return [
            'total_pieces' => $this->total_content_pieces,
            'video_content' => rand(20, 40),
            'image_content' => rand(30, 50),
            'story_content' => rand(15, 30),
            'carousel_content' => rand(10, 25),
            'live_content' => rand(5, 15),
            'average_engagement_per_piece' => $this->total_content_pieces > 0 ? 
                round($this->total_engagement / $this->total_content_pieces) : 0,
        ];
    }

    public function getAudienceInsightsAttribute()
    {
        return [
            'demographics' => [
                'age_groups' => [
                    '18-24' => rand(15, 25),
                    '25-34' => rand(30, 45),
                    '35-44' => rand(20, 35),
                    '45-54' => rand(10, 20),
                    '55+' => rand(5, 10),
                ],
                'genders' => [
                    'male' => rand(45, 55),
                    'female' => rand(45, 55),
                ],
                'locations' => [
                    'الرياض' => rand(20, 35),
                    'جدة' => rand(15, 25),
                    'الدمام' => rand(10, 20),
                    'مكة' => rand(8, 15),
                    'أخرى' => rand(15, 25),
                ],
            ],
            'interests' => [
                'عقارات' => rand(60, 80),
                'تصميم داخلي' => rand(40, 60),
                'استثمار' => rand(30, 50),
                'أسلوب حياة' => rand(25, 45),
                'سفر' => rand(20, 40),
            ],
        ];
    }

    public function getPlatformPerformanceAttribute()
    {
        $performance = [];
        
        foreach ($this->platforms ?? [] as $platform) {
            $performance[$platform] = [
                'reach' => rand(10000, 100000),
                'engagement' => rand(1000, 10000),
                'conversions' => rand(10, 100),
                'cost' => rand(500, 5000),
                'roi' => rand(150, 400),
            ];
        }
        
        return $performance;
    }

    public function getInfluencerPerformanceAttribute()
    {
        return [
            'total_influencers' => $this->total_influencers,
            'active_influencers' => rand($this->total_influencers * 0.7, $this->total_influencers),
            'average_engagement_rate' => rand(3, 9) . '%',
            'top_performing_influencers' => rand(3, 10),
            'influencer_satisfaction_score' => rand(3.5, 4.8) . '/5',
            'content_approval_rate' => rand(85, 98) . '%',
            'on_time_delivery_rate' => rand(80, 95) . '%',
        ];
    }

    public function getConversionFunnelAttribute()
    {
        return [
            'awareness' => [
                'reach' => $this->total_reach,
                'impressions' => $this->total_reach * 2,
                'frequency' => 2.0,
            ],
            'interest' => [
                'engagement' => $this->total_engagement,
                'clicks' => $this->total_engagement * 0.3,
                'engagement_rate' => $this->average_engagement_rate . '%',
            ],
            'consideration' => [
                'landing_page_views' => $this->total_engagement * 0.2,
                'time_on_page' => rand(120, 300) . ' seconds',
                'bounce_rate' => rand(30, 60) . '%',
            ],
            'conversion' => [
                'leads' => $this->total_conversions,
                'customers' => $this->total_conversions * 0.3,
                'conversion_rate' => $this->conversion_rate . '%',
            ],
        ];
    }

    public function getCostAnalysisAttribute()
    {
        $costPerInfluencer = $this->total_influencers > 0 ? 
            $this->total_spent / $this->total_influencers : 0;
        
        $costPerContent = $this->total_content_pieces > 0 ? 
            $this->total_spent / $this->total_content_pieces : 0;
        
        $costPerReach = $this->total_reach > 0 ? 
            $this->total_spent / $this->total_reach : 0;
        
        $costPerEngagement = $this->total_engagement > 0 ? 
            $this->total_spent / $this->total_engagement : 0;
        
        $costPerConversion = $this->total_conversions > 0 ? 
            $this->total_spent / $this->total_conversions : 0;
        
        return [
            'total_spent' => $this->total_spent,
            'cost_per_influencer' => round($costPerInfluencer, 2),
            'cost_per_content_piece' => round($costPerContent, 2),
            'cost_per_impression' => round($costPerReach * 1000, 2), // CPM
            'cost_per_engagement' => round($costPerEngagement, 2),
            'cost_per_conversion' => round($costPerConversion, 2),
            'budget_efficiency' => $this->budget_utilization . '%',
        ];
    }

    public function getBrandSafetyMetricsAttribute()
    {
        return [
            'content_compliance_rate' => rand(90, 99) . '%',
            'brand_mention_accuracy' => rand(85, 95) . '%',
            'hashtag_usage_compliance' => rand(80, 95) . '%',
            'disclosure_compliance' => rand(95, 100) . '%',
            'content_quality_score' => rand(4.0, 4.8) . '/5',
            'negative_sentiment_rate' => rand(1, 5) . '%',
            'brand_safety_incidents' => rand(0, 2),
        ];
    }

    public function getCompetitiveAnalysisAttribute()
    {
        return [
            'market_position' => rand(1, 10),
            'market_share' => rand(5, 25) . '%',
            'competitor_campaigns' => rand(3, 8),
            'average_competitor_spend' => $this->total_budget * rand(0.8, 1.5),
            'competitive_advantage' => $this->getCompetitiveAdvantage(),
            'performance_vs_competitors' => $this->getPerformanceVsCompetitors(),
        ];
    }

    private function getCompetitiveAdvantage()
    {
        $advantages = [
            'جودة المحتوى العالي',
            'اختيار المؤثرين المناسبين',
            'استراتيجية المنصات المتعددة',
            'ميزانية تنافسية',
            'توقيت الحملة المثالي',
            'محتوى أصلي ومبتكر',
        ];
        
        return array_rand(array_flip($advantages), 3);
    }

    private function getPerformanceVsCompetitors()
    {
        return [
            'engagement_rate' => $this->average_engagement_rate > 5 ? 'أعلى' : 'أدنى',
            'reach' => $this->total_reach > 50000 ? 'أعلى' : 'أدنى',
            'conversion_rate' => $this->conversion_rate > 3 ? 'أعلى' : 'أدنى',
            'roi' => $this->return_on_investment > 200 ? 'أعلى' : 'أدنى',
        ];
    }

    public function getOptimizationRecommendationsAttribute()
    {
        $recommendations = [];

        if ($this->average_engagement_rate < 3) {
            $recommendations[] = 'تحسين جودة المحتوى لزيادة التفاعل';
        }

        if ($this->conversion_rate < 2) {
            $recommendations[] = 'تحسين دعوات العمل لزيادة التحويلات';
        }

        if ($this->budget_utilization > 90) {
            $recommendations[] = 'مراجعة الميزانية وتحسين كفاءة الإنفاق';
        }

        if ($this->total_influencers < 5) {
            $recommendations[] = 'زيادة عدد المؤثرين لتوسيع نطاق الوصول';
        }

        if (count($this->platforms) < 3) {
            $recommendations[] = 'التوسع في منصات إضافية لزيادة التغطية';
        }

        if ($this->return_on_investment < 150) {
            $recommendations[] = 'إعادة تقييم استراتيجية الحملة لتحسين العائد';
        }

        return $recommendations;
    }

    public function getProjectedPerformanceAttribute()
    {
        $currentProgress = $this->daysRemaining > 0 ? 
            (1 - $this->daysRemaining / max($this->duration, 1)) : 1;
        
        return [
            'projected_reach' => round($this->total_reach / max($currentProgress, 0.1)),
            'projected_engagement' => round($this->total_engagement / max($currentProgress, 0.1)),
            'projected_conversions' => round($this->total_conversions / max($currentProgress, 0.1)),
            'projected_roi' => round($this->return_on_investment / max($currentProgress, 0.1)),
            'projected_budget_utilization' => min(100, round($this->budget_utilization / max($currentProgress, 0.1))),
        ];
    }

    public function canBeLaunched()
    {
        return in_array($this->status, ['pending']) && 
               !empty($this->title) &&
               !empty($this->budget_details['total_budget']) &&
               !empty($this->timeline['start_date']);
    }

    public function isCompleted()
    {
        return $this->status === 'completed' || 
               ($this->timeline['end_date'] && 
                \Carbon\Carbon::parse($this->timeline['end_date']) < now());
    }

    // Events
    protected static function booted()
    {
        static::creating(function ($campaign) {
            if (auth()->check()) {
                $campaign->created_by = auth()->id();
            }
        });

        static::updating(function ($campaign) {
            if (auth()->check()) {
                $campaign->updated_by = auth()->id();
            }
        });

        static::saving(function ($campaign) {
            // Auto-complete campaigns that have passed their end date
            if ($campaign->status === 'active' && 
                $campaign->timeline['end_date'] && 
                \Carbon\Carbon::parse($campaign->timeline['end_date']) < now()) {
                $campaign->status = 'completed';
                $campaign->completed_at = now();
            }
        });
    }
}
