<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lease extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_number',
        'property_id',
        'tenant_id',
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
        'termination_reason',
        'termination_date',
        'termination_notes',
        'suspension_reason',
        'suspension_notes',
        'activated_at',
        'activated_by',
        'terminated_at',
        'terminated_by',
        'suspended_at',
        'suspended_by',
        'resumed_at',
        'resumed_by',
        'user_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'termination_date' => 'date',
        'rent_amount' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'utilities_included' => 'array',
        'amenities_included' => 'array',
        'renewal_option' => 'boolean',
        'activated_at' => 'datetime',
        'terminated_at' => 'datetime',
        'suspended_at' => 'datetime',
        'resumed_at' => 'datetime',
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
        return $this->hasMany(LeaseDocument::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(LeaseReminder::class);
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
                    ->where('end_date', '>=', now())
                    ->where('status', 'active');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeTerminated($query)
    {
        return $query->where('status', 'terminated');
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

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->days_remaining <= 30 && $this->days_remaining >= 0;
    }

    public function getDurationAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getDurationInMonthsAttribute(): float
    {
        return $this->start_date->diffInMonths($this->end_date);
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

    public function getTotalRentAttribute(): float
    {
        return $this->monthly_rent * $this->duration_in_months;
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

    public function getSecurityDepositReceivedAttribute(): float
    {
        return $this->securityDeposits()->where('status', 'received')->sum('amount');
    }

    public function getSecurityDepositRefundedAttribute(): float
    {
        return $this->securityDeposits()->where('status', 'refunded')->sum('amount');
    }

    public function getLatestRenewalAttribute(): ?LeaseRenewal
    {
        return $this->renewals()->latest()->first();
    }

    public function getActiveViolationsAttribute(): int
    {
        return $this->violations()->where('status', 'active')->count();
    }

    // Methods
    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'activated_at' => now(),
            'activated_by' => auth()->id(),
        ]);

        $this->property->update(['status' => 'occupied']);
    }

    public function terminate(string $reason, ?string $notes = null): void
    {
        $this->update([
            'status' => 'terminated',
            'termination_reason' => $reason,
            'termination_date' => now(),
            'termination_notes' => $notes,
            'terminated_at' => now(),
            'terminated_by' => auth()->id(),
        ]);

        $this->property->update(['status' => 'vacant']);
    }

    public function suspend(string $reason, ?string $notes = null): void
    {
        $this->update([
            'status' => 'suspended',
            'suspension_reason' => $reason,
            'suspension_notes' => $notes,
            'suspended_at' => now(),
            'suspended_by' => auth()->id(),
        ]);
    }

    public function resume(): void
    {
        $this->update([
            'status' => 'active',
            'resumed_at' => now(),
            'resumed_by' => auth()->id(),
        ]);

        $this->property->update(['status' => 'occupied']);
    }

    public function canBeRenewed(): bool
    {
        return $this->renewal_option && 
               $this->status === 'active' && 
               $this->days_remaining <= 60;
    }

    public function generateRentSchedule(): void
    {
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $dueDay = $this->payment_due_day;

        $currentDate = $startDate->copy()->day($dueDay);
        if ($currentDate < $startDate) {
            $currentDate->addMonth();
        }

        while ($currentDate <= $endDate) {
            $this->rentPayments()->create([
                'due_date' => $currentDate,
                'amount' => $this->monthly_rent,
                'status' => 'pending',
                'user_id' => $this->user_id,
            ]);

            $currentDate->addMonth();
        }
    }

    public function calculateLateFee(): float
    {
        if (!$this->late_fee) return 0;

        return match($this->late_fee_type) {
            'fixed' => $this->late_fee,
            'percentage' => $this->monthly_rent * ($this->late_fee / 100),
            default => 0,
        };
    }

    public function getPaymentStatus(): string
    {
        $overdueCount = $this->rentPayments()->where('status', 'overdue')->count();
        
        if ($overdueCount > 0) {
            return 'overdue';
        }

        $pendingCount = $this->rentPayments()->where('status', 'pending')->count();
        
        if ($pendingCount > 0) {
            return 'pending';
        }

        return 'paid';
    }

    public function getOccupancyRate(): float
    {
        $totalDays = $this->duration;
        $occupiedDays = $this->rentPayments()->where('status', 'paid')->count() * 30; // Approximate
        
        return $totalDays > 0 ? ($occupiedDays / $totalDays) * 100 : 0;
    }
}
