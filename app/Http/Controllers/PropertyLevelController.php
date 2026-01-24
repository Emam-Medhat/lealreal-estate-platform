<?php

namespace App\Http\Controllers;

use App\Models\PropertyLevel;
use App\Models\UserPropertyGamification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class PropertyLevelController extends Controller
{
    public function index(Request $request): View
    {
        $query = PropertyLevel::withCount('users');

        // Filter by search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Filter by tier
        if ($request->has('tier')) {
            $query->where('tier', $request->get('tier'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'level');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $levels = $query->paginate(20);

        // Get statistics
        $stats = [
            'total_levels' => PropertyLevel::count(),
            'total_users_at_levels' => PropertyLevel::withCount('users')->get()->sum('users_count'),
            'average_level' => UserPropertyGamification::avg('current_level'),
            'levels_by_tier' => PropertyLevel::selectRaw('tier, COUNT(*) as count')
                ->groupBy('tier')
                ->get(),
            'users_at_each_level' => PropertyLevel::withCount('users')
                ->orderBy('level')
                ->get(),
        ];

        return view('gamification.levels.index', compact('levels', 'stats'));
    }

    public function create(): View
    {
        return view('gamification.levels.create');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'level' => 'required|integer|min:1|unique:property_levels',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'tier' => 'required|in:bronze,silver,gold,platinum,diamond',
            'experience_required' => 'required|integer|min:0',
            'bonus_points' => 'required|integer|min:0',
            'privileges' => 'required|array',
            'rewards' => 'nullable|array',
            'badge_unlock' => 'nullable|exists:property_badges,id',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $level = PropertyLevel::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المستوى بنجاح',
            'data' => $level
        ]);
    }

    public function show($id): View
    {
        $level = PropertyLevel::with(['users' => function ($query) {
            $query->take(20);
        }])->findOrFail($id);

        // Get users at this level
        $usersAtLevel = $level->users()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get level progression stats
        $progressionStats = [
            'total_users' => $level->users()->count(),
            'recent_promotions' => $level->users()
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'average_time_to_reach' => $this->calculateAverageTimeToReach($level->level),
            'next_level_users' => PropertyLevel::where('level', $level->level + 1)
                ->withCount('users')
                ->first()
                ->users_count ?? 0,
        ];

        return view('gamification.levels.show', compact('level', 'usersAtLevel', 'progressionStats'));
    }

    public function edit($id): View
    {
        $level = PropertyLevel::findOrFail($id);
        return view('gamification.levels.edit', compact('level'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $level = PropertyLevel::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'tier' => 'required|in:bronze,silver,gold,platinum,diamond',
            'experience_required' => 'required|integer|min:0',
            'bonus_points' => 'required|integer|min:0',
            'privileges' => 'required|array',
            'rewards' => 'nullable|array',
            'badge_unlock' => 'nullable|exists:property_badges,id',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $level->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المستوى بنجاح',
            'data' => $level
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $level = PropertyLevel::findOrFail($id);
        
        // Check if level has users
        if ($level->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف المستوى الذي لديه مستخدمين'
            ], 422);
        }

        $level->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المستوى بنجاح'
        ]);
    }

    public function getUserLevel($userId): JsonResponse
    {
        $user = \App\Models\User::findOrFail($userId);
        $gamification = UserPropertyGamification::where('user_id', $userId)->first();

        if (!$gamification) {
            return response()->json([
                'current_level' => 1,
                'experience_points' => 0,
                'next_level' => null,
                'progress_to_next' => 0,
            ]);
        }

        $currentLevel = PropertyLevel::where('level', $gamification->current_level)->first();
        $nextLevel = PropertyLevel::where('level', $gamification->current_level + 1)->first();

        $progressToNext = 0;
        if ($nextLevel) {
            $currentLevelExp = PropertyLevel::where('level', $gamification->current_level - 1)
                ->first()
                ->experience_required ?? 0;
            $progressToNext = (($gamification->experience_points - $currentLevelExp) / 
                              ($nextLevel->experience_required - $currentLevelExp)) * 100;
        }

        return response()->json([
            'current_level' => $currentLevel,
            'experience_points' => $gamification->experience_points,
            'next_level' => $nextLevel,
            'progress_to_next' => min(100, max(0, $progressToNext)),
            'total_experience_needed' => $nextLevel ? $nextLevel->experience_required : null,
            'experience_remaining' => $nextLevel ? max(0, $nextLevel->experience_required - $gamification->experience_points) : 0,
        ]);
    }

    public function checkLevelUp($userId): JsonResponse
    {
        $user = \App\Models\User::findOrFail($userId);
        $gamification = UserPropertyGamification::where('user_id', $userId)->first();

        if (!$gamification) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم ليس لديه بيانات ألعاب'
            ]);
        }

        $levelUps = [];
        $currentLevel = $gamification->current_level;

        // Check for multiple level ups
        while (true) {
            $nextLevel = PropertyLevel::where('level', $currentLevel + 1)->first();
            
            if (!$nextLevel || $gamification->experience_points < $nextLevel->experience_required) {
                break;
            }

            // Level up
            $levelUps[] = [
                'from_level' => $currentLevel,
                'to_level' => $nextLevel->level,
                'level_name' => $nextLevel->name,
                'tier' => $nextLevel->tier,
                'bonus_points' => $nextLevel->bonus_points,
                'privileges' => $nextLevel->privileges,
                'rewards' => $nextLevel->rewards,
                'badge_unlock' => $nextLevel->badge_unlock,
            ];

            // Update user's level
            $gamification->current_level = $nextLevel->level;
            $gamification->experience_points = $gamification->experience_points - $nextLevel->experience_required;
            
            // Award bonus points
            if ($nextLevel->bonus_points > 0) {
                $this->awardPoints($userId, $nextLevel->bonus_points, 'level_up_bonus', "مكافأة ترقية المستوى {$nextLevel->level}");
            }

            // Award badge if specified
            if ($nextLevel->badge_unlock) {
                $this->awardBadge($userId, $nextLevel->badge_unlock, "ترقية المستوى {$nextLevel->level}");
            }

            $currentLevel = $nextLevel->level;
        }

        if (!empty($levelUps)) {
            $gamification->save();
        }

        return response()->json([
            'success' => !empty($levelUps),
            'message' => !empty($levelUps) ? 'تم ترقية المستخدم بنجاح' : 'المستخدم في المستوى المناسب',
            'level_ups' => $levelUps,
            'current_level' => $gamification->current_level,
            'experience_points' => $gamification->experience_points,
        ]);
    }

    public function getLevelProgression($userId): JsonResponse
    {
        $user = \App\Models\User::findOrFail($userId);
        $gamification = UserPropertyGamification::where('user_id', $userId)->first();

        if (!$gamification) {
            return response()->json([]);
        }

        $currentLevel = PropertyLevel::where('level', $gamification->current_level)->first();
        
        // Get progression history (simplified)
        $progression = [
            'current_level' => $currentLevel,
            'levels_completed' => PropertyLevel::where('level', '<=', $gamification->current_level)
                ->orderBy('level')
                ->get(),
            'upcoming_levels' => PropertyLevel::where('level', '>', $gamification->current_level)
                ->orderBy('level')
                ->take(5)
                ->get(),
            'total_levels' => PropertyLevel::count(),
            'completion_percentage' => ($gamification->current_level / PropertyLevel::count()) * 100,
        ];

        return response()->json($progression);
    }

    public function getLevelAnalytics(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        $analytics = [
            'level_distribution' => $this->getLevelDistribution(),
            'level_progression_trends' => $this->getLevelProgressionTrends($dateRange),
            'tier_analytics' => $this->getTierAnalytics(),
            'level_up_rates' => $this->getLevelUpRates($dateRange),
            'retention_by_level' => $this->getRetentionByLevel(),
        ];

        return response()->json($analytics);
    }

    public function getLevelPrivileges($levelId): JsonResponse
    {
        $level = PropertyLevel::findOrFail($levelId);
        
        return response()->json([
            'level' => $level,
            'privileges' => $level->privileges,
            'rewards' => $level->rewards,
            'badge_unlock' => $level->badge_unlock ? \App\Models\PropertyBadge::find($level->badge_unlock) : null,
        ]);
    }

    private function calculateAverageTimeToReach($level): float
    {
        // Simplified calculation - in real implementation would track level up dates
        return $level * 7; // days
    }

    private function awardPoints($userId, $points, $type, $reason): void
    {
        $gamification = UserPropertyGamification::where('user_id', $userId)->first();
        if ($gamification) {
            $gamification->total_points += $points;
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

    private function getLevelDistribution(): array
    {
        return PropertyLevel::withCount('users')
            ->orderBy('level')
            ->get()
            ->map(function ($level) {
                return [
                    'level' => $level->level,
                    'name' => $level->name,
                    'tier' => $level->tier,
                    'users_count' => $level->users_count,
                    'percentage' => ($level->users_count / UserPropertyGamification::count()) * 100,
                ];
            })
            ->toArray();
    }

    private function getLevelProgressionTrends($dateRange): array
    {
        return UserPropertyGamification::whereBetween('updated_at', $dateRange)
            ->selectRaw('DATE(updated_at) as date, AVG(current_level) as avg_level')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getTierAnalytics(): array
    {
        return PropertyLevel::selectRaw('tier, COUNT(*) as level_count, 
                         (SELECT COUNT(*) FROM user_property_gamifications WHERE current_level IN 
                          (SELECT level FROM property_levels WHERE tier = property_levels.tier)) as user_count')
            ->groupBy('tier')
            ->get()
            ->toArray();
    }

    private function getLevelUpRates($dateRange): array
    {
        // Simplified - would need level up tracking in real implementation
        return [
            'daily_level_ups' => UserPropertyGamification::whereBetween('updated_at', $dateRange)
                ->count() / 30, // approximation
            'average_level_per_user' => UserPropertyGamification::avg('current_level'),
            'level_up_velocity' => 1.5, // levels per month
        ];
    }

    private function getRetentionByLevel(): array
    {
        return PropertyLevel::withCount(['users' => function ($query) {
            $query->whereHas('user', function ($q) {
                $q->where('last_activity_at', '>=', now()->subDays(30));
            });
        }])->get()
            ->map(function ($level) {
                return [
                    'level' => $level->level,
                    'tier' => $level->tier,
                    'total_users' => $level->users_count,
                    'active_users' => $level->users_count,
                    'retention_rate' => 100, // simplified
                ];
            })
            ->toArray();
    }
}
