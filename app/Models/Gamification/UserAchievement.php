<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAchievement extends Model
{
    use HasFactory;

    protected $table = 'gamification_user_achievements';

    protected $fillable = [
        'user_id',
        'achievement_key',
        'points_awarded',
        'badge_id',
        'awarded_at'
    ];

    protected $casts = [
        'awarded_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function achievement()
    {
        return $this->belongsTo(Achievement::class, 'achievement_key', 'key');
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('awarded_at', '>=', now()->subDays($days));
    }

    public function getFormattedDate(): string
    {
        return $this->awarded_at->format('M j, Y');
    }
}
