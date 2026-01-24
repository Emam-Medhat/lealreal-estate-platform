<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentCompliance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_id',
        'overall_status',
        'compliance_notes',
        'compliance_checks',
        'compliance_score',
        'checked_by',
        'checked_at',
        'next_review_date',
    ];

    protected $casts = [
        'compliance_checks' => 'array',
        'checked_at' => 'datetime',
        'next_review_date' => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function scopeCompliant($query)
    {
        return $query->where('overall_status', 'compliant');
    }

    public function scopeNonCompliant($query)
    {
        return $query->where('overall_status', 'non_compliant');
    }

    public function scopeNeedsReview($query)
    {
        return $query->where('overall_status', 'needs_review');
    }

    public function scopeDueForReview($query)
    {
        return $query->where('next_review_date', '<=', now());
    }

    public function scopeOverdue($query)
    {
        return $query->where('next_review_date', '<', now());
    }

    public function isCompliant(): bool
    {
        return $this->overall_status === 'compliant';
    }

    public function isNonCompliant(): bool
    {
        return $this->overall_status === 'non_compliant';
    }

    public function needsReview(): bool
    {
        return $this->overall_status === 'needs_review';
    }

    public function isDueForReview(): bool
    {
        return $this->next_review_date && $this->next_review_date->isPast();
    }

    public function isOverdue(): bool
    {
        return $this->next_review_date && $this->next_review_date->isPast();
    }

    public function getOverallStatusLabel(): string
    {
        return match($this->overall_status) {
            'compliant' => 'ممتثل',
            'non_compliant' => 'غير ممتثل',
            'needs_review' => 'يحتاج مراجعة',
            default => 'غير محدد',
        };
    }

    public function getFormattedCheckedAt(): string
    {
        return $this->checked_at ? $this->checked_at->format('Y-m-d H:i') : '';
    }

    public function getFormattedNextReviewDate(): string
    {
        return $this->next_review_date ? $this->next_review_date->format('Y-m-d') : '';
    }

    public function getTimeUntilReview(): string
    {
        if (!$this->next_review_date) {
            return 'غير محدد';
        }
        
        if ($this->next_review_date->isPast()) {
            return 'متأخر ' . $this->next_review_date->diffForHumans(now());
        }
        
        return 'متبقي ' . $this->next_review_date->diffForHumans(now());
    }

    public function getComplianceScore(): float
    {
        return $this->compliance_score ?? 0;
    }

    public function getComplianceGrade(): string
    {
        $score = $this->getComplianceScore();
        
        if ($score >= 95) return 'ممتاز';
        if ($score >= 85) return 'جيد جداً';
        if ($score >= 75) return 'جيد';
        if ($score >= 60) return 'مقبول';
        return 'ضعيف';
    }

    public function getComplianceColor(): string
    {
        $score = $this->getComplianceScore();
        
        if ($score >= 95) return '#10b981'; // green
        if ($score >= 85) return '#3b82f6'; // blue
        if ($score >= 75) return '#f59e0b'; // amber
        if ($score >= 60) return '#f97316'; // orange
        return '#ef4444'; // red
    }

    public function getChecksCount(): int
    {
        return count($this->compliance_checks ?? []);
    }

    public function getCompliantChecksCount(): int
    {
        return collect($this->compliance_checks ?? [])
            ->where('status', 'compliant')
            ->count();
    }

    public function getNonCompliantChecksCount(): int
    {
        return collect($this->compliance_checks ?? [])
            ->where('status', 'non_compliant')
            ->count();
    }

    public function getNotApplicableChecksCount(): int
    {
        return collect($this->compliance_checks ?? [])
            ->where('status', 'not_applicable')
            ->count();
    }

    public function getCriticalIssues(): array
    {
        return collect($this->compliance_checks ?? [])
            ->where('status', 'non_compliant')
            ->filter(function($check) {
                return ($check['mandatory'] ?? false);
            })
            ->values()
            ->toArray();
    }

    public function hasCriticalIssues(): bool
    {
        return !empty($this->getCriticalIssues());
    }

    public function getRecommendations(): array
    {
        $recommendations = [];
        $nonCompliantChecks = collect($this->compliance_checks ?? [])
            ->where('status', 'non_compliant');
        
        foreach ($nonCompliantChecks as $check) {
            $recommendations[] = [
                'requirement' => $check['requirement_id'] ?? 'غير محدد',
                'issue' => $check['notes'] ?? 'غير محدد',
                'priority' => ($check['mandatory'] ?? false) ? 'critical' : 'high',
                'suggestion' => $this->getSuggestionForCheck($check),
            ];
        }
        
        return $recommendations;
    }

    private function getSuggestionForCheck(array $check): string
    {
        $requirementId = $check['requirement_id'] ?? '';
        
        return match($requirementId) {
            'basic_info' => 'يرجى إكمال جميع المعلومات الأساسية للوثيقة',
            'proper_formatting' => 'يرجى مراجعة وتصحيح تنسيق الوثيقة',
            'contract_terms' => 'يرجى إكمال جميع بنود العقد المطلوبة',
            'signatures' => 'يرجى التأكد من وجود توقيعات جميع الأطراف',
            'legal_clauses' => 'يرجى إضافة البنود القانونية الإلزامية',
            'legal_references' => 'يرجى إضافة المراجع القانونية المناسبة',
            'jurisdiction' => 'يرجى تحديد الاختصاص القضائي بوضوح',
            'financial_data' => 'يرجى مراجعة وتصحيح البيانات المالية',
            'currency_specification' => 'يرجى تحديد العملة والأسعار بوضوح',
            default => 'يرجى مراجعة هذا البند وتصحيح أي مشاكل',
        };
    }

    public function calculateScore(): float
    {
        $checks = $this->compliance_checks ?? [];
        $totalChecks = count($checks);
        
        if ($totalChecks === 0) {
            return 0;
        }
        
        $compliantChecks = collect($checks)->where('status', 'compliant')->count();
        $notApplicableChecks = collect($checks)->where('status', 'not_applicable')->count();
        
        $applicableChecks = $totalChecks - $notApplicableChecks;
        
        if ($applicableChecks === 0) {
            return 100;
        }
        
        return ($compliantChecks / $applicableChecks) * 100;
    }

    public function updateScore()
    {
        $this->update(['compliance_score' => $this->calculateScore()]);
    }

    public function getComplianceSummary(): array
    {
        return [
            'overall_status' => $this->overall_status,
            'compliance_score' => $this->getComplianceScore(),
            'grade' => $this->getComplianceGrade(),
            'color' => $this->getComplianceColor(),
            'total_checks' => $this->getChecksCount(),
            'compliant_checks' => $this->getCompliantChecksCount(),
            'non_compliant_checks' => $this->getNonCompliantChecksCount(),
            'not_applicable_checks' => $this->getNotApplicableChecksCount(),
            'critical_issues_count' => count($this->getCriticalIssues()),
            'has_critical_issues' => $this->hasCriticalIssues(),
            'recommendations_count' => count($this->getRecommendations()),
        ];
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'overall_status_label' => $this->getOverallStatusLabel(),
            'compliance_grade' => $this->getComplianceGrade(),
            'compliance_color' => $this->getComplianceColor(),
            'time_until_review' => $this->getTimeUntilReview(),
            'checks_count' => $this->getChecksCount(),
            'compliant_checks_count' => $this->getCompliantChecksCount(),
            'non_compliant_checks_count' => $this->getNonCompliantChecksCount(),
            'not_applicable_checks_count' => $this->getNotApplicableChecksCount(),
            'critical_issues' => $this->getCriticalIssues(),
            'has_critical_issues' => $this->hasCriticalIssues(),
            'recommendations' => $this->getRecommendations(),
            'compliance_summary' => $this->getComplianceSummary(),
        ]);
    }
}
