<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'to',
        'message',
        'type',
        'status',
        'provider_message_id',
        'provider_response',
        'error_message',
        'sent_at',
        'delivered_at',
        'cost'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cost' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function markAsSent(string $messageId = null, array $response = [])
    {
        $this->update([
            'status' => 'sent',
            'provider_message_id' => $messageId,
            'provider_response' => json_encode($response)
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);
    }

    public function markAsFailed(string $errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage
        ]);
    }

    public function getFormattedCost(): string
    {
        return '$' . number_format($this->cost ?? 0, 2);
    }

    public function getDuration(): ?string
    {
        if (!$this->delivered_at || !$this->sent_at) {
            return null;
        }

        $duration = $this->sent_at->diffInSeconds($this->delivered_at);
        return $duration . ' seconds';
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSentBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('sent_at', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('sent_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('sent_at', now()->month)
                   ->whereYear('sent_at', now()->year);
    }
}
