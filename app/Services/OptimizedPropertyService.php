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
                'media' => function($query) {
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
                'id', 'title', 'slug', 'description', 'listing_type', 
                'featured', 'premium', 'views_count', 'created_at'
            ])->with([
                'propertyType:id,name,slug',
                'location:id,city,country,address',
                'price:property_id,price,currency',
                'media' => function($query) {
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
                    'media' => function($query) {
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
                'id', 'title', 'slug', 'description', 'listing_type', 
                'featured', 'premium', 'views_count', 'created_at'
            ])->with([
                'propertyType:id,name,slug',
                'location:id,city,country',
                'price:property_id,price,currency',
                'media' => function($query) {
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
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'listing_type' => $data['listing_type'],
                'property_type' => $data['property_type_id'],
                'agent_id' => $user->id,
                'status' => $data['status'] ?? 'draft',
                'featured' => $data['featured'] ?? false,
                'premium' => $data['premium'] ?? false,
                'views_count' => 0,
                'slug' => Str::slug($data['title']) . '-' . Str::random(5),
                'property_code' => 'PROP-' . strtoupper(Str::random(8)),
                'price' => $data['price'] ?? 0,
                'currency' => $data['currency'] ?? 'SAR',
                'address' => $data['location']['address'] ?? null,
                'city' => $data['location']['city'] ?? null,
                'country' => $data['location']['country'] ?? null,
                'latitude' => $data['location']['latitude'] ?? null,
                'longitude' => $data['location']['longitude'] ?? null,
                'bedrooms' => $data['details']['bedrooms'] ?? null,
                'bathrooms' => $data['details']['bathrooms'] ?? null,
                'area' => $data['details']['area'] ?? null,
            ]);

            // Create related records
            if (isset($data['location'])) {
                $property->location()->create($data['location']);
            }

            if (isset($data['price'])) {
                $priceData = is_array($data['price']) ? $data['price'] : [
                    'price' => $data['price'],
                    'currency' => $data['currency'] ?? 'SAR',
                    'price_type' => $data['listing_type'],
                    'effective_date' => now()->toDateString(),
                    'is_active' => true,
                    'set_by' => $user->id
                ];
                $property->price()->create($priceData);
            }

            if (isset($data['details'])) {
                $property->details()->create($data['details']);
            }

            // Clear relevant caches
            $this->clearPropertyCaches();

            return $property;
        });
    }

    /**
     * Update property with cache clearing
     */
    public function updateProperty(Property $property, array $data): Property
    {
        return DB::transaction(function () use ($property, $data) {
            $property->update($data);

            // Update related records for legacy support
            if (isset($data['address']) || isset($data['city']) || isset($data['country'])) {
                $property->location()->updateOrCreate([], [
                    'address' => $data['address'] ?? $property->address,
                    'city' => $data['city'] ?? $property->city,
                    'state' => $data['state'] ?? $property->state,
                    'country' => $data['country'] ?? $property->country,
                    'postal_code' => $data['postal_code'] ?? $property->postal_code,
                    'latitude' => $data['latitude'] ?? $property->latitude,
                    'longitude' => $data['longitude'] ?? $property->longitude,
                ]);
            }

            if (isset($data['price'])) {
                $property->price()->updateOrCreate([], [
                    'price' => $data['price'],
                    'currency' => $data['currency'] ?? $property->currency,
                    'price_type' => $data['listing_type'] ?? $property->listing_type,
                ]);
            }

            if (isset($data['bedrooms']) || isset($data['bathrooms']) || isset($data['area'])) {
                $property->details()->updateOrCreate([], [
                    'bedrooms' => $data['bedrooms'] ?? $property->bedrooms,
                    'bathrooms' => $data['bathrooms'] ?? $property->bathrooms,
                    'floors' => $data['floors'] ?? $property->floors,
                    'parking_spaces' => $data['parking_spaces'] ?? $property->parking_spaces,
                    'year_built' => $data['year_built'] ?? $property->year_built,
                    'area' => $data['area'] ?? $property->area,
                    'area_unit' => $data['area_unit'] ?? $property->area_unit,
                ]);
            }

            // Clear caches
            Cache::forget("property_details_{$property->id}");
            $this->clearPropertyCaches();

            return $property;
        });
    }

    /**
     * Delete property and clean up
     */
    public function deleteProperty(Property $property): bool
    {
        DB::transaction(function () use ($property) {
            // Delete related records
            $property->location()?->delete();
            $property->price()?->delete();
            $property->details()?->delete();
            $property->media()?->delete();
            
            // Delete property
            $property->delete();

            // Clear caches
            Cache::forget("property_details_{$property->id}");
            $this->clearPropertyCaches();
        });

        return true;
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
            'media' => function($query) {
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
        // In a real app, you might use cache tags if supported by driver
        // For simplicity, we can use a versioning system or just clear broad keys
        Cache::forget('property_stats');
        // More specific clearing logic here
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
                'id', 'title', 'slug', 'listing_type', 'featured', 'premium'
            ])
            ->with([
                'location:id,property_id,latitude,longitude,city,address',
                'price:property_id,price,currency',
                'media' => function($query) {
                    $query->select('id', 'property_id', 'file_path', 'media_type')
                          ->where('media_type', 'image')
                          ->limit(1);
                }
            ])
            ->whereHas('location', function($query) use ($bounds) {
                $query->whereBetween('latitude', [$bounds['south'], $bounds['north']])
                      ->whereBetween('longitude', [$bounds['west'], $bounds['east']]);
            })
            ->where('status', 'active')
            ->get();
        });
    }
}
