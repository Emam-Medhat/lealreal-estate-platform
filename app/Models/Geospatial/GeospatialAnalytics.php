<?php

namespace App\Models\Geospatial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Metaverse\MetaverseProperty;

class GeospatialAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'analysis_type',
        'analysis_radius',
        'analysis_parameters',
        'analysis_results',
        'confidence_score',
        'data_quality_score',
        'analysis_date',
        'status',
        'metadata',
    ];

    protected $casts = [
        'analysis_parameters' => 'array',
        'analysis_results' => 'array',
        'metadata' => 'array',
        'analysis_date' => 'datetime',
        'confidence_score' => 'decimal:2',
        'data_quality_score' => 'decimal:2',
    ];

    public function property()
    {
        return $this->belongsTo(MetaverseProperty::class);
    }

    public function heatmaps(): HasMany
    {
        return $this->hasMany(Heatmap::class, 'analytics_id');
    }

    public function locationIntelligence(): HasMany
    {
        return $this->hasMany(LocationIntelligence::class, 'analytics_id');
    }

    public function proximityAnalyses(): HasMany
    {
        return $this->hasMany(ProximityAnalysis::class, 'analytics_id');
    }

    public function demographicAnalyses(): HasMany
    {
        return $this->hasMany(DemographicAnalysis::class, 'analytics_id');
    }

    public function propertyDensities(): HasMany
    {
        return $this->hasMany(PropertyDensity::class, 'analytics_id');
    }

    public function walkScores(): HasMany
    {
        return $this->hasMany(WalkScore::class, 'analytics_id');
    }

    public function transitScores(): HasMany
    {
        return $this->hasMany(TransitScore::class, 'analytics_id');
    }

    public function schoolDistricts(): HasMany
    {
        return $this->hasMany(SchoolDistrict::class, 'analytics_id');
    }

    public function crimeMaps(): HasMany
    {
        return $this->hasMany(CrimeMap::class, 'analytics_id');
    }

    public function floodRisks(): HasMany
    {
        return $this->hasMany(FloodRisk::class, 'analytics_id');
    }

    public function earthquakeRisks(): HasMany
    {
        return $this->hasMany(EarthquakeRisk::class, 'analytics_id');
    }

    public function propertyAppreciationMaps(): HasMany
    {
        return $this->hasMany(PropertyAppreciationMap::class, 'analytics_id');
    }

    // Scopes
    public function scopeByAnalysisType($query, $type)
    {
        return $query->where('analysis_type', $type);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeHighConfidence($query)
    {
        return $query->where('confidence_score', '>=', 0.8);
    }

    public function scopeHighDataQuality($query)
    {
        return $query->where('data_quality_score', '>=', 0.8);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('analysis_date', '>=', now()->subDays($days));
    }
}
