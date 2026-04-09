<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class UserLevel extends Model
{
    use HasFactory;

    protected $table = 'gamification_user_levels';

    protected $fillable = [
        'user_id',
        'level',
        'total_points',
        'current_points',
        'leveled_up_at'
    ];

    protected $casts = [
        'leveled_up_at' => 'datetime',
        'level' => 'integer',
        'total_points' => 'integer',
        'current_points' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
