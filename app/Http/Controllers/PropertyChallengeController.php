<?php

namespace App\Http\Controllers;

use App\Models\PropertyChallenge;
use App\Models\UserPropertyGamification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class PropertyChallengeController extends Controller
{
    public function index(Request $request): View
    {
        $query = PropertyChallenge::withCount('participants');

        // Filter by search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
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

        $challenges = $query->paginate(20);

        // Get statistics
        $stats = [
            'total_challenges' => PropertyChallenge::count(),
            'active_challenges' => PropertyChallenge::where('status', 'active')->count(),
            'completed_challenges' => PropertyChallenge::where('status', 'completed')->count(),
            'total_participants' => PropertyChallenge::withCount('participants')->get()->sum('participants_count'),
            'challenges_by_difficulty' => PropertyChallenge::selectRaw('difficulty, COUNT(*) as count')
                ->groupBy('difficulty')
                ->get(),
            'challenges_by_type' => PropertyChallenge::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
        ];

        return view('gamification.challenges.index', compact('challenges', 'stats'));
    }

    public function create(): View
    {
        return view('gamification.challenges.create');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'type' => 'required|in:points,level,badges,property,social,custom',
            'difficulty' => 'required|in:easy,medium,hard,expert',
            'status' => 'required|in:draft,active,completed,expired',
            'start_date' => 'required|date|after_or_equal:now',
            'end_date' => 'required|date|after:start_date',
            'max_participants' => 'nullable|integer|min:1',
            'points_reward' => 'required|integer|min:0',
            'badge_reward' => 'nullable|exists:property_badges,id',
            'requirements' => 'required|array',
            'rules' => 'nullable|array',
            'prizes' => 'nullable|array',
            'image' => 'nullable|string|max:255',
        ]);

        $challenge = PropertyChallenge::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء التحدي بنجاح',
            'data' => $challenge
        ]);
    }

    public function show($id): View
    {
        $challenge = PropertyChallenge::with(['participants' => function ($query) {
            $query->take(20);
        }])->findOrFail($id);

        // Get top participants
        $topParticipants = $challenge->participants()
            ->with('user')
            ->orderBy('score', 'desc')
            ->orderBy('completed_at')
            ->take(10)
            ->get();

        // Check if current user is participating
        $userParticipation = null;
        if (auth()->check()) {
            $userParticipation = $challenge->participants()
                ->where('user_id', auth()->id())
                ->first();
        }

        return view('gamification.challenges.show', compact('challenge', 'topParticipants', 'userParticipation'));
    }

    public function edit($id): View
    {
        $challenge = PropertyChallenge::findOrFail($id);
        return view('gamification.challenges.edit', compact('challenge'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $challenge = PropertyChallenge::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'type' => 'required|in:points,level,badges,property,social,custom',
            'difficulty' => 'required|in:easy,medium,hard,expert',
            'status' => 'required|in:draft,active,completed,expired',
            'start_date' => 'required|date|after_or_equal:now',
            'end_date' => 'required|date|after:start_date',
            'max_participants' => 'nullable|integer|min:1',
            'points_reward' => 'required|integer|min:0',
            'badge_reward' => 'nullable|exists:property_badges,id',
            'requirements' => 'required|array',
            'rules' => 'nullable|array',
            'prizes' => 'nullable|array',
            'image' => 'nullable|string|max:255',
        ]);

        $challenge->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث التحدي بنجاح',
            'data' => $challenge
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $challenge = PropertyChallenge::findOrFail($id);
        
        // Check if challenge has participants
        if ($challenge->participants()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف التحدي الذي لديه مشاركين'
            ], 422);
        }

        $challenge->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف التحدي بنجاح'
        ]);
    }

    public function joinChallenge(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'challenge_id' => 'required|exists:property_challenges,id',
        ]);

        $challenge = PropertyChallenge::findOrFail($validated['challenge_id']);
        $user = auth()->user();

        // Check if challenge is active
        if ($challenge->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'التحدي غير متاح للمشاركة'
            ], 422);
        }

        // Check if challenge is within date range
        if (now()->lt($challenge->start_date) || now()->gt($challenge->end_date)) {
            return response()->json([
                'success' => false,
                'message' => 'التحدي خارج فترة المشاركة'
            ], 422);
        }

        // Check if user already joined
        if ($challenge->participants()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'أنت مشارك بالفعل في هذا التحدي'
            ], 422);
        }

        // Check max participants
        if ($challenge->max_participants && $challenge->participants()->count() >= $challenge->max_participants) {
            return response()->json([
                'success' => false,
                'message' => 'وصل التحدي إلى الحد الأقصى للمشاركين'
            ], 422);
        }

        // Join challenge
        $challenge->participants()->attach($user->id, [
            'joined_at' => now(),
            'status' => 'active',
            'score' => 0,
            'progress' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم الانضمام للتحدي بنجاح'
        ]);
    }

    public function leaveChallenge(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'challenge_id' => 'required|exists:property_challenges,id',
        ]);

        $challenge = PropertyChallenge::findOrFail($validated['challenge_id']);
        $user = auth()->user();

        // Check if user is participating
        $participation = $challenge->participants()->where('user_id', $user->id)->first();
        if (!$participation) {
            return response()->json([
                'success' => false,
                'message' => 'أنت لست مشاركاً في هذا التحدي'
            ], 422);
        }

        // Check if challenge is still active
        if ($challenge->status === 'active' && now()->lt($challenge->end_date)) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن ترك التحدي أثناء نشاطه'
            ], 422);
        }

        // Leave challenge
        $challenge->participants()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'تم ترك التحدي بنجاح'
        ]);
    }

    public function updateProgress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'challenge_id' => 'required|exists:property_challenges,id',
            'progress' => 'required|integer|min:0|max:100',
            'score' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $challenge = PropertyChallenge::findOrFail($validated['challenge_id']);
        $user = auth()->user();

        // Get user participation
        $participation = $challenge->participants()->where('user_id', $user->id)->first();
        if (!$participation) {
            return response()->json([
                'success' => false,
                'message' => 'أنت لست مشاركاً في هذا التحدي'
            ], 422);
        }

        // Update progress
        $challenge->participants()->updateExistingPivot($user->id, [
            'progress' => $validated['progress'],
            'score' => $validated['score'],
            'notes' => $validated['notes'],
            'updated_at' => now(),
        ]);

        // Check if challenge is completed
        if ($validated['progress'] >= 100 && !$participation->completed) {
            $this->completeChallenge($challenge, $user, $validated['score']);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث التقدم بنجاح'
        ]);
    }

    public function getUserChallenges($userId): JsonResponse
    {
        $user = \App\Models\User::findOrFail($userId);

        $challenges = PropertyChallenge::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['participants' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])->get();

        $availableChallenges = PropertyChallenge::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereDoesntHave('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->get();

        $stats = [
            'total_participated' => $challenges->count(),
            'completed' => $challenges->where('pivot.completed', true)->count(),
            'in_progress' => $challenges->where('pivot.completed', false)->count(),
            'total_points_earned' => $challenges->where('pivot.completed', true)->sum('points_reward'),
        ];

        return response()->json([
            'participated_challenges' => $challenges,
            'available_challenges' => $availableChallenges,
            'stats' => $stats,
        ]);
    }

    public function getChallengeAnalytics(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        $analytics = [
            'participation_trends' => $this->getParticipationTrends($dateRange),
            'completion_rates' => $this->getCompletionRates($dateRange),
            'popular_challenges' => $this->getPopularChallenges($dateRange),
            'difficulty_distribution' => $this->getDifficultyDistribution($dateRange),
            'challenge_types' => $this->getChallengeTypeStats($dateRange),
        ];

        return response()->json($analytics);
    }

    private function completeChallenge($challenge, $user, $score): void
    {
        // Mark as completed
        $challenge->participants()->updateExistingPivot($user->id, [
            'completed' => true,
            'completed_at' => now(),
            'final_score' => $score,
        ]);

        // Update user's gamification stats
        $gamification = UserPropertyGamification::where('user_id', $user->id)->first();
        if ($gamification) {
            $gamification->challenges_completed++;
            $gamification->save();
        }

        // Award points
        if ($challenge->points_reward > 0) {
            $this->awardPoints($user->id, $challenge->points_reward, 'challenge_reward', "مكافأة تحدي: {$challenge->title}");
        }

        // Award badge if specified
        if ($challenge->badge_reward) {
            $this->awardBadge($user->id, $challenge->badge_reward, "إكمال تحدي: {$challenge->title}");
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

    private function getParticipationTrends($dateRange): array
    {
        return \DB::table('challenge_user')
            ->join('property_challenges', 'challenge_user.challenge_id', '=', 'property_challenges.id')
            ->whereBetween('challenge_user.joined_at', $dateRange)
            ->selectRaw('DATE(challenge_user.joined_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getCompletionRates($dateRange): array
    {
        return \DB::table('challenge_user')
            ->join('property_challenges', 'challenge_user.challenge_id', '=', 'property_challenges.id')
            ->whereBetween('challenge_user.joined_at', $dateRange)
            ->selectRaw('property_challenges.difficulty, 
                         COUNT(*) as total,
                         SUM(CASE WHEN challenge_user.completed = 1 THEN 1 ELSE 0 END) as completed')
            ->groupBy('property_challenges.difficulty')
            ->get()
            ->map(function ($item) {
                return [
                    'difficulty' => $item->difficulty,
                    'total' => $item->total,
                    'completed' => $item->completed,
                    'completion_rate' => $item->total > 0 ? ($item->completed / $item->total) * 100 : 0,
                ];
            })
            ->toArray();
    }

    private function getPopularChallenges($dateRange): array
    {
        return PropertyChallenge::whereBetween('created_at', $dateRange)
            ->withCount('participants')
            ->orderBy('participants_count', 'desc')
            ->take(10)
            ->get()
            ->toArray();
    }

    private function getDifficultyDistribution($dateRange): array
    {
        return PropertyChallenge::whereBetween('created_at', $dateRange)
            ->selectRaw('difficulty, COUNT(*) as count')
            ->groupBy('difficulty')
            ->get()
            ->toArray();
    }

    private function getChallengeTypeStats($dateRange): array
    {
        return PropertyChallenge::whereBetween('created_at', $dateRange)
            ->selectRaw('type, COUNT(*) as count, AVG(points_reward) as avg_reward')
            ->groupBy('type')
            ->get()
            ->toArray();
    }
}
