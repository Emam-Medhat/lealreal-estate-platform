<?php

namespace App\Models\Neighborhood;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Neighborhood extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'city',
        'district',
        'description',
        'property_type',
        'status',
        'latitude',
        'longitude',
        'boundaries',
        'features',
        'amenities',
        'transportation',
        'schools',
        'healthcare',
        'shopping',
        'recreation',
        'safety_rating',
        'walkability_score',
        'transit_score',
        'green_space_ratio',
        'average_price',
        'price_range',
        'property_count',
        'resident_count',
        'population_density',
        'development_status',
        'infrastructure_quality',
        'community_engagement',
        'metadata',
        'rating',
        'review_count',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'latitude' => 'decimal:8,6',
        'longitude' => 'decimal:9,6',
        'boundaries' => 'array',
        'features' => 'array',
        'amenities' => 'array',
        'transportation' => 'array',
        'schools' => 'array',
        'healthcare' => 'array',
        'shopping' => 'array',
        'recreation' => 'array',
        'safety_rating' => 'decimal:2,1',
        'walkability_score' => 'decimal:2,1',
        'transit_score' => 'decimal:2,1',
        'green_space_ratio' => 'decimal:5,2',
        'average_price' => 'decimal:10,2',
        'price_range' => 'array',
        'property_count' => 'integer',
        'resident_count' => 'integer',
        'population_density' => 'decimal:8,2',
        'development_status' => 'string',
        'infrastructure_quality' => 'string',
        'community_engagement' => 'string',
        'metadata' => 'array',
        'rating' => 'decimal:2,1',
        'review_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the community associated with the neighborhood.
     */
    public function community(): HasOne
    {
        return $this->hasOne(Community::class, 'neighborhood_id');
    }

    /**
     * Get the guides for the neighborhood.
     */
    public function guides(): HasMany
    {
        return $this->hasMany(NeighborhoodGuide::class, 'neighborhood_id');
    }

    /**
     * Get the reviews for the neighborhood.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(NeighborhoodReview::class, 'neighborhood_id');
    }

    /**
     * Get the businesses in the neighborhood.
     */
    public function businesses(): HasMany
    {
        return $this->hasMany(LocalBusiness::class, 'neighborhood_id');
    }

    /**
     * Get the amenities in the neighborhood.
     */
    public function amenities(): HasMany
    {
        return $this->hasMany(CommunityAmenity::class, 'neighborhood_id');
    }

    /**
     * Get the events in the neighborhood.
     */
    public function events(): HasMany
    {
        return $this->hasMany(CommunityEvent::class, 'neighborhood_id');
    }

    /**
     * Get the statistics for the neighborhood.
     */
    public function statistics(): HasMany
    {
        return $this->hasMany(NeighborhoodStatistic::class, 'neighborhood_id');
    }

    /**
     * Get the boundaries for the neighborhood.
     */
    public function boundaries(): HasOne
    {
        return $this->hasOne(NeighborhoodBoundary::class, 'neighborhood_id');
    }

    /**
     * Scope a query to only include active neighborhoods.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to filter by city.
     */
    public function scopeByCity(Builder $query, string $city): Builder
    {
        return $query->where('city', $city);
    }

    /**
     * Scope a query to filter by district.
     */
    public function scopeByDistrict(Builder $query, string $district): Builder
    {
        return $query->where('district', $district);
    }

    /**
     * Scope a query to filter by property type.
     */
    public function scopeByPropertyType(Builder $query, string $propertyType): Builder
    {
        return $query->where('property_type', $propertyType);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by rating range.
     */
    public function scopeByRatingRange(Builder $query, $min, $max = null): Builder
    {
        if ($min !== null) {
            $query->where('rating', '>=', $min);
        }
        if ($max !== null) {
            $query->where('rating', '<=', $max);
        }
        return $query;
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopeByPriceRange(Builder $query, $min, $max = null): Builder
    {
        if ($min !== null) {
            $query->where('average_price', '>=', $min);
        }
        if ($max !== null) {
            $query->where('average_price', '<=', $max);
        }
        return $query;
    }

    /**
     * Scope a query to filter by resident count.
     */
    public function scopeByResidentCount(Builder $query, $min, $max = null): Builder
    {
        if ($min !== null) {
            $query->where('resident_count', '>=', $min);
        }
        if ($max !== null) {
            $query->where('resident_count', '<=', $max);
        }
        return $query;
    }

    /**
     * Scope a query to get neighborhoods with high ratings.
     */
    public function scopeHighRated(Builder $query): Builder
    {
        return $query->where('rating', '>=', 4.0);
    }

    /**
     * Scope a query to get neighborhoods with high walkability scores.
     */
    public function scopeHighWalkability(Builder $query): Builder
    {
        return $query->where('walkability_score', '>=', 70);
    }

    /**
     * Scope a query to get neighborhoods with good transit access.
     */
    public function scopeGoodTransit(Builder $query): Builder
    {
        return $query->where('transit_score', '>=', 70);
    }

    /**
     * Scope a query to get neighborhoods with high green space ratio.
     */
    public scopeGreenSpace(Builder $query): Builder
    {
        return $query->where('green_space_ratio', '>=', 30);
    }

    /**
     * Get the average rating for the neighborhood.
     */
    public function getAverageRatingAttribute(): float
    {
        return $this->rating ?? 0;
    }

    /**
     * Get the formatted price range.
     */
    public function getFormattedPriceRangeAttribute(): string
    {
        if (!$this->price_range) {
            return 'غير محدد';
        }

        $min = $this->price_range['min'] ?? 0;
        $max = $this->price_range['max'] ?? 0;

        if ($min === 0 && $max === 0) {
            return 'غير محدد';
        }

        return number_format($min, 2) . ' - ' . number_format($max, 2) . ' ريال';
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'development' => 'قيد التطوير',
            'planned' => 'مخططط',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Get the property type label.
     */
    public function getPropertyTypeLabelAttribute(): string
    {
        $types = [
            'residential' => 'سكني',
            'commercial' => 'تجاري',
            'mixed' => 'مختلط',
            'industrial' => 'صناعي',
        ];

        return $types[$this->property_type] ?? 'غير معروف';
    }

    /**
     * Get the development status label.
     */
    public function getDevelopmentStatusLabelAttribute(): string
    {
        $statuses = [
            'planning' => 'التخطيط',
            'under_development' => 'قيد التطوير',
            'developing' => 'قيد التطوير',
            'completed' => 'مكتمل',
            'maintenance' => 'صيانة',
        ];

        return $statuses[$this->development_status] ?? 'غير معروف';
    }

    /**
     * Get the infrastructure quality label.
     */
    public function getInfrastructureQualityLabelAttribute(): string
    {
        $qualities = [
            'excellent' => 'ممتطور',
            'good' => 'جيد',
            'fair' => 'متوسط',
            'poor' => 'ضعيف',
        ];

        return $qualities[$this->infrastructure_quality] ?? 'غير معروف';
    }

    /**
     * Get the community engagement label.
     */
    public function getCommunityEngagementLabelAttribute(): string
    {
        $levels = [
            'high' => 'عالي',
            'medium' => 'متوسط',
            'low' => 'منخفض',
        ];

        return $levels[$this->community_engagement] ?? 'غير معروف';
    }

    /**
     * Get the safety rating label.
     */
    public function getSafetyRatingLabelAttribute(): string
    {
        if ($this->safety_rating >= 8.0) {
            'آمن';
        } elseif ($this->safety_rating >= 6.0) {
            'جيد جداً';
        } elseif ($this->safety_rating >= 4.0) => {
            'جيد';
        } else {
            'ضعيف';
        }

        return $this->safety_rating . ' / 10';
    }

    /**
     * Get the walkability score label.
     */
    public function getWalkabilityScoreLabelAttribute(): string
    {
        if ($this->walkability_score >= 80) {
            'ممتاح';
        } elseif ($this->walkability_score >= 60) => {
            'جيد';
        } elseif ($this->walkability_score >= 40) => 'متوسط';
        } else {
            'ضعيف';
        }

        return $this->walkability_score . ' / 100';
    }

    /**
     * Get the transit score label.
     */
    public function getTransitScoreLabelAttribute(): string
    {
        if ($this->transit_score >= 80) {
            'ممتاح';
        } elseif ($this->transit_score >= 60) => {
            'جيد';
        } elseif ($this->transit_score >= 40) => 'متوسط';
        } else {
            'ضعيف';
        }

        return $this->transit_score . ' / 100';
    }

    /**
     * Get the green space ratio label.
     */
    public function getGreenSpaceRatioLabelAttribute(): string
    {
        if ($this->green_space_ratio >= 50) {
            'عالي';
        } elseif ($this->green_space_ratio >= 30) => 'جيد';
        } elseif ($this->green_space_ratio >= 20) => 'متوسط';
        else {
            'منخفض';
        }

        return $this->green_space_ratio . '%';
    }

    /**
     * Get the population density label.
     */
    public function getPopulationDensityLabelAttribute(): string
    {
        if ($this->population_density >= 10000) {
            'كثيف';
        } elseif ($this->population_density >= 5000) => 'عالي';
        } elseif ($this->population_density >= 1000) => 'متوسط';
        } else {
            'منخفض';
        }

        return number_format($this->population_density, 2) . ' نسم²';
    }

    /**
     * Get the average price label.
     */
    public function getAveragePriceLabelAttribute(): string
    {
        return number_format($this->average_price, 2) . ' ريال';
    }

    /**
     * Get the resident count label.
     */
    public function getResidentCountLabelAttribute(): string
    {
        return number_format($this->resident_count) . ' ساكن';
    }

    /**
     * Get the property count label.
     */
    public function getPropertyCountLabelAttribute(): string
    {
        return number_format($this->property_count) . ' عقار';
    }

    /**
     * Get the formatted coordinates.
     */
    public function getCoordinatesAttribute(): string
    {
        if ($this->latitude && $this->longitude) {
            return $this->latitude . ', ' . $this->longitude;
        }
        return 'غير محدد';
    }

    /**
     * Get the formatted boundaries.
     */
    public function getFormattedBoundariesAttribute(): string
    {
        if (!$this->boundaries) {
            return 'غير محدد';
        }

        return sprintf(
            'شمال: %.6f, جنوب: %.6f, شرق: %.6f, غرب: %.6f',
            $this->boundaries['north'] ?? 0,
            $this->boundaries['south'] ?? 0,
            $this->boundaries['east'] ?? 0,
            $this->boundaries['west'] ?? 0
        );
    }

    /**
     * Get the features array as a string.
     */
    public function getFeaturesListAttribute(): string
    {
        return implode(', ', $this->features ?? []);
    }

    /**
     * Get the amenities array as a string.
     */
    public function getAmenitiesListAttribute(): string
    {
        return implode(', ', $this->amenities ?? []);
    }

    /**
     * Get the transportation array as a string.
     */
    public function getTransportationListAttribute(): string
    {
        return implode(', ', $this->transportation ?? []);
    }

    /**
     * Get the schools array as a string.
     */
    public function getSchoolsListAttribute(): string
    {
        return implode(', ', $this->schools ?? []);
    }

    /**
     * Get the healthcare array as a string.
     */
    public function getHealthcareListAttribute(): string
    {
        return implode(', ', $this->healthcare ?? []);
    }

    /**
     * Get the shopping array as a string.
     */
    public function getShoppingListAttribute(): string
    {
        return implode(', ', $this->shopping ?? []);
    }

    /**
     * Get the recreation array as a string.
     */
    public function getRecreationListAttribute(): string
    {
        return implode(', ', $this->recreation ?? []);
    }

    /**
     * Get the metadata as JSON.
     */
    public function getMetadataAttribute(): string
    {
        return json_encode($this->metadata ?? []);
    }

    /**
     * Check if the neighborhood has coordinates.
     */
    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Check if the neighborhood has boundaries.
     */
    public function hasBoundaries(): bool
    {
        return !is_null($this->boundaries);
    }

    /**
     * Check if the neighborhood is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the neighborhood is in development.
     */
    public function isDevelopment(): bool
    {
        return in_array($this->development_status, ['planning', 'under_development', 'developing']);
    }

    /**
     * Check if the neighborhood is highly rated.
     */
    public function isHighlyRated(): bool
    {
        return $this->rating >= 4.0;
    }

    /**
     * Check if the neighborhood has good walkability.
     */
    public function hasGoodWalkability(): bool
    {
        return $this->walkability_score >= 70;
    }

    /**
     * Check if the neighborhood has good transit access.
     */
    public function hasGoodTransit(): bool
    {
        return $this->transit_score >= 70;
    }

    /**
     * Check if the neighborhood has sufficient green space.
     */
    public function hasGreenSpace(): bool
    {
        return $this->green_space_ratio >= 30;
    }

    /**
     * Check if the neighborhood is safe.
     */
    public function isSafe(): bool
    {
        return $this->safety_rating >= 6.0;
    }

    /**
     * Get the overall score.
     */
    public function getOverallScore(): float
    {
        // Calculate a weighted score based on various factors
        $scores = [
            'rating' => $this->rating * 0.25,
            'safety_rating' => $this->safety_rating * 0.20,
            'walkability_score' => ($this->walkability_score / 100) * 0.15,
            'transit_score' => ($this->transit_score / 100) * 0.15,
            'green_space_ratio' => ($this->green_space / 100) * 0.10,
            'infrastructure_quality' => $this->getInfrastructureQualityScore() * 0.15,
        ];

        return array_sum($scores);
    }

    /**
     * Get infrastructure quality score.
     */
    private function getInfrastructureQualityScore(): float
    {
        $qualityScores = [
            'excellent' => 1.0,
            'good' => 0.75,
            'fair' => 0.5,
            'poor' => 0.25,
        ];

        return $qualityScores[$this->infrastructure_quality] ?? 0.5;
    }

    /**
     * Get the full address.
     */
    public function getFullAddressAttribute(): string
    {
        return $this->city . ', $this->district . ', $this->name;
    }

    /**
     * Get the search index.
     */
    public function getSearchIndex(): array
    {
        return [
            'name' => $this->name,
            'city' => $this->city,
            'district' => $this->district,
            'description' => $this->description,
            'property_type' => $this->property_type,
            'status' => $this->status,
            'rating' => $this->rating,
            'average_price' => $this->average_price,
            'resident_count' => $this->resident_count,
            'safety_rating' => $this->safety_rating,
            'walkability_score' => $this->walkability_score,
            'transit_score' => $this->transit_score,
            'green_space_ratio' => $this->green_space_ratio,
        ];
    }

    /**
     * Bootstrap the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($builder) {
            $builder->whereNull('deleted_at');
        });
    }
}
