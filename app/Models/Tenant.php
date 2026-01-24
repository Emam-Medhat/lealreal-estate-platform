<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'national_id',
        'date_of_birth',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'employment_status',
        'employer_name',
        'monthly_income',
        'bank_name',
        'bank_account',
        'references',
        'notes',
        'status',
        'verified',
        'verified_at',
        'verified_by',
        'blacklisted',
        'blacklist_reason',
        'blacklist_notes',
        'blacklisted_at',
        'blacklisted_by',
        'screening_status',
        'screening_completed_at',
        'user_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'monthly_income' => 'decimal:2',
        'references' => 'array',
        'verified' => 'boolean',
        'verified_at' => 'datetime',
        'blacklisted' => 'boolean',
        'blacklisted_at' => 'datetime',
        'screening_completed_at' => 'datetime',
    ];

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    public function currentLease(): BelongsTo
    {
        return $this->belongsTo(Lease::class, 'current_lease_id');
    }

    public function rentPayments(): HasMany
    {
        return $this->hasMany(RentPayment::class);
    }

    public function securityDeposits(): HasMany
    {
        return $this->hasMany(SecurityDeposit::class);
    }

    public function screenings(): HasMany
    {
        return $this->hasMany(TenantScreening::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(RentalApplication::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TenantDocument::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(RentalViolation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function scopeBlacklisted($query)
    {
        return $query->where('blacklisted', true);
    }

    public function scopeWithActiveLease($query)
    {
        return $query->whereHas('currentLease', function($q) {
            $q->where('status', 'active');
        });
    }

    // Attributes
    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }

    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function getActiveLeaseAttribute(): ?Lease
    {
        return $this->leases()->where('status', 'active')->first();
    }

    public function getTotalLeasesAttribute(): int
    {
        return $this->leases()->count();
    }

    public function getTotalPaymentsAttribute(): float
    {
        return $this->rentPayments()->where('status', 'paid')->sum('amount');
    }

    public function getPendingPaymentsAttribute(): float
    {
        return $this->rentPayments()->where('status', 'pending')->sum('amount');
    }

    public function getOverduePaymentsAttribute(): float
    {
        return $this->rentPayments()->where('status', 'overdue')->sum('amount');
    }

    public function getSecurityDepositsTotalAttribute(): float
    {
        return $this->securityDeposits()->sum('amount');
    }

    public function getLatestScreeningAttribute(): ?TenantScreening
    {
        return $this->screenings()->latest()->first();
    }

    public function getIsGoodTenantAttribute(): bool
    {
        return !$this->blacklisted && 
               $this->verified && 
               $this->screening_status === 'passed' &&
               $this->overdue_payments === 0;
    }

    // Methods
    public function updateCurrentLease(): void
    {
        $activeLease = $this->leases()->where('status', 'active')->first();
        $this->update(['current_lease_id' => $activeLease?->id]);
    }

    public function canRent(): bool
    {
        return !$this->blacklisted && 
               $this->verified && 
               $this->screening_status === 'passed';
    }

    public function getPaymentHistory(): array
    {
        return $this->rentPayments()
            ->with(['lease.property'])
            ->orderBy('payment_date', 'desc')
            ->get()
            ->map(function($payment) {
                return [
                    'date' => $payment->payment_date,
                    'amount' => $payment->amount,
                    'status' => $payment->status,
                    'property' => $payment->lease->property->title,
                    'late_fee' => $payment->late_fee,
                ];
            })
            ->toArray();
    }

    public function getRentalHistory(): array
    {
        return $this->leases()
            ->with('property')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function($lease) {
                return [
                    'property' => $lease->property->title,
                    'start_date' => $lease->start_date,
                    'end_date' => $lease->end_date,
                    'rent_amount' => $lease->rent_amount,
                    'status' => $lease->status,
                    'duration' => $lease->start_date->diffInDays($lease->end_date),
                ];
            })
            ->toArray();
    }
}
