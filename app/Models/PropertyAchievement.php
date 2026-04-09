<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PropertyAchievement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'category',
        'difficulty',
        'points_reward',
        'badge_reward',
        'requirements',
        'conditions',
        'icon',
        'color',
        'hidden',
        'repeatable',
        'cooldown_period',
        'expires_at',
        'metadata',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'points_reward' => 'integer',
        'requirements' => 'array',
        'conditions' => 'array',
        'hidden' => 'boolean',
        'repeatable' => 'boolean',
        'cooldown_period' => 'integer',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'achievement_user')
            ->withPivot('unlocked_at', 'progress', 'progress_data', 'reason')
            ->withTimestamps();
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeVisible($query)
    {
        return $query->where('hidden', false);
    }

    public function scopeHidden($query)
    {
        return $query->where('hidden', true);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('expires_at', '<=', now()->addDays($days))
            ->where('expires_at', '>', now());
    }

    public function scopeEasy($query)
    {
        return $query->where('difficulty', 'easy');
    }

    public function scopeMedium($query)
    {
        return $query->where('difficulty', 'medium');
    }

    public function scopeHard($query)
    {
        return $query->where('difficulty', 'hard');
    }

    public function scopeExpert($query)
    {
        return $query->where('difficulty', 'expert');
    }

    public function scopePoints($query)
    {
        return $query->where('type', 'points');
    }

    public function scopeLevel($query)
    {
        return $query->where('type', 'level');
    }

    public function scopeBadges($query)
    {
        return $query->where('type', 'badges');
    }

    public function scopeChallenges($query)
    {
        return $query->where('type', 'challenges');
    }

    public function scopeQuests($query)
    {
        return $query->where('type', 'quests');
    }

    public function scopeSocial($query)
    {
        return $query->where('type', 'social');
    }

    public function scopeCustom($query)
    {
        return $query->where('type', 'custom');
    }

    public function scopeRepeatable($query)
    {
        return $query->where('repeatable', true);
    }

    public function scopeNotRepeatable($query)
    {
        return $query->where('repeatable', false);
    }

    // Methods
    public function isActive(): bool
    {
        return !$this->expires_at || $this->expires_at->greaterThan(now());
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isExpiringSoon($days = 7): bool
    {
        return $this->expires_at && 
               $this->expires_at->greaterThan(now()) && 
               $this->expires_at->lessThanOrEqualTo(now()->addDays($days));
    }

    public function getDaysUntilExpiry(): int
    {
        if (!$this->expires_at) {
            return -1; // No expiry
        }
        
        return now()->diffInDays($this->expires_at, false);
    }

    public function getTypeLabel(): string
    {
        $labels = [
            'points' => 'نقاط',
            'level' => 'مستوى',
            'badges' => 'شارات',
            'challenges' => 'تحديات',
            'quests' => 'مهام',
            'social' => 'اجتماعي',
            'custom' => 'مخصص',
        ];
        
        return $labels[$this->type] ?? $this->type;
    }

    public function getDifficultyLabel(): string
    {
        $labels = [
            'easy' => 'سهل',
            'medium' => 'متوسط',
            'hard' => 'صعب',
            'expert' => 'خبير',
        ];
        
        return $labels[$this->difficulty] ?? $this->difficulty;
    }

    public function getDifficultyColor(): string
    {
        $colors = [
            'easy' => '#28a745', // Green
            'medium' => '#ffc107', // Yellow
            'hard' => '#fd7e14', // Orange
            'expert' => '#dc3545', // Red
        ];
        
        return $colors[$this->difficulty] ?? '#6c757d';
    }

    public function getFormattedPointsReward(): string
    {
        return number_format($this->points_reward) . ' نقطة';
    }

    public function canBeUnlockedBy($user): bool
    {
        if (!$user) {
            return false;
        }

        if (!$this->isActive()) {
            return false;
        }

        if ($this->isHidden() && !$this->canUserSeeHidden($user)) {
            return false;
        }

        // Check if already unlocked
        if ($this->users()->where('user_id', $user->id)->exists()) {
            return false;
        }

        // Check cooldown period
        if ($this->repeatable && $this->cooldown_period) {
            $lastUnlock = $this->users()
                ->where('user_id', $user->id)
                ->orderBy('pivot_unlocked_at', 'desc')
                ->first();
            
            if ($lastUnlock && $lastUnlock->pivot->unlocked_at->diffInDays(now()) < $this->cooldown_period) {
                return false;
            }
        }

        return $this->checkRequirements($user);
    }

    private function canUserSeeHidden($user): bool
    {
        // Implement logic to check if user can see hidden achievements
        // This could be based on user level, role, or permissions
        return true; // Simplified for now
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function checkRequirements($user): bool
    {
        if (empty($this->requirements)) {
            return true;
        }

        $gamification = UserPropertyGamification::where('user_id', $user->id)->first();
        if (!$gamification) {
            return false;
        }

        foreach ($this->requirements as $requirement) {
            if (!$this->checkSingleRequirement($requirement, $user, $gamification)) {
                return false;
            }
        }

        return true;
    }

    private function checkSingleRequirement($requirement, $user, $gamification): bool
    {
        switch ($requirement['type']) {
            case 'total_points':
                return $gamification->total_points >= $requirement['value'];
            case 'current_level':
                return $gamification->current_level >= $requirement['value'];
            case 'badges_earned':
                return $gamification->badges_earned >= $requirement['value'];
            case 'challenges_completed':
                return $gamification->challenges_completed >= $requirement['value'];
            case 'quests_completed':
                return $gamification->quests_completed >= $requirement['value'];
            case 'current_streak':
                return $gamification->current_streak >= $requirement['value'];
            case 'properties_listed':
                $propertyCount = \App\Models\Property::where('user_id', $user->id)->count();
                return $propertyCount >= $requirement['value'];
            case 'properties_sold':
                $soldCount = \App\Models\Property::where('user_id', $user->id)
                    ->where('status', 'sold')
                    ->count();
                return $soldCount >= $requirement['value'];
            case 'specific_date':
                $targetDate = \Carbon\Carbon::parse($requirement['date']);
                return now()->greaterThanOrEqualTo($targetDate);
            case 'custom':
                // Custom requirement logic would go here
                return true;
            default:
                return false;
        }
    }

    public function unlock($userId, $reason = null): bool
    {
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return false;
        }

        if (!$this->canBeUnlockedBy($user)) {
            return false;
        }

        // Unlock achievement
        $this->users()->attach($userId, [
            'unlocked_at' => now(),
            'progress' => 100,
            'progress_data' => [],
            'reason' => $reason ?? 'Automatic unlock',
        ]);

        // Award points
        if ($this->points_reward > 0) {
            PropertyPoints::create([
                'user_id' => $userId,
                'points' => $this->points_reward,
                'type' => 'achievement_reward',
                'reason' => "إنجاز: {$this->name}",
                'awarded_at' => now(),
            ]);

            // Update user's total points
            $gamification = UserPropertyGamification::where('user_id', $userId)->first();
            if ($gamification) {
                $gamification->total_points += $this->points_reward;
                $gamification->experience_points += $this->points_reward;
                $gamification->save();
            }
        }

        // Award badge if specified
        if ($this->badge_reward) {
            $badge = PropertyBadge::find($this->badge_reward);
            if ($badge) {
                $badge->users()->attach($userId, [
                    'earned_at' => now(),
                    'reason' => "إنجاز: {$this->name}",
                ]);

                // Update user's badge count
                $gamification = UserPropertyGamification::where('user_id', $userId)->first();
                if ($gamification) {
                    $gamification->badges_earned++;
                    $gamification->save();
                }
            }
        }

        return true;
    }

    public function updateProgress($userId, $progress, $progressData = []): bool
    {
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return false;
        }

        // Check if user has this achievement
        $existingUnlock = $this->users()->where('user_id', $userId)->first();
        
        if ($existingUnlock && $existingUnlock->pivot->progress >= 100) {
            return false; // Already completed
        }

        $updateData = [
            'progress' => min(100, max(0, $progress)),
            'progress_data' => $progressData,
            'updated_at' => now(),
        ];

        if ($existingUnlock) {
            // Update existing progress
            $this->users()->updateExistingPivot($userId, $updateData);
            
            // Check if achievement is completed
            if ($progress >= 100 && $existingUnlock->pivot->progress < 100) {
                $this->completeAchievement($userId, $progressData);
            }
        } else {
            // Create new progress record
            $this->users()->attach($userId, array_merge([
                'unlocked_at' => now(),
                'progress' => $updateData['progress'],
                'progress_data' => $updateData['progress_data'],
                'reason' => 'Progress update',
            ], $updateData));
            
            // Check if achievement is completed immediately
            if ($progress >= 100) {
                $this->completeAchievement($userId, $progressData);
            }
        }

        return true;
    }

    private function completeAchievement($userId, $progressData): void
    {
        // Award points if not already awarded
        if ($this->points_reward > 0) {
            PropertyPoints::create([
                'user_id' => $userId,
                'points' => $this->points_reward,
                'type' => 'achievement_completion',
                'reason' => "إكمال إنجاز: {$this->name}",
                'awarded_at' => now(),
            ]);

            $gamification = UserPropertyGamification::where('user_id', $userId)->first();
            if ($gamification) {
                $gamification->total_points += $this->points_reward;
                $gamification->experience_points += $this->points_reward;
                $gamification->save();
            }
        }

        // Award badge if specified
        if ($this->badge_reward) {
            $badge = PropertyBadge::find($this->badge_reward);
            if ($badge) {
                $badge->users()->attach($userId, [
                    'earned_at' => now(),
                    'reason' => "إكمال إنجاز: {$this->name}",
                ]);

                $gamification = UserPropertyGamification::where('user_id', $userId)->first();
                if ($gamification) {
                    $gamification->badges_earned++;
                    $gamification->save();
                }
            }
        }
    }

    public function getUnlockCount(): int
    {
        return $this->users()->count();
    }

    public function getUniqueUnlockCount(): int
    {
        return $this->users()->distinct('user_id')->count('user_id');
    }

    public function getCompletionRate(): float
    {
        $totalUsers = \App\Models\User::count();
        $unlockedUsers = $this->getUniqueUnlockCount();
        
        return $totalUsers > 0 ? ($unlockedUsers / $totalUsers) * 100 : 0;
    }

    public function getUserProgress($userId): array
    {
        $userUnlock = $this->users()->where('user_id', $userId)->first();
        
        if (!$userUnlock) {
            return [
                'unlocked' => false,
                'progress' => 0,
                'progress_data' => [],
                'unlocked_at' => null,
            ];
        }

        return [
            'unlocked' => true,
            'progress' => $userUnlock->pivot->progress,
            'progress_data' => $userUnlock->pivot->progress_data ?? [],
            'unlocked_at' => $userUnlock->pivot->unlocked_at,
            'reason' => $userUnlock->pivot->reason,
            'is_completed' => $userUnlock->pivot->progress >= 100,
        ];
    }

    public function calculateProgress($userId): array
    {
        $user = \App\Models\User::find($userId);
        $gamification = UserPropertyGamification::where('user_id', $userId)->first();
        
        if (!$user || !$gamification) {
            return [
                'progress' => 0,
                'requirements_met' => [],
                'requirements_missing' => $this->requirements ?? [],
                'can_unlock' => false,
            ];
        }

        if (empty($this->requirements)) {
            return [
                'progress' => 100,
                'requirements_met' => [],
                'requirements_missing' => [],
                'can_unlock' => true,
            ];
        }

        $totalRequirements = count($this->requirements);
        $metRequirements = [];
        $missingRequirements = [];

        foreach ($this->requirements as $requirement) {
            $met = $this->checkSingleRequirement($requirement, $user, $gamification);
            
            if ($met) {
                $metRequirements[] = $requirement;
            } else {
                $missingRequirements[] = $requirement;
            }
        }

        $progress = ($totalRequirements > 0) ? (count($metRequirements) / $totalRequirements) * 100 : 0;

        return [
            'progress' => $progress,
            'requirements_met' => $metRequirements,
            'requirements_missing' => $missingRequirements,
            'can_unlock' => $progress >= 100 && $this->canBeUnlockedBy($user),
        ];
    }

    public function getAchievementStatistics(): array
    {
        $unlocks = $this->users;
        
        return [
            'total_unlocks' => $unlocks->count(),
            'unique_users' => $unlocks->distinct('user_id')->count('user_id'),
            'completion_rate' => $this->getCompletionRate(),
            'recent_unlocks' => $unlocks->orderBy('pivot_unlocked_at', 'desc')->take(10)->get(),
            'most_active_users' => $unlocks->with('user')
                ->orderBy('pivot_unlocked_at', 'desc')
                ->take(5)
                ->get(),
        ];
    }

    public function duplicate(): self
    {
        $newAchievement = $this->replicate();
        $newAchievement->name = $this->name . ' (نسخة)';
        $newAchievement->created_at = now();
        $newAchievement->updated_at = now();
        $newAchievement->save();
        
        return $newAchievement;
    }

    public function hide(): void
    {
        $this->hidden = true;
        $this->save();
    }

    public function show(): void
    {
        $this->hidden = false;
        $this->save();
    }

    public function extendExpiry($days): void
    {
        if ($this->expires_at) {
            $this->expires_at = $this->expires_at->addDays($days);
            $this->save();
        }
    }

    public static function getAvailableAchievements($userId = null): array
    {
        $query = self::visible()->active();
        
        if ($userId) {
            $query->whereDoesntHave('users', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
        }
        
        return $query->get()
            ->map(function ($achievement) use ($userId) {
                $achievement->progress = 0;
                $achievement->can_unlock = true;
                
                if ($userId) {
                    $progress = $achievement->calculateProgress($userId);
                    $achievement->progress = $progress['progress'];
                    $achievement->can_unlock = $progress['can_unlock'];
                }
                
                return $achievement;
            })
            ->toArray();
    }

    public static function getPopularAchievements($limit = 10): array
    {
        return self::withCount('users')
            ->orderBy('users_count', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($achievement) {
                return [
                    'id' => $achievement->id,
                    'name' => $achievement->name,
                    'type' => $achievement->type,
                    'category' => $achievement->category,
                    'difficulty' => $achievement->difficulty,
                    'points_reward' => $achievement->points_reward,
                    'unlock_count' => $achievement->users_count,
                    'completion_rate' => $achievement->getCompletionRate(),
                    'is_hidden' => $achievement->hidden,
                ];
            })
            ->toArray();
    }

    public static function getAchievementAnalytics($period = 'month'): array
    {
        $dateRange = self::getDateRange($period);
        
        return [
            'total_achievements' => self::whereBetween('created_at', $dateRange)->count(),
            'active_achievements' => self::active()->count(),
            'hidden_achievements' => self::hidden()->count(),
            'total_unlocks' => \DB::table('achievement_user')->whereBetween('unlocked_at', $dateRange)->count(),
            'unique_unlocks' => \DB::table('achievement_user')->whereBetween('unlocked_at', $dateRange)->distinct('user_id')->count('user_id'),
            'achievements_by_type' => self::getAchievementsByType($dateRange),
            'achievements_by_difficulty' => self::getAchievementsByDifficulty($dateRange),
            'achievements_by_category' => self::getAchievementsByCategory($dateRange),
            'recent_unlocks' => self::getRecentUnlocks($dateRange),
        ];
    }

    private static function getAchievementsByType($dateRange): array
    {
        return self::whereBetween('created_at', $dateRange)
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    private static function getAchievementsByDifficulty($dateRange): array
    {
        return self::whereBetween('created_at', $dateRange)
            ->selectRaw('difficulty, COUNT(*) as count')
            ->groupBy('difficulty')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    private static function getAchievementsByCategory($dateRange): array
    {
        return self::whereBetween('created_at', $dateRange)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    private static function getRecentUnlocks($dateRange): array
    {
        return \DB::table('achievement_user')
            ->join('property_achievements', 'achievement_user.achievement_id', '=', 'property_achievements.id')
            ->whereBetween('achievement_user.unlocked_at', $dateRange)
            ->selectRaw('property_achievements.name, COUNT(*) as unlock_count')
            ->groupBy('property_achievements.id', 'property_achievements.name')
            ->orderBy('unlock_count', 'desc')
            ->take(10)
            ->get()
            ->toArray();
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
