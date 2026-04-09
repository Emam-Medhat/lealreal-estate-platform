<?php

namespace App\Http\Controllers;

use App\Models\PromotedListing;
use App\Models\Property;
use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PromotedListingController extends Controller
{
    public function index()
    {
        $promotedListings = PromotedListing::with(['property', 'advertisement'])
            ->whereHas('property', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('ads.promoted-listings', compact('promotedListings'));
    }

    public function create()
    {
        $properties = Property::where('user_id', Auth::id())
            ->whereDoesntHave('promotedListing')
            ->get();

        return view('ads.create-promoted-listing', compact('properties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'promotion_type' => 'required|in:featured,premium,spotlight',
            'duration' => 'required|integer|min:1|max:365',
            'daily_budget' => 'required|numeric|min:1',
            'target_audience' => 'nullable|array',
            'promotion_text' => 'nullable|string|max:500',
            'highlight_features' => 'nullable|array',
            'call_to_action' => 'nullable|string|max:100',
            'priority_level' => 'required|integer|min:1|max:10'
        ]);

        $property = Property::findOrFail($request->property_id);
        
        if ($property->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if property is already promoted
        if ($property->promotedListing) {
            return back()->withErrors(['property_id' => 'العقار تم ترويجه بالفعل']);
        }

        DB::beginTransaction();
        
        try {
            $endDate = Carbon::now()->addDays($request->duration);
            
            $promotedListing = PromotedListing::create([
                'property_id' => $request->property_id,
                'user_id' => Auth::id(),
                'promotion_type' => $request->promotion_type,
                'duration' => $request->duration,
                'start_date' => now(),
                'end_date' => $endDate,
                'daily_budget' => $request->daily_budget,
                'total_budget' => $request->daily_budget * $request->duration,
                'promotion_text' => $request->promotion_text,
                'highlight_features' => $request->highlight_features ?? [],
                'call_to_action' => $request->call_to_action,
                'priority_level' => $request->priority_level,
                'status' => 'active'
            ]);

            // Create corresponding advertisement
            $advertisement = Advertisement::create([
                'user_id' => Auth::id(),
                'title' => $property->title ?? 'عقار مميز',
                'description' => $request->promotion_text ?? $property->description,
                'type' => 'native',
                'target_url' => route('properties.show', $property->id),
                'start_date' => now(),
                'end_date' => $endDate,
                'daily_budget' => $request->daily_budget,
                'status' => 'active',
                'approval_status' => 'approved',
                'promotion_type' => $request->promotion_type
            ]);

            // Link advertisement to promoted listing
            $promotedListing->update(['advertisement_id' => $advertisement->id]);

            DB::commit();

            return redirect()->route('promoted-listings.show', $promotedListing->id)
                ->with('success', 'تم ترويج العقار بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء ترويج العقار: ' . $e->getMessage());
        }
    }

    public function show(PromotedListing $promotedListing)
    {
        if ($promotedListing->user_id !== Auth::id() && !Auth::user()->role === 'admin') {
            abort(403);
        }

        $promotedListing->load(['property', 'advertisement', 'advertisement.placements']);

        // Get performance metrics
        $metrics = [
            'views' => $this->getPromotedViews($promotedListing),
            'clicks' => $this->getPromotedClicks($promotedListing),
            'inquiries' => $this->getPromotedInquiries($promotedListing),
            'ctr' => $this->calculatePromotedCTR($promotedListing),
            'cost_per_view' => $this->calculateCostPerView($promotedListing),
            'cost_per_inquiry' => $this->calculateCostPerInquiry($promotedListing),
            'total_spent' => $promotedListing->total_spent,
            'remaining_budget' => $promotedListing->remaining_budget
        ];

        // Get daily performance
        $dailyPerformance = $this->getDailyPerformance($promotedListing);

        return view('ads.show-promoted-listing', compact('promotedListing', 'metrics', 'dailyPerformance'));
    }

    public function edit(PromotedListing $promotedListing)
    {
        if ($promotedListing->user_id !== Auth::id()) {
            abort(403);
        }

        return view('ads.edit-promoted-listing', compact('promotedListing'));
    }

    public function update(Request $request, PromotedListing $promotedListing)
    {
        if ($promotedListing->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'promotion_text' => 'nullable|string|max:500',
            'highlight_features' => 'nullable|array',
            'call_to_action' => 'nullable|string|max:100',
            'daily_budget' => 'required|numeric|min:1',
            'priority_level' => 'required|integer|min:1|max:10'
        ]);

        $promotedListing->update([
            'promotion_text' => $request->promotion_text,
            'highlight_features' => $request->highlight_features ?? [],
            'call_to_action' => $request->call_to_action,
            'daily_budget' => $request->daily_budget,
            'priority_level' => $request->priority_level
        ]);

        // Update linked advertisement
        if ($promotedListing->advertisement) {
            $promotedListing->advertisement->update([
                'description' => $request->promotion_text,
                'daily_budget' => $request->daily_budget
            ]);
        }

        return redirect()->route('promoted-listings.show', $promotedListing->id)
            ->with('success', 'تم تحديث ترويج العقار بنجاح');
    }

    public function destroy(PromotedListing $promotedListing)
    {
        if ($promotedListing->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete linked advertisement
        if ($promotedListing->advertisement) {
            $promotedListing->advertisement->delete();
        }

        $promotedListing->delete();

        return redirect()->route('promoted-listings.index')
            ->with('success', 'تم إلغاء ترويج العقار بنجاح');
    }

    public function pause(PromotedListing $promotedListing)
    {
        if ($promotedListing->user_id !== Auth::id()) {
            abort(403);
        }

        $promotedListing->update(['status' => 'paused']);

        // Pause linked advertisement
        if ($promotedListing->advertisement) {
            $promotedListing->advertisement->update(['status' => 'paused']);
        }

        return back()->with('success', 'تم إيقاف الترويج مؤقتاً');
    }

    public function resume(PromotedListing $promotedListing)
    {
        if ($promotedListing->user_id !== Auth::id()) {
            abort(403);
        }

        $promotedListing->update(['status' => 'active']);

        // Resume linked advertisement
        if ($promotedListing->advertisement) {
            $promotedListing->advertisement->update(['status' => 'active']);
        }

        return back()->with('success', 'تم استئناف الترويج');
    }

    public function extend(Request $request, PromotedListing $promotedListing)
    {
        if ($promotedListing->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'additional_days' => 'required|integer|min:1|max:365',
            'additional_budget' => 'required|numeric|min:1'
        ]);

        $newEndDate = $promotedListing->end_date->addDays($request->additional_days);
        
        $promotedListing->update([
            'duration' => $promotedListing->duration + $request->additional_days,
            'end_date' => $newEndDate,
            'total_budget' => $promotedListing->total_budget + $request->additional_budget,
            'remaining_budget' => $promotedListing->remaining_budget + $request->additional_budget
        ]);

        // Update linked advertisement
        if ($promotedListing->advertisement) {
            $promotedListing->advertisement->update([
                'end_date' => $newEndDate,
                'daily_budget' => $promotedListing->daily_budget
            ]);
        }

        return back()->with('success', 'تم تمديد فترة الترويج بنجاح');
    }

    public function upgrade(Request $request, PromotedListing $promotedListing)
    {
        if ($promotedListing->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'new_promotion_type' => 'required|in:premium,spotlight',
            'additional_budget' => 'required|numeric|min:1'
        ]);

        $promotedListing->update([
            'promotion_type' => $request->new_promotion_type,
            'total_budget' => $promotedListing->total_budget + $request->additional_budget,
            'remaining_budget' => $promotedListing->remaining_budget + $request->additional_budget,
            'priority_level' => $request->new_promotion_type === 'spotlight' ? 10 : 7
        ]);

        return back()->with('success', 'تم ترقية مستوى الترويج بنجاح');
    }

    public function analytics(PromotedListing $promotedListing)
    {
        if ($promotedListing->user_id !== Auth::id() && !Auth::user()->role === 'admin') {
            abort(403);
        }

        $analytics = [
            'overview' => $this->getPromotedOverview($promotedListing),
            'performance' => $this->getPromotedPerformance($promotedListing),
            'audience' => $this->getPromotedAudience($promotedListing),
            'comparison' => $this->getPromotedComparison($promotedListing),
            'roi' => $this->getPromotedROI($promotedListing)
        ];

        return view('ads.promoted-analytics', compact('promotedListing', 'analytics'));
    }

    public function featuredListings()
    {
        $featuredListings = PromotedListing::with(['property', 'property.images'])
            ->where('promotion_type', 'featured')
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->orderBy('priority_level', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get();

        return view('ads.featured-listings', compact('featuredListings'));
    }

    public function spotlightListings()
    {
        $spotlightListings = PromotedListing::with(['property', 'property.images'])
            ->where('promotion_type', 'spotlight')
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->orderBy('priority_level', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('ads.spotlight-listings', compact('spotlightListings'));
    }

    public function trackView(PromotedListing $promotedListing, Request $request)
    {
        // Track view for promoted listing
        DB::table('promoted_listing_views')->insert([
            'promoted_listing_id' => $promotedListing->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'viewed_at' => now()
        ]);

        if (Auth::check()) {
            DB::table('promoted_listing_views')
                ->where('promoted_listing_id', $promotedListing->id)
                ->where('ip_address', $request->ip())
                ->update(['user_id' => Auth::id()]);
        }

        // Update view count
        $promotedListing->increment('views_count');

        return response()->json(['success' => true]);
    }

    public function trackInquiry(PromotedListing $promotedListing, Request $request)
    {
        // Track inquiry for promoted listing
        DB::table('promoted_listing_inquiries')->insert([
            'promoted_listing_id' => $promotedListing->id,
            'user_id' => Auth::id() ?? null,
            'contact_info' => $request->contact_info,
            'message' => $request->message,
            'inquiry_type' => $request->inquiry_type ?? 'general',
            'created_at' => now()
        ]);

        // Update inquiry count
        $promotedListing->increment('inquiries_count');

        return back()->with('success', 'تم إرسال الاستفسار بنجاح');
    }

    private function getPromotedViews($promotedListing)
    {
        return DB::table('promoted_listing_views')
            ->where('promoted_listing_id', $promotedListing->id)
            ->count();
    }

    private function getPromotedClicks($promotedListing)
    {
        if ($promotedListing->advertisement) {
            return $promotedListing->advertisement->clicks_count;
        }
        return 0;
    }

    private function getPromotedInquiries($promotedListing)
    {
        return DB::table('promoted_listing_inquiries')
            ->where('promoted_listing_id', $promotedListing->id)
            ->count();
    }

    private function calculatePromotedCTR($promotedListing)
    {
        $views = $this->getPromotedViews($promotedListing);
        $clicks = $this->getPromotedClicks($promotedListing);
        
        return $views > 0 ? ($clicks / $views) * 100 : 0;
    }

    private function calculateCostPerView($promotedListing)
    {
        $views = $this->getPromotedViews($promotedListing);
        $spent = $promotedListing->total_spent;
        
        return $views > 0 ? $spent / $views : 0;
    }

    private function calculateCostPerInquiry($promotedListing)
    {
        $inquiries = $this->getPromotedInquiries($promotedListing);
        $spent = $promotedListing->total_spent;
        
        return $inquiries > 0 ? $spent / $inquiries : 0;
    }

    private function getDailyPerformance($promotedListing)
    {
        return DB::table('promoted_listing_views')
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as views')
            ->where('promoted_listing_id', $promotedListing->id)
            ->whereDate('viewed_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getPromotedOverview($promotedListing) { return []; }
    private function getPromotedPerformance($promotedListing) { return []; }
    private function getPromotedAudience($promotedListing) { return []; }
    private function getPromotedComparison($promotedListing) { return []; }
    private function getPromotedROI($promotedListing) { return []; }
}
