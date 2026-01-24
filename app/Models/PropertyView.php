<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyView extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'session_id',
        'ip_address',
        'user_agent',
        'user_id',
        'referrer',
        'view_type',
        'duration_seconds',
        'metadata',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'metadata' => 'array',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('view_type', $type);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeUniqueViews($query)
    {
        return $query->groupBy('ip_address', 'property_id');
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration_seconds) {
            return 'N/A';
        }
        
        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;
        
        if ($minutes > 0) {
            return $minutes . 'm ' . $seconds . 's';
        }
        
        return $seconds . 's';
    }
}
