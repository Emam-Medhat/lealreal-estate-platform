<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefiLoanRepayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_id',
        'amount',
        'payment_date',
        'transaction_hash',
        'block_network',
        'gas_fee',
        'net_amount',
        'notes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:15,2',
        'gas_fee' => 'decimal:15,8',
        'net_amount' => 'decimal:15,2',
        'payment_date' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(DefiLoan::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function scopeByNetwork($query, $network)
    {
        return $query->where('block_network', $network);
    }

    // Helper methods
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
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
        return number_format($this->amount, 2);
    }

    public function getNetAmountFormattedAttribute(): string
    {
        return number_format($this->net_amount, 2);
    }

    public function getGasFeeFormattedAttribute(): string
    {
        return number_format($this->gas_fee, 8);
    }

    public function getPaymentDateFormattedAttribute(): string
    {
        return $this->payment_date->format('Y-m-d H:i:s');
    }

    public function getTransactionHashAttribute(): string
    {
        return $this->transaction_hash ?? '';
    }

    public function getBlockNetworkAttribute(): string
    {
        return $this->block_network ?? '';
    }

    public function getStatusAttribute(): string
    {
        return $this->status ?? 'pending';
    }

    public function getNotesAttribute(): string
    {
        return $this->notes ?? '';
    }

    public function getExplorerUrlAttribute(): string
    {
        $networks = [
            'ethereum' => 'https://etherscan.io/tx/',
            'polygon' => 'https://polygonscan.com/tx/',
            'bnb_chain' => 'https://bscscan.com/tx/',
            'avalanche' => 'https://snowtrace.io/tx/',
            'arbitrum' => 'https://arbiscan.io/tx/',
        ];

        $baseUrl = $networks[$this->block_network] ?? 'https://etherscan.io/tx/';
        return $this->transaction_hash ? $baseUrl . $this->transaction_hash : '';
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

    public function getRecentAttribute(): bool
    {
        return $this->payment_date->diffInHours(now()) < 24;
    }

    public function getPaymentStatusAttribute(): string
    {
        switch ($this->status) {
            case 'completed':
                return '✅ Completed';
            case 'pending':
                return '⏳ Pending';
            case 'failed':
                return '❌ Failed';
            default:
                return '❓ Unknown';
        }
    }
}
