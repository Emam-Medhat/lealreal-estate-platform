<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Rating;
use App\Models\ReviewResponse;
use App\Models\ReviewVote;
use App\Models\Property;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewAnalyticsController extends Controller
{
    public function dashboard()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('reviews.analytics.dashboard');
    }

    public function getOverviewStats()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $stats = [
            'total_reviews' => Review::count(),
            'approved_reviews' => Review::where('status', 'approved')->count(),
            'pending_reviews' => Review::where('status', 'pending')->count(),
            'average_rating' => Review::where('status', 'approved')->avg('rating'),
            'total_ratings' => Rating::count(),
            'total_responses' => ReviewResponse::count(),
            'total_votes' => ReviewVote::count(),
            'response_rate' => Review::whereHas('responses')->count() / Review::where('status', 'approved')->count() * 100
        ];

        return response()->json($stats);
    }

    public function getRatingTrends(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $period = $request->get('period', '30'); // days
        $startDate = now()->subDays($period);

        $trends = Review::where('status', 'approved')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, AVG(rating) as average_rating, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($trends);
    }

    public function getRatingDistribution()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $distribution = Review::where('status', 'approved')
            ->selectRaw('rating, COUNT(*) as count, (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM reviews WHERE status = "approved")) as percentage')
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();

        return response()->json($distribution);
    }

    public function getReviewsByType()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $byType = Review::where('status', 'approved')
            ->selectRaw('reviewable_type, COUNT(*) as count, AVG(rating) as average_rating')
            ->groupBy('reviewable_type')
            ->get();

        return response()->json($byType);
    }

    public function getTopRatedProperties($limit = 10)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $properties = Property::withCount(['reviews' => function($query) {
                $query->where('status', 'approved');
            }])
            ->withAvg(['reviews' => function($query) {
                $query->where('status', 'approved');
            }], 'rating')
            ->whereHas('reviews', function($query) {
                $query->where('status', 'approved');
            })
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('reviews_count')
            ->take($limit)
            ->get();

        return response()->json($properties);
    }

    public function getTopRatedAgents($limit = 10)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $agents = Agent::withCount(['reviews' => function($query) {
                $query->where('status', 'approved');
            }])
            ->withAvg(['reviews' => function($query) {
                $query->where('status', 'approved');
            }], 'rating')
            ->whereHas('reviews', function($query) {
                $query->where('status', 'approved');
            })
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('reviews_count')
            ->take($limit)
            ->get();

        return response()->json($agents);
    }

    public function getReviewActivity(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $period = $request->get('period', '30'); // days
        $startDate = now()->subDays($period);

        $activity = Review::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved, SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($activity);
    }

    public function getResponseAnalytics()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $analytics = [
            'total_responses' => ReviewResponse::count(),
            'average_response_time' => DB::table('review_responses')
                ->join('reviews', 'review_responses.review_id', '=', 'reviews.id')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, reviews.created_at, review_responses.created_at)) as avg_hours')
                ->first(),
            'response_rate_by_type' => Review::where('status', 'approved')
                ->leftJoin('review_responses', 'reviews.id', '=', 'review_responses.review_id')
                ->selectRaw('reviewable_type, COUNT(*) as total_reviews, COUNT(review_responses.id) as responses, (COUNT(review_responses.id) * 100.0 / COUNT(*)) as response_rate')
                ->groupBy('reviewable_type')
                ->get(),
            'most_responsive_types' => Review::where('status', 'approved')
                ->leftJoin('review_responses', 'reviews.id', '=', 'review_responses.review_id')
                ->selectRaw('reviewable_type, COUNT(review_responses.id) as response_count')
                ->groupBy('reviewable_type')
                ->orderByDesc('response_count')
                ->get()
        ];

        return response()->json($analytics);
    }

    public function getVotingAnalytics()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $analytics = [
            'total_votes' => ReviewVote::count(),
            'helpful_votes' => ReviewVote::where('vote_type', 'helpful')->count(),
            'not_helpful_votes' => ReviewVote::where('vote_type', 'not_helpful')->count(),
            'vote_distribution' => ReviewVote::selectRaw('vote_type, COUNT(*) as count')
                ->groupBy('vote_type')
                ->get(),
            'most_voted_reviews' => Review::withCount(['votes' => function($query) {
                    $query->where('vote_type', 'helpful');
                }])
                ->where('status', 'approved')
                ->orderByDesc('votes_count')
                ->take(10)
                ->get()
        ];

        return response()->json($analytics);
    }

    public function getSentimentAnalysis()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        // This would integrate with AI sentiment analysis
        $sentiment = [
            'positive' => Review::where('sentiment', 'positive')->count(),
            'negative' => Review::where('sentiment', 'negative')->count(),
            'neutral' => Review::where('sentiment', 'neutral')->count(),
            'by_rating' => Review::where('status', 'approved')
                ->selectRaw('rating, sentiment, COUNT(*) as count')
                ->groupBy('rating', 'sentiment')
                ->get(),
            'sentiment_trend' => Review::where('created_at', '>=', now()->subDays(30))
                ->selectRaw('DATE(created_at) as date, sentiment, COUNT(*) as count')
                ->groupBy('date', 'sentiment')
                ->orderBy('date')
                ->get()
        ];

        return response()->json($sentiment);
    }

    public function getKeywordAnalysis()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        // Extract common keywords from review content
        $keywords = Review::where('status', 'approved')
            ->selectRaw('content')
            ->get()
            ->flatMap(function($review) {
                return $this->extractKeywords($review->content);
            })
            ->countBy()
            ->sortDesc()
            ->take(20);

        return response()->json($keywords);
    }

    public function getReviewerAnalytics()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $analytics = [
            'total_reviewers' => Review::distinct('user_id')->count(),
            'active_reviewers' => Review::where('created_at', '>=', now()->subDays(30))
                ->distinct('user_id')
                ->count(),
            'top_reviewers' => Review::with('user')
                ->selectRaw('user_id, COUNT(*) as review_count, AVG(rating) as avg_rating')
                ->groupBy('user_id')
                ->orderByDesc('review_count')
                ->take(10)
                ->get(),
            'review_frequency' => Review::selectRaw('user_id, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(90))
                ->groupBy('user_id')
                ->selectRaw('COUNT(*) as frequency')
                ->get()
        ];

        return response()->json($analytics);
    }

    public function exportAnalytics(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'type' => 'required|in:overview,trends,distribution,responses,votes',
            'format' => 'required|in:csv,xlsx',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from'
        ]);

        // Export logic here
        return response()->json([
            'message' => 'Export functionality to be implemented',
            'type' => $request->type,
            'format' => $request->format
        ]);
    }

    public function getCustomReport(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'rating_min' => 'nullable|integer|min:1|max:5',
            'rating_max' => 'nullable|integer|min:1|max:5|gte:rating_min',
            'reviewable_type' => 'nullable|string|in:property,agent,user',
            'status' => 'nullable|array',
            'include_responses' => 'boolean',
            'include_votes' => 'boolean'
        ]);

        $query = Review::with(['user', 'reviewable']);

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->rating_min) {
            $query->where('rating', '>=', $request->rating_min);
        }

        if ($request->rating_max) {
            $query->where('rating', '<=', $request->rating_max);
        }

        if ($request->reviewable_type) {
            $query->where('reviewable_type', 'like', "%{$request->reviewable_type}%");
        }

        if ($request->status) {
            $query->whereIn('status', $request->status);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(50);

        $summary = [
            'total_reviews' => $reviews->total(),
            'average_rating' => $query->avg('rating'),
            'rating_distribution' => $query->selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->get(),
            'by_type' => $query->selectRaw('reviewable_type, COUNT(*) as count')
                ->groupBy('reviewable_type')
                ->get()
        ];

        return response()->json([
            'reviews' => $reviews,
            'summary' => $summary
        ]);
    }

    private function extractKeywords($text)
    {
        // Simple keyword extraction - in production, use NLP library
        $stopWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'is', 'are', 'was', 'were', 'been', 'be'];
        
        $words = str_word_count(strtolower($text), 1);
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return !in_array($word, $stopWords) && strlen($word) > 3;
        });

        return array_values($keywords);
    }
}
