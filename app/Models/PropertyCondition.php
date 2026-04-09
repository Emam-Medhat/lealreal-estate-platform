<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'inspector_id',
        'overall_condition',
        'structural_condition',
        'roof_condition',
        'foundation_condition',
        'electrical_condition',
        'plumbing_condition',
        'hvac_condition',
        'interior_condition',
        'exterior_condition',
        'age_years',
        'last_renovation_year',
        'maintenance_level',
        'energy_efficiency',
        'accessibility_features',
        'safety_features',
        'notes',
        'inspection_date',
    ];

    protected $casts = [
        'accessibility_features' => 'array',
        'safety_features' => 'array',
        'inspection_date' => 'datetime',
        'last_renovation_year' => 'integer',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(Inspector::class);
    }

    public function getConditionLabel($condition): string
    {
        $labels = [
            'excellent' => 'ممتاز',
            'good' => 'جيد',
            'fair' => 'متوسط',
            'poor' => 'ضعيف',
        ];

        return $labels[$condition] ?? $condition;
    }

    public function getConditionColor($condition): string
    {
        $colors = [
            'excellent' => 'success',
            'good' => 'info',
            'fair' => 'warning',
            'poor' => 'danger',
        ];

        return $colors[$condition] ?? 'secondary';
    }

    public function getOverallScore(): int
    {
        $score = 0;
        $fields = [
            'overall_condition',
            'structural_condition',
            'roof_condition',
            'foundation_condition',
            'electrical_condition',
            'plumbing_condition',
            'hvac_condition',
            'interior_condition',
            'exterior_condition',
        ];

        foreach ($fields as $field) {
            switch ($this->$field) {
                case 'excellent':
                    $score += 4;
                    break;
                case 'good':
                    $score += 3;
                    break;
                case 'fair':
                    $score += 2;
                    break;
                case 'poor':
                    $score += 1;
                    break;
            }
        }

        // Add energy efficiency score
        switch ($this->energy_efficiency) {
            case 'excellent':
                $score += 4;
                break;
            case 'good':
                $score += 3;
                break;
            case 'fair':
                $score += 2;
                break;
            case 'poor':
                $score += 1;
                break;
        }

        return $score;
    }

    public function getMaxScore(): int
    {
        return 40; // 10 fields * 4 points each
    }

    public function getScorePercentage(): float
    {
        return ($this->getOverallScore() / $this->getMaxScore()) * 100;
    }

    public function getGrade(): string
    {
        $percentage = $this->getScorePercentage();

        if ($percentage >= 90) return 'A+';
        if ($percentage >= 85) return 'A';
        if ($percentage >= 80) return 'B+';
        if ($percentage >= 75) return 'B';
        if ($percentage >= 70) return 'C+';
        if ($percentage >= 65) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    public function getMaintenanceLabel(): string
    {
        $labels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
        ];

        return $labels[$this->maintenance_level] ?? $this->maintenance_level;
    }

    public function getEnergyEfficiencyLabel(): string
    {
        $labels = [
            'excellent' => 'ممتاز',
            'good' => 'جيد',
            'fair' => 'متوسط',
            'poor' => 'ضعيف',
        ];

        return $labels[$this->energy_efficiency] ?? $this->energy_efficiency;
    }

    public function hasCriticalIssues(): bool
    {
        $criticalFields = [
            'structural_condition',
            'foundation_condition',
            'electrical_condition',
        ];

        foreach ($criticalFields as $field) {
            if ($this->$field === 'poor') {
                return true;
            }
        }

        return false;
    }

    public function getAccessibilityLabels(): array
    {
        $labels = [
            'wheelchair_accessible' => 'سهولة الوصول للكراسي المتحركة',
            'ramp_access' => 'منحدرات',
            'elevator_access' => 'مصاعد',
            'accessible_bathroom' => 'حمامات متاحة',
            'handrails' => 'درابزين',
            'wide_doorways' => 'أبواب واسعة',
        ];

        return array_map(function($feature) use ($labels) {
            return $labels[$feature] ?? $feature;
        }, $this->accessibility_features ?? []);
    }

    public function getSafetyLabels(): array
    {
        $labels = [
            'smoke_detectors' => 'كاشفات الدخان',
            'fire_extinguishers' => 'طفايات الحريق',
            'security_system' => 'نظام أمني',
            'emergency_lighting' => 'إنارة طوارئ',
            'first_aid_kit' => 'صندوق إسعافات أولية',
            'carbon_monoxide_detector' => 'كاشف أول أكسيد الكربون',
        ];

        return array_map(function($feature) use ($labels) {
            return $labels[$feature] ?? $feature;
        }, $this->safety_features ?? []);
    }

    public function getAgeCategory(): string
    {
        if ($this->age_years <= 5) return 'جديد';
        if ($this->age_years <= 15) return 'حديث';
        if ($this->age_years <= 30) return 'متوسط العمر';
        if ($this->age_years <= 50) return 'قديم';
        return 'قديم جداً';
    }

    public function needsRenovation(): bool
    {
        $currentYear = now()->year;
        $yearsSinceRenovation = $currentYear - ($this->last_renovation_year ?? 0);
        
        return $yearsSinceRenovation > 15;
    }

    public function scopeByCondition($query, $condition)
    {
        return $query->where('overall_condition', $condition);
    }

    public function scopeByAge($query, $maxAge)
    {
        return $query->where('age_years', '<=', $maxAge);
    }

    public function scopeByMaintenanceLevel($query, $level)
    {
        return $query->where('maintenance_level', $level);
    }

    public function scopeByEnergyEfficiency($query, $efficiency)
    {
        return $query->where('energy_efficiency', $efficiency);
    }
}
