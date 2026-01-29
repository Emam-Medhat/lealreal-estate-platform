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
use App\Services\SearchService;

class SearchController extends Controller
{
    protected SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

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
        $properties = $this->searchService->basicSearch($request, 20);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'properties' => $properties,
                'filters' => $request->only(['property_type', 'status', 'min_price', 'max_price', 'location']),
                'query' => $request->input('q')
            ]);
        }

        return view('search.results', compact('properties'));
    }

    public function advancedSearch(AdvancedSearchRequest $request)
    {
        $properties = $this->searchService->advancedSearch($request, 20);
        $filters = $request->validated();

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
        $properties = $this->searchService->mapSearch($request);

        return response()->json([
            'success' => true,
            'properties' => $properties,
            'total_results' => $properties->count()
        ]);
    }

    public function suggestions(Request $request)
    {
        $query = $request->input('q');
        $suggestions = $this->searchService->getSearchSuggestions($query);

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

    public function getSearchAnalytics(Request $request): JsonResponse
    {
        $this->authorize('viewAnalytics', SearchQuery::class);
        
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $analytics = $this->searchService->getSearchAnalytics($dateFrom, $dateTo);

        return response()->json([
            'success' => true,
            'analytics' => $analytics
        ]);
    }
}
