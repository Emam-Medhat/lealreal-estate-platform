<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValuePropertyWithAiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'valuation_method' => 'required|in:comparable_sales,income_approach,cost_approach,ai_hybrid',
            'property_type' => 'nullable|string|max:100',
            'property_age' => 'nullable|integer|min:0|max:100',
            'property_condition' => 'nullable|string|in:excellent,good,fair,poor,very_poor',
            'location_quality' => 'nullable|string|in:prime,good,average,poor',
            'market_data' => 'nullable|array',
            'market_data.*.recent_sales' => 'nullable|array|min:1|max:10',
            'market_data.*.recent_sales.*.sale_price' => 'required|numeric|min:0',
            'market_data.*.recent_sales.*.sale_date' => 'required|date',
            'market_data.*.recent_sales.*.property_size' => 'required|numeric|min:1',
            'market_data.*.neighborhood_trends' => 'nullable|string|max:500',
            'market_data.*.supply_demand' => 'nullable|string|max:500',
            'improvements' => 'nullable|array',
            'improvements.*.type' => 'required|string|max:100',
            'improvements.*.cost' => 'required|numeric|min:0',
            'improvements.*.impact' => 'required|numeric|min:0|max:100',
            'special_features' => 'nullable|array',
            'special_features.*.feature' => 'required|string|max:200',
            'special_features.*.value' => 'required|numeric|min:0',
            'external_factors' => 'nullable|array',
            'external_factors.*.factor' => 'required|string|max:200',
            'external_factors.*.impact' => 'required|numeric|min:-100|max:100',
            'notes' => 'nullable|string|max:1000',
            'priority_level' => 'nullable|in:low,medium,high,urgent',
            'client_requirements' => 'nullable|array',
            'client_requirements.*.requirement' => 'required|string|max:200',
            'client_requirements.*.importance' => 'required|integer|min:1|max:10',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'property_id.required' => 'يجب اختيار العقار',
            'property_id.exists' => 'العقار المحدد غير موجود',
            'valuation_method.required' => 'يجب اختيار طريقة التقييم',
            'valuation_method.in' => 'طريقة التقييم غير صالحة',
            'property_type.string' => 'نوع العقار يجب أن يكون نصاً',
            'property_type.max' => 'نوع العقار لا يجب أن يتجاوز 100 حرف',
            'property_age.integer' => 'عمر العقار يجب أن يكون رقماً',
            'property_age.min' => 'عمر العقار لا يمكن أن يكون أقل من 0',
            'property_age.max' => 'عمر العقار لا يمكن أن يتجاوز 100 سنة',
            'property_condition.in' => 'حالة العقار غير صالحة',
            'location_quality.in' => 'جودة الموقع غير صالحة',
            'market_data.array' => 'بيانات السوق يجب أن تكون مصفوفة',
            'market_data.*.recent_sales.required' => 'بيانات المبيعات الحديثة مطلوبة',
            'market_data.*.recent_sales.min' => 'يجب توفير بيانات بيع واحدة على الأقل',
            'market_data.*.recent_sales.max' => 'لا يمكن توفير أكثر من 10 بيانات بيع',
            'market_data.*.recent_sales.*.sale_price.required' => 'سعر البيع مطلوب',
            'market_data.*.recent_sales.*.sale_price.numeric' => 'سعر البيع يجب أن يكون رقماً',
            'market_data.*.recent_sales.*.sale_price.min' => 'سعر البيع لا يمكن أن يكون سالباً',
            'market_data.*.recent_sales.*.sale_date.required' => 'تاريخ البيع مطلوب',
            'market_data.*.recent_sales.*.sale_date.date' => 'تاريخ البيع يجب أن يكون تاريخاً صالحاً',
            'market_data.*.recent_sales.*.property_size.required' => 'مساحة العقار مطلوبة',
            'market_data.*.recent_sales.*.property_size.numeric' => 'مساحة العقار يجب أن تكون رقماً',
            'market_data.*.recent_sales.*.property_size.min' => 'مساحة العقار لا يمكن أن تكون أقل من 1',
            'market_data.*.neighborhood_trends.string' => 'اتجاهات الحي يجب أن تكون نصاً',
            'market_data.*.neighborhood_trends.max' => 'اتجاهات الحي لا يمكن أن تتجاوز 500 حرف',
            'market_data.*.supply_demand.string' => 'عرض وطلب السوق يجب أن يكون نصاً',
            'market_data.*.supply_demand.max' => 'عرض وطلب السوق لا يمكن أن تتجاوز 500 حرف',
            'improvements.array' => 'التحسينات يجب أن تكون مصفوفة',
            'improvements.*.type.required' => 'نوع التحسين مطلوب',
            'improvements.*.type.string' => 'نوع التحسين يجب أن يكون نصاً',
            'improvements.*.type.max' => 'نوع التحسين لا يمكن أن يتجاوز 100 حرف',
            'improvements.*.cost.required' => 'تكلفة التحسين مطلوبة',
            'improvements.*.cost.numeric' => 'تكلفة التحسين يجب أن تكون رقماً',
            'improvements.*.cost.min' => 'تكلفة التحسين لا يمكن أن تكون سالبة',
            'improvements.*.impact.required' => 'تأثير التحسين مطلوب',
            'improvements.*.impact.numeric' => 'تأثير التحسين يجب أن يكون رقماً',
            'improvements.*.impact.min' => 'تأثير التحسين لا يمكن أن يكون سالباً',
            'improvements.*.impact.max' => 'تأثير التحسين لا يمكن أن يتجاوز 100',
            'special_features.array' => 'المميزات الخاصة يجب أن تكون مصفوفة',
            'special_features.*.feature.required' => 'الميزة الخاصة مطلوبة',
            'special_features.*.feature.string' => 'الميزة الخاصة يجب أن تكون نصاً',
            'special_features.*.feature.max' => 'الميزة الخاصة لا يمكن أن تتجاوز 200 حرف',
            'special_features.*.value.required' => 'قيمة الميزة مطلوبة',
            'special_features.*.value.numeric' => 'قيمة الميزة يجب أن تكون رقماً',
            'special_features.*.value.min' => 'قيمة الميزة لا يمكن أن تكون سالبة',
            'external_factors.array' => 'العوامل الخارجية يجب أن تكون مصفوفة',
            'external_factors.*.factor.required' => 'العامل الخارجي مطلوب',
            'external_factors.*.factor.string' => 'العامل الخارجي يجب أن يكون نصاً',
            'external_factors.*.factor.max' => 'العامل الخارجي لا يمكن أن يتجاوز 200 حرف',
            'external_factors.*.impact.required' => 'تأثير العامل الخارجي مطلوب',
            'external_factors.*.impact.numeric' => 'تأثير العامل الخارجي يجب أن يكون رقماً',
            'external_factors.*.impact.min' => 'تأثير العامل الخارجي لا يمكن أن يكون أقل من -100',
            'external_factors.*.impact.max' => 'تأثير العامل الخارجي لا يمكن أن يتجاوز 100',
            'notes.string' => 'الملاحظات يجب أن تكون نصاً',
            'notes.max' => 'الملاحظات لا يمكن أن تتجاوز 1000 حرف',
            'priority_level.in' => 'مستوى الأولوية غير صالح',
            'client_requirements.array' => 'متطلبات العميل يجب أن تكون مصفوفة',
            'client_requirements.*.requirement.required' => 'المتطلب مطلوب',
            'client_requirements.*.requirement.string' => 'المتطلب يجب أن يكون نصاً',
            'client_requirements.*.requirement.max' => 'المتطلب لا يمكن أن يتجاوز 200 حرف',
            'client_requirements.*.importance.required' => 'أهمية المتطلب مطلوبة',
            'client_requirements.*.importance.integer' => 'أهمية المتطلب يجب أن تكون رقماً',
            'client_requirements.*.importance.min' => 'أهمية المتطلب لا يمكن أن تكون أقل من 1',
            'client_requirements.*.importance.max' => 'أهمية المتطلب لا يمكن أن تتجاوز 10',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'property_id' => 'العقار',
            'valuation_method' => 'طريقة التقييم',
            'property_type' => 'نوع العقار',
            'property_age' => 'عمر العقار',
            'property_condition' => 'حالة العقار',
            'location_quality' => 'جودة الموقع',
            'market_data' => 'بيانات السوق',
            'market_data.recent_sales' => 'المبيعات الحديثة',
            'market_data.recent_sales.sale_price' => 'سعر البيع',
            'market_data.recent_sales.sale_date' => 'تاريخ البيع',
            'market_data.recent_sales.property_size' => 'مساحة العقار',
            'market_data.neighborhood_trends' => 'اتجاهات الحي',
            'market_data.supply_demand' => 'عرض وطلب السوق',
            'improvements' => 'التحسينات',
            'improvements.type' => 'نوع التحسين',
            'improvements.cost' => 'تكلفة التحسين',
            'improvements.impact' => 'تأثير التحسين',
            'special_features' => 'الميزات الخاصة',
            'special_features.feature' => 'الميزة',
            'special_features.value' => 'قيمة الميزة',
            'external_factors' => 'العوامل الخارجية',
            'external_factors.factor' => 'العامل الخارجي',
            'external_factors.impact' => 'تأثير العامل',
            'notes' => 'الملاحظات',
            'priority_level' => 'مستوى الأولوية',
            'client_requirements' => 'متطلبات العميل',
            'client_requirements.requirement' => 'المتطلب',
            'client_requirements.importance' => 'أهمية المتطلب',
        ];
    }
}
