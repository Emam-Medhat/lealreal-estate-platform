<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyReward extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'category',
        'points_cost',
        'value',
        'status',
        'available',
        'stock_quantity',
        'max_redemptions_per_user',
        'expiry_date',
        'image',
        'terms_conditions',
        'redemption_instructions',
        'metadata',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'points_cost' => 'integer',
        'value' => 'decimal:2',
        'stock_quantity' => 'integer',
        'max_redemptions_per_user' => 'integer',
        'available' => 'boolean',
        'metadata' => 'array',
        'expiry_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'expiry_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function redemptions(): HasMany
    {
        return $this->hasMany(\App\Models\UserReward::class, 'reward_id');
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

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAvailable($query)
    {
        return $query->where('available', true)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>', now());
            });
    }

    public function scopeUnavailable($query)
    {
        return $query->where('available', false)
            ->orWhere('status', '!=', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
            ->where('expiry_date', '>', now());
    }

    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('stock_quantity')
              ->orWhere('stock_quantity', '>', 0);
        });
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    public function scopePoints($query)
    {
        return $query->where('type', 'points');
    }

    public function scopeBadge($query)
    {
        return $query->where('type', 'badge');
    }

    public function scopeDiscount($query)
    {
        return $query->where('type', 'discount');
    }

    public function scopeProduct($query)
    {
        return $query->where('type', 'product');
    }

    public function scopeService($query)
    {
        return $query->where('type', 'service');
    }

    public function scopeCustom($query)
    {
        return $query->where('type', 'custom');
    }

    public function scopeLowCost($query, $max = 100)
    {
        return $query->where('points_cost', '<=', $max);
    }

    public function scopeMediumCost($query, $min = 100, $max = 500)
    {
        return $query->where('points_cost', '>', $min)
            ->where('points_cost', '<=', $max);
    }

    public function scopeHighCost($query, $min = 500)
    {
        return $query->where('points_cost', '>', $min);
    }

    // Methods
    public function isAvailable(): bool
    {
        return $this->available && 
               $this->status === 'active' && 
               (!$this->expiry_date || $this->expiry_date->greaterThan(now())) &&
               (!$this->stock_quantity || $this->stock_quantity > 0);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon($days = 7): bool
    {
        return $this->expiry_date && 
               $this->expiry_date->greaterThan(now()) && 
               $this->expiry_date->lessThanOrEqualTo(now()->addDays($days));
    }

    public function isInStock(): bool
    {
        return !$this->stock_quantity || $this->stock_quantity > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity && $this->stock_quantity <= 0;
    }

    public function getDaysUntilExpiry(): int
    {
        if (!$this->expiry_date) {
            return -1; // No expiry
        }
        
        return now()->diffInDays($this->expiry_date, false);
    }

    public function getStockRemaining(): int
    {
        return $this->stock_quantity ?? -1; // -1 means unlimited
    }

    public function getTypeLabel(): string
    {
        $labels = [
            'points' => 'نقاط',
            'badge' => 'شارة',
            'discount' => 'خصم',
            'product' => 'منتج',
            'service' => 'خدمة',
            'custom' => 'مخصص',
        ];
        
        return $labels[$this->type] ?? $this->type;
    }

    public function getStatusLabel(): string
    {
        $labels = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'limited' => 'محدود',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColor(): string
    {
        $colors = [
            'active' => '#28a745', // Green
            'inactive' => '#dc3545', // Red
            'limited' => '#ffc107', // Yellow
        ];
        
        return $colors[$this->status] ?? '#6c757d';
    }

    public function getFormattedPointsCost(): string
    {
        return number_format($this->points_cost) . ' نقطة';
    }

    public function getFormattedValue(): string
    {
        if ($this->value) {
            return number_format($this->value, 2);
        }
        
        return '0.00';
    }

    public function canBeRedeemedBy($user): bool
    {
        if (!$user) {
            return false;
        }

        if (!$this->isAvailable()) {
            return false;
        }

        // Check user's points
        $userPoints = \App\Models\PropertyPoints::getUserTotalPoints($user->id);
        if ($userPoints < $this->points_cost) {
            return false;
        }

        // Check max redemptions per user
        if ($this->max_redemptions_per_user) {
            $userRedemptions = $this->redemptions()
                ->where('user_id', $user->id)
                ->count();
            
            if ($userRedemptions >= $this->max_redemptions_per_user) {
                return false;
            }
        }

        return true;
    }

    public function redeem($userId, $notes = null): bool
    {
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return false;
        }

        if (!$this->canBeRedeemedBy($user)) {
            return false;
        }

        // Check stock
        if ($this->stock_quantity && $this->stock_quantity <= 0) {
            return false;
        }

        // Create redemption record
        $redemption = \App\Models\UserReward::create([
            'user_id' => $userId,
            'reward_id' => $this->id,
            'points_cost' => $this->points_cost,
            'status' => 'pending',
            'notes' => $notes,
            'redeemed_at' => now(),
        ]);

        // Deduct points from user
        \App\Models\PropertyPoints::create([
            'user_id' => $userId,
            'points' => $this->points_cost,
            'type' => 'penalty',
            'reason' => "استبدال مكافأة: {$this->name}",
            'awarded_at' => now(),
        ]);

        // Update stock if applicable
        if ($this->stock_quantity) {
            $this->stock_quantity--;
            $this->save();
        }

        return true;
    }

    public function getRedemptionCount(): int
    {
        return $this->redemptions()->count();
    }

    public function getUniqueRedemptions(): int
    {
        return $this->redemptions()->distinct('user_id')->count('user_id');
    }

    public function getTotalPointsSpent(): int
    {
        return $this->redemptions()->sum('points_cost');
    }

    public function getRedemptionStatistics(): array
    {
        $redemptions = $this->redemptions;
        
        return [
            'total_redemptions' => $redemptions->count(),
            'unique_users' => $redemptions->distinct('user_id')->count('user_id'),
            'total_points_spent' => $redemptions->sum('points_cost'),
            'pending_redemptions' => $redemptions->where('status', 'pending')->count(),
            'processing_redemptions' => $redemptions->where('status', 'processing')->count(),
            'completed_redemptions' => $redemptions->where('status', 'completed')->count(),
            'cancelled_redemptions' => $redemptions->where('status', 'cancelled')->count(),
            'average_points_per_redemption' => $redemptions->avg('points_cost'),
            'most_recent_redemption' => $redemptions->orderBy('redeemed_at', 'desc')->first(),
        ];
    }

    public function getUserRedemptions($userId): array
    {
        return $this->redemptions()
            ->where('user_id', $userId)
            ->orderBy('redeemed_at', 'desc')
            ->get()
            ->map(function ($redemption) {
                return [
                    'id' => $redemption->id,
                    'points_cost' => $redemption->points_cost,
                    'status' => $redemption->status,
                    'status_label' => $redemption->getStatusLabel(),
                    'redeemed_at' => $redemption->redeemed_at->format('Y-m-d H:i:s'),
                    'notes' => $redemption->notes,
                ];
            })
            ->toArray();
    }

    public function getUserRedemptionCount($userId): int
    {
        return $this->redemptions()->where('user_id', $userId)->count();
    }

    public function canUserRedeem($userId): bool
    {
        $userRedemptions = $this->getUserRedemptionCount($userId);
        
        if ($this->max_redemptions_per_user && 
            $userRedemptions >= $this->max_redemptions_per_user) {
            return false;
        }

        return true;
    }

    public function getRemainingRedemptionsForUser($userId): int
    {
        if (!$this->max_redemptions_per_user) {
            return -1; // Unlimited
        }

        $userRedemptions = $this->getUserRedemptionCount($userId);
        return max(0, $this->max_redemptions_per_user - $userRedemptions);
    }

    public function duplicate(): self
    {
        $newReward = $this->replicate();
        $newReward->name = $this->name . ' (نسخة)';
        $newReward->status = 'draft';
        $newReward->created_at = now();
        $newReward->updated_at = now();
        $newReward->save();
        
        return $newReward;
    }

    public function activate(): void
    {
        $this->status = 'active';
        $this->available = true;
        $this->save();
    }

    public function deactivate(): void
    {
        $this->status = 'inactive';
        $this->available = false;
        $this->save();
    }

    public function addToStock($quantity): void
    {
        if ($this->stock_quantity !== null) {
            $this->stock_quantity += $quantity;
            $this->save();
        }
    }

    public function removeFromStock($quantity): void
    {
        if ($this->stock_quantity !== null) {
            $this->stock_quantity = max(0, $this->stock_quantity - $quantity);
            $this->save();
        }
    }

    public function setStock($quantity): void
    {
        $this->stock_quantity = $quantity;
        $this->save();
    }

    public function extendExpiry($days): void
    {
        if ($this->expiry_date) {
            $this->expiry_date = $this->expiry_date->addDays($days);
            $this->save();
        }
    }

    public function getFormattedStock(): string
    {
        if ($this->stock_quantity === null) {
            return 'غير محدود';
        }
        
        return number_format($this->stock_quantity);
    }

    public function getValuePerPoint(): float
    {
        if ($this->points_cost > 0 && $this->value > 0) {
            return $this->value / $this->points_cost;
        }
        
        return 0;
    }

    public function getFormattedValuePerPoint(): string
    {
        return number_format($this->getValuePerPoint(), 4);
    }

    public static function getAvailableRewards($userId = null): array
    {
        $query = self::available()->withCount('redemptions');
        
        if ($userId) {
            $query->with(['redemptions' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }]);
        }
        
        return $query->get()
            ->map(function ($reward) use ($userId) {
                $reward->can_afford = true;
                $reward->can_redeem = true;
                $reward->user_redemptions_count = 0;
                $reward->remaining_redemptions = -1;
                
                if ($userId) {
                    $userPoints = \App\Models\PropertyPoints::getUserTotalPoints($userId);
                    $reward->can_afford = $userPoints >= $reward->points_cost;
                    
                    $userRedemptions = $reward->redemptions->count();
                    $reward->user_redemptions_count = $userRedemptions;
                    
                    if ($reward->max_redemptions_per_user) {
                        $reward->can_redeem = $userRedemptions < $reward->max_redemptions_per_user;
                        $reward->remaining_redemptions = max(0, $reward->max_redemptions_per_user - $userRedemptions);
                    }
                    
                    $reward->points_shortage = max(0, $reward->points_cost - $userPoints);
                }
                
                return $reward;
            })
            ->toArray();
    }

    public static function getPopularRewards($limit = 10): array
    {
        return self::withCount('redemptions')
            ->orderBy('redemptions_count', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($reward) {
                return [
                    'id' => $reward->id,
                    'name' => $reward->name,
                    'type' => $reward->type,
                    'category' => $reward->category,
                    'points_cost' => $reward->points_cost,
                    'redemptions_count' => $reward->redemptions_count,
                    'total_points_spent' => $reward->redemptions_count * $reward->points_cost,
                    'is_available' => $reward->isAvailable(),
                ];
            })
            ->toArray();
    }

    public static function getRewardAnalytics($period = 'month'): array
    {
        $dateRange = self::getDateRange($period);
        
        return [
            'total_rewards' => self::whereBetween('created_at', $dateRange)->count(),
            'active_rewards' => self::available()->count(),
            'expired_rewards' => self::expired()->count(),
            'total_redemptions' => \App\Models\UserReward::whereBetween('created_at', $dateRange)->count(),
            'total_points_spent' => \App\Models\UserReward::whereBetween('created_at', $dateRange)->sum('points_cost'),
            'unique_users' => \App\Models\UserReward::whereBetween('created_at', $dateRange)->distinct('user_id')->count('user_id'),
            'rewards_by_type' => self::getRewardsByType($dateRange),
            'rewards_by_category' => self::getRewardsByCategory($dateRange),
            'top_redeemed_rewards' => self::getTopRedeemedRewards($dateRange),
        ];
    }

    private static function getRewardsByType($dateRange): array
    {
        return self::whereBetween('created_at', $dateRange)
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    private static function getRewardsByCategory($dateRange): array
    {
        return self::whereBetween('created_at', $dateRange)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    private static function getTopRedeemedRewards($dateRange): array
    {
        return \App\Models\UserReward::selectRaw('reward_id, COUNT(*) as redemption_count')
            ->whereBetween('created_at', $dateRange)
            ->groupBy('reward_id')
            ->orderBy('redemption_count', 'desc')
            ->take(10)
            ->with('reward')
            ->get()
            ->map(function ($item) {
                return [
                    'reward_id' => $item->reward_id,
                    'reward_name' => $item->reward->name,
                    'redemption_count' => $item->redemption_count,
                    'points_cost' => $item->reward->points_cost,
                    'total_points_spent' => $item->redemption_count * $item->reward->points_cost,
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
