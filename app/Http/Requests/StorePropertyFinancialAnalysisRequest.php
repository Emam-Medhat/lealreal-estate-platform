<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyFinancialAnalysisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'user_id' => 'required|exists:users,id',
            'analysis_date' => 'required|date',
            'current_value' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date|before_or_equal:analysis_date',
            'annual_rental_income' => 'required|numeric|min:0',
            'operating_expenses' => 'required|numeric|min:0',
            'vacancy_rate' => 'required|numeric|min:0|max:100',
            'appreciation_rate' => 'required|numeric|min:-20|max:50',
            'inflation_rate' => 'required|numeric|min:-10|max:20',
            'discount_rate' => 'required|numeric|min:0|max:50',
            'holding_period' => 'required|integer|min:1|max:50',
            'loan_amount' => 'nullable|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:30',
            'loan_term' => 'nullable|integer|min:1|max:50',
            'property_type' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'market_conditions' => 'nullable|array',
            'analysis_type' => 'required|in:investment,valuation,portfolio,comprehensive',
            'status' => 'required|in:active,inactive,completed,archived',
            'notes' => 'nullable|string|max:5000'
        ];
    }

    public function messages(): array
    {
        return [
            'property_id.required' => 'يجب اختيار العقار',
            'property_id.exists' => 'العقار المحدد غير موجود',
            'user_id.required' => 'يجب تحديد المستخدم',
            'user_id.exists' => 'المستخدم المحدد غير موجود',
            'analysis_date.required' => 'يجب تحديد تاريخ التحليل',
            'analysis_date.date' => 'تاريخ التحليل غير صالح',
            'current_value.required' => 'يجب تحديد القيمة الحالية',
            'current_value.numeric' => 'القيمة الحالية يجب أن تكون رقماً',
            'current_value.min' => 'القيمة الحالية يجب أن تكون 0 أو أكثر',
            'purchase_price.required' => 'يجب تحديد سعر الشراء',
            'purchase_price.numeric' => 'سعر الشراء يجب أن يكون رقماً',
            'purchase_price.min' => 'سعر الشراء يجب أن يكون 0 أو أكثر',
            'purchase_date.required' => 'يجب تحديد تاريخ الشراء',
            'purchase_date.date' => 'تاريخ الشراء غير صالح',
            'purchase_date.before_or_equal' => 'تاريخ الشراء يجب أن يكون قبل أو نفس تاريخ التحليل',
            'annual_rental_income.required' => 'يجب تحديد الدخل الإيجاري السنوي',
            'annual_rental_income.numeric' => 'الدخل الإيجاري السنوي يجب أن يكون رقماً',
            'annual_rental_income.min' => 'الدخل الإيجاري السنوي يجب أن يكون 0 أو أكثر',
            'operating_expenses.required' => 'يجب تحديد المصاريف التشغيلية',
            'operating_expenses.numeric' => 'المصاريف التشغيلية يجب أن تكون رقماً',
            'operating_expenses.min' => 'المصاريف التشغيلية يجب أن تكون 0 أو أكثر',
            'vacancy_rate.required' => 'يجب تحديد معدل الشغور',
            'vacancy_rate.numeric' => 'معدل الشغور يجب أن يكون رقماً',
            'vacancy_rate.min' => 'معدل الشغور يجب أن يكون بين 0 و 100',
            'vacancy_rate.max' => 'معدل الشغور يجب أن يكون بين 0 و 100',
            'appreciation_rate.required' => 'يجب تحديد معدل ارتفاع القيمة',
            'appreciation_rate.numeric' => 'معدل ارتفاع القيمة يجب أن يكون رقماً',
            'appreciation_rate.min' => 'معدل ارتفاع القيمة يجب أن يكون بين -20% و 50%',
            'appreciation_rate.max' => 'معدل ارتفاع القيمة يجب أن يكون بين -20% و 50%',
            'inflation_rate.required' => 'يجب تحديد معدل التضخم',
            'inflation_rate.numeric' => 'معدل التضخم يجب أن يكون رقماً',
            'inflation_rate.min' => 'معدل التضخم يجب أن يكون بين -10% و 20%',
            'inflation_rate.max' => 'معدل التضخم يجب أن يكون بين -10% و 20%',
            'discount_rate.required' => 'يجب تحديد معدل الخصم',
            'discount_rate.numeric' => 'معدل الخصم يجب أن يكون رقماً',
            'discount_rate.min' => 'معدل الخصم يجب أن يكون بين 0% و 50%',
            'discount_rate.max' => 'معدل الخصم يجب أن يكون بين 0% و 50%',
            'holding_period.required' => 'يجب تحديد فترة الاستحواذ',
            'holding_period.integer' => 'فترة الاستحواذ يجب أن تكون رقماً صحيحاً',
            'holding_period.min' => 'فترة الاستحواذ يجب أن تكون بين 1 و 50 سنة',
            'holding_period.max' => 'فترة الاستحواذ يجب أن تكون بين 1 و 50 سنة',
            'loan_amount.numeric' => 'مبلغ القرض يجب أن يكون رقماً',
            'loan_amount.min' => 'مبلغ القرض يجب أن يكون 0 أو أكثر',
            'interest_rate.numeric' => 'سعر الفائدة يجب أن يكون رقماً',
            'interest_rate.min' => 'سعر الفائدة يجب أن يكون بين 0% و 30%',
            'interest_rate.max' => 'سعر الفائدة يجب أن يكون بين 0% و 30%',
            'loan_term.integer' => 'مدة القرض يجب أن تكون رقماً صحيحاً',
            'loan_term.min' => 'مدة القرض يجب أن تكون بين 1 و 50 سنة',
            'loan_term.max' => 'مدة القرض يجب أن تكون بين 1 و 50 سنة',
            'property_type.required' => 'يجب تحديد نوع العقار',
            'property_type.string' => 'نوع العقار يجب أن يكون نصاً',
            'property_type.max' => 'نوع العقار يجب ألا يزيد عن 100 حرف',
            'location.required' => 'يجب تحديد الموقع',
            'location.string' => 'الموقع يجب أن يكون نصاً',
            'location.max' => 'الموقع يجب ألا يزيد عن 255 حرف',
            'analysis_type.required' => 'يجب تحديد نوع التحليل',
            'analysis_type.in' => 'نوع التحليل غير صالح',
            'status.required' => 'يجب تحديد الحالة',
            'status.in' => 'الحالة غير صالحة',
            'notes.string' => 'الملاحظات يجب أن تكون نصاً',
            'notes.max' => 'الملاحظات يجب ألا تزيد عن 5000 حرف'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'vacancy_rate' => $this->vacancy_rate / 100,
            'appreciation_rate' => $this->appreciation_rate / 100,
            'inflation_rate' => $this->inflation_rate / 100,
            'discount_rate' => $this->discount_rate / 100,
            'interest_rate' => $this->interest_rate ? $this->interest_rate / 100 : null
        ]);
    }

    public function attributes(): array
    {
        return [
            'property_id' => 'العقار',
            'user_id' => 'المستخدم',
            'analysis_date' => 'تاريخ التحليل',
            'current_value' => 'القيمة الحالية',
            'purchase_price' => 'سعر الشراء',
            'purchase_date' => 'تاريخ الشراء',
            'annual_rental_income' => 'الدخل الإيجاري السنوي',
            'operating_expenses' => 'المصاريف التشغيلية',
            'vacancy_rate' => 'معدل الشغور',
            'appreciation_rate' => 'معدل ارتفاع القيمة',
            'inflation_rate' => 'معدل التضخم',
            'discount_rate' => 'معدل الخصم',
            'holding_period' => 'فترة الاستحواذ',
            'loan_amount' => 'مبلغ القرض',
            'interest_rate' => 'سعر الفائدة',
            'loan_term' => 'مدة القرض',
            'property_type' => 'نوع العقار',
            'location' => 'الموقع',
            'market_conditions' => 'ظروف السوق',
            'analysis_type' => 'نوع التحليل',
            'status' => 'الحالة',
            'notes' => 'الملاحظات'
        ];
    }
}
