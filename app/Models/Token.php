<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Token extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'symbol',
        'address',
        'contract_address',
        'decimals',
        'total_supply',
        'circulating_supply',
        'price',
        'market_cap',
        'volume_24h',
        'price_change_24h',
        'price_change_7d',
        'price_change_30d',
        'all_time_high',
        'all_time_low',
        'holder_count',
        'transaction_count',
        'last_transaction_timestamp',
        'token_type',
        'standard',
        'description',
        'website',
        'twitter',
        'telegram',
        'discord',
        'logo_url',
        'is_verified',
        'verification_status',
        'tags',
        'metadata',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'total_supply' => 'decimal:18',
        'circulating_supply' => 'decimal:18',
        'price' => 'decimal:18',
        'market_cap' => 'decimal:18',
        'volume_24h' => 'decimal:18',
        'price_change_24h' => 'decimal:8',
        'price_change_7d' => 'decimal:8',
        'price_change_30d' => 'decimal:8',
        'all_time_high' => 'decimal:18',
        'all_time_low' => 'decimal:18',
        'last_transaction_timestamp' => 'datetime',
        'is_verified' => 'boolean',
        'tags' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scopes
    public function scopeByStandard($query, $standard)
    {
        return $query->where('standard', $standard);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('token_type', $type);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeActive($query)
    {
        return $query->where('circulating_supply', '>', 0);
    }

    public function scopeTopByMarketCap($query, $limit = 50)
    {
        return $query->orderBy('market_cap', 'desc')->limit($limit);
    }

    public function scopeTopByVolume($query, $limit = 50)
    {
        return $query->orderBy('volume_24h', 'desc')->limit($limit);
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 8);
    }

    public function getFormattedMarketCapAttribute()
    {
        return number_format($this->market_cap, 2);
    }

    public function getFormattedVolume24hAttribute()
    {
        return number_format($this->volume_24h, 2);
    }

    public function getFormattedTotalSupplyAttribute()
    {
        return number_format($this->total_supply, $this->decimals);
    }

    public function getFormattedCirculatingSupplyAttribute()
    {
        return number_format($this->circulating_supply, $this->decimals);
    }

    public function getPriceChange24hPercentAttribute()
    {
        return $this->price_change_24h ? number_format($this->price_change_24h, 2) . '%' : '0%';
    }

    public function getPriceChange7dPercentAttribute()
    {
        return $this->price_change_7d ? number_format($this->price_change_7d, 2) . '%' : '0%';
    }

    public function getPriceChange30dPercentAttribute()
    {
        return $this->price_change_30d ? number_format($this->price_change_30d, 2) . '%' : '0%';
    }

    public function getStandardLabelAttribute()
    {
        $labels = [
            'erc20' => 'ERC-20',
            'erc721' => 'ERC-721',
            'erc1155' => 'ERC-1155',
            'bep20' => 'BEP-20',
            'custom' => 'Custom'
        ];
        return $labels[$this->standard] ?? $this->standard;
    }

    public function getTokenTypeLabelAttribute()
    {
        $labels = [
            'utility' => 'Utility Token',
            'security' => 'Security Token',
            'governance' => 'Governance Token',
            'stablecoin' => 'Stablecoin',
            'defi' => 'DeFi Token',
            'gaming' => 'Gaming Token',
            'nft' => 'NFT Token',
            'metaverse' => 'Metaverse Token'
        ];
        return $labels[$this->token_type] ?? $this->token_type;
    }

    public function getTokenUrlAttribute()
    {
        return "https://etherscan.io/token/{$this->address}";
    }

    public function getCirculationRateAttribute()
    {
        if ($this->total_supply == 0) return 0;
        return ($this->circulating_supply / $this->total_supply) * 100;
    }

    public function getFormattedCirculationRateAttribute()
    {
        return number_format($this->circulation_rate, 2) . '%';
    }

    public function getAthDistanceAttribute()
    {
        if ($this->all_time_high == 0) return 0;
        return (($this->all_time_high - $this->price) / $this->all_time_high) * 100;
    }

    public function getFormattedAthDistanceAttribute()
    {
        return number_format($this->ath_distance, 2) . '%';
    }

    public function getAtlDistanceAttribute()
    {
        if ($this->all_time_low == 0) return 0;
        return (($this->price - $this->all_time_low) / $this->all_time_low) * 100;
    }

    public function getFormattedAtlDistanceAttribute()
    {
        return number_format($this->atl_distance, 2) . '%';
    }

    // Methods
    public function isVerified()
    {
        return $this->is_verified;
    }

    public function isErc20()
    {
        return $this->standard === 'erc20';
    }

    public function isErc721()
    {
        return $this->standard === 'erc721';
    }

    public function isErc1155()
    {
        return $this->standard === 'erc1155';
    }

    public function isStablecoin()
    {
        return $this->token_type === 'stablecoin';
    }

    public function isDefiToken()
    {
        return $this->token_type === 'defi';
    }

    public function isGovernanceToken()
    {
        return $this->token_type === 'governance';
    }

    public function getDaysSinceLastTransaction()
    {
        return $this->last_transaction_timestamp ? 
               $this->last_transaction_timestamp->diffInDays(now()) : 
               0;
    }

    public function getAverageTransactionValue()
    {
        if ($this->transaction_count == 0) return 0;
        return $this->volume_24h / $this->transaction_count;
    }

    public function getFormattedAverageTransactionValueAttribute()
    {
        return number_format($this->average_transaction_value, 8);
    }

    public function calculateMarketCap()
    {
        return $this->price * $this->circulating_supply;
    }

    public function updateMarketCap()
    {
        $this->market_cap = $this->calculateMarketCap();
        $this->save();
    }

    public function getHolderDistribution()
    {
        // This would get actual holder distribution from blockchain
        return [
            'top_10_holders' => 0,
            'top_100_holders' => 0,
            'whale_percentage' => 0,
            'retail_percentage' => 0
        ];
    }

    public function getLiquidityInfo()
    {
        // This would get actual liquidity info from DEXs
        return [
            'total_liquidity' => 0,
            'liquidity_pairs' => 0,
            'top_exchanges' => [],
            'liquidity_score' => 0
        ];
    }

    // Relationships
    public function smartContract(): BelongsTo
    {
        return $this->belongsTo(SmartContract::class, 'contract_address', 'address');
    }

    public function tokenBalances(): HasMany
    {
        return $this->hasMany(TokenBalance::class, 'token_address', 'address');
    }

    public function tokenTransactions(): HasMany
    {
        return $this->hasMany(TokenTransaction::class, 'token_address', 'address');
    }

    public function tokenPrices(): HasMany
    {
        return $this->hasMany(TokenPrice::class, 'token_address', 'address');
    }

    // Static Methods
    public static function getStats()
    {
        return [
            'total_tokens' => self::count(),
            'verified_tokens' => self::verified()->count(),
            'erc20_tokens' => self::byStandard('erc20')->count(),
            'erc721_tokens' => self::byStandard('erc721')->count(),
            'erc1155_tokens' => self::byStandard('erc1155')->count(),
            'active_tokens' => self::active()->count(),
            'total_market_cap' => self::sum('market_cap'),
            'total_volume_24h' => self::sum('volume_24h'),
            'total_holders' => self::sum('holder_count'),
            'tokens_today' => self::whereDate('created_at', today())->count(),
            'tokens_this_week' => self::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'tokens_this_month' => self::whereMonth('created_at', now()->month)->count(),
        ];
    }

    public static function getTopTokens($limit = 50)
    {
        return self::orderBy('market_cap', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getTopGainers($limit = 20)
    {
        return self::where('price_change_24h', '>', 0)
                   ->orderBy('price_change_24h', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getTopLosers($limit = 20)
    {
        return self::where('price_change_24h', '<', 0)
                   ->orderBy('price_change_24h', 'asc')
                   ->limit($limit)
                   ->get();
    }

    public static function getMostActive($limit = 20)
    {
        return self::orderBy('volume_24h', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getNewTokens($limit = 20)
    {
        return self::orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getVerifiedTokens($limit = 50)
    {
        return self::verified()
                   ->orderBy('market_cap', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getTokensByType($type, $limit = 50)
    {
        return self::byType($type)
                   ->orderBy('market_cap', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function searchTokens($query, $limit = 50)
    {
        return self::where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('symbol', 'like', "%{$query}%")
                      ->orWhere('address', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->orderBy('market_cap', 'desc')
                ->limit($limit)
                ->get();
    }

    public static function getDailyTokenCount($days = 30)
    {
        return self::where('created_at', '>=', now()->subDays($days))
                   ->groupBy('date')
                   ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                   ->orderBy('date', 'desc')
                   ->get();
    }

    public static function getTokenTypeDistribution()
    {
        return self::groupBy('token_type')
                   ->selectRaw('token_type, COUNT(*) as count')
                   ->orderBy('count', 'desc')
                   ->get();
    }

    public static function getStandardDistribution()
    {
        return self::groupBy('standard')
                   ->selectRaw('standard, COUNT(*) as count')
                   ->orderBy('count', 'desc')
                   ->get();
    }

    public static function getMarketStats()
    {
        return [
            'total_market_cap' => self::sum('market_cap'),
            'total_volume_24h' => self::sum('volume_24h'),
            'market_cap_change_24h' => self::avg('price_change_24h'),
            'volume_change_24h' => 0, // Would calculate from historical data
            'dominance_percentage' => 0, // Would calculate from Bitcoin dominance
            'fear_greed_index' => 50, // Would get from external API
            'active_tokens' => self::active()->count(),
            'gainers_count' => self::where('price_change_24h', '>', 0)->count(),
            'losers_count' => self::where('price_change_24h', '<', 0)->count(),
        ];
    }

    // Export Methods
    public static function exportToCsv($tokens)
    {
        $headers = [
            'Name', 'Symbol', 'Address', 'Standard', 'Type', 'Price', 'Market Cap', 
            'Volume 24h', 'Price Change 24h', 'Holders', 'Verified', 'Created At'
        ];

        $rows = $tokens->map(function ($token) {
            return [
                $token->name,
                $token->symbol,
                $token->address,
                $token->standard_label,
                $token->token_type_label,
                $token->formatted_price,
                $token->formatted_market_cap,
                $token->formatted_volume_24h,
                $token->price_change_24h_percent,
                $token->holder_count,
                $token->is_verified ? 'Yes' : 'No',
                $token->created_at
            ];
        });

        return collect([$headers])->concat($rows);
    }

    // Validation Methods
    public function validateToken()
    {
        $errors = [];
        
        if (empty($this->name)) {
            $errors[] = 'Token name is required';
        }
        
        if (empty($this->symbol)) {
            $errors[] = 'Token symbol is required';
        }
        
        if (empty($this->address)) {
            $errors[] = 'Token address is required';
        }
        
        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $this->address)) {
            $errors[] = 'Invalid token address format';
        }
        
        if ($this->decimals < 0 || $this->decimals > 18) {
            $errors[] = 'Decimals must be between 0 and 18';
        }
        
        if ($this->total_supply < 0) {
            $errors[] = 'Total supply must be positive';
        }
        
        if ($this->circulating_supply < 0) {
            $errors[] = 'Circulating supply must be positive';
        }
        
        if ($this->circulating_supply > $this->total_supply) {
            $errors[] = 'Circulating supply cannot exceed total supply';
        }
        
        return $errors;
    }

    // Price Methods
    public function updatePrice($newPrice)
    {
        $oldPrice = $this->price;
        $this->price = $newPrice;
        
        // Update price changes
        if ($oldPrice > 0) {
            $this->price_change_24h = (($newPrice - $oldPrice) / $oldPrice) * 100;
        }
        
        // Update market cap
        $this->updateMarketCap();
        
        // Update all-time high/low
        if ($newPrice > $this->all_time_high) {
            $this->all_time_high = $newPrice;
        }
        
        if ($this->all_time_low == 0 || $newPrice < $this->all_time_low) {
            $this->all_time_low = $newPrice;
        }
        
        $this->save();
        
        return $this;
    }

    public function recordPrice($price, $timestamp = null)
    {
        TokenPrice::create([
            'token_address' => $this->address,
            'price' => $price,
            'timestamp' => $timestamp ?: now(),
            'volume_24h' => $this->volume_24h,
            'market_cap' => $this->market_cap
        ]);
        
        return $this->updatePrice($price);
    }

    public function getPriceHistory($period = '24h')
    {
        $periods = [
            '1h' => 1,
            '24h' => 24,
            '7d' => 24 * 7,
            '30d' => 24 * 30,
            '90d' => 24 * 90,
            '1y' => 24 * 365
        ];
        
        $hours = $periods[$period] ?? 24;
        
        return TokenPrice::where('token_address', $this->address)
                        ->where('timestamp', '>=', now()->subHours($hours))
                        ->orderBy('timestamp', 'asc')
                        ->get();
    }

    public function getChartPoints($period = '24h', $interval = '1h')
    {
        $history = $this->getPriceHistory($period);
        
        return $history->map(function ($point) {
            return [
                'timestamp' => $point->timestamp->toISOString(),
                'price' => $point->price,
                'volume' => $point->volume_24h,
                'market_cap' => $point->market_cap
            ];
        });
    }

    // Holder Methods
    public function getTopHolders($limit = 100)
    {
        return TokenBalance::where('token_address', $this->address)
                           ->orderBy('balance', 'desc')
                           ->limit($limit)
                           ->get();
    }

    public function getHolderStats()
    {
        $balances = TokenBalance::where('token_address', $this->address)->get();
        
        return [
            'total_holders' => $balances->count(),
            'total_balance' => $balances->sum('balance'),
            'average_balance' => $balances->avg('balance'),
            'top_10_percentage' => $this->calculateTopHolderPercentage($balances, 10),
            'top_100_percentage' => $this->calculateTopHolderPercentage($balances, 100),
            'whale_count' => $balances->where('balance', '>', $this->total_supply * 0.01)->count(),
            'retail_count' => $balances->where('balance', '<=', $this->total_supply * 0.01)->count(),
        ];
    }

    private function calculateTopHolderPercentage($balances, $count)
    {
        $topHolders = $balances->sortByDesc('balance')->take($count);
        $topBalance = $topHolders->sum('balance');
        
        return $this->total_supply > 0 ? ($topBalance / $this->total_supply) * 100 : 0;
    }

    // Transaction Methods
    public function getTransactionStats()
    {
        return [
            'total_transactions' => $this->transaction_count,
            'last_transaction' => $this->last_transaction_timestamp,
            'days_since_last_transaction' => $this->days_since_last_transaction,
            'average_transaction_value' => $this->average_transaction_value,
            'transaction_frequency' => $this->calculateTransactionFrequency(),
        ];
    }

    private function calculateTransactionFrequency()
    {
        if (!$this->last_transaction_timestamp) return 0;
        
        $daysActive = $this->created_at->diffInDays(now());
        return $daysActive > 0 ? $this->transaction_count / $daysActive : 0;
    }

    public function getRecentTransactions($limit = 50)
    {
        return TokenTransaction::where('token_address', $this->address)
                              ->orderBy('timestamp', 'desc')
                              ->limit($limit)
                              ->get();
    }

    public function getVolumeHistory($period = '24h')
    {
        $periods = [
            '1h' => 1,
            '24h' => 24,
            '7d' => 24 * 7,
            '30d' => 24 * 30,
            '90d' => 24 * 90,
            '1y' => 24 * 365
        ];
        
        $hours = $periods[$period] ?? 24;
        
        return TokenPrice::where('token_address', $this->address)
                        ->where('timestamp', '>=', now()->subHours($hours))
                        ->orderBy('timestamp', 'asc')
                        ->get();
    }
}
