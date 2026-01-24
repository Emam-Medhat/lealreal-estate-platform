<?php

namespace App\Http\Controllers;

use App\Models\PropertyBadge;
use App\Models\UserPropertyGamification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class PropertyBadgeController extends Controller
{
    public function index(Request $request): View
    {
        $query = PropertyBadge::withCount('users');

        // Filter by search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Filter by rarity
        if ($request->has('rarity')) {
            $query->where('rarity', $request->get('rarity'));
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->get('category'));
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $badges = $query->paginate(20);

        // Get statistics
        $stats = [
            'total_badges' => PropertyBadge::count(),
            'total_awarded' => PropertyBadge::withCount('users')->get()->sum('users_count'),
            'unique_rarities' => PropertyBadge::distinct('rarity')->count('rarity'),
            'badges_by_rarity' => PropertyBadge::selectRaw('rarity, COUNT(*) as count')
                ->groupBy('rarity')
                ->get(),
            'badges_by_category' => PropertyBadge::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->get(),
        ];

        return view('gamification.badges.index', compact('badges', 'stats'));
    }

    public function create(): View
    {
        return view('gamification.badges.create');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'icon' => 'required|string|max:255',
            'rarity' => 'required|in:common,uncommon,rare,epic,legendary',
            'category' => 'required|string|max:100',
            'points_required' => 'required|integer|min:0',
            'level_required' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,hidden',
            'requirements' => 'nullable|array',
            'rewards' => 'nullable|array',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $badge = PropertyBadge::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الشارة بنجاح',
            'data' => $badge
        ]);
    }

    public function show($id): View
    {
        $badge = PropertyBadge::with(['users' => function ($query) {
            $query->take(20);
        }])->findOrFail($id);

        // Get recent earners
        $recentEarners = $badge->users()
            ->withPivot('earned_at')
            ->orderBy('pivot_earned_at', 'desc')
            ->take(10)
            ->get();

        return view('gamification.badges.show', compact('badge', 'recentEarners'));
    }

    public function edit($id): View
    {
        $badge = PropertyBadge::findOrFail($id);
        return view('gamification.badges.edit', compact('badge'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $badge = PropertyBadge::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'icon' => 'required|string|max:255',
            'rarity' => 'required|in:common,uncommon,rare,epic,legendary',
            'category' => 'required|string|max:100',
            'points_required' => 'required|integer|min:0',
            'level_required' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,hidden',
            'requirements' => 'nullable|array',
            'rewards' => 'nullable|array',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $badge->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الشارة بنجاح',
            'data' => $badge
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $badge = PropertyBadge::findOrFail($id);
        
        // Check if badge is awarded to any users
        if ($badge->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الشارة التي تم منحها للمستخدمين'
            ], 422);
        }

        $badge->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الشارة بنجاح'
        ]);
    }

    public function awardBadge(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'badge_id' => 'required|exists:property_badges,id',
            'user_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:255',
        ]);

        $badge = PropertyBadge::findOrFail($validated['badge_id']);
        $user = \App\Models\User::findOrFail($validated['user_id']);

        // Check if user already has this badge
        if ($badge->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم لديه هذه الشارة بالفعل'
            ], 422);
        }

        // Check badge requirements
        $gamification = UserPropertyGamification::where('user_id', $user->id)->first();
        if (!$gamification) {
            $gamification = $this->initializeUserGamification($user);
        }

        if ($gamification->total_points < $badge->points_required) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم ليس لديه نقاط كافية لهذه الشارة'
            ], 422);
        }

        if ($gamification->current_level < $badge->level_required) {
            return response()->json([
                'success' => false,
                'message' => 'مستوى المستخدم منخفض جداً لهذه الشارة'
            ], 422);
        }

        // Award badge
        $badge->users()->attach($user->id, [
            'earned_at' => now(),
            'reason' => $validated['reason'] ?? 'Manual award',
        ]);

        // Update user's badge count
        $gamification->badges_earned++;
        $gamification->save();

        // Award badge points if any
        if ($badge->rewards && isset($badge->rewards['points'])) {
            $this->awardPoints($user->id, $badge->rewards['points'], 'badge_reward', "مكافأة شارة: {$badge->name}");
        }

        return response()->json([
            'success' => true,
            'message' => 'تم منح الشارة بنجاح',
            'data' => $badge
        ]);
    }

    public function revokeBadge(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'badge_id' => 'required|exists:property_badges,id',
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:255',
        ]);

        $badge = PropertyBadge::findOrFail($validated['badge_id']);
        $user = \App\Models\User::findOrFail($validated['user_id']);

        // Check if user has this badge
        if (!$badge->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم ليس لديه هذه الشارة'
            ], 422);
        }

        // Revoke badge
        $badge->users()->detach($user->id);

        // Update user's badge count
        $gamification = UserPropertyGamification::where('user_id', $user->id)->first();
        if ($gamification) {
            $gamification->badges_earned = max(0, $gamification->badges_earned - 1);
            $gamification->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'تم سحب الشارة بنجاح'
        ]);
    }

    public function getUserBadges($userId): JsonResponse
    {
        $user = \App\Models\User::findOrFail($userId);
        
        $badges = $user->badges()
            ->withPivot('earned_at', 'reason')
            ->orderBy('pivot_earned_at', 'desc')
            ->get();

        $stats = [
            'total_badges' => $badges->count(),
            'by_rarity' => $badges->groupBy('rarity')->map->count(),
            'by_category' => $badges->groupBy('category')->map->count(),
            'recent_badges' => $badges->take(5),
        ];

        return response()->json([
            'badges' => $badges,
            'stats' => $stats,
        ]);
    }

    public function getAvailableBadges($userId): JsonResponse
    {
        $user = \App\Models\User::findOrFail($userId);
        $gamification = UserPropertyGamification::where('user_id', $userId)->first();

        if (!$gamification) {
            $gamification = $this->initializeUserGamification($user);
        }

        // Get badges user doesn't have
        $userBadgeIds = $user->badges()->pluck('property_badges.id');
        
        $availableBadges = PropertyBadge::where('status', 'active')
            ->whereNotIn('id', $userBadgeIds)
            ->where('level_required', '<=', $gamification->current_level)
            ->get()
            ->map(function ($badge) use ($gamification) {
                $badge->can_earn = $gamification->total_points >= $badge->points_required;
                $badge->points_shortage = max(0, $badge->points_required - $gamification->total_points);
                return $badge;
            });

        return response()->json($availableBadges);
    }

    public function checkAndAwardBadges($userId): JsonResponse
    {
        $user = \App\Models\User::findOrFail($userId);
        $gamification = UserPropertyGamification::where('user_id', $userId)->first();

        if (!$gamification) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم ليس لديه بيانات ألعاب'
            ]);
        }

        $userBadgeIds = $user->badges()->pluck('property_badges.id');
        $eligibleBadges = PropertyBadge::where('status', 'active')
            ->whereNotIn('id', $userBadgeIds)
            ->where('level_required', '<=', $gamification->current_level)
            ->where('points_required', '<=', $gamification->total_points)
            ->get();

        $awardedBadges = [];

        foreach ($eligibleBadges as $badge) {
            // Check additional requirements if any
            if ($this->checkBadgeRequirements($badge, $user, $gamification)) {
                $user->badges()->attach($badge->id, [
                    'earned_at' => now(),
                    'reason' => 'Automatic award',
                ]);

                $gamification->badges_earned++;
                $awardedBadges[] = $badge;

                // Award badge points if any
                if ($badge->rewards && isset($badge->rewards['points'])) {
                    $this->awardPoints($user->id, $badge->rewards['points'], 'badge_reward', "مكافأة شارة: {$badge->name}");
                }
            }
        }

        if (!empty($awardedBadges)) {
            $gamification->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'تم فحص ومنح الشارات المستحقة',
            'awarded_badges' => $awardedBadges,
            'awarded_count' => count($awardedBadges),
        ]);
    }

    public function getBadgeAnalytics(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        $analytics = [
            'badges_awarded_over_time' => $this->getBadgesAwardedOverTime($dateRange),
            'popular_badges' => $this->getPopularBadges($dateRange),
            'rarity_distribution' => $this->getRarityDistribution(),
            'category_analytics' => $this->getCategoryAnalytics($dateRange),
            'achievement_rate' => $this->getAchievementRate($dateRange),
        ];

        return response()->json($analytics);
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

    private function checkBadgeRequirements($badge, $user, $gamification): bool
    {
        if (empty($badge->requirements)) {
            return true;
        }

        foreach ($badge->requirements as $requirement) {
            switch ($requirement['type']) {
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
                case 'streak_days':
                    if ($gamification->current_streak < $requirement['value']) {
                        return false;
                    }
                    break;
                case 'property_listed':
                    // Check if user has listed properties
                    $propertyCount = \App\Models\Property::where('user_id', $user->id)->count();
                    if ($propertyCount < $requirement['value']) {
                        return false;
                    }
                    break;
                case 'property_sold':
                    // Check if user has sold properties
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

    private function getBadgesAwardedOverTime($dateRange): array
    {
        return \DB::table('badge_user')
            ->join('property_badges', 'badge_user.badge_id', '=', 'property_badges.id')
            ->whereBetween('badge_user.earned_at', $dateRange)
            ->selectRaw('DATE(badge_user.earned_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getPopularBadges($dateRange): array
    {
        return \DB::table('badge_user')
            ->join('property_badges', 'badge_user.badge_id', '=', 'property_badges.id')
            ->whereBetween('badge_user.earned_at', $dateRange)
            ->selectRaw('property_badges.name, property_badges.icon, COUNT(*) as count')
            ->groupBy('property_badges.id', 'property_badges.name', 'property_badges.icon')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get()
            ->toArray();
    }

    private function getRarityDistribution(): array
    {
        return PropertyBadge::selectRaw('rarity, COUNT(*) as total')
            ->groupBy('rarity')
            ->get()
            ->map(function ($item) {
                return [
                    'rarity' => $item->rarity,
                    'total_badges' => $item->total,
                    'awarded_count' => \DB::table('badge_user')
                        ->join('property_badges', 'badge_user.badge_id', '=', 'property_badges.id')
                        ->where('property_badges.rarity', $item->rarity)
                        ->count(),
                ];
            })
            ->toArray();
    }

    private function getCategoryAnalytics($dateRange): array
    {
        return \DB::table('badge_user')
            ->join('property_badges', 'badge_user.badge_id', '=', 'property_badges.id')
            ->whereBetween('badge_user.earned_at', $dateRange)
            ->selectRaw('property_badges.category, COUNT(*) as count')
            ->groupBy('property_badges.category')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    private function getAchievementRate($dateRange): float
    {
        $totalUsers = UserPropertyGamification::count();
        $usersWithBadges = \DB::table('badge_user')
            ->whereBetween('earned_at', $dateRange)
            ->distinct('user_id')
            ->count('user_id');

        return $totalUsers > 0 ? ($usersWithBadges / $totalUsers) * 100 : 0;
    }
}
