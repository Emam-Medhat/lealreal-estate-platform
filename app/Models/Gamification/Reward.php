<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'points_required',
        'reward_type',
        'reward_value',
        'is_active',
        'expires_at',
        'category',
        'icon',
        'terms',
    ];

    protected $casts = [
        'points_required' => 'integer',
        'reward_value' => 'decimal:2',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'terms' => 'array',
    ];

    public function userRewards(): HasMany
    {
        return $this->hasMany(UserReward::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function isAvailable(): bool
    {
        return $this->is_active && (!$this->expires_at || $this->expires_at > now());
    }

    public function canBeRedeemed(int $userPoints): bool
    {
        return $this->isAvailable() && $userPoints >= $this->points_required;
    }
}
