<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PredictPriceRequest extends FormRequest
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
            'prediction_model' => 'required|in:linear_regression,neural_network,ensemble,time_series,hybrid',
            'time_horizon' => 'required|in:1month,3months,6months,1year',
            'current_price' => 'required|numeric|min:0',
            'historical_data' => 'nullable|array',
            'historical_data.*.sale_price' => 'required|numeric|min:0',
            'historical_data.*.sale_date' => 'required|date',
            'historical_data.*.property_size' => 'required|numeric|min:1',
            'market_factors' => 'nullable|array',
            'market_factors.growth_rate' => 'nullable|numeric|min:-50|max:50',
            'market_factors.inflation_rate' => 'nullable|numeric|min:-10|max:30',
            'market_factors.interest_rate' => 'nullable|numeric|min:0|max:30',
            'market_factors.unemployment_rate' => 'nullable|numeric|min:0|max:30',
            'market_factors.supply_demand_ratio' => 'nullable|numeric|min:0|max:2',
            'market_factors.market_sentiment' => 'nullable|numeric|min:-50|max:50',
            'economic_indicators' => 'nullable|array',
            'economic_indicators.gdp_growth' => 'nullable|numeric|min:-10|max:10',
            'economic_indicators.inflation_rate' => 'nullable|numeric|min:0|max:30',
            'economic_indicators.interest_rate' => 'nullable|numeric|min:0|max:30',
            'economic_indicators.employment_rate' => 'nullable|numeric|min:0|max:30',
            'seasonal_adjustments' => 'nullable|array',
            'seasonal_adjustments.spring_factor' => 'nullable|numeric|min:0.5|max:2',
            'seasonal_adjustments.summer_factor' => 'nullable|numeric|min:0.5|max:2',
            'seasonal_adjustments.winter_factor' => 'nullable|numeric|min:0.5|max:2',
            'seasonal_adjustments.holiday_factor' => 'nullable|numeric|min:0.5|max:2',
            'comparable_properties' => 'nullable|array',
            'comparable_properties.*.id' => 'required|exists:properties,id',
            'comparable_properties.*.sale_price' => 'required|numeric|min:0',
            'comparable_properties.*.sale_date' => 'required|date',
            'comparable_properties.*.property_size' => 'required|numeric|min:1',
            'comparable_properties.*.property_type' => 'required|string|max:50',
            'comparable_properties.*.location_score' => 'required|numeric|min:0|max:100',
            'comparable_properties.*.condition_score' => 'required|numeric|min:0|max:100',
            'comparable_properties.*.age_difference' => 'required|integer|min:0|max:50',
            'comparable_properties.*.features_score' => 'required|numeric|min:0|max:100',
            'property_features' => 'nullable|array',
            'property_features.*.feature' => 'required|string|max:200',
            'property_features.*.value' => 'required|numeric|min:0',
            'property_features.*.importance' => 'required|integer|min:1|max:10',
            'external_factors' => 'nullable|array',
            'external_factors.*.factor' => 'required|string|max:200',
            'external_factors.*.impact' => 'required|numeric|min:-100|max:100',
            'prediction_parameters' => 'nullable|array',
            'prediction_parameters.confidence_level' => 'nullable|numeric|min:0|max:1',
            'prediction_parameters.risk_tolerance' => 'nullable|numeric|min:0|max:10',
            'prediction_parameters.investment_goal' => 'required|in:maximize_occupancy,maximize_revenue,balance',
            'prediction_parameters.market_conditions' => 'nullable|string|max:500',
            'prediction_parameters.custom_weights' => 'nullable|array',
            'target_price_range' => 'nullable|array',
            'target_price_range.min' => 'nullable|numeric|min:0',
            'target_price_range.max' => 'nullable|numeric|min:0',
            'include_forecast_confidence' => 'boolean',
            'include_risk_assessment' => 'boolean',
            'include_scenario_analysis' => 'boolean',
            'include_comparative_analysis' => 'boolean',
            'include_sensitivity_analysis' => 'boolean',
            'notes' => 'nullable|string|max:1000',
            'batch_prediction' => 'boolean',
            'property_ids' => 'required_if:batch_prediction|array|min:1|max:10',
            'property_ids.*' => 'exists:properties,id',
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
            'prediction_model.required' => 'يجب اختيار نموذج التنبؤ',
            'prediction_model.in' => 'نموذج التنبؤ غير صالحح',
            'time_horizon.required' => 'يجب اختيار الأفق الزمني',
            'time_horizon.in' => 'الأفق الزمني غير صالحح',
            'current_price.required' => 'السعر الحالي مطلوب',
            'current_price.numeric' => 'السعر الحالي يجب أن يكون رقماً',
            'current_price.min' => 'السعر الحالي لا يمكن أن يكون سالباً',
            'historical_data.array' => 'البيانات التاريخية يجب أن تكون مصفوفة',
            'historical_data.*.sale_price.required' => 'سعر البيع مطلوب',
            'historical_data.*.sale_price.numeric' => 'سعر البيع يجب أن يكون رقماً',
            'historical_data.*.sale_price.min' => 'سعر البيع لا يمكن أن يكون سالباً',
            'historical_data.*.sale_date.required' => 'تاريخ البيع مطلوب',
            'historical_data.*.sale_date.date' => 'تاريخ البيع يجب أن يكون تاريخاً صالحاً',
            'historical_data.*.property_size.required' => 'مساحة العقار مطلوبة',
            'historical_data.*.property_size.numeric' => 'مساحة العقار يجب أن تكون رقماً',
            'historical_data.*.property_size.min' => 'مساحة العقار لا يمكن أن تكون أقل من 1',
            'comparable_properties.array' => 'العقارات المقارنة يجب أن تكون مصفوفة',
            'comparable_properties.*.id.required' => 'رقم العقار المقارن مطلوب',
            'comparable_properties.*.id.exists' => 'العقار المقارن غير موجود',
            'comparable_properties.*.sale_price.required' => 'سعر البيع مطلوب',
            'comparable_properties.*.sale_price.numeric' => 'سعر البيع يجب أن يكون رقماً',
            'comparable_properties.*.sale_price.min' => 'سعر البيع لا يمكن أن يكون سالباً',
            'comparable_properties.*.sale_date.required' => 'تاريخ البيع مطلوب',
            'comparable_properties.*.sale_date.date' => 'تاريخ البيع يجب أن يكون تاريخاً صالحاً',
            'comparable_properties.*.property_size.required' => 'مساحة العقار مطلوبة',
            'comparable_properties.*.property_size.numeric' => 'مساحة العقار يجب أن يكون رقماً',
            'comparable_properties.*.property_type.required' => 'نوع العقار مطلوب',
            'comparable_properties.*.property_type.max' => 'نوع العقار لا يمكن أن يتجاوز 50 حرف',
            'comparable_properties.*.location_score.required' => 'درجة الموقع مطلوب',
            'comparable_properties.*.location_score.min' => 'درجة الموقع لا يمكن أن يكون أقل من 0',
            'comparable_properties.*.location_score.max' => 'درجة الموقع لا يمكن أن يتجاوز 100',
            'comparable_properties.*.condition_score.required' => 'درجة الحالة مطلوب',
            'comparable_properties.*.condition_score.min' => 'درجة الحالة لا يمكن أن يكون أقل من 0',
            'comparable_properties.*.condition_score.max' => 'درجة الحالة لا يمكن أن يتجاوز 100',
            'comparable_properties.*.age_difference.required' => 'الفارق العمر مطلوب',
            'comparable_properties.*.age_difference.integer' => 'الفارق العمر يجب أن يكون رقماً',
            'comparable_properties.*.age_difference.min' => 'الفارق العمر لا يمكن أن يكون أقل من 0',
            'comparable_properties.*.age_difference.max' => 'الفارق العمر لا يمكن أن يتجاوز 50',
            'comparable_properties.*.features_score.required' => 'درجة الميزات مطلوب',
            'comparable_properties.*.features_score.min' => 'درجة الميزات لا يمكن أن يكون أقل من 0',
            'comparable_properties.*.features_score.max' => 'درجة الميزات لا يمكن أن يتجاوز 100',
            'property_features.array' => 'ميزات العقار يجب أن تكون مصفوفة',
            'property_features.*.feature.required' => 'الميزة مطلوبة',
            'property_features.*.feature.string' => 'الميزة يجب أن يكون نصاً',
            'property_features.*.feature.max' => 'الميزة لا يمكن أن يتجاوز 200 حرف',
            'property_features.*.value.numeric' => 'قيمة الميزة مطلوبة',
            'property_features.*.importance.integer' => 'أهمية الميزة مطلوبة',
            'property_features.*.importance.min' => 'أهمية الميزة لا يمكن أن يكون أقل من 1',
            'property_features.*.importance.max' => 'أهمية الميزة لا يمكن أن يتجاوز 10',
            'external_factors.array' => 'العوامل الخارجية يجب أن تكون مصفوفة',
            'external_factors.*.factor.required' => 'العامل الخارجي مطلوب',
            'external_factors.*.factor.string' => 'العامل الخارجي يجب أن يكون نصاً',
            'external_factors.*.factor.max' => 'العامل الخارجي لا يمكن أن يتجاوز 200 حرف',
            'external_factors.*.impact.numeric' => 'تأثير العامل الخارجي مطلوب',
            'external_factors.*.impact.min' => 'تأثير العامل الخارجي لا يمكن أن يكون أقل من -100',
            'external_factors.*.impact.max' => 'تأثير العامل الخارجي لا يمكن أن يتجاوز 100',
            'prediction_parameters.confidence_level.numeric' => 'مستوى الثقة مطلوب',
            'prediction_parameters.confidence_level.min' => 'مستوى الثقة لا يمكن أن يكون أقل من 0',
            'prediction_parameters.confidence_level.max' => 'مستوى الثقة لا يمكن أن يتجاوز 1',
            'prediction_parameters.risk_tolerance.integer' => 'مستوى المخاطرة مطلوب',
            'prediction_parameters.risk_tolerance.min' => 'مستوى المخاطرة لا يمكن أن يكون أقل من 0',
            'prediction_parameters.risk_tolerance.max' => 'مستوى المخاطرة لا يمكن أن يتجاوز 10',
            'prediction_parameters.investment_goal.in' => 'الهدف الاستثمار غير صالحح',
            'prediction_parameters.investment_goal.max' => 'الهدف الاستثمار غير صالحح',
            'target_price_range.array' => 'نطاق السعر مطلوب',
            'target_price_range.min' => 'الحد الأدنى للسعر مطلوب',
            'target_price_range.max' => 'الحد الأعلى للسعر مطلوب',
            'include_forecast_confidence' => 'تضمين مستوى الثقة في التنبؤ',
            'include_risk_assessment' => 'تضمين تقييم المخاطر في التنبؤ',
            'include_scenario_analysis' => 'تضمين تحليل السيناريو',
            'batch_prediction.boolean' => 'وضعية التنبؤ الدفععي مطلوب',
            'property_ids' => 'العقارات مطلوبة للتنبؤ الدفععي',
            'property_ids.array' => 'العقارات يجب أن تكون مصفوفة',
            'property_ids.*.exists' => 'العقارات غير موجودة',
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
            'prediction_model' => 'نموذج التنبؤ',
            'time_horizon' => 'الأفق الزمني',
            'current_price' => 'السعر الحالي',
            'historical_data' => 'البيانات التاريخية',
            'market_factors' => 'عوامل السوق',
            'economic_indicators' => 'المؤشرات الاقتصادية',
            'seasonal_adjustments' => 'تعديلات موسمية',
            'comparable_properties' => 'العقارات المقارنة',
            'property_features' => 'ميزات العقار',
            'external_factors' => 'العوامل الخارجية',
            'prediction_parameters' => 'معاملات التنبؤ',
            'target_price_range' => 'نطاق السعر',
            'confidence_level' => 'مستوى الثقة',
            'risk_tolerance' => 'مستوى المخاطرة',
            'investment_goal' => 'الهدف الاستثمار',
            'market_conditions' => 'شروط السوق',
            'custom_weights' => 'أوزان مخصصة',
            'target_price_range.min' => 'الحد الأدنى للسعر',
            'target_price_range.max' => 'الحد الأعلى للسعر',
            'include_forecast_confidence' => 'تضمين مستوى الثقة',
            'include_risk_assessment' => 'تضمين تقييم المخاطر',
            'include_scenario_analysis' => 'تضمين تحليل السيناريو',
            'batch_prediction' => 'وضعية التنبؤ الدفععي',
            'property_ids' => 'العقارات للتنبؤ الدفععي',
        ];
    }
}
