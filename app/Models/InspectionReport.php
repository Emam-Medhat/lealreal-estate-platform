<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_id',
        'inspector_id',
        'overall_condition',
        'summary',
        'recommendations',
        'next_inspection_date',
        'estimated_repair_cost',
        'urgent_repairs',
        'report_date',
    ];

    protected $casts = [
        'next_inspection_date' => 'datetime',
        'report_date' => 'datetime',
        'estimated_repair_cost' => 'decimal:2',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(Inspector::class);
    }

    public function defects(): HasMany
    {
        return $this->hasMany(Defect::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(InspectionReportPhoto::class);
    }

    public function getConditionLabel(): string
    {
        $labels = [
            'excellent' => 'ممتاز',
            'good' => 'جيد',
            'fair' => 'متوسط',
            'poor' => 'ضعيف',
        ];

        return $labels[$this->overall_condition] ?? $this->overall_condition;
    }

    public function getConditionColor(): string
    {
        $colors = [
            'excellent' => 'success',
            'good' => 'info',
            'fair' => 'warning',
            'poor' => 'danger',
        ];

        return $colors[$this->overall_condition] ?? 'secondary';
    }

    public function getDefectCount(): int
    {
        return $this->defects()->count();
    }

    public function getCriticalDefectCount(): int
    {
        return $this->defects()->where('severity', 'critical')->count();
    }

    public function getHighDefectCount(): int
    {
        return $this->defects()->where('severity', 'high')->count();
    }

    public function getTotalDefectCost(): float
    {
        return $this->defects()->sum('estimated_cost');
    }

    public function hasCriticalIssues(): bool
    {
        return $this->getCriticalDefectCount() > 0;
    }

    public function getScore(): int
    {
        $score = 0;
        
        switch ($this->overall_condition) {
            case 'excellent':
                $score = 90;
                break;
            case 'good':
                $score = 75;
                break;
            case 'fair':
                $score = 60;
                break;
            case 'poor':
                $score = 40;
                break;
        }

        // Deduct points for defects
        $defectCount = $this->getDefectCount();
        $score -= min($defectCount * 5, 30);

        return max($score, 0);
    }

    public function getGrade(): string
    {
        $score = $this->getScore();

        if ($score >= 90) return 'A+';
        if ($score >= 85) return 'A';
        if ($score >= 80) return 'B+';
        if ($score >= 75) return 'B';
        if ($score >= 70) return 'C+';
        if ($score >= 65) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    public function isOverdueForNextInspection(): bool
    {
        return $this->next_inspection_date && $this->next_inspection_date->isPast();
    }

    public function getDaysUntilNextInspection(): int
    {
        if (!$this->next_inspection_date) {
            return -1;
        }

        return $this->next_inspection_date->diffInDays(now(), false);
    }
}
