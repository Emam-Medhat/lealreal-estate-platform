<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\AdCampaign;
use App\Models\AdPlacement;
use App\Models\AdClick;
use App\Models\AdImpression;
use App\Models\AdConversion;
use App\Models\AdBudget;
use App\Models\AdTargeting;
use App\Models\PromotedListing;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdvertisementController extends Controller
{
    public function index()
    {
        $ads = Advertisement::with(['campaign', 'placements', 'creatives'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('ads.index', compact('ads'));
    }

    public function create()
    {
        $campaigns = AdCampaign::where('user_id', Auth::id())
            ->where('status', 'active')
            ->get();
        
        $placements = AdPlacement::where('is_active', true)->get();
        $properties = Property::where('user_id', Auth::id())->get();

        return view('ads.create', compact('campaigns', 'placements', 'properties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:ad_campaigns,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'type' => 'required|in:banner,native,video,popup',
            'image' => 'required_if:type,banner,native|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video_url' => 'required_if:type,video|url|max:500',
            'target_url' => 'required|url|max:500',
            'placements' => 'required|array',
            'placements.*' => 'exists:ad_placements,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'daily_budget' => 'required|numeric|min:1',
            'target_audience' => 'nullable|array',
            'target_locations' => 'nullable|array',
            'target_age_range' => 'nullable|array',
            'target_interests' => 'nullable|array'
        ]);

        DB::beginTransaction();
        
        try {
            $ad = Advertisement::create([
                'user_id' => Auth::id(),
                'campaign_id' => $request->campaign_id,
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
                'target_url' => $request->target_url,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'daily_budget' => $request->daily_budget,
                'status' => 'pending',
                'approval_status' => 'pending'
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('ads', $imageName, 'public');
                $ad->update(['image_url' => $imagePath]);
            }

            // Handle video URL
            if ($request->video_url) {
                $ad->update(['video_url' => $request->video_url]);
            }

            // Attach placements
            $ad->placements()->attach($request->placements);

            // Create targeting
            if ($request->target_audience || $request->target_locations || $request->target_age_range || $request->target_interests) {
                AdTargeting::create([
                    'advertisement_id' => $ad->id,
                    'audience_criteria' => $request->target_audience ?? [],
                    'location_criteria' => $request->target_locations ?? [],
                    'age_range' => $request->target_age_range ?? [],
                    'interest_criteria' => $request->target_interests ?? []
                ]);
            }

            DB::commit();

            return redirect()->route('ads.show', $ad->id)
                ->with('success', 'تم إنشاء الإعلان بنجاح وجاري المراجعة');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الإعلان: ' . $e->getMessage());
        }
    }

    public function show(Advertisement $advertisement)
    {
        if ($advertisement->user_id !== Auth::id() && !Auth::user()->role === 'admin') {
            abort(403);
        }

        $advertisement->load(['campaign', 'placements', 'targeting', 'creatives']);
        
        // Get performance metrics
        $metrics = [
            'impressions' => $advertisement->impressions()->count(),
            'clicks' => $advertisement->clicks()->count(),
            'conversions' => $advertisement->conversions()->count(),
            'ctr' => $this->calculateCTR($advertisement),
            'cpc' => $this->calculateCPC($advertisement),
            'cpa' => $this->calculateCPA($advertisement),
            'total_spent' => $advertisement->total_spent,
            'daily_spent' => $advertisement->daily_spent
        ];

        // Get daily performance for the last 30 days
        $dailyPerformance = $this->getDailyPerformance($advertisement, 30);

        return view('ads.show', compact('advertisement', 'metrics', 'dailyPerformance'));
    }

    public function edit(Advertisement $advertisement)
    {
        if ($advertisement->user_id !== Auth::id()) {
            abort(403);
        }

        $campaigns = AdCampaign::where('user_id', Auth::id())
            ->where('status', 'active')
            ->get();
        
        $placements = AdPlacement::where('is_active', true)->get();

        return view('ads.edit', compact('advertisement', 'campaigns', 'placements'));
    }

    public function update(Request $request, Advertisement $advertisement)
    {
        if ($advertisement->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'campaign_id' => 'required|exists:ad_campaigns,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'target_url' => 'required|url|max:500',
            'placements' => 'required|array',
            'placements.*' => 'exists:ad_placements,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'daily_budget' => 'required|numeric|min:1'
        ]);

        $advertisement->update([
            'campaign_id' => $request->campaign_id,
            'title' => $request->title,
            'description' => $request->description,
            'target_url' => $request->target_url,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'daily_budget' => $request->daily_budget
        ]);

        // Update placements
        $advertisement->placements()->sync($request->placements);

        return redirect()->route('ads.show', $advertisement->id)
            ->with('success', 'تم تحديث الإعلان بنجاح');
    }

    public function destroy(Advertisement $advertisement)
    {
        if ($advertisement->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete image if exists
        if ($advertisement->image_url) {
            Storage::disk('public')->delete($advertisement->image_url);
        }

        $advertisement->delete();

        return redirect()->route('ads.index')
            ->with('success', 'تم حذف الإعلان بنجاح');
    }

    public function approve(Advertisement $advertisement)
    {
        if (!Auth::user()->role === 'admin') {
            abort(403);
        }

        $advertisement->update([
            'approval_status' => 'approved',
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => Auth::id()
        ]);

        return back()->with('success', 'تم الموافقة على الإعلان');
    }

    public function reject(Request $request, Advertisement $advertisement)
    {
        if (!Auth::user()->role === 'admin') {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $advertisement->update([
            'approval_status' => 'rejected',
            'status' => 'inactive',
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),
            'rejection_reason' => $request->rejection_reason
        ]);

        return back()->with('success', 'تم رفض الإعلان');
    }

    public function pause(Advertisement $advertisement)
    {
        if ($advertisement->user_id !== Auth::id()) {
            abort(403);
        }

        $advertisement->update(['status' => 'paused']);

        return back()->with('success', 'تم إيقاف الإعلان مؤقتاً');
    }

    public function resume(Advertisement $advertisement)
    {
        if ($advertisement->user_id !== Auth::id()) {
            abort(403);
        }

        if ($advertisement->approval_status === 'approved') {
            $advertisement->update(['status' => 'active']);
        }

        return back()->with('success', 'تم استئناف الإعلان');
    }

    public function duplicate(Advertisement $advertisement)
    {
        if ($advertisement->user_id !== Auth::id()) {
            abort(403);
        }

        $newAd = $advertisement->replicate();
        $newAd->title = $advertisement->title . ' (نسخة)';
        $newAd->status = 'pending';
        $newAd->approval_status = 'pending';
        $newAd->total_spent = 0;
        $newAd->daily_spent = 0;
        $newAd->impressions_count = 0;
        $newAd->clicks_count = 0;
        $newAd->conversions_count = 0;
        $newAd->save();

        // Copy placements
        $newAd->placements()->attach($advertisement->placements->pluck('id'));

        // Copy targeting if exists
        if ($advertisement->targeting) {
            $newTargeting = $advertisement->targeting->replicate();
            $newTargeting->advertisement_id = $newAd->id;
            $newTargeting->save();
        }

        return redirect()->route('ads.edit', $newAd->id)
            ->with('success', 'تم نسخ الإعلان بنجاح');
    }

    public function trackClick(Request $request, Advertisement $advertisement)
    {
        $clickData = [
            'advertisement_id' => $advertisement->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'clicked_at' => now()
        ];

        if (Auth::check()) {
            $clickData['user_id'] = Auth::id();
        }

        $click = AdClick::create($clickData);

        // Update ad click count
        $advertisement->increment('clicks_count');

        // Redirect to target URL
        return redirect($advertisement->target_url);
    }

    public function trackImpression(Request $request, Advertisement $advertisement)
    {
        $impressionData = [
            'advertisement_id' => $advertisement->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'page_url' => $request->header('referer'),
            'viewed_at' => now()
        ];

        if (Auth::check()) {
            $impressionData['user_id'] = Auth::id();
        }

        AdImpression::create($impressionData);

        // Update ad impression count
        $advertisement->increment('impressions_count');

        return response()->json(['success' => true]);
    }

    private function calculateCTR($advertisement)
    {
        $impressions = $advertisement->impressions_count;
        $clicks = $advertisement->clicks_count;
        
        return $impressions > 0 ? ($clicks / $impressions) * 100 : 0;
    }

    private function calculateCPC($advertisement)
    {
        $clicks = $advertisement->clicks_count;
        $totalSpent = $advertisement->total_spent;
        
        return $clicks > 0 ? $totalSpent / $clicks : 0;
    }

    private function calculateCPA($advertisement)
    {
        $conversions = $advertisement->conversions_count;
        $totalSpent = $advertisement->total_spent;
        
        return $conversions > 0 ? $totalSpent / $conversions : 0;
    }

    private function getDailyPerformance($advertisement, $days)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return DB::table('ad_impressions')
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as impressions')
            ->where('advertisement_id', $advertisement->id)
            ->where('viewed_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
