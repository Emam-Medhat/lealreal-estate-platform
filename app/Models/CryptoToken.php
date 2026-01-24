<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CryptoToken extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'symbol',
        'address',
        'decimals',
        'total_supply',
        'circulating_supply',
        'current_price',
        'market_cap',
        'volume_24h',
        'price_change_24h',
        'price_change_7d',
        'price_change_30d',
        'logo_url',
        'website_url',
        'blockchain_network',
        'token_standard',
        'is_active',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_supply' => 'decimal:30,0',
        'circulating_supply' => 'decimal:30,0',
        'current_price' => 'decimal:18,8',
        'market_cap' => 'decimal:30,2',
        'volume_24h' => 'decimal:30,2',
        'price_change_24h' => 'decimal:10,4',
        'price_change_7d' => 'decimal:10,4',
        'price_change_30d' => 'decimal:10,4',
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function stakingPositions(): HasMany
    {
        return $this->hasMany(DefiStaking::class, 'token_address', 'address');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CryptoTransaction::class, 'token_address', 'address');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByNetwork($query, $network)
    {
        return $query->where('blockchain_network', $network);
    }

    public function scopeByStandard($query, $standard)
    {
        return $query->where('token_standard', $standard);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getCurrentPriceFormattedAttribute(): string
    {
        return number_format($this->current_price, 8);
    }

    public function getMarketCapFormattedAttribute(): string
    {
        return number_format($this->market_cap, 2);
    }

    public function getVolume24hFormattedAttribute(): string
    {
        return number_format($this->volume_24h, 2);
    }

    public function getPriceChange24hFormattedAttribute(): string
    {
        $change = $this->price_change_24h;
        return ($change >= 0 ? '+' : '') . number_format($change, 4) . '%';
    }

    public function getPriceChange7dFormattedAttribute(): string
    {
        $change = $this->price_change_7d;
        return ($change >= 0 ? '+' : '') . number_format($change, 4) . '%';
    }

    public function getPriceChange30dFormattedAttribute(): string
    {
        $change = $this->price_change_30d;
        return ($change >= 0 ? '+' : '') . number_format($change, 4) . '%';
    }

    public function getTotalSupplyFormattedAttribute(): string
    {
        return number_format($this->total_supply);
    }

    public function getCirculatingSupplyFormattedAttribute(): string
    {
        return number_format($this->circulating_supply);
    }

    public function getNameAttribute(): string
    {
        return $this->name ?? '';
    }

    public function getSymbolAttribute(): string
    {
        return $this->symbol ?? '';
    }

    public function getAddressAttribute(): string
    {
        return $this->address ?? '';
    }

    public function getDecimalsAttribute(): int
    {
        return $this->decimals ?? 18;
    }

    public function getBlockchainNetworkAttribute(): string
    {
        return $this->blockchain_network ?? '';
    }

    public function getTokenStandardAttribute(): string
    {
        return $this->token_standard ?? 'ERC20';
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo_url ?? '';
    }

    public function getWebsiteUrlAttribute(): string
    {
        return $this->website_url ?? '';
    }

    public function getDescriptionAttribute(): string
    {
        return $this->description ?? '';
    }

    public function getPriceTrendAttribute(): string
    {
        $change24h = $this->price_change_24h;
        if ($change24h > 0) return 'up';
        if ($change24h < 0) return 'down';
        return 'stable';
    }

    public function getPriceTrendIconAttribute(): string
    {
        switch ($this->getPriceTrendAttribute()) {
            case 'up':
                return '📈';
            case 'down':
                return '📉';
            default:
                return '➡️';
        }
    }

    public function getCirculationRateAttribute(): float
    {
        if ($this->total_supply == 0) return 0;
        return ($this->circulating_supply / $this->total_supply) * 100;
    }

    public function getCirculationRateFormattedAttribute(): string
    {
        return number_format($this->getCirculationRateAttribute(), 2) . '%';
    }

    public function getMarketCapRankAttribute(): int
    {
        // This would typically come from an external API or cached data
        // For now, return a placeholder
        return 0;
    }

    public function getExplorerUrlAttribute(): string
    {
        $networks = [
            'ethereum' => 'https://etherscan.io/token/',
            'polygon' => 'https://polygonscan.com/token/',
            'bnb_chain' => 'https://bscscan.com/token/',
            'avalanche' => 'https://snowtrace.io/token/',
            'arbitrum' => 'https://arbiscan.io/token/',
            'solana' => 'https://solscan.io/token/',
        ];

        $baseUrl = $networks[$this->blockchain_network] ?? 'https://etherscan.io/token/';
        return $this->address ? $baseUrl . $this->address : '';
    }

    public function getFormattedPriceAttribute(): string
    {
        $price = $this->current_price;
        if ($price >= 1) {
            return '$' . number_format($price, 2);
        } elseif ($price >= 0.01) {
            return '$' . number_format($price, 4);
        } else {
            return '$' . number_format($price, 8);
        }
    }

    public function getMarketCapDisplayAttribute(): string
    {
        $cap = $this->market_cap;
        if ($cap >= 1000000000) {
            return '$' . number_format($cap / 1000000000, 2) . 'B';
        } elseif ($cap >= 1000000) {
            return '$' . number_format($cap / 1000000, 2) . 'M';
        } elseif ($cap >= 1000) {
            return '$' . number_format($cap / 1000, 2) . 'K';
        } else {
            return '$' . number_format($cap, 2);
        }
    }

    public function getVolumeDisplayAttribute(): string
    {
        $volume = $this->volume_24h;
        if ($volume >= 1000000) {
            return '$' . number_format($volume / 1000000, 2) . 'M';
        } elseif ($volume >= 1000) {
            return '$' . number_format($volume / 1000, 2) . 'K';
        } else {
            return '$' . number_format($volume, 2);
        }
    }

    public function isTopToken(): bool
    {
        return $this->getMarketCapRank() <= 100;
    }

    public function isStablecoin(): bool
    {
        $stablecoins = ['USDT', 'USDC', 'DAI', 'BUSD', 'USDP', 'TUSD', 'FRAX'];
        return in_array($this->symbol, $stablecoins);
    }

    public function isMemeToken(): bool
    {
        $memeTokens = ['DOGE', 'SHIB', 'PEPE', 'FLOKI', 'BABYDOGE'];
        return in_array($this->symbol, $memeTokens);
    }

    public function getTokenCategoryAttribute(): string
    {
        if ($this->isStablecoin()) return 'Stablecoin';
        if ($this->isMemeToken()) return 'Meme Token';
        return 'Utility Token';
    }
}
