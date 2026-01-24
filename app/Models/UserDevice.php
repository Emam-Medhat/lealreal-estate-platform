<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_name',
        'device_type',
        'platform',
        'browser',
        'browser_version',
        'ip_address',
        'user_agent',
        'fingerprint',
        'is_trusted',
        'is_active',
        'last_used_at',
        'expires_at',
        'location',
        'metadata',
    ];

    protected $casts = [
        'is_trusted' => 'boolean',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'location' => 'json',
        'metadata' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isMobile(): bool
    {
        return in_array($this->device_type, ['mobile', 'tablet']);
    }

    public function isDesktop(): bool
    {
        return $this->device_type === 'desktop';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTrusted($query)
    {
        return $query->where('is_trusted', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }
}
