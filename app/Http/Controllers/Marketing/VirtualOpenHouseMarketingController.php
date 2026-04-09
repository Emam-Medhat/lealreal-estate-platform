<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\VirtualOpenHouseMarketing;
use App\Models\Property\Property;
use App\Services\MarketingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VirtualOpenHouseMarketingController extends Controller
{
    protected $marketingService;

    public function __construct(MarketingService $marketingService)
    {
        $this->marketingService = $marketingService;
    }

    /**
     * Display a listing of virtual open house marketing campaigns.
     */
    public function index()
    {
        return Inertia::render('Marketing/VirtualOpenHouse/Index', [
            'campaigns' => $this->marketingService->getVirtualOpenHouseCampaigns(),
            'stats' => $this->marketingService->getVirtualOpenHouseStats()
        ]);
    }

    /**
     * Show the form for creating a new virtual open house marketing campaign.
     */
    public function create()
    {
        return Inertia::render('Marketing/VirtualOpenHouse/Create', [
            'properties' => $this->marketingService->getActiveProperties(),
            'platforms' => ['zoom', 'teams', 'google_meet', 'skype', 'custom'],
            'event_types' => ['live_tour', 'recorded_tour', 'qna_session', 'webinar', 'presentation'],
        ]);
    }

    /**
     * Store a newly created virtual open house marketing campaign.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'platform' => 'required|string|in:zoom,teams,google_meet,skype,custom',
            'event_type' => 'required|string|in:live_tour,recorded_tour,qna_session,webinar,presentation',
            'scheduled_at' => 'required|date|after:now',
            'duration' => 'required|integer|min:15|max:480',
            'max_attendees' => 'nullable|integer|min:1|max:1000',
            'registration_required' => 'boolean',
            'registration_deadline' => 'nullable|date|before:scheduled_at',
            'meeting_link' => 'nullable|string|max:500',
            'meeting_id' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:50',
            'host_info' => 'nullable|array',
            'host_info.name' => 'nullable|string|max:255',
            'host_info.email' => 'nullable|email|max:255',
            'host_info.phone' => 'nullable|string|max:20',
            'host_info.bio' => 'nullable|string',
            'promotion_channels' => 'nullable|array',
            'promotion_channels.*' => 'string|in:email,social_media,website,sms,whatsapp',
            'email_template' => 'nullable|string',
            'social_media_posts' => 'nullable|array',
            'social_media_posts.*.platform' => 'required|string|in:facebook,twitter,instagram,linkedin',
            'social_media_posts.*.content' => 'required|string',
            'social_media_posts.*.scheduled_at' => 'nullable|date',
            'reminder_settings' => 'nullable|array',
            'reminder_settings.enabled' => 'boolean',
            'reminder_settings.intervals' => 'nullable|array',
            'reminder_settings.intervals.*' => 'integer|min:1',
            'recording_settings' => 'nullable|array',
            'recording_settings.record_session' => 'boolean',
            'recording_settings.auto_share' => 'boolean',
            'recording_settings.share_duration' => 'nullable|integer|min:1|max:30',
            'follow_up_settings' => 'nullable|array',
            'follow_up_settings.enabled' => 'boolean',
            'follow_up_settings.send_recording' => 'boolean',
            'follow_up_settings.send_survey' => 'boolean',
            'follow_up_settings.schedule_next_steps' => 'boolean',
            'custom_banner' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'featured_images' => 'nullable|array',
            'featured_images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'virtual_tour_link' => 'nullable|string|max:500',
            'property_video_url' => 'nullable|string|max:500',
            'floor_plans' => 'nullable|array',
            'floor_plans.*' => 'image|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $campaign = VirtualOpenHouseMarketing::create([
            'property_id' => $validated['property_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'platform' => $validated['platform'],
            'event_type' => $validated['event_type'],
            'scheduled_at' => $validated['scheduled_at'],
            'duration' => $validated['duration'],
            'max_attendees' => $validated['max_attendees'] ?? null,
            'registration_required' => $validated['registration_required'] ?? false,
            'registration_deadline' => $validated['registration_deadline'] ?? null,
            'meeting_link' => $validated['meeting_link'] ?? null,
            'meeting_id' => $validated['meeting_id'] ?? null,
            'password' => $validated['password'] ?? null,
            'host_info' => $validated['host_info'] ?? [],
            'promotion_channels' => $validated['promotion_channels'] ?? [],
            'email_template' => $validated['email_template'] ?? null,
            'social_media_posts' => $validated['social_media_posts'] ?? [],
            'reminder_settings' => $validated['reminder_settings'] ?? [],
            'recording_settings' => $validated['recording_settings'] ?? [],
            'follow_up_settings' => $validated['follow_up_settings'] ?? [],
            'virtual_tour_link' => $validated['virtual_tour_link'] ?? null,
            'property_video_url' => $validated['property_video_url'] ?? null,
            'status' => 'scheduled',
        ]);

        // Handle custom banner upload
        if ($request->hasFile('custom_banner')) {
            $path = $request->file('custom_banner')->store('virtual-open-house-banners', 'public');
            $campaign->update(['custom_banner' => $path]);
        }

        // Handle featured images upload
        if ($request->hasFile('featured_images')) {
            $imagePaths = [];
            foreach ($request->file('featured_images') as $image) {
                $path = $image->store('virtual-open-house-images', 'public');
                $imagePaths[] = $path;
            }
            $campaign->update(['featured_images' => json_encode($imagePaths)]);
        }

        // Handle floor plans upload
        if ($request->hasFile('floor_plans')) {
            $planPaths = [];
            foreach ($request->file('floor_plans') as $plan) {
                $path = $plan->store('virtual-open-house-plans', 'public');
                $planPaths[] = $path;
            }
            $campaign->update(['floor_plans' => json_encode($planPaths)]);
        }

        $this->marketingService->clearCache();

        return redirect()->route('marketing.virtual-open-house.index')
            ->with('success', 'تم إنشاء حملة البيت المفتوح الافتراضي بنجاح');
    }

    /**
     * Display the specified virtual open house marketing campaign.
     */
    public function show(VirtualOpenHouseMarketing $virtualOpenHouseMarketing)
    {
        $virtualOpenHouseMarketing->load(['property', 'registrations']);

        return Inertia::render('Marketing/VirtualOpenHouse/Show', [
            'campaign' => $virtualOpenHouseMarketing,
            'analytics' => $this->getCampaignAnalytics($virtualOpenHouseMarketing),
        ]);
    }

    /**
     * Show the form for editing the specified virtual open house marketing campaign.
     */
    public function edit(VirtualOpenHouseMarketing $virtualOpenHouseMarketing)
    {
        $properties = Property::where('status', 'active')->get();
        
        return Inertia::render('Marketing/VirtualOpenHouse/Edit', [
            'campaign' => $virtualOpenHouseMarketing,
            'properties' => $properties,
            'platforms' => ['zoom', 'teams', 'google_meet', 'skype', 'custom'],
            'event_types' => ['live_tour', 'recorded_tour', 'qna_session', 'webinar', 'presentation'],
        ]);
    }

    /**
     * Update the specified virtual open house marketing campaign.
     */
    public function update(Request $request, VirtualOpenHouseMarketing $virtualOpenHouseMarketing)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'platform' => 'required|string|in:zoom,teams,google_meet,skype,custom',
            'event_type' => 'required|string|in:live_tour,recorded_tour,qna_session,webinar,presentation',
            'scheduled_at' => 'required|date|after:now',
            'duration' => 'required|integer|min:15|max:480',
            'max_attendees' => 'nullable|integer|min:1|max:1000',
            'registration_required' => 'boolean',
            'registration_deadline' => 'nullable|date|before:scheduled_at',
            'meeting_link' => 'nullable|string|max:500',
            'meeting_id' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:50',
            'host_info' => 'nullable|array',
            'host_info.name' => 'nullable|string|max:255',
            'host_info.email' => 'nullable|email|max:255',
            'host_info.phone' => 'nullable|string|max:20',
            'host_info.bio' => 'nullable|string',
            'promotion_channels' => 'nullable|array',
            'promotion_channels.*' => 'string|in:email,social_media,website,sms,whatsapp',
            'email_template' => 'nullable|string',
            'social_media_posts' => 'nullable|array',
            'social_media_posts.*.platform' => 'required|string|in:facebook,twitter,instagram,linkedin',
            'social_media_posts.*.content' => 'required|string',
            'social_media_posts.*.scheduled_at' => 'nullable|date',
            'reminder_settings' => 'nullable|array',
            'reminder_settings.enabled' => 'boolean',
            'reminder_settings.intervals' => 'nullable|array',
            'reminder_settings.intervals.*' => 'integer|min:1',
            'recording_settings' => 'nullable|array',
            'recording_settings.record_session' => 'boolean',
            'recording_settings.auto_share' => 'boolean',
            'recording_settings.share_duration' => 'nullable|integer|min:1|max:30',
            'follow_up_settings' => 'nullable|array',
            'follow_up_settings.enabled' => 'boolean',
            'follow_up_settings.send_recording' => 'boolean',
            'follow_up_settings.send_survey' => 'boolean',
            'follow_up_settings.schedule_next_steps' => 'boolean',
            'custom_banner' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'featured_images' => 'nullable|array',
            'featured_images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'virtual_tour_link' => 'nullable|string|max:500',
            'property_video_url' => 'nullable|string|max:500',
            'floor_plans' => 'nullable|array',
            'floor_plans.*' => 'image|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $virtualOpenHouseMarketing->update([
            'property_id' => $validated['property_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'platform' => $validated['platform'],
            'event_type' => $validated['event_type'],
            'scheduled_at' => $validated['scheduled_at'],
            'duration' => $validated['duration'],
            'max_attendees' => $validated['max_attendees'] ?? null,
            'registration_required' => $validated['registration_required'] ?? false,
            'registration_deadline' => $validated['registration_deadline'] ?? null,
            'meeting_link' => $validated['meeting_link'] ?? null,
            'meeting_id' => $validated['meeting_id'] ?? null,
            'password' => $validated['password'] ?? null,
            'host_info' => $validated['host_info'] ?? [],
            'promotion_channels' => $validated['promotion_channels'] ?? [],
            'email_template' => $validated['email_template'] ?? null,
            'social_media_posts' => $validated['social_media_posts'] ?? [],
            'reminder_settings' => $validated['reminder_settings'] ?? [],
            'recording_settings' => $validated['recording_settings'] ?? [],
            'follow_up_settings' => $validated['follow_up_settings'] ?? [],
            'virtual_tour_link' => $validated['virtual_tour_link'] ?? null,
            'property_video_url' => $validated['property_video_url'] ?? null,
        ]);

        // Handle custom banner upload
        if ($request->hasFile('custom_banner')) {
            // Delete old banner
            if ($virtualOpenHouseMarketing->custom_banner) {
                Storage::disk('public')->delete($virtualOpenHouseMarketing->custom_banner);
            }
            $path = $request->file('custom_banner')->store('virtual-open-house-banners', 'public');
            $virtualOpenHouseMarketing->update(['custom_banner' => $path]);
        }

        // Handle featured images upload
        if ($request->hasFile('featured_images')) {
            // Delete old images
            if ($virtualOpenHouseMarketing->featured_images) {
                $oldImages = json_decode($virtualOpenHouseMarketing->featured_images, true);
                foreach ($oldImages as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $imagePaths = [];
            foreach ($request->file('featured_images') as $image) {
                $path = $image->store('virtual-open-house-images', 'public');
                $imagePaths[] = $path;
            }
            $virtualOpenHouseMarketing->update(['featured_images' => json_encode($imagePaths)]);
        }

        // Handle floor plans upload
        if ($request->hasFile('floor_plans')) {
            // Delete old plans
            if ($virtualOpenHouseMarketing->floor_plans) {
                $oldPlans = json_decode($virtualOpenHouseMarketing->floor_plans, true);
                foreach ($oldPlans as $oldPlan) {
                    Storage::disk('public')->delete($oldPlan);
                }
            }

            $planPaths = [];
            foreach ($request->file('floor_plans') as $plan) {
                $path = $plan->store('virtual-open-house-plans', 'public');
                $planPaths[] = $path;
            }
            $virtualOpenHouseMarketing->update(['floor_plans' => json_encode($planPaths)]);
        }

        $this->marketingService->clearCache();

        return redirect()->route('marketing.virtual-open-house.index')
            ->with('success', 'تم تحديث حملة البيت المفتوح الافتراضي بنجاح');
    }

    /**
     * Remove the specified virtual open house marketing campaign.
     */
    public function destroy(VirtualOpenHouseMarketing $virtualOpenHouseMarketing)
    {
        // Delete associated files
        if ($virtualOpenHouseMarketing->custom_banner) {
            Storage::disk('public')->delete($virtualOpenHouseMarketing->custom_banner);
        }
        if ($virtualOpenHouseMarketing->featured_images) {
            $images = json_decode($virtualOpenHouseMarketing->featured_images, true);
            foreach ($images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        if ($virtualOpenHouseMarketing->floor_plans) {
            $plans = json_decode($virtualOpenHouseMarketing->floor_plans, true);
            foreach ($plans as $plan) {
                Storage::disk('public')->delete($plan);
            }
        }

        $virtualOpenHouseMarketing->delete();

        $this->marketingService->clearCache();

        return redirect()->route('marketing.virtual-open-house.index')
            ->with('success', 'تم حذف حملة البيت المفتوح الافتراضي بنجاح');
    }

    /**
     * Start a virtual open house session.
     */
    public function start(VirtualOpenHouseMarketing $virtualOpenHouseMarketing)
    {
        if ($virtualOpenHouseMarketing->status !== 'scheduled') {
            return back()->with('error', 'لا يمكن بدء هذه الحملة');
        }

        // Mock API call to start meeting
        $this->startMeeting($virtualOpenHouseMarketing);

        $virtualOpenHouseMarketing->update([
            'status' => 'active',
            'started_at' => now(),
        ]);

        return back()->with('success', 'تم بدء الجلسة بنجاح');
    }

    /**
     * End a virtual open house session.
     */
    public function end(VirtualOpenHouseMarketing $virtualOpenHouseMarketing)
    {
        if ($virtualOpenHouseMarketing->status !== 'active') {
            return back()->with('error', 'لا يمكن إنهاء هذه الحملة');
        }

        // Mock API call to end meeting
        $this->endMeeting($virtualOpenHouseMarketing);

        $virtualOpenHouseMarketing->update([
            'status' => 'completed',
            'ended_at' => now(),
        ]);

        return back()->with('success', 'تم إنهاء الجلسة بنجاح');
    }

    /**
     * Get analytics for a virtual open house campaign.
     */
    public function analytics(VirtualOpenHouseMarketing $virtualOpenHouseMarketing)
    {
        $analytics = $this->getCampaignAnalytics($virtualOpenHouseMarketing);

        return Inertia::render('Marketing/VirtualOpenHouse/Analytics', [
            'campaign' => $virtualOpenHouseMarketing,
            'analytics' => $analytics,
        ]);
    }

    /**
     * Duplicate a virtual open house campaign.
     */
    public function duplicate(VirtualOpenHouseMarketing $virtualOpenHouseMarketing)
    {
        $newCampaign = $virtualOpenHouseMarketing->replicate();
        $newCampaign->title = $virtualOpenHouseMarketing->title . ' (نسخة)';
        $newCampaign->status = 'scheduled';
        $newCampaign->scheduled_at = null;
        $newCampaign->started_at = null;
        $newCampaign->ended_at = null;
        $newCampaign->total_attendees = 0;
        $newCampaign->total_views = 0;
        $newCampaign->save();

        return redirect()->route('marketing.virtual-open-house.edit', $newCampaign)
            ->with('success', 'تم نسخ الحملة بنجاح');
    }

    /**
     * Export virtual open house campaigns data.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $campaigns = VirtualOpenHouseMarketing::with(['property'])->get();

        if ($format === 'csv') {
            $filename = 'virtual-open-house-campaigns-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($campaigns) {
                $file = fopen('php://output', 'w');
                
                // CSV Header
                fputcsv($file, [
                    'ID', 'العنوان', 'العقار', 'المنصة', 'نوع الحدث', 'الحالة', 
                    'التاريخ المجدول', 'المدة', 'عدد الحاضرين', 'عدد المشاهدات', 'تاريخ الإنشاء'
                ]);

                // CSV Data
                foreach ($campaigns as $campaign) {
                    fputcsv($file, [
                        $campaign->id,
                        $campaign->title,
                        $campaign->property?->title ?? 'N/A',
                        $campaign->platform,
                        $campaign->event_type,
                        $campaign->status,
                        $campaign->scheduled_at->format('Y-m-d H:i:s'),
                        $campaign->duration,
                        $campaign->total_attendees,
                        $campaign->total_views,
                        $campaign->created_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return back()->with('error', 'تنسيق التصدير غير مدعوم');
    }

    /**
     * Get campaign analytics data.
     */
    private function getCampaignAnalytics(VirtualOpenHouseMarketing $campaign)
    {
        // Mock analytics data
        return [
            'registration_stats' => [
                'total_registrations' => rand(10, 100),
                'confirmed_attendees' => rand(5, 80),
                'attendance_rate' => rand(60, 95) . '%',
                'registration_trend' => [
                    'last_7_days' => rand(5, 30),
                    'last_30_days' => rand(20, 100),
                ],
            ],
            'engagement_metrics' => [
                'average_attendance_time' => rand(15, 45) . ' minutes',
                'peak_attendance_time' => rand(5, 20) . ' minutes after start',
                'questions_asked' => rand(5, 25),
                'chat_messages' => rand(10, 50),
                'poll_participation' => rand(30, 80) . '%',
            ],
            'conversion_metrics' => [
                'leads_generated' => rand(3, 20),
                'property_inquiries' => rand(2, 15),
                'scheduled_tours' => rand(1, 10),
                'conversion_rate' => rand(5, 25) . '%',
            ],
            'technical_metrics' => [
                'average_connection_quality' => rand(3, 5) . '/5',
                'technical_issues' => rand(0, 5),
                'device_breakdown' => [
                    'desktop' => rand(40, 70),
                    'mobile' => rand(20, 40),
                    'tablet' => rand(10, 20),
                ],
            ],
            'platform_performance' => [
                'zoom' => $this->getPlatformStats('zoom'),
                'teams' => $this->getPlatformStats('teams'),
                'google_meet' => $this->getPlatformStats('google_meet'),
            ],
        ];
    }

    /**
     * Get platform-specific statistics.
     */
    private function getPlatformStats($platform)
    {
        $campaigns = VirtualOpenHouseMarketing::where('platform', $platform);

        return [
            'total_campaigns' => $campaigns->count(),
            'avg_attendees' => $campaigns->avg('total_attendees') ?? 0,
            'avg_duration' => $campaigns->avg('duration') ?? 0,
            'success_rate' => rand(80, 95) . '%',
        ];
    }

    /**
     * Mock method to start a meeting.
     */
    private function startMeeting(VirtualOpenHouseMarketing $campaign)
    {
        // In a real implementation, this would make API calls to the meeting platform
        // For now, we'll just simulate the start
        
        // Simulate API delay
        usleep(100000); // 0.1 second delay

        return true;
    }

    /**
     * Mock method to end a meeting.
     */
    private function endMeeting(VirtualOpenHouseMarketing $campaign)
    {
        // In a real implementation, this would make API calls to the meeting platform
        // For now, we'll just simulate the end
        
        // Simulate API delay
        usleep(100000); // 0.1 second delay

        // Update mock metrics
        $campaign->update([
            'total_attendees' => rand(10, 100),
            'total_views' => rand(50, 500),
        ]);

        return true;
    }
}
