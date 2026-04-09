<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class InsuranceProvider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_code',
        'name',
        'name_ar',
        'description',
        'description_ar',
        'provider_type',
        'status',
        'license_number',
        'license_expiry',
        'registration_number',
        'tax_id',
        'phone',
        'phone_secondary',
        'email',
        'email_support',
        'website',
        'address',
        'address_ar',
        'city',
        'city_ar',
        'state',
        'state_ar',
        'postal_code',
        'country',
        'country_ar',
        'contact_person',
        'contact_person_title',
        'contact_person_phone',
        'contact_person_email',
        'services_offered',
        'coverage_types',
        'specializations',
        'regions_served',
        'min_premium',
        'max_coverage',
        'commission_rate',
        'payment_terms',
        'claims_processing_days',
        'customer_satisfaction',
        'financial_rating',
        'rating_agency',
        'rating_date',
        'accreditations',
        'certifications',
        'awards',
        'years_in_business',
        'policies_issued',
        'total_premiums',
        'total_claims_paid',
        'claims_ratio',
        'key_personnel',
        'branch_offices',
        'partners',
        'technology_platforms',
        'api_integrations',
        'documents',
        'photos',
        'logo',
        'verified',
        'verified_at',
        'featured',
        'recommended',
        'notes',
        'notes_ar',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'license_expiry' => 'date',
        'rating_date' => 'date',
        'min_premium' => 'decimal:2',
        'max_coverage' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'customer_satisfaction' => 'decimal:2',
        'financial_rating' => 'decimal:2',
        'total_premiums' => 'decimal:2',
        'total_claims_paid' => 'decimal:2',
        'claims_ratio' => 'decimal:2',
        'services_offered' => 'array',
        'coverage_types' => 'array',
        'specializations' => 'array',
        'regions_served' => 'array',
        'accreditations' => 'array',
        'certifications' => 'array',
        'awards' => 'array',
        'key_personnel' => 'array',
        'branch_offices' => 'array',
        'partners' => 'array',
        'technology_platforms' => 'array',
        'api_integrations' => 'array',
        'documents' => 'array',
        'photos' => 'array',
        'verified' => 'boolean',
        'verified_at' => 'datetime',
        'featured' => 'boolean',
        'recommended' => 'boolean',
    ];

    // Relationships
    public function policies(): HasMany
    {
        return $this->hasMany(InsurancePolicy::class, 'insurance_provider_id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(InsuranceClaim::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(InsuranceQuote::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->where('verified', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeRecommended($query)
    {
        return $query->where('recommended', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('provider_type', $type);
    }

    public function scopeByRegion($query, $region)
    {
        return $query->whereJsonContains('regions_served', $region);
    }

    public function scopeByCoverageType($query, $coverageType)
    {
        return $query->whereJsonContains('coverage_types', $coverageType);
    }

    // Attributes
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' && $this->license_expiry >= now();
    }

    public function getIsLicenseExpiringAttribute(): bool
    {
        return $this->license_expiry <= now()->addDays(90) && $this->license_expiry >= now();
    }

    public function getIsLicenseExpiredAttribute(): bool
    {
        return $this->license_expiry < now();
    }

    public function getDaysUntilLicenseExpiryAttribute(): int
    {
        return now()->diffInDays($this->license_expiry, false);
    }

    public function getAverageClaimProcessingTimeAttribute(): float
    {
        // Calculate average processing time from claims data
        return $this->claims_processing_days ?? 30;
    }

    public function getClaimsRatioAttribute(): float
    {
        if ($this->total_premiums > 0) {
            return ($this->total_claims_paid / $this->total_premiums) * 100;
        }
        return 0;
    }

    public function getPerformanceScoreAttribute(): float
    {
        $score = 0;
        
        // Customer satisfaction (40%)
        if ($this->customer_satisfaction) {
            $score += ($this->customer_satisfaction / 5) * 40;
        }
        
        // Financial rating (30%)
        if ($this->financial_rating) {
            $score += ($this->financial_rating / 5) * 30;
        }
        
        // Claims ratio (20%)
        $claimsRatio = $this->claims_ratio;
        if ($claimsRatio <= 50) {
            $score += 20;
        } elseif ($claimsRatio <= 70) {
            $score += 15;
        } elseif ($claimsRatio <= 90) {
            $score += 10;
        } else {
            $score += 5;
        }
        
        // Years in business (10%)
        if ($this->years_in_business >= 10) {
            $score += 10;
        } elseif ($this->years_in_business >= 5) {
            $score += 7;
        } elseif ($this->years_in_business >= 2) {
            $score += 4;
        } else {
            $score += 1;
        }
        
        return $score;
    }

    public function getRiskLevelAttribute(): string
    {
        $score = $this->performance_score;
        
        if ($score >= 80) {
            return 'low';
        } elseif ($score >= 60) {
            return 'medium';
        } elseif ($score >= 40) {
            return 'high';
        } else {
            return 'very_high';
        }
    }

    public function getActivePoliciesCountAttribute(): int
    {
        return $this->policies()->where('status', 'active')->count();
    }

    public function getPendingClaimsCountAttribute(): int
    {
        return $this->claims()->whereIn('status', ['pending', 'submitted', 'processing'])->count();
    }

    public function getApprovedClaimsCountAttribute(): int
    {
        return $this->claims()->where('status', 'approved')->count();
    }

    public function getRejectedClaimsCountAttribute(): int
    {
        return $this->claims()->where('status', 'rejected')->count();
    }

    // Methods
    public function canIssuePolicy(string $coverageType): bool
    {
        return in_array($coverageType, $this->coverage_types ?? []);
    }

    public function canServeRegion(string $region): bool
    {
        return in_array($region, $this->regions_served ?? []);
    }

    public function calculatePremium(float $basePremium, array $riskFactors): float
    {
        $premium = $basePremium;
        
        // Apply risk factors
        foreach ($riskFactors as $factor => $value) {
            switch ($factor) {
                case 'property_age':
                    if ($value > 20) $premium *= 1.2;
                    elseif ($value > 10) $premium *= 1.1;
                    break;
                case 'property_value':
                    if ($value > 1000000) $premium *= 1.15;
                    break;
                case 'location_risk':
                    if ($value === 'high') $premium *= 1.25;
                    elseif ($value === 'medium') $premium *= 1.1;
                    break;
            }
        }
        
        // Apply commission
        if ($this->commission_rate > 0) {
            $premium *= (1 + $this->commission_rate / 100);
        }
        
        return round($premium, 2);
    }

    public function estimateClaimProcessingTime(): int
    {
        $baseDays = $this->claims_processing_days ?? 30;
        
        // Adjust based on workload
        $pendingClaims = $this->pending_claims_count;
        if ($pendingClaims > 100) {
            $baseDays *= 1.5;
        } elseif ($pendingClaims > 50) {
            $baseDays *= 1.2;
        }
        
        return (int) $baseDays;
    }

    public function getCoverageOptions(): array
    {
        return $this->coverage_types ?? [];
    }

    public function getServiceAreas(): array
    {
        return $this->regions_served ?? [];
    }

    public function getSpecializations(): array
    {
        return $this->specializations ?? [];
    }

    public function updatePerformanceMetrics(): void
    {
        $this->update([
            'policies_issued' => $this->policies()->count(),
            'total_premiums' => $this->policies()->sum('premium_amount'),
            'total_claims_paid' => $this->claims()->where('status', 'approved')->sum('claimed_amount'),
            'claims_ratio' => $this->claims_ratio,
        ]);
    }

    public function generateQuote(array $policyData): array
    {
        $basePremium = $this->calculatePremium($policyData['base_premium'], $policyData['risk_factors'] ?? []);
        
        return [
            'provider_id' => $this->id,
            'provider_name' => $this->name,
            'premium_amount' => $basePremium,
            'coverage_amount' => $policyData['coverage_amount'],
            'deductible' => $policyData['deductible'] ?? 0,
            'payment_terms' => $this->payment_terms,
            'claims_processing_days' => $this->estimateClaimProcessingTime(),
            'customer_satisfaction' => $this->customer_satisfaction,
            'financial_rating' => $this->financial_rating,
            'special_notes' => $this->notes,
        ];
    }

    public function verify(): void
    {
        $this->update([
            'verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function feature(): void
    {
        $this->update(['featured' => true]);
    }

    public function recommend(): void
    {
        $this->update(['recommended' => true]);
    }
}
