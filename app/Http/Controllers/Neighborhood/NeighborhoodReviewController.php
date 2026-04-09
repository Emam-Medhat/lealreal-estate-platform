<?php

namespace App\Http\Controllers\Neighborhood;

use App\Http\Controllers\Controller;
use App\Models\Neighborhood\NeighborhoodReview;
use App\Models\Neighborhood\Neighborhood;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class NeighborhoodReviewController extends Controller
{
    /**
     * Display the neighborhood reviews dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['neighborhood_id', 'rating_range', 'status']);
        
        // Get review statistics
        $stats = [
            'total_reviews' => NeighborhoodReview::count(),
            'published_reviews' => NeighborhoodReview::where('status', 'published')->count(),
            'average_rating' => NeighborhoodReview::avg('rating') ?? 0,
            'total_ratings' => NeighborhoodReview::sum('rating') ?? 0,
            'featured_reviews' => $this->getFeaturedReviews(),
            'recent_reviews' => $this->getRecentReviews(),
            'rating_distribution' => $this->getRatingDistribution(),
        ];

        // Get reviews with filters
        $reviews = NeighborhoodReview::with(['neighborhood'])
            ->when($filters['neighborhood_id'], function ($query, $neighborhoodId) {
                return $query->where('neighborhood_id', $neighborhoodId);
            })
            ->when($filters['rating_range'], function ($query, $range) {
                if ($range === 'excellent') {
                    return $query->where('rating', '>=', 4.5);
                } elseif ($range === 'good') {
                    return $query->whereBetween('rating', [3.5, 4.5]);
                } elseif ($range === 'average') {
                    return $query->whereBetween('rating', [2.5, 3.5]);
                } elseif ($range === 'poor') {
                    return $query->where('rating', '<', 2.5);
                }
            })
            ->when($filters['status'], function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(12);

        // Get neighborhoods and rating ranges for filters
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $ratingRanges = ['excellent', 'good', 'average', 'poor'];
        $statuses = ['draft', 'published', 'hidden', 'reported'];

        return Inertia::render('NeighborhoodReview/Index', [
            'stats' => $stats,
            'reviews' => $reviews,
            'neighborhoods' => $neighborhoods,
            'ratingRanges' => $ratingRanges,
            'statuses' => $statuses,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new neighborhood review.
     */
    public function create(): \Inertia\Response
    {
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $ratings = [1, 2, 3, 4, 5];

        return Inertia::render('NeighborhoodReview/Create', [
            'neighborhoods' => $neighborhoods,
            'ratings' => $ratings,
        ]);
    }

    /**
     * Store a newly created neighborhood review.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'neighborhood_id' => 'required|exists:neighborhoods,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10|max:2000',
            'rating' => 'required|integer|min:1|max:5',
            'status' => 'required|string',
            'reviewer_name' => 'required|string|max:255',
            'reviewer_email' => 'nullable|email|max:255',
            'reviewer_phone' => 'nullable|string|max:20',
            'reviewer_type' => 'nullable|string|in:resident,owner,visitor,professional',
            'pros' => 'nullable|array',
            'cons' => 'nullable|array',
            'recommendation' => 'nullable|string|in:yes,no,maybe',
            'experience_period' => 'nullable|string|max:100',
            'property_type' => 'nullable|string|max:100',
            'property_details' => 'nullable|array',
            'property_details.property_type' => 'nullable|string',
            'property_details.property_size' => 'nullable|string',
            'property_details.property_value' => 'nullable|string',
            'property_details.ownership_duration' => 'nullable|string',
            'community_aspects' => 'nullable|array',
            'community_aspects.safety' => 'nullable|integer|min:1|max:5',
            'community_aspects.cleanliness' => 'nullable|integer|min:1|max:5',
            'community_aspects.friendliness' => 'nullable|integer|min:1|max:5',
            'community_aspects.activities' => 'nullable|integer|min:1|max:5',
            'community_aspects.facilities' => 'nullable|integer|min:1|max:5',
            'community_aspects.transportation' => 'nullable|integer|min:1|max:5',
            'community_aspects.schools' => 'nullable|integer|min:1|max:5',
            'community_aspects.shopping' => 'nullable|integer|min:1|max:5',
            'community_aspects.healthcare' => 'nullable|integer|min:1|max:5',
            'improvement_suggestions' => 'nullable|array',
            'images' => 'nullable|array',
            'photos' => 'nullable|array',
            'videos' => 'nullable|array',
            'verified' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $review = NeighborhoodReview::create([
                'neighborhood_id' => $validated['neighborhood_id'],
                'title' => $validated['title'],
                'content' => $validated['content'],
                'rating' => $validated['rating'],
                'status' => $validated['status'],
                'reviewer_name' => $validated['reviewer_name'],
                'reviewer_email' => $validated['reviewer_email'] ?? null,
                'reviewer_phone' => $validated['reviewer_phone'] ?? null,
                'reviewer_type' => $validated['reviewer_type'] ?? null,
                'pros' => $validated['pros'] ?? [],
                'cons' => $validated['cons'] ?? [],
                'recommendation' => $validated['recommendation'] ?? null,
                'experience_period' => $validated['experience_period'] ?? null,
                'property_type' => $validated['property_type'] ?? null,
                'property_details' => $validated['property_details'] ?? [],
                'community_aspects' => $validated['community_aspects'] ?? [],
                'improvement_suggestions' => $validated['improvement_suggestions'] ?? [],
                'images' => $validated['images'] ?? [],
                'photos' => $validated['photos'] ?? [],
                'videos' => $validated['videos'] ?? [],
                'verified' => $validated['verified'] ?? false,
                'featured' => $validated['featured'] ?? false,
                'tags' => $validated['tags'] ?? [],
                'metadata' => $validated['metadata'] ?? [],
                'helpful_count' => 0,
                'report_count' => 0,
                'view_count' => 0,
            ]);

            // Update neighborhood rating
            $this->updateNeighborhoodRating($validated['neighborhood_id']);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة تقييم الحي بنجاح',
                'review' => $review,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة تقييم الحي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified neighborhood review.
     */
    public function show(NeighborhoodReview $review): \Inertia\Response
    {
        // Increment view count
        $review->increment('view_count');

        $review->load(['neighborhood']);

        // Get related reviews
        $relatedReviews = NeighborhoodReview::where('neighborhood_id', $review->neighborhood_id)
            ->where('id', '!=', $review->id)
            ->where('status', 'published')
            ->take(3)
            ->get(['id', 'title', 'rating', 'reviewer_name', 'created_at']);

        return Inertia::render('NeighborhoodReview/Show', [
            'review' => $review,
            'relatedReviews' => $relatedReviews,
        ]);
    }

    /**
     * Show the form for editing the specified neighborhood review.
     */
    public function edit(NeighborhoodReview $review): \Inertia\Response
    {
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $ratings = [1, 2, 3, 4, 5];

        return Inertia::render('NeighborhoodReview/Edit', [
            'review' => $review,
            'neighborhoods' => $neighborhoods,
            'ratings' => $ratings,
        ]);
    }

    /**
     * Update the specified neighborhood review.
     */
    public function update(Request $request, NeighborhoodReview $review): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10|max:2000',
            'rating' => 'required|integer|min:1|max:5',
            'status' => 'required|string',
            'reviewer_name' => 'required|string|max:255',
            'reviewer_email' => 'nullable|email|max:255',
            'reviewer_phone' => 'nullable|string|max:20',
            'reviewer_type' => 'nullable|string|in:resident,owner,visitor,professional',
            'pros' => 'nullable|array',
            'cons' => 'nullable|array',
            'recommendation' => 'nullable|string|in:yes,no,maybe',
            'experience_period' => 'nullable|string|max:100',
            'property_type' => 'nullable|string|max:100',
            'property_details' => 'nullable|array',
            'community_aspects' => 'nullable|array',
            'improvement_suggestions' => 'nullable|array',
            'images' => 'nullable|array',
            'verified' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $review->update($validated);

            // Update neighborhood rating
            $this->updateNeighborhoodRating($review->neighborhood_id);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث تقييم الحي بنجاح',
                'review' => $review,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث تقييم الحي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified neighborhood review.
     */
    public function destroy(NeighborhoodReview $review): JsonResponse
    {
        try {
            $neighborhoodId = $review->neighborhood_id;
            $review->delete();

            // Update neighborhood rating
            $this->updateNeighborhoodRating($neighborhoodId);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف تقييم الحي بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف تقييم الحي: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark review as helpful.
     */
    public function markHelpful(Request $request, NeighborhoodReview $review): JsonResponse
    {
        try {
            $review->increment('helpful_count');

            return response()->json([
                'success' => true,
                'message' => 'تم تمييز التقييم كمفيد',
                'helpful_count' => $review->helpful_count,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تمييز التقييم: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Report a review.
     */
    public function report(Request $request, NeighborhoodReview $review): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'reporter_name' => 'required|string|max:255',
            'reporter_email' => 'required|email|max:255',
        ]);

        try {
            // Mock implementation - in real app, this would create a report record
            $review->increment('report_count');

            return response()->json([
                'success' => true,
                'message' => 'تم إبلاغ عن التقييم بنجاح',
                'report_count' => $review->report_count,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إبلاغ عن التقييم: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get review statistics.
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $filters = $request->only(['neighborhood_id', 'rating_range', 'status']);
        
        $query = NeighborhoodReview::query();
        
        if ($filters['neighborhood_id']) {
            $query->where('neighborhood_id', $filters['neighborhood_id']);
        }
        
        if ($filters['rating_range']) {
            if ($filters['rating_range'] === 'excellent') {
                $query->where('rating', '>=', 4.5);
            } elseif ($filters['rating_range'] === 'good') {
                $query->whereBetween('rating', [3.5, 4.5]);
            } elseif ($filters['rating_range'] === 'average') {
                $query->whereBetween('rating', [2.5, 3.5]);
            } elseif ($rating_range === 'poor') {
                $query->where('rating', '<', 2.5);
            }
        }
        
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        $statistics = [
            'total_reviews' => $query->count(),
            'published_reviews' => $query->where('status', 'published')->count(),
            'average_rating' => $query->avg('rating') ?? 0,
            'total_ratings' => $query->sum('rating') ?? 0,
            'helpful_count' => $query->sum('helpful_count') ?? 0,
            'report_count' => $query->sum('report_count') ?? 0,
        ];

        return response()->json([
            'success' => true,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get reviews by neighborhood.
     */
    public function getByNeighborhood(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'neighborhood_id' => 'required|exists:neighborhoods,id',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $limit = $validated['limit'] ?? 20;

        try {
            $reviews = NeighborhoodReview::where('neighborhood_id', $validated['neighborhood_id'])
                ->where('status', 'published')
                ->orderBy('rating', 'desc')
                ->take($limit)
                ->get(['id', 'title', 'content', 'rating', 'reviewer_name', 'created_at']);

            return response()->json([
                'success' => true,
                'reviews' => $reviews,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب التقييمات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search reviews.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2|max:255',
            'limit' => 'nullable|integer|min:1|max:50',
            'neighborhood_id' => 'nullable|exists:neighborhoods,id',
        ]);

        $limit = $validated['limit'] ?? 20;
        $query = $validated['query'];

        try {
            $reviews = NeighborhoodReview::where('status', 'published')
                ->when($validated['neighborhood_id'], function ($q, $neighborhoodId) {
                    return $q->where('neighborhood_id', $neighborhoodId);
                })
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('content', 'LIKE', "%{$query}%")
                      ->orWhere('reviewer_name', 'LIKE', "%{$query}%");
                })
                ->orderBy('rating', 'desc')
                ->take($limit)
                ->get(['id', 'title', 'content', 'rating', 'reviewer_name', 'neighborhood_id', 'created_at']);

            return response()->json([
                'success' => true,
                'reviews' => $reviews,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء البحث في التقييمات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export review data.
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,json',
            'filters' => 'nullable|array',
            'include_details' => 'nullable|boolean',
            'include_community_aspects' => 'nullable|boolean',
        ]);

        try {
            $exportData = $this->prepareReviewExport($validated);
            $filename = $this->generateReviewExportFilename($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز بيانات التقييمات للتصدير',
                'filename' => $filename,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير بيانات التقييمات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get featured reviews.
     */
    private function getFeaturedReviews(): array
    {
        return NeighborhoodReview::where('status', 'published')
            ->where('featured', true)
            ->where('rating', '>=', 4.5)
            ->orderBy('rating', 'desc')
            ->take(5)
            ->with(['neighborhood'])
            ->get(['title', 'rating', 'reviewer_name', 'neighborhood_id'])
            ->toArray();
    }

    /**
     * Get recent reviews.
     */
    private function getRecentReviews(): array
    {
        return NeighborhoodReview::where('status', 'published')
            ->latest()
            ->take(5)
            ->with(['neighborhood'])
            ->get(['title', 'rating', 'reviewer_name', 'neighborhood_id', 'created_at'])
            ->toArray();
    }

    /**
     * Get rating distribution.
     */
    private function getRatingDistribution(): array
    {
        return NeighborhoodReview::select('rating', DB::raw('count(*) as count'))
            ->where('status', 'published')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Update neighborhood rating.
     */
    private function updateNeighborhoodRating(int $neighborhoodId): void
    {
        $reviews = NeighborhoodReview::where('neighborhood_id', $neighborhoodId)
            ->where('status', 'published')
            ->get(['rating']);

        if ($reviews->count() > 0) {
            $averageRating = $reviews->avg('rating');
            $totalRating = $reviews->sum('rating');
            $reviewCount = $reviews->count();

            Neighborhood::where('id', $neighborhoodId)->update([
                'rating' => $averageRating,
                'review_count' => $reviewCount,
            ]);
        }
    }

    /**
     * Prepare review export data.
     */
    private function prepareReviewExport(array $options): array
    {
        $filters = $options['filters'] ?? [];
        $includeDetails = $options['include_details'] ?? false;
        $includeCommunityAspects = $options['include_community_aspects'] ?? false;

        $query = NeighborhoodReview::with(['neighborhood']);
        
        if (isset($filters['neighborhood_id'])) {
            $query->where('neighborhood_id', $filters['neighborhood_id']);
        }
        
        if (isset($filters['rating_range'])) {
            if ($filters['rating_range'] === 'excellent') {
                $query->where('rating', '>=', 4.5);
            } elseif ($filters['rating_range'] === 'good') {
                $query->whereBetween('rating', [3.5, 4.5]);
            } elseif ($filters['rating_range'] === 'average') {
                $query->whereBetween('rating', [2.5, 3.5]);
            } elseif ($filters['rating_range'] === 'poor') {
                $query->where('rating', '<', 2.5);
            }
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $reviews = $query->get();

        $data = $reviews->map(function ($review) use ($includeDetails, $includeCommunityAspects) {
            $item = [
                'id' => $review->id,
                'title' => $review->title,
                'neighborhood' => $review->neighborhood?->name ?? 'غير معروف',
                'rating' => $review->rating,
                'reviewer_name' => $review->reviewer_name,
                'reviewer_type' => $review->reviewer_type,
                'recommendation' => $review->recommendation,
                'experience_period' => $review->experience_period,
                'property_type' => $review->property_type,
                'status' => $review->status,
                'helpful_count' => $review->helpful_count,
                'report_count' => $review->report_count,
                'view_count' => $review->view_count,
                'verified' => $review->verified,
                'featured' => $review->featured,
                'created_at' => $review->created_at->format('Y-m-d H:i:s'),
            ];

            if ($includeDetails) {
                $item['content'] = $review->content;
                $item['pros'] = $review->pros;
                $item['cons'] = $review->cons;
                $item['improvement_suggestions'] = $review->improvement_suggestions;
            }

            if ($includeCommunityAspects) {
                $item['community_aspects'] = $review->community_aspects;
            }

            return $item;
        });

        return [
            'headers' => ['ID', 'Title', 'Neighborhood', 'Rating', 'Reviewer', 'Type', 'Recommendation', 'Status', 'Created At'],
            'rows' => $data->toArray(),
        ];
    }

    /**
     * Generate review export filename.
     */
    private function generateReviewExportFilename(array $options): string
    {
        $format = $options['format'];
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "neighborhood_reviews_export_{$timestamp}.{$format}";
    }
}
