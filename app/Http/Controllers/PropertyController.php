<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\PropertyDetail;
use App\Models\PropertyLocation;
use App\Models\PropertyMedia;
use App\Models\PropertyPrice;
use App\Models\PropertyPriceHistory;
use App\Models\PropertyAnalytic;
use App\Models\PropertyStatusHistory;
use App\Models\PropertyAmenity;
use App\Models\PropertyFeature;
use App\Models\PropertyDocument;
use App\Models\PropertyView;
use App\Services\OptimizedPropertyService;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PropertyController extends BaseController
{
    protected $propertyService;
    protected $propertyRepository;

    public function __construct(
        OptimizedPropertyService $propertyService,
        PropertyRepositoryInterface $propertyRepository
    ) {
        $this->propertyService = $propertyService;
        $this->propertyRepository = $propertyRepository;
        $this->middleware('auth')->except(['index', 'show', 'search']);
    }

    /**
     * Display a listing of properties with optimized caching and filtering
     */
    public function index(Request $request)
    {
        $this->rateLimit($request, 100, 5);

        $filters = $request->only([
            'property_type',
            'listing_type',
            'min_price',
            'max_price',
            'city',
            'bedrooms',
            'featured',
            'premium',
            'sort',
            'order'
        ]);

        $perPage = $this->getPerPage($request, 12, 50);

        $properties = $this->getCachedData(
            'properties_index_' . md5(serialize($filters) . $perPage),
            function () use ($filters, $perPage) {
                return $this->propertyRepository->getFilteredProperties($filters, $perPage);
            },
            'short'
        );

        $propertyTypes = $this->getCachedData(
            'property_types_active',
            function () {
                return PropertyType::active()->ordered()->get(['id', 'name', 'description']);
            },
            'medium'
        );

        return view('properties.index', compact('properties', 'propertyTypes'));
    }

    /**
     * Display the specified property with optimized loading and view tracking
     */
    public function show(Request $request, $id)
    {
        $this->rateLimit($request, 100, 5);

        $property = $this->getCachedData(
            "property_show_{$id}",
            function () use ($id) {
                return $this->propertyRepository->findById($id, ['*'], [
                    'agent:id,name,phone,email',
                    'company:id,name,logo',
                    'media' => function ($query) {
                        return $query->orderBy('sort_order', 'asc')->get(['id', 'property_id', 'file_path', 'media_type', 'sort_order']);
                    },
                    'location:id,property_id,city,state,country,address,latitude,longitude',
                    'price:id,property_id,price,currency,price_per_sqm',
                    'details:id,property_id,bedrooms,bathrooms,area,area_unit,year_built,parking_spaces',
                    'amenities:id,name,icon',
                    'features:id,name,description',
                    'analytics' => function ($query) {
                        return $query->latest()->take(10);
                    }
                ]);
            },
            'medium'
        );

        if (!$property) {
            return $this->errorResponse('Property not found', 404);
        }

        // Increment view count asynchronously
        dispatch(function () use ($property) {
            $this->propertyService->incrementViewCount($property->id);
        });

        $similarProperties = $this->getCachedData(
            "similar_properties_{$id}",
            function () use ($property) {
                return $this->propertyService->getSimilarProperties($property->id, 6);
            },
            'medium'
        );

        return view('properties.show', compact('property', 'similarProperties'));
    }

    /**
     * Show the form for creating a new property with optimized data loading
     */
    public function create(Request $request)
    {
        $this->rateLimit($request, 50, 5);

        $propertyTypes = $this->getCachedData(
            'property_types_active',
            function () {
                return PropertyType::active()->ordered()->get(['id', 'name', 'description']);
            },
            'medium'
        );

        $amenities = $this->getCachedData(
            'property_amenities_active',
            function () {
                return PropertyAmenity::active()->ordered()->get(['id', 'name', 'icon', 'description']);
            },
            'medium'
        );

        $features = $this->getCachedData(
            'property_features_active',
            function () {
                return PropertyFeature::active()->ordered()->get(['id', 'name', 'description']);
            },
            'medium'
        );

        return view('properties.create', compact('propertyTypes', 'amenities', 'features'));
    }

    /**
     * Store a newly created property with validation, caching, and error handling
     */
    public function store(StorePropertyRequest $request)
    {
        $this->rateLimit($request, 30, 5);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $property = $this->propertyService->createProperty($request->validated(), $user);

            // Upload images if they exist
            if ($request->hasFile('images')) {
                $this->handleImageUpload($request->file('images'), $property->id);
            }

            // Handle documents upload
            if ($request->hasFile('documents')) {
                $this->handleDocumentUpload($request->file('documents'), $property->id);
            }

            DB::commit();

            // Notify all users about the new property
            try {
                $users = User::all();
                Notification::send($users, new PropertyCreated($property));
            } catch (\Exception $e) {
                Log::error('Failed to send property notifications: ' . $e->getMessage());
                // Continue execution as the property is already created
            }

            // Clear relevant caches
            $this->clearCache('properties_index');
            $this->clearCache('property_types_active');
            $this->clearCache('featured_properties');
            $this->clearCache('latest_properties');

            return redirect()->route('properties.show', $property)
                ->with('success', 'Property created successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Property creation failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create property: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified property
     */
    public function edit(Request $request, $id)
    {
        $this->rateLimit($request, 50, 5);

        $property = $this->propertyRepository->findById($id, ['*'], [
            'media',
            'location',
            'price',
            'details',
            'amenities',
            'features',
            'documents'
        ]);

        if (!$property) {
            return $this->errorResponse('Property not found', 404);
        }

        $propertyTypes = $this->getCachedData(
            'property_types_active',
            function () {
                return PropertyType::active()->ordered()->get(['id', 'name', 'icon', 'description']);
            },
            'medium'
        );

        $amenities = $this->getCachedData(
            'property_amenities_active',
            function () {
                return PropertyAmenity::active()->ordered()->get(['id', 'name', 'icon', 'description']);
            },
            'medium'
        );

        $features = $this->getCachedData(
            'property_features_active',
            function () {
                return PropertyFeature::active()->ordered()->get(['id', 'name', 'description']);
            },
            'medium'
        );

        return view('properties.edit', compact('property', 'propertyTypes', 'amenities', 'features'));
    }

    /**
     * Update the specified property with validation and cache management
     */
    public function update(UpdatePropertyRequest $request, $id)
    {
        $this->rateLimit($request, 50, 5);

        try {
            DB::beginTransaction();

            $property = $this->propertyService->updateProperty($id, $request->validated());

            if (!$property) {
                return $this->errorResponse('Property not found', 404);
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                $this->handleImageUpload($request->file('images'), $property->id);
            }

            // Handle new document uploads
            if ($request->hasFile('documents')) {
                $this->handleDocumentUpload($request->file('documents'), $property->id);
            }

            DB::commit();

            // Clear relevant caches
            $this->clearCache("property_show_{$id}");
            $this->clearCache('properties_index');
            $this->clearCache('featured_properties');
            $this->clearCache('latest_properties');

            return redirect()->route('properties.show', $property)
                ->with('success', 'Property updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Property update failed: ' . $e->getMessage(), [
                'property_id' => $id,
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update property: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified property with proper cleanup
     */
    public function destroy(Request $request, $id)
    {
        $this->rateLimit($request, 30, 5);

        try {
            $success = $this->propertyService->deleteProperty($id);

            if (!$success) {
                return $this->errorResponse('Property not found', 404);
            }

            // Clear relevant caches
            $this->clearCache("property_show_{$id}");
            $this->clearCache('properties_index');
            $this->clearCache('featured_properties');
            $this->clearCache('latest_properties');

            return redirect()->route('properties.index')
                ->with('success', 'Property deleted successfully');

        } catch (\Exception $e) {
            Log::error('Property deletion failed: ' . $e->getMessage(), [
                'property_id' => $id,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete property: ' . $e->getMessage());
        }
    }

    /**
     * Search properties with advanced filtering and caching
     */
    public function search(Request $request)
    {
        $this->rateLimit($request, 100, 5);

        $filters = $request->only([
            'query',
            'property_type',
            'listing_type',
            'min_price',
            'max_price',
            'city',
            'state',
            'bedrooms',
            'bathrooms',
            'area_min',
            'area_max',
            'featured',
            'premium',
            'sort',
            'order'
        ]);

        $perPage = $this->getPerPage($request, 12, 50);

        $properties = $this->getCachedData(
            'property_search_' . md5(serialize($filters) . $perPage),
            function () use ($filters, $perPage) {
                return $this->propertyRepository->searchProperties($filters['query'] ?? '', $filters, $perPage);
            },
            'short'
        );

        $propertyTypes = $this->getCachedData(
            'property_types_active',
            function () {
                return PropertyType::active()->ordered()->get(['id', 'name', 'icon']);
            },
            'medium'
        );

        return view('properties.search', compact('properties', 'propertyTypes', 'filters'));
    }

    /**
     * Get featured properties with caching
     */
    public function featured(Request $request)
    {
        $this->rateLimit($request, 100, 5);

        $limit = min($request->get('limit', 6), 20);

        $properties = $this->getCachedData(
            "featured_properties_{$limit}",
            function () use ($limit) {
                return $this->propertyRepository->getFeatured($limit);
            },
            'medium'
        );

        return $this->jsonResponse($properties, 'Featured properties retrieved successfully');
    }

    /**
     * Get latest properties with caching
     */
    public function latest(Request $request)
    {
        $this->rateLimit($request, 100, 5);

        $limit = min($request->get('limit', 6), 20);

        $properties = $this->getCachedData(
            "latest_properties_{$limit}",
            function () use ($limit) {
                return $this->propertyRepository->getLatest($limit);
            },
            'short'
        );

        return $this->jsonResponse($properties, 'Latest properties retrieved successfully');
    }

    /**
     * Toggle property featured status
     */
    public function toggleFeatured(Request $request, $id)
    {
        $this->rateLimit($request, 30, 5);

        try {
            $property = $this->propertyService->toggleFeatured($id);

            if (!$property) {
                return $this->errorResponse('Property not found', 404);
            }

            // Clear relevant caches
            $this->clearCache("property_show_{$id}");
            $this->clearCache('featured_properties');
            $this->clearCache('properties_index');

            return $this->jsonResponse($property, 'Property featured status updated successfully');

        } catch (\Exception $e) {
            Log::error('Toggle featured failed: ' . $e->getMessage(), [
                'property_id' => $id,
                'user_id' => auth()->id()
            ]);

            return $this->errorResponse('Failed to toggle featured status', 500);
        }
    }

    /**
     * Get property analytics
     */
    public function analytics(Request $request, $id)
    {
        $this->rateLimit($request, 60, 5);

        $analytics = $this->getCachedData(
            "property_analytics_{$id}",
            function () use ($id) {
                return $this->propertyService->getPropertyAnalytics($id);
            },
            'medium'
        );

        return $this->jsonResponse($analytics, 'Property analytics retrieved successfully');
    }

    /**
     * Export properties with memory-efficient processing
     */
    public function export(Request $request)
    {
        $this->rateLimit($request, 10, 5);

        $filters = $request->only([
            'property_type',
            'listing_type',
            'min_price',
            'max_price',
            'city',
            'featured',
            'premium'
        ]);
        $format = $request->get('format', 'csv');

        try {
            $job = new \App\Jobs\ExportPropertiesJob($filters, $format, auth()->id());
            dispatch($job);

            return $this->jsonResponse(['job_id' => $job->getJobId()], 'Export job started successfully');

        } catch (\Exception $e) {
            Log::error('Property export failed: ' . $e->getMessage(), [
                'filters' => $filters,
                'format' => $format,
                'user_id' => auth()->id()
            ]);

            return $this->errorResponse('Export failed', 500);
        }
    }

    /**
     * Handle image upload with optimization
     */
    private function handleImageUpload($images, $propertyId)
    {
        foreach ($images as $index => $image) {
            $path = $image->store('properties/' . $propertyId, 'public');

            PropertyMedia::create([
                'property_id' => $propertyId,
                'image_url' => $path,
                'image_type' => $image->getMimeType(),
                'sort_order' => $index,
                'is_primary' => $index === 0
            ]);
        }
    }

    /**
     * Handle document upload
     */
    private function handleDocumentUpload($documents, $propertyId)
    {
        foreach ($documents as $document) {
            $path = $document->store('properties/' . $propertyId . '/documents', 'public');

            PropertyDocument::create([
                'property_id' => $propertyId,
                'document_url' => $path,
                'document_name' => $document->getClientOriginalName(),
                'document_type' => $document->getMimeType(),
                'file_size' => $document->getSize()
            ]);
        }
    }
}
