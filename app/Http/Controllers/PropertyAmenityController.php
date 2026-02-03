<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyAmenity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropertyAmenityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index(Property $property)
    {
        $amenities = $property->amenities()
            ->withPivot('notes', 'custom_value')
            ->orderBy('name')
            ->get();

        $availableAmenities = PropertyAmenity::active()
            ->whereNotIn('id', $property->amenities->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('properties.amenities.index', compact('property', 'amenities', 'availableAmenities'));
    }

    public function show(Property $property, PropertyAmenity $amenity)
    {
        return response()->json([
            'success' => true,
            'data' => $amenity,
        ]);
    }

    public function attach(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'amenity_id' => 'required|exists:property_amenities,id',
            'notes' => 'nullable|string|max:500',
            'custom_value' => 'nullable|string|max:255',
        ]);

        $amenity = PropertyAmenity::findOrFail($request->amenity_id);

        // Check if already attached
        if ($property->amenities()->where('property_amenities.id', $amenity->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Amenity already attached to property',
            ]);
        }

        $property->amenities()->attach($amenity->id, [
            'notes' => $request->notes,
            'custom_value' => $request->custom_value,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Amenity added successfully',
            'amenity' => $amenity,
        ]);
    }

    public function detach(Request $request, Property $property, PropertyAmenity $amenity): JsonResponse
    {
        $this->authorize('update', $property);

        $property->amenities()->detach($amenity->id);

        return response()->json([
            'success' => true,
            'message' => 'Amenity removed successfully',
        ]);
    }

    public function bulkAttach(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'amenity_ids' => 'required|array',
            'amenity_ids.*' => 'exists:property_amenities,id',
            'notes' => 'nullable|array',
            'custom_values' => 'nullable|array',
        ]);

        $amenityIds = $request->amenity_ids;
        $notes = $request->notes ?? [];
        $customValues = $request->custom_values ?? [];

        $attachedCount = 0;
        $errors = [];

        foreach ($amenityIds as $index => $amenityId) {
            try {
                // Check if already attached
                if ($property->amenities()->where('property_amenities.id', $amenityId)->exists()) {
                    continue;
                }

                $property->amenities()->attach($amenityId, [
                    'notes' => $notes[$index] ?? null,
                    'custom_value' => $customValues[$index] ?? null,
                ]);

                $attachedCount++;

            } catch (\Exception $e) {
                $errors[] = "Failed to attach amenity {$amenityId}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => $attachedCount > 0,
            'message' => $attachedCount . ' amenities added successfully',
            'attached_count' => $attachedCount,
            'errors' => $errors,
        ]);
    }

    public function bulkDetach(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'amenity_ids' => 'required|array',
            'amenity_ids.*' => 'exists:property_amenities,id',
        ]);

        $detachedCount = $property->amenities()->whereIn('property_amenities.id', $request->amenity_ids)->detach();

        return response()->json([
            'success' => $detachedCount > 0,
            'message' => $detachedCount . ' amenities removed successfully',
            'detached_count' => $detachedCount,
        ]);
    }

    public function updatePivot(Request $request, Property $property, PropertyAmenity $amenity): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'notes' => 'nullable|string|max:500',
            'custom_value' => 'nullable|string|max:255',
        ]);

        $property->amenities()->updateExistingPivot($amenity->id, [
            'notes' => $request->notes,
            'custom_value' => $request->custom_value,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Amenity details updated successfully',
        ]);
    }

    public function getAmenitiesByCategory(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'required|string',
        ]);

        $amenities = PropertyAmenity::where('category', $request->category)
            ->active()
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $amenities,
        ]);
    }

    public function searchAmenities(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'nullable|string|min:2',
        ]);

        $query = $request->query('query');
        
        if (empty($query)) {
            // If no query provided, return popular or recent amenities
            $amenities = PropertyAmenity::active()
                ->ordered()
                ->limit(20)
                ->get();
        } else {
            // Search with query
            $amenities = PropertyAmenity::where('name', 'like', '%' . $query . '%')
                ->orWhere('description', 'like', '%' . $query . '%')
                ->active()
                ->ordered()
                ->limit(20)
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $amenities,
            'query' => $query,
        ]);
    }

    public function getPopularAmenities(Request $request): JsonResponse
    {
        $limit = $request->limit ?? 20;

        $popularAmenities = PropertyAmenity::withCount(['properties' => function($query) {
                $query->where('status', 'active');
            }])
            ->where('is_active', true)
            ->orderBy('properties_count', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $popularAmenities,
        ]);
    }

    public function getAmenityStats(Property $property): JsonResponse
    {
        $this->authorize('viewStats', $property);

        $amenities = $property->amenities;
        
        $stats = [
            'total_amenities' => $amenities->count(),
            'by_category' => $amenities->groupBy('category')->map->count(),
            'by_type' => $amenities->groupBy('type')->map->count(),
            'custom_amenities' => $amenities->where('is_custom', true)->count(),
            'standard_amenities' => $amenities->where('is_custom', false)->count(),
            'amenities_with_notes' => $amenities->whereNotNull('pivot.notes')->count(),
            'amenities_with_custom_values' => $amenities->whereNotNull('pivot.custom_value')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    public function compareAmenities(Request $request): JsonResponse
    {
        $request->validate([
            'property_ids' => 'required|array|min:2|max:5',
            'property_ids.*' => 'exists:properties,id',
        ]);

        $properties = Property::with(['amenities' => function($query) {
                $query->orderBy('name');
            }])
            ->whereIn('id', $request->property_ids)
            ->where('status', 'active')
            ->get();

        // Get all unique amenities across all properties
        $allAmenities = $properties->flatMap->amenities->unique('id');

        // Create comparison matrix
        $comparison = $allAmenities->map(function($amenity) use ($properties) {
            return [
                'amenity' => $amenity,
                'properties' => $properties->map(function($property) use ($amenity) {
                    $hasAmenity = $property->amenities->contains($amenity);
                    $pivot = $property->amenities->find($amenity->id)?->pivot;
                    
                    return [
                        'has_amenity' => $hasAmenity,
                        'notes' => $pivot?->notes,
                        'custom_value' => $pivot?->custom_value,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'properties' => $properties,
                'comparison' => $comparison,
            ],
        ]);
    }

    public function suggestAmenities(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $suggestions = $this->generateAmenitySuggestions($property);

        return response()->json([
            'success' => true,
            'data' => $suggestions,
        ]);
    }

    public function exportAmenities(Property $property): JsonResponse
    {
        $this->authorize('view', $property);

        $amenities = $property->amenities()
            ->withPivot('notes', 'custom_value')
            ->orderBy('name')
            ->get();

        $exportData = $amenities->map(function($amenity) {
            return [
                'name' => $amenity->name,
                'category' => $amenity->category,
                'type' => $amenity->type,
                'description' => $amenity->description,
                'icon' => $amenity->icon,
                'notes' => $amenity->pivot->notes,
                'custom_value' => $amenity->pivot->custom_value,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $exportData,
        ]);
    }

    private function generateAmenitySuggestions(Property $property): array
    {
        $suggestions = [];
        $propertyType = $property->propertyType;
        $location = $property->location;

        // Suggestions based on property type
        if ($propertyType) {
            $typeSuggestions = PropertyAmenity::where('category', $propertyType->name)
                ->whereNotIn('id', $property->amenities->pluck('id'))
                ->active()
                ->limit(5)
                ->get();

            $suggestions[] = [
                'type' => 'property_type',
                'title' => 'Based on Property Type',
                'amenities' => $typeSuggestions,
            ];
        }

        // Suggestions based on location
        if ($location) {
            $locationSuggestions = PropertyAmenity::where('category', 'location')
                ->whereNotIn('id', $property->amenities->pluck('id'))
                ->active()
                ->limit(5)
                ->get();

            $suggestions[] = [
                'type' => 'location',
                'title' => 'Based on Location',
                'amenities' => $locationSuggestions,
            ];
        }

        // Popular amenities in the area
        $popularAmenities = PropertyAmenity::withCount(['properties' => function($query) use ($location) {
                if ($location) {
                    $query->whereHas('location', function($q) use ($location) {
                        $q->where('city', $location->city);
                    })->where('status', 'active');
                }
            }])
            ->whereNotIn('id', $property->amenities->pluck('id'))
            ->where('is_active', true)
            ->orderBy('properties_count', 'desc')
            ->limit(10)
            ->get();

        if ($popularAmenities->isNotEmpty()) {
            $suggestions[] = [
                'type' => 'popular',
                'title' => 'Popular in Your Area',
                'amenities' => $popularAmenities,
            ];
        }

        return $suggestions;
    }
}
