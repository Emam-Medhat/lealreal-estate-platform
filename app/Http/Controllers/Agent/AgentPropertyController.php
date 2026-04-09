<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Agent;
use App\Models\Property;
use App\Models\PropertyPrice;
use App\Models\PropertyMedia;
use App\Models\UserActivityLog;
use App\Services\OptimizedPropertyService;
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
    protected $optimizedPropertyService;

    public function __construct(
        PropertyRepositoryInterface $propertyRepository,
        OptimizedPropertyService $optimizedPropertyService
    ) {
        $this->propertyRepository = $propertyRepository;
        $this->optimizedPropertyService = $optimizedPropertyService;
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
        try {
            $agent = Auth::user()->agent;
            
            // Check if user has an agent profile
            if (!$agent) {
                return redirect()->route('dashboard')->with('error', 'Agent profile not found. Please contact administrator.');
            }
            
            $data = $request->validated();
            $data['agent_id'] = $agent->id;
            
            $property = $this->optimizedPropertyService->createProperty($data, Auth::user());

            // Handle property media
            if ($request->hasFile('images')) {
                $this->optimizedPropertyService->handleImageUpload($request->file('images'), $property->id);
            }

            // Handle documents
            if ($request->hasFile('documents')) {
                $this->optimizedPropertyService->handleDocumentUpload($request->file('documents'), $property->id);
            }

            // Handle videos
            if ($request->hasFile('videos')) {
                $this->optimizedPropertyService->handleVideoUpload($request->file('videos'), $property->id);
            }

            // Handle floor plans
            if ($request->hasFile('floor_plans')) {
                $this->optimizedPropertyService->handleFloorPlanUpload($request->file('floor_plans'), $property->id);
            }

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created_property',
                'details' => "Created property: {$property->title}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('agent.properties.show', $property)
                ->with('success', 'Property created successfully.');

        } catch (\Exception $e) {
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
        
        try {
            $data = $request->validated();
            
            // Explicitly handle boolean fields that might be missing from request if unchecked
            // and ensure they are passed as boolean values
            $data['featured'] = $request->boolean('featured');
            $data['premium'] = $request->boolean('premium');
            $data['is_negotiable'] = $request->boolean('is_negotiable');
            
            // Add other potentially missing booleans if they exist in request/form
            if ($request->has('includes_vat') || $request->exists('includes_vat')) {
                $data['includes_vat'] = $request->boolean('includes_vat');
            }
            
            $this->optimizedPropertyService->updateProperty($property, $data);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated_property',
                'details' => "Updated property: {$property->title}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('agent.properties.show', $property)
                ->with('success', 'Property updated successfully.');

        } catch (\Exception $e) {
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
