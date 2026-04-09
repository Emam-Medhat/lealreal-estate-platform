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
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewPropertyNotification;

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
        // Don't cache when there are filters applied OR when user just created a property
        $hasFilters = $request->hasAny(['q', 'property_type', 'listing_type', 'min_price', 'max_price', 'city', 'bedrooms', 'bathrooms', 'featured', 'premium']);
        $justCreated = session()->has('property_just_created');

        if ($hasFilters || $justCreated) {
            // Clear the session flag
            session()->forget('property_just_created');
            $data = $this->getPropertiesData($request);
        } else {
            // Try to get from cache first for unfiltered results
            $cacheKey = $this->generateCacheKey($request);
            $data = Cache::remember($cacheKey, 300, function () use ($request) {
                return $this->getPropertiesData($request);
            });
        }

        return view('properties.index', $data);
    }

    /**
     * Generate cache key based on request parameters
     */
    private function generateCacheKey(Request $request): string
    {
        $params = $request->only([
            'q',
            'property_type',
            'listing_type',
            'min_price',
            'max_price',
            'city',
            'bedrooms',
            'bathrooms',
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
            'properties.id',
            'properties.agent_id',
            'properties.property_type',
            'properties.title',
            'properties.description',
            'properties.listing_type',
            'properties.bedrooms',
            'properties.bathrooms',
            'properties.featured',
            'properties.premium',
            'properties.views_count',
            'properties.created_at'
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
        // Keyword search (q)
        if ($request->q) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('properties.title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('properties.description', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas(
                        'location',
                        function ($loc) use ($searchTerm) {
                            $loc->where('city', 'like', '%' . $searchTerm . '%')
                                ->orWhere('country', 'like', '%' . $searchTerm . '%')
                                ->orWhere('address', 'like', '%' . $searchTerm . '%')
                                ->orWhere('state', 'like', '%' . $searchTerm . '%');
                        }
                    );
            });
        }

        // Property type filter
        if ($request->property_type) {
            $query->whereHas('propertyType', function ($q) use ($request) {
                $q->where('slug', $request->property_type);
            });
        }

        // Listing type filter
        if ($request->listing_type) {
            $query->where('properties.listing_type', $request->listing_type);
        }

        // Price filters
        if ($request->min_price || $request->max_price) {
            // Check if we already joined pricing (for sorting)
            $hasPricingJoin = collect($query->getQuery()->joins)->contains(function ($join) {
                return $join->table === 'property_prices';
            });

            if (!$hasPricingJoin) {
                $query->join('property_prices', 'properties.id', '=', 'property_prices.property_id');
            }

            if ($request->min_price) {
                $query->where('property_prices.price', '>=', $request->min_price);
            }

            if ($request->max_price) {
                $query->where('property_prices.price', '<=', $request->max_price);
            }
        }

        // City filter (specific)
        if ($request->city) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('city', 'like', '%' . $request->city . '%');
            });
        }

        // Bedrooms filter
        if ($request->bedrooms) {
            $query->where('properties.bedrooms', '>=', $request->bedrooms);
        }

        // Bathrooms filter
        if ($request->bathrooms) {
            $query->where('properties.bathrooms', '>=', $request->bathrooms);
        }

        // Featured and premium filters
        if ($request->featured) {
            $query->where('properties.featured', true);
        }

        if ($request->premium) {
            $query->where('properties.premium', true);
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
                    $query->leftJoin('property_prices', 'properties.id', '=', 'property_prices.property_id');
                }
                $query->orderByRaw('property_prices.price ASC NULLS LAST');
                break;
            case 'price_high':
                if (!$request->min_price && !$request->max_price) {
                    $query->leftJoin('property_prices', 'properties.id', '=', 'property_prices.property_id');
                }
                $query->orderByRaw('property_prices.price DESC NULLS LAST');
                break;
            case 'area':
                $query->join('property_details', 'properties.id', '=', 'property_details.property_id')
                    ->orderBy('property_details.area', 'desc');
                break;
            case 'views':
                $query->orderBy('properties.views_count', 'desc');
                break;
            default:
                $query->orderBy('properties.created_at', 'desc');
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
        $clients = Client::select('id', 'name')->orderBy('name')->get();

        return view('properties.partial.create', compact('propertyTypes', 'amenities', 'features', 'clients'));
    }

    /**
     * Store a newly created property in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'owner_id' => 'nullable|exists:clients,id',
            'listing_type' => 'required|in:sale,rent,lease',
            'property_type_id' => 'required|exists:property_types,id',
            'status' => 'required|in:draft,active,inactive',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'neighborhood' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'floors' => 'nullable|integer|min:0',
            'parking_spaces' => 'nullable|integer|min:0',
            'year_built' => 'nullable|integer|min:1800|max:' . date('Y'),
            'area' => 'nullable|numeric|min:0',
            'area_unit' => 'nullable|string|in:sq_m,sq_ft,acre,hectare',
            'land_area' => 'nullable|numeric|min:0',
            'land_area_unit' => 'nullable|string|in:sq_m,sq_ft,acre,hectare',
            'is_negotiable' => 'boolean',
            'includes_vat' => 'boolean',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'featured' => 'boolean',
            'premium' => 'boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:property_amenities,id',
            'features' => 'nullable|array',
            'features.*' => 'exists:property_features,id',
            'specifications' => 'nullable|array',
            'specifications.finishing_type' => 'nullable|string',
            'specifications.interface' => 'nullable|string',
            'specifications.furnished' => 'nullable|string',
            'specifications.notes' => 'nullable|string',
            'schools' => 'nullable|string|max:255',
            'hospitals' => 'nullable|string|max:255',
            'shopping_centers' => 'nullable|string|max:255',
            'restaurants' => 'nullable|string|max:255',
            'transportation' => 'nullable|string|max:255',
            'parks' => 'nullable|string|max:255',
            'universities' => 'nullable|string|max:255',
            'metro_stations' => 'nullable|string|max:255',
            'cinemas' => 'nullable|string|max:255',
            'gyms' => 'nullable|string|max:255',
            'maintenance_fees' => 'nullable|numeric|min:0',
            'service_charges' => 'nullable|numeric|min:0',
            'property_tax' => 'nullable|numeric|min:0',
            'tax_year' => 'nullable|integer|min:2000',
            'insurance_cost' => 'nullable|numeric|min:0',
            'rental_start_date' => 'nullable|date|required_if:listing_type,rent,lease',
            'rental_end_date' => 'nullable|date|after_or_equal:rental_start_date',
            'expiry_date' => 'nullable|date',
            'materials' => 'nullable|string',
            'interior_features' => 'nullable|string',
            'exterior_features' => 'nullable|string',
            'payment_frequency' => 'nullable|string|in:monthly,quarterly,annually',
            'rent_period' => 'nullable|string',
            'original_price' => 'nullable|numeric|min:0',
            'virtual_tour_url' => 'nullable|url',
            'video_url' => 'nullable|url',

            // Legal & Energy fields
            'ownership_type' => 'nullable|string|max:255',
            'deed_number' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'zoning' => 'nullable|string|max:255',
            'energy_rating' => 'nullable|string|max:10',
            'solar_panels' => 'boolean',
            'water_heating' => 'boolean',
            'insulation' => 'boolean',
            'double_glazing' => 'boolean',
            'air_conditioning' => 'boolean',

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

        try {
            // Map request data to service structure
            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'owner_id' => $validated['owner_id'] ?? null,
                'listing_type' => $validated['listing_type'],
                'property_type_id' => $validated['property_type_id'],
                'status' => $validated['status'] ?? 'draft',
                'price' => $validated['price'],
                'currency' => $validated['currency'] ?? 'SAR',
                'featured' => $request->has('featured'),
                'premium' => $request->has('premium'),
                'virtual_tour_url' => $validated['virtual_tour_url'] ?? null,
                'video_url' => $validated['video_url'] ?? null,

                // Location
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'country' => $validated['country'],
                'postal_code' => $validated['postal_code'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'neighborhood' => $validated['neighborhood'],
                'district' => $validated['district'],
                'schools' => $validated['schools'] ?? null,
                'hospitals' => $validated['hospitals'] ?? null,
                'shopping_centers' => $validated['shopping_centers'] ?? null,
                'restaurants' => $validated['restaurants'] ?? null,
                'transportation' => $validated['transportation'] ?? null,
                'parks' => $validated['parks'] ?? null,
                'universities' => $validated['universities'] ?? null,
                'metro_stations' => $validated['metro_stations'] ?? null,
                'cinemas' => $validated['cinemas'] ?? null,
                'gyms' => $validated['gyms'] ?? null,

                // Details
                'bedrooms' => $validated['bedrooms'],
                'bathrooms' => $validated['bathrooms'],
                'floors' => $validated['floors'],
                'parking_spaces' => $validated['parking_spaces'],
                'year_built' => $validated['year_built'],
                'area' => $validated['area'],
                'area_unit' => $validated['area_unit'],
                'land_area' => $validated['land_area'],
                'land_area_unit' => $validated['land_area_unit'],
                'materials' => $validated['materials'] ?? null,
                'interior_features' => $validated['interior_features'] ?? null,
                'exterior_features' => $validated['exterior_features'] ?? null,
                'specifications' => isset($validated['specifications']) ? (is_array($validated['specifications']) ? json_encode($validated['specifications']) : $validated['specifications']) : null,

                // Legal & Energy
                'ownership_type' => $validated['ownership_type'] ?? null,
                'deed_number' => $validated['deed_number'] ?? null,
                'registration_number' => $validated['registration_number'] ?? null,
                'zoning' => $validated['zoning'] ?? null,
                'energy_rating' => $validated['energy_rating'] ?? null,
                'solar_panels' => $request->has('solar_panels'),
                'water_heating' => $request->has('water_heating'),
                'insulation' => $request->has('insulation'),
                'double_glazing' => $request->has('double_glazing'),
                'air_conditioning' => $request->has('air_conditioning'),

                // Financial
                'maintenance_fees' => $validated['maintenance_fees'] ?? null,
                'service_charges' => $validated['service_charges'] ?? null,
                'property_tax' => $validated['property_tax'] ?? null,
                'tax_year' => $validated['tax_year'] ?? null,
                'insurance_cost' => $validated['insurance_cost'] ?? null,
                'rental_start_date' => $validated['rental_start_date'] ?? null,
                'rental_end_date' => $validated['rental_end_date'] ?? null,
                'expiry_date' => $validated['expiry_date'] ?? null,
                'payment_frequency' => $validated['payment_frequency'] ?? null,
                'rent_period' => $validated['rent_period'] ?? null,
                'original_price' => $validated['original_price'] ?? null,
                'is_negotiable' => $request->has('is_negotiable'),
                'includes_vat' => $request->has('includes_vat'),
                'vat_rate' => $validated['vat_rate'] ?? null,

                // Relations
                'amenities' => $validated['amenities'] ?? [],
                'features' => $validated['features'] ?? [],
            ];

            $property = $this->propertyService->createProperty($data, Auth::user());

            // Handle facade image (Main Property Image)
            if ($request->hasFile('facade_image')) {
                \Log::info('Facade image found for property ' . $property->id);
                $this->propertyService->handleFacadeImageUpload($request->file('facade_image'), $property->id);
            }

            // Handle general images
            if ($request->hasFile('images')) {
                \Log::info('General images found in request: ' . count($request->file('images')));
                try {
                    $uploadedImages = $this->propertyService->handleImageUpload($request->file('images'), $property->id);
                    \Log::info('Images uploaded for property ' . $property->id . ': ' . count($uploadedImages));
                } catch (\Exception $e) {
                    \Log::error('Image upload failed: ' . $e->getMessage());
                }
            } else {
                \Log::info('No general images found in request for property ' . $property->id);
            }

            // Handle room-specific images
            if ($request->hasFile('room_images')) {
                \Log::info('Room images found for property ' . $property->id);
                $uploadedRoomImages = $this->propertyService->handleRoomImageUpload($request->file('room_images'), $property->id);
                \Log::info('Room images uploaded for property ' . $property->id);
            }

            try {
                if (Auth::check()) {
                    Auth::user()->notify(new NewPropertyNotification($property));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Notify current user failed: ' . $e->getMessage());
            }

            return redirect()
                ->route('optimized.properties.show', $property)
                ->with('success', 'تم إضافة العقار "' . $property->title . '" بنجاح!')
                ->with('property_just_created', true);

        } catch (Exception $e) {
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
        $clients = Client::select('id', 'name')->orderBy('name')->get();

        return view('properties.edit', compact('property', 'propertyTypes', 'amenities', 'features', 'clients'));
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
            'owner_id' => 'nullable|exists:clients,id',
            'listing_type' => 'required|in:sale,rent,lease',
            'property_type_id' => 'required|exists:property_types,id',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'neighborhood' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'floors' => 'nullable|integer|min:0',
            'parking_spaces' => 'nullable|integer|min:0',
            'year_built' => 'nullable|integer|min:1800|max:' . date('Y'),
            'area' => 'nullable|numeric|min:0',
            'area_unit' => 'nullable|string|in:sq_m,sq_ft,acre,hectare',
            'land_area' => 'nullable|numeric|min:0',
            'land_area_unit' => 'nullable|string|in:sq_m,sq_ft,acre,hectare',
            'is_negotiable' => 'boolean',
            'includes_vat' => 'boolean',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'featured' => 'boolean',
            'premium' => 'boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:property_amenities,id',
            'features' => 'nullable|array',
            'features.*' => 'exists:property_features,id',
            'specifications' => 'nullable|array',
            'specifications.finishing_type' => 'nullable|string',
            'specifications.interface' => 'nullable|string',
            'specifications.furnished' => 'nullable|string',
            'specifications.notes' => 'nullable|string',
            'schools' => 'nullable|string|max:255',
            'hospitals' => 'nullable|string|max:255',
            'shopping_centers' => 'nullable|string|max:255',
            'restaurants' => 'nullable|string|max:255',
            'transportation' => 'nullable|string|max:255',
            'parks' => 'nullable|string|max:255',
            'universities' => 'nullable|string|max:255',
            'metro_stations' => 'nullable|string|max:255',
            'cinemas' => 'nullable|string|max:255',
            'gyms' => 'nullable|string|max:255',
            'maintenance_fees' => 'nullable|numeric|min:0',
            'service_charges' => 'nullable|numeric|min:0',
            'property_tax' => 'nullable|numeric|min:0',
            'tax_year' => 'nullable|integer|min:2000',
            'insurance_cost' => 'nullable|numeric|min:0',
            'rental_start_date' => 'nullable|date|required_if:listing_type,rent,lease',
            'rental_end_date' => 'nullable|date|after_or_equal:rental_start_date',
            'expiry_date' => 'nullable|date',
            'payment_frequency' => 'nullable|string|in:monthly,quarterly,annually',
            'rent_period' => 'nullable|string',
            'original_price' => 'nullable|numeric|min:0',
            'virtual_tour_url' => 'nullable|url',
            'video_url' => 'nullable|url',

            // Legal & Energy fields
            'ownership_type' => 'nullable|string|max:255',
            'deed_number' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'zoning' => 'nullable|string|max:255',
            'energy_rating' => 'nullable|string|max:10',
            'solar_panels' => 'boolean',
            'water_heating' => 'boolean',
            'insulation' => 'boolean',
            'double_glazing' => 'boolean',
            'air_conditioning' => 'boolean',

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

        try {
            // Map request data to database columns
            $updateData = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'owner_id' => $validated['owner_id'] ?? null,
                'listing_type' => $validated['listing_type'],
                'property_type_id' => $validated['property_type_id'],
                'price' => $validated['price'],
                'currency' => $validated['currency'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'country' => $validated['country'],
                'postal_code' => $validated['postal_code'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'neighborhood' => $validated['neighborhood'],
                'district' => $validated['district'],
                'bedrooms' => $validated['bedrooms'],
                'bathrooms' => $validated['bathrooms'],
                'floors' => $validated['floors'],
                'parking_spaces' => $validated['parking_spaces'],
                'year_built' => $validated['year_built'],
                'area' => $validated['area'],
                'area_unit' => $validated['area_unit'],
                'land_area' => $validated['land_area'],
                'land_area_unit' => $validated['land_area_unit'],
                'featured' => $request->has('featured'),
                'premium' => $request->has('premium'),
                'video_url' => $validated['video_url'] ?? null,
                'virtual_tour_url' => $validated['virtual_tour_url'] ?? null,
                'specifications' => isset($validated['specifications']) ? (is_array($validated['specifications']) ? json_encode($validated['specifications']) : $validated['specifications']) : null,

                // Legal & Energy
                'ownership_type' => $validated['ownership_type'] ?? null,
                'deed_number' => $validated['deed_number'] ?? null,
                'registration_number' => $validated['registration_number'] ?? null,
                'zoning' => $validated['zoning'] ?? null,
                'energy_rating' => $validated['energy_rating'] ?? null,
                'solar_panels' => $request->has('solar_panels'),
                'water_heating' => $request->has('water_heating'),
                'insulation' => $request->has('insulation'),
                'double_glazing' => $request->has('double_glazing'),
                'air_conditioning' => $request->has('air_conditioning'),

                // Nearby Places
                'schools' => $validated['schools'] ?? null,
                'hospitals' => $validated['hospitals'] ?? null,
                'shopping_centers' => $validated['shopping_centers'] ?? null,
                'restaurants' => $validated['restaurants'] ?? null,
                'transportation' => $validated['transportation'] ?? null,
                'parks' => $validated['parks'] ?? null,
                'universities' => $validated['universities'] ?? null,
                'metro_stations' => $validated['metro_stations'] ?? null,
                'cinemas' => $validated['cinemas'] ?? null,
                'gyms' => $validated['gyms'] ?? null,

                // Financial
                'maintenance_fees' => $validated['maintenance_fees'] ?? null,
                'service_charges' => $validated['service_charges'] ?? null,
                'property_tax' => $validated['property_tax'] ?? null,
                'tax_year' => $validated['tax_year'] ?? null,
                'insurance_cost' => $validated['insurance_cost'] ?? null,
                'rental_start_date' => $validated['rental_start_date'] ?? null,
                'rental_end_date' => $validated['rental_end_date'] ?? null,
                'expiry_date' => $validated['expiry_date'] ?? null,
                'materials' => $validated['materials'] ?? null,
                'interior_features' => $validated['interior_features'] ?? null,
                'exterior_features' => $validated['exterior_features'] ?? null,
                'payment_frequency' => $validated['payment_frequency'] ?? null,
                'rent_period' => $validated['rent_period'] ?? null,
                'original_price' => $validated['original_price'] ?? null,
                'is_negotiable' => $request->has('is_negotiable'),
                'includes_vat' => $request->has('includes_vat'),
                'vat_rate' => $validated['vat_rate'] ?? null,

                // Relations for updateService
                'amenities' => $validated['amenities'] ?? [],
                'features' => $validated['features'] ?? [],
            ];

            $property = $this->propertyService->updateProperty($property, $updateData);

            // Handle facade image
            if ($request->hasFile('facade_image')) {
                $this->propertyService->handleFacadeImageUpload($request->file('facade_image'), $property->id);
            }

            // Handle main images
            if ($request->hasFile('images')) {
                $this->propertyService->handleImageUpload($request->file('images'), $property->id);
            }

            // Handle room-specific images
            if ($request->hasFile('room_images')) {
                $this->propertyService->handleRoomImageUpload($request->file('room_images'), $property->id);
            }

            return redirect()
                ->route('optimized.properties.show', $property)
                ->with('success', 'تم تحديث العقار "' . $property->title . '" بنجاح!');
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
                }
                ,
                'propertyAmenities:id,name,icon',
                'features:id,name,icon',
                'propertyDocuments',
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
                'agent_id',
                'property_type',
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
                ->where(
                    function ($query) use ($searchTerm) {
                        $query->where('title', 'like', '%' . $searchTerm . '%')
                            ->orWhere('description', 'like', '%' . $searchTerm . '%')
                            ->orWhereHas(
                                'location',
                                function ($q) use ($searchTerm) {
                                    $q->where('city', 'like', '%' . $searchTerm . '%')
                                        ->orWhere('country', 'like', '%' . $searchTerm . '%')
                                        ->orWhere('address', 'like', '%' . $searchTerm . '%');
                                }
                            );
                    }
                )
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
        try {
            if (config('cache.default') !== 'file' && config('cache.default') !== 'database') {
                Cache::tags(['properties'])->flush();
            }
        } catch (\Exception $e) {
            \Log::warning('Cache tagging not supported in clearCache: ' . $e->getMessage());
        }

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