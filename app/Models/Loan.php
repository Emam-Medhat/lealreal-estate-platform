<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'borrower_id',
        'lender_id',
        'loan_number',
        'type',
        'purpose',
        'amount',
        'approved_amount',
        'currency',
        'interest_rate',
        'approved_rate',
        'interest_type',
        'loan_term_months',
        'approved_terms',
        'payment_frequency',
        'monthly_payment',
        'collateral_type',
        'collateral_value',
        'collateral_description',
        'guarantor',
        'fees',
        'terms',
        'disbursement_method',
        'disbursement_schedule',
        'documents',
        'notes',
        'status',
        'approved_at',
        'approved_by',
        'disbursed_at',
        'disbursed_by',
        'disbursed_amount',
        'started_at',
        'paid_off_at',
        'total_payment',
        'total_interest',
        'total_fees',
        'outstanding_balance',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'guarantor' => 'array',
        'fees' => 'array',
        'disbursement_schedule' => 'array',
        'documents' => 'array',
        'amount' => 'decimal:15,2',
        'approved_amount' => 'decimal:15,2',
        'interest_rate' => 'decimal:8,4',
        'approved_rate' => 'decimal:8,4',
        'monthly_payment' => 'decimal:15,2',
        'collateral_value' => 'decimal:15,2',
        'disbursed_amount' => 'decimal:15,2',
        'total_payment' => 'decimal:15,2',
        'total_interest' => 'decimal:15,2',
        'total_fees' => 'decimal:15,2',
        'outstanding_balance' => 'decimal:15,2',
        'approved_at' => 'datetime',
        'approved_by' => 'integer',
        'disbursed_at' => 'datetime',
        'disbursed_by' => 'integer',
        'started_at' => 'datetime',
        'paid_off_at' => 'datetime',
        'borrower_id' => 'integer',
        'lender_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function lender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lender_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LoanPayment::class);
    }

    public function disbursements(): HasMany
    {
        return $this->hasMany(LoanDisbursement::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LoanDocument::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByBorrower($query, $borrowerId)
    {
        return $query->where('borrower_id', $borrowerId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['disbursed', 'active']);
    }

    // Helper methods
    public function isActive(): bool
    {
        return in_array($this->status, ['disbursed', 'active']);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending_approval';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isDisbursed(): bool
    {
        return $this->status === 'disbursed';
    }

    public function isPaidOff(): bool
    {
        return $this->status === 'paid_off';
    }

    public function isDefaulted(): bool
    {
        return $this->status === 'defaulted';
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 2);
    }

    public function getApprovedAmountFormattedAttribute(): string
    {
        return number_format($this->approved_amount, 2);
    }

    public function getInterestRateFormattedAttribute(): string
    {
        return number_format($this->interest_rate, 2) . '%';
    }

    public function getApprovedRateFormattedAttribute(): string
    {
        return number_format($this->approved_rate, 2) . '%';
    }

    public function getMonthlyPaymentFormattedAttribute(): string
    {
        return number_format($this->monthly_payment, 2);
    }

    public function getOutstandingBalanceFormattedAttribute(): string
    {
        return number_format($this->outstanding_balance, 2);
    }

    public function getTotalInterestFormattedAttribute(): string
    {
        return number_format($this->total_interest, 2);
    }

    public function getTotalFeesFormattedAttribute(): string
    {
        return number_format($this->total_fees, 2);
    }

    public function getCollateralValueFormattedAttribute(): string
    {
        return number_format($this->collateral_value, 2);
    }

    public function getDisbursedAmountFormattedAttribute(): string
    {
        return number_format($this->disbursed_amount, 2);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->approved_amount == 0) return 0;
        
        $paidAmount = $this->approved_amount - $this->outstanding_balance;
        return ($paidAmount / $this->approved_amount) * 100;
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->started_at) return 0;
        
        $totalDays = $this->loan_term_months * 30; // Approximate
        $daysPassed = now()->diffInDays($this->started_at);
        
        return max(0, $totalDays - $daysPassed);
    }

    public function getNextPaymentDateAttribute(): ?string
    {
        if (!$this->started_at) return null;
        
        $lastPayment = $this->payments()->latest('payment_date')->first();
        
        if ($lastPayment) {
            return $lastPayment->payment_date->addMonth()->format('Y-m-d');
        } else {
            return $this->started_at->addMonth()->format('Y-m-d');
        }
    }

    public function getLoanStatusAttribute(): string
    {
        switch ($this->status) {
            case 'pending_approval':
                return '🟡 Pending Approval';
            case 'approved':
                return '🟢 Approved';
            case 'disbursed':
                return '🔵 Disbursed';
            case 'active':
                return '🟢 Active';
            case 'paid_off':
                return '✅ Paid Off';
            case 'defaulted':
                return '🔴 Defaulted';
            case 'restructured':
                return '🟠 Restructured';
            default:
                return '❓ Unknown';
        }
    }

    public function getLoanTypeDisplayAttribute(): string
    {
        $types = [
            'personal' => 'Personal Loan',
            'business' => 'Business Loan',
            'mortgage' => 'Mortgage',
            'auto' => 'Auto Loan',
            'student' => 'Student Loan',
            'other' => 'Other Loan',
        ];

        return $types[$this->type] ?? 'Unknown';
    }

    public function getInterestTypeDisplayAttribute(): string
    {
        $types = [
            'fixed' => 'Fixed Rate',
            'variable' => 'Variable Rate',
        ];

        return $types[$this->interest_type] ?? 'Unknown';
    }

    public function getPaymentFrequencyDisplayAttribute(): string
    {
        $frequencies = [
            'weekly' => 'Weekly',
            'bi_weekly' => 'Bi-Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
        ];

        return $frequencies[$this->payment_frequency] ?? 'Unknown';
    }

    public function getRemainingPaymentsAttribute(): int
    {
        if (!$this->started_at) return $this->loan_term_months;
        
        $monthsPassed = now()->diffInMonths($this->started_at);
        return max(0, $this->loan_term_months - $monthsPassed);
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->approved_amount - $this->outstanding_balance;
    }

    public function getTotalPaidFormattedAttribute(): string
    {
        return number_format($this->getTotalPaidAttribute(), 2);
    }

    public function getEffectiveInterestRateAttribute(): float
    {
        if ($this->approved_amount == 0 || $this->total_fees == 0) return $this->approved_rate;
        
        // Calculate effective rate including fees
        $totalCost = $this->total_interest + $this->total_fees;
        $annualRate = ($totalCost / $this->approved_amount) * (365 / ($this->loan_term_months * 30));
        
        return $annualRate * 100;
    }

    public function getEffectiveInterestRateFormattedAttribute(): string
    {
        return number_format($this->getEffectiveInterestRateAttribute(), 2) . '%';
    }

    public function isOverdue(): bool
    {
        if (!$this->started_at || !$this->isActive()) return false;
        
        $nextPaymentDate = $this->getNextPaymentDateAttribute();
        return $nextPaymentDate && now()->isAfter($nextPaymentDate);
    }

    public function getOverdueDaysAttribute(): int
    {
        if (!$this->isOverdue()) return 0;
        
        $nextPaymentDate = $this->getNextPaymentDateAttribute();
        return $nextPaymentDate ? now()->diffInDays($nextPaymentDate) : 0;
    }

    public function getLateFeeAttribute(): float
    {
        if (!$this->isOverdue()) return 0;
        
        $lateFeeRate = 0.05; // 5% late fee
        return $this->monthly_payment * $lateFeeRate;
    }

    public function getLateFeeFormattedAttribute(): string
    {
        return number_format($this->getLateFeeAttribute(), 2);
    }

    public function getNextPaymentAmountAttribute(): float
    {
        $baseAmount = $this->monthly_payment;
        
        if ($this->isOverdue()) {
            $baseAmount += $this->getLateFeeAttribute();
        }
        
        return $baseAmount;
    }

    public function getNextPaymentAmountFormattedAttribute(): string
    {
        return number_format($this->getNextPaymentAmountAttribute(), 2);
    }

    public function getAmortizationScheduleAttribute(): array
    {
        $schedule = [];
        $balance = $this->approved_amount;
        $monthlyRate = $this->approved_rate / 100 / 12;
        $numPayments = $this->loan_term_months;
        $payment = $this->monthly_payment;
        
        for ($month = 1; $month <= $numPayments; $month++) {
            if ($balance <= 0) break;
            
            $interest = $balance * $monthlyRate;
            $principal = min($payment - $interest, $balance);
            $balance -= $principal;
            
            $schedule[] = [
                'month' => $month,
                'payment' => $payment,
                'principal' => $principal,
                'interest' => $interest,
                'balance' => $balance,
            ];
        }
        
        return $schedule;
    }
}
