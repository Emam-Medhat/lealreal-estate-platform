<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agent_id',
        'title',
        'slug',
        'property_code',
        'description',
        'description',
        'property_type',
        'listing_type',
        'status',
        'bedrooms',
        'bathrooms',
        'area',
        'area_unit',
        'floors',
        'year_built',
        'parking_spaces',
        'land_area',
        'land_area_unit',
        'featured',
        'premium',
        'views_count',
        'favorites_count',
        'inquiries_count',
        'specifications',
        'materials',
        'interior_features',
        'exterior_features',
        'nearby_places',
        'schools',
        'hospitals',
        'shopping_centers',
        'restaurants',
        'public_transport',
        'ownership_type',
        'deed_number',
        'registration_number',
        'zoning',
        'building_permit',
        'occupancy_permit',
        'energy_rating',
        'solar_panels',
        'water_heating',
        'insulation',
        'double_glazing',
        'air_conditioning',
        'virtual_tour_url',
        'price',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'area' => 'decimal:2',
        'land_area' => 'decimal:2',
        'featured' => 'boolean',
        'premium' => 'boolean',
        'solar_panels' => 'boolean',
        'double_glazing' => 'boolean',
        'views_count' => 'integer',
        'favorites_count' => 'integer',
        'inquiries_count' => 'integer',
        'specifications' => 'array',
        'materials' => 'array',
        'interior_features' => 'array',
        'exterior_features' => 'array',
        'nearby_places' => 'array',
        'schools' => 'array',
        'hospitals' => 'array',
        'shopping_centers' => 'array',
        'restaurants' => 'array',
        'public_transport' => 'array',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(PropertyMedia::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyMedia::class)->where('media_type', 'image');
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'property_type', 'id');
    }

    public function location(): HasOne
    {
        return $this->hasOne(PropertyLocation::class);
    }

    public function details(): HasOne
    {
        return $this->hasOne(PropertyDetail::class);
    }

    public function pricing(): HasOne
    {
        return $this->hasOne(PropertyPrice::class);
    }

    public function propertyAmenities(): BelongsToMany
    {
        return $this->belongsToMany(PropertyAmenity::class, 'property_amenity_property');
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(PropertyFeature::class, 'property_feature_property');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PropertyDocument::class);
    }

    public function floorPlans(): HasMany
    {
        return $this->hasMany(PropertyFloorPlan::class);
    }

    public function virtualTours(): HasMany
    {
        return $this->hasMany(PropertyVirtualTour::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopePremium($query)
    {
        return $query->where('premium', true);
    }

    public function scopeForSale($query)
    {
        return $query->where('listing_type', 'sale');
    }

    public function scopeForRent($query)
    {
        return $query->where('listing_type', 'rent');
    }

    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByPropertyType($query, $type)
    {
        return $query->where('property_type', $type);
    }

    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    public function scopeAreaRange($query, $min, $max)
    {
        return $query->whereBetween('area', [$min, $max]);
    }

    // Helper Methods
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    public function getFormattedAreaAttribute(): string
    {
        return number_format($this->area, 2) . ' ' . $this->area_unit;
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->country,
            $this->postal_code
        ]);
        return implode(', ', $parts);
    }

    public function getMainImageAttribute(): ?string
    {
        $mainImage = $this->images()->where('is_primary', true)->first();
        if ($mainImage) {
            return asset('storage/properties/' . $mainImage->file_path);
        }

        $firstImage = $this->images()->first();
        if ($firstImage) {
            return asset('storage/properties/' . $firstImage->file_path);
        }

        return asset('images/default-property.jpg');
    }

    public function incrementViews(): void
    {
        try {
            $this->increment('views_count');
        } catch (\Exception $e) {
            // Fallback: update directly if increment fails
            $this->views_count = ($this->views_count ?? 0) + 1;
            $this->save();
        }
    }

    public function incrementInquiries(): void
    {
        $this->increment('inquiries_count');
    }

    public function incrementFavorites(): void
    {
        $this->increment('favorites_count');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'active';
    }

    public function getDaysOnMarketAttribute(): int
    {
        if (!$this->created_at) {
            return 0;
        }

        return $this->created_at->diffInDays(now());
    }
}
