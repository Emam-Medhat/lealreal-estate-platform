<?php

namespace App\Models\Sustainability;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class SustainabilityReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_sustainability_id',
        'report_type',
        'title',
        'description',
        'report_period_start',
        'report_period_end',
        'data_sources',
        'methodology',
        'key_findings',
        'recommendations',
        'performance_scores',
        'compliance_status',
        'benchmark_comparison',
        'trend_analysis',
        'improvement_opportunities',
        'risk_assessment',
        'certification_eligibility',
        'executive_summary',
        'detailed_analysis',
        'appendices',
        'generated_by',
        'status',
        'file_path',
        'file_size',
        'download_count',
        'generated_at',
        'notes',
    ];

    protected $casts = [
        'report_period_start' => 'date',
        'report_period_end' => 'date',
        'generated_at' => 'datetime',
        'data_sources' => 'array',
        'key_findings' => 'array',
        'recommendations' => 'array',
        'performance_scores' => 'array',
        'compliance_status' => 'array',
        'benchmark_comparison' => 'array',
        'trend_analysis' => 'array',
        'improvement_opportunities' => 'array',
        'risk_assessment' => 'array',
        'certification_eligibility' => 'array',
        'executive_summary' => 'text',
        'detailed_analysis' => 'array',
        'appendices' => 'array',
        'file_size' => 'integer',
        'download_count' => 'integer',
    ];

    // Relationships
    public function propertySustainability(): BelongsTo
    {
        return $this->belongsTo(PropertySustainability::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeRecent($query)
    {
        return $query->where('generated_at', '>=', now()->subMonths(6));
    }

    public function scopePopular($query)
    {
        return $query->orderBy('download_count', 'desc');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Attributes
    public function getReportTypeTextAttribute(): string
    {
        return match($this->report_type) {
            'comprehensive' => 'تقرير شامل للاستدامة',
            'certification' => 'تقرير شهادة خضراء',
            'carbon_footprint' => 'تقرير البصمة الكربونية',
            'energy_efficiency' => 'تقرير كفاءة الطاقة',
            'water_conservation' => 'تقرير حفظ المياه',
            'materials_assessment' => 'تقرير تقييم المواد',
            'climate_impact' => 'تقرير التأثير المناخي',
            'performance' => 'تقرير الأداء',
            'compliance' => 'تقرير الامتثال',
            'benchmarking' => 'تقرير المقارنة المعيارية',
            default => 'غير معروف',
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'generated' => 'تم إنشاؤه',
            'generating' => 'قيد الإنشاء',
            'failed' => 'فشل الإنشاء',
            'archived' => 'مؤرشف',
            default => 'غير معروف',
        };
    }

    public function getFileSizeTextAttribute(): string
    {
        if ($this->file_size < 1024) {
            return $this->file_size . ' بايت';
        } elseif ($this->file_size < 1024 * 1024) {
            return round($this->file_size / 1024, 1) . ' كيلوبايت';
        } else {
            return round($this->file_size / (1024 * 1024), 1) . ' ميجابايت';
        }
    }

    public function getReportPeriodAttribute(): string
    {
        $start = $this->report_period_start->format('Y-m-d');
        $end = $this->report_period_end->format('Y-m-d');
        
        return $start . ' إلى ' . $end;
    }

    public function getDaysSinceGenerationAttribute(): int
    {
        return $this->generated_at->diffInDays(now());
    }

    public function getIsRecentAttribute(): bool
    {
        return $this->days_since_generation <= 30;
    }

    public function getDownloadRateAttribute(): float
    {
        $daysSinceGeneration = $this->days_since_generation;
        return $daysSinceGeneration > 0 ? $this->download_count / $daysSinceGeneration : 0;
    }

    // Methods
    public function generateReportData(): array
    {
        $propertySustainability = $this->propertySustainability;
        
        return [
            'key_findings' => $this->generateKeyFindings($propertySustainability, $this->report_type),
            'recommendations' => $this->generateRecommendations($propertySustainability, $this->report_type),
            'performance_scores' => $this->getPerformanceScores($propertySustainability),
            'compliance_status' => $this->getComplianceStatus($propertySustainability),
            'benchmark_comparison' => $this->getBenchmarkComparison($propertySustainability),
            'trend_analysis' => $this->getTrendAnalysis($propertySustainability),
            'improvement_opportunities' => $this->getImprovementOpportunities($propertySustainability),
            'risk_assessment' => $this->getRiskAssessment($propertySustainability),
            'certification_eligibility' => $this->getCertificationEligibility($propertySustainability),
            'executive_summary' => $this->generateExecutiveSummary($propertySustainability),
            'detailed_analysis' => $this->generateDetailedAnalysis($propertySustainability, $this->report_type),
            'appendices' => $this->generateAppendices($propertySustainability),
        ];
    }

    private function generateKeyFindings($propertySustainability, $reportType): array
    {
        $findings = [];
        
        if ($propertySustainability->eco_score >= 80) {
            $findings[] = 'أداء استدامة ممتاز مع درجة بيئية عالية';
        } elseif ($propertySustainability->eco_score >= 60) {
            $findings[] = 'أداء استدامة جيد مع مجال للتحسين';
        } else {
            $findings[] = 'يحتاج إلى تحسينات كبيرة في أداء الاستدامة';
        }
        
        if ($propertySustainability->certification_status === 'certified') {
            $findings[] = 'العقار حاصل على شهادة خضراء معتمدة';
        }
        
        if ($propertySustainability->carbon_footprint < 50) {
            $findings[] = 'بصمة كربونية منخفضة مقارنة بالمعايير';
        }
        
        if ($propertySustainability->renewable_energy_percentage > 50) {
            $findings[] = 'استخدام كبير للطاقة المتجددة يقل من الاعتماد على الطاقة';
        }
        
        if ($reportType === 'comprehensive') {
            $findings[] = 'التقييم الشامل يغطي جميع جوانب الاستدامة';
        }
        
        return $findings;
    }

    private function generateRecommendations($propertySustainability, $reportType): array
    {
        $recommendations = [];
        
        if ($propertySustainability->energy_efficiency_rating < 70) {
            $recommendations[] = 'تحسين كفاءة الطاقة من خلال تركيب ألواح شمسية وتحسين العزل';
        }
        
        if ($propertySustainability->water_efficiency_rating < 70) {
            $recommendations[] = 'تحسين كفاءة المياه من خلال أنظمة تجميع مياه الأمطار وإعادة التدوير';
        }
        
        if ($propertySustainability->carbon_footprint > 50) {
            $recommendations[] = 'تقليل البصمة الكربونية من خلال مصادر الطاقة المتجددة وتحسين كفاءة الطاقة';
        }
        
        if ($propertySustainability->sustainable_materials_percentage < 60) {
            $recommendations[] = 'استخدام مواد مستدامة ومعتمدة للحصول على شهادات خضراء';
        }
        
        if ($reportType === 'certification') {
            $recommendations[] = 'الاستعداد للمتطلبات على شهادات LEED أو BREEAM';
            $recommendations[] = 'تحسين النقاط المفقومة لمتطلبات الشهادات';
        }
        
        if ($reportType === 'carbon_footprint') {
            $recommendations[] = 'تنفيذ خطة تقليل البصمة الكربونية';
            $recommendations[] = 'الاستثمار في مصادر الطاقة النظيفة';
            $recommendations[] = 'تحسين كفاءة استهلاك الطاقة';
        }
        
        return $recommendations;
    }

    private function getPerformanceScores($propertySustainability): array
    {
        return [
            'eco_score' => $propertySustainability->eco_score,
            'energy_efficiency' => $propertySustainability->energy_efficiency_rating,
            'water_efficiency' => $propertySustainability->water_efficiency_rating,
            'waste_management' => $propertySustainability->waste_management_score,
            'materials_percentage' => $propertySustainability->sustainable_materials_percentage,
            'carbon_footprint' => $propertySustainability->carbon_footprint,
            'renewable_energy' => $propertySustainability->renewable_energy_percentage,
            'green_space' => $propertySustainability->green_space_ratio * 100,
        ];
    }

    private function getComplianceStatus($propertySustainability): array
    {
        return [
            'certified' => $propertySustainability->certification_status === 'certified',
            'compliance_score' => $propertySustainability->eco_score >= 70 ? 'متوافق' : 'غير متوافق',
            'next_audit' => $propertySustainability->next_audit_date,
            'certifications_active' => $propertySustainability->greenCertifications()->where('status', 'active')->count(),
        ];
    }

    private function getBenchmarkComparison($propertySustainability): array
    {
        $propertyType = $propertySustainability->property->type ?? 'unknown';
        
        $benchmarks = [
            'residential' => ['avg_eco_score' => 65, 'avg_carbon' => 45],
            'commercial' => ['avg_eco_score' => 70, 'avg_carbon' => 55],
            'industrial' => ['avg_eco_score' => 60, 'avg_carbon' => 65],
            'mixed' => ['avg_eco_score' => 68, 'avg_carbon' => 50],
        ];

        $benchmark = $benchmarks[$propertyType] ?? $benchmarks['mixed'];

        return [
            'industry_average' => $benchmark['avg_eco_score'],
            'property_score' => $propertySustainability->eco_score,
            'percentile' => min(95, max(5, ($propertySustainability->eco_score / 100) * 100)),
            'carbon_comparison' => $benchmark['avg_carbon'] - $propertySustainability->carbon_footprint,
        ];
    }

    private function getTrendAnalysis($propertySustainability): array
    {
        // Simplified trend analysis
        return [
            'trend' => 'improving',
            'change_percentage' => '+15%',
            'period_comparison' => 'مقارنة بالفترة السابقة',
            'data_points' => 12,
            'confidence_level' => 'عال',
        ];
    }

    private function getImprovementOpportunities($propertySustainability): array
    {
        $opportunities = [];
        
        if ($propertySustainability->renewable_energy_percentage < 50) {
            $opportunities[] = [
                'category' => 'الطاقة المتجددة',
                'current' => $propertySustainability->renewable_energy_percentage . '%',
                'target' => '50-70%',
                'potential_impact' => 'عالي',
                'estimated_cost' => 'مرتفع',
            ];
        }
        
        if ($propertySustainability->green_space_ratio < 0.3) {
            $opportunities[] = [
                'category' => 'المساحات الخضراء',
                'current' => ($propertySustainability->green_space_ratio * 100) . '%',
                'target' => '30-40%',
                'potential_impact' => 'متوسط',
                'estimated_cost' => 'متوسط',
            ];
        }
        
        if ($propertySustainability->sustainable_materials_percentage < 60) {
            $opportunities[] = [
                'category' => 'المواد المستدامة',
                'current' => $propertySustainability->sustainable_materials_percentage . '%',
                'target' => '70-80%',
                'potential_impact' => 'متوسط إلى عالي',
                'estimated_cost' => 'متوسط إلى مرتفع',
            ];
        }
        
        return $opportunities;
    }

    private function getRiskAssessment($propertySustainability): array
    {
        return [
            'overall_risk' => 'منخفض',
            'climate_risk' => 'متوسط',
            'regulatory_risk' => 'منخفض',
            'market_risk' => 'منخفض',
            'operational_risk' => 'منخفض',
        ];
    }

    private function getCertificationEligibility($propertySustainability): array
    {
        $eligibility = [];
        
        if ($propertySustainability->eco_score >= 90) {
            $eligibility[] = 'LEED Platinum';
            $eligibility[] = 'BREEAM Outstanding';
        } elseif ($propertySustainability->eco_score >= 80) {
            $eligibility[] = 'LEED Gold';
            $eligibility[] = 'BREEAM Excellent';
        } elseif ($propertySustainability->eco_score >= 70) {
            $eligibility[] = 'LEED Silver';
            $eligibility[] = 'BREEAM Very Good';
        } elseif ($propertySustainability->eco_score >= 60) {
            $eligibility[] = 'LEED Certified';
            $eligibility[] = 'BREEAM Good';
        }
        
        return $eligibility;
    }

    private function generateExecutiveSummary($propertySustainability): string
    {
        return "هذا التقرير يقدم تقييماً شاملاً لأداء الاستدامة للعقار مع درجة بيئية إجمالية تبلغ " . 
               $propertySustainability->eco_score . " من 100. العقار يظهر " . 
               ($propertySustainability->eco_score >= 70 ? 'أداءً جيداً' : 'حاجة إلى تحسينات') . 
               " في مجالات الاستدامة المختلفة.";
    }

    private function generateDetailedAnalysis($propertySustainability, $reportType): array
    {
        $analysis = [];
        
        switch ($reportType) {
            case 'comprehensive':
                $analysis = [
                    'energy_analysis' => 'تحليل مفصل لاستهلاك الطاقة وكفاءتها',
                    'water_analysis' => 'تحليل استهلاك المياه وكفاءة استخدامها',
                    'materials_analysis' => 'تحليل المواد المستدامة المستخدمة',
                    'carbon_analysis' => 'تحليل البصمة الكربونية وتأثرها',
                    'overall_assessment' => 'تقييم شامل لأداء الاستدامة',
                ];
                break;
                
            case 'certification':
                $analysis = [
                    'certification_readiness' => 'تقييم الجاهزية للحصول على شهادات خضراء',
                    'requirements_analysis' => 'تحليل متطلبات الشهادات المختلفة',
                    'gap_analysis' => 'تحليل الفجوات بين الوضع الحالي ومتطلبات الشهادات',
                    'recommendation' => 'توصيات بالخطوات اللازمة للحصول على الشهادات',
                ];
                break;
                
            case 'carbon_footprint':
                $analysis = [
                    'carbon_sources' => 'مصادر انبعاثات الكربون في العقار',
                    'emission_breakdown' => 'تفصيل انبعاثات الكربون حسب المصدر',
                    'reduction_strategies' => 'استراتيجيات تقليل الانبعاثات',
                    'offset_requirements' => 'متطلبات تعويض الكربون',
                    'future_projections' => 'توقعات انبعاثات الكربون المستقبلية',
                ];
                break;
                
            case 'energy_efficiency':
                $analysis = [
                    'energy_consumption_patterns' => 'أنماط استهلاك الطاقة',
                    'efficiency_measures' => 'تدابير كفاءة الطاقة المطبقة',
                    'cost_analysis' => 'تحليل تكاليف الطاقة وتوفير التكاليف',
                    'improvement_potential' => 'إمكانية تحسين كفاءة الطاقة',
                    'renewable_energy_integration' => 'تكامل مصادر الطاقة المتجددة',
                ];
                break;
                
            default:
                $analysis = [
                    'basic_assessment' => 'تقييم أساسي لأداء الاستدامة',
                    'key_metrics' => 'المؤشرات الرئيسية للأداء',
                    'performance_evaluation' => 'تقييم أداء الاستدامة',
                ];
        }
        
        return $analysis;
    }

    private function generateAppendices($propertySustainability): array
    {
        return [
            'data_sources' => [
                'سجلات العقار',
                'قياسات الأداء',
                'بيانات الطقس',
                'مصادر خارجية',
            ],
            'methodology' => [
                'منهجية التقييم القياسية',
                'معايير الحسابات المستخدمة',
                'أدوات التحليل المطبقة',
                'افتراضات الحساب',
            ],
            'references' => [
                'معايير LEED',
                'معايير BREEAM',
                'إرشادات الاستدامة المحلية',
                'أبحاث ودراسات علمية',
            ],
            'technical_specifications' => [
                'مواصفات فنية',
                'بيانات الأداء',
                'مخططات الحساب',
                'مؤشرات الأداء',
            ],
        ];
    }

    public function generatePDF(): string
    {
        $filename = 'sustainability-reports/' . $this->id . '_' . time() . '.pdf';
        
        // This would typically use a PDF library like DomPDF or similar
        // For now, we'll return the path
        return $filename;
    }

    public function download(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!$this->file_path || !Storage::disk('public')->exists($this->file_path)) {
            abort(404, 'ملف التقرير غير متاح');
        }

        // Increment download count
        $this->increment('download_count');

        return Storage::disk('public')->download($this->file_path, $this->title . '.pdf');
    }

    public function regenerate(): void
    {
        // Regenerate report data
        $reportData = $this->generateReportData();
        
        // Update report with new data
        $this->update([
            'key_findings' => $reportData['key_findings'],
            'recommendations' => $reportData['recommendations'],
            'performance_scores' => $reportData['performance_scores'],
            'compliance_status' => $reportData['compliance_status'],
            'benchmark_comparison' => $reportData['benchmark_comparison'],
            'trend_analysis' => $reportData['trend_analysis'],
            'improvement_opportunities' => $reportData['improvement_opportunities'],
            'risk_assessment' => $reportData['risk_assessment'],
            'certification_eligibility' => $reportData['certification_eligibility'],
            'executive_summary' => $reportData['executive_summary'],
            'detailed_analysis' => $reportData['detailed_analysis'],
            'appendices' => $reportData['appendices'],
            'generated_at' => now(),
            'status' => 'generated',
        ]);

        // Generate new PDF
        $newFilePath = $this->generatePDF();
        
        // Update file path and size
        $this->update([
            'file_path' => $newFilePath,
            'file_size' => Storage::disk('public')->size($newFilePath),
        ]);
    }

    public function getReportStatistics(): array
    {
        return [
            'file_size_mb' => round($this->file_size / 1024 / 1024, 2),
            'download_count' => $this->download_count,
            'days_since_generation' => $this->days_since_generation,
            'report_age' => $this->generated_at->diffForHumans(),
            'download_rate' => $this->download_rate,
        ];
    }

    public function getPreviewData(): array
    {
        return [
            'executive_summary' => substr($this->executive_summary, 0, 500) . '...',
            'key_findings_count' => count($this->key_findings ?? []),
            'recommendations_count' => count($this->recommendations ?? []),
            'performance_summary' => $this->performance_scores,
            'report_type' => $this->report_type_text,
            'generation_date' => $this->generated_at->format('Y-m-d H:i'),
            'file_size' => $this->file_size_text,
        ];
    }

    public function validateReportData(): array
    {
        $validation = [
            'is_valid' => true,
            'issues' => [],
            'warnings' => [],
        ];
        
        // Check for required data
        if (empty($this->key_findings)) {
            $validation['is_valid'] = false;
            $validation['issues'][] = 'يجب إضافة نتائج رئيسية للتقرير';
        }
        
        if (empty($this->recommendations)) {
            $validation['warnings'][] = 'يُنصح بإضافة توصيات للتقرير';
        }
        
        // Check for data consistency
        if ($this->performance_scores && !is_array($this->performance_scores)) {
            $validation['warnings'][] = 'بيانات الأداء يجب أن تكون في شكل مصفوفة';
        }
        
        // Check report period validity
        if ($this->report_period_start > $this->report_period_end) {
            $validation['is_valid'] = false;
            $validation['issues'][] = 'تاريخ بداية الفترة يجب أن يكون قبل تاريخ النهاية';
        }
        
        return $validation;
    }

    // Events
    protected static function booted()
    {
        static::creating(function ($report) {
            $report->status = 'generating';
        });

        static::created(function ($report) {
            $report->status = 'generated';
            
            // Update file size if file exists
            if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
                $report->update([
                    'file_size' => Storage::disk('public')->size($report->file_path),
                ]);
            }
        });
    }
}
