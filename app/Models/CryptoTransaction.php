<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptoTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'investor_id',
        'transaction_hash',
        'block_number',
        'block_network',
        'from_address',
        'to_address',
        'token_address',
        'token_symbol',
        'amount',
        'gas_used',
        'gas_price',
        'gas_fee',
        'transaction_type',
        'status',
        'confirmations',
        'transaction_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:18,8',
        'gas_used' => 'decimal:18,0',
        'gas_price' => 'decimal:18,0',
        'gas_fee' => 'decimal:18,8',
        'confirmations' => 'integer',
        'transaction_date' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(CryptoToken::class, 'token_address', 'address');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByNetwork($query, $network)
    {
        return $query->where('block_network', $network);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    // Helper methods
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 8);
    }

    public function getGasFeeFormattedAttribute(): string
    {
        return number_format($this->gas_fee, 8);
    }

    public function getNetAmountAttribute(): float
    {
        return $this->amount - $this->gas_fee;
    }

    public function getNetAmountFormattedAttribute(): string
    {
        return number_format($this->getNetAmountAttribute(), 8);
    }

    public function getTransactionDateFormattedAttribute(): string
    {
        return $this->transaction_date->format('Y-m-d H:i:s');
    }

    public function getTransactionHashAttribute(): string
    {
        return $this->transaction_hash ?? '';
    }

    public function getFromAddressAttribute(): string
    {
        return $this->from_address ?? '';
    }

    public function getToAddressAttribute(): string
    {
        return $this->to_address ?? '';
    }

    public function getTokenAddressAttribute(): string
    {
        return $this->token_address ?? '';
    }

    public function getTokenSymbolAttribute(): string
    {
        return $this->token_symbol ?? '';
    }

    public function getBlockNetworkAttribute(): string
    {
        return $this->block_network ?? '';
    }

    public function getTransactionTypeAttribute(): string
    {
        return $this->transaction_type ?? 'transfer';
    }

    public function getStatusAttribute(): string
    {
        return $this->status ?? 'pending';
    }

    public function getNotesAttribute(): string
    {
        return $this->notes ?? '';
    }

    public function getConfirmationsAttribute(): int
    {
        return $this->confirmations ?? 0;
    }

    public function getBlockNumberAttribute(): int
    {
        return $this->block_number ?? 0;
    }

    public function getGasUsedAttribute(): int
    {
        return $this->gas_used ?? 0;
    }

    public function getGasPriceAttribute(): int
    {
        return $this->gas_price ?? 0;
    }

    public function getExplorerUrlAttribute(): string
    {
        $networks = [
            'ethereum' => 'https://etherscan.io/tx/',
            'polygon' => 'https://polygonscan.com/tx/',
            'bnb_chain' => 'https://bscscan.com/tx/',
            'avalanche' => 'https://snowtrace.io/tx/',
            'arbitrum' => 'https://arbiscan.io/tx/',
            'solana' => 'https://solscan.io/tx/',
        ];

        $baseUrl = $networks[$this->block_network] ?? 'https://etherscan.io/tx/';
        return $baseUrl . $this->transaction_hash;
    }

    public function getTransactionStatusAttribute(): string
    {
        switch ($this->status) {
            case 'confirmed':
                return '✅ Confirmed';
            case 'pending':
                return '⏳ Pending';
            case 'failed':
                return '❌ Failed';
            default:
                return '❓ Unknown';
        }
    }

    public function getTransactionTypeIconAttribute(): string
    {
        switch ($this->transaction_type) {
            case 'buy':
                return '🟢';
            case 'sell':
                return '🔴';
            case 'transfer':
                return '➡️';
            case 'stake':
                return '🔒';
            case 'unstake':
                return '🔓';
            case 'claim':
                return '🎁';
            default:
                return '📋';
        }
    }

    public function getHighValueAttribute(): bool
    {
        return $this->amount > 1000; // Consider transactions over 1000 tokens as high value
    }

    public function getHighFeeAttribute(): bool
    {
        return $this->gas_fee > 0.01; // Consider fees over 0.01 as high
    }

    public function getRecentAttribute(): bool
    {
        return $this->transaction_date->diffInHours(now()) < 24;
    }

    public function getFormattedAmountWithSymbolAttribute(): string
    {
        $symbol = $this->token_symbol ? ' ' . $this->token_symbol : '';
        return $this->getAmountFormattedAttribute() . $symbol;
    }

    public function getFormattedNetAmountWithSymbolAttribute(): string
    {
        $symbol = $this->token_symbol ? ' ' . $this->token_symbol : '';
        return $this->getNetAmountFormattedAttribute() . $symbol;
    }

    public function getTransactionSummaryAttribute(): string
    {
        $type = $this->getTransactionTypeIconAttribute() . ' ' . ucfirst($this->transaction_type);
        $amount = $this->getFormattedAmountWithSymbolAttribute();
        $status = $this->getTransactionStatusAttribute();
        
        return "{$type}: {$amount} ({$status})";
    }

    public function getFeePercentageAttribute(): float
    {
        if ($this->amount == 0) return 0;
        return ($this->gas_fee / $this->amount) * 100;
    }

    public function getFeePercentageFormattedAttribute(): string
    {
        return number_format($this->getFeePercentageAttribute(), 4) . '%';
    }

    public function isFromInvestor(): bool
    {
        return $this->investor && $this->from_address === $this->investor->wallet_address;
    }

    public function isToInvestor(): bool
    {
        return $this->investor && $this->to_address === $this->investor->wallet_address;
    }

    public function getDirectionAttribute(): string
    {
        if ($this->isFromInvestor()) return 'outgoing';
        if ($this->isToInvestor()) return 'incoming';
        return 'unknown';
    }

    public function getDirectionIconAttribute(): string
    {
        switch ($this->getDirectionAttribute()) {
            case 'outgoing':
                return '⬆️';
            case 'incoming':
                return '⬇️';
            default:
                return '↔️';
        }
    }
}
