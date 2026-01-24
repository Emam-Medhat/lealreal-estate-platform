<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PropertyQuest extends Model
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
        'experience_reward',
        'badge_reward',
        'objectives',
        'rewards',
        'prerequisites',
        'image',
        'story_text',
        'metadata',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'max_participants' => 'integer',
        'points_reward' => 'integer',
        'experience_reward' => 'integer',
        'objectives' => 'array',
        'rewards' => 'array',
        'prerequisites' => 'array',
        'metadata' => 'array',
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
        return $this->belongsToMany(User::class, 'quest_user')
            ->withPivot('accepted_at', 'status', 'progress', 'objectives_completed', 'completed_at', 'final_score', 'notes')
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

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
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

    public function scopeDaily($query)
    {
        return $query->where('type', 'daily');
    }

    public function scopeWeekly($query)
    {
        return $query->where('type', 'weekly');
    }

    public function scopeMonthly($query)
    {
        return $query->where('type', 'monthly');
    }

    public function scopeStory($query)
    {
        return $query->where('type', 'story');
    }

    public function scopeSeasonal($query)
    {
        return $query->where('type', 'seasonal');
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
            'daily' => 'يومي',
            'weekly' => 'أسبوعي',
            'monthly' => 'شهري',
            'story' => 'قصة',
            'seasonal' => 'موسمي',
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

        return $this->checkPrerequisites($user);
    }

    private function checkPrerequisites($user): bool
    {
        if (empty($this->prerequisites)) {
            return true;
        }

        $gamification = UserPropertyGamification::where('user_id', $user->id)->first();
        if (!$gamification) {
            return false;
        }

        foreach ($this->prerequisites as $prerequisite) {
            if (!$this->checkSinglePrerequisite($prerequisite, $user, $gamification)) {
                return false;
            }
        }

        return true;
    }

    private function checkSinglePrerequisite($prerequisite, $user, $gamification): bool
    {
        switch ($prerequisite['type']) {
            case 'min_level':
                return $gamification->current_level >= $prerequisite['value'];
            case 'min_points':
                return $gamification->total_points >= $prerequisite['value'];
            case 'completed_quests':
                return $gamification->quests_completed >= $prerequisite['value'];
            case 'specific_quest':
                $completedQuest = self::whereHas('participants', function ($query) use ($user) {
                    $query->where('user_id', $user->id)->where('completed', true);
                })->where('id', $prerequisite['quest_id'])->first();
                return $completedQuest !== null;
            case 'badge_required':
                $hasBadge = $user->badges()->where('badge_id', $prerequisite['badge_id'])->exists();
                return $hasBadge;
            default:
                return false;
        }
    }

    public function join($userId, $data = []): bool
    {
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return false;
        }

        if (!$this->canJoin($user)) {
            return false;
        }

        $this->participants()->attach($userId, array_merge([
            'accepted_at' => now(),
            'status' => 'active',
            'progress' => 0,
            'objectives_completed' => [],
        ], $data));

        return true;
    }

    public function abandon($userId, $reason = null): bool
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        
        if (!$participant) {
            return false;
        }

        if ($participant->pivot->completed) {
            return false;
        }

        $this->participants()->updateExistingPivot($userId, [
            'status' => 'abandoned',
            'notes' => $reason,
            'abandoned_at' => now(),
        ]);

        return true;
    }

    public function updateProgress($userId, $objectiveIndex, $completed, $progressData = []): bool
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        
        if (!$participant) {
            return false;
        }

        $objectivesCompleted = $participant->pivot->objectives_completed ?? [];
        $objectivesCompleted[$objectiveIndex] = [
            'completed' => $completed,
            'completed_at' => $completed ? now() : null,
            'progress_data' => $progressData,
        ];

        // Calculate overall progress
        $totalObjectives = count($this->objectives);
        $completedObjectives = count(array_filter($objectivesCompleted, function ($obj) {
            return $obj['completed'] ?? false;
        }));
        $progress = ($totalObjectives > 0) ? ($completedObjectives / $totalObjectives) * 100 : 0;

        $updateData = [
            'progress' => $progress,
            'objectives_completed' => $objectivesCompleted,
            'updated_at' => now(),
        ];

        // Check if quest is completed
        if ($progress >= 100 && !$participant->pivot->completed) {
            $this->completeQuest($userId, $objectivesCompleted);
        }

        $this->participants()->updateExistingPivot($userId, $updateData);
        return true;
    }

    private function completeQuest($userId, $objectivesCompleted): void
    {
        // Award points
        if ($this->points_reward > 0) {
            PropertyPoints::create([
                'user_id' => $userId,
                'points' => $this->points_reward,
                'type' => 'quest_reward',
                'reason' => "إكمال مهمة: {$this->title}",
                'awarded_at' => now(),
            ]);

            // Update user's gamification stats
            $gamification = UserPropertyGamification::where('user_id', $userId)->first();
            if ($gamification) {
                $gamification->quests_completed++;
                $gamification->save();
            }
        }

        // Award experience
        if ($this->experience_reward > 0) {
            $gamification = UserPropertyGamification::where('user_id', $userId)->first();
            if ($gamification) {
                $gamification->experience_points += $this->experience_reward;
                $gamification->save();
            }
        }

        // Award badge if specified
        if ($this->badge_reward) {
            $badge = PropertyBadge::find($this->badge_reward);
            if ($badge) {
                $badge->users()->attach($userId, [
                    'earned_at' => now(),
                    'reason' => "إكمال مهمة: {$this->title}",
                ]);

                $gamification = UserPropertyGamification::where('user_id', $userId)->first();
                if ($gamification) {
                    $gamification->badges_earned++;
                    $gamification->save();
                }
            }
        }

        // Mark as completed
        $this->participants()->updateExistingPivot($userId, [
            'completed' => true,
            'completed_at' => now(),
            'final_progress' => 100,
        ]);
    }

    public function getTopParticipants($limit = 10): array
    {
        return $this->participants()
            ->orderByPivot('progress', 'desc')
            ->orderByPivot('completed_at')
            ->take($limit)
            ->get()
            ->map(function ($participant) {
                return [
                    'user_id' => $participant->id,
                    'user_name' => $participant->name,
                    'progress' => $participant->pivot->progress,
                    'completed' => $participant->pivot->completed,
                    'completed_at' => $participant->pivot->completed_at,
                    'accepted_at' => $participant->pivot->accepted_at,
                ];
            })
            ->toArray();
    }

    public function getQuestStatistics(): array
    {
        $participants = $this->participants;
        $completed = $participants->wherePivot('completed', true);
        $inProgress = $participants->wherePivot('completed', false);

        return [
            'total_participants' => $participants->count(),
            'completed_count' => $completed->count(),
            'in_progress_count' => $inProgress->count(),
            'abandoned_count' => $inProgress->wherePivot('status', 'abandoned')->count(),
            'completion_rate' => $this->getCompletionRate(),
            'average_progress' => $participants->avg('pivot.progress'),
            'days_remaining' => $this->getDaysRemaining(),
            'is_active' => $this->isActive(),
            'is_full' => $this->isFull(),
        ];
    }

    public function getQuestProgressForUser($userId): array
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        
        if (!$participant) {
            return [
                'participating' => false,
                'progress' => 0,
                'objectives_completed' => [],
                'completed' => false,
            ];
        }

        return [
            'participating' => true,
            'progress' => $participant->pivot->progress,
            'objectives_completed' => $participant->pivot->objectives_completed ?? [],
            'completed' => $participant->pivot->completed,
            'accepted_at' => $participant->pivot->accepted_at,
            'completed_at' => $participant->pivot->completed_at,
            'status' => $participant->pivot->status,
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
        
        // Add bonus for experience reward
        if ($this->experience_reward > 0) {
            $baseScore += $this->experience_reward / 10;
        }
        
        // Add bonus for badge rewards
        if ($this->badge_reward) {
            $baseScore += 50; // Badge bonus
        }
        
        // Add bonus for rewards
        if (!empty($this->rewards)) {
            $baseScore += count($this->rewards) * 25;
        }
        
        return $baseScore;
    }

    public function getQuestScore(): int
    {
        $difficultyScore = $this->calculateDifficultyScore();
        $rewardScore = $this->calculateRewardScore();
        
        return ($difficultyScore * 10) + $rewardScore;
    }

    public function getRecommendedLevel(): int
    {
        $score = $this->getQuestScore();
        
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
        $newQuest = $this->replicate();
        $newQuest->title = $this->title . ' (نسخة)';
        $newQuest->status = 'draft';
        $newQuest->created_at = now();
        $newQuest->updated_at = now();
        $newQuest->save();
        
        return $newQuest;
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

    public static function getActiveQuests(): array
    {
        return self::active()
            ->withCount('participants')
            ->orderBy('end_date')
            ->get()
            ->map(function ($quest) {
                return [
                    'id' => $quest->id,
                    'title' => $quest->title,
                    'type' => $quest->type,
                    'difficulty' => $quest->difficulty,
                    'participants_count' => $quest->participants_count,
                    'days_remaining' => $quest->getDaysRemaining(),
                    'points_reward' => $quest->points_reward,
                    'is_full' => $quest->isFull(),
                ];
            })
            ->toArray();
    }

    public static function getUpcomingQuests(): array
    {
        return self::upcoming()
            ->withCount('participants')
            ->orderBy('start_date')
            ->take(5)
            ->get()
            ->map(function ($quest) {
                return [
                    'id' => $quest->id,
                    'title' => $quest->title,
                    'type' => $quest->type,
                    'difficulty' => $quest->difficulty,
                    'max_participants' => $quest->max_participants,
                    'days_until_start' => $quest->getDaysUntilStart(),
                    'points_reward' => $quest->points_reward,
                ];
            })
            ->toArray();
    }

    public static function getQuestAnalytics($period = 'month'): array
    {
        $dateRange = self::getDateRange($period);
        
        return [
            'total_quests' => self::whereBetween('created_at', $dateRange)->count(),
            'active_quests' => self::active()->count(),
            'completed_quests' => self::where('status', 'completed')->count(),
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
            $quests = self::where('difficulty', $difficulty)->get();
            $totalQuests = $quests->count();
            
            if ($totalQuests === 0) {
                $rates[$difficulty] = 0;
                continue;
            }
            
            $totalParticipants = 0;
            $completedParticipants = 0;
            
            foreach ($quests as $quest) {
                $participants = $quest->participants;
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
