<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PropertyPoints extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'property_id',
        'points',
        'type',
        'reason',
        'description',
        'awarded_at',
        'expires_at',
        'metadata',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'points' => 'integer',
        'metadata' => 'array',
        'awarded_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'awarded_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeEarned($query)
    {
        return $query->where('type', 'earned');
    }

    public function scopeBonus($query)
    {
        return $query->where('type', 'bonus');
    }

    public function scopePenalty($query)
    {
        return $query->where('type', 'penalty');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeExpiring($query, $days = 7)
    {
        return $query->where('expires_at', '<=', now()->addDays($days))
            ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
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

    public function getRemainingDays(): int
    {
        if (!$this->expires_at) {
            return -1; // No expiry
        }
        
        return now()->diffInDays($this->expires_at, false);
    }

    public function getValue(): int
    {
        // Calculate point value based on type and age
        $baseValue = $this->points;
        
        if ($this->type === 'bonus') {
            $baseValue *= 1.5;
        } elseif ($this->type === 'penalty') {
            $baseValue *= 0.5;
        }
        
        // Apply time decay if expired
        if ($this->isExpired()) {
            $daysExpired = now()->diffInDays($this->expires_at);
            $decayFactor = max(0.1, 1 - ($daysExpired * 0.01));
            $baseValue *= $decayFactor;
        }
        
        return (int) $baseValue;
    }

    public function getFormattedPoints(): string
    {
        return number_format($this->points) . ' نقطة';
    }

    public function getTypeLabel(): string
    {
        $labels = [
            'earned' => 'مكتسبة',
            'bonus' => 'مكافأة',
            'penalty' => 'خصم',
        ];
        
        return $labels[$this->type] ?? $this->type;
    }

    public function getStatusLabel(): string
    {
        if ($this->isExpired()) {
            return 'منتهية';
        } elseif ($this->isExpiringSoon()) {
            return 'تنتهي قريباً';
        } else {
            return 'نشطة';
        }
    }

    public function canBeUsed(): bool
    {
        return !$this->isExpired() && $this->points > 0;
    }

    public function applyDecay($decayRate = 0.01): void
    {
        if ($this->points > 0 && !$this->isExpired()) {
            $this->points = max(0, $this->points - ($this->points * $decayRate));
            $this->save();
        }
    }

    public function extendExpiry($days): void
    {
        if ($this->expires_at) {
            $this->expires_at = $this->expires_at->addDays($days);
            $this->save();
        }
    }

    public function transferTo($targetUserId, $amount = null): bool
    {
        $transferAmount = $amount ?? $this->points;
        
        if ($transferAmount <= 0 || $this->points < $transferAmount) {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        // Create new points record for target user
        self::create([
            'user_id' => $targetUserId,
            'points' => $transferAmount,
            'type' => 'earned',
            'reason' => "تحويل من المستخدم {$this->user_id}",
            'awarded_at' => now(),
        ]);

        // Update current record
        $this->points -= $transferAmount;
        $this->save();

        return true;
    }

    public function getTransactionHistory($userId, $limit = 50): array
    {
        return self::where('user_id', $userId)
            ->with(['user', 'property'])
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($point) {
                return [
                    'id' => $point->id,
                    'points' => $point->points,
                    'type' => $point->type,
                    'type_label' => $point->getTypeLabel(),
                    'reason' => $point->reason,
                    'description' => $point->description,
                    'awarded_at' => $point->awarded_at->format('Y-m-d H:i:s'),
                    'expires_at' => $point->expires_at ? $point->expires_at->format('Y-m-d H:i:s') : null,
                    'status_label' => $point->getStatusLabel(),
                    'property_title' => $point->property ? $point->property->title : null,
                    'value' => $point->getValue(),
                    'formatted_points' => $point->getFormattedPoints(),
                ];
            })
            ->toArray();
    }

    public function getPointsSummary($userId, $period = 'month'): array
    {
        $dateRange = $this->getDateRange($period);
        
        $points = self::where('user_id', $userId)
            ->whereBetween('created_at', $dateRange)
            ->get();

        return [
            'total_earned' => $points->where('type', 'earned')->sum('points'),
            'total_bonus' => $points->where('type', 'bonus')->sum('points'),
            'total_penalty' => $points->where('type', 'penalty')->sum('points'),
            'net_points' => $points->sum('points'),
            'total_transactions' => $points->count(),
            'average_per_transaction' => $points->avg('points'),
            'highest_transaction' => $points->max('points'),
            'lowest_transaction' => $points->min('points'),
            'expired_points' => $points->filter(function ($p) {
                return $p->isExpired();
            })->sum('points'),
            'expiring_soon' => $points->filter(function ($p) {
                return $p->isExpiringSoon();
            })->sum('points'),
        ];
    }

    public function getPointsAnalytics($period = 'month'): array
    {
        $dateRange = $this->getDateRange($period);
        
        return [
            'daily_trends' => $this->getDailyTrends($dateRange),
            'type_distribution' => $this->getTypeDistribution($dateRange),
            'top_earners' => $this->getTopEarners($dateRange),
            'usage_patterns' => $this->getUsagePatterns($dateRange),
            'expiry_analysis' => $this->getExpiryAnalysis($dateRange),
        ];
    }

    private function getDateRange($period): array
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

    private function getDailyTrends($dateRange): array
    {
        return self::whereBetween('created_at', $dateRange)
            ->selectRaw('DATE(created_at) as date, SUM(points) as total_points, COUNT(*) as transactions')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getTypeDistribution($dateRange): array
    {
        return self::whereBetween('created_at', $dateRange)
            ->selectRaw('type, SUM(points) as total_points, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->toArray();
    }

    private function getTopEarners($dateRange): array
    {
        return self::whereBetween('created_at', $dateRange)
            ->where('points', '>', 0)
            ->selectRaw('user_id, SUM(points) as total_points, COUNT(*) as transactions')
            ->groupBy('user_id')
            ->orderBy('total_points', 'desc')
            ->take(10)
            ->with('user')
            ->get()
            ->map(function ($user) {
                return [
                    'user_id' => $user->user_id,
                    'user_name' => $user->user->name,
                    'total_points' => $user->total_points,
                    'transactions' => $user->transactions,
                ];
            })
            ->toArray();
    }

    private function getUsagePatterns($dateRange): array
    {
        return self::whereBetween('created_at', $dateRange)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get()
            ->toArray();
    }

    private function getExpiryAnalysis($dateRange): array
    {
        $totalPoints = self::whereBetween('created_at', $dateRange)
            ->whereNotNull('expires_at')
            ->sum('points');

        $expiredPoints = self::whereBetween('created_at', $dateRange)
            ->where('expires_at', '<', now())
            ->sum('points');

        $expiringSoonPoints = self::whereBetween('created_at', $dateRange)
            ->where('expires_at', '<=', now()->addDays(7))
            ->where('expires_at', '>', now())
            ->sum('points');

        return [
            'total_points_with_expiry' => $totalPoints,
            'expired_points' => $expiredPoints,
            'expiring_soon_points' => $expiringSoonPoints,
            'expiry_rate' => $totalPoints > 0 ? ($expiredPoints / $totalPoints) * 100 : 0,
        ];
    }

    public static function getUserTotalPoints($userId): int
    {
        return self::where('user_id', $userId)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->sum('points');
    }

    public static function getUserAvailablePoints($userId): int
    {
        return self::where('user_id', $userId)
            ->where('points', '>', 0)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->sum('points');
    }

    public static function cleanupExpiredPoints(): int
    {
        $expiredPoints = self::where('expires_at', '<', now())->get();
        $totalExpired = $expiredPoints->sum('points');
        
        // Archive expired points before deletion
        foreach ($expiredPoints as $point) {
            \DB::table('expired_points_archive')->insert([
                'user_id' => $point->user_id,
                'property_id' => $point->property_id,
                'points' => $point->points,
                'type' => $point->type,
                'reason' => $point->reason,
                'original_awarded_at' => $point->awarded_at,
                'original_expires_at' => $point->expires_at,
                'archived_at' => now(),
            ]);
        }
        
        // Delete expired points
        self::where('expires_at', '<', now())->delete();
        
        return $totalExpired;
    }

    public static function generatePointsReport($filters = []): array
    {
        $query = self::query();

        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['date_range'])) {
            $dateRange = self::getDateRange($filters['date_range']);
            $query->whereBetween('created_at', $dateRange);
        }

        $points = $query->with(['user', 'property'])->get();

        return [
            'total_points' => $points->sum('points'),
            'total_transactions' => $points->count(),
            'average_points' => $points->avg('points'),
            'points_by_type' => $points->groupBy('type')->map->sum(),
            'points_by_user' => $points->groupBy('user_id')->map->sum(),
            'top_earners' => $points->groupBy('user_id')
                ->map->sum()
                ->sortDesc()
                ->take(10),
            'expiry_summary' => [
                'expiring_soon' => $points->filter(function ($p) {
                    return $p->isExpiringSoon();
                })->sum('points'),
                'expired' => $points->filter(function ($p) {
                    return $p->isExpired();
                })->sum('points'),
            ],
            'generated_at' => now(),
        ];
    }
}
