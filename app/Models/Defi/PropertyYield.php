<?php

namespace App\Models\Defi;

use App\Models\User;
use App\Models\Defi\PropertyStaking;
use App\Models\Defi\PropertyLiquidityPool;
use App\Models\Defi\FractionalOwnership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Support\Facades\DB;

class PropertyYield extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'property_staking_id',
        'property_liquidity_pool_id',
        'fractional_ownership_id',
        'type',
        'amount',
        'apr',
        'period',
        'description',
        'status',
        'metadata',
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
        'metadata' => AsArrayObject::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the yield.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the staking position associated with the yield.
     */
    public function staking(): BelongsTo
    {
        return $this->belongsTo(PropertyStaking::class, 'property_staking_id');
    }

    /**
     * Get the liquidity pool associated with the yield.
     */
    public function pool(): BelongsTo
    {
        return $this->belongsTo(PropertyLiquidityPool::class, 'property_liquidity_pool_id');
    }

    /**
     * Get the fractional ownership associated with the yield.
     */
    public function ownership(): BelongsTo
    {
        return $this->belongsTo(FractionalOwnership::class, 'fractional_ownership_id');
    }

    /**
     * Get the payouts for the yield.
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(PropertyYieldPayout::class);
    }

    /**
     * Get the compounds for the yield.
     */
    public function compounds(): HasMany
    {
        return $this->hasMany(PropertyYieldCompound::class);
    }

    /**
     * Scope a query to only include active yields.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include yields by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include yields by period.
     */
    public function scopeByPeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    /**
     * Scope a query to only include yields by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include yields by APR range.
     */
    public function scopeByAprRange($query, $minApr, $maxApr = null)
    {
        $query->where('apr', '>=', $minApr);
        
        if ($maxApr !== null) {
            $query->where('apr', '<=', $maxApr);
        }
        
        return $query;
    }

    /**
     * Get the type text attribute.
     */
    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'staking' => 'تخزين',
            'pool' => 'مجمع سيولة',
            'dividend' => 'توزيع أرباح',
            'compound' => 'تراكم',
            'referral' => 'إحالة',
            default => $this->type,
        };
    }

    /**
     * Get the status text attribute.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            'cancelled' => 'ملغي',
            'suspended' => 'معلق',
            default => $this->status,
        };
    }

    /**
     * Get the period text attribute.
     */
    public function getPeriodTextAttribute(): string
    {
        return match($this->period) {
            'daily' => 'يومي',
            'weekly' => 'أسبوعي',
            'monthly' => 'شهري',
            'quarterly' => 'ربع سنوي',
            'yearly' => 'سنوي',
            default => $this->period,
        };
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
     * Get the next payout date.
     */
    public function getNextPayoutDateAttribute(): string
    {
        $period = $this->period;
        $lastPayout = $this->payouts()->latest('created_at')->first();
        
        if (!$lastPayout) {
            return now()->addMonth()->format('Y-m-d');
        }
        
        switch ($period) {
            case 'daily':
                return $lastPayout->created_at->addDay()->format('Y-m-d');
            case 'weekly':
                return $lastPayout->created_at->addWeek()->format('Y-m-d');
            case 'monthly':
                return $lastPayout->created_at->addMonth()->format('Y-m-d');
            case 'quarterly':
                return $lastPayout->created_at->addQuarter()->format('Y-m-d');
            case 'yearly':
                return $lastPayout->created_at->addYear()->format('Y-m-d');
            default:
                return now()->addMonth()->format('y-m-d');
        }
    }

    /**
     * Get the total payouts count.
     */
    public function getTotalPayoutsAttribute(): int
    {
        return $this->payouts()->count();
    }

    /**
     * Get the average payout amount.
     */
    public function getAveragePayoutAttribute(): float
    {
        return $this->payouts()->avg('amount') ?? 0;
    }

    /**
     * Get the claimable amount.
     */
    public function getClaimableAmountAttribute(): float
    {
        $dailyRate = $this->apr / 365 / 100;
        $daysSinceLastClaim = now()->diffInDays($this->last_claimed_at ?? $this->created_at);
        
        return $this->amount * $dailyRate * $daysSinceLastClaim;
    }

    /**
     * Get the compound amount.
     */
    public function getCompoundAmountAttribute(): float
    {
        if ($this->type !== 'compound') {
            return 0;
        }

        $principal = $this->amount;
        $rate = $this->apr / 100 / 365;
        $days = 1; // Daily compounding
        
        return $principal * pow(1 + $rate, $days) - $principal;
    }

    /**
     * Get the yield on investment.
     */
    public function getYieldOnInvestmentAttribute(): float
    {
        $principal = $this->getInvestmentAmount();
        
        if ($principal <= 0) {
            return 0;
        }
        
        return ($this->amount / $principal) * 100;
    }

    /**
     * Get the effective APR.
     */
    public function getEffectiveAprAttribute(): float
    {
        if ($this->type === 'compound') {
            // Calculate effective APR with compounding
            $principal = $this->amount;
            $rate = $this->apr / 100 / 365;
            $periods = 365; // Daily compounding
            
            return ((pow(1 + $rate, $periods) - 1) * 100);
        }
        
        return $this->apr;
    }

    /**
     * Get the compounding effect.
     */
    public function getCompoundingEffectAttribute(): float
    {
        if ($this->type !== 'compound') {
            return 0;
        }

        // This would calculate the difference between simple and compound interest
        // For now, return a mock calculation
        return $this->apr * 0.05; // 5% additional yield from compounding
    }

    /**
     * Get the investment amount.
     */
    public function getInvestmentAmount(): float
    {
        switch ($this->type) {
            case 'staking':
                return $this->staking ? $this->staking->amount : 0;
            case 'pool':
                $userLiquidity = $this->pool ? $this->pool->liquidityProviders()
                    ->where('user_id', $this->user_id)
                    ->first() : null;
                return $userLiquidity ? $userLiquidity->amount : 0;
            case 'dividend':
                return $this->ownership ? $this->ownership->total_invested : 0;
            default:
                return 0;
        }
    }

    /**
     * Get the last claimed at timestamp.
     */
    public function getLastClaimedAtAttribute(): ?string
    {
        return $this->last_claimed_at ? $this->last_claimed_at->format('Y-m-d H:i:s') : null;
    }

    /**
     * Get the last compounded at timestamp.
     */
    public function getLastCompoundedAtAttribute(): ?string
    {
        return $this->last_compounded_at ? $this->last_compounded_at->format('Y-m-d H:i:s') : null;
    }

    /**
     * Check if the yield is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the yield supports compounding.
     */
    public function supportsCompounding(): bool
    {
        return $this->type === 'compound';
    }

    /**
     * Check if there are claimable earnings.
     */
    public function hasClaimableEarnings(): bool
    {
        return $this->claimable_amount > 0;
    }

    /**
     * Check if compounding is available.
     */
    public function isCompoundingAvailable(): bool
    {
        return $this->supports_compounding && $this->compound_amount > 0;
    }

    /**
     * Claim earnings.
     */
    public function claim(): bool
    {
        if (!$this->hasClaimableEarnings()) {
            return false;
        }

        $claimableAmount = $this->claimable_amount;

        DB::beginTransaction();

        try {
            // Create payout record
            $this->payouts()->create([
                'amount' => $claimableAmount,
                'currency' => 'USD',
                'status' => 'completed',
                'claimed_at' => now(),
                'created_at' => now(),
            ]);

            // Update yield
            $this->update([
                'amount' => $this->amount + $claimableAmount,
                'last_claimed_at' => now(),
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Compound earnings.
     */
    public function compound(): bool
    {
        if (!$this->isCompoundingAvailable()) {
            return false;
        }

        $compoundAmount = $this->compound_amount;

        DB::beginTransaction();

        try {
            // Create compound record
            $this->compounds()->create([
                'amount' => $compoundAmount,
                'apr' => $this->apr,
                'period' => $this->period,
                'status' => 'completed',
                'compounded_at' => now(),
                'created_at' => now(),
            ]);

            // Update yield
            $this->update([
                'amount' => $this->amount + $compoundAmount,
                'last_compounded_at' => now(),
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Complete the yield.
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
     * Suspend the yield.
     */
    public function suspend(): bool
    {
        return $this->update([
            'status' => 'suspended',
            'suspended_at' => now(),
        ]);
    }

    /**
     * Cancel the yield.
     */
    public function cancel(): bool
    {
        return $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Get yield statistics.
     */
    public static function getStatistics(): array
    {
        $stats = [
            'total_yields' => self::count(),
            'active_yields' => self::active()->count(),
            'total_earned' => self::active()->sum('amount'),
            'staking_yields' => self::byType('staking')->sum('amount'),
            'pool_yields' => self::byType('pool')->sum('amount'),
            'dividend_yields' => self::byType('dividend')->sum('amount'),
            'compound_yields' => self::byType('compound')->sum('amount'),
            'referral_yields' => self::byType('referral')->sum('amount'),
            'average_apr' => self::active()->avg('apr'),
            'total_payouts' => self::withCount('payouts')->get()->sum('payouts_count'),
            'total_compounds' => self::withCount('compounds')->get()->sum('compounds_count'),
        ];

        return $stats;
    }

    /**
     * Get monthly yield data.
     */
    public static function getMonthlyData(): array
    {
        $monthlyData = [];
        
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthData = [
                'month' => $date->format('Y-m'),
                'new_yields' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'completed_yields' => self::whereMonth('completed_at', $date->month)
                    ->whereYear('completed_at', $date->year)
                    ->count(),
                'total_earned' => self::active()->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('amount'),
                'total_payouts' => self::active()->whereHas('payouts', function ($query) use ($date) {
                    $query->whereMonth('claimed_at', $date->month)
                        ->whereYear('claimed_at', $date->year);
                })->withSum('payouts as payout_amount', 'amount')
                ->get()
                ->sum('payout_amount'),
            ];
            
            $monthlyData[] = $monthData;
        }

        return $monthlyData;
    }

    /**
     * Get top performing yields.
     */
    public static function getTopPerformingYields($limit = 10): array
    {
        return self::with(['user', 'staking', 'pool', 'ownership'])
            ->active()
            ->orderBy('apr', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($yield) {
                return [
                    'id' => $yield->id,
                    'type' => $yield->type,
                    'amount' => $yield->amount,
                    'apr' => $yield->apr,
                    'period' => $yield->period,
                    'daily_earnings' => $yield->daily_earnings,
                    'monthly_earnings' => $yield->monthly_earnings,
                    'yearly_earnings' => $yield->yearly_earnings,
                    'effective_apr' => $yield->effective_apr,
                    'compounding_effect' => $yield->compounding_effect,
                    'user' => $yield->user,
                ];
            })
            ->toArray();
    }

    /**
     * Get yield distribution by type.
     */
    public static function getTypeDistribution(): array
    {
        return self::active()->groupBy('type')->map->count()->toArray();
    }

    /**
     * Get yield distribution by period.
     */
    public static function getPeriodDistribution(): array
    {
        $distribution = [
            'daily' => 0,
            'weekly' => 0,
            'monthly' => 0,
            'quarterly' => 0,
            'yearly' => 0,
        ];

        $yields = self::active()->get();

        foreach ($yields as $yield) {
            $distribution[$yield->period]++;
        }

        return $distribution;
    }

    /**
     * Get yield distribution by APR range.
     */
    public static function getAprDistribution(): array
    {
        $distribution = [
            'low' => 0, // < 5%
            'medium' => 0, // 5-15%
            'high' => 0, // 15-25%
            'very_high' => 0, // > 25%
        ];

        $yields = self::active()->get();

        foreach ($yields as $yield) {
            if ($yield->apr < 5) {
                $distribution['low']++;
            } elseif ($yield->apr < 15) {
                $distribution['medium']++;
            } elseif ($yield->apr < 25) {
                $distribution['high']++;
            } else {
                $distribution['very_high']++;
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

        static::creating(function ($yield) {
            if (!$yield->apr) {
                $yield->apr = $yield->apr ?? 5; // Default 5% APR
            }
        });

        static::updating(function ($yield) {
            if ($yield->isDirty('amount') && $yield->isDirty('apr')) {
                // This would trigger yield change notifications
                \Log::info("Yield {$yield->id} updated: amount={$yield->amount}, apr={$yield->apr}");
            }
        });
    }
}
