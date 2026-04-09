<?php

namespace App\Http\Controllers;

use App\Models\PropertyQuest;
use App\Models\UserPropertyGamification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class PropertyQuestController extends Controller
{
    public function index(Request $request): View
    {
        $query = PropertyQuest::withCount('participants');

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

        $quests = $query->paginate(20);

        // Get statistics
        $stats = [
            'total_quests' => PropertyQuest::count(),
            'active_quests' => PropertyQuest::where('status', 'active')->count(),
            'completed_quests' => PropertyQuest::where('status', 'completed')->count(),
            'total_participants' => PropertyQuest::withCount('participants')->get()->sum('participants_count'),
            'quests_by_difficulty' => PropertyQuest::selectRaw('difficulty, COUNT(*) as count')
                ->groupBy('difficulty')
                ->get(),
            'quests_by_type' => PropertyQuest::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
        ];

        return view('gamification.quests.index', compact('quests', 'stats'));
    }

    public function create(): View
    {
        return view('gamification.quests.create');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'type' => 'required|in:daily,weekly,monthly,story,seasonal,custom',
            'difficulty' => 'required|in:easy,medium,hard,expert',
            'status' => 'required|in:draft,active,completed,expired',
            'start_date' => 'required|date|after_or_equal:now',
            'end_date' => 'required|date|after:start_date',
            'max_participants' => 'nullable|integer|min:1',
            'points_reward' => 'required|integer|min:0',
            'experience_reward' => 'required|integer|min:0',
            'badge_reward' => 'nullable|exists:property_badges,id',
            'objectives' => 'required|array',
            'rewards' => 'nullable|array',
            'prerequisites' => 'nullable|array',
            'image' => 'nullable|string|max:255',
            'story_text' => 'nullable|string',
        ]);

        $quest = PropertyQuest::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المهمة بنجاح',
            'data' => $quest
        ]);
    }

    public function show($id): View
    {
        $quest = PropertyQuest::with(['participants' => function ($query) {
            $query->take(20);
        }])->findOrFail($id);

        // Get top participants
        $topParticipants = $quest->participants()
            ->with('user')
            ->orderBy('progress', 'desc')
            ->orderBy('completed_at')
            ->take(10)
            ->get();

        // Check if current user is participating
        $userParticipation = null;
        if (auth()->check()) {
            $userParticipation = $quest->participants()
                ->where('user_id', auth()->id())
                ->first();
        }

        return view('gamification.quests.show', compact('quest', 'topParticipants', 'userParticipation'));
    }

    public function edit($id): View
    {
        $quest = PropertyQuest::findOrFail($id);
        return view('gamification.quests.edit', compact('quest'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $quest = PropertyQuest::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'type' => 'required|in:daily,weekly,monthly,story,seasonal,custom',
            'difficulty' => 'required|in:easy,medium,hard,expert',
            'status' => 'required|in:draft,active,completed,expired',
            'start_date' => 'required|date|after_or_equal:now',
            'end_date' => 'required|date|after:start_date',
            'max_participants' => 'nullable|integer|min:1',
            'points_reward' => 'required|integer|min:0',
            'experience_reward' => 'required|integer|min:0',
            'badge_reward' => 'nullable|exists:property_badges,id',
            'objectives' => 'required|array',
            'rewards' => 'nullable|array',
            'prerequisites' => 'nullable|array',
            'image' => 'nullable|string|max:255',
            'story_text' => 'nullable|string',
        ]);

        $quest->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المهمة بنجاح',
            'data' => $quest
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $quest = PropertyQuest::findOrFail($id);
        
        // Check if quest has participants
        if ($quest->participants()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف المهمة التي لديها مشاركين'
            ], 422);
        }

        $quest->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المهمة بنجاح'
        ]);
    }

    public function acceptQuest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'quest_id' => 'required|exists:property_quests,id',
        ]);

        $quest = PropertyQuest::findOrFail($validated['quest_id']);
        $user = auth()->user();

        // Check if quest is active
        if ($quest->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'المهمة غير متاحة للمشاركة'
            ], 422);
        }

        // Check if quest is within date range
        if (now()->lt($quest->start_date) || now()->gt($quest->end_date)) {
            return response()->json([
                'success' => false,
                'message' => 'المهمة خارج فترة المشاركة'
            ], 422);
        }

        // Check if user already accepted
        if ($quest->participants()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'أنت مشارك بالفعل في هذه المهمة'
            ], 422);
        }

        // Check prerequisites
        if (!$this->checkPrerequisites($quest, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'أنت لا تفي بمتطلبات المهمة'
            ], 422);
        }

        // Check max participants
        if ($quest->max_participants && $quest->participants()->count() >= $quest->max_participants) {
            return response()->json([
                'success' => false,
                'message' => 'وصلت المهمة إلى الحد الأقصى للمشاركين'
            ], 422);
        }

        // Accept quest
        $quest->participants()->attach($user->id, [
            'accepted_at' => now(),
            'status' => 'active',
            'progress' => 0,
            'objectives_completed' => [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم قبول المهمة بنجاح'
        ]);
    }

    public function abandonQuest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'quest_id' => 'required|exists:property_quests,id',
        ]);

        $quest = PropertyQuest::findOrFail($validated['quest_id']);
        $user = auth()->user();

        // Check if user is participating
        $participation = $quest->participants()->where('user_id', $user->id)->first();
        if (!$participation) {
            return response()->json([
                'success' => false,
                'message' => 'أنت لست مشاركاً في هذه المهمة'
            ], 422);
        }

        // Check if quest is already completed
        if ($participation->completed) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن التخلي عن المهمة المكتملة'
            ], 422);
        }

        // Abandon quest
        $quest->participants()->updateExistingPivot($user->id, [
            'status' => 'abandoned',
            'abandoned_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم التخلي عن المهمة بنجاح'
        ]);
    }

    public function updateQuestProgress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'quest_id' => 'required|exists:property_quests,id',
            'objective_index' => 'required|integer|min:0',
            'completed' => 'required|boolean',
            'progress_data' => 'nullable|array',
        ]);

        $quest = PropertyQuest::findOrFail($validated['quest_id']);
        $user = auth()->user();

        // Get user participation
        $participation = $quest->participants()->where('user_id', $user->id)->first();
        if (!$participation) {
            return response()->json([
                'success' => false,
                'message' => 'أنت لست مشاركاً في هذه المهمة'
            ], 422);
        }

        // Update objectives
        $objectivesCompleted = $participation->objectives_completed ?? [];
        $objectivesCompleted[$validated['objective_index']] = [
            'completed' => $validated['completed'],
            'completed_at' => $validated['completed'] ? now() : null,
            'progress_data' => $validated['progress_data'] ?? [],
        ];

        // Calculate overall progress
        $totalObjectives = count($quest->objectives);
        $completedObjectives = count(array_filter($objectivesCompleted, function ($obj) {
            return $obj['completed'] ?? false;
        }));
        $progress = ($completedObjectives / $totalObjectives) * 100;

        // Update participation
        $quest->participants()->updateExistingPivot($user->id, [
            'progress' => $progress,
            'objectives_completed' => $objectivesCompleted,
            'updated_at' => now(),
        ]);

        // Check if quest is completed
        if ($progress >= 100 && !$participation->completed) {
            $this->completeQuest($quest, $user, $objectivesCompleted);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث تقدم المهمة بنجاح',
            'progress' => $progress,
            'objectives_completed' => $completedObjectives,
        ]);
    }

    public function getUserQuests($userId): JsonResponse
    {
        $user = \App\Models\User::findOrFail($userId);

        $userQuests = PropertyQuest::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['participants' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])->get();

        $availableQuests = PropertyQuest::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereDoesntHave('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->get()
            ->map(function ($quest) use ($userId) {
                $quest->can_accept = $this->checkPrerequisites($quest, \App\Models\User::find($userId));
                return $quest;
            });

        $stats = [
            'total_accepted' => $userQuests->count(),
            'completed' => $userQuests->where('pivot.completed', true)->count(),
            'in_progress' => $userQuests->where('pivot.completed', false)->where('pivot.status', 'active')->count(),
            'abandoned' => $userQuests->where('pivot.status', 'abandoned')->count(),
            'total_points_earned' => $userQuests->where('pivot.completed', true)->sum('points_reward'),
        ];

        return response()->json([
            'user_quests' => $userQuests,
            'available_quests' => $availableQuests,
            'stats' => $stats,
        ]);
    }

    public function getQuestAnalytics(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        $analytics = [
            'participation_trends' => $this->getParticipationTrends($dateRange),
            'completion_rates' => $this->getCompletionRates($dateRange),
            'popular_quests' => $this->getPopularQuests($dateRange),
            'difficulty_distribution' => $this->getDifficultyDistribution($dateRange),
            'quest_types' => $this->getQuestTypeStats($dateRange),
        ];

        return response()->json($analytics);
    }

    private function checkPrerequisites($quest, $user): bool
    {
        if (empty($quest->prerequisites)) {
            return true;
        }

        $gamification = UserPropertyGamification::where('user_id', $user->id)->first();
        if (!$gamification) {
            return false;
        }

        foreach ($quest->prerequisites as $prerequisite) {
            switch ($prerequisite['type']) {
                case 'min_level':
                    if ($gamification->current_level < $prerequisite['value']) {
                        return false;
                    }
                    break;
                case 'min_points':
                    if ($gamification->total_points < $prerequisite['value']) {
                        return false;
                    }
                    break;
                case 'completed_quests':
                    if ($gamification->quests_completed < $prerequisite['value']) {
                        return false;
                    }
                    break;
                case 'specific_quest':
                    $completedQuest = PropertyQuest::whereHas('participants', function ($query) use ($user) {
                        $query->where('user_id', $user->id)->where('completed', true);
                    })->where('id', $prerequisite['quest_id'])->first();
                    if (!$completedQuest) {
                        return false;
                    }
                    break;
                case 'badge_required':
                    $hasBadge = $user->badges()->where('badge_id', $prerequisite['badge_id'])->exists();
                    if (!$hasBadge) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    private function completeQuest($quest, $user, $objectivesCompleted): void
    {
        // Mark as completed
        $quest->participants()->updateExistingPivot($user->id, [
            'completed' => true,
            'completed_at' => now(),
            'final_progress' => 100,
            'objectives_completed' => $objectivesCompleted,
        ]);

        // Update user's gamification stats
        $gamification = UserPropertyGamification::where('user_id', $user->id)->first();
        if ($gamification) {
            $gamification->quests_completed++;
            $gamification->save();
        }

        // Award points
        if ($quest->points_reward > 0) {
            $this->awardPoints($user->id, $quest->points_reward, 'quest_reward', "مكافأة مهمة: {$quest->title}");
        }

        // Award experience
        if ($quest->experience_reward > 0) {
            $this->awardExperience($user->id, $quest->experience_reward);
        }

        // Award badge if specified
        if ($quest->badge_reward) {
            $this->awardBadge($user->id, $quest->badge_reward, "إكمال مهمة: {$quest->title}");
        }
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

    private function awardExperience($userId, $experience): void
    {
        $gamification = UserPropertyGamification::where('user_id', $userId)->first();
        if ($gamification) {
            $gamification->experience_points += $experience;
            $gamification->save();
        }
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
        return \DB::table('quest_user')
            ->join('property_quests', 'quest_user.quest_id', '=', 'property_quests.id')
            ->whereBetween('quest_user.accepted_at', $dateRange)
            ->selectRaw('DATE(quest_user.accepted_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getCompletionRates($dateRange): array
    {
        return \DB::table('quest_user')
            ->join('property_quests', 'quest_user.quest_id', '=', 'property_quests.id')
            ->whereBetween('quest_user.accepted_at', $dateRange)
            ->selectRaw('property_quests.difficulty, 
                         COUNT(*) as total,
                         SUM(CASE WHEN quest_user.completed = 1 THEN 1 ELSE 0 END) as completed')
            ->groupBy('property_quests.difficulty')
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

    private function getPopularQuests($dateRange): array
    {
        return PropertyQuest::whereBetween('created_at', $dateRange)
            ->withCount('participants')
            ->orderBy('participants_count', 'desc')
            ->take(10)
            ->get()
            ->toArray();
    }

    private function getDifficultyDistribution($dateRange): array
    {
        return PropertyQuest::whereBetween('created_at', $dateRange)
            ->selectRaw('difficulty, COUNT(*) as count')
            ->groupBy('difficulty')
            ->get()
            ->toArray();
    }

    private function getQuestTypeStats($dateRange): array
    {
        return PropertyQuest::whereBetween('created_at', $dateRange)
            ->selectRaw('type, COUNT(*) as count, AVG(points_reward) as avg_reward')
            ->groupBy('type')
            ->get()
            ->toArray();
    }
}
