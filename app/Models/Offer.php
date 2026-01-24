<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'buyer_id',
        'seller_id',
        'agent_id',
        'offer_price',
        'offer_type',
        'contingencies',
        'expiry_date',
        'notes',
        'status',
        'type',
        'accepted_at',
        'rejected_at',
        'rejection_reason',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'offer_price' => 'decimal:2',
        'contingencies' => 'array',
        'expiry_date' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function counterOffers(): HasMany
    {
        return $this->hasMany(Offer::class, 'original_offer_id');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
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

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'accepted' => 'success',
            'rejected' => 'danger',
            'countered' => 'info',
            'expired' => 'secondary',
            default => 'primary'
        };
    }

    public function negotiation()
    {
        return $this->hasOne(Negotiation::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'under_negotiation']);
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

    public function canBeAccepted(): bool
    {
        return in_array($this->status, ['pending', 'under_negotiation']) && !$this->isExpired();
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, ['pending', 'under_negotiation']);
    }

    public function canBeWithdrawn(): bool
    {
        return in_array($this->status, ['pending', 'under_negotiation']) && !$this->isExpired();
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
        return '$' . number_format($this->offer_price, 2);
    }

    public function getFormattedEarnestMoney(): string
    {
        return $this->earnest_money_amount ? '$' . number_format($this->earnest_money_amount, 2) : 'None';
    }

    public function getFinancingTypeLabel(): string
    {
        $labels = [
            'cash' => 'Cash',
            'mortgage' => 'Mortgage',
            'owner_financing' => 'Owner Financing'
        ];

        return $labels[$this->financing_type] ?? 'Unknown';
    }

    public function getStatusLabel(): string
    {
        $labels = [
            'pending' => 'Pending',
            'under_negotiation' => 'Under Negotiation',
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
        if (!$this->expires_at) {
            return -1;
        }

        return now()->diffInDays($this->expires_at, false);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'under_negotiation']);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        });
    }
}
