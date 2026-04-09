<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EcoScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'overall_score',
        'energy_score',
        'water_score',
        'waste_score',
        'materials_score',
        'transport_score',
        'biodiversity_score',
        'score_breakdown',
        'improvement_suggestions',
        'eco_level',
        'certification_requirements',
        'assessment_date',
        'next_assessment_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'score_breakdown' => 'array',
        'improvement_suggestions' => 'array',
        'certification_requirements' => 'array',
        'assessment_date' => 'date',
        'next_assessment_date' => 'date',
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

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAssessed($query)
    {
        return $query->where('status', 'assessed');
    }

    public function scopeImproving($query)
    {
        return $query->where('status', 'improving');
    }

    public function scopeCertified($query)
    {
        return $query->where('status', 'certified');
    }

    public function getEcoLevelAttribute($value): string
    {
        return match($value) {
            'bronze' => 'برونزي',
            'silver' => 'فضي',
            'gold' => 'ذهبي',
            'platinum' => 'بلاتيني',
            'diamond' => 'ألماس',
            default => $value,
        };
    }

    public function getStatusAttribute($value): string
    {
        return match($value) {
            'pending' => 'معلق',
            'assessed' => 'تم التقييم',
            'improving' => 'يتم التحسين',
            'certified' => 'معتمد',
            default => $value,
        };
    }

    public function getScoreGrade(): string
    {
        if ($this->overall_score >= 90) return 'A+';
        if ($this->overall_score >= 85) return 'A';
        if ($this->overall_score >= 80) return 'B+';
        if ($this->overall_score >= 75) return 'B';
        if ($this->overall_score >= 70) return 'C+';
        if ($this->overall_score >= 65) return 'C';
        if ($this->overall_score >= 60) return 'D';
        return 'F';
    }

    public function getCertificationReady(): bool
    {
        return $this->overall_score >= 75;
    }

    public function getImprovementAreas(): array
    {
        $areas = [];
        
        if ($this->energy_score < 70) {
            $areas[] = 'كفاءة الطاقة';
        }
        
        if ($this->water_score < 70) {
            $areas[] = 'حفظ المياه';
        }
        
        if ($this->waste_score < 70) {
            $areas[] = 'إدارة النفايات';
        }
        
        if ($this->materials_score < 70) {
            $areas[] = 'المواد المستدامة';
        }
        
        if ($this->transport_score < 70) {
            $areas[] = 'النقل المستدام';
        }
        
        if ($this->biodiversity_score < 70) {
            $areas[] = 'التنوع البيولوجي';
        }
        
        return $areas;
    }

    public function getNextLevelScore(): float
    {
        $levels = [
            'bronze' => 60,
            'silver' => 70,
            'gold' => 80,
            'platinum' => 90,
            'diamond' => 95,
        ];
        
        $currentLevelIndex = array_search($this->eco_level, array_keys($levels));
        $nextLevelIndex = min($currentLevelIndex + 1, count($levels) - 1);
        
        return $levels[array_keys($levels)[$nextLevelIndex]];
    }
}
