<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class PortfolioAnalysis extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'analysis_name',
        'analysis_date',
        'total_properties_count',
        'total_property_value',
        'total_equity_value',
        'total_loan_balance',
        'total_annual_income',
        'total_annual_expenses',
        'total_net_operating_income',
        'portfolio_cap_rate',
        'portfolio_cash_flow',
        'portfolio_cash_on_cash_return',
        'portfolio_roi',
        'diversification_score',
        'geographic_diversification',
        'property_type_diversification',
        'price_range_diversification',
        'risk_score',
        'volatility_index',
        'correlation_matrix',
        'beta_coefficient',
        'sharpe_ratio',
        'sortino_ratio',
        'maximum_drawdown',
        'value_at_risk_95',
        'expected_shortfall_95',
        'stress_test_results',
        'monte_carlo_simulation',
        'optimization_results',
        'rebalancing_recommendations',
        'performance_attribution',
        'benchmark_comparison',
        'market_exposure',
        'sector_allocation',
        'geographic_allocation',
        'liquidity_analysis',
        'concentration_risk',
        'leverage_ratio',
        'debt_service_coverage_ratio',
        'interest_coverage_ratio',
        'breakdown_metrics',
        'trend_analysis',
        'forecast_projections',
        'risk_adjusted_returns',
        'analysis_period_start',
        'analysis_period_end',
        'benchmark_index',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'analysis_date' => 'date',
        'analysis_period_start' => 'date',
        'analysis_period_end' => 'date',
        'total_properties_count' => 'integer',
        'total_property_value' => 'decimal:2',
        'total_equity_value' => 'decimal:2',
        'total_loan_balance' => 'decimal:2',
        'total_annual_income' => 'decimal:2',
        'total_annual_expenses' => 'decimal:2',
        'total_net_operating_income' => 'decimal:2',
        'portfolio_cap_rate' => 'decimal:4',
        'portfolio_cash_flow' => 'decimal:2',
        'portfolio_cash_on_cash_return' => 'decimal:4',
        'portfolio_roi' => 'decimal:4',
        'diversification_score' => 'decimal:3',
        'geographic_diversification' => 'decimal:3',
        'property_type_diversification' => 'decimal:3',
        'price_range_diversification' => 'decimal:3',
        'risk_score' => 'decimal:3',
        'volatility_index' => 'decimal:3',
        'correlation_matrix' => 'json',
        'beta_coefficient' => 'decimal:3',
        'sharpe_ratio' => 'decimal:3',
        'sortino_ratio' => 'decimal:3',
        'maximum_drawdown' => 'decimal:3',
        'value_at_risk_95' => 'decimal:2',
        'expected_shortfall_95' => 'decimal:2',
        'stress_test_results' => 'json',
        'monte_carlo_simulation' => 'json',
        'optimization_results' => 'json',
        'rebalancing_recommendations' => 'json',
        'performance_attribution' => 'json',
        'benchmark_comparison' => 'json',
        'market_exposure' => 'json',
        'sector_allocation' => 'json',
        'geographic_allocation' => 'json',
        'liquidity_analysis' => 'json',
        'concentration_risk' => 'json',
        'leverage_ratio' => 'decimal:3',
        'debt_service_coverage_ratio' => 'decimal:3',
        'interest_coverage_ratio' => 'decimal:3',
        'breakdown_metrics' => 'json',
        'trend_analysis' => 'json',
        'forecast_projections' => 'json',
        'risk_adjusted_returns' => 'json',
        'notes' => 'text'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByUser(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('analysis_date', 'desc');
    }

    public function calculateLoanToValue(): float
    {
        return $this->total_property_value > 0 
            ? ($this->total_loan_balance / $this->total_property_value) * 100 
            : 0;
    }

    public function calculateEquityToValue(): float
    {
        return $this->total_property_value > 0 
            ? ($this->total_equity_value / $this->total_property_value) * 100 
            : 0;
    }

    public function calculateOperatingExpenseRatio(): float
    {
        return $this->total_annual_income > 0 
            ? ($this->total_annual_expenses / $this->total_annual_income) * 100 
            : 0;
    }

    public function calculateNetOperatingIncomeMargin(): float
    {
        return $this->total_annual_income > 0 
            ? ($this->total_net_operating_income / $this->total_annual_income) * 100 
            : 0;
    }

    public function calculatePortfolioBeta(): float
    {
        return $this->beta_coefficient ?? 1.0;
    }

    public function assessPortfolioRisk(): string
    {
        $riskScore = $this->risk_score;
        
        if ($riskScore <= 0.3) return 'low';
        if ($riskScore <= 0.6) return 'medium';
        return 'high';
    }

    public function assessDiversification(): string
    {
        $diversificationScore = $this->diversification_score;
        
        if ($diversificationScore >= 0.8) return 'excellent';
        if ($diversificationScore >= 0.6) return 'good';
        if ($diversificationScore >= 0.4) return 'moderate';
        return 'poor';
    }

    public function calculatePortfolioEfficiency(): float
    {
        if ($this->risk_score <= 0) {
            return 0;
        }
        
        return $this->portfolio_roi / $this->risk_score;
    }

    public function getPortfolioMetrics(): array
    {
        return [
            'total_properties_count' => $this->total_properties_count,
            'total_property_value' => $this->total_property_value,
            'total_equity_value' => $this->total_equity_value,
            'total_loan_balance' => $this->total_loan_balance,
            'total_annual_income' => $this->total_annual_income,
            'total_annual_expenses' => $this->total_annual_expenses,
            'total_net_operating_income' => $this->total_net_operating_income,
            'portfolio_cap_rate' => $this->portfolio_cap_rate,
            'portfolio_cash_flow' => $this->portfolio_cash_flow,
            'portfolio_cash_on_cash_return' => $this->portfolio_cash_on_cash_return,
            'portfolio_roi' => $this->portfolio_roi,
            'loan_to_value' => $this->calculateLoanToValue(),
            'equity_to_value' => $this->calculateEquityToValue(),
            'operating_expense_ratio' => $this->calculateOperatingExpenseRatio(),
            'net_operating_income_margin' => $this->calculateNetOperatingIncomeMargin(),
            'leverage_ratio' => $this->leverage_ratio,
            'debt_service_coverage_ratio' => $this->debt_service_coverage_ratio,
            'interest_coverage_ratio' => $this->interest_coverage_ratio
        ];
    }

    public function getRiskAnalysis(): array
    {
        return [
            'risk_score' => $this->risk_score,
            'risk_level' => $this->assessPortfolioRisk(),
            'volatility_index' => $this->volatility_index,
            'beta_coefficient' => $this->beta_coefficient,
            'sharpe_ratio' => $this->sharpe_ratio,
            'sortino_ratio' => $this->sortino_ratio,
            'maximum_drawdown' => $this->maximum_drawdown,
            'value_at_risk_95' => $this->value_at_risk_95,
            'expected_shortfall_95' => $this->expected_shortfall_95,
            'concentration_risk' => $this->concentration_risk,
            'portfolio_efficiency' => $this->calculatePortfolioEfficiency()
        ];
    }

    public function getDiversificationAnalysis(): array
    {
        return [
            'diversification_score' => $this->diversification_score,
            'diversification_level' => $this->assessDiversification(),
            'geographic_diversification' => $this->geographic_diversification,
            'property_type_diversification' => $this->property_type_diversification,
            'price_range_diversification' => $this->price_range_diversification,
            'sector_allocation' => $this->sector_allocation,
            'geographic_allocation' => $this->geographic_allocation,
            'concentration_risk' => $this->concentration_risk
        ];
    }

    public function getPerformanceAnalysis(): array
    {
        return [
            'portfolio_roi' => $this->portfolio_roi,
            'portfolio_cash_on_cash_return' => $this->portfolio_cash_on_cash_return,
            'portfolio_cap_rate' => $this->portfolio_cap_rate,
            'sharpe_ratio' => $this->sharpe_ratio,
            'sortino_ratio' => $this->sortino_ratio,
            'beta_coefficient' => $this->beta_coefficient,
            'performance_attribution' => $this->performance_attribution,
            'benchmark_comparison' => $this->benchmark_comparison,
            'risk_adjusted_returns' => $this->risk_adjusted_returns,
            'trend_analysis' => $this->trend_analysis
        ];
    }

    public function getOptimizationRecommendations(): array
    {
        return [
            'optimization_results' => $this->optimization_results,
            'rebalancing_recommendations' => $this->rebalancing_recommendations,
            'stress_test_results' => $this->stress_test_results,
            'monte_carlo_simulation' => $this->monte_carlo_simulation,
            'forecast_projections' => $this->forecast_projections
        ];
    }

    public function calculatePortfolioAlpha(): float
    {
        $benchmarkReturn = $this->getBenchmarkReturn();
        $portfolioReturn = $this->portfolio_roi;
        $beta = $this->calculatePortfolioBeta();
        $riskFreeRate = 0.02; // Assuming 2% risk-free rate
        
        return $portfolioReturn - ($riskFreeRate + $beta * ($benchmarkReturn - $riskFreeRate));
    }

    private function getBenchmarkReturn(): float
    {
        // This would typically fetch actual benchmark data
        return 8.0; // Assuming 8% benchmark return
    }

    public function assessPortfolioHealth(): string
    {
        $healthScore = 0;
        
        // Risk assessment (30%)
        if ($this->risk_score <= 0.4) $healthScore += 30;
        elseif ($this->risk_score <= 0.7) $healthScore += 20;
        else $healthScore += 10;
        
        // Diversification (25%)
        if ($this->diversification_score >= 0.7) $healthScore += 25;
        elseif ($this->diversification_score >= 0.5) $healthScore += 15;
        else $healthScore += 5;
        
        // Performance (25%)
        if ($this->portfolio_roi >= 12) $healthScore += 25;
        elseif ($this->portfolio_roi >= 8) $healthScore += 15;
        else $healthScore += 5;
        
        // Efficiency (20%)
        if ($this->sharpe_ratio >= 1.5) $healthScore += 20;
        elseif ($this->sharpe_ratio >= 1.0) $healthScore += 10;
        else $healthScore += 5;
        
        if ($healthScore >= 80) return 'excellent';
        if ($healthScore >= 60) return 'good';
        if ($healthScore >= 40) return 'fair';
        return 'poor';
    }
}
