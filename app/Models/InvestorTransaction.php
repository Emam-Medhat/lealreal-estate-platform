<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestorTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'investor_id',
        'portfolio_id',
        'type',
        'amount',
        'currency',
        'description',
        'reference',
        'transaction_date',
        'status',
        'fee',
        'tax',
        'net_amount',
        'payment_method',
        'payment_details',
        'transaction_hash',
        'blockchain_confirmations',
        'exchange_rate',
        'notes',
        'receipt',
        'supporting_documents',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'payment_details' => 'array',
        'supporting_documents' => 'array',
        'amount' => 'decimal:15,2',
        'fee' => 'decimal:15,2',
        'tax' => 'decimal:15,2',
        'net_amount' => 'decimal:15,2',
        'exchange_rate' => 'decimal:10,6',
        'blockchain_confirmations' => 'integer',
        'transaction_date' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(InvestorPortfolio::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
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

    public function isInvestment(): bool
    {
        return $this->type === 'investment';
    }

    public function isReturn(): bool
    {
        return $this->type === 'return';
    }

    public function isWithdrawal(): bool
    {
        return $this->type === 'withdrawal';
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 2);
    }

    public function getNetAmountFormattedAttribute(): string
    {
        return number_format($this->net_amount, 2);
    }

    public function getFeeFormattedAttribute(): string
    {
        return number_format($this->fee, 2);
    }

    public function getTaxFormattedAttribute(): string
    {
        return number_format($this->tax, 2);
    }

    public function getTransactionDateFormattedAttribute(): string
    {
        return $this->transaction_date->format('Y-m-d H:i:s');
    }

    public function getReceiptUrlAttribute(): string
    {
        return $this->receipt ? asset('storage/' . $this->receipt) : '';
    }

    public function getSupportingDocumentsCountAttribute(): int
    {
        return count($this->supporting_documents ?? []);
    }

    public function getPaymentDetailsAttribute(): array
    {
        return $this->payment_details ?? [];
    }

    public function getExchangeRateAttribute(): float
    {
        return $this->exchange_rate ?? 1.0;
    }

    public function getCurrencyAttribute(): string
    {
        return $this->currency ?? 'USD';
    }

    public function getStatusAttribute(): string
    {
        return $this->status ?? 'pending';
    }

    public function getTypeAttribute(): string
    {
        return $this->type ?? 'investment';
    }

    public function getReferenceAttribute(): string
    {
        return $this->reference ?? '';
    }

    public function getDescriptionAttribute(): string
    {
        return $this->description ?? '';
    }

    public function getPaymentMethodAttribute(): string
    {
        return $this->payment_method ?? 'bank_transfer';
    }

    public function getTransactionHashAttribute(): string
    {
        return $this->transaction_hash ?? '';
    }

    public function getBlockchainConfirmationsAttribute(): int
    {
        return $this->blockchain_confirmations ?? 0;
    }
}
