<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PropertyChallenge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'difficulty',
        'status',
        'start_date',
        'end_date',
        'max_participants',
        'points_reward',
        'badge_reward',
        'requirements',
        'rules',
        'prizes',
        'image',
        'metadata',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'requirements' => 'array',
        'rules' => 'array',
        'prizes' => 'array',
        'metadata' => 'array',
        'max_participants' => 'integer',
        'points_reward' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'challenge_user')
            ->withPivot('joined_at', 'status', 'score', 'progress', 'completed_at', 'final_score', 'notes')
            ->withTimestamps();
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
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

    public function scopeProperty($query)
    {
        return $query->where('type', 'property');
    }

    public function scopeSocial($query)
    {
        return $query->where('type', 'social');
    }

    public function scopeCustom($query)
    {
        return $query->where('type', 'custom');
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->start_date->lessThanOrEqualTo(now()) && 
               $this->end_date->greaterThanOrEqualTo(now());
    }

    public function isUpcoming(): bool
    {
        return $this->start_date->greaterThan(now());
    }

    public function isExpired(): bool
    {
        return $this->end_date->lessThan(now());
    }

    public function isFull(): bool
    {
        return $this->max_participants && 
               $this->participants()->count() >= $this->max_participants;
    }

    public function getDaysRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        
        return now()->diffInDays($this->end_date);
    }

    public function getDaysUntilStart(): int
    {
        if ($this->isUpcoming()) {
            return now()->diffInDays($this->start_date);
        }
        
        return 0;
    }

    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getParticipantCount(): int
    {
        return $this->participants()->count();
    }

    public function getCompletedCount(): int
    {
        return $this->participants()->wherePivot('completed', true)->count();
    }

    public function getCompletionRate(): float
    {
        $total = $this->getParticipantCount();
        $completed = $this->getCompletedCount();
        
        return $total > 0 ? ($completed / $total) * 100 : 0;
    }

    public function getTypeLabel(): string
    {
        $labels = [
            'points' => 'نقاط',
            'level' => 'مستوى',
            'badges' => 'شارات',
            'property' => 'عقار',
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

    public function getStatusLabel(): string
    {
        $labels = [
            'draft' => 'مسودة',
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'expired' => 'منتهي',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColor(): string
    {
        $colors = [
            'draft' => '#6c757d', // Gray
            'active' => '#28a745', // Green
            'completed' => '#007bff', // Blue
            'expired' => '#dc3545', // Red
        ];
        
        return $colors[$this->status] ?? '#6c757d';
    }

    public function canJoin($user): bool
    {
        if (!$user) {
            return false;
        }

        if (!$this->isActive()) {
            return false;
        }

        if ($this->isFull()) {
            return false;
        }

        if ($this->participants()->where('user_id', $user->id)->exists()) {
            return false;
        }

        return true;
    }

    public function join($userId, $data = []): bool
    {
        if (!$this->canJoin(\App\Models\User::find($userId))) {
            return false;
        }

        $this->participants()->attach($userId, array_merge([
            'joined_at' => now(),
            'status' => 'active',
            'score' => 0,
            'progress' => 0,
        ], $data));

        return true;
    }

    public function leave($userId): bool
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        
        if (!$participant) {
            return false;
        }

        $this->participants()->detach($userId);
        return true;
    }

    public function updateProgress($userId, $progress, $score = null, $notes = null): bool
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        
        if (!$participant) {
            return false;
        }

        $updateData = [
            'progress' => min(100, max(0, $progress)),
            'updated_at' => now(),
        ];

        if ($score !== null) {
            $updateData['score'] = $score;
        }

        if ($notes !== null) {
            $updateData['notes'] = $notes;
        }

        // Check if completed
        if ($progress >= 100 && !$participant->pivot->completed) {
            $updateData['completed'] = true;
            $updateData['completed_at'] = now();
            $updateData['final_score'] = $score ?? $participant->pivot->score;
        }

        $this->participants()->updateExistingPivot($userId, $updateData);
        return true;
    }

    public function getTopParticipants($limit = 10): array
    {
        return $this->participants()
            ->orderByPivot('score', 'desc')
            ->orderByPivot('completed_at')
            ->take($limit)
            ->get()
            ->map(function ($participant) {
                return [
                    'user_id' => $participant->id,
                    'user_name' => $participant->name,
                    'score' => $participant->pivot->score,
                    'progress' => $participant->pivot->progress,
                    'completed' => $participant->pivot->completed,
                    'completed_at' => $participant->pivot->completed_at,
                    'joined_at' => $participant->pivot->joined_at,
                ];
            })
            ->toArray();
    }

    public function getChallengeStatistics(): array
    {
        $participants = $this->participants;
        $completed = $participants->wherePivot('completed', true);
        $inProgress = $participants->wherePivot('completed', false);

        return [
            'total_participants' => $participants->count(),
            'completed_count' => $completed->count(),
            'in_progress_count' => $inProgress->count(),
            'completion_rate' => $this->getCompletionRate(),
            'average_score' => $completed->avg('pivot.score'),
            'highest_score' => $completed->max('pivot.score'),
            'average_progress' => $participants->avg('pivot.progress'),
            'days_remaining' => $this->getDaysRemaining(),
            'is_active' => $this->isActive(),
            'is_full' => $this->isFull(),
        ];
    }

    public function calculateDifficultyScore(): int
    {
        $scores = [
            'easy' => 1,
            'medium' => 2,
            'hard' => 3,
            'expert' => 4,
        ];
        
        return $scores[$this->difficulty] ?? 1;
    }

    public function calculateRewardScore(): int
    {
        $baseScore = $this->points_reward ?? 0;
        
        // Add bonus for badge rewards
        if ($this->badge_reward) {
            $baseScore += 50; // Badge bonus
        }
        
        // Add bonus for prizes
        if (!empty($this->prizes)) {
            $baseScore += count($this->prizes) * 25;
        }
        
        return $baseScore;
    }

    public function getChallengeScore(): int
    {
        $difficultyScore = $this->calculateDifficultyScore();
        $rewardScore = $this->calculateRewardScore();
        
        return ($difficultyScore * 10) + $rewardScore;
    }

    public function getRecommendedLevel(): int
    {
        $score = $this->getChallengeScore();
        
        if ($score <= 50) {
            return 1;
        } elseif ($score <= 100) {
            return 5;
        } elseif ($score <= 200) {
            return 10;
        } elseif ($score <= 300) {
            return 15;
        } else {
            return 20;
        }
    }

    public function duplicate(): self
    {
        $newChallenge = $this->replicate();
        $newChallenge->title = $this->title . ' (نسخة)';
        $newChallenge->status = 'draft';
        $newChallenge->created_at = now();
        $newChallenge->updated_at = now();
        $newChallenge->save();
        
        return $newChallenge;
    }

    public function archive(): void
    {
        $this->status = 'completed';
        $this->save();
    }

    public function extend($days): void
    {
        $this->end_date = $this->end_date->addDays($days);
        $this->save();
    }

    public function getFormattedDuration(): string
    {
        $duration = $this->getDurationInDays();
        
        if ($duration < 7) {
            return $duration . ' أيام';
        } elseif ($duration < 30) {
            return round($duration / 7, 1) . ' أسابيع';
        } elseif ($duration < 365) {
            return round($duration / 30, 1) . ' أشهر';
        } else {
            return round($duration / 365, 1) . ' سنوات';
        }
    }

    public function getFormattedRewards(): array
    {
        $rewards = [];
        
        if ($this->points_reward > 0) {
            $rewards[] = [
                'type' => 'points',
                'value' => $this->points_reward,
                'label' => number_format($this->points_reward) . ' نقطة',
            ];
        }
        
        if ($this->badge_reward) {
            $badge = \App\Models\PropertyBadge::find($this->badge_reward);
            if ($badge) {
                $rewards[] = [
                    'type' => 'badge',
                    'value' => $badge->name,
                    'icon' => $badge->icon,
                    'label' => $badge->name,
                ];
            }
        }
        
        if (!empty($this->prizes)) {
            foreach ($this->prizes as $prize) {
                $rewards[] = [
                    'type' => 'prize',
                    'value' => $prize['name'] ?? 'جائزة',
                    'label' => $prize['name'] ?? 'جائزة',
                ];
            }
        }
        
        return $rewards;
    }

    public static function getActiveChallenges(): array
    {
        return self::active()
            ->withCount('participants')
            ->orderBy('end_date')
            ->get()
            ->map(function ($challenge) {
                return [
                    'id' => $challenge->id,
                    'title' => $challenge->title,
                    'type' => $challenge->type,
                    'difficulty' => $challenge->difficulty,
                    'participants_count' => $challenge->participants_count,
                    'days_remaining' => $challenge->getDaysRemaining(),
                    'points_reward' => $challenge->points_reward,
                    'is_full' => $challenge->isFull(),
                ];
            })
            ->toArray();
    }

    public static function getUpcomingChallenges(): array
    {
        return self::upcoming()
            ->withCount('participants')
            ->orderBy('start_date')
            ->take(5)
            ->get()
            ->map(function ($challenge) {
                return [
                    'id' => $challenge->id,
                    'title' => $challenge->title,
                    'type' => $challenge->type,
                    'difficulty' => $challenge->difficulty,
                    'max_participants' => $challenge->max_participants,
                    'days_until_start' => $challenge->getDaysUntilStart(),
                    'points_reward' => $challenge->points_reward,
                ];
            })
            ->toArray();
    }

    public static function getChallengeAnalytics($period = 'month'): array
    {
        $dateRange = self::getDateRange($period);
        
        return [
            'total_challenges' => self::whereBetween('created_at', $dateRange)->count(),
            'active_challenges' => self::active()->count(),
            'completed_challenges' => self::where('status', 'completed')->count(),
            'total_participants' => self::withCount('participants')->get()->sum('participants_count'),
            'average_participants' => self::withCount('participants')->get()->avg('participants_count'),
            'completion_rates' => self::getCompletionRatesByDifficulty(),
            'popular_types' => self::getPopularTypes($dateRange),
            'difficulty_distribution' => self::getDifficultyDistribution($dateRange),
        ];
    }

    private static function getCompletionRatesByDifficulty(): array
    {
        $difficulties = ['easy', 'medium', 'hard', 'expert'];
        $rates = [];
        
        foreach ($difficulties as $difficulty) {
            $challenges = self::where('difficulty', $difficulty)->get();
            $totalChallenges = $challenges->count();
            
            if ($totalChallenges === 0) {
                $rates[$difficulty] = 0;
                continue;
            }
            
            $totalParticipants = 0;
            $completedParticipants = 0;
            
            foreach ($challenges as $challenge) {
                $participants = $challenge->participants;
                $totalParticipants += $participants->count();
                $completedParticipants += $participants->wherePivot('completed', true)->count();
            }
            
            $rates[$difficulty] = $totalParticipants > 0 ? 
                ($completedParticipants / $totalParticipants) * 100 : 0;
        }
        
        return $rates;
    }

    private static function getPopularTypes($dateRange): array
    {
        return self::whereBetween('created_at', $dateRange)
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    private static function getDifficultyDistribution($dateRange): array
    {
        return self::whereBetween('created_at', $dateRange)
            ->selectRaw('difficulty, COUNT(*) as count')
            ->groupBy('difficulty')
            ->orderBy('count', 'desc')
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
