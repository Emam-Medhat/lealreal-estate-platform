<?php

namespace App\Models\Sustainability;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EcoScore extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_sustainability_id',
        'overall_score',
        'energy_score',
        'water_score',
        'waste_score',
        'materials_score',
        'transport_score',
        'biodiversity_score',
        'air_quality_score',
        'noise_pollution_score',
        'community_impact_score',
        'innovation_score',
        'calculation_method',
        'calculation_version',
        'data_sources',
        'factors_considered',
        'weightings_used',
        'benchmark_comparison',
        'improvement_areas',
        'strength_areas',
        'recommendations',
        'target_score',
        'achievement_date',
        'certification_eligibility',
        'calculated_at',
        'calculated_by',
        'notes',
    ];

    protected $casts = [
        'overall_score' => 'decimal:2',
        'energy_score' => 'decimal:2',
        'water_score' => 'decimal:2',
        'waste_score' => 'decimal:2',
        'materials_score' => 'decimal:2',
        'transport_score' => 'decimal:2',
        'biodiversity_score' => 'decimal:2',
        'air_quality_score' => 'decimal:2',
        'noise_pollution_score' => 'decimal:2',
        'community_impact_score' => 'decimal:2',
        'innovation_score' => 'decimal:2',
        'calculated_at' => 'datetime',
        'achievement_date' => 'date',
        'data_sources' => 'array',
        'factors_considered' => 'array',
        'weightings_used' => 'array',
        'benchmark_comparison' => 'array',
        'improvement_areas' => 'array',
        'strength_areas' => 'array',
        'recommendations' => 'array',
        'certification_eligibility' => 'array',
    ];

    // Relationships
    public function propertySustainability(): BelongsTo
    {
        return $this->belongsTo(PropertySustainability::class);
    }

    public function calculator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    // Scopes
    public function scopeHighScore($query)
    {
        return $query->where('overall_score', '>=', 80);
    }

    public function scopeLowScore($query)
    {
        return $query->where('overall_score', '<', 60);
    }

    public function scopeRecent($query)
    {
        return $query->where('calculated_at', '>=', now()->subMonths(6));
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('calculation_method', $method);
    }

    // Attributes
    public function getPerformanceLevelAttribute(): string
    {
        if ($this->overall_score >= 90) return 'ممتاز';
        if ($this->overall_score >= 80) return 'جيد جداً';
        if ($this->overall_score >= 70) return 'جيد';
        if ($this->overall_score >= 60) return 'متوسط';
        return 'ضعيف';
    }

    public function getCalculationMethodTextAttribute(): string
    {
        return match($this->calculation_method) {
            'standard' => 'قياسي',
            'advanced' => 'متقدم',
            'custom' => 'مخصص',
            default => 'غير معروف',
        };
    }

    public function getDaysUntilTargetAttribute(): ?int
    {
        if (!$this->achievement_date) return null;
        return now()->diffInDays($this->achievement_date, false);
    }

    public function getIsTargetAchievedAttribute(): bool
    {
        return $this->achievement_date && $this->achievement_date->isPast();
    }

    public function getScoreProgressAttribute(): float
    {
        if (!$this->target_score) return 0;
        return min(100, ($this->overall_score / $this->target_score) * 100);
    }

    public function getScoreChangeAttribute(): ?float
    {
        $previousScore = $this->propertySustainability
            ->ecoScores()
            ->where('id', '<', $this->id)
            ->orderBy('calculated_at', 'desc')
            ->first();

        if (!$previousScore) return null;
        
        return $this->overall_score - $previousScore->overall_score;
    }

    public function getTrendAttribute(): string
    {
        $change = $this->score_change;
        
        if ($change === null) return 'no_data';
        if ($change > 0) return 'improving';
        if ($change < 0) return 'declining';
        return 'stable';
    }

    // Methods
    public function calculateOverallScore(): float
    {
        $weights = [
            'energy' => 0.25,
            'water' => 0.20,
            'waste' => 0.15,
            'materials' => 0.15,
            'transport' => 0.10,
            'biodiversity' => 0.05,
            'air_quality' => 0.03,
            'noise_pollution' => 0.02,
            'community_impact' => 0.03,
            'innovation' => 0.02,
        ];

        $totalScore = (
            $this->energy_score * $weights['energy'] +
            $this->water_score * $weights['water'] +
            $this->waste_score * $weights['waste'] +
            $this->materials_score * $weights['materials'] +
            $this->transport_score * $weights['transport'] +
            $this->biodiversity_score * $weights['biodiversity'] +
            $this->air_quality_score * $weights['air_quality'] +
            $this->noise_pollution_score * $weights['noise_pollution'] +
            $this->community_impact_score * $weights['community_impact'] +
            $this->innovation_score * $weights['innovation']
        );

        return round($totalScore, 1);
    }

    public function updateOverallScore(): void
    {
        $this->overall_score = $this->calculateOverallScore();
        $this->save();
    }

    public function getScoreBreakdown(): array
    {
        $weights = [
            'energy' => 25,
            'water' => 20,
            'waste' => 15,
            'materials' => 15,
            'transport' => 10,
            'biodiversity' => 5,
            'air_quality' => 3,
            'noise_pollution' => 2,
            'community_impact' => 3,
            'innovation' => 2,
        ];

        return [
            'energy' => [
                'score' => $this->energy_score,
                'weight' => $weights['energy'],
                'contribution' => $this->energy_score * $weights['energy'],
                'status' => $this->getCategoryStatus($this->energy_score),
            ],
            'water' => [
                'score' => $this->water_score,
                'weight' => $weights['water'],
                'contribution' => $this->water_score * $weights['water'],
                'status' => $this->getCategoryStatus($this->water_score),
            ],
            'waste' => [
                'score' => $this->waste_score,
                'weight' => $weights['waste'],
                'contribution' => $this->waste_score * $weights['waste'],
                'status' => $this->getCategoryStatus($this->waste_score),
            ],
            'materials' => [
                'score' => $this->materials_score,
                'weight' => $weights['materials'],
                'contribution' => $this->materials_score * $weights['materials'],
                'status' => $this->getCategoryStatus($this->materials_score),
            ],
            'transport' => [
                'score' => $this->transport_score,
                'weight' => $weights['transport'],
                'contribution' => $this->transport_score * $weights['transport'],
                'status' => $this->getCategoryStatus($this->transport_score),
            ],
            'biodiversity' => [
                'score' => $this->biodiversity_score,
                'weight' => $weights['biodiversity'],
                'contribution' => $this->biodiversity_score * $weights['biodiversity'],
                'status' => $this->getCategoryStatus($this->biodiversity_score),
            ],
        ];
    }

    private function getCategoryStatus(float $score): string
    {
        if ($score >= 90) return 'ممتاز';
        if ($score >= 80) return 'جيد جداً';
        if ($score >= 70) return 'جيد';
        if ($score >= 60) return 'متوسط';
        return 'ضعيف';
    }

    public function getTopPerformingCategories(): array
    {
        $categories = $this->getScoreBreakdown();
        
        // Sort by contribution
        uasort($categories, function ($a, $b) {
            return $b['contribution'] <=> $a['contribution'];
        });
        
        return array_slice($categories, 0, 3, true);
    }

    public function getLowPerformingCategories(): array
    {
        $categories = $this->getScoreBreakdown();
        
        // Sort by score (ascending)
        uasort($categories, function ($a, $b) {
            return $a['score'] <=> $b['score'];
        });
        
        return array_slice($categories, 0, 3, true);
    }

    public function getImprovementOpportunities(): array
    {
        $opportunities = [];
        $categories = $this->getScoreBreakdown();
        
        foreach ($categories as $category => $data) {
            if ($data['score'] < 70) {
                $opportunities[] = [
                    'category' => $category,
                    'current_score' => $data['score'],
                    'target_score' => min(85, $data['score'] + 20),
                    'potential_improvement' => min(85, $data['score'] + 20) - $data['score'],
                    'priority' => $data['score'] < 50 ? 'عاجل' : 'مرتفع',
                    'recommendations' => $this->getCategoryRecommendations($category),
                ];
            }
        }
        
        return $opportunities;
    }

    private function getCategoryRecommendations(string $category): array
    {
        $recommendations = [
            'energy' => [
                'تركيب ألواح شمسية',
                'تحسين عزل المبنى',
                'استخدام أجهزة موفرة للطاقة',
                'تركيب أنظمة إضاءة LED',
            ],
            'water' => [
                'تركيب نظام تجميع مياه الأمطار',
                'إعادة تدوير المياه الرمادية',
                'استخدام أجهزة منخفضة التدفق',
                'تحسين أنظمة الري',
            ],
            'waste' => [
                'زيادة إعادة التدوير',
                'تنفيذ برنامج الكمبوست',
                'تقليل استخدام البلاستيك',
                'شراء منتجات صديقة',
            ],
            'materials' => [
                'استخدام مواد معاد تدويرها',
                'اختيار مواد محلية',
                'استخدام مواد طبيعية',
                'الحصول على شهادات خضراء',
            ],
            'transport' => [
                'تشجيع استخدام النقل العام',
                'توفير أماكن لدراجات الهوائية',
                'تركيب محطات شحن كهربائي',
                'تشجيع العمل عن بعد',
            ],
            'biodiversity' => [
                'زراعة نباتات محلية',
                'إنشاء حدائق خضراء',
                'توفير موائل للحياة البرية',
                'تقليل استخدام المبيدات',
            ],
        ];
        
        return $recommendations[$category] ?? ['تحسين الأداء العام'];
    }

    public function getCertificationReadiness(): array
    {
        $readiness = [];
        
        // Check LEED readiness
        $leedScore = $this->energy_score * 0.35 + 
                    $this->water_score * 0.25 + 
                    $this->materials_score * 0.20 + 
                    $this->waste_score * 0.10 + 
                    $this->innovation_score * 0.10;
        
        $readiness['leed'] = [
            'score' => round($leedScore, 1),
            'level' => $this->getLEEDLevel($leedScore),
            'ready' => $leedScore >= 60,
            'gaps' => $this->getLEEDGaps($leedScore),
        ];
        
        // Check BREEAM readiness
        $breeamScore = $this->energy_score * 0.30 + 
                     $this->water_score * 0.20 + 
                     $this->materials_score * 0.25 + 
                     $this->waste_score * 0.15 + 
                     $this->innovation_score * 0.10;
        
        $readiness['breeam'] = [
            'score' => round($breeamScore, 1),
            'level' => $this->getBREEAMLevel($breeamScore),
            'ready' => $breeamScore >= 55,
            'gaps' => $this->getBREEAMGaps($breeamScore),
        ];
        
        return $readiness;
    }

    private function getLEEDLevel(float $score): string
    {
        if ($score >= 80) return 'Platinum';
        if ($score >= 65) return 'Gold';
        if ($score >= 50) return 'Silver';
        if ($score >= 40) return 'Certified';
        return 'Not Ready';
    }

    private function getBREEAMLevel(float $score): string
    {
        if ($score >= 85) return 'Outstanding';
        if ($score >= 70) return 'Excellent';
        if ($score >= 55) return 'Very Good';
        if ($score >= 45) return 'Good';
        return 'Not Ready';
    }

    private function getLEEDGaps(float $score): array
    {
        $gaps = [];
        
        if ($this->energy_score < 70) $gaps[] = 'تحسين كفاءة الطاقة';
        if ($this->water_score < 70) $gaps[] = 'تحسين كفاءة المياه';
        if ($this->materials_score < 70) $gaps[] = 'تحسين استخدام المواد';
        if ($this->waste_score < 70) $gaps[] = 'تحسين إدارة النفايات';
        
        return $gaps;
    }

    private function getBREEAMGaps(float $score): array
    {
        $gaps = [];
        
        if ($this->energy_score < 65) $gaps[] = 'تحسين أداء الطاقة';
        if ($this->water_score < 65) $gaps[] = 'تحسين إدارة المياه';
        if ($this->materials_score < 65) $gaps[] = 'تحسين المواد';
        if ($this->waste_score < 65) $gaps[] = 'تحسين النفايات';
        
        return $gaps;
    }

    public function getBenchmarkComparison(): array
    {
        $benchmarks = [
            'residential' => ['avg_score' => 65, 'energy' => 60, 'water' => 65, 'materials' => 70],
            'commercial' => ['avg_score' => 70, 'energy' => 65, 'water' => 70, 'materials' => 75],
            'industrial' => ['avg_score' => 60, 'energy' => 55, 'water' => 60, 'materials' => 65],
            'mixed' => ['avg_score' => 68, 'energy' => 62, 'water' => 68, 'materials' => 72],
        ];

        $propertyType = $this->propertySustainability->property->type ?? 'mixed';
        $benchmark = $benchmarks[$propertyType] ?? $benchmarks['mixed'];

        return [
            'overall_difference' => $this->overall_score - $benchmark['avg_score'],
            'energy_difference' => $this->energy_score - $benchmark['energy'],
            'water_difference' => $this->water_score - $benchmark['water'],
            'materials_difference' => $this->materials_score - $benchmark['materials'],
            'performance_percentile' => $this->calculatePercentile($this->overall_score, $benchmark['avg_score']),
            'ranking' => $this->getRanking($benchmark['avg_score']),
        ];
    }

    private function calculatePercentile(float $value, float $benchmark): float
    {
        $difference = $value - $benchmark;
        $percentile = 50 + ($difference * 2);
        
        return max(0, min(100, $percentile));
    }

    private function getRanking(float $benchmark): string
    {
        if ($this->overall_score >= $benchmark + 20) return 'أعلى من المتوسط';
        if ($this->overall_score >= $benchmark - 10) return 'قريب من المتوسط';
        return 'أقل من المتوسط';
    }

    public function getHistoricalTrend(): array
    {
        $historicalScores = $this->propertySustainability
            ->ecoScores()
            ->orderBy('calculated_at', 'asc')
            ->get();

        if ($historicalScores->count() < 2) {
            return [
                'trend' => 'insufficient_data',
                'change' => 0,
                'direction' => 'neutral',
                'volatility' => 0,
            ];
        }

        $scores = $historicalScores->pluck('overall_score')->toArray();
        $changes = [];
        
        for ($i = 1; $i < count($scores); $i++) {
            $changes[] = $scores[$i] - $scores[$i-1];
        }
        
        $avgChange = array_sum($changes) / count($changes);
        $volatility = $this->calculateVolatility($changes);
        
        return [
            'trend' => $avgChange > 1 ? 'improving' : ($avgChange < -1 ? 'declining' : 'stable'),
            'average_change' => round($avgChange, 2),
            'direction' => $avgChange > 0 ? 'up' : ($avgChange < 0 ? 'down' : 'neutral'),
            'volatility' => round($volatility, 2),
            'stability' => $volatility < 5 ? 'stable' : ($volatility < 15 ? 'moderate' : 'volatile'),
        ];
    }

    private function calculateVolatility(array $changes): float
    {
        if (count($changes) < 2) return 0;
        
        $mean = array_sum($changes) / count($changes);
        $squaredDiffs = array_map(function($change) use ($mean) {
            return pow($change - $mean, 2);
        }, $changes);
        
        $variance = array_sum($squaredDiffs) / (count($changes) - 1);
        
        return sqrt($variance);
    }

    public function generateImprovementPlan(): array
    {
        $plan = [];
        $opportunities = $this->getImprovementOpportunities();
        
        foreach ($opportunities as $opportunity) {
            $plan[] = [
                'category' => $opportunity['category'],
                'current_score' => $opportunity['current_score'],
                'target_score' => $opportunity['target_score'],
                'improvement_needed' => $opportunity['potential_improvement'],
                'priority' => $opportunity['priority'],
                'actions' => $opportunity['recommendations'],
                'estimated_timeline' => $this->getTimelineForCategory($opportunity['category']),
                'estimated_cost' => $this->getCostForCategory($opportunity['category']),
                'expected_impact' => $this->getImpactForCategory($opportunity['category']),
            ];
        }
        
        return $plan;
    }

    private function getTimelineForCategory(string $category): string
    {
        $timelines = [
            'energy' => '3-6 أشهر',
            'water' => '1-3 أشهر',
            'waste' => '1-2 أشهر',
            'materials' => '6-12 شهر',
            'transport' => '2-4 أشهر',
            'biodiversity' => '6-12 شهر',
        ];
        
        return $timelines[$category] ?? '3-6 أشهر';
    }

    private function getCostForCategory(string $category): string
    {
        $costs = [
            'energy' => 'مرتفع',
            'water' => 'متوسط',
            'waste' => 'منخفض',
            'materials' => 'مرتفع',
            'transport' => 'متوسط',
            'biodiversity' => 'منخفض إلى متوسط',
        ];
        
        return $costs[$category] ?? 'متوسط';
    }

    private function getImpactForCategory(string $category): string
    {
        $impacts = [
            'energy' => 'عالي',
            'water' => 'متوسط إلى عالي',
            'waste' => 'متوسط',
            'materials' => 'عالي',
            'transport' => 'متوسط',
            'biodiversity' => 'منخفض إلى متوسط',
        ];
        
        return $impacts[$category] ?? 'متوسط';
    }

    public function getScoreValidation(): array
    {
        $validation = [
            'is_valid' => true,
            'issues' => [],
            'warnings' => [],
        ];
        
        // Check for unrealistic scores
        if ($this->overall_score > 100 || $this->overall_score < 0) {
            $validation['is_valid'] = false;
            $validation['issues'][] = 'الدرجة الإجمالية خارج النطاق المسموح (0-100)';
        }
        
        // Check for category consistency
        $categories = ['energy_score', 'water_score', 'waste_score', 'materials_score'];
        foreach ($categories as $category) {
            $score = $this->$category;
            if ($score > 100 || $score < 0) {
                $validation['is_valid'] = false;
                $validation['issues'][] = "درجة {$category} خارج النطاق المسموح (0-100)";
            }
        }
        
        // Check for large discrepancies
        $categoryScores = [$this->energy_score, $this->water_score, $this->waste_score, $this->materials_score];
        $maxScore = max($categoryScores);
        $minScore = min($categoryScores);
        
        if ($maxScore - $minScore > 50) {
            $validation['warnings'][] = 'هناك تباين كبير بين درجات الفئات المختلفة';
        }
        
        // Check calculation consistency
        $calculatedOverall = $this->calculateOverallScore();
        if (abs($this->overall_score - $calculatedOverall) > 1) {
            $validation['warnings'][] = 'الدرجة الإجمالية المحسوبة لا تتطابق الدرجة المخزنة';
        }
        
        return $validation;
    }

    // Events
    protected static function booted()
    {
        static::created(function ($ecoScore) {
            // Update property sustainability eco score
            $ecoScore->propertySustainability->update([
                'eco_score' => $ecoScore->overall_score,
            ]);
        });

        static::updated(function ($ecoScore) {
            // Update property sustainability if overall score changed
            if ($ecoScore->wasChanged('overall_score')) {
                $ecoScore->propertySustainability->update([
                    'eco_score' => $ecoScore->overall_score,
                ]);
            }
        });
    }
}
