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
use App\Services\AgentPropertyService;

class PropertyController extends Controller
{
    protected $agentPropertyService;

    public function __construct(AgentPropertyService $agentPropertyService)
    {
        $this->middleware('auth');
        $this->middleware('agent');
        $this->agentPropertyService = $agentPropertyService;
    }

    public function index(Request $request)
    {
        $agent = Auth::user();
        
        $properties = $this->agentPropertyService->getAgentProperties($agent, $request);

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

        $property = $this->agentPropertyService->createProperty($agent, $validated);

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

        $this->agentPropertyService->updateProperty($property, $validated);

        // Handle images upload
        if ($request->hasFile('images')) {
            $this->agentPropertyService->uploadPropertyImages($property, $request->file('images'));
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

        $this->agentPropertyService->deleteProperty($property);

        return redirect()
            ->route('agent.properties.index')
            ->with('success', 'Property deleted successfully!');
    }

    public function duplicate(Property $property)
    {
        if ($property->agent_id !== Auth::id()) {
            abort(403);
        }

        $newProperty = $this->agentPropertyService->duplicateProperty($property);

        return redirect()
            ->route('agent.properties.edit', $newProperty)
            ->with('success', 'Property duplicated successfully!');
    }

    public function publish(Property $property)
    {
        if ($property->agent_id !== Auth::id()) {
            abort(403);
        }

        $this->agentPropertyService->publishProperty($property);

        return redirect()
            ->route('agent.properties.show', $property)
            ->with('success', 'Property published successfully!');
    }

    public function archive(Property $property)
    {
        if ($property->agent_id !== Auth::id()) {
            abort(403);
        }

        $this->agentPropertyService->archiveProperty($property);

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

        $uploadedImages = $this->agentPropertyService->uploadPropertyImages($property, $request->file('images'));

        return response()->json(['images' => $uploadedImages]);
    }

    public function deleteImage(Property $property, PropertyMedia $media)
    {
        if ($property->agent_id !== Auth::id() || $media->property_id !== $property->id) {
            abort(403);
        }

        $this->agentPropertyService->deletePropertyImage($media);

        return response()->json(['success' => true]);
    }

    public function setPrimaryImage(Property $property, PropertyMedia $media)
    {
        if ($property->agent_id !== Auth::id() || $media->property_id !== $property->id) {
            abort(403);
        }

        $this->agentPropertyService->setPrimaryPropertyImage($property, $media);

        return response()->json(['success' => true]);
    }


}
