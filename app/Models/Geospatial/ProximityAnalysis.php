<?php

namespace App\Models\Geospatial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Metaverse\MetaverseProperty;

class ProximityAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'analytics_id',
        'proximity_score',
        'walk_score',
        'transit_score',
        'amenity_scores',
        'distance_analysis',
        'accessibility_metrics',
        'nearby_facilities',
        'analysis_radius',
        'analysis_parameters',
        'metadata',
        'status',
    ];

    protected $casts = [
        'amenity_scores' => 'array',
        'distance_analysis' => 'array',
        'accessibility_metrics' => 'array',
        'nearby_facilities' => 'array',
        'analysis_parameters' => 'array',
        'metadata' => 'array',
        'proximity_score' => 'decimal:2',
        'walk_score' => 'decimal:2',
        'transit_score' => 'decimal:2',
        'analysis_radius' => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(MetaverseProperty::class);
    }

    public function analytics(): BelongsTo
    {
        return $this->belongsTo(GeospatialAnalytics::class, 'analytics_id');
    }

    // Scopes
    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeHighWalkability($query)
    {
        return $query->where('walk_score', '>=', 70);
    }

    public function scopeGoodTransit($query)
    {
        return $query->where('transit_score', '>=', 60);
    }

    public function scopeHighProximity($query)
    {
        return $query->where('proximity_score', '>=', 8.0);
    }
}
