<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyLeaderboard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'period',
        'category',
        'score',
        'rank',
        'previous_rank',
        'change',
        'metadata',
        'calculated_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'rank' => 'integer',
        'previous_rank' => 'integer',
        'change' => 'integer',
        'metadata' => 'array',
        'calculated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'calculated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
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

    public function scopeDaily($query)
    {
        return $query->where('period', 'daily');
    }

    public function scopeWeekly($query)
    {
        return $query->where('period', 'weekly');
    }

    public function scopeMonthly($query)
    {
        return $query->where('period', 'monthly');
    }

    public function scopeQuarterly($query)
    {
        return $query->where('period', 'quarterly');
    }

    public function scopeYearly($query)
    {
        return $query->where('period', 'yearly');
    }

    public function scopeAllTime($query)
    {
        return $query->where('period', 'all_time');
    }

    public function scopeTopRanked($query, $limit = 10)
    {
        return $query->orderBy('rank')->take($limit);
    }

    public function scopeRecentlyCalculated($query, $hours = 24)
    {
        return $query->where('calculated_at', '>=', now()->subHours($hours));
    }

    // Methods
    public function getRankChange(): int
    {
        if ($this->previous_rank === null) {
            return 0;
        }

        return $this->previous_rank - $this->rank;
    }

    public function getRankChangeLabel(): string
    {
        $change = $this->getRankChange();
        
        if ($change > 0) {
            return '↑ ' . abs($change);
        } elseif ($change < 0) {
            return '↓ ' . abs($change);
        } else {
            return '—';
        }
    }

    public function getRankChangeColor(): string
    {
        $change = $this->getRankChange();
        
        if ($change > 0) {
            return '#28a745'; // Green
        } elseif ($change < 0) {
            return '#dc3545'; // Red
        } else {
            return '#6c757d'; // Gray
        }
    }

    public function getTypeLabel(): string
    {
        $labels = [
            'points' => 'النقاط',
            'level' => 'المستوى',
            'badges' => 'الشارات',
            'challenges' => 'التحديات',
            'quests' => 'المهام',
        ];
        
        return $labels[$this->type] ?? $this->type;
    }

    public function getPeriodLabel(): string
    {
        $labels = [
            'daily' => 'يومي',
            'weekly' => 'أسبوعي',
            'monthly' => 'شهري',
            'quarterly' => 'ربع سنوي',
            'yearly' => 'سنوي',
            'all_time' => 'كل الأوقات',
        ];
        
        return $labels[$this->period] ?? $this->period;
    }

    public function getFormattedScore(): string
    {
        return number_format($this->score);
    }

    public function getRankWithSuffix(): string
    {
        $rank = $this->rank;
        
        if ($rank >= 11 && $rank <= 13) {
            return $rank . 'th';
        } elseif ($rank % 10 == 1) {
            return $rank . 'st';
        } elseif ($rank % 10 == 2) {
            return $rank . 'nd';
        } elseif ($rank % 10 == 3) {
            return $rank . 'rd';
        } else {
            return $rank . 'th';
        }
    }

    public function isInTop($limit): bool
    {
        return $this->rank <= $limit;
    }

    public function getMedal(): ?string
    {
        if ($this->rank === 1) {
            return '🥇';
        } elseif ($this->rank === 2) {
            return '🥈';
        } elseif ($this->rank === 3) {
            return '🥉';
        } elseif ($this->isInTop(10)) {
            return '⭐';
        } elseif ($this->isInTop(100)) {
            return '🏆';
        }
        
        return null;
    }

    public function getPerformanceLevel(): string
    {
        if ($this->isInTop(1)) {
            return 'الأول';
        } elseif ($this->isInTop(3)) {
            return 'الثلاثة الأوائل';
        } elseif ($this->isInTop(10)) {
            return 'أفضل 10';
        } elseif ($this->isInTop(25)) {
            return 'أفضل 25';
        } elseif ($this->isInTop(100)) {
            return 'أفضل 100';
        } else {
            return 'خارج القائمة';
        }
    }

    public function updateRank($newRank): void
    {
        $this->previous_rank = $this->rank;
        $this->rank = $newRank;
        $this->change = $this->getRankChange();
        $this->calculated_at = now();
        $this->save();
    }

    public function recalculateScore($newScore): void
    {
        $this->score = $newScore;
        $this->calculated_at = now();
        $this->save();
    }

    public function getScoreDifference($otherLeaderboard): int
    {
        return $this->score - $otherLeaderboard->score;
    }

    public function getRankDifference($otherLeaderboard): int
    {
        return $otherLeaderboard->rank - $this->rank;
    }

    public function isAbove($otherLeaderboard): bool
    {
        return $this->rank < $otherLeaderboard->rank;
    }

    public function isBelow($otherLeaderboard): bool
    {
        return $this->rank > $otherLeaderboard->rank;
    }

    public function getLeaderboardPosition($userId, $type, $period, $category = 'general'): ?int
    {
        return self::where('user_id', $userId)
            ->where('type', $type)
            ->where('period', $period)
            ->where('category', $category)
            ->orderBy('rank')
            ->first()
            ?->rank;
    }

    public function getUserRankings($userId): array
    {
        return self::where('user_id', $userId)
            ->orderBy('period', 'desc')
            ->orderBy('type')
            ->get()
            ->groupBy('type')
            ->map(function ($rankings) {
                return $rankings->sortByDesc('period')->first();
            })
            ->toArray();
    }

    public function getLeaderboardSummary($type, $period, $category = 'general'): array
    {
        $leaderboard = self::where('type', $type)
            ->where('period', $period)
            ->where('category', $category)
            ->orderBy('rank')
            ->get();

        return [
            'total_participants' => $leaderboard->count(),
            'top_10' => $leaderboard->take(10),
            'average_score' => $leaderboard->avg('score'),
            'median_score' => $this->calculateMedian($leaderboard->pluck('score')->toArray()),
            'highest_score' => $leaderboard->max('score'),
            'lowest_score' => $leaderboard->min('score'),
            'score_distribution' => $this->getScoreDistribution($leaderboard),
            'rank_distribution' => $this->getRankDistribution($leaderboard),
            'calculated_at' => $leaderboard->max('calculated_at'),
        ];
    }

    private function calculateMedian(array $scores): float
    {
        sort($scores);
        $count = count($scores);
        
        if ($count === 0) {
            return 0;
        }
        
        $middle = floor($count / 2);
        
        if ($count % 2 === 0) {
            return ($scores[$middle - 1] + $scores[$middle]) / 2;
        } else {
            return $scores[$middle];
        }
    }

    private function getScoreDistribution($leaderboard): array
    {
        $scores = $leaderboard->pluck('score')->toArray();
        $maxScore = max($scores);
        $minScore = min($scores);
        $range = $maxScore - $minScore;
        
        if ($range === 0) {
            return ['all' => count($scores)];
        }
        
        $bins = 5;
        $binSize = $range / $bins;
        $distribution = [];
        
        for ($i = 0; $i < $bins; $i++) {
            $min = $minScore + ($i * $binSize);
            $max = ($i === $bins - 1) ? $maxScore : $minScore + (($i + 1) * $binSize);
            
            $count = 0;
            foreach ($scores as $score) {
                if ($score >= $min && $score < $max) {
                    $count++;
                } elseif ($i === $bins - 1 && $score === $maxScore) {
                    $count++;
                }
            }
            
            $distribution[] = [
                'range' => round($min) . ' - ' . round($max),
                'count' => $count,
                'percentage' => ($count / count($scores)) * 100,
            ];
        }
        
        return $distribution;
    }

    private function getRankDistribution($leaderboard): array
    {
        $ranks = $leaderboard->pluck('rank')->toArray();
        $total = count($ranks);
        
        $distribution = [
            'top_1_percent' => 0,
            'top_5_percent' => 0,
            'top_10_percent' => 0,
            'top_25_percent' => 0,
            'top_50_percent' => 0,
        ];
        
        foreach ($ranks as $rank) {
            if ($rank <= ceil($total * 0.01)) {
                $distribution['top_1_percent']++;
            }
            if ($rank <= ceil($total * 0.05)) {
                $distribution['top_5_percent']++;
            }
            if ($rank <= ceil($total * 0.10)) {
                $distribution['top_10_percent']++;
            }
            if ($rank <= ceil($total * 0.25)) {
                $distribution['top_25_percent']++;
            }
            if ($rank <= ceil($total * 0.50)) {
                $distribution['top_50_percent']++;
            }
        }
        
        // Convert to percentages
        foreach ($distribution as $key => $value) {
            $distribution[$key] = ($value / $total) * 100;
        }
        
        return $distribution;
    }

    public static function generateLeaderboard($type, $period, $category = 'general'): void
    {
        // Clear existing leaderboard
        self::where('type', $type)
            ->where('period', $period)
            ->where('category', $category)
            ->delete();

        // Get data based on type
        $data = self::getDataByType($type, $period);
        
        // Rank users
        $rank = 1;
        foreach ($data as $userData) {
            self::create([
                'user_id' => $userData['user_id'],
                'type' => $type,
                'period' => $period,
                'category' => $category,
                'score' => $userData['score'],
                'rank' => $rank,
                'previous_rank' => self::getPreviousRank($userData['user_id'], $type, $period, $category),
                'change' => 0, // Will be calculated after all ranks are set
                'calculated_at' => now(),
            ]);
            
            $rank++;
        }
        
        // Update rank changes
        self::updateRankChanges($type, $period, $category);
    }

    private static function getDataByType($type, $period): array
    {
        $dateRange = self::getDateRangeForPeriod($period);
        
        switch ($type) {
            case 'points':
                return self::getPointsData($dateRange);
            case 'level':
                return self::getLevelData();
            case 'badges':
                return self::getBadgesData();
            case 'challenges':
                return self::getChallengesData();
            case 'quests':
                return self::getQuestsData();
            default:
                return [];
        }
    }

    private static function getPointsData($dateRange): array
    {
        return \DB::table('property_points')
            ->selectRaw('user_id, SUM(points) as score')
            ->whereBetween('created_at', $dateRange)
            ->groupBy('user_id')
            ->orderBy('score', 'desc')
            ->get()
            ->toArray();
    }

    private static function getLevelData(): array
    {
        return \DB::table('user_property_gamifications')
            ->selectRaw('user_id, current_level as score')
            ->orderBy('current_level', 'desc')
            ->get()
            ->toArray();
    }

    private static function getBadgesData(): array
    {
        return \DB::table('badge_user')
            ->selectRaw('user_id, COUNT(*) as score')
            ->groupBy('user_id')
            ->orderBy('score', 'desc')
            ->get()
            ->toArray();
    }

    private static function getChallengesData(): array
    {
        return \DB::table('user_property_gamifications')
            ->selectRaw('user_id, challenges_completed as score')
            ->orderBy('challenges_completed', 'desc')
            ->get()
            ->toArray();
    }

    private static function getQuestsData(): array
    {
        return \DB::table('user_property_gamifications')
            ->selectRaw('user_id, quests_completed as score')
            ->orderBy('quests_completed', 'desc')
            ->get()
            ->toArray();
    }

    private static function getDateRangeForPeriod($period): array
    {
        $now = now();
        
        switch ($period) {
            case 'daily':
                return [$now->startOfDay(), $now->endOfDay()];
            case 'weekly':
                return [$now->startOfWeek(), $now->endOfWeek()];
            case 'monthly':
                return [$now->startOfMonth(), $now->endOfMonth()];
            case 'quarterly':
                return [$now->startOfQuarter(), $now->endOfQuarter()];
            case 'yearly':
                return [$now->startOfYear(), $now->endOfYear()];
            case 'all_time':
                return ['2020-01-01', $now];
            default:
                return [$now->startOfMonth(), $now->endOfMonth()];
        }
    }

    private static function getPreviousRank($userId, $type, $period, $category): ?int
    {
        $previousPeriod = self::getPreviousPeriod($period);
        
        return self::where('user_id', $userId)
            ->where('type', $type)
            ->where('period', $previousPeriod)
            ->where('category', $category)
            ->first()
            ?->rank;
    }

    private static function getPreviousPeriod($period): string
    {
        switch ($period) {
            case 'daily':
                return 'daily';
            case 'weekly':
                return 'weekly';
            case 'monthly':
                return 'monthly';
            case 'quarterly':
                return 'quarterly';
            case 'yearly':
                return 'yearly';
            case 'all_time':
                return 'all_time';
            default:
                return 'monthly';
        }
    }

    private static function updateRankChanges($type, $period, $category): void
    {
        $leaderboards = self::where('type', $type)
            ->where('period', $period)
            ->where('category', $category)
            ->get();

        foreach ($leaderboards as $leaderboard) {
            $change = $leaderboard->previous_rank ? 
                $leaderboard->previous_rank - $leaderboard->rank : 0;
            
            $leaderboard->update(['change' => $change]);
        }
    }

    public static function getLeaderboardAnalytics($type, $period): array
    {
        $leaderboard = self::where('type', $type)
            ->where('period', $period)
            ->orderBy('rank')
            ->get();

        return [
            'total_participants' => $leaderboard->count(),
            'new_participants' => $leaderboard->where('created_at', '>=', now()->subDays(7))->count(),
            'average_rank_change' => $leaderboard->avg('change'),
            'improved_ranks' => $leaderboard->where('change', '>', 0)->count(),
            'declined_ranks' => $leaderboard->where('change', '<', 0)->count(),
            'stable_ranks' => $leaderboard->where('change', 0)->count(),
            'top_movers' => $leaderboard->sortByDesc('change')->take(5),
            'top_losers' => $leaderboard->sortBy('change')->take(5),
        ];
    }
}
