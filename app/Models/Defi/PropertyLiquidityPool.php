<?php

namespace App\Models\Defi;

use App\Models\User;
use App\Models\Defi\PropertyToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Support\Facades\DB;

class PropertyLiquidityPool extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'property_token_id',
        'property_id',
        'creator_id',
        'name',
        'description',
        'apr',
        'fee_percentage',
        'total_liquidity',
        'total_shares',
        'provider_count',
        'minimum_liquidity',
        'maximum_liquidity',
        'lock_period',
        'auto_compound',
        'rebalancing_enabled',
        'rebalancing_threshold',
        'volume_24h',
        'volume_7d',
        'volume_30d',
        'fees_collected_24h',
        'fees_collected_7d',
        'fees_collected_30d',
        'status',
        'is_public',
        'smart_contract_address',
        'deployed_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'apr' => 'decimal:5',
        'fee_percentage' => 'decimal:5',
        'total_liquidity' => 'decimal:18',
        'total_shares' => 'decimal:18',
        'minimum_liquidity' => 'decimal:18',
        'maximum_liquidity' => 'decimal:18',
        'volume_24h' => 'decimal:18',
        'volume_7d' => 'decimal:18',
        'volume_30d' => 'decimal:18',
        'fees_collected_24h' => 'decimal:18',
        'fees_collected_7d' => 'decimal:18',
        'fees_collected_30d' => 'decimal:18',
        'rebalancing_threshold' => 'decimal:5',
        'auto_compound' => 'boolean',
        'rebalancing_enabled' => 'boolean',
        'is_public' => 'boolean',
        'deployed_at' => 'datetime',
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
     * Get the token associated with the liquidity pool.
     */
    public function token(): BelongsTo
    {
        return $this->belongsTo(PropertyToken::class, 'property_token_id');
    }

    /**
     * Get the property associated with the liquidity pool.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Metaverse\MetaverseProperty::class, 'property_id');
    }

    /**
     * Get the creator of the liquidity pool.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the liquidity providers for the pool.
     */
    public function liquidityProviders(): HasMany
    {
        return $this->hasMany(PropertyLiquidityProvider::class);
    }

    /**
     * Get the compounds for the pool.
     */
    public function compounds(): HasMany
    {
        return $this->hasMany(PropertyLiquidityCompound::class);
    }

    /**
     * Get the rebalancing events for the pool.
     */
    public function rebalancingEvents(): HasMany
    {
        return $this->hasMany(PropertyLiquidityRebalancing::class);
    }

    /**
     * Scope a query to only include active pools.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include public pools.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include pools by creator.
     */
    public function scopeByCreator($query, $creatorId)
    {
        return $query->where('creator_id', $creatorId);
    }

    /**
     * Scope a query to only include pools by APR range.
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
     * Scope a query to only include pools by TVL range.
     */
    public function scopeByTvlRange($query, $minTvl, $maxTvl = null)
    {
        $query->where('total_liquidity', '>=', $minTvl);
        
        if ($maxTvl !== null) {
            $query->where('total_liquidity', '<=', $maxTvl);
        }
        
        return $query;
    }

    /**
     * Scope a query to only include pools with auto-compound.
     */
    public function scopeWithAutoCompound($query)
    {
        return $query->where('auto_compound', true);
    }

    /**
     * Get the status text attribute.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'active' => 'نشط',
            'suspended' => 'معلق',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            'deleted' => 'محذوف',
            default => $this->status,
        };
    }

    /**
     * Get the share price.
     */
    public function getSharePriceAttribute(): float
    {
        return $this->total_shares > 0 ? $this->total_liquidity / $this->total_shares : 1;
    }

    /**
     * Get the available liquidity.
     */
    public function getAvailableLiquidityAttribute(): float
    {
        if ($this->maximum_liquidity) {
            return $this->maximum_liquidity - $this->total_liquidity;
        }
        
        return $this->total_liquidity;
    }

    /**
     * Get the utilization rate.
     */
    public function getUtilizationRateAttribute(): float
    {
        if ($this->maximum_liquidity && $this->maximum_liquidity > 0) {
            return ($this->total_liquidity / $this->maximum_liquidity) * 100;
        }
        
        // For pools without maximum, calculate based on volume
        return min(100, ($this->volume_24h / $this->total_liquidity) * 100);
    }

    /**
     * Get the impermanent loss.
     */
    public function getImpermanentLossAttribute(): float
    {
        // This would calculate based on actual price movements
        // For single-asset pools, impermanent loss is 0
        return 0;
    }

    /**
     * Get the daily fees.
     */
    public function getDailyFeesAttribute(): float
    {
        return $this->volume_24h * ($this->fee_percentage / 100);
    }

    /**
     * Get the weekly fees.
     */
    public function getWeeklyFeesAttribute(): float
    {
        return $this->volume_7d * ($this->fee_percentage / 100);
    }

    /**
     * Get the monthly fees.
     */
    public function getMonthlyFeesAttribute(): float
    {
        return $this->volume_30d * ($this->fee_percentage / 100);
    }

    /**
     * Get the total fees collected.
     */
    public function getTotalFeesCollectedAttribute(): float
    {
        return $this->fees_collected_30d;
    }

    /**
     * Get the TVL (Total Value Locked).
     */
    public function getTvlAttribute(): float
    {
        return $this->total_liquidity;
    }

    /**
     * Get the market cap.
     */
    public function getMarketCapAttribute(): float
    {
        return $this->tvl;
    }

    /**
     * Get the price change percentage (24h).
     */
    public function getPriceChange24hAttribute(): float
    {
        // This would calculate based on historical TVL data
        // For now, return a mock calculation
        return rand(-5, 5);
    }

    /**
     * Get the volume change percentage (24h).
     */
    public function getVolumeChange24hAttribute(): float
    {
        // This would calculate based on historical volume data
        // For now, return a mock calculation
        return rand(-10, 10);
    }

    /**
     * Get the APR change percentage (24h).
     */
    public function getAprChange24hAttribute(): float
    {
        // This would calculate based on historical APR data
        // For now, return a mock calculation
        return rand(-2, 2);
    }

    /**
     * Get the next compound date.
     */
    public function getNextCompoundDateAttribute(): ?string
    {
        if (!$this->auto_compound) {
            return null;
        }

        $lastCompound = $this->compounds()->latest()->first();
        return $lastCompound ? $lastCompound->compounded_at->addDay()->format('Y-m-d H:i:s') : now()->addDay()->format('Y-m-d H:i:s');
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
     * Get the provider count.
     */
    public function getProviderCountAttribute(): int
    {
        return $this->liquidityProviders()->where('status', 'active')->count();
    }

    /**
     * Get the top providers.
     */
    public function getTopProvidersAttribute(): array
    {
        return $this->liquidityProviders()
            ->where('status', 'active')
            ->orderBy('amount', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($provider) {
                return [
                    'user_id' => $provider->user_id,
                    'amount' => $provider->amount,
                    'shares' => $provider->shares,
                    'share_percentage' => ($provider->shares / $this->total_shares) * 100,
                    'joined_at' => $provider->created_at,
                ];
            })
            ->toArray();
    }

    /**
     * Check if the pool is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the pool is deployed.
     */
    public function isDeployed(): bool
    {
        return !is_null($this->deployed_at);
    }

    /**
     * Check if the pool is public.
     */
    public function isPublic(): bool
    {
        return $this->is_public;
    }

    /**
     * Check if the pool supports auto-compound.
     */
    public function supportsAutoCompound(): bool
    {
        return $this->auto_compound;
    }

    /**
     * Check if the pool supports rebalancing.
     */
    public function supportsRebalancing(): bool
    {
        return $this->rebalancing_enabled;
    }

    /**
     * Check if the pool has capacity.
     */
    public function hasCapacity(): bool
    {
        if (!$this->maximum_liquidity) {
            return true;
        }

        return $this->total_liquidity < $this->maximum_liquidity;
    }

    /**
     * Calculate the shares for a given liquidity amount.
     */
    public function calculateShares($liquidityAmount): float
    {
        $sharePrice = $this->share_price;
        return $sharePrice > 0 ? $liquidityAmount / $sharePrice : 0;
    }

    /**
     * Calculate the liquidity amount for given shares.
     */
    public function calculateLiquidityAmount($shares): float
    {
        return $shares * $this->share_price;
    }

    /**
     * Calculate the user's share percentage.
     */
    public function calculateUserSharePercentage($userId): float
    {
        $provider = $this->liquidityProviders()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        if (!$provider) {
            return 0;
        }

        return ($provider->shares / $this->total_shares) * 100;
    }

    /**
     * Calculate the user's daily earnings.
     */
    public function calculateUserDailyEarnings($userId): float
    {
        $sharePercentage = $this->calculateUserSharePercentage($userId);
        $dailyFees = $this->daily_fees;
        
        return $dailyFees * ($sharePercentage / 100);
    }

    /**
     * Add liquidity to the pool.
     */
    public function addLiquidity($amount, $userId): bool
    {
        if (!$this->hasCapacity()) {
            return false;
        }

        if ($amount < $this->minimum_liquidity) {
            return false;
        }

        $shares = $this->calculateShares($amount);

        DB::beginTransaction();

        try {
            // Create or update liquidity provider
            $provider = $this->liquidityProviders()
                ->where('user_id', $userId)
                ->first();

            if ($provider) {
                $provider->update([
                    'amount' => $provider->amount + $amount,
                    'shares' => $provider->shares + $shares,
                    'updated_at' => now(),
                ]);
            } else {
                $this->liquidityProviders()->create([
                    'user_id' => $userId,
                    'amount' => $amount,
                    'shares' => $shares,
                    'lock_period' => $this->lock_period,
                    'locked_until' => now()->addDays($this->lock_period),
                    'status' => 'active',
                    'created_at' => now(),
                ]);
            }

            // Update pool
            $this->update([
                'total_liquidity' => $this->total_liquidity + $amount,
                'total_shares' => $this->total_shares + $shares,
                'provider_count' => $this->liquidityProviders()->where('status', 'active')->distinct('user_id')->count(),
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Remove liquidity from the pool.
     */
    public function removeLiquidity($amount, $userId): bool
    {
        $provider = $this->liquidityProviders()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        if (!$provider || $amount > $provider->amount) {
            return false;
        }

        $shares = $this->calculateShares($amount);

        DB::beginTransaction();

        try {
            // Update or remove provider
            if ($provider->amount - $amount <= 0) {
                $provider->update([
                    'status' => 'withdrawn',
                    'withdrawn_at' => now(),
                ]);
            } else {
                $provider->update([
                    'amount' => $provider->amount - $amount,
                    'shares' => $provider->shares - $shares,
                    'updated_at' => now(),
                ]);
            }

            // Update pool
            $this->update([
                'total_liquidity' => $this->total_liquidity - $amount,
                'total_shares' => $this->total_shares - $shares,
                'provider_count' => $this->liquidityProviders()->where('status', 'active')->distinct('user_id')->count(),
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
        if (!$this->auto_compound || !$this->isActive()) {
            return false;
        }

        $feesToCompound = $this->fees_collected_24h;

        if ($feesToCompound <= 0) {
            return false;
        }

        $newShares = $this->calculateShares($feesToCompound);

        DB::beginTransaction();

        try {
            // Create compound record
            $this->compounds()->create([
                'amount' => $feesToCompound,
                'shares' => $newShares,
                'apr' => $this->apr,
                'status' => 'completed',
                'compounded_at' => now(),
                'created_at' => now(),
            ]);

            // Update pool
            $this->update([
                'total_liquidity' => $this->total_liquidity + $feesToCompound,
                'total_shares' => $this->total_shares + $newShares,
                'last_compounded_at' => now(),
            ]);

            // Distribute shares to providers proportionally
            $this->distributeCompoundShares($newShares);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Rebalance the pool.
     */
    public function rebalance(): bool
    {
        if (!$this->rebalancing_enabled || !$this->isActive()) {
            return false;
        }

        // This would implement rebalancing logic
        // For now, just create a rebalancing event
        $this->rebalancingEvents()->create([
            'type' => 'automatic',
            'reason' => 'Threshold reached',
            'old_tvl' => $this->total_liquidity,
            'new_tvl' => $this->total_liquidity,
            'status' => 'completed',
            'rebalanced_at' => now(),
            'created_at' => now(),
        ]);

        return true;
    }

    /**
     * Deploy the pool.
     */
    public function deploy($smartContractAddress = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        return $this->update([
            'status' => 'active',
            'smart_contract_address' => $smartContractAddress,
            'deployed_at' => now(),
        ]);
    }

    /**
     * Suspend the pool.
     */
    public function suspend(): bool
    {
        return $this->update([
            'status' => 'suspended',
            'suspended_at' => now(),
        ]);
    }

    /**
     * Distribute compound shares to providers.
     */
    private function distributeCompoundShares($newShares): void
    {
        $providers = $this->liquidityProviders()->where('status', 'active')->get();
        
        foreach ($providers as $provider) {
            $sharePercentage = $provider->shares / $this->total_shares;
            $additionalShares = $newShares * $sharePercentage;
            
            $provider->update([
                'shares' => $provider->shares + $additionalShares,
                'amount' => ($provider->shares + $additionalShares) * $this->share_price,
            ]);
        }
    }

    /**
     * Get pool statistics.
     */
    public static function getStatistics(): array
    {
        $stats = [
            'total_pools' => self::count(),
            'active_pools' => self::active()->count(),
            'public_pools' => self::public()->count(),
            'total_tvl' => self::active()->sum('total_liquidity'),
            'total_providers' => self::active()->sum('provider_count'),
            'total_volume_24h' => self::active()->sum('volume_24h'),
            'total_fees_24h' => self::active()->sum('fees_collected_24h'),
            'average_apr' => self::active()->avg('apr'),
            'auto_compound_pools' => self::withAutoCompound()->count(),
            'rebalancing_pools' => self::where('rebalancing_enabled', true)->count(),
        ];

        return $stats;
    }

    /**
     * Get monthly pool data.
     */
    public static function getMonthlyData(): array
    {
        $monthlyData = [];
        
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthData = [
                'month' => $date->format('Y-m'),
                'new_pools' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'deployed_pools' => self::whereMonth('deployed_at', $date->month)
                    ->whereYear('deployed_at', $date->year)
                    ->count(),
                'total_tvl' => self::active()->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('total_liquidity'),
                'total_volume' => self::active()->whereHas('compounds', function ($query) use ($date) {
                    $query->whereMonth('compounded_at', $date->month)
                        ->whereYear('compounded_at', $date->year);
                })->withSum('compounds as volume', 'amount')
                ->get()
                ->sum('volume'),
            ];
            
            $monthlyData[] = $monthData;
        }

        return $monthlyData;
    }

    /**
     * Get top performing pools.
     */
    public static function getTopPerformingPools($limit = 10): array
    {
        return self::with(['creator', 'token', 'property'])
            ->active()
            ->public()
            ->orderBy('apr', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($pool) {
                return [
                    'id' => $pool->id,
                    'name' => $pool->name,
                    'apr' => $pool->apr,
                    'tvl' => $pool->tvl,
                    'provider_count' => $pool->provider_count,
                    'volume_24h' => $pool->volume_24h,
                    'fees_collected_24h' => $pool->fees_collected_24h,
                    'auto_compound' => $pool->auto_compound,
                    'rebalancing_enabled' => $pool->rebalancing_enabled,
                    'utilization_rate' => $pool->utilization_rate,
                    'creator' => $pool->creator,
                    'token' => $pool->token,
                    'property' => $pool->property,
                ];
            })
            ->toArray();
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pool) {
            if (!$pool->provider_count) {
                $pool->provider_count = 0;
            }
        });

        static::updating(function ($pool) {
            if ($pool->isDirty('total_liquidity') || $pool->isDirty('total_shares')) {
                    $pool->share_price = $pool->total_shares > 0 ? $pool->total_liquidity / $pool->total_shares : 1;
                }
            });
        });
    }
}
