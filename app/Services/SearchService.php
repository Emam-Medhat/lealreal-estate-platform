<?php

namespace App\Services;

use App\Models\Property;
use App\Models\SearchQuery;
use App\Models\SearchAnalytic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SearchService
{
    protected int $cacheTTL; // Cache Time To Live in seconds (e.g., 1 hour)

    public function __construct()
    {
        $this->cacheTTL = config('cache.ttl', 3600); // Default to 1 hour
    }

    /**
     * Performs a basic property search with caching.
     *
     * @param Request $request
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function basicSearch(Request $request, int $perPage = 12)
    {
        $searchQuery = $request->input('query');
        $cacheKey = 'basic_search_' . md5(json_encode($request->all()) . $perPage);

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($request, $searchQuery, $perPage) {
            $properties = Property::with(['media', 'propertyType', 'location', 'price'])
                ->where(function ($query) use ($searchQuery) {
                    $query->where('title', 'like', '%' . $searchQuery . '%')
                        ->orWhere('description', 'like', '%' . $searchQuery . '%')
                        ->orWhereHas('location', function ($q) use ($searchQuery) {
                            $q->where('address', 'like', '%' . $searchQuery . '%')
                                ->orWhere('city', 'like', '%' . $searchQuery . '%')
                                ->orWhere('state', 'like', '%' . $searchQuery . '%')
                                ->orWhere('zip_code', 'like', '%' . $searchQuery . '%');
                        });
                })
                ->when($request->input('property_type'), function ($query, $type) {
                    $query->whereHas('propertyType', function ($q) use ($type) {
                        $q->where('slug', $type);
                    });
                })
                ->when($request->input('min_price'), function ($query, $minPrice) {
                    $query->whereHas('price', function ($q) use ($minPrice) {
                        $q->where('price', '>=', $minPrice);
                    });
                })
                ->when($request->input('max_price'), function ($query, $maxPrice) {
                    $query->whereHas('price', function ($q) use ($maxPrice) {
                        $q->where('price', '<=', $maxPrice);
                    });
                })
                ->when($request->input('bedrooms'), function ($query, $bedrooms) {
                    $query->where('bedrooms', '>=', $bedrooms);
                })
                ->when($request->input('bathrooms'), function ($query, $bathrooms) {
                    $query->where('bathrooms', '>=', $bathrooms);
                })
                ->when($request->input('sort_by', 'latest') === 'latest', function ($query) {
                    $query->latest();
                })
                ->when($request->input('sort_by') === 'price_asc', function ($query) {
                    $query->orderBy(
                        Property::select('price')
                            ->from('property_prices')
                            ->whereColumn('property_prices.property_id', 'properties.id')
                            ->limit(1)
                    );
                })
                ->when($request->input('sort_by') === 'price_desc', function ($query) {
                    $query->orderByDesc(
                        Property::select('price')
                            ->from('property_prices')
                            ->whereColumn('property_prices.property_id', 'properties.id')
                            ->limit(1)
                    );
                })
                ->paginate($perPage);

            $this->recordSearchAnalytic($request, $properties->total(), 'basic');
            return $properties;
        });
    }

    /**
     * Performs an advanced property search with caching.
     *
     * @param Request $request
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function advancedSearch(Request $request, int $perPage = 12)
    {
        $cacheKey = 'advanced_search_' . md5(json_encode($request->all()) . $perPage);

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($request, $perPage) {
            $properties = Property::with(['media', 'propertyType', 'location', 'price'])
                ->when($request->input('keywords'), function ($query, $keywords) {
                    $query->where(function ($q) use ($keywords) {
                        $q->where('title', 'like', '%' . $keywords . '%')
                            ->orWhere('description', 'like', '%' . $keywords . '%');
                    });
                })
                ->when($request->input('property_type'), function ($query, $type) {
                    $query->whereHas('propertyType', function ($q) use ($type) {
                        $q->where('slug', $type);
                    });
                })
                ->when($request->input('min_price'), function ($query, $minPrice) {
                    $query->whereHas('price', function ($q) use ($minPrice) {
                        $q->where('price', '>=', $minPrice);
                    });
                })
                ->when($request->input('max_price'), function ($query, $maxPrice) {
                    $query->whereHas('price', function ($q) use ($maxPrice) {
                        $q->where('price', '<=', $maxPrice);
                    });
                })
                ->when($request->input('bedrooms'), function ($query, $bedrooms) {
                    $query->where('bedrooms', '>=', $bedrooms);
                })
                ->when($request->input('bathrooms'), function ($query, $bathrooms) {
                    $query->where('bathrooms', '>=', $bathrooms);
                })
                ->when($request->input('city'), function ($query, $city) {
                    $query->whereHas('location', function ($q) use ($city) {
                        $q->where('city', 'like', '%' . $city . '%');
                    });
                })
                ->when($request->input('state'), function ($query, $state) {
                    $query->whereHas('location', function ($q) use ($state) {
                        $q->where('state', 'like', '%' . $state . '%');
                    });
                })
                ->when($request->input('zip_code'), function ($query, $zipCode) {
                    $query->whereHas('location', function ($q) use ($zipCode) {
                        $q->where('zip_code', 'like', '%' . $zipCode . '%');
                    });
                })
                ->when($request->input('min_area'), function ($query, $minArea) {
                    $query->where('area', '>=', $minArea);
                })
                ->when($request->input('max_area'), function ($query, $maxArea) {
                    $query->where('area', '<=', $maxArea);
                })
                ->when($request->input('amenities'), function ($query, $amenities) {
                    $query->whereHas('amenities', function ($q) use ($amenities) {
                        $q->whereIn('amenities.id', $amenities);
                    });
                })
                ->when($request->input('sort_by', 'latest') === 'latest', function ($query) {
                    $query->latest();
                })
                ->when($request->input('sort_by') === 'price_asc', function ($query) {
                    $query->orderBy(
                        Property::select('price')
                            ->from('property_prices')
                            ->whereColumn('property_prices.property_id', 'properties.id')
                            ->limit(1)
                    );
                })
                ->when($request->input('sort_by') === 'price_desc', function ($query) {
                    $query->orderByDesc(
                        Property::select('price')
                            ->from('property_prices')
                            ->whereColumn('property_prices.property_id', 'properties.id')
                            ->limit(1)
                    );
                })
                ->paginate($perPage);

            $this->recordSearchAnalytic($request, $properties->total(), 'advanced');
            return $properties;
        });
    }

    /**
     * Records a search analytic.
     *
     * @param Request $request
     * @param int $resultCount
     * @return void
     */
    protected function recordSearchAnalytic(Request $request, int $resultCount, string $type): void
    {
        if (Auth::check()) {
            SearchQuery::create([
                'user_id' => Auth::id(),
                'query' => $request->all(),
                'type' => $type,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        SearchAnalytic::create([
            'user_id' => Auth::id(),
            'search_query' => $request->fullUrl(),
            'result_count' => $resultCount,
            'ip_address' => $request->ip(),
            'searched_at' => Carbon::now(),
        ]);
    }

    /**
     * Retrieves search suggestions based on the query.
     *
     * @param string $query
     * @return \Illuminate\Support\Collection
     */
    public function getSearchSuggestions(string $query): \Illuminate\Support\Collection
    {
        if (strlen($query) < 2) {
            return collect();
        }

        $cacheKey = 'search_suggestions_' . md5($query);

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($query) {
            $cities = DB::table('property_locations')
                ->select('city as name', DB::raw("'city' as type"), DB::raw('COUNT(*) as count'))
                ->where('city', 'like', '%' . $query . '%')
                ->groupBy('city')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();

            $states = DB::table('property_locations')
                ->select('state as name', DB::raw("'state' as type"), DB::raw('COUNT(*) as count'))
                ->where('state', 'like', '%' . $query . '%')
                ->groupBy('state')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();

            $propertyTypes = DB::table('property_types')
                ->select('name', DB::raw("'property_type' as type"), DB::raw('COUNT(*) as count'))
                ->where('name', 'like', '%' . $query . '%')
                ->groupBy('name')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();

            return $cities->merge($states)->merge($propertyTypes);
        });
    }

    /**
     * Performs a map-based property search with caching.
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function mapSearch(Request $request)
    {
        $cacheKey = 'map_search_' . md5(json_encode($request->all()));

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($request) {
            $bounds = $request->only(['ne_lat', 'ne_lng', 'sw_lat', 'sw_lng']);
            $filters = $request->only(['property_type', 'min_price', 'max_price', 'bedrooms', 'bathrooms']);

            $properties = Property::with(['location', 'price', 'media'])
                ->where('status', 'published')
                ->when($filters['property_type'] ?? null, function ($q, $type) {
                    $q->where('property_type_id', $type);
                })
                ->when($filters['min_price'] ?? null, function ($q, $minPrice) {
                    $q->whereHas('price', function ($priceQuery) use ($minPrice) {
                        $priceQuery->where('price', '>=', $minPrice);
                    });
                })
                ->when($filters['max_price'] ?? null, function ($q, $maxPrice) {
                    $q->whereHas('price', function ($priceQuery) use ($maxPrice) {
                        $priceQuery->where('price', '<=', $maxPrice);
                    });
                })
                ->when($filters['bedrooms'] ?? null, function ($q, $bedrooms) {
                    $q->where('bedrooms', '>=', $bedrooms);
                })
                ->when($filters['bathrooms'] ?? null, function ($q, $bathrooms) {
                    $q->where('bathrooms', '>=', $bathrooms);
                })
                ->when(isset($bounds['ne_lat']) && isset($bounds['ne_lng']) && isset($bounds['sw_lat']) && isset($bounds['sw_lng']), function ($q) use ($bounds) {
                    $q->whereHas('location', function ($locQuery) use ($bounds) {
                        $locQuery->whereBetween('latitude', [$bounds['sw_lat'], $bounds['ne_lat']])
                               ->whereBetween('longitude', [$bounds['sw_lng'], $bounds['ne_lng']]);
                    });
                })
                ->limit(100) // Limit for map performance
                ->get();

            $this->recordSearchAnalytic($request, $properties->count(), 'map');
            return $properties;
        });
    }

    /**
     * Retrieves search analytics for a given date range.
     *
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array
     */
    public function getSearchAnalytics(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $dateFrom = $dateFrom ? Carbon::parse($dateFrom) : now()->subDays(30);
        $dateTo = $dateTo ? Carbon::parse($dateTo) : now();

        return Cache::remember("search_analytics_" . md5($dateFrom->toDateString() . $dateTo->toDateString()), $this->cacheTTL, function () use ($dateFrom, $dateTo) {
            return [
                'total_searches' => SearchQuery::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
                'unique_users' => SearchQuery::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->distinct('user_id')
                    ->count('user_id'),
                'search_types' => SearchQuery::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->pluck('count', 'type'),
                'top_queries' => SearchQuery::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->selectRaw('JSON_EXTRACT(query, "$.q") as query, COUNT(*) as count')
                    ->whereRaw('JSON_EXTRACT(query, "$.q") IS NOT NULL')
                    ->groupBy('query')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
            ];
        });
    }

    /**
     * Invalidates all search-related caches.
     *
     * @return void
     */
    public function invalidateSearchCache(): void
    {
        // Invalidate all caches that start with 'basic_search_' or 'advanced_search_'
        // This might be too broad for a large application, consider more granular invalidation
        // if specific property updates should only invalidate relevant search results.
        foreach (Cache::getStore()->getKeys() as $key) {
            if (str_starts_with($key, 'basic_search_') || str_starts_with($key, 'advanced_search_') || str_starts_with($key, 'search_suggestions_') || str_starts_with($key, 'search_analytics_') || str_starts_with($key, 'map_search_')) {
                Cache::forget($key);
            }
        }
    }
}
