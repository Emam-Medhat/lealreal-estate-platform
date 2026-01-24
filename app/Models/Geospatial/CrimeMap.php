<?php

namespace App\Models\Geospatial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Metaverse\MetaverseProperty;

class CrimeMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'analytics_id',
        'safety_score',
        'crime_rate',
        'crime_types',
        'crime_trends',
        'incident_data',
        'police_presence',
        'neighborhood_watch',
        'security_measures',
        'safety_recommendations',
        'comparative_analysis',
        'analysis_parameters',
        'metadata',
        'status',
    ];

    protected $casts = [
        'crime_types' => 'array',
        'crime_trends' => 'array',
        'incident_data' => 'array',
        'police_presence' => 'array',
        'neighborhood_watch' => 'array',
        'security_measures' => 'array',
        'safety_recommendations' => 'array',
        'comparative_analysis' => 'array',
        'analysis_parameters' => 'array',
        'metadata' => 'array',
        'safety_score' => 'decimal:2',
        'crime_rate' => 'decimal:2',
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

    public function scopeVerySafe($query)
    {
        return $query->where('safety_score', '>=', 9.0);
    }

    public function scopeSafe($query)
    {
        return $query->where('safety_score', '>=', 7.0);
    }

    public function scopeUnsafe($query)
    {
        return $query->where('safety_score', '<', 5.0);
    }

    public function scopeLowCrime($query)
    {
        return $query->where('crime_rate', '<=', 2.0);
    }
}
