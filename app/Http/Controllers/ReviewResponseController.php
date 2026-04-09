<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewResponse;
use App\Http\Requests\RespondToReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewResponseController extends Controller
{
    public function store(RespondToReviewRequest $request, Review $review)
    {
        // Check if user can respond to this review
        if (!$this->canRespondToReview($review)) {
            return back()->with('error', 'غير مصرح لك بالرد على هذا التقييم');
        }

        DB::beginTransaction();
        
        try {
            $response = $review->responses()->create([
                'user_id' => Auth::id(),
                'content' => $request->content,
                'is_official' => $this->isOfficialResponse($review),
                'status' => 'published'
            ]);

            // Update review status to indicate it has been responded to
            if ($review->status === 'approved') {
                $review->update(['has_response' => true]);
            }

            // Notify the review author about the response
            $this->notifyReviewAuthor($review, $response);

            DB::commit();

            return back()->with('success', 'تم إضافة الرد بنجاح');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة الرد: ' . $e->getMessage());
        }
    }

    public function update(Request $request, ReviewResponse $response)
    {
        // Check if user can edit this response
        if ($response->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|min:5'
        ]);

        $response->update([
            'content' => $request->content,
            'edited_at' => now()
        ]);

        return back()->with('success', 'تم تحديث الرد بنجاح');
    }

    public function destroy(ReviewResponse $response)
    {
        // Check if user can delete this response
        if ($response->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $review = $response->review;
        $response->delete();

        // Update review response status
        $hasOtherResponses = $review->responses()->count() > 0;
        $review->update(['has_response' => $hasOtherResponses]);

        return back()->with('success', 'تم حذف الرد بنجاح');
    }

    public function markHelpful(ReviewResponse $response)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'يجب تسجيل الدخول'], 401);
        }

        $existingVote = $response->votes()
            ->where('user_id', Auth::id())
            ->where('vote_type', 'helpful')
            ->first();

        if ($existingVote) {
            $existingVote->delete();
            $helpfulCount = $response->votes()->where('vote_type', 'helpful')->count();
            return response()->json(['helpful' => $helpfulCount, 'voted' => false]);
        } else {
            $response->votes()->create([
                'user_id' => Auth::id(),
                'vote_type' => 'helpful'
            ]);
        }

        $helpfulCount = $response->votes()->where('vote_type', 'helpful')->count();
        return response()->json(['helpful' => $helpfulCount, 'voted' => true]);
    }

    public function report(Request $request, ReviewResponse $response)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        $response->flags()->create([
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'description' => $request->description
        ]);

        return back()->with('success', 'تم الإبلاغ عن الرد بنجاح');
    }

    private function canRespondToReview(Review $review)
    {
        $user = Auth::user();

        // Admin can respond to any review
        if ($user->isAdmin()) {
            return true;
        }

        // Owner of the reviewed item can respond
        if ($review->reviewable_type === 'App\Models\Property') {
            return $review->reviewable->user_id === $user->id;
        }

        if ($review->reviewable_type === 'App\Models\Agent') {
            return $review->reviewable->user_id === $user->id;
        }

        if ($review->reviewable_type === 'App\Models\User') {
            return $review->reviewable_id === $user->id;
        }

        return false;
    }

    private function isOfficialResponse(Review $review)
    {
        $user = Auth::user();

        // Admin responses are official
        if ($user->isAdmin()) {
            return true;
        }

        // Owner responses are official
        if ($review->reviewable_type === 'App\Models\Property') {
            return $review->reviewable->user_id === $user->id;
        }

        if ($review->reviewable_type === 'App\Models\Agent') {
            return $review->reviewable->user_id === $user->id;
        }

        return false;
    }

    private function notifyReviewAuthor(Review $review, ReviewResponse $response)
    {
        // Create notification for the review author
        $review->user->notifications()->create([
            'type' => 'review_response',
            'title' => 'تم الرد على تقييمك',
            'message' => "قام {$response->user->name} بالرد على تقييمك",
            'data' => [
                'review_id' => $review->id,
                'response_id' => $response->id
            ]
        ]);
    }

    public function getResponses(Review $review)
    {
        $responses = $review->responses()
            ->with(['user', 'votes'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($responses);
    }
}
