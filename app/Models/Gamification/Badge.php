<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Badge extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gamification_badges';

    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'category',
        'rarity',
        'requirements',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'requirements' => 'json',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByRarity($query, string $rarity)
    {
        return $query->where('rarity', $rarity);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function isOwnedByUser(int $userId): bool
    {
        return $this->userBadges()
            ->where('user_id', $userId)
            ->exists();
    }

    public function getOwnershipCount(): int
    {
        return $this->userBadges()->count();
    }

    public static function getCategories(): array
    {
        return [
            'achievement' => 'Achievement',
            'milestone' => 'Milestone',
            'special' => 'Special',
            'seasonal' => 'Seasonal',
            'exclusive' => 'Exclusive'
        ];
    }

    public static function getRarities(): array
    {
        return [
            'common' => 'Common',
            'uncommon' => 'Uncommon',
            'rare' => 'Rare',
            'epic' => 'Epic',
            'legendary' => 'Legendary'
        ];
    }
}
