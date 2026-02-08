<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Services\GamificationService;
use App\Models\Gamification\Achievement;
use App\Models\Gamification\Badge;
use App\Models\Gamification\Reward;
use App\Models\Gamification\Challenge;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExtendedGamificationController extends Controller
{
    private GamificationService $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function index()
    {
        return view('gamification.index');
    }

    public function achievements()
    {
        $achievements = Achievement::with(['userAchievements' => function($query) {
            $query->where('user_id', auth()->id());
        }])->get();

        return view('gamification.achievements', compact('achievements'));
    }

    public function badges()
    {
        $badges = Badge::with(['userBadges' => function($query) {
            $query->where('user_id', auth()->id());
        }])->get();

        return view('gamification.badges', compact('badges'));
    }

    public function rewards()
    {
        $rewards = Reward::with(['userRewards' => function($query) {
            $query->where('user_id', auth()->id());
        }])->get();

        return view('gamification.rewards', compact('rewards'));
    }

    public function challenges()
    {
        $challenges = Challenge::with(['userChallenges' => function($query) {
            $query->where('user_id', auth()->id());
        }])->where('is_active', true)->get();

        return view('gamification.challenges', compact('challenges'));
    }

    public function leaderboard()
    {
        $result = $this->gamificationService->getLeaderboard('global');
        return view('gamification.leaderboard', [
            'leaderboard' => $result['leaderboard'] ?? []
        ]);
    }

    public function joinChallenge(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'challenge_id' => 'required|integer|exists:challenges,id'
            ]);

            $result = $this->gamificationService->joinChallenge(
                auth()->id(),
                $request->challenge_id
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to join challenge',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function claimReward(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'reward_id' => 'required|integer|exists:rewards,id'
            ]);

            $result = $this->gamificationService->awardReward(
                auth()->id(),
                $request->reward_id
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to claim reward',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
