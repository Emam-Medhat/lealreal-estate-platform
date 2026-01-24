<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReviewVerificationController extends Controller
{
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $verifications = ReviewVerification::with(['review.user', 'review.reviewable', 'verifiedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reviews.verification.index', compact('verifications'));
    }

    public function verify(Review $review)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($review->is_verified) {
            return back()->with('error', 'هذا التقييم موثق بالفعل');
        }

        DB::beginTransaction();
        
        try {
            // Create verification record
            $verification = ReviewVerification::create([
                'review_id' => $review->id,
                'verified_by' => Auth::id(),
                'verification_method' => 'manual',
                'verification_status' => 'verified',
                'verified_at' => now(),
                'notes' => 'تم التحقق يدوياً من صحة التقييم'
            ]);

            // Mark review as verified
            $review->update([
                'is_verified' => true,
                'verified_at' => now(),
                'verified_by' => Auth::id()
            ]);

            // Notify reviewer
            $review->user->notifications()->create([
                'type' => 'review_verified',
                'title' => 'تم توثيق تقييمك',
                'message' => 'تم توثيق تقييمك بنجاح',
                'data' => ['review_id' => $review->id]
            ]);

            DB::commit();

            return back()->with('success', 'تم توثيق التقييم بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء توثيق التقييم');
        }
    }

    public function requestVerification(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        if ($review->is_verified) {
            return back()->with('info', 'تقييمك موثق بالفعل');
        }

        // Check if verification request already exists
        $existingRequest = ReviewVerification::where('review_id', $review->id)
            ->where('verification_status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->with('info', 'تم إرسال طلب التوثيق بالفعل');
        }

        DB::beginTransaction();
        
        try {
            $verification = ReviewVerification::create([
                'review_id' => $review->id,
                'verification_method' => 'user_request',
                'verification_status' => 'pending',
                'notes' => 'طلب توثيق من صاحب التقييم'
            ]);

            // Notify admins
            User::where('role', 'admin')->get()->each(function($admin) use ($review) {
                $admin->notifications()->create([
                    'type' => 'verification_request',
                    'title' => 'طلب توثيق تقييم',
                    'message' => "تم طلب توثيق التقييم: {$review->title}",
                    'data' => ['review_id' => $review->id, 'verification_id' => $verification->id]
                ]);
            });

            DB::commit();

            return back()->with('success', 'تم إرسال طلب التوثيق بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء إرسال طلب التوثيق');
        }
    }

    public function approveVerification(ReviewVerification $verification)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($verification->verification_status !== 'pending') {
            return back()->with('error', 'طلب التوثيق ليس في انتظار الموافقة');
        }

        DB::beginTransaction();
        
        try {
            $verification->update([
                'verification_status' => 'approved',
                'verified_by' => Auth::id(),
                'verified_at' => now()
            ]);

            // Mark review as verified
            $verification->review->update([
                'is_verified' => true,
                'verified_at' => now(),
                'verified_by' => Auth::id()
            ]);

            // Notify reviewer
            $verification->review->user->notifications()->create([
                'type' => 'verification_approved',
                'title' => 'تم قبول طلب التوثيق',
                'message' => 'تم قبول طلب توثيق تقييمك',
                'data' => ['review_id' => $verification->review_id]
            ]);

            DB::commit();

            return back()->with('success', 'تم قبول طلب التوثيق بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء قبول طلب التوثيق');
        }
    }

    public function rejectVerification(Request $request, ReviewVerification $verification)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500'
        ]);

        if ($verification->verification_status !== 'pending') {
            return back()->with('error', 'طلب التوثيق ليس في انتظار الموافقة');
        }

        DB::beginTransaction();
        
        try {
            $verification->update([
                'verification_status' => 'rejected',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'rejection_reason' => $request->rejection_reason
            ]);

            // Notify reviewer
            $verification->review->user->notifications()->create([
                'type' => 'verification_rejected',
                'title' => 'تم رفض طلب التوثيق',
                'message' => 'تم رفض طلب توثيق تقييمك: ' . $request->rejection_reason,
                'data' => ['review_id' => $verification->review_id]
            ]);

            DB::commit();

            return back()->with('success', 'تم رفض طلب التوثيق بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء رفض طلب التوثيق');
        }
    }

    public function bulkVerify(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id',
            'verification_method' => 'required|string',
            'notes' => 'nullable|string|max:1000'
        ]);

        $reviews = Review::whereIn('id', $request->review_ids)
            ->where('is_verified', false)
            ->get();

        $verifiedCount = 0;

        foreach ($reviews as $review) {
            ReviewVerification::create([
                'review_id' => $review->id,
                'verified_by' => Auth::id(),
                'verification_method' => $request->verification_method,
                'verification_status' => 'verified',
                'verified_at' => now(),
                'notes' => $request->notes
            ]);

            $review->update([
                'is_verified' => true,
                'verified_at' => now(),
                'verified_by' => Auth::id()
            ]);

            $verifiedCount++;
        }

        return back()->with('success', "تم توثيق {$verifiedCount} من التقييمات بنجاح");
    }

    public function autoVerify(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'criteria' => 'required|array',
            'criteria.min_review_count' => 'nullable|integer|min:1',
            'criteria.min_account_age' => 'nullable|integer|min:1', // days
            'criteria.required_fields' => 'nullable|array',
            'criteria.exclude_anonymous' => 'boolean'
        ]);

        $query = Review::where('is_verified', false)
            ->where('status', 'approved');

        // Apply criteria
        if ($request->criteria['min_review_count'] ?? null) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('review_count', '>=', $request->criteria['min_review_count']);
            });
        }

        if ($request->criteria['min_account_age'] ?? null) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('created_at', '<=', now()->subDays($request->criteria['min_account_age']));
            });
        }

        if ($request->criteria['exclude_anonymous'] ?? false) {
            $query->where('is_anonymous', false);
        }

        $reviews = $query->get();
        $verifiedCount = 0;

        foreach ($reviews as $review) {
            // Check required fields
            $hasRequiredFields = true;
            if ($request->criteria['required_fields'] ?? null) {
                foreach ($request->criteria['required_fields'] as $field) {
                    if (empty($review->$field)) {
                        $hasRequiredFields = false;
                        break;
                    }
                }
            }

            if ($hasRequiredFields) {
                ReviewVerification::create([
                    'review_id' => $review->id,
                    'verified_by' => Auth::id(),
                    'verification_method' => 'auto',
                    'verification_status' => 'verified',
                    'verified_at' => now(),
                    'notes' => 'تم التوثيق التلقائي بناءً على المعايير المحددة'
                ]);

                $review->update([
                    'is_verified' => true,
                    'verified_at' => now(),
                    'verified_by' => Auth::id()
                ]);

                $verifiedCount++;
            }
        }

        return back()->with('success', "تم توثيق {$verifiedCount} من التقييمات تلقائياً");
    }

    public function getVerificationStats()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $stats = [
            'total_reviews' => Review::count(),
            'verified_reviews' => Review::where('is_verified', true)->count(),
            'unverified_reviews' => Review::where('is_verified', false)->count(),
            'verification_rate' => Review::where('is_verified', true)->count() / Review::count() * 100,
            'pending_requests' => ReviewVerification::where('verification_status', 'pending')->count(),
            'by_method' => ReviewVerification::selectRaw('verification_method, COUNT(*) as count')
                ->groupBy('verification_method')
                ->get(),
            'by_status' => ReviewVerification::selectRaw('verification_status, COUNT(*) as count')
                ->groupBy('verification_status')
                ->get(),
            'recent_verifications' => ReviewVerification::where('created_at', '>=', now()->subDays(30))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
        ];

        return response()->json($stats);
    }

    public function exportVerifications(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'format' => 'required|in:csv,xlsx',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'verification_status' => 'nullable|array',
            'verification_method' => 'nullable|array'
        ]);

        $query = ReviewVerification::with(['review.user', 'review.reviewable', 'verifiedBy']);

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->verification_status) {
            $query->whereIn('verification_status', $request->verification_status);
        }

        if ($request->verification_method) {
            $query->whereIn('verification_method', $request->verification_method);
        }

        $verifications = $query->get();

        // Export logic here
        return response()->json([
            'message' => 'Export functionality to be implemented',
            'format' => $request->format,
            'count' => $verifications->count()
        ]);
    }
}
