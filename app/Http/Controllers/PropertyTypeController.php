<?php

namespace App\Http\Controllers;

use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropertyTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index()
    {
        // Get property types with count using subquery for performance (avoids N+1)
        $propertyTypes = PropertyType::active()
            ->ordered()
            ->addSelect(['properties_count' => \App\Models\Property::selectRaw('count(*)')
                ->whereColumn('property_type', 'property_types.name')
                ->where('status', 'active')
            ])
            ->get();

        return view('property-types.index', compact('propertyTypes'));
    }

    public function create()
    {
        return view('property-types.create');
    }

    public function show(PropertyType $propertyType)
    {
        $propertyType->load([
            'properties' => function($query) {
                $query->where('status', 'active')
                      ->with(['media' => function($query) {
                          $query->where('media_type', 'image')->limit(3);
                      }, 'price', 'location'])
                      ->latest()
                      ->limit(12);
            }
        ]);

        return view('property-types.show', compact('propertyType'));
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', PropertyType::class);

        $request->validate([
            'name' => 'required|string|max:255|unique:property_types',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $propertyType = PropertyType::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Property type created successfully',
            'property_type' => $propertyType,
        ]);
    }

    public function update(Request $request, PropertyType $propertyType): JsonResponse
    {
        $this->authorize('update', $propertyType);

        $request->validate([
            'name' => 'required|string|max:255|unique:property_types,name,' . $propertyType->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $propertyType->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Property type updated successfully',
            'property_type' => $propertyType,
        ]);
    }

    public function destroy(PropertyType $propertyType): JsonResponse
    {
        $this->authorize('delete', $propertyType);

        // Check if property type has properties
        if ($propertyType->properties()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete property type with associated properties',
            ]);
        }

        $propertyType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Property type deleted successfully',
        ]);
    }

    public function getProperties(PropertyType $propertyType, Request $request): JsonResponse
    {
        $query = $propertyType->properties()
            ->where('status', 'active')
            ->with([
                'media' => function($query) {
                    $query->where('media_type', 'image')->limit(3);
                },
                'price',
                'location'
            ]);

        // Apply filters
        if ($request->min_price) {
            $query->whereHas('price', function($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            });
        }

        if ($request->max_price) {
            $query->whereHas('price', function($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
            });
        }

        if ($request->city) {
            $query->whereHas('location', function($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        if ($request->bedrooms) {
            $query->whereHas('details', function($q) use ($request) {
                $q->where('bedrooms', '>=', $request->bedrooms);
            });
        }

        // Sort
        $sort = $request->sort ?? 'created_at';
        $order = $request->order ?? 'desc';
        
        switch ($sort) {
            case 'price_low':
                $query->join('property_prices', 'properties.id', '=', 'property_prices.property_id')
                      ->orderBy('property_prices.price', 'asc');
                break;
            case 'price_high':
                $query->join('property_prices', 'properties.id', '=', 'property_prices.property_id')
                      ->orderBy('property_prices.price', 'desc');
                break;
            case 'area':
                $query->join('property_details', 'properties.id', '=', 'property_details.property_id')
                      ->orderBy('property_details.area', 'desc');
                break;
            default:
                $query->orderBy($sort, $order);
        }

        $properties = $query->paginate($request->per_page ?? 12);

        return response()->json([
            'success' => true,
            'data' => $properties,
        ]);
    }

    public function getStats(PropertyType $propertyType): JsonResponse
    {
        $stats = [
            'total_properties' => $propertyType->properties()->count(),
            'active_properties' => $propertyType->properties()->where('status', 'active')->count(),
            'average_price' => $propertyType->properties()
                ->where('status', 'active')
                ->join('property_prices', 'properties.id', '=', 'property_prices.property_id')
                ->avg('property_prices.price'),
            'price_range' => [
                'min' => $propertyType->properties()
                    ->where('status', 'active')
                    ->join('property_prices', 'properties.id', '=', 'property_prices.property_id')
                    ->min('property_prices.price'),
                'max' => $propertyType->properties()
                    ->where('status', 'active')
                    ->join('property_prices', 'properties.id', '=', 'property_prices.property_id')
                    ->max('property_prices.price'),
            ],
            'by_city' => $propertyType->properties()
                ->where('status', 'active')
                ->join('property_locations', 'properties.id', '=', 'property_locations.property_id')
                ->groupBy('property_locations.city')
                ->selectRaw('property_locations.city as city, count(*) as count')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'recent_properties' => $propertyType->properties()
                ->where('status', 'active')
                ->with(['media' => function($query) {
                    $query->where('media_type', 'image')->limit(1);
                }, 'price'])
                ->latest()
                ->limit(5)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $propertyTypes = PropertyType::where('name', 'like', '%' . $request->query . '%')
            ->orWhere('description', 'like', '%' . $request->query . '%')
            ->active()
            ->ordered()
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $propertyTypes,
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $this->authorize('reorder', PropertyType::class);

        $request->validate([
            'types' => 'required|array',
            'types.*.id' => 'required|exists:property_types,id',
            'types.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->types as $type) {
            PropertyType::where('id', $type['id'])->update([
                'sort_order' => $type['sort_order']
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Property types reordered successfully',
        ]);
    }

    public function toggleStatus(PropertyType $propertyType): JsonResponse
    {
        $this->authorize('update', $propertyType);

        $propertyType->update([
            'is_active' => !$propertyType->is_active
        ]);

        $status = $propertyType->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'message' => "Property type {$status} successfully",
            'property_type' => $propertyType,
        ]);
    }

    public function export(PropertyType $propertyType): JsonResponse
    {
        $this->authorize('view', $propertyType);

        $properties = $propertyType->properties()
            ->where('status', 'active')
            ->with(['location', 'price', 'details'])
            ->get();

        $exportData = $properties->map(function($property) {
            return [
                'id' => $property->id,
                'title' => $property->title,
                'property_code' => $property->property_code,
                'listing_type' => $property->listing_type,
                'price' => $property->price->formatted_price,
                'currency' => $property->price->currency,
                'address' => $property->location->full_address,
                'city' => $property->location->city,
                'country' => $property->location->country,
                'bedrooms' => $property->details->bedrooms,
                'bathrooms' => $property->details->bathrooms,
                'area' => $property->details->formatted_area,
                'year_built' => $property->details->year_built,
                'status' => $property->status,
                'featured' => $property->featured,
                'premium' => $property->premium,
                'created_at' => $property->created_at->toISOString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'property_type' => $propertyType,
                'properties' => $exportData,
                'export_date' => now()->toISOString(),
            ],
        ]);
    }
}
