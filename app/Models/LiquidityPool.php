<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class LiquidityPool extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'contract_address',
        'token0_address',
        'token1_address',
        'lp_token_address',
        'token0_reserve',
        'token1_reserve',
        'total_supply',
        'lp_token_price',
        'volume_24h',
        'volume_change_24h',
        'fees_24h',
        'total_fees',
        'apr',
        'fee_rate',
        'total_liquidity_providers',
        'active_liquidity_providers',
        'pool_status',
        'created_timestamp',
        'last_trade_timestamp',
        'total_trades',
        'total_swaps',
        'average_trade_size',
        'price_impact',
        'is_verified',
        'verification_status',
        'tags',
        'metadata',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'token0_reserve' => 'decimal:18',
        'token1_reserve' => 'decimal:18',
        'total_supply' => 'decimal:18',
        'lp_token_price' => 'decimal:18',
        'volume_24h' => 'decimal:18',
        'volume_change_24h' => 'decimal:8',
        'fees_24h' => 'decimal:18',
        'total_fees' => 'decimal:18',
        'apr' => 'decimal:8',
        'fee_rate' => 'decimal:5',
        'created_timestamp' => 'datetime',
        'last_trade_timestamp' => 'datetime',
        'average_trade_size' => 'decimal:18',
        'price_impact' => 'decimal:8',
        'is_verified' => 'boolean',
        'tags' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('pool_status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByToken($query, $address)
    {
        return $query->where(function($q) use ($address) {
                    $q->where('token0_address', $address)
                      ->orWhere('token1_address', $address);
                });
    }

    public function scopeByVolume($query, $min = 0, $max = null)
    {
        $query->where('volume_24h', '>=', $min);
        if ($max !== null) {
            $query->where('volume_24h', '<=', $max);
        }
        return $query;
    }

    // Accessors
    public function getFormattedToken0ReserveAttribute()
    {
        return number_format($this->token0_reserve, 8);
    }

    public function getFormattedToken1ReserveAttribute()
    {
        return number_format($this->token1_reserve, 8);
    }

    public function getFormattedTotalSupplyAttribute()
    {
        return number_format($this->total_supply, 8);
    }

    public function getFormattedLpTokenPriceAttribute()
    {
        return number_format($this->lp_token_price, 8);
    }

    public function getFormattedVolume24hAttribute()
    {
        return number_format($this->volume_24h, 8);
    }

    public function getFormattedFees24hAttribute()
    {
        return number_format($this->fees_24h, 8);
    }

    public function getFormattedTotalFeesAttribute()
    {
        return number_format($this->total_fees, 8);
    }

    public function getFormattedAprAttribute()
    {
        return number_format($this->apr, 2) . '%';
    }

    public function getFormattedFeeRateAttribute()
    {
        return number_format($this->fee_rate, 3) . '%';
    }

    public function getPoolStatusLabelAttribute()
    {
        $labels = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'paused' => 'مؤقت',
            'closed' => 'مغلق'
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

    public function getToken0UrlAttribute()
    {
        return "https://etherscan.io/token/{$this->token0_address}";
    }

    public function getToken1UrlAttribute()
    {
        return "https://etherscan.io/token/{$this->token1_address}";
    }

    public function getLpTokenUrlAttribute()
    {
        return "https://etherscan.io/token/{$this->lp_token_address}";
    }

    public function getActiveProviderRateAttribute()
    {
        if ($this->total_liquidity_providers == 0) return 0;
        return ($this->active_liquidity_providers / $this->total_liquidity_providers) * 100;
    }

    public function getFormattedActiveProviderRateAttribute()
    {
        return number_format($this->active_provider_rate, 2) . '%';
    }

    public function getTotalLiquidityValueAttribute()
    {
        // Calculate total liquidity value (would need actual token prices)
        return $this->token0_reserve + $this->token1_reserve;
    }

    public function getFormattedTotalLiquidityValueAttribute()
    {
        return number_format($this->total_liquidity_value, 8);
    }

    public function getFormattedAverageTradeSizeAttribute()
    {
        return number_format($this->average_trade_size, 8);
    }

    public function getFormattedPriceImpactAttribute()
    {
        return number_format($this->price_impact, 4) . '%';
    }

    public function getDaysSinceLastTradeAttribute()
    {
        return $this->last_trade_timestamp ? 
               $this->last_trade_timestamp->diffInDays(now()) : 
               0;
    }

    public function getPoolAgeAttribute()
    {
        return $this->created_timestamp ? 
               $this->created_timestamp->diffInDays(now()) : 
               0;
    }

    public function getFormattedPoolAgeAttribute()
    {
        return $this->pool_age . ' يوم';
    }

    // Methods
    public function isVerified()
    {
        return $this->is_verified;
    }

    public function isActive()
    {
        return $this->pool_status === 'active';
    }

    public function getTokenPrice($tokenAddress)
    {
        if ($tokenAddress === $this->token0_address) {
            return $this->token1_reserve / $this->token0_reserve;
        } elseif ($tokenAddress === $this->token1_address) {
            return $this->token0_reserve / $this->token1_reserve;
        }
        return 0;
    }

    public function calculateSwapOutput($inputAmount, $inputToken)
    {
        if ($inputToken === $this->token0_address) {
            $inputReserve = $this->token0_reserve;
            $outputReserve = $this->token1_reserve;
        } else {
            $inputReserve = $this->token1_reserve;
            $outputReserve = $this->token0_reserve;
        }

        // Apply fee (0.3% by default)
        $feeRate = $this->fee_rate / 100;
        $inputAmountWithFee = $inputAmount * (1 - $feeRate);

        // Calculate output using constant product formula
        $outputAmount = ($inputAmountWithFee * $outputReserve) / ($inputReserve + $inputAmountWithFee);

        return $outputAmount;
    }

    public function calculatePriceImpact($inputAmount, $inputToken)
    {
        if ($inputToken === $this->token0_address) {
            $inputReserve = $this->token0_reserve;
        } else {
            $inputReserve = $this->token1_reserve;
        }

        $priceImpact = ($inputAmount / ($inputReserve + $inputAmount)) * 100;
        return $priceImpact;
    }

    public function calculateLiquidityShare($lpTokenAmount)
    {
        if ($this->total_supply == 0) return 0;
        return ($lpTokenAmount / $this->total_supply) * 100;
    }

    public function calculateLiquidityValue($lpTokenAmount)
    {
        $share = $this->calculateLiquidityShare($lpTokenAmount) / 100;
        return $this->total_liquidity_value * $share;
    }

    public function calculateApr()
    {
        if ($this->total_liquidity_value == 0) return 0;
        
        // Annualized fees based on 24h volume
        $annualFees = $this->fees_24h * 365;
        return ($annualFees / $this->total_liquidity_value) * 100;
    }

    public function updateApr()
    {
        $this->apr = $this->calculateApr();
        $this->save();
    }

    public function getPoolMetrics()
    {
        return [
            'total_liquidity' => $this->total_liquidity_value,
            'volume_24h' => $this->volume_24h,
            'fees_24h' => $this->fees_24h,
            'apr' => $this->apr,
            'total_providers' => $this->total_liquidity_providers,
            'active_providers' => $this->active_liquidity_providers,
            'total_trades' => $this->total_trades,
            'average_trade_size' => $this->average_trade_size,
            'price_impact' => $this->price_impact
        ];
    }

    // Relationships
    public function smartContract(): BelongsTo
    {
        return $this->belongsTo(SmartContract::class, 'contract_address', 'address');
    }

    public function token0(): BelongsTo
    {
        return $this->belongsTo(Token::class, 'token0_address', 'address');
    }

    public function token1(): BelongsTo
    {
        return $this->belongsTo(Token::class, 'token1_address', 'address');
    }

    public function lpToken(): BelongsTo
    {
        return $this->belongsTo(Token::class, 'lp_token_address', 'address');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(LiquidityPosition::class, 'pool_id');
    }

    public function trades(): HasMany
    {
        return $this->hasMany(LiquidityTrade::class, 'pool_id');
    }

    public function swaps(): HasMany
    {
        return $this->hasMany(LiquiditySwap::class, 'pool_id');
    }

    // Static Methods
    public static function getStats()
    {
        return [
            'total_pools' => self::count(),
            'active_pools' => self::active()->count(),
            'verified_pools' => self::verified()->count(),
            'total_liquidity' => self::sum('token0_reserve') + self::sum('token1_reserve'),
            'total_volume_24h' => self::sum('volume_24h'),
            'total_fees_24h' => self::sum('fees_24h'),
            'total_providers' => self::sum('total_liquidity_providers'),
            'average_apr' => self::avg('apr'),
            'pools_today' => self::whereDate('created_at', today())->count(),
            'pools_this_week' => self::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'pools_this_month' => self::whereMonth('created_at', now()->month)->count(),
        ];
    }

    public static function getTopPools($limit = 20)
    {
        return self::orderBy('volume_24h', 'desc')
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
                   ->orderBy('volume_24h', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getPoolsByToken($tokenAddress, $limit = 50)
    {
        return self::byToken($tokenAddress)
                   ->orderBy('volume_24h', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getPoolsByVolume($min = 0, $max = null, $limit = 50)
    {
        return self::byVolume($min, $max)
                   ->orderBy('volume_24h', 'desc')
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
                ->orderBy('volume_24h', 'desc')
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

    public static function getVolumeDistribution()
    {
        return self::selectRaw('
                CASE 
                    WHEN volume_24h < 1000 THEN "< 1K"
                    WHEN volume_24h < 10000 THEN "1K-10K"
                    WHEN volume_24h < 100000 THEN "10K-100K"
                    WHEN volume_24h < 1000000 THEN "100K-1M"
                    WHEN volume_24h < 10000000 THEN "1M-10M"
                    ELSE "> 10M"
                END as volume_range,
                COUNT(*) as count
            ')
            ->groupBy('volume_range')
            ->orderBy('count', 'desc')
            ->get();
    }

    public static function getLiquidityStats()
    {
        return [
            'total_liquidity' => self::sum('token0_reserve') + self::sum('token1_reserve'),
            'total_volume_24h' => self::sum('volume_24h'),
            'total_fees_24h' => self::sum('fees_24h'),
            'total_providers' => self::sum('total_liquidity_providers'),
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
            'Name', 'Contract Address', 'Token0', 'Token1', 'Total Liquidity', 
            'Volume 24h', 'Fees 24h', 'APR', 'Total Providers', 'Active Providers', 
            'Status', 'Verified', 'Created At'
        ];

        $rows = $pools->map(function ($pool) {
            return [
                $pool->name,
                $pool->contract_address,
                $pool->token0_address,
                $pool->token1_address,
                $pool->formatted_total_liquidity_value,
                $pool->formatted_volume_24h,
                $pool->formatted_fees_24h,
                $pool->formatted_apr,
                $pool->total_liquidity_providers,
                $pool->active_liquidity_providers,
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
        
        if (empty($this->token0_address)) {
            $errors[] = 'Token0 address is required';
        }
        
        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $this->token0_address)) {
            $errors[] = 'Invalid token0 address format';
        }
        
        if (empty($this->token1_address)) {
            $errors[] = 'Token1 address is required';
        }
        
        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $this->token1_address)) {
            $errors[] = 'Invalid token1 address format';
        }
        
        if ($this->token0_address === $this->token1_address) {
            $errors[] = 'Token0 and Token1 cannot be the same';
        }
        
        if ($this->fee_rate < 0 || $this->fee_rate > 10) {
            $errors[] = 'Fee rate must be between 0 and 10';
        }
        
        if ($this->apr < 0) {
            $errors[] = 'APR must be positive';
        }
        
        return $errors;
    }

    // Pool Operations
    public function addLiquidity($user, $amount0, $amount1)
    {
        // Calculate LP tokens to mint
        if ($this->total_supply == 0) {
            $lpTokens = sqrt($amount0 * $amount1);
        } else {
            $lpTokens = min(
                ($amount0 * $this->total_supply) / $this->token0_reserve,
                ($amount1 * $this->total_supply) / $this->token1_reserve
            );
        }

        // Create liquidity position
        $position = LiquidityPosition::create([
            'pool_id' => $this->id,
            'user_address' => $user,
            'amount0' => $amount0,
            'amount1' => $amount1,
            'lp_tokens' => $lpTokens,
            'added_at' => now(),
            'status' => 'active'
        ]);

        // Update pool stats
        $this->token0_reserve += $amount0;
        $this->token1_reserve += $amount1;
        $this->total_supply += $lpTokens;
        $this->total_liquidity_providers++;
        $this->save();

        return $position;
    }

    public function removeLiquidity($positionId, $user, $lpTokenAmount)
    {
        $position = LiquidityPosition::find($positionId);
        
        if (!$position || $position->user_address !== $user) {
            throw new \Exception('Invalid position');
        }
        
        if ($position->status !== 'active') {
            throw new \Exception('Position is not active');
        }

        // Calculate amounts to return
        $amount0 = ($lpTokenAmount * $this->token0_reserve) / $this->total_supply;
        $amount1 = ($lpTokenAmount * $this->token1_reserve) / $this->total_supply;

        // Update position
        $position->status = 'removed';
        $position->removed_at = now();
        $position->removed_amount0 = $amount0;
        $position->removed_amount1 = $amount1;
        $position->save();

        // Update pool stats
        $this->token0_reserve -= $amount0;
        $this->token1_reserve -= $amount1;
        $this->total_supply -= $lpTokenAmount;
        $this->save();

        return $position;
    }

    public function swap($user, $inputAmount, $inputToken, $outputToken)
    {
        if (!in_array($inputToken, [$this->token0_address, $this->token1_address]) ||
            !in_array($outputToken, [$this->token0_address, $this->token1_address])) {
            throw new \Exception('Invalid tokens for this pool');
        }

        if ($inputToken === $outputToken) {
            throw new \Exception('Input and output tokens cannot be the same');
        }

        // Calculate output and price impact
        $outputAmount = $this->calculateSwapOutput($inputAmount, $inputToken);
        $priceImpact = $this->calculatePriceImpact($inputAmount, $inputToken);
        $feeAmount = $inputAmount * ($this->fee_rate / 100);

        // Create swap record
        $swap = LiquiditySwap::create([
            'pool_id' => $this->id,
            'user_address' => $user,
            'input_token' => $inputToken,
            'output_token' => $outputToken,
            'input_amount' => $inputAmount,
            'output_amount' => $outputAmount,
            'fee_amount' => $feeAmount,
            'price_impact' => $priceImpact,
            'swapped_at' => now()
        ]);

        // Update pool reserves
        if ($inputToken === $this->token0_address) {
            $this->token0_reserve += $inputAmount;
            $this->token1_reserve -= $outputAmount;
        } else {
            $this->token1_reserve += $inputAmount;
            $this->token0_reserve -= $outputAmount;
        }

        $this->volume_24h += $inputAmount;
        $this->fees_24h += $feeAmount;
        $this->total_fees += $feeAmount;
        $this->total_trades++;
        $this->last_trade_timestamp = now();
        $this->save();

        return $swap;
    }

    public function getUserPosition($userAddress)
    {
        return LiquidityPosition::where('pool_id', $this->id)
                               ->where('user_address', $userAddress)
                               ->where('status', 'active')
                               ->first();
    }

    public function getUserStats($userAddress)
    {
        $position = $this->getUserPosition($userAddress);
        
        if (!$position) {
            return [
                'position' => null,
                'lp_tokens' => 0,
                'share_percentage' => 0,
                'liquidity_value' => 0,
                'earned_fees' => 0
            ];
        }

        $sharePercentage = $this->calculateLiquidityShare($position->lp_tokens);
        $liquidityValue = $this->calculateLiquidityValue($position->lp_tokens);

        return [
            'position' => $position,
            'lp_tokens' => $position->lp_tokens,
            'share_percentage' => $sharePercentage,
            'liquidity_value' => $liquidityValue,
            'earned_fees' => $liquidityValue * ($this->apr / 100) / 365 // Daily earnings
        ];
    }
}
