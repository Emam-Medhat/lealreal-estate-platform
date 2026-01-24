<?php

namespace App\Http\Requests\Investor;

use Illuminate\Foundation\Http\FormRequest;

class AssessRiskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'portfolio_id' => 'required|exists:investor_portfolios,id',
            'assessment_name' => 'required|string|max:255',
            'risk_type' => 'required|in:portfolio,investment,market,credit,liquidity,operational',
            'assessment_methodology' => 'required|in:quantitative,qualitative,hybrid',
            'market_risk' => 'required|numeric|min:0|max:100',
            'credit_risk' => 'required|numeric|min:0|max:100',
            'liquidity_risk' => 'required|numeric|min:0|max:100',
            'operational_risk' => 'required|numeric|min:0|max:100',
            'concentration_risk' => 'required|numeric|min:0|max:100',
            'currency_risk' => 'required|numeric|min:0|max:100',
            'regulatory_risk' => 'required|numeric|min:0|max:100',
            'risk_factors' => 'nullable|array',
            'risk_factors.*.factor' => 'required|string|max:255',
            'risk_factors.*.impact' => 'required|in:low,medium,high,critical',
            'risk_factors.*.probability' => 'required|numeric|min:0|max:1',
            'mitigation_strategies' => 'nullable|array',
            'mitigation_strategies.*.strategy' => 'required|string|max:500',
            'mitigation_strategies.*.effectiveness' => 'required|in:low,medium,high',
            'risk_tolerance_comparison' => 'nullable|array',
            'risk_tolerance_comparison.investor_tolerance' => 'required|in:conservative,moderate,aggressive',
            'risk_tolerance_comparison.portfolio_risk' => 'required|in:low,medium,high,critical',
            'stress_test_results' => 'nullable|array',
            'stress_test_results.*.scenario' => 'required|string|max:255',
            'stress_test_results.*.impact' => 'required|numeric',
            'scenario_analysis' => 'nullable|array',
            'scenario_analysis.*.scenario' => 'required|string|max:255',
            'scenario_analysis.*.outcome' => 'required|string|max:500',
            'next_review_date' => 'required|date|after:today',
            'assessor_notes' => 'nullable|string|max:2000',
            'supporting_documents' => 'nullable|array',
            'supporting_documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'portfolio_id.required' => 'Portfolio is required.',
            'portfolio_id.exists' => 'Selected portfolio does not exist.',
            'assessment_name.required' => 'Assessment name is required.',
            'risk_type.required' => 'Risk type is required.',
            'assessment_methodology.required' => 'Assessment methodology is required.',
            'market_risk.required' => 'Market risk score is required.',
            'market_risk.numeric' => 'Market risk must be a number.',
            'market_risk.min' => 'Market risk cannot be negative.',
            'market_risk.max' => 'Market risk cannot exceed 100.',
            'credit_risk.required' => 'Credit risk score is required.',
            'credit_risk.numeric' => 'Credit risk must be a number.',
            'liquidity_risk.required' => 'Liquidity risk score is required.',
            'operational_risk.required' => 'Operational risk score is required.',
            'concentration_risk.required' => 'Concentration risk score is required.',
            'currency_risk.required' => 'Currency risk score is required.',
            'regulatory_risk.required' => 'Regulatory risk score is required.',
            'risk_factors.*.factor.required' => 'Risk factor description is required.',
            'risk_factors.*.impact.required' => 'Risk factor impact is required.',
            'risk_factors.*.probability.required' => 'Risk factor probability is required.',
            'risk_factors.*.probability.numeric' => 'Risk factor probability must be a number.',
            'mitigation_strategies.*.strategy.required' => 'Mitigation strategy is required.',
            'mitigation_strategies.*.effectiveness.required' => 'Mitigation effectiveness is required.',
            'risk_tolerance_comparison.investor_tolerance.required' => 'Investor risk tolerance is required.',
            'risk_tolerance_comparison.portfolio_risk.required' => 'Portfolio risk level is required.',
            'stress_test_results.*.scenario.required' => 'Stress test scenario is required.',
            'stress_test_results.*.impact.required' => 'Stress test impact is required.',
            'scenario_analysis.*.scenario.required' => 'Scenario analysis scenario is required.',
            'scenario_analysis.*.outcome.required' => 'Scenario analysis outcome is required.',
            'next_review_date.required' => 'Next review date is required.',
            'next_review_date.after' => 'Next review date must be in the future.',
            'supporting_documents.*.mimes' => 'Supporting documents must be PDF, DOC, DOCX, XLS, or XLSX files.',
            'supporting_documents.*.max' => 'Supporting document size cannot exceed 10MB.',
        ];
    }
}
