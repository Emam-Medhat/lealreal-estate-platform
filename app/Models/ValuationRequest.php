<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValuationRequest extends Model
{
    protected $fillable = [
        'address',
        'city',
        'inputs',
        'result_summary',
        'status',
    ];

    protected $casts = [
        'inputs' => 'array',
        'result_summary' => 'array',
    ];
}
