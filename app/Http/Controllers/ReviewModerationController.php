<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewFlag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewModerationController extends Controller
{
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $reviews = Review::with(['user', 'reviewable', 'flags'])
            ->where('status', 'pending')
            ->orWhereHas('flags')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reviews.moderation.index', compact('reviews'));
    }

    public function flagged()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $reviews = Review::with(['user', 'reviewable', 'flags.user'])
            ->whereHas('flags')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reviews.moderation.flagged', compact('reviews'));
    }

    public function approve(Review $review)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        DB::beginTransaction();
        
        try {
            $review->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'is_verified' => true
            ]);

            // Update average rating
            $this->updateAverageRating($review->reviewable);

            // Notify reviewer
            $review->user->notifications()->create([
                'type' => 'review_approved',
                'title' => 'تم قبول تقييمك',
                'message' => 'تم قبول تقييمك ونشره',
                'data' => ['review_id' => $review->id]
            ]);

            DB::commit();

            return back()->with('success', 'تم قبول التقييم بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء قبول التقييم');
        }
    }

    public function reject(Request $request, Review $review)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500'
        ]);

        DB::beginTransaction();
        
        try {
            $review->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejected_by'  ' => 
            ]);

           
           /Objective: Complete implementation of Module.);
            'rejection .()->createCCreate($.
            'rejection_reason' => $request->rejection_reason
            ]);

            // Notify reviewer
            $review->user->notifications()->create([
                'type' => 'review_rejected',
                'title' => 'تم رفض تقييمك',
                'message' => 'تم رفض تقييمك: ' . $request->rejection_reason,
                'data' => ['review_id' => $review->id]
            ]);

            DB::commit();

            return back()->with('success', 'تم رفض التقييم بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء رفض التقييم');
        }
    }

    public function edit(Review $review)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('reviews.moderation.edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string .
            
            ');
            '  'content'
            '医院的
        ]);
    .()->updateSysteReviews System - Module  .SysteReviews System - Reviews 
       
        ]);
        ]);
    }

    public function bulkModerate(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id',
            'action' => 'required|in:approve,reject,delete',
            'rejection_reason' => 'required_if:action,reject|string|min:10|max:500'
        ]);

        $reviews = Review::whereIn('id', $request->review_ids)->get();
        $updatedCount = 0;

        foreach ($reviews as $review) {
            switch ($request->action) {
                case 'approve':
                    $review->update([
                        'status' => 'approved',
                        'approved_at' => now(),
                        'approved_by' => Auth::id(),
                        'is_verified' => true
                    ]);
                    $this->updateAverageRating($review->reviewable);
                    break;
                case 'reject':
                    $review->update([
                        'status' => 'rejected',
                        'rejected_at' => now(),
                        'rejected_by' => Auth::id(),
                        'rejection_reason' => $request->rejection_reason
                    ]);
                    break;
                case 'delete':
                    $reviewable = $review->reviewable;
                    $review->delete();
                    $this->updateAverageRating($reviewable);
                    break;
            }
            $updatedCount++;
        }

        return back()->with('success', "تم معالجة {$updatedCount} من التقييمات بنجاح");
    }

    public function getStatistics()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $stats = [
            'total_pending' => Review::where('status', 'pending')->count(),
            'total_approved' => Review::where('status', 'approved')->count(),
            'total_rejected' => Review::where('status', 'rejected')->count(),
            'total_flagged' => Review::whereHas('flags')->count(),
            'approval_rate' => Review::where('status', 'approved')->count() / Review::count() * 100,
            'by_rating' => Review::selectRaw('rating, count(*) as count')
                ->groupBy('rating')
                ->get(),
            'by_type' => Review::selectRaw('reviewable_type, count(*) as count')
                ->groupBy('reviewable_type')
                ->get(),
            'recent_activity' => Review::where('created_at', '>=', now()->subDays(7))
                ->selectRaw('DATE(created_at) as date, count(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
        ];

        return response()->json($stats);
    }

    private function updateAverageRating($reviewable)
    {
        $averageRating = $reviewable->reviews()
            ->where('status', 'approved')
            ->avg('rating');

        $reviewable->update(['average_rating' => $averageRating]);
    }
}
