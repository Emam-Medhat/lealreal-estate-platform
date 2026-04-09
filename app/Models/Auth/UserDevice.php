<?php

namespace App\Models\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_name',
        'device_type',
        'platform',
        'browser',
        'ip_address',
        'user_agent',
        'is_trusted',
        'last_used_at',
        'biometric_data',
    ];

    protected $casts = [
        'is_trusted' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    protected $hidden = [
        'biometric_data',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    public function isMobile(): bool
    {
        return $this->device_type === 'mobile';
    }

    public function isDesktop(): bool
    {
        return $this->device_type === 'desktop';
    }

    public function isTablet(): bool
    {
        return $this->device_type === 'tablet';
    }

    public function isOnline(): bool
    {
        return $this->sessions()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->exists();
    }

    public function getPlatformIconAttribute(): string
    {
        return match ($this->platform) {
            'Windows' => 'fab fa-windows',
            'macOS' => 'fab fa-apple',
            'Linux' => 'fab fa-linux',
            'Android' => 'fab fa-android',
            'iOS' => 'fab fa-apple',
            default => 'fas fa-desktop',
        };
    }

    public function getBrowserIconAttribute(): string
    {
        return match ($this->browser) {
            'Chrome' => 'fab fa-chrome',
            'Firefox' => 'fab fa-firefox',
            'Safari' => 'fab fa-safari',
            'Edge' => 'fab fa-edge',
            default => 'fas fa-globe',
        };
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeTrusted($query)
    {
        return $query->where('is_trusted', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('device_type', $type);
    }

    public function scopeRecentlyUsed($query, $days = 30)
    {
        return $query->where('last_used_at', '>', now()->subDays($days));
    }
}
