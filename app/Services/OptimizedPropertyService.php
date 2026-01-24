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
        $property = Property::select('property_type_id')->find($propertyId);
        
        if (!$property) {
            return new Collection();
        }

        $cacheKey = "similar_properties_{$propertyId}_{$limit}";
        
        return Cache::remember($cacheKey, 900, function () use ($property, $limit) {
            return Property::select(['id', 'title', 'slug', 'listing_type'])
                ->with([
                    'price:property_id,price,currency',
                    'media' => function($query) {
                        $query->select('id', 'property_id', 'file_path', 'media_type')
                              ->where('media_type', 'image')
                              ->limit(1);
                    }
                ])
                ->where('property_type_id', $property->property_type_id)
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
                'property_type_id' => $data['property_type_id'],
                'agent_id' => $user->id,
                'status' => 'draft',
                'featured' => false,
                'premium' => false,
                'views_count' => 0,
                'slug' => Str::slug($data['title']),
            ]);

            // Create related records
            if (isset($data['location'])) {
                $property->location()->create($data['location']);
            }

            if (isset($data['price'])) {
                $property->price()->create($data['price']);
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
        $property->update($data);

        // Clear caches
        Cache::forget("property_details_{$property->id}");
        $this->clearPropertyCaches();

        return $property;
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
     * Build optimized properties query
     */
    private function buildPropertiesQuery(array $filters)
    {
        $query = Property::select([
            'id', 'title', 'slug', 'description', 'listing_type', 
            'featured', 'premium', 'views_count', 'created_at'
        ])->with([
            'propertyType:id,name,slug',
            'location:id,city,country,address',
            'price:property_id,price,currency',
            'media' => function($query) {
                $query->select('id', 'property_id', 'file_path', 'media_type')
                      ->where('media_type', 'image')
                      ->limit(3);
            }
        ]);

        // Apply filters
        $this->applyQueryFilters($query, $filters);

        return $query;
    }

    /**
     * Apply filters to query
     */
    private function applyQueryFilters($query, array $filters): void
    {
        foreach ($filters as $key => $value) {
            if (empty($value)) continue;

            switch ($key) {
                case 'property_type':
                    $query->whereHas('propertyType', function($q) use ($value) {
                        $q->where('slug', $value);
                    });
                    break;
                case 'listing_type':
                    $query->where('listing_type', $value);
                    break;
                case 'min_price':
                case 'max_price':
                    $query->whereHas('price', function($q) use ($key, $value) {
                        $operator = $key === 'min_price' ? '>=' : '<=';
                        $q->where('price', $operator, $value);
                    });
                    break;
                case 'city':
                    $query->whereHas('location', function($q) use ($value) {
                        $q->where('city', 'like', '%' . $value . '%');
                    });
                    break;
                case 'bedrooms':
                    $query->whereHas('details', function($q) use ($value) {
                        $q->where('bedrooms', '>=', $value);
                    });
                    break;
                case 'featured':
                    $query->where('featured', true);
                    break;
                case 'premium':
                    $query->where('premium', true);
                    break;
            }
        }
    }

    /**
     * Generate cache key for properties
     */
    private function generatePropertiesCacheKey(array $filters, int $perPage): string
    {
        return 'properties_' . md5(serialize($filters) . $perPage);
    }

    /**
     * Clear property-related caches
     */
    private function clearPropertyCaches(): void
    {
        Cache::tags(['properties'])->flush();
        Cache::forget('property_stats');
        Cache::forget('featured_properties');
        Cache::forget('property_types_active');
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
