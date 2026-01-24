<?php

namespace App\Models\Geospatial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Metaverse\MetaverseProperty;

class PropertyAppreciationMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'analytics_id',
        'analysis_method',
        'time_period',
        'analysis_radius',
        'include_economic_factors',
        'include_market_sentiment',
        'weight_factors',
        'current_value',
        'annual_appreciation_rate',
        'projected_value_5yr',
        'projected_value_10yr',
        'market_trend',
        'appreciation_drivers',
        'risk_factors',
        'investment_recommendations',
        'metadata',
        'status',
    ];

    protected $casts = [
        'weight_factors' => 'array',
        'appreciation_drivers' => 'array',
        'risk_factors' => 'array',
        'investment_recommendations' => 'array',
        'metadata' => 'array',
        'include_economic_factors' => 'boolean',
        'include_market_sentiment' => 'boolean',
        'current_value' => 'decimal:2',
        'annual_appreciation_rate' => 'decimal:2',
        'projected_value_5yr' => 'decimal:2',
        'projected_value_10yr' => 'decimal:2',
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

    public function scopeByAnalysisMethod($query, $method)
    {
        return $query->where('analysis_method', $method);
    }

    public function scopeByMarketTrend($query, $trend)
    {
        return $query->where('market_trend', $trend);
    }

    public function scopeHighAppreciation($query)
    {
        return $query->where('annual_appreciation_rate', '>=', 10.0);
    }

    public function scopeModerateAppreciation($query)
    {
        return $query->where('annual_appreciation_rate', '>=', 5.0)->where('annual_appreciation_rate', '<', 10.0);
    }

    public function scopeLowAppreciation($query)
    {
        return $query->where('annual_appreciation_rate', '<', 5.0);
    }

    public function scopeBullishMarket($query)
    {
        return $query->where('market_trend', 'bullish');
    }

    public function scopeBearishMarket($query)
    {
        return $query->where('market_trend', 'bearish');
    }

    public function scopeStableMarket($query)
    {
        return $query->where('market_trend', 'stable');
    }
}
