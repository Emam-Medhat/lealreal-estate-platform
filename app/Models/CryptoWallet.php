<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CryptoWallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'blockchain',
        'address',
        'private_key',
        'mnemonic',
        'wallet_type',
        'currency',
        'balance',
        'is_default',
        'description',
        'metadata',
        'status',
        'balance_updated_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'private_key' => 'encrypted',
        'mnemonic' => 'encrypted',
        'metadata' => 'array',
        'balance' => 'decimal:18,8',
        'is_default' => 'boolean',
        'balance_updated_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    protected $hidden = [
        'private_key',
        'mnemonic',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CryptoTransaction::class);
    }

    // Scopes
    public function scopeByBlockchain($query, $blockchain)
    {
        return $query->where('blockchain', $blockchain);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isFrozen(): bool
    {
        return $this->status === 'frozen';
    }

    public function isDeleted(): bool
    {
        return $this->status === 'deleted';
    }

    public function isHotWallet(): bool
    {
        return $this->wallet_type === 'hot';
    }

    public function isColdWallet(): bool
    {
        return $this->wallet_type === 'cold';
    }

    public function isHardwareWallet(): bool
    {
        return $this->wallet_type === 'hardware';
    }

    public function isExchangeWallet(): bool
    {
        return $this->wallet_type === 'exchange';
    }

    public function getBalanceFormattedAttribute(): string
    {
        return number_format($this->balance, 8);
    }

    public function getShortAddressAttribute(): string
    {
        return substr($this->address, 0, 10) . '...' . substr($this->address, -8);
    }

    public function getBlockchainDisplayAttribute(): string
    {
        $blockchains = [
            'ethereum' => 'Ethereum',
            'polygon' => 'Polygon',
            'binance' => 'Binance Smart Chain',
            'avalanche' => 'Avalanche',
            'solana' => 'Solana',
            'bitcoin' => 'Bitcoin',
        ];

        return $blockchains[$this->blockchain] ?? 'Unknown';
    }

    public function getWalletTypeDisplayAttribute(): string
    {
        $types = [
            'hot' => 'Hot Wallet',
            'cold' => 'Cold Wallet',
            'hardware' => 'Hardware Wallet',
            'exchange' => 'Exchange Wallet',
        ];

        return $types[$this->wallet_type] ?? 'Unknown';
    }

    public function getStatusDisplayAttribute(): string
    {
        $statuses = [
            'active' => '🟢 Active',
            'inactive' => '🔴 Inactive',
            'frozen' => '🔵 Frozen',
            'deleted' => '⚫ Deleted',
        ];

        return $statuses[$this->status] ?? '❓ Unknown';
    }

    public function getExplorerUrlAttribute(): string
    {
        $explorers = [
            'ethereum' => 'https://etherscan.io/address/',
            'polygon' => 'https://polygonscan.com/address/',
            'binance' => 'https://bscscan.com/address/',
            'avalanche' => 'https://snowtrace.io/address/',
            'solana' => 'https://solscan.io/account/',
            'bitcoin' => 'https://blockstream.info/address/',
        ];

        return ($explorers[$this->blockchain] ?? '') . $this->address;
    }

    public function getQrCodeAttribute(): string
    {
        // Generate QR code for wallet address (mock implementation)
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
    }

    public function getRecentTransactionsAttribute(): HasMany
    {
        return $this->transactions()->latest()->limit(10);
    }

    public function getTotalTransactionsAttribute(): int
    {
        return $this->transactions()->count();
    }

    public function getSentTransactionsAttribute(): HasMany
    {
        return $this->transactions()->where('type', 'send');
    }

    public function getReceivedTransactionsAttribute(): HasMany
    {
        return $this->transactions()->where('type', 'receive');
    }

    public function getTotalSentAttribute(): float
    {
        return $this->transactions()->where('type', 'send')->sum('amount');
    }

    public function getTotalReceivedAttribute(): float
    {
        return $this->transactions()->where('type', 'receive')->sum('amount');
    }

    public function getTotalSentFormattedAttribute(): string
    {
        return number_format($this->getTotalSentAttribute(), 8);
    }

    public function getTotalReceivedFormattedAttribute(): string
    {
        return number_format($this->getTotalReceivedAttribute(), 8);
    }

    public function getNetTransactionsAttribute(): float
    {
        return $this->getTotalReceivedAttribute() - $this->getTotalSentAttribute();
    }

    public function getNetTransactionsFormattedAttribute(): string
    {
        $net = $this->getNetTransactionsAttribute();
        return ($net >= 0 ? '+' : '') . number_format($net, 8);
    }

    public function getFirstTransactionDateAttribute(): ?string
    {
        $firstTransaction = $this->transactions()->oldest()->first();
        return $firstTransaction ? $firstTransaction->created_at->format('Y-m-d') : null;
    }

    public function getLastTransactionDateAttribute(): ?string
    {
        $lastTransaction = $this->transactions()->latest()->first();
        return $lastTransaction ? $lastTransaction->created_at->format('Y-m-d') : null;
    }

    public function getDaysSinceLastTransactionAttribute(): int
    {
        $lastTransaction = $this->transactions()->latest()->first();
        return $lastTransaction ? now()->diffInDays($lastTransaction->created_at) : 0;
    }

    public function getTransactionVolumeAttribute(): float
    {
        return $this->transactions()->sum('amount');
    }

    public function getTransactionVolumeFormattedAttribute(): string
    {
        return number_format($this->getTransactionVolumeAttribute(), 8);
    }

    public function getAverageTransactionAttribute(): float
    {
        $count = $this->transactions()->count();
        return $count > 0 ? $this->getTransactionVolumeAttribute() / $count : 0;
    }

    public function getAverageTransactionFormattedAttribute(): string
    {
        return number_format($this->getAverageTransactionAttribute(), 8);
    }

    public function getIsHighValueAttribute(): bool
    {
        return $this->balance > 10; // Consider > 10 units as high value
    }

    public function getSecurityLevelAttribute(): string
    {
        switch ($this->wallet_type) {
            case 'hardware':
                return '🔒 High Security';
            case 'cold':
                return '🔐 Medium Security';
            case 'hot':
                return '🔓 Low Security';
            case 'exchange':
                return '🌐 Exchange Security';
            default:
                return '❓ Unknown';
        }
    }

    public function getBackupStatusAttribute(): string
    {
        // Mock check for backup status
        if ($this->private_key || $this->mnemonic) {
            return '✅ Backed Up';
        } else {
            return '⚠️ Not Backed Up';
        }
    }

    public function getSupportsStakingAttribute(): bool
    {
        $stakingSupported = ['ethereum', 'polygon', 'solana', 'avalanche'];
        return in_array($this->blockchain, $stakingSupported);
    }

    public function getSupportsDefiAttribute(): bool
    {
        $defiSupported = ['ethereum', 'polygon', 'binance', 'avalanche'];
        return in_array($this->blockchain, $defiSupported);
    }

    public function getNetworkFeeEstimateAttribute(): float
    {
        // Mock network fee estimates
        $fees = [
            'ethereum' => 0.01,
            'polygon' => 0.001,
            'binance' => 0.0005,
            'avalanche' => 0.002,
            'solana' => 0.00001,
            'bitcoin' => 0.0001,
        ];

        return $fees[$this->blockchain] ?? 0.001;
    }

    public function getNetworkFeeEstimateFormattedAttribute(): string
    {
        return number_format($this->getNetworkFeeEstimateAttribute(), 8);
    }

    public function canSendTransaction(float $amount): bool
    {
        return $this->balance >= ($amount + $this->getNetworkFeeEstimateAttribute());
    }

    public function getMaxSendableAttribute(): float
    {
        return max(0, $this->balance - $this->getNetworkFeeEstimateAttribute());
    }

    public function getMaxSendableFormattedAttribute(): string
    {
        return number_format($this->getMaxSendableAttribute(), 8);
    }

    public function getPortfolioValueAttribute(): float
    {
        // Mock conversion to USD
        $rates = [
            'ETH' => 3000,
            'BTC' => 45000,
            'MATIC' => 1.5,
            'BNB' => 300,
            'AVAX' => 35,
            'SOL' => 100,
        ];

        return ($rates[$this->currency] ?? 1) * $this->balance;
    }

    public function getPortfolioValueFormattedAttribute(): string
    {
        return '$' . number_format($this->getPortfolioValueAttribute(), 2);
    }

    public function get24hChangeAttribute(): float
    {
        // Mock 24h price change
        return (rand(-10, 10) / 100); // Random -10% to +10%
    }

    public function get24hChangeFormattedAttribute(): string
    {
        $change = $this->get24hChangeAttribute();
        return ($change >= 0 ? '+' : '') . number_format($change * 100, 2) . '%';
    }

    public function get24hChangeColorAttribute(): string
    {
        $change = $this->get24hChangeAttribute();
        return $change >= 0 ? 'text-green-600' : 'text-red-600';
    }
}
