<?php

namespace App\Models\Geospatial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Metaverse\MetaverseProperty;

class TransitScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'analytics_id',
        'transit_score',
        'bus_score',
        'metro_score',
        'tram_score',
        'train_score',
        'transit_options',
        'accessibility_metrics',
        'service_frequency',
        'coverage_analysis',
        'improvement_suggestions',
        'analysis_parameters',
        'metadata',
        'status',
    ];

    protected $casts = [
        'transit_options' => 'array',
        'accessibility_metrics' => 'array',
        'service_frequency' => 'array',
        'coverage_analysis' => 'array',
        'improvement_suggestions' => 'array',
        'analysis_parameters' => 'array',
        'metadata' => 'array',
        'transit_score' => 'decimal:2',
        'bus_score' => 'decimal:2',
        'metro_score' => 'decimal:2',
        'tram_score' => 'decimal:2',
        'train_score' => 'decimal:2',
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

    public function scopeExcellentTransit($query)
    {
        return $query->where('transit_score', '>=', 90);
    }

    public function scopeGoodTransit($query)
    {
        return $query->where('transit_score', '>=', 70);
    }

    public function scopePoorTransit($query)
    {
        return $query->where('transit_score', '<', 50);
    }
}
