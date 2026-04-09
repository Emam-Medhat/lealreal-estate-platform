<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class PropertyFinancialAnalysis extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_id',
        'user_id',
        'analysis_date',
        'current_value',
        'purchase_price',
        'purchase_date',
        'annual_rental_income',
        'operating_expenses',
        'vacancy_rate',
        'appreciation_rate',
        'inflation_rate',
        'discount_rate',
        'holding_period',
        'loan_amount',
        'interest_rate',
        'loan_term',
        'property_type',
        'location',
        'market_conditions',
        'analysis_type',
        'status',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'analysis_date' => 'date',
        'purchase_date' => 'date',
        'current_value' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'annual_rental_income' => 'decimal:2',
        'operating_expenses' => 'decimal:2',
        'vacancy_rate' => 'decimal:3',
        'appreciation_rate' => 'decimal:3',
        'inflation_rate' => 'decimal:3',
        'discount_rate' => 'decimal:3',
        'holding_period' => 'integer',
        'loan_amount' => 'decimal:2',
        'interest_rate' => 'decimal:3',
        'loan_term' => 'integer',
        'market_conditions' => 'json',
        'notes' => 'text'
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function roiCalculations(): HasMany
    {
        return $this->hasMany(RoiCalculation::class);
    }

    public function cashFlowProjections(): HasMany
    {
        return $this->hasMany(CashFlowProjection::class);
    }

    public function capRateCalculations(): HasMany
    {
        return $this->hasMany(CapRateCalculation::class);
    }

    public function propertyValuations(): HasMany
    {
        return $this->hasMany(PropertyValuation::class);
    }

    public function investmentScenarios(): HasMany
    {
        return $this->hasMany(InvestmentScenario::class);
    }

    public function portfolioAnalyses(): HasMany
    {
        return $this->hasMany(PortfolioAnalysis::class);
    }

    public function taxBenefits(): HasMany
    {
        return $this->hasMany(TaxBenefit::class);
    }

    public function appreciationProjections(): HasMany
    {
        return $this->hasMany(AppreciationProjection::class);
    }

    public function scopeByProperty(Builder $query, $propertyId): Builder
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByUser(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus(Builder $query, $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByAnalysisType(Builder $query, $type): Builder
    {
        return $query->where('analysis_type', $type);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function calculateNetOperatingIncome(): float
    {
        $effectiveGrossIncome = $this->annual_rental_income * (1 - $this->vacancy_rate);
        return $effectiveGrossIncome - $this->operating_expenses;
    }

    public function calculateCapitalizationRate(): float
    {
        $noi = $this->calculateNetOperatingIncome();
        return $this->current_value > 0 ? ($noi / $this->current_value) * 100 : 0;
    }

    public function calculateGrossRentMultiplier(): float
    {
        return $this->annual_rental_income > 0 ? $this->current_value / $this->annual_rental_income : 0;
    }

    public function calculateCashOnCashReturn(): float
    {
        $noi = $this->calculateNetOperatingIncome();
        $annualDebtService = $this->calculateAnnualDebtService();
        $cashFlow = $noi - $annualDebtService;
        $equityInvestment = $this->purchase_price - $this->loan_amount;
        
        return $equityInvestment > 0 ? ($cashFlow / $equityInvestment) * 100 : 0;
    }

    public function calculateAnnualDebtService(): float
    {
        if ($this->loan_amount <= 0 || $this->interest_rate <= 0 || $this->loan_term <= 0) {
            return 0;
        }

        $monthlyRate = $this->interest_rate / 12;
        $totalPayments = $this->loan_term * 12;
        
        if ($monthlyRate == 0) {
            return $this->loan_amount / $totalPayments * 12;
        }

        $monthlyPayment = $this->loan_amount * 
            ($monthlyRate * pow(1 + $monthlyRate, $totalPayments)) / 
            (pow(1 + $monthlyRate, $totalPayments) - 1);
        
        return $monthlyPayment * 12;
    }

    public function calculateReturnOnInvestment(): array
    {
        $noi = $this->calculateNetOperatingIncome();
        $annualDebtService = $this->calculateAnnualDebtService();
        $cashFlow = $noi - $annualDebtService;
        
        $totalInvestment = $this->purchase_price;
        $equityInvestment = $this->purchase_price - $this->loan_amount;
        
        $roi = $totalInvestment > 0 ? ($cashFlow / $totalInvestment) * 100 : 0;
        $cashOnCash = $equityInvestment > 0 ? ($cashFlow / $equityInvestment) * 100 : 0;
        
        return [
            'roi' => $roi,
            'cash_on_cash_return' => $cashOnCash,
            'net_operating_income' => $noi,
            'annual_debt_service' => $annualDebtService,
            'annual_cash_flow' => $cashFlow,
            'total_investment' => $totalInvestment,
            'equity_investment' => $equityInvestment
        ];
    }

    public function getAnalysisSummary(): array
    {
        return [
            'property_value' => $this->current_value,
            'purchase_price' => $this->purchase_price,
            'annual_income' => $this->annual_rental_income,
            'operating_expenses' => $this->operating_expenses,
            'net_operating_income' => $this->calculateNetOperatingIncome(),
            'cap_rate' => $this->calculateCapitalizationRate(),
            'gross_rent_multiplier' => $this->calculateGrossRentMultiplier(),
            'cash_on_cash_return' => $this->calculateCashOnCashReturn(),
            'vacancy_rate' => $this->vacancy_rate * 100,
            'appreciation_rate' => $this->appreciation_rate * 100,
            'holding_period' => $this->holding_period,
            'loan_to_value' => $this->purchase_price > 0 ? ($this->loan_amount / $this->purchase_price) * 100 : 0
        ];
    }
}
