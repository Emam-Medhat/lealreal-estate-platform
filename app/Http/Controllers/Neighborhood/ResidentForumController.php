<?php

namespace App\Http\Controllers\Neighborhood;

use App\Http\Controllers\Controller;
use App\Models\Neighborhood\ResidentPost;
use App\Models\Neighborhood\Community;
use App\Models\Neighborhood\Neighborhood;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ResidentForumController extends Controller
{
    /**
     * Display the resident forum dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['community_id', 'post_type', 'status', 'date_range']);
        
        // Get forum statistics
        $stats = [
            'total_posts' => ResidentPost::count(),
            'published_posts' => ResidentPost::where('status', 'published')->count(),
            'total_comments' => 0, // Mock data - in real app, this would count comments
            'active_users' => 0, // Mock data - in real app, this would count unique users
            'featured_posts' => $this->getFeaturedPosts(),
            'popular_topics' => $this->getPopularTopics(),
            'monthly_posts' => $this->getMonthlyPosts(),
        ];

        // Get posts with filters
        $posts = ResidentPost::with(['community', 'community.neighborhood', 'author'])
            ->when($filters['community_id'], function ($query, $communityId) {
                return $query->where('community_id', $communityId);
            })
            ->when($filters['post_type'], function ($query, $postType) {
                return $query->where('post_type', $postType);
            })
            ->when($filters['status'], function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($filters['date_range'], function ($query, $dateRange) {
                if ($dateRange === 'today') {
                    return $query->whereDate('created_at', today());
                } elseif ($dateRange === 'week') {
                    return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($dateRange === 'month') {
                    return $query->whereMonth('created_at', now()->month);
                } elseif ($dateRange === 'year') {
                    return $query->whereYear('created_at', now()->year());
                }
            })
            ->latest()
            ->paginate(12);

        // Get communities and post types for filters
        $communities = Community::where('status', 'active')->with(['neighborhood'])->get(['id', 'name', 'neighborhood_id']);
        $postTypes = ['discussion', 'question', 'announcement', 'complaint', 'suggestion', 'introduction', 'marketplace', 'event', 'other'];
        $statuses = ['draft', 'published', 'hidden', 'reported', 'archived'];
        $dateRanges = ['today', 'week', 'month', 'year'];

        return Inertia::render('ResidentForum/Index', [
            'stats' => $stats,
            'posts' => $posts,
            'communities' => $communities,
            'postTypes' => $postTypes,
            'statuses' => $statuses,
            'dateRanges' => $dateRanges,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resident post.
     */
    public function create(): \Inertia\Response
    {
        $communities = Community::where('status', 'active')->with(['neighborhood'])->get(['id', 'name', 'neighborhood_id']);
        $postTypes = ['discussion', 'question', 'announcement', 'complaint', 'suggestion', 'introduction', 'marketplace', 'event', 'other'];
        $statuses = ['draft', 'published', 'hidden', 'reported', 'archived'];

        return Inertia::render('ResidentForum/Create', [
            'communities' => $communities,
            'postTypes' => $postTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Store a newly created resident post.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'community_id' => 'required|exists:communities,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10|max:5000',
            'post_type' => 'required|string',
            'status' => 'required|string',
            'author_name' => 'required|string|max:255',
            'author_email' => 'nullable|email|max:255',
            'author_phone' => 'nullable|string|max:20',
            'author_type' => 'nullable|string|in:resident,owner,manager,moderator,admin',
            'tags' => 'nullable|array',
            'category' => 'nullable|string|max:100',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'is_pinned' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',
            'moderation_required' => 'nullable|boolean',
            'expiry_date' => 'nullable|date|after:created_at',
            'attachments' => 'nullable|array',
            'attachments.*.0.name' => 'required|string|max:255',
            'attachments.*.type' => 'required|string|max:50',
            'attachments.*.size' => 'required|integer|min:0',
            'attachments.*.url' => 'required|string|max:500',
            'poll_data' => 'nullable|array',
            'poll_data.question' => 'nullable|string|max:255',
            'poll_data.options' => 'nullable|array',
            'poll_data.options.*.text' => 'required|string|max:255',
            'poll_data.options.*.votes' => 'nullable|integer|min:0',
            'poll_data.ends_at' => 'nullable|date',
            'event_data' => 'nullable|array',
            'event_data.start_date' => 'nullable|date',
            'event_data.end_date' => 'nullable|date|after_or_equal:start_date',
            'event_data.location' => 'nullable|string|max:255',
            'resident_id' => 'nullable|exists:users,id', // Assuming resident_id maps to users table
            'contact_info' => 'nullable|array',
            'social_sharing' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $post = ResidentPost::create([
                'community_id' => $validated['community_id'],
                'title' => $validated['title'],
                'content' => $validated['content'],
                'post_type' => $validated['post_type'],
                'status' => $validated['status'],
                'author_name' => $validated['author_name'],
                'author_email' => $validated['author_email'] ?? null,
                'author_phone' => $validated['author_phone'] ?? null,
                'author_type' => $validated['author_type'] ?? 'resident',
                'tags' => $validated['tags'] ?? [],
                'category' => $validated['category'] ?? null,
                'priority' => $validated['priority'] ?? 'medium',
                'is_pinned' => $validated['is_pinned'] ?? false,
                'is_featured' => $validated['is_featured'] ?? false,
                'allow_comments' => $validated['allow_comments'] ?? true,
                'moderation_required' => $validated['moderation_required'] ?? false,
                'expiry_date' => $validated['expiry_date'] ?? null,
                'attachments' => $validated['attachments'] ?? [],
                'poll_data' => $validated['poll_data'] ?? [],
                'event_data' => $validated['event_data'] ?? [],
                'resident_id' => $validated['resident_id'] ?? auth()->id(), // Use authenticated user's ID or null
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
                'message' => 'Post created successfully',
                'post' => $post,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating post: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resident post.
     */
    public function show(ResidentPost $post)
    {
        // Increment view count
        $post->increment('view_count');

        $post->load(['community', 'community.neighborhood', 'author']);

        // Get related posts
        $relatedPosts = ResidentPost::where('community_id', $post->community_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->take(3)
            ->get(['id', 'title', 'post_type', 'created_at']);

        // Get recent comments (mock data for now)
        $recentComments = $this->getRecentComments($post);

        return Inertia::render('ResidentForum/Show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'recentComments' => $recentComments,
        ]);
    }

    /**
     * Show the form for editing the specified resident post.
     */
    public function edit(ResidentPost $post): \Inertia\Response
    {
        $communities = Community::where('status', 'active')->with(['neighborhood'])->get(['id', 'name', 'neighborhood_id']);
        $postTypes = ['discussion', 'question', 'announcement', 'complaint', 'suggestion', 'introduction', 'marketplace', 'event', 'other'];
        $statuses = ['draft', 'published', 'hidden', 'reported', 'archived'];

        return Inertia::render('ResidentForum/Edit', [
            'post' => $post,
            'communities' => $communities,
            'postTypes' => $postTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified resident post.
     */
    public function update(Request $request, ResidentPost $post): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10|max:5000',
            'post_type' => 'required|string',
            'status' => 'required|string',
            'author_name' => 'required|string|max:255',
            'author_email' => 'nullable|email|max:255',
            'author_phone' => 'nullable|string|max:20',
            'author_type' => 'nullable|string|in:resident,owner,manager,moderator,admin',
            'tags' => 'nullable|array',
            'category' => 'nullable|string|max:100',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'is_pinned' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',
            'moderation_required' => 'nullable|boolean',
            'expiry_date' => 'nullable|date|after:created_at',
            'attachments' => 'nullable|array',
            'poll_data' => 'nullable|array',
            'event_data' => 'nullable|array',
            'contact_info' => 'nullable|array',
            'social_sharing' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $post->update($validated);

            // Update community activity
            $this->updateCommunityActivity($post->community_id);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المنشور بنجاح',
                'post' => $post,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث المنشور: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resident post.
     */
    public function destroy(ResidentPost $post): JsonResponse
    {
        try {
            $communityId = $post->community_id;
            $post->delete();

            // Update community activity
            $this->updateCommunityActivity($communityId);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المنشور بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المنشور: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Like a post.
     */
    public function like(Request $request, ResidentPost $post): JsonResponse
    {
        try {
            // Mock implementation - in real app, this would create a like record
            $post->increment('like_count');

            return response()->json([
                'success' => 'true',
                'message' => 'تم إعجاب المنشور بنجاح',
                'like_count' => $post->like_count,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إعجاب المنشور: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Comment on a post.
     */
    public function comment(Request $request, ResidentPost $post): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|min:1|max:1000',
            'author_name' => 'required|string|max:255',
            'author_email' => 'nullable|email|max:255',
            'parent_id' => 'nullable|integer|exists:resident_posts,id',
            'is_anonymous' => 'nullable|boolean',
        ]);

        try {
            // Mock implementation - in real app, this would create a comment record
            $post->increment('comment_count');

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة تعليق بنجاح',
                'comment_count' => $post->comment_count,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأثناء إضافة تعليق: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Share a post.
     */
    public function share(Request $request, ResidentPost $post): JsonResponse
    {
        try {
            // Mock implementation - in real app, this would create a share record
            $post->increment('share_count');

            return response()->json([
                'success' => true,
                'message'=> 'تم مشاركة المنشور بنجاح',
                'share_count' => $post->share_count,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأثناء مشاركة المنشور: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Pin a post.
     */
    public function pin(Request $request, ResidentPost $post): JsonResponse
    {
        try {
            $post->update(['is_pinned' => !$post->is_pinned]);

            return response()->json([
                'success' => 'true',
                'message' => $post->is_pinned ? 'تم تثبيت المنشور' : 'تم إلغاء تثبيت المنشور',
                'is_pinned' => $post->is_pinned,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تثبيت المنشور: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Feature a post.
     */
    public function feature(Request $request, ResidentPost $post): JsonResponse
    {
        try {
            $post->update(['is_featured' => !$post->is_featured]);

            return response()->json([
                'success' => 'true',
                'message' => $post->is_featured ? 'تم تمييز المنشور' : 'تم إلغاء تمييز المنشور',
                'is_featured' => $post->is_featured,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأثناء تمييز المنشور: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get forum statistics.
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $filters = $request->only(['community_id', 'post_type', 'status', 'date_range']);
        
        $query = ResidentPost::query();
        
        if ($filters['community_id']) {
            $query->where('community_id', $filters['community_id']);
        }
        
        if ($filters['post_type']) {
            $query->where('post_type', $filters['post_type']);
        }
        
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }
        
        if ($filters['date_range']) {
            if ($filters['date_range'] === 'today') {
                $query->whereDate('created_at', today());
            } elseif ($filters['date_range'] === 'week') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($filters['date_range'] === 'month') {
                $query->whereMonth('created_at', now()->month);
            } elseif ($filters['date_range'] === 'year') {
                $query->whereYear('created_at', now()->year());
            }
        }

        $statistics = [
            'total_posts' => $query->count(),
            'published_posts' => $query->where('status', 'published')->count(),
            'pinned_posts' => $query->where('is_pinned', true)->count(),
            'featured_posts' => $query->where('is_featured', true)->count(),
            'total_likes' => $query->sum('like_count') ?? 0,
            'total_comments' => $query->sum('comment_count') ?? 0,
            'total_shares' => $query->sum('share_count') ?? 0,
            'active_users' => 0, // Mock data
        ];

        return response()->json([
            'success' => true,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get posts by community.
     */
    public function getByCommunity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'community_id' => 'required|exists:communities,id',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $limit = $validated['limit'] ?? 20;

        try {
            $posts = ResidentPost::where('community_id', $validated['community_id'])
                ->where('status', 'published')
                ->orderBy('is_pinned', 'desc')
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get(['id', 'title', 'content', 'post_type', 'author_name', 'created_at', 'is_pinned', 'is_featured']);

            return response()->json([
                'success' => true,
                'posts' => $posts,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب المنشورات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search posts.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2|max:255',
            'limit' => 'nullable|integer|min:1|max:50',
            'community_id' => 'nullable|exists:communities,id',
            'post_type' => 'nullable|string',
        ]);

        $limit = $validated['limit'] ?? 20;
        $query = $validated['query'];

        try {
            $posts = ResidentPost::where('status', 'published')
                ->when($validated['community_id'], function ($q, $communityId) {
                    return $q->where('community_id', $communityId);
                })
                ->when($validated['post_type'], function ($q, $postType) {
                    return $q->where('post_type', $postType);
                })
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('content', 'LIKE', "%{$query}%")
                      ->orWhere('tags', 'LIKE', "%{$query}%");
                })
                ->orderBy('is_pinned', 'desc')
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get(['id', 'title', 'content', 'post_type', 'author_name', 'community_id', 'created_at', 'is_pinned', 'is_featured']);

            return response()->json([
                'success' => true,
                'posts' => $posts,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء البحث في المنشورات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get trending posts.
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
            $query = ResidentPost::where('status', 'published');

            if ($period === 'day') {
                $query->whereDate('created_at', today());
            } elseif ($period === 'week') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($period === 'month') {
                $query->whereMonth('created_at', now()->month());
            } elseif ($period === 'year') {
                $query->whereYear('created_at', now()->year());
            }

            $posts = $query->orderBy('view_count', 'desc')
                ->orderBy('like_count', 'desc')
                ->orderBy('comment_count', 'desc')
                ->take($limit)
                ->with(['community', 'community.neighborhood'])
                ->get(['id', 'title', 'content', 'post_type', 'author_name', 'view_count', 'like_count', 'comment_count', 'community_id']);

            return response()->json([
                'success' => true,
                'posts' => $posts,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب المنشورات المتداولة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export post data.
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,json',
            'filters' => 'nullable|array',
            'include_comments' => 'nullable|boolean',
            'include_attachments' => 'nullable|boolean',
        ]);

        try {
            $exportData = $this->preparePostExport($validated);
            $filename = $this->generatePostExportFilename($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز بيانات المنتدى للتصدير',
                'filename' => $filename,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير بيانات المنتدى: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get featured posts.
     */
    private function getFeaturedPosts(): array
    {
        return ResidentPost::where('status', 'published')
            ->where('is_featured', true)
            ->orderBy('view_count', 'desc')
            ->take(5)
            ->with(['community', 'community.neighborhood'])
            ->get(['title', 'post_type', 'author_name', 'view_count', 'community_id', 'created_at'])
            ->toArray();
    }

    /**
     * Get popular topics.
     */
    private function getPopularTopics(): array
    {
        return ResidentPost::select('tags', DB::raw('count(*) as count'))
            ->where('status', 'published')
            ->whereNotNull('tags')
            ->get()
            ->flatMap(function ($item) {
                return $item['tags'] ?? [];
            })
            ->count()
            ->sort()
            ->take(10)
            ->toArray();
    }

    /**
     * Get monthly posts.
     */
    private function getMonthlyPosts(): array
    {
        return ResidentPost::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as count')
            )
            ->where('status', 'published')
            ->where('created_at', '>=', now()->subYear())
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
        Community::where('id', $communityId)->increment('post_count');
        Community::where('id', $communityId)->update(['activity_level' => 'high']);
    }

    /**
     * Get recent comments.
     */
    private function getRecentComments(ResidentPost $post): array
    {
        // Mock implementation - in real app, this would get actual comments
        $comments = [];
        
        for ($i = 1; $i <= min(3, $post->comment_count); $i++) {
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
     * Prepare post export data.
     */
    private function preparePostExport(array $options): array
    {
        $filters = $options['filters'] ?? [];
        $includeComments = $options['include_comments'] ?? false;
        $includeAttachments = $options['include_attachments'] ?? false;

        $query = ResidentPost::with(['community', 'community.neighborhood']);
        
        if (isset($filters['community_id'])) {
            $query->where('community_id', $filters['community_id']);
        }
        
        if (isset($filters['post_type'])) {
            $query->where('post_type', $filters['post_type']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $posts = $query->get();

        $data = $posts->map(function ($post) use ($includeComments, $includeAttachments) {
            $item = [
                'id' => $post->id,
                'title' => $post->title,
                'community' => $post->community?->name ?? 'غير معروف',
                'neighborhood' => $post->community?->neighborhood?->name ?? 'غير معروف',
                'post_type' => $post->post_type,
                'content' => $post->content,
                'author_name' => $post->author_name,
                'author_type' => $post->author_type,
                'tags' => $post->tags ?? [],
                'category' => $post->category,
                'priority' => $post->priority,
                'is_pinned' => $post->is_pinned,
                'is_featured' => $post->is_featured,
                'status' => $post->status,
                'view_count' => $post->view_count,
                'like_count' => $post->like_count,
                'comment_count' => $post->comment_count,
                'share_count' => $post->share_count,
                'created_at' => $post->created_at->format('Y-m-d H:i:s'),
            ];

            if ($includeComments) {
                $item['comments'] = $this->getRecentComments($post);
            }

            if ($includeAttachments) {
                $item['attachments'] = $post->attachments ?? [];
            }

            return $item;
        });

        return [
            'headers' => ['ID', 'Title', 'Community', 'Post Type', 'Author', 'Status', 'Created At'],
            'rows' => $data->toArray(),
        ];
    }

    /**
     * Generate post export filename.
     */
    private function generatePostExportFilename(array $options): string
    {
        $format = $options['format'];
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "resident_posts_export_{$timestamp}.{$format}";
    }
}
