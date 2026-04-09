<?php

namespace App\Http\Requests\Investor;

use Illuminate\Foundation\Http\FormRequest;

class InvestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'portfolio_id' => 'required|exists:investor_portfolios,id',
            'opportunity_id' => 'nullable|exists:investment_opportunities,id',
            'fund_id' => 'nullable|exists:investment_funds,id',
            'campaign_id' => 'nullable|exists:investment_crowdfunding,id',
            'loan_id' => 'nullable|exists:defi_loans,id',
            'staking_id' => 'nullable|exists:defi_staking,id',
            'investment_amount' => 'required|numeric|min:0.01|max:1000000',
            'investment_terms_accepted' => 'required|accepted',
            'auto_reinvest' => 'nullable|boolean',
            'investment_period' => 'nullable|in:1_month,3_months,6_months,1_year,3_years,5_years',
            'notes' => 'nullable|string|max:1000',
            'supporting_documents' => 'nullable|array',
            'supporting_documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'portfolio_id.required' => 'Portfolio is required.',
            'portfolio_id.exists' => 'Selected portfolio does not exist.',
            'opportunity_id.exists' => 'Selected investment opportunity does not exist.',
            'fund_id.exists' => 'Selected investment fund does not exist.',
            'campaign_id.exists' => 'Selected crowdfunding campaign does not exist.',
            'loan_id.exists' => 'Selected DeFi loan does not exist.',
            'staking_id.exists' => 'Selected DeFi staking does not exist.',
            'investment_amount.required' => 'Investment amount is required.',
            'investment_amount.numeric' => 'Investment amount must be a number.',
            'investment_amount.min' => 'Investment amount must be at least 0.01.',
            'investment_amount.max' => 'Investment amount cannot exceed 1,000,000.',
            'investment_terms_accepted.required' => 'You must accept the investment terms.',
            'investment_terms_accepted.accepted' => 'You must accept the investment terms.',
            'supporting_documents.*.mimes' => 'Supporting documents must be PDF, DOC, DOCX, JPG, JPEG, or PNG files.',
            'supporting_documents.*.max' => 'Supporting document size cannot exceed 10MB.',
        ];
    }
}
