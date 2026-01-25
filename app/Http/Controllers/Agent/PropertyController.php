<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyMedia;
use App\Models\VirtualTour;
use App\Models\SpecialOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('agent');
    }

    public function index(Request $request)
    {
        $agent = Auth::user();
        
        $properties = Property::where('agent_id', $agent->id)
            ->with(['media'])
            ->when($request->boolean('featured'), function($query) {
                $query->where('featured', true);
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->type, function($query, $type) {
                $query->where('property_type', $type);
            })
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('property_code', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('agent.properties.index', compact('properties'));
    }

    public function featured(Request $request)
    {
        $request->merge(['featured' => true]);

        return $this->index($request);
    }

    public function create()
    {
        return view('agent.properties.create');
    }

    public function store(Request $request)
    {
        $agent = Auth::user();
            
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'property_type' => 'required|in:apartment,villa,house,land,commercial',
            'listing_type' => 'required|in:sale,rent',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|in:SAR,USD,EUR',
            'area' => 'required|numeric|min:0',
            'area_unit' => 'required|in:sq_m,sq_ft',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'floors' => 'nullable|integer|min:0',
            'year_built' => 'nullable|integer|min:1900|max:' . date('Y'),
            'status' => 'required|in:draft,active,inactive,sold,rented',
            'featured' => 'boolean',
            'premium' => 'boolean',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'amenities' => 'nullable|string',
            'nearby_places' => 'nullable|string',
            'virtual_tour_url' => 'nullable|url',
            'video_url' => 'nullable|url',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx|max:10240',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Convert comma-separated strings to arrays
        if (!empty($validated['amenities'])) {
            $validated['amenities'] = array_map('trim', explode(',', $validated['amenities']));
        } else {
            $validated['amenities'] = [];
        }

        if (!empty($validated['nearby_places'])) {
            $validated['nearby_places'] = array_map('trim', explode(',', $validated['nearby_places']));
        } else {
            $validated['nearby_places'] = [];
        }

        $validated['agent_id'] = $agent->id;
        $validated['property_code'] = $this->generatePropertyCode();
        $validated['views_count'] = 0;
        $validated['inquiries_count'] = 0;
        $validated['favorites_count'] = 0;

        $property = Property::create($validated);

        return redirect()
            ->route('agent.properties.show', $property)
            ->with('success', 'Property created successfully!');
    }

    public function show(Property $property)
    {
        if ($property->agent_id !== Auth::id()) {
            abort(403);
        }

        $property->load(['media']);
        
        return view('agent.properties.show', compact('property'));
    }

    public function edit(Property $property)
    {
        if ($property->agent_id !== Auth::id()) {
            abort(403);
        }

        return view('agent.properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        if ($property->agent_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'property_type' => 'required|in:apartment,villa,house,land,commercial',
            'listing_type' => 'required|in:sale,rent',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|in:SAR,USD,EUR',
            'area' => 'required|numeric|min:0',
            'area_unit' => 'required|in:sq_m,sq_ft',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'floors' => 'nullable|integer|min:0',
            'year_built' => 'nullable|integer|min:1900|max:' . date('Y'),
            'status' => 'required|in:draft,active,inactive,sold,rented',
            'featured' => 'boolean',
            'premium' => 'boolean',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'amenities' => 'nullable|string',
            'nearby_places' => 'nullable|string',
            'virtual_tour_url' => 'nullable|url',
            'video_url' => 'nullable|url',
        ]);

        // Convert comma-separated strings to arrays
        if (!empty($validated['amenities'])) {
            $validated['amenities'] = array_map('trim', explode(',', $validated['amenities']));
        } else {
            $validated['amenities'] = [];
        }

        if (!empty($validated['nearby_places'])) {
            $validated['nearby_places'] = array_map('trim', explode(',', $validated['nearby_places']));
        } else {
            $validated['nearby_places'] = [];
        }

        $property->update($validated);

        // Handle images upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties/images', 'public');
                PropertyMedia::create([
                    'property_id' => $property->id,
                    'file_path' => $path,
                    'file_name' => $image->getClientOriginalName(),
                    'file_type' => $image->getMimeType(),
                    'file_size' => $image->getSize(),
                    'media_type' => 'image',
                    'is_primary' => $index === 0 && $property->images()->count() === 0,
                    'is_featured' => false,
                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        return redirect()
            ->route('agent.properties.show', $property)
            ->with('success', 'Property updated successfully!');
    }

    public function destroy(Property $property)
    {
        if ($property->agent_id !== Auth::id()) {
            abort(403);
        }

        // Delete associated media files
        foreach ($property->media as $media) {
            Storage::disk('public')->delete($media->file_path);
            $media->delete();
        }

        $property->delete();

        return redirect()
            ->route('agent.properties.index')
            ->with('success', 'Property deleted successfully!');
    }

    public function duplicate(Property $property)
    {
        if ($property->agent_id !== Auth::id()) {
            abort(403);
        }

        $newProperty = $property->replicate();
        $newProperty->title = $property->title . ' (Copy)';
        $newProperty->property_code = $this->generatePropertyCode();
        $newProperty->status = 'draft';
        $newProperty->views_count = 0;
        $newProperty->inquiries_count = 0;
        $newProperty->favorites_count = 0;
        $newProperty->save();

        return redirect()
            ->route('agent.properties.edit', $newProperty)
            ->with('success', 'Property duplicated successfully!');
    }

    public function publish(Property $property)
    {
        if ($property->agent_id !== Auth::id()) {
            abort(403);
        }

        $property->update(['status' => 'active']);

        return redirect()
            ->route('agent.properties.show', $property)
            ->with('success', 'Property published successfully!');
    }

    public function archive(Property $property)
    {
        if ($property->agent_id !== Auth::id()) {
            abort(403);
        }

        $property->update(['status' => 'inactive']);

        return redirect()
            ->route('agent.properties.show', $property)
            ->with('success', 'Property archived successfully!');
    }

    public function uploadImages(Request $request, Property $property)
    {
        if ($property->agent_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('properties/images', 'public');
            $media = PropertyMedia::create([
                'property_id' => $property->id,
                'file_path' => $path,
                'file_name' => $image->getClientOriginalName(),
                'file_type' => $image->getMimeType(),
                'file_size' => $image->getSize(),
                'media_type' => 'image',
                'uploaded_by' => Auth::id(),
            ]);

            $uploadedImages[] = [
                'id' => $media->id,
                'url' => Storage::url($path),
                'name' => $image->getClientOriginalName(),
            ];
        }

        return response()->json(['images' => $uploadedImages]);
    }

    public function deleteImage(Property $property, PropertyMedia $media)
    {
        if ($property->agent_id !== Auth::id() || $media->property_id !== $property->id) {
            abort(403);
        }

        Storage::disk('public')->delete($media->file_path);
        $media->delete();

        return response()->json(['success' => true]);
    }

    public function setPrimaryImage(Property $property, PropertyMedia $media)
    {
        if ($property->agent_id !== Auth::id() || $media->property_id !== $property->id) {
            abort(403);
        }

        // Remove primary status from all other images
        PropertyMedia::where('property_id', $property->id)
            ->where('media_type', 'image')
            ->update(['is_primary' => false]);

        // Set this image as primary
        $media->update(['is_primary' => true]);

        return response()->json(['success' => true]);
    }

    private function generatePropertyCode()
    {
        do {
            $code = 'PROP-' . strtoupper(uniqid());
        } while (Property::where('property_code', $code)->exists());

        return $code;
    }
}
