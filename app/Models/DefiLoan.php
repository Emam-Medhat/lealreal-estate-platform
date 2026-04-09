<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefiLoan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'investor_id',
        'borrower_address',
        'loan_amount',
        'loan_purpose',
        'loan_type',
        'collateral_type',
        'collateral_value',
        'collateral_address',
        'interest_rate',
        'loan_term_days',
        'repayment_frequency',
        'grace_period_days',
        'late_fee_rate',
        'early_repayment_penalty',
        'minimum_credit_score',
        'required_documents',
        'smart_contract_address',
        'blockchain_network',
        'token_standard',
        'status',
        'total_repayments',
        'total_repaid',
        'total_interest',
        'monthly_payment',
        'collateral_ratio',
        'documents',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'required_documents' => 'array',
        'documents' => 'array',
        'loan_amount' => 'decimal:15,2',
        'collateral_value' => 'decimal:15,2',
        'interest_rate' => 'decimal:8,4',
        'loan_term_days' => 'integer',
        'grace_period_days' => 'integer',
        'late_fee_rate' => 'decimal:8,4',
        'early_repayment_penalty' => 'decimal:8,4',
        'minimum_credit_score' => 'integer',
        'total_repayments' => 'decimal:15,2',
        'total_repaid' => 'decimal:15,2',
        'total_interest' => 'decimal:15,2',
        'monthly_payment' => 'decimal:15,2',
        'collateral_ratio' => 'decimal:8,4',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_address', 'wallet_address');
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(DefiLoanRepayment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('loan_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByBlockchain($query, $network)
    {
        return $query->where('blockchain_network', $network);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isDefaulted(): bool
    {
        return $this->status === 'defaulted';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending_approval';
    }

    public function getRemainingBalanceAttribute(): float
    {
        return $this->total_repayments - $this->total_repaid;
    }

    public function getRepaymentProgressAttribute(): float
    {
        return $this->total_repayments > 0 ? ($this->total_repaid / $this->total_repayments) * 100 : 0;
    }

    public function getDaysActiveAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    public function getDaysRemainingAttribute(): int
    {
        return max(0, $this->loan_term_days - $this->getDaysActiveAttribute());
    }

    public function getLoanAmountFormattedAttribute(): string
    {
        return number_format($this->loan_amount, 2);
    }

    public function getCollateralValueFormattedAttribute(): string
    {
        return number_format($this->collateral_value, 2);
    }

    public function getInterestRateFormattedAttribute(): string
    {
        return number_format($this->interest_rate, 2) . '%';
    }

    public function getTotalRepaymentsFormattedAttribute(): string
    {
        return number_format($this->total_repayments, 2);
    }

    public function getTotalRepaidFormattedAttribute(): string
    {
        return number_format($this->total_repaid, 2);
    }

    public function getRemainingBalanceFormattedAttribute(): string
    {
        return number_format($this->getRemainingBalanceAttribute(), 2);
    }

    public function getMonthlyPaymentFormattedAttribute(): string
    {
        return number_format($this->monthly_payment, 2);
    }

    public function getCollateralRatioFormattedAttribute(): string
    {
        return number_format($this->collateral_ratio, 2) . '%';
    }

    public function getDocumentsCountAttribute(): int
    {
        return count($this->documents ?? []);
    }

    public function getRequiredDocumentsCountAttribute(): int
    {
        return count($this->required_documents ?? []);
    }

    public function getLoanPurposeAttribute(): string
    {
        return $this->loan_purpose ?? '';
    }

    public function getLoanTypeAttribute(): string
    {
        return $this->loan_type ?? 'personal';
    }

    public function getCollateralTypeAttribute(): string
    {
        return $this->collateral_type ?? 'none';
    }

    public function getRepaymentFrequencyAttribute(): string
    {
        return $this->repayment_frequency ?? 'monthly';
    }

    public function getBlockchainNetworkAttribute(): string
    {
        return $this->blockchain_network ?? 'ethereum';
    }

    public function getTokenStandardAttribute(): string
    {
        return $this->token_standard ?? 'ERC20';
    }

    public function getStatusAttribute(): string
    {
        return $this->status ?? 'pending_approval';
    }

    public function getBorrowerAddressAttribute(): string
    {
        return $this->borrower_address ?? '';
    }

    public function getCollateralAddressAttribute(): string
    {
        return $this->collateral_address ?? '';
    }

    public function getSmartContractAddressAttribute(): string
    {
        return $this->smart_contract_address ?? '';
    }

    public function getNotesAttribute(): string
    {
        return $this->notes ?? '';
    }

    public function getDocumentsAttribute(): array
    {
        return $this->documents ?? [];
    }

    public function getRequiredDocumentsAttribute(): array
    {
        return $this->required_documents ?? [];
    }

    public function getInterestEarnedAttribute(): float
    {
        return $this->total_repaid - $this->loan_amount;
    }

    public function getInterestEarnedFormattedAttribute(): string
    {
        return number_format($this->getInterestEarnedAttribute(), 2);
    }

    public function getAnnualizedReturnAttribute(): float
    {
        if ($this->loan_amount == 0 || $this->getDaysActiveAttribute() == 0) return 0;
        
        $dailyReturn = ($this->getInterestEarnedAttribute() / $this->getDaysActiveAttribute()) / $this->loan_amount;
        return $dailyReturn * 365 * 100;
    }

    public function getAnnualizedReturnFormattedAttribute(): string
    {
        return number_format($this->getAnnualizedReturnAttribute(), 2) . '%';
    }

    public function getNextPaymentDueAttribute(): ?string
    {
        if (!$this->isActive()) return null;
        
        $lastRepayment = $this->repayments()->latest('payment_date')->first();
        $frequency = $this->repayment_frequency;
        
        $nextDue = $lastRepayment ? 
            $lastRepayment->payment_date->addDays($this->getFrequencyDays($frequency)) :
            $this->created_at->addDays($this->getFrequencyDays($frequency));
        
        return $nextDue->format('Y-m-d');
    }

    private function getFrequencyDays(string $frequency): int
    {
        $frequencies = [
            'daily' => 1,
            'weekly' => 7,
            'monthly' => 30,
            'quarterly' => 90,
        ];
        
        return $frequencies[$frequency] ?? 30;
    }
}
