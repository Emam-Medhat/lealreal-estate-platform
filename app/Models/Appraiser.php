<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appraiser extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'license_number',
        'specializations',
        'experience_years',
        'certifications',
        'bio',
        'hourly_rate',
        'is_active',
        'address',
        'city',
        'country',
        'education',
        'professional_memberships',
    ];

    protected $casts = [
        'specializations' => 'array',
        'certifications' => 'array',
        'professional_memberships' => 'array',
        'hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function appraisals(): HasMany
    {
        return $this->hasMany(Appraisal::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(AppraisalReport::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }

    public function getSpecializationLabels(): array
    {
        $labels = [
            'residential' => 'سكني',
            'commercial' => 'تجاري',
            'industrial' => 'صناعي',
            'land' => 'أراضي',
            'agricultural' => 'زراعي',
            'special_purpose' => 'غرض خاص',
        ];

        return array_map(function($spec) use ($labels) {
            return $labels[$spec] ?? $spec;
        }, $this->specializations ?? []);
    }

    public function getAverageRating(): float
    {
        return $this->appraisals()->avg('rating') ?? 0;
    }

    public function getTotalAppraisals(): int
    {
        return $this->appraisals()->count();
    }

    public function getCompletedAppraisals(): int
    {
        return $this->appraisals()->where('status', 'completed')->count();
    }

    public function getThisMonthAppraisals(): int
    {
        return $this->appraisals()
            ->whereMonth('scheduled_date', now()->month)
            ->count();
    }

    public function getTotalRevenue(): float
    {
        return $this->appraisals()->sum('estimated_cost') ?? 0;
    }

    public function getTotalValueAppraised(): float
    {
        return $this->reports()->sum('estimated_value') ?? 0;
    }

    public function getCompletionRate(): float
    {
        $total = $this->getTotalAppraisals();
        $completed = $this->getCompletedAppraisals();

        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    public function isAvailableOn($date): bool
    {
        $appraisalsCount = $this->appraisals()
            ->whereDate('scheduled_date', $date)
            ->where('status', '!=', 'cancelled')
            ->count();

        return $appraisalsCount < 6; // Max 6 appraisals per day
    }

    public function getAppraisalsOn($date): HasMany
    {
        return $this->appraisals()
            ->whereDate('scheduled_date', $date)
            ->where('status', '!=', 'cancelled');
    }

    public function getUpcomingAppraisals(): HasMany
    {
        return $this->appraisals()
            ->where('status', 'scheduled')
            ->where('scheduled_date', '>=', now())
            ->orderBy('scheduled_date')
            ->take(5);
    }

    public function getRecentReports(): HasMany
    {
        return $this->reports()
            ->with('appraisal.property')
            ->latest()
            ->take(5);
    }

    public function hasSpecialization($specialization): bool
    {
        return in_array($specialization, $this->specializations ?? []);
    }

    public function addSpecialization($specialization): void
    {
        $specializations = $this->specializations ?? [];
        
        if (!in_array($specialization, $specializations)) {
            $specializations[] = $specialization;
            $this->specializations = $specializations;
            $this->save();
        }
    }

    public function removeSpecialization($specialization): void
    {
        $specializations = $this->specializations ?? [];
        
        if (($key = array_search($specialization, $specializations)) !== false) {
            unset($specializations[$key]);
            $this->specializations = array_values($specializations);
            $this->save();
        }
    }

    public function getCertificationLabels(): array
    {
        $labels = [
            'certified_appraiser' => 'مقيم معتمد',
            'member_appraisal_institute' => 'عضو معهد التقييم',
            'real_estate_license' => 'رخصة عقارية',
            'valuation_expert' => 'خبير تقييم',
        ];

        return array_map(function($cert) use ($labels) {
            return $labels[$cert] ?? $cert;
        }, $this->certifications ?? []);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySpecialization($query, $specialization)
    {
        return $query->whereJsonContains('specializations', $specialization);
    }

    public function scopeByExperience($query, $minYears)
    {
        return $query->where('experience_years', '>=', $minYears);
    }

    public function canBeDeleted(): bool
    {
        return $this->appraisals()->where('status', '!=', 'completed')->count() === 0;
    }

    public function getAverageValuePerSqm(): float
    {
        $reports = $this->reports()->whereNotNull('value_per_sqm')->get();
        
        if ($reports->isEmpty()) {
            return 0;
        }

        return $reports->avg('value_per_sqm');
    }

    public function getMostCommonAppraisalType(): string
    {
        $mostCommon = $this->appraisals()
            ->selectRaw('appraisal_type, COUNT(*) as count')
            ->groupBy('appraisal_type')
            ->orderByDesc('count')
            ->first();

        return $mostCommon ? $mostCommon->appraisal_type : 'none';
    }
}
