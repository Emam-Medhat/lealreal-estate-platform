<?php

namespace App\Http\Controllers;

use App\Http\Requests\Search\BasicSearchRequest;
use App\Http\Requests\Search\AdvancedSearchRequest;
use App\Models\Property;
use App\Models\SearchQuery;
use App\Models\SavedSearch;
use App\Models\UserActivityLog;
use App\Models\SearchAnalytic;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index()
    {
        $featuredProperties = Property::where('featured', true)
            ->where('status', 'published')
            ->with(['location', 'price', 'media'])
            ->latest()
            ->limit(6)
            ->get();

        $recentSearches = Auth::check() 
            ? SearchQuery::where('user_id', Auth::id())
                ->latest()
                ->limit(5)
                ->get()
            : collect([]);

        return view('search.index', compact('featuredProperties', 'recentSearches'));
    }

    public function basicSearch(BasicSearchRequest $request)
    {
        $query = $request->input('q');
        $filters = $request->only(['property_type', 'status', 'min_price', 'max_price', 'location']);

        $properties = Property::with(['location', 'price', 'media'])
            ->where('status', 'published')
            ->when($query, function ($q, $query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('title', 'like', "%{$query}%")
                           ->orWhere('description', 'like', "%{$query}%")
                           ->orWhereHas('location', function ($locQuery) use ($query) {
                               $locQuery->where('address', 'like', "%{$query}%")
                                       ->orWhere('city', 'like', "%{$query}%")
                                       ->orWhere('state', 'like', "%{$query}%");
                           });
                });
            })
            ->when($filters['property_type'] ?? null, function ($q, $type) {
                $q->where('property_type_id', $type);
            })
            ->when($filters['location'] ?? null, function ($q, $location) {
                $q->whereHas('location', function ($locQuery) use ($location) {
                    $locQuery->where('city', 'like', "%{$location}%")
                            ->orWhere('state', 'like', "%{$location}%");
                });
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
            ->latest()
            ->paginate(20);

        // Log search query
        $this->logSearchQuery($request, 'basic');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'properties' => $properties,
                'filters' => $filters,
                'query' => $query
            ]);
        }

        return view('search.results', compact('properties', 'query', 'filters'));
    }

    public function advancedSearch(AdvancedSearchRequest $request)
    {
        $filters = $request->validated();
        
        $properties = Property::with(['location', 'price', 'media', 'amenities', 'features'])
            ->where('status', 'published')
            ->when($filters['keyword'] ?? null, function ($q, $keyword) {
                $q->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('title', 'like', "%{$keyword}%")
                           ->orWhere('description', 'like', "%{$keyword}%");
                });
            })
            ->when($filters['property_types'] ?? null, function ($q, $types) {
                $q->whereIn('property_type_id', $types);
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
            ->when($filters['min_bedrooms'] ?? null, function ($q, $minBedrooms) {
                $q->where('bedrooms', '>=', $minBedrooms);
            })
            ->when($filters['max_bedrooms'] ?? null, function ($q, $maxBedrooms) {
                $q->where('bedrooms', '<=', $maxBedrooms);
            })
            ->when($filters['min_bathrooms'] ?? null, function ($q, $minBathrooms) {
                $q->where('bathrooms', '>=', $minBathrooms);
            })
            ->when($filters['max_bathrooms'] ?? null, function ($q, $maxBathrooms) {
                $q->where('bathrooms', '<=', $maxBathrooms);
            })
            ->when($filters['min_area'] ?? null, function ($q, $minArea) {
                $q->where('area', '>=', $minArea);
            })
            ->when($filters['max_area'] ?? null, function ($q, $maxArea) {
                $q->where('area', '<=', $maxArea);
            })
            ->when($filters['cities'] ?? null, function ($q, $cities) {
                $q->whereHas('location', function ($locQuery) use ($cities) {
                    $locQuery->whereIn('city', $cities);
                });
            })
            ->when($filters['states'] ?? null, function ($q, $states) {
                $q->whereHas('location', function ($locQuery) use ($states) {
                    $locQuery->whereIn('state', $states);
                });
            })
            ->when($filters['amenities'] ?? null, function ($q, $amenities) {
                $q->whereHas('amenities', function ($amenQuery) use ($amenities) {
                    $amenQuery->whereIn('amenities.id', $amenities);
                });
            })
            ->when($filters['features'] ?? null, function ($q, $features) {
                $q->whereHas('features', function ($featureQuery) use ($features) {
                    $featureQuery->whereIn('features.id', $features);
                });
            })
            ->when($filters['year_built_min'] ?? null, function ($q, $yearBuiltMin) {
                $q->where('year_built', '>=', $yearBuiltMin);
            })
            ->when($filters['year_built_max'] ?? null, function ($q, $yearBuiltMax) {
                $q->where('year_built', '<=', $yearBuiltMax);
            })
            ->when($filters['sort_by'] ?? 'latest', function ($q, $sortBy) {
                switch ($sortBy) {
                    case 'price_low':
                        $q->join('property_prices', 'properties.id', '=', 'property_prices.property_id')
                          ->orderBy('property_prices.price', 'asc');
                        break;
                    case 'price_high':
                        $q->join('property_prices', 'properties.id', '=', 'property_prices.property_id')
                          ->orderBy('property_prices.price', 'desc');
                        break;
                    case 'area_low':
                        $q->orderBy('area', 'asc');
                        break;
                    case 'area_high':
                        $q->orderBy('area', 'desc');
                        break;
                    case 'newest':
                        $q->orderBy('created_at', 'desc');
                        break;
                    case 'oldest':
                        $q->orderBy('created_at', 'asc');
                        break;
                    default:
                        $q->latest();
                }
            })
            ->paginate(20);

        // Log search query
        $this->logSearchQuery($request, 'advanced');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'properties' => $properties,
                'filters' => $filters,
                'total_results' => $properties->total()
            ]);
        }

        return view('search.advanced-results', compact('properties', 'filters'));
    }

    public function mapSearch(Request $request)
    {
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

        // Log search query
        $this->logSearchQuery($request, 'map');

        return response()->json([
            'success' => true,
            'properties' => $properties,
            'total_results' => $properties->count()
        ]);
    }

    public function suggestions(Request $request)
    {
        $query = $request->input('q');
        
        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $cities = DB::table('property_locations')
            ->select('city as name', 'city as type', DB::raw('COUNT(*) as count'))
            ->where('city', 'like', "%{$query}%")
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        $states = DB::table('property_locations')
            ->select('state as name', 'state as type', DB::raw('COUNT(*) as count'))
            ->where('state', 'like', "%{$query}%")
            ->groupBy('state')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        $propertyTypes = DB::table('property_types')
            ->select('name', 'type', DB::raw('COUNT(properties.id) as count'))
            ->leftJoin('properties', 'property_types.id', '=', 'properties.property_type_id')
            ->where('property_types.name', 'like', "%{$query}%")
            ->groupBy('property_types.id', 'property_types.name')
            ->orderBy('count', 'desc')
            ->limit(3)
            ->get();

        $suggestions = $cities->merge($states)->merge($propertyTypes);

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    public function saveSearch(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'query' => 'required|array',
            'frequency' => 'nullable|in:daily,weekly,monthly',
        ]);

        $savedSearch = SavedSearch::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'query' => $request->query,
            'frequency' => $request->frequency ?? 'weekly',
            'is_active' => true,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'saved_search',
            'details' => "Saved search: {$request->name}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'saved_search' => $savedSearch,
            'message' => 'Search saved successfully'
        ]);
    }

    public function getSavedSearches()
    {
        $savedSearches = Auth::check()
            ? SavedSearch::where('user_id', Auth::id())
                ->where('is_active', true)
                ->latest()
                ->get()
            : collect([]);

        return response()->json([
            'success' => true,
            'saved_searches' => $savedSearches
        ]);
    }

    public function deleteSavedSearch(SavedSearch $savedSearch)
    {
        if ($savedSearch->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $savedSearch->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_saved_search',
            'details' => "Deleted saved search: {$savedSearch->name}",
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Saved search deleted successfully'
        ]);
    }

    private function logSearchhandQuery(Request $request, string $type)
    {
        if (!Auth::check()) {
            return;
        }

        SearchQuery::create([
            'user_id' => Auth::id(),
            'query' => $request->all(),
            'type' => $type,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Update search analytics
        SearchAnalytic::create([
            'search_type' => $type,
            'query_params' => $request->all(),
            'results_count' => 0, // Will be updated after search execution
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
        ]);
    }

    public function getSearchAnalytics(Request $request): JsonResponse
    {
        $this->authorize('viewAnalytics', SearchQuery::class);
        
        $dateFrom = $request->date_from ?? now()->subDays(30);
        $dateTo = $request->date_to ?? now();

        $analytics = [
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

        return response()->json([
            'success' => true,
            'analytics' => $analytics
        ]);
    }
}
