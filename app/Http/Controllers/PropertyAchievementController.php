<?php

namespace App\Http\Controllers;

use App\Models\PropertyAchievement;
use App\Models\UserPropertyGamification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class PropertyAchievementController extends Controller
{
    public function index(Request $request): View
    {
        $query = PropertyAchievement::withCount('users');

        // Filter by search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->get('category'));
        }

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->get('difficulty'));
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $achievements = $query->paginate(20);

        // Get statistics
        $stats = [
            'total_achievements' => PropertyAchievement::count(),
            'total_unlocked' => PropertyAchievement::withCount('users')->get()->sum('users_count'),
            'unique_users' => PropertyAchievement::distinct('user_id')->count('user_id'),
            'achievements_by_category' => PropertyAchievement::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->get(),
            'achievements_by_difficulty' => PropertyAchievement::selectRaw('difficulty, COUNT(*) as count')
                ->groupBy('difficulty')
                ->get(),
            'achievements_by_type' => PropertyAchievement::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
        ];

        return view('gamification.achievements.index', compact('achievements', 'stats'));
    }

    public function create(): View
    {
        return view('gamification.achievements.create');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'type' => 'required|in:points,level,badges,challenges,quests,social,custom',
            'category' => 'required|string|max:100',
            'difficulty' => 'required|in:easy,medium,hard,expert',
            'points_reward' => 'required|integer|min:0',
            'badge_reward' => 'nullable|exists:property_badges,id',
            'requirements' => 'required|array',
            'conditions' => 'nullable|array',
            'icon' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'hidden' => 'required|boolean',
            'repeatable' => 'required|boolean',
            'cooldown_period' => 'nullable|integer|min:0',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $achievement = PropertyAchievement::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الإنجاز بنجاح',
            'data' => $achievement
        ]);
    }

    public function show($id): View
    {
        $achievement = PropertyAchievement::with(['users' => function ($query) {
            $query->take(20);
        }])->findOrFail($id);

        // Get recent unlockers
        $recentUnlockers = $achievement->users()
            ->withPivot('unlocked_at', 'progress')
            ->with('user')
            ->orderBy('pivot_unlocked_at', 'desc')
            ->take(10)
            ->get();

        return view('gamification.achievements.show', compact('achievement', 'recentUnlockers'));
    }

    public function edit($id): View
    {
        $achievement = PropertyAchievement::findOrFail($id);
        return view('gamification.achievements.edit', compact('achievement'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $achievement = PropertyAchievement::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'type' => 'required|in:points,level,badges,challenges,quests,social,custom',
            'category' => 'required|string|max:100',
            'difficulty' => 'required|in:easy,medium,hard,expert',
            'points_reward' => 'required|integer|min:0',
            'badge_reward' => 'nullable|exists:property_badges,id',
            'requirements' => 'required|array',
            'conditions' => 'nullable|array',
            'icon' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'hidden' => 'required|boolean',
            'repeatable' => 'required|boolean',
            'cooldown_period' => 'nullable|integer|min:0',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $achievement->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الإنجاز بنجاح',
            'data' => $achievement
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $achievement = PropertyAchievement::findOrFail($id);
        
        // Check if achievement has been unlocked by users
        if ($achievement->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الإنجاز الذي تم فتحه من قبل المستخدمين'
            ], 422);
        }

        $achievement->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الإنجاز بنجاح'
        ]);
    }

    public function unlockAchievement(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'achievement_id' => 'required|exists:property_achievements,id',
            'user_id' => 'required|exists:users,id',
            'progress_data' => 'nullable|array',
        ]);

        $achievement = PropertyAchievement::findOrFail($validated['achievement_id']);
        $user = \App\Models\User::findOrFail($validated['user_id']);

        // Check if already unlocked
        if ($achievement->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم فتح هذا الإنجاز بالفعل'
            ], 422);
        }

        // Check if achievement is expired
        if ($achievement->expires_at && $achievement->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'الإنجاز منتهي الصلاحية'
            ], 422);
        }

        // Check cooldown period
        if ($achievement->repeatable && $achievement->cooldown_period) {
            $lastUnlock = $achievement->users()
                ->where('user_id', $user->id)
                ->orderBy('pivot_unlocked_at', 'desc')
                ->first();

            if ($lastUnlock && $lastUnlock->pivot->unlocked_at->diffInDays(now()) < $achievement->cooldown_period) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن فتح الإنجاز مرة أخرى خلال فترة التبريد'
                ], 422);
            }
        }

        // Check requirements
        if (!$this->checkAchievementRequirements($achievement, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم لا يفي بمتطلبات الإنجاز'
            ], 422);
        }

        // Unlock achievement
        $achievement->users()->attach($user->id, [
            'unlocked_at' => now(),
            'progress' => 100,
            'progress_data' => $validated['progress_data'] ?? [],
        ]);

        // Award points
        if ($achievement->points_reward > 0) {
            $this->awardPoints($user->id, $achievement->points_reward, 'achievement_reward', "مكافأة إنجاز: {$achievement->name}");
        }

        // Award badge if specified
        if ($achievement->badge_reward) {
            $this->awardBadge($user->id, $achievement->badge_reward, "فتح إنجاز: {$achievement->name}");
        }

        return response()->json([
            'success' => true,
            'message' => 'تم فتح الإنجاز بنجاح',
            'data' => $achievement
        ]);
    }

    public function getUserAchievements($userId): JsonResponse
    {
        $user = \App\Models\User::findOrFail($userId);

        $unlockedAchievements = $user->achievements()
            ->withPivot('unlocked_at', 'progress', 'progress_data')
            ->orderBy('pivot_unlocked_at', 'desc')
            ->get();

        $availableAchievements = PropertyAchievement::where('hidden', false)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->whereDoesntHave('users', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->get()
            ->map(function ($achievement) use ($userId) {
                $achievement->progress = $this->calculateAchievementProgress($achievement, $userId);
                $achievement->can_unlock = $achievement->progress >= 100;
                return $achievement;
            });

        $stats = [
            'total_unlocked' => $unlockedAchievements->count(),
            'total_points_earned' => $unlockedAchievements->sum('points_reward'),
            'by_category' => $unlockedAchievements->groupBy('category')->map->count(),
            'by_difficulty' => $unlockedAchievements->groupBy('difficulty')->map->count(),
            'recent_unlocks' => $unlockedAchievements->take(5),
        ];

        return response()->json([
            'unlocked_achievements' => $unlockedAchievements,
            'available_achievements' => $availableAchievements,
            'stats' => $stats,
        ]);
    }

    public function updateProgress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'achievement_id' => 'required|exists:property_achievements,id',
            'user_id' => 'required|exists:users,id',
            'progress' => 'required|integer|min:0|max:100',
            'progress_data' => 'nullable|array',
        ]);

        $achievement = PropertyAchievement::findOrFail($validated['achievement_id']);
        $user = \App\Models\User::findOrFail($validated['user_id']);

        // Check if already unlocked
        $existingUnlock = $achievement->users()->where('user_id', $user->id)->first();
        
        if ($existingUnlock && $existingUnlock->pivot->progress >= 100) {
            return response()->json([
                'success' => false,
                'message' => 'الإنجاز مفتوح بالفعل'
            ], 422);
        }

        // Update or create progress
        if ($existingUnlock) {
            $achievement->users()->updateExistingPivot($user->id, [
                'progress' => $validated['progress'],
                'progress_data' => $validated['progress_data'],
                'updated_at' => now(),
            ]);
        } else {
            $achievement->users()->attach($user->id, [
                'progress' => $validated['progress'],
                'progress_data' => $validated['progress_data'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Check if achievement is completed
        if ($validated['progress'] >= 100 && (!$existingUnlock || $existingUnlock->pivot->progress < 100)) {
            $this->completeAchievement($achievement, $user, $validated['progress_data'] ?? []);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث تقدم الإنجاز بنجاح'
        ]);
    }

    public function checkAndUnlockAchievements($userId): JsonResponse
    {
        $user = \App\Models\User::findOrFail($userId);
        $gamification = UserPropertyGamification::where('user_id', $userId)->first();

        if (!$gamification) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم ليس لديه بيانات ألعاب'
            ]);
        }

        $userAchievementIds = $user->achievements()->pluck('property_achievements.id');
        $eligibleAchievements = PropertyAchievement::where('hidden', false)
            ->whereNotIn('id', $userAchievementIds)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })->get();

        $unlockedAchievements = [];

        foreach ($eligibleAchievements as $achievement) {
            $progress = $this->calculateAchievementProgress($achievement, $userId);
            
            if ($progress >= 100 && $this->checkAchievementRequirements($achievement, $user)) {
                $user->achievements()->attach($achievement->id, [
                    'unlocked_at' => now(),
                    'progress' => 100,
                    'progress_data' => [],
                ]);

                // Award points
                if ($achievement->points_reward > 0) {
                    $this->awardPoints($user->id, $achievement->points_reward, 'achievement_reward', "مكافأة إنجاز: {$achievement->name}");
                }

                // Award badge if specified
                if ($achievement->badge_reward) {
                    $this->awardBadge($user->id, $achievement->badge_reward, "فتح إنجاز: {$achievement->name}");
                }

                $unlockedAchievements[] = $achievement;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'تم فحص الإنجازات المستحقة',
            'unlocked_achievements' => $unlockedAchievements,
            'unlocked_count' => count($unlockedAchievements),
        ]);
    }

    public function getAchievementAnalytics(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        $analytics = [
            'unlock_trends' => $this->getUnlockTrends($dateRange),
            'popular_achievements' => $this->getPopularAchievements($dateRange),
            'difficulty_distribution' => $this->getDifficultyDistribution($dateRange),
            'category_analytics' => $this->getCategoryAnalytics($dateRange),
            'completion_rates' => $this->getCompletionRates($dateRange),
        ];

        return response()->json($analytics);
    }

    private function checkAchievementRequirements($achievement, $user): bool
    {
        if (empty($achievement->requirements)) {
            return true;
        }

        $gamification = UserPropertyGamification::where('user_id', $user->id)->first();
        if (!$gamification) {
            return false;
        }

        foreach ($achievement->requirements as $requirement) {
            switch ($requirement['type']) {
                case 'total_points':
                    if ($gamification->total_points < $requirement['value']) {
                        return false;
                    }
                    break;
                case 'current_level':
                    if ($gamification->current_level < $requirement['value']) {
                        return false;
                    }
                    break;
                case 'badges_earned':
                    if ($gamification->badges_earned < $requirement['value']) {
                        return false;
                    }
                    break;
                case 'challenges_completed':
                    if ($gamification->challenges_completed < $requirement['value']) {
                        return false;
                    }
                    break;
                case 'quests_completed':
                    if ($gamification->quests_completed < $requirement['value']) {
                        return false;
                    }
                    break;
                case 'current_streak':
                    if ($gamification->current_streak < $requirement['value']) {
                        return false;
                    }
                    break;
                case 'properties_listed':
                    $propertyCount = \App\Models\Property::where('user_id', $user->id)->count();
                    if ($propertyCount < $requirement['value']) {
                        return false;
                    }
                    break;
                case 'properties_sold':
                    $soldCount = \App\Models\Property::where('user_id', $user->id)
                        ->where('status', 'sold')
                        ->count();
                    if ($soldCount < $requirement['value']) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    private function calculateAchievementProgress($achievement, $userId): int
    {
        $gamification = UserPropertyGamification::where('user_id', $userId)->first();
        if (!$gamification) {
            return 0;
        }

        if (empty($achievement->requirements)) {
            return 100;
        }

        $totalRequirements = count($achievement->requirements);
        $metRequirements = 0;

        foreach ($achievement->requirements as $requirement) {
            $met = false;
            
            switch ($requirement['type']) {
                case 'total_points':
                    $met = $gamification->total_points >= $requirement['value'];
                    break;
                case 'current_level':
                    $met = $gamification->current_level >= $requirement['value'];
                    break;
                case 'badges_earned':
                    $met = $gamification->badges_earned >= $requirement['value'];
                    break;
                case 'challenges_completed':
                    $met = $gamification->challenges_completed >= $requirement['value'];
                    break;
                case 'quests_completed':
                    $met = $gamification->quests_completed >= $requirement['value'];
                    break;
                case 'current_streak':
                    $met = $gamification->current_streak >= $requirement['value'];
                    break;
                case 'properties_listed':
                    $propertyCount = \App\Models\Property::where('user_id', $userId)->count();
                    $met = $propertyCount >= $requirement['value'];
                    break;
                case 'properties_sold':
                    $soldCount = \App\Models\Property::where('user_id', $userId)
                        ->where('status', 'sold')
                        ->count();
                    $met = $soldCount >= $requirement['value'];
                    break;
            }

            if ($met) {
                $metRequirements++;
            }
        }

        return ($metRequirements / $totalRequirements) * 100;
    }

    private function completeAchievement($achievement, $user, $progressData): void
    {
        // Mark as completed
        $achievement->users()->updateExistingPivot($user->id, [
            'progress' => 100,
            'unlocked_at' => now(),
            'progress_data' => $progressData,
        ]);

        // Award points
        if ($achievement->points_reward > 0) {
            $this->awardPoints($user->id, $achievement->points_reward, 'achievement_reward', "مكافأة إنجاز: {$achievement->name}");
        }

        // Award badge if specified
        if ($achievement->badge_reward) {
            $this->awardBadge($user->id, $achievement->badge_reward, "فتح إنجاز: {$achievement->name}");
        }
    }

    private function awardPoints($userId, $points, $type, $reason): void
    {
        $gamification = UserPropertyGamification::where('user_id', $userId)->first();
        if ($gamification) {
            $gamification->total_points += $points;
            $gamification->experience_points += $points;
            $gamification->save();
        }

        // Create points record
        \App\Models\PropertyPoints::create([
            'user_id' => $userId,
            'points' => $points,
            'type' => $type,
            'reason' => $reason,
            'awarded_at' => now(),
        ]);
    }

    private function awardBadge($userId, $badgeId, $reason): void
    {
        $badge = \App\Models\PropertyBadge::find($badgeId);
        if ($badge) {
            $badge->users()->attach($userId, [
                'earned_at' => now(),
                'reason' => $reason,
            ]);

            $gamification = UserPropertyGamification::where('user_id', $userId)->first();
            if ($gamification) {
                $gamification->badges_earned++;
                $gamification->save();
            }
        }
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

    private function getUnlockTrends($dateRange): array
    {
        return \DB::table('achievement_user')
            ->join('property_achievements', 'achievement_user.achievement_id', '=', 'property_achievements.id')
            ->whereBetween('achievement_user.unlocked_at', $dateRange)
            ->selectRaw('DATE(achievement_user.unlocked_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getPopularAchievements($dateRange): array
    {
        return \DB::table('achievement_user')
            ->join('property_achievements', 'achievement_user.achievement_id', '=', 'property_achievements.id')
            ->whereBetween('achievement_user.unlocked_at', $dateRange)
            ->selectRaw('property_achievements.name, property_achievements.category, COUNT(*) as unlock_count')
            ->groupBy('property_achievements.id', 'property_achievements.name', 'property_achievements.category')
            ->orderBy('unlock_count', 'desc')
            ->take(10)
            ->get()
            ->toArray();
    }

    private function getDifficultyDistribution($dateRange): array
    {
        return \DB::table('achievement_user')
            ->join('property_achievements', 'achievement_user.achievement_id', '=', 'property_achievements.id')
            ->whereBetween('achievement_user.unlocked_at', $dateRange)
            ->selectRaw('property_achievements.difficulty, COUNT(*) as count')
            ->groupBy('property_achievements.difficulty')
            ->get()
            ->toArray();
    }

    private function getCategoryAnalytics($dateRange): array
    {
        return \DB::table('achievement_user')
            ->join('property_achievements', 'achievement_user.achievement_id', '=', 'property_achievements.id')
            ->whereBetween('achievement_user.unlocked_at', $dateRange)
            ->selectRaw('property_achievements.category, COUNT(*) as unlock_count, SUM(property_achievements.points_reward) as total_points')
            ->groupBy('property_achievements.category')
            ->get()
            ->toArray();
    }

    private function getCompletionRates($dateRange): array
    {
        $totalUsers = UserPropertyGamification::count();
        
        return PropertyAchievement::selectRaw('category, difficulty, 
                         (SELECT COUNT(*) FROM achievement_user WHERE achievement_id = property_achievements.id) as unlocked_count')
            ->get()
            ->map(function ($achievement) use ($totalUsers) {
                return [
                    'category' => $achievement->category,
                    'difficulty' => $achievement->difficulty,
                    'unlocked_count' => $achievement->unlocked_count,
                    'completion_rate' => $totalUsers > 0 ? ($achievement->unlocked_count / $totalUsers) * 100 : 0,
                ];
            })
            ->toArray();
    }
}
