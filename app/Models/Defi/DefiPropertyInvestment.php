<?php

namespace App\Models\Defi;

use App\Models\User;
use App\Models\Defi\PropertyToken;
use App\Models\Defi\PropertyLiquidityPool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Support\Facades\DB;

class DefiPropertyInvestment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'investment_type',
        'amount',
        'currency',
        'property_id',
        'property_token_id',
        'liquidity_pool_id',
        'investment_period',
        'expected_roi',
        'actual_roi',
        'total_returns',
        'profit_loss',
        'roi',
        'risk_level',
        'auto_reinvest',
        'status',
        'matured_at',
        'parent_investment_id',
        'reinvested_into_id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:8',
        'expected_roi' => 'decimal:5',
        'actual_roi' => 'decimal:5',
        'total_returns' => 'decimal:8',
        'profit_loss' => 'decimal:8',
        'roi' => 'decimal:5',
        'auto_reinvest' => 'boolean',
        'is_public' => 'boolean',
        'matured_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'parent_investment_id',
        'reinvested_into_id',
    ];

    /**
     * Get the user that owns the investment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the property associated with the investment.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Metaverse\MetaverseProperty::class, 'property_id');
    }

    /**
     * Get the token associated with the investment.
     */
    public function token(): BelongsTo
    {
        return $this->belongsTo(PropertyToken::class, 'property_token_id');
    }

    /**
     * Get the liquidity pool associated with the investment.
     */
    public function pool(): BelongsTo
    {
        return $this->belongsTo(PropertyLiquidityPool::class, 'liquidity_pool_id');
    }

    /**
     * Get the parent investment.
     */
    public function parentInvestment(): BelongsTo
    {
        return $this->belongsTo(DefiPropertyInvestment::class, 'parent_investment_id');
    }

    /**
     * Get the reinvested investment.
     */
    public function reinvestedInvestment(): BelongsTo
    {
        return $this->belongsTo(DefiPropertyInvestment::class, 'reinvested_into_id');
    }

    /**
     * Get the child investments.
     */
    public function childInvestments(): HasMany
    {
        return $this->hasMany(DefiPropertyInvestment::class, 'parent_investment_id');
    }

    /**
     * Get the transactions for the investment.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(DefiInvestmentTransaction::class);
    }

    /**
     * Get the payouts for the investment.
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(DefiInvestmentPayout::class);
    }

    /**
     * Scope a query to only include active investments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include completed investments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include investments by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('investment_type', $type);
    }

    /**
     * Scope a query to only include investments by risk level.
     */
    public function scopeByRiskLevel($query, $riskLevel)
    {
        return $query->where('risk_level', $riskLevel);
    }

    /**
     * Scope a query to only include investments by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include investments with auto-reinvest.
     */
    public function scopeWithAutoReinvest($query)
    {
        return $query->where('auto_reinvest', true);
    }

    /**
     * Scope a query to only include matured investments.
     */
    public function scopeMatured($query)
    {
        return $query->where('matured_at', '<=', now());
    }

    /**
     * Get the investment type text attribute.
     */
    public function getInvestmentTypeTextAttribute(): string
    {
        return match($this->investment_type) {
            'direct' => 'مباشر',
            'token' => 'توكن',
            'pool' => 'مجمع سيولة',
            'fractional' => 'جزئي',
            default => $this->investment_type,
        };
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
            'reinvested' => 'مستثمر مرة أخرى',
            default => $this->status,
        };
    }

    /**
     * Get the risk level text attribute.
     */
    public function getRiskLevelTextAttribute(): string
    {
        return match($this->risk_level) {
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'مرتفع',
            'critical' => 'حرج',
            default => $this->risk_level,
        };
    }

    /**
     * Get the currency text attribute.
     */
    public function getCurrencyTextAttribute(): string
    {
        return match($this->currency) {
            'USD' => 'دولار أمريكي',
            'EUR' => 'يورو',
            'GBP' => 'جنيه إسترليني',
            'ETH' => 'إيثيريوم',
            'BTC' => 'بيتكوين',
            'USDT' => 'تيثر',
            default => $this->currency,
        };
    }

    /**
     * Get the current value.
     */
    public function getCurrentValueAttribute(): float
    {
        switch ($this->investment_type) {
            case 'direct':
                return $this->property ? $this->property->price : $this->amount;
            case 'token':
                return $this->token ? ($this->amount * $this->token->price_per_token) : $this->amount;
            case 'pool':
                return $this->pool ? $this->pool->total_liquidity : $this->amount;
            case 'fractional':
                return $this->token ? ($this->amount * $this->token->price_per_token) : $this->amount;
            default:
                return $this->amount;
        }
    }

    /**
     * Get the total value.
     */
    public function getTotalValueAttribute(): float
    {
        return $this->current_value + $this->total_returns;
    }

    /**
     * Get the net profit/loss.
     */
    public function getNetProfitLossAttribute(): float
    {
        return $this->total_value - $this->amount;
    }

    /**
     * Get the net ROI.
     */
    public function getNetRoiAttribute(): float
    {
        if ($this->amount <= 0) {
            return 0;
        }

        return ($this->net_profit_loss / $this->amount) * 100;
    }

    /**
     * Get the daily earnings.
     */
    public function getDailyEarningsAttribute(): float
    {
        $dailyRate = $this->expected_roi / 365 / 100;
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
        return $this->amount * ($this->expected_roi / 100);
    }

    /**
     * Get the progress percentage.
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->investment_period <= 0) {
            return 0;
        }
        
        $daysElapsed = now()->diffInDays($this->created_at);
        return min(100, ($daysElapsed / $this->investment_period) * 100);
    }

    /**
     * Get the days until maturity.
     */
    public function getDaysUntilMaturityAttribute(): int
    {
        if (!$this->matured_at) {
            return 0;
        }

        return max(0, now()->diffInDays($this->matured_at));
    }

    /**
     * Get the performance rating.
     */
    public function getPerformanceRatingAttribute(): string
    {
        $roi = $this->net_roi;
        
        if ($roi >= 20) {
            return 'excellent';
        } elseif ($roi >= 10) {
            return 'good';
        } elseif ($roi >= 5) {
            return 'average';
        } elseif ($roi >= 0) {
            return 'below_average';
        } else {
            return 'poor';
        }
    }

    /**
     * Get the investment age in days.
     */
    public function getInvestmentAgeAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Get the compound interest earned.
     */
    public function getCompoundInterestAttribute(): float
    {
        if (!$this->auto_reinvest) {
            return 0;
        }

        // Calculate compound interest
        $principal = $this->amount;
        $rate = $this->expected_roi / 100 / 365;
        $days = $this->investment_age;
        
        return $principal * pow(1 + $rate, $days) - $principal;
    }

    /**
     * Get the effective APR.
     */
    public function getEffectiveAprAttribute(): float
    {
        if (!$this->auto_reinvest) {
            return $this->expected_roi;
        }

        // Calculate effective APR with compounding
        $principal = $this->amount;
        $rate = $this->expected_roi / 100 / 365;
        $periods = 365; // Daily compounding
        
        return ((pow(1 + $rate, $periods) - 1) * 100);
    }

    /**
     * Check if the investment is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the investment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the investment is matured.
     */
    public function isMatured(): bool
    {
        return $this->matured_at && $this->matured_at <= now();
    }

    /**
     * Check if the investment can be withdrawn.
     */
    public function canBeWithdrawn(): bool
    {
        return $this->isActive() && $this->isMatured();
    }

    /**
     * Check if the investment supports auto-reinvest.
     */
    public function supportsAutoReinvest(): bool
    {
        return $this->auto_reinvest;
    }

    /**
     * Check if the investment is profitable.
     */
    public function isProfitable(): bool
    {
        return $this->net_profit_loss > 0;
    }

    /**
     * Calculate the withdrawal amount.
     */
    public function calculateWithdrawalAmount(): float
    {
        return $this->total_value;
    }

    /**
     * Calculate the reinvestment amount.
     */
    public function calculateReinvestmentAmount(): float
    {
        return $this->total_returns;
    }

    /**
     * Withdraw the investment.
     */
    public function withdraw(): bool
    {
        if (!$this->canBeWithdrawn()) {
            return false;
        }

        DB::beginTransaction();

        try {
            // Create payout record
            $this->payouts()->create([
                'amount' => $this->calculateWithdrawalAmount(),
                'currency' => $this->currency,
                'type' => 'withdrawal',
                'status' => 'completed',
                'processed_at' => now(),
                'created_at' => now(),
            ]);

            // Update investment status
            $this->update([
                'status' => 'withdrawn',
                'withdrawn_at' => now(),
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Reinvest the returns.
     */
    public function reinvest(): bool
    {
        if (!$this->auto_reinvest || !$this->isCompleted()) {
            return false;
        }

        $reinvestmentAmount = $this->calculateReinvestmentAmount();

        if ($reinvestmentAmount <= 0) {
            return false;
        }

        DB::beginTransaction();

        try {
            // Create new investment
            $newInvestment = self::create([
                'user_id' => $this->user_id,
                'investment_type' => $this->investment_type,
                'amount' => $reinvestmentAmount,
                'currency' => $this->currency,
                'property_id' => $this->property_id,
                'property_token_id' => $this->property_token_id,
                'liquidity_pool_id' => $this->liquidity_pool_id,
                'investment_period' => $this->investment_period,
                'expected_roi' => $this->expected_roi,
                'actual_roi' => 0,
                'total_returns' => 0,
                'profit_loss' => 0,
                'roi' => 0,
                'risk_level' => $this->risk_level,
                'auto_reinvest' => $this->auto_reinvest,
                'status' => 'active',
                'matured_at' => now()->addDays($this->investment_period),
                'parent_investment_id' => $this->id,
                'created_at' => now(),
            ]);

            // Update original investment
            $this->update([
                'status' => 'reinvested',
                'reinvested_at' => now(),
                'reinvested_into_id' => $newInvestment->id,
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Complete the investment.
     */
    public function complete(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        return $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'actual_roi' => $this->net_roi,
        ]);
    }

    /**
     * Update returns.
     */
    public function updateReturns($amount): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $newTotalReturns = $this->total_returns + $amount;
        $newProfitLoss = $newTotalReturns - $this->amount;
        $newRoi = ($newProfitLoss / $this->amount) * 100;

        return $this->update([
            'total_returns' => $newTotalReturns,
            'profit_loss' => $newProfitLoss,
            'roi' => $newRoi,
        ]);
    }

    /**
     * Get investment statistics.
     */
    public static function getStatistics(): array
    {
        $stats = [
            'total_investments' => self::count(),
            'active_investments' => self::active()->count(),
            'completed_investments' => self::completed()->count(),
            'total_invested' => self::active()->sum('amount'),
            'total_returns' => self::active()->sum('total_returns'),
            'total_profit_loss' => self::active()->sum('profit_loss'),
            'average_roi' => self::active()->avg('roi'),
            'average_investment_period' => self::avg('investment_period'),
            'auto_reinvest_count' => self::withAutoReinvest()->count(),
            'matured_investments' => self::matured()->count(),
        ];

        return $stats;
    }

    /**
     * Get monthly investment data.
     */
    public static function getMonthlyData(): array
    {
        $monthlyData = [];
        
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthData = [
                'month' => $date->format('Y-m'),
                'new_investments' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'completed_investments' => self::whereMonth('completed_at', $date->month)
                    ->whereYear('completed_at', $date->year)
                    ->count(),
                'total_invested' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('amount'),
                'total_returns' => self::active()->whereHas('payouts', function ($query) use ($date) {
                    $query->whereMonth('processed_at', $date->month)
                        ->whereYear('processed_at', $date->year);
                })->withSum('payouts as returns', 'amount')
                ->get()
                ->sum('returns'),
            ];
            
            $monthlyData[] = $monthData;
        }

        return $monthlyData;
    }

    /**
     * Get top performing investments.
     */
    public static function getTopPerformingInvestments($limit = 10): array
    {
        return self::with(['user', 'property', 'token', 'pool'])
            ->active()
            ->orderBy('roi', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($investment) {
                return [
                    'id' => $investment->id,
                    'investment_type' => $investment->investment_type,
                    'amount' => $investment->amount,
                    'current_value' => $investment->current_value,
                    'roi' => $investment->roi,
                    'net_roi' => $investment->net_roi,
                    'performance_rating' => $investment->performance_rating,
                    'investment_period' => $investment->investment_period,
                    'auto_reinvest' => $investment->auto_reinvest,
                    'user' => $investment->user,
                ];
            })
            ->toArray();
    }

    /**
     * Get investment distribution by type.
     */
    public static function getTypeDistribution(): array
    {
        return self::active()->groupBy('investment_type')->map->count()->toArray();
    }

    /**
     * Get investment distribution by risk level.
     */
    public static function getRiskDistribution(): array
    {
        $distribution = [
            'low' => 0,
            'medium' => 0,
            'high' => 0,
            'critical' => 0,
        ];

        $investments = self::active()->get();

        foreach ($investments as $investment) {
            $distribution[$investment->risk_level]++;
        }

        return $distribution;
    }

    /**
     * Get investment distribution by ROI.
     */
    public static function getRoiDistribution(): array
    {
        $distribution = [
            'negative' => 0, // < 0%
            'low' => 0, // 0-5%
            'medium' => 0, // 5-15%
            'high' => 0, // 15-25%
            'very_high' => 0, // > 25%
        ];

        $investments = self::active()->get();

        foreach ($investments as $investment) {
            if ($investment->roi < 0) {
                $distribution['negative']++;
            } elseif ($investment->roi < 5) {
                $distribution['low']++;
            } elseif ($investment->roi < 15) {
                $distribution['medium']++;
            } elseif ($investment->roi < 25) {
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

        static::creating(function ($investment) {
            if (!$investment->matured_at) {
                $investment->matured_at = now()->addDays($investment->investment_period);
            }
        });

        static::updating(function ($investment) {
            if ($investment->isDirty('status') && $investment->status === 'completed') {
                $investment->completed_at = now();
                $investment->actual_roi = $investment->net_roi;
            }
        });
    }
}
