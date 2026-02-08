<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PropertyService;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropertyApiController extends ApiController
{
    protected $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
        $this->middleware('auth:api');
    }

    /**
     * Get paginated properties with filtering
     */
    public function index(Request $request): JsonResponse
    {
        $this->rateLimit($request, 100, 5);

        $filters = $request->only(['search', 'type', 'status', 'city', 'state', 'min_price', 'max_price', 'featured']);
        $perPage = min($request->get('per_page', 20), 100);

        $properties = $this->getCachedData(
            'properties:' . md5(serialize($filters) . $perPage),
            function () use ($filters, $perPage) {
                return $this->propertyService->getProperties($filters, $perPage);
            },
            'short'
        );

        return $this->paginatedResponse($properties, 'Properties retrieved successfully');
    }

    /**
     * Get a specific property
     */
    public function show(Property $property): JsonResponse
    {
        $this->rateLimit(request(), 200, 5);

        $propertyData = $this->getCachedData(
            "property:{$property->id}",
            function () use ($property) {
                return $property->load([
                    'agent:id,name,email',
                    'images',
                    'amenities',
                    'features',
                    'nearbyFacilities'
                ]);
            },
            'medium'
        );

        return $this->apiResponse($propertyData, 'Property retrieved successfully');
    }

    /**
     * Create a new property
     */
    public function store(Request $request): JsonResponse
    {
        $this->rateLimit($request, 30, 5);

        $validated = $this->validateApiRequest($request, [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:apartment,villa,house,land,commercial',
            'price' => 'required|numeric|min:0',
            'area' => 'required|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'featured' => 'boolean',
            'status' => 'sometimes|in:draft,published,sold,rented',
            'amenities' => 'nullable|array',
            'features' => 'nullable|array',
            'nearby_facilities' => 'nullable|array',
        ]);

        $property = $this->propertyService->createProperty($validated, auth()->user());
        
        // Clear relevant caches
        $this->clearApiCache('properties');
        $this->clearApiCache('dashboard');

        return $this->apiResponse($property, 'Property created successfully', 201);
    }

    /**
     * Update a property
     */
    public function update(Request $request, Property $property): JsonResponse
    {
        $this->rateLimit($request, 50, 5);

        $validated = $this->validateApiRequest($request, [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'type' => 'sometimes|in:apartment,villa,house,land,commercial',
            'price' => 'sometimes|numeric|min:0',
            'area' => 'sometimes|numeric|min:0',
            'bedrooms' => 'sometimes|integer|min:0',
            'bathrooms' => 'sometimes|integer|min:0',
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:100',
            'country' => 'sometimes|string|max:100',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'featured' => 'sometimes|boolean',
            'status' => 'sometimes|in:draft,published,sold,rented',
            'amenities' => 'sometimes|array',
            'features' => 'sometimes|array',
            'nearby_facilities' => 'sometimes|array',
        ]);

        $this->propertyService->updateProperty($property, $validated);
        
        // Clear relevant caches
        $this->clearApiCache('properties');
        $this->clearApiCache("property:{$property->id}");

        return $this->apiResponse($property->fresh(), 'Property updated successfully');
    }

    /**
     * Delete a property
     */
    public function destroy(Property $property): JsonResponse
    {
        $this->rateLimit(request(), 30, 5);

        $this->propertyService->deleteProperty($property);
        
        // Clear relevant caches
        $this->clearApiCache('properties');
        $this->clearApiCache("property:{$property->id}");

        return $this->apiResponse(null, 'Property deleted successfully');
    }

    /**
     * Search properties
     */
    public function search(Request $request): JsonResponse
    {
        $this->rateLimit($request, 100, 5);

        $query = $request->get('q', '');
        $filters = $request->only(['type', 'status', 'city', 'min_price', 'max_price']);
        $limit = min($request->get('limit', 50), 100);

        if (empty($query)) {
            return $this->errorResponse('Search query is required', 400);
        }

        $properties = $this->getCachedData(
            'search:' . md5($query . serialize($filters) . $limit),
            function () use ($query, $filters, $limit) {
                return $this->propertyService->searchProperties($query, $filters, $limit);
            },
            'short'
        );

        return $this->apiResponse($properties, 'Search results retrieved successfully');
    }

    /**
     * Get featured properties
     */
    public function featured(Request $request): JsonResponse
    {
        $this->rateLimit($request, 200, 5);

        $limit = min($request->get('limit', 10), 50);

        $properties = $this->getCachedData(
            "featured_properties:{$limit}",
            function () use ($limit) {
                return $this->propertyService->getFeaturedProperties($limit);
            },
            'short'
        );

        return $this->apiResponse($properties, 'Featured properties retrieved successfully');
    }

    /**
     * Get properties by location
     */
    public function byLocation(Request $request): JsonResponse
    {
        $this->rateLimit($request, 100, 5);

        $city = $request->get('city');
        $state = $request->get('state');
        $limit = min($request->get('limit', 20), 50);

        if (empty($city)) {
            return $this->errorResponse('City is required', 400);
        }

        $properties = $this->getCachedData(
            "location:{$city}:{$state}:{$limit}",
            function () use ($city, $state, $limit) {
                return $this->propertyService->getPropertiesByLocation($city, $state, $limit);
            },
            'medium'
        );

        return $this->apiResponse($properties, 'Properties by location retrieved successfully');
    }

    /**
     * Get dashboard statistics
     */
    public function dashboard(): JsonResponse
    {
        $this->rateLimit(request(), 60, 5);

        $stats = $this->getCachedData(
            'property_dashboard_stats',
            function () {
                return $this->propertyService->getPropertyStats();
            },
            'short'
        );

        return $this->apiResponse($stats, 'Dashboard statistics retrieved successfully');
    }

    /**
     * Get property recommendations
     */
    public function recommendations(Request $request): JsonResponse
    {
        $this->rateLimit($request, 60, 5);

        $limit = min($request->get('limit', 10), 20);

        $properties = $this->getCachedData(
            'recommendations:' . auth()->id() . ":{$limit}",
            function () use ($limit) {
                return $this->propertyService->getPropertyRecommendations(auth()->user(), $limit);
            },
            'medium'
        );

        return $this->apiResponse($properties, 'Property recommendations retrieved successfully');
    }

    /**
     * Get property performance metrics
     */
    public function performance(): JsonResponse
    {
        $this->rateLimit(request(), 30, 10);

        $metrics = $this->getCachedData(
            'property_performance_metrics',
            function () {
                return $this->propertyService->getPropertyPerformanceMetrics();
            },
            'long'
        );

        return $this->apiResponse($metrics, 'Performance metrics retrieved successfully');
    }

    /**
     * Bulk update properties
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $this->rateLimit($request, 10, 5); // Very low rate for bulk operations

        $validated = $this->validateApiRequest($request, [
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:properties,id',
            'data' => 'required|array',
            'data.status' => 'sometimes|in:draft,published,sold,rented',
            'data.featured' => 'sometimes|boolean',
            'data.price' => 'sometimes|numeric|min:0',
        ]);

        $updated = $this->propertyService->bulkUpdateProperties($validated['ids'], $validated['data']);
        
        // Clear relevant caches
        $this->clearApiCache('properties');
        $this->clearApiCache('dashboard');

        return $this->apiResponse(['updated_count' => $updated], 'Properties updated successfully');
    }

    /**
     * Export properties
     */
    public function export(Request $request): JsonResponse
    {
        $this->rateLimit($request, 5, 10); // Very low rate for exports

        $filters = $request->only(['status', 'type', 'city', 'min_price', 'max_price']);
        $format = $request->get('format', 'csv');

        if (!in_array($format, ['csv', 'excel', 'json'])) {
            return $this->errorResponse('Invalid format. Use csv, excel, or json', 400);
        }

        // Generate export job
        $job = new \App\Jobs\ExportPropertiesJob($filters, $format, auth()->id());
        dispatch($job);

        return $this->apiResponse(
            ['job_id' => $job->getJobId()],
            'Export job started successfully',
            202
        );
    }

    /**
     * Toggle property featured status
     */
    public function toggleFeatured(Property $property): JsonResponse
    {
        $this->rateLimit(request(), 50, 5);

        $property->update([
            'featured' => !$property->featured,
            'featured_at' => $property->featured ? null : now(),
        ]);

        // Clear relevant caches
        $this->clearApiCache('properties');
        $this->clearApiCache("property:{$property->id}");
        $this->clearApiCache('featured_properties');

        return $this->apiResponse($property, 'Property featured status updated successfully');
    }

    /**
     * Get property images
     */
    public function images(Property $property): JsonResponse
    {
        $this->rateLimit(request(), 100, 5);

        $images = $this->getCachedData(
            "property_images:{$property->id}",
            function () use ($property) {
                return $property->images()->orderBy('sort_order')->get();
            },
            'medium'
        );

        return $this->apiResponse($images, 'Property images retrieved successfully');
    }

    /**
     * Get property analytics
     */
    public function analytics(Property $property): JsonResponse
    {
        $this->rateLimit(request(), 30, 5);

        $analytics = $this->getCachedData(
            "property_analytics:{$property->id}",
            function () use ($property) {
                return [
                    'views_count' => $property->views_count ?? 0,
                    'inquiries_count' => $property->inquiries_count ?? 0,
                    'views_this_week' => $this->getViewsThisWeek($property),
                    'inquiries_this_week' => $this->getInquiriesThisWeek($property),
                    'conversion_rate' => $this->getConversionRate($property),
                    'last_viewed_at' => $property->last_viewed_at,
                    'created_at' => $property->created_at,
                    'updated_at' => $property->updated_at,
                ];
            },
            'medium'
        );

        return $this->apiResponse($analytics, 'Property analytics retrieved successfully');
    }

    /**
     * Get views this week for a property
     */
    private function getViewsThisWeek(Property $property): int
    {
        return $property->views()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
    }

    /**
     * Get inquiries this week for a property
     */
    private function getInquiriesThisWeek(Property $property): int
    {
        return $property->inquiries()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
    }

    /**
     * Get conversion rate for a property
     */
    private function getConversionRate(Property $property): float
    {
        $views = $property->views_count ?? 0;
        $inquiries = $property->inquiries_count ?? 0;

        return $views > 0 ? ($inquiries / $views) * 100 : 0;
    }
}
