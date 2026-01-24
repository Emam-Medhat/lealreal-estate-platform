<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EnvironmentalAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'audit_title',
        'audit_type',
        'audit_date',
        'auditor_id',
        'audit_criteria',
        'findings',
        'non_compliance_issues',
        'recommendations',
        'compliance_score',
        'audit_status',
        'follow_up_date',
        'corrective_actions',
        'audit_report_path',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'audit_criteria' => 'array',
        'findings' => 'array',
        'non_compliance_issues' => 'array',
        'recommendations' => 'array',
        'corrective_actions' => 'array',
        'audit_date' => 'date',
        'follow_up_date' => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(SmartProperty::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeScheduled($query)
    {
        return $query->where('audit_status', 'scheduled');
    }

    public function scopeInProgress($query)
    {
        return $query->where('audit_status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('audit_status', 'completed');
    }

    public function scopeReviewed($query)
    {
        return $query->where('audit_status', 'reviewed');
    }

    public function scopeApproved($query)
    {
        return $query->where('audit_status', 'approved');
    }

    public function getAuditTypeAttribute($value): string
    {
        return match($value) {
            'comprehensive' => 'شامل',
            'energy' => 'كفاءة الطاقة',
            'water' => 'حفظ المياه',
            'waste' => 'إدارة النفايات',
            'materials' => 'المواد المستدامة',
            'compliance' => 'الامتثال',
            default => $value,
        };
    }

    public function getAuditStatusAttribute($value): string
    {
        return match($value) {
            'scheduled' => 'مجدول',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'reviewed' => 'تم المراجعة',
            'approved' => 'معتمد',
            default => $value,
        };
    }

    public function getComplianceGrade(): string
    {
        if ($this->compliance_score >= 90) return 'A+';
        if ($this->compliance_score >= 85) return 'A';
        if ($this->compliance_score >= 80) return 'B+';
        if ($this->compliance_score >= 75) return 'B';
        if ($this->compliance_score >= 70) return 'C+';
        if ($this->compliance_score >= 65) return 'C';
        if ($this->compliance_score >= 60) return 'D';
        return 'F';
    }

    public function hasNonComplianceIssues(): bool
    {
        return !empty($this->non_compliance_issues);
    }

    public function getNonComplianceCount(): int
    {
        return count($this->non_compliance_issues ?? []);
    }

    public function hasCorrectiveActions(): bool
    {
        return !empty($this->corrective_actions);
    }

    public function getCorrectiveActionsCount(): int
    {
        return count($this->corrective_actions ?? []);
    }

    public function isOverdue(): bool
    {
        return $this->follow_up_date && $this->follow_up_date->isPast();
    }

    public function getDaysUntilFollowUp(): int
    {
        if (!$this->follow_up_date) {
            return 0;
        }

        return max(0, $this->follow_up_date->diffInDays(now()));
    }
}
