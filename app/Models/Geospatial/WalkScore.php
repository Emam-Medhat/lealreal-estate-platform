<?php

namespace App\Models\Geospatial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Metaverse\MetaverseProperty;

class WalkScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'analytics_id',
        'walk_score',
        'transit_score',
        'bike_score',
        'amenity_scores',
        'walkability_factors',
        'nearby_amenities',
        'pedestrian_infrastructure',
        'safety_metrics',
        'improvement_suggestions',
        'analysis_parameters',
        'metadata',
        'status',
    ];

    protected $casts = [
        'amenity_scores' => 'array',
        'walkability_factors' => 'array',
        'nearby_amenities' => 'array',
        'pedestrian_infrastructure' => 'array',
        'safety_metrics' => 'array',
        'improvement_suggestions' => 'array',
        'analysis_parameters' => 'array',
        'metadata' => 'array',
        'walk_score' => 'decimal:2',
        'transit_score' => 'decimal:2',
        'bike_score' => 'decimal:2',
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

    public function scopeExcellentWalkability($query)
    {
        return $query->where('walk_score', '>=', 90);
    }

    public function scopeGoodWalkability($query)
    {
        return $query->where('walk_score', '>=', 70);
    }

    public function scopePoorWalkability($query)
    {
        return $query->where('walk_score', '<', 50);
    }
}
