<?php

namespace App\Http\Controllers;

use App\Models\AdPlacement;
use App\Models\Advertisement;
use App\Models\AdImpression;
use App\Models\AdClick;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdPlacementController extends Controller
{
    public function index()
    {
        $placements = AdPlacement::with(['ads'])
            ->orderBy('name')
            ->paginate(20);

        return view('ads.placements', compact('placements'));
    }

    public function create()
    {
        return view('ads.create-placement');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:header,sidebar,content,footer,popup,mobile',
            'position' => 'required|string|max:255',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
            'max_ads' => 'required|integer|min:1',
            'pricing_model' => 'required|in:cpm,cpc,cpa',
            'base_price' => 'required|numeric|min:0.01',
            'min_bid' => 'required|numeric|min:0.01',
            'target_pages' => 'nullable|array',
            'excluded_pages' => 'nullable|array',
            'device_types' => 'nullable|array'
        ]);

        $placement = AdPlacement::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'position' => $request->position,
            'width' => $request->width,
            'height' => $request->height,
            'max_ads' => $request->max_ads,
            'pricing_model' => $request->pricing_model,
            'base_price' => $request->base_price,
            'min_bid' => $request->min_bid,
            'target_pages' => $request->target_pages ?? [],
            'excluded_pages' => $request->excluded_pages ?? [],
            'device_types' => $request->device_types ?? [],
            'is_active' => true
        ]);

        return redirect()->route('placements.show', $placement->id)
            ->with('success', 'تم إنشاء موضع الإعلان بنجاح');
    }

    public function show(AdPlacement $placement)
    {
        $placement->load(['ads', 'ads.user']);

        // Get performance metrics
        $metrics = [
            'total_impressions' => $this->getPlacementImpressions($placement),
            'total_clicks' => $this->getPlacementClicks($placement),
            'total_revenue' => $this->getPlacementRevenue($placement),
            'active_ads' => $placement->ads()->where('status', 'active')->count(),
            'fill_rate' => $this->calculateFillRate($placement),
            'ctr' => $this->calculatePlacementCTR($placement),
            'ecpm' => $this->calculatePlacementECPM($placement)
        ];

        // Get top performing ads
        $topAds = $this->getTopPerformingAds($placement, 10);

        return view('ads.show-placement', compact('placement', 'metrics', 'topAds'));
    }

    public function edit(AdPlacement $placement)
    {
        return view('ads.edit-placement', compact('placement'));
    }

    public function update(Request $request, AdPlacement $placement)
    {
        if (!Auth::user()->role === 'admin') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:header,sidebar,content,footer,popup,mobile',
            'position' => 'required|string|max:255',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
            'max_ads' => 'required|integer|min:1',
            'pricing_model' => 'required|in:cpm,cpc,cpa',
            'base_price' => 'required|numeric|min:0.01',
            'min_bid' => 'required|numeric|min:0.01'
        ]);

        $placement->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'position' => $request->position,
            'width' => $request->width,
            'height' => $request->height,
            'max_ads' => $request->max_ads,
            'pricing_model' => $request->pricing_model,
            'base_price' => $request->base_price,
            'min_bid' => $request->min_bid,
            'target_pages' => $request->target_pages ?? [],
            'excluded_pages' => $request->excluded_pages ?? [],
            'device_types' => $request->device_types ?? []
        ]);

        return redirect()->route('placements.show', $placement->id)
            ->with('success', 'تم تحديث موضع الإعلان بنجاح');
    }

    public function destroy(AdPlacement $placement)
    {
        if (!Auth::user()->role === 'admin') {
            abort(403);
        }

        // Check if placement has active ads
        if ($placement->ads()->where('status', 'active')->exists()) {
            return back()->with('error', 'لا يمكن حذف الموضع الذي يحتوي على إعلانات نشطة');
        }

        $placement->delete();

        return redirect()->route('placements.index')
            ->with('success', 'تم حذف موضع الإعلان بنجاح');
    }

    public function activate(AdPlacement $placement)
    {
        if (!Auth::user()->role === 'admin') {
            abort(403);
        }

        $placement->update(['is_active' => true]);

        return back()->with('success', 'تم تفعيل موضع الإعلان');
    }

    public function deactivate(AdPlacement $placement)
    {
        if (!Auth::user()->role === 'admin') {
            abort(403);
        }

        $placement->update(['is_active' => false]);

        // Deactivate all ads in this placement
        $placement->ads()->update(['status' => 'paused']);

        return back()->with('success', 'تم إيقاف موضع الإعلان');
    }

    public function getAds(AdPlacement $placement, Request $request)
    {
        if (!$placement->is_active) {
            return response()->json(['ads' => []]);
        }

        // Get eligible ads for this placement
        $ads = $placement->ads()
            ->where('status', 'active')
            ->where('approval_status', 'approved')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereHas('budget', function($query) {
                $query->where('remaining_budget', '>', 0)
                      ->where('daily_remaining', '>', 0);
            })
            ->with(['user'])
            ->get();

        // Filter ads based on targeting criteria
        $eligibleAds = $ads->filter(function($ad) use ($request) {
            return $this->isAdEligibleForUser($ad, $request);
        });

        // Sort by bid amount and get top ads
        $sortedAds = $eligibleAds->sortByDesc(function($ad) {
            return $ad->daily_budget;
        });

        $selectedAds = $sortedAds->take($placement->max_ads);

        return response()->json([
            'ads' => $selectedAds->map(function($ad) {
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'description' => $ad->description,
                    'image_url' => $ad->image_url,
                    'video_url' => $ad->video_url,
                    'target_url' => route('ads.click', $ad->id),
                    'track_url' => route('ads.impression', $ad->id),
                    'type' => $ad->type
                ];
            })
        ]);
    }

    public function analytics(AdPlacement $placement)
    {
        if (!Auth::user()->role === 'admin') {
            abort(403);
        }

        $placement->load(['ads']);

        $analytics = [
            'overview' => $this->getPlacementOverview($placement),
            'performance' => $this->getPlacementPerformance($placement),
            'revenue' => $this->getPlacementRevenueAnalytics($placement),
            'timeline' => $this->getPlacementTimeline($placement)
        ];

        return view('ads.placement-analytics', compact('placement', 'analytics'));
    }

    public function updatePricing(Request $request, AdPlacement $placement)
    {
        if (!Auth::user()->role === 'admin') {
            abort(403);
        }

        $request->validate([
            'base_price' => 'required|numeric|min:0.01',
            'min_bid' => 'required|numeric|min:0.01'
        ]);

        $placement->update([
            'base_price' => $request->base_price,
            'min_bid' => $request->min_bid
        ]);

        return back()->with('success', 'تم تحديث الأسعار بنجاح');
    }

    private function getPlacementImpressions($placement)
    {
        return AdImpression::join('advertisements', 'ad_impressions.advertisement_id', '=', 'advertisements.id')
            ->join('ad_placement_advertisement', 'advertisements.id', '=', 'ad_placement_advertisement.advertisement_id')
            ->where('ad_placement_advertisement.ad_placement_id', $placement->id)
            ->count();
    }

    private function getPlacementClicks($placement)
    {
        return AdClick::join('advertisements', 'ad_clicks.advertisement_id', '=', 'advertisements.id')
            ->join('ad_placement_advertisement', 'advertisements.id', '=', 'ad_placement_advertisement.advertisement_id')
            ->where('ad_placement_advertisement.ad_placement_id', $placement->id)
            ->count();
    }

    private function getPlacementRevenue($placement)
    {
        // This would depend on your pricing model and actual billing logic
        $impressions = $this->getPlacementImpressions($placement);
        $clicks = $this->getPlacementClicks($placement);
        
        if ($placement->pricing_model === 'cpm') {
            return ($impressions / 1000) * $placement->base_price;
        } elseif ($placement->pricing_model === 'cpc') {
            return $clicks * $placement->base_price;
        }
        
        return 0;
    }

    private function calculateFillRate($placement)
    {
        $totalSlots = $placement->max_ads;
        $activeAds = $placement->ads()->where('status', 'active')->count();
        
        return $totalSlots > 0 ? ($activeAds / $totalSlots) * 100 : 0;
    }

    private function calculatePlacementCTR($placement)
    {
        $impressions = $this->getPlacementImpressions($placement);
        $clicks = $this->getPlacementClicks($placement);
        
        return $impressions > 0 ? ($clicks / $impressions) * 100 : 0;
    }

    private function calculatePlacementECPM($placement)
    {
        $impressions = $this->getPlacementImpressions($placement);
        $revenue = $this->getPlacementRevenue($placement);
        
        return $impressions > 0 ? ($revenue / $impressions) * 1000 : 0;
    }

    private function getTopPerformingAds($placement, $limit)
    {
        return Advertisement::join('ad_placement_advertisement', 'advertisements.id', '=', 'ad_placement_advertisement.advertisement_id')
            ->where('ad_placement_advertisement.ad_placement_id', $placement->id)
            ->with(['user'])
            ->orderBy('impressions_count', 'desc')
            ->limit($limit)
            ->get();
    }

    private function isAdEligibleForUser($ad, $request)
    {
        // Check if ad has targeting criteria
        if (!$ad->targeting) {
            return true;
        }

        $targeting = $ad->targeting;
        $user = Auth::user();

        // Check location targeting
        if (!empty($targeting->location_criteria) && $user) {
            // Implement location checking logic
        }

        // Check age targeting
        if (!empty($targeting->age_range) && $user) {
            // Implement age checking logic
        }

        // Check interest targeting
        if (!empty($targeting->interest_criteria) && $user) {
            // Implement interest checking logic
        }

        return true;
    }

    private function getPlacementOverview($placement)
    {
        return [
            'total_impressions' => $this->getPlacementImpressions($placement),
            'total_clicks' => $this->getPlacementClicks($placement),
            'total_revenue' => $this->getPlacementRevenue($placement),
            'active_ads' => $placement->ads()->where('status', 'active')->count(),
            'fill_rate' => $this->calculateFillRate($placement),
            'ctr' => $this->calculatePlacementCTR($placement),
            'ecpm' => $this->calculatePlacementECPM($placement)
        ];
    }

    private function getPlacementPerformance($placement)
    {
        $startDate = Carbon::now()->subDays(30);
        
        return DB::table('ad_impressions')
            ->join('advertisements', 'ad_impressions.advertisement_id', '=', 'advertisements.id')
            ->join('ad_placement_advertisement', 'advertisements.id', '=', 'ad_placement_advertisement.advertisement_id')
            ->selectRaw('DATE(ad_impressions.viewed_at) as date, COUNT(*) as impressions')
            ->where('ad_placement_advertisement.ad_placement_id', $placement->id)
            ->where('ad_impressions.viewed_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getPlacementRevenueAnalytics($placement)
    {
        // Implement detailed revenue analytics
        return [
            'daily_revenue' => [],
            'revenue_by_ad_type' => [],
            'revenue_by_pricing_model' => []
        ];
    }

    private function getPlacementTimeline($placement)
    {
        return [
            'created_at' => $placement->created_at,
            'last_updated' => $placement->updated_at,
            'performance_history' => []
        ];
    }
}
