<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounterOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'user_id',
        'amount',
        'message',
        'changes',
        'valid_until',
        'status',
        'accepted_at',
        'rejected_at',
        'withdrawn_at',
        'rejection_reason'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'changes' => 'array',
        'valid_until' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'withdrawn_at' => 'datetime'
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isWithdrawn(): bool
    {
        return $this->status === 'withdrawn';
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    public function canBeAccepted(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeWithdrawn(): bool
    {
        return $this->status === 'pending';
    }

    public function accept()
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now()
        ]);
    }

    public function reject(string $reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason
        ]);
    }

    public function withdraw()
    {
        $this->update([
            'status' => 'withdrawn',
            'withdrawn_at' => now()
        ]);
    }

    public function getFormattedAmount(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    public function getStatusLabel(): string
    {
        $labels = [
            'pending' => 'Pending',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'withdrawn' => 'Withdrawn',
            'expired' => 'Expired'
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    public function getTimeAgo(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getDaysUntilExpiration(): int
    {
        if (!$this->valid_until) {
            return -1;
        }

        return now()->diffInDays($this->valid_until, false);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByOffer($query, $offerId)
    {
        return $query->where('offer_id', $offerId);
    }

    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<', now());
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('valid_until')
                  ->orWhere('valid_until', '>', now());
        });
    }
}
