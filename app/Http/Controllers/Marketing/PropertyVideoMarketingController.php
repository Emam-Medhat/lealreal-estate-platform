<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\PropertyVideo;
use App\Models\Property\Property;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PropertyVideoMarketingController extends Controller
{
    /**
     * Display a listing of property videos.
     */
    public function index()
    {
        $videos = PropertyVideo::with(['property'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Marketing/Video/Index', [
            'videos' => $videos,
            'stats' => [
                'total_videos' => PropertyVideo::count(),
                'published_videos' => PropertyVideo::where('status', 'published')->count(),
                'draft_videos' => PropertyVideo::where('status', 'draft')->count(),
                'processing_videos' => PropertyVideo::where('status', 'processing')->count(),
                'total_views' => PropertyVideo::sum('views'),
                'total_duration' => PropertyVideo::sum('duration'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new property video.
     */
    public function create()
    {
        $properties = Property::where('status', 'active')->get();
        
        return Inertia::render('Marketing/Video/Create', [
            'properties' => $properties,
            'video_types' => ['property_tour', 'neighborhood_tour', 'testimonial', 'agent_intro', 'market_update', 'virtual_open_house'],
            'qualities' => ['720p', '1080p', '4k'],
            'formats' => ['mp4', 'mov', 'avi', 'wmv'],
        ]);
    }

    /**
     * Store a newly created property video.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'video_type' => 'required|string|in:property_tour,neighborhood_tour,testimonial,agent_intro,market_update,virtual_open_house',
            'video_file' => 'required|file|mimes:mp4,mov,avi,wmv|max:102400', // 100MB max
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'quality' => 'required|string|in:720p,1080p,4k',
            'duration' => 'nullable|integer|min:1',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'featured' => 'boolean',
            'allow_comments' => 'boolean',
            'allow_downloads' => 'boolean',
            'password_protected' => 'boolean',
            'password' => 'nullable|string|max:50',
            'call_to_action' => 'nullable|array',
            'call_to_action.enabled' => 'boolean',
            'call_to_action.text' => 'nullable|string|max:255',
            'call_to_action.url' => 'nullable|string|max:500',
            'call_to_action.button_text' => 'nullable|string|max:50',
            'seo_settings' => 'nullable|array',
            'seo_settings.meta_title' => 'nullable|string|max:255',
            'seo_settings.meta_description' => 'nullable|string|max:500',
            'seo_settings.meta_keywords' => 'nullable|string',
            'seo_settings.canonical_url' => 'nullable|string|max:500',
            'distribution_settings' => 'nullable|array',
            'distribution_settings.youtube' => 'boolean',
            'distribution_settings.facebook' => 'boolean',
            'distribution_settings.instagram' => 'boolean',
            'distribution_settings.tiktok' => 'boolean',
            'distribution_settings.vimeo' => 'boolean',
            'additional_media' => 'nullable|array',
            'additional_media.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:10240',
            'transcript' => 'nullable|string',
            'subtitles' => 'nullable|file|mimes:vtt,srt|max:1024',
        ]);

        // Handle video file upload
        $videoPath = $request->file('video_file')->store('property-videos', 'public');
        
        // Get video duration (mock implementation)
        $duration = $validated['duration'] ?? $this->getVideoDuration($videoPath);

        $video = PropertyVideo::create([
            'property_id' => $validated['property_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'video_type' => $validated['video_type'],
            'video_file' => $videoPath,
            'quality' => $validated['quality'],
            'duration' => $duration,
            'tags' => $validated['tags'] ?? [],
            'featured' => $validated['featured'] ?? false,
            'allow_comments' => $validated['allow_comments'] ?? true,
            'allow_downloads' => $validated['allow_downloads'] ?? false,
            'password_protected' => $validated['password_protected'] ?? false,
            'password' => $validated['password'] ?? null,
            'call_to_action' => $validated['call_to_action'] ?? [],
            'seo_settings' => $validated['seo_settings'] ?? [],
            'distribution_settings' => $validated['distribution_settings'] ?? [],
            'transcript' => $validated['transcript'] ?? null,
            'status' => 'processing',
        ]);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('video-thumbnails', 'public');
            $video->update(['thumbnail' => $thumbnailPath]);
        }

        // Handle subtitles upload
        if ($request->hasFile('subtitles')) {
            $subtitlesPath = $request->file('subtitles')->store('video-subtitles', 'public');
            $video->update(['subtitles' => $subtitlesPath]);
        }

        // Handle additional media files
        if ($request->hasFile('additional_media')) {
            $mediaPaths = [];
            foreach ($request->file('additional_media') as $file) {
                $path = $file->store('video-additional-media', 'public');
                $mediaPaths[] = $path;
            }
            $video->update(['additional_media' => json_encode($mediaPaths)]);
        }

        // Simulate video processing
        $this->processVideo($video);

        return redirect()->route('marketing.video.index')
            ->with('success', 'تم إنشاء الفيديو بنجاح وجاري المعالجة');
    }

    /**
     * Display the specified property video.
     */
    public function show(PropertyVideo $propertyVideo)
    {
        $propertyVideo->load(['property', 'comments']);

        return Inertia::render('Marketing/Video/Show', [
            'video' => $propertyVideo,
            'analytics' => $this->getVideoAnalytics($propertyVideo),
        ]);
    }

    /**
     * Show the form for editing the specified property video.
     */
    public function edit(PropertyVideo $propertyVideo)
    {
        $properties = Property::where('status', 'active')->get();
        
        return Inertia::render('Marketing/Video/Edit', [
            'video' => $propertyVideo,
            'properties' => $properties,
            'video_types' => ['property_tour', 'neighborhood_tour', 'testimonial', 'agent_intro', 'market_update', 'virtual_open_house'],
            'qualities' => ['720p', '1080p', '4k'],
            'formats' => ['mp4', 'mov', 'avi', 'wmv'],
        ]);
    }

    /**
     * Update the specified property video.
     */
    public function update(Request $request, PropertyVideo $propertyVideo)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'video_type' => 'required|string|in:property_tour,neighborhood_tour,testimonial,agent_intro,market_update,virtual_open_house',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:102400',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'quality' => 'required|string|in:720p,1080p,4k',
            'duration' => 'nullable|integer|min:1',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'featured' => 'boolean',
            'allow_comments' => 'boolean',
            'allow_downloads' => 'boolean',
            'password_protected' => 'boolean',
            'password' => 'nullable|string|max:50',
            'call_to_action' => 'nullable|array',
            'call_to_action.enabled' => 'boolean',
            'call_to_action.text' => 'nullable|string|max:255',
            'call_to_action.url' => 'nullable|string|max:500',
            'call_to_action.button_text' => 'nullable|string|max:50',
            'seo_settings' => 'nullable|array',
            'seo_settings.meta_title' => 'nullable|string|max:255',
            'seo_settings.meta_description' => 'nullable|string|max:500',
            'seo_settings.meta_keywords' => 'nullable|string',
            'seo_settings.canonical_url' => 'nullable|string|max:500',
            'distribution_settings' => 'nullable|array',
            'distribution_settings.youtube' => 'boolean',
            'distribution_settings.facebook' => 'boolean',
            'distribution_settings.instagram' => 'boolean',
            'distribution_settings.tiktok' => 'boolean',
            'distribution_settings.vimeo' => 'boolean',
            'additional_media' => 'nullable|array',
            'additional_media.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:10240',
            'transcript' => 'nullable|string',
            'subtitles' => 'nullable|file|mimes:vtt,srt|max:1024',
        ]);

        $propertyVideo->update([
            'property_id' => $validated['property_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'video_type' => $validated['video_type'],
            'quality' => $validated['quality'],
            'duration' => $validated['duration'] ?? $propertyVideo->duration,
            'tags' => $validated['tags'] ?? [],
            'featured' => $validated['featured'] ?? false,
            'allow_comments' => $validated['allow_comments'] ?? true,
            'allow_downloads' => $validated['allow_downloads'] ?? false,
            'password_protected' => $validated['password_protected'] ?? false,
            'password' => $validated['password'] ?? null,
            'call_to_action' => $validated['call_to_action'] ?? [],
            'seo_settings' => $validated['seo_settings'] ?? [],
            'distribution_settings' => $validated['distribution_settings'] ?? [],
            'transcript' => $validated['transcript'] ?? null,
        ]);

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            // Delete old video file
            if ($propertyVideo->video_file) {
                Storage::disk('public')->delete($propertyVideo->video_file);
            }
            
            $videoPath = $request->file('video_file')->store('property-videos', 'public');
            $propertyVideo->update([
                'video_file' => $videoPath,
                'status' => 'processing',
            ]);
            
            // Simulate video processing
            $this->processVideo($propertyVideo);
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($propertyVideo->thumbnail) {
                Storage::disk('public')->delete($propertyVideo->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('video-thumbnails', 'public');
            $propertyVideo->update(['thumbnail' => $thumbnailPath]);
        }

        // Handle subtitles upload
        if ($request->hasFile('subtitles')) {
            // Delete old subtitles
            if ($propertyVideo->subtitles) {
                Storage::disk('public')->delete($propertyVideo->subtitles);
            }
            $subtitlesPath = $request->file('subtitles')->store('video-subtitles', 'public');
            $propertyVideo->update(['subtitles' => $subtitlesPath]);
        }

        // Handle additional media files
        if ($request->hasFile('additional_media')) {
            // Delete old media files
            if ($propertyVideo->additional_media) {
                $oldMediaFiles = json_decode($propertyVideo->additional_media, true);
                foreach ($oldMediaFiles as $oldFile) {
                    Storage::disk('public')->delete($oldFile);
                }
            }

            $mediaPaths = [];
            foreach ($request->file('additional_media') as $file) {
                $path = $file->store('video-additional-media', 'public');
                $mediaPaths[] = $path;
            }
            $propertyVideo->update(['additional_media' => json_encode($mediaPaths)]);
        }

        return redirect()->route('marketing.video.index')
            ->with('success', 'تم تحديث الفيديو بنجاح');
    }

    /**
     * Remove the specified property video.
     */
    public function destroy(PropertyVideo $propertyVideo)
    {
        // Delete associated files
        if ($propertyVideo->video_file) {
            Storage::disk('public')->delete($propertyVideo->video_file);
        }
        if ($propertyVideo->thumbnail) {
            Storage::disk('public')->delete($propertyVideo->thumbnail);
        }
        if ($propertyVideo->subtitles) {
            Storage::disk('public')->delete($propertyVideo->subtitles);
        }
        if ($propertyVideo->additional_media) {
            $mediaFiles = json_decode($propertyVideo->additional_media, true);
            foreach ($mediaFiles as $file) {
                Storage::disk('public')->delete($file);
            }
        }

        $propertyVideo->delete();

        return redirect()->route('marketing.video.index')
            ->with('success', 'تم حذف الفيديو بنجاح');
    }

    /**
     * Publish a property video.
     */
    public function publish(PropertyVideo $propertyVideo)
    {
        if ($propertyVideo->status !== 'draft') {
            return back()->with('error', 'لا يمكن نشر هذا الفيديو');
        }

        $propertyVideo->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return back()->with('success', 'تم نشر الفيديو بنجاح');
    }

    /**
     * Get analytics for a property video.
     */
    public function analytics(PropertyVideo $propertyVideo)
    {
        $analytics = $this->getVideoAnalytics($propertyVideo);

        return Inertia::render('Marketing/Video/Analytics', [
            'video' => $propertyVideo,
            'analytics' => $analytics,
        ]);
    }

    /**
     * Duplicate a property video.
     */
    public function duplicate(PropertyVideo $propertyVideo)
    {
        $newVideo = $propertyVideo->replicate();
        $newVideo->title = $propertyVideo->title . ' (نسخة)';
        $newVideo->status = 'draft';
        $newVideo->published_at = null;
        $newVideo->views = 0;
        $newVideo->save();

        return redirect()->route('marketing.video.edit', $newVideo)
            ->with('success', 'تم نسخ الفيديو بنجاح');
    }

    /**
     * Export property videos data.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $videos = PropertyVideo::with(['property'])->get();

        if ($format === 'csv') {
            $filename = 'property-videos-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($videos) {
                $file = fopen('php://output', 'w');
                
                // CSV Header
                fputcsv($file, [
                    'ID', 'العنوان', 'العقار', 'نوع الفيديو', 'الحالة', 
                    'الجودة', 'المدة', 'المشاهدات', 'مميز', 'تاريخ النشر'
                ]);

                // CSV Data
                foreach ($videos as $video) {
                    fputcsv($file, [
                        $video->id,
                        $video->title,
                        $video->property?->title ?? 'N/A',
                        $video->video_type,
                        $video->status,
                        $video->quality,
                        $video->duration,
                        $video->views,
                        $video->featured ? 'نعم' : 'لا',
                        $video->published_at?->format('Y-m-d H:i:s') ?? 'N/A'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return back()->with('error', 'تنسيق التصدير غير مدعوم');
    }

    /**
     * Get video analytics data.
     */
    private function getVideoAnalytics(PropertyVideo $video)
    {
        // Mock analytics data
        return [
            'engagement_metrics' => [
                'views' => $video->views,
                'unique_viewers' => rand($video->views * 0.7, $video->views),
                'average_watch_time' => rand(30, $video->duration * 0.8) . ' seconds',
                'completion_rate' => rand(20, 80) . '%',
                'engagement_rate' => rand(5, 25) . '%',
                'shares' => rand(10, 500),
                'likes' => rand(50, 2000),
                'comments' => rand(5, 100),
                'downloads' => $video->allow_downloads ? rand(5, 50) : 0,
            ],
            'audience_demographics' => [
                'age_groups' => [
                    '18-24' => rand(10, 20),
                    '25-34' => rand(25, 40),
                    '35-44' => rand(20, 35),
                    '45-54' => rand(15, 25),
                    '55+' => rand(5, 15),
                ],
                'genders' => [
                    'male' => rand(45, 55),
                    'female' => rand(45, 55),
                ],
                'locations' => [
                    'الرياض' => rand(25, 40),
                    'جدة' => rand(15, 25),
                    'الدمام' => rand(10, 20),
                    'مكة' => rand(8, 15),
                    'أخرى' => rand(10, 20),
                ],
                'devices' => [
                    'desktop' => rand(40, 60),
                    'mobile' => rand(30, 50),
                    'tablet' => rand(5, 15),
                ],
            ],
            'performance_over_time' => [
                'views_trend' => [
                    'last_7_days' => rand(100, 1000),
                    'last_30_days' => rand(500, 5000),
                    'last_90_days' => rand(2000, 20000),
                ],
                'peak_viewing_times' => [
                    'morning' => rand(15, 25),
                    'afternoon' => rand(20, 30),
                    'evening' => rand(35, 45),
                    'night' => rand(10, 20),
                ],
            ],
            'conversion_metrics' => [
                'click_through_rate' => rand(2, 10) . '%',
                'lead_generation' => rand(5, 50),
                'property_inquiries' => rand(3, 30),
                'tour_requests' => rand(1, 15),
            ],
            'technical_metrics' => [
                'buffer_rate' => rand(1, 10) . '%',
                'playback_failures' => rand(0, 5),
                'average_bandwidth' => rand(2, 10) . ' Mbps',
                'video_quality_distribution' => [
                    '720p' => rand(20, 40),
                    '1080p' => rand(40, 60),
                    '4k' => rand(5, 20),
                ],
            ],
        ];
    }

    /**
     * Get video duration (mock implementation).
     */
    private function getVideoDuration($videoPath)
    {
        // In a real implementation, this would use a video processing library
        // to get the actual duration of the video file
        return rand(60, 600); // Random duration between 1-10 minutes
    }

    /**
     * Process video (mock implementation).
     */
    private function processVideo(PropertyVideo $video)
    {
        // In a real implementation, this would use a video processing service
        // like FFmpeg or a cloud service to process the video
        
        // Simulate processing delay
        sleep(2);
        
        // Update status to published after processing
        $video->update([
            'status' => 'published',
            'processed_at' => now(),
        ]);
    }
}
