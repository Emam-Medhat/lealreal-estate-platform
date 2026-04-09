<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'lease_id',
        'adjustment_number',
        'old_rent',
        'new_rent',
        'adjustment_type',
        'adjustment_amount',
        'adjustment_percentage',
        'effective_date',
        'reason',
        'description',
        'status',
        'approved_at',
        'approved_by',
        'applied_at',
        'applied_by',
        'notes',
        'documents',
        'user_id',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'approved_at' => 'datetime',
        'applied_at' => 'datetime',
        'old_rent' => 'decimal:2',
        'new_rent' => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
        'adjustment_percentage' => 'decimal:2',
        'documents' => 'array',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function appliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeApplied($query)
    {
        return $query->where('status', 'applied');
    }

    public function scopeIncrease($query)
    {
        return $query->where('adjustment_type', 'increase');
    }

    public function scopeDecrease($query)
    {
        return $query->where('adjustment_type', 'decrease');
    }

    // Attributes
    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getIsAppliedAttribute(): bool
    {
        return $this->status === 'applied';
    }

    public function getIsIncreaseAttribute(): bool
    {
        return $this->adjustment_type === 'increase';
    }

    public function getIsDecreaseAttribute(): bool
    {
        return $this->adjustment_type === 'decrease';
    }

    public function getMonthlyDifferenceAttribute(): float
    {
        return $this->new_rent - $this->old_rent;
    }

    public function getAnnualDifferenceAttribute(): float
    {
        return $this->monthly_difference * 12;
    }

    public function getEffectiveSoonAttribute(): bool
    {
        return $this->effective_date <= now()->addDays(30) && $this->effective_date >= now();
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->effective_date < now() && $this->status !== 'applied';
    }

    // Methods
    public function approve(): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);
    }

    public function apply(): void
    {
        $this->update([
            'status' => 'applied',
            'applied_at' => now(),
            'applied_by' => auth()->id(),
        ]);

        // Update lease rent amount
        if ($this->lease) {
            $this->lease->update(['rent_amount' => $this->new_rent]);
        }

        // Update property default rent
        if ($this->property) {
            $this->property->update(['rent_amount' => $this->new_rent]);
        }
    }

    public function reject(string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'notes' => ($this->notes ?? '') . "\n\nRejected: " . $reason . " (" . now()->toDateString() . ")",
        ]);
    }

    public function calculateAdjustmentAmount(): float
    {
        return match($this->adjustment_type) {
            'increase' => $this->new_rent - $this->old_rent,
            'decrease' => $this->old_rent - $this->new_rent,
            default => 0,
        };
    }

    public function calculateAdjustmentPercentage(): float
    {
        if ($this->old_rent == 0) return 0;
        return ($this->adjustment_amount / $this->old_rent) * 100;
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeApplied(): bool
    {
        return $this->status === 'approved' && $this->effective_date <= now();
    }

    public function getStatusBadge(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge badge-warning">معلق</span>',
            'approved' => '<span class="badge badge-info">موافق</span>',
            'applied' => '<span class="badge badge-success">مطبق</span>',
            'rejected' => '<span class="badge badge-danger">مرفوض</span>',
            default => '<span class="badge badge-secondary">' . $this->status . '</span>',
        };
    }

    public function getTypeBadge(): string
    {
        return match($this->adjustment_type) {
            'increase' => '<span class="badge badge-danger">زيادة</span>',
            'decrease' => '<span class="badge badge-success">نقصان</span>',
            default => '<span class="badge badge-secondary">' . $this->adjustment_type . '</span>',
        };
    }

    public function generateAdjustmentNumber(): string
    {
        return 'RADJ-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function getImpactSummary(): string
    {
        $direction = $this->is_increase ? 'زيادة' : 'نقصان';
        $amount = abs($this->adjustment_amount);
        $percentage = abs($this->adjustment_percentage);
        
        return "{$direction} {$amount} ريال ({$percentage}%) شهرياً";
    }
}
