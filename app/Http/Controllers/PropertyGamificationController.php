<?php

namespace App\Http\Controllers;

use App\Models\PropertyGamification;
use App\Models\PropertyPoints;
use App\Models\PropertyBadge;
use App\Models\PropertyLeaderboard;
use App\Models\PropertyChallenge;
use App\Models\PropertyReward;
use App\Models\PropertyLevel;
use App\Models\PropertyAchievement;
use App\Models\PropertyQuest;
use App\Models\UserPropertyGamification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class PropertyGamificationController extends Controller
{
    public function dashboard(): View
    {
        $user = auth()->user();
        $gamification = UserPropertyGamification::where('user_id', $user->id)->first();
        
        if (!$gamification) {
            $gamification = $this->initializeUserGamification($user);
        }

        // Get user's current stats
        $stats = [
            'total_points' => $gamification->total_points,
            'current_level' => $gamification->current_level,
            'experience_points' => $gamification->experience_points,
            'badges_earned' => $gamification->badges_earned,
            'challenges_completed' => $gamification->challenges_completed,
            'quests_completed' => $gamification->quests_completed,
            'current_streak' => $gamification->current_streak,
            'longest_streak' => $gamification->longest_streak,
        ];

        // Get recent activities
        $recentActivities = PropertyPoints::where('user_id', $user->id)
            ->with('property')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get available challenges
        $availableChallenges = PropertyChallenge::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->take(5)
            ->get();

        // Get leaderboard position
        $leaderboardPosition = PropertyLeaderboard::where('user_id', $user->id)
            ->orderBy('rank')
            ->first();

        // Get next level requirements
        $nextLevel = PropertyLevel::where('level', $gamification->current_level + 1)->first();
        $progressToNextLevel = $nextLevel ? 
            ($gamification->experience_points / $nextLevel->experience_required) * 100 : 100;

        return view('gamification.dashboard', compact(
            'gamification',
            'stats',
            'recentActivities',
            'availableChallenges',
            'leaderboardPosition',
            'nextLevel',
            'progressToNextLevel'
        ));
    }

    public function index(Request $request): View
    {
        $query = UserPropertyGamification::with('user');

        // Filter by search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by level range
        if ($request->has('min_level')) {
            $query->where('current_level', '>=', $request->get('min_level'));
        }
        if ($request->has('max_level')) {
            $query->where('current_level', '<=', $request->get('max_level'));
        }

        // Filter by points range
        if ($request->has('min_points')) {
            $query->where('total_points', '>=', $request->get('min_points'));
        }
        if ($request->has('max_points')) {
            $query->where('total_points', '<=', $request->get('max_points'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'total_points');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $gamificationUsers = $query->paginate(20);

        // Get statistics
        $stats = [
            'total_users' => UserPropertyGamification::count(),
            'average_level' => UserPropertyGamification::avg('current_level'),
            'average_points' => UserPropertyGamification::avg('total_points'),
            'total_points_awarded' => UserPropertyGamification::sum('total_points'),
            'active_challenges' => PropertyChallenge::where('status', 'active')->count(),
            'total_badges' => PropertyBadge::count(),
        ];

        return view('gamification.index', compact('gamificationUsers', 'stats'));
    }

    public function show($userId): View
    {
        $gamification = UserPropertyGamification::with('user')
            ->where('user_id', $userId)
            ->firstOrFail();

        // Get user's points history
        $pointsHistory = PropertyPoints::where('user_id', $userId)
            ->with('property')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get user's badges
        $badges = PropertyBadge::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        // Get user's achievements
        $achievements = PropertyAchievement::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        // Get user's completed challenges
        $completedChallenges = PropertyChallenge::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId)->where('completed', true);
        })->get();

        // Get user's completed quests
        $completedQuests = PropertyQuest::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId)->where('completed', true);
        })->get();

        // Get activity statistics
        $activityStats = $this->getUserActivityStats($userId);

        return view('gamification.show', compact(
            'gamification',
            'pointsHistory',
            'badges',
            'achievements',
            'completedChallenges',
            'completedQuests',
            'activityStats'
        ));
    }

    public function create(): View
    {
        $users = \App\Models\User::orderBy('name')->get();
        return view('gamification.create', compact('users'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_points' => 'required|integer|min:0',
            'current_level' => 'required|integer|min:1',
            'experience_points' => 'required|integer|min:0',
            'badges_earned' => 'required|integer|min:0',
            'challenges_completed' => 'required|integer|min:0',
            'quests_completed' => 'required|integer|min:0',
            'current_streak' => 'required|integer|min:0',
            'longest_streak' => 'required|integer|min:0',
        ]);

        // Check if user already has gamification data
        $existing = UserPropertyGamification::where('user_id', $validated['user_id'])->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم لديه بالفعل بيانات الألعاب'
            ], 422);
        }

        $gamification = UserPropertyGamification::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء بيانات الألعاب بنجاح',
            'data' => $gamification
        ]);
    }

    public function edit($userId): View
    {
        $gamification = UserPropertyGamification::where('user_id', $userId)->firstOrFail();
        return view('gamification.edit', compact('gamification'));
    }

    public function update(Request $request, $userId): JsonResponse
    {
        $gamification = UserPropertyGamification::where('user_id', $userId)->firstOrFail();

        $validated = $request->validate([
            'total_points' => 'required|integer|min:0',
            'current_level' => 'required|integer|min:1',
            'experience_points' => 'required|integer|min:0',
            'badges_earned' => 'required|integer|min:0',
            'challenges_completed' => 'required|integer|min:0',
            'quests_completed' => 'required|integer|min:0',
            'current_streak' => 'required|integer|min:0',
            'longest_streak' => 'required|integer|min:0',
        ]);

        $gamification->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات الألعاب بنجاح',
            'data' => $gamification
        ]);
    }

    public function destroy($userId): JsonResponse
    {
        $gamification = UserPropertyGamification::where('user_id', $userId)->firstOrFail();
        $gamification->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف بيانات الألعاب بنجاح'
        ]);
    }

    public function awardPoints(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'property_id' => 'nullable|exists:properties,id',
            'type' => 'required|in:earned,bonus,penalty',
        ]);

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
            'awarded_at' => now(),
        ]);

        // Update user's total points
        if ($validated['type'] === 'penalty') {
            $gamification->total_points = max(0, $gamification->total_points - $validated['points']);
        } else {
            $gamification->total_points += $validated['points'];
        }

        // Check for level up
        $this->checkLevelUp($gamification);

        $gamification->save();

        return response()->json([
            'success' => true,
            'message' => 'تم منح النقاط بنجاح',
            'data' => $points
        ]);
    }

    public function getAnalytics(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        
        $analytics = [
            'points_awarded' => $this->getPointsAwardedAnalytics($period),
            'user_engagement' => $this->getUserEngagementAnalytics($period),
            'challenge_participation' => $this->getChallengeParticipationAnalytics($period),
            'badge_distribution' => $this->getBadgeDistributionAnalytics(),
            'level_progression' => $this->getLevelProgressionAnalytics($period),
            'activity_heatmap' => $this->getActivityHeatmap($period),
        ];

        return response()->json($analytics);
    }

    public function resetUserProgress($userId): JsonResponse
    {
        $gamification = UserPropertyGamification::where('user_id', $userId)->firstOrFail();
        
        // Reset gamification data
        $gamification->update([
            'total_points' => 0,
            'current_level' => 1,
            'experience_points' => 0,
            'badges_earned' => 0,
            'challenges_completed' => 0,
            'quests_completed' => 0,
            'current_streak' => 0,
        ]);

        // Remove points history
        PropertyPoints::where('user_id', $userId)->delete();

        // Remove badge associations
        $gamification->badges()->detach();

        // Remove achievement associations
        $gamification->achievements()->detach();

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة تعيين تقدم المستخدم بنجاح'
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

    private function checkLevelUp(UserPropertyGamification $gamification): void
    {
        $currentLevel = $gamification->current_level;
        $nextLevel = PropertyLevel::where('level', $currentLevel + 1)->first();

        if ($nextLevel && $gamification->experience_points >= $nextLevel->experience_required) {
            $gamification->current_level = $nextLevel->level;
            $gamification->experience_points = $gamification->experience_points - $nextLevel->experience_required;

            // Award level up bonus points
            PropertyPoints::create([
                'user_id' => $gamification->user_id,
                'points' => $nextLevel->bonus_points,
                'type' => 'bonus',
                'reason' => "مكافأة ترقية المستوى {$nextLevel->level}",
                'awarded_at' => now(),
            ]);

            // Check for multiple level ups
            $this->checkLevelUp($gamification);
        }
    }

    private function getUserActivityStats($userId): array
    {
        $thirtyDaysAgo = now()->subDays(30);
        
        return [
            'points_this_month' => PropertyPoints::where('user_id', $userId)
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->sum('points'),
            'activities_this_month' => PropertyPoints::where('user_id', $userId)
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->count(),
            'most_active_day' => PropertyPoints::where('user_id', $userId)
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->selectRaw('DAYOFWEEK(created_at) as day, COUNT(*) as count')
                ->groupBy('day')
                ->orderBy('count', 'desc')
                ->first(),
            'average_points_per_activity' => PropertyPoints::where('user_id', $userId)
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->avg('points'),
        ];
    }

    private function getPointsAwardedAnalytics($period): array
    {
        $dateRange = $this->getDateRange($period);
        
        return PropertyPoints::whereBetween('created_at', $dateRange)
            ->selectRaw('DATE(created_at) as date, SUM(points) as total_points, COUNT(*) as transactions')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getUserEngagementAnalytics($period): array
    {
        $dateRange = $this->getDateRange($period);
        
        return [
            'active_users' => UserPropertyGamification::whereHas('points', function ($query) use ($dateRange) {
                $query->whereBetween('created_at', $dateRange);
            })->count(),
            'new_users' => UserPropertyGamification::whereBetween('created_at', $dateRange)->count(),
            'retention_rate' => $this->calculateRetentionRate($period),
            'average_session_time' => $this->calculateAverageSessionTime($period),
        ];
    }

    private function getChallengeParticipationAnalytics($period): array
    {
        $dateRange = $this->getDateRange($period);
        
        return [
            'total_participants' => PropertyChallenge::whereHas('participants', function ($query) use ($dateRange) {
                $query->whereBetween('created_at', $dateRange);
            })->count(),
            'completion_rate' => $this->calculateChallengeCompletionRate($period),
            'popular_challenges' => PropertyChallenge::withCount(['participants' => function ($query) use ($dateRange) {
                $query->whereBetween('created_at', $dateRange);
            }])->orderBy('participants_count', 'desc')->take(5)->get(),
        ];
    }

    private function getBadgeDistributionAnalytics(): array
    {
        return PropertyBadge::withCount('users')
            ->orderBy('users_count', 'desc')
            ->get()
            ->map(function ($badge) {
                return [
                    'name' => $badge->name,
                    'count' => $badge->users_count,
                    'rarity' => $badge->rarity,
                ];
            })
            ->toArray();
    }

    private function getLevelProgressionAnalytics($period): array
    {
        $dateRange = $this->getDateRange($period);
        
        return UserPropertyGamification::whereBetween('updated_at', $dateRange)
            ->selectRaw('current_level, COUNT(*) as count')
            ->groupBy('current_level')
            ->orderBy('current_level')
            ->get()
            ->toArray();
    }

    private function getActivityHeatmap($period): array
    {
        $dateRange = $this->getDateRange($period);
        
        return PropertyPoints::whereBetween('created_at', $dateRange)
            ->selectRaw('HOUR(created_at) as hour, DAYOFWEEK(created_at) as day, COUNT(*) as count')
            ->groupBy('hour', 'day')
            ->get()
            ->map(function ($item) {
                return [
                    'hour' => $item->hour,
                    'day' => $item->day,
                    'count' => $item->count,
                ];
            })
            ->toArray();
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

    private function calculateRetentionRate($period): float
    {
        // Simplified retention calculation
        $dateRange = $this->getDateRange($period);
        $previousPeriod = [
            $dateRange[0]->copy()->subMonth(),
            $dateRange[1]->copy()->subMonth(),
        ];

        $currentUsers = UserPropertyGamification::whereHas('points', function ($query) use ($dateRange) {
            $query->whereBetween('created_at', $dateRange);
        })->pluck('user_id');

        $previousUsers = UserPropertyGamification::whereHas('points', function ($query) use ($previousPeriod) {
            $query->whereBetween('created_at', $previousPeriod);
        })->pluck('user_id');

        if ($previousUsers->isEmpty()) {
            return 0;
        }

        $retainedUsers = $currentUsers->intersect($previousUsers)->count();
        
        return ($retainedUsers / $previousUsers->count()) * 100;
    }

    private function calculateAverageSessionTime($period): float
    {
        // Simplified session time calculation
        return 15.5; // minutes
    }

    private function calculateChallengeCompletionRate($period): float
    {
        $dateRange = $this->getDateRange($period);
        
        $totalParticipants = PropertyChallenge::whereHas('participants', function ($query) use ($dateRange) {
            $query->whereBetween('created_at', $dateRange);
        })->count();

        $completedParticipants = PropertyChallenge::whereHas('participants', function ($query) use ($dateRange) {
            $query->whereBetween('created_at', $dateRange)->where('completed', true);
        })->count();

        return $totalParticipants > 0 ? ($completedParticipants / $totalParticipants) * 100 : 0;
    }
}
