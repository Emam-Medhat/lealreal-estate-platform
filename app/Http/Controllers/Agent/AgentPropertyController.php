<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\StorePropertyRequest;
use App\Http\Requests\Agent\UpdatePropertyRequest;
use App\Models\Agent;
use App\Models\Property;
use App\Models\PropertyPrice;
use App\Models\PropertyMedia;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Repositories\Contracts\PropertyRepositoryInterface;

class AgentPropertyController extends Controller
{
    protected $propertyRepository;

    public function __construct(PropertyRepositoryInterface $propertyRepository)
    {
        $this->propertyRepository = $propertyRepository;
    }

    public function index(Request $request)
    {
        $agent = Auth::user()->agent;
        
        // Check if user has an agent profile
        if (!$agent) {
            return redirect()->route('dashboard')->with('error', 'Agent profile not found. Please contact administrator.');
        }
        
        $filters = $request->only(['search', 'status', 'property_type', 'min_price', 'max_price']);
        
        $properties = $this->propertyRepository->getAgentPropertiesPaginated($agent->id, $filters, 20);

        return view('agent.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('agent.properties.create');
    }

    public function store(StorePropertyRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $agent = Auth::user()->agent;
            
            // Check if user has an agent profile
            if (!$agent) {
                return redirect()->route('dashboard')->with('error', 'Agent profile not found. Please contact administrator.');
            }
            
            $property = Property::create([
                'agent_id' => $agent->id,
                'title' => $request->title,
                'slug' => Str::slug($request->title) . '-' . time(),
                'property_code' => 'PROP-' . strtoupper(uniqid()),
                'description' => $request->description,
                'property_type' => $request->property_type,
                'listing_type' => $request->listing_type,
                'status' => $request->status ?? 'draft',
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'area' => $request->area,
                'area_unit' => $request->area_unit ?? 'sq_m',
                'floors' => $request->floors,
                'year_built' => $request->year_built,
                'parking_spaces' => $request->parking_spaces,
                'land_area' => $request->land_area,
                'land_area_unit' => $request->land_area_unit,
                'featured' => $request->featured ?? false,
                'premium' => $request->premium ?? false,
                'views_count' => 0,
                'inquiries_count' => 0,
                'specifications' => $request->specifications,
                'materials' => $request->materials,
                'interior_features' => $request->interior_features,
                'exterior_features' => $request->exterior_features,
                'nearby_places' => $request->nearby_places,
                'schools' => $request->schools,
                'hospitals' => $request->hospitals,
                'shopping_centers' => $request->shopping_centers,
                'restaurants' => $request->restaurants,
                'public_transport' => $request->public_transport,
                'ownership_type' => $request->ownership_type,
                'deed_number' => $request->deed_number,
                'registration_number' => $request->registration_number,
                'zoning' => $request->zoning,
                'building_permit' => $request->building_permit,
                'occupancy_permit' => $request->occupancy_permit,
                'energy_rating' => $request->energy_rating,
                'solar_panels' => $request->solar_panels ?? false,
                'water_heating' => $request->water_heating,
                'insulation' => $request->insulation,
                'double_glazing' => $request->double_glazing ?? false,
                'air_conditioning' => $request->air_conditioning,
                'virtual_tour_url' => $request->virtual_tour_url,
            ]);

            // Create property location
            $property->location()->create([
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'neighborhood' => $request->neighborhood,
                'coordinates' => [
                    'lat' => $request->latitude,
                    'lng' => $request->longitude,
                ],
            ]);

            // Create property price
            $property->price()->create([
                'price' => $request->price,
                'currency' => $request->currency ?? 'USD',
                'price_type' => $request->listing_type,
                'rent_period' => $request->payment_frequency,
                'is_negotiable' => $request->is_negotiable ?? false,
                'original_price' => $request->original_price,
                'discount_percentage' => $request->discount_percentage,
                'includes_vat' => $request->includes_vat ?? false,
                'vat_rate' => $request->vat_rate,
                'service_charges' => $request->service_charges,
                'maintenance_fees' => $request->maintenance_fees,
                'payment_terms' => $request->payment_terms,
            ]);

            // Handle property media
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('properties/images', 'public');
                    
                    PropertyMedia::create([
                        'property_id' => $property->id,
                        'type' => 'image',
                        'file_path' => $path,
                        'file_name' => $image->getClientOriginalName(),
                        'file_size' => $image->getSize(),
                        'mime_type' => $image->getMimeType(),
                        'sort_order' => $index,
                        'is_featured' => $index === 0, // First image is featured
                    ]);
                }
            }

            // Handle documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $index => $document) {
                    $path = $document->store('properties/documents', 'public');
                    
                    PropertyMedia::create([
                        'property_id' => $property->id,
                        'type' => 'document',
                        'file_path' => $path,
                        'file_name' => $document->getClientOriginalName(),
                        'file_size' => $document->getSize(),
                        'mime_type' => $document->getMimeType(),
                        'sort_order' => $index,
                    ]);
                }
            }

            // Handle videos
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $index => $video) {
                    $path = $video->store('properties/videos', 'public');
                    
                    PropertyMedia::create([
                        'property_id' => $property->id,
                        'type' => 'video',
                        'file_path' => $path,
                        'file_name' => $video->getClientOriginalName(),
                        'file_size' => $video->getSize(),
                        'mime_type' => $video->getMimeType(),
                        'sort_order' => $index,
                    ]);
                }
            }

            // Handle floor plans
            if ($request->hasFile('floor_plans')) {
                foreach ($request->file('floor_plans') as $index => $floorPlan) {
                    $path = $floorPlan->store('properties/floor_plans', 'public');
                    
                    PropertyMedia::create([
                        'property_id' => $property->id,
                        'type' => 'floor_plan',
                        'file_path' => $path,
                        'file_name' => $floorPlan->getClientOriginalName(),
                        'file_size' => $floorPlan->getSize(),
                        'mime_type' => $floorPlan->getMimeType(),
                        'sort_order' => $index,
                    ]);
                }
            }

            // Add amenities if provided
            if ($request->amenities) {
                $property->amenities()->attach($request->amenities);
            }

            // Add features if provided
            if ($request->features) {
                $property->features()->attach($request->features);
            }

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created_property',
                'details' => "Created property: {$property->title}",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return redirect()->route('agent.properties.show', $property)
                ->with('success', 'Property created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create property: ' . $e->getMessage());
        }
    }

    public function addPhotos(Property $property)
    {
        $this->authorize('view', $property);
        
        $property->load('media');
        
        return view('agent.properties.add-photos', compact('property'));
    }

    public function uploadPhotos(Request $request, Property $property)
    {
        $this->authorize('update', $property);
        
        $request->validate([
            'photos.*' => 'required|image|mimes:jpeg,jpg,png,gif|max:10240',
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('properties/images', 'public');
                
                PropertyMedia::create([
                    'property_id' => $property->id,
                    'type' => 'image',
                    'file_path' => $path,
                    'file_name' => $photo->getClientOriginalName(),
                    'file_size' => $photo->getSize(),
                    'mime_type' => $photo->getMimeType(),
                    'sort_order' => $property->media->count() + $index,
                    'is_featured' => $property->media->count() === 0, // First photo is featured
                ]);
            }
        }

        return redirect()->route('agent.properties.add-photos', $property)
            ->with('success', 'Photos uploaded successfully!');
    }

    public function deletePhoto(PropertyMedia $media)
    {
        $property = $media->property;
        $this->authorize('update', $property);
        
        // Delete file from storage
        Storage::disk('public')->delete($media->file_path);
        
        // Delete database record
        $media->delete();
        
        return response()->json(['success' => true]);
    }

    public function show(Property $property)
    {
        $this->authorize('view', $property);
        
        $property->load([
            'location', 
            'pricing', 
            'media' => function ($query) {
                $query->orderBy('sort_order');
            },
            'propertyAmenities',
            'features',
            'agent.profile'
        ]);

        return view('agent.properties.show', compact('property'));
    }

    public function edit(Property $property)
    {
        $this->authorize('update', $property);
        
        $property->load(['location', 'pricing', 'media', 'propertyAmenities', 'features']);
        
        return view('agent.properties.edit', compact('property'));
    }

    public function update(UpdatePropertyRequest $request, Property $property)
    {
        $this->authorize('update', $property);
        
        DB::beginTransaction();
        
        try {
            $property->update([
                'title' => $request->title,
                'description' => $request->description,
                'property_type' => $request->property_type,
                'status' => $request->status,
                'bedrooms' => $request->bedrooms,
                'bathrooms' => $request->bathrooms,
                'area' => $request->area,
                'year_built' => $request->year_built,
                'parking_spaces' => $request->parking_spaces,
                'listing_expires_at' => $request->listing_expires_at,
                'featured' => $request->featured ?? false,
            ]);

            // Update location
            $property->location()->update([
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'neighborhood' => $request->neighborhood,
                'coordinates' => [
                    'lat' => $request->latitude,
                    'lng' => $request->longitude,
                ],
            ]);

            // Update price
            if ($property->price) {
                $property->price()->update([
                    'price' => $request->price,
                    'currency' => $request->currency,
                    'price_type' => $request->price_type,
                    'rent_period' => $request->rent_period,
                    'is_negotiable' => $request->is_negotiable ?? false,
                    'original_price' => $request->original_price,
                    'discount_percentage' => $request->discount_percentage,
                ]);
            } else {
                // Create price if it doesn't exist
                PropertyPrice::create([
                    'property_id' => $property->id,
                    'price' => $request->price,
                    'currency' => $request->currency,
                    'price_type' => $request->price_type,
                    'rent_period' => $request->rent_period,
                    'is_negotiable' => $request->is_negotiable ?? false,
                    'original_price' => $request->original_price,
                    'discount_percentage' => $request->discount_percentage,
                    'effective_date' => now()->toDateString(),
                    'is_active' => true,
                ]);
            }

            // Update amenities
            if ($request->amenities) {
                $property->amenities()->sync($request->amenities);
            }

            // Update features
            if ($request->features) {
                $property->features()->sync($request->features);
            }

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated_property',
                'details' => "Updated property: {$property->title}",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return redirect()->route('agent.properties.show', $property)
                ->with('success', 'Property updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update property: ' . $e->getMessage());
        }
    }

    public function destroy(Property $property)
    {
        $this->authorize('delete', $property);
        
        DB::beginTransaction();
        
        try {
            // Delete property media files
            foreach ($property->media as $media) {
                Storage::disk('public')->delete($media->file_path);
                $media->delete();
            }
            
            // Delete property
            $property->delete();

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted_property',
                'details' => "Deleted property: {$property->title}",
                'ip_address' => request()->ip(),
            ]);

            DB::commit();

            return redirect()->route('agent.properties.index')
                ->with('success', 'Property deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Failed to delete property: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Property $property): JsonResponse
    {
        $this->authorize('update', $property);
        
        $newStatus = $property->status === 'active' ? 'inactive' : 'active';
        $property->update(['status' => $newStatus]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'toggled_property_status',
            'details' => "Toggled property {$property->title} status to {$newStatus}",
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => "Property status changed to {$newStatus}"
        ]);
    }

    public function uploadImages(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);
        
        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $uploadedImages = [];

        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('properties/images', 'public');
            
            $media = PropertyMedia::create([
                'property_id' => $property->id,
                'type' => 'image',
                'file_path' => $path,
                'file_name' => $image->getClientOriginalName(),
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
                'sort_order' => $property->media()->count() + $index,
                'is_featured' => false,
            ]);

            $uploadedImages[] = [
                'id' => $media->id,
                'url' => asset('storage/' . $path),
                'name' => $image->getClientOriginalName(),
            ];
        }

        return response()->json([
            'success' => true,
            'images' => $uploadedImages,
            'message' => 'Images uploaded successfully'
        ]);
    }

    public function deleteImage(PropertyMedia $media): JsonResponse
    {
        $property = $media->property;
        $this->authorize('update', $property);
        
        Storage::disk('public')->delete($media->file_path);
        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);
    }

    public function setFeaturedImage(PropertyMedia $media): JsonResponse
    {
        $property = $media->property;
        $this->authorize('update', $property);
        
        // Remove featured status from all other images
        $property->media()->where('type', 'image')->update(['is_featured' => false]);
        
        // Set this image as featured
        $media->update(['is_featured' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Featured image set successfully'
        ]);
    }

    public function featured(Request $request)
    {
        $agent = Auth::user()->agent;
        
        // Check if user has an agent profile
        if (!$agent) {
            return redirect()->route('dashboard')->with('error', 'Agent profile not found. Please contact administrator.');
        }
        
        $properties = $agent->properties()
            ->where('featured', true)
            ->with(['location', 'price', 'media'])
            ->latest()
            ->paginate(12);

        return view('agent.properties.featured', compact('properties'));
    }

    public function toggleFeatured(Request $request, Property $property)
    {
        $this->authorizePropertyAccess($property);
        
        $property->featured = !$property->featured;
        $property->save();

        return response()->json([
            'success' => true,
            'message' => 'Property featured status updated successfully',
            'featured' => $property->featured
        ]);
    }

    public function getPropertyStats(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        // Check if user has an agent profile
        if (!$agent) {
            return response()->json(['error' => 'Agent profile not found'], 404);
        }
        
        $stats = [
            'total_properties' => $agent->properties()->count(),
            'active_properties' => $agent->properties()->where('status', 'active')->count(),
            'sold_properties' => $agent->properties()->where('status', 'sold')->count(),
            'pending_properties' => $agent->properties()->where('status', 'pending')->count(),
            'expired_properties' => $agent->properties()
                ->where('listing_expires_at', '<', now())
                ->count(),
            'featured_properties' => $agent->properties()->where('featured', true)->count(),
            'total_views' => $agent->properties()->sum('views_count'),
            'total_inquiries' => $agent->properties()->sum('inquiries_count'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
