<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'tenant_id',
        'rental_number',
        'start_date',
        'end_date',
        'rent_amount',
        'security_deposit',
        'rent_frequency',
        'payment_due_day',
        'late_fee',
        'late_fee_type',
        'status',
        'terms_and_conditions',
        'special_terms',
        'utilities_included',
        'amenities_included',
        'maintenance_responsibility',
        'renewal_option',
        'renewal_terms',
        'termination_notice_days',
        'user_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rent_amount' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'utilities_included' => 'array',
        'amenities_included' => 'array',
        'renewal_option' => 'boolean',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function rentPayments(): HasMany
    {
        return $this->hasMany(RentPayment::class);
    }

    public function securityDeposits(): HasMany
    {
        return $this->hasMany(SecurityDeposit::class);
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(LeaseRenewal::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(RentalViolation::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(RentalDocument::class);
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(RentAdjustment::class);
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

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('end_date', '<=', now()->addDays($days))
                    ->where('end_date', '>=', now());
    }

    // Attributes
    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date < now();
    }

    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->diffInDays($this->end_date));
    }

    public function getMonthlyRentAttribute(): float
    {
        return match($this->rent_frequency) {
            'monthly' => $this->rent_amount,
            'quarterly' => $this->rent_amount / 3,
            'annually' => $this->rent_amount / 12,
            default => $this->rent_amount,
        };
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->rentPayments()->where('status', 'paid')->sum('amount');
    }

    public function getTotalPendingAttribute(): float
    {
        return $this->rentPayments()->where('status', 'pending')->sum('amount');
    }

    public function getTotalOverdueAttribute(): float
    {
        return $this->rentPayments()->where('status', 'overdue')->sum('amount');
    }
}
