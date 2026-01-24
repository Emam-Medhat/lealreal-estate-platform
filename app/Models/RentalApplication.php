<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'tenant_id',
        'application_number',
        'applicant_name',
        'applicant_email',
        'applicant_phone',
        'applicant_address',
        'applicant_income',
        'applicant_employment',
        'applicant_references',
        'move_in_date',
        'lease_duration',
        'offered_rent',
        'special_requests',
        'status',
        'priority',
        'screening_result',
        'screening_date',
        'review_date',
        'reviewed_by',
        'approval_date',
        'approved_by',
        'rejection_date',
        'rejected_by',
        'rejection_reason',
        'notes',
        'documents',
        'lease_id',
        'user_id',
    ];

    protected $casts = [
        'move_in_date' => 'date',
        'screening_date' => 'datetime',
        'review_date' => 'datetime',
        'approval_date' => 'datetime',
        'rejection_date' => 'datetime',
        'offered_rent' => 'decimal:2',
        'applicant_income' => 'decimal:2',
        'applicant_references' => 'array',
        'documents' => 'array',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
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

    public function scopeReviewing($query)
    {
        return $query->where('status', 'reviewing');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    // Attributes
    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsReviewingAttribute(): bool
    {
        return $this->status === 'reviewing';
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === 'rejected';
    }

    public function getDaysSinceApplicationAttribute(): int
    {
        return now()->diffInDays($this->created_at);
    }

    public function getRentDifferenceAttribute(): float
    {
        return $this->offered_rent - $this->property->rent_amount;
    }

    public function getRentDifferencePercentageAttribute(): float
    {
        if ($this->property->rent_amount == 0) return 0;
        return ($this->rent_difference / $this->property->rent_amount) * 100;
    }

    public function getScreeningPassedAttribute(): bool
    {
        return $this->screening_result === 'passed';
    }

    public function getHasLeaseAttribute(): bool
    {
        return !is_null($this->lease_id);
    }

    // Methods
    public function startReview(): void
    {
        $this->update([
            'status' => 'reviewing',
            'review_date' => now(),
            'reviewed_by' => auth()->id(),
        ]);
    }

    public function approve(?string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'approval_date' => now(),
            'approved_by' => auth()->id(),
            'notes' => $notes,
        ]);
    }

    public function reject(string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_date' => now(),
            'rejected_by' => auth()->id(),
            'rejection_reason' => $reason,
        ]);
    }

    public function createLease(): Lease
    {
        $lease = Lease::create([
            'lease_number' => 'LS-' . date('Y') . '-' . str_pad(Lease::count() + 1, 6, '0', STR_PAD_LEFT),
            'property_id' => $this->property_id,
            'tenant_id' => $this->tenant_id,
            'start_date' => $this->move_in_date,
            'end_date' => $this->move_in_date->copy()->addMonths($this->lease_duration),
            'rent_amount' => $this->offered_rent,
            'security_deposit' => $this->offered_rent,
            'rent_frequency' => 'monthly',
            'payment_due_day' => 1,
            'status' => 'active',
            'terms_and_conditions' => 'Standard lease terms based on application #' . $this->application_number,
            'maintenance_responsibility' => 'tenant',
            'termination_notice_days' => 30,
            'user_id' => auth()->id(),
        ]);

        $this->update(['lease_id' => $lease->id]);

        // Update property status
        $this->property->update(['status' => 'occupied']);

        return $lease;
    }

    public function canBeReviewed(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'reviewing' && $this->screening_result === 'passed';
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, ['pending', 'reviewing']);
    }

    public function canCreateLease(): bool
    {
        return $this->status === 'approved' && !$this->lease_id;
    }

    public function getStatusBadge(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge badge-warning">معلق</span>',
            'reviewing' => '<span class="badge badge-info">قيد المراجعة</span>',
            'approved' => '<span class="badge badge-success">مقبولة</span>',
            'rejected' => '<span class="badge badge-danger">مرفوضة</span>',
            default => '<span class="badge badge-secondary">' . $this->status . '</span>',
        };
    }

    public function getPriorityBadge(): string
    {
        return match($this->priority) {
            'high' => '<span class="badge badge-danger">عالية</span>',
            'medium' => '<span class="badge badge-warning">متوسطة</span>',
            'low' => '<span class="badge badge-secondary">منخفضة</span>',
            default => '<span class="badge badge-secondary">' . $this->priority . '</span>',
        };
    }
}
