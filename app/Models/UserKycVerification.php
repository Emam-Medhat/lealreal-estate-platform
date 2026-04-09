<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserKycVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'verification_type',
        'status',
        'document_type',
        'document_number',
        'document_front_image',
        'document_back_image',
        'selfie_image',
        'address_proof',
        'full_name',
        'date_of_birth',
        'nationality',
        'residence_country',
        'address',
        'city',
        'state',
        'postal_code',
        'phone',
        'occupation',
        'income_source',
        'annual_income',
        'purpose_of_account',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'rejected_at',
        'reviewer_id',
        'rejection_reason',
        'notes',
        'verification_data',
        'metadata',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'verification_data' => 'json',
        'metadata' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'in_review' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            'expired' => 'gray',
            default => 'gray'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => __('Pending'),
            'in_review' => __('In Review'),
            'approved' => __('Approved'),
            'rejected' => __('Rejected'),
            'expired' => __('Expired'),
            default => __('Unknown')
        };
    }

    public function getVerificationTypeLabelAttribute(): string
    {
        return match($this->verification_type) {
            'individual' => __('Individual'),
            'business' => __('Business'),
            'corporate' => __('Corporate'),
            default => __('Unknown')
        };
    }

    public function getDocumentTypeLabelAttribute(): string
    {
        return match($this->document_type) {
            'passport' => __('Passport'),
            'national_id' => __('National ID'),
            'driver_license' => __('Driver License'),
            'residence_permit' => __('Residence Permit'),
            default => __('Unknown')
        };
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInReview(): bool
    {
        return $this->status === 'in_review';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function canBeSubmitted(): bool
    {
        return in_array($this->status, ['pending', 'rejected']);
    }

    public function canBeReviewed(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'in_review';
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, ['pending', 'in_review']);
    }

    public function submit(): void
    {
        $this->update([
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
    }

    public function startReview(int $reviewerId): void
    {
        $this->update([
            'status' => 'in_review',
            'reviewed_at' => now(),
            'reviewer_id' => $reviewerId,
        ]);
    }

    public function approve(?string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'notes' => $notes,
        ]);
    }

    public function reject(string $reason, ?string $notes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'notes' => $notes,
        ]);
    }

    public function expire(): void
    {
        $this->update([
            'status' => 'expired',
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInReview($query)
    {
        return $query->where('status', 'in_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('verification_type', $type);
    }

    public function scopeByDocumentType($query, string $documentType)
    {
        return $query->where('document_type', $documentType);
    }

    public function scopeByReviewer($query, int $reviewerId)
    {
        return $query->where('reviewer_id', $reviewerId);
    }

    public function scopeSubmittedAfter($query, $date)
    {
        return $query->where('submitted_at', '>=', $date);
    }

    public function scopeApprovedAfter($query, $date)
    {
        return $query->where('approved_at', '>=', $date);
    }
}
