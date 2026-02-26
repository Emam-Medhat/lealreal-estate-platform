<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NeighborhoodProfile extends Model
{
    protected $fillable = [
        'user_id',
        'weights_json',
        'result_json',
    ];

    protected $casts = [
        'weights_json' => 'array',
        'result_json' => 'array',
    ];
}
