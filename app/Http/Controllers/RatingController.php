<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Property;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'ratingable_type' => 'required|string|in:property,agent,user',
            'ratingable_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'category' => 'nullable|string|max:50'
        ]);

        $ratingable = $this->getRatingableModel($request->ratingable_type, $request->ratingable_id);
        
        if (!$ratingable) {
            return response()->json(['error' => 'العنصر المطلوب تقييمه غير موجود'], 404);
        }

        // Check if user already rated
        $existingRating = Rating::where('user_id', Auth::id())
            ->where('ratingable_type', get_class($ratingable))
            ->where('ratingable_id', $ratingable->id)
            ->when($request->category, function($query, $category) {
                return $query->where('category', $category);
            })
            ->first();

        if ($existingRating) {
            // Update existing rating
            $existingRating->update([
                'rating' => $request->rating,
                'updated_at' => now()
            ]);

            $this->updateAverageRating($ratingable, $request->category);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث التقييم بنجاح',
                'rating' => $existingRating
            ]);
        } else {
            // Create new rating
            $rating = Rating::create([
                'user_id' => Auth::id(),
                'ratingable_type' => get_class($ratingable),
                'ratingable_id' => $ratingable->id,
                'rating' => $request->rating,
                'category' => $request->category,
                'ip_address' => $request->ip()
            ]);

            $this->updateAverageRating($ratingable, $request->category);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة التقييم بنجاح',
                'rating' => $rating
            ]);
        }
    }

    public function getRatings($type, $id, $category = null)
    {
        $ratingable = $this->getRatingableModel($type, $id);
        
        if (!$ratingable) {
            return response()->json(['error' => 'العنصر غير موجود'], 404);
        }

        $query = $ratingable->ratings()
            ->with('user');

        if ($category) {
            $query->where('category', $category);
        }

        $ratings = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        $statistics = $this->getRatingStatisticsPrivate($ratingable, $category);

        return response()->json([
            'ratings' => $ratings,
            'statistics' => $statistics,
            'user_rating' => Auth::check() ? $this->getUserRating($ratingable, $category) : null
        ]);
    }

    public function getRatingStatistics($type, $id, $category = null)
    {
        $ratingable = $this->getRatingableModel($type, $id);
        
        if (!$ratingable) {
            return response()->json(['error' => 'العنصر غير موجود'], 404);
        }

        $statistics = $this->getRatingStatisticsPrivate($ratingable, $category);

        return response()->json($statistics);
    }

    public function destroy(Rating $rating)
    {
        if ($rating->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $ratingable = $rating->ratingable;
        $category = $rating->category;
        
        $rating->delete();

        $this->updateAverageRating($ratingable, $category);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف التقييم بنجاح'
        ]);
    }

    public function bulkRate(Request $request)
    {
        $request->validate([
            'ratings' => 'required|array|min:1',
            'ratings.*.ratingable_type' => 'required|string|in:property,agent,user',
            'ratings.*.ratingable_id' => 'required|integer',
            'ratings.*.rating' => 'required|integer|min:1|max:5',
            'ratings.*.category' => 'nullable|string|max:50'
        ]);

        DB::beginTransaction();
        
        try {
            $results = [];
            
            foreach ($request->ratings as $ratingData) {
                $ratingable = $this->getRatingableModel($ratingData['ratingable_type'], $ratingData['ratingable_id']);
                
                if (!$ratingable) {
                    $results[] = ['error' => 'العنصر غير موجود', 'data' => $ratingData];
                    continue;
                }

                $existingRating = Rating::where('user_id', Auth::id())
                    ->where('ratingable_type', get_class($ratingable))
                    ->where('ratingable_id', $ratingable->id)
                    ->when($ratingData['category'] ?? null, function($query, $category) {
                        return $query->where('category', $category);
                    })
                    ->first();

                if ($existingRating) {
                    $existingRating->update([
                        'rating' => $ratingData['rating'],
                        'updated_at' => now()
                    ]);
                    $results[] = ['success' => true, 'action' => 'updated', 'rating' => $existingRating];
                } else {
                    $rating = Rating::create([
                        'user_id' => Auth::id(),
                        'ratingable_type' => get_class($ratingable),
                        'ratingable_id' => $ratingable->id,
                        'rating' => $ratingData['rating'],
                        'category' => $ratingData['category'] ?? null,
                        'ip_address' => $request->ip()
                    ]);
                    $results[] = ['success' => true, 'action' => 'created', 'rating' => $rating];
                }

                $this->updateAverageRating($ratingable, $ratingData['category'] ?? null);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تمت عملية التقييم بنجاح',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'حدث خطأ أثناء عملية التقييم',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getRatingableModel($type, $id)
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

    private function updateAverageRating($ratingable, $category = null)
    {
        $query = $ratingable->ratings();
        
        if ($category) {
            $query->where('category', $category);
            $averageRating = $query->avg('rating');
            
            // Update category-specific rating
            $ratingData = $ratingable->rating_data ?? [];
            $ratingData[$category] = $averageRating;
            $ratingable->update(['rating_data' => $ratingData]);
        } else {
            $averageRating = $query->avg('rating');
            $ratingable->update(['average_rating' => $averageRating]);
        }

        // Update total ratings count
        $totalRatings = $ratingable->ratings()->count();
        $ratingable->update(['total_ratings' => $totalRatings]);
    }

    private function getRatingStatisticsPrivate($ratingable, $category = null)
    {
        $query = $ratingable->ratings();
        
        if ($category) {
            $query->where('category', $category);
        }

        $totalRatings = $query->count();
        $averageRating = $query->avg('rating') ?? 0;

        // Rating distribution
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $query->where('rating', $i)->count();
        }

        // Recent ratings trend
        $recentTrend = $query->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, AVG(rating) as average_rating')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_ratings' => $totalRatings,
            'average_rating' => round($averageRating, 2),
            'distribution' => $distribution,
            'recent_trend' => $recentTrend,
            'category' => $category
        ];
    }

    private function getUserRating($ratingable, $category = null)
    {
        $query = $ratingable->ratings()->where('user_id', Auth::id());
        
        if ($category) {
            $query->where('category', $category);
        }

        return $query->first();
    }
}
