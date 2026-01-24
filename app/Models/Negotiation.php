<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Negotiation extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'initiated_by',
        'status',
        'message',
        'proposed_terms',
        'final_terms',
        'expires_at',
        'last_activity_at',
        'completed_at',
        'paused_at',
        'resumed_at',
        'terminated_at',
        'termination_reason'
    ];

    protected $casts = [
        'proposed_terms' => 'array',
        'final_terms' => 'array',
        'expires_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'completed_at' => 'datetime',
        'paused_at' => 'datetime',
        'resumed_at' => 'datetime',
        'terminated_at' => 'datetime'
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(NegotiationParticipant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(NegotiationMessage::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(NegotiationProposal::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(NegotiationDocument::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isTerminated(): bool
    {
        return $this->status === 'terminated';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canSendMessage(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function canProposeTerms(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function pause()
    {
        $this->update([
            'status' => 'paused',
            'paused_at' => now()
        ]);
    }

    public function resume()
    {
        $this->update([
            'status' => 'active',
            'resumed_at' => now()
        ]);
    }

    public function complete(array $finalTerms)
    {
        $this->update([
            'status' => 'completed',
            'final_terms' => $finalTerms,
            'completed_at' => now()
        ]);
    }

    public function terminate(string $reason)
    {
        $this->update([
            'status' => 'terminated',
            'termination_reason' => $reason,
            'terminated_at' => now()
        ]);
    }

    public function addParticipant(User $user)
    {
        return $this->participants()->firstOrCreate(['user_id' => $user->id]);
    }

    public function removeParticipant(User $user)
    {
        return $this->participants()->where('user_id', $user->id)->delete();
    }

    public function isUserParticipant(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    public function getStatusLabel(): string
    {
        $labels = [
            'active' => 'Active',
            'paused' => 'Paused',
            'completed' => 'Completed',
            'terminated' => 'Terminated'
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    public function getTimeAgo(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getLastActivityTime(): string
    {
        return $this->last_activity_at ? $this->last_activity_at->diffForHumans() : 'Never';
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
        return $query->where('status', 'active');
    }

    public function scopePaused($query)
    {
        return $query->where('status', 'paused');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByInitiator($query, $userId)
    {
        return $query->where('initiated_by', $userId);
    }

    public function scopeByOffer($query, $offerId)
    {
        return $query->where('offer_id', $offerId);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        });
    }

    public function updateLastActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }
}
