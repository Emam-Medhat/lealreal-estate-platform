<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyFeature;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropertyFeatureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index(Property $property)
    {
        $features = $property->features()
            ->withPivot('notes', 'custom_value')
            ->orderBy('name')
            ->get();

        $availableFeatures = PropertyFeature::active()
            ->whereNotIn('id', $property->features->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('properties.features.index', compact('property', 'features', 'availableFeatures'));
    }

    public function show(Property $property, PropertyFeature $feature)
    {
        return response()->json([
            'success' => true,
            'data' => $feature,
        ]);
    }

    public function attach(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'feature_id' => 'required|exists:property_features,id',
            'notes' => 'nullable|string|max:500',
            'custom_value' => 'nullable|string|max:255',
        ]);

        $feature = PropertyFeature::findOrFail($request->feature_id);

        // Check if already attached
        if ($property->features()->where('property_features.id', $feature->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Feature already attached to property',
            ]);
        }

        $property->features()->attach($feature->id, [
            'notes' => $request->notes,
            'custom_value' => $request->custom_value,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feature added successfully',
            'feature' => $feature,
        ]);
    }

    public function detach(Request $request, Property $property, PropertyFeature $feature): JsonResponse
    {
        $this->authorize('update', $property);

        $property->features()->detach($feature->id);

        return response()->json([
            'success' => true,
            'message' => 'Feature removed successfully',
        ]);
    }

    public function bulkAttach(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'feature_ids' => 'required|array',
            'feature_ids.*' => 'exists:property_features,id',
            'notes' => 'nullable|array',
            'custom_values' => 'nullable|array',
        ]);

        $featureIds = $request->feature_ids;
        $notes = $request->notes ?? [];
        $customValues = $request->custom_values ?? [];

        $attachedCount = 0;
        $errors = [];

        foreach ($featureIds as $index => $featureId) {
            try {
                // Check if already attached
                if ($property->features()->where('property_features.id', $featureId)->exists()) {
                    continue;
                }

                $property->features()->attach($featureId, [
                    'notes' => $notes[$index] ?? null,
                    'custom_value' => $customValues[$index] ?? null,
                ]);

                $attachedCount++;

            } catch (\Exception $e) {
                $errors[] = "Failed to attach feature {$featureId}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => $attachedCount > 0,
            'message' => $attachedCount . ' features added successfully',
            'attached_count' => $attachedCount,
            'errors' => $errors,
        ]);
    }

    public function bulkDetach(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'feature_ids' => 'required|array',
            'feature_ids.*' => 'exists:property_features,id',
        ]);

        $detachedCount = $property->features()->whereIn('property_features.id', $request->feature_ids)->detach();

        return response()->json([
            'success' => $detachedCount > 0,
            'message' => $detachedCount . ' features removed successfully',
            'detached_count' => $detachedCount,
        ]);
    }

    public function updatePivot(Request $request, Property $property, PropertyFeature $feature): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'notes' => 'nullable|string|max:500',
            'custom_value' => 'nullable|string|max:255',
        ]);

        $property->features()->updateExistingPivot($feature->id, [
            'notes' => $request->notes,
            'custom_value' => $request->custom_value,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feature details updated successfully',
        ]);
    }

    public function getFeaturesByCategory(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'required|string',
        ]);

        $features = PropertyFeature::where('category', $request->category)
            ->active()
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $features,
        ]);
    }

    public function searchFeatures(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $features = PropertyFeature::where('name', 'like', '%' . $request->query . '%')
            ->orWhere('description', 'like', '%' . $request->query . '%')
            ->active()
            ->ordered()
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $features,
        ]);
    }

    public function getPremiumFeatures(Request $request): JsonResponse
    {
        $features = PropertyFeature::where('is_premium', true)
            ->where('is_active', true)
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $features,
        ]);
    }

    public function getPopularFeatures(Request $request): JsonResponse
    {
        $limit = $request->limit ?? 20;

        $popularFeatures = PropertyFeature::withCount(['properties' => function($query) {
                $query->where('status', 'active');
            }])
            ->where('is_active', true)
            ->orderBy('properties_count', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $popularFeatures,
        ]);
    }

    public function getFeatureStats(Property $property): JsonResponse
    {
        $this->authorize('viewStats', $property);

        $features = $property->features;
        
        $stats = [
            'total_features' => $features->count(),
            'premium_features' => $features->where('is_premium', true)->count(),
            'standard_features' => $features->where('is_premium', false)->count(),
            'by_category' => $features->groupBy('category')->map->count(),
            'by_type' => $features->groupBy('type')->map->count(),
            'custom_features' => $features->where('is_custom', true)->count(),
            'features_with_notes' => $features->whereNotNull('pivot.notes')->count(),
            'features_with_custom_values' => $features->whereNotNull('pivot.custom_value')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    public function compareFeatures(Request $request): JsonResponse
    {
        $request->validate([
            'property_ids' => 'required|array|min:2|max:5',
            'property_ids.*' => 'exists:properties,id',
        ]);

        $properties = Property::with(['features' => function($query) {
                $query->orderBy('name');
            }])
            ->whereIn('id', $request->property_ids)
            ->where('status', 'active')
            ->get();

        // Get all unique features across all properties
        $allFeatures = $properties->flatMap->features->unique('id');

        // Create comparison matrix
        $comparison = $allFeatures->map(function($feature) use ($properties) {
            return [
                'feature' => $feature,
                'properties' => $properties->map(function($property) use ($feature) {
                    $hasFeature = $property->features->contains($feature);
                    $pivot = $property->features->find($feature->id)?->pivot;
                    
                    return [
                        'has_feature' => $hasFeature,
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

    public function suggestFeatures(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $suggestions = $this->generateFeatureSuggestions($property);

        return response()->json([
            'success' => true,
            'data' => $suggestions,
        ]);
    }

    public function exportFeatures(Property $property): JsonResponse
    {
        $this->authorize('view', $property);

        $features = $property->features()
            ->withPivot('notes', 'custom_value')
            ->orderBy('name')
            ->get();

        $exportData = $features->map(function($feature) {
            return [
                'name' => $feature->name,
                'category' => $feature->category,
                'type' => $feature->type,
                'description' => $feature->description,
                'icon' => $feature->icon,
                'is_premium' => $feature->is_premium,
                'notes' => $feature->pivot->notes,
                'custom_value' => $feature->pivot->custom_value,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $exportData,
        ]);
    }

    private function generateFeatureSuggestions(Property $property): array
    {
        $suggestions = [];
        $propertyType = $property->propertyType;
        $location = $property->location;

        // Suggestions based on property type
        if ($propertyType) {
            $typeSuggestions = PropertyFeature::where('category', $propertyType->name)
                ->whereNotIn('id', $property->features->pluck('id'))
                ->active()
                ->limit(5)
                ->get();

            $suggestions[] = [
                'type' => 'property_type',
                'title' => 'Based on Property Type',
                'features' => $typeSuggestions,
            ];
        }

        // Suggestions based on property premium status
        if ($property->premium) {
            $premiumSuggestions = PropertyFeature::where('is_premium', true)
                ->whereNotIn('id', $property->features->pluck('id'))
                ->active()
                ->limit(5)
                ->get();

            $suggestions[] = [
                'type' => 'premium',
                'title' => 'Premium Features',
                'features' => $premiumSuggestions,
            ];
        }

        // Popular features in the area
        $popularFeatures = PropertyFeature::withCount(['properties' => function($query) use ($location) {
                if ($location) {
                    $query->whereHas('location', function($q) use ($location) {
                        $q->where('city', $location->city);
                    })->where('status', 'active');
                }
            }])
            ->whereNotIn('id', $property->features->pluck('id'))
            ->wherewhere('is_active', true)
            ->orderBy('properties_count', 'desc')
            ->limit(10)
            ->get();

        if ($popularFeatures->isNotEmpty()) {
            $suggestions[] = [
                'type' => 'popular',
                'title' => 'Popular in Your Area',
                'features' => $popularFeatures,
            ];
        }

        return $suggestions;
    }
}
