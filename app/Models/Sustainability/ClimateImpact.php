<?php

namespace App\Models\Sustainability;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClimateImpact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_sustainability_id',
        'climate_risk_score',
        'heat_wave_risk',
        'flooding_risk',
        'sea_level_rise_risk',
        'drought_risk',
        'storm_risk',
        'wildfire_risk',
        'air_quality_risk',
        'extreme_precipitation_risk',
        'vulnerability_score',
        'adaptation_capacity',
        'resilience_score',
        'location_coordinates',
        'elevation',
        'distance_from_coast',
        'proximity_to_water_bodies',
        'water_body_type',
        'flood_zone',
        'soil_type',
        'vegetation_cover',
        'building_foundation_type',
        'building_materials',
        'roof_condition',
        'drainage_system',
        'drainage_capacity',
        'cooling_system_type',
        'cooling_efficiency',
        'ventilation_system',
        'insulation_level',
        'shade_provision',
        'green_roof',
        'permeable_surfaces',
        'rainwater_harvesting',
        'flood_barriers',
        'barrier_type',
        'fire_resistance_rating',
        'emergency_power_system',
        'emergency_water_storage',
        'emergency_shelter_capacity',
        'early_warning_system',
        'evacuation_plan',
        'climate_resilience_features',
        'adaptation_measures_implemented',
        'implemented_measures',
        'planned_adaptation_measures',
        'adaptation_cost_estimate',
        'adaptation_timeline',
        'insurance_coverage',
        'insurance_provider',
        'coverage_details',
        'climate_data_sources',
        'assessment_methodology',
        'assessment_date',
        'next_assessment_date',
        'assessor_name',
        'assessor_qualifications',
        'projected_climate_impacts',
        'time_horizon_years',
        'scenario_used',
        'confidence_level',
        'uncertainty_factors',
        'mitigation_strategies',
        'monitoring_plan',
        'monitoring_frequency',
        'key_performance_indicators',
        'stakeholder_engagement',
        'community_resilience_contribution',
        'biodiversity_impact',
        'ecosystem_services_impact',
        'climate_justice_considerations',
        'notes',
    ];

    protected $casts = [
        'climate_risk_score' => 'decimal:2',
        'heat_wave_risk' => 'decimal:2',
        'flooding_risk' => 'decimal:2',
        'sea_level_rise_risk' => 'decimal:2',
        'drought_risk' => 'decimal:2',
        'storm_risk' => 'decimal:2',
        'wildfire_risk' => 'decimal:2',
        'air_quality_risk' => 'decimal:2',
        'extreme_precipitation_risk' => 'decimal:2',
        'vulnerability_score' => 'decimal:2',
        'adaptation_capacity' => 'decimal:2',
        'resilience_score' => 'decimal:2',
        'elevation' => 'decimal:2',
        'distance_from_coast' => 'decimal:2',
        'vegetation_cover' => 'decimal:2',
        'fire_resistance_rating' => 'integer',
        'emergency_shelter_capacity' => 'integer',
        'assessment_date' => 'date',
        'next_assessment_date' => 'date',
        'time_horizon_years' => 'integer',
        'confidence_level' => 'decimal:2',
        'building_materials' => 'array',
        'climate_resilience_features' => 'array',
        'implemented_measures' => 'array',
        'planned_adaptation_measures' => 'array',
        'adaptation_cost_estimate' => 'decimal:2',
        'projected_climate_impacts' => 'array',
        'uncertainty_factors' => 'array',
        'mitigation_strategies' => 'array',
        'key_performance_indicators' => 'array',
        'proximity_to_water_bodies' => 'boolean',
        'drainage_system' => 'boolean',
        'cooling_efficiency' => 'integer',
        'ventilation_system' => 'boolean',
        'shade_provision' => 'boolean',
        'green_roof' => 'boolean',
        'rainwater_harvesting' => 'boolean',
        'flood_barriers' => 'boolean',
        'emergency_power_system' => 'boolean',
        'emergency_water_storage' => 'boolean',
        'early_warning_system' => 'boolean',
        'evacuation_plan' => 'boolean',
        'adaptation_measures_implemented' => 'boolean',
        'insurance_coverage' => 'boolean',
        'monitoring_plan' => 'boolean',
        'stakeholder_engagement' => 'boolean',
    ];

    // Relationships
    public function propertySustainability(): BelongsTo
    {
        return $this->belongsTo(PropertySustainability::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_name');
    }

    // Scopes
    public function scopeHighRisk($query)
    {
        return $query->where('climate_risk_score', '>=', 70);
    }

    public function scopeLowRisk($query)
    {
        return $query->where('climate_risk_score', '<', 40);
    }

    public function scopeWithAdaptation($query)
    {
        return $query->where('adaptation_measures_implemented', true);
    }

    public function scopeWithInsurance($query)
    {
        return $query->where('insurance_coverage', true);
    }

    public function scopeRecent($query)
    {
        return $query->where('assessment_date', '>=', now()->subMonths(6));
    }

    // Attributes
    public function getRiskLevelAttribute(): string
    {
        if ($this->climate_risk_score >= 80) return 'مرتفع جداً';
        if ($this->climate_risk_score >= 60) return 'مرتفع';
        if ($this->climate_risk_score >= 40) return 'متوسط';
        if ($this->climate_risk_score >= 20) return 'منخفض';
        return 'منخفض جداً';
    }

    public function getVulnerabilityLevelAttribute(): string
    {
        if ($this->vulnerability_score >= 80) return 'مرتفع جداً';
        if ($this->vulnerability_score >= 60) return 'مرتفع';
        if ($this->vulnerability_score >= 40) return 'متوسط';
        if ($this->vulnerability_score >= 20) return 'منخفض';
        return 'منخفض جداً';
    }

    public function getResilienceLevelAttribute(): string
    {
        if ($this->resilience_score >= 80) return 'مرتفع جداً';
        if ($this->resilience_score >= 60) return 'مرتفع';
        if ($this->resilience_score >= 40) return 'متوسط';
        if ($this->resilience_score >= 20) return 'منخفض';
        return 'منخفض جداً';
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

    public function getAdaptationReadinessAttribute(): float
    {
        if ($this->adaptation_capacity == 0) return 0;
        
        $implementedMeasures = count($this->implemented_measures ?? []);
        $plannedMeasures = count($planned_adaptation_measures ?? []);
        
        $implementationScore = ($implementedMeasures / ($implementedMeasures + $plannedMeasures)) * 50;
        
        return min(100, $this->adaptation_capacity + $implementationScore);
    }

    public function getClimateRiskBreakdown(): array
    {
        return [
            'heat_waves' => [
                'risk' => $this->heat_wave_risk,
                'level' => $this->getRiskCategory($this->heat_wave_risk),
                'impact' => 'صحة الإنسان والبنية التحتية',
                'mitigation' => ['تحسين التبريد', 'توفير أماكن مبردة', 'ساعات خضراء'],
            ],
            'flooding' => [
                'risk' => $this->flooding_risk,
                'level' => $this->getRiskCategory($this->flooding_risk),
                'impact' => 'أضرار الممتلكات والبنية التحتية',
                'mitigation' => ['حواجز فيضانات', 'تحسين الصرف', 'رفع مستوى المباني'],
            ],
            'sea_level_rise' => [
                'risk' => $this->sea_level_rise_risk,
                'level' => $this->getRiskCategory($this->sea_level_rise_risk),
                'impact' => 'فقدان المناطق الساحلية',
                'mitigation' => ['حماية السواحل', 'بنية مرنة', 'تخطيط للهجرة'],
            ],
            'drought' => [
                'risk' => $this->drought_risk,
                'level' => $this->getRiskCategory($this->drought_risk),
                'impact' => 'ندرة المياه والجفاف',
                'mitigation' => ['حصاد مياه', 'زراعة مقاومة للجفاف', 'كفاءة استخدام المياه'],
            ],
            'storms' => [
                'risk' => $this->storm_risk,
                'level' => $this->getRiskCategory($this->storm_risk),
                'impact' => 'أضرار الرياح والأعاصف',
                'mitigation' => ['بناء مقاوم للرياح', 'حماية الأصول', 'تخطيط للطوارئء'],
            ],
            'wildfires' => [
                'risk' => $this->wildfire_risk,
                'level' => $this->getRiskCategory($this->wildfire_risk),
                'impact' => 'خطرق الحرائق والدخان',
                'mitigation' => ['مناطق عازلة', 'مواد مقاومة للحريق', 'أنظمة إنذار مبكر'],
            ],
        ];
    }

    private function getRiskCategory(float $risk): string
    {
        if ($risk >= 80) return 'مرتفع جداً';
        if ($risk >= 60) return 'مرتفع';
        if ($risk >= 40) return 'متوسط';
        if ($risk >= 20) return 'منخفض';
        return 'منخفض';
    }

    public function getVulnerabilityFactors(): array
    {
        $factors = [];
        
        if ($this->elevation < 10) {
            $factors[] = [
                'factor' => 'انخفاض الارتفاع',
                'risk' => 'مرتفع',
                'description' => 'الموقع منخفض الارتفاع مما يزيد من خطر الفيضانات',
            ];
        }
        
        if ($this->proximity_to_water_bodies) {
            $factors[] = [
                'factor' => 'القرب من المسطحات المائية',
                'risk' => $this->distance_from_coast < 50 ? 'مرتفع جداً' : 'متوسط',
                'description' => 'القرب من المسطحات المائية يزيد من خطر الفيضانات وارتفاع مستوى سطح البحر',
            ];
        }
        
        if ($this->building_foundation_type === 'poor') {
            $factors[] = [
                'factor' => 'أساس ضعيف',
                'risk' => 'مرتفع',
                'description' => 'أساس المبنى ضعيف لا يوفر حماية كافية من المخاطر',
            ];
        }
        
        if ($this->vegetation_cover < 0.2) {
            $factors[] = [
                'factor' => 'قلة الغطاء النباتي',
                'risk' => 'متوسط',
                'description' => 'قلة الغطاء النباتي تزيد من تأثير موجات الحرارة',
            ];
        }
        
        return $factors;
    }

    public function getAdaptationMeasures(): array
    {
        $measures = [
            'implemented' => $this->implemented_measures ?? [],
            'planned' => $this->planned_adaptation_measures ?? [],
            'recommended' => $this->generateRecommendedMeasures(),
        ];
        
        return $measures;
    }

    private function generateRecommendedMeasures(): array
    {
        $measures = [];
        
        if ($this->heat_wave_risk > 60) {
            $measures[] = 'تركيب أنظمة تبريد فعالة';
            $measures[] = 'تحسين عزل المبنى';
            $measures[] = 'توفير أماكن مبردة';
        }
        
        if ($this->flooding_risk > 60) {
            $measures[] = 'تركيب حواجز فيضانات';
            $measures[] = 'تحسين أنظمة الصرف';
            $measures[] = 'رفع مستوى الأرض حول المبنى';
        }
        
        if ($this->storm_risk > 60) {
            $measures[] = 'تعزيز هيكل المبنى';
            $measures[] = 'تأمكانة الأصول الخارجية';
            $measures[] = 'تركيب نوافذ مقاومة للرياح';
        }
        
        if ($this->drought_risk > 60) {
            $measures[] = 'تركيب أنظمة حصاد مياه';
            $measures[] = 'استخدام نباتات مقاومة للجفاف';
            $measures[] = 'تحسين كفاءة استخدام المياه';
        }
        
        return $measures;
    }

    public function getEmergencyPreparedness(): array
    {
        return [
            'power_system' => [
                'available' => $this->emergency_power_system,
                'status' => $this->emergency_power_system ? 'متوفر' : 'غير متوفر',
                'adequacy' => $this->emergency_power_system ? 'جيد' : 'غير كاف',
            ],
            'water_storage' => [
                'available' => $this->emergency_water_storage,
                'status' => $this->emergency_water_storage ? 'متوفر' : 'غير متوفر',
                'capacity' => $this->emergency_water_storage ? 'كافٍ' : 'غير كافٍ',
            ],
            'shelter_capacity' => [
                'available' => $this->emergency_shelter_capacity > 0,
                'capacity' => $this->emergency_shelter_capacity,
                'adequacy' => $this->emergency_shelter_capacity >= 10 ? 'كافٍ' : 'غير كافٍ',
            ],
            'warning_system' => [
                'available' => $this->early_warning_system,
                'status' => $this->early_warning_system ? 'نشط' : 'غير نشط',
                'coverage' => $this->early_warning_system ? 'شامل' : 'محدود',
            ],
            'evacuation_plan' => [
                'available' => $this->evacuation_plan,
                'status' => $this->evacuation_plan ? 'متوفر' : 'غير متوفر',
                'tested' => $this->evacuation_plan ? 'مختبر' : 'غير مختبر',
            ],
        ];
    }

    public function getInsuranceCoverage(): array
    {
        $coverage = [
            'has_insurance' => $this->insurance_coverage,
            'provider' => $this->insurance_provider,
            'details' => $this->coverage_details ?? 'غير متوفر',
            'coverage_for_climate_risks' => $this->insurance_coverage ? 'مشمولة' : 'غير مشمولة',
        ];
        
        if ($this->insurance_coverage) {
            $coverage['recommended_coverage'] = [
                'damage_from_extreme_weather' => 'أضرار من الطقس الجوية',
                'business_interruption' => 'انقطاع العمل',
                'additional_living_expenses' => 'تكاليف معيشية إضافية',
                'debris_removal' => 'إزالة الحطام',
            ];
        }
        
        return $coverage;
    }

    public function getProjectedImpacts(): array
    {
        $impacts = $this->projected_climate_impacts ?? [];
        
        // Add default projections if none exist
        if (empty($impacts)) {
            $impacts = [
                'temperature_increase' => '+2-4°C by 2050',
                'precipitation_change' => '±20% by 2050',
                'sea_level_rise' => '+0.5-1m by 2100',
                'extreme_events' => '2-3x increase by 2050',
            ];
        }
        
        return $impacts;
    }

    public function getRiskAssessment(): array
    {
        return [
            'overall_risk' => $this->climate_risk_score,
            'risk_level' => $this->risk_level,
            'highest_risks' => $this->getHighestRisks(),
            'critical_vulnerabilities' => $this->getCriticalVulnerabilities(),
            'time_sensitive_risks' => $this->getTimeSensitiveRisks(),
            'cascading_effects' => $this->analyzeCascadingEffects(),
        ];
    }

    private function getHighestRisks(): array
    {
        $risks = [
            'موجات الحرارة' => $this->heat_wave_risk,
            'الفيضانات' => $this->flooding_risk,
            'ارتفاع مستوى سطح البحر' => $this->sea_level_rise_risk,
            'الجفاف' => $this->drought_risk,
            'العواصف' => $this->storm_risk,
            'حرائق الغابات' => $this->wildfire_risk,
        ];
        
        arsort($risks);
        return array_slice($risks, 0, 3, true);
    }

    private function getCriticalVulnerabilities(): array
    {
        $vulnerabilities = [];
        
        if ($this->vulnerability_score > 70) {
            $vulnerabilities[] = 'ضعف عام في القدرة على التكيف';
        }
        
        if ($this->adaptation_capacity < 50) {
            $vulnerabilities[] = 'قدرة تكيف منخفضة';
        }
        
        if (!$this->insurance_coverage) {
            $vulnerabilities[] = 'عدم وجود تغطية تأمينية';
        }
        
        if (!$this->emergency_power_system) {
            $vulnerabilities[] = 'عدم وجود طاقة طوارئ';
        }
        
        return $vulnerabilities;
    }

    private function getTimeSensitiveRisks(): array
    {
        return [
            'immediate' => 'مخاطر تتطلب إجراءات فورية',
            'short_term' => 'مخاطر خلال 1-3 سنوات',
            'medium_term' => 'مخاطر خلال 3-10 سنوات',
            'long_term' => 'مخاطر طويلة الأمد',
        ];
    }

    private function analyzeCascadingEffects(): array
    {
        return [
            'infrastructure' => 'تأثير على البنية التحتية',
            'health' => 'تأثير على الصحة العامة',
            'economy' => 'تأثير اقتصادي',
            'social' => 'تأثير اجتماعي',
            'environmental' => 'تأثير بيئي',
        ];
    }

    public function generateDetailedAdaptationPlan(): array
    {
        $plan = [];
        
        if ($this->climate_risk_score > 70) {
            $plan[] = [
                'phase' => 'المرحلة الأولى (عاجلة)',
                'actions' => [
                    'تقييم المخاطر التفصيلي',
                    'وضع خطة طوارئء',
                    'تنفيذ إجراءات عاجلة',
                ],
                'timeline' => '0-3 أشهر',
                'budget' => 'مرتفع',
                'priority' => 'عاجل',
                'responsible_party' => 'مالك العقار',
            ];
        }
        
        if ($this->adaptation_capacity < 60) {
            $plan[] = [
                'phase' => 'المرحلة الثانية (تحسين)',
                'actions' => [
                    'تركيب أنظمة تكيف',
                    'تحسين البنية التحتية',
                    'تدريب السكان',
                ],
                'timeline' => '3-12 شهر',
                'budget' => 'متوسط إلى مرتفع',
                'priority' => 'مرتفع',
                'responsible_party' => 'مالك العقار',
            ];
        }
        
        if (!$this->insurance_coverage) {
            $plan[] = [
                'phase' => 'المرحلة الثالثة (حماية)',
                'actions' => [
                    'الحصول على تأمين مناسب',
                    'مراجعة التغطية الحالية',
                    'تحديث وثائق التأمين',
                ],
                'timeline' => '1-3 أشهر',
                'budget' => 'منخفض إلى متوسط',
                'priority' => 'متوسط',
                'responsible_party' => 'مالك العقار',
            ];
        }
        
        return $plan;
    }

    public function getMonitoringRequirements(): array
    {
        $requirements = [
            'performance_metrics' => [
                'مؤشرات أداء التكيف',
                'مؤشرات الاستجابة',
                'مؤشرات التأثير',
            ],
            'monitoring_frequency' => $this->monitoring_frequency ?? 'شهري',
            'key_indicators' => $this->key_performance_indicators ?? [
                'استهلاك الطاقة',
                'استهلاك المياه',
                'درجة الحرارة',
                'مستوى المياه الجوفية',
            ],
            'data_sources' => $this->climate_data_sources ?? [],
            'reporting_schedule' => [
                'تقارير شهري',
                'تقارير ربع سنوي',
                'تقارير سنوي',
                'تقارير عند الحوادث',
            ],
        ];
        
        return $requirements;
    }

    public function getStakeholderEngagement(): array
    {
        return [
            'engagement_status' => $this->stakeholder_engagement,
            'stakeholders' => [
                'مالك العقار',
                'السكان',
                'المجيران المحليون',
                'السلطات المحلية',
                'المقاولون',
                'المستثمرون',
                'المجيران',
            ],
            'engagement_methods' => [
                'اجتماعات دورية',
                'ورش عمل',
                'حملقات معلومات',
                'تدريبات',
                'تمارينات عامة',
            ],
            'community_contribution' => $this->community_resilience_contribution ?? 'محدودة',
        ];
    }

    public function calculateClimateJusticeScore(): float
    {
        $score = 50; // Base score
        
        // Positive factors
        if ($this->community_resilience_contribution) {
            $score += 20;
        }
        
        if ($this->stakeholder_engagement) {
            $score += 15;
        }
        
        if ($this->climate_justice_considerations) {
            $score += 15;
        }
        
        // Negative factors
        if ($this->vulnerability_score > 70) {
            $score -= 20;
        }
        
        if ($this->adaptation_capacity < 50) {
            $score -= 15;
        }
        
        return max(0, min(100, $score));
    }

    public function getEnvironmentalImpact(): array
    {
        return [
            'biodiversity' => [
                'impact' => $this->biodiversity_impact ?? 'متوسط',
                'measures' => $this->biodiversity_protection ? 'مطبقة' : 'غير مطبقة',
                'improvement_opportunities' => [
                    'حماية التنوع البيولوجي',
                    'المساحات الخضراء',
                    'الممرور الطبيعية',
                ],
            ],
            'ecosystem_services' => [
                'impact' => $this->ecosystem_services_impact ?? 'متوسط',
                'services_affected' => [
                    'تنقية الهواء',
                    'تنظيم المياه',
                    'تلطيف الحرارة',
                    'تكوين التربة',
                ],
                'restoration_opportunities' => [
                    'استعادة الغابات',
                    'تحسين جودة المياه',
                    'حماية التربة',
                ],
            ],
            'climate_mitigation' => [
                'carbon_reduction' => $this->calculateCarbonReduction(),
                'climate_resilience' => $this->resilience_score,
                'adaptation_benefits' => $this->calculateAdaptationBenefits(),
            ],
        ];
    }

    private function calculateCarbonReduction(): float
    {
        // Simplified calculation based on adaptation measures
        $baseReduction = 0;
        
        if ($this->renewable_energy_percentage > 0) {
            $baseReduction += $this->renewable_energy_percentage * 0.5;
        }
        
        if ($this->green_roof) {
            $baseReduction += 10;
        }
        
        if ($this->permeable_surfaces > 0.3) {
            $baseReduction += 5;
        }
        
        return min(50, $baseReduction);
    }

    private function calculateAdaptationBenefits(): array
    {
        return [
            'risk_reduction' => round($this->climate_risk_score * 0.3, 1),
            'cost_savings' => round($this->adaptation_cost_estimate * 0.2, 1),
            'property_value_protection' => round($this->adaptation_cost_estimate * 0.1, 1),
            'insurance_premium_reduction' => round($this->adaptation_cost_estimate * 0.05, 1),
        ];
    }

    // Events
    protected static function booted()
    {
        static::created(function ($climateImpact) {
            // Update property sustainability with climate impact data
            // This would typically trigger updates to related models
        });

        static::updated(function ($climateImpact) {
            // Update related models when climate impact changes
            if ($climateImpact->wasChanged(['climate_risk_score', 'adaptation_capacity', 'resilience_score'])) {
                // Update property sustainability or related metrics
            }
        });
    }
}
