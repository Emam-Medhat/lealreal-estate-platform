<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxExemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_tax_id',
        'user_id',
        'exemption_type',
        'exemption_amount',
        'approved_amount',
        'reason',
        'status',
        'application_date',
        'approved_date',
        'rejected_date',
        'approved_by',
        'rejected_by',
        'notes',
        'rejection_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'exemption_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'application_date' => 'date',
        'approved_date' => 'date',
        'rejected_date' => 'date',
    ];

    public function propertyTax(): BelongsTo
    {
        return $this->belongsTo(PropertyTax::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

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

    public function scopeByType($query, $type)
    {
        return $query->where('exemption_type', $type);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getEffectiveExemptionAmountAttribute(): float
    {
        return $this->approved_amount ?? $this->exemption_amount;
    }

    public function canBeReviewed(): bool
    {
        return $this->status === 'pending';
    }

    public function approve(float $approvedAmount, int $approverId, ?string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_amount' => $approvedAmount,
            'approved_date' => now(),
            'approved_by' => $approverId,
            'notes' => $notes,
        ]);
    }

    public function reject(string $rejectionReason, int $rejecterId): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $rejectionReason,
            'rejected_date' => now(),
            'rejected_by' => $rejecterId,
        ]);
    }
}
