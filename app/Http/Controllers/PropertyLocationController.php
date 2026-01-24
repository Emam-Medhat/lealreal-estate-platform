<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyLocation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropertyLocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show', 'nearby', 'map']);
    }

    public function index(Property $property)
    {
        $location = $property->location;
        $nearbyProperties = $this->getNearbyProperties($property, 5); // 5km radius

        return view('properties.location.index', compact('property', 'location', 'nearbyProperties'));
    }

    public function show(Property $property, PropertyLocation $location)
    {
        return response()->json([
            'success' => true,
            'data' => $location,
        ]);
    }

    public function store(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'neighborhood' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'coordinates' => 'nullable|array',
            'nearby_landmarks' => 'nullable|array',
            'transportation' => 'nullable|array',
        ]);

        $location = PropertyLocation::updateOrCreate(
            ['property_id' => $property->id],
            $request->only([
                'address', 'city', 'state', 'country', 'postal_code',
                'latitude', 'longitude', 'neighborhood', 'district',
                'coordinates', 'nearby_landmarks', 'transportation'
            ])
        );

        return response()->json([
            'success' => true,
            'message' => 'Location saved successfully',
            'location' => $location,
        ]);
    }

    public function update(Request $request, Property $property, PropertyLocation $location): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'neighborhood' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'coordinates' => 'nullable|array',
            'nearby_landmarks' => 'nullable|array',
            'transportation' => 'nullable|array',
        ]);

        $location->update($request->only([
            'address', 'city', 'state', 'country', 'postal_code',
            'latitude', 'longitude', 'neighborhood', 'district',
            'coordinates', 'nearby_landmarks', 'transportation'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'location' => $location,
        ]);
    }

    public function nearby(Request $request, Property $property): JsonResponse
    {
        $request->validate([
            'radius' => 'nullable|numeric|min:0.5|max:50',
            'property_type' => 'nullable|exists:property_types,id',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $radius = $request->radius ?? 5; // Default 5km
        $limit = $request->limit ?? 20;

        $nearbyProperties = $this->getNearbyProperties($property, $radius, $request->property_type, $limit);

        return response()->json([
            'success' => true,
            'data' => [
                'nearby_properties' => $nearbyProperties,
                'radius' => $radius,
                'total_count' => $nearbyProperties->count(),
            ],
        ]);
    }

    public function map(Request $request, Property $property): JsonResponse
    {
        $bounds = $request->only(['north', 'south', 'east', 'west']);
        
        if (!$property->location || !$property->location->latitude || !$property->location->longitude) {
            return response()->json([
                'success' => false,
                'message' => 'Property location not available',
            ]);
        }

        $mapData = [
            'center' => [
                'lat' => (float) $property->location->latitude,
                'lng' => (float) $property->location->longitude,
            ],
            'property' => [
                'id' => $property->id,
                'title' => $property->title,
                'price' => $property->price->formatted_price,
                'position' => [
                    'lat' => (float) $property->location->latitude,
                    'lng' => (float) $property->location->longitude,
                ],
                'image' => $property->media->first()?->getUrlAttribute(),
                'url' => route('properties.show', $property),
            ],
        ];

        // Get nearby properties for map
        if (isset($bounds['north'])) {
            $nearbyProperties = Property::where('id', '!=', $property->id)
                ->where('status', 'active')
                ->whereHas('location', function($query) use ($bounds) {
                    $query->whereBetween('latitude', [$bounds['south'], $bounds['north']])
                          ->whereBetween('longitude', [$bounds['west'], $bounds['east']]);
                })
                ->with(['location', 'price', 'media' => function($query) {
                    $query->where('media_type', 'image')->limit(1);
                }])
                ->get();

            $mapData['nearby_properties'] = $nearbyProperties->map(function($prop) {
                return [
                    'id' => $prop->id,
                    'title' => $prop->title,
                    'price' => $prop->price->formatted_price,
                    'position' => [
                        'lat' => (float) $prop->location->latitude,
                        'lng' => (float) $prop->location->longitude,
                    ],
                    'image' => $prop->media->first()?->getUrlAttribute(),
                    'url' => route('properties.show', $prop),
                ];
            });
        }

        return response()->json([
            'success' => true,
            'data' => $mapData,
        ]);
    }

    public function geocode(Request $request): JsonResponse
    {
        $request->validate([
            'address' => 'required|string|max:500',
        ]);

        $address = $request->address;
        
        // This would use a geocoding service like Google Maps API
        // For now, return mock data
        $geocodedData = [
            'address' => $address,
            'latitude' => 24.7136, // Riyadh coordinates
            'longitude' => 46.6753,
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
            'formatted_address' => $address . ', Riyadh, Saudi Arabia',
        ];

        return response()->json([
            'success' => true,
            'data' => $geocodedData,
        ]);
    }

    public function reverseGeocode(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $lat = $request->latitude;
        $lng = $request->longitude;
        
        // This would use a reverse geocoding service
        // For now, return mock data
        $addressData = [
            'address' => '123 Main Street',
            'city' => 'Riyadh',
            'state' => 'Riyadh Province',
            'country' => 'Saudi Arabia',
            'postal_code' => '12345',
            'neighborhood' => 'Al Olaya',
            'district' => 'Al Olaya District',
            'formatted_address' => '123 Main Street, Al Olaya, Riyadh, Saudi Arabia',
        ];

        return response()->json([
            'success' => true,
            'data' => $addressData,
        ]);
    }

    public function calculateDistance(Request $request): JsonResponse
    {
        $request->validate([
            'from_lat' => 'required|numeric|between:-90,90',
            'from_lng' => 'required|numeric|between:-180,180',
            'to_lat' => 'required|numeric|between:-90,90',
            'to_lng' => 'required|numeric|between:-180,180',
            'unit' => 'nullable|in:km,miles',
        ]);

        $unit = $request->unit ?? 'km';
        
        $distance = $this->calculateDistanceBetweenPoints(
            $request->from_lat,
            $request->from_lng,
            $request->to_lat,
            $request->to_lng,
            $unit
        );

        return response()->json([
            'success' => true,
            'data' => [
                'distance' => $distance,
                'unit' => $unit,
            ],
        ]);
    }

    public function getLocationStats(Property $property): JsonResponse
    {
        $this->authorize('viewStats', $property);

        $location = $property->location;
        
        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'No location data available',
            ]);
        }

        $stats = [
            'nearby_properties_count' => $this->getNearbyProperties($property, 5)->count(),
            'properties_in_city' => Property::whereHas('location', function($query) use ($location) {
                $query->where('city', $location->city);
            })->where('status', 'active')->count(),
            'average_price_in_area' => $this->getAveragePriceInArea($location),
            'price_comparison' => $this->comparePriceToArea($property, $location),
            'transportation_options' => $location->transportation ?? [],
            'nearby_landmarks' => $location->nearby_landmarks ?? [],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    private function getNearbyProperties(Property $property, float $radius, ?int $propertyTypeId = null, int $limit = 20)
    {
        if (!$property->location || !$property->location->latitude || !$property->location->longitude) {
            return collect();
        }

        $query = Property::where('id', '!=', $property->id)
            ->where('status', 'active')
            ->whereHas('location', function($query) use ($property, $radius) {
                $query->selectRaw(
                    "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
                    [$property->location->latitude, $property->location->longitude, $property->location->latitude]
                )->having('distance', '<=', $radius);
            });

        if ($propertyTypeId) {
            $query->where('property_type_id', $propertyTypeId);
        }

        return $query->with([
            'propertyType',
            'location',
            'price',
            'media' => function($query) {
                $query->where('media_type', 'image')->limit(1);
            }
        ])->limit($limit)->get();
    }

    private function calculateDistanceBetweenPoints(float $lat1, float $lng1, float $lat2, float $lng2, string $unit = 'km'): float
    {
        $earthRadius = $unit === 'km' ? 6371 : 3959; // Earth's radius in km or miles

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function getAveragePriceInArea(PropertyLocation $location): ?float
    {
        $nearbyProperties = Property::whereHas('location', function($query) use ($location) {
            $query->where('city', $location->city);
        })->where('status', 'active')->with('price')->get();

        if ($nearbyProperties->isEmpty()) {
            return null;
        }

        return $nearbyProperties->avg('price.price');
    }

    private function comparePriceToArea(Property $property, PropertyLocation $location): array
    {
        $averagePrice = $this->getAveragePriceInArea($location);
        $currentPrice = $property->price->price;

        if (!$averagePrice) {
            return [
                'comparison' => 'no_data',
                'difference_percentage' => 0,
                'message' => 'No comparable properties in the area',
            ];
        }

        $difference = (($currentPrice - $averagePrice) / $averagePrice) * 100;

        if ($difference > 10) {
            $comparison = 'higher';
            $message = 'Property is priced ' . round($difference, 1) . '% above area average';
        } elseif ($difference < -10) {
            $comparison = 'lower';
            $message = 'Property is priced ' . round(abs($difference), 1) . '% below area average';
        } else {
            $comparison = 'similar';
            $message = 'Property is priced similarly to area average';
        }

        return [
            'comparison' => $comparison,
            'difference_percentage' => $difference,
            'area_average' => $averagePrice,
            'property_price' => $currentPrice,
            'message' => $message,
        ];
    }
}
