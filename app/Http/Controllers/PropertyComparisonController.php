<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;

class PropertyComparisonController extends Controller
{
    public function index()
    {
        $propertyIds = Session::get('property_comparison', []);
        
        if (empty($propertyIds)) {
            return view('properties.comparison', [
                'properties' => collect(),
                'comparisonData' => collect(),
            ]);
        }

        $properties = Property::with([
            'propertyType',
            'location',
            'details',
            'price',
            'media' => function($query) {
                $query->where('media_type', 'image')->limit(3);
            },
            'amenities',
            'features',
            'agent'
        ])
        ->whereIn('id', $propertyIds)
        ->where('status', 'active')
        ->get();

        // Prepare comparison data
        $comparisonData = $this->prepareComparisonData($properties);

        return view('properties.comparison', compact('properties', 'comparisonData'));
    }

    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
        ]);

        $propertyId = $request->property_id;
        $comparison = Session::get('property_comparison', []);

        // Check if property exists and is active
        $property = Property::where('id', $propertyId)
            ->where('status', 'active')
            ->first();

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found or not available',
            ]);
        }

        // Add to comparison (max 4 properties)
        if (!in_array($propertyId, $comparison)) {
            if (count($comparison) >= 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can compare maximum 4 properties at a time',
                ]);
            }
            
            $comparison[] = $propertyId;
            Session::put('property_comparison', $comparison);
        }

        return response()->json([
            'success' => true,
            'message' => 'Property added to comparison',
            'comparison_count' => count($comparison),
            'property' => [
                'id' => $property->id,
                'title' => $property->title,
                'price' => $property->price->formatted_price,
                'image' => $property->media->first()?->getUrlAttribute(),
            ]
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
        ]);

        $propertyId = $request->property_id;
        $comparison = Session::get('property_comparison', []);

        // Remove from comparison
        $comparison = array_filter($comparison, function($id) use ($propertyId) {
            return $id != $propertyId;
        });

        Session::put('property_comparison', array_values($comparison));

        return response()->json([
            'success' => true,
            'message' => 'Property removed from comparison',
            'comparison_count' => count($comparison),
        ]);
    }

    public function clear(): JsonResponse
    {
        Session::forget('property_comparison');

        return response()->json([
            'success' => true,
            'message' => 'Comparison cleared',
        ]);
    }

    public function compare(Request $request): JsonResponse
    {
        $propertyIds = $request->input('properties', []);

        if (count($propertyIds) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least 2 properties to compare',
            ]);
        }

        if (count($propertyIds) > 4) {
            return response()->json([
                'success' => false,
                'message' => 'You can compare maximum 4 properties at a time',
            ]);
        }

        $properties = Property::with([
            'propertyType',
            'location',
            'details',
            'price',
            'media' => function($query) {
                $query->where('media_type', 'image');
            },
            'amenities',
            'features',
            'documents',
            'virtualTours',
            'floorPlans',
            'agent'
        ])
        ->whereIn('id', $propertyIds)
        ->where('status', 'active')
        ->get();

        if ($properties->count() < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Some properties are not available for comparison',
            ]);
        }

        $comparisonData = $this->prepareComparisonData($properties);

        return response()->json([
            'success' => true,
            'data' => [
                'properties' => $properties,
                'comparison' => $comparisonData,
            ]
        ]);
    }

    public function getComparisonData(): JsonResponse
    {
        $propertyIds = Session::get('property_comparison', []);

        if (empty($propertyIds)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'properties' => [],
                    'comparison_count' => 0,
                ]
            ]);
        }

        $properties = Property::with([
            'propertyType',
            'location',
            'price',
            'media' => function($query) {
                $query->where('media_type', 'image')->limit(1);
            }
        ])
        ->whereIn('id', $propertyIds)
        ->where('status', 'active')
        ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'properties' => $properties->map(function($property) {
                    return [
                        'id' => $property->id,
                        'title' => $property->title,
                        'price' => $property->price->formatted_price,
                        'location' => $property->location->city . ', ' . $property->location->country,
                        'property_type' => $property->propertyType->name,
                        'image' => $property->media->first()?->getUrlAttribute(),
                        'url' => route('properties.show', $property),
                    ];
                }),
                'comparison_count' => $properties->count(),
            ]
        ]);
    }

    public function exportComparison(Request $request)
    {
        $propertyIds = Session::get('property_comparison', []);

        if (empty($propertyIds)) {
            return back()->with('error', 'No properties in comparison');
        }

        $properties = Property::with([
            'propertyType',
            'location',
            'details',
            'price',
            'amenities',
            'features',
            'agent'
        ])
        ->whereIn('id', $propertyIds)
        ->where('status', 'active')
        ->get();

        $comparisonData = $this->prepareComparisonData($properties);

        // Generate PDF or Excel export
        // This is a placeholder - you would implement actual export logic
        
        return response()->json([
            'success' => true,
            'message' => 'Comparison exported successfully',
            'data' => $comparisonData,
        ]);
    }

    private function prepareComparisonData($properties)
    {
        $comparison = collect();

        // Basic Information
        $comparison->put('basic_info', [
            'title' => $properties->pluck('title'),
            'property_code' => $properties->pluck('property_code'),
            'property_type' => $properties->pluck('propertyType.name'),
            'listing_type' => $properties->pluck('listing_type'),
            'status' => $properties->pluck('status'),
            'featured' => $properties->pluck('featured'),
            'premium' => $properties->pluck('premium'),
        ]);

        // Price Information
        $comparison->put('price_info', [
            'price' => $properties->map(function($prop) {
                return $prop->price ? $prop->price->formatted_price : number_format($prop->price ?? 0, 2) . ' ' . ($prop->currency ?? 'USD');
            }),
            'price_per_sqm' => $properties->map(function($prop) {
                return $prop->price ? $prop->price->formatted_price_per_sqm : null;
            }),
            'currency' => $properties->map(function($prop) {
                return $prop->price ? $prop->price->currency : ($prop->currency ?? 'USD');
            }),
            'negotiable' => $properties->map(function($prop) {
                return $prop->price ? $prop->price->is_negotiable : false;
            }),
            'includes_vat' => $properties->map(function($prop) {
                return $prop->price ? $prop->price->includes_vat : false;
            }),
        ]);

        // Property Details
        $comparison->put('property_details', [
            'bedrooms' => $properties->pluck('details.bedrooms'),
            'bathrooms' => $properties->pluck('details.bathrooms'),
            'floors' => $properties->pluck('details.floors'),
            'parking_spaces' => $properties->pluck('details.parking_spaces'),
            'year_built' => $properties->pluck('details.year_built'),
            'area' => $properties->pluck('details.formatted_area'),
            'land_area' => $properties->pluck('details.formatted_land_area'),
        ]);

        // Location Information
        $comparison->put('location_info', [
            'address' => $properties->pluck('location.full_address'),
            'city' => $properties->pluck('location.city'),
            'country' => $properties->pluck('location.country'),
            'neighborhood' => $properties->pluck('location.neighborhood'),
            'coordinates' => $properties->pluck('location.coordinates_array'),
        ]);

        // Amenities Comparison
        $allAmenities = $properties->flatMap->amenities->unique('id');
        $amenitiesMatrix = $allAmenities->map(function($amenity) use ($properties) {
            return [
                'name' => $amenity->name,
                'icon' => $amenity->icon,
                'properties' => $properties->map(function($property) use ($amenity) {
                    return $property->amenities->contains($amenity);
                }),
            ];
        });
        $comparison->put('amenities', $amenitiesMatrix);

        // Features Comparison
        $allFeatures = $properties->flatMap->features->unique('id');
        $featuresMatrix = $allFeatures->map(function($feature) use ($properties) {
            return [
                'name' => $feature->name,
                'icon' => $feature->icon,
                'premium' => $feature->is_premium,
                'properties' => $properties->map(function($property) use ($feature) {
                    return $property->features->contains($feature);
                }),
            ];
        });
        $comparison->put('features', $featuresMatrix);

        // Media Information
        $comparison->put('media_info', [
            'image_count' => $properties->map(function($property) {
                return $property->media->where('media_type', 'image')->count();
            }),
            'video_count' => $properties->map(function($property) {
                return $property->media->where('media_type', 'video')->count();
            }),
            'document_count' => $properties->map(function($property) {
                return $property->documents->count();
            }),
            'virtual_tour_count' => $properties->map(function($property) {
                return $property->virtualTours->count();
            }),
            'floor_plan_count' => $properties->map(function($property) {
                return $property->floorPlans->count();
            }),
        ]);

        // Agent Information
        $comparison->put('agent_info', [
            'agent_name' => $properties->pluck('agent.name'),
            'agent_email' => $properties->pluck('agent.email'),
            'agent_phone' => $properties->pluck('agent.phone'),
        ]);

        // Engagement Metrics
        $comparison->put('engagement', [
            'views_count' => $properties->pluck('views_count'),
            'favorites_count' => $properties->pluck('favorites_count'),
            'inquiries_count' => $properties->pluck('inquiries_count'),
            'days_on_market' => $properties->map(function($property) {
                return $property->created_at->diffInDays(now());
            }),
        ]);

        return $comparison;
    }
}
