<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
use App\Services\OptimizedPropertyService;
use App\Helpers\RealTimeNotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    protected $propertyService;

    public function __construct(OptimizedPropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
        $this->middleware('auth')->except(['index', 'show', 'search']);
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'property_type', 'listing_type', 'min_price', 'max_price', 
            'city', 'bedrooms', 'featured', 'premium', 'sort', 'order'
        ]);

        $properties = $this->propertyService->getProperties($filters, 12);
        
        // Use caching for property types as they don't change often
        $propertyTypes = \Illuminate\Support\Facades\Cache::remember('property_types_active', 3600, function() {
            return PropertyType::active()->ordered()->get();
        });

        return view('properties.index', compact('properties', 'propertyTypes'));
    }

    public function show(Property $property)
    {
        // Use optimized service for details
        $property = $this->propertyService->getPropertyDetails($property->id);

        if (!$property) {
            abort(404);
        }

        // Increment views asynchronously
        $this->propertyService->incrementViewCount($property->id);

        // Get similar properties efficiently
        $similarProperties = $this->propertyService->getSimilarProperties($property->id);

        return view('properties.show', compact('property', 'similarProperties'));
    }

    public function create()
    {
        $propertyTypes = PropertyType::active()->ordered()->get();
        $amenities = PropertyAmenity::active()->ordered()->get();
        $features = PropertyFeature::active()->ordered()->get();

        return view('properties.create', compact('propertyTypes', 'amenities', 'features'));
    }

    public function store(StorePropertyRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Create property with all data in single table
            $property = Property::create([
                'agent_id' => $user->id,
                'property_type' => $request->property_type_id,
                'title' => $request->title,
                'description' => $request->description,
                'listing_type' => $request->listing_type,
                'status' => $request->status ?? 'draft',
                'featured' => $request->featured ?? false,
                'premium' => $request->premium ?? false,
                'property_code' => $this->generatePropertyCode(),
                'views_count' => 0,
                'favorites_count' => 0,
                'inquiries_count' => 0,
                'price' => $request->price,
                'currency' => $request->currency,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'floors' => $request->floors,
                'year_built' => $request->year_built,
                'area' => $request->area,
                'area_unit' => $request->area_unit,
                'virtual_tour_url' => $request->virtual_tour_url,
            ]);

            // Create related records
            $property->details()->create([
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'floors' => $request->floors,
                'parking_spaces' => $request->parking_spaces,
                'year_built' => $request->year_built,
                'area' => $request->area,
                'area_unit' => $request->area_unit,
                'land_area' => $request->land_area,
                'land_area_unit' => $request->land_area_unit,
                'specifications' => $request->specifications,
                'materials' => $request->materials,
                'interior_features' => $request->interior_features,
                'exterior_features' => $request->exterior_features,
            ]);

            $property->location()->create([
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'neighborhood' => $request->neighborhood,
                'district' => $request->district,
                'coordinates' => $request->coordinates,
                'nearby_landmarks' => $request->nearby_landmarks,
                'transportation' => $request->transportation,
            ]);

            $property->pricing()->create([
                'price' => $request->price,
                'currency' => $request->currency,
                'price_type' => $request->listing_type,
                'price_per_sqm' => $request->area > 0 ? $request->price / $request->area : null,
                'is_negotiable' => $request->is_negotiable ?? false,
                'includes_vat' => $request->includes_vat ?? false,
                'vat_rate' => $request->vat_rate ?? 0,
                'service_charges' => $request->service_charges,
                'maintenance_fees' => $request->maintenance_fees,
                'payment_frequency' => $request->payment_frequency,
                'payment_terms' => $request->payment_terms,
                'effective_date' => now()->toDateString(),
                'is_active' => true,
            ]);

            // Attach amenities and features if they exist
            if ($request->amenities) {
                $property->amenities()->attach($request->amenities);
            }

            if ($request->features) {
                $property->features()->attach($request->features);
            }

            // Upload images if they exist
            if ($request->hasFile('images')) {
                $this->uploadImages($property, $request->file('images'));
            }

            // Upload documents if they exist
            if ($request->hasFile('documents')) {
                $this->uploadDocuments($property, $request->file('documents'));
            }

            DB::commit();

            // Send real-time notifications
            try {
                RealTimeNotificationHelper::propertyCreated(
                    $property->id,
                    $property->title,
                    $user->id
                );
                \Log::info('Property notification sent successfully', [
                    'property_id' => $property->id,
                    'agent_id' => $user->id,
                    'title' => $property->title
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send property notification', [
                    'error' => $e->getMessage(),
                    'property_id' => $property->id,
                    'agent_id' => $user->id
                ]);
            }

            return redirect()
                ->route('properties.show', $property)
                ->with('success', 'تم إنشاء العقار بنجاح!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Failed to create property: ' . $e->getMessage());
        }
    }

    public function edit(Property $property)
    {
        $this->authorize('update', $property);

        $property->load([
            'details',
            'location',
            'price',
            'amenities',
            'features',
            'documents',
            'virtualTours',
            'floorPlans',
            'neighborhoods'
        ]);

        $propertyTypes = PropertyType::active()->ordered()->get();
        $amenities = PropertyAmenity::active()->ordered()->get();
        $features = PropertyFeature::active()->ordered()->get();

        return view('properties.edit', compact('property', 'propertyTypes', 'amenities', 'features'));
    }

    public function update(UpdatePropertyRequest $request, Property $property)
    {
        $this->authorize('update', $property);

        try {
            DB::beginTransaction();

            $oldStatus = $property->status;

            // Update property
            $property->update([
                'property_type' => $request->property_type_id,
                'title' => $request->title,
                'description' => $request->description,
                'listing_type' => $request->listing_type,
                'status' => $request->status,
                'featured' => $request->featured ?? false,
                'premium' => $request->premium ?? false,
                'price' => $request->price,
                'currency' => $request->currency,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'floors' => $request->floors,
                'year_built' => $request->year_built,
                'area' => $request->area,
                'area_unit' => $request->area_unit,
                'virtual_tour_url' => $request->virtual_tour_url,
            ]);

            // Update details
            $property->details->update([
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'floors' => $request->floors,
                'parking_spaces' => $request->parking_spaces,
                'year_built' => $request->year_built,
                'area' => $request->area,
                'area_unit' => $request->area_unit,
                'land_area' => $request->land_area,
                'land_area_unit' => $request->land_area_unit,
                'specifications' => $request->specifications,
                'materials' => $request->materials,
                'interior_features' => $request->interior_features,
                'exterior_features' => $request->exterior_features,
            ]);

            // Update location
            $property->location->update([
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'neighborhood' => $request->neighborhood,
                'district' => $request->district,
                'coordinates' => $request->coordinates,
                'nearby_landmarks' => $request->nearby_landmarks,
                'transportation' => $request->transportation,
            ]);

            // Update price
            // Save to main table as well
            $property->update(['price' => $request->price]);

            $propertyPrice = $property->price()->first();

            if ($propertyPrice) {
                $oldPrice = $propertyPrice->price;
                $newPrice = $request->price;

                $propertyPrice->update([
                    'price' => $newPrice,
                    'currency' => $request->currency,
                    'price_type' => $request->listing_type,
                    'price_per_sqm' => $request->area > 0 ? $newPrice / $request->area : null,
                    'is_negotiable' => $request->is_negotiable ?? false,
                    'includes_vat' => $request->includes_vat ?? false,
                    'vat_rate' => $request->vat_rate ?? 0,
                    'service_charges' => $request->service_charges,
                    'maintenance_fees' => $request->maintenance_fees,
                    'payment_frequency' => $request->payment_frequency,
                    'payment_terms' => $request->payment_terms,
                ]);

                // Record price history if changed
                if ($oldPrice != $newPrice) {
                    $changeType = $newPrice > $oldPrice ? 'increase' : 'decrease';
                    $changePercentage = abs(($newPrice - $oldPrice) / $oldPrice * 100);

                    PropertyPriceHistory::create([
                        'property_id' => $property->id,
                        'old_price' => $oldPrice,
                        'new_price' => $newPrice,
                        'currency' => $request->currency,
                        'change_reason' => $request->price_change_reason,
                        'change_type' => $changeType,
                        'change_percentage' => $changePercentage,
                        'changed_by' => Auth::id(),
                    ]);
                }
            } else {
                // Create price if it doesn't exist
                PropertyPrice::create([
                    'property_id' => $property->id,
                    'price' => $request->price,
                    'currency' => $request->currency,
                    'price_type' => $request->listing_type,
                    'price_per_sqm' => $request->area > 0 ? $request->price / $request->area : null,
                    'is_negotiable' => $request->is_negotiable ?? false,
                    'includes_vat' => $request->includes_vat ?? false,
                    'vat_rate' => $request->vat_rate ?? 0,
                    'service_charges' => $request->service_charges,
                    'maintenance_fees' => $request->maintenance_fees,
                    'payment_frequency' => $request->payment_frequency,
                    'payment_terms' => $request->payment_terms,
                    'effective_date' => now()->toDateString(),
                    'is_active' => true,
                ]);
            }

            // Sync amenities
            $property->amenities()->sync($request->amenities ?? []);

            // Sync features
            $property->features()->sync($request->features ?? []);

            // Upload new images
            if ($request->hasFile('images')) {
                $this->uploadImages($property, $request->file('images'));
            }

            // Upload new documents
            if ($request->hasFile('documents')) {
                $this->uploadDocuments($property, $request->file('documents'));
            }

            // Record status change if changed
            if ($oldStatus != $request->status) {
                PropertyStatusHistory::recordStatusChange(
                    $property->id,
                    $oldStatus,
                    $request->status,
                    $request->status_change_reason,
                    Auth::id()
                );
            }

            DB::commit();

            return redirect()
                ->route('properties.show', $property)
                ->with('success', 'Property updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Failed to update property: ' . $e->getMessage());
        }
    }

    public function destroy(Property $property)
    {
        $this->authorize('delete', $property);

        try {
            DB::beginTransaction();

            // Soft delete property (cascade will handle related records)
            $property->delete();

            DB::commit();

            return redirect()
                ->route('properties.index')
                ->with('success', 'Property deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Failed to delete property: ' . $e->getMessage());
        }
    }

    private function uploadImages(Property $property, $images)
    {
        foreach ($images as $index => $image) {
            $path = $image->store('properties/images', 'public');

            PropertyMedia::create([
                'property_id' => $property->id,
                'media_type' => 'image',
                'file_name' => $image->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
                'width' => getimagesize($image)[0] ?? null,
                'height' => getimagesize($image)[1] ?? null,
                'is_primary' => $index === 0, // First image is primary
                'sort_order' => $index,
                'uploaded_by' => Auth::id(),
            ]);
        }
    }

    private function uploadDocuments(Property $property, $documents)
    {
        foreach ($documents as $document) {
            $path = $document->store('properties/documents', 'public');

            PropertyDocument::create([
                'property_id' => $property->id,
                'title' => $document->getClientOriginalName(),
                'file_name' => $document->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => pathinfo($document->getClientOriginalName(), PATHINFO_EXTENSION),
                'file_size' => $document->getSize(),
                'mime_type' => $document->getMimeType(),
            ]);
        }
    }

    private function recordView(Property $property)
    {
        PropertyView::create([
            'property_id' => $property->id,
            'session_id' => session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => Auth::id(),
            'referrer' => request()->header('referer'),
            'view_type' => 'detail',
        ]);

        // Increment view count
        $property->increment('views_count');

        // Record analytics
        PropertyAnalytic::recordMetric($property->id, 'views');
    }

    private function generatePropertyCode(): string
    {
        do {
            $code = 'PROP-' . strtoupper(Str::random(8));
        } while (Property::where('property_code', $code)->exists());

        return $code;
    }

    public function recommendations(Request $request)
    {
        $user = Auth::user();
        
        // Get user's preferences and history
        $userFavorites = $user->favoriteProperties()->pluck('properties.id')->toArray();
        
        // Get user views safely - handle missing table
        $userViews = [];
        try {
            if (Schema::hasTable('property_views')) {
                $userViews = \App\Models\PropertyView::where('user_id', $user->id)->pluck('property_id')->toArray();
            }
        } catch (\Exception $e) {
            // Table doesn't exist, continue with empty views
            $userViews = [];
        }
        
        // Get properties user has interacted with
        $interactedProperties = array_unique(array_merge($userFavorites, $userViews));
        
        // Get recommended properties based on user behavior
        $recommendedProperties = Property::with([
            'agent.profile',
            'location',
            'propertyType',
            'media' => function ($query) {
                $query->where('media_type', 'image')->orderBy('sort_order');
            },
            'propertyAmenities',
            'features'
        ])
        ->where('status', 'active')
        ->whereNotIn('id', $interactedProperties) // Exclude already interacted properties
        ->inRandomOrder()
        ->limit(12)
        ->get();
        
        // If user has no history, get featured properties
        if ($recommendedProperties->isEmpty()) {
            $recommendedProperties = Property::with([
                'agent.profile',
                'location',
                'propertyType',
                'media' => function ($query) {
                    $query->where('media_type', 'image')->orderBy('sort_order');
                },
                'propertyAmenities',
                'features'
            ])
            ->where('status', 'active')
            ->where('featured', true)
            ->latest()
            ->limit(12)
            ->get();
        }
        
        return view('properties.recommendations', compact('recommendedProperties'));
    }
}
