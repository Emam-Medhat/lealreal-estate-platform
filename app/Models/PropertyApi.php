<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyApi extends Model
{
    protected $fillable = [
        'name',
        'base_url',
        'api_key',
        'secret_key',
        'status',
        'last_synced_at',
    ];
}
