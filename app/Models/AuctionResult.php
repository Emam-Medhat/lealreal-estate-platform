<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AuctionResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'winner_id',
        'final_price',
        'completed_at',
        'notes',
        'status',
        'winner_confirmed_at',
        'rejection_reason',
        'rejected_at'
    ];

    protected $casts = [
        'final_price' => 'decimal:2',
        'completed_at' => 'datetime',
        'winner_confirmed_at' => 'datetime',
        'rejected_at' => 'datetime'
    ];

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function confirmWinner()
    {
        $this->update([
            'status' => 'confirmed',
            'winner_confirmed_at' => now()
        ]);
    }

    public function rejectWinner(string $reason)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_at' => now()
        ]);
    }

    public function getFormattedFinalPrice(): string
    {
        return '$' . number_format($this->final_price, 2);
    }

    public function getTimeAgo(): string
    {
        return $this->completed_at->diffForHumans();
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByWinner($query, $userId)
    {
        return $query->where('winner_id', $userId);
    }

    public function scopeByAuction($query, $auctionId)
    {
        return $query->where('auction_id', $auctionId);
    }

    public function scopeCompletedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('completed_at', [$startDate, $endDate]);
    }
}
