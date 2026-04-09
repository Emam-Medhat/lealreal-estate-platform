<?php

namespace App\Http\Controllers\Neighborhood;

use App\Http\Controllers\Controller;
use App\Models\Neighborhood\Community;
use App\Models\Neighborhood\Neighborhood;
use App\Models\Neighborhood\CommunityEvent;
use App\Models\Neighborhood\ResidentPost;
use App\Models\Neighborhood\CommunityNews;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CommunityController extends Controller
{
    /**
     * Display the communities dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['status', 'activity_level', 'member_count_range']);
        
        // Get community statistics
        $stats = [
            'total_communities' => Community::count(),
            'active_communities' => Community::where('status', 'active')->count(),
            'total_members' => Community::sum('member_count'),
            'total_events' => CommunityEvent::count(),
            'total_posts' => ResidentPost::count(),
            'high_activity_communities' => Community::where('activity_level', 'high')->count(),
            'popular_communities' => $this->getPopularCommunities(),
            'recent_activities' => $this->getRecentActivities(),
        ];

        // Get recent communities
        $recentCommunities = Community::with(['neighborhood'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($community) {
                return [
                    'id' => $community->id,
                    'name' => $community->name,
                    'neighborhood' => $community->neighborhood?->name ?? 'غير معروف',
                    'description' => $community->description,
                    'member_count' => $community->member_count,
                    'activity_level' => $community->activity_level,
                    'status' => $community->status,
                    'created_at' => $community->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Get communities with filters
        $communities = Community::with(['neighborhood'])
            ->when($filters['status'], function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($filters['activity_level'], function ($query, $activityLevel) {
                return $query->where('activity_level', $activityLevel);
            })
            ->when($filters['member_count_range'], function ($query, $range) {
                if ($range === 'low') {
                    return $query->where('member_count', '<', 100);
                } elseif ($range === 'medium') {
                    return $query->whereBetween('member_count', [100, 500]);
                } elseif ($range === 'high') {
                    return $query->where('member_count', '>', 500);
                }
            })
            ->latest()
            ->paginate(12);

        // Get activity levels and statuses for filters
        $activityLevels = ['low', 'medium', 'high'];
        $statuses = ['active', 'inactive', 'suspended'];

        return Inertia::render('Community/Index', [
            'stats' => $stats,
            'recentCommunities' => $recentCommunities,
            'communities' => $communities,
            'activityLevels' => $activityLevels,
            'statuses' => $statuses,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new community.
     */
    public function create(): \Inertia\Response
    {
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $activityLevels = ['low', 'medium', 'high'];
        $statuses = ['active', 'inactive', 'suspended'];

        return Inertia::render('Community/Create', [
            'neighborhoods' => $neighborhoods,
            'activityLevels' => $activityLevels,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Store a newly created community.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'neighborhood_id' => 'required|exists:neighborhoods,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'activity_level' => 'required|string',
            'status' => 'required|string',
            'rules' => 'nullable|array',
            'guidelines' => 'nullable|array',
            'moderation_settings' => 'nullable|array',
            'privacy_settings' => 'nullable|array',
            'notification_settings' => 'nullable|array',
            'features' => 'nullable|array',
            'amenities' => 'nullable|array',
            'social_links' => 'nullable|array',
            'contact_info' => 'nullable|array',
            'admin_info' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $community = Community::create([
                'neighborhood_id' => $validated['neighborhood_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'activity_level' => $validated['activity_level'],
                'status' => $validated['status'],
                'rules' => $validated['rules'] ?? [],
                'guidelines' => $validated['guidelines'] ?? [],
                'moderation_settings' => $validated['moderation_settings'] ?? [],
                'privacy_settings' => $validated['privacy_settings'] ?? [],
                'notification_settings' => $validated['notification_settings'] ?? [],
                'features' => $validated['features'] ?? [],
                'amenities' => $validated['amenities'] ?? [],
                'social_links' => $validated['social_links'] ?? [],
                'contact_info' => $validated['contact_info'] ?? [],
                'admin_info' => $validated['admin_info'] ?? [],
                'metadata' => $validated['metadata'] ?? [],
                'member_count' => 0,
                'post_count' => 0,
                'event_count' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء المجتمع بنجاح',
                'community' => $community,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء المجتمع: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified community.
     */
    public function show(Community $community): \Inertia\Response
    {
        $community->load(['neighborhood', 'events', 'posts', 'news']);

        // Get community statistics
        $statistics = $this->getCommunityStatistics($community);

        // Get recent activities
        $recentActivities = $this->getCommunityRecentActivities($community);

        // Get community members (mock data for now)
        $members = $this->getCommunityMembers($community);

        return Inertia::render('Community/Show', [
            'community' => $community,
            'statistics' => $statistics,
            'recentActivities' => $recentActivities,
            'members' => $members,
        ]);
    }

    /**
     * Show the form for editing the specified community.
     */
    public function edit(Community $community): \Inertia\Response
    {
        $neighborhoods = Neighborhood::where('status', 'active')->get(['id', 'name', 'city', 'district']);
        $activityLevels = ['low', 'medium', 'high'];
        $statuses = ['active', 'inactive', 'suspended'];

        return Inertia::render('Community/Edit', [
            'community' => $community,
            'neighborhoods' => $neighborhoods,
            'activityLevels' => $activityLevels,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified community.
     */
    public function update(Request $request, Community $community): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'activity_level' => 'required|string',
            'status' => 'required|string',
            'rules' => 'nullable|array',
            'guidelines' => 'nullable|array',
            'moderation_settings' => 'nullable|array',
            'privacy_settings' => 'nullable|array',
            'notification_settings' => 'nullable|array',
            'features' => 'nullable|array',
            'amenities' => 'nullable|array',
            'social_links' => 'nullable|array',
            'contact_info' => 'nullable|array',
            'admin_info' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $community->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المجتمع بنجاح',
                'community' => $community,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث المجتمع: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified community.
     */
    public function destroy(Community $community): JsonResponse
    {
        try {
            $community->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المجتمع بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المجتمع: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Join a community.
     */
    public function join(Request $request, Community $community): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'member_type' => 'required|string|in:resident,owner,manager,moderator',
        ]);

        try {
            // Mock implementation - in real app, this would create a community membership record
            $community->increment('member_count');

            return response()->json([
                'success' => true,
                'message' => 'تم الانضمام للمجتمع بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الانضمام للمجتمع: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Leave a community.
     */
    public function leave(Request $request, Community $community): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            // Mock implementation - in real app, this would delete the community membership record
            $community->decrement('member_count');

            return response()->json([
                'success' => true,
                'message' => 'تم مغادرة المجتمع بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء مغادرة المجتمع: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get community statistics.
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'activity_level']);
        
        $statistics = [
            'total_communities' => Community::when($filters['status'], function ($query, $status) {
                return $query->where('status', $status);
            })->when($filters['activity_level'], function ($query, $activityLevel) {
                return $query->where('activity_level', $activityLevel);
            })->count(),
            
            'active_communities' => Community::when($filters['activity_level'], function ($query, $activityLevel) {
                return $query->where('activity_level', $activityLevel);
            })->where('status', 'active')->count(),
            
            'total_members' => Community::when($filters['status'], function ($query, $status) {
                return $query->where('status', $status);
            })->when($filters['activity_level'], function ($query, $activityLevel) {
                return $query->where('activity_level', $activityLevel);
            })->sum('member_count'),
            
            'total_events' => Community::when($filters['status'], function ($query, $status) {
                return $query->where('status', $status);
            })->when($filters['activity_level'], function ($query, $activityLevel) {
                return $query->where('activity_level', $activityLevel);
            })->sum('event_count'),
            
            'total_posts' => Community::when($filters['status'], function ($query, $status) {
                return $query->where('status', $status);
            })->when($filters['activity_level'], function ($query, $activityLevel) {
                return $query->where('activity_level', $activityLevel);
            })->sum('post_count'),
        ];

        return response()->json([
            'success' => true,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get community activity feed.
     */
    public function getActivityFeed(Request $request, Community $community): JsonResponse
    {
        $validated = $request->validate([
            'limit' => 'nullable|integer|min:1|max:50',
            'type' => 'nullable|string|in:all,posts,events,news',
        ]);

        $limit = $validated['limit'] ?? 20;
        $type = $validated['type'] ?? 'all';

        $activities = [];

        if ($type === 'all' || $type === 'posts') {
            $posts = $community->posts()->latest()->take($limit)->get();
            foreach ($posts as $post) {
                $activities[] = [
                    'type' => 'post',
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'author' => $post->author_name,
                    'created_at' => $post->created_at->format('Y-m-d H:i:s'),
                    'likes' => $post->likes_count ?? 0,
                    'comments' => $post->comments_count ?? 0,
                ];
            }
        }

        if ($type === 'all' || $type === 'events') {
            $events = $community->events()->latest()->take($limit)->get();
            foreach ($events as $event) {
                $activities[] = [
                    'type' => 'event',
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'start_date' => $event->start_date,
                    'location' => $event->location,
                    'created_at' => $event->created_at->format('Y-m-d H:i:s'),
                ];
            }
        }

        if ($type === 'all' || $type === 'news') {
            $news = $community->news()->latest()->take($limit)->get();
            foreach ($news as $item) {
                $activities[] = [
                    'type' => 'news',
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content,
                    'author' => $item->author_name,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            }
        }

        // Sort by date
        usort($activities, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return response()->json([
            'success' => true,
            'activities' => array_slice($activities, 0, $limit),
        ]);
    }

    /**
     * Get community members.
     */
    public function getMembers(Request $request, Community $community): JsonResponse
    {
        $validated = $request->validate([
            'limit' => 'nullable|integer|min:1|max:100',
            'member_type' => 'nullable|string|in:all,resident,owner,manager,moderator',
        ]);

        $limit = $validated['limit'] ?? 50;
        $memberType = $validated['member_type'] ?? 'all';

        // Mock implementation - in real app, this would query community members
        $members = $this->getCommunityMembers($community, $limit, $memberType);

        return response()->json([
            'success' => true,
            'members' => $members,
        ]);
    }

    /**
     * Export community data.
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,json',
            'filters' => 'nullable|array',
            'include_members' => 'nullable|boolean',
            'include_activities' => 'nullable|boolean',
            'include_events' => 'nullable|boolean',
        ]);

        try {
            $exportData = $this->prepareCommunityExport($validated);
            $filename = $this->generateCommunityExportFilename($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز بيانات المجتمعات للتصدير',
                'filename' => $filename,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير بيانات المجتمعات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get popular communities.
     */
    private function getPopularCommunities(): array
    {
        return Community::where('status', 'active')
            ->where('member_count', '>', 50)
            ->orderBy('member_count', 'desc')
            ->take(5)
            ->with(['neighborhood'])
            ->get(['name', 'neighborhood_id', 'member_count', 'activity_level'])
            ->toArray();
    }

    /**
     * Get recent activities.
     */
    private function getRecentActivities(): array
    {
        $activities = [];
        
        // Recent community creations
        $recentCommunities = Community::latest()->take(3)->get();
        foreach ($recentCommunities as $community) {
            $activities[] = [
                'type' => 'community_created',
                'title' => 'مجتمع جديد',
                'description' => $community->name,
                'created_at' => $community->created_at->format('Y-m-d H:i:s'),
            ];
        }
        
        // Recent events
        $recentEvents = CommunityEvent::latest()->take(3)->get();
        foreach ($recentEvents as $event) {
            $activities[] = [
                'type' => 'event_created',
                'title' => 'فعالية جديدة',
                'description' => $event->title,
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
     * Get community statistics.
     */
    private function getCommunityStatistics(Community $community): array
    {
        return [
            'total_members' => $community->member_count,
            'total_posts' => $community->post_count,
            'total_events' => $community->event_count,
            'active_members' => intval($community->member_count * 0.7), // Mock calculation
            'weekly_posts' => $community->posts()->where('created_at', '>=', now()->subWeek())->count(),
            'upcoming_events' => $community->events()->where('start_date', '>=', now())->count(),
            'engagement_rate' => 85.5, // Mock calculation
        ];
    }

    /**
     * Get community recent activities.
     */
    private function getCommunityRecentActivities(Community $community): array
    {
        $activities = [];
        
        // Recent posts
        $recentPosts = $community->posts()->latest()->take(3)->get();
        foreach ($recentPosts as $post) {
            $activities[] = [
                'type' => 'post',
                'title' => $post->title,
                'description' => substr($post->content, 0, 100) . '...',
                'author' => $post->author_name,
                'created_at' => $post->created_at->format('Y-m-d H:i:s'),
            ];
        }
        
        // Recent events
        $recentEvents = $community->events()->latest()->take(3)->get();
        foreach ($recentEvents as $event) {
            $activities[] = [
                'type' => 'event',
                'title' => $event->title,
                'description' => substr($event->description, 0, 100) . '...',
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
     * Get community members.
     */
    private function getCommunityMembers(Community $community, int $limit = 50, string $memberType = 'all'): array
    {
        // Mock implementation - in real app, this would query community members
        $members = [];
        
        for ($i = 1; $i <= min($limit, 20); $i++) {
            $members[] = [
                'id' => $i,
                'name' => 'عضو ' . $i,
                'avatar' => 'https://picsum.photos/seed/member' . $i . '/50/50.jpg',
                'member_type' => $memberType === 'all' ? ['resident', 'owner', 'manager', 'moderator'][array_rand(['resident', 'owner', 'manager', 'moderator'])] : $memberType,
                'joined_at' => now()->subDays(rand(1, 365))->format('Y-m-d H:i:s'),
                'last_active' => now()->subHours(rand(1, 72))->format('Y-m-d H:i:s'),
                'posts_count' => rand(0, 50),
                'is_online' => rand(0, 1) === 1,
            ];
        }
        
        return $members;
    }

    /**
     * Prepare community export data.
     */
    private function prepareCommunityExport(array $options): array
    {
        $filters = $options['filters'] ?? [];
        $includeMembers = $options['include_members'] ?? false;
        $includeActivities = $options['include_activities'] ?? false;
        $includeEvents = $options['include_events'] ?? false;

        $query = Community::with(['neighborhood']);
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['activity_level'])) {
            $query->where('activity_level', $filters['activity_level']);
        }

        $communities = $query->get();

        $data = $communities->map(function ($community) use ($includeMembers, $includeActivities, $includeEvents) {
            $item = [
                'id' => $community->id,
                'name' => $community->name,
                'neighborhood' => $community->neighborhood?->name ?? 'غير معروف',
                'description' => $community->description,
                'member_count' => $community->member_count,
                'post_count' => $community->post_count,
                'event_count' => $community->event_count,
                'activity_level' => $community->activity_level,
                'status' => $community->status,
                'created_at' => $community->created_at->format('Y-m-d H:i:s'),
            ];

            if ($includeMembers) {
                $item['members'] = $this->getCommunityMembers($community, 10);
            }

            if ($includeActivities) {
                $item['recent_posts'] = $community->posts()->latest()->take(5)->get(['title', 'content', 'created_at']);
            }

            if ($includeEvents) {
                $item['upcoming_events'] = $community->events()->where('start_date', '>=', now())->take(5)->get(['title', 'start_date', 'location']);
            }

            return $item;
        });

        return [
            'headers' => ['ID', 'Name', 'Neighborhood', 'Member Count', 'Post Count', 'Event Count', 'Activity Level', 'Status', 'Created At'],
            'rows' => $data->toArray(),
        ];
    }

    /**
     * Generate community export filename.
     */
    private function generateCommunityExportFilename(array $options): string
    {
        $format = $options['format'];
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "communities_export_{$timestamp}.{$format}";
    }
}
