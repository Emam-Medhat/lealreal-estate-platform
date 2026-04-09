<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_session_id',
        'event_name',
        'page_url',
        'user_agent',
        'ip_address',
        'properties',
        'created_at',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    public function userSession(): BelongsTo
    {
        return $this->belongsTo(UserSession::class);
    }

    public function scopeByEvent($query, $eventName)
    {
        return $query->where('event_name', $eventName);
    }

    public function scopeByPage($query, $pageUrl)
    {
        return $query->where('page_url', $pageUrl);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>', now()->subHours($hours));
    }

    public function getPropertyAttribute()
    {
        return $this->properties['property'] ?? null;
    }

    public function getDeviceTypeAttribute()
    {
        $userAgent = $this->user_agent ?? '';
        
        if (strpos($userAgent, 'Mobile') !== false) {
            return 'mobile';
        } elseif (strpos($userAgent, 'Tablet') !== false) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    public function getBrowserAttribute()
    {
        $userAgent = $this->user_agent ?? '';
        
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'edge';
        } else {
            return 'other';
        }
    }

    public function getOsAttribute()
    {
        $userAgent = $this->user_agent ?? '';
        
        if (strpos($userAgent, 'Windows') !== false) {
            return 'windows';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            return 'macos';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            return 'linux';
        } elseif (strpos($userAgent, 'Android') !== false) {
            return 'android';
        } elseif (strpos($userAgent, 'iOS') !== false) {
            return 'ios';
        } else {
            return 'other';
        }
    }

    public function isPageView()
    {
        return $this->event_name === 'page_view';
    }

    public function isClick()
    {
        return $this->event_name === 'click';
    }

    public function isConversion()
    {
        return in_array($this->event_name, ['purchase', 'signup', 'lead']);
    }

    public function isInteraction()
    {
        return in_array($this->event_name, ['click', 'scroll', 'form_submit', 'video_play']);
    }
}
