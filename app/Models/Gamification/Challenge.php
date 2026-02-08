<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Challenge extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gamification_challenges';

    protected $fillable = [
        'name',
        'description',
        'type',
        'difficulty',
        'requirements',
        'reward_points',
        'reward_badge_id',
        'start_date',
        'end_date',
        'max_participants',
        'is_active',
        'created_by',
        'metadata'
    ];

    protected $casts = [
        'requirements' => 'json',
        'metadata' => 'json',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'reward_points' => 'integer'
    ];

    public function rewardBadge()
    {
        return $this->belongsTo(Badge::class, 'reward_badge_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->hasMany(UserChallenge::class);
    }
}
