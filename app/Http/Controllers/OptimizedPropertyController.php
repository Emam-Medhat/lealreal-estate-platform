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
use App\Models\PropertyAmenity;
use App\Models\PropertyFeature;
use App\Models\PropertyDocument;
use App\Models\PropertyVirtualTour;
use App\Models\PropertyFloorPlan;
use App\Models\PropertyNeighborhood;
use App\Services\OptimizedPropertyService;
use App\Jobs\ProcessPropertyView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class OptimizedPropertyController extends Controller
{
    protected $propertyService;

    public function __construct(OptimizedPropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
        $this->middleware('auth')->except(['index', 'show', 'search']);
    }

    /**
     * Display a listing of the resource with caching and optimization.
     */
    public function index(Request $request)
    {
        // Generate cache key based on filters
        $cacheKey = $this->generateCacheKey($request);

        // Try to get from cache first
        $cachedData = Cache::remember($cacheKey, 300, function () use ($request) {
            return $this->getPropertiesData($request);
        });

        return view('properties.index', $cachedData);
    }

    /**
     * Generate cache key based on request parameters
     */
    private function generateCacheKey(Request $request): string
    {
        $params = $request->only([
            'property_type',
            'listing_type',
            'min_price',
            'max_price',
            'city',
            'bedrooms',
            'featured',
            'premium',
            'sort',
            'order',
            'page'
        ]);

        return 'properties_' . md5(serialize($params));
    }

    /**
     * Get properties data with optimized queries
     */
    private function getPropertiesData(Request $request): array
    {
        // Use eager loading with specific columns only
        $query = Property::select([
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
                    'pricing:property_id,price,currency',
                    'media' => function ($query) {
                        $query->select('id', 'property_id', 'file_path', 'media_type')
                            ->where('media_type', 'image')
                            ->limit(3);
                    }
                ]);

        // Apply filters efficiently
        $this->applyFilters($query, $request);

        // Apply sorting efficiently
        $this->applySorting($query, $request);

        // Get properties with pagination
        $properties = $query->paginate(12);

        // Get property types from cache
        $propertyTypes = Cache::remember('property_types_active', 3600, function () {
            return PropertyType::select('id', 'name', 'slug')
                ->where('is_active', true)
                ->orWhere('is_active', 1) // Handle integer values
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });

        return [
            'properties' => $properties,
            'propertyTypes' => $propertyTypes
        ];
    }

    /**
     * Apply filters to the query
     */
    private function applyFilters($query, Request $request): void
    {
        // Property type filter
        if ($request->property_type) {
            $query->whereHas('propertyType', function ($q) use ($request) {
                $q->where('slug', $request->property_type);
            });
        }

        // Listing type filter
        if ($request->listing_type) {
            $query->where('listing_type', $request->listing_type);
        }

        // Price filters - use joins instead of whereHas for better performance
        if ($request->min_price || $request->max_price) {
            $query->join('property_prices', 'properties.id', '=', 'property_prices.property_id');

            if ($request->min_price) {
                $query->where('property_prices.price', '>=', $request->min_price);
            }

            if ($request->max_price) {
                $query->where('property_prices.price', '<=', $request->max_price);
            }
        }

        // City filter
        if ($request->city) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('city', 'like', '%' . $request->city . '%');
            });
        }

        // Bedrooms filter
        if ($request->bedrooms) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('bedrooms', '>=', $request->bedrooms);
            });
        }

        // Featured and premium filters
        if ($request->featured) {
            $query->where('featured', true);
        }

        if ($request->premium) {
            $query->where('premium', true);
        }
    }

    /**
     * Apply sorting to the query
     */
    private function applySorting($query, Request $request): void
    {
        $sort = $request->sort ?? 'created_at';

        switch ($sort) {
            case 'price_low':
                if (!$request->min_price && !$request->max_price) {
                    $query->leftJoin('property_prices', 'properties.id', '=', 'property_prices.property_id')
                        ->orderByRaw('property_prices.price ASC NULLS LAST');
                }
                break;
            case 'price_high':
                if (!$request->min_price && !$request->max_price) {
                    $query->leftJoin('property_prices', 'properties.id', '=', 'property_prices.property_id')
                        ->orderByRaw('property_prices.price DESC NULLS LAST');
                }
                break;
            case 'area':
                $query->join('property_details', 'properties.id', '=', 'property_details.property_id')
                    ->orderBy('property_details.area', 'desc');
                break;
            case 'views':
                $query->orderBy('views_count', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
    }

    /**
     * Show the form for creating a new property.
     */
    public function create()
    {
        $propertyTypes = $this->propertyService->getPropertyTypes();
        $amenities = PropertyAmenity::where('is_active', true)->orderBy('name')->get();
        $features = PropertyFeature::where('is_active', true)->orderBy('name')->get();

        return view('properties.create', compact('propertyTypes', 'amenities', 'features'));
    }

    /**
     * Store a newly created property in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'listing_type' => 'required|in:sale,rent',
            'property_type' => 'required|string|in:apartment,villa,house,land,commercial',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'area' => 'nullable|integer|min:0',
            'area_unit' => 'nullable|string',
            'land_area' => 'nullable|integer|min:0',
            'land_area_unit' => 'nullable|string',
            'featured' => 'boolean',
                'premium' => 'boolean',
                'images.*' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
                'room_images.living_room.*' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
                'room_images.kitchen.*' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
                'room_images.bedrooms.*' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
                'room_images.bathrooms.*' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
                'room_images.entrance.*' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
                'room_images.outdoor.*' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
                'room_images.garage.*' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
                'room_images.amenities.*' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
            ]);

            DB::beginTransaction();

            $property = Property::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'listing_type' => $validated['listing_type'],
                'property_type' => $validated['property_type'],
                'agent_id' => Auth::user()->id,
                'property_code' => 'PROP-' . strtoupper(uniqid()),
                'price' => $validated['price'] ?? 0,
                'currency' => $validated['currency'] ?? 'SAR',
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? 'Unknown',
                'country' => $validated['country'] ?? 'Saudi Arabia',
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'status' => 'draft',
                'featured' => $validated['featured'] ?? false,
                'premium' => $validated['premium'] ?? false,
                'views_count' => 0,
                'area' => $validated['area'] ?? null,
                'area_unit' => $validated['area_unit'] ?? 'sq_m',
                'bedrooms' => $validated['bedrooms'] ?? null,
                'bathrooms' => $validated['bathrooms'] ?? null,
            ]);

            // Handle main images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('properties/images', 'public');

                    PropertyMedia::create([
                        'property_id' => $property->id,
                        'media_type' => 'image',
                        'file_path' => $path,
                        'file_name' => $image->getClientOriginalName(),
                        'file_size' => $image->getSize(),
                        'file_type' => $image->getMimeType(),
                        'sort_order' => $index,
                        'is_featured' => $index === 0,
                        'uploaded_by' => Auth::user()->id,
                    ]);
                }
            }

            // Handle room-specific images
            $roomTypes = ['living_room', 'kitchen', 'bedrooms', 'bathrooms', 'entrance', 'outdoor', 'garage', 'amenities'];
            foreach ($roomTypes as $roomType) {
                if ($request->hasFile("room_images.{$roomType}")) {
                    foreach ($request->file("room_images.{$roomType}") as $index => $image) {
                        $path = $image->store('properties/images/' . $roomType, 'public');

                        PropertyMedia::create([
                            'property_id' => $property->id,
                            'media_type' => 'image',
                            'file_path' => $path,
                            'file_name' => $image->getClientOriginalName(),
                            'file_size' => $image->getSize(),
                            'file_type' => $image->getMimeType(),
                            'sort_order' => $index,
                            'is_featured' => false,
                            'uploaded_by' => Auth::user()->id,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('optimized.properties.show', $property)
                ->with('success', 'Property created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error creating property: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified property.
     */
    public function edit(Property $property)
    {
        // Check if user can edit this property
        $this->authorize('update', $property);

        $propertyTypes = $this->propertyService->getPropertyTypes();
        $amenities = PropertyAmenity::where('is_active', true)->orderBy('name')->get();
        $features = PropertyFeature::where('is_active', true)->orderBy('name')->get();

        return view('properties.edit', compact('property', 'propertyTypes', 'amenities', 'features'));
    }

    /**
     * Update the specified property in storage.
     */
    public function update(Request $request, Property $property)
    {
        $this->authorize('update', $property);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'listing_type' => 'required|in:sale,rent',
            'property_type_id' => 'required|exists:property_types,id',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'location' => 'required|array',
            'location.city' => 'required|string|max:255',
            'location.country' => 'required|string|max:255',
            'location.address' => 'nullable|string|max:500',
            'location.latitude' => 'nullable|numeric',
            'location.longitude' => 'nullable|numeric',
            'details' => 'nullable|array',
            'details.bedrooms' => 'nullable|integer|min:0',
            'details.bathrooms' => 'nullable|integer|min:0',
            'details.area' => 'nullable|integer|min:0',
            'featured' => 'boolean',
            'premium' => 'boolean',
        ]);

        try {
            $property = $this->propertyService->updateProperty($property, $validated);

            return redirect()
                ->route('optimized.properties.show', $property)
                ->with('success', 'Property updated successfully!');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error updating property: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified property from storage.
     */
    public function destroy(Property $property)
    {
        $this->authorize('delete', $property);

        try {
            $this->propertyService->deleteProperty($property);

            return redirect()
                ->route('optimized.properties.index')
                ->with('success', 'Property deleted successfully!');
        } catch (Exception $e) {
            return back()
                ->with('error', 'Error deleting property: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified property with caching
     */
    public function show(Property $property)
    {
        // Cache property details
        $cacheKey = "property_{$property->id}_details";

        $propertyData = Cache::remember($cacheKey, 1800, function () use ($property) {
            return Property::with([
                'propertyType:id,name,slug',
                'location',
                'details',
                'pricing',
                'media' => function ($query) {
                    $query->where('media_type', 'image')->orderBy('sort_order');
                },
                'propertyAmenities:id,name,icon',
                'features:id,name,icon',
                'documents',
                'floorPlans',
                'virtualTours'
            ])->findOrFail($property->id);
        });

        // Get similar properties from cache
        $similarProperties = Cache::remember(
            "property_{$property->id}_similar",
            900,
            function () use ($property) {
                return Property::select(['id', 'title', 'listing_type'])
                    ->with([
                        'pricing:property_id,price,currency',
                        'media' => function ($query) {
                            $query->select('id', 'property_id', 'file_path', 'media_type')
                                ->where('media_type', 'image')
                                ->limit(1);
                        }
                    ])
                    ->where('property_type', $property->property_type)
                    ->where('id', '!=', $property->id)
                    ->where('status', 'active')
                    ->limit(6)
                    ->get();
            }
        );

        // Increment view count asynchronously
        $this->incrementViewCount($property->id);

        return view('properties.show', [
            'property' => $propertyData,
            'similarProperties' => $similarProperties
        ]);
    }

    /**
     * Increment view count asynchronously
     */
    private function incrementViewCount(int $propertyId): void
    {
        // Use queue for better performance
        dispatch(function () use ($propertyId) {
            Property::where('id', $propertyId)->increment('views_count');
        })->afterResponse();
    }

    /**
     * Search properties with optimized caching
     */
    public function search(Request $request)
    {
        $searchTerm = $request->get('q');

        if (empty($searchTerm)) {
            return redirect()->route('properties.index');
        }

        $cacheKey = 'search_' . md5($searchTerm . '_' . $request->get('page', 1));

        $results = Cache::remember($cacheKey, 600, function () use ($searchTerm, $request) {
            return Property::select([
                'id',
                'title',
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
                                ->orWhere('country', 'like', '%' . $searchTerm . '%')
                                ->orWhere('address', 'like', '%' . $searchTerm . '%');
                        });
                })
                ->where('status', 'active')
                ->orderBy('featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        });

        return view('properties.search', [
            'properties' => $results,
            'searchTerm' => $searchTerm
        ]);
    }

    /**
     * Clear property cache
     */
    public function clearCache()
    {
        Cache::tags(['properties'])->flush();
        return response()->json(['message' => 'Cache cleared successfully']);
    }

    /**
     * Get property statistics with caching
     */
    public function getStats()
    {
        return Cache::remember('property_stats', 3600, function () {
            return [
                'total_properties' => Property::count(),
                'active_properties' => Property::where('status', 'active')->count(),
                'featured_properties' => Property::where('featured', true)->count(),
                'premium_properties' => Property::where('premium', true)->count(),
                'total_views' => Property::sum('views_count'),
                'average_price' => Property::join('property_prices', 'properties.id', '=', 'property_prices.property_id')
                    ->avg('property_prices.price'),
            ];
        });
    }
}
