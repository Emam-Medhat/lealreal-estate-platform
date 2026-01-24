<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'title',
        'description',
        'starting_price',
        'current_price',
        'reserve_price',
        'bid_increment',
        'start_time',
        'end_time',
        'type',
        'status',
        'auto_extend',
        'bid_count',
        'winner_id',
        'final_price',
        'created_by',
        'started_at',
        'ended_at',
        'last_bid_at'
    ];

    protected $casts = [
        'starting_price' => 'decimal:2',
        'current_price' => 'decimal:2',
        'reserve_price' => 'decimal:2',
        'bid_increment' => 'decimal:2',
        'final_price' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'auto_extend' => 'boolean',
        'bid_count' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'last_bid_at' => 'datetime'
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(AuctionParticipant::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(AuctionResult::class);
    }

    public function highestBid(): HasOne
    {
        return $this->hasOne(AuctionBid::class)->orderBy('amount', 'desc');
    }

    public function images(): HasMany
    {
        return $this->hasMany(AuctionImage::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->start_time <= now() && $this->end_time > now();
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming' || $this->start_time > now();
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function hasEnded(): bool
    {
        return $this->end_time <= now();
    }

    public function getTimeRemaining(): string
    {
        if ($this->hasEnded()) {
            return 'Ended';
        }

        return $this->end_time->diffForHumans(now(), true);
    }

    public function getNextBidAmount(): float
    {
        return $this->current_price + $this->bid_increment;
    }

    public function addParticipant(User $user): AuctionParticipant
    {
        return $this->participants()->firstOrCreate(['user_id' => $user->id], [
            'joined_at' => now(),
            'status' => 'active'
        ]);
    }

    public function removeParticipant(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->delete();
    }

    public function isUserParticipant(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    public function canUserParticipate(User $user): bool
    {
        // Check if user is not the creator
        if ($this->created_by === $user->id) {
            return false;
        }

        // Check if auction is active or upcoming
        if (!in_array($this->status, ['active', 'upcoming'])) {
            return false;
        }

        // Check if auction hasn't ended
        if ($this->hasEnded()) {
            return false;
        }

        return true;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                   ->where('start_time', '<=', now())
                   ->where('end_time', '>', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')
                   ->orWhere('start_time', '>', now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByCreator($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function getFormattedStartingPrice(): string
    {
        return '$' . number_format($this->starting_price, 2);
    }

    public function getFormattedCurrentPrice(): string
    {
        return '$' . number_format($this->current_price, 2);
    }

    public function getFormattedReservePrice(): string
    {
        return $this->reserve_price ? '$' . number_format($this->reserve_price, 2) : 'No Reserve';
    }

    public function getFormattedBidIncrement(): string
    {
        return '$' . number_format($this->bid_increment, 2);
    }

    public function getProgressPercentage(): int
    {
        if ($this->starting_price == 0) {
            return 0;
        }

        $progress = (($this->current_price - $this->starting_price) / $this->starting_price) * 100;
        return min(100, max(0, round($progress)));
    }
}
