<?php

namespace App\Http\Requests\Investor;

use Illuminate\Foundation\Http\FormRequest;

class CalculateRoiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'portfolio_id' => 'required|exists:investor_portfolios,id',
            'calculation_type' => 'required|in:simple,advanced,risk_adjusted,benchmark',
            'initial_investment' => 'required|numeric|min:0',
            'current_value' => 'required|numeric|min:0',
            'total_returns' => 'nullable|numeric|min:0',
            'end_date' => 'nullable|date|after_or_equal:today',
            'include_dividends' => 'nullable|boolean',
            'calculation_method' => 'required|in:time_weighted,money_weighted,xirr',
            'benchmark_comparison' => 'nullable|array',
            'benchmark_comparison.*.benchmark' => 'required|string|max:100',
            'benchmark_comparison.*.value' => 'required|numeric',
            'assumptions' => 'nullable|array',
            'assumptions.growth_rate' => 'nullable|numeric|min:-100|max:100',
            'assumptions.inflation_rate' => 'nullable|numeric|min:0|max:50',
            'assumptions.tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'portfolio_id.required' => 'Portfolio is required.',
            'portfolio_id.exists' => 'Selected portfolio does not exist.',
            'calculation_type.required' => 'Calculation type is required.',
            'initial_investment.required' => 'Initial investment amount is required.',
            'initial_investment.numeric' => 'Initial investment must be a number.',
            'initial_investment.min' => 'Initial investment cannot be negative.',
            'current_value.required' => 'Current value is required.',
            'current_value.numeric' => 'Current value must be a number.',
            'current_value.min' => 'Current value cannot be negative.',
            'end_date.after_or_equal' => 'End date must be today or in the future.',
            'calculation_method.required' => 'Calculation method is required.',
            'benchmark_comparison.*.benchmark.required' => 'Benchmark name is required.',
            'benchmark_comparison.*.value.required' => 'Benchmark value is required.',
            'benchmark_comparison.*.value.numeric' => 'Benchmark value must be a number.',
            'assumptions.growth_rate.numeric' => 'Growth rate must be a number.',
            'assumptions.inflation_rate.numeric' => 'Inflation rate must be a number.',
            'assumptions.tax_rate.numeric' => 'Tax rate must be a number.',
        ];
    }
}
