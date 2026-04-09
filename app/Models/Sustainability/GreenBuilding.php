<?php

namespace App\Models\Sustainability;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GreenBuilding extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_sustainability_id',
        'building_name',
        'certification_standard',
        'certification_level',
        'certification_number',
        'certification_date',
        'expiry_date',
        'certification_body',
        'assessor_name',
        'assessment_date',
        'total_score',
        'energy_score',
        'water_score',
        'materials_score',
        'indoor_environment_score',
        'site_score',
        'innovation_score',
        'regional_priority_score',
        'building_type',
        'building_size',
        'size_unit',
        'construction_year',
        'renovation_year',
        'occupancy_type',
        'occupancy_rate',
        'green_features',
        'sustainable_materials_used',
        'energy_efficiency_measures',
        'water_conservation_measures',
        'waste_management_systems',
        'indoor_air_quality_measures',
        'site_sustainability_features',
        'innovation_features',
        'performance_monitoring',
        'monitoring_systems',
        'commissioning_date',
        'commissioning_type',
        'energy_modeling_performed',
        'energy_modeling_results',
        'daylighting_analysis_performed',
        'daylighting_results',
        'thermal_comfort_analysis_performed',
        'thermal_comfort_results',
        'acoustic_analysis_performed',
        'acoustic_results',
        'life_cycle_assessment_performed',
        'life_cycle_results',
        'cost_benefit_analysis_performed',
        'cost_benefit_results',
        'maintenance_plan',
        'maintenance_plan_document',
        'user_training_provided',
        'training_materials',
        'certification_documents',
        'building_photos',
        'verification_url',
        'annual_energy_consumption',
        'annual_water_consumption',
        'annual_waste_generated',
        'annual_carbon_emissions',
        'renewable_energy_percentage',
        'recycling_rate',
        'green_space_ratio',
        'stormwater_management',
        'heat_island_reduction',
        'light_pollution_reduction',
        'biodiversity_protection',
        'certification_status',
        'next_assessment_date',
        'notes',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'energy_score' => 'decimal:2',
        'water_score' => 'decimal:2',
        'materials_score' => 'decimal:2',
        'indoor_environment_score' => 'decimal:2',
        'site_score' => 'decimal:2',
        'innovation_score' => 'decimal:2',
        'regional_priority_score' => 'decimal:2',
        'building_size' => 'decimal:2',
        'construction_year' => 'integer',
        'renovation_year' => 'integer',
        'occupancy_rate' => 'decimal:2',
        'certification_date' => 'date',
        'expiry_date' => 'date',
        'assessment_date' => 'date',
        'commissioning_date' => 'date',
        'next_assessment_date' => 'date',
        'annual_energy_consumption' => 'decimal:2',
        'annual_water_consumption' => 'decimal:2',
        'annual_waste_generated' => 'decimal:2',
        'annual_carbon_emissions' => 'decimal:2',
        'renewable_energy_percentage' => 'decimal:2',
        'recycling_rate' => 'decimal:2',
        'green_space_ratio' => 'decimal:2',
        'green_features' => 'array',
        'sustainable_materials_used' => 'array',
        'energy_efficiency_measures' => 'array',
        'water_conservation_measures' => 'array',
        'waste_management_systems' => 'array',
        'indoor_air_quality_measures' => 'array',
        'site_sustainability_features' => 'array',
        'innovation_features' => 'array',
        'monitoring_systems' => 'array',
        'energy_modeling_results' => 'array',
        'daylighting_results' => 'array',
        'thermal_comfort_results' => 'array',
        'acoustic_results' => 'array',
        'life_cycle_results' => 'array',
        'cost_benefit_results' => 'array',
        'training_materials' => 'array',
        'certification_documents' => 'array',
        'building_photos' => 'array',
        'maintenance_plan' => 'boolean',
        'user_training_provided' => 'boolean',
        'energy_modeling_performed' => 'boolean',
        'daylighting_analysis_performed' => 'boolean',
        'thermal_comfort_analysis_performed' => 'boolean',
        'acoustic_analysis_performed' => 'boolean',
        'life_cycle_assessment_performed' => 'boolean',
        'cost_benefit_analysis_performed' => 'boolean',
        'performance_monitoring' => 'boolean',
        'stormwater_management' => 'boolean',
        'heat_island_reduction' => 'boolean',
        'light_pollution_reduction' => 'boolean',
        'biodiversity_protection' => 'boolean',
    ];

    // Relationships
    public function propertySustainability(): BelongsTo
    {
        return $this->belongsTo(PropertySustainability::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('certification_status', 'active');
    }

    public function scopeByStandard($query, $standard)
    {
        return $query->where('certification_standard', $standard);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('certification_level', $level);
    }

    public function scopeExpiringSoon($query, $days = 90)
    {
        return $query->where('certification_status', 'active')
                    ->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now());
    }

    public function scopeHighPerformance($query)
    {
        return $query->where('total_score', '>=', 80);
    }

    // Attributes
    public function getCertificationStandardTextAttribute(): string
    {
        return match($this->certification_standard) {
            'leed' => 'LEED',
            'breeam' => 'BREEAM',
            'estidama' => 'Estidama',
            'green_globes' => 'Green Globes',
            'energy_star' => 'ENERGY STAR',
            'passive_house' => 'Passive House',
            'living_building' => 'Living Building Challenge',
            'well' => 'WELL Building Standard',
            'local_green' => 'شهادة خضراء محلية',
            default => 'غير معروف',
        };
    }

    public function getCertificationLevelTextAttribute(): string
    {
        return match($this->certification_level) {
            'platinum' => 'Platinum - البلاتيني',
            'gold' => 'Gold - الذهبي',
            'silver' => 'Silver - الفضي',
            'bronze' => 'Bronze - البرونزي',
            'certified' => 'Certified - معتمد',
            default => 'غير معروف',
        };
    }

    public function getCertificationStatusTextAttribute(): string
    {
        return match($this->certification_status) {
            'pending' => 'قيد المعالجة',
            'active' => 'نشط',
            'suspended' => 'معلق',
            'expired' => 'منتهي الصلاحية',
            'revoked' => 'ملغي',
            default => 'غير معروف',
        };
    }

    public function getSizeUnitTextAttribute(): string
    {
        return match($this->size_unit) {
            'sq_m' => 'متر مربع',
            'sq_ft' => 'قدم مربع',
            default => 'غير معروف',
        };
    }

    public function getBuildingAgeAttribute(): int
    {
        return date('Y') - $this->construction_year;
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) return null;
        return now()->diffInDays($this->expiry_date, false);
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->days_until_expiry !== null && 
               $this->days_until_expiry > 0 && 
               $this->days_until_expiry <= 90;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getIsValidAttribute(): bool
    {
        return $this->certification_status === 'active' && !$this->is_expired;
    }

    public function getBuildingSizeInSqMAttribute(): float
    {
        return $this->size_unit === 'sq_ft' ? $this->building_size * 0.092903 : $this->building_size;
    }

    public function getEnergyPerformanceIndexAttribute(): float
    {
        if ($this->annual_energy_consumption <= 0 || $this->building_size_in_sq_m <= 0) return 0;
        
        return $this->annual_energy_consumption / $this->building_size_in_sq_m; // kWh per sqm per year
    }

    public function getWaterPerformanceIndexAttribute(): float
    {
        if ($this->annual_water_consumption <= 0 || $this->building_size_in_sq_m <= 0) return 0;
        
        return $this->annual_water_consumption / $this->building_size_in_sq_m; // liters per sqm per year
    }

    public function getCarbonIntensityAttribute(): float
    {
        if ($this->annual_carbon_emissions <= 0 || $this->building_size_in_sq_m <= 0) return 0;
        
        return $this->annual_carbon_emissions / $this->building_size_in_sq_m; // kg CO2 per sqm per year
    }

    // Methods
    public function calculatePerformanceMetrics(): array
    {
        $metrics = [];
        
        // Energy performance
        $metrics['energy_performance'] = [
            'score' => $this->energy_score,
            'annual_consumption' => $this->annual_energy_consumption,
            'performance_index' => $this->energy_performance_index,
            'renewable_percentage' => $this->renewable_energy_percentage,
            'efficiency_measures' => $this->energy_efficiency_measures,
        ];
        
        // Water performance
        $metrics['water_performance'] = [
            'score' => $this->water_score,
            'annual_consumption' => $this->annual_water_consumption,
            'performance_index' => $this->water_performance_index,
            'conservation_measures' => $this->water_conservation_measures,
        ];
        
        // Materials performance
        $metrics['materials_performance'] = [
            'score' => $this->materials_score,
            'sustainable_materials' => $this->sustainable_materials_used,
        ];
        
        // Indoor environment
        $metrics['indoor_environment'] = [
            'score' => $this->indoor_environment_score,
            'air_quality_measures' => $this->indoor_air_quality_measures,
        ];
        
        // Site performance
        $metrics['site_performance'] = [
            'score' => $this->site_score,
            'green_space_ratio' => $this->green_space_ratio,
            'site_features' => $this->site_sustainability_features,
        ];
        
        return $metrics;
    }

    public function checkComplianceStatus(): array
    {
        $compliance = [];
        
        // Check certification expiry
        if ($this->expiry_date) {
            $daysUntilExpiry = now()->diffInDays($this->expiry_date, false);
            $compliance['certification_valid'] = $daysUntilExpiry > 0;
            $compliance['days_until_expiry'] = $daysUntilExpiry;
            $compliance['expiry_warning'] = $daysUntilExpiry > 0 && $daysUntilExpiry <= 90;
        }
        
        // Check next assessment
        if ($this->next_assessment_date) {
            $daysUntilAssessment = now()->diffInDays($this->next_assessment_date, false);
            $compliance['assessment_due'] = $daysUntilAssessment <= 30;
            $compliance['days_until_assessment'] = $daysUntilAssessment;
        }
        
        // Check performance thresholds
        $compliance['energy_compliant'] = $this->energy_score >= 70;
        $compliance['water_compliant'] = $this->water_score >= 70;
        $compliance['materials_compliant'] = $this->materials_score >= 70;
        $compliance['indoor_compliant'] = $this->indoor_environment_score >= 70;
        $compliance['site_compliant'] = $this->site_score >= 70;
        $compliance['overall_compliant'] = $this->total_score >= 75;
        
        return $compliance;
    }

    public function getDetailedPerformanceData(): array
    {
        return [
            'energy_performance' => [
                'score' => $this->energy_score,
                'annual_consumption' => $this->annual_energy_consumption,
                'performance_index' => $this->energy_performance_index,
                'renewable_percentage' => $this->renewable_energy_percentage,
                'efficiency_measures' => $this->energy_efficiency_measures,
                'benchmark_comparison' => $this->getEnergyBenchmark(),
            ],
            'water_performance' => [
                'score' => $this->water_score,
                'annual_consumption' => $this->annual_water_consumption,
                'performance_index' => $this->water_performance_index,
                'conservation_measures' => $this->water_conservation_measures,
                'benchmark_comparison' => $this->getWaterBenchmark(),
            ],
            'materials_performance' => [
                'score' => $this->materials_score,
                'sustainable_materials' => $this->sustainable_materials_used,
                'recycling_rate' => $this->recycling_rate,
                'benchmark_comparison' => $this->getMaterialsBenchmark(),
            ],
            'indoor_environment' => [
                'score' => $this->indoor_environment_score,
                'air_quality_measures' => $this->indoor_air_quality_measures,
                'thermal_comfort' => $this->thermal_comfort_analysis_performed,
                'acoustic_performance' => $this->acoustic_analysis_performed,
                'benchmark_comparison' => $this->getIndoorBenchmark(),
            ],
            'site_performance' => [
                'score' => $this->site_score,
                'green_space_ratio' => $this->green_space_ratio,
                'stormwater_management' => $this->stormwater_management,
                'biodiversity_protection' => $this->biodiversity_protection,
                'site_features' => $this->site_sustainability_features,
                'benchmark_comparison' => $this->getSiteBenchmark(),
            ],
        ];
    }

    private function getEnergyBenchmark(): array
    {
        $benchmarks = [
            'residential' => ['avg_index' => 150, 'excellent' => 100, 'good' => 120, 'poor' => 200],
            'commercial' => ['avg_index' => 200, 'excellent' => 150, 'good' => 180, 'poor' => 250],
            'industrial' => ['avg_index' => 300, 'excellent' => 200, 'good' => 250, 'poor' => 400],
        ];

        $buildingType = $this->building_type ?? 'residential';
        $benchmark = $benchmarks[$buildingType] ?? $benchmarks['residential'];

        return [
            'benchmark' => $benchmark['avg_index'],
            'performance' => $this->energy_performance_index <= $benchmark['excellent'] ? 'ممتاز' : 
                        ($this->energy_performance_index <= $benchmark['good'] ? 'جيد' : 'ضعيف'),
            'difference' => $benchmark['avg_index'] - $this->energy_performance_index,
        ];
    }

    private function getWaterBenchmark(): array
    {
        $benchmarks = [
            'residential' => ['avg_index' => 100, 'excellent' => 70, 'good' => 85, 'poor' => 150],
            'commercial' => ['avg_index' => 150, 'excellent' => 100, 'good' => 120, 'poor' => 200],
            'industrial' => ['avg_index' => 200, 'excellent' => 150, 'good' => 170, 'poor' => 300],
        ];

        $buildingType = $this->building_type ?? 'residential';
        $benchmark = $benchmarks[$buildingType] ?? $benchmarks['residential'];

        return [
            'benchmark' => $benchmark['avg_index'],
            'performance' => $this->water_performance_index <= $benchmark['excellent'] ? 'ممتاز' : 
                        ($this->water_performance_index <= $benchmark['good'] ? 'جيد' : 'ضعيف'),
            'difference' => $benchmark['avg_index'] - $this->water_performance_index,
        ];
    }

    private function getMaterialsBenchmark(): array
    {
        return [
            'benchmark_recycling_rate' => 50,
            'performance' => $this->recycling_rate >= 70 ? 'ممتاز' : 
                        ($this->recycling_rate >= 50 ? 'جيد' : 'ضعيف'),
            'difference' => $this->recycling_rate - 50,
        ];
    }

    private function getIndoorBenchmark(): array
    {
        return [
            'benchmark_score' => 75,
            'performance' => $this->indoor_environment_score >= 85 ? 'ممتاز' : 
                        ($this->indoor_environment_score >= 75 ? 'جيد' : 'ضعيف'),
            'difference' => $this->indoor_environment_score - 75,
        ];
    }

    private function getSiteBenchmark(): array
    {
        return [
            'benchmark_green_space' => 0.3, // 30% green space
            'performance' => $this->green_space_ratio >= 0.4 ? 'ممتاز' : 
                        ($this->green_space_ratio >= 0.3 ? 'جيد' : 'ضعيف'),
            'difference' => $this->green_space_ratio - 0.3,
        ];
    }

    public function getComplianceData(): array
    {
        return [
            'certification_compliance' => [
                'status' => $this->certification_status_text,
                'level' => $this->certification_level_text,
                'score' => $this->total_score,
                'expiry_date' => $this->expiry_date,
                'next_assessment' => $this->next_assessment_date,
                'valid' => $this->is_valid,
            ],
            'performance_compliance' => [
                'energy' => $this->energy_score >= 70,
                'water' => $this->water_score >= 70,
                'materials' => $this->materials_score >= 70,
                'indoor_environment' => $this->indoor_environment_score >= 70,
                'site' => $this->site_score >= 70,
            ],
            'monitoring_compliance' => [
                'performance_monitoring' => $this->performance_monitoring,
                'monitoring_systems' => $this->monitoring_systems,
                'maintenance_plan' => $this->maintenance_plan,
            ],
        ];
    }

    public function getCertificationEligibility(): array
    {
        $eligibility = [];
        
        if ($this->total_score >= 90) {
            $eligibility[] = 'LEED Platinum';
            $eligibility[] = 'BREEAM Outstanding';
        } elseif ($this->total_score >= 80) {
            $eligibility[] = 'LEED Gold';
            $eligibility[] = 'BREEAM Excellent';
        } elseif ($this->total_score >= 70) {
            $eligibility[] = 'LEED Silver';
            $eligibility[] = 'BREEAM Very Good';
        } elseif ($this->total_score >= 60) {
            $eligibility[] = 'LEED Certified';
            $eligibility[] = 'BREEAM Good';
        }
        
        return $eligibility;
    }

    public function verifyCertification(): array
    {
        return [
            'certificate_number' => $this->certification_number,
            'certification_standard' => $this->certification_standard_text,
            'certification_level' => $this->certification_level_text,
            'certification_date' => $this->certification_date,
            'expiry_date' => $this->expiry_date,
            'status' => $this->certification_status_text,
            'verification_url' => $this->verification_url,
            'is_valid' => $this->is_valid,
        ];
    }

    public function generateImprovementPlan(): array
    {
        $plan = [];
        
        if ($this->energy_score < 70) {
            $plan[] = [
                'category' => 'الطاقة',
                'current_score' => $this->energy_score,
                'target_score' => min(85, $this->energy_score + 20),
                'actions' => ['تحسين أنظمة التكييف والتدفئة', 'تركيب ألواح شمسية', 'استخدام أجهزة موفرة'],
                'timeline' => '3-6 أشهر',
                'estimated_cost' => 'مرتفع',
                'priority' => 'عاجل',
            ];
        }
        
        if ($this->water_score < 70) {
            $plan[] = [
                'category' => 'المياه',
                'current_score' => $this->water_score,
                'target_score' => min(85, $this->water_score + 20),
                'actions' => ['تركيب أنظمة تجميع مياه الأمطار', 'تحسين أنظمة الصرف', 'استخدام أجهزة منخفضة التدفق'],
                'timeline' => '2-4 أشهر',
                'estimated_cost' => 'متوسط',
                'priority' => 'مرتفع',
            ];
        }
        
        if ($this->materials_score < 70) {
            $plan[] = [
                'category' => 'المواد',
                'current_score' => $this->materials_score,
                'target_score' => min(85, $this->materials_score + 20),
                'actions' => ['استخدام مواد معاد تدويرها', 'اختيار مواد محلية', 'الحصول على شهادات خضراء'],
                'timeline' => '6-12 شهر',
                'estimated_cost' => 'مرتفع',
                'priority' => 'متوسط',
            ];
        }
        
        return $plan;
    }

    public function getEnvironmentalBenefits(): array
    {
        $benefits = [];
        
        // Energy benefits
        if ($this->renewable_energy_percentage > 0) {
            $benefits[] = [
                'category' => 'الطاقة',
                'description' => 'استخدام الطاقة المتجددة يقل من الاعتماد على الوقود الأحفوري',
                'impact' => 'تقليل انبعاثات الكربون',
                'quantity' => $this->renewable_energy_percentage . '%',
            ];
        }
        
        // Water benefits
        if ($this->water_conservation_measures) {
            $benefits[] = [
                'category' => 'المياه',
                'description' => 'حفظ المياه يقل من استهلاك الموارد المائية',
                'impact' => 'الحفاظ على الموارد المائية',
                'quantity' => count($this->water_conservation_measures) . ' إجراء',
            ];
        }
        
        // Green space benefits
        if ($this->green_space_ratio > 0) {
            $benefits[] = [
                'category' => 'المساحات الخضراء',
                'description' => 'المساحات الخضراء تحسن جودة الهواء والبيئة',
                'impact' => 'تحسين جودة الهواء والتنوع البيولوجي',
                'quantity' => ($this->green_space_ratio * 100) . '% من الموقع',
            ];
        }
        
        return $benefits;
    }

    public function getFinancialBenefits(): array
    {
        $benefits = [];
        
        // Energy savings
        if ($this->renewable_energy_percentage > 0) {
            $annualEnergyCost = $this->annual_energy_consumption * 0.15; // $0.15 per kWh
            $savings = $annualEnergyCost * ($this->renewable_energy_percentage / 100);
            
            $benefits[] = [
                'category' => 'توفير الطاقة',
                'description' => 'توفير في تكاليف الطاقة من خلال الطاقة المتجددة',
                'annual_savings' => round($savings, 2),
                'payback_period' => '5-10 سنوات',
            ];
        }
        
        // Water savings
        if ($this->water_conservation_measures) {
            $annualWaterCost = $this->annual_water_consumption * 0.002; // $0.002 per liter
            $savings = $annualWaterCost * 0.2; // Assumed 20% savings
            
            $benefits[] = [
                'category' => 'توفير المياه',
                'description' => 'توفير في تكاليف المياه من خلال الإجراءات المطبقة',
                'annual_savings' => round($savings, 2),
                'payback_period' => '2-5 سنوات',
            ];
        }
        
        return $benefits;
    }

    // Events
    protected static function booted()
    {
        static::created(function ($greenBuilding) {
            // Update property sustainability certification status if applicable
            if ($greenBuilding->certification_status === 'active') {
                $greenBuilding->propertySustainability->update([
                    'certification_status' => 'certified',
                ]);
            }
        });

        static::updated(function ($greenBuilding) {
            // Update property sustainability certification status if changed
            if ($greenBuilding->wasChanged('certification_status')) {
                $status = match($greenBuilding->certification_status) {
                    'active' => 'certified',
                    'expired', 'suspended', 'revoked' => 'not_certified',
                    default => 'in_progress',
                };
                
                $greenBuilding->propertySustainability->update([
                    'certification_status' => $status,
                ]);
            }
        });
    }
}
