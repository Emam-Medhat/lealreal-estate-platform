<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestorProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'investor_id',
        'bio',
        'professional_background',
        'investment_philosophy',
        'risk_appetite',
        'investment_horizon',
        'preferred_sectors',
        'excluded_sectors',
        'min_investment',
        'max_investment',
        'geographic_focus',
        'investment_criteria',
        'achievements',
        'certifications',
        'education',
        'social_links',
        'contact_preferences',
        'profile_picture',
        'resume',
        'updated_by',
    ];

    protected $casts = [
        'preferred_sectors' => 'array',
        'excluded_sectors' => 'array',
        'geographic_focus' => 'array',
        'investment_criteria' => 'array',
        'achievements' => 'array',
        'certifications' => 'array',
        'education' => 'array',
        'social_links' => 'array',
        'contact_preferences' => 'array',
        'min_investment' => 'decimal:15,2',
        'max_investment' => 'decimal:15,2',
        'updated_by' => 'integer',
    ];

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    // Scopes
    public function scopeByRiskAppetite($query, $appetite)
    {
        return $query->where('risk_appetite', $appetite);
    }

    public function scopeByInvestmentHorizon($query, $horizon)
    {
        return $query->where('investment_horizon', $horizon);
    }

    // Helper methods
    public function getProfilePictureUrlAttribute(): string
    {
        return $this->profile_picture ? asset('storage/' . $this->profile_picture) : '';
    }

    public function getResumeUrlAttribute(): string
    {
        return $this->resume ? asset('storage/' . $this->resume) : '';
    }

    public function getPreferredSectorsCountAttribute(): int
    {
        return count($this->preferred_sectors ?? []);
    }

    public function getExcludedSectorsCountAttribute(): int
    {
        return count($this->excluded_sectors ?? []);
    }

    public function getGeographicFocusCountAttribute(): int
    {
        return count($this->geographic_focus ?? []);
    }

    public function getInvestmentCriteriaCountAttribute(): int
    {
        return count($this->investment_criteria ?? []);
    }

    public function getAchievementsCountAttribute(): int
    {
        return count($this->achievements ?? []);
    }

    public function getCertificationsCountAttribute(): int
    {
        return count($this->certifications ?? []);
    }

    public function getEducationCountAttribute(): int
    {
        return count($this->education ?? []);
    }

    public function getSocialLinksCountAttribute(): int
    {
        return count($this->social_links ?? []);
    }

    public function getInvestmentRangeAttribute(): string
    {
        $min = $this->min_investment ? number_format($this->min_investment, 2) : '0';
        $max = $this->max_investment ? number_format($this->max_investment, 2) : 'Unlimited';
        
        return "$min - $max";
    }

    public function getRiskProfileAttribute(): array
    {
        return [
            'appetite' => $this->risk_appetite,
            'horizon' => $this->investment_horizon,
            'level' => $this->getRiskLevel(),
            'description' => $this->getRiskDescription(),
        ];
    }

    public function getContactPreferencesAttribute(): array
    {
        return $this->contact_preferences ?? [
            'email' => true,
            'phone' => false,
            'newsletter' => true,
        ];
    }

    public function getEducationLevelAttribute(): string
    {
        $education = $this->education ?? [];
        if (empty($education)) return 'Not specified';
        
        $highestDegree = collect($education)->sortByDesc('year')->first();
        return $highestDegree['degree'] ?? 'Not specified';
    }

    public function getLatestCertificationAttribute(): ?array
    {
        $certifications = $this->certifications ?? [];
        if (empty($certifications)) return null;
        
        return collect($certifications)->sortByDesc('date')->first();
    }

    public function getSocialPlatformsAttribute(): array
    {
        $socialLinks = $this->social_links ?? [];
        return collect($socialLinks)->pluck('url', 'platform')->toArray();
    }

    private function getRiskLevel(): string
    {
        $levels = [
            'conservative' => 'Low',
            'moderate' => 'Medium',
            'aggressive' => 'High',
        ];

        return $levels[$this->risk_appetite] ?? 'Medium';
    }

    private function getRiskDescription(): string
    {
        $descriptions = [
            'conservative' => 'Prefers low-risk investments with stable returns',
            'moderate' => 'Balanced approach between risk and return',
            'aggressive' => 'Willing to take higher risks for potentially higher returns',
        ];

        return $descriptions[$this->risk_appetite] ?? 'Balanced investment approach';
    }

    public function getProfileCompletionAttribute(): float
    {
        $fields = [
            'bio' => !empty($this->bio),
            'professional_background' => !empty($this->professional_background),
            'investment_philosophy' => !empty($this->investment_philosophy),
            'risk_appetite' => !empty($this->risk_appetite),
            'investment_horizon' => !empty($this->investment_horizon),
            'preferred_sectors' => !empty($this->preferred_sectors),
            'geographic_focus' => !empty($this->geographic_focus),
            'investment_criteria' => !empty($this->investment_criteria),
            'profile_picture' => !empty($this->profile_picture),
            'social_links' => !empty($this->social_links),
        ];

        $completed = count(array_filter($fields));
        $total = count($fields);
        
        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }
}
