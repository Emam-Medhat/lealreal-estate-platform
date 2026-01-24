<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyWebhook extends Model
{
    protected $fillable = [
        'name',
        'url',
        'event_type',
        'secret',
        'status',
        'last_triggered_at',
    ];

    protected $casts = [
        'last_triggered_at' => 'datetime',
    ];
}
