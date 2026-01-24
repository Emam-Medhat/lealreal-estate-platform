<?php

namespace App\Models\Defi;

use App\Models\User;
use App\Models\Metaverse\MetaverseProperty;
use App\Models\Defi\TokenDistribution;
use App\Models\Defi\DefiCollateral;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Support\Facades\DB;

class PropertyToken extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'property_id',
        'owner_id',
        'token_name',
        'token_symbol',
        'total_supply',
        'distributed_supply',
        'price_per_token',
        'currency',
        'blockchain',
        'smart_contract_address',
        'token_standard',
        'decimals',
        'minting_enabled',
        'burning_enabled',
        'transfer_fee',
        'royalty_percentage',
        'metadata',
        'status',
        'minted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_supply' => 'decimal:18',
        'distributed_supply' => 'decimal:18',
        'price_per_token' => 'decimal:8',
        'transfer_fee' => 'decimal:5',
        'royalty_percentage' => 'decimal:5',
        'minting_enabled' => 'boolean',
        'burning_enabled' => 'boolean',
        'fractional_ownership_enabled' => 'boolean',
        'investment_enabled' => 'boolean',
        'is_public' => 'boolean',
        'metadata' => AsArrayObject::class,
        'minted_at' => 'datetime',
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
     * Get the property associated with the token.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(MetaverseProperty::class, 'property_id');
    }

    /**
     * Get the owner of the token.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the distributions for the token.
     */
    public function distributions(): HasMany
    {
        return $this->hasMany(TokenDistribution::class);
    }

    /**
     * Get the collateral for the token.
     */
    public function collateral(): HasOne
    {
        return $this->hasOne(DefiCollateral::class);
    }

    /**
     * Get the fractional ownerships for the token.
     */
    public function fractionalOwnerships(): HasMany
    {
        return $this->hasMany(FractionalOwnership::class);
    }

    /**
     * Get the staking positions for the token.
     */
    public function stakingPositions(): HasMany
    {
        return $this->hasMany(PropertyStaking::class);
    }

    /**
     * Get the liquidity pools for the token.
     */
    public function liquidityPools(): HasMany
    {
        return $this->hasMany(PropertyLiquidityPool::class);
    }

    /**
     * Get the yields for the token.
     */
    public function yields(): HasMany
    {
        return $this->hasMany(PropertyYield::class);
    }

    /**
     * Get the DAOs for the token.
     */
    public function daos(): HasMany
    {
        return $this->hasMany(PropertyDao::class);
    }

    /**
     * Get the payments for the token.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(CryptoPropertyPayment::class);
    }

    /**
     * Scope a query to only include active tokens.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include minted tokens.
     */
    public function scopeMinted($query)
    {
        return $query->whereNotNull('minted_at');
    }

    /**
     * Scope a query to only include public tokens.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include tokens by blockchain.
     */
    public function scopeByBlockchain($query, $blockchain)
    {
        return $query->where('blockchain', $blockchain);
    }

    /**
     * Scope a query to only include tokens by token standard.
     */
    public function scopeByStandard($query, $standard)
    {
        return $query->where('token_standard', $standard);
    }

    /**
     * Scope a query to only include tokens with fractional ownership enabled.
     */
    public function scopeWithFractionalOwnership($query)
    {
        return $query->where('fractional_ownership_enabled', true);
    }

    /**
     * Scope a query to only include tokens with investment enabled.
     */
    public function scopeWithInvestment($query)
    {
        return $query->where('investment_enabled', true);
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
            'burned' => 'محروق',
            'deleted' => 'محذوف',
            default => $this->status,
        };
    }

    /**
     * Get the blockchain text attribute.
     */
    public function getBlockchainTextAttribute(): string
    {
        return match($this->blockchain) {
            'ethereum' => 'إيثيريوم',
            'polygon' => 'بوليغون',
            'binance_smart_chain' => 'سلسلة بينانس الذكية',
            'solana' => 'سولانا',
            'avalanche' => 'أفالانش',
            'bitcoin' => 'بيتكوين',
            'cardano' => 'كاردانو',
            'polkadot' => 'بولكادوت',
            default => $this->blockchain,
        };
    }

    /**
     * Get the token standard text attribute.
     */
    public function getTokenStandardTextAttribute(): string
    {
        return match($this->token_standard) {
            'ERC-20' => 'ERC-20',
            'ERC-721' => 'ERC-721',
            'ERC-1155' => 'ERC-1155',
            'BEP-20' => 'BEP-20',
            'BEP-721' => 'BEP-721',
            'SPL' => 'SPL',
            'NATIVE' => 'أصلي',
            default => $this->token_standard,
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
            'USDC' => 'يوس دي سي',
            'DAI' => 'داي',
            'MATIC' => 'ماتيك',
            'BNB' => 'بي إن بي',
            'SOL' => 'سول',
            'ADA' => 'أدا',
            default => $this->currency,
        };
    }

    /**
     * Get the available supply.
     */
    public function getAvailableSupplyAttribute(): float
    {
        return $this->total_supply - $this->distributed_supply;
    }

    /**
     * Get the market cap.
     */
    public function getMarketCapAttribute(): float
    {
        return $this->total_supply * $this->price_per_token;
    }

    /**
     * Get the distributed market cap.
     */
    public function getDistributedMarketCapAttribute(): float
    {
        return $this->distributed_supply * $this->price_per_token;
    }

    /**
     * Get the available market cap.
     */
    public function getAvailableMarketCapAttribute(): float
    {
        return $this->available_supply * $this->price_per_token;
    }

    /**
     * Get the distribution percentage.
     */
    public function getDistributionPercentageAttribute(): float
    {
        return $this->total_supply > 0 ? ($this->distributed_supply / $this->total_supply) * 100 : 0;
    }

    /**
     * Get the current value.
     */
    public function getCurrentValueAttribute(): float
    {
        return $this->distributed_supply * $this->price_per_token;
    }

    /**
     * Get the holders count.
     */
    public function getHoldersCountAttribute(): int
    {
        return $this->distributions()
            ->where('status', 'completed')
            ->distinct('to_address')
            ->count();
    }

    /**
     * Get the transactions count.
     */
    public function getTransactionsCountAttribute(): int
    {
        return $this->distributions()->count();
    }

    /**
     * Get the total volume.
     */
    public function getTotalVolumeAttribute(): float
    {
        return $this->distributions()
            ->where('status', 'completed')
            ->sum('amount');
    }

    /**
     * Get the 24h volume.
     */
    public function getVolume24hAttribute(): float
    {
        return $this->distributions()
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDay())
            ->sum('amount');
    }

    /**
     * Get the 7d volume.
     */
    public function getVolume7dAttribute(): float
    {
        return $this->distributions()
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subWeek())
            ->sum('amount');
    }

    /**
     * Get the 30d volume.
     */
    public function getVolume30dAttribute(): float
    {
        return $this->distributions()
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonth())
            ->sum('amount');
    }

    /**
     * Get the total fees collected.
     */
    public function getTotalFeesCollectedAttribute(): float
    {
        return $this->distributions()
            ->where('status', 'completed')
            ->sum('fee');
    }

    /**
     * Get the 24h fees collected.
     */
    public function getFees24hAttribute(): float
    {
        return $this->distributions()
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDay())
            ->sum('fee');
    }

    /**
     * Get the 7d fees collected.
     */
    public function getFees7dAttribute(): float
    {
        return $this->distributions()
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subWeek())
            ->sum('fee');
    }

    /**
     * Get the 30d fees collected.
     */
    public function getFees30dAttribute(): float
    {
        return $this->distributions()
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonth())
            ->sum('fee');
    }

    /**
     * Get the price change percentage (24h).
     */
    public function getPriceChange24hAttribute(): float
    {
        // This would calculate based on historical price data
        // For now, return a mock calculation
        return rand(-10, 10);
    }

    /**
     * Get the price change percentage (7d).
     */
    public function getPriceChange7dAttribute(): float
    {
        // This would calculate based on historical price data
        // For now, return a mock calculation
        return rand(-20, 20);
    }

    /**
     * Get the price change percentage (30d).
     */
    public function getPriceChange30dAttribute(): float
    {
        // This would calculate based on historical price data
        // For now, return a mock calculation
        return rand(-30, 30);
    }

    /**
     * Check if the token is minted.
     */
    public function isMinted(): bool
    {
        return !is_null($this->minted_at);
    }

    /**
     * Check if the token can be minted.
     */
    public function canBeMinted(): bool
    {
        return $this->status === 'pending' && !$this->isMinted();
    }

    /**
     * Check if the token can be burned.
     */
    public function canBeBurned(): bool
    {
        return $this->burning_enabled && $this->isMinted();
    }

    /**
     * Check if the token can be transferred.
     */
    public function canBeTransferred(): bool
    {
        return $this->isMinted() && $this->status === 'active';
    }

    /**
     * Check if the token supports fractional ownership.
     */
    public function supportsFractionalOwnership(): bool
    {
        return $this->fractional_ownership_enabled;
    }

    /**
     * Check if the token supports investment.
     */
    public function supportsInvestment(): bool
    {
        return $this->investment_enabled;
    }

    /**
     * Calculate the transfer fee amount.
     */
    public function calculateTransferFee($amount): float
    {
        return $amount * ($this->transfer_fee / 100);
    }

    /**
     * Calculate the royalty amount.
     */
    public function calculateRoyalty($amount): float
    {
        return $amount * ($this->royalty_percentage / 100);
    }

    /**
     * Get the top holders.
     */
    public function getTopHolders($limit = 10): array
    {
        return $this->distributions()
            ->selectRaw('to_address, SUM(amount) as balance')
            ->where('status', 'completed')
            ->groupBy('to_address')
            ->orderBy('balance', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get the holder distribution.
     */
    public function getHolderDistribution(): array
    {
        $distribution = [
            'whales' => 0, // > 1% of supply
            'dolphins' => 0, // 0.1% - 1% of supply
            'fish' => 0, // < 0.1% of supply
        ];

        $holders = $this->distributions()
            ->selectRaw('to_address, SUM(amount) as balance')
            ->where('status', 'completed')
            ->groupBy('to_address')
            ->get();

        foreach ($holders as $holder) {
            $percentage = ($holder->balance / $this->total_supply) * 100;
            
            if ($percentage > 1) {
                $distribution['whales']++;
            } elseif ($percentage > 0.1) {
                $distribution['dolphins']++;
            } else {
                $distribution['fish']++;
            }
        }

        return $distribution;
    }

    /**
     * Mint the token.
     */
    public function mint($smartContractAddress = null): bool
    {
        if (!$this->canBeMinted()) {
            return false;
        }

        return $this->update([
            'status' => 'active',
            'smart_contract_address' => $smartContractAddress,
            'minted_at' => now(),
        ]);
    }

    /**
     * Burn tokens.
     */
    public function burn($amount): bool
    {
        if (!$this->canBeBurned()) {
            return false;
        }

        if ($amount > $this->distributed_supply) {
            return false;
        }

        return $this->update([
            'distributed_supply' => $this->distributed_supply - $amount,
            'total_supply' => $this->total_supply - $amount,
        ]);
    }

    /**
     * Update price.
     */
    public function updatePrice($newPrice): bool
    {
        return $this->update([
            'price_per_token' => $newPrice,
        ]);
    }

    /**
     * Get token statistics.
     */
    public static function getStatistics(): array
    {
        $stats = [
            'total_tokens' => self::count(),
            'active_tokens' => self::active()->count(),
            'minted_tokens' => self::minted()->count(),
            'total_supply' => self::sum('total_supply'),
            'distributed_supply' => self::sum('distributed_supply'),
            'total_market_cap' => self::sum('total_supply') * self::avg('price_per_token'),
            'total_volume_24h' => self::withSum('distributions as volume_24h', function ($query) {
                $query->where('status', 'completed')->where('created_at', '>=', now()->subDay());
            })->get()->sum('volume_24h'),
            'total_holders' => self::withCount('distributions as holders_count')->get()->sum('holders_count'),
            'blockchain_distribution' => self::groupBy('blockchain')->map->count(),
            'token_standard_distribution' => self::groupBy('token_standard')->map->count(),
        ];

        return $stats;
    }

    /**
     * Get monthly token data.
     */
    public static function getMonthlyData(): array
    {
        $monthlyData = [];
        
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthData = [
                'month' => $date->format('Y-m'),
                'new_tokens' => self::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'minted_tokens' => self::whereMonth('minted_at', $date->month)
                    ->whereYear('minted_at', $date->year)
                    ->count(),
                'total_volume' => self::whereHas('distributions', function ($query) use ($date) {
                    $query->where('status', 'completed')
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year);
                })->withSum('distributions as volume', 'amount')
                ->get()
                ->sum('volume'),
            ];
            
            $monthlyData[] = $monthData;
        }

        return $monthlyData;
    }

    /**
     * Get top performing tokens.
     */
    public static function getTopPerformingTokens($limit = 10): array
    {
        return self::with(['property', 'owner'])
            ->active()
            ->public()
            ->orderBy('distributed_supply', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($token) {
                return [
                    'id' => $token->id,
                    'token_name' => $token->token_name,
                    'token_symbol' => $token->token_symbol,
                    'price_per_token' => $token->price_per_token,
                    'distributed_supply' => $token->distributed_supply,
                    'market_cap' => $token->market_cap,
                    'holders_count' => $token->holders_count,
                    'volume_24h' => $token->volume_24h,
                    'price_change_24h' => $token->price_change_24h,
                    'property' => $token->property,
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

        static::creating(function ($token) {
            if (!$token->token_symbol) {
                $token->token_symbol = strtoupper(substr($token->token_name, 0, 8));
            }
            
            if (!$token->decimals) {
                $token->decimals = match($token->token_standard) {
                    'ERC-20', 'BEP-20' => 18,
                    'ERC-721', 'BEP-721' => 0,
                    'ERC-1155' => 18,
                    'SPL' => 9,
                    default => 18,
                };
            }
        });

        static::updating(function ($token) {
            if ($token->isDirty('price_per_token')) {
                // This would trigger price change notifications
                \Log::info("Token {$token->id} price updated to {$token->price_per_token}");
            }
        });
    }
}
