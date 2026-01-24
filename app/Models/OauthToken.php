<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OauthToken extends Model
{
    protected $fillable = [
        'external_integration_id',
        'access_token',
        'refresh_token',
        'expires_in',
        'token_type',
        'scope',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function externalIntegration()
    {
        return $this->belongsTo(ExternalIntegration::class);
    }
}
