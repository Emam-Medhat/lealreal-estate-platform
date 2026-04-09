<?php

namespace App\Models\Geospatial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Metaverse\MetaverseProperty;

class EarthquakeRisk extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'analytics_id',
        'risk_score',
        'risk_level',
        'seismic_zone',
        'fault_line_distance',
        'soil_type',
        'building_code_compliance',
        'structural_assessment',
        'historical_earthquakes',
        'probability_magnitude',
        'potential_damage',
        'mitigation_recommendations',
        'retrofitting_needs',
        'analysis_parameters',
        'metadata',
        'status',
    ];

    protected $casts = [
        'soil_type' => 'array',
        'building_code_compliance' => 'array',
        'structural_assessment' => 'array',
        'historical_earthquakes' => 'array',
        'probability_magnitude' => 'array',
        'potential_damage' => 'array',
        'mitigation_recommendations' => 'array',
        'retrofitting_needs' => 'array',
        'analysis_parameters' => 'array',
        'metadata' => 'array',
        'risk_score' => 'decimal:2',
        'fault_line_distance' => 'decimal:2',
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

    public function scopeBySeismicZone($query, $zone)
    {
        return $query->where('seismic_zone', $zone);
    }

    public function scopeLowRisk($query)
    {
        return $query->where('risk_score', '<=', 3.0);
    }

    public function scopeModerateRisk($query)
    {
        return $query->where('risk_score', '>', 3.0)->where('risk_score', '<=', 6.0);
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
