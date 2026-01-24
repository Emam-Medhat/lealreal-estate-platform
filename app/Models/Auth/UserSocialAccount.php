<?php

namespace App\Models\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSocialAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'provider_token',
        'provider_refresh_token',
        'provider_expires_at',
        'provider_data',
    ];

    protected $casts = [
        'provider_expires_at' => 'datetime',
        'provider_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->provider_expires_at && $this->provider_expires_at->isPast();
    }

    public function needsRefresh(): bool
    {
        return $this->provider_refresh_token && $this->isExpired();
    }

    public function getProviderNameAttribute(): string
    {
        return ucfirst($this->provider);
    }

    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('provider_expires_at')
              ->orWhere('provider_expires_at', '>', now());
        });
    }
}
