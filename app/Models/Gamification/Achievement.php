<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Achievement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gamification_achievements';

    protected $fillable = [
        'key',
        'name',
        'description',
        'type',
        'points_reward',
        'badge_id',
        'icon',
        'requirements',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'requirements' => 'json',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function isUnlockedByUser(int $userId): bool
    {
        return $this->userAchievements()
            ->where('user_id', $userId)
            ->exists();
    }

    public function getUnlockCount(): int
    {
        return $this->userAchievements()->count();
    }

    public static function getTypes(): array
    {
        return [
            'property_listing' => 'Property Listing',
            'property_sale' => 'Property Sale',
            'user_engagement' => 'User Engagement',
            'social_activity' => 'Social Activity',
            'investment' => 'Investment',
            'milestone' => 'Milestone',
            'special' => 'Special'
        ];
    }
}
