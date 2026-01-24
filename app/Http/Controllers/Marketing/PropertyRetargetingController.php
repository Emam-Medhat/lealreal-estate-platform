<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\RetargetingAudience;
use App\Models\Property\Property;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PropertyRetargetingController extends Controller
{
    /**
     * Display a listing of retargeting audiences.
     */
    public function index()
    {
        $audiences = RetargetingAudience::with(['property'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Marketing/Retargeting/Index', [
            'audiences' => $audiences,
            'stats' => [
                'total_audiences' => RetargetingAudience::count(),
                'active_audiences' => RetargetingAudience::where('status', 'active')->count(),
                'paused_audiences' => RetargetingAudience::where('status', 'paused')->count(),
                'total_audience_size' => RetargetingAudience::sum('audience_size'),
                'total_campaigns' => RetargetingAudience::sum('total_campaigns'),
                'average_conversion_rate' => RetargetingAudience::avg('conversion_rate') ?? 0,
            ]
        ]);
    }

    /**
     * Show the form for creating a new retargeting audience.
     */
    public function create()
    {
        $properties = Property::where('status', 'active')->get();
        
        return Inertia::render('Marketing/Retargeting/Create', [
            'properties' => $properties,
            'audience_types' => ['website_visitors', 'property_viewers', 'cart_abandoners', 'search_users', 'email_subscribers', 'social_engagers'],
            'platforms' => ['google_ads', 'facebook', 'instagram', 'linkedin', 'twitter', 'tiktok', 'pinterest'],
            'retargeting_types' => ['pixel_based', 'list_based', 'dynamic', 'hybrid'],
            'timeframes' => ['1_day', '7_days', '14_days', '30_days', '60_days', '90_days', '180_days'],
        ]);
    }

    /**
     * Store a newly created retargeting audience.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'audience_type' => 'required|string|in:website_visitors,property_viewers,cart_abandoners,search_users,email_subscribers,social_engagers',
            'platform' => 'required|string|in:google_ads,facebook,instagram,linkedin,twitter,tiktok,pinterest',
            'retargeting_type' => 'required|string|in:pixel_based,list_based,dynamic,hybrid',
            'targeting_criteria' => 'required|array',
            'targeting_criteria.visited_pages' => 'nullable|array',
            'targeting_criteria.visited_pages.*' => 'string|max:500',
            'targeting_criteria.time_on_page' => 'nullable|integer|min:0',
            'targeting_criteria.page_views' => 'nullable|integer|min:1',
            'targeting_criteria.scroll_depth' => 'nullable|integer|min:0|max:100',
            'targeting_criteria.property_interactions' => 'nullable|array',
            'targeting_criteria.property_interactions.*' => 'string|in:viewed,favorited,shared,inquired',
            'targeting_criteria.search_criteria' => 'nullable|array',
            'targeting_criteria.search_criteria.*' => 'string|max:255',
            'targeting_criteria.behavioral_signals' => 'nullable|array',
            'targeting_criteria.behavioral_signals.*' => 'string|max:255',
            'audience_rules' => 'required|array',
            'audience_rules.inclusion_rules' => 'nullable|array',
            'audience_rules.inclusion_rules.*.field' => 'required|string|max:255',
            'audience_rules.inclusion_rules.*.operator' => 'required|string|in:equals,not_equals,contains,not_contains,greater_than,less_than,between',
            'audience_rules.inclusion_rules.*.value' => 'required|string',
            'audience_rules.exclusion_rules' => 'nullable|array',
            'audience_rules.exclusion_rules.*.field' => 'required|string|max:255',
            'audience_rules.exclusion_rules.*.operator' => 'required|string|in:equals,not_equals,contains,not_contains,greater_than,less_than,between',
            'audience_rules.exclusion_rules.*.value' => 'required|string',
            'time_settings' => 'required|array',
            'time_settings.retargeting_window' => 'required|string|in:1_day,7_days,14_days,30_days,60_days,90_days,180_days',
            'time_settings.frequency_capping' => 'nullable|integer|min:1|max:50',
            'time_settings.ad_schedule' => 'nullable|array',
            'time_settings.ad_schedule.*.day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'time_settings.ad_schedule.*.start_time' => 'required|string|max:5',
            'time_settings.ad_schedule.*.end_time' => 'required|string|max:5',
            'budget_settings' => 'required|array',
            'budget_settings.daily_budget' => 'required|numeric|min:0',
            'budget_settings.total_budget' => 'nullable|numeric|min:0',
            'budget_settings.currency' => 'required|string|max:3',
            'budget_settings.bid_strategy' => 'required|string|in:manual,cpc,cpm,cpa,roas',
            'budget_settings.max_cpc' => 'nullable|numeric|min:0',
            'budget_settings.max_cpm' => 'nullable|numeric|min:0',
            'budget_settings.target_cpa' => 'nullable|numeric|min:0',
            'budget_settings.target_roas' => 'nullable|numeric|min:0',
            'creative_settings' => 'nullable|array',
            'creative_settings.ad_formats' => 'required|array',
            'creative_settings.ad_formats.*' => 'string|in:image,video,carousel,story,reel,collection',
            'creative_settings.headlines' => 'nullable|array',
            'creative_settings.headlines.*' => 'string|max:255',
            'creative_settings.descriptions' => 'nullable|array',
            'creative_settings.descriptions.*' => 'string|max:500',
            'creative_settings.call_to_action' => 'nullable|array',
            'creative_settings.call_to_action.*' => 'string|max:50',
            'creative_settings.creative_assets' => 'nullable|array',
            'creative_settings.creative_assets.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:10240',
            'pixel_tracking' => 'nullable|array',
            'pixel_tracking.pixel_id' => 'nullable|string|max:255',
            'pixel_tracking.conversion_events' => 'nullable|array',
            'pixel_tracking.conversion_events.*' => 'string|max:255',
            'pixel_tracking.custom_events' => 'nullable|array',
            'pixel_tracking.custom_events.*.name' => 'required|string|max:255',
            'pixel_tracking.custom_events.*.value' => 'nullable|numeric',
            'audience_segments' => 'nullable|array',
            'audience_segments.*.name' => 'required|string|max:255',
            'audience_segments.*.criteria' => 'required|array',
            'audience_segments.*.criteria.*.field' => 'required|string|max:255',
            'audience_segments.*.criteria.*.operator' => 'required|string|in:equals,not_equals,contains,not_contains,greater_than,less_than,between',
            'audience_segments.*.criteria.*.value' => 'required|string',
            'performance_goals' => 'nullable|array',
            'performance_goals.click_through_rate' => 'nullable|numeric|min:0|max:100',
            'performance_goals.conversion_rate' => 'nullable|numeric|min:0|max:100',
            'performance_goals.cost_per_conversion' => 'nullable|numeric|min:0',
            'performance_goals.return_on_ad_spend' => 'nullable|numeric|min:0',
            'performance_goals.impression_share' => 'nullable|numeric|min:0|max:100',
            'integration_settings' => 'nullable|array',
            'integration_settings.crm_integration' => 'boolean',
            'integration_settings.email_integration' => 'boolean',
            'integration_settings.analytics_integration' => 'boolean',
            'integration_settings.custom_api_endpoints' => 'nullable|array',
            'integration_settings.custom_api_endpoints.*' => 'string|max:500',
        ]);

        $audience = RetargetingAudience::create([
            'property_id' => $validated['property_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'audience_type' => $validated['audience_type'],
            'platform' => $validated['platform'],
            'retargeting_type' => $validated['retargeting_type'],
            'targeting_criteria' => $validated['targeting_criteria'],
            'audience_rules' => $validated['audience_rules'],
            'time_settings' => $validated['time_settings'],
            'budget_settings' => $validated['budget_settings'],
            'creative_settings' => $validated['creative_settings'] ?? [],
            'pixel_tracking' => $validated['pixel_tracking'] ?? [],
            'audience_segments' => $validated['audience_segments'] ?? [],
            'performance_goals' => $validated['performance_goals'] ?? [],
            'integration_settings' => $validated['integration_settings'] ?? [],
            'status' => 'draft',
        ]);

        // Handle creative assets upload
        if ($request->hasFile('creative_settings.creative_assets')) {
            $assetPaths = [];
            foreach ($request->file('creative_settings.creative_assets') as $file) {
                $path = $file->store('retargeting-creative-assets', 'public');
                $assetPaths[] = $path;
            }
            $audience->update(['creative_settings' => array_merge($audience->creative_settings, ['creative_assets' => $assetPaths])]);
        }

        return redirect()->route('marketing.retargeting.index')
            ->with('success', 'تم إنشاء جمهور إعادة الاستهداف بنجاح');
    }

    /**
     * Display the specified retargeting audience.
     */
    public function show(RetargetingAudience $retargetingAudience)
    {
        $retargetingAudience->load(['property', 'campaigns', 'segments']);

        return Inertia::render('Marketing/Retargeting/Show', [
            'audience' => $retargetingAudience,
            'analytics' => $this->getAudienceAnalytics($retargetingAudience),
        ]);
    }

    /**
     * Show the form for editing the specified retargeting audience.
     */
    public function edit(RetargetingAudience $retargetingAudience)
    {
        $properties = Property::where('status', 'active')->get();
        
        return Inertia::render('Marketing/Retargeting/Edit', [
            'audience' => $retargetingAudience,
            'properties' => $properties,
            'audience_types' => ['website_visitors', 'property_viewers', 'cart_abandoners', 'search_users', 'email_subscribers', 'social_engagers'],
            'platforms' => ['google_ads', 'facebook', 'instagram', 'linkedin', 'twitter', 'tiktok', 'pinterest'],
            'retargeting_types' => ['pixel_based', 'list_based', 'dynamic', 'hybrid'],
            'timeframes' => ['1_day', '7_days', '14_days', '30_days', '60_days', '90_days', '180_days'],
        ]);
    }

    /**
     * Update the specified retargeting audience.
     */
    public function update(Request $request, RetargetingAudience $retargetingAudience)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'audience_type' => 'required|string|in:website_visitors,property_viewers,cart_abandoners,search_users,email_subscribers,social_engagers',
            'platform' => 'required|string|in:google_ads,facebook,instagram,linkedin,twitter,tiktok,pinterest',
            'retargeting_type' => 'required|string|in:pixel_based,list_based,dynamic,hybrid',
            'targeting_criteria' => 'required|array',
            'targeting_criteria.visited_pages' => 'nullable|array',
            'targeting_criteria.visited_pages.*' => 'string|max:500',
            'targeting_criteria.time_on_page' => 'nullable|integer|min:0',
            'targeting_criteria.page_views' => 'nullable|integer|min:1',
            'targeting_criteria.scroll_depth' => 'nullable|integer|min:0|max:100',
            'targeting_criteria.property_interactions' => 'nullable|array',
            'targeting_criteria.property_interactions.*' => 'string|in:viewed,favorited,shared,inquired',
            'targeting_criteria.search_criteria' => 'nullable|array',
            'targeting_criteria.search_criteria.*' => 'string|max:255',
            'targeting_criteria.behavioral_signals' => 'nullable|array',
            'targeting_criteria.behavioral_signals.*' => 'string|max:255',
            'audience_rules' => 'required|array',
            'audience_rules.inclusion_rules' => 'nullable|array',
            'audience_rules.inclusion_rules.*.field' => 'required|string|max:255',
            'audience_rules.inclusion_rules.*.operator' => 'required|string|in:equals,not_equals,contains,not_contains,greater_than,less_than,between',
            'audience_rules.inclusion_rules.*.value' => 'required|string',
            'audience_rules.exclusion_rules' => 'nullable|array',
            'audience_rules.exclusion_rules.*.field' => 'required|string|max:255',
            'audience_rules.exclusion_rules.*.operator' => 'required|string|in:equals,not_equals,contains,not_contains,greater_than,less_than,between',
            'audience_rules.exclusion_rules.*.value' => 'required|string',
            'time_settings' => 'required|array',
            'time_settings.retargeting_window' => 'required|string|in:1_day,7_days,14_days,30_days,60_days,90_days,180_days',
            'time_settings.frequency_capping' => 'nullable|integer|min:1|max:50',
            'time_settings.ad_schedule' => 'nullable|array',
            'time_settings.ad_schedule.*.day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'time_settings.ad_schedule.*.start_time' => 'required|string|max:5',
            'time_settings.ad_schedule.*.end_time' => 'required|string|max:5',
            'budget_settings' => 'required|array',
            'budget_settings.daily_budget' => 'required|numeric|min:0',
            'budget_settings.total_budget' => 'nullable|numeric|min:0',
            'budget_settings.currency' => 'required|string|max:3',
            'budget_settings.bid_strategy' => 'required|string|in:manual,cpc,cpm,cpa,roas',
            'budget_settings.max_cpc' => 'nullable|numeric|min:0',
            'budget_settings.max_cpm' => 'nullable|numeric|min:0',
            'budget_settings.target_cpa' => 'nullable|numeric|min:0',
            'budget_settings.target_roas' => 'nullable|numeric|min:0',
            'creative_settings' => 'nullable|array',
            'creative_settings.ad_formats' => 'required|array',
            'creative_settings.ad_formats.*' => 'string|in:image,video,carousel,story,reel,collection',
            'creative_settings.headlines' => 'nullable|array',
            'creative_settings.headlines.*' => 'string|max:255',
            'creative_settings.descriptions' => 'nullable|array',
            'creative_settings.descriptions.*' => 'string|max:500',
            'creative_settings.call_to_action' => 'nullable|array',
            'creative_settings.call_to_action.*' => 'string|max:50',
            'creative_settings.creative_assets' => 'nullable|array',
            'creative_settings.creative_assets.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:10240',
            'pixel_tracking' => 'nullable|array',
            'pixel_tracking.pixel_id' => 'nullable|string|max:255',
            'pixel_tracking.conversion_events' => 'nullable|array',
            'pixel_tracking.conversion_events.*' => 'string|max:255',
            'pixel_tracking.custom_events' => 'nullable|array',
            'pixel_tracking.custom_events.*.name' => 'required|string|max:255',
            'pixel_tracking.custom_events.*.value' => 'nullable|numeric',
            'audience_segments' => 'nullable|array',
            'audience_segments.*.name' => 'required|string|max:255',
            'audience_segments.*.criteria' => 'required|array',
            'audience_segments.*.criteria.*.field' => 'required|string|max:255',
            'audience_segments.*.criteria.*.operator' => 'required|string|in:equals,not_equals,contains,not_contains,greater_than,less_than,between',
            'audience_segments.*.criteria.*.value' => 'required|string',
            'performance_goals' => 'nullable|array',
            'performance_goals.click_through_rate' => 'nullable|numeric|min:0|max:100',
            'performance_goals.conversion_rate' => 'nullable|numeric|min:0|max:100',
            'performance_goals.cost_per_conversion' => 'nullable|numeric|min:0',
            'performance_goals.return_on_ad_spend' => 'nullable|numeric|min:0',
            'performance_goals.impression_share' => 'nullable|numeric|min:0|max:100',
            'integration_settings' => 'nullable|array',
            'integration_settings.crm_integration' => 'boolean',
            'integration_settings.email_integration' => 'boolean',
            'integration_settings.analytics_integration' => 'boolean',
            'integration_settings.custom_api_endpoints' => 'nullable|array',
            'integration_settings.custom_api_endpoints.*' => 'string|max:500',
        ]);

        $retargetingAudience->update([
            'property_id' => $validated['property_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'audience_type' => $validated['audience_type'],
            'platform' => $validated['platform'],
            'retargeting_type' => $validated['retargeting_type'],
            'targeting_criteria' => $validated['targeting_criteria'],
            'audience_rules' => $validated['audience_rules'],
            'time_settings' => $validated['time_settings'],
            'budget_settings' => $validated['budget_settings'],
            'creative_settings' => $validated['creative_settings'] ?? [],
            'pixel_tracking' => $validated['pixel_tracking'] ?? [],
            'audience_segments' => $validated['audience_segments'] ?? [],
            'performance_goals' => $validated['performance_goals'] ?? [],
            'integration_settings' => $validated['integration_settings'] ?? [],
        ]);

        // Handle creative assets upload
        if ($request->hasFile('creative_settings.creative_assets')) {
            // Delete old assets
            if (isset($retargetingAudience->creative_settings['creative_assets'])) {
                $oldAssets = $retargetingAudience->creative_settings['creative_assets'];
                foreach ($oldAssets as $oldAsset) {
                    Storage::disk('public')->delete($oldAsset);
                }
            }

            $assetPaths = [];
            foreach ($request->file('creative_settings.creative_assets') as $file) {
                $path = $file->store('retargeting-creative-assets', 'public');
                $assetPaths[] = $path;
            }
            $retargetingAudience->update(['creative_settings' => array_merge($retargetingAudience->creative_settings, ['creative_assets' => $assetPaths])]);
        }

        return redirect()->route('marketing.retargeting.index')
            ->with('success', 'تم تحديث جمهور إعادة الاستهداف بنجاح');
    }

    /**
     * Remove the specified retargeting audience.
     */
    public function destroy(RetargetingAudience $retargetingAudience)
    {
        // Delete associated creative assets
        if (isset($retargetingAudience->creative_settings['creative_assets'])) {
            $assets = $retargetingAudience->creative_settings['creative_assets'];
            foreach ($assets as $asset) {
                Storage::disk('public')->delete($asset);
            }
        }

        $retargetingAudience->delete();

        return redirect()->route('marketing.retargeting.index')
            ->with('success', 'تم حذف جمهور إعادة الاستهداف بنجاح');
    }

    /**
     * Activate a retargeting audience.
     */
    public function activate(RetargetingAudience $retargetingAudience)
    {
        if ($retargetingAudience->status !== 'draft') {
            return back()->with('error', 'لا يمكن تفعيل هذا الجمهور');
        }

        // Mock API call to activate audience
        $this->activateAudience($retargetingAudience);

        $retargetingAudience->update([
            'status' => 'active',
            'activated_at' => now(),
        ]);

        return back()->with('success', 'تم تفعيل الجمهور بنجاح');
    }

    /**
     * Pause a retargeting audience.
     */
    public function pause(RetargetingAudience $retargetingAudience)
    {
        if ($retargetingAudience->status !== 'active') {
            return back()->with('error', 'لا يمكن إيقاف هذا الجمهور');
        }

        $retargetingAudience->update([
            'status' => 'paused',
            'paused_at' => now(),
        ]);

        return back()->with('success', 'تم إيقاف الجمهور بنجاح');
    }

    /**
     * Resume a retargeting audience.
     */
    public function resume(RetargetingAudience $retargetingAudience)
    {
        if ($retargetingAudience->status !== 'paused') {
            return back()->with('error', 'لا يمكن استئناف هذا الجمهور');
        }

        $retargetingAudience->update([
            'status' => 'active',
            'resumed_at' => now(),
        ]);

        return back()->with('success', 'تم استئناف الجمهور بنجاح');
    }

    /**
     * Get analytics for a retargeting audience.
     */
    public function analytics(RetargetingAudience $retargetingAudience)
    {
        $analytics = $this->getAudienceAnalytics($retargetingAudience);

        return Inertia::render('Marketing/Retargeting/Analytics', [
            'audience' => $retargetingAudience,
            'analytics' => $analytics,
        ]);
    }

    /**
     * Duplicate a retargeting audience.
     */
    public function duplicate(RetargetingAudience $retargetingAudience)
    {
        $newAudience = $retargetingAudience->replicate();
        $newAudience->name = $retargetingAudience->name . ' (نسخة)';
        $newAudience->status = 'draft';
        $newAudience->activated_at = null;
        $newAudience->paused_at = null;
        $newAudience->resumed_at = null;
        $newAudience->audience_size = 0;
        $newAudience->total_campaigns = 0;
        $newAudience->conversion_rate = 0;
        $newAudience->save();

        return redirect()->route('marketing.retargeting.edit', $newAudience)
            ->with('success', 'تم نسخ الجمهور بنجاح');
    }

    /**
     * Export retargeting audiences data.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $audiences = RetargetingAudience::with(['property'])->get();

        if ($format === 'csv') {
            $filename = 'retargeting-audiences-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($audiences) {
                $file = fopen('php://output', 'w');
                
                // CSV Header
                fputcsv($file, [
                    'ID', 'الاسم', 'العقار', 'نوع الجمهور', 'المنصة', 'الحالة', 
                    'حجم الجمهور', 'معدل التحويل', 'الحملات', 'تاريخ التفعيل', 'تاريخ الإنشاء'
                ]);

                // CSV Data
                foreach ($audiences as $audience) {
                    fputcsv($file, [
                        $audience->id,
                        $audience->name,
                        $audience->property?->title ?? 'N/A',
                        $audience->audience_type,
                        $audience->platform,
                        $audience->status,
                        $audience->audience_size,
                        $audience->conversion_rate . '%',
                        $audience->total_campaigns,
                        $audience->activated_at?->format('Y-m-d H:i:s') ?? 'N/A',
                        $audience->created_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return back()->with('error', 'تنسيق التصدير غير مدعوم');
    }

    /**
     * Get audience analytics data.
     */
    private function getAudienceAnalytics(RetargetingAudience $audience)
    {
        // Mock analytics data
        return [
            'performance_metrics' => [
                'impressions' => rand(10000, 1000000),
                'clicks' => rand(100, 10000),
                'click_through_rate' => rand(1, 10) . '%',
                'conversions' => rand(5, 500),
                'conversion_rate' => rand(1, 8) . '%',
                'cost_per_click' => rand(1, 20),
                'cost_per_conversion' => rand(50, 500),
                'return_on_ad_spend' => rand(200, 800) . '%',
                'total_spend' => rand(500, 50000),
            ],
            'audience_insights' => [
                'audience_size' => $audience->audience_size,
                'new_users' => rand($audience->audience_size * 0.1, $audience->audience_size * 0.3),
                'returning_users' => rand($audience->audience_size * 0.7, $audience->audience_size * 0.9),
                'demographics' => [
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
                        'الرياض' => rand(20, 35),
                        'جدة' => rand(15, 25),
                        'الدمام' => rand(10, 20),
                        'مكة' => rand(8, 15),
                        'أخرى' => rand(20, 35),
                    ],
                ],
            ],
            'conversion_funnel' => [
                'awareness' => [
                    'users' => rand(1000, 10000),
                    'rate' => '100%',
                ],
                'interest' => [
                    'users' => rand(500, 5000),
                    'rate' => rand(40, 60) . '%',
                ],
                'consideration' => [
                    'users' => rand(200, 2000),
                    'rate' => rand(30, 50) . '%',
                ],
                'conversion' => [
                    'users' => rand(50, 500),
                    'rate' => rand(5, 25) . '%',
                ],
            ],
            'platform_performance' => [
                'google_ads' => [
                    'impressions' => rand(5000, 500000),
                    'clicks' => rand(50, 5000),
                    'conversions' => rand(2, 200),
                    'cost' => rand(200, 20000),
                ],
                'facebook' => [
                    'impressions' => rand(3000, 300000),
                    'clicks' => rand(30, 3000),
                    'conversions' => rand(1, 150),
                    'cost' => rand(150, 15000),
                ],
                'instagram' => [
                    'impressions' => rand(2000, 200000),
                    'clicks' => rand(20, 2000),
                    'conversions' => rand(1, 100),
                    'cost' => rand(100, 10000),
                ],
            ],
            'time_performance' => [
                'hourly_breakdown' => [
                    '12am-6am' => rand(5, 15),
                    '6am-12pm' => rand(20, 30),
                    '12pm-6pm' => rand(35, 45),
                    '6pm-12am' => rand(25, 35),
                ],
                'daily_trend' => [
                    'monday' => rand(10, 20),
                    'tuesday' => rand(12, 22),
                    'wednesday' => rand(14, 24),
                    'thursday' => rand(16, 26),
                    'friday' => rand(18, 28),
                    'saturday' => rand(15, 25),
                    'sunday' => rand(12, 22),
                ],
            ],
            'device_performance' => [
                'desktop' => [
                    'impressions' => rand(4000, 400000),
                    'clicks' => rand(40, 4000),
                    'conversions' => rand(3, 300),
                    'conversion_rate' => rand(2, 8) . '%',
                ],
                'mobile' => [
                    'impressions' => rand(5000, 500000),
                    'clicks' => rand(50, 5000),
                    'conversions' => rand(2, 200),
                    'conversion_rate' => rand(1, 6) . '%',
                ],
                'tablet' => [
                    'impressions' => rand(1000, 100000),
                    'clicks' => rand(10, 1000),
                    'conversions' => rand(0, 50),
                    'conversion_rate' => rand(1, 5) . '%',
                ],
            ],
        ];
    }

    /**
     * Mock method to activate audience.
     */
    private function activateAudience(RetargetingAudience $audience)
    {
        // In a real implementation, this would make API calls to advertising platforms
        // to create and activate the retargeting audience
        
        // Simulate API delay
        usleep(150000); // 0.15 second delay

        // Update mock metrics
        $audience->update([
            'audience_size' => rand(1000, 50000),
            'total_campaigns' => rand(1, 10),
            'conversion_rate' => rand(1, 8),
        ]);

        return true;
    }
}
