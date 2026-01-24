<?php

namespace App\Http\Controllers;

use App\Models\AdCampaign;
use App\Models\Advertisement;
use App\Models\AdBudget;
use App\Models\AdTargeting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdCampaignController extends Controller
{
    public function index()
    {
        $campaigns = AdCampaign::with(['ads', 'budget'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('ads.campaigns', compact('campaigns'));
    }

    public function create()
    {
        return view('ads.create-campaign');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'objective' => 'required|in:awareness,traffic,conversions,engagement',
            'total_budget' => 'required|numeric|min:10',
            'daily_budget' => 'required|numeric|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'target_audience' => 'nullable|array',
            'target_locations' => 'nullable|array',
            'target_age_range' => 'nullable|array',
            'target_interests' => 'nullable|array'
        ]);

        DB::beginTransaction();
        
        try {
            $campaign = AdCampaign::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'description' => $request->description,
                'objective' => $request->objective,
                'status' => 'draft',
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ]);

            // Create budget
            AdBudget::create([
                'campaign_id' => $campaign->id,
                'total_budget' => $request->total_budget,
                'daily_budget' => $request->daily_budget,
                'remaining_budget' => $request->total_budget,
                'daily_remaining' => $request->daily_budget,
                'spent_amount' => 0
            ]);

            // Create targeting if provided
            if ($request->target_audience || $request->target_locations || $request->target_age_range || $request->target_interests) {
                AdTargeting::create([
                    'campaign_id' => $campaign->id,
                    'audience_criteria' => $request->target_audience ?? [],
                    'location_criteria' => $request->target_locations ?? [],
                    'age_range' => $request->target_age_range ?? [],
                    'interest_criteria' => $request->target_interests ?? []
                ]);
            }

            DB::commit();

            return redirect()->route('campaigns.show', $campaign->id)
                ->with('success', 'تم إنشاء الحملة الإعلانية بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الحملة: ' . $e->getMessage());
        }
    }

    public function show(AdCampaign $campaign)
    {
        if ($campaign->user_id !== Auth::id() && !Auth::user()->role === 'admin') {
            abort(403);
        }

        $campaign->load(['ads', 'budget', 'targeting']);
        
        // Get performance metrics
        $metrics = [
            'total_ads' => $campaign->ads->count(),
            'active_ads' => $campaign->ads()->where('status', 'active')->count(),
            'total_impressions' => $campaign->ads->sum('impressions_count'),
            'total_clicks' => $campaign->ads->sum('clicks_count'),
            'total_conversions' => $campaign->ads->sum('conversions_count'),
            'total_spent' => $campaign->budget->spent_amount ?? 0,
            'remaining_budget' => $campaign->budget->remaining_budget ?? 0,
            'ctr' => $this->calculateCampaignCTR($campaign),
            'cpc' => $this->calculateCampaignCPC($campaign)
        ];

        // Get daily performance
        $dailyPerformance = $this->getCampaignDailyPerformance($campaign, 30);

        return view('ads.show-campaign', compact('campaign', 'metrics', 'dailyPerformance'));
    }

    public function edit(AdCampaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        return view('ads.edit-campaign', compact('campaign'));
    }

    public function update(Request $request, AdCampaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'objective' => 'required|in:awareness,traffic,conversions,engagement',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date'
        ]);

        $campaign->update([
            'name' => $request->name,
            'description' => $request->description,
            'objective' => $request->objective,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);

        return redirect()->route('campaigns.show', $campaign->id)
            ->with('success', 'تم تحديث الحملة بنجاح');
    }

    public function destroy(AdCampaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if campaign has active ads
        if ($campaign->ads()->where('status', 'active')->exists()) {
            return back()->with('error', 'لا يمكن حذف الحملة التي تحتوي على إعلانات نشطة');
        }

        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'تم حذف الحملة بنجاح');
    }

    public function launch(AdCampaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        if ($campaign->ads->count() === 0) {
            return back()->with('error', 'يجب إضافة إعلانات قبل إطلاق الحملة');
        }

        $campaign->update([
            'status' => 'active',
            'launched_at' => now()
        ]);

        return back()->with('success', 'تم إطلاق الحملة بنجاح');
    }

    public function pause(AdCampaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $campaign->update(['status' => 'paused']);

        // Pause all ads in campaign
        $campaign->ads()->update(['status' => 'paused']);

        return back()->with('success', 'تم إيقاف الحملة مؤقتاً');
    }

    public function resume(AdCampaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $campaign->update(['status' => 'active']);

        // Resume approved ads in campaign
        $campaign->ads()->where('approval_status', 'approved')->update(['status' => 'active']);

        return back()->with('success', 'تم استئناف الحملة');
    }

    public function duplicate(AdCampaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $newCampaign = $campaign->replicate();
        $newCampaign->name = $campaign->name . ' (نسخة)';
        $newCampaign->status = 'draft';
        $newCampaign->launched_at = null;
        $newCampaign->save();

        // Copy budget
        if ($campaign->budget) {
            $newBudget = $campaign->budget->replicate();
            $newBudget->campaign_id = $newCampaign->id;
            $newBudget->spent_amount = 0;
            $newBudget->remaining_budget = $newBudget->total_budget;
            $newBudget->daily_remaining = $newBudget->daily_budget;
            $newBudget->save();
        }

        // Copy targeting if exists
        if ($campaign->targeting) {
            $newTargeting = $campaign->targeting->replicate();
            $newTargeting->campaign_id = $newCampaign->id;
            $newTargeting->save();
        }

        return redirect()->route('campaigns.edit', $newCampaign->id)
            ->with('success', 'تم نسخ الحملة بنجاح');
    }

    public function analytics(AdCampaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $campaign->load(['ads', 'budget', 'targeting']);

        // Get detailed analytics
        $analytics = [
            'overview' => $this->getCampaignOverview($campaign),
            'performance' => $this->getCampaignPerformance($campaign),
            'demographics' => $this->getCampaignDemographics($campaign),
            'placements' => $this->getCampaignPlacements($campaign),
            'timeline' => $this->getCampaignTimeline($campaign)
        ];

        return view('ads.campaign-analytics', compact('campaign', 'analytics'));
    }

    public function updateBudget(Request $request, AdCampaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'total_budget' => 'required|numeric|min:' . ($campaign->budget->spent_amount ?? 0),
            'daily_budget' => 'required|numeric|min:1'
        ]);

        $budget = $campaign->budget;
        $oldBudget = $budget->total_budget;
        
        $budget->update([
            'total_budget' => $request->total_budget,
            'daily_budget' => $request->daily_budget,
            'remaining_budget' => $request->total_budget - $budget->spent_amount
        ]);

        return back()->with('success', 'تم تحديث الميزانية بنجاح');
    }

    private function calculateCampaignCTR($campaign)
    {
        $impressions = $campaign->ads->sum('impressions_count');
        $clicks = $campaign->ads->sum('clicks_count');
        
        return $impressions > 0 ? ($clicks / $impressions) * 100 : 0;
    }

    private function calculateCampaignCPC($campaign)
    {
        $clicks = $campaign->ads->sum('clicks_count');
        $totalSpent = $campaign->budget->spent_amount ?? 0;
        
        return $clicks > 0 ? $totalSpent / $clicks : 0;
    }

    private function getCampaignDailyPerformance($campaign, $days)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return DB::table('ad_impressions')
            ->join('advertisements', 'ad_impressions.advertisement_id', '=', 'advertisements.id')
            ->selectRaw('DATE(ad_impressions.viewed_at) as date, COUNT(*) as impressions')
            ->where('advertisements.campaign_id', $campaign->id)
            ->where('ad_impressions.viewed_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getCampaignOverview($campaign)
    {
        return [
            'total_spent' => $campaign->budget->spent_amount ?? 0,
            'remaining_budget' => $campaign->budget->remaining_budget ?? 0,
            'budget_utilization' => $this->calculateBudgetUtilization($campaign),
            'days_remaining' => $campaign->end_date->diffInDays(Carbon::now()),
            'ads_performance' => $this->getAdsPerformance($campaign)
        ];
    }

    private function getCampaignPerformance($campaign)
    {
        return [
            'impressions' => $campaign->ads->sum('impressions_count'),
            'clicks' => $campaign->ads->sum('clicks_count'),
            'conversions' => $campaign->ads->sum('conversions_count'),
            'ctr' => $this->calculateCampaignCTR($campaign),
            'cpc' => $this->calculateCampaignCPC($campaign),
            'cpa' => $this->calculateCampaignCPA($campaign)
        ];
    }

    private function getCampaignDemographics($campaign)
    {
        // This would require additional tracking tables for user demographics
        return [
            'age_groups' => [],
            'genders' => [],
            'locations' => [],
            'devices' => []
        ];
    }

    private function getCampaignPlacements($campaign)
    {
        return DB::table('ad_placement_advertisement')
            ->join('ad_placements', 'ad_placement_advertisement.ad_placement_id', '=', 'ad_placements.id')
            ->join('advertisements', 'ad_placement_advertisement.advertisement_id', '=', 'advertisements.id')
            ->select('ad_placements.name', 'ad_placements.type', 
                    DB::raw('COUNT(advertisements.id) as ads_count'),
                    DB::raw('SUM(advertisements.impressions_count) as impressions'),
                    DB::raw('SUM(advertisements.clicks_count) as clicks'))
            ->where('advertisements.campaign_id', $campaign->id)
            ->groupBy('ad_placements.id', 'ad_placements.name', 'ad_placements.type')
            ->get();
    }

    private function getCampaignTimeline($campaign)
    {
        return [
            'created_at' => $campaign->created_at,
            'launched_at' => $campaign->launched_at,
            'start_date' => $campaign->start_date,
            'end_date' => $campaign->end_date,
            'status_history' => [] // Would require status history tracking
        ];
    }

    private function calculateBudgetUtilization($campaign)
    {
        $totalBudget = $campaign->budget->total_budget ?? 0;
        $spent = $campaign->budget->spent_amount ?? 0;
        
        return $totalBudget > 0 ? ($spent / $totalBudget) * 100 : 0;
    }

    private function calculateCampaignCPA($campaign)
    {
        $conversions = $campaign->ads->sum('conversions_count');
        $totalSpent = $campaign->budget->spent_amount ?? 0;
        
        return $conversions > 0 ? $totalSpent / $conversions : 0;
    }

    private function getAdsPerformance($campaign)
    {
        return $campaign->ads()->selectRaw('
            COUNT(*) as total_ads,
            SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_ads,
            SUM(CASE WHEN approval_status = "approved" THEN 1 ELSE 0 END) as approved_ads,
            AVG(impressions_count) as avg_impressions,
            AVG(clicks_count) as avg_clicks
        ')->first();
    }
}
