<?php

namespace App\Models\Geospatial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Metaverse\MetaverseProperty;

class FloodRisk extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'analytics_id',
        'risk_score',
        'risk_level',
        'flood_zone',
        'elevation_data',
        'historical_floods',
        'flood_probability',
        'potential_damage',
        'mitigation_measures',
        'insurance_requirements',
        'climate_change_impact',
        'emergency_routes',
        'analysis_parameters',
        'metadata',
        'status',
    ];

    protected $casts = [
        'elevation_data' => 'array',
        'historical_floods' => 'array',
        'mitigation_measures' => 'array',
        'insurance_requirements' => 'array',
        'climate_change_impact' => 'array',
        'emergency_routes' => 'array',
        'analysis_parameters' => 'array',
        'metadata' => 'array',
        'risk_score' => 'decimal:2',
        'flood_probability' => 'decimal:2',
        'potential_damage' => 'decimal:2',
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

    public function scopeByRiskLevel($query, $level)
    {
        return $query->where('risk_level', $level);
    }

    public function scopeLowRisk($query)
    {
        return $query->where('risk_score', '<=', 3.0);
    }

    public function scopeHighRisk($query)
    {
        return $query->where('risk_score', '>=', 7.0);
    }

    public function scopeVeryHighRisk($query)
    {
        return $query->where('risk_score', '>=', 8.5);
    }
}
