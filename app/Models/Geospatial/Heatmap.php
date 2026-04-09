<?php

namespace App\Models\Geospatial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Metaverse\MetaverseProperty;

class Heatmap extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'analytics_id',
        'heatmap_type',
        'data_points',
        'intensity_levels',
        'color_scheme',
        'bounds',
        'zoom_level',
        'grid_size',
        'analysis_parameters',
        'metadata',
        'status',
    ];

    protected $casts = [
        'data_points' => 'array',
        'intensity_levels' => 'array',
        'bounds' => 'array',
        'analysis_parameters' => 'array',
        'metadata' => 'array',
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
        return $query->where('heatmap_type', $type);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByColorScheme($query, $scheme)
    {
        return $query->where('color_scheme', $scheme);
    }
}
