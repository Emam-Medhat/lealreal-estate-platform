<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaseRenewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id',
        'renewal_number',
        'old_end_date',
        'new_end_date',
        'old_rent_amount',
        'new_rent_amount',
        'renewal_type',
        'status',
        'renewal_terms',
        'notes',
        'requested_at',
        'requested_by',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'effective_date',
        'user_id',
    ];

    protected $casts = [
        'old_end_date' => 'date',
        'new_end_date' => 'date',
        'effective_date' => 'date',
        'old_rent_amount' => 'decimal:2',
        'new_rent_amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
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

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Attributes
    public function getRentIncreaseAttribute(): float
    {
        return $this->new_rent_amount - $this->old_rent_amount;
    }

    public function getRentIncreasePercentageAttribute(): float
    {
        if ($this->old_rent_amount == 0) return 0;
        return ($this->rent_increase / $this->old_rent_amount) * 100;
    }

    public function getExtensionDaysAttribute(): int
    {
        return $this->new_end_date->diffInDays($this->old_end_date);
    }

    public function getIsRentIncreaseAttribute(): bool
    {
        return $this->new_rent_amount > $this->old_rent_amount;
    }

    public function getIsExtensionAttribute(): bool
    {
        return $this->new_end_date > $this->old_end_date;
    }
}
