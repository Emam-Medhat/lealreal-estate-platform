<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class InsurancePolicy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'policy_number',
        'title',
        'title_ar',
        'description',
        'description_ar',
        'insurance_provider_id',
        'property_id',
        'policy_type',
        'status',
        'start_date',
        'end_date',
        'premium_amount',
        'coverage_amount',
        'deductible',
        'payment_frequency',
        'payment_method',
        'activated_at',
        'suspended_at',
        'cancelled_at',
        'cancellation_reason',
        'cancellation_date',
        'refund_amount',
        'auto_renewal',
        'renewal_terms',
        'special_conditions',
        'special_conditions_ar',
        'exclusions',
        'exclusions_ar',
        'notes',
        'notes_ar',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'premium_amount' => 'decimal:2',
        'coverage_amount' => 'decimal:2',
        'deductible' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'activated_at' => 'datetime',
        'suspended_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'cancellation_date' => 'date',
        'auto_renewal' => 'boolean',
        'special_conditions' => 'array',
        'exclusions' => 'array',
    ];

    // Relationships
    public function provider(): BelongsTo
    {
        return $this->belongsTo(InsuranceProvider::class, 'insurance_provider_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function coverages(): HasMany
    {
        return $this->hasMany(InsuranceCoverage::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(InsuranceClaim::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InsurancePayment::class);
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(InsuranceRenewal::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(InsuranceDocument::class);
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

    public function scopeExpiring($query, $days = 30)
    {
        return $query->where('end_date', '<=', now()->addDays($days))
                    ->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now())
                    ->where('status', 'active');
    }

    public function scopeByProvider($query, $providerId)
    {
        return $query->where('insurance_provider_id', $providerId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('policy_type', $type);
    }

    // Attributes
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' && $this->end_date >= now();
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date < now();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->end_date <= now()->addDays(30) && $this->end_date >= now();
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        return now()->diffInDays($this->end_date, false);
    }

    public function getRemainingCoverageAttribute(): float
    {
        $totalClaims = $this->claims()->where('status', 'approved')->sum('claimed_amount');
        return max(0, $this->coverage_amount - $totalClaims);
    }

    public function getAnnualPremiumAttribute(): float
    {
        return match($this->payment_frequency) {
            'monthly' => $this->premium_amount * 12,
            'quarterly' => $this->premium_amount * 4,
            'semi_annually' => $this->premium_amount * 2,
            'annually' => $this->premium_amount,
            default => $this->premium_amount,
        };
    }

    public function getMonthlyPremiumAttribute(): float
    {
        return match($this->payment_frequency) {
            'monthly' => $this->premium_amount,
            'quarterly' => $this->premium_amount / 3,
            'semi_annually' => $this->premium_amount / 6,
            'annually' => $this->premium_amount / 12,
            default => $this->premium_amount,
        };
    }

    public function getClaimCountAttribute(): int
    {
        return $this->claims()->count();
    }

    public function getApprovedClaimAmountAttribute(): float
    {
        return $this->claims()->where('status', 'approved')->sum('claimed_amount');
    }

    public function getPendingClaimAmountAttribute(): float
    {
        return $this->claims()->where('status', 'pending')->sum('claimed_amount');
    }

    public function getTotalPaidPremiumAttribute(): float
    {
        return $this->payments()->where('status', 'paid')->sum('amount');
    }

    public function getOutstandingPremiumAttribute(): float
    {
        return $this->payments()->where('status', 'pending')->sum('amount');
    }

    // Methods
    public function canClaim(float $amount): bool
    {
        return $this->remaining_coverage >= $amount && $this->is_active;
    }

    public function calculateClaimAmount(float $damageAmount): float
    {
        $amountAfterDeductible = max(0, $damageAmount - $this->deductible);
        return min($amountAfterDeductible, $this->remaining_coverage);
    }

    public function renew(array $renewalData): InsuranceRenewal
    {
        return $this->renewals()->create([
            'renewal_number' => $this->generateRenewalNumber(),
            'renewal_date' => $renewalData['renewal_date'] ?? now(),
            'old_premium_amount' => $this->premium_amount,
            'new_premium_amount' => $renewalData['new_premium_amount'] ?? $this->premium_amount,
            'old_coverage_amount' => $this->coverage_amount,
            'new_coverage_amount' => $renewalData['new_coverage_amount'] ?? $this->coverage_amount,
            'renewal_terms' => $renewalData['renewal_terms'] ?? null,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);
    }

    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'activated_at' => now(),
        ]);
    }

    public function suspend(): void
    {
        $this->update([
            'status' => 'suspended',
            'suspended_at' => now(),
        ]);
    }

    public function cancel(string $reason, ?Carbon $cancellationDate = null, float $refundAmount = 0): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'cancellation_date' => $cancellationDate ?? now(),
            'refund_amount' => $refundAmount,
        ]);
    }

    public function getRiskLevel(): string
    {
        $claimCount = $this->claim_count;
        $claimAmount = $this->approved_claim_amount;
        $coverageRatio = $claimAmount / $this->coverage_amount;

        if ($claimCount === 0) {
            return 'low';
        } elseif ($claimCount <= 2 && $coverageRatio < 0.5) {
            return 'medium';
        } elseif ($claimCount <= 5 && $coverageRatio < 0.8) {
            return 'high';
        } else {
            return 'very_high';
        }
    }

    public function getPremiumTrend(): array
    {
        $renewals = $this->renewals()->orderBy('created_at', 'desc')->take(5)->get();
        
        $trend = [
            'current' => $this->premium_amount,
            'previous' => $renewals->first()?->old_premium_amount ?? $this->premium_amount,
            'change' => 0,
            'change_percentage' => 0,
            'history' => [],
        ];

        if ($trend['previous'] > 0) {
            $trend['change'] = $trend['current'] - $trend['previous'];
            $trend['change_percentage'] = ($trend['change'] / $trend['previous']) * 100;
        }

        foreach ($renewals as $renewal) {
            $trend['history'][] = [
                'date' => $renewal->renewal_date,
                'amount' => $renewal->new_premium_amount,
            ];
        }

        return $trend;
    }

    private function generateRenewalNumber(): string
    {
        $prefix = 'REN';
        $year = date('Y');
        $sequence = static::whereYear('created_at', $year)->count() + 1;
        
        return $prefix . $year . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }
}
