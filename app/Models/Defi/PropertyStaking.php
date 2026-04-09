<?php

namespace App\Models\Defi;

use App\Models\User;
use App\Models\Defi\PropertyToken;
use App\Models\Defi\PropertyYield;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Support\Facades\DB;

class PropertyStaking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'property_token_id',
        'amount',
        'staking_period',
        'apr',
        'rewards_rate',
        'lock_period',
        'auto_compound',
        'minimum_stake',
        'maximum_stake',
        'total_earned',
        'total_yields',
        'last_compound_at',
        'unstaking_available_at',
        'status',
        'smart_contract_address',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:18',
        'apr' => 'decimal:5',
        'rewards_rate' => 'decimal:5',
        'minimum_stake' => 'decimal:18',
        'maximum_stake' => 'decimal:18',
        'total_earned' => 'decimal:18',
        'total_yields' => 'decimal:18',
        'auto_compound' => 'boolean',
        'is_public' => 'boolean',
        'last_compound_at' => 'datetime',
        'unstaking_available_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'smart_contract_address',
    ];

    /**
     * Get the user that owns the staking position.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the token being staked.
     */
    public function token(): BelongsTo
    {
        return $this->belongsTo(PropertyToken::class, 'property_token_id');
    }

    /**
     * Get the yields for the staking position.
     */
    public function yields(): HasMany
    {
        return $this->hasMany(PropertyYield::class);
    }

    /**
     * Get the compounds for the staking position.
     */
    public function compounds(): HasMany
    {
        return $this->hasMany(PropertyStakingCompound::class);
    }

    /**
     * Get the unstaking requests for the staking position.
     */
    public function unstakingRequests(): HasMany
    {
        return $this->hasMany(PropertyStakingUnstake::class);
    }

    /**
     * Scope a query to only include active staking positions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include completed staking positions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include staking positions by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include staking positions by token.
     */
    public function scopeByToken($query, $tokenId)
    {
        return $query->where('property_token_id', $tokenId);
    }

    /**
     * Scope a query to only include staking positions with auto-compound.
     */
    public function scopeWithAutoCompound($query)
    {
        return $query->where('auto_compound', true);
    }

    /**
     * Scope a query to only include unstakable positions.
     */
    public function scopeUnstakable($query)
    {
        return $query->where('unstaking_available_at', '<=', now());
    }

    /**
     * Get the status text attribute.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'withdrawn' => 'مسحوب',
            'failed' => 'فشل',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }

    /**
     * Get the current value.
     */
    public function getCurrentValueAttribute(): float
    {
        return $this->amount * $this->token->price_per_token;
    }

    /**
     * Get the total value earned.
     */
    public function getTotalValueEarnedAttribute(): float
    {
        return $this->total_earned + $this->total_yields;
    }

    /**
     * Get the total value.
     */
    public function getTotalValueAttribute(): float
    {
        return $this->current_value + $this->total_value_earned;
    }

    /**
     * Get the daily earnings.
     */
    public function getDailyEarningsAttribute(): float
    {
        $dailyRate = $this->apr / 365 / 100;
        return $this->amount * $dailyRate;
    }

    /**
     * Get the weekly earnings.
     */
    public function getWeeklyEarningsAttribute(): float
    {
        return $this->daily_earnings * 7;
    }

    /**
     * Get the monthly earnings.
     */
    public function getMonthlyEarningsAttribute(): float
    {
        return $this->daily_earnings * 30;
    }

    /**
     * Get the yearly earnings.
     */
    public function getYearlyEarningsAttribute(): float
    {
        return $this->amount * ($this->apr / 100);
    }

    /**
     * Get the progress percentage.
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->staking_period <= 0) {
            return 0;
        }
        
        $daysElapsed = now()->diffInDays($this->created_at);
        return min(100, ($daysElapsed / $this->staking_period) * 100);
    }

    /**
     * Get the days until unlock.
     */
    public function getDaysUntilUnlockAttribute(): int
    {
        if ($this->lock_period <= 0) {
            return 0;
        }
        
        return max(0, now()->diffInDays($this->unstaking_available_at));
    }

    /**
     * Get the effective APR.
     */
    public function getEffectiveAprAttribute(): float
    {
        if (!$this->auto_compound) {
            return $this->apr;
        }

        // Calculate effective APR with compounding
        $principal = $this->amount;
        $rate = $this->apr / 100 / 365;
        $periods = 365; // Daily compounding
        
        return ((pow(1 + $rate, $periods) - 1) * 100);
    }

    /**
     * Get the next compound date.
     */
    public function getNextCompoundDateAttribute(): ?string
    {
        if (!$this->auto_compound) {
            return null;
        }

        $lastCompound = $this->last_compound_at ?? $this->created_at;
        return $lastCompound->addDay()->format('Y-m-d H:i:s');
    }

    /**
     * Get the total compounds count.
     */
    public function getTotalCompoundsAttribute(): int
    {
        return $this->compounds()->count();
    }

    /**
     * Get the last compound amount.
     */
    public function getLastCompoundAmountAttribute(): float
    {
        $lastCompound = $this->compounds()->latest()->first();
        return $lastCompound ? $lastCompound->amount : 0;
    }

    /**
     * Get the average compound amount.
     */
    public function getAverageCompoundAmountAttribute(): float
    {
        return $this->compounds()->avg('amount') ?? 0;
    }

    /**
     * Get the yield on investment.
     */
    public function getYieldOnInvestmentAttribute(): float
    {
        if ($this->amount <= 0) {
            return 0;
        }

        return ($this->total_value_earned / $this->amount) * 100;
    }

    /**
     * Get the annual percentage yield (APY).
     */
    public function getApyAttribute(): float
    {
        return $this->effective_apr;
    }

    /**
     * Check if the staking position is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the staking position is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the staking position can be unstaked.
     */
    public function canBeUnstaked(): bool
    {
        return $this->isActive() && $this->days_until_unlock <= 0;
    }

    /**
     * Check if the staking position supports auto-compound.
     */
    public function supportsAutoCompound(): bool
    {
        return $this->auto_compound;
    }

    /**
     * Check if the staking position is profitable.
     */
    public function isProfitable(): bool
    {
        return $this->total_value_earned > 0;
    }

    /**
     * Calculate the compound amount.
     */
    public function calculateCompoundAmount(): float
    {
        if (!$this->auto_compound) {
            return 0;
        }

        $principal = $this->amount + $this->total_earned;
        $rate = $this->apr / 100 / 365;
        $periods = 1; // Daily compounding
        
        return $principal * pow(1 + $rate, $periods) - $principal;
    }

    /**
     * Calculate the unstaking penalty.
     */
    public function calculateUnstakingPenalty(): float
    {
        if ($this->days_until_unlock > 0) {
            // Early unstaking penalty
            return $this->amount * 0.05; // 5% penalty
        }

        return 0;
    }

    /**
     * Calculate the net unstaking amount.
     */
    public function calculateNetUnstakingAmount(): float
    {
        $grossAmount = $this->amount + $this->total_earned;
        $penalty = $this->calculateUnstakingPenalty();
        
        return $grossAmount - $penalty;
    }

    /**
     * Compound earnings.
     */
    public function compound(): bool
    {
        if (!$this->auto_compound || !$this->isActive()) {
            return false;
        }

        $compoundAmount = $this->calculateCompoundAmount();
        
        if ($compoundAmount <= 0) {
            return false;
        }

        DB::beginTransaction();

        try {
            // Create compound record
            $this->compounds()->create([
                'amount' => $compoundAmount,
                'apr' => $this->apr,
                'period' => 'daily',
                'status' => 'completed',
                'compounded_at' => now(),
                'created_at' => now(),
            ]);

            // Update staking position
            $this->update([
                'total_earned' => $this->total_earned + $compoundAmount,
                'total_yields' => $this->total_yields + $compoundAmount,
                'last_compound_at' => now(),
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Unstake the position.
     */
    public function unstake($amount = null): bool
    {
        if (!$this->canBeUnstaked()) {
            return false;
        }

        $unstakeAmount = $amount ?? $this->amount;
        
        if ($unstakeAmount > $this->amount) {
            return false;
        }

        DB::beginTransaction();

        try {
            // Create unstaking request
            $this->unstakingRequests()->create([
                'amount' => $unstakeAmount,
                'penalty' => $this->calculateUnstakingPenalty(),
                'net_amount' => $this->calculateNetUnstakingAmount(),
                'status' => 'pending',
                'requested_at' => now(),
                'created_at' => now(),
            ]);

            // Update staking position
            if ($unstakeAmount >= $this->amount) {
                $this->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            } else {
                $this->update([
                    'amount' => $this->amount - $unstakeAmount,
                ]);
            }

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Complete the staking position.
     */
    public function complete(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        return $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Get staking statistics.
     */
    public static function getStatistics(): array
    {
        $stats = [
            'total_staking_positions' => self::count(),
            'active_staking_positions' => self::active()->count(),
            'completed_staking_positions' => self::completed()->count(),
            'total_staked' => self::active()->sum('amount'),
            'total_earned' => self::active()->sum('total_earned'),
            'total_yields' => self::active()->sum('total_yields'),
            'average_apr' => self::active()->avg('apr'),
            'average_staking_period' => self::avg('staking_period'),
            'total_compounds' => self::withCount('compounds')->get()->sum('compounds_count'),
            'auto_compound_positions' => self::withAutoCompound()->count(),
        ];

        return $stats;
    }

    /**
     * Get monthly staking data.
     */
    public static function getMonthlyData(): array
    {
        $monthlyData = [];
        
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthData = [
                'month' => $date->format('Y-m'),
                'new_positions' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'completed_positions' => self::whereMonth('completed_at', $date->month)
                    ->whereYear('completed_at', $date->year)
                    ->count(),
                'total_staked' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('amount'),
                'total_earned' => self::active()->whereHas('yields', function ($query) use ($date) {
                    $query->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year);
                })->withSum('yields as earned', 'amount')
                ->get()
                ->sum('earned'),
            ];
            
            $monthlyData[] = $monthData;
        }

        return $monthlyData;
    }

    /**
     * Get top performing staking positions.
     */
    public static function getTopPerformingPositions($limit = 10): array
    {
        return self::with(['user', 'token'])
            ->active()
            ->orderBy('total_earned', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($position) {
                return [
                    'id' => $position->id,
                    'amount' => $position->amount,
                    'apr' => $position->apr,
                    'total_earned' => $position->total_earned,
                    'yield_on_investment' => $position->yield_on_investment,
                    'staking_period' => $position->staking_period,
                    'auto_compound' => $position->auto_compound,
                    'user' => $position->user,
                    'token' => $position->token,
                ];
            })
            ->toArray();
    }

    /**
     * Get staking distribution by APR.
     */
    public static function getAprDistribution(): array
    {
        $distribution = [
            'low' => 0, // < 5%
            'medium' => 0, // 5-15%
            'high' => 0, // 15-25%
            'very_high' => 0, // > 25%
        ];

        $positions = self::active()->get();

        foreach ($positions as $position) {
            if ($position->apr < 5) {
                $distribution['low']++;
            } elseif ($position->apr < 15) {
                $distribution['medium']++;
            } elseif ($position->apr < 25) {
                $distribution['high']++;
            } else {
                $distribution['very_high']++;
            }
        }

        return $distribution;
    }

    /**
     * Get staking distribution by period.
     */
    public static function getPeriodDistribution(): array
    {
        $distribution = [
            'short' => 0, // < 30 days
            'medium' => 0, // 30-90 days
            'long' => 0, // 90-365 days
            'very_long' => 0, // > 365 days
        ];

        $positions = self::active()->get();

        foreach ($positions as $position) {
            if ($position->staking_period < 30) {
                $distribution['short']++;
            } elseif ($position->staking_period < 90) {
                $distribution['medium']++;
            } elseif ($position->staking_period < 365) {
                $distribution['long']++;
            } else {
                $distribution['very_long']++;
            }
        }

        return $distribution;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($staking) {
            if (!$staking->unstaking_available_at) {
                $staking->unstaking_available_at = now()->addDays($staking->lock_period);
            }
            
            if (!$staking->rewards_rate) {
                $staking->rewards_rate = $staking->apr;
            }
        });

        static::updating(function ($staking) {
            if ($staking->isDirty('status') && $staking->status === 'active') {
                $staking->unstaking_available_at = now()->addDays($staking->lock_period);
            }
        });
    }
}
