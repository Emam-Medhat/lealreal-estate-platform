<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\InfluencerCampaign;
use App\Models\Property\Property;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PropertyInfluencerMarketingController extends Controller
{
    /**
     * Display a listing of influencer campaigns.
     */
    public function index()
    {
        $campaigns = InfluencerCampaign::with(['property'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Marketing/Influencer/Index', [
            'campaigns' => $campaigns,
            'stats' => [
                'total_campaigns' => InfluencerCampaign::count(),
                'active_campaigns' => InfluencerCampaign::where('status', 'active')->count(),
                'completed_campaigns' => InfluencerCampaign::where('status', 'completed')->count(),
                'pending_campaigns' => InfluencerCampaign::where('status', 'pending')->count(),
                'total_influencers' => InfluencerCampaign::sum('total_influencers'),
                'total_budget' => InfluencerCampaign::sum('total_budget'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new influencer campaign.
     */
    public function create()
    {
        $properties = Property::where('status', 'active')->get();
        
        return Inertia::render('Marketing/Influencer/Create', [
            'properties' => $properties,
            'campaign_types' => ['property_promotion', 'neighborhood_showcase', 'lifestyle_content', 'event_coverage', 'testimonial', 'comparison'],
            'platforms' => ['instagram', 'youtube', 'tiktok', 'twitter', 'facebook', 'snapchat', 'linkedin'],
            'influencer_tiers' => ['nano', 'micro', 'macro', 'mega'],
            'content_types' => ['video', 'image', 'story', 'reel', 'carousel', 'live_stream', 'blog_post'],
        ]);
    }

    /**
     * Store a newly created influencer campaign.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'campaign_type' => 'required|string|in:property_promotion,neighborhood_showcase,lifestyle_content,event_coverage,testimonial,comparison',
            'campaign_objectives' => 'required|array',
            'campaign_objectives.*' => 'string|max:255',
            'target_audience' => 'required|array',
            'target_audience.age_range' => 'required|string|max:50',
            'target_audience.genders' => 'required|array',
            'target_audience.genders.*' => 'string|in:male,female,other',
            'target_audience.locations' => 'required|array',
            'target_audience.locations.*' => 'string|max:255',
            'target_audience.interests' => 'required|array',
            'target_audience.interests.*' => 'string|max:100',
            'platforms' => 'required|array',
            'platforms.*' => 'string|in:instagram,youtube,tiktok,twitter,facebook,snapchat,linkedin',
            'content_requirements' => 'required|array',
            'content_requirements.content_types' => 'required|array',
            'content_requirements.content_types.*' => 'string|in:video,image,story,reel,carousel,live_stream,blog_post',
            'content_requirements.content_count' => 'required|array',
            'content_requirements.content_count.*' => 'integer|min:1',
            'content_requirements.content_guidelines' => 'required|string',
            'content_requirements.brand_guidelines' => 'nullable|string',
            'content_requirements.hashtag_requirements' => 'nullable|array',
            'content_requirements.hashtag_requirements.*' => 'string|max:100',
            'content_requirements.mention_requirements' => 'nullable|array',
            'content_requirements.mention_requirements.*' => 'string|max:100',
            'influencer_requirements' => 'required|array',
            'influencer_requirements.min_followers' => 'nullable|integer|min:0',
            'influencer_requirements.max_followers' => 'nullable|integer|min:0',
            'influencer_requirements.min_engagement_rate' => 'nullable|numeric|min:0|max:100',
            'influencer_requirements.tier' => 'nullable|string|in:nano,micro,macro,mega',
            'influencer_requirements.categories' => 'required|array',
            'influencer_requirements.categories.*' => 'string|max:100',
            'influencer_requirements.exclusions' => 'nullable|array',
            'influencer_requirements.exclusions.*' => 'string|max:255',
            'budget_details' => 'required|array',
            'budget_details.total_budget' => 'required|numeric|min:0',
            'budget_details.currency' => 'required|string|max:3',
            'budget_details.payment_model' => 'required|string|in:fixed_rate,cpc,cpa,hybrid',
            'budget_details.platform_breakdown' => 'required|array',
            'budget_details.platform_breakdown.*' => 'numeric|min:0',
            'budget_details.content_fees' => 'nullable|numeric|min:0',
            'budget_details.usage_rights' => 'nullable|numeric|min:0',
            'budget_details.exclusivity_fee' => 'nullable|numeric|min:0',
            'timeline' => 'required|array',
            'timeline.start_date' => 'required|date|after:today',
            'timeline.end_date' => 'required|date|after:start_date',
            'timeline.content_deadline' => 'nullable|date|before:end_date',
            'timeline.review_period' => 'nullable|integer|min:1|max:30',
            'timeline.approval_process' => 'nullable|string',
            'deliverables' => 'required|array',
            'deliverables.content_approval' => 'boolean',
            'deliverables.performance_reports' => 'boolean',
            'deliverables.usage_rights' => 'boolean',
            'deliverables.exclusivity_period' => 'nullable|integer|min:0',
            'deliverables.content_ownership' => 'boolean',
            'legal_requirements' => 'nullable|array',
            'legal_requirements.contract_required' => 'boolean',
            'legal_requirements.terms_and_conditions' => 'nullable|string',
            'legal_requirements.disclosure_requirements' => 'nullable|string',
            'legal_requirements.compliance_guidelines' => 'nullable|string',
            'campaign_assets' => 'nullable|array',
            'campaign_assets.*' => 'file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:10240',
            'measurement_kpis' => 'required|array',
            'measurement_kpis.primary_kpis' => 'required|array',
            'measurement_kpis.primary_kpis.*' => 'string|max:255',
            'measurement_kpis.secondary_kpis' => 'nullable|array',
            'measurement_kpis.secondary_kpis.*' => 'string|max:255',
            'measurement_kpis.target_metrics' => 'nullable|array',
            'measurement_kpis.target_metrics.*' => 'string|max:255',
        ]);

        $campaign = InfluencerCampaign::create([
            'property_id' => $validated['property_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'campaign_type' => $validated['campaign_type'],
            'campaign_objectives' => $validated['campaign_objectives'],
            'target_audience' => $validated['target_audience'],
            'platforms' => $validated['platforms'],
            'content_requirements' => $validated['content_requirements'],
            'influencer_requirements' => $validated['influencer_requirements'],
            'budget_details' => $validated['budget_details'],
            'timeline' => $validated['timeline'],
            'deliverables' => $validated['deliverables'],
            'legal_requirements' => $validated['legal_requirements'] ?? [],
            'measurement_kpis' => $validated['measurement_kpis'],
            'status' => 'pending',
        ]);

        // Handle campaign assets upload
        if ($request->hasFile('campaign_assets')) {
            $assetPaths = [];
            foreach ($request->file('campaign_assets') as $file) {
                $path = $file->store('influencer-campaign-assets', 'public');
                $assetPaths[] = $path;
            }
            $campaign->update(['campaign_assets' => json_encode($assetPaths)]);
        }

        return redirect()->route('marketing.influencer.index')
            ->with('success', 'تم إنشاء حملة المؤثرين بنجاح');
    }

    /**
     * Display the specified influencer campaign.
     */
    public function show(InfluencerCampaign $influencerCampaign)
    {
        $influencerCampaign->load(['property', 'influencers', 'content']);

        return Inertia::render('Marketing/Influencer/Show', [
            'campaign' => $influencerCampaign,
            'analytics' => $this->getCampaignAnalytics($influencerCampaign),
        ]);
    }

    /**
     * Show the form for editing the specified influencer campaign.
     */
    public function edit(InfluencerCampaign $influencerCampaign)
    {
        $properties = Property::where('status', 'active')->get();
        
        return Inertia::render('Marketing/Influencer/Edit', [
            'campaign' => $influencerCampaign,
            'properties' => $properties,
            'campaign_types' => ['property_promotion', 'neighborhood_showcase', 'lifestyle_content', 'event_coverage', 'testimonial', 'comparison'],
            'platforms' => ['instagram', 'youtube', 'tiktok', 'twitter', 'facebook', 'snapchat', 'linkedin'],
            'influencer_tiers' => ['nano', 'micro', 'macro', 'mega'],
            'content_types' => ['video', 'image', 'story', 'reel', 'carousel', 'live_stream', 'blog_post'],
        ]);
    }

    /**
     * Update the specified influencer campaign.
     */
    public function update(Request $request, InfluencerCampaign $influencerCampaign)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'campaign_type' => 'required|string|in:property_promotion,neighborhood_showcase,lifestyle_content,event_coverage,testimonial,comparison',
            'campaign_objectives' => 'required|array',
            'campaign_objectives.*' => 'string|max:255',
            'target_audience' => 'required|array',
            'target_audience.age_range' => 'required|string|max:50',
            'target_audience.genders' => 'required|array',
            'target_audience.genders.*' => 'string|in:male,female,other',
            'target_audience.locations' => 'required|array',
            'target_audience.locations.*' => 'string|max:255',
            'target_audience.interests' => 'required|array',
            'target_audience.interests.*' => 'string|max:100',
            'platforms' => 'required|array',
            'platforms.*' => 'string|in:instagram,youtube,tiktok,twitter,facebook,snapchat,linkedin',
            'content_requirements' => 'required|array',
            'content_requirements.content_types' => 'required|array',
            'content_requirements.content_types.*' => 'string|in:video,image,story,reel,carousel,live_stream,blog_post',
            'content_requirements.content_count' => 'required|array',
            'content_requirements.content_count.*' => 'integer|min:1',
            'content_requirements.content_guidelines' => 'required|string',
            'content_requirements.brand_guidelines' => 'nullable|string',
            'content_requirements.hashtag_requirements' => 'nullable|array',
            'content_requirements.hashtag_requirements.*' => 'string|max:100',
            'content_requirements.mention_requirements' => 'nullable|array',
            'content_requirements.mention_requirements.*' => 'string|max:100',
            'influencer_requirements' => 'required|array',
            'influencer_requirements.min_followers' => 'nullable|integer|min:0',
            'influencer_requirements.max_followers' => 'nullable|integer|min:0',
            'influencer_requirements.min_engagement_rate' => 'nullable|numeric|min:0|max:100',
            'influencer_requirements.tier' => 'nullable|string|in:nano,micro,macro,mega',
            'influencer_requirements.categories' => 'required|array',
            'influencer_requirements.categories.*' => 'string|max:100',
            'influencer_requirements.exclusions' => 'nullable|array',
            'influencer_requirements.exclusions.*' => 'string|max:255',
            'budget_details' => 'required|array',
            'budget_details.total_budget' => 'required|numeric|min:0',
            'budget_details.currency' => 'required|string|max:3',
            'budget_details.payment_model' => 'required|string|in:fixed_rate,cpc,cpa,hybrid',
            'budget_details.platform_breakdown' => 'required|array',
            'budget_details.platform_breakdown.*' => 'numeric|min:0',
            'budget_details.content_fees' => 'nullable|numeric|min:0',
            'budget_details.usage_rights' => 'nullable|numeric|min:0',
            'budget_details.exclusivity_fee' => 'nullable|numeric|min:0',
            'timeline' => 'required|array',
            'timeline.start_date' => 'required|date|after:today',
            'timeline.end_date' => 'required|date|after:start_date',
            'timeline.content_deadline' => 'nullable|date|before:end_date',
            'timeline.review_period' => 'nullable|integer|min:1|max:30',
            'timeline.approval_process' => 'nullable|string',
            'deliverables' => 'required|array',
            'deliverables.content_approval' => 'boolean',
            'deliverables.performance_reports' => 'boolean',
            'deliverables.usage_rights' => 'boolean',
            'deliverables.exclusivity_period' => 'nullable|integer|min:0',
            'deliverables.content_ownership' => 'boolean',
            'legal_requirements' => 'nullable|array',
            'legal_requirements.contract_required' => 'boolean',
            'legal_requirements.terms_and_conditions' => 'nullable|string',
            'legal_requirements.disclosure_requirements' => 'nullable|string',
            'legal_requirements.compliance_guidelines' => 'nullable|string',
            'campaign_assets' => 'nullable|array',
            'campaign_assets.*' => 'file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:10240',
            'measurement_kpis' => 'required|array',
            'measurement_kpis.primary_kpis' => 'required|array',
            'measurement_kpis.primary_kpis.*' => 'string|max:255',
            'measurement_kpis.secondary_kpis' => 'nullable|array',
            'measurement_kpis.secondary_kpis.*' => 'string|max:255',
            'measurement_kpis.target_metrics' => 'nullable|array',
            'measurement_kpis.target_metrics.*' => 'string|max:255',
        ]);

        $influencerCampaign->update([
            'property_id' => $validated['property_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'campaign_type' => $validated['campaign_type'],
            'campaign_objectives' => $validated['campaign_objectives'],
            'target_audience' => $validated['target_audience'],
            'platforms' => $validated['platforms'],
            'content_requirements' => $validated['content_requirements'],
            'influencer_requirements' => $validated['influencer_requirements'],
            'budget_details' => $validated['budget_details'],
            'timeline' => $validated['timeline'],
            'deliverables' => $validated['deliverables'],
            'legal_requirements' => $validated['legal_requirements'] ?? [],
            'measurement_kpis' => $validated['measurement_kpis'],
        ]);

        // Handle campaign assets upload
        if ($request->hasFile('campaign_assets')) {
            // Delete old assets
            if ($influencerCampaign->campaign_assets) {
                $oldAssets = json_decode($influencerCampaign->campaign_assets, true);
                foreach ($oldAssets as $oldAsset) {
                    Storage::disk('public')->delete($oldAsset);
                }
            }

            $assetPaths = [];
            foreach ($request->file('campaign_assets') as $file) {
                $path = $file->store('influencer-campaign-assets', 'public');
                $assetPaths[] = $path;
            }
            $influencerCampaign->update(['campaign_assets' => json_encode($assetPaths)]);
        }

        return redirect()->route('marketing.influencer.index')
            ->with('success', 'تم تحديث حملة المؤثرين بنجاح');
    }

    /**
     * Remove the specified influencer campaign.
     */
    public function destroy(InfluencerCampaign $influencerCampaign)
    {
        // Delete associated assets
        if ($influencerCampaign->campaign_assets) {
            $assets = json_decode($influencerCampaign->campaign_assets, true);
            foreach ($assets as $asset) {
                Storage::disk('public')->delete($asset);
            }
        }

        $influencerCampaign->delete();

        return redirect()->route('marketing.influencer.index')
            ->with('success', 'تم حذف حملة المؤثرين بنجاح');
    }

    /**
     * Launch an influencer campaign.
     */
    public function launch(InfluencerCampaign $influencerCampaign)
    {
        if ($influencerCampaign->status !== 'pending') {
            return back()->with('error', 'لا يمكن إطلاق هذه الحملة');
        }

        // Mock API call to launch campaign
        $this->launchCampaign($influencerCampaign);

        $influencerCampaign->update([
            'status' => 'active',
            'launched_at' => now(),
        ]);

        return back()->with('success', 'تم إطلاق الحملة بنجاح');
    }

    /**
     * Pause an influencer campaign.
     */
    public function pause(InfluencerCampaign $influencerCampaign)
    {
        if ($influencerCampaign->status !== 'active') {
            return back()->with('error', 'لا يمكن إيقاف هذه الحملة');
        }

        $influencerCampaign->update([
            'status' => 'paused',
            'paused_at' => now(),
        ]);

        return back()->with('success', 'تم إيقاف الحملة بنجاح');
    }

    /**
     * Resume an influencer campaign.
     */
    public function resume(InfluencerCampaign $influencerCampaign)
    {
        if ($influencerCampaign->status !== 'paused') {
            return back()->with('error', 'لا يمكن استئناف هذه الحملة');
        }

        $influencerCampaign->update([
            'status' => 'active',
            'resumed_at' => now(),
        ]);

        return back()->with('success', 'تم استئناف الحملة بنجاح');
    }

    /**
     * Complete an influencer campaign.
     */
    public function complete(InfluencerCampaign $influencerCampaign)
    {
        if ($influencerCampaign->status !== 'active') {
            return back()->with('error', 'لا يمكن إكمال هذه الحملة');
        }

        $influencerCampaign->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'تم إكمال الحملة بنجاح');
    }

    /**
     * Get analytics for an influencer campaign.
     */
    public function analytics(InfluencerCampaign $influencerCampaign)
    {
        $analytics = $this->getCampaignAnalytics($influencerCampaign);

        return Inertia::render('Marketing/Influencer/Analytics', [
            'campaign' => $influencerCampaign,
            'analytics' => $analytics,
        ]);
    }

    /**
     * Duplicate an influencer campaign.
     */
    public function duplicate(InfluencerCampaign $influencerCampaign)
    {
        $newCampaign = $influencerCampaign->replicate();
        $newCampaign->title = $influencerCampaign->title . ' (نسخة)';
        $newCampaign->status = 'pending';
        $newCampaign->launched_at = null;
        $newCampaign->completed_at = null;
        $newCampaign->total_influencers = 0;
        $newCampaign->save();

        return redirect()->route('marketing.influencer.edit', $newCampaign)
            ->with('success', 'تم نسخ الحملة بنجاح');
    }

    /**
     * Export influencer campaigns data.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $campaigns = InfluencerCampaign::with(['property'])->get();

        if ($format === 'csv') {
            $filename = 'influencer-campaigns-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($campaigns) {
                $file = fopen('php://output', 'w');
                
                // CSV Header
                fputcsv($file, [
                    'ID', 'العنوان', 'العقار', 'نوع الحملة', 'الحالة', 
                    'الميزانية', 'المنصات', 'عدد المؤثرين', 'تاريخ الإطلاق', 'تاريخ الإنشاء'
                ]);

                // CSV Data
                foreach ($campaigns as $campaign) {
                    fputcsv($file, [
                        $campaign->id,
                        $campaign->title,
                        $campaign->property?->title ?? 'N/A',
                        $campaign->campaign_type,
                        $campaign->status,
                        $campaign->budget_details['total_budget'] ?? 0,
                        implode(', ', $campaign->platforms),
                        $campaign->total_influencers,
                        $campaign->launched_at?->format('Y-m-d H:i:s') ?? 'N/A',
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
    private function getCampaignAnalytics(InfluencerCampaign $campaign)
    {
        // Mock analytics data
        return [
            'performance_metrics' => [
                'total_reach' => rand(50000, 500000),
                'total_impressions' => rand(100000, 1000000),
                'total_engagement' => rand(5000, 50000),
                'engagement_rate' => rand(2, 8) . '%',
                'click_through_rate' => rand(1, 5) . '%',
                'conversion_rate' => rand(0.5, 3) . '%',
                'cost_per_engagement' => rand(5, 50),
                'cost_per_click' => rand(10, 100),
                'return_on_investment' => rand(150, 400) . '%',
            ],
            'content_performance' => [
                'total_content_pieces' => rand(20, 200),
                'video_views' => rand(10000, 100000),
                'image_likes' => rand(1000, 10000),
                'story_views' => rand(5000, 50000),
                'carousel_swipes' => rand(500, 5000),
                'live_stream_views' => rand(1000, 10000),
                'blog_reads' => rand(500, 5000),
            ],
            'influencer_performance' => [
                'total_influencers' => $campaign->total_influencers,
                'active_influencers' => rand($campaign->total_influencers * 0.7, $campaign->total_influencers),
                'average_engagement_rate' => rand(3, 9) . '%',
                'top_performing_influencers' => rand(3, 10),
                'influencer_satisfaction_score' => rand(3.5, 4.8) . '/5',
            ],
            'audience_insights' => [
                'demographics' => [
                    'age_groups' => [
                        '18-24' => rand(15, 25),
                        '25-34' => rand(30, 45),
                        '35-44' => rand(20, 35),
                        '45-54' => rand(10, 20),
                        '55+' => rand(5, 10),
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
                'interests' => [
                    'عقارات' => rand(60, 80),
                    'تصميم داخلي' => rand(40, 60),
                    'استثمار' => rand(30, 50),
                    'أسلوب حياة' => rand(25, 45),
                    'سفر' => rand(20, 40),
                ],
            ],
            'platform_breakdown' => [
                'instagram' => [
                    'reach' => rand(20000, 200000),
                    'engagement' => rand(2000, 20000),
                    'content_pieces' => rand(10, 100),
                ],
                'youtube' => [
                    'reach' => rand(15000, 150000),
                    'engagement' => rand(1000, 10000),
                    'content_pieces' => rand(5, 50),
                ],
                'tiktok' => [
                    'reach' => rand(10000, 100000),
                    'engagement' => rand(1500, 15000),
                    'content_pieces' => rand(8, 80),
                ],
                'twitter' => [
                    'reach' => rand(5000, 50000),
                    'engagement' => rand(500, 5000),
                    'content_pieces' => rand(15, 150),
                ],
            ],
            'roi_analysis' => [
                'total_investment' => $campaign->budget_details['total_budget'] ?? 0,
                'estimated_value' => rand($campaign->budget_details['total_budget'] * 1.5, $campaign->budget_details['total_budget'] * 4),
                'roi_percentage' => rand(150, 400) . '%',
                'cost_per_lead' => rand(50, 500),
                'lead_to_customer_rate' => rand(5, 20) . '%',
                'customer_lifetime_value' => rand(10000, 100000),
            ],
        ];
    }

    /**
     * Mock method to launch campaign.
     */
    private function launchCampaign(InfluencerCampaign $campaign)
    {
        // In a real implementation, this would make API calls to influencer platforms
        // and notify selected influencers about the campaign
        
        // Simulate API delay
        usleep(200000); // 0.2 second delay

        // Update mock metrics
        $campaign->update([
            'total_influencers' => rand(5, 50),
        ]);

        return true;
    }
}
