<?php

namespace App\Models\Defi;

use App\Models\User;
use App\Models\Defi\PropertyToken;
use App\Models\Defi\TokenDistribution;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Support\Facades\DB;

class FractionalOwnership extends Model
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
        'property_id',
        'shares_owned',
        'total_invested',
        'average_cost_per_share',
        'ownership_percentage',
        'total_dividends',
        'last_dividend_date',
        'status',
        'is_for_sale',
        'sale_price_per_share',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'shares_owned' => 'decimal:18',
        'total_invested' => 'decimal:8',
        'average_cost_per_share' => 'decimal:8',
        'ownership_percentage' => 'decimal:5',
        'total_dividends' => 'decimal:8',
        'sale_price_per_share' => 'decimal:8',
        'is_for_sale' => 'boolean',
        'last_dividend_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the fractional ownership.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the token associated with the fractional ownership.
     */
    public function token(): BelongsTo
    {
        return $this->belongsTo(PropertyToken::class, 'property_token_id');
    }

    /**
     * Get the property associated with the fractional ownership.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Metaverse\MetaverseProperty::class, 'property_id');
    }

    /**
     * Get the distributions for the fractional ownership.
     */
    public function distributions(): HasMany
    {
        return $this->hasMany(TokenDistribution::class);
    }

    /**
     * Get the dividend payments for the fractional ownership.
     */
    public function dividendPayments(): HasMany
    {
        return $this->hasMany(FractionalOwnershipDividend::class);
    }

    /**
     * Get the sale offers for the fractional ownership.
     */
    public function saleOffers(): HasMany
    {
        return $this->hasMany(FractionalOwnershipSale::class);
    }

    /**
     * Scope a query to only include active ownership positions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include sold ownership positions.
     */
    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }

    /**
     * Scope a query to only include ownership positions for sale.
     */
    public function scopeForSale($query)
    {
        return $query->where('is_for_sale', true);
    }

    /**
     * Scope a query to only include ownership positions by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include ownership positions by token.
     */
    public function scopeByToken($query, $tokenId)
    {
        return $query->where('property_token_id', $tokenId);
    }

    /**
     * Scope a query to only include ownership positions by minimum percentage.
     */
    public function scopeByMinPercentage($query, $percentage)
    {
        return $query->where('ownership_percentage', '>=', $percentage);
    }

    /**
     * Get the status text attribute.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'active' => 'نشط',
            'sold' => 'مباع',
            'transferred' => 'منقول',
            'suspended' => 'معلق',
            'deleted' => 'محذوف',
            default => $this->status,
        };
    }

    /**
     * Get the current value.
     */
    public function getCurrentValueAttribute(): float
    {
        return $this->shares_owned * $this->token->price_per_token;
    }

    /**
     * Get the total value invested.
     */
    public function getTotalValueInvestedAttribute(): float
    {
        return $this->total_invested;
    }

    /**
     * Get the profit/loss amount.
     */
    public function getProfitLossAttribute(): float
    {
        return $this->current_value - $this->total_invested;
    }

    /**
     * Get the profit/loss percentage.
     */
    public function getProfitLossPercentageAttribute(): float
    {
        if ($this->total_invested <= 0) {
            return 0;
        }

        return ($this->profit_loss / $this->total_invested) * 100;
    }

    /**
     * Get the dividend yield.
     */
    public function getDividendYieldAttribute(): float
    {
        if ($this->current_value <= 0) {
            return 0;
        }

        return ($this->total_dividends / $this->current_value) * 100;
    }

    /**
     * Get the monthly dividends.
     */
    public function getMonthlyDividendsAttribute(): float
    {
        // This would calculate based on actual dividend payments
        // For now, return a mock calculation
        $annualDividendRate = 0.05; // 5% annual dividend
        return $this->current_value * ($annualDividendRate / 12);
    }

    /**
     * Get the yearly dividends.
     */
    public function getYearlyDividendsAttribute(): float
    {
        // This would calculate based on actual dividend payments
        // For now, return a mock calculation
        $annualDividendRate = 0.05; // 5% annual dividend
        return $this->current_value * $annualDividendRate;
    }

    /**
     * Get the estimated annual dividends.
     */
    public function getEstimatedAnnualDividendsAttribute(): float
    {
        return $this->yearly_dividends;
    }

    /**
     * Get the ownership rank.
     */
    public function getOwnershipRankAttribute(): int
    {
        return FractionalOwnership::where('property_token_id', $this->property_token_id)
            ->where('status', 'active')
            ->where('ownership_percentage', '>', $this->ownership_percentage)
            ->count() + 1;
    }

    /**
     * Get the total shareholders.
     */
    public function getTotalShareholdersAttribute(): int
    {
        return FractionalOwnership::where('property_token_id', $this->property_token_id)
            ->where('status', 'active')
            ->count();
    }

    /**
     * Get the next dividend date.
     */
    public function getNextDividendDateAttribute(): string
    {
        // This would calculate based on actual dividend schedule
        // For now, return a mock date
        $lastDividend = $this->last_dividend_date ?? $this->created_at;
        return $lastDividend->addMonth()->format('Y-m-d');
    }

    /**
     * Get the days since last dividend.
     */
    public function getDaysSinceLastDividendAttribute(): int
    {
        $lastDividend = $this->last_dividend_date ?? $this->created_at;
        return now()->diffInDays($lastDividend);
    }

    /**
     * Get the average holding period.
     */
    public function getAverageHoldingPeriodAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Get the sale proceeds.
     */
    public function getSaleProceedsAttribute(): float
    {
        if (!$this->is_for_sale || !$this->sale_price_per_share) {
            return 0;
        }

        return $this->shares_owned * $this->sale_price_per_share;
    }

    /**
     * Get the sale profit/loss.
     */
    public function getSaleProfitLossAttribute(): float
    {
        if (!$this->is_for_sale) {
            return 0;
        }

        return $this->sale_proceeds - $this->total_invested;
    }

    /**
     * Get the sale profit/loss percentage.
     */
    public function getSaleProfitLossPercentageAttribute(): float
    {
        if (!$this->is_for_sale || $this->total_invested <= 0) {
            return 0;
        }

        return ($this->sale_profit_loss / $this->total_invested) * 100;
    }

    /**
     * Check if the ownership position is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the ownership position is profitable.
     */
    public function isProfitable(): bool
    {
        return $this->profit_loss > 0;
    }

    /**
     * Check if the ownership position is for sale.
     */
    public function isForSale(): bool
    {
        return $this->is_for_sale && $this->sale_price_per_share > 0;
    }

    /**
     * Check if dividends are available.
     */
    public function hasDividendsAvailable(): bool
    {
        return $this->days_since_last_dividend >= 30; // Monthly dividends
    }

    /**
     * Calculate the sale price.
     */
    public function calculateSalePrice($percentage = 10): float
    {
        $currentPrice = $this->token->price_per_token;
        $markup = $currentPrice * ($percentage / 100);
        
        return $currentPrice + $markup;
    }

    /**
     * Set the sale price.
     */
    public function setSalePrice($pricePerShare): bool
    {
        if ($pricePerShare <= 0) {
            return false;
        }

        return $this->update([
            'is_for_sale' => true,
            'sale_price_per_share' => $pricePerShare,
        ]);
    }

    /**
     * Remove from sale.
     */
    public function removeFromSale(): bool
    {
        return $this->update([
            'is_for_sale' => false,
            'sale_price_per_share' => null,
        ]);
    }

    /**
     * Buy shares.
     */
    public function buyShares($amount, $pricePerShare = null): bool
    {
        $pricePerShare = $pricePerShare ?? $this->token->price_per_token;
        $cost = $amount * $pricePerShare;

        DB::beginTransaction();

        try {
            // Create distribution record
            $this->distributions()->create([
                'from_address' => '0x0000000000000000000000000000000000000000', // Minting address
                'to_address' => auth()->user()->wallet_address,
                'amount' => $amount,
                'price' => $pricePerShare,
                'total_amount' => $cost,
                'transaction_hash' => '0x' . bin2hex(random_bytes(32)),
                'status' => 'completed',
                'created_at' => now(),
            ]);

            // Update ownership position
            $newSharesOwned = $this->shares_owned + $amount;
            $newTotalInvested = $this->total_invested + $cost;
            $newAverageCost = $newTotalInvested / $newSharesOwned;
            $newOwnershipPercentage = ($newSharesOwned / $this->token->total_supply) * 100;

            $this->update([
                'shares_owned' => $newSharesOwned,
                'total_invested' => $newTotalInvested,
                'average_cost_per_share' => $newAverageCost,
                'ownership_percentage' => $newOwnershipPercentage,
            ]);

            // Update token distributed supply
            $this->token->increment('distributed_supply', $amount);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Sell shares.
     */
    public function sellShares($amount, $pricePerShare = null): bool
    {
        if ($amount > $this->shares_owned) {
            return false;
        }

        $pricePerShare = $pricePerShare ?? $this->token->price_per_token;
        $proceeds = $amount * $pricePerShare;

        DB::beginTransaction();

        try {
            // Create distribution record
            $this->distributions()->create([
                'from_address' => auth()->user()->wallet_address,
                'to_address' => '0x0000000000000000000000000000000000000000', // Burn address
                'amount' => $amount,
                'price' => $pricePerShare,
                'total_amount' => $proceeds,
                'transaction_hash' => '0x' . bin2hex(random_bytes(32)),
                'status' => 'completed',
                'created_at' => now(),
            ]);

            // Update ownership position
            $newSharesOwned = $this->shares_owned - $amount;
            $newTotalInvested = $this->total_invested * ($newSharesOwned / $this->shares_owned);
            $newOwnershipPercentage = ($newSharesOwned / $this->token->total_supply) * 100;

            if ($newSharesOwned <= 0) {
                $this->update([
                    'status' => 'sold',
                    'sold_at' => now(),
                    'shares_owned' => 0,
                    'ownership_percentage' => 0,
                ]);
            } else {
                $this->update([
                    'shares_owned' => $newSharesOwned,
                    'total_invested' => $newTotalInvested,
                    'ownership_percentage' => $newOwnershipPercentage,
                ]);
            }

            // Update token distributed supply
            $this->token->decrement('distributed_supply', $amount);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Claim dividends.
     */
    public function claimDividends(): bool
    {
        if (!$this->hasDividendsAvailable()) {
            return false;
        }

        $dividendAmount = $this->monthly_dividends;

        DB::beginTransaction();

        try {
            // Create dividend payment record
            $this->dividendPayments()->create([
                'amount' => $dividendAmount,
                'currency' => 'USD',
                'status' => 'completed',
                'paid_at' => now(),
                'created_at' => now(),
            ]);

            // Update ownership position
            $this->update([
                'total_dividends' => $this->total_dividends + $dividendAmount,
                'last_dividend_date' => now(),
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Transfer ownership.
     */
    public function transferTo($newUserId): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        return $this->update([
            'user_id' => $newUserId,
            'status' => 'transferred',
            'transferred_at' => now(),
        ]);
    }

    /**
     * Get ownership statistics.
     */
    public static function getStatistics(): array
    {
        $stats = [
            'total_ownerships' => self::count(),
            'active_ownerships' => self::active()->count(),
            'sold_ownerships' => self::sold()->count(),
            'total_shares' => self::active()->sum('shares_owned'),
            'total_invested' => self::active()->sum('total_invested'),
            'total_dividends' => self::active()->sum('total_dividends'),
            'average_ownership' => self::active()->avg('ownership_percentage'),
            'total_profit_loss' => self::active()->sum(function ($ownership) {
                return ($ownership->shares_owned * $ownership->token->price_per_token) - $ownership->total_invested;
            }),
            'for_sale_count' => self::forSale()->count(),
        ];

        return $stats;
    }

    /**
     * Get monthly ownership data.
     */
    public static function getMonthlyData(): array
    {
        $monthlyData = [];
        
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthData = [
                'month' => $date->format('Y-m'),
                'new_ownerships' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'sold_ownerships' => self::whereMonth('sold_at', $date->month)
                    ->whereYear('sold_at', $date->year)
                    ->count(),
                'total_invested' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('total_invested'),
                'total_dividends' => self::active()->whereHas('dividendPayments', function ($query) use ($date) {
                    $query->whereMonth('paid_at', $date->month)
                        ->whereYear('paid_at', $date->year);
                })->withSum('dividendPayments as dividends', 'amount')
                ->get()
                ->sum('dividends'),
            ];
            
            $monthlyData[] = $monthData;
        }

        return $monthlyData;
    }

    /**
     * Get top performing ownership positions.
     */
    public static function getTopPerformingPositions($limit = 10): array
    {
        return self::with(['user', 'token', 'property'])
            ->active()
            ->orderBy('ownership_percentage', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($ownership) {
                return [
                    'id' => $ownership->id,
                    'shares_owned' => $ownership->shares_owned,
                    'ownership_percentage' => $ownership->ownership_percentage,
                    'total_invested' => $ownership->total_invested,
                    'current_value' => $ownership->current_value,
                    'profit_loss' => $ownership->profit_loss,
                    'profit_loss_percentage' => $ownership->profit_loss_percentage,
                    'dividend_yield' => $ownership->dividend_yield,
                    'ownership_rank' => $ownership->ownership_rank,
                    'user' => $ownership->user,
                    'token' => $ownership->token,
                    'property' => $ownership->property,
                ];
            })
            ->toArray();
    }

    /**
     * Get ownership distribution by percentage.
     */
    public static function getPercentageDistribution(): array
    {
        $distribution = [
            'small' => 0, // < 1%
            'medium' => 0, // 1-5%
            'large' => 0, // 5-10%
            'very_large' => 0, // > 10%
        ];

        $ownerships = self::active()->get();

        foreach ($ownerships as $ownership) {
            if ($ownership->ownership_percentage < 1) {
                $distribution['small']++;
            } elseif ($ownership->ownership_percentage < 5) {
                $distribution['medium']++;
            } elseif ($ownership->ownership_percentage < 10) {
                $distribution['large']++;
            } else {
                $distribution['very_large']++;
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

        static::creating(function ($ownership) {
            if (!$ownership->ownership_percentage) {
                $ownership->ownership_percentage = ($ownership->shares_owned / $ownership->token->total_supply) * 100;
            }
            
            if (!$ownership->average_cost_per_share) {
                $ownership->average_cost_per_share = $ownership->total_invested / $ownership->shares_owned;
            }
        });

        static::updating(function ($ownership) {
            if ($ownership->isDirty('shares_owned') || $ownership->isDirty('total_invested')) {
                $ownership->average_cost_per_share = $ownership->total_invested / $ownership->shares_owned;
                $ownership->ownership_percentage = ($ownership->shares_owned / $ownership->token->total_supply) * 100;
            }
        });
    }
}
