<?php

namespace App\Models\Geospatial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Metaverse\MetaverseProperty;

class LocationIntelligence extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'analytics_id',
        'intelligence_type',
        'location_score',
        'market_analysis',
        'competitive_analysis',
        'investment_potential',
        'growth_indicators',
        'risk_factors',
        'recommendations',
        'analysis_parameters',
        'metadata',
        'status',
    ];

    protected $casts = [
        'market_analysis' => 'array',
        'competitive_analysis' => 'array',
        'growth_indicators' => 'array',
        'risk_factors' => 'array',
        'recommendations' => 'array',
        'analysis_parameters' => 'array',
        'metadata' => 'array',
        'location_score' => 'decimal:2',
        'investment_potential' => 'decimal:2',
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
    public function scopeByType($query, $type)
    {
        return $query->where('intelligence_type', $type);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeHighPotential($query)
    {
        return $query->where('investment_potential', '>=', 8.0);
    }

    public function scopeLowRisk($query)
    {
        return $query->whereJsonLength('risk_factors', '<=', 2);
    }
}
