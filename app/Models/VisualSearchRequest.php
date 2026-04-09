<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisualSearchRequest extends Model
{
    protected $fillable = [
        'image_path',
        'result_json',
    ];

    protected $casts = [
        'result_json' => 'array',
    ];
}
