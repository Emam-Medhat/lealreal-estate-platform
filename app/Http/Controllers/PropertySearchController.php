<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchPropertyRequest;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\PropertyAmenity;
use App\Models\PropertyFeature;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropertySearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with([
            'propertyType',
            'location',
            'details',
            'price',
            'media' => function($query) {
                $query->where('media_type', 'image')->limit(1);
            }
        ])->where('status', 'active');

        // Apply search filters
        if ($request->q) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%')
                  ->orWhere('property_code', 'like', '%' . $request->q . '%')
                  ->orWhereHas('location', function($locationQuery) use ($request) {
                      $locationQuery->where('address', 'like', '%' . $request->q . '%')
                                   ->orWhere('city', 'like', '%' . $request->q . '%')
                                   ->orWhere('neighborhood', 'like', '%' . $request->q . '%');
                  });
            });
        }

        if ($request->property_type) {
            $query->whereHas('propertyType', function($q) use ($request) {
                $q->where('slug', $request->property_type);
            });
        }

        if ($request->listing_type) {
            $query->where('listing_type', $request->listing_type);
        }

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

        if ($request->min_area) {
            $query->whereHas('details', function($q) use ($request) {
                $q->where('area', '>=', $request->min_area);
            });
        }

        if ($request->max_area) {
            $query->whereHas('details', function($q) use ($request) {
                $q->where('area', '<=', $request->max_area);
            });
        }

        if ($request->bedrooms) {
            $query->whereHas('details', function($q) use ($request) {
                $q->where('bedrooms', '>=', $request->bedrooms);
            });
        }

        if ($request->bathrooms) {
            $query->whereHas('details', function($q) use ($request) {
                $q->where('bathrooms', '>=', $request->bathrooms);
            });
        }

        if ($request->city) {
            $query->whereHas('location', function($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        if ($request->country) {
            $query->whereHas('location', function($q) use ($request) {
                $q->where('country', $request->country);
            });
        }

        if ($request->amenities) {
            $amenityIds = explode(',', $request->amenities);
            $query->whereHas('amenities', function($q) use ($amenityIds) {
                $q->whereIn('property_amenities.id', $amenityIds);
            });
        }

        if ($request->features) {
            $featureIds = explode(',', $request->features);
            $query->whereHas('features', function($q) use ($featureIds) {
                $q->whereIn('property_features.id', $featureIds);
            });
        }

        if ($request->featured) {
            $query->where('featured', true);
        }

        if ($request->premium) {
            $query->where('premium', true);
        }

        // Location-based search
        if ($request->lat && $request->lng && $request->radius) {
            $query->whereHas('location', function($q) use ($request) {
                $q->selectRaw(
                    "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
                    [$request->lat, $request->lng, $request->lat]
                )->having('distance', '<=', $request->radius);
            });
        }

        // Sorting
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
            case 'bedrooms':
                $query->join('property_details', 'properties.id', '=', 'property_details.property_id')
                      ->orderBy('property_details.bedrooms', 'desc');
                break;
            case 'bathrooms':
                $query->join('property_details', 'properties.id', '=', 'property_details.property_id')
                      ->orderBy('property_details.bathrooms', 'desc');
                break;
            case 'views':
                $query->orderBy('views_count', 'desc');
                break;
            case 'relevance':
                if ($request->q) {
                    $query->orderByRaw(
                        "CASE 
                            WHEN title LIKE ? THEN 1
                            WHEN description LIKE ? THEN 2
                            WHEN property_code LIKE ? THEN 3
                            ELSE 4
                        END",
                        ['%' . $request->q . '%', '%' . $request->q . '%', '%' . $request->q . '%']
                    );
                }
                $query->orderBy('featured', 'desc')
                      ->orderBy('premium', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy($sort, $order);
        }

        $properties = $query->paginate($request->per_page ?? 12);
        
        // Get search metadata
        $propertyTypes = PropertyType::active()->ordered()->get();
        $amenities = PropertyAmenity::active()->ordered()->get();
        $features = PropertyFeature::active()->ordered()->get();

        // Get cities for autocomplete
        $cities = Property::whereHas('location')
            ->distinct()
            ->pluck('location.city')
            ->filter()
            ->sort()
            ->values();

        return view('properties.search', compact(
            'properties',
            'propertyTypes',
            'amenities',
            'features',
            'cities'
        ));
    }

    public function apiSearch(Request $request): JsonResponse
    {
        $query = Property::with([
            'propertyType',
            'location',
            'details',
            'price',
            'media' => function($query) {
                $query->where('media_type', 'image')->limit(1);
            }
        ])->where('status', 'active');

        // Apply same search logic as index method
        if ($request->q) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%')
                  ->orWhere('property_code', 'like', '%' . $request->q . '%');
            });
        }

        // Apply other filters...
        $properties = $query->limit($request->limit ?? 10)->get();

        return response()->json([
            'success' => true,
            'data' => $properties->map(function($property) {
                return [
                    'id' => $property->id,
                    'title' => $property->title,
                    'description' => Str::limit($property->description, 100),
                    'property_code' => $property->property_code,
                    'price' => $property->price->formatted_price,
                    'location' => $property->location->city . ', ' . $property->location->country,
                    'property_type' => $property->propertyType->name,
                    'listing_type' => $property->listing_type,
                    'bedrooms' => $property->details->bedrooms,
                    'bathrooms' => $property->details->bathrooms,
                    'area' => $property->details->formatted_area,
                    'image' => $property->media->first()?->getUrlAttribute(),
                    'url' => route('properties.show', $property),
                    'featured' => $property->featured,
                    'premium' => $property->premium,
                ];
            })
        ]);
    }

    public function autocomplete(Request $request): JsonResponse
    {
        $query = $request->get('query');
        
        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $properties = Property::where('status', 'active')
            ->where(function($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%')
                  ->orWhere('property_code', 'like', '%' . $query . '%')
                  ->orWhereHas('location', function($locationQuery) use ($query) {
                      $locationQuery->where('address', 'like', '%' . $query . '%')
                                   ->orWhere('city', 'like', '%' . $query . '%');
                  });
            })
            ->with(['location', 'propertyType'])
            ->limit(10)
            ->get();

        $suggestions = $properties->map(function($property) use ($query) {
            $highlightedTitle = str_ireplace($query, "<strong>{$query}</strong>", $property->title);
            
            return [
                'id' => $property->id,
                'title' => $highlightedTitle,
                'property_code' => $property->property_code,
                'location' => $property->location->city . ', ' . $property->location->country,
                'property_type' => $property->propertyType->name,
                'url' => route('properties.show', $property),
            ];
        });

        return response()->json(['suggestions' => $suggestions]);
    }

    public function mapSearch(Request $request): JsonResponse
    {
        $bounds = $request->only(['north', 'south', 'east', 'west']);
        
        $properties = Property::with([
            'location',
            'price',
            'media' => function($query) {
                $query->where('media_type', 'image')->limit(1);
            }
        ])->where('status', 'active')
        ->whereHas('location', function($query) use ($bounds) {
            $query->whereBetween('latitude', [$bounds['south'], $bounds['north']])
                  ->whereBetween('longitude', [$bounds['west'], $bounds['east']]);
        })->get();

        return response()->json([
            'properties' => $properties->map(function($property) {
                return [
                    'id' => $property->id,
                    'title' => $property->title,
                    'price' => $property->price->formatted_price,
                    'position' => [
                        'lat' => (float) $property->location->latitude,
                        'lng' => (float) $property->location->longitude,
                    ],
                    'image' => $property->media->first()?->getUrlAttribute(),
                    'url' => route('properties.show', $property),
                    'featured' => $property->featured,
                    'premium' => $property->premium,
                ];
            })
        ]);
    }

    public function advancedSearch(Request $request)
    {
        $query = Property::with([
            'propertyType',
            'location',
            'details',
            'price',
            'media' => function($query) {
                $query->where('media_type', 'image')->limit(1);
            },
            'amenities',
            'features'
        ])->where('status', 'active');

        // Advanced filters
        if ($request->year_built_min) {
            $query->whereHas('details', function($q) use ($request) {
                $q->where('year_built', '>=', $request->year_built_min);
            });
        }

        if ($request->year_built_max) {
            $query->whereHas('details', function($q) use ($request) {
                $q->where('year_built', '<=', $request->year_built_max);
            });
        }

        if ($request->parking_spaces) {
            $query->whereHas('details', function($q) use ($request) {
                $q->where('parking_spaces', '>=', $request->parking_spaces);
            });
        }

        if ($request->floors) {
            $query->whereHas('details', function($q) use ($request) {
                $q->where('floors', '>=', $request->floors);
            });
        }

        if ($request->negotiable) {
            $query->whereHas('price', function($q) {
                $q->where('is_negotiable', true);
            });
        }

        if ($request->includes_vat) {
            $query->whereHas('price', function($q) {
                $q->where('includes_vat', true);
            });
        }

        // Date posted filter
        if ($request->posted_within) {
            $date = now()->subDays($request->posted_within);
            $query->where('created_at', '>=', $date);
        }

        $properties = $query->paginate($request->per_page ?? 12);

        return view('properties.advanced-search', compact('properties'));
    }
}
