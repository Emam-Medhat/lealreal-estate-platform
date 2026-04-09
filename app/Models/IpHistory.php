<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpHistory extends Model
{
    protected $table = 'ip_history';

    protected $fillable = [
        'ip_address',
        'user_id',
        'action',
        'user_agent',
        'country_code',
        'city',
        'metadata',
        'is_vpn',
        'is_proxy',
        'is_tor',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_vpn' => 'boolean',
        'is_proxy' => 'boolean',
        'is_tor' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Count distinct users from a given IP address within a time window.
     */
    public static function countUsersFromIp(string $ip, int $hours = 24): int
    {
        return static::where('ip_address', $ip)
            ->where('created_at', '>=', now()->subHours($hours))
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * Get all IPs a user has used recently.
     */
    public static function getUserIps(int $userId, int $days = 7): \Illuminate\Support\Collection
    {
        return static::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->distinct('ip_address')
            ->pluck('ip_address');
    }

    public function scopeForIp($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecentHours($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
}
