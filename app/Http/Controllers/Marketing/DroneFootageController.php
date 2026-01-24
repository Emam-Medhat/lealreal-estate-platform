<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\DroneFootage;
use App\Models\Property\Property;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DroneFootageController extends Controller
{
    /**
     * Display a listing of drone footage.
     */
    public function index()
    {
        $footages = DroneFootage::with(['property'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Marketing/Drone/Index', [
            'footages' => $footages,
            'stats' => [
                'total_footages' => DroneFootage::count(),
                'published_footages' => DroneFootage::where('status', 'published')->count(),
                'draft_footages' => DroneFootage::where('status', 'draft')->count(),
                'processing_footages' => DroneFootage::where('status', 'processing')->count(),
                'total_views' => DroneFootage::sum('views'),
                'total_duration' => DroneFootage::sum('duration'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new drone footage.
     */
    public function create()
    {
        $properties = Property::where('status', 'active')->get();
        
        return Inertia::render('Marketing/Drone/Create', [
            'properties' => $properties,
            'footage_types' => ['aerial_tour', 'neighborhood_overview', 'property_highlight', 'construction_progress', 'before_after', 'cinematic'],
            'qualities' => ['720p', '1080p', '4k', '8k'],
            'formats' => ['mp4', 'mov', 'avi', 'wmv'],
            'weather_conditions' => ['sunny', 'cloudy', 'partly_cloudy', 'golden_hour', 'blue_hour', 'sunset', 'sunrise'],
        ]);
    }

    /**
     * Store a newly created drone footage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'footage_type' => 'required|string|in:aerial_tour,neighborhood_overview,property_highlight,construction_progress,before_after,cinematic',
            'video_file' => 'required|file|mimes:mp4,mov,avi,wmv|max:204800', // 200MB max
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'quality' => 'required|string|in:720p,1080p,4k,8k',
            'duration' => 'nullable|integer|min:1',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'featured' => 'boolean',
            'allow_comments' => 'boolean',
            'allow_downloads' => 'boolean',
            'password_protected' => 'boolean',
            'password' => 'nullable|string|max:50',
            'flight_info' => 'nullable|array',
            'flight_info.drone_model' => 'nullable|string|max:255',
            'flight_info.pilot_name' => 'nullable|string|max:255',
            'flight_info.flight_date' => 'nullable|date',
            'flight_info.weather_condition' => 'nullable|string|in:sunny,cloudy,partly_cloudy,golden_hour,blue_hour,sunset,sunrise',
            'flight_info.altitude' => 'nullable|integer|min:1|max:500',
            'flight_info.flight_time' => 'nullable|integer|min:1',
            'flight_info.location_coordinates' => 'nullable|string|max:255',
            'flight_info.permit_required' => 'boolean',
            'flight_info.permit_number' => 'nullable|string|max:255',
            'editing_info' => 'nullable|array',
            'editing_info.software_used' => 'nullable|string|max:255',
            'editing_info.editor_name' => 'nullable|string|max:255',
            'editing_info.color_grading' => 'boolean',
            'editing_info.sound_design' => 'boolean',
            'editing_info.special_effects' => 'boolean',
            'editing_info.editing_duration' => 'nullable|integer|min:1',
            'music_info' => 'nullable|array',
            'music_info.track_title' => 'nullable|string|max:255',
            'music_info.artist' => 'nullable|string|max:255',
            'music_info.license_type' => 'nullable|string|in:royalty_free,licensed,custom',
            'music_info.license_number' => 'nullable|string|max:255',
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
            'distribution_settings.vimeo' => 'boolean',
            'distribution_settings.instagram' => 'boolean',
            'distribution_settings.tiktok' => 'boolean',
            'distribution_settings.facebook' => 'boolean',
            'additional_media' => 'nullable|array',
            'additional_media.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:10240',
            'transcript' => 'nullable|string',
            'subtitles' => 'nullable|file|mimes:vtt,srt|max:1024',
            'behind_the_scenes' => 'nullable|array',
            'behind_the_scenes.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle video file upload
        $videoPath = $request->file('video_file')->store('drone-footages', 'public');
        
        // Get video duration (mock implementation)
        $duration = $validated['duration'] ?? $this->getVideoDuration($videoPath);

        $footage = DroneFootage::create([
            'property_id' => $validated['property_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'footage_type' => $validated['footage_type'],
            'video_file' => $videoPath,
            'quality' => $validated['quality'],
            'duration' => $duration,
            'tags' => $validated['tags'] ?? [],
            'featured' => $validated['featured'] ?? false,
            'allow_comments' => $validated['allow_comments'] ?? true,
            'allow_downloads' => $validated['allow_downloads'] ?? false,
            'password_protected' => $validated['password_protected'] ?? false,
            'password' => $validated['password'] ?? null,
            'flight_info' => $validated['flight_info'] ?? [],
            'editing_info' => $validated['editing_info'] ?? [],
            'music_info' => $validated['music_info'] ?? [],
            'call_to_action' => $validated['call_to_action'] ?? [],
            'seo_settings' => $validated['seo_settings'] ?? [],
            'distribution_settings' => $validated['distribution_settings'] ?? [],
            'transcript' => $validated['transcript'] ?? null,
            'status' => 'processing',
        ]);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('drone-thumbnails', 'public');
            $footage->update(['thumbnail' => $thumbnailPath]);
        }

        // Handle subtitles upload
        if ($request->hasFile('subtitles')) {
            $subtitlesPath = $request->file('subtitles')->store('drone-subtitles', 'public');
            $footage->update(['subtitles' => $subtitlesPath]);
        }

        // Handle additional media files
        if ($request->hasFile('additional_media')) {
            $mediaPaths = [];
            foreach ($request->file('additional_media') as $file) {
                $path = $file->store('drone-additional-media', 'public');
                $mediaPaths[] = $path;
            }
            $footage->update(['additional_media' => json_encode($mediaPaths)]);
        }

        // Handle behind the scenes images
        if ($request->hasFile('behind_the_scenes')) {
            $btsPaths = [];
            foreach ($request->file('behind_the_scenes') as $image) {
                $path = $image->store('drone-behind-scenes', 'public');
                $btsPaths[] = $path;
            }
            $footage->update(['behind_the_scenes' => json_encode($btsPaths)]);
        }

        // Simulate video processing
        $this->processVideo($footage);

        return redirect()->route('marketing.drone.index')
            ->with('success', 'تم إنشاء لقطات الدرون بنجاح وجاري المعالجة');
    }

    /**
     * Display the specified drone footage.
     */
    public function show(DroneFootage $droneFootage)
    {
        $droneFootage->load(['property', 'comments']);

        return Inertia::render('Marketing/Drone/Show', [
            'footage' => $droneFootage,
            'analytics' => $this->getFootageAnalytics($droneFootage),
        ]);
    }

    /**
     * Show the form for editing the specified drone footage.
     */
    public function edit(DroneFootage $droneFootage)
    {
        $properties = Property::where('status', 'active')->get();
        
        return Inertia::render('Marketing/Drone/Edit', [
            'footage' => $droneFootage,
            'properties' => $properties,
            'footage_types' => ['aerial_tour', 'neighborhood_overview', 'property_highlight', 'construction_progress', 'before_after', 'cinematic'],
            'qualities' => ['720p', '1080p', '4k', '8k'],
            'formats' => ['mp4', 'mov', 'avi', 'wmv'],
            'weather_conditions' => ['sunny', 'cloudy', 'partly_cloudy', 'golden_hour', 'blue_hour', 'sunset', 'sunrise'],
        ]);
    }

    /**
     * Update the specified drone footage.
     */
    public function update(Request $request, DroneFootage $droneFootage)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'footage_type' => 'required|string|in:aerial_tour,neighborhood_overview,property_highlight,construction_progress,before_after,cinematic',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:204800',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'quality' => 'required|string|in:720p,1080p,4k,8k',
            'duration' => 'nullable|integer|min:1',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'featured' => 'boolean',
            'allow_comments' => 'boolean',
            'allow_downloads' => 'boolean',
            'password_protected' => 'boolean',
            'password' => 'nullable|string|max:50',
            'flight_info' => 'nullable|array',
            'flight_info.drone_model' => 'nullable|string|max:255',
            'flight_info.pilot_name' => 'nullable|string|max:255',
            'flight_info.flight_date' => 'nullable|date',
            'flight_info.weather_condition' => 'nullable|string|in:sunny,cloudy,partly_cloudy,golden_hour,blue_hour,sunset,sunrise',
            'flight_info.altitude' => 'nullable|integer|min:1|max:500',
            'flight_info.flight_time' => 'nullable|integer|min:1',
            'flight_info.location_coordinates' => 'nullable|string|max:255',
            'flight_info.permit_required' => 'boolean',
            'flight_info.permit_number' => 'nullable|string|max:255',
            'editing_info' => 'nullable|array',
            'editing_info.software_used' => 'nullable|string|max:255',
            'editing_info.editor_name' => 'nullable|string|max:255',
            'editing_info.color_grading' => 'boolean',
            'editing_info.sound_design' => 'boolean',
            'editing_info.special_effects' => 'boolean',
            'editing_info.editing_duration' => 'nullable|integer|min:1',
            'music_info' => 'nullable|array',
            'music_info.track_title' => 'nullable|string|max:255',
            'music_info.artist' => 'nullable|string|max:255',
            'music_info.license_type' => 'nullable|string|in:royalty_free,licensed,custom',
            'music_info.license_number' => 'nullable|string|max:255',
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
            'distribution_settings.vimeo' => 'boolean',
            'distribution_settings.instagram' => 'boolean',
            'distribution_settings.tiktok' => 'boolean',
            'distribution_settings.facebook' => 'boolean',
            'additional_media' => 'nullable|array',
            'additional_media.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:10240',
            'transcript' => 'nullable|string',
            'subtitles' => 'nullable|file|mimes:vtt,srt|max:1024',
            'behind_the_scenes' => 'nullable|array',
            'behind_the_scenes.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $droneFootage->update([
            'property_id' => $validated['property_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'footage_type' => $validated['footage_type'],
            'quality' => $validated['quality'],
            'duration' => $validated['duration'] ?? $droneFootage->duration,
            'tags' => $validated['tags'] ?? [],
            'featured' => $validated['featured'] ?? false,
            'allow_comments' => $validated['allow_comments'] ?? true,
            'allow_downloads' => $validated['allow_downloads'] ?? false,
            'password_protected' => $validated['password_protected'] ?? false,
            'password' => $validated['password'] ?? null,
            'flight_info' => $validated['flight_info'] ?? [],
            'editing_info' => $validated['editing_info'] ?? [],
            'music_info' => $validated['music_info'] ?? [],
            'call_to_action' => $validated['call_to_action'] ?? [],
            'seo_settings' => $validated['seo_settings'] ?? [],
            'distribution_settings' => $validated['distribution_settings'] ?? [],
            'transcript' => $validated['transcript'] ?? null,
        ]);

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            // Delete old video file
            if ($droneFootage->video_file) {
                Storage::disk('public')->delete($droneFootage->video_file);
            }
            
            $videoPath = $request->file('video_file')->store('drone-footages', 'public');
            $droneFootage->update([
                'video_file' => $videoPath,
                'status' => 'processing',
            ]);
            
            // Simulate video processing
            $this->processVideo($droneFootage);
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($droneFootage->thumbnail) {
                Storage::disk('public')->delete($droneFootage->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('drone-thumbnails', 'public');
            $droneFootage->update(['thumbnail' => $thumbnailPath]);
        }

        // Handle subtitles upload
        if ($request->hasFile('subtitles')) {
            // Delete old subtitles
            if ($droneFootage->subtitles) {
                Storage::disk('public')->delete($droneFootage->subtitles);
            }
            $subtitlesPath = $request->file('subtitles')->store('drone-subtitles', 'public');
            $droneFootage->update(['subtitles' => $subtitlesPath]);
        }

        // Handle additional media files
        if ($request->hasFile('additional_media')) {
            // Delete old media files
            if ($droneFootage->additional_media) {
                $oldMediaFiles = json_decode($droneFootage->additional_media, true);
                foreach ($oldMediaFiles as $oldFile) {
                    Storage::disk('public')->delete($oldFile);
                }
            }

            $mediaPaths = [];
            foreach ($request->file('additional_media') as $file) {
                $path = $file->store('drone-additional-media', 'public');
                $mediaPaths[] = $path;
            }
            $droneFootage->update(['additional_media' => json_encode($mediaPaths)]);
        }

        // Handle behind the scenes images
        if ($request->hasFile('behind_the_scenes')) {
            // Delete old BTS images
            if ($droneFootage->behind_the_scenes) {
                $oldBtsImages = json_decode($droneFootage->behind_the_scenes, true);
                foreach ($oldBtsImages as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $btsPaths = [];
            foreach ($request->file('behind_the_scenes') as $image) {
                $path = $image->store('drone-behind-scenes', 'public');
                $btsPaths[] = $path;
            }
            $droneFootage->update(['behind_the_scenes' => json_encode($btsPaths)]);
        }

        return redirect()->route('marketing.drone.index')
            ->with('success', 'تم تحديث لقطات الدرون بنجاح');
    }

    /**
     * Remove the specified drone footage.
     */
    public function destroy(DroneFootage $droneFootage)
    {
        // Delete associated files
        if ($droneFootage->video_file) {
            Storage::disk('public')->delete($droneFootage->video_file);
        }
        if ($droneFootage->thumbnail) {
            Storage::disk('public')->delete($droneFootage->thumbnail);
        }
        if ($droneFootage->subtitles) {
            Storage::disk('public')->delete($droneFootage->subtitles);
        }
        if ($droneFootage->additional_media) {
            $mediaFiles = json_decode($droneFootage->additional_media, true);
            foreach ($mediaFiles as $file) {
                Storage::disk('public')->delete($file);
            }
        }
        if ($droneFootage->behind_the_scenes) {
            $btsImages = json_decode($droneFootage->behind_the_scenes, true);
            foreach ($btsImages as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $droneFootage->delete();

        return redirect()->route('marketing.drone.index')
            ->with('success', 'تم حذف لقطات الدرون بنجاح');
    }

    /**
     * Publish drone footage.
     */
    public function publish(DroneFootage $droneFootage)
    {
        if ($droneFootage->status !== 'draft') {
            return back()->with('error', 'لا يمكن نشر هذه اللقطات');
        }

        $droneFootage->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return back()->with('success', 'تم نشر اللقطات بنجاح');
    }

    /**
     * Get analytics for drone footage.
     */
    public function analytics(DroneFootage $droneFootage)
    {
        $analytics = $this->getFootageAnalytics($droneFootage);

        return Inertia::render('Marketing/Drone/Analytics', [
            'footage' => $droneFootage,
            'analytics' => $analytics,
        ]);
    }

    /**
     * Duplicate drone footage.
     */
    public function duplicate(DroneFootage $droneFootage)
    {
        $newFootage = $droneFootage->replicate();
        $newFootage->title = $droneFootage->title . ' (نسخة)';
        $newFootage->status = 'draft';
        $newFootage->published_at = null;
        $newFootage->views = 0;
        $newFootage->save();

        return redirect()->route('marketing.drone.edit', $newFootage)
            ->with('success', 'تم نسخ اللقطات بنجاح');
    }

    /**
     * Export drone footage data.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $footages = DroneFootage::with(['property'])->get();

        if ($format === 'csv') {
            $filename = 'drone-footages-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($footages) {
                $file = fopen('php://output', 'w');
                
                // CSV Header
                fputcsv($file, [
                    'ID', 'العنوان', 'العقار', 'نوع اللقطات', 'الحالة', 
                    'الجودة', 'المدة', 'المشاهدات', 'مميز', 'تاريخ النشر'
                ]);

                // CSV Data
                foreach ($footages as $footage) {
                    fputcsv($file, [
                        $footage->id,
                        $footage->title,
                        $footage->property?->title ?? 'N/A',
                        $footage->footage_type,
                        $footage->status,
                        $footage->quality,
                        $footage->duration,
                        $footage->views,
                        $footage->featured ? 'نعم' : 'لا',
                        $footage->published_at?->format('Y-m-d H:i:s') ?? 'N/A'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return back()->with('error', 'تنسيق التصدير غير مدعوم');
    }

    /**
     * Get footage analytics data.
     */
    private function getFootageAnalytics(DroneFootage $footage)
    {
        // Mock analytics data
        return [
            'engagement_metrics' => [
                'views' => $footage->views,
                'unique_viewers' => rand($footage->views * 0.7, $footage->views),
                'average_watch_time' => rand(30, $footage->duration * 0.8) . ' seconds',
                'completion_rate' => rand(25, 85) . '%',
                'engagement_rate' => rand(8, 30) . '%',
                'shares' => rand(20, 800),
                'likes' => rand(100, 3000),
                'comments' => rand(10, 150),
                'downloads' => $footage->allow_downloads ? rand(10, 80) : 0,
            ],
            'audience_demographics' => [
                'age_groups' => [
                    '18-24' => rand(8, 18),
                    '25-34' => rand(22, 38),
                    '35-44' => rand(25, 40),
                    '45-54' => rand(12, 28),
                    '55+' => rand(5, 12),
                ],
                'genders' => [
                    'male' => rand(50, 60),
                    'female' => rand(40, 50),
                ],
                'locations' => [
                    'الرياض' => rand(20, 35),
                    'جدة' => rand(18, 30),
                    'الدمام' => rand(12, 22),
                    'مكة' => rand(10, 18),
                    'أخرى' => rand(15, 25),
                ],
                'devices' => [
                    'desktop' => rand(45, 65),
                    'mobile' => rand(25, 45),
                    'tablet' => rand(5, 15),
                ],
            ],
            'performance_over_time' => [
                'views_trend' => [
                    'last_7_days' => rand(200, 1500),
                    'last_30_days' => rand(1000, 8000),
                    'last_90_days' => rand(5000, 30000),
                ],
                'peak_viewing_times' => [
                    'morning' => rand(12, 22),
                    'afternoon' => rand(18, 28),
                    'evening' => rand(40, 50),
                    'night' => rand(8, 18),
                ],
            ],
            'conversion_metrics' => [
                'click_through_rate' => rand(3, 12) . '%',
                'lead_generation' => rand(8, 60),
                'property_inquiries' => rand(5, 40),
                'tour_requests' => rand(2, 20),
            ],
            'technical_metrics' => [
                'buffer_rate' => rand(1, 8) . '%',
                'playback_failures' => rand(0, 3),
                'average_bandwidth' => rand(3, 15) . ' Mbps',
                'video_quality_distribution' => [
                    '720p' => rand(15, 25),
                    '1080p' => rand(35, 50),
                    '4k' => rand(20, 35),
                    '8k' => rand(2, 10),
                ],
            ],
            'drone_specific_metrics' => [
                'aerial_shots_impression' => rand(70, 95) . '%',
                'cinematic_quality_rating' => rand(4.0, 4.8) . '/5',
                'unique_camera_angles' => rand(8, 25),
                'property_coverage_score' => rand(75, 95) . '%',
                'neighborhood_visibility_score' => rand(60, 90) . '%',
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
        return rand(60, 900); // Random duration between 1-15 minutes
    }

    /**
     * Process video (mock implementation).
     */
    private function processVideo(DroneFootage $footage)
    {
        // In a real implementation, this would use a video processing service
        // like FFmpeg or a cloud service to process the video
        
        // Simulate processing delay
        sleep(3);
        
        // Update status to published after processing
        $footage->update([
            'status' => 'published',
            'processed_at' => now(),
        ]);
    }
}
