<?php

namespace App\Models\Sustainability;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WaterConservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_sustainability_id',
        'water_consumption',
        'consumption_unit',
        'water_efficiency_rating',
        'rainwater_harvesting',
        'rainwater_capacity',
        'greywater_recycling',
        'greywater_capacity',
        'low_flow_fixtures',
        'fixture_types',
        'smart_irrigation',
        'irrigation_type',
        'drip_irrigation',
        'xeriscaping',
        'native_plants',
        'leak_detection_system',
        'water_metering',
        'water_pressure_optimization',
        'hot_water_efficiency',
        'hot_water_system_type',
        'pool_cover',
        'pool_recycling_system',
        'water_treatment_system',
        'treatment_type',
        'conservation_goals',
        'monitoring_frequency',
        'assessment_date',
        'next_assessment_date',
        'assessed_by',
        'potential_savings',
        'recommendations',
        'notes',
    ];

    protected $casts = [
        'water_consumption' => 'decimal:2',
        'water_efficiency_rating' => 'integer',
        'rainwater_capacity' => 'decimal:2',
        'greywater_capacity' => 'decimal:2',
        'fixture_types' => 'array',
        'conservation_goals' => 'array',
        'assessment_date' => 'date',
        'next_assessment_date' => 'date',
        'potential_savings' => 'decimal:2',
        'rainwater_harvesting' => 'boolean',
        'greywater_recycling' => 'boolean',
        'low_flow_fixtures' => 'boolean',
        'smart_irrigation' => 'boolean',
        'drip_irrigation' => 'boolean',
        'xeriscaping' => 'boolean',
        'native_plants' => 'boolean',
        'leak_detection_system' => 'boolean',
        'water_metering' => 'boolean',
        'water_pressure_optimization' => 'boolean',
        'hot_water_efficiency' => 'boolean',
        'pool_cover' => 'boolean',
        'pool_recycling_system' => 'boolean',
        'water_treatment_system' => 'boolean',
        'recommendations' => 'array',
    ];

    // Relationships
    public function propertySustainability(): BelongsTo
    {
        return $this->belongsTo(PropertySustainability::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    // Scopes
    public function scopeHighEfficiency($query)
    {
        return $query->where('water_efficiency_rating', '>=', 80);
    }

    public function scopeLowEfficiency($query)
    {
        return $query->where('water_efficiency_rating', '<', 60);
    }

    public function scopeWithRainwater($query)
    {
        return $query->where('rainwater_harvesting', true);
    }

    public function scopeWithGreywater($query)
    {
        return $query->where('greywater_recycling', true);
    }

    public function scopeWithLeakDetection($query)
    {
        return $query->where('leak_detection_system', true);
    }

    // Attributes
    public function getPerformanceLevelAttribute(): string
    {
        if ($this->water_efficiency_rating >= 90) return 'ممتاز';
        if ($this->water_efficiency_rating >= 80) return 'جيد جداً';
        if ($this->water_efficiency_rating >= 70) return 'جيد';
        if ($this->water_efficiency_rating >= 60) return 'متوسط';
        return 'ضعيف';
    }

    public function getConsumptionUnitTextAttribute(): string
    {
        return match($this->consumption_unit) {
            'liters_per_day' => 'لتر يومياً',
            'gallons_per_day' => 'جالون يومياً',
            'cubic_meters_per_month' => 'متر مكعب شهرياً',
            default => 'غير معروف',
        };
    }

    public function getDailyConsumptionAttribute(): float
    {
        return match($this->consumption_unit) {
            'liters_per_day' => $this->water_consumption,
            'gallons_per_day' => $this->water_consumption * 3.785, // Convert to liters
            'cubic_meters_per_month' => ($this->water_consumption * 1000) / 30, // Convert to liters per day
            default => $this->water_consumption,
        };
    }

    public function getAnnualConsumptionAttribute(): float
    {
        return $this->daily_consumption * 365;
    }

    public function getAnnualCostAttribute(): float
    {
        // Simplified cost calculation (assuming $0.002 per liter)
        return $this->annual_consumption * 0.002;
    }

    public function getEstimatedSavingsAttribute(): float
    {
        return $this->annual_cost * ($this->potential_savings / 100);
    }

    public function getDaysUntilNextAssessmentAttribute(): ?int
    {
        if (!$this->next_assessment_date) return null;
        return now()->diffInDays($this->next_assessment_date, false);
    }

    public function getIsAssessmentOverdueAttribute(): bool
    {
        return $this->next_assessment_date && $this->next_assessment_date->isPast();
    }

    public function getRainwaterHarvestingPotentialAttribute(): float
    {
        // Simplified calculation: roof area × rainfall × collection efficiency
        $roofArea = 200; // Assumed roof area in m²
        $annualRainfall = 100; // Assumed annual rainfall in mm
        $collectionEfficiency = 0.8; // 80% collection efficiency
        
        return ($roofArea * $annualRainfall * $collectionEfficiency) / 1000; // Convert to m³
    }

    public function getGreywaterRecyclingPotentialAttribute(): float
    {
        // Simplified calculation: 70% of indoor water use can be recycled
        $indoorWaterUse = $this->daily_consumption * 0.8; // 80% is indoor use
        
        return $indoorWaterUse * 0.7; // 70% can be recycled
    }

    // Methods
    public function calculateWaterEfficiency(): int
    {
        $baseScore = 50;
        
        // Add points for conservation measures
        if ($this->rainwater_harvesting) $baseScore += 15;
        if ($this->greywater_recycling) $baseScore += 15;
        if ($this->low_flow_fixtures) $baseScore += 10;
        if ($this->smart_irrigation) $baseScore += 10;
        if ($this->drip_irrigation) $baseScore += 10;
        if ($this->xeriscaping) $baseScore += 8;
        if ($this->native_plants) $baseScore += 5;
        if ($this->leak_detection_system) $baseScore += 7;
        if ($this->water_metering) $baseScore += 5;
        if ($this->water_pressure_optimization) $baseScore += 5;
        
        // Penalty for high consumption
        $consumption = $this->daily_consumption;
        if ($consumption > 500) { // liters per day
            $baseScore -= min(20, ($consumption - 500) / 50);
        }
        
        return max(0, min(100, $baseScore));
    }

    public function updateWaterEfficiency(): void
    {
        $this->water_efficiency_rating = $this->calculateWaterEfficiency();
        $this->save();
    }

    public function getConservationFeatures(): array
    {
        $features = [];
        
        if ($this->rainwater_harvesting) {
            $features[] = [
                'feature' => 'تجميع مياه الأمطار',
                'capacity' => $this->rainwater_capacity . ' لتر',
                'potential_savings' => '30%',
                'status' => 'نشط',
            ];
        }
        
        if ($this->greywater_recycling) {
            $features[] = [
                'feature' => 'إعادة تدوير المياه الرمادية',
                'capacity' => $this->greywater_capacity . ' لتر',
                'potential_savings' => '25%',
                'status' => 'نشط',
            ];
        }
        
        if ($this->low_flow_fixtures) {
            $features[] = [
                'feature' => 'أجهزة منخفضة التدفق',
                'types' => $this->fixture_types,
                'potential_savings' => '20%',
                'status' => 'نشط',
            ];
        }
        
        if ($this->smart_irrigation) {
            $features[] = [
                'feature' => 'ري ذكي',
                'type' => $this->irrigation_type,
                'potential_savings' => '15%',
                'status' => 'نشط',
            ];
        }
        
        if ($this->leak_detection_system) {
            $features[] = [
                'feature' => 'نظام كشف التسريبات',
                'potential_savings' => '10%',
                'status' => 'نشط',
            ];
        }
        
        return $features;
    }

    public function getWaterUsageBreakdown(): array
    {
        // Simplified water usage breakdown
        $total = $this->daily_consumption;
        
        return [
            'indoor_use' => [
                'amount' => $total * 0.8,
                'percentage' => 80,
                'categories' => [
                    'showering' => $total * 0.25,
                    'toilets' => $total * 0.20,
                    'faucets' => $total * 0.15,
                    'washing_machine' => $total * 0.10,
                    'dishwasher' => $total * 0.05,
                    'other' => $total * 0.05,
                ],
            ],
            'outdoor_use' => [
                'amount' => $total * 0.2,
                'percentage' => 20,
                'categories' => [
                    'irrigation' => $total * 0.15,
                    'pool' => $total * 0.03,
                    'cleaning' => $total * 0.02,
                ],
            ],
        ];
    }

    public function getImprovementRecommendations(): array
    {
        $recommendations = [];
        
        if ($this->water_efficiency_rating < 60) {
            $recommendations[] = [
                'priority' => 'عاجل',
                'category' => 'شامل',
                'action' => 'تحسين شامل لكفاءة المياه',
                'potential_saving' => '40-60%',
                'estimated_cost' => 'مرتفع',
                'timeline' => '3-6 أشهر',
            ];
        }
        
        if (!$this->rainwater_harvesting) {
            $recommendations[] = [
                'priority' => 'مرتفع',
                'category' => 'تجميع المياه',
                'action' => 'تركيب نظام تجميع مياه الأمطار',
                'potential_saving' => '30%',
                'estimated_cost' => 'متوسط',
                'timeline' => '1-2 أشهر',
            ];
        }
        
        if (!$this->greywater_recycling) {
            $recommendations[] = [
                'priority' => 'مرتفع',
                'category' => 'إعادة التدوير',
                'action' => 'تركيب نظام إعادة تدوير المياه الرمادية',
                'potential_saving' => '25%',
                'estimated_cost' => 'مرتفع',
                'timeline' => '2-3 أشهر',
            ];
        }
        
        if (!$this->low_flow_fixtures) {
            $recommendations[] = [
                'priority' => 'متوسط',
                'category' => 'الأجهزة',
                'action' => 'تركيب أجهزة منخفضة التدفق',
                'potential_saving' => '20%',
                'estimated_cost' => 'منخفض',
                'timeline' => '1-2 أسابيع',
            ];
        }
        
        if (!$this->smart_irrigation && $this->daily_consumption > 300) {
            $recommendations[] = [
                'priority' => 'متوسط',
                'category' => 'الري',
                'action' => 'تركيب نظام ري ذكي',
                'potential_saving' => '15%',
                'estimated_cost' => 'متوسط',
                'timeline' => '1 شهر',
            ];
        }
        
        return $recommendations;
    }

    public function getBenchmarkComparison(): array
    {
        $benchmarks = [
            'residential' => ['avg_rating' => 65, 'avg_consumption' => 300],
            'commercial' => ['avg_rating' => 70, 'avg_consumption' => 500],
            'industrial' => ['avg_rating' => 60, 'avg_consumption' => 1000],
            'mixed' => ['avg_rating' => 68, 'avg_consumption' => 400],
        ];

        $propertyType = $this->propertySustainability->property->type ?? 'mixed';
        $benchmark = $benchmarks[$propertyType] ?? $benchmarks['mixed'];

        return [
            'rating_difference' => $this->water_efficiency_rating - $benchmark['avg_rating'],
            'consumption_difference' => $benchmark['avg_consumption'] - $this->daily_consumption,
            'performance_percentile' => $this->calculatePercentile($this->water_efficiency_rating, $benchmark['avg_rating']),
            'cost_comparison' => [
                'benchmark_cost' => $benchmark['avg_consumption'] * 365 * 0.002,
                'actual_cost' => $this->annual_cost,
                'savings_vs_benchmark' => max(0, $benchmark['avg_consumption'] * 365 * 0.002 - $this->annual_cost),
            ],
        ];
    }

    private function calculatePercentile(float $value, float $benchmark): float
    {
        $difference = $value - $benchmark;
        $percentile = 50 + ($difference * 2);
        
        return max(0, min(100, $percentile));
    }

    public function getWaterQualityMetrics(): array
    {
        return [
            'treatment_system' => $this->water_treatment_system,
            'treatment_type' => $this->treatment_type,
            'water_quality_standards' => $this->checkWaterQualityStandards(),
            'testing_frequency' => $this->getTestingFrequency(),
            'last_test_date' => $this->getLastTestDate(),
        ];
    }

    private function checkWaterQualityStandards(): array
    {
        return [
            'ph_level' => 'متوافق',
            'turbidity' => 'متوافق',
            'bacteria' => 'متوافق',
            'chemicals' => 'متوافق',
            'overall_status' => 'جيد',
        ];
    }

    private function getTestingFrequency(): string
    {
        return match($this->monitoring_frequency) {
            'daily' => 'يومي',
            'weekly' => 'أسبوعي',
            'monthly' => 'شهري',
            'quarterly' => 'ربع سنوي',
            default => 'غير محدد',
        };
    }

    private function getLastTestDate(): ?\Carbon\Carbon
    {
        // This would typically come from test records
        return now()->subMonth();
    }

    public function getConservationPlan(): array
    {
        $plan = [];
        
        // Short-term goals (0-6 months)
        $plan['short_term'] = [
            'goals' => [
                'تقليل استهلاك المياه بنسبة 15%',
                'تركيب أجهزة منخفضة التدفق',
                'كشف وإصلاح التسريبات',
            ],
            'actions' => $this->getShortTermActions(),
            'estimated_cost' => 'منخفض إلى متوسط',
            'expected_savings' => '15-25%',
        ];
        
        // Medium-term goals (6-18 months)
        $plan['medium_term'] = [
            'goals' => [
                'تثبيت نظام تجميع مياه الأمطار',
                'تركيب نظام ري ذكي',
                'تحسين كفاءة المياه الساخنة',
            ],
            'actions' => $this->getMediumTermActions(),
            'estimated_cost' => 'متوسط إلى مرتفع',
            'expected_savings' => '25-40%',
        ];
        
        // Long-term goals (18+ months)
        $plan['long_term'] = [
            'goals' => [
                'تثبيت نظام إعادة تدوير المياه الرمادية',
                'تحقيق الاكتفاء الذاتي من المياه',
                'الحصول على شهادة المياه الخضراء',
            ],
            'actions' => $this->getLongTermActions(),
            'estimated_cost' => 'مرتفع',
            'expected_savings' => '40-60%',
        ];
        
        return $plan;
    }

    private function getShortTermActions(): array
    {
        $actions = [];
        
        if (!$this->low_flow_fixtures) {
            $actions[] = 'تركيب أجهزة منخفضة التدفق';
        }
        
        if (!$this->leak_detection_system) {
            $actions[] = 'تركيب نظام كشف التسريبات';
        }
        
        if (!$this->water_metering) {
            $actions[] = 'تركيب عدادات مياه ذكية';
        }
        
        return $actions;
    }

    private function getMediumTermActions(): array
    {
        $actions = [];
        
        if (!$this->rainwater_harvesting) {
            $actions[] = 'تركيب نظام تجميع مياه الأمطار';
        }
        
        if (!$this->smart_irrigation) {
            $actions[] = 'تركيب نظام ري ذكي';
        }
        
        if (!$this->water_pressure_optimization) {
            $actions[] = 'تحسين ضغط المياه';
        }
        
        return $actions;
    }

    private function getLongTermActions(): array
    {
        $actions = [];
        
        if (!$this->greywater_recycling) {
            $actions[] = 'تركيب نظام إعادة تدوير المياه الرمادية';
        }
        
        if (!$this->xeriscaping) {
            $actions[] = 'تطبيق زراعة صحراوية';
        }
        
        if (!$this->native_plants) {
            $actions[] = 'زراعة نباتات محلية';
        }
        
        return $actions;
    }

    public function getEnvironmentalImpact(): array
    {
        $annualSavings = $this->estimated_savings;
        $waterSaved = $annualSavings / 0.002; // Convert cost back to liters
        
        return [
            'water_saved_liters' => round($waterSaved, 0),
            'water_saved_m3' => round($waterSaved / 1000, 2),
            'energy_saved_kwh' => round($waterSaved * 0.001, 2), // Energy for water treatment
            'co2_reduced_kg' => round($waterSaved * 0.0005, 2), // CO2 from water treatment
            'ecosystem_benefits' => [
                'reduced_strain_on_aquifers' => 'تقليل الضغط على طبقات المياه الجوفية',
                'improved_water_quality' => 'تحسين جودة المياه',
                'enhanced_biodiversity' => 'تعزيز التنوع البيولوجي',
                'climate_resilience' => 'مقاومة تغير المناخ',
            ],
        ];
    }

    // Events
    protected static function booted()
    {
        static::created(function ($waterConservation) {
            // Update property sustainability water efficiency rating
            $waterConservation->propertySustainability->update([
                'water_efficiency_rating' => $waterConservation->water_efficiency_rating,
            ]);
        });

        static::updated(function ($waterConservation) {
            // Update property sustainability if efficiency rating changed
            if ($waterConservation->wasChanged('water_efficiency_rating')) {
                $waterConservation->propertySustainability->update([
                    'water_efficiency_rating' => $waterConservation->water_efficiency_rating,
                ]);
            }
        });
    }
}
