<?php

namespace App\Services;

use App\Models\User;
use App\Models\Gamification\Achievement;
use App\Models\Gamification\Badge;
use App\Models\PropertyLeaderboard as Leaderboard;
use App\Models\Gamification\Reward;
use App\Models\Gamification\UserAchievement;
use App\Models\Gamification\UserBadge;
use App\Models\Gamification\UserReward;
use App\Models\Gamification\UserLevel;
use App\Models\Gamification\Challenge;
use App\Models\Gamification\UserChallenge;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class GamificationService
{
    private const ACHIEVEMENT_TYPES = [
        'property_listing' => [
            'first_property' => ['points' => 100, 'badge_id' => 1],
            '10_properties' => ['points' => 500, 'badge_id' => 2],
            '50_properties' => ['points' => 2000, 'badge_id' => 3],
            '100_properties' => ['points' => 5000, 'badge_id' => 4]
        ],
        'property_sale' => [
            'first_sale' => ['points' => 200, 'badge_id' => 5],
            '10_sales' => ['points' => 1000, 'badge_id' => 6],
            '50_sales' => ['points' => 5000, 'badge_id' => 7],
            'million_dollar_sales' => ['points' => 3000, 'badge_id' => 8]
        ],
        'user_engagement' => [
            'daily_login' => ['points' => 10, 'badge_id' => 9],
            '7_day_streak' => ['points' => 100, 'badge_id' => 10],
            '30_day_streak' => ['points' => 500, 'badge_id' => 11],
            'profile_complete' => ['points' => 50, 'badge_id' => 12]
        ],
        'social_activity' => [
            'first_review' => ['points' => 25, 'badge_id' => 13],
            '10_reviews' => ['points' => 200, 'badge_id' => 14],
            'helpful_votes' => ['points' => 150, 'badge_id' => 15],
            'community_leader' => ['points' => 1000, 'badge_id' => 16]
        ],
        'investment' => [
            'first_investment' => ['points' => 300, 'badge_id' => 17],
            'diversified_portfolio' => ['points' => 800, 'badge_id' => 18],
            'profitable_investor' => ['points' => 1500, 'badge_id' => 19],
            'investment_guru' => ['points' => 5000, 'badge_id' => 20]
        ]
    ];

    private const LEVEL_THRESHOLDS = [
        1 => ['name' => 'Beginner', 'min_points' => 0, 'max_points' => 499, 'color' => '#808080'],
        2 => ['name' => 'Novice', 'min_points' => 500, 'max_points' => 1499, 'color' => '#8BC34A'],
        3 => ['name' => 'Intermediate', 'min_points' => 1500, 'max_points' => 4999, 'color' => '#2196F3'],
        4 => ['name' => 'Advanced', 'min_points' => 5000, 'max_points' => 14999, 'color' => '#9C27B0'],
        5 => ['name' => 'Expert', 'min_points' => 15000, 'max_points' => 49999, 'color' => '#FF9800'],
        6 => ['name' => 'Master', 'min_points' => 50000, 'max_points' => 149999, 'color' => '#F44336'],
        7 => ['name' => 'Grandmaster', 'min_points' => 150000, 'max_points' => 499999, 'color' => '#E91E63'],
        8 => ['name' => 'Legend', 'min_points' => 500000, 'max_points' => PHP_INT_MAX, 'color' => '#FFD700']
    ];

    private const REWARD_TYPES = [
        'discount' => [
            'name' => 'Discount Coupon',
            'description' => 'Get discount on services',
            'value_type' => 'percentage',
            'expiry_days' => 30
        ],
        'feature_unlock' => [
            'name' => 'Feature Unlock',
            'description' => 'Unlock premium features',
            'value_type' => 'boolean',
            'expiry_days' => null
        ],
        'badge' => [
            'name' => 'Special Badge',
            'description' => 'Exclusive badge for your profile',
            'value_type' => 'badge_id',
            'expiry_days' => null
        ],
        'points' => [
            'name' => 'Bonus Points',
            'description' => 'Extra points for your account',
            'value_type' => 'integer',
            'expiry_days' => null
        ],
        'service_credit' => [
            'name' => 'Service Credit',
            'description' => 'Credit for premium services',
            'value_type' => 'currency',
            'expiry_days' => 90
        ]
    ];

    private const CACHE_DURATION = 1800; // 30 minutes

    public function trackUserActivity(int $userId, string $activity, array $metadata = []): array
    {
        try {
            $user = User::findOrFail($userId);
            
            // Check if activity qualifies for achievements
            $achievements = $this->checkAchievements($user, $activity, $metadata);
            
            // Award points for activity
            $pointsAwarded = $this->awardActivityPoints($user, $activity, $metadata);
            
            // Check for level up
            $levelUp = $this->checkLevelUp($user, $pointsAwarded);
            
            // Update user stats
            $this->updateUserStats($user, $activity, $metadata);
            
            // Check for challenges
            $challengeProgress = $this->updateChallengeProgress($user, $activity, $metadata);
            
            // Update leaderboard
            $this->updateLeaderboard($user, $pointsAwarded);
            
            return [
                'success' => true,
                'user_id' => $userId,
                'activity' => $activity,
                'points_awarded' => $pointsAwarded,
                'achievements_unlocked' => $achievements,
                'level_up' => $levelUp,
                'challenge_progress' => $challengeProgress,
                'tracked_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to track user activity', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'activity' => $activity
            ]);

            return [
                'success' => false,
                'message' => 'Failed to track activity',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getUserGamificationProfile(int $userId): array
    {
        try {
            $user = User::findOrFail($userId);
            
            $profile = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar_url,
                    'level' => $this->getUserLevel($user),
                    'total_points' => $this->getUserTotalPoints($user),
                    'current_streak' => $this->getUserStreak($user),
                    'rank' => $this->getUserRank($user)
                ],
                'achievements' => $this->getUserAchievements($user),
                'badges' => $this->getUserBadges($user),
                'rewards' => $this->getUserRewards($user),
                'challenges' => $this->getUserChallenges($user),
                'statistics' => $this->getUserGamificationStats($user),
                'progress' => $this->getUserProgress($user),
                'next_rewards' => $this->getNextAvailableRewards($user)
            ];

            return [
                'success' => true,
                'profile' => $profile,
                'generated_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get user gamification profile', [
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get profile',
                'error' => $e->getMessage()
            ];
        }
    }

    public function createChallenge(array $challengeData): array
    {
        try {
            // Validate challenge data
            $validatedData = $this->validateChallengeData($challengeData);
            
            // Create challenge
            $challenge = Challenge::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'type' => $validatedData['type'],
                'difficulty' => $validatedData['difficulty'],
                'requirements' => $validatedData['requirements'],
                'reward_points' => $validatedData['reward_points'],
                'reward_badge_id' => $validatedData['reward_badge_id'] ?? null,
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'max_participants' => $validatedData['max_participants'] ?? null,
                'is_active' => $validatedData['is_active'] ?? true,
                'created_by' => $validatedData['created_by'],
                'metadata' => $validatedData['metadata'] ?? [],
                'created_at' => now()
            ]);

            return [
                'success' => true,
                'challenge' => $challenge,
                'message' => 'Challenge created successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to create challenge', [
                'error' => $e->getMessage(),
                'challenge_data' => $challengeData
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create challenge',
                'error' => $e->getMessage()
            ];
        }
    }

    public function joinChallenge(int $userId, int $challengeId): array
    {
        try {
            $user = User::findOrFail($userId);
            $challenge = Challenge::findOrFail($challengeId);
            
            // Check if user can join
            if (!$this->canUserJoinChallenge($user, $challenge)) {
                return [
                    'success' => false,
                    'message' => 'Cannot join this challenge'
                ];
            }

            // Create user challenge record
            $userChallenge = UserChallenge::create([
                'user_id' => $userId,
                'challenge_id' => $challengeId,
                'status' => 'active',
                'progress' => 0,
                'joined_at' => now()
            ]);

            return [
                'success' => true,
                'user_challenge' => $userChallenge,
                'message' => 'Successfully joined challenge'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to join challenge', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'challenge_id' => $challengeId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to join challenge',
                'error' => $e->getMessage()
            ];
        }
    }

    public function awardReward(int $userId, int $rewardId, array $metadata = []): array
    {
        try {
            $user = User::findOrFail($userId);
            $reward = Reward::findOrFail($rewardId);
            
            // Check if user can receive reward
            if (!$this->canUserReceiveReward($user, $reward)) {
                return [
                    'success' => false,
                    'message' => 'Cannot receive this reward'
                ];
            }

            // Create user reward record
            $userReward = UserReward::create([
                'user_id' => $userId,
                'reward_id' => $rewardId,
                'status' => 'active',
                'value' => $reward->value,
                'expires_at' => $reward->expiry_days ? now()->addDays($reward->expiry_days) : null,
                'metadata' => $metadata,
                'awarded_at' => now()
            ]);

            // Apply reward effects
            $this->applyRewardEffects($user, $reward, $userReward);

            return [
                'success' => true,
                'user_reward' => $userReward,
                'reward' => $reward,
                'message' => 'Reward awarded successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to award reward', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'reward_id' => $rewardId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to award reward',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getLeaderboard(string $type = 'global', array $filters = []): array
    {
        try {
            $cacheKey = "leaderboard_{$type}_" . md5(json_encode($filters));
            $cachedLeaderboard = Cache::get($cacheKey);
            
            if ($cachedLeaderboard) {
                return [
                    'success' => true,
                    'leaderboard' => $cachedLeaderboard,
                    'cached' => true
                ];
            }

            $leaderboard = match($type) {
                'global' => $this->getGlobalLeaderboard($filters),
                'weekly' => $this->getWeeklyLeaderboard($filters),
                'monthly' => $this->getMonthlyLeaderboard($filters),
                'challenge' => $this->getChallengeLeaderboard($filters),
                'regional' => $this->getRegionalLeaderboard($filters),
                default => throw new \InvalidArgumentException("Unknown leaderboard type: {$type}")
            };

            // Cache the leaderboard
            Cache::put($cacheKey, $leaderboard, self::CACHE_DURATION);

            return [
                'success' => true,
                'leaderboard' => $leaderboard,
                'type' => $type,
                'generated_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get leaderboard', [
                'error' => $e->getMessage(),
                'type' => $type,
                'filters' => $filters
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get leaderboard',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getUserStats(int $userId): array
    {
        try {
            $user = User::findOrFail($userId);
            
            return [
                'total_points' => $user->gamification_points ?? 0,
                'level' => $user->level ?? 1,
                'achievements_count' => UserAchievement::where('user_id', $userId)->count(),
                'badges_count' => UserBadge::where('user_id', $userId)->count(),
                'challenges_count' => 0, // UserChallenge model doesn't exist
                'current_streak' => $this->getCurrentStreak($userId),
                'rank' => $this->getUserRankSimple($userId),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting user stats: ' . $e->getMessage());
            return [
                'total_points' => 0,
                'level' => 1,
                'achievements_count' => 0,
                'badges_count' => 0,
                'challenges_count' => 0, // UserChallenge model doesn't exist
                'current_streak' => 0,
                'rank' => 0,
            ];
        }
    }

    public function getUserAchievements(int $userId): array
    {
        try {
            return UserAchievement::with('achievement')
                ->where('user_id', $userId)
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting user achievements: ' . $e->getMessage());
            return [];
        }
    }

    public function getUserBadges(int $userId): array
    {
        try {
            return UserBadge::with('badge')
                ->where('user_id', $userId)
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting user badges: ' . $e->getMessage());
            return [];
        }
    }

    public function getUserChallenges(int $userId): array
    {
        try {
            // Return empty array for now since UserChallenge model doesn't exist
            return [];
        } catch (\Exception $e) {
            Log::error('Error getting user challenges: ' . $e->getMessage());
            return [];
        }
    }

    public function getAvailableAchievements(): array
    {
        try {
            return Achievement::all()->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting available achievements: ' . $e->getMessage());
            return [];
        }
    }

    public function getAvailableBadges(): array
    {
        try {
            return Badge::all()->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting available badges: ' . $e->getMessage());
            return [];
        }
    }

    public function getAvailableChallenges(): array
    {
        try {
            // Return empty array for now since Challenge model doesn't exist
            return [];
        } catch (\Exception $e) {
            Log::error('Error getting available challenges: ' . $e->getMessage());
            return [];
        }
    }

    public function getUserRankSimple(int $userId): int
    {
        try {
            $userPoints = User::findOrFail($userId)->gamification_points ?? 0;
            return User::where('gamification_points', '>', $userPoints)->count() + 1;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getCurrentStreak(int $userId): int
    {
        try {
            // Simple streak calculation based on last login
            $user = User::findOrFail($userId);
            if (!$user->last_login_at) {
                return 0;
            }
            
            $daysDiff = Carbon::now()->diffInDays($user->last_login_at);
            return $daysDiff <= 1 ? ($user->login_streak ?? 1) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    
    public function getUserRewards(int $userId): array
    {
        try {
            return UserReward::with('reward')
                ->where('user_id', $userId)
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting user rewards: ' . $e->getMessage());
            return [];
        }
    }

    public function getAvailableRewards(): array
    {
        try {
            return Reward::all()->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting available rewards: ' . $e->getMessage());
            return [];
        }
    }

    public function getGamificationStatistics(): array
    {
        try {
            $stats = [
                'overview' => [
                    'total_users' => User::count(),
                    'active_users' => User::where('last_login_at', '>=', now()->subDays(7))->count(),
                    'total_points_awarded' => UserLevel::sum('total_points'),
                    'average_points_per_user' => UserLevel::avg('total_points') ?? 0,
                    'total_achievements_unlocked' => UserAchievement::count(),
                    'total_badges_earned' => UserBadge::count(),
                    'total_rewards_claimed' => UserReward::count()
                ],
                'achievements' => $this->getAchievementStatistics(),
                'badges' => $this->getBadgeStatistics(),
                'rewards' => $this->getRewardStatistics(),
                'challenges' => $this->getChallengeStatistics(),
                'levels' => $this->getLevelStatistics(),
                'engagement' => $this->getEngagementStatistics(),
                'leaderboards' => $this->getLeaderboardStatistics()
            ];

            return [
                'success' => true,
                'statistics' => $stats,
                'generated_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get gamification statistics', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get statistics',
                'error' => $e->getMessage()
            ];
        }
    }

    // Private helper methods
    private function checkAchievements(User $user, string $activity, array $metadata): array
    {
        $unlockedAchievements = [];
        
        if (isset(self::ACHIEVEMENT_TYPES[$activity])) {
            foreach (self::ACHIEVEMENT_TYPES[$activity] as $achievementKey => $achievementData) {
                if ($this->shouldAwardAchievement($user, $achievementKey, $metadata)) {
                    $achievement = $this->awardAchievement($user, $achievementKey, $achievementData);
                    if ($achievement) {
                        $unlockedAchievements[] = $achievement;
                    }
                }
            }
        }
        
        return $unlockedAchievements;
    }

    private function shouldAwardAchievement(User $user, string $achievementKey, array $metadata): bool
    {
        // Check if user already has this achievement
        $existingAchievement = UserAchievement::where('user_id', $user->id)
            ->where('achievement_key', $achievementKey)
            ->first();
        
        if ($existingAchievement) {
            return false;
        }

        // Check achievement criteria
        return match($achievementKey) {
            'first_property' => $this->getUserPropertyCount($user) >= 1,
            '10_properties' => $this->getUserPropertyCount($user) >= 10,
            '50_properties' => $this->getUserPropertyCount($user) >= 50,
            '100_properties' => $this->getUserPropertyCount($user) >= 100,
            'first_sale' => $this->getUserSaleCount($user) >= 1,
            '10_sales' => $this->getUserSaleCount($user) >= 10,
            '50_sales' => $this->getUserSaleCount($user) >= 50,
            'million_dollar_sales' => $this->getUserTotalSalesValue($user) >= 1000000,
            'daily_login' => $this->hasUserLoggedInToday($user),
            '7_day_streak' => $this->getUserLoginStreak($user) >= 7,
            '30_day_streak' => $this->getUserLoginStreak($user) >= 30,
            'profile_complete' => $this->isUserProfileComplete($user),
            'first_review' => $this->getUserReviewCount($user) >= 1,
            '10_reviews' => $this->getUserReviewCount($user) >= 10,
            'helpful_votes' => $this->getUserHelpfulVotes($user) >= 50,
            'community_leader' => $this->getUserCommunityScore($user) >= 1000,
            'first_investment' => $this->getUserInvestmentCount($user) >= 1,
            'diversified_portfolio' => $this->getUserPortfolioDiversity($user) >= 5,
            'profitable_investor' => $this->getUserInvestmentProfit($user) > 0,
            'investment_guru' => $this->getUserInvestmentROI($user) >= 50,
            default => false
        };
    }

    private function awardAchievement(User $user, string $achievementKey, array $achievementData): ?array
    {
        try {
            $userAchievement = UserAchievement::create([
                'user_id' => $user->id,
                'achievement_key' => $achievementKey,
                'points_awarded' => $achievementData['points'],
                'badge_id' => $achievementData['badge_id'],
                'awarded_at' => now()
            ]);

            // Award badge if applicable
            if ($achievementData['badge_id']) {
                $this->awardBadge($user, $achievementData['badge_id']);
            }

            // Add points to user total
            $this->addUserPoints($user, $achievementData['points']);

            return [
                'achievement_key' => $achievementKey,
                'points' => $achievementData['points'],
                'badge_id' => $achievementData['badge_id'],
                'awarded_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to award achievement', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'achievement_key' => $achievementKey
            ]);
            return null;
        }
    }

    private function awardBadge(User $user, int $badgeId): void
    {
        UserBadge::firstOrCreate([
            'user_id' => $user->id,
            'badge_id' => $badgeId
        ], [
            'awarded_at' => now()
        ]);
    }

    private function awardActivityPoints(User $user, string $activity, array $metadata): int
    {
        $points = match($activity) {
            'property_listing' => 10,
            'property_view' => 1,
            'property_inquiry' => 5,
            'property_sale' => 50,
            'review_written' => 15,
            'review_helpful' => 2,
            'investment_made' => 25,
            'daily_login' => 5,
            'profile_update' => 3,
            'referral_completed' => 100,
            'challenge_completed' => 75,
            default => 0
        };

        if ($points > 0) {
            $this->addUserPoints($user, $points);
        }

        return $points;
    }

    private function addUserPoints(User $user, int $points): void
    {
        $userLevel = UserLevel::firstOrCreate([
            'user_id' => $user->id
        ], [
            'level' => 1,
            'total_points' => 0,
            'current_points' => 0
        ]);

        $userLevel->update([
            'total_points' => $userLevel->total_points + $points,
            'current_points' => $userLevel->current_points + $points,
            'updated_at' => now()
        ]);
    }

    private function checkLevelUp(User $user, int $pointsAwarded): ?array
    {
        $userLevel = UserLevel::where('user_id', $user->id)->first();
        
        if (!$userLevel) {
            return null;
        }

        $currentLevel = $userLevel->level;
        $totalPoints = $userLevel->total_points;
        
        $newLevel = $this->calculateLevel($totalPoints);
        
        if ($newLevel > $currentLevel) {
            $userLevel->update([
                'level' => $newLevel,
                'leveled_up_at' => now()
            ]);

            return [
                'old_level' => $currentLevel,
                'new_level' => $newLevel,
                'level_name' => self::LEVEL_THRESHOLDS[$newLevel]['name'],
                'leveled_up_at' => now()->toISOString()
            ];
        }

        return null;
    }

    private function calculateLevel(int $totalPoints): int
    {
        foreach (self::LEVEL_THRESHOLDS as $level => $threshold) {
            if ($totalPoints >= $threshold['min_points'] && $totalPoints <= $threshold['max_points']) {
                return $level;
            }
        }
        
        return 1;
    }

    private function updateUserStats(User $user, string $activity, array $metadata): void
    {
        // Update user statistics based on activity
        $stats = $user->gamification_stats ?? [];
        
        switch ($activity) {
            case 'property_listing':
                $stats['properties_listed'] = ($stats['properties_listed'] ?? 0) + 1;
                break;
            case 'property_sale':
                $stats['properties_sold'] = ($stats['properties_sold'] ?? 0) + 1;
                $stats['total_sales_value'] = ($stats['total_sales_value'] ?? 0) + ($metadata['sale_price'] ?? 0);
                break;
            case 'review_written':
                $stats['reviews_written'] = ($stats['reviews_written'] ?? 0) + 1;
                break;
            case 'investment_made':
                $stats['investments_made'] = ($stats['investments_made'] ?? 0) + 1;
                $stats['total_invested'] = ($stats['total_invested'] ?? 0) + ($metadata['investment_amount'] ?? 0);
                break;
        }
        
        $user->update(['gamification_stats' => $stats]);
    }

    private function updateChallengeProgress(User $user, string $activity, array $metadata): array
    {
        $activeChallenges = UserChallenge::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('challenge')
            ->get();

        $progressUpdates = [];

        foreach ($activeChallenges as $userChallenge) {
            $challenge = $userChallenge->challenge;
            
            if ($this->activityContributesToChallenge($activity, $challenge, $metadata)) {
                $newProgress = $this->calculateChallengeProgress($userChallenge, $activity, $metadata);
                
                $userChallenge->update([
                    'progress' => $newProgress,
                    'updated_at' => now()
                ]);

                if ($newProgress >= 100) {
                    $this->completeChallenge($userChallenge);
                }

                $progressUpdates[] = [
                    'challenge_id' => $challenge->id,
                    'challenge_name' => $challenge->name,
                    'old_progress' => $userChallenge->progress,
                    'new_progress' => $newProgress,
                    'completed' => $newProgress >= 100
                ];
            }
        }

        return $progressUpdates;
    }

    private function updateLeaderboard(User $user, int $pointsAwarded): void
    {
        // Update global leaderboard
        Leaderboard::updateOrCreate(
            ['user_id' => $user->id, 'type' => 'global'],
            ['score' => DB::raw("score + {$pointsAwarded}"), 'updated_at' => now()]
        );

        // Update weekly leaderboard
        Leaderboard::updateOrCreate(
            ['user_id' => $user->id, 'type' => 'weekly'],
            ['score' => DB::raw("score + {$pointsAwarded}"), 'updated_at' => now()]
        );
    }

    // Data retrieval methods
    public function getLeaderboardStatistics(string $type = 'points', string $period = 'all_time'): array
    {
        // Calculate statistics based on actual data
        $query = Leaderboard::where('type', 'global');
        
        $totalParticipants = $query->count();
        $averageScore = $query->avg('score') ?? 0;
        $highestScore = $query->max('score') ?? 0;
        $lowestScore = $query->min('score') ?? 0;
        
        return [
            'total_participants' => $totalParticipants,
            'average_score' => round($averageScore, 2),
            'highest_score' => $highestScore,
            'lowest_score' => $lowestScore,
            'period' => $period,
            'type' => $type
        ];
    }

    public function getTopPerformers(string $type = 'points', string $period = 'all_time'): array
    {
        // Get top 5 performers
        return Leaderboard::where('type', 'global')
            ->orderBy('score', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $user = User::find($item->user_id);
                return [
                    'user_id' => $item->user_id,
                    'user_name' => $user ? $user->name : 'Unknown',
                    'total_points' => $item->score,
                    'challenges_completed' => 0, // Placeholder
                    'badges_earned' => UserBadge::where('user_id', $item->user_id)->count()
                ];
            })
            ->toArray();
    }

    public function getCategoryDistribution(string $type = 'points', string $period = 'all_time'): array
    {
        // Distribution by user level
        $levels = UserLevel::select('level', DB::raw('count(*) as count'))
            ->groupBy('level')
            ->get();
            
        $total = $levels->sum('count');
        
        return $levels->map(function ($item) use ($total) {
            $levelInfo = self::LEVEL_THRESHOLDS[$item->level] ?? ['name' => 'Unknown'];
            return [
                'category' => $levelInfo['name'],
                'count' => $item->count,
                'percentage' => $total > 0 ? ($item->count / $total) * 100 : 0
            ];
        })->toArray();
    }

    public function getUserLeaderboardPosition(int $userId): array
    {
        $user = User::find($userId);
        if (!$user) return [];

        $rankInfo = $this->getUserRank($user);
        $userLevel = $this->getUserLevel($user);
        
        return [
            'rank' => $rankInfo['global_rank'] ?? '-',
            'score' => $this->getUserTotalPoints($user),
            'category_label' => $userLevel['name'] ?? 'General',
            'percentile' => $rankInfo['percentile'] ?? 0
        ];
    }

    private function getUserLevel(User $user): array
    {
        $userLevel = UserLevel::where('user_id', $user->id)->first();
        
        if (!$userLevel) {
            return [
                'level' => 1,
                'name' => 'Beginner',
                'total_points' => 0,
                'current_points' => 0,
                'progress_percentage' => 0,
                'points_to_next_level' => 500
            ];
        }

        $levelInfo = self::LEVEL_THRESHOLDS[$userLevel->level];
        $nextLevelInfo = self::LEVEL_THRESHOLDS[$userLevel->level + 1] ?? null;
        
        $progressPercentage = $nextLevelInfo 
            ? (($userLevel->total_points - $levelInfo['min_points']) / 
               ($nextLevelInfo['min_points'] - $levelInfo['min_points'])) * 100
            : 100;

        return [
            'level' => $userLevel->level,
            'name' => $levelInfo['name'],
            'total_points' => $userLevel->total_points,
            'current_points' => $userLevel->current_points,
            'progress_percentage' => round($progressPercentage, 2),
            'points_to_next_level' => $nextLevelInfo ? $nextLevelInfo['min_points'] - $userLevel->total_points : 0,
            'color' => $levelInfo['color']
        ];
    }

    private function getUserTotalPoints(User $user): int
    {
        $userLevel = UserLevel::where('user_id', $user->id)->first();
        return $userLevel ? $userLevel->total_points : 0;
    }

    private function getUserStreak(User $user): array
    {
        // Calculate login streak
        $lastLogin = $user->last_login_at;
        
        if (!$lastLogin) {
            return ['current_streak' => 0, 'longest_streak' => 0];
        }

        // Simplified streak calculation
        return [
            'current_streak' => 1,
            'longest_streak' => 5
        ];
    }

    public function getUserRank(User|int $user): array
    {
        if (is_int($user)) {
            $user = User::find($user);
            if (!$user) {
                return [
                    'global_rank' => null,
                    'total_users' => 0,
                    'percentile' => 0
                ];
            }
        }

        $globalLeaderboard = Leaderboard::where('type', 'global')
            ->orderBy('score', 'desc')
            ->get();

        $userRank = $globalLeaderboard->search(function($item) use ($user) {
            return $item->user_id === $user->id;
        });

        return [
            'global_rank' => $userRank !== false ? $userRank + 1 : null,
            'total_users' => $globalLeaderboard->count(),
            'percentile' => $userRank !== false ? (($globalLeaderboard->count() - $userRank) / $globalLeaderboard->count()) * 100 : 0
        ];
    }

    // Additional helper methods would be implemented here...
    private function getUserPropertyCount(User $user): int { return 0; }
    private function getUserSaleCount(User $user): int { return 0; }
    private function getUserTotalSalesValue(User $user): float { return 0; }
    private function hasUserLoggedInToday(User $user): bool { return true; }
    private function getUserLoginStreak(User $user): int { return 1; }
    private function isUserProfileComplete(User $user): bool { return true; }
    private function getUserReviewCount(User $user): int { return 0; }
    private function getUserHelpfulVotes(User $user): int { return 0; }
    private function getUserCommunityScore(User $user): int { return 0; }
    private function getUserInvestmentCount(User $user): int { return 0; }
    private function getUserPortfolioDiversity(User $user): int { return 0; }
    private function getUserInvestmentProfit(User $user): float { return 0; }
    private function getUserInvestmentROI(User $user): float { return 0; }

        private function getUserGamificationStats(User $user): array { return []; }
    private function getUserProgress(User $user): array { return []; }
    private function getNextAvailableRewards(User $user): array { return []; }

    private function validateChallengeData(array $data): array { return $data; }
    private function canUserJoinChallenge(User $user, Challenge $challenge): bool { return true; }
    private function canUserReceiveReward(User $user, Reward $reward): bool { return true; }
    private function applyRewardEffects(User $user, Reward $reward, UserReward $userReward): void {}
    private function activityContributesToChallenge(string $activity, Challenge $challenge, array $metadata): bool { return false; }
    private function calculateChallengeProgress(UserChallenge $userChallenge, string $activity, array $metadata): int { return 0; }
    private function completeChallenge(UserChallenge $userChallenge): void {}

    private function getGlobalLeaderboard(array $filters): array 
    {
        return Leaderboard::where('type', 'global')
            ->orderBy('score', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($item) {
                $user = \App\Models\User::find($item->user_id);
                return [
                    'rank' => $item->rank,
                    'user_id' => $item->user_id,
                    'user_name' => $user ? $user->name : 'Unknown',
                    'user_email' => $user ? $user->email : 'unknown@example.com',
                    'score' => $item->score,
                    'change' => $item->change ?? 0,
                    'previous_rank' => $item->previous_rank,
                    'calculated_at' => $item->calculated_at,
                    'avatar' => $user ? $user->avatar_url : null,
                ];
            })
            ->toArray();
    }
    private function getWeeklyLeaderboard(array $filters): array 
    {
        // For now, return global leaderboard as fallback
        return $this->getGlobalLeaderboard($filters);
    }

    private function getMonthlyLeaderboard(array $filters): array 
    {
        // For now, return global leaderboard as fallback
        return $this->getGlobalLeaderboard($filters);
    }

    private function getChallengeLeaderboard(array $filters): array 
    {
        return [];
    }

    private function getRegionalLeaderboard(array $filters): array 
    {
        return [];
    }

    private function getAchievementStatistics(): array { return []; }
    private function getBadgeStatistics(): array { return []; }
    private function getRewardStatistics(): array { return []; }
    private function getChallengeStatistics(): array { return []; }
    private function getLevelStatistics(): array { return []; }
    private function getEngagementStatistics(): array { return []; }
}
