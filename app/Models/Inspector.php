<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inspector extends Model
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
    ];

    protected $casts = [
        'specializations' => 'array',
        'certifications' => 'array',
        'hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(InspectionReport::class);
    }

    public function propertyConditions(): HasMany
    {
        return $this->hasMany(PropertyCondition::class);
    }

    public function complianceChecks(): HasMany
    {
        return $this->hasMany(ComplianceCheck::class);
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
            'structural' => 'إنشائي',
            'electrical' => 'كهربائي',
            'plumbing' => 'سباكة',
            'hvac' => 'تكييف',
            'safety' => 'سلامة',
            'environmental' => 'بيئي',
        ];

        return array_map(function($spec) use ($labels) {
            return $labels[$spec] ?? $spec;
        }, $this->specializations ?? []);
    }

    public function getAverageRating(): float
    {
        return $this->inspections()->avg('rating') ?? 0;
    }

    public function getTotalInspections(): int
    {
        return $this->inspections()->count();
    }

    public function getCompletedInspections(): int
    {
        return $this->inspections()->where('status', 'completed')->count();
    }

    public function getThisMonthInspections(): int
    {
        return $this->inspections()
            ->whereMonth('scheduled_date', now()->month)
            ->count();
    }

    public function getTotalRevenue(): float
    {
        return $this->inspections()->sum('estimated_cost') ?? 0;
    }

    public function getCompletionRate(): float
    {
        $total = $this->getTotalInspections();
        $completed = $this->getCompletedInspections();

        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    public function isAvailableOn($date): bool
    {
        $inspectionsCount = $this->inspections()
            ->whereDate('scheduled_date', $date)
            ->where('status', '!=', 'cancelled')
            ->count();

        return $inspectionsCount < 8; // Max 8 inspections per day
    }

    public function getInspectionsOn($date): HasMany
    {
        return $this->inspections()
            ->whereDate('scheduled_date', $date)
            ->where('status', '!=', 'cancelled');
    }

    public function getUpcomingInspections(): HasMany
    {
        return $this->inspections()
            ->where('status', 'scheduled')
            ->where('scheduled_date', '>=', now())
            ->orderBy('scheduled_date')
            ->take(5);
    }

    public function getRecentReports(): HasMany
    {
        return $this->reports()
            ->with('inspection.property')
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
            'certified_inspector' => 'مفتش معتمد',
            'structural_engineer' => 'مهندس إنشائي',
            'electrical_engineer' => 'مهندس كهربائي',
            'safety_professional' => 'محترف سلامة',
            'environmental_specialist' => 'أخصائي بيئي',
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
        return $this->inspections()->where('status', '!=', 'completed')->count() === 0;
    }
}
