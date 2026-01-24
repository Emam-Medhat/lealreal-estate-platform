<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserPropertyGamification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'total_points',
        'current_level',
        'experience_points',
        'badges_earned',
        'challenges_completed',
        'quests_completed',
        'current_streak',
        'longest_streak',
        'last_activity_at',
        'joined_at',
        'metadata',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'total_points' => 'integer',
        'current_level' => 'integer',
        'experience_points' => 'integer',
        'badges_earned' => 'integer',
        'challenges_completed' => 'integer',
        'quests_completed' => 'integer',
        'current_streak' => 'integer',
        'longest_streak' => 'integer',
        'metadata' => 'array',
        'last_activity_at' => 'datetime',
        'joined_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'last_activity_at' => 'datetime',
        'joined_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function points(): HasMany
    {
        return $this->hasMany(PropertyPoints::class, 'user_id');
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(PropertyBadge::class, 'badge_user')
            ->withPivot('earned_at', 'reason')
            ->withTimestamps();
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(PropertyAchievement::class, 'achievement_user')
            ->withPivot('unlocked_at', 'progress', 'progress_data', 'reason')
            ->withTimestamps();
    }

    public function challenges(): BelongsToMany
    {
        return $this->belongsToMany(PropertyChallenge::class, 'challenge_user')
            ->withPivot('joined_at', 'status', 'score', 'progress', 'completed_at', 'final_score', 'notes')
            ->withTimestamps();
    }

    public function quests(): BelongsToMany
    {
        return $this->belongsToMany(PropertyQuest::class, 'quest_user')
            ->withPivot('accepted_at', 'status', 'progress', 'objectives_completed', 'completed_at', 'final_score', 'notes')
            ->withTimestamps();
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(\App\Models\UserReward::class, 'user_id');
    }

    public function leaderboardEntries(): HasMany
    {
        return $this->hasMany(PropertyLeaderboard::class, 'user_id');
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('current_level', $level);
    }

    public function scopeByMinLevel($query, $level)
    {
        return $query->where('current_level', '>=', $level);
    }

    public function scopeByMaxLevel($query, $level)
    {
        return $query->where('current_level', '<=', $level);
    }

    public function scopeByPointsRange($query, $min, $max)
    {
        return $query->whereBetween('total_points', [$min, $max]);
    }

    public function scopeActive($query, $days = 7)
    {
        return $query->where('last_activity_at', '>=', now()->subDays($days));
    }

    public function scopeInactive($query, $days = 30)
    {
        return $query->where('last_activity_at', '<', now()->subDays($days));
    }

    public function scopeNewUsers($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeHighScorers($query, $minPoints = 1000)
    {
        return $query->where('total_points', '>=', $minPoints);
    }

    public function scopeTopLevel($query, $minLevel = 10)
    {
        return $query->where('current_level', '>=', $minLevel);
    }

    public function scopeWithStreak($query, $minStreak = 7)
    {
        return $query->where('current_streak', '>=', $minStreak);
    }

    // Methods
    public function addPoints($points, $type = 'earned', $reason = null): void
    {
        $this->total_points += $points;
        $this->experience_points += $points;
        $this->save();

        // Create points record
        PropertyPoints::create([
            'user_id' => $this->user_id,
            'points' => $points,
            'type' => $type,
            'reason' => $reason,
            'awarded_at' => now(),
        ]);

        $this->updateActivityStreak();
    }

    public function removePoints($points, $reason = null): void
    {
        $this->total_points = max(0, $this->total_points - $points);
        $this->save();

        // Create points record
        PropertyPoints::create([
            'user_id' => $this->user_id,
            'points' => $points,
            'type' => 'penalty',
            'reason' => $reason,
            'awarded_at' => now(),
        ]);
    }

    public function setPoints($points, $reason = null): void
    {
        $oldPoints = $this->total_points;
        $this->total_points = max(0, $points);
        $this->save();

        // Create points record for the difference
        $difference = $points - $oldPoints;
        if ($difference !== 0) {
            PropertyPoints::create([
                'user_id' => $this->user_id,
                'points' => $difference,
                'type' => $difference > 0 ? 'earned' : 'penalty',
                'reason' => $reason ?? 'Points adjustment',
                'awarded_at' => now(),
            ]);
        }

        $this->updateActivityStreak();
    }

    public function addExperience($experience): void
    {
        $this->experience_points += $experience;
        $this->save();
    }

    public function levelUp(): bool
    {
        $nextLevel = PropertyLevel::where('level', $this->current_level + 1)->first();
        
        if (!$nextLevel) {
            return false;
        }

        if ($this->experience_points < $nextLevel->experience_required) {
            return false;
        }

        $this->current_level = $nextLevel->level;
        $this->experience_points = $this->experience_points - $nextLevel->experience_required;
        
        // Award level up bonus
        if ($nextLevel->bonus_points > 0) {
            $this->total_points += $nextLevel->bonus_points;
            PropertyPoints::create([
                'user_id' => $this->user_id,
                'points' => $nextLevel->bonus_points,
                'type' => 'level_up_bonus',
                'reason' => "مكافأة ترقية المستوى {$nextLevel->level}",
                'awarded_at' => now(),
            ]);
        }

        // Award badge if applicable
        if ($nextLevel->badge_unlock) {
            $badge = PropertyBadge::find($nextLevel->badge_unlock);
            if ($badge) {
                $badge->users()->attach($this->user_id, [
                    'earned_at' => now(),
                    'reason' => "ترقية المستوى {$nextLevel->level}",
                ]);
                $this->badges_earned++;
            }
        }

        $this->save();
        $this->updateActivityStreak();

        return true;
    }

    public function checkAndLevelUp(): bool
    {
        while (true) {
            $nextLevel = PropertyLevel::where('level', $this->current_level + 1)->first();
            
            if (!$nextLevel || $this->experience_points < $nextLevel->experience_required) {
                break;
            }

            $this->levelUp();
        }

        return true;
    }

    public function incrementBadgeCount(): void
    {
        $this->badges_earned++;
        $this->save();
    }

    public function incrementChallengeCount(): void
    {
        $this->challenges_completed++;
        $this->save();
    }

    public function incrementQuestCount(): void
    {
        $this->quests_completed++;
        $this->save();
    }

    public function updateActivityStreak(): void
    {
        $this->last_activity_at = now();
        
        // Check if activity was yesterday
        if ($this->joined_at && $this->joined_at->isYesterday()) {
            $this->current_streak++;
            
            if ($this->current_streak > $this->longest_streak) {
                $this->longest_streak = $this->current_streak;
            }
        } else {
            // Streak broken, reset
            if ($this->current_streak > $this->longest_streak) {
                $this->longest_streak = $this->current_streak;
            }
            
            $this->current_streak = 1;
        }
        
        $this->save();
    }

    public function getFormattedTotalPoints(): string
    {
        return number_format($this->total_points);
    }

    public function getFormattedExperiencePoints(): string
    {
        return number_format($this->experience_points);
    }

    public function getFormattedCurrentLevel(): string
    {
        return 'المستوى ' . $this->current_level;
    }

    public function getFormattedCurrentStreak(): string
    {
        return $this->current_streak . ' أيام';
    }

    public function getFormattedLongestStreak(): string
    {
        return $this->longest_streak . ' أيام';
    }

    public function getLevelProgress(): array
    {
        $currentLevel = PropertyLevel::where('level', $this->current_level)->first();
        $nextLevel = PropertyLevel::where('level', $this->current_level + 1)->first();
        
        if (!$currentLevel) {
            return [
                'current_level' => 1,
                'experience_required' => 0,
                'user_experience' => $this->experience_points,
                'progress' => 100,
                'experience_to_next' => 0,
                'is_max_level' => true,
            ];
        }

        $experienceFromPrevious = $currentLevel->getExperienceFromPreviousLevel();
        $experienceInLevel = $this->experience_points - ($currentLevel->experience_required - $experienceFromPrevious);
        $progress = min(100, ($experienceInLevel / $experienceFromPrevious) * 100);

        return [
            'current_level' => $this->current_level,
            'experience_required' => $currentLevel->experience_required,
            'user_experience' => $this->experience_points,
            'experience_in_level' => $experienceInLevel,
            'experience_from_previous' => $experienceFromPrevious,
            'progress' => $progress,
            'experience_to_next' => $nextLevel ? ($nextLevel->experience_required - $this->experience_points) : 0,
            'is_max_level' => $nextLevel === null,
        ];
    }

    public function getRankingPosition($type = 'points', $period = 'all_time'): ?int
    {
        return PropertyLeaderboard::where('user_id', $this->user_id)
            ->where('type', $type)
            ->where('period', $period)
            ->orderBy('rank')
            ->first()
            ?->rank;
    }

    public function getGlobalRanking(): array
    {
        return [
            'points_rank' => $this->getRankingPosition('points'),
            'level_rank' => $this->getRankingPosition('level'),
            'badges_rank' => $this->getRankingPosition('badges'),
            'challenges_rank' => $this->getRankingPosition('challenges'),
            'quests_rank' => $this->getRankingPosition('quests'),
        ];
    }

    public function getRecentActivity($limit = 10): array
    {
        return PropertyPoints::where('user_id', $this->user_id)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->with('property')
            ->get()
            ->map(function ($point) {
                return [
                    'id' => $point->id,
                    'points' => $point->points,
                    'type' => $point->type,
                    'reason' => $point->reason,
                    'created_at' => $point->created_at->format('Y-m-d H:i:s'),
                    'property_title' => $point->property ? $point->property->title : null,
                ];
            })
            ->toArray();
    }

    public function getActivityStats($period = 'month'): array
    {
        $dateRange = $this->getDateRange($period);
        
        return [
            'points_earned' => PropertyPoints::where('user_id', $this->user_id)
                ->where('type', 'earned')
                ->whereBetween('created_at', $dateRange)
                ->sum('points'),
            'points_lost' => PropertyPoints::where('user_id', $this->user_id)
                ->where('type', 'penalty')
                ->whereBetween('created_at', $dateRange)
                ->sum('points'),
            'net_points' => PropertyPoints::where('user_id', $this->user_id)
                ->whereBetween('created_at', $dateRange)
                ->sum('points'),
            'total_transactions' => PropertyPoints::where('user_id', $this->user_id)
                ->whereBetween('created_at', $dateRange)
                ->count(),
            'badges_earned' => $this->badges()->whereBetween('badge_user.earned_at', $dateRange)->count(),
            'achievements_unlocked' => $this->achievements()->whereBetween('achievement_user.unlocked_at', $dateRange)->count(),
            'challenges_completed' => $this->challenges()->wherePivot('completed', true)->count(),
            'quests_completed' => $this->quests()->wherePivot('completed', true)->count(),
            'active_days' => $this->getActiveDaysCount($period),
        ];
    }

    private function getActiveDaysCount($period): int
    {
        $dateRange = $this->getDateRange($period);
        
        return PropertyPoints::where('user_id', $this->user_id)
            ->where('type', 'earned')
            ->whereBetween('created_at', $dateRange)
            ->distinct('date')
            ->count('date');
    }

    public function getGamificationSummary(): array
    {
        return [
            'user_id' => $this->user_id,
            'total_points' => $this->total_points,
            'current_level' => $this->current_level,
            'experience_points' => $this->experience_points,
            'badges_earned' => $this->badges_earned,
            'challenges_completed' => $this->challenges_completed,
            'quests_completed' => $this->quests_completed,
            'current_streak' => $this->current_streak,
            'longest_streak' => $this->longest_streak,
            'last_activity_at' => $this->last_activity_at,
            'joined_at' => $this->joined_at,
            'level_progress' => $this->getLevelProgress(),
            'global_rankings' => $this->getGlobalRanking(),
            'available_badges' => $this->getAvailableBadges(),
            'available_achievements' => $this->getAvailableAchievements(),
            'available_quests' => $this->getAvailableQuests(),
        ];
    }

    private function getAvailableBadges(): array
    {
        return PropertyBadge::where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->whereDoesntHave('users', function ($query) {
                $query->where('user_id', $this->user_id);
            })
            ->get()
            ->map(function ($badge) {
                return [
                    'id' => $badge->id,
                    'name' => $badge->name,
                    'points_required' => $badge->points_required,
                    'level_required' => $badge->level_required,
                    'can_earn' => $badge->canBeEarnedBy($this->user),
                    'progress' => $badge->getProgressForUser($this->user_id)['progress'],
                ];
            })
            ->toArray();
    }

    private function getAvailableAchievements(): array
    {
        return PropertyAchievement::where('hidden', false)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->whereDoesntHave('users', function ($query) {
                $query->where('user_id', $this->user_id);
            })
            ->get()
            ->map(function ($achievement) {
                return [
                    'id' => $achievement->id,
                    'name' => $achievement->name,
                    'points_reward' => $achievement->points_reward,
                    'difficulty' => $achievement->difficulty,
                    'can_unlock' => $achievement->canBeUnlockedBy($this->user),
                    'progress' => $achievement->calculateProgress($this->user_id)['progress'],
                ];
            })
            ->toArray();
    }

    private function getAvailableQuests(): array
    {
        return PropertyQuest::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereDoesntHave('participants', function ($query) {
                $query->where('user_id', $this->user_id);
            })
            ->get()
            ->map(function ($quest) {
                return [
                    'id' => $quest->id,
                    'title' => $quest->title,
                    'type' => $quest->type,
                    'difficulty' => $quest->difficulty,
                    'points_reward' => $quest->points_reward,
                    'can_join' => $quest->canJoin($this->user),
                    'days_remaining' => $quest->getDaysRemaining(),
                ];
            })
            ->toArray();
    }

    public function calculateGamificationScore(): int
    {
        $score = 0;
        
        // Points contribution (40%)
        $score += ($this->total_points / 100) * 40;
        
        // Level contribution (30%)
        $maxLevel = PropertyLevel::max('level') ?? 1;
        $score += ($this->current_level / $maxLevel) * 30;
        
        // Badges contribution (15%)
        $maxBadges = PropertyBadge::count();
        $score += ($this->badges_earned / $maxBadges) * 15;
        
        // Achievements contribution (10%)
        $maxAchievements = PropertyAchievement::count();
        $score += ($this->achievements()->count() / $maxAchievements) * 10;
        
        // Challenges contribution (10%)
        $maxChallenges = PropertyChallenge::count();
        $score += ($this->challenges_completed / $maxChallenges) * 10;
        
        // Quests contribution (5%)
        $maxQuests = PropertyQuest::count();
        $score += ($this->quests_completed / $maxQuests) * 5;
        
        return min(100, $score);
    }

    public function getEngagementLevel(): string
    {
        $score = $this->calculateGamificationScore();
        
        if ($score >= 90) {
            return 'نشط جداً';
        } elseif ($score >= 75) {
            return 'نشط';
        } elseif ($score >= 50) {
            return 'متوسط';
        } elseif ($score >= 25) {
            return 'منخفضل';
        } else {
            return 'غير نشط';
        }
    }

    public function isPowerUser(): bool
    {
        return $this->calculateGamificationScore() >= 75;
    }

    public function isNewUser($days = 30): bool
    {
        return $this->created_at->greaterThan(now()->subDays($days));
    }

    public function isReturningUser($days = 30): bool
    {
        return $this->last_activity_at && 
               $this->last_activity_at->lessThan(now()->subDays($days));
    }

    public static function getGamificationOverview(): array
    {
        $totalUsers = self::count();
        $activeUsers = self::active()->count();
        $totalPoints = self::sum('total_points');
        $averagePoints = self::avg('total_points');
        $averageLevel = self::avg('current_level');
        $totalBadges = self::withCount('badges')->get()->sum('badges_count');
        $totalAchievements = self::withCount('achievements')->get()->sum('achievements_count');
        $totalChallenges = self::withCount('challenges')->get()->sum('challenges_completed');
        $totalQuests = self::withCount('quests')->get()->sum('quests_completed');
        
        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'total_points' => $totalPoints,
            'average_points' => $averagePoints,
            'total_badges' => $totalBadges,
            'total_achievements' => $totalAchievements,
            'total_challenges' => $totalChallenges,
            'total_quests' => $totalQuests,
            'average_level' => $averageLevel,
            'engagement_distribution' => self::getEngagementDistribution(),
        ];
    }

    private static function getEngagementDistribution(): array
    {
        $users = self::all();
        $distribution = [
            'beginner' => 0,
            'intermediate' => 0,
            'advanced' => 0,
            'expert' => 0,
        ];
        
        foreach ($users as $user) {
            $level = $user->current_level;
            
            if ($level <= 5) {
                $distribution['beginner']++;
            } elseif ($level <= 15) {
                $distribution['intermediate']++;
            } elseif ($level <= 30) {
                $distribution['advanced']++;
            } else {
                $distribution['expert']++;
            }
        }
        
        return $distribution;
    }

    private static function getDateRange($period): array
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
}
