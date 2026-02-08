<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Services\GamificationService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GamificationController extends Controller
{
    private GamificationService $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function dashboard()
    {
        $user = auth()->user();
        $stats = $this->gamificationService->getUserStats($user->id);
        $achievements = $this->gamificationService->getUserAchievements($user->id);
        $badges = $this->gamificationService->getUserBadges($user->id);
        $challenges = $this->gamificationService->getUserChallenges($user->id);
        
        return view('gamification.dashboard', compact('stats', 'achievements', 'badges', 'challenges'));
    }

    public function achievements()
    {
        $user = auth()->user();
        $achievements = $this->gamificationService->getUserAchievements($user->id);
        $availableAchievements = $this->gamificationService->getAvailableAchievements();
        
        return view('gamification.achievements', compact('achievements', 'availableAchievements'));
    }

    public function badges()
    {
        $user = auth()->user();
        $badges = $this->gamificationService->getUserBadges($user->id);
        $availableBadges = $this->gamificationService->getAvailableBadges();
        
        return view('gamification.badges', compact('badges', 'availableBadges'));
    }

    public function challenges()
    {
        $user = auth()->user();
        $challenges = $this->gamificationService->getUserChallenges($user->id);
        $availableChallenges = $this->gamificationService->getAvailableChallenges();
        
        return view('gamification.challenges', compact('challenges', 'availableChallenges'));
    }

    public function leaderboard(Request $request)
    {
        // If it's an AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $leaderboard = $this->gamificationService->getLeaderboard(
                    $request->type ?? 'points',
                    $request->all()
                );
                
                // Pagination logic (manual since getLeaderboard returns collection)
                $page = $request->page ?? 1;
                $limit = $request->limit ?? 10;
                $offset = ($page - 1) * $limit;
                
                $paginatedItems = $leaderboard->slice($offset, $limit)->values();
                
                return response()->json([
                    'success' => true,
                    'leaderboard' => $paginatedItems,
                    'pagination' => [
                        'current_page' => (int)$page,
                        'total_pages' => ceil($leaderboard->count() / $limit),
                        'total_items' => $leaderboard->count(),
                        'limit' => (int)$limit
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load leaderboard: ' . $e->getMessage()
                ], 500);
            }
        }

        $leaderboard = $this->gamificationService->getLeaderboard();
        $userRank = $this->gamificationService->getUserRank(auth()->user()->id);
        
        return view('gamification.leaderboard', compact('leaderboard', 'userRank'));
    }

    public function getLeaderboardStats(Request $request): JsonResponse
    {
        try {
            $stats = $this->gamificationService->getLeaderboardStatistics(
                $request->type ?? 'points',
                $request->period ?? 'all_time'
            );
            
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTopPerformers(Request $request): JsonResponse
    {
        try {
            $performers = $this->gamificationService->getTopPerformers(
                $request->type ?? 'points',
                $request->period ?? 'all_time'
            );
            
            return response()->json([
                'success' => true,
                'top_performers' => $performers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get top performers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCategoryDistribution(Request $request): JsonResponse
    {
        try {
            $distribution = $this->gamificationService->getCategoryDistribution(
                $request->type ?? 'points',
                $request->period ?? 'all_time'
            );
            
            return response()->json([
                'success' => true,
                'distribution' => $distribution
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get distribution',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserPosition(Request $request): JsonResponse
    {
        try {
            $position = $this->gamificationService->getUserLeaderboardPosition(auth()->id());
            
            return response()->json([
                'success' => true,
                'position' => $position
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get user position',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportLeaderboard(Request $request)
    {
        // Implementation for export would go here
        // For now just redirect back
        return redirect()->back()->with('success', 'Export started');
    }

    public function rewards(Request $request)
    {
        $user = auth()->user();
        $rewards = $this->gamificationService->getUserRewards($user->id);
        $availableRewards = $this->gamificationService->getAvailableRewards();
        
        // If it's an AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'rewards' => $availableRewards,
                'user_rewards' => $rewards,
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => count($availableRewards),
                    'total' => count($availableRewards)
                ]
            ]);
        }
        
        // Otherwise return the view
        return view('gamification.rewards', compact('rewards', 'availableRewards'));
    }

    public function trackActivity(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'activity' => 'required|string',
                'metadata' => 'array'
            ]);

            $result = $this->gamificationService->trackUserActivity(
                auth()->id(),
                $request->activity,
                $request->metadata ?? []
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to track activity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getProfile(): JsonResponse
    {
        try {
            $result = $this->gamificationService->getUserGamificationProfile(auth()->id());
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLeaderboard(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'type' => 'in:global,weekly,monthly,challenge,regional'
            ]);

            $result = $this->gamificationService->getLeaderboard(
                $request->type ?? 'global',
                $request->all()
            );
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get leaderboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStatistics(): JsonResponse
    {
        try {
            $result = $this->gamificationService->getGamificationStatistics();
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
