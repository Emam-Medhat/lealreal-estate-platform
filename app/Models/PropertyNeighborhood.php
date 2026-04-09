<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyNeighborhood extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'name',
        'description',
        'distance_km',
        'travel_time_minutes',
        'transportation_method',
        'amenities',
        'demographics',
        'safety_rating',
        'livability_score',
        'statistics',
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'travel_time_minutes' => 'integer',
        'amenities' => 'array',
        'demographics' => 'array',
        'safety_rating' => 'decimal:2',
        'livability_score' => 'decimal:2',
        'statistics' => 'array',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function getFormattedDistanceAttribute(): ?string
    {
        if (!$this->distance_km) {
            return null;
        }
        return number_format($this->distance_km, 2) . ' km';
    }

    public function getFormattedTravelTimeAttribute(): ?string
    {
        if (!$this->travel_time_minutes) {
            return null;
        }
        
        if ($this->travel_time_minutes < 60) {
            return $this->travel_time_minutes . ' min';
        }
        
        $hours = floor($this->travel_time_minutes / 60);
        $minutes = $this->travel_time_minutes % 60;
        
        return $hours . 'h ' . $minutes . 'min';
    }

    public function getSafetyStarsAttribute(): string
    {
        if (!$this->safety_rating) {
            return 'N/A';
        }
        
        $stars = round($this->safety_rating / 2);
        return str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
    }

    public function getLivabilityStarsAttribute(): string
    {
        if (!$this->livability_score) {
            return 'N/A';
        }
        
        $stars = round($this->livability_score / 2);
        return str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
    }

    public function scopeByTransportation($query, $method)
    {
        return $query->where('transportation_method', $method);
    }

    public function scopeNearby($query, $maxKm = 5)
    {
        return $query->where('distance_km', '<=', $maxKm);
    }
}
