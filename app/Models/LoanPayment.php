<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_id',
        'payment_number',
        'amount',
        'principal_amount',
        'interest_amount',
        'late_fee',
        'payment_date',
        'payment_method',
        'transaction_reference',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:15,2',
        'principal_amount' => 'decimal:15,2',
        'interest_amount' => 'decimal:15,2',
        'late_fee' => 'decimal:15,2',
        'payment_date' => 'datetime',
        'loan_id' => 'integer',
        'payment_number' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByLoan($query, $loanId)
    {
        return $query->where('loan_id', $loanId);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeLate($query)
    {
        return $query->where('late_fee', '>', 0);
    }

    // Helper methods
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isLate(): bool
    {
        return $this->late_fee > 0;
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 2);
    }

    public function getPrincipalAmountFormattedAttribute(): string
    {
        return number_format($this->principal_amount, 2);
    }

    public function getInterestAmountFormattedAttribute(): string
    {
        return number_format($this->interest_amount, 2);
    }

    public function getLateFeeFormattedAttribute(): string
    {
        return number_format($this->late_fee, 2);
    }

    public function getStatusDisplayAttribute(): string
    {
        $statuses = [
            'pending' => '🟡 Pending',
            'paid' => '🟢 Paid',
            'failed' => '🔴 Failed',
            'cancelled' => '⚫ Cancelled',
        ];

        return $statuses[$this->status] ?? '❓ Unknown';
    }

    public function getPaymentMethodDisplayAttribute(): string
    {
        $methods = [
            'bank_transfer' => 'Bank Transfer',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'cash' => 'Cash',
            'check' => 'Check',
            'crypto' => 'Cryptocurrency',
        ];

        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    public function getDaysLateAttribute(): int
    {
        if (!$this->loan || !$this->payment_date) return 0;
        
        $expectedDate = $this->loan->started_at->addMonths($this->payment_number);
        return now()->diffInDays($expectedDate);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->getDaysLateAttribute() > 0 && !$this->isPaid();
    }

    public function getOverdueDaysFormattedAttribute(): string
    {
        $days = $this->getDaysLateAttribute();
        return $days > 0 ? $days . ' days' : 'On time';
    }

    public function getPrincipalPercentageAttribute(): float
    {
        if ($this->amount == 0) return 0;
        return ($this->principal_amount / $this->amount) * 100;
    }

    public function getInterestPercentageAttribute(): float
    {
        if ($this->amount == 0) return 0;
        return ($this->interest_amount / $this->amount) * 100;
    }

    public function getLateFeePercentageAttribute(): float
    {
        if ($this->amount == 0) return 0;
        return ($this->late_fee / $this->amount) * 100;
    }

    public function getPrincipalPercentageFormattedAttribute(): string
    {
        return number_format($this->getPrincipalPercentageAttribute(), 1) . '%';
    }

    public function getInterestPercentageFormattedAttribute(): string
    {
        return number_format($this->getInterestPercentageAttribute(), 1) . '%';
    }

    public function getLateFeePercentageFormattedAttribute(): string
    {
        return number_format($this->getLateFeePercentageAttribute(), 1) . '%';
    }

    public function getRemainingPrincipalAttribute(): float
    {
        if (!$this->loan) return 0;
        
        return $this->loan->outstanding_balance;
    }

    public function getRemainingPrincipalFormattedAttribute(): string
    {
        return number_format($this->getRemainingPrincipalAttribute(), 2);
    }

    public function getPaymentProgressAttribute(): float
    {
        if (!$this->loan) return 0;
        
        $totalPayments = $this->loan->loan_term_months;
        return ($this->payment_number / $totalPayments) * 100;
    }

    public function getPaymentProgressFormattedAttribute(): string
    {
        return number_format($this->getPaymentProgressAttribute(), 1) . '%';
    }

    public function getExpectedPaymentDateAttribute(): ?string
    {
        if (!$this->loan) return null;
        
        return $this->loan->started_at->addMonths($this->payment_number)->format('Y-m-d');
    }

    public function getPaymentScheduleAttribute(): array
    {
        if (!$this->loan) return [];
        
        return [
            'payment_number' => $this->payment_number,
            'expected_date' => $this->getExpectedPaymentDateAttribute(),
            'actual_date' => $this->payment_date ? $this->payment_date->format('Y-m-d') : null,
            'amount' => $this->amount,
            'principal' => $this->principal_amount,
            'interest' => $this->interest_amount,
            'late_fee' => $this->late_fee,
            'total' => $this->amount + $this->late_fee,
            'status' => $this->status,
            'days_late' => $this->getDaysLateAttribute(),
            'is_overdue' => $this->getIsOverdueAttribute(),
        ];
    }

    public function getPaymentSummaryAttribute(): array
    {
        return [
            'amount' => $this->amount,
            'principal' => $this->principal_amount,
            'interest' => $this->interest_amount,
            'late_fee' => $this->late_fee,
            'total' => $this->amount + $this->late_fee,
            'payment_number' => $this->payment_number,
            'payment_date' => $this->payment_date,
            'status' => $this->status,
            'method' => $this->payment_method,
            'reference' => $this->transaction_reference,
        ];
    }

    public function canBeProcessed(): bool
    {
        return $this->status === 'pending' && $this->payment_date <= now();
    }

    public function canBeCancelled(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeRefunded(): bool
    {
        return $this->status === 'paid' && now()->diffInDays($this->payment_date) <= 30;
    }

    public function getPaymentBreakdownAttribute(): array
    {
        return [
            'principal' => [
                'amount' => $this->principal_amount,
                'percentage' => $this->getPrincipalPercentageAttribute(),
                'formatted' => $this->getPrincipalAmountFormattedAttribute(),
            ],
            'interest' => [
                'amount' => $this->interest_amount,
                'percentage' => $this->getInterestPercentageAttribute(),
                'formatted' => $this->getInterestAmountFormattedAttribute(),
            ],
            'late_fee' => [
                'amount' => $this->late_fee,
                'percentage' => $this->getLateFeePercentageAttribute(),
                'formatted' => $this->getLateFeeFormattedAttribute(),
            ],
            'total' => [
                'amount' => $this->amount + $this->late_fee,
                'formatted' => number_format($this->amount + $this->late_fee, 2),
            ],
        ];
    }
}
