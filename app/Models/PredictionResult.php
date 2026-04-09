<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredictionResult extends Model
{
    protected $fillable = [
        'property_id',
        'horizon',
        'forecast_value',
        'confidence',
        'factors_json',
    ];

    protected $casts = [
        'factors_json' => 'array',
        'confidence' => 'float',
        'forecast_value' => 'float',
    ];
}
