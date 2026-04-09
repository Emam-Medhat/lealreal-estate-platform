<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_session_id',
        'user_id',
        'conversion_type',
        'conversion_value',
        'property_id',
        'conversion_step',
        'source',
        'medium',
        'campaign',
        'converted_at',
        'properties',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'conversion_value' => 'decimal:2',
        'converted_at' => 'datetime',
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function userSession(): BelongsTo
    {
        return $this->belongsTo(UserSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(AnalyticEvent::class, 'user_session_id', 'user_session_id');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('conversion_type', $type);
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('converted_at', [$startDate, $endDate]);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('converted_at', '>', now()->subDays($days));
    }

    public function isPurchase()
    {
        return $this->conversion_type === 'purchase';
    }

    public function isLead()
    {
        return $this->conversion_type === 'lead';
    }

    public function isSignup()
    {
        return $this->conversion_type === 'signup';
    }

    public function getConversionPathAttribute()
    {
        $events = $this->events()->orderBy('created_at')->get();
        $path = [];
        
        foreach ($events as $event) {
            $path[] = $event->event_name;
        }
        
        return $path;
    }

    public function getTimeToConvertAttribute()
    {
        if (!$this->userSession || !$this->converted_at) {
            return null;
        }

        $firstEvent = $this->userSession->events()->orderBy('created_at')->first();
        
        if ($firstEvent && $this->converted_at) {
            return $firstEvent->created_at->diffInMinutes($this->converted_at);
        }

        return null;
    }

    public function getConversionValuePerSessionAttribute()
    {
        if (!$this->userSession) {
            return 0;
        }

        $sessionEvents = $this->userSession->events()->count();
        return $sessionEvents > 0 ? $this->conversion_value / $sessionEvents : 0;
    }
}
