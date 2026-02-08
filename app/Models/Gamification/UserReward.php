<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reward_id',
        'status',
        'redeemed_at',
        'expires_at',
        'redemption_code',
        'notes',
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRedeemed($query)
    {
        return $query->where('status', 'redeemed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function isRedeemed(): bool
    {
        return $this->status === 'redeemed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               ($this->expires_at && $this->expires_at < now());
    }

    public function redeem(): bool
    {
        if ($this->isRedeemed() || $this->isExpired()) {
            return false;
        }

        $this->status = 'redeemed';
        $this->redeemed_at = now();
        $this->redemption_code = $this->generateRedemptionCode();
        
        return $this->save();
    }

    private function generateRedemptionCode(): string
    {
        return 'REW-' . strtoupper(uniqid()) . '-' . rand(1000, 9999);
    }
}
