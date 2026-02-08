<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class UserChallenge extends Model
{
    use HasFactory;

    protected $table = 'gamification_user_challenges';

    protected $fillable = [
        'user_id',
        'challenge_id',
        'status',
        'progress',
        'joined_at',
        'completed_at'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }
}
