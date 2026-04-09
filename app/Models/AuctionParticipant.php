<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'user_id',
        'joined_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'joined_at' => 'datetime'
    ];

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bids()
    {
        return $this->hasMany(AuctionBid::class, 'user_id', 'user_id')
            ->where('auction_id', $this->auction_id);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasBid(): bool
    {
        return $this->bids()->exists();
    }

    public function getHighestBid()
    {
        return $this->bids()->orderBy('amount', 'desc')->first();
    }

    public function getTotalBids(): int
    {
        return $this->bids()->count();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAuction($query, $auctionId)
    {
        return $query->where('auction_id', $auctionId);
    }
}
