<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyLevel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'level',
        'name',
        'description',
        'tier',
        'experience_required',
        'bonus_points',
        'privileges',
        'rewards',
        'badge_unlock',
        'color',
        'icon',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'experience_required' => 'integer',
        'bonus_points' => 'integer',
        'privileges' => 'array',
        'rewards' => 'array',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(UserPropertyGamification::class, 'current_level');
    }

    // Scopes
    public function scopeByTier($query, $tier)
    {
        return $query->where('tier', $tier);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    public function scopeBronze($query)
    {
        return $query->where('tier', 'bronze');
    }

    public function scopeSilver($query)
    {
        return $query->where('tier', 'silver');
    }

    public function scopeGold($query)
    {
        return $query->where('tier', 'gold');
    }

    public function scopePlatinum($query)
    {
        return $query->where('tier', 'platinum');
    }

    public function scopeDiamond($query)
    {
        return $query->where('tier', 'diamond');
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByMinLevel($query, $level)
    {
        return $query->where('level', '>=', $level);
    }

    public function scopeByMaxLevel($query, $level)
    {
        return $query->where('level', '<=', $level);
    }

    public function scopeBeginner($query)
    {
        return $query->whereBetween('level', [1, 5]);
    }

    public function scopeIntermediate($query)
    {
        return $query->whereBetween('level', [6, 15]);
    }

    public function scopeAdvanced($query)
    {
        return $query->whereBetween('level', [16, 30]);
    }

    public function scopeExpert($query)
    {
        return $query->where('level', '>', 30);
    }

    // Methods
    public function getTierLabel(): string
    {
        $labels = [
            'bronze' => 'برونزي',
            'silver' => 'فضي',
            'gold' => 'ذهبي',
            'platinum' => 'بلاتيني',
            'diamond' => 'ألماسي',
        ];
        
        return $labels[$this->tier] ?? $this->tier;
    }

    public function getTierColor(): string
    {
        $colors = [
            'bronze' => '#CD7F32', // Bronze
            'silver' => '#C0C0C0', // Silver
            'gold' => '#FFD700', // Gold
            'platinum' => '#E5E4E2', // Platinum
            'diamond' => '#B9F2FF', // Diamond
        ];
        
        return $colors[$this->tier] ?? '#808080';
    }

    public function getFormattedLevel(): string
    {
        return 'المستوى ' . $this->level;
    }

    public function getFormattedExperienceRequired(): string
    {
        return number_format($this->experience_required) . ' نقطة خبرة';
    }

    public function getFormattedBonusPoints(): string
    {
        return '+' . number_format($this->bonus_points) . ' نقطة';
    }

    public function isHigherThan($otherLevel): bool
    {
        return $this->level > $otherLevel;
    }

    public function isLowerThan($otherLevel): bool
    {
        return $this->level < $otherLevel;
    }

    public function getLevelDifference($otherLevel): int
    {
        return $this->level - $otherLevel;
    }

    public function getNextLevel(): ?self
    {
        return self::where('level', $this->level + 1)->first();
    }

    public function getPreviousLevel(): ?self
    {
        return self::where('level', $this->level - 1)->first();
    }

    public function getExperienceToNextLevel(): int
    {
        $nextLevel = $this->getNextLevel();
        
        if (!$nextLevel) {
            return 0;
        }
        
        return $nextLevel->experience_required - $this->experience_required;
    }

    public function getExperienceFromPreviousLevel(): int
    {
        $previousLevel = $this->getPreviousLevel();
        
        if (!$previousLevel) {
            return $this->experience_required;
        }
        
        return $this->experience_required - $previousLevel->experience_required;
    }

    public function getUserCount(): int
    {
        return $this->users()->count();
    }

    public function getActiveUserCount(): int
    {
        return $this->users()
            ->whereHas('user', function ($query) {
                $query->where('last_activity_at', '>=', now()->subDays(30));
            })
            ->count();
    }

    public function getLevelStatistics(): array
    {
        $users = $this->users;
        
        return [
            'total_users' => $users->count(),
            'active_users' => $this->getActiveUserCount(),
            'average_points' => $users->avg('total_points'),
            'average_badges' => $users->avg('badges_earned'),
            'average_challenges' => $users->avg('challenges_completed'),
            'average_quests' => $users->avg('quests_completed'),
            'highest_points' => $users->max('total_points'),
            'most_active_user' => $users->sortByDesc('total_points')->first(),
        ];
    }

    public function hasPrivilege($privilege): bool
    {
        return in_array($privilege, $this->privileges ?? []);
    }

    public function getPrivilegesList(): array
    {
        return $this->privileges ?? [];
    }

    public function getRewardsList(): array
    {
        return $this->rewards ?? [];
    }

    public function getBadge(): ?PropertyBadge
    {
        if ($this->badge_unlock) {
            return PropertyBadge::find($this->badge_unlock);
        }
        
        return null;
    }

    public function getLevelProgress($userExperience): array
    {
        $previousLevel = $this->getPreviousLevel();
        $experienceFromPrevious = $previousLevel ? 
            $this->experience_required - $previousLevel->experience_required : 
            $this->experience_required;
        
        $experienceInLevel = $userExperience - ($this->experience_required - $experienceFromPrevious);
        $progress = min(100, ($experienceInLevel / $experienceFromPrevious) * 100);
        
        return [
            'current_level' => $this->level,
            'experience_required' => $this->experience_required,
            'user_experience' => $userExperience,
            'experience_in_level' => $experienceInLevel,
            'experience_from_previous' => $experienceFromPrevious,
            'progress' => $progress,
            'experience_to_next' => $this->getExperienceToNextLevel(),
            'is_max_level' => $this->getNextLevel() === null,
        ];
    }

    public function calculateLevelUpBonus($userExperience): int
    {
        if ($userExperience >= $this->experience_required) {
            return $this->bonus_points;
        }
        
        return 0;
    }

    public function promoteUser($userId): bool
    {
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return false;
        }

        $gamification = UserPropertyGamification::where('user_id', $userId)->first();
        if (!$gamification) {
            return false;
        }

        if ($gamification->experience_points < $this->experience_required) {
            return false;
        }

        // Update user's level
        $oldLevel = $gamification->current_level;
        $gamification->current_level = $this->level;
        $gamification->experience_points = $gamification->experience_points - $this->experience_required;
        
        // Award bonus points
        if ($this->bonus_points > 0) {
            $gamification->total_points += $this->bonus_points;
            
            // Create points record
            PropertyPoints::create([
                'user_id' => $userId,
                'points' => $this->bonus_points,
                'type' => 'level_up_bonus',
                'reason' => "مكافأة ترقية المستوى {$this->level}",
                'awarded_at' => now(),
            ]);
        }

        // Award badge if applicable
        if ($this->badge_unlock) {
            $badge = PropertyBadge::find($this->badge_unlock);
            if ($badge) {
                $badge->users()->attach($userId, [
                    'earned_at' => now(),
                    'reason' => "ترقية المستوى {$this->level}",
                ]);
                
                $gamification->badges_earned++;
            }
        }

        $gamification->save();

        // Log level up
        PropertyPoints::create([
            'user_id' => $userId,
            'points' => 0,
            'type' => 'level_up',
            'reason' => "ترقية من المستوى {$oldLevel} إلى المستوى {$this->level}",
            'awarded_at' => now(),
        ]);

        return true;
    }

    public function getLevelRequirements(): array
    {
        return [
            'experience_required' => $this->experience_required,
            'previous_level' => $this->getPreviousLevel(),
            'next_level' => $this->getNextLevel(),
            'experience_from_previous' => $this->getExperienceFromPreviousLevel(),
            'experience_to_next' => $this->getExperienceToNextLevel(),
            'bonus_points' => $this->bonus_points,
            'privileges' => $this->getPrivilegesList(),
            'rewards' => $this->getRewardsList(),
            'badge' => $this->getBadge(),
        ];
    }

    public function getLevelPath(): array
    {
        $levels = self::orderBy('level')->get();
        $path = [];
        
        foreach ($levels as $level) {
            $path[] = [
                'level' => $level->level,
                'name' => $level->name,
                'tier' => $level->tier,
                'tier_label' => $level->getTierLabel(),
                'tier_color' => $level->getTierColor(),
                'experience_required' => $level->experience_required,
                'bonus_points' => $level->bonus_points,
                'user_count' => $level->getUserCount(),
            ];
        }
        
        return $path;
    }

    public function getLevelComparison($otherLevel): array
    {
        return [
            'level_difference' => $this->getLevelDifference($otherLevel),
            'experience_difference' => $this->experience_required - $otherLevel->experience_required,
            'bonus_difference' => $this->bonus_points - $otherLevel->bonus_points,
            'tier_comparison' => [
                'current_tier' => $this->tier,
                'other_tier' => $otherLevel->tier,
                'is_higher_tier' => $this->getTierRank() > $otherLevel->getTierRank(),
            ],
        ];
    }

    private function getTierRank(): int
    {
        $ranks = [
            'bronze' => 1,
            'silver' => 2,
            'gold' => 3,
            'platinum' => 4,
            'diamond' => 5,
        ];
        
        return $ranks[$this->tier] ?? 0;
    }

    public static function getMaxLevel(): int
    {
        return self::max('level') ?? 1;
    }

    public static function getLevelByExperience($experience): ?self
    {
        return self::where('experience_required', '<=', $experience)
            ->orderBy('level', 'desc')
            ->first();
    }

    public static function getLevelForUser($userId): ?self
    {
        $gamification = UserPropertyGamification::where('user_id', $userId)->first();
        
        if (!$gamification) {
            return self::where('level', 1)->first();
        }
        
        return self::where('level', $gamification->current_level)->first();
    }

    public static function getUserLevelProgress($userId): array
    {
        $level = self::getLevelForUser($userId);
        $gamification = UserPropertyGamification::where('user_id', $userId)->first();
        
        if (!$level || !$gamification) {
            return [
                'current_level' => 1,
                'experience_points' => 0,
                'progress' => 0,
                'experience_to_next' => 0,
            ];
        }
        
        return $level->getLevelProgress($gamification->experience_points);
    }

    public static function getLevelDistribution(): array
    {
        return self::withCount('users')
            ->orderBy('level')
            ->get()
            ->map(function ($level) {
                return [
                    'level' => $level->level,
                    'name' => $level->name,
                    'tier' => $level->tier,
                    'tier_label' => $level->getTierLabel(),
                    'tier_color' => $level->getTierColor(),
                    'user_count' => $level->users_count,
                    'percentage' => ($level->users_count / UserPropertyGamification::count()) * 100,
                ];
            })
            ->toArray();
    }

    public static function getTierDistribution(): array
    {
        $levels = self::all();
        $tierCounts = [
            'bronze' => 0,
            'silver' => 0,
            'gold' => 0,
            'platinum' => 0,
            'diamond' => 0,
        ];
        
        foreach ($levels as $level) {
            $tierCounts[$level->tier] += $level->getUserCount();
        }
        
        return $tierCounts;
    }

    public static function getLevelAnalytics($period = 'month'): array
    {
        $dateRange = self::getDateRange($period);
        
        return [
            'total_levels' => self::count(),
            'active_levels' => self::active()->count(),
            'total_users_at_levels' => self::withCount('users')->get()->sum('users_count'),
            'average_users_per_level' => self::withCount('users')->get()->avg('users_count'),
            'level_distribution' => self::getLevelDistribution(),
            'tier_distribution' => self::getTierDistribution(),
            'recent_level_ups' => PropertyPoints::whereBetween('created_at', $dateRange)
                ->where('type', 'level_up')
                ->count(),
            'most_active_levels' => self::getMostActiveLevels($dateRange),
        ];
    }

    private static function getMostActiveLevels($dateRange): array
    {
        return PropertyPoints::selectRaw('reason, COUNT(*) as count')
            ->whereBetween('created_at', $dateRange)
            ->where('type', 'level_up')
            ->where('reason', 'like', 'ترقية إلى المستوى %')
            ->groupBy('reason')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                preg_match('/المستوى (\d+)/', $item->reason, $matches);
                $level = $matches[1] ?? 0;
                
                return [
                    'level' => (int) $level,
                    'count' => $item->count,
                ];
            })
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
