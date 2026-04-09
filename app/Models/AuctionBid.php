<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionBid extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'user_id',
        'amount',
        'is_auto_bid',
        'max_auto_bid',
        'is_winning_bid',
        'created_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'max_auto_bid' => 'decimal:2',
        'is_auto_bid' => 'boolean',
        'is_winning_bid' => 'boolean',
        'created_at' => 'datetime'
    ];

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isHighestBid(): bool
    {
        $highestBid = $this->auction->bids()->orderBy('amount', 'desc')->first();
        return $highestBid && $highestBid->id === $this->id;
    }

    public function isOutbid(): bool
    {
        return !$this->isHighestBid() && $this->auction->status === 'active';
    }

    public function getFormattedAmount(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    public function getFormattedMaxAutoBid(): string
    {
        return $this->max_auto_bid ? '$' . number_format($this->max_auto_bid, 2) : 'Not Set';
    }

    public function canBeRetracted(): bool
    {
        // Check if bid is not the highest bid
        if ($this->isHighestBid()) {
            return false;
        }

        // Check if auction is still active
        if ($this->auction->status !== 'active') {
            return false;
        }

        // Check if auction is not ending soon (within 5 minutes)
        if ($this->auction->end_time->diffInMinutes(now()) <= 5) {
            return false;
        }

        // Check if bid was placed recently (within 1 hour)
        if ($this->created_at->diffInHours(now()) > 1) {
            return false;
        }

        return true;
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAuction($query, $auctionId)
    {
        return $query->where('auction_id', $auctionId);
    }

    public function scopeHighest($query)
    {
        return $query->orderBy('amount', 'desc');
    }

    public function scopeAutoBids($query)
    {
        return $query->where('is_auto_bid', true);
    }

    public function scopeManualBids($query)
    {
        return $query->where('is_auto_bid', false);
    }

    public function scopeWinning($query)
    {
        return $query->where('is_winning_bid', true);
    }

    public function getTimeAgo(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getBidIncrement(): float
    {
        return $this->auction->bid_increment;
    }

    public function getNextValidBidAmount(): float
    {
        return $this->amount + $this->getBidIncrement();
    }

    public function isAboveReservePrice(): bool
    {
        $reservePrice = $this->auction->reserve_price;
        return $reservePrice ? $this->amount >= $reservePrice : true;
    }

    public function getBidPosition(): int
    {
        return $this->auction->bids()
            ->where('amount', '>', $this->amount)
            ->count() + 1;
    }

    public function updateWinningStatus(): void
    {
        $this->update([
            'is_winning_bid' => $this->isHighestBid()
        ]);
    }
}
