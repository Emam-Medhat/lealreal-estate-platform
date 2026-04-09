<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NegotiationParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'negotiation_id',
        'user_id',
        'joined_at',
        'status',
        'last_read_at'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'last_read_at' => 'datetime'
    ];

    public function negotiation(): BelongsTo
    {
        return $this->belongsTo(Negotiation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function markAsRead(): void
    {
        $this->update(['last_read_at' => now()]);
    }

    public function hasUnreadMessages(): bool
    {
        if (!$this->last_read_at) {
            return true;
        }

        return $this->negotiation->messages()
            ->where('created_at', '>', $this->last_read_at)
            ->where('user_id', '!=', $this->user_id)
            ->exists();
    }

    public function getUnreadCount(): int
    {
        if (!$this->last_read_at) {
            return $this->negotiation->messages()
                ->where('user_id', '!=', $this->user_id)
                ->count();
        }

        return $this->negotiation->messages()
            ->where('created_at', '>', $this->last_read_at)
            ->where('user_id', '!=', $this->user_id)
            ->count();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByNegotiation($query, $negotiationId)
    {
        return $query->where('negotiation_id', $negotiationId);
    }
}
