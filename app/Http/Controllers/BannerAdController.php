<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\AdPlacement;
use App\Models\AdClick;
use App\Models\AdImpression;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BannerAdController extends Controller
{
    public function index()
    {
        $bannerAds = Advertisement::with(['campaign', 'placements'])
            ->where('type', 'banner')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('ads.banner-ads', compact('bannerAds'));
    }

    public function create()
    {
        $campaigns = Auth::user()->adCampaigns()->where('status', 'active')->get();
        $placements = AdPlacement::where('type', 'header')
            ->orWhere('type', 'sidebar')
            ->orWhere('type', 'footer')
            ->where('is_active', true)
            ->get();

        return view('ads.create-banner-ad', compact('campaigns', 'placements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:ad_campaigns,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'target_url' => 'required|url|max:500',
            'placements' => 'required|array',
            'placements.*' => 'exists:ad_placements,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'daily_budget' => 'required|numeric|min:1',
            'banner_size' => 'required|in:leaderboard,medium_rectangle,large_rectangle,wide_skyscraper,custom',
            'custom_width' => 'required_if:banner_size,custom|integer|min:100|max:1200',
            'custom_height' => 'required_if:banner_size,custom|integer|min:50|max:600',
            'animation_type' => 'nullable|in:none,fade,slide,zoom',
            'click_tracking' => 'boolean',
            'impression_tracking' => 'boolean'
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('banner-ads', $imageName, 'public');
        }

        // Get banner dimensions
        $dimensions = $this->getBannerDimensions($request->banner_size, $request->custom_width, $request->custom_height);

        $bannerAd = Advertisement::create([
            'user_id' => Auth::id(),
            'campaign_id' => $request->campaign_id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => 'banner',
            'image_url' => $imagePath,
            'target_url' => $request->target_url,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'daily_budget' => $request->daily_budget,
            'status' => 'pending',
            'approval_status' => 'pending',
            'banner_size' => $request->banner_size,
            'width' => $dimensions['width'],
            'height' => $dimensions['height'],
            'animation_type' => $request->animation_type ?? 'none',
            'click_tracking' => $request->click_tracking ?? true,
            'impression_tracking' => $request->impression_tracking ?? true
        ]);

        // Attach placements
        $bannerAd->placements()->attach($request->placements);

        return redirect()->route('banner-ads.show', $bannerAd->id)
            ->with('success', 'تم إنشاء إعلان البانر بنجاح');
    }

    public function show(Advertisement $bannerAd)
    {
        if ($bannerAd->user_id !== Auth::id() && !Auth::user()->role === 'admin') {
            abort(403);
        }

        if ($bannerAd->type !== 'banner') {
            abort(404);
        }

        $bannerAd->load(['campaign', 'placements', 'clicks', 'impressions']);

        // Get performance metrics
        $metrics = [
            'impressions' => $bannerAd->impressions_count,
            'clicks' => $bannerAd->clicks_count,
            'ctr' => $this->calculateCTR($bannerAd),
            'cpc' => $this->calculateCPC($bannerAd),
            'cpm' => $this->calculateCPM($bannerAd),
            'total_spent' => $bannerAd->total_spent,
            'daily_spent' => $bannerAd->daily_spent,
            'placement_performance' => $this->getPlacementPerformance($bannerAd)
        ];

        // Get daily performance
        $dailyPerformance = $this->getDailyPerformance($bannerAd, 30);

        return view('ads.show-banner-ad', compact('bannerAd', 'metrics', 'dailyPerformance'));
    }

    public function edit(Advertisement $bannerAd)
    {
        if ($bannerAd->user_id !== Auth::id()) {
            abort(403);
        }

        if ($bannerAd->type !== 'banner') {
            abort(404);
        }

        $campaigns = Auth::user()->adCampaigns()->where('status', 'active')->get();
        $placements = AdPlacement::where('is_active', true)->get();

        return view('ads.edit-banner-ad', compact('bannerAd', 'campaigns', 'placements'));
    }

    public function update(Request $request, Advertisement $bannerAd)
    {
        if ($bannerAd->user_id !== Auth::id()) {
            abort(403);
        }

        if ($bannerAd->type !== 'banner') {
            abort(404);
        }

        $request->validate([
            'campaign_id' => 'required|exists:ad_campaigns,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'target_url' => 'required|url|max:500',
            'placements' => 'required|array',
            'placements.*' => 'exists:ad_placements,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'daily_budget' => 'required|numeric|min:1',
            'animation_type' => 'nullable|in:none,fade,slide,zoom'
        ]);

        // Handle image upload
        $imagePath = $bannerAd->image_url;
        if ($request->hasFile('image')) {
            // Delete old image
            if ($bannerAd->image_url) {
                Storage::disk('public')->delete($bannerAd->image_url);
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('banner-ads', $imageName, 'public');
        }

        $bannerAd->update([
            'campaign_id' => $request->campaign_id,
            'title' => $request->title,
            'description' => $request->description,
            'image_url' => $imagePath,
            'target_url' => $request->target_url,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'daily_budget' => $request->daily_budget,
            'animation_type' => $request->animation_type ?? 'none'
        ]);

        // Update placements
        $bannerAd->placements()->sync($request->placements);

        return redirect()->route('banner-ads.show', $bannerAd->id)
            ->with('success', 'تم تحديث إعلان البانر بنجاح');
    }

    public function destroy(Advertisement $bannerAd)
    {
        if ($bannerAd->user_id !== Auth::id()) {
            abort(403);
        }

        if ($bannerAd->type !== 'banner') {
            abort(404);
        }

        // Delete image
        if ($bannerAd->image_url) {
            Storage::disk('public')->delete($bannerAd->image_url);
        }

        $bannerAd->delete();

        return redirect()->route('banner-ads.index')
            ->with('success', 'تم حذف إعلان البانر بنجاح');
    }

    public function preview(Advertisement $bannerAd)
    {
        if ($bannerAd->user_id !== Auth::id()) {
            abort(403);
        }

        if ($bannerAd->type !== 'banner') {
            abort(404);
        }

        return view('ads.preview-banner-ad', compact('bannerAd'));
    }

    public function getBannerCode(Advertisement $bannerAd)
    {
        if ($bannerAd->user_id !== Auth::id()) {
            abort(403);
        }

        if ($bannerAd->type !== 'banner') {
            abort(404);
        }

        $embedCode = $this->generateEmbedCode($bannerAd);

        return view('ads.banner-code', compact('bannerAd', 'embedCode'));
    }

    public function trackClick(Request $request, Advertisement $bannerAd)
    {
        if ($bannerAd->type !== 'banner') {
            abort(404);
        }

        if (!$bannerAd->click_tracking) {
            return redirect($bannerAd->target_url);
        }

        // Track click
        AdClick::create([
            'advertisement_id' => $bannerAd->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'clicked_at' => now(),
            'user_id' => Auth::id() ?? null
        ]);

        // Update click count
        $bannerAd->increment('clicks_count');

        return redirect($bannerAd->target_url);
    }

    public function trackImpression(Request $request, Advertisement $bannerAd)
    {
        if ($bannerAd->type !== 'banner') {
            return response()->json(['success' => false]);
        }

        if (!$bannerAd->impression_tracking) {
            return response()->json(['success' => true]);
        }

        // Track impression
        AdImpression::create([
            'advertisement_id' => $bannerAd->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'page_url' => $request->header('referer'),
            'viewed_at' => now(),
            'user_id' => Auth::id() ?? null
        ]);

        // Update impression count
        $bannerAd->increment('impressions_count');

        return response()->json(['success' => true]);
    }

    public function getBannerSizes()
    {
        $sizes = [
            'leaderboard' => ['width' => 728, 'height' => 90, 'name' => 'Leaderboard (728x90)'],
            'medium_rectangle' => ['width' => 300, 'height' => 250, 'name' => 'Medium Rectangle (300x250)'],
            'large_rectangle' => ['width' => 336, 'height' => 280, 'name' => 'Large Rectangle (336x280)'],
            'wide_skyscraper' => ['width' => 160, 'height' => 600, 'name' => 'Wide Skyscraper (160x600)'],
            'custom' => ['width' => 0, 'height' => 0, 'name' => 'Custom Size']
        ];

        return response()->json($sizes);
    }

    private function getBannerDimensions($size, $customWidth = null, $customHeight = null)
    {
        $dimensions = [
            'leaderboard' => ['width' => 728, 'height' => 90],
            'medium_rectangle' => ['width' => 300, 'height' => 250],
            'large_rectangle' => ['width' => 336, 'height' => 280],
            'wide_skyscraper' => ['width' => 160, 'height' => 600],
            'custom' => ['width' => $customWidth, 'height' => $customHeight]
        ];

        return $dimensions[$size] ?? ['width' => 300, 'height' => 250];
    }

    private function calculateCTR($bannerAd)
    {
        $impressions = $bannerAd->impressions_count;
        $clicks = $bannerAd->clicks_count;
        
        return $impressions > 0 ? ($clicks / $impressions) * 100 : 0;
    }

    private function calculateCPC($bannerAd)
    {
        $clicks = $bannerAd->clicks_count;
        $spent = $bannerAd->total_spent;
        
        return $clicks > 0 ? $spent / $clicks : 0;
    }

    private function calculateCPM($bannerAd)
    {
        $impressions = $bannerAd->impressions_count;
        $spent = $bannerAd->total_spent;
        
        return $impressions > 0 ? ($spent / $impressions) * 1000 : 0;
    }

    private function getPlacementPerformance($bannerAd)
    {
        return $bannerAd->placements->map(function($placement) use ($bannerAd) {
            return [
                'placement' => $placement,
                'impressions' => $bannerAd->impressions()->count(),
                'clicks' => $bannerAd->clicks()->count(),
                'ctr' => $this->calculateCTR($bannerAd)
            ];
        });
    }

    private function getDailyPerformance($bannerAd, $days)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return $bannerAd->impressions()
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as impressions')
            ->where('viewed_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function generateEmbedCode($bannerAd)
    {
        $baseUrl = url('/');
        $imageUrl = Storage::url($bannerAd->image_url);
        $clickUrl = route('banner-ads.click', $bannerAd->id);
        $trackUrl = route('banner-ads.impression', $bannerAd->id);

        return "
<div id='banner-ad-{$bannerAd->id}' style='width: {$bannerAd->width}px; height: {$bannerAd->height}px;'>
    <a href='{$clickUrl}' target='_blank' onclick='trackBannerClick({$bannerAd->id})'>
        <img src='{$imageUrl}' alt='{$bannerAd->title}' style='width: 100%; height: 100%; object-fit: cover;'>
    </a>
</div>
<script>
function trackBannerImpression(adId) {
    fetch('{$trackUrl}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
        }
    });
}

function trackBannerClick(adId) {
    // Click is tracked server-side
}

// Track impression when ad is loaded
document.addEventListener('DOMContentLoaded', function() {
    trackBannerImpression({$bannerAd->id});
});
</script>";
    }
}
