<?php

namespace App\Models\Geospatial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Metaverse\MetaverseProperty;

class DemographicAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'analytics_id',
        'population_density',
        'median_income',
        'age_distribution',
        'education_levels',
        'employment_rate',
        'household_composition',
        'ethnic_diversity',
        'migration_patterns',
        'demographic_trends',
        'analysis_parameters',
        'metadata',
        'status',
    ];

    protected $casts = [
        'age_distribution' => 'array',
        'education_levels' => 'array',
        'household_composition' => 'array',
        'ethnic_diversity' => 'array',
        'migration_patterns' => 'array',
        'demographic_trends' => 'array',
        'analysis_parameters' => 'array',
        'metadata' => 'array',
        'population_density' => 'decimal:2',
        'median_income' => 'decimal:2',
        'employment_rate' => 'decimal:2',
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

    public function scopeHighIncome($query)
    {
        return $query->where('median_income', '>=', 100000);
    }

    public function scopeHighEmployment($query)
    {
        return $query->where('employment_rate', '>=', 85);
    }

    public function scopeHighDensity($query)
    {
        return $query->where('population_density', '>=', 1000);
    }
}
