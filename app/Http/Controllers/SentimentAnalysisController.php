<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewSentimentAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SentimentAnalysisController extends Controller
{
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('reviews.sentiment.index');
    }

    public function analyzeReview(Review $review)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        // Perform sentiment analysis
        $sentiment = $this->performSentimentAnalysis($review->content);

        // Save analysis results
        ReviewSentimentAnalysis::updateOrCreate(
            ['review_id' => $review->id],
            [
                'sentiment' => $sentiment['sentiment'],
                'confidence' => $sentiment['confidence'],
                'positive_score' => $sentiment['positive_score'],
                'negative_score' => $sentiment['negative_score'],
                'neutral_score' => $sentiment['neutral_score'],
                'keywords' => $sentiment['keywords'],
                'emotions' => $sentiment['emotions'],
                'analyzed_at' => now()
            ]
        );

        // Update review sentiment
        $review->update(['sentiment' => $sentiment['sentiment']]);

        return back()->with('success', 'تم تحليل المشاعر بنجاح');
    }

    public function batchAnalyze(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id'
        ]);

        $reviews = Review::whereIn('id', $request->review_ids)->get();
        $analyzedCount = 0;

        foreach ($reviews as $review) {
            $sentiment = $this->performSentimentAnalysis($review->content);

            ReviewSentimentAnalysis::updateOrCreate(
                ['review_id' => $review->id],
                [
                    'sentiment' => $sentiment['sentiment'],
                    'confidence' => $sentiment['confidence'],
                    'positive_score' => $sentiment['positive_score'],
                    'negative_score' => $sentiment['negative_score'],
                    'neutral_score' => $sentiment['neutral_score'],
                    'keywords' => $sentiment['keywords'],
                    'emotions' => $sentiment['emotions'],
                    'analyzed_at' => now()
                ]
            );

            $review->update(['sentiment' => $sentiment['sentiment']]);
            $analyzedCount++;
        }

        return back()->with('success', "تم تحليل {$analyzedCount} من التقييمات بنجاح");
    }

    public function analyzeAll()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $unanalyzedReviews = Review::whereDoesntHave('sentimentAnalysis')
            ->where('status', 'approved')
            ->get();

        $analyzedCount = 0;

        foreach ($unanalyzedReviews as $review) {
            $sentiment = $this->performSentimentAnalysis($review->content);

            ReviewSentimentAnalysis::create([
                'review_id' => $review->id,
                'sentiment' => $sentiment['sentiment'],
                'confidence' => $sentiment['confidence'],
                'positive_score' => $sentiment['positive_score'],
                'negative_score' => $sentiment['negative_score'],
                'neutral_score' => $sentiment['neutral_score'],
                'keywords' => $sentiment['keywords'],
                'emotions' => $sentiment['emotions'],
                'analyzed_at' => now()
            ]);

            $review->update(['sentiment' => $sentiment['sentiment']]);
            $analyzedCount++;
        }

        return back()->with('success', "تم تحليل {$analyzedCount} من التقييمات بنجاح");
    }

    public function getSentimentStats()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $stats = [
            'total_analyzed' => ReviewSentimentAnalysis::count(),
            'positive' => ReviewSentimentAnalysis::where('sentiment', 'positive')->count(),
            'negative' => ReviewSentimentAnalysis::where('sentiment', 'negative')->count(),
            'neutral' => ReviewSentimentAnalysis::where('sentiment', 'neutral')->count(),
            'average_confidence' => ReviewSentimentAnalysis::avg('confidence'),
            'by_rating' => Review::join('review_sentiment_analyses', 'reviews.id', '=', 'review_sentiment_analyses.review_id')
                ->selectRaw('reviews.rating, sentiment, COUNT(*) as count')
                ->groupBy('reviews.rating', 'sentiment')
                ->get(),
            'sentiment_trend' => ReviewSentimentAnalysis::where('analyzed_at', '>=', now()->subDays(30))
                ->selectRaw('DATE(analyzed_at) as date, sentiment, COUNT(*) as count')
                ->groupBy('date', 'sentiment')
                ->orderBy('date')
                ->get()
        ];

        return response()->json($stats);
    }

    public function getEmotionAnalysis()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $emotions = ReviewSentimentAnalysis::selectRaw('emotions')
            ->whereNotNull('emotions')
            ->get()
            ->flatMap(function ($analysis) {
                return $analysis->emotions ?? [];
            })
            ->countBy()
            ->sortDesc();

        return response()->json($emotions);
    }

    public function getKeywordAnalysis()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $keywords = ReviewSentimentAnalysis::selectRaw('keywords')
            ->whereNotNull('keywords')
            ->get()
            ->flatMap(function ($analysis) {
                return $analysis->keywords ?? [];
            })
            ->countBy()
            ->sortDesc()
            ->take(50);

        return response()->json($keywords);
    }

    public function getSentimentByRating()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $data = Review::join('review_sentiment_analyses', 'reviews.id', '=', 'review_sentiment_analyses.review_id')
            ->selectRaw('reviews.rating, sentiment, AVG(confidence) as avg_confidence, COUNT(*) as count')
            ->groupBy('reviews.rating', 'sentiment')
            ->orderBy('reviews.rating')
            ->get();

        return response()->json($data);
    }

    public function getSentimentByType()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $data = Review::join('review_sentiment_analyses', 'reviews.id', '=', 'review_sentiment_analyses.review_id')
            ->selectRaw('reviewable_type, sentiment, COUNT(*) as count')
            ->groupBy('reviewable_type', 'sentiment')
            ->get();

        return response()->json($data);
    }

    public function getNegativeReviews()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $reviews = Review::with(['user', 'reviewable'])
            ->where('sentiment', 'negative')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reviews.sentiment.negative', compact('reviews'));
    }

    public function getPositiveReviews()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $reviews = Review::with(['user', 'reviewable'])
            ->where('sentiment', 'positive')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reviews.sentiment.positive', compact('reviews'));
    }

    public function exportSentimentData(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'format' => 'required|in:csv,xlsx',
            'sentiment' => 'nullable|array',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'min_confidence' => 'nullable|numeric|min:0|max:1'
        ]);

        $query = Review::with(['user', 'reviewable', 'sentimentAnalysis'])
            ->whereHas('sentimentAnalysis');

        if ($request->sentiment) {
            $query->whereIn('sentiment', $request->sentiment);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->min_confidence) {
            $query->whereHas('sentimentAnalysis', function ($q) use ($request) {
                $q->where('confidence', '>=', $request->min_confidence);
            });
        }

        $reviews = $query->get();

        // Export logic here
        return response()->json([
            'message' => 'Export functionality to be implemented',
            'format' => $request->format,
            'count' => $reviews->count()
        ]);
    }

    private function performSentimentAnalysis($text)
    {
        // Simple sentiment analysis - in production, use AI service like OpenAI, Google Cloud NLP, etc.

        // Arabic positive words
        $positiveWords = ['ممتاز', 'رائع', 'جميل', 'خبر', 'مريح', 'نظيف', 'جيد', 'مفيد', 'سعيد', 'محبوب', 'مثالي', 'أفضل'];

        // Arabic negative words
        $negativeWords = ['سيء', 'فظيع', 'مخيب', 'سيء جدا', 'فاشل', 'سيء', 'غير جيد', 'مزعج', 'صعب', 'مشكلة', 'خطأ', 'سيء'];

        $text = strtolower($text);
        $words = explode(' ', $text);

        $positiveCount = 0;
        $negativeCount = 0;
        $neutralCount = count($words);

        foreach ($words as $word) {
            if (in_array($word, $positiveWords)) {
                $positiveCount++;
                $neutralCount--;
            }
        }

        $WEIGHTED_WORDS = [
            'ممتاز' => 2,
            'رائع' => 2,
            'جميل' => 1.5,
            'جيد' => 1,
            'سيء' => -1,
            'فظيع' => -2,
            'فاشل' => -2,
        ];

        $score = 0;
        $matchedWords = [];

        foreach ($words as $word) {
            if (isset($WEIGHTED_WORDS[$word])) {
                $score += $WEIGHTED_WORDS[$word];
                $matchedWords[] = $word;
            }
        }

        $totalWords = count($words);
        $normalizedScore = $totalWords > 0 ? $score / $totalWords : 0;

        if ($normalizedScore > 0.1) {
            $sentiment = 'positive';
        } elseif ($normalizedScore < -0.1) {
            $sentiment = 'negative';
        } else {
            $sentiment = 'neutral';
        }

        $confidence = min(abs($normalizedScore) * 2, 1);

        return [
            'sentiment' => $sentiment,
            'confidence' => $confidence,
            'positive_score' => max(0, $normalizedScore),
            'negative_score' => max(0, -$normalizedScore),
            'neutral_score' => 1 - $confidence,
            'keywords' => $matchedWords,
            'emotions' => $this->detectEmotions($text, $sentiment)
        ];
    }

    private function detectEmotions($text, $sentiment)
    {
        $emotions = [];

        // Simple emotion detection based on keywords
        if ($sentiment === 'positive') {
            if (str_contains($text, 'سعيد'))
                $emotions[] = 'joy';
            if (str_contains($text, 'ممتن'))
                $emotions[] = 'gratitude';
            if (str_contains($text, 'متحمس'))
                $emotions[] = 'excitement';
        } elseif ($sentiment === 'negative') {
            if (str_contains($text, 'غاضب'))
                $emotions[] = 'anger';
            if (str_contains($text, 'حزين'))
                $emotions[] = 'sadness';
            if (str_contains($text, 'قلق'))
                $emotions[] = 'fear';
        }

        return $emotions;
    }
}
