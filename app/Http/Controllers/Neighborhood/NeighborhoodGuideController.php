<?php

namespace App\Http\Controllers\Neighborhood;

use App\Http\Controllers\Controller;
use App\Models\Neighborhood\NeighborhoodGuide;
use App\Models\Neighborhood\Neighborhood;
use App\Models\Neighborhood\LocalBusiness;
use App\Models\Neighborhood\CommunityAmenity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class NeighborhoodGuideController extends Controller
{
    /**
     * Display the neighborhood guides dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['neighborhood_id', 'guide_type', 'status']);
        
        // Get guide statistics
        $stats = [
            'total_guides' => NeighborhoodGuide::count(),
            'published_guides' => NeighborhoodGuide::where('status', 'published')->count(),
            'draft_guides' => NeighborhoodGuide::where('status', 'draft')->count(),
            'total_views' => NeighborhoodGuide::sum('view_count') ?? 0,
            'average_rating' => NeighborhoodGuide::avg('rating') ?? 0,
            'featured_guides' => $this->getFeaturedGuides(),
            'popular_guides' => $this->getPopularGuides(),
        ];

        // Get recent guides
        $recentGuides = NeighborhoodGuide::with(['neighborhood'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($guide) {
                return [
                    'id' => $guide->id,
                    'title' => $guide->title,
                    'neighborhood' => $guide->neighborhood?->name ?? 'غير معروف',
                    'guide_type' => $guide->guide_type,
                    'description' => substr($guide->description, 0, 100) . '...',
                    'rating' => $guide->rating,
                    'view_count' => $guide->view_count,
                    'status' => $guide->status,
                    'created_at' => $guide->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Get guides with filters
        $guides = NeighborhoodGuide::with(['neighborhood'])
            ->when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })
            ->when($filters['guide_type'], function ($query, $guideType) {
                return $query->where('guide_type', $guideType);
            })
            ->when($filters['status'], function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(12);

        // Get neighborhoods and guide types for filters
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $guideTypes = ['general', 'living', 'schools', 'healthcare', 'shopping', 'transportation', 'recreation', 'safety'];
        $statuses = ['draft', 'published', 'archived'];

        return Inertia::render('NeighborhoodGuide/Index', [
            'stats' => $stats,
            'recentGuides' => $recentGuides,
            'guides' => $guides,
            'neighborhoods' => $neighborhoods,
            'guideTypes' => $guideTypes,
            'statuses' => $statuses,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new neighborhood guide.
     */
    public function create(): \Inertia\Response
    {
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $guideTypes = ['general', 'living', 'schools', 'healthcare', 'shopping', 'transportation', 'recreation', 'safety'];
        $statuses = ['draft', 'published', 'archived'];

        return Inertia::render('NeighborhoodGuide/Create', [
            'neighborhoods' => $neighborhoods,
            'guideTypes' => $guideTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Store a newly created neighborhood guide.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'neighborhood_id' => 'required|exists:neighborhoods,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'guide_type' => 'required|string',
            'status' => 'required|string',
            'content' => 'nullable|array',
            'content.sections' => 'nullable|array',
            'content.highlights' => 'nullable|array',
            'content.tips' => 'nullable|array',
            'content.warnings' => 'nullable|array',
            'content.recommendations' => 'nullable|array',
            'media' => 'nullable|array',
            'media.images' => 'nullable|array',
            'media.videos' => 'nullable|array',
            'media.documents' => 'nullable|array',
            'contact_info' => 'nullable|array',
            'contact_info.phone' => 'nullable|string',
            'contact_info.email' => 'nullable|email',
            'contact_info.website' => 'nullable|url',
            'contact_info.address' => 'nullable|string',
            'useful_links' => 'nullable|array',
            'emergency_contacts' => 'nullable|array',
            'transportation_info' => 'nullable|array',
            'local_services' => 'nullable|array',
            'cost_of_living' => 'nullable|array',
            'weather_info' => 'nullable|array',
            'cultural_info' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $guide = NeighborhoodGuide::create([
                'neighborhood_id' => $validated['neighborhood_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'guide_type' => $validated['guide_type'],
                'status' => $validated['status'],
                'content' => $validated['content'] ?? [],
                'media' => $validated['media'] ?? [],
                'contact_info' => $validated['contact_info'] ?? [],
                'useful_links' => $validated['useful_links'] ?? [],
                'emergency_contacts' => $validated['emergency_contacts'] ?? [],
                'transportation_info' => $validated['transportation_info'] ?? [],
                'local_services' => $validated['local_services'] ?? [],
                'cost_of_living' => $validated['cost_of_living'] ?? [],
                'weather_info' => $validated['weather_info'] ?? [],
                'cultural_info' => $validated['cultural_info'] ?? [],
                'metadata' => $validated['metadata'] ?? [],
                'view_count' => 0,
                'rating' => 0,
                'review_count' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء دليل الحي بنجاح',
                'guide' => $guide,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء دليل الحي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified neighborhood guide.
     */
    public function show(NeighborhoodGuide $guide): \Inertia\Response
    {
        // Increment view count
        $guide->increment('view_count');

        $guide->load(['neighborhood']);

        // Get related guides
        $relatedGuides = NeighborhoodGuide::where('neighborhood_id', $guide->neighborhood_id)
            ->where('id', '!=', $guide->id)
            ->where('status', 'published')
            ->take(5)
            ->get(['id', 'title', 'guide_type', 'rating', 'view_count']);

        // Get neighborhood businesses and amenities
        $businesses = LocalBusiness::where('neighborhood_id', $guide->neighborhood_id)
            ->take(10)
            ->get(['id', 'name', 'category', 'rating', 'address']);

        $amenities = CommunityAmenity::where('neighborhood_id', $guide->neighborhood_id)
            ->take(10)
            ->get(['id', 'name', 'type', 'description']);

        return Inertia::render('NeighborhoodGuide/Show', [
            'guide' => $guide,
            'relatedGuides' => $relatedGuides,
            'businesses' => $businesses,
            'amenities' => $amenities,
        ]);
    }

    /**
     * Show the form for editing the specified neighborhood guide.
     */
    public function edit(NeighborhoodGuide $guide): \Inertia\Response
    {
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $guideTypes = ['general', 'living', 'schools', 'healthcare', 'shopping', 'transportation', 'recreation', 'safety'];
        $statuses = ['draft', 'published', 'archived'];

        return Inertia::render('NeighborhoodGuide/Edit', [
            'guide' => $guide,
            'neighborhoods' => $neighborhoods,
            'guideTypes' => $guideTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified neighborhood guide.
     */
    public function update(Request $request, NeighborhoodGuide $guide): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'guide_type' => 'required|string',
            'status' => 'required|string',
            'content' => 'nullable|array',
            'media' => 'nullable|array',
            'contact_info' => 'nullable|array',
            'useful_links' => 'nullable|array',
            'emergency_contacts' => 'nullable|array',
            'transportation_info' => 'nullable|array',
            'local_services' => 'nullable|array',
            'cost_of_living' => 'nullable|array',
            'weather_info' => 'nullable|array',
            'cultural_info' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $guide->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث دليل الحي بنجاح',
                'guide' => $guide,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث دليل الحي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified neighborhood guide.
     */
    public function destroy(NeighborhoodGuide $guide): JsonResponse
    {
        try {
            $guide->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف دليل الحي بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف دليل الحي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get guide statistics.
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $filters = $request->only(['neighborhood_id', 'guide_type', 'status']);
        
        $statistics = [
            'total_guides' => NeighborhoodGuide::when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })->when($filters['guide_type'], function ($query, $guideType) {
                return $query->where('guide_type', $guideType);
            })->when($filters['status'], function ($query, $status) {
                return $query->where('status', $status);
            })->count(),
            
            'published_guides' => NeighborhoodGuide::when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })->when($filters['guide_type'], function ($query, $guideType) {
                return $query->where('guide_type', $guideType);
            })->where('status', 'published')->count(),
            
            'total_views' => NeighborhoodGuide::when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })->when($filters['guide_type'], function ($query, $guideType) {
                return $query->where('guide_type', $guideType);
            })->sum('view_count'),
            
            'average_rating' => NeighborhoodGuide::when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })->when($filters['guide_type'], function ($query, $guideType) {
                return $query->where('guide_type', $guideType);
            })->avg('rating') ?? 0,
        ];

        return response()->json([
            'success' => true,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get guide content sections.
     */
    public function getContentSections(NeighborhoodGuide $guide): JsonResponse
    {
        try {
            $content = $guide->content ?? [];
            $sections = $content['sections'] ?? [];

            return response()->json([
                'success' => true,
                'sections' => $sections,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب محتوى الدليل: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Rate a guide.
     */
    public function rate(Request $request, NeighborhoodGuide $guide): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'user_name' => 'required|string|max:255',
        ]);

        try {
            // Mock implementation - in real app, this would create a rating record
            // Update guide rating (mock calculation)
            $newRating = ($guide->rating * $guide->review_count + $validated['rating']) / ($guide->review_count + 1);
            $guide->update([
                'rating' => $newRating,
                'review_count' => $guide->review_count + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تقييم الدليل بنجاح',
                'rating' => $newRating,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تقييم الدليل: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search guides.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2|max:255',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $limit = $validated['limit'] ?? 20;
        $query = $validated['query'];

        try {
            $guides = NeighborhoodGuide::where('status', 'published')
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('guide_type', 'LIKE', "%{$query}%");
                })
                ->with(['neighborhood'])
                ->take($limit)
                ->get(['id', 'title', 'description', 'guide_type', 'rating', 'view_count', 'neighborhood_id']);

            return response()->json([
                'success' => true,
                'guides' => $guides,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء البحث في الأدلة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export guide data.
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,json',
            'filters' => 'nullable|array',
            'include_content' => 'nullable|boolean',
            'include_media' => 'nullable|boolean',
        ]);

        try {
            $exportData = $this->prepareGuideExport($validated);
            $filename = $this->generateGuideExportFilename($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز بيانات الأدلة للتصدير',
                'filename' => $filename,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير بيانات الأدلة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get featured guides.
     */
    private function getFeaturedGuides(): array
    {
        return NeighborhoodGuide::where('status', 'published')
            ->where('rating', '>=', 4.0)
            ->orderBy('rating', 'desc')
            ->take(5)
            ->with(['neighborhood'])
            ->get(['title', 'neighborhood_id', 'guide_type', 'rating', 'view_count'])
            ->toArray();
    }

    /**
     * Get popular guides.
     */
    private function getPopularGuides(): array
    {
        return NeighborhoodGuide::where('status', 'published')
            ->orderBy('view_count', 'desc')
            ->take(5)
            ->with(['neighborhood'])
            ->get(['title', 'neighborhood_id', 'guide_type', 'rating', 'view_count'])
            ->toArray();
    }

    /**
     * Prepare guide export data.
     */
    private function prepareGuideExport(array $options): array
    {
        $filters = $options['filters'] ?? [];
        $includeContent = $options['include_content'] ?? false;
        $includeMedia = $options['include_media'] ?? false;

        $query = NeighborhoodGuide::with(['neighborhood']);
        
        if (isset($filters['neighborhood_id'])) {
            $query->where('neighborhood_id', $filters['neighborhood_id']);
        }
        
        if (isset($filters['guide_type'])) {
            $query->where('guide_type', $filters['guide_type']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $guides = $query->get();

        $data = $guides->map(function ($guide) use ($includeContent, $includeMedia) {
            $item = [
                'id' => $guide->id,
                'title' => $guide->title,
                'neighborhood' => $guide->neighborhood?->name ?? 'غير معروف',
                'guide_type' => $guide->guide_type,
                'description' => $guide->description,
                'status' => $guide->status,
                'rating' => $guide->rating,
                'review_count' => $guide->review_count,
                'view_count' => $guide->view_count,
                'created_at' => $guide->created_at->format('Y-m-d H:i:s'),
            ];

            if ($includeContent) {
                $item['content'] = $guide->content;
            }

            if ($includeMedia) {
                $item['media'] = $guide->media;
            }

            return $item;
        });

        return [
            'headers' => ['ID', 'Title', 'Neighborhood', 'Guide Type', 'Status', 'Rating', 'View Count', 'Created At'],
            'rows' => $data->toArray(),
        ];
    }

    /**
     * Generate guide export filename.
     */
    private function generateGuideExportFilename(array $options): string
    {
        $format = $options['format'];
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "neighborhood_guides_export_{$timestamp}.{$format}";
    }
}
