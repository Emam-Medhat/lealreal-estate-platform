<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PropertyBadge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'rarity',
        'category',
        'points_required',
        'level_required',
        'status',
        'requirements',
        'rewards',
        'expires_at',
        'image',
        'metadata',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'requirements' => 'array',
        'rewards' => 'array',
        'metadata' => 'array',
        'points_required' => 'integer',
        'level_required' => 'integer',
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
        return $this->belongsToMany(User::class, 'badge_user')
            ->withPivot('earned_at', 'reason', 'progress')
            ->withTimestamps();
    }

    // Scopes
    public function scopeByRarity($query, $rarity)
    {
        return $query->where('rarity', $rarity);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeHidden($query)
    {
        return $query->where('status', 'hidden');
    }

    public function scopeCommon($query)
    {
        return $query->where('rarity', 'common');
    }

    public function scopeUncommon($query)
    {
        return $query->where('rarity', 'uncommon');
    }

    public function scopeRare($query)
    {
        return $query->where('rarity', 'rare');
    }

    public function scopeEpic($query)
    {
        return $query->where('rarity', 'epic');
    }

    public function scopeLegendary($query)
    {
        return $query->where('rarity', 'legendary');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
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

    // Methods
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

    public function getRarityLabel(): string
    {
        $labels = [
            'common' => 'شائع',
            'uncommon' => 'غير شائع',
            'rare' => 'نادر',
            'epic' => 'أسطوري',
            'legendary' => 'أسطوري',
        ];
        
        return $labels[$this->rarity] ?? $this->rarity;
    }

    public function getRarityColor(): string
    {
        $colors = [
            'common' => '#808080', // Gray
            'uncommon' => '#008000', // Green
            'rare' => '#0000FF', // Blue
            'epic' => '#800080', // Purple
            'legendary' => '#FFD700', // Gold
        ];
        
        return $colors[$this->rarity] ?? '#808080';
    }

    public function getStatusLabel(): string
    {
        $labels = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'hidden' => 'مخفي',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }

    public function getPointsRequiredFormatted(): string
    {
        return number_format($this->points_required) . ' نقطة';
    }

    public function canBeEarnedBy($user): bool
    {
        if (!$user) {
            return false;
        }

        // Check if badge is active
        if ($this->status !== 'active') {
            return false;
        }

        // Check if badge is expired
        if ($this->isExpired()) {
            return false;
        }

        // Check if user already has this badge
        if ($this->users()->where('user_id', $user->id)->exists()) {
            return false;
        }

        // Get user's gamification data
        $gamification = UserPropertyGamification::where('user_id', $user->id)->first();
        if (!$gamification) {
            return false;
        }

        // Check points requirement
        if ($gamification->total_points < $this->points_required) {
            return false;
        }

        // Check level requirement
        if ($gamification->current_level < $this->level_required) {
            return false;
        }

        // Check additional requirements
        if (!empty($this->requirements)) {
            foreach ($this->requirements as $requirement) {
                if (!$this->checkRequirement($requirement, $user, $gamification)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function checkRequirement($requirement, $user, $gamification): bool
    {
        switch ($requirement['type']) {
            case 'challenges_completed':
                return $gamification->challenges_completed >= $requirement['value'];
            case 'quests_completed':
                return $gamification->quests_completed >= $requirement['value'];
            case 'badges_earned':
                return $gamification->badges_earned >= $requirement['value'];
            case 'current_streak':
                return $gamification->current_streak >= $requirement['value'];
            case 'property_listed':
                $propertyCount = \App\Models\Property::where('user_id', $user->id)->count();
                return $propertyCount >= $requirement['value'];
            case 'property_sold':
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

    public function awardTo($userId, $reason = null): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        if (!$this->canBeEarnedBy($user)) {
            return false;
        }

        // Award badge
        $this->users()->attach($userId, [
            'earned_at' => now(),
            'reason' => $reason ?? 'Manual award',
            'progress' => 100,
        ]);

        // Update user's badge count
        $gamification = UserPropertyGamification::where('user_id', $userId)->first();
        if ($gamification) {
            $gamification->badges_earned++;
            $gamification->save();
        }

        // Award rewards if any
        if (!empty($this->rewards)) {
            $this->awardRewards($userId, $this->rewards);
        }

        return true;
    }

    private function awardRewards($userId, $rewards): void
    {
        foreach ($rewards as $reward) {
            switch ($reward['type']) {
                case 'points':
                    PropertyPoints::create([
                        'user_id' => $userId,
                        'points' => $reward['value'],
                        'type' => 'badge_reward',
                        'reason' => "مكافأة شارة: {$this->name}",
                        'awarded_at' => now(),
                    ]);
                    break;
                case 'experience':
                    $gamification = UserPropertyGamification::where('user_id', $userId)->first();
                    if ($gamification) {
                        $gamification->experience_points += $reward['value'];
                        $gamification->save();
                    }
                    break;
                case 'level':
                    $gamification = UserPropertyGamification::where('user_id', $userId)->first();
                    if ($gamification) {
                        $gamification->current_level = max($gamification->current_level, $reward['value']);
                        $gamification->save();
                    }
                    break;
            }
        }
    }

    public function revokeFrom($userId, $reason = null): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        // Check if user has this badge
        if (!$this->users()->where('user_id', $userId)->exists()) {
            return false;
        }

        // Revoke badge
        $this->users()->detach($userId);

        // Update user's badge count
        $gamification = UserPropertyGamification::where('user_id', $userId)->first();
        if ($gamification) {
            $gamification->badges_earned = max(0, $gamification->badges_earned - 1);
            $gamification->save();
        }

        return true;
    }

    public function getProgressForUser($userId): array
    {
        $user = User::find($userId);
        if (!$user) {
            return [];
        }

        $gamification = UserPropertyGamification::where('user_id', $userId)->first();
        if (!$gamification) {
            return [
                'can_earn' => false,
                'progress' => 0,
                'requirements_met' => [],
                'requirements_missing' => $this->requirements ?? [],
            ];
        }

        $progress = 0;
        $requirementsMet = [];
        $requirementsMissing = [];

        // Check basic requirements
        if ($gamification->total_points >= $this->points_required) {
            $progress += 30;
            $requirementsMet[] = 'points';
        } else {
            $requirementsMissing[] = [
                'type' => 'points',
                'required' => $this->points_required,
                'current' => $gamification->total_points,
                'missing' => max(0, $this->points_required - $gamification->total_points),
            ];
        }

        if ($gamification->current_level >= $this->level_required) {
            $progress += 30;
            $requirementsMet[] = 'level';
        } else {
            $requirementsMissing[] = [
                'type' => 'level',
                'required' => $this->level_required,
                'current' => $gamification->current_level,
                'missing' => max(0, $this->level_required - $gamification->current_level),
            ];
        }

        // Check additional requirements
        if (!empty($this->requirements)) {
            $requirementWeight = 40 / count($this->requirements);
            
            foreach ($this->requirements as $requirement) {
                if ($this->checkRequirement($requirement, $user, $gamification)) {
                    $progress += $requirementWeight;
                    $requirementsMet[] = $requirement['type'];
                } else {
                    $requirementsMissing[] = $requirement;
                }
            }
        }

        return [
            'can_earn' => $this->canBeEarnedBy($user),
            'progress' => min(100, $progress),
            'requirements_met' => $requirementsMet,
            'requirements_missing' => $requirementsMissing,
            'points_shortage' => max(0, $this->points_required - $gamification->total_points),
            'level_shortage' => max(0, $this->level_required - $gamification->current_level),
        ];
    }

    public function getBadgeStatistics(): array
    {
        $totalUsers = $this->users()->count();
        $recentEarners = $this->users()
            ->withPivot('earned_at')
            ->orderBy('pivot_earned_at', 'desc')
            ->take(10)
            ->get();

        return [
            'total_earners' => $totalUsers,
            'recent_earners' => $recentEarners->map(function ($user) {
                return [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'earned_at' => $user->pivot->earned_at,
                    'reason' => $user->pivot->reason,
                ];
            })->toArray(),
            'first_earned_at' => $this->users()
                ->orderBy('pivot_earned_at', 'asc')
                ->first()
                ?->pivot->earned_at,
            'last_earned_at' => $this->users()
                ->orderBy('pivot_earned_at', 'desc')
                ->first()
                ?->pivot->earned_at,
            'earning_rate' => $this->calculateEarningRate(),
        ];
    }

    private function calculateEarningRate(): float
    {
        $firstEarned = $this->users()
            ->orderBy('pivot_earned_at', 'asc')
            ->first()
            ?->pivot->earned_at;

        if (!$firstEarned) {
            return 0;
        }

        $daysSinceFirst = $firstEarned->diffInDays(now());
        $totalEarners = $this->users()->count();

        return $daysSinceFirst > 0 ? ($totalEarners / $daysSinceFirst) * 100 : 0;
    }

    public static function getBadgesByRarity(): array
    {
        return [
            'common' => self::common()->count(),
            'uncommon' => self::uncommon()->count(),
            'rare' => self::rare()->count(),
            'epic' => self::epic()->count(),
            'legendary' => self::legendary()->count(),
        ];
    }

    public static function getBadgesByCategory(): array
    {
        return self::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    public static function getMostEarnedBadges($limit = 10): array
    {
        return self::withCount('users')
            ->orderBy('users_count', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($badge) {
                return [
                    'id' => $badge->id,
                    'name' => $badge->name,
                    'rarity' => $badge->rarity,
                    'category' => $badge->category,
                    'earners_count' => $badge->users_count,
                    'icon' => $badge->icon,
                ];
            })
            ->toArray();
    }

    public static function cleanupExpiredBadges(): int
    {
        $expiredBadges = self::expired()->get();
        $count = $expiredBadges->count();
        
        // Archive expired badges
        foreach ($expiredBadges as $badge) {
            \DB::table('expired_badges_archive')->insert([
                'badge_id' => $badge->id,
                'name' => $badge->name,
                'rarity' => $badge->rarity,
                'category' => $badge->category,
                'original_expires_at' => $badge->expires_at,
                'archived_at' => now(),
            ]);
        }
        
        // Update status to expired
        self::expired()->update(['status' => 'expired']);
        
        return $count;
    }
}
