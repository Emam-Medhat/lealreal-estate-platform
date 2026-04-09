<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class StakingPool extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'contract_address',
        'token_address',
        'reward_token_address',
        'total_staked',
        'total_rewards',
        'apr',
        'apy',
        'lock_period',
        'minimum_stake',
        'maximum_stake',
        'reward_rate',
        'reward_period',
        'compounding_enabled',
        'early_withdrawal_penalty',
        'total_stakers',
        'active_stakers',
        'pool_status',
        'start_time',
        'end_time',
        'last_reward_timestamp',
        'total_deposits',
        'total_withdrawals',
        'average_stake_amount',
        'total_reward_paid',
        'pending_rewards',
        'is_verified',
        'verification_status',
        'tags',
        'metadata',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'total_staked' => 'decimal:18',
        'total_rewards' => 'decimal:18',
        'apr' => 'decimal:8',
        'apy' => 'decimal:8',
        'reward_rate' => 'decimal:18',
        'minimum_stake' => 'decimal:18',
        'maximum_stake' => 'decimal:18',
        'last_reward_timestamp' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'compounding_enabled' => 'boolean',
        'is_verified' => 'boolean',
        'tags' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('pool_status', 'active')
                    ->where('start_time', '<=', now())
                    ->where(function($q) {
                        $q->whereNull('end_time')
                          ->orWhere('end_time', '>', now());
                    });
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByToken($query, $address)
    {
        return $query->where('token_address', $address);
    }

    public function scopeByRewardToken($query, $address)
    {
        return $query->where('reward_token_address', $address);
    }

    public function scopeByApr($query, $min = 0, $max = null)
    {
        $query->where('apr', '>=', $min);
        if ($max !== null) {
            $query->where('apr', '<=', $max);
        }
        return $query;
    }

    // Accessors
    public function getFormattedTotalStakedAttribute()
    {
        return number_format($this->total_staked, 8);
    }

    public function getFormattedTotalRewardsAttribute()
    {
        return number_format($this->total_rewards, 8);
    }

    public function getFormattedAprAttribute()
    {
        return number_format($this->apr, 2) . '%';
    }

    public function getFormattedApyAttribute()
    {
        return number_format($this->apy, 2) . '%';
    }

    public function getFormattedRewardRateAttribute()
    {
        return number_format($this->reward_rate, 8);
    }

    public function getFormattedMinimumStakeAttribute()
    {
        return number_format($this->minimum_stake, 8);
    }

    public function getFormattedMaximumStakeAttribute()
    {
        return number_format($this->maximum_stake, 8);
    }

    public function getPoolStatusLabelAttribute()
    {
        $labels = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'paused' => 'مؤقت',
            'closed' => 'مغلق',
            'ended' => 'منتهي'
        ];
        return $labels[$this->pool_status] ?? $this->pool_status;
    }

    public function getVerificationStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'قيد الانتظار',
            'verified' => 'تم التحقق',
            'rejected' => 'مرفوض',
            'not_verified' => 'لم يتم التحقق'
        ];
        return $labels[$this->verification_status] ?? $this->verification_status;
    }

    public function getPoolUrlAttribute()
    {
        return "https://etherscan.io/address/{$this->contract_address}";
    }

    public function getTokenUrlAttribute()
    {
        return "https://etherscan.io/token/{$this->token_address}";
    }

    public function getRewardTokenUrlAttribute()
    {
        return "https://etherscan.io/token/{$this->reward_token_address}";
    }

    public function getActiveStakerRateAttribute()
    {
        if ($this->total_stakers == 0) return 0;
        return ($this->active_stakers / $this->total_stakers) * 100;
    }

    public function getFormattedActiveStakerRateAttribute()
    {
        return number_format($this->active_staker_rate, 2) . '%';
    }

    public function getAverageStakePerStakerAttribute()
    {
        if ($this->active_stakers == 0) return 0;
        return $this->total_staked / $this->active_stakers;
    }

    public function getFormattedAverageStakePerStakerAttribute()
    {
        return number_format($this->average_stake_per_staker, 8);
    }

    public function getPoolUtilizationAttribute()
    {
        if ($this->maximum_stake == 0) return 0;
        return ($this->total_staked / $this->maximum_stake) * 100;
    }

    public function getFormattedPoolUtilizationAttribute()
    {
        return number_format($this->pool_utilization, 2) . '%';
    }

    public function getDaysSinceLastRewardAttribute()
    {
        return $this->last_reward_timestamp ? 
               $this->last_reward_timestamp->diffInDays(now()) : 
               0;
    }

    public function getFormattedLockPeriodAttribute()
    {
        $periods = [
            0 => 'بدون قفل',
            86400 => '24 ساعة',
            604800 => '7 أيام',
            1209600 => '14 يوم',
            2592000 => '30 يوم',
            7776000 => '90 يوم',
            15552000 => '180 يوم',
            31536000 => '365 يوم'
        ];
        
        return $periods[$this->lock_period] ?? $this->lock_period . ' ثانية';
    }

    public function getFormattedRewardPeriodAttribute()
    {
        $periods = [
            3600 => 'كل ساعة',
            86400 => 'يومياً',
            604800 => 'أسبوعياً',
            1209600 => 'كل أسبوعين',
            2592000 => 'شهرياً'
        ];
        
        return $periods[$this->reward_period] ?? $this->reward_period . ' ثانية';
    }

    public function getFormattedEarlyWithdrawalPenaltyAttribute()
    {
        return number_format($this->early_withdrawal_penalty, 2) . '%';
    }

    // Methods
    public function isVerified()
    {
        return $this->is_verified;
    }

    public function isActive()
    {
        return $this->pool_status === 'active' && 
               $this->start_time <= now() && 
               (!$this->end_time || $this->end_time > now());
    }

    public function isFull()
    {
        return $this->maximum_stake > 0 && $this->total_staked >= $this->maximum_stake;
    }

    public function canStake($amount)
    {
        return $amount >= $this->minimum_stake && 
               ($this->maximum_stake == 0 || $this->total_staked + $amount <= $this->maximum_stake);
    }

    public function calculateRewards($amount, $days)
    {
        $dailyRate = $this->apr / 365 / 100;
        $rewards = $amount * $dailyRate * $days;
        
        if ($this->compounding_enabled) {
            // Compound daily
            $rewards = $amount * pow(1 + $dailyRate, $days) - $amount;
        }
        
        return $rewards;
    }

    public function calculateApy()
    {
        if (!$this->compounding_enabled) {
            return $this->apr;
        }
        
        // Convert APR to APY with daily compounding
        $dailyRate = $this->apr / 365 / 100;
        return (pow(1 + $dailyRate, 365) - 1) * 100;
    }

    public function updateApy()
    {
        $this->apy = $this->calculateApy();
        $this->save();
    }

    public function getEstimatedRewards($amount, $period = '1y')
    {
        $periods = [
            '1d' => 1,
            '1w' => 7,
            '1m' => 30,
            '3m' => 90,
            '6m' => 180,
            '1y' => 365
        ];
        
        $days = $periods[$period] ?? 365;
        return $this->calculateRewards($amount, $days);
    }

    public function getFormattedEstimatedRewards($amount, $period = '1y')
    {
        return number_format($this->getEstimatedRewards($amount, $period), 8);
    }

    public function getRewardDistribution()
    {
        return [
            'total_rewards' => $this->total_rewards,
            'pending_rewards' => $this->pending_rewards,
            'paid_rewards' => $this->total_reward_paid,
            'last_reward' => $this->last_reward_timestamp,
            'days_since_last_reward' => $this->days_since_last_reward
        ];
    }

    public function getStakeStats()
    {
        return [
            'total_staked' => $this->total_staked,
            'total_stakers' => $this->total_stakers,
            'active_stakers' => $this->active_stakers,
            'average_stake' => $this->average_stake_amount,
            'average_stake_per_staker' => $this->average_stake_per_staker,
            'pool_utilization' => $this->pool_utilization,
            'total_deposits' => $this->total_deposits,
            'total_withdrawals' => $this->total_withdrawals
        ];
    }

    public function getPerformanceStats()
    {
        return [
            'apr' => $this->apr,
            'apy' => $this->apy,
            'reward_rate' => $this->reward_rate,
            'compounding_enabled' => $this->compounding_enabled,
            'lock_period' => $this->lock_period,
            'early_withdrawal_penalty' => $this->early_withdrawal_penalty
        ];
    }

    // Relationships
    public function smartContract(): BelongsTo
    {
        return $this->belongsTo(SmartContract::class, 'contract_address', 'address');
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(Token::class, 'token_address', 'address');
    }

    public function rewardToken(): BelongsTo
    {
        return $this->belongsTo(Token::class, 'reward_token_address', 'address');
    }

    public function stakes(): HasMany
    {
        return $this->hasMany(StakingPosition::class, 'pool_id');
    }

    public function rewards(): HasMany
    {
        return $this->hasMany(StakingReward::class, 'pool_id');
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(StakingDeposit::class, 'pool_id');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(StakingWithdrawal::class, 'pool_id');
    }

    // Static Methods
    public static function getStats()
    {
        return [
            'total_pools' => self::count(),
            'active_pools' => self::active()->count(),
            'verified_pools' => self::verified()->count(),
            'total_staked' => self::sum('total_staked'),
            'total_rewards' => self::sum('total_rewards'),
            'total_stakers' => self::sum('total_stakers'),
            'active_stakers' => self::sum('active_stakers'),
            'average_apr' => self::avg('apr'),
            'average_apy' => self::avg('apy'),
            'pools_today' => self::whereDate('created_at', today())->count(),
            'pools_this_week' => self::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'pools_this_month' => self::whereMonth('created_at', now()->month)->count(),
        ];
    }

    public static function getTopPools($limit = 20)
    {
        return self::orderBy('total_staked', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getHighestAprPools($limit = 20)
    {
        return self::orderBy('apr', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getNewPools($limit = 20)
    {
        return self::orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getVerifiedPools($limit = 50)
    {
        return self::verified()
                   ->orderBy('total_staked', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getPoolsByToken($tokenAddress, $limit = 50)
    {
        return self::byToken($tokenAddress)
                   ->orderBy('total_staked', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getPoolsByApr($min = 0, $max = null, $limit = 50)
    {
        return self::byApr($min, $max)
                   ->orderBy('apr', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function searchPools($query, $limit = 50)
    {
        return self::where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('contract_address', 'like', "%{$query}%");
                })
                ->orderBy('total_staked', 'desc')
                ->limit($limit)
                ->get();
    }

    public static function getDailyPoolCount($days = 30)
    {
        return self::where('created_at', '>=', now()->subDays($days))
                   ->groupBy('date')
                   ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                   ->orderBy('date', 'desc')
                   ->get();
    }

    public static function getAprDistribution()
    {
        return self::selectRaw('
                CASE 
                    WHEN apr < 1 THEN "< 1%"
                    WHEN apr < 5 THEN "1-5%"
                    WHEN apr < 10 THEN "5-10%"
                    WHEN apr < 20 THEN "10-20%"
                    WHEN apr < 50 THEN "20-50%"
                    ELSE "> 50%"
                END as apr_range,
                COUNT(*) as count
            ')
            ->groupBy('apr_range')
            ->orderBy('count', 'desc')
            ->get();
    }

    public static function getStakingStats()
    {
        return [
            'total_value_locked' => self::sum('total_staked'),
            'total_rewards_distributed' => self::sum('total_rewards'),
            'total_stakers' => self::sum('total_stakers'),
            'average_apr' => self::avg('apr'),
            'highest_apr' => self::max('apr'),
            'lowest_apr' => self::min('apr'),
            'total_pools' => self::count(),
            'active_pools' => self::active()->count(),
        ];
    }

    // Export Methods
    public static function exportToCsv($pools)
    {
        $headers = [
            'Name', 'Contract Address', 'Token', 'Reward Token', 'Total Staked', 
            'Total Rewards', 'APR', 'APY', 'Total Stakers', 'Active Stakers', 
            'Status', 'Verified', 'Created At'
        ];

        $rows = $pools->map(function ($pool) {
            return [
                $pool->name,
                $pool->contract_address,
                $pool->token_address,
                $pool->reward_token_address,
                $pool->formatted_total_staked,
                $pool->formatted_total_rewards,
                $pool->formatted_apr,
                $pool->formatted_apy,
                $pool->total_stakers,
                $pool->active_stakers,
                $pool->status_label,
                $pool->is_verified ? 'Yes' : 'No',
                $pool->created_at
            ];
        });

        return collect([$headers])->concat($rows);
    }

    // Validation Methods
    public function validatePool()
    {
        $errors = [];
        
        if (empty($this->name)) {
            $errors[] = 'Pool name is required';
        }
        
        if (empty($this->contract_address)) {
            $errors[] = 'Contract address is required';
        }
        
        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $this->contract_address)) {
            $errors[] = 'Invalid contract address format';
        }
        
        if (empty($this->token_address)) {
            $errors[] = 'Token address is required';
        }
        
        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $this->token_address)) {
            $errors[] = 'Invalid token address format';
        }
        
        if ($this->apr < 0) {
            $errors[] = 'APR must be positive';
        }
        
        if ($this->minimum_stake < 0) {
            $errors[] = 'Minimum stake must be positive';
        }
        
        if ($this->maximum_stake < 0) {
            $errors[] = 'Maximum stake must be positive';
        }
        
        if ($this->maximum_stake > 0 && $this->minimum_stake > $this->maximum_stake) {
            $errors[] = 'Minimum stake cannot exceed maximum stake';
        }
        
        if ($this->lock_period < 0) {
            $errors[] = 'Lock period must be positive';
        }
        
        if ($this->early_withdrawal_penalty < 0 || $this->early_withdrawal_penalty > 100) {
            $errors[] = 'Early withdrawal penalty must be between 0 and 100';
        }
        
        return $errors;
    }

    // Staking Operations
    public function stake($user, $amount)
    {
        if (!$this->canStake($amount)) {
            throw new \Exception('Cannot stake this amount');
        }
        
        // Create staking position
        $position = StakingPosition::create([
            'pool_id' => $this->id,
            'user_address' => $user,
            'amount' => $amount,
            'staked_at' => now(),
            'lock_period' => $this->lock_period,
            'unlock_time' => now()->addSeconds($this->lock_period),
            'status' => 'active'
        ]);
        
        // Update pool stats
        $this->total_staked += $amount;
        $this->total_deposits++;
        $this->save();
        
        return $position;
    }

    public function unstake($positionId, $user)
    {
        $position = StakingPosition::find($positionId);
        
        if (!$position || $position->user_address !== $user) {
            throw new \Exception('Invalid position');
        }
        
        if ($position->status !== 'active') {
            throw new \Exception('Position is not active');
        }
        
        // Check lock period
        if (now() < $position->unlock_time) {
            // Apply early withdrawal penalty
            $penalty = $position->amount * ($this->early_withdrawal_penalty / 100);
            $withdrawalAmount = $position->amount - $penalty;
        } else {
            $withdrawalAmount = $position->amount;
        }
        
        // Update position
        $position->status = 'withdrawn';
        $position->withdrawn_at = now();
        $position->withdrawal_amount = $withdrawalAmount;
        $position->save();
        
        // Update pool stats
        $this->total_staked -= $position->amount;
        $this->total_withdrawals++;
        $this->save();
        
        return $position;
    }

    public function claimRewards($positionId, $user)
    {
        $position = StakingPosition::find($positionId);
        
        if (!$position || $position->user_address !== $user) {
            throw new \Exception('Invalid position');
        }
        
        // Calculate pending rewards
        $pendingRewards = $this->calculateRewards($position->amount, 
            $position->staked_at->diffInDays(now()));
        
        // Create reward record
        $reward = StakingReward::create([
            'pool_id' => $this->id,
            'position_id' => $positionId,
            'user_address' => $user,
            'amount' => $pendingRewards,
            'claimed_at' => now()
        ]);
        
        // Update pool stats
        $this->total_reward_paid += $pendingRewards;
        $this->last_reward_timestamp = now();
        $this->save();
        
        return $reward;
    }

    public function distributeRewards()
    {
        $activePositions = StakingPosition::where('pool_id', $this->id)
                                         ->where('status', 'active')
                                         ->get();
        
        foreach ($activePositions as $position) {
            $rewards = $this->calculateRewards($position->amount, 
                $position->last_reward_at ? 
                $position->last_reward_at->diffInDays(now()) : 
                $position->staked_at->diffInDays(now()));
            
            if ($rewards > 0) {
                StakingReward::create([
                    'pool_id' => $this->id,
                    'position_id' => $position->id,
                    'user_address' => $position->user_address,
                    'amount' => $rewards,
                    'distributed_at' => now()
                ]);
                
                $position->last_reward_at = now();
                $position->total_rewards += $rewards;
                $position->save();
            }
        }
        
        $this->last_reward_timestamp = now();
        $this->save();
    }

    public function getPoolMetrics()
    {
        return [
            'total_staked' => $this->total_staked,
            'total_stakers' => $this->total_stakers,
            'active_stakers' => $this->active_stakers,
            'apr' => $this->apr,
            'apy' => $this->apy,
            'utilization' => $this->pool_utilization,
            'average_stake' => $this->average_stake_amount,
            'total_rewards' => $this->total_rewards,
            'reward_rate' => $this->reward_rate,
            'compounding' => $this->compounding_enabled,
            'lock_period' => $this->lock_period,
            'status' => $this->pool_status
        ];
    }

    public function getUserPosition($userAddress)
    {
        return StakingPosition::where('pool_id', $this->id)
                           ->where('user_address', $userAddress)
                           ->where('status', 'active')
                           ->first();
    }

    public function getUserRewards($userAddress)
    {
        return StakingReward::where('pool_id', $this->id)
                          ->where('user_address', $userAddress)
                          ->sum('amount');
    }

    public function getUserStats($userAddress)
    {
        $position = $this->getUserPosition($userAddress);
        $rewards = $this->getUserRewards($userAddress);
        
        return [
            'position' => $position,
            'total_staked' => $position ? $position->amount : 0,
            'pending_rewards' => $position ? 
                $this->calculateRewards($position->amount, 
                $position->last_reward_at ? 
                $position->last_reward_at->diffInDays(now()) : 
                $position->staked_at->diffInDays(now())) : 0,
            'total_rewards' => $rewards,
            'staked_at' => $position ? $position->staked_at : null,
            'unlock_time' => $position ? $position->unlock_time : null,
            'can_withdraw' => $position ? now() >= $position->unlock_time : false
        ];
    }
}
