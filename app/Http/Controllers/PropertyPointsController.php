<?php

namespace App\Http\Controllers;

use App\Models\PropertyPoints;
use App\Models\UserPropertyGamification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class PropertyPointsController extends Controller
{
    public function index(Request $request): View
    {
        $query = PropertyPoints::with(['user', 'property']);

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        // Filter by property
        if ($request->has('property_id')) {
            $query->where('property_id', $request->get('property_id'));
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('created_at', '>=', $request->get('start_date'));
        }
        if ($request->has('end_date')) {
            $query->where('created_at', '<=', $request->get('end_date'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $points = $query->paginate(20);

        // Get statistics
        $stats = [
            'total_points_awarded' => PropertyPoints::sum('points'),
            'total_transactions' => PropertyPoints::count(),
            'average_points_per_transaction' => PropertyPoints::avg('points'),
            'points_this_month' => PropertyPoints::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('points'),
            'unique_users' => PropertyPoints::distinct('user_id')->count('user_id'),
            'points_by_type' => PropertyPoints::selectRaw('type, SUM(points) as total, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
        ];

        return view('gamification.points.index', compact('points', 'stats'));
    }

    public function create(): View
    {
        $users = \App\Models\User::orderBy('name')->get();
        $properties = \App\Models\Property::orderBy('title')->get();
        
        return view('gamification.points.create', compact('users', 'properties'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'property_id' => 'nullable|exists:properties,id',
            'points' => 'required|integer|min:1|max:10000',
            'type' => 'required|in:earned,bonus,penalty',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // Get or create user gamification record
        $gamification = UserPropertyGamification::where('user_id', $validated['user_id'])->first();
        if (!$gamification) {
            $gamification = $this->initializeUserGamification(\App\Models\User::find($validated['user_id']));
        }

        // Create points record
        $points = PropertyPoints::create([
            'user_id' => $validated['user_id'],
            'property_id' => $validated['property_id'],
            'points' => $validated['points'],
            'type' => $validated['type'],
            'reason' => $validated['reason'],
            'description' => $validated['description'],
            'awarded_at' => now(),
        ]);

        // Update user's total points
        if ($validated['type'] === 'penalty') {
            $gamification->total_points = max(0, $gamification->total_points - $validated['points']);
        } else {
            $gamification->total_points += $validated['points'];
            $gamification->experience_points += $validated['points'];
        }

        // Update activity streak
        $this->updateActivityStreak($gamification);

        $gamification->save();

        return response()->json([
            'success' => true,
            'message' => 'تم منح النقاط بنجاح',
            'data' => $points
        ]);
    }

    public function show($id): View
    {
        $points = PropertyPoints::with(['user', 'property'])->findOrFail($id);
        
        return view('gamification.points.show', compact('points'));
    }

    public function edit($id): View
    {
        $points = PropertyPoints::findOrFail($id);
        $users = \App\Models\User::orderBy('name')->get();
        $properties = \App\Models\Property::orderBy('title')->get();
        
        return view('gamification.points.edit', compact('points', 'users', 'properties'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $points = PropertyPoints::findOrFail($id);

        $validated = $request->validate([
            'points' => 'required|integer|min:1|max:10000',
            'type' => 'required|in:earned,bonus,penalty',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // Calculate difference in points
        $oldPoints = $points->points;
        $newPoints = $validated['points'];
        $difference = $newPoints - $oldPoints;

        // Update points record
        $points->update($validated);

        // Update user's total points
        $gamification = UserPropertyGamification::where('user_id', $points->user_id)->first();
        if ($gamification) {
            if ($validated['type'] === 'penalty') {
                $gamification->total_points = max(0, $gamification->total_points - $difference);
            } else {
                $gamification->total_points += $difference;
                $gamification->experience_points += $difference;
            }
            $gamification->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث النقاط بنجاح',
            'data' => $points
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $points = PropertyPoints::findOrFail($id);

        // Update user's total points
        $gamification = UserPropertyGamification::where('user_id', $points->user_id)->first();
        if ($gamification) {
            if ($points->type === 'penalty') {
                $gamification->total_points += $points->points;
            } else {
                $gamification->total_points = max(0, $gamification->total_points - $points->points);
                $gamification->experience_points = max(0, $gamification->experience_points - $points->points);
            }
            $gamification->save();
        }

        $points->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف النقاط بنجاح'
        ]);
    }

    public function bulkAward(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'points' => 'required|integer|min:1|max:10000',
            'type' => 'required|in:earned,bonus,penalty',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $awardedPoints = [];
        $errors = [];

        foreach ($validated['user_ids'] as $userId) {
            try {
                // Get or create user gamification record
                $gamification = UserPropertyGamification::where('user_id', $userId)->first();
                if (!$gamification) {
                    $gamification = $this->initializeUserGamification(\App\Models\User::find($userId));
                }

                // Create points record
                $points = PropertyPoints::create([
                    'user_id' => $userId,
                    'points' => $validated['points'],
                    'type' => $validated['type'],
                    'reason' => $validated['reason'],
                    'description' => $validated['description'],
                    'awarded_at' => now(),
                ]);

                // Update user's total points
                if ($validated['type'] === 'penalty') {
                    $gamification->total_points = max(0, $gamification->total_points - $validated['points']);
                } else {
                    $gamification->total_points += $validated['points'];
                    $gamification->experience_points += $validated['points'];
                }

                // Update activity streak
                $this->updateActivityStreak($gamification);

                $gamification->save();

                $awardedPoints[] = $points;

            } catch (\Exception $e) {
                $errors[] = "User ID {$userId}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => count($errors) === 0,
            'message' => count($awardedPoints) > 0 ? "تم منح النقاط لـ " . count($awardedPoints) . " مستخدم" : 'فشل منح النقاط',
            'awarded_count' => count($awardedPoints),
            'errors' => $errors,
        ]);
    }

    public function getUserPoints($userId): JsonResponse
    {
        $points = PropertyPoints::where('user_id', $userId)
            ->with('property')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_points' => PropertyPoints::where('user_id', $userId)->sum('points'),
            'earned_points' => PropertyPoints::where('user_id', $userId)->where('type', 'earned')->sum('points'),
            'bonus_points' => PropertyPoints::where('user_id', $userId)->where('type', 'bonus')->sum('points'),
            'penalty_points' => PropertyPoints::where('user_id', $userId)->where('type', 'penalty')->sum('points'),
            'points_this_month' => PropertyPoints::where('user_id', $userId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('points'),
            'total_transactions' => PropertyPoints::where('user_id', $userId)->count(),
        ];

        return response()->json([
            'points' => $points,
            'stats' => $stats,
        ]);
    }

    public function getPointsAnalytics(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        $analytics = [
            'points_over_time' => PropertyPoints::whereBetween('created_at', $dateRange)
                ->selectRaw('DATE(created_at) as date, SUM(points) as total_points, COUNT(*) as transactions')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'points_by_type' => PropertyPoints::whereBetween('created_at', $dateRange)
                ->selectRaw('type, SUM(points) as total, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
            'top_users' => PropertyPoints::whereBetween('created_at', $dateRange)
                ->selectRaw('user_id, SUM(points) as total_points')
                ->groupBy('user_id')
                ->orderBy('total_points', 'desc')
                ->take(10)
                ->with('user')
                ->get(),
            'daily_average' => PropertyPoints::whereBetween('created_at', $dateRange)
                ->selectRaw('DATE(created_at) as date, AVG(points) as avg_points')
                ->groupBy('date')
                ->get(),
            'points_distribution' => $this->getPointsDistribution($dateRange),
        ];

        return response()->json($analytics);
    }

    public function exportPoints(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,pdf',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'user_id' => 'nullable|exists:users,id',
            'type' => 'nullable|in:earned,bonus,penalty',
        ]);

        $query = PropertyPoints::with(['user', 'property']);

        if (isset($validated['start_date'])) {
            $query->where('created_at', '>=', $validated['start_date']);
        }
        if (isset($validated['end_date'])) {
            $query->where('created_at', '<=', $validated['end_date']);
        }
        if (isset($validated['user_id'])) {
            $query->where('user_id', $validated['user_id']);
        }
        if (isset($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        $points = $query->get();

        // Generate export file
        $filename = "points_export_" . date('Y-m-d_H-i-s') . ".{$validated['format']}";
        
        // This would generate the actual file based on format
        // For now, return success response
        
        return response()->json([
            'success' => true,
            'message' => 'تم تصدير النقاط بنجاح',
            'filename' => $filename,
            'count' => $points->count(),
        ]);
    }

    private function initializeUserGamification($user): UserPropertyGamification
    {
        return UserPropertyGamification::create([
            'user_id' => $user->id,
            'total_points' => 0,
            'current_level' => 1,
            'experience_points' => 0,
            'badges_earned' => 0,
            'challenges_completed' => 0,
            'quests_completed' => 0,
            'current_streak' => 0,
            'longest_streak' => 0,
            'last_activity_at' => now(),
            'joined_at' => now(),
        ]);
    }

    private function updateActivityStreak(UserPropertyGamification $gamification): void
    {
        $lastActivity = $gamification->last_activity_at;
        $now = now();

        // Check if last activity was yesterday
        if ($lastActivity && $lastActivity->isYesterday()) {
            $gamification->current_streak++;
        } elseif ($lastActivity && $lastActivity->isToday()) {
            // Same day, don't update streak
            return;
        } else {
            // Streak broken
            if ($gamification->current_streak > $gamification->longest_streak) {
                $gamification->longest_streak = $gamification->current_streak;
            }
            $gamification->current_streak = 1;
        }

        $gamification->last_activity_at = $now;
    }

    private function getDateRange($period): array
    {
        $now = now();
        
        switch ($period) {
            case 'week':
                return [$now->startOfWeek(), $now->endOfWeek()];
            case 'month':
                return [$now->startOfMonth(), $now->endOfMonth()];
            case 'quarter':
                return [$now->startOfQuarter(), $now->endOfQuarter()];
            case 'year':
                return [$now->startOfYear(), $now->endOfYear()];
            default:
                return [$now->subMonth(), $now];
        }
    }

    private function getPointsDistribution($dateRange): array
    {
        $points = PropertyPoints::whereBetween('created_at', $dateRange)->get();
        
        $distribution = [
            '1-10' => 0,
            '11-50' => 0,
            '51-100' => 0,
            '101-500' => 0,
            '501-1000' => 0,
            '1000+' => 0,
        ];

        foreach ($points as $point) {
            if ($point->points <= 10) {
                $distribution['1-10']++;
            } elseif ($point->points <= 50) {
                $distribution['11-50']++;
            } elseif ($point->points <= 100) {
                $distribution['51-100']++;
            } elseif ($point->points <= 500) {
                $distribution['101-500']++;
            } elseif ($point->points <= 1000) {
                $distribution['501-1000']++;
            } else {
                $distribution['1000+']++;
            }
        }

        return $distribution;
    }
}
