<?php

namespace App\Http\Controllers\Neighborhood;

use App\Http\Controllers\Controller;
use App\Models\Neighborhood\LocalBusiness;
use App\Models\Neighborhood\Neighborhood;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class LocalBusinessController extends Controller
{
    /**
     * Display the local businesses dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['neighborhood_id', 'category', 'status', 'rating_range']);
        
        // Get business statistics
        $stats = [
            'total_businesses' => LocalBusiness::count(),
            'active_businesses' => LocalBusiness::where('status', 'active')->count(),
            'total_categories' => LocalBusiness::distinct('category')->count(),
            'average_rating' => LocalBusiness::avg('rating') ?? 0,
            'featured_businesses' => $this->getFeaturedBusinesses(),
            'popular_categories' => $this->getPopularCategories(),
            'recent_businesses' => $this->getRecentBusinesses(),
        ];

        // Get businesses with filters
        $businesses = LocalBusiness::with(['neighborhood'])
            ->when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })
            ->when($filters['category'], function ($query, $category) {
                return $query->where('category', $category);
            })
            ->when($filters['status'], function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($filters['rating_range'], function ($query, $range) {
                if ($range === 'high') {
                    return $query->where('rating', '>=', 4.0);
                } elseif ($range === 'medium') {
                    return $query->whereBetween('rating', [3.0, 4.0]);
                } elseif ($range === 'low') {
                    return $query->where('rating', '<', 3.0);
                }
            })
            ->latest()
            ->paginate(12);

        // Get neighborhoods and categories for filters
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $categories = ['restaurant', 'cafe', 'grocery', 'pharmacy', 'clinic', 'school', 'bank', 'gas_station', 'beauty_salon', 'fitness', 'retail', 'service', 'other'];
        $statuses = ['active', 'inactive', 'closed', 'pending'];
        $ratingRanges = ['high', 'medium', 'low'];

        return Inertia::render('LocalBusiness/Index', [
            'stats' => $stats,
            'businesses' => $businesses,
            'neighborhoods' => $neighborhoods,
            'categories' => $categories,
            'statuses' => $statuses,
            'ratingRanges' => $ratingRanges,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new local business.
     */
    public function create(): \Inertia\Response
    {
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $categories = ['restaurant', 'cafe', 'grocery', 'pharmacy', 'clinic', 'school', 'bank', 'gas_station', 'beauty_salon', 'fitness', 'retail', 'service', 'other'];
        $statuses = ['active', 'inactive', 'closed', 'pending'];

        return Inertia::render('LocalBusiness/Create', [
            'neighborhoods' => $neighborhoods,
            'categories' => $categories,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Store a newly created local business.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'neighborhood_id' => 'required|exists:neighborhoods,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'status' => 'required|string',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'opening_hours' => 'nullable|array',
            'opening_hours.monday' => 'nullable|array',
            'opening_hours.tuesday' => 'nullable|array',
            'opening_hours.wednesday' => 'nullable|array',
            'opening_hours.thursday' => 'nullable|array',
            'opening_hours.friday' => 'nullable|array',
            'opening_hours.saturday' => 'nullable|array',
            'opening_hours.sunday' => 'nullable|array',
            'services' => 'nullable|array',
            'products' => 'nullable|array',
            'specialties' => 'nullable|array',
            'price_range' => 'nullable|string|max:50',
            'payment_methods' => 'nullable|array',
            'delivery_options' => 'nullable|array',
            'contact_person' => 'nullable|string|max:255',
            'social_media' => 'nullable|array',
            'social_media.facebook' => 'nullable|url',
            'social_media.instagram' => 'nullable|url',
            'social_media.twitter' => 'nullable|url',
            'social_media.linkedin' => 'nullable|url',
            'images' => 'nullable|array',
            'logo' => 'nullable|string|max:255',
            'cover_image' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'verified' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $business = LocalBusiness::create([
                'neighborhood_id' => $validated['neighborhood_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'status' => $validated['status'],
                'address' => $validated['address'],
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'website' => $validated['website'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'opening_hours' => $validated['opening_hours'] ?? [],
                'services' => $validated['services'] ?? [],
                'products' => $validated['products'] ?? [],
                'specialties' => $validated['specialties'] ?? [],
                'price_range' => $validated['price_range'] ?? null,
                'payment_methods' => $validated['payment_methods'] ?? [],
                'delivery_options' => $validated['delivery_options'] ?? [],
                'contact_person' => $validated['contact_person'] ?? null,
                'social_media' => $validated['social_media'] ?? [],
                'images' => $validated['images'] ?? [],
                'logo' => $validated['logo'] ?? null,
                'cover_image' => $validated['cover_image'] ?? null,
                'gallery' => $validated['gallery'] ?? [],
                'verified' => $validated['verified'] ?? false,
                'featured' => $validated['featured'] ?? false,
                'tags' => $validated['tags'] ?? [],
                'metadata' => $validated['metadata'] ?? [],
                'rating' => 0,
                'review_count' => 0,
                'view_count' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة العمل المحلي بنجاح',
                'business' => $business,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة العمل المحلي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified local business.
     */
    public function show(LocalBusiness $business): \Inertia\Response
    {
        // Increment view count
        $business->increment('view_count');

        $business->load(['neighborhood']);

        // Get nearby businesses
        $nearbyBusinesses = LocalBusiness::where('neighborhood_id', $business->neighborhood_id)
            ->where('id', '!=', $business->id)
            ->where('category', $business->category)
            ->take(5)
            ->get(['id', 'name', 'category', 'rating', 'address']);

        // Get businesses in same category
        $sameCategoryBusinesses = LocalBusiness::where('category', $business->category)
            ->where('id', '!=', $business->id)
            ->where('status', 'active')
            ->take(5)
            ->get(['id', 'name', 'neighborhood_id', 'rating', 'address']);

        return Inertia::render('LocalBusiness/Show', [
            'business' => $business,
            'nearbyBusinesses' => $nearbyBusinesses,
            'sameCategoryBusinesses' => $sameCategoryBusinesses,
        ]);
    }

    /**
     * Show the form for editing the specified local business.
     */
    public function edit(LocalBusiness $business): \Inertia\Response
    {
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $categories = ['restaurant', 'cafe', 'grocery', 'pharmacy', 'clinic', 'school', 'bank', 'gas_station', 'beauty_salon', 'fitness', 'retail', 'service', 'other'];
        $statuses = ['active', 'inactive', 'closed', 'pending'];

        return Inertia::render('LocalBusiness/Edit', [
            'business' => $business,
            'neighborhoods' => $neighborhoods,
            'categories' => $categories,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified local business.
     */
    public function update(Request $request, LocalBusiness $business): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'status' => 'required|string',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'opening_hours' => 'nullable|array',
            'services' => 'nullable|array',
            'products' => 'nullable|array',
            'specialties' => 'nullable|array',
            'price_range' => 'nullable|string|max:50',
            'payment_methods' => 'nullable|array',
            'delivery_options' => 'nullable|array',
            'contact_person' => 'nullable|string|max:255',
            'social_media' => 'nullable|array',
            'images' => 'nullable|array',
            'logo' => 'nullable|string|max:255',
            'cover_image' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'verified' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $business->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث العمل المحلي بنجاح',
                'business' => $business,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث العمل المحلي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified local business.
     */
    public function destroy(LocalBusiness $business): JsonResponse
    {
        try {
            $business->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف العمل المحلي بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف العمل المحلي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get business statistics.
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $filters = $request->only(['neighborhood_id', 'category', 'status']);
        
        $statistics = [
            'total_businesses' => LocalBusiness::when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })->when($filters['category'], function ($query, $category) {
                return $query->where('category', $category);
            })->when($filters['status'], function ($query, $status) {
                return $query->where('status', $status);
            })->count(),
            
            'active_businesses' => LocalBusiness::when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })->when($filters['category'], function ($query, $category) {
                return $query->where('category', $category);
            })->where('status', 'active')->count(),
            
            'average_rating' => LocalBusiness::when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })->when($filters['category'], function ($query, $category) {
                return $query->where('category', $category);
            })->avg('rating') ?? 0,
            
            'verified_businesses' => LocalBusiness::when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })->when($filters['category'], function ($query, $category) {
                return $query->where('category', $category);
            })->where('verified', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get businesses by category.
     */
    public function getByCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $limit = $validated['limit'] ?? 20;

        try {
            $businesses = LocalBusiness::where('category', $validated['category'])
                ->where('status', 'active')
                ->with(['neighborhood'])
                ->orderBy('rating', 'desc')
                ->take($limit)
                ->get(['id', 'name', 'description', 'category', 'rating', 'address', 'neighborhood_id', 'logo']);

            return response()->json([
                'success' => true,
                'businesses' => $businesses,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الأعمال المحلية: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get nearby businesses.
     */
    public function getNearby(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.5|max:50',
            'category' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $radius = $validated['radius'] ?? 5;
        $limit = $validated['limit'] ?? 20;

        try {
            // Mock implementation - in real app, this would use geospatial queries
            $businesses = LocalBusiness::where('status', 'active')
                ->when($validated['category'], function ($query, $category) {
                    return $query->where('category', $category);
                })
                ->with(['neighborhood'])
                ->orderBy('rating', 'desc')
                ->take($limit)
                ->get(['id', 'name', 'category', 'rating', 'address', 'latitude', 'longitude', 'neighborhood_id']);

            return response()->json([
                'success' => true,
                'businesses' => $businesses,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الأعمال المجاورة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Rate a business.
     */
    public function rate(Request $request, LocalBusiness $business): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'user_name' => 'required|string|max:255',
        ]);

        try {
            // Mock implementation - in real app, this would create a rating record
            // Update business rating (mock calculation)
            $newRating = ($business->rating * $business->review_count + $validated['rating']) / ($business->review_count + 1);
            $business->update([
                'rating' => $newRating,
                'review_count' => $business->review_count + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تقييم العمل بنجاح',
                'rating' => $newRating,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تقييم العمل: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search businesses.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2|max:255',
            'limit' => 'nullable|integer|min:1|max:50',
            'category' => 'nullable|string',
        ]);

        $limit = $validated['limit'] ?? 20;
        $query = $validated['query'];

        try {
            $businesses = LocalBusiness::where('status', 'active')
                ->when($validated['category'], function ($q, $category) {
                    return $q->where('category', $category);
                })
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('category', 'LIKE', "%{$query}%")
                      ->orWhere('address', 'LIKE', "%{$query}%");
                })
                ->with(['neighborhood'])
                ->orderBy('rating', 'desc')
                ->take($limit)
                ->get(['id', 'name', 'description', 'category', 'rating', 'address', 'neighborhood_id', 'logo']);

            return response()->json([
                'success' => true,
                'businesses' => $businesses,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء البحث في الأعمال المحلية: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export business data.
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
            $exportData = $this->prepareBusinessExport($validated);
            $filename = $this->generateBusinessExportFilename($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز بيانات الأعمال المحلية للتصدير',
                'filename' => $filename,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير بيانات الأعمال المحلية: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get featured businesses.
     */
    private function getFeaturedBusinesses(): array
    {
        return LocalBusiness::where('status', 'active')
            ->where('featured', true)
            ->where('rating', '>=', 4.0)
            ->orderBy('rating', 'desc')
            ->take(5)
            ->with(['neighborhood'])
            ->get(['name', 'category', 'rating', 'neighborhood_id'])
            ->toArray();
    }

    /**
     * Get popular categories.
     */
    private function getPopularCategories(): array
    {
        return LocalBusiness::select('category', DB::raw('count(*) as count'))
            ->where('status', 'active')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get()
            ->toArray();
    }

    /**
     * Get recent businesses.
     */
    private function getRecentBusinesses(): array
    {
        return LocalBusiness::where('status', 'active')
            ->latest()
            ->take(5)
            ->with(['neighborhood'])
            ->get(['name', 'category', 'neighborhood_id', 'created_at'])
            ->toArray();
    }

    /**
     * Prepare business export data.
     */
    private function prepareBusinessExport(array $options): array
    {
        $filters = $options['filters'] ?? [];
        $includeContactInfo = $options['include_contact_info'] ?? false;
        $includeHours = $options['include_hours'] ?? false;

        $query = LocalBusiness::with(['neighborhood']);
        
        if (isset($filters['neighborhood_id'])) {
            $query->where('neighborhood_id', $filters['neighborhood_id']);
        }
        
        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $businesses = $query->get();

        $data = $businesses->map(function ($business) use ($includeContactInfo, $includeHours) {
            $item = [
                'id' => $business->id,
                'name' => $business->name,
                'neighborhood' => $business->neighborhood?->name ?? 'غير معروف',
                'category' => $business->category,
                'description' => $business->description,
                'address' => $business->address,
                'status' => $business->status,
                'rating' => $business->rating,
                'review_count' => $business->review_count,
                'verified' => $business->verified,
                'featured' => $business->featured,
                'created_at' => $business->created_at->format('Y-m-d H:i:s'),
            ];

            if ($includeContactInfo) {
                $item['phone'] = $business->phone;
                $item['email'] = $business->email;
                $item['website'] = $business->website;
                $item['contact_person'] = $business->contact_person;
            }

            if ($includeHours) {
                $item['opening_hours'] = $business->opening_hours;
            }

            return $item;
        });

        return [
            'headers' => ['ID', 'Name', 'Neighborhood', 'Category', 'Status', 'Rating', 'Verified', 'Featured', 'Created At'],
            'rows' => $data->toArray(),
        ];
    }

    /**
     * Generate business export filename.
     */
    private function generateBusinessExportFilename(array $options): string
    {
        $format = $options['format'];
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "local_businesses_export_{$timestamp}.{$format}";
    }
}
