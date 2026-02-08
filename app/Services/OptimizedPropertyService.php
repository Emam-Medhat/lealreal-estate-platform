<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyType;
use App\Models\PropertyLocation;
use App\Models\PropertyPrice;
use App\Models\PropertyMedia;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OptimizedPropertyService
{
    /**
     * Get properties with caching and optimization
     */
    public function getProperties(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $cacheKey = $this->generatePropertiesCacheKey($filters, $perPage);

        return Cache::remember($cacheKey, 300, function () use ($filters, $perPage) {
            return $this->buildPropertiesQuery($filters)->paginate($perPage);
        });
    }

    /**
     * Get property details with caching
     */
    public function getPropertyDetails(int $propertyId): ?Property
    {
        $cacheKey = "property_details_{$propertyId}";

        return Cache::remember($cacheKey, 1800, function () use ($propertyId) {
            return Property::with([
                'propertyType:id,name,slug',
                'location',
                'details',
                'price',
                'media' => function ($query) {
                    $query->where('media_type', 'image')->orderBy('sort_order');
                },
                'amenities:id,name,icon',
                'features:id,name,icon'
            ])->find($propertyId);
        });
    }

    /**
     * Search properties with optimized indexing
     */
    public function searchProperties(string $searchTerm, array $filters = []): Collection
    {
        $cacheKey = 'search_' . md5($searchTerm . serialize($filters));

        return Cache::remember($cacheKey, 600, function () use ($searchTerm, $filters) {
            return Property::select([
                'id',
                'title',
                'slug',
                'description',
                'listing_type',
                'featured',
                'premium',
                'views_count',
                'created_at'
            ])->with([
                        'propertyType:id,name,slug',
                        'location:id,city,country,address',
                        'price:property_id,price,currency',
                        'media' => function ($query) {
                            $query->select('id', 'property_id', 'file_path', 'media_type')
                                ->where('media_type', 'image')
                                ->limit(1);
                        }
                    ])
                ->where(function ($query) use ($searchTerm) {
                    $query->where('title', 'like', '%' . $searchTerm . '%')
                        ->orWhere('description', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('location', function ($q) use ($searchTerm) {
                            $q->where('city', 'like', '%' . $searchTerm . '%')
                                ->orWhere('country', 'like', '%' . $searchTerm . '%');
                        });
                })
                ->where('status', 'active')
                ->orderBy('featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
        });
    }

    /**
     * Get similar properties efficiently
     */
    public function getSimilarProperties(int $propertyId, int $limit = 6): Collection
    {
        $property = Property::select('property_type')->find($propertyId);

        if (!$property) {
            return new Collection();
        }

        $cacheKey = "similar_properties_{$propertyId}_{$limit}";

        return Cache::remember($cacheKey, 900, function () use ($property, $limit, $propertyId) {
            return Property::select(['id', 'title', 'slug', 'listing_type'])
                ->with([
                    'price:property_id,price,currency',
                    'media' => function ($query) {
                        $query->select('id', 'property_id', 'file_path', 'media_type')
                            ->where('media_type', 'image')
                            ->limit(1);
                    }
                ])
                ->where('property_type', $property->property_type)
                ->where('id', '!=', $propertyId)
                ->where('status', 'active')
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get featured properties with caching
     */
    public function getFeaturedProperties(int $limit = 12): Collection
    {
        return Cache::remember('featured_properties', 3600, function () use ($limit) {
            return Property::select([
                'id',
                'title',
                'slug',
                'description',
                'listing_type',
                'featured',
                'premium',
                'views_count',
                'created_at'
            ])->with([
                        'propertyType:id,name,slug',
                        'location:id,city,country',
                        'price:property_id,price,currency',
                        'media' => function ($query) {
                            $query->select('id', 'property_id', 'file_path', 'media_type')
                                ->where('media_type', 'image')
                                ->limit(1);
                        }
                    ])
                ->where('featured', true)
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get property statistics with caching
     */
    public function getPropertyStats(): array
    {
        return Cache::remember('property_stats', 3600, function () {
            return [
                'total_properties' => Property::count(),
                'active_properties' => Property::where('status', 'active')->count(),
                'featured_properties' => Property::where('featured', true)->count(),
                'premium_properties' => Property::where('premium', true)->count(),
                'total_views' => Property::sum('views_count'),
                'average_price' => DB::table('property_prices')
                    ->join('properties', 'properties.id', '=', 'property_prices.property_id')
                    ->where('properties.status', 'active')
                    ->avg('property_prices.price'),
            ];
        });
    }

    /**
     * Get property analytics
     */
    public function getPropertyAnalytics(int $propertyId): array
    {
        return Cache::remember("property_analytics_{$propertyId}", 1800, function () use ($propertyId) {
            $property = Property::findOrFail($propertyId);

            return [
                'views_count' => $property->views_count,
                'favorites_count' => $property->favorites_count,
                'inquiries_count' => $property->inquiries_count,
                'status' => $property->status,
                'featured' => $property->featured,
                'price_history' => $property->priceHistory()->latest()->take(10)->get(),
                'status_history' => $property->statusHistory()->latest()->take(10)->get(),
            ];
        });
    }

    /**
     * Increment view count asynchronously
     */
    public function incrementViewCount(int $propertyId): void
    {
        Queue::push(function () use ($propertyId) {
            Property::where('id', $propertyId)->increment('views_count');

            // Clear cache for this property
            Cache::forget("property_details_{$propertyId}");
        });
    }

    /**
     * Create property with optimized operations
     */
    public function createProperty(array $data, User $user): Property
    {
        return DB::transaction(function () use ($data, $user) {
            $property = Property::create([
                'agent_id' => $user->id, // Assuming current user is the agent
                'property_type' => $data['property_type_id'] ?? null, // Correct column name from model definition
                'title' => $data['title'],
                'description' => $data['description'],
                'listing_type' => $data['listing_type'],
                'status' => $data['status'] ?? 'active',
                'featured' => $data['featured'] ?? false,
                'premium' => $data['premium'] ?? false,
                'property_code' => $this->generatePropertyCode(),
                'views_count' => 0,
                'favorites_count' => 0,
                'inquiries_count' => 0,
                'price' => $data['price'],
                'currency' => $data['currency'] ?? 'SAR',
                'address' => $data['address'] ?? '', // Fallback to empty string if missing
                'city' => $data['city'] ?? '',       // Fallback to empty string if missing
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? '',     // Fallback to empty string if missing
                'postal_code' => $data['postal_code'] ?? null, // Mapped directly to properties table
                'latitude' => $data['latitude'] ?? null,      // Mapped directly to properties table
                'longitude' => $data['longitude'] ?? null,    // Mapped directly to properties table
                'bedrooms' => $data['bedrooms'] ?? 0,
                'bathrooms' => $data['bathrooms'] ?? 0,
                'floors' => $data['floors'] ?? 0,
                'year_built' => $data['year_built'] ?? null,
                'area' => $data['area'] ?? 0, // Ensure area is not null
                'area_unit' => $data['area_unit'] ?? 'sq_m',
                'virtual_tour_url' => $data['virtual_tour_url'] ?? null,
                'slug' => Str::slug($data['title']) . '-' . Str::random(5),
            ]);

            // Create related records
            $property->details()->create([
                'bedrooms' => $data['bedrooms'] ?? null,
                'bathrooms' => $data['bathrooms'] ?? null,
                'floors' => $data['floors'] ?? null,
                'parking_spaces' => $data['parking_spaces'] ?? null,
                'year_built' => $data['year_built'] ?? null,
                'area' => $data['area'] ?? 0,
                'area_unit' => $data['area_unit'] ?? 'sq_m',
                'land_area' => $data['land_area'] ?? null,
                'land_area_unit' => $data['land_area_unit'] ?? 'sq_m',
                'specifications' => $data['specifications'] ?? null,
                'materials' => $data['materials'] ?? null,
                'interior_features' => $data['interior_features'] ?? null,
                'exterior_features' => $data['exterior_features'] ?? null,
            ]);

            $property->location()->create([
                'address' => $data['address'] ?? '',
                'city' => $data['city'] ?? '',
                'state' => $data['state'] ?? '',
                'country' => $data['country'] ?? '',
                'postal_code' => $data['postal_code'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'neighborhood' => $data['neighborhood'] ?? null,
                'district' => $data['district'] ?? null,
                'coordinates' => $data['coordinates'] ?? null,
                'nearby_landmarks' => $data['nearby_landmarks'] ?? null,
                'transportation' => $data['transportation'] ?? null,
            ]);

            $property->price()->create([
                'price' => $data['price'],
                'currency' => $data['currency'] ?? 'SAR',
                'price_type' => $data['listing_type'],
                'price_per_sqm' => (isset($data['area']) && $data['area'] > 0) ? $data['price'] / $data['area'] : null,
                'is_negotiable' => $data['is_negotiable'] ?? false,
                'includes_vat' => $data['includes_vat'] ?? false,
                'vat_rate' => $data['vat_rate'] ?? 0,
                'service_charges' => $data['service_charges'] ?? 0,
                'maintenance_fees' => $data['maintenance_fees'] ?? 0,
                'payment_frequency' => $data['payment_frequency'] ?? null,
                'payment_terms' => $data['payment_terms'] ?? null,
                'effective_date' => now()->toDateString(),
                'is_active' => true,
            ]);

            if (isset($data['amenities'])) {
                $property->amenities()->sync($data['amenities']);
            }

            if (isset($data['features'])) {
                $property->features()->sync($data['features']);
            }

            // Clear relevant caches
            $this->clearPropertyCaches();

            return $property;
        });
    }

    private function generatePropertyCode(): string
    {
        do {
            $code = 'PROP-' . strtoupper(Str::random(8));
        } while (Property::where('property_code', $code)->exists());

        return $code;
    }

    /**
     * Update property with cache clearing
     */
    public function updateProperty(Property $property, array $data): Property
    {
        return DB::transaction(function () use ($property, $data) {
            $oldStatus = $property->status;

            // Update property
            $property->update($data);

            // Update related records
            if ($property->details) {
                $property->details->update($data);
            } else {
                $property->details()->create($data);
            }

            if ($property->location) {
                $property->location->update($data);
            } else {
                $property->location()->create($data);
            }

            if (isset($data['price'])) {
                $propertyPrice = $property->price;
                if ($propertyPrice) {
                    $oldPrice = $propertyPrice->price;
                    $newPrice = $data['price'];

                    $propertyPrice->update([
                        'price' => $newPrice,
                        'currency' => $data['currency'] ?? $propertyPrice->currency,
                        'price_type' => $data['listing_type'] ?? $propertyPrice->price_type,
                        'price_per_sqm' => (isset($data['area']) && $data['area'] > 0) ? $newPrice / $data['area'] : null,
                        'is_negotiable' => $data['is_negotiable'] ?? $propertyPrice->is_negotiable,
                        'includes_vat' => $data['includes_vat'] ?? $propertyPrice->includes_vat,
                        'vat_rate' => $data['vat_rate'] ?? $propertyPrice->vat_rate,
                        'service_charges' => $data['service_charges'] ?? $propertyPrice->service_charges,
                        'maintenance_fees' => $data['maintenance_fees'] ?? $propertyPrice->maintenance_fees,
                        'payment_frequency' => $data['payment_frequency'] ?? $propertyPrice->payment_frequency,
                        'payment_terms' => $data['payment_terms'] ?? $propertyPrice->payment_terms,
                    ]);

                    // Record price history
                    if ($oldPrice != $newPrice) {
                        \App\Models\PropertyPriceHistory::create([
                            'property_id' => $property->id,
                            'old_price' => $oldPrice,
                            'new_price' => $newPrice,
                            'currency' => $data['currency'] ?? $propertyPrice->currency,
                            'change_type' => $newPrice > $oldPrice ? 'increase' : 'decrease',
                            'change_percentage' => abs(($newPrice - $oldPrice) / $oldPrice * 100),
                            'changed_by' => auth()->id(),
                        ]);
                    }
                } else {
                    $property->price()->create($data);
                }
            }

            if (isset($data['amenities'])) {
                $property->amenities()->sync($data['amenities']);
            }

            if (isset($data['features'])) {
                $property->features()->sync($data['features']);
            }

            // Record status change
            if ($oldStatus != ($data['status'] ?? $oldStatus)) {
                \App\Models\PropertyStatusHistory::recordStatusChange(
                    $property->id,
                    $oldStatus,
                    $data['status'],
                    $data['status_change_reason'] ?? null,
                    auth()->id()
                );
            }

            // Clear caches
            Cache::forget("property_details_{$property->id}");
            $this->clearPropertyCaches();

            return $property;
        });
    }

    /**
     * Toggle property featured status
     */
    public function toggleFeatured(int $propertyId): Property
    {
        $property = Property::findOrFail($propertyId);
        $property->featured = !$property->featured;

        if ($property->featured) {
            $property->featured_at = now();
        }

        $property->save();
        $this->clearPropertyCaches();
        Cache::forget("property_details_{$propertyId}");
        Cache::forget("property_analytics_{$propertyId}");

        return $property;
    }

    /**
     * Delete property and clean up
     */
    public function deleteProperty(int $propertyId): bool
    {
        $property = Property::find($propertyId);
        if (!$property)
            return false;

        return DB::transaction(function () use ($property) {
            // Delete related records
            $property->location()?->delete();
            $property->price()?->delete();
            $property->details()?->delete();
            $property->media()?->delete();

            // Delete property
            $property->delete();

            // Clear caches
            Cache::forget("property_details_{$property->id}");
            Cache::forget("property_analytics_{$property->id}");
            $this->clearPropertyCaches();

            return true;
        });
    }

    /**
     * Build properties query with eager loading and filtering
     */
    protected function buildPropertiesQuery(array $filters): \Illuminate\Database\Eloquent\Builder
    {
        $query = Property::query()->with([
            'propertyType:id,name,slug',
            'location:id,property_id,city,country,address',
            'price:id,property_id,price,currency',
            'media' => function ($query) {
                $query->select('id', 'property_id', 'file_path', 'media_type')
                    ->where('media_type', 'image')
                    ->orderBy('sort_order');
            },
            'agent:id,name,avatar'
        ]);

        // Apply status filter (default to active)
        $query->where('status', $filters['status'] ?? 'active');

        // Apply basic filters
        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['q'] . '%');
            });
        }

        if (!empty($filters['property_type'])) {
            $query->whereHas('propertyType', function ($q) use ($filters) {
                $q->where('slug', $filters['property_type']);
            });
        }

        if (!empty($filters['listing_type'])) {
            $query->where('listing_type', $filters['listing_type']);
        }

        if (!empty($filters['max_price'])) {
            $query->whereHas('price', function ($q) use ($filters) {
                $q->where('price', '<=', $filters['max_price']);
            });
        }

        if (!empty($filters['min_price'])) {
            $query->whereHas('price', function ($q) use ($filters) {
                $q->where('price', '>=', $filters['min_price']);
            });
        }

        if (!empty($filters['bedrooms'])) {
            $query->whereHas('details', function ($q) use ($filters) {
                $q->where('bedrooms', '>=', $filters['bedrooms']);
            });
        }

        if (!empty($filters['featured'])) {
            $query->where('featured', true);
        }

        if (!empty($filters['premium'])) {
            $query->where('premium', true);
        }

        // Apply sorting
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_high':
                $query->whereHas('price', function ($q) {
                    $q->orderBy('price', 'desc');
                });
                break;
            case 'price_low':
                $query->whereHas('price', function ($q) {
                    $q->orderBy('price', 'asc');
                });
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'featured':
                $query->orderBy('featured', 'desc')->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    /**
     * Generate cache key for properties based on filters
     */
    protected function generatePropertiesCacheKey(array $filters, int $perPage): string
    {
        return 'properties_list_' . md5(serialize($filters) . $perPage . request('page', 1));
    }

    /**
     * Clear property-related caches
     */
    public function clearPropertyCaches(): void
    {
        Cache::forget('property_stats');
        Cache::forget('featured_properties');
        Cache::forget('property_types_active');
        // Since we can't easily clear dynamic keys like 'properties_list_*' without tags,
        // we rely on TTL or we could implement a versioning strategy.
        // For now, these main static keys are important.
    }


    /**
     * Get property types from cache
     */
    public function getPropertyTypes(): Collection
    {
        return Cache::remember('property_types_active', 3600, function () {
            return PropertyType::select('id', 'name', 'slug')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Bulk operations for better performance
     */
    public function bulkUpdateStatus(array $propertyIds, string $status): int
    {
        $updated = Property::whereIn('id', $propertyIds)->update(['status' => $status]);

        if ($updated > 0) {
            $this->clearPropertyCaches();
        }

        return $updated;
    }

    /**
     * Get properties by coordinates for map view
     */
    public function getPropertiesForMap(array $bounds, array $filters = []): Collection
    {
        $cacheKey = 'map_properties_' . md5(serialize($bounds) . serialize($filters));

        return Cache::remember($cacheKey, 600, function () use ($bounds, $filters) {
            return Property::select([
                'id',
                'title',
                'slug',
                'listing_type',
                'featured',
                'premium'
            ])
                ->with([
                    'location:id,property_id,latitude,longitude,city,address',
                    'price:property_id,price,currency',
                    'media' => function ($query) {
                        $query->select('id', 'property_id', 'file_path', 'media_type')
                            ->where('media_type', 'image')
                            ->limit(1);
                    }
                ])
                ->whereHas('location', function ($query) use ($bounds) {
                    $query->whereBetween('latitude', [$bounds['south'], $bounds['north']])
                        ->whereBetween('longitude', [$bounds['west'], $bounds['east']]);
                })
                ->where('status', 'active')
                ->get();
        });
    }
}
