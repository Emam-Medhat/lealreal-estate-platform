<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MortgageApplication extends Model
{
    protected $fillable = [
        'user_id',
        'encrypted_blob_ref',
        'status',
        'offers_json',
    ];

    protected $casts = [
        'offers_json' => 'array',
    ];
}
