<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculateROIRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_value' => 'required|numeric|min:0|max:999999999.99',
            'down_payment' => 'required|numeric|min:0|max:999999999.99',
            'loan_amount' => 'required|numeric|min:0|max:999999999.99',
            'interest_rate' => 'required|numeric|min:0|max:30',
            'loan_term' => 'required|integer|min:1|max:50',
            'annual_income' => 'required|numeric|min:0|max:999999999.99',
            'annual_expenses' => 'required|numeric|min:0|max:999999999.99',
            'holding_period' => 'required|integer|min:1|max:50',
            'appreciation_rate' => 'required|numeric|min:-20|max:50',
            'selling_costs' => 'required|numeric|min:0|max:50',
            'tax_rate' => 'required|numeric|min:0|max:50',
            'renovation_costs' => 'nullable|numeric|min:0|max:999999999.99',
            'property_management_fee' => 'nullable|numeric|min:0|max:999999999.99',
            'insurance_costs' => 'nullable|numeric|min:0|max:999999999.99',
            'property_tax_rate' => 'required|numeric|min:0|max:20',
            'maintenance_reserve_percentage' => 'required|numeric|min:0|max:20',
            'exit_strategy' => 'nullable|string|max:100',
            'risk_tolerance_level' => 'required|in:low,medium,high',
            'investment_objective' => 'nullable|string|max:255',
            'scenario_type' => 'required|in:base_case,optimistic,pessimistic,stress_test',
            'include_monte_carlo' => 'boolean',
            'include_sensitivity' => 'boolean',
            'discount_rate' => 'nullable|numeric|min:0|max:30',
            'inflation_rate' => 'nullable|numeric|min:-10|max:20'
        ];
    }

    public function messages(): array
    {
        return [
            'property_value.required' => 'يجب تحديد قيمة العقار',
            'property_value.numeric' => 'قيمة العقار يجب أن تكون رقماً',
            'property_value.min' => 'قيمة العقار يجب أن تكون 0 أو أكثر',
            'property_value.max' => 'قيمة العقار كبيرة جداً',
            'down_payment.required' => 'يجب تحديد الدفعة المقدمة',
            'down_payment.numeric' => 'الدفعة المقدمة يجب أن تكون رقماً',
            'down_payment.min' => 'الدفعة المقدمة يجب أن تكون 0 أو أكثر',
            'down_payment.max' => 'الدفعة المقدمة كبيرة جداً',
            'loan_amount.required' => 'يجب تحديد مبلغ القرض',
            'loan_amount.numeric' => 'مبلغ القرض يجب أن يكون رقماً',
            'loan_amount.min' => 'مبلغ القرض يجب أن يكون 0 أو أكثر',
            'loan_amount.max' => 'مبلغ القرض كبير جداً',
            'interest_rate.required' => 'يجب تحديد سعر الفائدة',
            'interest_rate.numeric' => 'سعر الفائدة يجب أن يكون رقماً',
            'interest_rate.min' => 'سعر الفائدة يجب أن يكون بين 0% و 30%',
            'interest_rate.max' => 'سعر الفائدة يجب أن يكون بين 0% و 30%',
            'loan_term.required' => 'يجب تحديد مدة القرض',
            'loan_term.integer' => 'مدة القرض يجب أن تكون رقماً صحيحاً',
            'loan_term.min' => 'مدة القرض يجب أن تكون بين 1 و 50 سنة',
            'loan_term.max' => 'مدة القرض يجب أن تكون بين 1 و 50 سنة',
            'annual_income.required' => 'يجب تحديد الدخل السنوي',
            'annual_income.numeric' => 'الدخل السنوي يجب أن يكون رقماً',
            'annual_income.min' => 'الدخل السنوي يجب أن يكون 0 أو أكثر',
            'annual_income.max' => 'الدخل السنوي كبير جداً',
            'annual_expenses.required' => 'يجب تحديد المصاريف السنوية',
            'annual_expenses.numeric' => 'المصاريف السنوية يجب أن تكون رقماً',
            'annual_expenses.min' => 'المصاريف السنوية يجب أن تكون 0 أو أكثر',
            'annual_expenses.max' => 'المصاريف السنوية كبيرة جداً',
            'holding_period.required' => 'يجب تحديد فترة الاستحواذ',
            'holding_period.integer' => 'فترة الاستحواذ يجب أن تكون رقماً صحيحاً',
            'holding_period.min' => 'فترة الاستحواذ يجب أن تكون بين 1 و 50 سنة',
            'holding_period.max' => 'فترة الاستحواذ يجب أن تكون بين 1 و 50 سنة',
            'appreciation_rate.required' => 'يجب تحديد معدل ارتفاع القيمة',
            'appreciation_rate.numeric' => 'معدل ارتفاع القيمة يجب أن يكون رقماً',
            'appreciation_rate.min' => 'معدل ارتفاع القيمة يجب أن يكون بين -20% و 50%',
            'appreciation_rate.max' => 'معدل ارتفاع القيمة يجب أن يكون بين -20% و 50%',
            'selling_costs.required' => 'يجب تحديد تكاليف البيع',
            'selling_costs.numeric' => 'تكاليف البيع يجب أن تكون رقماً',
            'selling_costs.min' => 'تكاليف البيع يجب أن تكون بين 0% و 50%',
            'selling_costs.max' => 'تكاليف البيع يجب أن تكون بين 0% و 50%',
            'tax_rate.required' => 'يجب تحديد ضريبة الأرباح الرأسمالية',
            'tax_rate.numeric' => 'ضريبة الأرباح الرأسمالية يجب أن تكون رقماً',
            'tax_rate.min' => 'ضريبة الأرباح الرأسمالية يجب أن تكون بين 0% و 50%',
            'tax_rate.max' => 'ضريبة الأرباح الرأسمالية يجب أن تكون بين 0% و 50%',
            'renovation_costs.numeric' => 'تكاليف التجديد يجب أن تكون رقماً',
            'renovation_costs.min' => 'تكاليف التجديد يجب أن تكون 0 أو أكثر',
            'renovation_costs.max' => 'تكاليف التجديد كبيرة جداً',
            'property_management_fee.numeric' => 'رسوم الإدارة يجب أن تكون رقماً',
            'property_management_fee.min' => 'رسوم الإدارة يجب أن تكون 0 أو أكثر',
            'property_management_fee.max' => 'رسوم الإدارة كبيرة جداً',
            'insurance_costs.numeric' => 'تكاليف التأمين يجب أن تكون رقماً',
            'insurance_costs.min' => 'تكاليف التأمين يجب أن تكون 0 أو أكثر',
            'insurance_costs.max' => 'تكاليف التأمين كبيرة جداً',
            'property_tax_rate.required' => 'يجب تحديد ضريبة العقار',
            'property_tax_rate.numeric' => 'ضريبة العقار يجب أن تكون رقماً',
            'property_tax_rate.min' => 'ضريبة العقار يجب أن تكون بين 0% و 20%',
            'property_tax_rate.max' => 'ضريبة العقار يجب أن تكون بين 0% و 20%',
            'maintenance_reserve_percentage.required' => 'يجب تحديد نسبة احتياط الصيانة',
            'maintenance_reserve_percentage.numeric' => 'نسبة احتياط الصيانة يجب أن تكون رقماً',
            'maintenance_reserve_percentage.min' => 'نسبة احتياط الصيانة يجب أن تكون بين 0% و 20%',
            'maintenance_reserve_percentage.max' => 'نسبة احتياط الصيانة يجب أن تكون بين 0% و 20%',
            'exit_strategy.string' => 'استراتيجية الخروج يجب أن تكون نصاً',
            'exit_strategy.max' => 'استراتيجية الخروج يجب ألا تزيد عن 100 حرف',
            'risk_tolerance_level.required' => 'يجب تحديد مستوى تحمل المخاطرة',
            'risk_tolerance_level.in' => 'مستوى تحمل المخاطرة غير صالح',
            'investment_objective.string' => 'الهدف الاستثماري يجب أن يكون نصاً',
            'investment_objective.max' => 'الهدف الاستثماري يجب ألا يزيد عن 255 حرف',
            'scenario_type.required' => 'يجب تحديد نوع السيناريو',
            'scenario_type.in' => 'نوع السيناريو غير صالح',
            'discount_rate.numeric' => 'معدل الخصم يجب أن يكون رقماً',
            'discount_rate.min' => 'معدل الخصم يجب أن يكون بين 0% و 30%',
            'discount_rate.max' => 'معدل الخصم يجب أن يكون بين 0% و 30%',
            'inflation_rate.numeric' => 'معدل التضخم يجب أن يكون رقماً',
            'inflation_rate.min' => 'معدل التضخم يجب أن يكون بين -10% و 20%',
            'inflation_rate.max' => 'معدل التضخم يجب أن يكون بين -10% و 20%'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'interest_rate' => $this->interest_rate / 100,
            'appreciation_rate' => $this->appreciation_rate / 100,
            'selling_costs' => $this->selling_costs / 100,
            'tax_rate' => $this->tax_rate / 100,
            'property_tax_rate' => $this->property_tax_rate / 100,
            'maintenance_reserve_percentage' => $this->maintenance_reserve_percentage / 100,
            'discount_rate' => $this->discount_rate ? $this->discount_rate / 100 : null,
            'inflation_rate' => $this->inflation_rate ? $this->inflation_rate / 100 : null,
            'include_monte_carlo' => $this->boolean('include_monte_carlo'),
            'include_sensitivity' => $this->boolean('include_sensitivity')
        ]);
    }

    public function attributes(): array
    {
        return [
            'property_value' => 'قيمة العقار',
            'down_payment' => 'الدفعة المقدمة',
            'loan_amount' => 'مبلغ القرض',
            'interest_rate' => 'سعر الفائدة',
            'loan_term' => 'مدة القرض',
            'annual_income' => 'الدخل السنوي',
            'annual_expenses' => 'المصاريف السنوية',
            'holding_period' => 'فترة الاستحواذ',
            'appreciation_rate' => 'معدل ارتفاع القيمة',
            'selling_costs' => 'تكاليف البيع',
            'tax_rate' => 'ضريبة الأرباح الرأسمالية',
            'renovation_costs' => 'تكاليف التجديد',
            'property_management_fee' => 'رسوم الإدارة',
            'insurance_costs' => 'تكاليف التأمين',
            'property_tax_rate' => 'ضريبة العقار',
            'maintenance_reserve_percentage' => 'نسبة احتياط الصيانة',
            'exit_strategy' => 'استراتيجية الخروج',
            'risk_tolerance_level' => 'مستوى تحمل المخاطرة',
            'investment_objective' => 'الهدف الاستثماري',
            'scenario_type' => 'نوع السيناريو',
            'include_monte_carlo' => 'تضمين محاكاة مونت كارلو',
            'include_sensitivity' => 'تضمين تحليل الحساسية',
            'discount_rate' => 'معدل الخصم',
            'inflation_rate' => 'معدل التضخم'
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate that down payment + loan amount equals property value (approximately)
            $propertyValue = $this->input('property_value');
            $downPayment = $this->input('down_payment');
            $loanAmount = $this->input('loan_amount');
            
            $totalInvestment = $downPayment + $loanAmount;
            $difference = abs($totalInvestment - $propertyValue);
            
            if ($difference > ($propertyValue * 0.05)) { // Allow 5% tolerance
                $validator->errors()->add('property_value', 'يجب أن تكون الدفعة المقدمة + مبلغ القرض تساوي قيمة العقار تقريباً');
            }

            // Validate that down payment is at least 20% of property value
            if ($downPayment < ($propertyValue * 0.2)) {
                $validator->errors()->add('down_payment', 'الدفعة المقدمة يجب أن تكون على الأقل 20% من قيمة العقار');
            }

            // Validate that annual income is reasonable for the property value
            $annualIncome = $this->input('annual_income');
            $incomeRatio = $annualIncome / $propertyValue;
            
            if ($incomeRatio > 0.2) { // More than 20% annual return seems unrealistic
                $validator->errors()->add('annual_income', 'الدخل السنوي مرتفع جداً بالنسبة لقيمة العقار');
            }
            
            if ($incomeRatio < 0.01) { // Less than 1% annual return seems too low
                $validator->errors()->add('annual_income', 'الدخل السنوي منخفض جداً بالنسبة لقيمة العقار');
            }

            // Validate that expenses are reasonable for the income
            $annualExpenses = $this->input('annual_expenses');
            $expenseRatio = $annualExpenses / $annualIncome;
            
            if ($expenseRatio > 0.8) { // Expenses more than 80% of income
                $validator->errors()->add('annual_expenses', 'المصاريف السنوية مرتفعة جداً بالنسبة للدخل');
            }
        });
    }
}
