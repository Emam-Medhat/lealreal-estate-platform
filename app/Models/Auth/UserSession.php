<?php

namespace App\Models\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'session_id',
        'ip_address',
        'user_agent',
        'payload',
        'last_activity',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'payload' => 'array',
        'last_activity' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(UserDevice::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function extend($minutes = null): void
    {
        $this->update([
            'expires_at' => now()->addMinutes($minutes ?? config('session.lifetime')),
            'last_activity' => now(),
        ]);
    }

    public function getLocationAttribute(): ?string
    {
        // This would integrate with a geolocation service
        // For now, return null or implement basic IP geolocation
        return null;
    }

    public function getDeviceNameAttribute(): string
    {
        return $this->device?->device_name ?? $this->parseDeviceFromUserAgent();
    }

    private function parseDeviceFromUserAgent(): string
    {
        $userAgent = $this->user_agent ?? '';
        
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod/', $userAgent)) {
            if (preg_match('/iPad/', $userAgent)) {
                return 'Tablet';
            }
            return 'Mobile';
        }
        
        return 'Desktop';
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function scopeByDevice($query, $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('last_activity', '>', now()->subHours($hours));
    }
}
