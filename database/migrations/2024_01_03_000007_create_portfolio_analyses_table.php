<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('analysis_name');
            $table->date('analysis_date');
            $table->integer('total_properties_count');
            $table->decimal('total_property_value', 18, 2);
            $table->decimal('total_equity_value', 18, 2);
            $table->decimal('total_loan_balance', 18, 2);
            $table->decimal('total_annual_income', 18, 2);
            $table->decimal('total_annual_expenses', 18, 2);
            $table->decimal('total_net_operating_income', 18, 2);
            $table->decimal('portfolio_cap_rate', 8, 4);
            $table->decimal('portfolio_cash_flow', 18, 2);
            $table->decimal('portfolio_cash_on_cash_return', 8, 4);
            $table->decimal('portfolio_roi', 8, 4);
            $table->decimal('diversification_score', 5, 3);
            $table->decimal('geographic_diversification', 5, 3);
            $table->decimal('property_type_diversification', 5, 3);
            $table->decimal('price_range_diversification', 5, 3);
            $table->decimal('risk_score', 5, 3);
            $table->decimal('volatility_index', 5, 3);
            $table->json('correlation_matrix')->nullable();
            $table->decimal('beta_coefficient', 8, 3)->nullable();
            $table->decimal('sharpe_ratio', 8, 3)->nullable();
            $table->decimal('sortino_ratio', 8, 3)->nullable();
            $table->decimal('maximum_drawdown', 5, 3)->nullable();
            $table->decimal('value_at_risk_95', 15, 2)->nullable();
            $table->decimal('expected_shortfall_95', 15, 2)->nullable();
            $table->json('stress_test_results')->nullable();
            $table->json('monte_carlo_simulation')->nullable();
            $table->json('optimization_results')->nullable();
            $table->json('rebalancing_recommendations')->nullable();
            $table->json('performance_attribution')->nullable();
            $table->json('benchmark_comparison')->nullable();
            $table->json('market_exposure')->nullable();
            $table->json('sector_allocation')->nullable();
            $table->json('geographic_allocation')->nullable();
            $table->json('liquidity_analysis')->nullable();
            $table->json('concentration_risk')->nullable();
            $table->decimal('leverage_ratio', 5, 3);
            $table->decimal('debt_service_coverage_ratio', 5, 3);
            $table->decimal('interest_coverage_ratio', 5, 3);
            $table->json('breakdown_metrics')->nullable();
            $table->json('trend_analysis')->nullable();
            $table->json('forecast_projections')->nullable();
            $table->json('risk_adjusted_returns')->nullable();
            $table->date('analysis_period_start')->nullable();
            $table->date('analysis_period_end')->nullable();
            $table->string('benchmark_index')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'analysis_date']);
            $table->index('analysis_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_analyses');
    }
};
