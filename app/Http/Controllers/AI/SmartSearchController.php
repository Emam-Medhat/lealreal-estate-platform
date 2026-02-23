<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\SmartSearchService;
use App\Services\AI\FilterTransformer;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Smart Search Controller
 *
 * POST /ai/search  → parse NL text → apply filters → return paginated results
 * GET  /ai/search  → render search UI
 */
class SmartSearchController extends Controller
{
    public function __construct(
        private readonly SmartSearchService $searchService,
        private readonly FilterTransformer $transformer
    ) {
    }

    // ─── View ──────────────────────────────────────────────────────────────

    public function index()
    {
        return view('ai.smart-search');
    }

    // ─── API ───────────────────────────────────────────────────────────────

    /**
     * POST /api/ai/search
     *
     * { "text": "شقة ٣ غرف في القاهرة الجديدة بسعر متوسط" }
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'text' => ['required', 'string', 'min:3', 'max:1000'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

        // 1. Parse free text to filters
        $parsed = $this->searchService->parse($request->input('text'));
        $rawFilters = $parsed['filters'];

        // 2. Transform to Eloquent-compatible params
        $queryParams = $this->transformer->transform($rawFilters);

        // 3. Build and execute property query
        $query = $this->buildQuery($queryParams);

        $perPage = $request->input('per_page', 12);
        $properties = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'parsed' => $parsed['raw'],
            'cached' => $parsed['cached'],
            'total' => $properties->total(),
            'data' => $properties->items(),
            'current_page' => $properties->currentPage(),
            'last_page' => $properties->lastPage(),
        ]);
    }

    // ─── Query Builder ─────────────────────────────────────────────────────

    private function buildQuery(array $params): \Illuminate\Database\Eloquent\Builder
    {
        $query = Property::with([
            'propertyType',
            'location',
            'details',
            'price',
            'media' => fn($q) => $q->where('media_type', 'image')->limit(1),
        ])->where('status', 'active');

        if (!empty($params['property_type'])) {
            $query->whereHas(
                'propertyType',
                fn($q) =>
                $q->where('slug', $params['property_type'])
                    ->orWhere('name_ar', $params['property_type'])
            );
        }

        if (!empty($params['city'])) {
            $query->whereHas(
                'location',
                fn($q) =>
                $q->where('city', 'like', '%' . $params['city'] . '%')
            );
        }

        if (!empty($params['district'])) {
            $query->whereHas(
                'location',
                fn($q) =>
                $q->where('district', 'like', '%' . $params['district'] . '%')
            );
        }

        if (!empty($params['compound'])) {
            $query->where('compound_name', 'like', '%' . $params['compound'] . '%');
        }

        if (!empty($params['bedrooms'])) {
            $query->whereHas(
                'details',
                fn($q) =>
                $q->where('bedrooms', '>=', $params['bedrooms'])
            );
        }

        if (!empty($params['bathrooms'])) {
            $query->whereHas(
                'details',
                fn($q) =>
                $q->where('bathrooms', '>=', $params['bathrooms'])
            );
        }

        if (!empty($params['min_price'])) {
            $query->whereHas(
                'price',
                fn($q) =>
                $q->where('price', '>=', $params['min_price'])
            );
        }

        if (!empty($params['max_price'])) {
            $query->whereHas(
                'price',
                fn($q) =>
                $q->where('price', '<=', $params['max_price'])
            );
        }

        if (isset($params['furnished'])) {
            $query->whereHas(
                'details',
                fn($q) =>
                $q->where('furnished', $params['furnished'])
            );
        }

        if (!empty($params['listing_type'])) {
            $query->where('listing_type', $params['listing_type']);
        }

        if (!empty($params['special_features'])) {
            foreach ($params['special_features'] as $feature) {
                $query->whereHas(
                    'amenities',
                    fn($q) =>
                    $q->where('name', 'like', '%' . $feature . '%')
                );
            }
        }

        return $query->orderByDesc('featured')->orderByDesc('created_at');
    }
}
