<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\SocialMediaPost;
use App\Models\Property\Property;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SocialMediaMarketingController extends Controller
{
    /**
     * Display a listing of social media posts.
     */
    public function index()
    {
        $posts = SocialMediaPost::with(['property'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Marketing/SocialMedia/Index', [
            'posts' => $posts,
            'stats' => [
                'total_posts' => SocialMediaPost::count(),
                'scheduled_posts' => SocialMediaPost::where('status', 'scheduled')->count(),
                'published_posts' => SocialMediaPost::where('status', 'published')->count(),
                'draft_posts' => SocialMediaPost::where('status', 'draft')->count(),
                'total_engagement' => SocialMediaPost::sum('total_engagement'),
                'total_reach' => SocialMediaPost::sum('reach'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new social media post.
     */
    public function create()
    {
        $properties = Property::where('status', 'active')->get();
        
        return Inertia::render('Marketing/SocialMedia/Create', [
            'properties' => $properties,
            'platforms' => ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'tiktok'],
            'post_types' => ['image', 'video', 'carousel', 'story', 'reel', 'text'],
        ]);
    }

    /**
     * Store a newly created social media post.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'platform' => 'required|string|in:facebook,twitter,instagram,linkedin,youtube,tiktok',
            'post_type' => 'required|string|in:image,video,carousel,story,reel,text',
            'scheduled_at' => 'nullable|date|after:now',
            'hashtags' => 'nullable|string',
            'mentions' => 'nullable|string',
            'media_files' => 'nullable|array',
            'media_files.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:10240',
            'call_to_action' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'boost_post' => 'boolean',
        ]);

        $post = SocialMediaPost::create([
            'property_id' => $validated['property_id'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'platform' => $validated['platform'],
            'post_type' => $validated['post_type'],
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'hashtags' => $validated['hashtags'] ?? null,
            'mentions' => $validated['mentions'] ?? null,
            'call_to_action' => $validated['call_to_action'] ?? null,
            'target_audience' => $validated['target_audience'] ?? null,
            'budget' => $validated['budget'] ?? 0,
            'boost_post' => $validated['boost_post'] ?? false,
            'status' => $validated['scheduled_at'] ? 'scheduled' : 'draft',
        ]);

        // Handle media file uploads
        if ($request->hasFile('media_files')) {
            $mediaPaths = [];
            foreach ($request->file('media_files') as $file) {
                $path = $file->store('social-media-media', 'public');
                $mediaPaths[] = $path;
            }
            $post->update(['media_files' => json_encode($mediaPaths)]);
        }

        return redirect()->route('marketing.social-media.index')
            ->with('success', 'تم إنشاء المنشور بنجاح');
    }

    /**
     * Display the specified social media post.
     */
    public function show(SocialMediaPost $socialMediaPost)
    {
        $socialMediaPost->load(['property', 'analytics']);

        return Inertia::render('Marketing/SocialMedia/Show', [
            'post' => $socialMediaPost,
            'analytics' => $this->getPostAnalytics($socialMediaPost),
        ]);
    }

    /**
     * Show the form for editing the specified social media post.
     */
    public function edit(SocialMediaPost $socialMediaPost)
    {
        $properties = Property::where('status', 'active')->get();
        
        return Inertia::render('Marketing/SocialMedia/Edit', [
            'post' => $socialMediaPost,
            'properties' => $properties,
            'platforms' => ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'tiktok'],
            'post_types' => ['image', 'video', 'carousel', 'story', 'reel', 'text'],
        ]);
    }

    /**
     * Update the specified social media post.
     */
    public function update(Request $request, SocialMediaPost $socialMediaPost)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'platform' => 'required|string|in:facebook,twitter,instagram,linkedin,youtube,tiktok',
            'post_type' => 'required|string|in:image,video,carousel,story,reel,text',
            'scheduled_at' => 'nullable|date|after:now',
            'hashtags' => 'nullable|string',
            'mentions' => 'nullable|string',
            'media_files' => 'nullable|array',
            'media_files.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:10240',
            'call_to_action' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'boost_post' => 'boolean',
        ]);

        $socialMediaPost->update([
            'property_id' => $validated['property_id'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'platform' => $validated['platform'],
            'post_type' => $validated['post_type'],
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'hashtags' => $validated['hashtags'] ?? null,
            'mentions' => $validated['mentions'] ?? null,
            'call_to_action' => $validated['call_to_action'] ?? null,
            'target_audience' => $validated['target_audience'] ?? null,
            'budget' => $validated['budget'] ?? 0,
            'boost_post' => $validated['boost_post'] ?? false,
            'status' => $validated['scheduled_at'] ? 'scheduled' : $socialMediaPost->status,
        ]);

        // Handle media file uploads
        if ($request->hasFile('media_files')) {
            // Delete old media files
            if ($socialMediaPost->media_files) {
                $oldMediaFiles = json_decode($socialMediaPost->media_files, true);
                foreach ($oldMediaFiles as $oldFile) {
                    Storage::disk('public')->delete($oldFile);
                }
            }

            $mediaPaths = [];
            foreach ($request->file('media_files') as $file) {
                $path = $file->store('social-media-media', 'public');
                $mediaPaths[] = $path;
            }
            $socialMediaPost->update(['media_files' => json_encode($mediaPaths)]);
        }

        return redirect()->route('marketing.social-media.index')
            ->with('success', 'تم تحديث المنشور بنجاح');
    }

    /**
     * Remove the specified social media post.
     */
    public function destroy(SocialMediaPost $socialMediaPost)
    {
        // Delete media files
        if ($socialMediaPost->media_files) {
            $mediaFiles = json_decode($socialMediaPost->media_files, true);
            foreach ($mediaFiles as $file) {
                Storage::disk('public')->delete($file);
            }
        }

        $socialMediaPost->delete();

        return redirect()->route('marketing.social-media.index')
            ->with('success', 'تم حذف المنشور بنجاح');
    }

    /**
     * Publish a social media post immediately.
     */
    public function publish(SocialMediaPost $socialMediaPost)
    {
        if ($socialMediaPost->status !== 'draft' && $socialMediaPost->status !== 'scheduled') {
            return back()->with('error', 'لا يمكن نشر هذا المنشور');
        }

        // Mock API call to publish on social media platform
        $this->publishToPlatform($socialMediaPost);

        $socialMediaPost->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return back()->with('success', 'تم نشر المنشور بنجاح');
    }

    /**
     * Schedule a social media post.
     */
    public function schedule(Request $request, SocialMediaPost $socialMediaPost)
    {
        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $socialMediaPost->update([
            'status' => 'scheduled',
            'scheduled_at' => $validated['scheduled_at'],
        ]);

        return back()->with('success', 'تم جدولة المنشور بنجاح');
    }

    /**
     * Get analytics for a social media post.
     */
    public function analytics(SocialMediaPost $socialMediaPost)
    {
        $analytics = $this->getPostAnalytics($socialMediaPost);

        return Inertia::render('Marketing/SocialMedia/Analytics', [
            'post' => $socialMediaPost,
            'analytics' => $analytics,
        ]);
    }

    /**
     * Duplicate a social media post.
     */
    public function duplicate(SocialMediaPost $socialMediaPost)
    {
        $newPost = $socialMediaPost->replicate();
        $newPost->title = $socialMediaPost->title . ' (نسخة)';
        $newPost->status = 'draft';
        $newPost->published_at = null;
        $newPost->scheduled_at = null;
        $newPost->save();

        return redirect()->route('marketing.social-media.edit', $newPost)
            ->with('success', 'تم نسخ المنشور بنجاح');
    }

    /**
     * Export social media posts data.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $posts = SocialMediaPost::with(['property'])->get();

        if ($format === 'csv') {
            $filename = 'social-media-posts-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($posts) {
                $file = fopen('php://output', 'w');
                
                // CSV Header
                fputcsv($file, [
                    'ID', 'العنوان', 'المنصة', 'نوع المنشور', 'الحالة', 
                    'العقار', 'التفاعل', 'الوصول', 'تاريخ النشر'
                ]);

                // CSV Data
                foreach ($posts as $post) {
                    fputcsv($file, [
                        $post->id,
                        $post->title,
                        $post->platform,
                        $post->post_type,
                        $post->status,
                        $post->property?->title ?? 'N/A',
                        $post->total_engagement,
                        $post->reach,
                        $post->published_at?->format('Y-m-d H:i:s') ?? 'N/A'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return back()->with('error', 'تنسيق التصدير غير مدعوم');
    }

    /**
     * Get platform-specific insights.
     */
    public function platformInsights()
    {
        $insights = [
            'facebook' => $this->getPlatformInsights('facebook'),
            'twitter' => $this->getPlatformInsights('twitter'),
            'instagram' => $this->getPlatformInsights('instagram'),
            'linkedin' => $this->getPlatformInsights('linkedin'),
            'youtube' => $this->getPlatformInsights('youtube'),
            'tiktok' => $this->getPlatformInsights('tiktok'),
        ];

        return Inertia::render('Marketing/SocialMedia/Insights', [
            'insights' => $insights,
        ]);
    }

    /**
     * Get post analytics data.
     */
    private function getPostAnalytics(SocialMediaPost $post)
    {
        // Mock analytics data
        return [
            'engagement_rate' => rand(1, 15) . '%',
            'reach' => rand(1000, 50000),
            'impressions' => rand(2000, 100000),
            'likes' => rand(50, 5000),
            'comments' => rand(10, 500),
            'shares' => rand(5, 200),
            'clicks' => rand(20, 1000),
            'saves' => rand(10, 300),
            'video_views' => $post->post_type === 'video' ? rand(100, 10000) : 0,
            'video_completion_rate' => $post->post_type === 'video' ? rand(20, 80) . '%' : 0,
            'best_posting_time' => '6:00 PM - 9:00 PM',
            'top_hashtags' => ['#عقارات', '#بيع_وتأجير', '#عقار_مميز'],
            'audience_demographics' => [
                'age_groups' => [
                    '18-24' => 15,
                    '25-34' => 35,
                    '35-44' => 30,
                    '45-54' => 15,
                    '55+' => 5,
                ],
                'genders' => [
                    'male' => 55,
                    'female' => 45,
                ],
                'locations' => [
                    'الرياض' => 40,
                    'جدة' => 25,
                    'الدمام' => 20,
                    'أخرى' => 15,
                ],
            ],
        ];
    }

    /**
     * Get platform-specific insights.
     */
    private function getPlatformInsights($platform)
    {
        $posts = SocialMediaPost::where('platform', $platform);

        return [
            'total_posts' => $posts->count(),
            'published_posts' => $posts->where('status', 'published')->count(),
            'scheduled_posts' => $posts->where('status', 'scheduled')->count(),
            'avg_engagement' => $posts->avg('total_engagement') ?? 0,
            'avg_reach' => $posts->avg('reach') ?? 0,
            'best_performing_post_type' => $posts->groupBy('post_type')
                ->map(function ($group) {
                    return $group->avg('total_engagement');
                })
                ->sortDesc()
                ->keys()
                ->first() ?? 'image',
            'posting_frequency' => $posts->where('published_at', '>=', now()->subDays(30))->count() . ' posts/month',
            'optimal_posting_times' => $this->getOptimalPostingTimes($platform),
        ];
    }

    /**
     * Get optimal posting times for a platform.
     */
    private function getOptimalPostingTimes($platform)
    {
        $times = [
            'facebook' => ['1:00 PM - 4:00 PM', '6:00 PM - 9:00 PM'],
            'twitter' => ['12:00 PM - 3:00 PM', '5:00 PM - 6:00 PM'],
            'instagram' => ['11:00 AM - 1:00 PM', '7:00 PM - 9:00 PM'],
            'linkedin' => ['9:00 AM - 11:00 AM', '12:00 PM - 1:00 PM'],
            'youtube' => ['2:00 PM - 4:00 PM', '7:00 PM - 10:00 PM'],
            'tiktok' => ['6:00 PM - 10:00 PM', '12:00 PM - 2:00 PM'],
        ];

        return $times[$platform] ?? ['9:00 AM - 5:00 PM'];
    }

    /**
     * Mock method to publish to social media platform.
     */
    private function publishToPlatform(SocialMediaPost $post)
    {
        // In a real implementation, this would make API calls to the respective platform
        // For now, we'll just simulate the publication
        
        // Simulate API delay
        usleep(100000); // 0.1 second delay

        // Update mock engagement metrics
        $post->update([
            'reach' => rand(1000, 50000),
            'total_engagement' => rand(50, 5000),
        ]);

        return true;
    }
}
