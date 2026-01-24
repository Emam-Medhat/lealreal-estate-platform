<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SustainabilityReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'report_title',
        'report_type',
        'report_period_start',
        'report_period_end',
        'report_data',
        'key_metrics',
        'recommendations',
        'overall_sustainability_score',
        'compliance_status',
        'benchmark_comparison',
        'status',
        'report_file_path',
        'generated_date',
        'next_review_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'report_data' => 'array',
        'key_metrics' => 'array',
        'recommendations' => 'array',
        'compliance_status' => 'array',
        'benchmark_comparison' => 'array',
        'report_period_start' => 'date',
        'report_period_end' => 'date',
        'generated_date' => 'date',
        'next_review_date' => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(SmartProperty::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeGenerating($query)
    {
        return $query->where('status', 'generating');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function getReportTypeAttribute($value): string
    {
        return match($value) {
            'comprehensive' => 'شامل',
            'carbon' => 'البصمة الكربونية',
            'energy' => 'كفاءة الطاقة',
            'water' => 'حفظ المياه',
            'materials' => 'المواد المستدامة',
            'climate' => 'التأثير المناخي',
            'custom' => 'مخصص',
            default => $value,
        };
    }

    public function getStatusAttribute($value): string
    {
        return match($value) {
            'generating' => 'قيد الإنشاء',
            'completed' => 'مكتمل',
            'approved' => 'معتمد',
            'rejected' => 'مرفوض',
            default => $value,
        };
    }

    public function getPeriodInDays(): int
    {
        return $this->report_period_start->diffInDays($this->report_period_end);
    }

    public function isCompliant(): bool
    {
        return isset($this->compliance_status['overall_compliance']) &&
               $this->compliance_status['overall_compliance'] === true;
    }

    public function getPerformanceRating(): string
    {
        if ($this->overall_sustainability_score >= 90) return 'ممتاز';
        if ($this->overall_sustainability_score >= 80) return 'جيد جداً';
        if ($this->overall_sustainability_score >= 70) return 'جيد';
        if ($this->overall_sustainability_score >= 60) return 'متوسط';
        return 'يحتاج تحسين';
    }

    public function hasRecommendations(): bool
    {
        return !empty($this->recommendations);
    }

    public function getRecommendationsCount(): int
    {
        return count($this->recommendations ?? []);
    }
}
