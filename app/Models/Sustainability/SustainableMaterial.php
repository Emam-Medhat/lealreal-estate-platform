<?php

namespace App\Models\Sustainability;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SustainableMaterial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_sustainability_id',
        'material_name',
        'material_type',
        'category',
        'manufacturer',
        'supplier',
        'quantity',
        'unit',
        'cost_per_unit',
        'total_cost',
        'sustainability_rating',
        'recycled_content_percentage',
        'renewable_content_percentage',
        'local_content_percentage',
        'certification',
        'certification_body',
        'certification_date',
        'expiry_date',
        'lifespan_years',
        'maintenance_requirements',
        'installation_date',
        'installation_location',
        'carbon_footprint',
        'energy_consumption',
        'water_usage',
        'waste_generated',
        'is_local',
        'local_distance_km',
        'end_of_life_plan',
        'recyclable',
        'biodegradable',
        'health_safety_rating',
        'fire_resistance_rating',
        'durability_rating',
        'maintenance_cost_per_year',
        'warranty_period',
        'technical_specifications',
        'environmental_impact',
        'documents',
        'images',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'cost_per_unit' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'sustainability_rating' => 'decimal:2',
        'recycled_content_percentage' => 'decimal:2',
        'renewable_content_percentage' => 'decimal:2',
        'local_content_percentage' => 'decimal:2',
        'certification_date' => 'date',
        'expiry_date' => 'date',
        'lifespan_years' => 'integer',
        'installation_date' => 'date',
        'carbon_footprint' => 'decimal:2',
        'energy_consumption' => 'decimal:2',
        'water_usage' => 'decimal:2',
        'waste_generated' => 'decimal:2',
        'local_distance_km' => 'decimal:2',
        'maintenance_cost_per_year' => 'decimal:2',
        'warranty_period' => 'integer',
        'health_safety_rating' => 'integer',
        'fire_resistance_rating' => 'integer',
        'durability_rating' => 'integer',
        'technical_specifications' => 'array',
        'environmental_impact' => 'array',
        'documents' => 'array',
        'images' => 'array',
        'is_local' => 'boolean',
        'recyclable' => 'boolean',
        'biodegradable' => 'boolean',
    ];

    // Relationships
    public function propertySustainability(): BelongsTo
    {
        return $this->belongsTo(PropertySustainability::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('material_type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeCertified($query)
    {
        return $query->whereNotNull('certification');
    }

    public function scopeLocal($query)
    {
        return $query->where('is_local', true);
    }

    public function scopeRecyclable($query)
    {
        return $query->where('recyclable', true);
    }

    public function scopeHighSustainability($query)
    {
        return $query->where('sustainability_rating', '>=', 80);
    }

    // Attributes
    public function getMaterialTypeTextAttribute(): string
    {
        return match($this->material_type) {
            'recycled' => 'مواد معاد تدويرها',
            'renewable' => 'مواد متجددة',
            'natural' => 'مواد طبيعية',
            'low_impact' => 'مواد منخفضة التأثير',
            'certified' => 'مواد معتمدة',
            'local' => 'مواد محلية',
            default => 'غير معروف',
        };
    }

    public function getUnitTextAttribute(): string
    {
        return match($this->unit) {
            'kg' => 'كيلوجرام',
            'tons' => 'طن',
            'meters' => 'متر',
            'square_meters' => 'متر مربع',
            'cubic_meters' => 'متر مكعب',
            'pieces' => 'قطعة',
            default => 'غير معروف',
        };
    }

    public function getSustainabilityRatingTextAttribute(): string
    {
        if ($this->sustainability_rating >= 90) return 'A+ - ممتاز';
        if ($this->sustainability_rating >= 80) return 'A - ممتاز';
        if ($this->sustainability_rating >= 70) return 'B - جيد جداً';
        if ($this->sustainability_rating >= 60) return 'C - جيد';
        return 'D - مقبول';
    }

    public function getAgeAttribute(): int
    {
        return $this->installation_date ? $this->installation_date->age : 0;
    }

    public function getRemainingLifespanAttribute(): int
    {
        return max(0, $this->lifespan_years - $this->age);
    }

    public function getIsWarrantyValidAttribute(): bool
    {
        return $this->warranty_period > 0 && $this->age < $this->warranty_period;
    }

    public function getWarrantyExpiryDateAttribute(): ?\Carbon\Carbon
    {
        return $this->installation_date ? $this->installation_date->addYears($this->warranty_period) : null;
    }

    public function getIsCertificationValidAttribute(): bool
    {
        return $this->certification_date && 
               $this->expiry_date && 
               now()->between($this->certification_date, $this->expiry_date);
    }

    public function getTotalCarbonFootprintAttribute(): float
    {
        return $this->carbon_footprint * $this->quantity;
    }

    public function getTotalEnergyConsumptionAttribute(): float
    {
        return $this->energy_consumption * $this->quantity;
    }

    public function getTotalWaterUsageAttribute(): float
    {
        return $this->water_usage * $this->quantity;
    }

    public function getTotalWasteGeneratedAttribute(): float
    {
        return $this->waste_generated * $this->quantity;
    }

    // Methods
    public function calculateSustainabilityScore(): float
    {
        $score = 0;
        
        // Base score from rating
        $ratingScores = ['A+' => 40, 'A' => 35, 'B' => 30, 'C' => 25, 'D' => 20];
        $score += $ratingScores[$this->sustainability_rating_text] ?? 20;
        
        // Content scores
        $score += ($this->recycled_content_percentage / 100) * 15;
        $score += ($this->renewable_content_percentage / 100) * 15;
        $score += ($this->local_content_percentage / 100) * 10;
        
        // Feature scores
        if ($this->recyclable) $score += 10;
        if ($this->biodegradable) $score += 10;
        if ($this->certification) $score += 10;
        if ($this->is_local) $score += 5;
        
        return min(100, $score);
    }

    public function getEnvironmentalImpactScore(): float
    {
        // Lower impact = higher score
        $impactScore = 100;
        
        // Deduct points for environmental factors
        if ($this->carbon_footprint > 0) {
            $impactScore -= min(30, $this->carbon_footprint / 10);
        }
        
        if ($this->energy_consumption > 0) {
            $impactScore -= min(20, $this->energy_consumption / 100);
        }
        
        if ($this->water_usage > 0) {
            $impactScore -= min(20, $this->water_usage / 1000);
        }
        
        if ($this->waste_generated > 0) {
            $impactScore -= min(30, $this->waste_generated / 10);
        }
        
        return max(0, $impactScore);
    }

    public function getCostEfficiencyScore(): float
    {
        // Lower cost per unit with higher sustainability rating = better efficiency
        $ratingMultipliers = ['A+' => 1.5, 'A' => 1.3, 'B' => 1.1, 'C' => 0.9, 'D' => 0.7];
        $multiplier = $ratingMultipliers[$this->sustainability_rating_text] ?? 0.7;
        
        return ($this->cost_per_unit / $multiplier) * 100; // Normalized score
    }

    public function getLifecycleCost(): float
    {
        $totalCost = $this->total_cost;
        $maintenanceCost = $this->maintenance_cost_per_year * $this->remaining_lifespan;
        
        return $totalCost + $maintenanceCost;
    }

    public function getEndOfLifeValue(): float
    {
        $value = 0;
        
        if ($this->recyclable) {
            $value = $this->total_cost * 0.2; // 20% of original cost
        }
        
        if ($this->biodegradable) {
            $value += $this->total_cost * 0.05; // Additional 5% for biodegradable
        }
        
        return $value;
    }

    public function getNetLifecycleCost(): float
    {
        return $this->lifecycle_cost - $this->end_of_life_value;
    }

    public function getMaterialProfile(): array
    {
        return [
            'basic_info' => [
                'name' => $this->material_name,
                'type' => $this->material_type_text,
                'category' => $this->category,
                'manufacturer' => $this->manufacturer,
                'supplier' => $this->supplier,
            ],
            'specifications' => [
                'quantity' => $this->quantity . ' ' . $this->unit_text,
                'cost_per_unit' => $this->cost_per_unit,
                'total_cost' => $this->total_cost,
                'lifespan' => $this->lifespan_years . ' سنة',
                'warranty' => $this->warranty_period . ' سنة',
            ],
            'sustainability' => [
                'rating' => $this->sustainability_rating_text,
                'recycled_content' => $this->recycled_content_percentage . '%',
                'renewable_content' => $this->renewable_content_percentage . '%',
                'local_content' => $this->local_content_percentage . '%',
                'certification' => $this->certification ?? 'غير معتمد',
            ],
            'environmental' => [
                'carbon_footprint' => $this->total_carbon_footprint,
                'energy_consumption' => $this->total_energy_consumption,
                'water_usage' => $this->total_water_usage,
                'waste_generated' => $this->total_waste_generated,
                'recyclable' => $this->recyclable ? 'نعم' : 'لا',
                'biodegradable' => $this->biodegradable ? 'نعم' : 'لا',
            ],
        ];
    }

    public function getPerformanceMetrics(): array
    {
        return [
            'sustainability_score' => $this->calculateSustainabilityScore(),
            'environmental_impact_score' => $this->getEnvironmentalImpactScore(),
            'cost_efficiency_score' => $this->getCostEfficiencyScore(),
            'health_safety_score' => $this->health_safety_rating * 10,
            'fire_resistance_score' => $this->fire_resistance_rating * 10,
            'durability_score' => $this->durability_rating * 10,
            'overall_score' => $this->calculateOverallScore(),
        ];
    }

    private function calculateOverallScore(): float
    {
        $weights = [
            'sustainability' => 0.3,
            'environmental' => 0.25,
            'cost_efficiency' => 0.2,
            'health_safety' => 0.15,
            'durability' => 0.1,
        ];

        $scores = $this->getPerformanceMetrics();
        
        $overallScore = (
            $scores['sustainability_score'] * $weights['sustainability'] +
            $scores['environmental_impact_score'] * $weights['environmental'] +
            $scores['cost_efficiency_score'] * $weights['cost_efficiency'] +
            $scores['health_safety_score'] * $weights['health_safety'] +
            $scores['durability_score'] * $weights['durability']
        );

        return round($overallScore, 1);
    }

    public function getMaintenanceSchedule(): array
    {
        $schedule = [];
        
        // Monthly checks
        $schedule['monthly'] = [
            'visual_inspection',
            'cleaning_check',
        ];
        
        // Quarterly maintenance
        $schedule['quarterly'] = [
            'detailed_inspection',
            'performance_check',
        ];
        
        // Annual maintenance
        $schedule['annual'] = [
            'comprehensive_maintenance',
            'safety_inspection',
            'warranty_validation',
        ];
        
        return $schedule;
    }

    public function getNextMaintenanceDate(): \Carbon\Carbon
    {
        return $this->installation_date->addMonths(3);
    }

    public function getReplacementRecommendation(): array
    {
        if ($this->remaining_lifespan > 5) {
            return [
                'action' => 'continue_use',
                'reason' => 'المادة لا تزال في حالة جيدة',
                'timeline' => 'لا يوجد',
                'priority' => 'منخفض',
            ];
        }
        
        if ($this->remaining_lifespan > 2) {
            return [
                'action' => 'plan_replacement',
                'reason' => 'يجب التخطيط للاستبدال قريباً',
                'timeline' => '1-2 سنة',
                'priority' => 'متوسط',
            ];
        }
        
        return [
            'action' => 'replace_soon',
            'reason' => 'المادة قريبة من نهاية عمرها الافتراضي',
            'timeline' => '0-6 أشهر',
            'priority' => 'مرتفع',
        ];
    }

    public function getAlternatives(): array
    {
        // Simplified alternative recommendations
        $alternatives = [];
        
        if ($this->material_type === 'recycled') {
            $alternatives[] = [
                'material' => 'مواد طبيعية',
                'benefits' => ['تأثير بيئي أقل', 'متجددة'],
                'cost_impact' => '+10-20%',
                'availability' => 'جيدة',
            ];
        }
        
        if ($this->material_type === 'natural') {
            $alternatives[] = [
                'material' => 'مواد معاد تدويرها',
                'benefits' => ['تكلفة أقل', 'دورة حياة معروفة'],
                'cost_impact' => '-5-15%',
                'availability' => 'ممتازة',
            ];
        }
        
        return $alternatives;
    }

    public function getComplianceStatus(): array
    {
        $standards = [
            'building_code' => $this->health_safety_rating >= 7,
            'fire_safety' => $this->fire_resistance_rating >= 7,
            'environmental' => $this->sustainability_rating >= 60,
            'local_regulations' => $this->certification || $this->is_local,
        ];

        $compliantStandards = array_filter($standards);
        $compliancePercentage = (count($compliantStandards) / count($standards)) * 100;

        return [
            'overall_compliance' => $compliancePercentage >= 75,
            'compliance_percentage' => round($compliancePercentage, 1),
            'standards_met' => array_keys($compliantStandards),
            'standards_not_met' => array_keys(array_diff_key($standards, $compliantStandards)),
            'recommendations' => $this->getComplianceRecommendations($standards),
        ];
    }

    private function getComplianceRecommendations(array $standards): array
    {
        $recommendations = [];

        if (!$standards['building_code']) {
            $recommendations[] = 'تحسين معايير السلامة والصحة لتلبية كود البناء';
        }

        if (!$standards['fire_safety']) {
            $recommendations[] = 'تحسين مقاومة الحريق لتلبية معايير السلامة';
        }

        if (!$standards['environmental']) {
            $recommendations[] = 'تحسين تقييم الاستدامة لتلبية المعايير البيئية';
        }

        if (!$standards['local_regulations']) {
            $recommendations[] = 'الحصول على شهادات معتمدة أو استخدام مواد محلية';
        }

        return $recommendations;
    }

    public function getDocumentation(): array
    {
        return [
            'certificates' => $this->certification ? [
                'name' => $this->certification,
                'body' => $this->certification_body,
                'date' => $this->certification_date,
                'expiry' => $this->expiry_date,
                'valid' => $this->is_certification_valid,
            ] : null,
            'technical_docs' => $this->documents,
            'images' => $this->images,
            'specifications' => $this->technical_specifications,
        ];
    }

    public function generateLifecycleReport(): array
    {
        return [
            'installation' => [
                'date' => $this->installation_date,
                'location' => $this->installation_location,
                'cost' => $this->total_cost,
            ],
            'operation' => [
                'age' => $this->age . ' سنة',
                'remaining_lifespan' => $this->remaining_lifespan . ' سنة',
                'maintenance_cost' => $this->maintenance_cost_per_year,
                'total_maintenance' => $this->maintenance_cost_per_year * $this->age,
            ],
            'environmental' => [
                'total_carbon_footprint' => $this->total_carbon_footprint,
                'total_energy_consumption' => $this->total_energy_consumption,
                'total_water_usage' => $this->total_water_usage,
                'total_waste_generated' => $this->total_waste_generated,
            ],
            'end_of_life' => [
                'recyclable' => $this->recyclable,
                'biodegradable' => $this->biodegradable,
                'estimated_value' => $this->end_of_life_value,
                'plan' => $this->end_of_life_plan,
            ],
            'financial' => [
                'initial_cost' => $this->total_cost,
                'maintenance_cost' => $this->maintenance_cost_per_year * $this->age,
                'lifecycle_cost' => $this->lifecycle_cost,
                'net_cost' => $this->net_lifecycle_cost,
                'cost_per_year' => $this->lifecycle_cost / max(1, $this->age),
            ],
        ];
    }
}
