<?php

namespace App\Http\Controllers\Neighborhood;

use App\Http\Controllers\Controller;
use App\Models\Neighborhood\CommunityNews;
use App\Models\Neighborhood\Community;
use App\Models\Neighborhood\Neighborhood;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CommunityNewsController extends Controller
{
    /**
     * Display the community news dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['community_id', 'news_type', 'status', 'date_range']);
        
        // Get news statistics
        $stats = [
            'total_news' => CommunityNews::count(),
            'published_news' => CommunityNews::where('status', 'published')->count(),
            'featured_news' => CommunityNews::where('is_featured', true)->count(),
            'total_views' => CommunityNews::sum('view_count') ?? 0,
            'popular_news' => $this->getPopularNews(),
            'recent_news' => $this->getRecentNews(),
            'monthly_news' => $this->getMonthlyNews(),
        ];

        // Get news with filters
        $news = CommunityNews::with(['community', 'community.neighborhood', 'author'])
            ->when($filters['community_id'], function ($query, $communityId) {
                return $query->where('community_id', $communityId);
            })
            ->when($filters['news_type'], function ($query, $newsType) {
                return $query->where('news_type', $newsType);
            })
            ->when($filters['status'], function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($filters['date_range'], function ($query, $dateRange) {
                if ($dateRange === 'today') {
                    return $query->whereDate('published_at', today());
                } elseif ($dateRange === 'week') {
                    return $query->whereBetween('published_at', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($dateRange === 'month') {
                    return $query->whereMonth('published_at', now()->month);
                } elseif ($dateRange === 'year') {
                    return $query->whereYear('published_at', now()->year);
                }
            })
            ->orderBy('is_featured', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        // Get communities and news types for filters
        $communities = Community::where('status', 'active')->with(['neighborhood'])->get(['id', 'name', 'neighborhood_id']);
        $newsTypes = ['announcement', 'event', 'update', 'warning', 'celebration', 'policy', 'maintenance', 'community', 'other'];
        $statuses = ['draft', 'published', 'archived', 'hidden'];
        $dateRanges = ['today', 'week', 'month', 'year'];

        return Inertia::render('CommunityNews/Index', [
            'stats' => $stats,
            'news' => $news,
            'communities' => $communities,
            'newsTypes' => $newsTypes,
            'statuses' => $statuses,
            'dateRanges' => $dateRanges,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new community news.
     */
    public function create(): \Inertia\Response
    {
        $communities = Community::where('status', 'active')->with(['neighborhood'])->get(['id', 'name', 'neighborhood_id']);
        $newsTypes = ['announcement', 'event', 'update', 'warning', 'celebration', 'policy', 'maintenance', 'community', 'other'];
        $statuses = ['draft', 'published', 'archived', 'hidden'];

        return Inertia::render('CommunityNews/Create', [
            'communities' => $communities,
            'newsTypes' => $newsTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Store a newly created community news.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'community_id' => 'required|exists:communities,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10|max:5000',
            'summary' => 'nullable|string|max:500',
            'news_type' => 'required|string',
            'status' => 'required|string',
            'author_name' => 'required|string|max:255',
            'author_email' => 'nullable|email|max:255',
            'author_phone' => 'nullable|string|max:20',
            'author_role' => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'is_featured' => 'nullable|boolean',
            'is_pinned' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',
            'send_notifications' => 'nullable|boolean',
            'target_audience' => 'nullable|array',
            'target_audience.residents' => 'nullable|boolean',
            'target_audience.owners' => 'nullable|boolean',
            'target_audience.managers' => 'nullable|boolean',
            'target_audience.all' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'category' => 'nullable|string|max:100',
            'images' => 'nullable|array',
            'cover_image' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'videos' => 'nullable|array',
            'attachments' => 'nullable|array',
            'attachments.*.name' => 'required|string|max:255',
            'attachments.*.type' => 'required|string|max:50',
            'attachments.*.size' => 'required|integer|min:0',
            'attachments.*.url' => 'required|string|max:500',
            'related_links' => 'nullable|array',
            'related_links.*.title' => 'required|string|max:255',
            'related_links.*.url' => 'required|url|max:500',
            'contact_info' => 'nullable|array',
            'contact_info.email' => 'nullable|email|max:255',
            'contact_info.phone' => 'nullable|string|max:20',
            'contact_info.website' => 'nullable|url|max:255',
            'social_sharing' => 'nullable|array',
            'social_sharing.facebook' => 'nullable|url',
            'social_sharing.twitter' => 'nullable|url',
            'social_sharing.instagram' => 'nullable|url',
            'social_sharing.linkedin' => 'nullable|url',
            'metadata' => 'nullable|array',
        ]);

        try {
            $news = CommunityNews::create([
                'community_id' => $validated['community_id'],
                'title' => $validated['title'],
                'content' => $validated['content'],
                'summary' => $validated['summary'] ?? null,
                'news_type' => $validated['news_type'],
                'status' => $validated['status'],
                'author_name' => $validated['author_name'],
                'author_email' => $validated['author_email'] ?? null,
                'author_phone' => $validated['author_phone'] ?? null,
                'author_role' => $validated['author_role'] ?? null,
                'published_at' => $validated['published_at'] ?? now(),
                'expires_at' => $validated['expires_at'] ?? null,
                'priority' => $validated['priority'] ?? 'medium',
                'is_featured' => $validated['is_featured'] ?? false,
                'is_pinned' => $validated['is_pinned'] ?? false,
                'allow_comments' => $validated['allow_comments'] ?? true,
                'send_notifications' => $validated['send_notifications'] ?? false,
                'target_audience' => $validated['target_audience'] ?? [],
                'tags' => $validated['tags'] ?? [],
                'category' => $validated['category'] ?? null,
                'images' => $validated['images'] ?? [],
                'cover_image' => $validated['cover_image'] ?? null,
                'gallery' => $validated['gallery'] ?? [],
                'videos' => $validated['videos'] ?? [],
                'attachments' => $validated['attachments'] ?? [],
                'related_links' => $validated['related_links'] ?? [],
                'contact_info' => $validated['contact_info'] ?? [],
                'social_sharing' => $validated['social_sharing'] ?? [],
                'metadata' => $validated['metadata'] ?? [],
                'view_count' => 0,
                'like_count' => 0,
                'comment_count' => 0,
                'share_count' => 0,
            ]);

            // Update community activity
            $this->updateCommunityActivity($validated['community_id']);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء خبر المجتمع بنجاح',
                'news' => $news,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء خبر المجتمع: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified community news.
     */
    public function show(CommunityNews $news): \Inertia\Response
    {
        // Increment view count
        $news->increment('view_count');

        $news->load(['community', 'community.neighborhood', 'author']);

        // Get related news
        $relatedNews = CommunityNews::where('community_id', $news->community_id)
            ->where('id', '!=', $news->id)
            ->where('status', 'published')
            ->take(3)
            ->get(['id', 'title', 'news_type', 'published_at']);

        // Get recent comments (mock data for now)
        $recentComments = $this->getRecentComments($news);

        return Inertia::render('CommunityNews/Show', [
            'news' => $news,
            'relatedNews' => $relatedNews,
            'recentComments' => $recentComments,
        ]);
    }

    /**
     * Show the form for editing the specified community news.
     */
    public function edit(CommunityNews $news): \Inertia\Response
    {
        $communities = Community::where('status', 'active')->with(['neighborhood'])->get(['id', 'name', 'neighborhood_id']);
        $newsTypes = ['announcement', 'event', 'update', 'warning', 'celebration', 'policy', 'maintenance', 'community', 'other'];
        $statuses = ['draft', 'published', 'archived', 'hidden'];

        return Inertia::render('CommunityNews/Edit', [
            'news' => $news,
            'communities' => $communities,
            'newsTypes' => $newsTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified community news.
     */
    public function update(Request $request, CommunityNews $news): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10|max:5000',
            'summary' => 'nullable|string|max:500',
            'news_type' => 'required|string',
            'status' => 'required|string',
            'author_name' => 'required|string|max:255',
            'author_email' => 'nullable|email|max:255',
            'author_phone' => 'nullable|string|max:20',
            'author_role' => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'is_featured' => 'nullable|boolean',
            'is_pinned' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',
            'send_notifications' => 'nullable|boolean',
            'target_audience' => 'nullable|array',
            'tags' => 'nullable|array',
            'category' => 'nullable|string|max:100',
            'images' => 'nullable|array',
            'cover_image' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'videos' => 'nullable|array',
            'attachments' => 'nullable|array',
            'related_links' => 'nullable|array',
            'contact_info' => 'nullable|array',
            'social_sharing' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $news->update($validated);

            // Update community activity
            $this->updateCommunityActivity($news->community_id);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث خبر المجتمع بنجاح',
                'news' => $news,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث خبر المجتمع: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified community news.
     */
    public function destroy(CommunityNews $news): JsonResponse
    {
        try {
            $communityId = $news->community_id;
            $news->delete();

            // Update community activity
            $this->updateCommunityActivity($communityId);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف خبر المجتمع بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف خبر المجتمع: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Like a news article.
     */
    public function like(Request $request, CommunityNews $news): JsonResponse
    {
        try {
            // Mock implementation - in real app, this would create a like record
            $news->increment('like_count');

            return response()->json([
                'success' => true,
                'message' => 'تم إعجاب الخبر بنجاح',
                'like_count' => $news->like_count,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إعجاب الخبر: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Comment on a news article.
     */
    public function comment(Request $request, CommunityNews $news): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|min:1|max:1000',
            'author_name' => 'required|string|max:255',
            'author_email' => 'nullable|email|max:255',
            'parent_id' => 'nullable|integer|exists:community_news,id',
            'is_anonymous' => 'nullable|boolean',
        ]);

        try {
            // Mock implementation - in real app, this would create a comment record
            $news->increment('comment_count');

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة تعليق بنجاح',
                'comment_count' => $news->comment_count,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة تعليق: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Share a news article.
     */
    public function share(Request $request, CommunityNews $news): JsonResponse
    {
        try {
            // Mock implementation - in real app, this would create a share record
            $news->increment('share_count');

            return response()->json([
                'success' => true,
                'message' => 'تم مشاركة الخبر بنجاح',
                'share_count' => $news->share_count,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء مشاركة الخبر: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Pin a news article.
     */
    public function pin(Request $request, CommunityNews $news): JsonResponse
    {
        try {
            $news->update(['is_pinned' => !$news->is_pinned]);

            return response()->json([
                'success' => true,
                'message' => $news->is_pinned ? 'تم تثبيت الخبر' : 'تم إلغاء تثبيت الخبر',
                'is_pinned' => $news->is_pinned,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تثبيت الخبر: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Feature a news article.
     */
    public function feature(Request $request, CommunityNews $news): JsonResponse
    {
        try {
            $news->update(['is_featured' => !$news->is_featured]);

            return response()->json([
                'success' => true,
                'message' => $news->is_featured ? 'تم تمييز الخبر' : 'تم إلغاء تمييز الخبر',
                'is_featured' => $news->is_featured,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تمييز الخبر: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get news statistics.
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $filters = $request->only(['community_id', 'news_type', 'status', 'date_range']);
        
        $query = CommunityNews::query();
        
        if ($filters['community_id']) {
            $query->where('community_id', $filters['community_id']);
        }
        
        if ($filters['news_type']) {
            $query->where('news_type', $filters['news_type']);
        }
        
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }
        
        if ($filters['date_range']) {
            if ($filters['date_range'] === 'today') {
                $query->whereDate('published_at', today());
            } elseif ($filters['date_range'] === 'week') {
                $query->whereBetween('published_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($filters['date_range'] === 'month') {
                $query->whereMonth('published_at', now()->month);
            } elseif ($filters['date_range'] === 'year') {
                $query->whereYear('published_at', now()->year());
            }
        }

        $statistics = [
            'total_news' => $query->count(),
            'published_news' => $query->where('status', 'published')->count(),
            'featured_news' => $query->where('is_featured', true)->count(),
            'pinned_news' => $query->where('is_pinned', true)->count(),
            'total_views' => $query->sum('view_count') ?? 0,
            'total_likes' => $query->sum('like_count') ?? 0,
            'total_comments' => $query->sum('comment_count') ?? 0,
            'total_shares' => $query->sum('share_count') ?? 0,
        ];

        return response()->json([
            'success' => true,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get news by community.
     */
    public function getByCommunity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'community_id' => 'required|exists:communities,id',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $limit = $validated['limit'] ?? 20;

        try {
            $news = CommunityNews::where('community_id', $validated['community_id'])
                ->where('status', 'published')
                ->orderBy('is_featured', 'desc')
                ->orderBy('is_pinned', 'desc')
                ->orderBy('published_at', 'desc')
                ->take($limit)
                ->get(['id', 'title', 'summary', 'news_type', 'published_at', 'is_featured', 'is_pinned']);

            return response()->json([
                'success' => true,
                'news' => $news,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الأخبار: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search news.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2|max:255',
            'limit' => 'nullable|integer|min:1|max:50',
            'community_id' => 'nullable|exists:communities,id',
            'news_type' => 'nullable|string',
        ]);

        $limit = $validated['limit'] ?? 20;
        $query = $validated['query'];

        try {
            $news = CommunityNews::where('status', 'published')
                ->when($validated['community_id'], function ($q, $communityId) {
                    return $q->where('community_id', $communityId);
                })
                ->when($validated['news_type'], function ($q, $newsType) {
                    return $q->where('news_type', $newsType);
                })
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('content', 'LIKE', "%{$query}%")
                      ->orWhere('summary', 'LIKE', "%{$query}%")
                      ->orWhere('tags', 'LIKE', "%{$query}%");
                })
                ->orderBy('is_featured', 'desc')
                ->orderBy('published_at', 'desc')
                ->take($limit)
                ->get(['id', 'title', 'summary', 'news_type', 'author_name', 'community_id', 'published_at', 'is_featured', 'is_pinned']);

            return response()->json([
                'success' => true,
                'news' => $news,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء البحث في الأخبار: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get trending news.
     */
    public function getTrending(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'limit' => 'nullable|integer|min:1|max:50',
            'period' => 'nullable|string|in:day,week,month,year',
        ]);

        $limit = $validated['limit'] ?? 20;
        $period = $validated['period'] ?? 'week';

        try {
            $query = CommunityNews::where('status', 'published');

            if ($period === 'day') {
                $query->whereDate('published_at', today());
            } elseif ($period === 'week') {
                $query->whereBetween('published_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($period === 'month') {
                $query->whereMonth('published_at', now()->month());
            } elseif ($period === 'year') {
                $query->whereYear('published_at', now()->year());
            }

            $news = $query->orderBy('view_count', 'desc')
                ->orderBy('like_count', 'desc')
                ->orderBy('comment_count', 'desc')
                ->take($limit)
                ->with(['community', 'community.neighborhood'])
                ->get(['id', 'title', 'summary', 'news_type', 'author_name', 'view_count', 'like_count', 'comment_count', 'community_id', 'published_at']);

            return response()->json([
                'success' => true,
                'news' => $news,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الأخبار المتداولة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export news data.
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,json',
            'filters' => 'nullable|array',
            'include_content' => 'nullable|boolean',
            'include_attachments' => 'nullable|boolean',
        ]);

        try {
            $exportData = $this->prepareNewsExport($validated);
            $filename = $this->generateNewsExportFilename($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز بيانات أخبار المجتمع للتصدير',
                'filename' => $filename,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير بيانات أخبار المجتمع: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get popular news.
     */
    private function getPopularNews(): array
    {
        return CommunityNews::where('status', 'published')
            ->orderBy('view_count', 'desc')
            ->take(5)
            ->with(['community', 'community.neighborhood'])
            ->get(['title', 'news_type', 'author_name', 'view_count', 'community_id', 'published_at'])
            ->toArray();
    }

    /**
     * Get recent news.
     */
    private function getRecentNews(): array
    {
        return CommunityNews::where('status', 'published')
            ->latest('published_at')
            ->take(5)
            ->with(['community', 'community.neighborhood'])
            ->get(['title', 'news_type', 'author_name', 'community_id', 'published_at'])
            ->toArray();
    }

    /**
     * Get monthly news.
     */
    private function getMonthlyNews(): array
    {
        return CommunityNews::select(
                DB::raw('YEAR(published_at) as year'),
                DB::raw('MONTH(published_at) as month'),
                DB::raw('count(*) as count')
            )
            ->where('status', 'published')
            ->where('published_at', '>=', now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Update community activity.
     */
    private function updateCommunityActivity(int $communityId): void
    {
        // Mock implementation - in real app, this would update community activity metrics
        Community::where('id', $communityId)->increment('news_count');
        Community::where('id', $communityId)->update(['activity_level' => 'high']);
    }

    /**
     * Get recent comments.
     */
    private function getRecentComments(CommunityNews $news): array
    {
        // Mock implementation - in real app, this would get actual comments
        $comments = [];
        
        for ($i = 1; $i <= min(3, $news->comment_count); $i++) {
            $comments[] = [
                'id' => $i,
                'content' => 'تعليق هذا تعليق تجريبي',
                'author_name' => 'مستخدم ' . $i,
                'author_email' => 'user' . $i . '@example.com',
                'created_at' => now()->subMinutes(rand(1, 1440))->format('Y-m-d H:i:s'),
                'likes' => rand(0, 10),
                'is_anonymous' => rand(0, 1) === 1,
            ];
        }
        
        return $comments;
    }

    /**
     * Prepare news export data.
     */
    private function prepareNewsExport(array $options): array
    {
        $filters = $options['filters'] ?? [];
        $includeContent = $options['include_content'] ?? false;
        $includeAttachments = $options['include_attachments'] ?? false;

        $query = CommunityNews::with(['community', 'community.neighborhood']);
        
        if (isset($filters['community_id'])) {
            $query->where('community_id', $filters['community_id']);
        }
        
        if (isset($filters['news_type'])) {
            $query->where('news_type', $filters['news_type']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $news = $query->get();

        $data = $news->map(function ($item) use ($includeContent, $includeAttachments) {
            $newsItem = [
                'id' => $item->id,
                'title' => $item->title,
                'community' => $item->community?->name ?? 'غير معروف',
                'neighborhood' => $item->community?->neighborhood?->name ?? 'غير معروف',
                'news_type' => $item->news_type,
                'summary' => $item->summary,
                'author_name' => $item->author_name,
                'author_role' => $item->author_role,
                'tags' => $item->tags ?? [],
                'category' => $item->category,
                'priority' => $item->priority,
                'is_featured' => $item->is_featured,
                'is_pinned' => $item->is_pinned,
                'status' => $item->status,
                'published_at' => $item->published_at->format('Y-m-d H:i:s'),
                'expires_at' => $item->expires_at?->format('Y-m-d H:i:s'),
                'view_count' => $item->view_count,
                'like_count' => $item->like_count,
                'comment_count' => $item->comment_count,
                'share_count' => $item->share_count,
            ];

            if ($includeContent) {
                $newsItem['content'] = $item->content;
            }

            if ($includeAttachments) {
                $newsItem['attachments'] = $item->attachments ?? [];
            }

            return $newsItem;
        });

        return [
            'headers' => ['ID', 'Title', 'Community', 'News Type', 'Author', 'Status', 'Published At'],
            'rows' => $data->toArray(),
        ];
    }

    /**
     * Generate news export filename.
     */
    private function generateNewsExportFilename(array $options): string
    {
        $format = $options['format'];
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "community_news_export_{$timestamp}.{$format}";
    }
}
