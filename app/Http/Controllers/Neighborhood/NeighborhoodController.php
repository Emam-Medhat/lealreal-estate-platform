<?php

namespace App\Http\Controllers\Neighborhood;

use App\Http\Controllers\Controller;
use App\Models\Neighborhood\Neighborhood;
use App\Models\Neighborhood\Community;
use App\Models\Neighborhood\NeighborhoodGuide;
use App\Models\Neighborhood\LocalBusiness;
use App\Models\Neighborhood\CommunityAmenity;
use App\Models\Neighborhood\NeighborhoodReview;
use App\Models\Neighborhood\NeighborhoodStatistic;
use App\Models\Metaverse\MetaverseProperty;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class NeighborhoodController extends Controller
{
    /**
     * Display the neighborhoods dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['city', 'district', 'status', 'property_type']);
        
        // Get neighborhood statistics
        $stats = [
            'total_neighborhoods' => Neighborhood::count(),
            'active_communities' => Community::where('status', 'active')->count(),
            'total_businesses' => LocalBusiness::count(),
            'total_amenities' => CommunityAmenity::count(),
            'average_rating' => Neighborhood::avg('rating') ?? 0,
            'featured_neighborhoods' => $this->getFeaturedNeighborhoods(),
            'popular_districts' => $this->getPopularDistricts(),
        ];

        // Get recent neighborhoods
        $recentNeighborhoods = Neighborhood::with(['community', 'reviews'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($neighborhood) {
                return [
                    'id' => $neighborhood->id,
                    'name' => $neighborhood->name,
                    'city' => $neighborhood->city,
                    'district' => $neighborhood->district,
                    'description' => $neighborhood->description,
                    'rating' => $neighborhood->rating,
                    'property_count' => $neighborhood->property_count,
                    'resident_count' => $neighborhood->resident_count,
                    'average_price' => $neighborhood->average_price,
                    'status' => $neighborhood->status,
                    'created_at' => $neighborhood->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Get neighborhoods with filters
        $neighborhoods = Neighborhood::with(['community', 'reviews'])
            ->when($filters['city'], function ($query, $city) {
                return $query->where('city', $city);
            })
            ->when($filters['district'], function ($query, $district) {
                return $query->where('district', $district);
            })
            ->when($filters['status'], function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(12);

        // Get cities and districts for filters
        $cities = Neighborhood::distinct()->pluck('city')->filter();
        $districts = Neighborhood::distinct()->pluck('district')->filter();

        return Inertia::render('Neighborhood/Index', [
            'stats' => $stats,
            'recentNeighborhoods' => $recentNeighborhoods,
            'neighborhoods' => $neighborhoods,
            'cities' => $cities,
            'districts' => $districts,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new neighborhood.
     */
    public function create(): \Inertia\Response
    {
        $cities = ['الرياض', 'جدة', 'مكة المكرمة', 'المدينة المنورة', 'الدمام', 'الخبر', 'الطائف', 'تبوك', 'بريدة', 'أبها'];
        $districts = ['شمال', 'جنوب', 'شرق', 'غرب', 'وسط', 'شمال شرق', 'شمال غرب', 'جنوب شرق', 'جنوب غرب'];
        $propertyTypes = ['residential', 'commercial', 'mixed', 'industrial'];
        $statuses = ['active', 'inactive', 'development', 'planned'];

        return Inertia::render('Neighborhood/Create', [
            'cities' => $cities,
            'districts' => $districts,
            'propertyTypes' => $propertyTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Store a newly created neighborhood.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'description' => 'required|string',
            'property_type' => 'required|string',
            'status' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'boundaries' => 'nullable|array',
            'boundaries.north' => 'nullable|numeric',
            'boundaries.south' => 'nullable|numeric',
            'boundaries.east' => 'nullable|numeric',
            'boundaries.west' => 'nullable|numeric',
            'features' => 'nullable|array',
            'amenities' => 'nullable|array',
            'transportation' => 'nullable|array',
            'schools' => 'nullable|array',
            'healthcare' => 'nullable|array',
            'shopping' => 'nullable|array',
            'recreation' => 'nullable|array',
            'safety_rating' => 'nullable|numeric|min:0|max:10',
            'walkability_score' => 'nullable|numeric|min:0|max:100',
            'transit_score' => 'nullable|numeric|min:0|max:100',
            'green_space_ratio' => 'nullable|numeric|min:0|max:100',
            'average_price' => 'nullable|numeric|min:0',
            'price_range' => 'nullable|array',
            'price_range.min' => 'nullable|numeric|min:0',
            'price_range.max' => 'nullable|numeric|min:0',
            'property_count' => 'nullable|integer|min:0',
            'resident_count' => 'nullable|integer|min:0',
            'population_density' => 'nullable|numeric|min:0',
            'development_status' => 'nullable|string',
            'infrastructure_quality' => 'nullable|string',
            'community_engagement' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        try {
            $neighborhood = Neighborhood::create([
                'name' => $validated['name'],
                'city' => $validated['city'],
                'district' => $validated['district'],
                'description' => $validated['description'],
                'property_type' => $validated['property_type'],
                'status' => $validated['status'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'boundaries' => $validated['boundaries'] ?? null,
                'features' => $validated['features'] ?? [],
                'amenities' => $validated['amenities'] ?? [],
                'transportation' => $validated['transportation'] ?? [],
                'schools' => $validated['schools'] ?? [],
                'healthcare' => $validated['healthcare'] ?? [],
                'shopping' => $validated['shopping'] ?? [],
                'recreation' => $validated['recreation'] ?? [],
                'safety_rating' => $validated['safety_rating'] ?? 0,
                'walkability_score' => $validated['walkability_score'] ?? 0,
                'transit_score' => $validated['transit_score'] ?? 0,
                'green_space_ratio' => $validated['green_space_ratio'] ?? 0,
                'average_price' => $validated['average_price'] ?? 0,
                'price_range' => $validated['price_range'] ?? null,
                'property_count' => $validated['property_count'] ?? 0,
                'resident_count' => $validated['resident_count'] ?? 0,
                'population_density' => $validated['population_density'] ?? 0,
                'development_status' => $validated['development_status'] ?? null,
                'infrastructure_quality' => $validated['infrastructure_quality'] ?? null,
                'community_engagement' => $validated['community_engagement'] ?? null,
                'metadata' => $validated['metadata'] ?? [],
                'rating' => 0,
                'review_count' => 0,
            ]);

            // Create associated community
            Community::create([
                'neighborhood_id' => $neighborhood->id,
                'name' => $neighborhood->name . ' Community',
                'description' => 'مجتمع ' . $neighborhood->name,
                'status' => 'active',
                'member_count' => 0,
                'activity_level' => 'low',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الحي بنجاح',
                'neighborhood' => $neighborhood,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الحي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified neighborhood.
     */
    public function show(Neighborhood $neighborhood): \Inertia\Response
    {
        $neighborhood->load(['community', 'reviews', 'guides', 'businesses', 'amenities', 'events', 'statistics']);

        // Get nearby properties
        $nearbyProperties = MetaverseProperty::where('city', $neighborhood->city)
            ->where('district', $neighborhood->district)
            ->take(10)
            ->get();

        // Get neighborhood statistics
        $statistics = $this->getNeighborhoodStatistics($neighborhood);

        // Get recent activities
        $recentActivities = $this->getRecentActivities($neighborhood);

        return Inertia::render('Neighborhood/Show', [
            'neighborhood' => $neighborhood,
            'nearbyProperties' => $nearbyProperties,
            'statistics' => $statistics,
            'recentActivities' => $recentActivities,
        ]);
    }

    /**
     * Show the form for editing the specified neighborhood.
     */
    public function edit(Neighborhood $neighborhood): \Inertia\Response
    {
        $cities = ['الرياض', 'جدة', 'مكة المكرمة', 'المدينة المنورة', 'الدمام', 'الخبر', 'الطائف', 'تبوك', 'بريدة', 'أبها'];
        $districts = ['شمال', 'جنوب', 'شرق', 'غرب', 'وسط', 'شمال شرق', 'شمال غرب', 'جنوب شرق', 'جنوب غرب'];
        $propertyTypes = ['residential', 'commercial', 'mixed', 'industrial'];
        $statuses = ['active', 'inactive', 'development', 'planned'];

        return Inertia::render('Neighborhood/Edit', [
            'neighborhood' => $neighborhood,
            'cities' => $cities,
            'districts' => $districts,
            'propertyTypes' => $propertyTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified neighborhood.
     */
    public function update(Request $request, Neighborhood $neighborhood): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'description' => 'required|string',
            'property_type' => 'required|string',
            'status' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'boundaries' => 'nullable|array',
            'features' => 'nullable|array',
            'amenities' => 'nullable|array',
            'transportation' => 'nullable|array',
            'schools' => 'nullable|array',
            'healthcare' => 'nullable|array',
            'shopping' => 'nullable|array',
            'recreation' => 'nullable|array',
            'safety_rating' => 'nullable|numeric|min:0|max:10',
            'walkability_score' => 'nullable|numeric|min:0|max:100',
            'transit_score' => 'nullable|numeric|min:0|max:100',
            'green_space_ratio' => 'nullable|numeric|min:0|max:100',
            'average_price' => 'nullable|numeric|min:0',
            'price_range' => 'nullable|array',
            'property_count' => 'nullable|integer|min:0',
            'resident_count' => 'nullable|integer|min:0',
            'population_density' => 'nullable|numeric|min:0',
            'development_status' => 'nullable|string',
            'infrastructure_quality' => 'nullable|string',
            'community_engagement' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        try {
            $neighborhood->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الحي بنجاح',
                'neighborhood' => $neighborhood,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الحي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified neighborhood.
     */
    public function destroy(Neighborhood $neighborhood): JsonResponse
    {
        try {
            $neighborhood->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الحي بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الحي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get neighborhood statistics.
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $filters = $request->only(['city', 'district', 'property_type']);
        
        $statistics = [
            'total_neighborhoods' => Neighborhood::when($filters['city'], function ($query, $city) {
                return $query->where('city', $city);
            })->when($filters['district'], function ($query, $district) {
                return $query->where('district', $district);
            })->when($filters['property_type'], function ($query, $propertyType) {
                return $query->where('property_type', $propertyType);
            })->count(),
            
            'average_rating' => Neighborhood::when($filters['city'], function ($query, $city) {
                return $query->where('city', $city);
            })->when($filters['district'], function ($query, $district) {
                return $query->where('district', $district);
            })->when($filters['property_type'], function ($query, $propertyType) {
                return $query->where('property_type', $propertyType);
            })->avg('rating') ?? 0,
            
            'average_price' => Neighborhood::when($filters['city'], function ($query, $city) {
                return $query->where('city', $city);
            })->when($filters['district'], function ($query, $district) {
                return $query->where('district', $district);
            })->when($filters['property_type'], function ($query, $propertyType) {
                return $query->where('property_type', $propertyType);
            })->avg('average_price') ?? 0,
            
            'total_properties' => Neighborhood::when($filters['city'], function ($query, $city) {
                return $query->where('city', $city);
            })->when($filters['district'], function ($query, $district) {
                return $query->where('district', $district);
            })->when($filters['property_type'], function ($query, $propertyType) {
                return $query->where('property_type', $propertyType);
            })->sum('property_count'),
            
            'total_residents' => Neighborhood::when($filters['city'], function ($query, $city) {
                return $query->where('city', $city);
            })->when($filters['district'], function ($query, $district) {
                return $query->where('district', $district);
            })->when($filters['property_type'], function ($query, $propertyType) {
                return $query->where('property_type', $propertyType);
            })->sum('resident_count'),
        ];

        return response()->json([
            'success' => true,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get neighborhood comparison.
     */
    public function getComparison(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'neighborhood_ids' => 'required|array|min:2|max:5',
            'neighborhood_ids.*' => 'exists:neighborhoods,id',
        ]);

        $neighborhoods = Neighborhood::whereIn('id', $validated['neighborhood_ids'])
            ->with(['reviews', 'statistics'])
            ->get();

        $comparison = $neighborhoods->map(function ($neighborhood) {
            return [
                'id' => $neighborhood->id,
                'name' => $neighborhood->name,
                'city' => $neighborhood->city,
                'district' => $neighborhood->district,
                'rating' => $neighborhood->rating,
                'average_price' => $neighborhood->average_price,
                'property_count' => $neighborhood->property_count,
                'resident_count' => $neighborhood->resident_count,
                'safety_rating' => $neighborhood->safety_rating,
                'walkability_score' => $neighborhood->walkability_score,
                'transit_score' => $neighborhood->transit_score,
                'green_space_ratio' => $neighborhood->green_space_ratio,
                'review_count' => $neighborhood->review_count,
            ];
        });

        return response()->json([
            'success' => true,
            'comparison' => $comparison,
        ]);
    }

    /**
     * Export neighborhood data.
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,json',
            'filters' => 'nullable|array',
            'include_statistics' => 'nullable|boolean',
            'include_reviews' => 'nullable|boolean',
            'include_amenities' => 'nullable|boolean',
        ]);

        try {
            $exportData = $this->prepareNeighborhoodExport($validated);
            $filename = $this->generateNeighborhoodExportFilename($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز بيانات الأحياء للتصدير',
                'filename' => $filename,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير بيانات الأحياء: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get featured neighborhoods.
     */
    private function getFeaturedNeighborhoods(): array
    {
        return Neighborhood::where('status', 'active')
            ->where('rating', '>=', 4.0)
            ->orderBy('rating', 'desc')
            ->take(5)
            ->get(['name', 'city', 'district', 'rating', 'average_price'])
            ->toArray();
    }

    /**
     * Get popular districts.
     */
    private function getPopularDistricts(): array
    {
        return Neighborhood::select('district', DB::raw('count(*) as count'))
            ->where('status', 'active')
            ->groupBy('district')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get()
            ->toArray();
    }

    /**
     * Get neighborhood statistics.
     */
    private function getNeighborhoodStatistics(Neighborhood $neighborhood): array
    {
        return [
            'total_businesses' => LocalBusiness::where('neighborhood_id', $neighborhood->id)->count(),
            'total_amenities' => CommunityAmenity::where('neighborhood_id', $neighborhood->id)->count(),
            'total_events' => $neighborhood->events()->count(),
            'total_reviews' => $neighborhood->reviews()->count(),
            'average_rating' => $neighborhood->reviews()->avg('rating') ?? 0,
            'community_members' => $neighborhood->community ? $neighborhood->community->member_count : 0,
        ];
    }

    /**
     * Get recent activities.
     */
    private function getRecentActivities(Neighborhood $neighborhood): array
    {
        $activities = [];
        
        // Recent reviews
        $recentReviews = $neighborhood->reviews()->latest()->take(3)->get();
        foreach ($recentReviews as $review) {
            $activities[] = [
                'type' => 'review',
                'title' => 'تقييم جديد',
                'description' => $review->title,
                'user' => $review->user_name,
                'rating' => $review->rating,
                'created_at' => $review->created_at->format('Y-m-d H:i:s'),
            ];
        }
        
        // Recent events
        $recentEvents = $neighborhood->events()->latest()->take(3)->get();
        foreach ($recentEvents as $event) {
            $activities[] = [
                'type' => 'event',
                'title' => 'فعالية جديدة',
                'description' => $event->title,
                'date' => $event->start_date,
                'created_at' => $event->created_at->format('Y-m-d H:i:s'),
            ];
        }
        
        // Sort by date
        usort($activities, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_slice($activities, 0, 5);
    }

    /**
     * Prepare neighborhood export data.
     */
    private function prepareNeighborhoodExport(array $options): array
    {
        $filters = $options['filters'] ?? [];
        $includeStatistics = $options['include_statistics'] ?? false;
        $includeReviews = $options['include_reviews'] ?? false;
        $includeAmenities = $options['include_amenities'] ?? false;

        $query = Neighborhood::query();
        
        if (isset($filters['city'])) {
            $query->where('city', $filters['city']);
        }
        
        if (isset($filters['district'])) {
            $query->where('district', $filters['district']);
        }
        
        if (isset($filters['property_type'])) {
            $query->where('property_type', $filters['property_type']);
        }

        $neighborhoods = $query->get();

        $data = $neighborhoods->map(function ($neighborhood) use ($includeStatistics, $includeReviews, $includeAmenities) {
            $item = [
                'id' => $neighborhood->id,
                'name' => $neighborhood->name,
                'city' => $neighborhood->city,
                'district' => $neighborhood->district,
                'description' => $neighborhood->description,
                'property_type' => $neighborhood->property_type,
                'status' => $neighborhood->status,
                'rating' => $neighborhood->rating,
                'review_count' => $neighborhood->review_count,
                'average_price' => $neighborhood->average_price,
                'property_count' => $neighborhood->property_count,
                'resident_count' => $neighborhood->resident_count,
                'safety_rating' => $neighborhood->safety_rating,
                'walkability_score' => $neighborhood->walkability_score,
                'transit_score' => $neighborhood->transit_score,
                'green_space_ratio' => $neighborhood->green_space_ratio,
                'created_at' => $neighborhood->created_at->format('Y-m-d H:i:s'),
            ];

            if ($includeStatistics) {
                $item['statistics'] = $this->getNeighborhoodStatistics($neighborhood);
            }

            if ($includeReviews) {
                $item['reviews'] = $neighborhood->reviews()->take(5)->get(['rating', 'title', 'comment', 'created_at']);
            }

            if ($includeAmenities) {
                $item['amenities'] = CommunityAmenity::where('neighborhood_id', $neighborhood->id)->get(['name', 'type', 'description']);
            }

            return $item;
        });

        return [
            'headers' => ['ID', 'Name', 'City', 'District', 'Property Type', 'Rating', 'Average Price', 'Property Count', 'Status', 'Created At'],
            'rows' => $data->toArray(),
        ];
    }

    /**
     * Generate neighborhood export filename.
     */
    private function generateNeighborhoodExportFilename(array $options): string
    {
        $format = $options['format'];
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "neighborhoods_export_{$timestamp}.{$format}";
    }
}
