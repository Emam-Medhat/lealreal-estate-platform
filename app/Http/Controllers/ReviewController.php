<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Property;
use App\Models\Agent;
use App\Models\User;
use App\Http\Requests\StoreReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'reviewable', 'responses'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reviews.index', compact('reviews'));
    }

    public function create($type, $id)
    {
        $reviewable = $this->getReviewableModel($type, $id);
        
        if (!$reviewable) {
            abort(404);
        }

        // Check if user already reviewed this item
        $existingReview = Review::where('user_id', Auth::id())
            ->where('reviewable_type', get_class($reviewable))
            ->where('reviewable_id', $reviewable->id)
            ->first();

        if ($existingReview) {
            return redirect()->route('reviews.show', $existingReview->id)
                ->with('info', 'لقد قمت بتقييم هذا العنصر بالفعل');
        }

        return view('reviews.create', compact('reviewable', 'type'));
    }

    public function store(StoreReviewRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $reviewable = $this->getReviewableModel($request->type, $request->reviewable_id);
            
            $review = Review::create([
                'user_id' => Auth::id(),
                'reviewable_type' => get_class($reviewable),
                'reviewable_id' => $reviewable->id,
                'title' => $request->title,
                'content' => $request->content,
                'rating' => $request->rating,
                'pros' => $request->pros,
                'cons' => $request->cons,
                'recommendation' => $request->recommendation,
                'is_verified' => false,
                'is_anonymous' => $request->has('is_anonymous'),
                'status' => 'pending'
            ]);

            // Update average rating for the reviewable item
            $this->updateAverageRating($reviewable);

            DB::commit();

            return redirect()->route('reviews.show', $review->id)
                ->with('success', 'تم إضافة التقييم بنجاح وجاري مراجعته');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة التقييم: ' . $e->getMessage());
        }
    }

    public function show(Review $review)
    {
        $review->load(['user', 'reviewable', 'responses.user', 'votes']);
        
        // Check if current user voted on this review
        $userVote = null;
        if (Auth::check()) {
            $userVote = $review->votes()
                ->where('user_id', Auth::id())
                ->first();
        }

        return view('reviews.show', compact('review', 'userVote'));
    }

    public function myReviews()
    {
        $reviews = Review::where('user_id', Auth::id())
            ->with(['reviewable', 'responses'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reviews.my-reviews', compact('reviews'));
    }

    public function edit(Review $review)
    {
        if ($review->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        return view('reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        if ($review->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:5',
            'pros' => 'nullable|string',
            'cons' => 'nullable|string',
            'recommendation' => 'nullable|boolean'
        ]);

        $review->update([
            'title' => $request->title,
            'content' => $request->content,
            'rating' => $request->rating,
            'pros' => $request->pros,
            'cons' => $request->cons,
            'recommendation' => $request->recommendation,
            'is_anonymous' => $request->has('is_anonymous')
        ]);

        // Update average rating
        $this->updateAverageRating($review->reviewable);

        return redirect()->route('reviews.show', $review->id)
            ->with('success', 'تم تحديث التقييم بنجاح');
    }

    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $reviewable = $review->reviewable;
        $review->delete();

        // Update average rating
        $this->updateAverageRating($reviewable);

        return redirect()->route('reviews.my-reviews')
            ->with('success', 'تم حذف التقييم بنجاح');
    }

    public function helpful(Review $review)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'يجب تسجيل الدخول'], 401);
        }

        $existingVote = $review->votes()
            ->where('user_id', Auth::id())
            ->first();

        if ($existingVote) {
            if ($existingVote->vote_type === 'helpful') {
                $existingVote->delete();
                $helpfulCount = $review->votes()->where('vote_type', 'helpful')->count();
                return response()->json(['helpful' => $helpfulCount, 'voted' => false]);
            } else {
                $existingVote->update(['vote_type' => 'helpful']);
            }
        } else {
            $review->votes()->create([
                'user_id' => Auth::id(),
                'vote_type' => 'helpful'
            ]);
        }

        $helpfulCount = $review->votes()->where('vote_type', 'helpful')->count();
        return response()->json(['helpful' => $helpfulCount, 'voted' => true]);
    }

    public function notHelpful(Review $review)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'يجب تسجيل الدخول'], 401);
        }

        $existingVote = $review->votes()
            ->where('user_id', Auth::id())
            ->first();

        if ($existingVote) {
            if ($existingVote->vote_type === 'not_helpful') {
                $existingVote->delete();
                $notHelpfulCount = $review->votes()->where('vote_type', 'not_helpful')->count();
                return response()->json(['not_helpful' => $notHelpfulCount, 'voted' => false]);
            } else {
                $existingVote->update(['vote_type' => 'not_helpful']);
            }
        } else {
            $review->votes()->create([
                'user_id' => Auth::id(),
                'vote_type' => 'not_helpful'
            ]);
        }

        $notHelpfulCount = $review->votes()->where('vote_type', 'not_helpful')->count();
        return response()->json(['not_helpful' => $notHelpfulCount, 'voted' => true]);
    }

    public function report(Request $request, Review $review)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        $review->flags()->create([
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'description' => $request->description
        ]);

        return back()->with('success', 'تم الإبلاغ عن التقييم بنجاح');
    }

    private function getReviewableModel($type, $id)
    {
        switch ($type) {
            case 'property':
                return Property::findOrFail($id);
            case 'agent':
                return Agent::findOrFail($id);
            case 'user':
                return User::findOrFail($id);
            default:
                return null;
        }
    }

    private function updateAverageRating($reviewable)
    {
        $averageRating = $reviewable->reviews()
            ->where('status', 'approved')
            ->avg('rating');

        $reviewable->update(['average_rating' => $averageRating]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $reviews = Review::with(['user', 'reviewable'])
            ->where('status', 'approved')
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reviews.search', compact('reviews', 'query'));
    }
}
