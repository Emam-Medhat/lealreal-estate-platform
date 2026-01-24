<?php

namespace App\Models\Neighborhood;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CommunityAmenity extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'neighborhood_id',
        'name',
        'description',
        'type',
        'status',
        'address',
        'latitude',
        'longitude',
        'phone',
        'email',
        'website',
        'opening_hours',
        'facilities',
        'services',
        'accessibility',
        'capacity',
        'area_size',
        'year_built',
        'last_renovated',
        'maintenance_info',
        'contact_info',
        'rules',
        'fees',
        'images',
        'main_image',
        'gallery',
        'verified',
        'featured',
        'tags',
        'metadata',
        'rating',
        'review_count',
        'visit_count',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'neighborhood_id' => 'integer',
        'latitude' => 'decimal:8,6',
        'longitude' => 'decimal:9,6',
        'opening_hours' => 'array',
        'facilities' => 'array',
        'services' => 'array',
        'accessibility' => 'array',
        'capacity' => 'integer',
        'area_size' => 'decimal:10,2',
        'year_built' => 'integer',
        'last_renovated' => 'integer',
        'maintenance_info' => 'array',
        'contact_info' => 'array',
        'rules' => 'array',
        'fees' => 'array',
        'images' => 'array',
        'main_image' => 'string',
        'gallery' => 'array',
        'verified' => 'boolean',
        'featured' => 'boolean',
        'tags' => 'array',
        'metadata' => 'array',
        'rating' => 'decimal:2,1',
        'review_count' => 'integer',
        'visit_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the neighborhood that owns the amenity.
     */
    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class, 'neighborhood_id');
    }

    /**
     * Scope a query to only include active amenities.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to filter by neighborhood.
     */
    public function scopeByNeighborhood(Builder $query, int $neighborhoodId): Builder
    {
        return $query->where('neighborhood_id', $neighborhoodId);
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to get verified amenities.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('verified', true);
    }

    /**
     * Scope a query to get featured amenities.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    /**
     * Scope a query to get amenities by rating range.
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
     * Scope a query to get amenities by capacity range.
     */
    public function scopeByCapacityRange(Builder $query, $min, $max = null): Builder
    {
        if ($min !== null) {
            $query->where('capacity', '>=', $min);
        }
        if ($max !== null) {
            $query->where('capacity', '<=', $max);
        }
        return $query;
    }

    /**
     * Scope a query to get amenities by area size range.
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
     * Scope a query to get amenities with specific facilities.
     */
    public function scopeWithFacility(Builder $query, string $facility): Builder
    {
        return $query->whereJsonContains('facilities', $facility);
    }

    /**
     * Scope a query to get amenities with specific services.
     */
    public function scopeWithService(Builder $query, string $service): Builder
    {
        return $query->whereJsonContains('services', $service);
    }

    /**
     * Scope a query to get amenities with accessibility features.
     */
    public function scopeWithAccessibility(Builder $query, string $feature): Builder
    {
        return $query->whereJsonContains('accessibility', $feature);
    }

    /**
     * Scope a query to get free amenities.
     */
    public function scopeFree(Builder $query): Builder
    {
        return $query->whereJsonContains('fees', ['admission' => 0]);
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'maintenance' => 'صيانة',
            'closed' => 'مغلق',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Get the type label.
     */
    public function getTypeLabelAttribute(): string
    {
        $types = [
            'park' => 'حديقة',
            'playground' => 'ملعب أطفال',
            'sports_facility' => 'منشأة رياضية',
            'community_center' => 'مركز مجتمعي',
            'library' => 'مكتبة',
            'health_center' => 'مركز صحي',
            'school' => 'مدرسة',
            'mosque' => 'مسجد',
            'shopping_center' => 'مركز تسوق',
            'restaurant' => 'مطعم',
            'cafe' => 'مقهى',
            'pharmacy' => 'صيدلية',
            'bank' => 'بنك',
            'gas_station' => 'محطة وقود',
            'public_transport' => 'مواصلات عامة',
            'parking' => 'موقف سيارات',
            'other' => 'أخرى',
        ];

        return $types[$this->type] ?? 'غير معروف';
    }

    /**
     * Get the rating label.
     */
    public function getRatingLabelAttribute(): string
    {
        return $this->rating . ' / 5';
    }

    /**
     * Get the visit count label.
     */
    public function getVisitCountLabelAttribute(): string
    {
        return number_format($this->visit_count) . ' زيارة';
    }

    /**
     * Get the review count label.
     */
    public function getReviewCountLabelAttribute(): string
    {
        return number_format($this->review_count) . ' تقييم';
    }

    /**
     * Get the capacity label.
     */
    public function getCapacityLabelAttribute(): string
    {
        if ($this->capacity === 0) {
            return 'غير محدد';
        }
        return number_format($this->capacity) . ' شخص';
    }

    /**
     * Get the area size label.
     */
    public function getAreaSizeLabelAttribute(): string
    {
        if ($this->area_size === 0) {
            return 'غير محدد';
        }
        return number_format($this->area_size, 2) . ' متر مربع';
    }

    /**
     * Get the year built label.
     */
    public function getYearBuiltLabelAttribute(): string
    {
        return $this->year_built ? $this->year_built : 'غير محدد';
    }

    /**
     * Get the last renovated label.
     */
    public function getLastRenovatedLabelAttribute(): string
    {
        return $this->last_renovated ? $this->last_renovated : 'غير محدد';
    }

    /**
     * Get the age of the amenity.
     */
    public function getAgeAttribute(): int
    {
        return $this->year_built ? date('Y') - $this->year_built : 0;
    }

    /**
     * Get the age label.
     */
    public function getAgeLabelAttribute(): string
    {
        $age = $this->age;
        
        if ($age === 0) {
            return 'غير محدد';
        } elseif ($age < 1) {
            return 'أقل من سنة';
        } elseif ($age < 5) {
            return $age . ' سنوات';
        } elseif ($age < 10) {
            return $age . ' سنوات';
        } else {
            return $age . ' سنة';
        }
    }

    /**
     * Get the opening hours for today.
     */
    public function getTodayHoursAttribute(): array
    {
        $today = now()->format('l');
        return $this->opening_hours[strtolower($today)] ?? [];
    }

    /**
     * Get the opening hours for today as formatted string.
     */
    public function getTodayHoursFormattedAttribute(): string
    {
        $hours = $this->today_hours;
        
        if (empty($hours)) {
            return 'غير محدد';
        }

        $open = $hours['open'] ?? '';
        $close = $hours['close'] ?? '';

        if (empty($open) && empty($close)) {
            return 'مغلق';
        }

        if ($open === '24/7') {
            return 'مفتوح على مدار 24 ساعة';
        }

        return $open . ' - ' . $close;
    }

    /**
     * Get the facilities list.
     */
    public function getFacilitiesListAttribute(): string
    {
        return implode(', ', $this->facilities ?? []);
    }

    /**
     * Get the services list.
     */
    public function getServicesListAttribute(): string
    {
        return implode(', ', $this->services ?? []);
    }

    /**
     * Get the accessibility features list.
     */
    public function getAccessibilityFeaturesAttribute(): array
    {
        return $this->accessibility ?? [];
    }

    /**
     * Get the accessibility features list as string.
     */
    public function getAccessibilityListAttribute(): string
    {
        return implode(', ', $this->accessibility_features);
    }

    /**
     * Get the rules list.
     */
    public function getRulesListAttribute(): string
    {
        return implode(', ', $this->rules ?? []);
    }

    /**
     * Get the fees info.
     */
    public function getFeesInfoAttribute(): array
    {
        return $this->fees ?? [];
    }

    /**
     * Get the admission fee.
     */
    public function getAdmissionFeeAttribute(): float
    {
        return $this->fees['admission'] ?? 0;
    }

    /**
     * Get the admission fee label.
     */
    public function getAdmissionFeeLabelAttribute(): string
    {
        $fee = $this->admission_fee;
        
        if ($fee === 0) {
            return 'مجاني';
        }
        
        return number_format($fee, 2) . ' ريال';
    }

    /**
     * Get the tags list.
     */
    public function getTagsListAttribute(): string
    {
        return implode(', ', $this->tags ?? []);
    }

    /**
     * Get the images list.
     */
    public function getImagesListAttribute(): array
    {
        return $this->images ?? [];
    }

    /**
     * Get the gallery list.
     */
    public function getGalleryListAttribute(): array
    {
        return $this->gallery ?? [];
    }

    /**
     * Get the metadata as JSON.
     */
    public function getMetadataAttribute(): string
    {
        return json_encode($this->metadata ?? []);
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
     * Get the contact info array.
     */
    public function getContactInfoArrayAttribute(): array
    {
        return [
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'manager' => $this->contact_info['manager'] ?? null,
            'phone' => $this->contact_info['phone'] ?? null,
            'email' => $this->contact_info['email'] ?? null,
        ];
    }

    /**
     * Check if the amenity is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the amenity is verified.
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * Check if the amenity is featured.
     */
    public function isFeatured(): bool
    {
        return $this->featured;
    }

    /**
     * Check if the amenity is highly rated.
     */
    public function isHighlyRated(): bool
    {
        return $this->rating >= 4.0;
    }

    /**
     * Check if the amenity has coordinates.
     */
    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Check if the amenity has opening hours.
     */
    public function hasOpeningHours(): bool
    {
        return !empty($this->opening_hours);
    }

    /**
     * Check if the amenity is open now.
     */
    public function isOpenNow(): bool
    {
        if (!$this->hasOpeningHours()) {
            return false;
        }

        $todayHours = $this->today_hours;
        
        if (empty($todayHours)) {
            return false;
        }

        $open = $todayHours['open'] ?? null;
        $close = $todayHours['close'] ?? null;

        if ($open === '24/7') {
            return true;
        }

        if (empty($open) || empty($close)) {
            return false;
        }

        $now = now()->format('H:i');
        return $now >= $open && $now <= $close;
    }

    /**
     * Check if the amenity has facilities.
     */
    public function hasFacilities(): bool
    {
        return !empty($this->facilities);
    }

    /**
     * Check if the amenity has services.
     */
    public function hasServices(): bool
    {
        return !empty($this->services);
    }

    /**
     * Check if the amenity has accessibility features.
     */
    public function hasAccessibility(): bool
    {
        return !empty($this->accessibility);
    }

    /**
     * Check if the amenity is wheelchair accessible.
     */
    public function isWheelchairAccessible(): bool
    {
        return ($this->accessibility['wheelchair'] ?? false);
    }

    /**
     * Check if the amenity has parking.
     */
    public function hasParking(): bool
    {
        return ($this->accessibility['parking'] ?? false);
    }

    /**
     * Check if the amenity has elevator.
     */
    public function hasElevator(): bool
    {
        return ($this->accessibility['elevator'] ?? false);
    }

    /**
     * Check if the amenity has ramp.
     */
    public function hasRamp(): bool
    {
        return ($this->accessibility['ramp'] ?? false);
    }

    /**
     * Check if the amenity has accessible toilet.
     */
    public function hasAccessibleToilet(): bool
    {
        return ($this->accessibility['toilet'] ?? false);
    }

    /**
     * Check if the amenity is free.
     */
    public function isFree(): bool
    {
        return $this->admission_fee === 0;
    }

    /**
     * Check if the amenity has capacity.
     */
    public function hasCapacity(): bool
    {
        return $this->capacity > 0;
    }

    /**
     * Check if the amenity is large.
     */
    public function isLarge(): bool
    {
        return $this->area_size >= 1000;
    }

    /**
     * Check if the amenity is medium sized.
     */
    public function isMedium(): bool
    {
        return $this->area_size >= 500 && $this->area_size < 1000;
    }

    /**
     * Check if the amenity is small.
     */
    public function isSmall(): bool
    {
        return $this->area_size < 500;
    }

    /**
     * Check if the amenity is newly built.
     */
    public function isNew(): bool
    {
        return $this->age <= 5;
    }

    /**
     * Check if the amenity is well-maintained.
     */
    public function isWellMaintained(): bool
    {
        return $this->last_renovated && ($this->age - (date('Y') - $this->last_renovated)) <= 5;
    }

    /**
     * Get the amenity status label.
     */
    public function getAmenityStatusAttribute(): string
    {
        if ($this->isOpenNow()) {
            return 'مفتوح الآن';
        } else {
            return 'مغلق الآن';
        }
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
     * Get the full address.
     */
    public function getFullAddressAttribute(): string
    {
        if ($this->neighborhood) {
            return $this->neighborhood->city . ', ' . $this->neighborhood->district . ', ' . $this->neighborhood->name . ', ' . $this->address;
        }
        return $this->address;
    }

    /**
     * Get the search index.
     */
    public function getSearchIndex(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'rating' => $this->rating,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'facilities' => $this->facilities,
            'services' => $this->services,
            'tags' => $this->tags,
            'neighborhood' => $this->neighborhood?->name ?? '',
            'city' => $this->neighborhood?->city ?? '',
            'district' => $this->neighborhood?->district ?? '',
        ];
    }

    /**
     * Increment visit count.
     */
    public function incrementVisitCount(): void
    {
        $this->increment('visit_count');
    }

    /**
     * Update rating.
     */
    public function updateRating(float $newRating): void
    {
        $totalRating = $this->rating * $this->review_count;
        $this->review_count++;
        $this->rating = ($totalRating + $newRating) / $this->review_count;
        $this->save();
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
