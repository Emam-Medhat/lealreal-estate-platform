<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalyzeCashFlowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_value' => 'required|numeric|min:0|max:999999999.99',
            'monthly_rent' => 'required|numeric|min:0|max:999999999.99',
            'other_income' => 'required|numeric|min:0|max:999999999.99',
            'vacancy_rate' => 'required|numeric|min:0|max:100',
            'operating_expenses' => 'required|numeric|min:0|max:999999999.99',
            'capital_expenditures' => 'required|numeric|min:0|max:999999999.99',
            'loan_payment' => 'nullable|numeric|min:0|max:999999999.99',
            'property_tax' => 'required|numeric|min:0|max:999999999.99',
            'insurance' => 'required|numeric|min:0|max:999999999.99',
            'management_fee' => 'required|numeric|min:0|max:999999999.99',
            'projection_years' => 'required|integer|min:1|max:50',
            'rent_growth' => 'required|numeric|min:-20|max:50',
            'expense_growth' => 'required|numeric|min:-20|max:50',
            'appreciation_rate' => 'required|numeric|min:-20|max:50',
            'include_inflation' => 'boolean',
            'inflation_rate' => 'nullable|numeric|min:-10|max:20',
            'maintenance_reserve' => 'nullable|numeric|min:0|max:20',
            'vacancy_trend' => 'nullable|numeric|min:-50|max:50',
            'economic_factors' => 'nullable|array',
            'market_factors' => 'nullable|array',
            'property_factors' => 'nullable|array',
            'scenario_type' => 'required|in:conservative,moderate,aggressive',
            'include_sensitivity' => 'boolean',
            'sensitivity_variables' => 'nullable|array',
            'sensitivity_ranges' => 'nullable|array',
            'benchmark_comparison' => 'boolean',
            'benchmark_properties' => 'nullable|array',
            'export_format' => 'nullable|in:json,csv,pdf,excel'
        ];
    }

    public function messages(): array
    {
        return [
            'property_value.required' => 'يجب تحديد قيمة العقار',
            'property_value.numeric' => 'قيمة العقار يجب أن تكون رقماً',
            'property_value.min' => 'قيمة العقار يجب أن تكون 0 أو أكثر',
            'property_value.max' => 'قيمة العقار كبيرة جداً',
            'monthly_rent.required' => 'يجب تحديد الإيجار الشهري',
            'monthly_rent.numeric' => 'الإيجار الشهري يجب أن يكون رقماً',
            'monthly_rent.min' => 'الإيجار الشهري يجب أن يكون 0 أو أكثر',
            'monthly_rent.max' => 'الإيجار الشهري كبير جداً',
            'other_income.required' => 'يجب تحديد الدخل الآخر',
            'other_income.numeric' => 'الدخل الآخر يجب أن يكون رقماً',
            'other_income.min' => 'الدخل الآخر يجب أن يكون 0 أو أكثر',
            'other_income.max' => 'الدخل الآخر كبير جداً',
            'vacancy_rate.required' => 'يجب تحديد معدل الشغور',
            'vacancy_rate.numeric' => 'معدل الشغور يجب أن يكون رقماً',
            'vacancy_rate.min' => 'معدل الشغور يجب أن يكون بين 0% و 100%',
            'vacancy_rate.max' => 'معدل الشغور يجب أن يكون بين 0% و 100%',
            'operating_expenses.required' => 'يجب تحديد المصاريف التشغيلية',
            'operating_expenses.numeric' => 'المصاريف التشغيلية يجب أن تكون رقماً',
            'operating_expenses.min' => 'المصاريف التشغيلية يجب أن تكون 0 أو أكثر',
            'operating_expenses.max' => 'المصاريف التشغيلية كبيرة جداً',
            'capital_expenditures.required' => 'يجب تحديد النفقات الرأسمالية',
            'capital_expenditures.numeric' => 'النفقات الرأسمالية يجب أن تكون رقماً',
            'capital_expenditures.min' => 'النفقات الرأسمالية يجب أن تكون 0 أو أكثر',
            'capital_expenditures.max' => 'النفقات الرأسمالية كبيرة جداً',
            'loan_payment.numeric' => 'قسط القرض يجب أن يكون رقماً',
            'loan_payment.min' => 'قسط القرض يجب أن يكون 0 أو أكثر',
            'loan_payment.max' => 'قسط القرض كبير جداً',
            'property_tax.required' => 'يجب تحديد ضريبة العقار',
            'property_tax.numeric' => 'ضريبة العقار يجب أن تكون رقماً',
            'property_tax.min' => 'ضريبة العقار يجب أن تكون 0 أو أكثر',
            'property_tax.max' => 'ضريبة العقار كبيرة جداً',
            'insurance.required' => 'يجب تحديد التأمين',
            'insurance.numeric' => 'التأمين يجب أن يكون رقماً',
            'insurance.min' => 'التأمين يجب أن يكون 0 أو أكثر',
            'insurance.max' => 'التأمين كبير جداً',
            'management_fee.required' => 'يجب تحديد رسوم الإدارة',
            'management_fee.numeric' => 'رسوم الإدارة يجب أن تكون رقماً',
            'management_fee.min' => 'رسوم الإدارة يجب أن تكون 0 أو أكثر',
            'management_fee.max' => 'رسوم الإدارة كبيرة جداً',
            'projection_years.required' => 'يجب تحديد فترة التوقع',
            'projection_years.integer' => 'فترة التوقع يجب أن تكون رقماً صحيحاً',
            'projection_years.min' => 'فترة التوقع يجب أن تكون بين 1 و 50 سنة',
            'projection_years.max' => 'فترة التوقع يجب أن تكون بين 1 و 50 سنة',
            'rent_growth.required' => 'يجب تحديد معدل نمو الإيجار',
            'rent_growth.numeric' => 'معدل نمو الإيجار يجب أن يكون رقماً',
            'rent_growth.min' => 'معدل نمو الإيجار يجب أن يكون بين -20% و 50%',
            'rent_growth.max' => 'معدل نمو الإيجار يجب أن يكون بين -20% و 50%',
            'expense_growth.required' => 'يجب تحديد معدل نمو المصاريف',
            'expense_growth.numeric' => 'معدل نمو المصاريف يجب أن يكون رقماً',
            'expense_growth.min' => 'معدل نمو المصاريف يجب أن يكون بين -20% و 50%',
            'expense_growth.max' => 'معدل نمو المصاريف يجب أن يكون بين -20% و 50%',
            'appreciation_rate.required' => 'يجب تحديد معدل ارتفاع القيمة',
            'appreciation_rate.numeric' => 'معدل ارتفاع القيمة يجب أن يكون رقماً',
            'appreciation_rate.min' => 'معدل ارتفاع القيمة يجب أن يكون بين -20% و 50%',
            'appreciation_rate.max' => 'معدل ارتفاع القيمة يجب أن يكون بين -20% و 50%',
            'inflation_rate.numeric' => 'معدل التضخم يجب أن يكون رقماً',
            'inflation_rate.min' => 'معدل التضخم يجب أن يكون بين -10% و 20%',
            'inflation_rate.max' => 'معدل التضخم يجب أن يكون بين -10% و 20%',
            'maintenance_reserve.numeric' => 'احتياط الصيانة يجب أن يكون رقماً',
            'maintenance_reserve.min' => 'احتياط الصيانة يجب أن يكون بين 0% و 20%',
            'maintenance_reserve.max' => 'احتياط الصيانة يجب أن يكون بين 0% و 20%',
            'vacancy_trend.numeric' => 'اتجاه الشغور يجب أن يكون رقماً',
            'vacancy_trend.min' => 'اتجاه الشغور يجب أن يكون بين -50% و 50%',
            'vacancy_trend.max' => 'اتجاه الشغور يجب أن يكون بين -50% و 50%',
            'scenario_type.required' => 'يجب تحديد نوع السيناريو',
            'scenario_type.in' => 'نوع السيناريو غير صالح',
            'export_format.in' => 'تنسيق التصدير غير صالح'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'vacancy_rate' => $this->vacancy_rate / 100,
            'rent_growth' => $this->rent_growth / 100,
            'expense_growth' => $this->expense_growth / 100,
            'appreciation_rate' => $this->appreciation_rate / 100,
            'inflation_rate' => $this->inflation_rate ? $this->inflation_rate / 100 : null,
            'maintenance_reserve' => $this->maintenance_reserve ? $this->maintenance_reserve / 100 : null,
            'vacancy_trend' => $this->vacancy_trend ? $this->vacancy_trend / 100 : null,
            'include_inflation' => $this->boolean('include_inflation'),
            'include_sensitivity' => $this->boolean('include_sensitivity'),
            'benchmark_comparison' => $this->boolean('benchmark_comparison')
        ]);
    }

    public function attributes(): array
    {
        return [
            'property_value' => 'قيمة العقار',
            'monthly_rent' => 'الإيجار الشهري',
            'other_income' => 'الدخل الآخر',
            'vacancy_rate' => 'معدل الشغور',
            'operating_expenses' => 'المصاريف التشغيلية',
            'capital_expenditures' => 'النفقات الرأسمالية',
            'loan_payment' => 'قسط القرض',
            'property_tax' => 'ضريبة العقار',
            'insurance' => 'التأمين',
            'management_fee' => 'رسوم الإدارة',
            'projection_years' => 'فترة التوقع',
            'rent_growth' => 'معدل نمو الإيجار',
            'expense_growth' => 'معدل نمو المصاريف',
            'appreciation_rate' => 'معدل ارتفاع القيمة',
            'include_inflation' => 'تضمين التضخم',
            'inflation_rate' => 'معدل التضخم',
            'maintenance_reserve' => 'احتياط الصيانة',
            'vacancy_trend' => 'اتجاه الشغور',
            'economic_factors' => 'العوامل الاقتصادية',
            'market_factors' => 'عوامل السوق',
            'property_factors' => 'عوامل العقار',
            'scenario_type' => 'نوع السيناريو',
            'include_sensitivity' => 'تضمين تحليل الحساسية',
            'sensitivity_variables' => 'متغيرات الحساسية',
            'sensitivity_ranges' => 'نطاقات الحساسية',
            'benchmark_comparison' => 'مقارنة بالمعيار',
            'benchmark_properties' => 'عقارات المعيار',
            'export_format' => 'تنسيق التصدير'
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate that monthly income is reasonable for property value
            $propertyValue = $this->input('property_value');
            $monthlyRent = $this->input('monthly_rent');
            $otherIncome = $this->input('other_income');
            $totalMonthlyIncome = $monthlyRent + $otherIncome;
            
            $annualIncome = $totalMonthlyIncome * 12;
            $incomeRatio = $annualIncome / $propertyValue;
            
            if ($incomeRatio > 0.25) { // More than 25% annual return seems unrealistic
                $validator->errors()->add('monthly_rent', 'الدخل الشهري مرتفع جداً بالنسبة لقيمة العقار');
            }
            
            if ($incomeRatio < 0.005) { // Less than 0.5% annual return seems too low
                $validator->errors()->add('monthly_rent', 'الدخل الشهري منخفض جداً بالنسبة لقيمة العقار');
            }

            // Validate that expenses are reasonable for the income
            $operatingExpenses = $this->input('operating_expenses');
            $propertyTax = $this->input('property_tax');
            $insurance = $this->input('insurance');
            $managementFee = $this->input('management_fee');
            $totalMonthlyExpenses = $operatingExpenses + $propertyTax + $insurance + $managementFee;
            
            $expenseRatio = $totalMonthlyExpenses / $totalMonthlyIncome;
            
            if ($expenseRatio > 0.8) { // Expenses more than 80% of income
                $validator->errors()->add('operating_expenses', 'المصاريف الشهرية مرتفعة جداً بالنسبة للدخل');
            }

            // Validate capital expenditures
            $capitalExpenditures = $this->input('capital_expenditures');
            $capExRatio = $capitalExpenditures / $annualIncome;
            
            if ($capExRatio > 0.3) { // CapEx more than 30% of annual income
                $validator->errors()->add('capital_expenditures', 'النفقات الرأسمالية مرتفعة جداً بالنسبة للدخل السنوي');
            }

            // Validate loan payment if provided
            $loanPayment = $this->input('loan_payment');
            if ($loanPayment && $loanPayment > $totalMonthlyIncome) {
                $validator->errors()->add('loan_payment', 'قسط القرض لا يمكن أن يتجاوز الدخل الشهري');
            }

            // Validate growth rates
            $rentGrowth = $this->input('rent_growth');
            $expenseGrowth = $this->input('expense_growth');
            
            if ($rentGrowth < 0 && $expenseGrowth > 0) {
                $validator->errors()->add('rent_growth', 'لا يمكن أن ينخفض الإيجار بينما ترتفع المصاريف');
            }

            // Validate vacancy rate
            $vacancyRate = $this->input('vacancy_rate');
            if ($vacancyRate > 0.3) { // More than 30% vacancy
                $validator->errors()->add('vacancy_rate', 'معدل الشغور مرتفع جداً');
            }

            // Validate projection years
            $projectionYears = $this->input('projection_years');
            if ($projectionYears > 30 && !$this->input('include_sensitivity')) {
                $validator->errors()->add('projection_years', 'فترة التوقعات الطويلة تتطلب تحليل حساسية');
            }
        });
    }
}
