<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyType;
use App\Models\PropertyAmenity;
use App\Models\PropertyFeature;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropertyFilterController extends Controller
{
    public function filter(Request $request): JsonResponse
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

        // Apply filters
        $this->applyFilters($query, $request);

        // Get filtered results
        $properties = $query->paginate($request->per_page ?? 12);

        // Get filter options
        $filterOptions = $this->getFilterOptions($query, $request);

        return response()->json([
            'success' => true,
            'data' => [
                'properties' => $properties->items(),
                'pagination' => [
                    'current_page' => $properties->currentPage(),
                    'last_page' => $properties->lastPage(),
                    'per_page' => $properties->perPage(),
                    'total' => $properties->total(),
                    'from' => $properties->firstItem(),
                    'to' => $properties->lastItem(),
                ],
                'filters' => $filterOptions,
                'applied_filters' => $this->getAppliedFilters($request),
            ]
        ]);
    }

    public function quickFilters(Request $request): JsonResponse
    {
        $filters = [
            'featured' => Property::where('status', 'active')
                ->where('featured', true)
                ->with(['media' => function($query) {
                    $query->where('media_type', 'image')->limit(1);
                }, 'price'])
                ->limit(6)
                ->get(),
            
            'premium' => Property::where('status', 'active')
                ->where('premium', true)
                ->with(['media' => function($query) {
                    $query->where('media_type', 'image')->limit(1);
                }, 'price'])
                ->limit(6)
                ->get(),
            
            'latest' => Property::where('status', 'active')
                ->latest()
                ->with(['media' => function($query) {
                    $query->where('media_type', 'image')->limit(1);
                }, 'price'])
                ->limit(6)
                ->get(),
            
            'price_drop' => Property::where('status', 'active')
                ->whereHas('priceHistory', function($query) {
                    $query->where('change_type', 'decrease')
                          ->where('created_at', '>=', now()->subDays(30));
                })
                ->with(['media' => function($query) {
                    $query->where('media_type', 'image')->limit(1);
                }, 'price'])
                ->limit(6)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $filters
        ]);
    }

    public function filterByType($type): JsonResponse
    {
        $propertyType = PropertyType::where('slug', $type)->firstOrFail();
        
        $properties = Property::where('property_type_id', $propertyType->id)
            ->where('status', 'active')
            ->with([
                'propertyType',
                'location',
                'details',
                'price',
                'media' => function($query) {
                    $query->where('media_type', 'image')->limit(3);
                }
            ])
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => [
                'type' => $propertyType,
                'properties' => $properties,
            ]
        ]);
    }

    public function filterByLocation($city): JsonResponse
    {
        $properties = Property::whereHas('location', function($query) use ($city) {
                $query->where('city', $city);
            })
            ->where('status', 'active')
            ->with([
                'propertyType',
                'location',
                'details',
                'price',
                'media' => function($query) {
                    $query->where('media_type', 'image')->limit(1);
                }
            ])
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => [
                'city' => $city,
                'properties' => $properties,
            ]
        ]);
    }

    public function filterByPriceRange(Request $request): JsonResponse
    {
        $request->validate([
            'min_price' => 'required|numeric|min:0',
            'max_price' => 'required|numeric|min:0',
        ]);

        $properties = Property::whereHas('price', function($query) use ($request) {
                $query->where('price', '>=', $request->min_price)
                      ->where('price', '<=', $request->max_price);
            })
            ->where('status', 'active')
            ->with([
                'propertyType',
                'location',
                'details',
                'price',
                'media' => function($query) {
                    $query->where('media_type', 'image')->limit(1);
                }
            ])
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => [
                'price_range' => [
                    'min' => $request->min_price,
                    'max' => $request->max_price,
                ],
                'properties' => $properties,
            ]
        ]);
    }

    public function savedFilters(Request $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // This would typically be stored in a saved_filters table
        // For now, we'll return a mock response
        return response()->json([
            'success' => true,
            'data' => [
                'filters' => [
                    [
                        'id' => 1,
                        'name' => 'Apartments under 500k',
                        'criteria' => [
                            'property_type' => 'apartment',
                            'max_price' => 500000,
                            'listing_type' => 'sale',
                        ],
                    ],
                    [
                        'id' => 2,
                        'name' => '3+ bedrooms in Riyadh',
                        'criteria' => [
                            'city' => 'Riyadh',
                            'bedrooms' => 3,
                            'listing_type' => 'sale',
                        ],
                    ],
                ]
            ]
        ]);
    }

    private function applyFilters($query, Request $request)
    {
        // Text search
        if ($request->q) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%')
                  ->orWhere('property_code', 'like', '%' . $request->q . '%');
            });
        }

        // Property type
        if ($request->property_type) {
            $query->whereHas('propertyType', function($q) use ($request) {
                $q->where('slug', $request->property_type);
            });
        }

        // Listing type
        if ($request->listing_type) {
            $query->where('listing_type', $request->listing_type);
        }

        // Price range
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

        // Area range
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

        // Bedrooms and bathrooms
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

        // Location
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

        // Amenities
        if ($request->amenities) {
            $amenityIds = is_array($request->amenities) ? $request->amenities : explode(',', $request->amenities);
            $query->whereHas('amenities', function($q) use ($amenityIds) {
                $q->whereIn('property_amenities.id', $amenityIds);
            });
        }

        // Features
        if ($request->features) {
            $featureIds = is_array($request->features) ? $request->features : explode(',', $request->features);
            $query->whereHas('features', function($q) use ($featureIds) {
                $q->whereIn('property_features.id', $featureIds);
            });
        }

        // Special flags
        if ($request->featured) {
            $query->where('featured', true);
        }

        if ($request->premium) {
            $query->where('premium', true);
        }

        if ($request->negotiable) {
            $query->whereHas('price', function($q) {
                $q->where('is_negotiable', true);
            });
        }

        // Date posted
        if ($request->posted_within) {
            $date = now()->subDays($request->posted_within);
            $query->where('created_at', '>=', $date);
        }

        // Year built
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
            case 'views':
                $query->orderBy('views_count', 'desc');
                break;
            default:
                $query->orderBy($sort, $order);
        }
    }

    private function getFilterOptions($query, Request $request)
    {
        return [
            'property_types' => PropertyType::active()->ordered()->get(),
            'cities' => Property::whereHas('location')
                ->distinct()
                ->pluck('location.city')
                ->filter()
                ->sort()
                ->values(),
            'price_ranges' => [
                ['min' => 0, 'max' => 100000, 'label' => 'Under 100K'],
                ['min' => 100000, 'max' => 250000, 'label' => '100K - 250K'],
                ['min' => 250000, 'max' => 500000, 'label' => '250K - 500K'],
                ['min' => 500000, 'max' => 1000000, 'label' => '500K - 1M'],
                ['min' => 1000000, 'max' => null, 'label' => 'Over 1M'],
            ],
            'area_ranges' => [
                ['min' => 0, 'max' => 50, 'label' => 'Under 50 m²'],
                ['min' => 50, 'max' => 100, 'label' => '50 - 100 m²'],
                ['min' => 100, 'max' => 200, 'label' => '100 - 200 m²'],
                ['min' => 200, 'max' => 500, 'label' => '200 - 500 m²'],
                ['min' => 500, 'max' => null, 'label' => 'Over 500 m²'],
            ],
            'amenities' => PropertyAmenity::active()->ordered()->get(),
            'features' => PropertyFeature::active()->ordered()->get(),
        ];
    }

    private function getAppliedFilters(Request $request)
    {
        $applied = [];
        
        foreach ($request->all() as $key => $value) {
            if ($value && in_array($key, [
                'property_type', 'listing_type', 'min_price', 'max_price',
                'min_area', 'max_area', 'bedrooms', 'bathrooms', 'city',
                'country', 'featured', 'premium', 'negotiable', 'posted_within'
            ])) {
                $applied[$key] = $value;
            }
        }
        
        return $applied;
    }
}
