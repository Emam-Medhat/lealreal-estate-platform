<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentSimulation extends Model
{
    protected $fillable = [
        'inputs_json',
        'results_json',
        'share_card_url',
    ];

    protected $casts = [
        'inputs_json' => 'array',
        'results_json' => 'array',
    ];
}
