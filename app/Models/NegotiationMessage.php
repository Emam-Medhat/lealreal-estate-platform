<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NegotiationMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'negotiation_id',
        'user_id',
        'message',
        'type',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    public function negotiation(): BelongsTo
    {
        return $this->belongsTo(Negotiation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isProposal(): bool
    {
        return $this->type === 'proposal';
    }

    public function isQuestion(): bool
    {
        return $this->type === 'question';
    }

    public function isAnswer(): bool
    {
        return $this->type === 'answer';
    }

    public function isRegularMessage(): bool
    {
        return $this->type === 'text';
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function getTypeLabel(): string
    {
        $labels = [
            'text' => 'Message',
            'proposal' => 'Proposal',
            'question' => 'Question',
            'answer' => 'Answer'
        ];

        return $labels[$this->type] ?? 'Unknown';
    }

    public function getTimeAgo(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function scopeByNegotiation($query, $negotiationId)
    {
        return $query->where('negotiation_id', $negotiationId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'asc');
    }
}
