<?php

namespace App\Http\Controllers;

use App\Models\PropertyLeaderboard;
use App\Models\UserPropertyGamification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class PropertyLeaderboardController extends Controller
{
    public function index(Request $request): View
    {
        $query = PropertyLeaderboard::with('user');

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        // Filter by period
        if ($request->has('period')) {
            $query->where('period', $request->get('period'));
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->get('category'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'rank');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $leaderboards = $query->paginate(20);

        // Get statistics
        $stats = [
            'total_leaderboards' => PropertyLeaderboard::distinct('type')->count('type'),
            'total_participants' => PropertyLeaderboard::distinct('user_id')->count('user_id'),
            'leaderboards_by_type' => PropertyLeaderboard::selectRaw('type, COUNT(DISTINCT user_id) as participants')
                ->groupBy('type')
                ->get(),
            'leaderboards_by_period' => PropertyLeaderboard::selectRaw('period, COUNT(DISTINCT user_id) as participants')
                ->groupBy('period')
                ->get(),
        ];

        return view('gamification.leaderboard.index', compact('leaderboards', 'stats'));
    }

    public function create(): View
    {
        $users = \App\Models\User::orderBy('name')->get();
        return view('gamification.leaderboard.create', compact('users'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:points,level,badges,challenges,quests',
            'period' => 'required|in:daily,weekly,monthly,quarterly,yearly,all_time',
            'category' => 'nullable|string|max:100',
            'score' => 'required|integer|min:0',
            'rank' => 'required|integer|min:1',
            'previous_rank' => 'nullable|integer|min:1',
            'change' => 'nullable|integer',
        ]);

        // Check if entry already exists
        $existing = PropertyLeaderboard::where('user_id', $validated['user_id'])
            ->where('type', $validated['type'])
            ->where('period', $validated['period'])
            ->where('category', $validated['category'] ?? 'general')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم موجود بالفعل في لوحة المتصدرين لهذه الفئة'
            ], 422);
        }

        $leaderboard = PropertyLeaderboard::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة المستخدم إلى لوحة المتصدرين بنجاح',
            'data' => $leaderboard
        ]);
    }

    public function show($id): View
    {
        $leaderboard = PropertyLeaderboard::with('user')->findOrFail($id);
        
        return view('gamification.leaderboard.show', compact('leaderboard'));
    }

    public function edit($id): View
    {
        $leaderboard = PropertyLeaderboard::findOrFail($id);
        $users = \App\Models\User::orderBy('name')->get();
        
        return view('gamification.leaderboard.edit', compact('leaderboard', 'users'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $leaderboard = PropertyLeaderboard::findOrFail($id);

        $validated = $request->validate([
            'score' => 'required|integer|min:0',
            'rank' => 'required|integer|min:1',
            'previous_rank' => 'nullable|integer|min:1',
            'change' => 'nullable|integer',
        ]);

        $leaderboard->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث لوحة المتصدرين بنجاح',
            'data' => $leaderboard
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $leaderboard = PropertyLeaderboard::findOrFail($id);
        $leaderboard->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف السجل من لوحة المتصدرين بنجاح'
        ]);
    }

    public function getLeaderboard(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:points,level,badges,challenges,quests',
            'period' => 'required|in:daily,weekly,monthly,quarterly,yearly,all_time',
            'category' => 'nullable|string|max:100',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $limit = $validated['limit'] ?? 50;

        $leaderboard = PropertyLeaderboard::with('user')
            ->where('type', $validated['type'])
            ->where('period', $validated['period'])
            ->where('category', $validated['category'] ?? 'general')
            ->orderBy('rank')
            ->take($limit)
            ->get();

        // Get user's position if logged in
        $userPosition = null;
        if (auth()->check()) {
            $userPosition = PropertyLeaderboard::where('user_id', auth()->id())
                ->where('type', $validated['type'])
                ->where('period', $validated['period'])
                ->where('category', $validated['category'] ?? 'general')
                ->first();
        }

        return response()->json([
            'leaderboard' => $leaderboard,
            'user_position' => $userPosition,
            'total_participants' => PropertyLeaderboard::where('type', $validated['type'])
                ->where('period', $validated['period'])
                ->where('category', $validated['category'] ?? 'general')
                ->count(),
        ]);
    }

    public function generateLeaderboard(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:points,level,badges,challenges,quests',
            'period' => 'required|in:daily,weekly,monthly,quarterly,yearly,all_time',
            'category' => 'nullable|string|max:100',
        ]);

        // Clear existing leaderboard for this type/period/category
        PropertyLeaderboard::where('type', $validated['type'])
            ->where('period', $validated['period'])
            ->where('category', $validated['category'] ?? 'general')
            ->delete();

        // Get users data based on type
        $usersData = $this->getUsersDataByType($validated['type'], $validated['period']);

        // Rank users
        $rankedUsers = $usersData->sortByDesc('score')->values();
        $rank = 1;

        foreach ($rankedUsers as $userData) {
            // Get previous rank if exists
            $previousEntry = PropertyLeaderboard::where('user_id', $userData['user_id'])
                ->where('type', $validated['type'])
                ->where('period', $this->getPreviousPeriod($validated['period']))
                ->where('category', $validated['category'] ?? 'general')
                ->first();

            PropertyLeaderboard::create([
                'user_id' => $userData['user_id'],
                'type' => $validated['type'],
                'period' => $validated['period'],
                'category' => $validated['category'] ?? 'general',
                'score' => $userData['score'],
                'rank' => $rank,
                'previous_rank' => $previousEntry ? $previousEntry->rank : null,
                'change' => $previousEntry ? ($previousEntry->rank - $rank) : null,
            ]);

            $rank++;
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء لوحة المتصدرين بنجاح',
            'participants' => $rankedUsers->count(),
        ]);
    }

    public function getUserRankings($userId): JsonResponse
    {
        $user = \App\Models\User::findOrFail($userId);

        $rankings = PropertyLeaderboard::where('user_id', $userId)
            ->orderBy('period', 'desc')
            ->orderBy('type')
            ->get()
            ->groupBy('type');

        $bestRankings = [];
        foreach ($rankings as $type => $typeRankings) {
            $bestRankings[$type] = $typeRankings->sortBy('rank')->first();
        }

        return response()->json([
            'user' => $user,
            'rankings' => $rankings,
            'best_rankings' => $bestRankings,
        ]);
    }

    public function getLeaderboardAnalytics(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        $analytics = [
            'top_performers' => $this->getTopPerformers($dateRange),
            'rank_changes' => $this->getRankChanges($dateRange),
            'participation_trends' => $this->getParticipationTrends($dateRange),
            'category_performance' => $this->getCategoryPerformance($dateRange),
            'leaderboard_types' => $this->getLeaderboardTypeStats($dateRange),
        ];

        return response()->json($analytics);
    }

    public function exportLeaderboard(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:points,level,badges,challenges,quests',
            'period' => 'required|in:daily,weekly,monthly,quarterly,yearly,all_time',
            'category' => 'nullable|string|max:100',
            'format' => 'required|in:csv,xlsx,pdf',
        ]);

        $leaderboard = PropertyLeaderboard::with('user')
            ->where('type', $validated['type'])
            ->where('period', $validated['period'])
            ->where('category', $validated['category'] ?? 'general')
            ->orderBy('rank')
            ->get();

        $filename = "leaderboard_{$validated['type']}_{$validated['period']}_" . date('Y-m-d_H-i-s') . ".{$validated['format']}";

        return response()->json([
            'success' => true,
            'message' => 'تم تصدير لوحة المتصدرين بنجاح',
            'filename' => $filename,
            'count' => $leaderboard->count(),
        ]);
    }

    private function getUsersDataByType($type, $period): \Illuminate\Support\Collection
    {
        switch ($type) {
            case 'points':
                return $this->getPointsData($period);
            case 'level':
                return $this->getLevelData($period);
            case 'badges':
                return $this->getBadgesData($period);
            case 'challenges':
                return $this->getChallengesData($period);
            case 'quests':
                return $this->getQuestsData($period);
            default:
                return collect();
        }
    }

    private function getPointsData($period): \Illuminate\Support\Collection
    {
        $dateRange = $this->getDateRangeForPeriod($period);

        return \DB::table('property_points')
            ->selectRaw('user_id, SUM(points) as score')
            ->whereBetween('created_at', $dateRange)
            ->groupBy('user_id')
            ->get()
            ->map(function ($item) {
                return [
                    'user_id' => $item->user_id,
                    'score' => (int) $item->score,
                ];
            });
    }

    private function getLevelData($period): \Illuminate\Support\Collection
    {
        return UserPropertyGamification::select('user_id', 'current_level as score')
            ->where('current_level', '>', 1)
            ->get();
    }

    private function getBadgesData($period): \Illuminate\Support\Collection
    {
        return \DB::table('badge_user')
            ->selectRaw('user_id, COUNT(*) as score')
            ->groupBy('user_id')
            ->get()
            ->map(function ($item) {
                return [
                    'user_id' => $item->user_id,
                    'score' => (int) $item->score,
                ];
            });
    }

    private function getChallengesData($period): \Illuminate\Support\Collection
    {
        return UserPropertyGamification::select('user_id', 'challenges_completed as score')
            ->where('challenges_completed', '>', 0)
            ->get();
    }

    private function getQuestsData($period): \Illuminate\Support\Collection
    {
        return UserPropertyGamification::select('user_id', 'quests_completed as score')
            ->where('quests_completed', '>', 0)
            ->get();
    }

    private function getDateRangeForPeriod($period): array
    {
        $now = now();
        
        switch ($period) {
            case 'daily':
                return [$now->startOfDay(), $now->endOfDay()];
            case 'weekly':
                return [$now->startOfWeek(), $now->endOfWeek()];
            case 'monthly':
                return [$now->startOfMonth(), $now->endOfMonth()];
            case 'quarterly':
                return [$now->startOfQuarter(), $now->endOfQuarter()];
            case 'yearly':
                return [$now->startOfYear(), $now->endOfYear()];
            case 'all_time':
                return ['2020-01-01', $now];
            default:
                return [$now->startOfMonth(), $now->endOfMonth()];
        }
    }

    private function getPreviousPeriod($period): string
    {
        switch ($period) {
            case 'daily':
                return 'daily';
            case 'weekly':
                return 'weekly';
            case 'monthly':
                return 'monthly';
            case 'quarterly':
                return 'quarterly';
            case 'yearly':
                return 'yearly';
            case 'all_time':
                return 'all_time';
            default:
                return 'monthly';
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

    private function getTopPerformers($dateRange): array
    {
        return PropertyLeaderboard::with('user')
            ->whereBetween('created_at', $dateRange)
            ->orderBy('rank')
            ->take(10)
            ->get()
            ->groupBy('type')
            ->map(function ($group) {
                return $group->take(3);
            })
            ->toArray();
    }

    private function getRankChanges($dateRange): array
    {
        return PropertyLeaderboard::whereBetween('created_at', $dateRange)
            ->whereNotNull('change')
            ->selectRaw('type, AVG(change) as avg_change, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->toArray();
    }

    private function getParticipationTrends($dateRange): array
    {
        return PropertyLeaderboard::whereBetween('created_at', $dateRange)
            ->selectRaw('DATE(created_at) as date, COUNT(DISTINCT user_id) as participants')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getCategoryPerformance($dateRange): array
    {
        return PropertyLeaderboard::whereBetween('created_at', $dateRange)
            ->selectRaw('category, COUNT(DISTINCT user_id) as participants, AVG(rank) as avg_rank')
            ->groupBy('category')
            ->get()
            ->toArray();
    }

    private function getLeaderboardTypeStats($dateRange): array
    {
        return PropertyLeaderboard::whereBetween('created_at', $dateRange)
            ->selectRaw('type, period, COUNT(DISTINCT user_id) as participants')
            ->groupBy('type', 'period')
            ->get()
            ->toArray();
    }
}
