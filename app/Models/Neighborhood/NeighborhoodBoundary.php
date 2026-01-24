<?php

namespace App\Models\Neighborhood;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class NeighborhoodBoundary extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'neighborhood_id',
        'name',
        'description',
        'boundary_type',
        'status',
        'coordinates',
        'bounds',
        'center_point',
        'area_size',
        'perimeter',
        'elevation_data',
        'land_use',
        'zoning_info',
        'infrastructure',
        'natural_features',
        'access_points',
        'landmarks',
        'metadata',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'neighborhood_id' => 'integer',
        'coordinates' => 'array',
        'bounds' => 'array',
        'center_point' => 'array',
        'area_size' => 'decimal:10,2',
        'perimeter' => 'decimal:10,2',
        'elevation_data' => 'array',
        'land_use' => 'array',
        'zoning_info' => 'array',
        'infrastructure' => 'array',
        'natural_features' => 'array',
        'access_points' => 'array',
        'landmarks' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the neighborhood that owns the boundary.
     */
    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class, 'neighborhood_id');
    }

    /**
     * Scope a query to filter by neighborhood.
     */
    public function scopeByNeighborhood(Builder $query, int $neighborhoodId): Builder
    {
        return $query->where('neighborhood_id', $neighborhoodId);
    }

    /**
     * Scope a query to filter by boundary type.
     */
    public function scopeByType(Builder $query, string $boundaryType): Builder
    {
        return $query->where('boundary_type', $boundaryType);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to get active boundaries.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to get boundaries with coordinates.
     */
    public function scopeWithCoordinates(Builder $query): Builder
    {
        return $query->whereNotNull('coordinates');
    }

    /**
     * Scope a query to get boundaries with bounds.
     */
    public function scopeWithBounds(Builder $query): Builder
    {
        return $query->whereNotNull('bounds');
    }

    /**
     * Scope a query to get boundaries by area size range.
     */
    public function scopeByAreaRange(Builder $query, $min, $max = null): Builder
    {
        if ($min !== null) {
            $query->where('area_size', '>=', $min);
        }
        if ($max !== null) {
            $query->where('area_size', '<=', $max);
        }
        return $query;
    }

    /**
     * Get the boundary type label.
     */
    public function getBoundaryTypeLabelAttribute(): string
    {
        $types = [
            'administrative' => 'إداري',
            'natural' => 'طبيعي',
            'planned' => 'مخطط',
            'historical' => 'تاريخي',
            'custom' => 'مخصص',
        ];

        return $types[$this->boundary_type] ?? 'غير معروف';
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'draft' => 'مسودة',
            'archived' => 'مؤرشف',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Get the coordinates array.
     */
    public function getCoordinatesArrayAttribute(): array
    {
        return $this->coordinates ?? [];
    }

    /**
     * Get the bounds array.
     */
    public function getBoundsArrayAttribute(): array
    {
        return $this->bounds ?? [];
    }

    /**
     * Get the center point.
     */
    public function getCenterPointArrayAttribute(): array
    {
        return $this->center_point ?? [];
    }

    /**
     * Get the elevation data.
     */
    public function getElevationDataAttribute(): array
    {
        return $this->elevation_data ?? [];
    }

    /**
     * Get the land use data.
     */
    public function getLandUseAttribute(): array
    {
        return $this->land_use ?? [];
    }

    /**
     * Get the zoning info.
     */
    public function getZoningInfoAttribute(): array
    {
        return $this->zoning_info ?? [];
    }

    /**
     * Get the infrastructure data.
     */
    public function getInfrastructureAttribute(): array
    {
        return $this->infrastructure ?? [];
    }

    /**
     * Get the natural features.
     */
    public function getNaturalFeaturesAttribute(): array
    {
        return $this->natural_features ?? [];
    }

    /**
     * Get the access points.
     */
    public function getAccessPointsAttribute(): array
    {
        return $this->access_points ?? [];
    }

    /**
     * Get the landmarks.
     */
    public function getLandmarksAttribute(): array
    {
        return $this->landmarks ?? [];
    }

    /**
     * Get the metadata as JSON.
     */
    public function getMetadataAttribute(): string
    {
        return json_encode($this->metadata ?? []);
    }

    /**
     * Get the area size label.
     */
    public function getAreaSizeLabelAttribute(): string
    {
        if ($this->area_size === 0) {
            return 'غير محدد';
        }
        return number_format($this->area_size, 2) . ' كم²';
    }

    /**
     * Get the perimeter label.
     */
    public function getPerimeterLabelAttribute(): string
    {
        if ($this->perimeter === 0) {
            return 'غير محدد';
        }
        return number_format($this->perimeter, 2) . ' كم';
    }

    /**
     * Get the north bound.
     */
    public function getNorthBoundAttribute(): float
    {
        return $this->bounds['north'] ?? 0;
    }

    /**
     * Get the south bound.
     */
    public function getSouthBoundAttribute(): float
    {
        return $this->bounds['south'] ?? 0;
    }

    /**
     * Get the east bound.
     */
    public function getEastBoundAttribute(): float
    {
        return $this->bounds['east'] ?? 0;
    }

    /**
     * Get the west bound.
     */
    public function getWestBoundAttribute(): float
    {
        return $this->bounds['west'] ?? 0;
    }

    /**
     * Get the center latitude.
     */
    public function getCenterLatitudeAttribute(): float
    {
        return $this->center_point['latitude'] ?? 0;
    }

    /**
     * Get the center longitude.
     */
    public function getCenterLongitudeAttribute(): float
    {
        return $this->center_point['longitude'] ?? 0;
    }

    /**
     * Get the center coordinates.
     */
    public function getCenterCoordinatesAttribute(): string
    {
        if ($this->center_point) {
            return $this->center_point['latitude'] . ', ' . $this->center_point['longitude'];
        }
        return 'غير محدد';
    }

    /**
     * Get the formatted bounds.
     */
    public function getFormattedBoundsAttribute(): string
    {
        if (!$this->bounds) {
            return 'غير محدد';
        }

        return sprintf(
            'شمال: %.6f, جنوب: %.6f, شرق: %.6f, غرب: %.6f',
            $this->north_bound,
            $this->south_bound,
            $this->east_bound,
            $this->west_bound
        );
    }

    /**
     * Get the elevation range.
     */
    public function getElevationRangeAttribute(): array
    {
        $elevation = $this->elevation_data;
        
        if (empty($elevation)) {
            return ['min' => 0, 'max' => 0, 'average' => 0];
        }

        return [
            'min' => $elevation['min_elevation'] ?? 0,
            'max' => $elevation['max_elevation'] ?? 0,
            'average' => $elevation['average_elevation'] ?? 0,
        ];
    }

    /**
     * Get the elevation range label.
     */
    public function getElevationRangeLabelAttribute(): string
    {
        $range = $this->elevation_range;
        
        if ($range['min'] === 0 && $range['max'] === 0) {
            return 'غير محدد';
        }

        return $range['min'] . ' - ' . $range['max'] . ' متر (متوسط: ' . $range['average'] . ' متر)';
    }

    /**
     * Get the land use breakdown.
     */
    public function getLandUseBreakdownAttribute(): array
    {
        return $this->land_use['breakdown'] ?? [];
    }

    /**
     * Get the dominant land use.
     */
    public function getDominantLandUseAttribute(): string
    {
        $breakdown = $this->land_use_breakdown;
        
        if (empty($breakdown)) {
            return 'غير محدد';
        }

        $maxPercentage = 0;
        $dominantUse = 'غير محدد';

        foreach ($breakdown as $use => $percentage) {
            if ($percentage > $maxPercentage) {
                $maxPercentage = $percentage;
                $dominantUse = $use;
            }
        }

        return $dominantUse;
    }

    /**
     * Get the zoning types.
     */
    public function getZoningTypesAttribute(): array
    {
        return $this->zoning_info['types'] ?? [];
    }

    /**
     * Get the infrastructure types.
     */
    public function getInfrastructureTypesAttribute(): array
    {
        return $this->infrastructure['types'] ?? [];
    }

    /**
     * Get the natural features types.
     */
    public function getNaturalFeaturesTypesAttribute(): array
    {
        return $this->natural_features['types'] ?? [];
    }

    /**
     * Get the access points count.
     */
    public function getAccessPointsCountAttribute(): int
    {
        return count($this->access_points ?? []);
    }

    /**
     * Get the landmarks count.
     */
    public function getLandmarksCountAttribute(): int
    {
        return count($this->landmarks ?? []);
    }

    /**
     * Check if the boundary is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the boundary is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if the boundary is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the boundary is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Check if the boundary has coordinates.
     */
    public function hasCoordinates(): bool
    {
        return !empty($this->coordinates);
    }

    /**
     * Check if the boundary has bounds.
     */
    public function hasBounds(): bool
    {
        return !empty($this->bounds);
    }

    /**
     * Check if the boundary has a center point.
     */
    public function hasCenterPoint(): bool
    {
        return !empty($this->center_point);
    }

    /**
     * Check if the boundary has elevation data.
     */
    public function hasElevationData(): bool
    {
        return !empty($this->elevation_data);
    }

    /**
     * Check if the boundary has land use data.
     */
    public function hasLandUseData(): bool
    {
        return !empty($this->land_use);
    }

    /**
     * Check if the boundary has zoning info.
     */
    public function hasZoningInfo(): bool
    {
        return !empty($this->zoning_info);
    }

    /**
     * Check if the boundary has infrastructure data.
     */
    public function hasInfrastructureData(): bool
    {
        return !empty($this->infrastructure);
    }

    /**
     * Check if the boundary has natural features.
     */
    public function hasNaturalFeatures(): bool
    {
        return !empty($this->natural_features);
    }

    /**
     * Check if the boundary has access points.
     */
    public function hasAccessPoints(): bool
    {
        return !empty($this->access_points);
    }

    /**
     * Check if the boundary has landmarks.
     */
    public function hasLandmarks(): bool
    {
        return !empty($this->landmarks);
    }

    /**
     * Check if the boundary is large.
     */
    public function isLarge(): bool
    {
        return $this->area_size >= 10; // 10 km² or more
    }

    /**
     * Check if the boundary is medium sized.
     */
    public function isMedium(): bool
    {
        return $this->area_size >= 1 && $this->area_size < 10; // 1-10 km²
    }

    /**
     * Check if the boundary is small.
     */
    public function isSmall(): bool
    {
        return $this->area_size < 1; // Less than 1 km²
    }

    /**
     * Check if the boundary is irregular.
     */
    public function isIrregular(): bool
    {
        return $this->boundary_type === 'natural' || $this->boundary_type === 'custom';
    }

    /**
     * Check if the boundary is regular.
     */
    public function isRegular(): bool
    {
        return $this->boundary_type === 'administrative' || $this->boundary_type === 'planned';
    }

    /**
     * Get the size category.
     */
    public function getSizeCategory(): string
    {
        if ($this->isLarge()) {
            return 'large';
        } elseif ($this->isMedium()) {
            return 'medium';
        } else {
            return 'small';
        }
    }

    /**
     * Get the size category label.
     */
    public function getSizeCategoryLabelAttribute(): string
    {
        $categories = [
            'large' => 'كبير',
            'medium' => 'متوسط',
            'small' => 'صغير',
        ];

        return $categories[$this->size_category] ?? 'غير معروف';
    }

    /**
     * Get the shape complexity score.
     */
    public function getShapeComplexityScore(): float
    {
        if (!$this->hasCoordinates()) {
            return 0;
        }

        $coordinateCount = count($this->coordinates);
        
        // More points = more complex shape
        if ($coordinateCount <= 4) {
            return 0.2; // Simple shape
        } elseif ($coordinateCount <= 8) {
            return 0.5; // Medium complexity
        } elseif ($coordinateCount <= 16) {
            return 0.8; // High complexity
        } else {
            return 1.0; // Very complex
        }
    }

    /**
     * Get the shape complexity label.
     */
    public function getShapeComplexityLabelAttribute(): string
    {
        $score = $this->shape_complexity_score;

        if ($score >= 0.8) {
            return 'معقد جداً';
        } elseif ($score >= 0.6) {
            return 'معقد';
        } elseif ($score >= 0.4) {
            return 'متوسط';
        } elseif ($score >= 0.2) {
            return 'بسيط';
        } else {
            return 'بسيط جداً';
        }
    }

    /**
     * Get the full title with neighborhood.
     */
    public function getFullTitleAttribute(): string
    {
        if ($this->neighborhood) {
            return $this->name . ' - ' . $this->neighborhood->name;
        }
        return $this->name;
    }

    /**
     * Get the search index.
     */
    public function getSearchIndex(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'boundary_type' => $this->boundary_type,
            'status' => $this->status,
            'area_size' => $this->area_size,
            'perimeter' => $this->perimeter,
            'dominant_land_use' => $this->dominant_land_use,
            'neighborhood' => $this->neighborhood?->name ?? '',
            'city' => $this->neighborhood?->city ?? '',
            'district' => $this->neighborhood?->district ?? '',
            'center_coordinates' => $this->center_coordinates,
            'bounds' => $this->bounds,
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
