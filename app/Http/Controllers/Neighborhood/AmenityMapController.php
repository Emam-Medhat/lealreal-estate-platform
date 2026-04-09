<?php

namespace App\Http\Controllers\Neighborhood;

use App\Http\Controllers\Controller;
use App\Models\Neighborhood\CommunityAmenity;
use App\Models\Neighborhood\Neighborhood;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AmenityMapController extends Controller
{
    /**
     * Display the amenity map dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['neighborhood_id', 'amenity_type', 'status']);
        
        // Get amenity statistics
        $stats = [
            'total_amenities' => CommunityAmenity::count(),
            'active_amenities' => CommunityAmenity::where('status', 'active')->count(),
            'total_types' => CommunityAmenity::distinct('type')->count(),
            'average_rating' => CommunityAmenity::avg('rating') ?? 0,
            'featured_amenities' => $this->getFeaturedAmenities(),
            'popular_types' => $this->getPopularTypes(),
            'coverage_stats' => $this->getCoverageStats(),
        ];

        // Get amenities with filters
        $amenities = CommunityAmenity::with(['neighborhood'])
            ->when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })
            ->when($filters['amenity_type'], function ($query, $amenityType) {
                return $query->where('type', $amenityType);
            })
            ->when($filters['status'], function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(12);

        // Get neighborhoods and amenity types for filters
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $amenityTypes = ['park', 'playground', 'sports_facility', 'community_center', 'library', 'health_center', 'school', 'mosque', 'shopping_center', 'restaurant', 'cafe', 'pharmacy', 'bank', 'gas_station', 'public_transport', 'parking', 'other'];
        $statuses = ['active', 'inactive', 'maintenance', 'closed'];

        return Inertia::render('AmenityMap/Index', [
            'stats' => $stats,
            'amenities' => $amenities,
            'neighborhoods' => $neighborhoods,
            'amenityTypes' => $amenityTypes,
            'statuses' => $statuses,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new community amenity.
     */
    public function create(): \Inertia\Response
    {
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $amenityTypes = ['park', 'playground', 'sports_facility', 'community_center', 'library', 'health_center', 'school', 'mosque', 'shopping_center', 'restaurant', 'cafe', 'pharmacy', 'bank', 'gas_station', 'public_transport', 'parking', 'other'];
        $statuses = ['active', 'inactive', 'maintenance', 'closed'];

        return Inertia::render('AmenityMap/Create', [
            'neighborhoods' => $neighborhoods,
            'amenityTypes' => $amenityTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Store a newly created community amenity.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'neighborhood_id' => 'required|exists:neighborhoods,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string',
            'status' => 'required|string',
            'address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'opening_hours' => 'nullable|array',
            'opening_hours.monday' => 'nullable|array',
            'opening_hours.tuesday' => 'nullable|array',
            'opening_hours.wednesday' => 'nullable|array',
            'opening_hours.thursday' => 'nullable|array',
            'opening_hours.friday' => 'nullable|array',
            'opening_hours.saturday' => 'nullable|array',
            'opening_hours.sunday' => 'nullable|array',
            'facilities' => 'nullable|array',
            'services' => 'nullable|array',
            'accessibility' => 'nullable|array',
            'accessibility.wheelchair' => 'nullable|boolean',
            'accessibility.parking' => 'nullable|boolean',
            'accessibility.elevator' => 'nullable|boolean',
            'accessibility.ramp' => 'nullable|boolean',
            'accessibility.toilet' => 'nullable|boolean',
            'capacity' => 'nullable|integer|min:0',
            'area_size' => 'nullable|numeric|min:0',
            'year_built' => 'nullable|integer|min:1900|max:' . date('Y'),
            'last_renovated' => 'nullable|integer|min:1900|max:' . date('Y'),
            'maintenance_info' => 'nullable|array',
            'maintenance_info.frequency' => 'nullable|string',
            'maintenance_info.last_maintenance' => 'nullable|date',
            'maintenance_info.next_maintenance' => 'nullable|date',
            'contact_info' => 'nullable|array',
            'contact_info.manager' => 'nullable|string|max:255',
            'contact_info.phone' => 'nullable|string|max:20',
            'contact_info.email' => 'nullable|email|max:255',
            'rules' => 'nullable|array',
            'fees' => 'nullable|array',
            'fees.admission' => 'nullable|numeric|min:0',
            'fees.membership' => 'nullable|numeric|min:0',
            'fees.parking' => 'nullable|numeric|min:0',
            'images' => 'nullable|array',
            'main_image' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'verified' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $amenity = CommunityAmenity::create([
                'neighborhood_id' => $validated['neighborhood_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'status' => $validated['status'],
                'address' => $validated['address'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'website' => $validated['website'] ?? null,
                'opening_hours' => $validated['opening_hours'] ?? [],
                'facilities' => $validated['facilities'] ?? [],
                'services' => $validated['services'] ?? [],
                'accessibility' => $validated['accessibility'] ?? [],
                'capacity' => $validated['capacity'] ?? null,
                'area_size' => $validated['area_size'] ?? null,
                'year_built' => $validated['year_built'] ?? null,
                'last_renovated' => $validated['last_renovated'] ?? null,
                'maintenance_info' => $validated['maintenance_info'] ?? [],
                'contact_info' => $validated['contact_info'] ?? [],
                'rules' => $validated['rules'] ?? [],
                'fees' => $validated['fees'] ?? [],
                'images' => $validated['images'] ?? [],
                'main_image' => $validated['main_image'] ?? null,
                'gallery' => $validated['gallery'] ?? [],
                'verified' => $validated['verified'] ?? false,
                'featured' => $validated['featured'] ?? false,
                'tags' => $validated['tags'] ?? [],
                'metadata' => $validated['metadata'] ?? [],
                'rating' => 0,
                'review_count' => 0,
                'visit_count' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة مرفق المجتمع بنجاح',
                'amenity' => $amenity,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة مرفق المجتمع: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified community amenity.
     */
    public function show(CommunityAmenity $amenity): \Inertia\Response
    {
        // Increment visit count
        $amenity->increment('visit_count');

        $amenity->load(['neighborhood']);

        // Get nearby amenities
        $nearbyAmenities = CommunityAmenity::where('neighborhood_id', $amenity->neighborhood_id)
            ->where('id', '!=', $amenity->id)
            ->where('type', $amenity->type)
            ->take(5)
            ->get(['id', 'name', 'type', 'rating', 'address']);

        // Get amenities in same neighborhood
        $sameNeighborhoodAmenities = CommunityAmenity::where('neighborhood_id', $amenity->neighborhood_id)
            ->where('id', '!=', $amenity->id)
            ->where('status', 'active')
            ->take(10)
            ->get(['id', 'name', 'type', 'rating', 'address']);

        return Inertia::render('AmenityMap/Show', [
            'amenity' => $amenity,
            'nearbyAmenities' => $nearbyAmenities,
            'sameNeighborhoodAmenities' => $sameNeighborhoodAmenities,
        ]);
    }

    /**
     * Show the form for editing the specified community amenity.
     */
    public function edit(CommunityAmenity $amenity): \Inertia\Response
    {
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $amenityTypes = ['park', 'playground', 'sports_facility', 'community_center', 'library', 'health_center', 'school', 'mosque', 'shopping_center', 'restaurant', 'cafe', 'pharmacy', 'bank', 'gas_station', 'public_transport', 'parking', 'other'];
        $statuses = ['active', 'inactive', 'maintenance', 'closed'];

        return Inertia::render('AmenityMap/Edit', [
            'amenity' => $amenity,
            'neighborhoods' => $neighborhoods,
            'amenityTypes' => $amenityTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified community amenity.
     */
    public function update(Request $request, CommunityAmenity $amenity): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string',
            'status' => 'required|string',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'opening_hours' => 'nullable|array',
            'facilities' => 'nullable|array',
            'services' => 'nullable|array',
            'accessibility' => 'nullable|array',
            'capacity' => 'nullable|integer|min:0',
            'area_size' => 'nullable|numeric|min:0',
            'year_built' => 'nullable|integer|min:1900|max:' . date('Y'),
            'last_renovated' => 'nullable|integer|min:1900|max:' . date('Y'),
            'maintenance_info' => 'nullable|array',
            'contact_info' => 'nullable|array',
            'rules' => 'nullable|array',
            'fees' => 'nullable|array',
            'images' => 'nullable|array',
            'main_image' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'verified' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $amenity->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث مرفق المجتمع بنجاح',
                'amenity' => $amenity,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث مرفق المجتمع: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified community amenity.
     */
    public function destroy(CommunityAmenity $amenity): JsonResponse
    {
        try {
            $amenity->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف مرفق المجتمع بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف مرفق المجتمع: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get amenity map data.
     */
    public function getMapData(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',
            'amenity_types' => 'nullable|array',
            'amenity_types.*' => 'string',
            'bounds' => 'nullable|array',
            'bounds.north' => 'nullable|numeric',
            'bounds.south' => 'nullable|numeric',
            'bounds.east' => 'nullable|numeric',
            'bounds.west' => 'nullable|numeric',
        ]);

        try {
            $query = CommunityAmenity::where('status', 'active')
                ->with(['neighborhood']);

            if ($validated['neighborhood_id']) {
                $query->where('neighborhood_id', $validated['neighborhood_id']);
            }

            if (!empty($validated['amenity_types'])) {
                $query->whereIn('type', $validated['amenity_types']);
            }

            $amenities = $query->get([
                'id', 'name', 'type', 'description', 'address', 'latitude', 'longitude',
                'rating', 'status', 'main_image', 'neighborhood_id'
            ]);

            $mapData = $amenities->map(function ($amenity) {
                return [
                    'id' => $amenity->id,
                    'name' => $amenity->name,
                    'type' => $amenity->type,
                    'description' => $amenity->description,
                    'address' => $amenity->address,
                    'latitude' => $amenity->latitude,
                    'longitude' => $amenity->longitude,
                    'rating' => $amenity->rating,
                    'status' => $amenity->status,
                    'main_image' => $amenity->main_image,
                    'neighborhood' => $amenity->neighborhood?->name ?? 'غير معروف',
                ];
            });

            return response()->json([
                'success' => true,
                'amenities' => $mapData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب بيانات الخريطة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get amenity statistics.
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $filters = $request->only(['neighborhood_id', 'amenity_type', 'status']);
        
        $statistics = [
            'total_amenities' => CommunityAmenity::when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })->when($filters['amenity_type'], function ($query, $amenityType) {
                return $query->where('type', $amenityType);
            })->when($filters['status'], function ($query, $status) {
                return $query->where('status', $status);
            })->count(),
            
            'active_amenities' => CommunityAmenity::when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })->when($filters['amenity_type'], function ($query, $amenityType) {
                return $query->where('type', $amenityType);
            })->where('status', 'active')->count(),
            
            'average_rating' => CommunityAmenity::when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })->when($filters['amenity_type'], function ($query, $amenityType) {
                return $query->where('type', $amenityType);
            })->avg('rating') ?? 0,
            
            'total_visits' => CommunityAmenity::when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })->when($filters['amenity_type'], function ($query, $amenityType) {
                return $query->where('type', $amenityType);
            })->sum('visit_count'),
        ];

        return response()->json([
            'success' => true,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get amenities by type.
     */
    public function getByType(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $limit = $validated['limit'] ?? 20;

        try {
            $amenities = CommunityAmenity::where('type', $validated['type'])
                ->where('status', 'active')
                ->with(['neighborhood'])
                ->orderBy('rating', 'desc')
                ->take($limit)
                ->get(['id', 'name', 'description', 'type', 'rating', 'address', 'neighborhood_id', 'main_image']);

            return response()->json([
                'success' => true,
                'amenities' => $amenities,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب المرافق: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get nearby amenities.
     */
    public function getNearby(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.5|max:50',
            'amenity_types' => 'nullable|array',
            'amenity_types.*' => 'string',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $radius = $validated['radius'] ?? 5;
        $limit = $validated['limit'] ?? 20;

        try {
            // Mock implementation - in real app, this would use geospatial queries
            $query = CommunityAmenity::where('status', 'active')
                ->with(['neighborhood']);

            if (!empty($validated['amenity_types'])) {
                $query->whereIn('type', $validated['amenity_types']);
            }

            $amenities = $query->orderBy('rating', 'desc')
                ->take($limit)
                ->get(['id', 'name', 'type', 'description', 'address', 'latitude', 'longitude', 'rating', 'neighborhood_id', 'main_image']);

            return response()->json([
                'success' => true,
                'amenities' => $amenities,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب المرافق المجاورة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Rate an amenity.
     */
    public function rate(Request $request, CommunityAmenity $amenity): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'user_name' => 'required|string|max:255',
        ]);

        try {
            // Mock implementation - in real app, this would create a rating record
            // Update amenity rating (mock calculation)
            $newRating = ($amenity->rating * $amenity->review_count + $validated['rating']) / ($amenity->review_count + 1);
            $amenity->update([
                'rating' => $newRating,
                'review_count' => $amenity->review_count + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تقييم المرفق بنجاح',
                'rating' => $newRating,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تقييم المرفق: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search amenities.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2|max:255',
            'limit' => 'nullable|integer|min:1|max:50',
            'amenity_type' => 'nullable|string',
        ]);

        $limit = $validated['limit'] ?? 20;
        $query = $validated['query'];

        try {
            $amenities = CommunityAmenity::where('status', 'active')
                ->when($validated['amenity_type'], function ($q, $amenityType) {
                    return $q->where('type', $amenityType);
                })
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('type', 'LIKE', "%{$query}%")
                      ->orWhere('address', 'LIKE', "%{$query}%");
                })
                ->with(['neighborhood'])
                ->orderBy('rating', 'desc')
                ->take($limit)
                ->get(['id', 'name', 'description', 'type', 'rating', 'address', 'neighborhood_id', 'main_image']);

            return response()->json([
                'success' => true,
                'amenities' => $amenities,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء البحث في المرافق: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export amenity data.
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,json',
            'filters' => 'nullable|array',
            'include_contact_info' => 'nullable|boolean',
            'include_hours' => 'nullable|boolean',
        ]);

        try {
            $exportData = $this->prepareAmenityExport($validated);
            $filename = $this->generateAmenityExportFilename($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز بيانات المرافق للتصدير',
                'filename' => $filename,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير بيانات المرافق: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get featured amenities.
     */
    private function getFeaturedAmenities(): array
    {
        return CommunityAmenity::where('status', 'active')
            ->where('featured', true)
            ->where('rating', '>=', 4.0)
            ->orderBy('rating', 'desc')
            ->take(5)
            ->with(['neighborhood'])
            ->get(['name', 'type', 'rating', 'neighborhood_id'])
            ->toArray();
    }

    /**
     * Get popular types.
     */
    private function getPopularTypes(): array
    {
        return CommunityAmenity::select('type', DB::raw('count(*) as count'))
            ->where('status', 'active')
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get()
            ->toArray();
    }

    /**
     * Get coverage statistics.
     */
    private function getCoverageStats(): array
    {
        $totalNeighborhoods = Neighborhood::where('status', 'active')->count();
        $neighborhoodsWithAmenities = Neighborhood::where('status', 'active')
            ->whereHas('amenities')
            ->count();

        return [
            'total_neighborhoods' => $totalNeighborhoods,
            'neighborhoods_with_amenities' => $neighborhoodsWithAmenities,
            'coverage_percentage' => $totalNeighborhoods > 0 ? round(($neighborhoodsWithAmenities / $totalNeighborhoods) * 100, 2) : 0,
        ];
    }

    /**
     * Prepare amenity export data.
     */
    private function prepareAmenityExport(array $options): array
    {
        $filters = $options['filters'] ?? [];
        $includeContactInfo = $options['include_contact_info'] ?? false;
        $includeHours = $options['include_hours'] ?? false;

        $query = CommunityAmenity::with(['neighborhood']);
        
        if (isset($filters['neighborhood_id'])) {
            $query->where('neighborhood_id', $filters['neighborhood_id']);
        }
        
        if (isset($filters['amenity_type'])) {
            $query->where('type', $filters['amenity_type']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $amenities = $query->get();

        $data = $amenities->map(function ($amenity) use ($includeContactInfo, $includeHours) {
            $item = [
                'id' => $amenity->id,
                'name' => $amenity->name,
                'neighborhood' => $amenity->neighborhood?->name ?? 'غير معروف',
                'type' => $amenity->type,
                'description' => $amenity->description,
                'address' => $amenity->address,
                'status' => $amenity->status,
                'rating' => $amenity->rating,
                'review_count' => $amenity->review_count,
                'visit_count' => $amenity->visit_count,
                'verified' => $amenity->verified,
                'featured' => $amenity->featured,
                'created_at' => $amenity->created_at->format('Y-m-d H:i:s'),
            ];

            if ($includeContactInfo) {
                $item['phone'] = $amenity->phone;
                $item['email'] = $amenity->email;
                $item['website'] = $amenity->website;
                $item['contact_info'] = $amenity->contact_info;
            }

            if ($includeHours) {
                $item['opening_hours'] = $amenity->opening_hours;
            }

            return $item;
        });

        return [
            'headers' => ['ID', 'Name', 'Neighborhood', 'Type', 'Status', 'Rating', 'Verified', 'Featured', 'Created At'],
            'rows' => $data->toArray(),
        ];
    }

    /**
     * Generate amenity export filename.
     */
    private function generateAmenityExportFilename(array $options): string
    {
        $format = $options['format'];
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "community_amenities_export_{$timestamp}.{$format}";
    }
}
