<?php

namespace App\Models\Marketing;

use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RetargetingAudience extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'name',
        'description',
        'audience_type',
        'platform',
        'retargeting_type',
        'status',
        'targeting_criteria',
        'audience_rules',
        'time_settings',
        'budget_settings',
        'creative_settings',
        'pixel_tracking',
        'audience_segments',
        'performance_goals',
        'integration_settings',
        'audience_size',
        'total_campaigns',
        'conversion_rate',
        'activated_at',
        'paused_at',
        'resumed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'targeting_criteria' => 'array',
        'audience_rules' => 'array',
        'time_settings' => 'array',
        'budget_settings' => 'array',
        'creative_settings' => 'array',
        'pixel_tracking' => 'array',
        'audience_segments' => 'array',
        'performance_goals' => 'array',
        'integration_settings' => 'array',
        'budget_settings.daily_budget' => 'decimal:2',
        'budget_settings.total_budget' => 'decimal:2',
        'budget_settings.max_cpc' => 'decimal:2',
        'budget_settings.max_cpm' => 'decimal:2',
        'budget_settings.target_cpa' => 'decimal:2',
        'budget_settings.target_roas' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
        'activated_at' => 'datetime',
        'paused_at' => 'datetime',
        'resumed_at' => 'datetime',
        'time_settings.frequency_capping' => 'integer',
        'performance_goals.click_through_rate' => 'decimal:2',
        'performance_goals.conversion_rate' => 'decimal:2',
        'performance_goals.cost_per_conversion' => 'decimal:2',
        'performance_goals.return_on_ad_spend' => 'decimal:2',
        'performance_goals.impression_share' => 'decimal:2',
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

    public function scopePaused($query)
    {
        return $query->where('status', 'paused');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('audience_type', $type);
    }

    public function scopePixelBased($query)
    {
        return $query->where('retargeting_type', 'pixel_based');
    }

    public function scopeListBased($query)
    {
        return $query->where('retargeting_type', 'list_based');
    }

    public function scopeDynamic($query)
    {
        return $query->where('retargeting_type', 'dynamic');
    }

    public function scopeWithHighConversion($query)
    {
        return $query->where('conversion_rate', '>=', 5);
    }

    // Methods
    public function activate()
    {
        $this->update([
            'status' => 'active',
            'activated_at' => now(),
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

    public function getTypeDisplayNameAttribute()
    {
        return match($this->audience_type) {
            'website_visitors' => 'زوار الموقع',
            'property_viewers' => 'مشاهدي العقار',
            'cart_abandoners' => 'المهملين',
            'search_users' => 'الباحثين',
            'email_subscribers' => 'المشتركين في البريد',
            'social_engagers' => 'المتفاعلين مع وسائل التواصل',
            default => $this->audience_type,
        };
    }

    public function getPlatformDisplayNameAttribute()
    {
        return match($this->platform) {
            'google_ads' => 'Google Ads',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'linkedin' => 'LinkedIn',
            'twitter' => 'Twitter',
            'tiktok' => 'TikTok',
            'pinterest' => 'Pinterest',
            default => $this->platform,
        };
    }

    public function getRetargetingTypeDisplayNameAttribute()
    {
        return match($this->retargeting_type) {
            'pixel_based' => 'قائم على البيكسل',
            'list_based' => 'قائم على القائمة',
            'dynamic' => 'ديناميكي',
            'hybrid' => 'هجين',
            default => $this->retargeting_type,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'paused' => 'yellow',
            'draft' => 'gray',
            'archived' => 'red',
            default => 'gray',
        };
    }

    public function getBudgetUtilizationAttribute()
    {
        $totalBudget = $this->budget_settings['total_budget'] ?? 0;
        $dailyBudget = $this->budget_settings['daily_budget'] ?? 0;
        
        // Mock calculation - in real implementation this would track actual spend
        $spent = $totalBudget * 0.3; // Assume 30% spent
        
        return $totalBudget > 0 ? ($spent / $totalBudget) * 100 : 0;
    }

    public function getRemainingBudgetAttribute()
    {
        $totalBudget = $this->budget_settings['total_budget'] ?? 0;
        $utilization = $this->budget_utilization / 100;
        
        return $totalBudget * (1 - $utilization);
    }

    public function getDailySpendAttribute()
    {
        $dailyBudget = $this->budget_settings['daily_budget'] ?? 0;
        
        // Mock calculation - in real implementation this would track actual daily spend
        return $dailyBudget * 0.8; // Assume 80% of daily budget used
    }

    public function getAudienceHealthScoreAttribute()
    {
        $score = 0;
        
        // Audience size (25%)
        if ($this->audience_size > 1000) {
            $score += 25;
        } elseif ($this->audience_size > 500) {
            $score += 15;
        } elseif ($this->audience_size > 100) {
            $score += 10;
        }
        
        // Conversion rate (35%)
        if ($this->conversion_rate > 5) {
            $score += 35;
        } elseif ($this->conversion_rate > 3) {
            $score += 25;
        } elseif ($this->conversion_rate > 1) {
            $score += 15;
        }
        
        // Frequency capping (20%)
        $frequencyCap = $this->time_settings['frequency_capping'] ?? 0;
        if ($frequencyCap > 0 && $frequencyCap <= 5) {
            $score += 20;
        } elseif ($frequencyCap > 5 && $frequencyCap <= 10) {
            $score += 10;
        }
        
        // Retargeting window (20%)
        $window = $this->time_settings['retargeting_window'] ?? '30_days';
        if (in_array($window, ['7_days', '14_days', '30_days'])) {
            $score += 20;
        } elseif (in_array($window, ['60_days', '90_days'])) {
            $score += 10;
        }
        
        return $score;
    }

    public function getAudienceHealthStatusAttribute()
    {
        $score = $this->audience_health_score;
        
        return match(true) {
            $score >= 80 => 'ممتاز',
            $score >= 60 => 'جيد',
            $score >= 40 => 'متوسط',
            default => 'يحتاج تحسين',
        };
    }

    public function getTargetingComplexityAttribute()
    {
        $complexity = 0;
        
        // Inclusion rules
        $inclusionRules = count($this->audience_rules['inclusion_rules'] ?? []);
        $complexity += $inclusionRules * 2;
        
        // Exclusion rules
        $exclusionRules = count($this->audience_rules['exclusion_rules'] ?? []);
        $complexity += $exclusionRules * 3;
        
        // Segments
        $segments = count($this->audience_segments ?? []);
        $complexity += $segments * 5;
        
        return match(true) {
            $complexity <= 5 => 'بسيط',
            $complexity <= 15 => 'متوسط',
            $complexity <= 30 => 'معقد',
            default => 'معقد جداً',
        };
    }

    public function getPerformanceMetricsAttribute()
    {
        return [
            'impressions' => rand(10000, 1000000),
            'clicks' => rand(100, 10000),
            'conversions' => rand(5, 500),
            'click_through_rate' => rand(1, 10) . '%',
            'conversion_rate' => $this->conversion_rate . '%',
            'cost_per_click' => rand(1, 20),
            'cost_per_conversion' => rand(50, 500),
            'return_on_ad_spend' => rand(200, 800) . '%',
            'frequency' => rand(1, 5),
            'reach' => $this->audience_size * rand(0.5, 2),
        ];
    }

    public function getAudienceInsightsAttribute()
    {
        return [
            'demographics' => [
                'age_groups' => [
                    '18-24' => rand(10, 20),
                    '25-34' => rand(25, 40),
                    '35-44' => rand(20, 35),
                    '45-54' => rand(15, 25),
                    '55+' => rand(5, 15),
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
                    'أخرى' => rand(20, 35),
                ],
            ],
            'behavioral_patterns' => [
                'device_usage' => [
                    'desktop' => rand(40, 60),
                    'mobile' => rand(30, 50),
                    'tablet' => rand(5, 15),
                ],
                'time_of_activity' => [
                    'morning' => rand(15, 25),
                    'afternoon' => rand(20, 30),
                    'evening' => rand(35, 45),
                    'night' => rand(10, 20),
                ],
                'engagement_level' => [
                    'high' => rand(20, 40),
                    'medium' => rand(40, 60),
                    'low' => rand(10, 30),
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

    public function getConversionFunnelAttribute()
    {
        return [
            'awareness' => [
                'impressions' => rand(10000, 100000),
                'reach' => $this->audience_size * rand(0.5, 2),
                'frequency' => rand(1, 5),
            ],
            'interest' => [
                'clicks' => rand(100, 10000),
                'click_through_rate' => rand(1, 10) . '%',
                'landing_page_views' => rand(500, 5000),
            ],
            'consideration' => [
                'form_submissions' => rand(10, 100),
                'time_on_page' => rand(60, 300) . ' seconds',
                'bounce_rate' => rand(30, 70) . '%',
            ],
            'conversion' => [
                'conversions' => rand(5, 500),
                'conversion_rate' => $this->conversion_rate . '%',
                'cost_per_conversion' => rand(50, 500),
            ],
        ];
    }

    public function getPlatformPerformanceAttribute()
    {
        $performance = [];
        
        foreach (['google_ads', 'facebook', 'instagram', 'linkedin', 'twitter'] as $platform) {
            $performance[$platform] = [
                'impressions' => rand(5000, 500000),
                'clicks' => rand(50, 5000),
                'conversions' => rand(2, 250),
                'cost' => rand(100, 10000),
                'roi' => rand(150, 400),
                'quality_score' => rand(5, 10),
            ];
        }
        
        return $performance;
    }

    public function getTimePerformanceAttribute()
    {
        return [
            'hourly_breakdown' => [
                '12am-6am' => rand(5, 15),
                '6am-12pm' => rand(20, 30),
                '12pm-6pm' => rand(35, 45),
                '6pm-12am' => rand(25, 35),
            ],
            'daily_trend' => [
                'monday' => rand(10, 20),
                'tuesday' => rand(12, 22),
                'wednesday' => rand(14, 24),
                'thursday' => rand(16, 26),
                'friday' => rand(18, 28),
                'saturday' => rand(15, 25),
                'sunday' => rand(12, 22),
            ],
            'seasonal_trend' => [
                'q1' => rand(20, 30),
                'q2' => rand(25, 35),
                'q3' => rand(30, 40),
                'q4' => rand(25, 35),
            ],
        ];
    }

    public function getDevicePerformanceAttribute()
    {
        return [
            'desktop' => [
                'impressions' => rand(4000, 400000),
                'clicks' => rand(40, 4000),
                'conversions' => rand(3, 300),
                'conversion_rate' => rand(2, 8) . '%',
                'cost_per_conversion' => rand(40, 400),
            ],
            'mobile' => [
                'impressions' => rand(5000, 500000),
                'clicks' => rand(50, 5000),
                'conversions' => rand(2, 200),
                'conversion_rate' => rand(1, 6) . '%',
                'cost_per_conversion' => rand(50, 500),
            ],
            'tablet' => [
                'impressions' => rand(1000, 100000),
                'clicks' => rand(10, 1000),
                'conversions' => rand(0, 50),
                'conversion_rate' => rand(1, 5) . '%',
                'cost_per_conversion' => rand(60, 600),
            ],
        ];
    }

    public function getOptimizationRecommendationsAttribute()
    {
        $recommendations = [];

        if ($this->conversion_rate < 2) {
            $recommendations[] = [
                'type' => 'conversion',
                'priority' => 'high',
                'description' => 'تحسين صفحة الهبوط لزيادة معدل التحويل',
                'impact' => 'High',
                'effort' => 'Medium',
            ];
        }

        if ($this->audience_size < 500) {
            $recommendations[] = [
                'type' => 'audience_size',
                'priority' => 'medium',
                'description' => 'توسيع معايير الجمهور لزيادة حجم الجمهور',
                'impact' => 'Medium',
                'effort' => 'Low',
            ];
        }

        $frequencyCap = $this->time_settings['frequency_capping'] ?? 0;
        if ($frequencyCap > 10) {
            $recommendations[] = [
                'type' => 'frequency',
                'priority' => 'medium',
                'description' => 'تقليل تكرار الإعلانات لتجنب إرهاق الجمهور',
                'impact' => 'Medium',
                'effort' => 'Low',
            ];
        }

        if (empty($this->audience_segments)) {
            $recommendations[] = [
                'type' => 'segmentation',
                'priority' => 'low',
                'description' => 'إنشاء شرائح جمهور مخصصة لتحسين الاستهداف',
                'impact' => 'Medium',
                'effort' => 'High',
            ];
        }

        return $recommendations;
    }

    public function getProjectedPerformanceAttribute()
    {
        $currentMetrics = $this->performance_metrics;
        
        return [
            'projected_impressions' => $currentMetrics['impressions'] * 1.2,
            'projected_clicks' => $currentMetrics['clicks'] * 1.15,
            'projected_conversions' => $currentMetrics['conversions'] * 1.1,
            'projected_roi' => $currentMetrics['return_on_ad_spend'] * 1.05,
            'projected_cost_per_conversion' => $currentMetrics['cost_per_conversion'] * 0.9,
        ];
    }

    public function getABTestResultsAttribute()
    {
        return [
            'test_a' => [
                'name' => 'الإعلان الأصلي',
                'click_through_rate' => $this->performance_metrics['click_through_rate'],
                'conversion_rate' => $this->conversion_rate . '%',
                'cost_per_conversion' => $this->performance_metrics['cost_per_conversion'],
            ],
            'test_b' => [
                'name' => 'الإعلان المعدل',
                'click_through_rate' => rand(1, 10) . '%',
                'conversion_rate' => rand(1, 8) . '%',
                'cost_per_conversion' => rand(40, 400),
            ],
            'winner' => rand(0, 1) === 0 ? 'test_a' : 'test_b',
            'confidence' => rand(85, 98) . '%',
            'improvement' => rand(5, 25) . '%',
        ];
    }

    public function canBeActivated()
    {
        return in_array($this->status, ['draft']) && 
               !empty($this->name) &&
               !empty($this->budget_settings['daily_budget']) &&
               $this->audience_size > 0;
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isPaused()
    {
        return $this->status === 'paused';
    }

    // Events
    protected static function booted()
    {
        static::creating(function ($audience) {
            if (auth()->check()) {
                $audience->created_by = auth()->id();
            }
        });

        static::updating(function ($audience) {
            if (auth()->check()) {
                $audience->updated_by = auth()->id();
            }
        });
    }
}
