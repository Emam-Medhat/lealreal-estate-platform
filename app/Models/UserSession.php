<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_id',
        'location',
        'is_active',
        'last_activity_at',
        'expires_at',
        'login_method',
        'two_factor_verified',
        'biometric_verified',
        'security_flags',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_activity_at' => 'datetime',
        'expires_at' => 'datetime',
        'two_factor_verified' => 'boolean',
        'biometric_verified' => 'boolean',
        'location' => 'json',
        'security_flags' => 'json',
        'metadata' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(UserDevice::class, 'device_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isSecure(): bool
    {
        return $this->two_factor_verified || $this->biometric_verified;
    }

    public function refresh(): void
    {
        $this->update([
            'last_activity_at' => now(),
            'expires_at' => now()->addMinutes(config('session.lifetime', 120)),
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSecure($query)
    {
        return $query->where(function ($q) {
            $q->where('two_factor_verified', true)
              ->orWhere('biometric_verified', true);
        });
    }
}
