<?php

namespace App\Models\Geospatial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Metaverse\MetaverseProperty;

class PropertyDensity extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'analytics_id',
        'density_level',
        'density_score',
        'property_count',
        'area_size',
        'density_distribution',
        'property_types',
        'density_trends',
        'development_potential',
        'analysis_radius',
        'analysis_parameters',
        'metadata',
        'status',
    ];

    protected $casts = [
        'density_distribution' => 'array',
        'property_types' => 'array',
        'density_trends' => 'array',
        'development_potential' => 'array',
        'analysis_parameters' => 'array',
        'metadata' => 'array',
        'density_score' => 'decimal:2',
        'property_count' => 'integer',
        'area_size' => 'decimal:2',
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

    public function scopeByDensityLevel($query, $level)
    {
        return $query->where('density_level', $level);
    }

    public function scopeHighDensity($query)
    {
        return $query->where('density_score', '>=', 8.0);
    }

    public function scopeLowDensity($query)
    {
        return $query->where('density_score', '<=', 3.0);
    }
}
