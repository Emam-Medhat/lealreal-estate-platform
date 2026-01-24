<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculateAppreciationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_value' => 'required|numeric|min:0|max:999999999.99',
            'base_appreciation_rate' => 'required|numeric|min:-20|max:50',
            'projection_period' => 'required|integer|min:1|max:50',
            'appreciation_model' => 'required|in:linear,compound,variable,market_based',
            'market_factors' => 'nullable|array',
            'market_factors.inflation_trend' => 'nullable|numeric|min:-20|max:20',
            'market_factors.supply_demand' => 'nullable|numeric|min:-50|max:50',
            'market_factors.interest_rate_trend' => 'nullable|numeric|min:-10|max:10',
            'market_factors.gdp_growth' => 'nullable|numeric|min:-10|max:20',
            'market_factors.unemployment_rate' => 'nullable|numeric|min:0|max:50',
            'market_factors.population_growth' => 'nullable|numeric|min:-5|max:10',
            'market_factors.market_sentiment' => 'nullable|integer|min:1|max:10',
            'property_factors' => 'nullable|array',
            'property_factors.property_age' => 'nullable|numeric|min:0|max:100',
            'property_factors.renovation_value' => 'nullable|numeric|min:0|max:100',
            'property_factors.property_condition' => 'nullable|integer|min:1|max:10',
            'property_factors.location_score' => 'nullable|integer|min:1|max:10',
            'property_factors.amenity_score' => 'nullable|integer|min:1|max:10',
            'property_factors.size_factor' => 'nullable|numeric|min:0.5|max:2',
            'economic_assumptions' => 'nullable|array',
            'economic_assumptions.inflation_rate' => 'nullable|numeric|min:-10|max:20',
            'economic_assumptions.interest_rate_trend' => 'nullable|numeric|min:-10|max:10',
            'economic_assumptions.gdp_growth' => 'nullable|numeric|min:-10|max:20',
            'economic_assumptions.inflation_adjustment' => 'boolean',
            'economic_assumptions.real_appreciation' => 'boolean',
            'scenario_analysis' => 'boolean',
            'monte_carlo_simulation' => 'boolean',
            'sensitivity_analysis' => 'boolean',
            'confidence_level' => 'nullable|integer|min:50|max:99',
            'risk_adjustment' => 'boolean',
            'include_market_cycles' => 'boolean',
            'cycle_length' => 'nullable|integer|min:3|max:20',
            'seasonal_adjustment' => 'boolean',
            'geographic_factors' => 'nullable|array',
            'geographic_factors.urbanization_trend' => 'nullable|numeric|min:-10|max:10',
            'geographic_factors.infrastructure_development' => 'nullable|numeric|min:0|max:100',
            'geographic_factors.school_quality_impact' => 'nullable|numeric|min:-20|max:20',
            'geographic_factors.transportation_access' => 'nullable|integer|min:1|max:10',
            'regulatory_factors' => 'nullable|array',
            'regulatory_factors.zoning_changes' => 'nullable|numeric|min:-50|max:50',
            'regulatory_factors.tax_policy_impact' => 'nullable|numeric|min:-30|max:30',
            'regulatory_factors.building_regulations' => 'nullable|integer|min:1|max:10',
            'comparable_properties' => 'nullable|array',
            'comparable_properties.count' => 'nullable|integer|min:3|max:100',
            'comparable_properties.distance_radius' => 'nullable|numeric|min:0.1|max:50',
            'comparable_properties.time_period' => 'nullable|integer|min:1|max:10',
            'export_format' => 'nullable|in:json,csv,pdf,excel',
            'include_projections' => 'boolean',
            'projection_frequency' => 'nullable|in:monthly,quarterly,annually'
        ];
    }

    public function messages(): array
    {
        return [
            'current_value.required' => 'يجب تحديد القيمة الحالية',
            'current_value.numeric' => 'القيمة الحالية يجب أن تكون رقماً',
            'current_value.min' => 'القيمة الحالية يجب أن تكون 0 أو أكثر',
            'current_value.max' => 'القيمة الحالية كبيرة جداً',
            'base_appreciation_rate.required' => 'يجب تحديد معدل ارتفاع القيمة الأساسي',
            'base_appreciation_rate.numeric' => 'معدل ارتفاع القيمة الأساسي يجب أن يكون رقماً',
            'base_appreciation_rate.min' => 'معدل ارتفاع القيمة الأساسي يجب أن يكون بين -20% و 50%',
            'base_appreciation_rate.max' => 'معدل ارتفاع القيمة الأساسي يجب أن يكون بين -20% و 50%',
            'projection_period.required' => 'يجب تحديد فترة التوقع',
            'projection_period.integer' => 'فترة التوقع يجب أن تكون رقماً صحيحاً',
            'projection_period.min' => 'فترة التوقع يجب أن تكون بين 1 و 50 سنة',
            'projection_period.max' => 'فترة التوقع يجب أن تكون بين 1 و 50 سنة',
            'appreciation_model.required' => 'يجب تحديد نموذج التوقع',
            'appreciation_model.in' => 'نموذج التوقع غير صالح',
            'market_factors.inflation_trend.numeric' => 'اتجاه التضخم يجب أن يكون رقماً',
            'market_factors.inflation_trend.min' => 'اتجاه التضخم يجب أن يكون بين -20% و 20%',
            'market_factors.inflation_trend.max' => 'اتجاه التضخم يجب أن يكون بين -20% و 20%',
            'market_factors.supply_demand.numeric' => 'العرض والطلب يجب أن يكون رقماً',
            'market_factors.supply_demand.min' => 'العرض والطلب يجب أن يكون بين -50% و 50%',
            'market_factors.supply_demand.max' => 'العرض والطلب يجب أن يكون بين -50% و 50%',
            'market_factors.interest_rate_trend.numeric' => 'اتجاه سعر الفائدة يجب أن يكون رقماً',
            'market_factors.interest_rate_trend.min' => 'اتجاه سعر الفائدة يجب أن يكون بين -10% و 10%',
            'market_factors.interest_rate_trend.max' => 'اتجاه سعر الفائدة يجب أن يكون بين -10% و 10%',
            'market_factors.gdp_growth.numeric' => 'نمو الناتج المحلي يجب أن يكون رقماً',
            'market_factors.gdp_growth.min' => 'نمو الناتج المحلي يجب أن يكون بين -10% و 20%',
            'market_factors.gdp_growth.max' => 'نمو الناتج المحلي يجب أن يكون بين -10% و 20%',
            'market_factors.unemployment_rate.numeric' => 'معدل البطالة يجب أن يكون رقماً',
            'market_factors.unemployment_rate.min' => 'معدل البطالة يجب أن يكون بين 0% و 50%',
            'market_factors.unemployment_rate.max' => 'معدل البطالة يجب أن يكون بين 0% و 50%',
            'market_factors.population_growth.numeric' => 'نمو السكان يجب أن يكون رقماً',
            'market_factors.population_growth.min' => 'نمو السكان يجب أن يكون بين -5% و 10%',
            'market_factors.population_growth.max' => 'نمو السكان يجب أن يكون بين -5% و 10%',
            'market_factors.market_sentiment.integer' => 'معنويات السوق يجب أن تكون رقماً صحيحاً',
            'market_factors.market_sentiment.min' => 'معنويات السوق يجب أن تكون بين 1 و 10',
            'market_factors.market_sentiment.max' => 'معنويات السوق يجب أن تكون بين 1 و 10',
            'property_factors.property_age.numeric' => 'عمر العقار يجب أن يكون رقماً',
            'property_factors.property_age.min' => 'عمر العقار يجب أن يكون بين 0% و 100%',
            'property_factors.property_age.max' => 'عمر العقار يجب أن يكون بين 0% و 100%',
            'property_factors.renovation_value.numeric' => 'قيمة التجديد يجب أن تكون رقماً',
            'property_factors.renovation_value.min' => 'قيمة التجديد يجب أن تكون بين 0% و 100%',
            'property_factors.renovation_value.max' => 'قيمة التجديد يجب أن تكون بين 0% و 100%',
            'property_factors.property_condition.integer' => 'حالة العقار يجب أن تكون رقماً صحيحاً',
            'property_factors.property_condition.min' => 'حالة العقار يجب أن تكون بين 1 و 10',
            'property_factors.property_condition.max' => 'حالة العقار يجب أن تكون بين 1 و 10',
            'property_factors.location_score.integer' => 'درجة الموقع يجب أن تكون رقماً صحيحاً',
            'property_factors.location_score.min' => 'درجة الموقع يجب أن تكون بين 1 و 10',
            'property_factors.location_score.max' => 'درجة الموقع يجب أن تكون بين 1 و 10',
            'property_factors.amenity_score.integer' => 'درجة المرافق يجب أن تكون رقماً صحيحاً',
            'property_factors.amenity_score.min' => 'درجة المرافق يجب أن تكون بين 1 و 10',
            'property_factors.amenity_score.max' => 'درجة المرافق يجب أن تكون بين 1 و 10',
            'property_factors.size_factor.numeric' => 'عامل الحجم يجب أن يكون رقماً',
            'property_factors.size_factor.min' => 'عامل الحجم يجب أن يكون بين 0.5 و 2',
            'property_factors.size_factor.max' => 'عامل الحجم يجب أن يكون بين 0.5 و 2',
            'economic_assumptions.inflation_rate.numeric' => 'معدل التضخم يجب أن يكون رقماً',
            'economic_assumptions.inflation_rate.min' => 'معدل التضخم يجب أن يكون بين -10% و 20%',
            'economic_assumptions.inflation_rate.max' => 'معدل التضخم يجب أن يكون بين -10% و 20%',
            'economic_assumptions.interest_rate_trend.numeric' => 'اتجاه سعر الفائدة يجب أن يكون رقماً',
            'economic_assumptions.interest_rate_trend.min' => 'اتجاه سعر الفائدة يجب أن يكون بين -10% و 10%',
            'economic_assumptions.interest_rate_trend.max' => 'اتجاه سعر الفائدة يجب أن يكون بين -10% و 10%',
            'economic_assumptions.gdp_growth.numeric' => 'نمو الناتج المحلي يجب أن يكون رقماً',
            'economic_assumptions.gdp_growth.min' => 'نمو الناتج المحلي يجب أن يكون بين -10% و 20%',
            'economic_assumptions.gdp_growth.max' => 'نمو الناتج المحلي يجب أن يكون بين -10% و 20%',
            'confidence_level.integer' => 'مستوى الثقة يجب أن يكون رقماً صحيحاً',
            'confidence_level.min' => 'مستوى الثقة يجب أن يكون بين 50 و 99',
            'confidence_level.max' => 'مستوى الثقة يجب أن يكون بين 50 و 99',
            'cycle_length.integer' => 'طول الدورة يجب أن يكون رقماً صحيحاً',
            'cycle_length.min' => 'طول الدورة يجب أن يكون بين 3 و 20 سنة',
            'cycle_length.max' => 'طول الدورة يجب أن يكون بين 3 و 20 سنة',
            'geographic_factors.urbanization_trend.numeric' => 'اتجاه التحضر يجب أن يكون رقماً',
            'geographic_factors.urbanization_trend.min' => 'اتجاه التحضر يجب أن يكون بين -10% و 10%',
            'geographic_factors.urbanization_trend.max' => 'اتجاه التحضر يجب أن يكون بين -10% و 10%',
            'geographic_factors.infrastructure_development.numeric' => 'تطوير البنية التحتية يجب أن يكون رقماً',
            'geographic_factors.infrastructure_development.min' => 'تطوير البنية التحتية يجب أن يكون بين 0% و 100%',
            'geographic_factors.infrastructure_development.max' => 'تطوير البنية التحتية يجب أن يكون بين 0% و 100%',
            'geographic_factors.school_quality_impact.numeric' => 'تأثير جودة المدارس يجب أن يكون رقماً',
            'geographic_factors.school_quality_impact.min' => 'تأثير جودة المدارس يجب أن يكون بين -20% و 20%',
            'geographic_factors.school_quality_impact.max' => 'تأثير جودة المدارس يجب أن يكون بين -20% و 20%',
            'geographic_factors.transportation_access.integer' => 'وصول النقل يجب أن يكون رقماً صحيحاً',
            'geographic_factors.transportation_access.min' => 'وصول النقل يجب أن يكون بين 1 و 10',
            'geographic_factors.transportation_access.max' => 'وصول النقل يجب أن يكون بين 1 و 10',
            'regulatory_factors.zoning_changes.numeric' => 'تغييرات التقسيم يجب أن تكون رقماً',
            'regulatory_factors.zoning_changes.min' => 'تغييرات التقسيم يجب أن تكون بين -50% و 50%',
            'regulatory_factors.zoning_changes.max' => 'تغييرات التقسيم يجب أن تكون بين -50% و 50%',
            'regulatory_factors.tax_policy_impact.numeric' => 'تأثير السياسة الضريبية يجب أن يكون رقماً',
            'regulatory_factors.tax_policy_impact.min' => 'تأثير السياسة الضريبية يجب أن يكون بين -30% و 30%',
            'regulatory_factors.tax_policy_impact.max' => 'تأثير السياسة الضريبية يجب أن يكون بين -30% و 30%',
            'regulatory_factors.building_regulations.integer' => 'لوائحات البناء يجب أن تكون رقماً صحيحاً',
            'regulatory_factors.building_regulations.min' => 'لوائح البناء يجب أن تكون بين 1 و 10',
            'regulatory_factors.building_regulations.max' => 'لوائح البناء يجب أن تكون بين 1 و 10',
            'comparable_properties.count.integer' => 'عدد العقارات المقارنة يجب أن يكون رقماً صحيحاً',
            'comparable_properties.count.min' => 'عدد العقارات المقارنة يجب أن يكون بين 3 و 100',
            'comparable_properties.count.max' => 'عدد العقارات المقارنة يجب أن يكون بين 3 و 100',
            'comparable_properties.distance_radius.numeric' => 'نصف القطر للمسافة يجب أن يكون رقماً',
            'comparable_properties.distance_radius.min' => 'نصف القطر للمسافة يجب أن يكون بين 0.1 و 50 كم',
            'comparable_properties.distance_radius.max' => 'نصف القطر للمسافة يجب أن يكون بين 0.1 و 50 كم',
            'comparable_properties.time_period.integer' => 'الفترة الزمنية يجب أن تكون رقماً صحيحاً',
            'comparable_properties.time_period.min' => 'الفترة الزمنية يجب أن تكون بين 1 و 10 سنوات',
            'comparable_properties.time_period.max' => 'الفترة الزمنية يجب أن تكون بين 1 و 10 سنوات',
            'export_format.in' => 'تنسيق التصدير غير صالح',
            'projection_frequency.in' => 'تكرار التوقع غير صالح'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'base_appreciation_rate' => $this->base_appreciation_rate / 100,
            'scenario_analysis' => $this->boolean('scenario_analysis'),
            'monte_carlo_simulation' => $this->boolean('monte_carlo_simulation'),
            'sensitivity_analysis' => $this->boolean('sensitivity_analysis'),
            'risk_adjustment' => $this->boolean('risk_adjustment'),
            'include_market_cycles' => $this->boolean('include_market_cycles'),
            'seasonal_adjustment' => $this->boolean('seasonal_adjustment'),
            'economic_assumptions.inflation_adjustment' => $this->boolean('economic_assumptions.inflation_adjustment'),
            'economic_assumptions.real_appreciation' => $this->boolean('economic_assumptions.real_appreciation'),
            'include_projections' => $this->boolean('include_projections')
        ]);

        // Convert nested percentage fields
        if ($this->has('market_factors')) {
            $marketFactors = $this->input('market_factors');
            foreach (['inflation_trend', 'supply_demand', 'interest_rate_trend', 'gdp_growth', 'unemployment_rate', 'population_growth'] as $field) {
                if (isset($marketFactors[$field])) {
                    $marketFactors[$field] = $marketFactors[$field] / 100;
                }
            }
            $this->merge(['market_factors' => $marketFactors]);
        }

        if ($this->has('property_factors')) {
            $propertyFactors = $this->input('property_factors');
            foreach (['property_age', 'renovation_value'] as $field) {
                if (isset($propertyFactors[$field])) {
                    $propertyFactors[$field] = $propertyFactors[$field] / 100;
                }
            }
            $this->merge(['property_factors' => $propertyFactors]);
        }

        if ($this->has('economic_assumptions')) {
            $economicAssumptions = $this->input('economic_assumptions');
            foreach (['inflation_rate', 'interest_rate_trend', 'gdp_growth'] as $field) {
                if (isset($economicAssumptions[$field])) {
                    $economicAssumptions[$field] = $economicAssumptions[$field] / 100;
                }
            }
            $this->merge(['economic_assumptions' => $economicAssumptions]);
        }

        if ($this->has('geographic_factors')) {
            $geographicFactors = $this->input('geographic_factors');
            foreach (['urbanization_trend', 'infrastructure_development', 'school_quality_impact'] as $field) {
                if (isset($geographicFactors[$field])) {
                    $geographicFactors[$field] = $geographicFactors[$field] / 100;
                }
            }
            $this->merge(['geographic_factors' => $geographicFactors]);
        }

        if ($this->has('regulatory_factors')) {
            $regulatoryFactors = $this->input('regulatory_factors');
            foreach (['zoning_changes', 'tax_policy_impact'] as $field) {
                if (isset($regulatoryFactors[$field])) {
                    $regulatoryFactors[$field] = $regulatoryFactors[$field] / 100;
                }
            }
            $this->merge(['regulatory_factors' => $regulatoryFactors]);
        }
    }

    public function attributes(): array
    {
        return [
            'current_value' => 'القيمة الحالية',
            'base_appreciation_rate' => 'معدل ارتفاع القيمة الأساسي',
            'projection_period' => 'فترة التوقع',
            'appreciation_model' => 'نموذج التوقع',
            'market_factors' => 'عوامل السوق',
            'market_factors.inflation_trend' => 'اتجاه التضخم',
            'market_factors.supply_demand' => 'العرض والطلب',
            'market_factors.interest_rate_trend' => 'اتجاه سعر الفائدة',
            'market_factors.gdp_growth' => 'نمو الناتج المحلي',
            'market_factors.unemployment_rate' => 'معدل البطالة',
            'market_factors.population_growth' => 'نمو السكان',
            'market_factors.market_sentiment' => 'معنويات السوق',
            'property_factors' => 'عوامل العقار',
            'property_factors.property_age' => 'عمر العقار',
            'property_factors.renovation_value' => 'قيمة التجديد',
            'property_factors.property_condition' => 'حالة العقار',
            'property_factors.location_score' => 'درجة الموقع',
            'property_factors.amenity_score' => 'درجة المرافق',
            'property_factors.size_factor' => 'عامل الحجم',
            'economic_assumptions' => 'الافتراضات الاقتصادية',
            'economic_assumptions.inflation_rate' => 'معدل التضخم',
            'economic_assumptions.interest_rate_trend' => 'اتجاه سعر الفائدة',
            'economic_assumptions.gdp_growth' => 'نمو الناتج المحلي',
            'economic_assumptions.inflation_adjustment' => 'تعديل التضخم',
            'economic_assumptions.real_appreciation' => 'ارتفاع القيمة الحقيقي',
            'scenario_analysis' => 'تحليل السيناريوهات',
            'monte_carlo_simulation' => 'محاكاة مونت كارلو',
            'sensitivity_analysis' => 'تحليل الحساسية',
            'confidence_level' => 'مستوى الثقة',
            'risk_adjustment' => 'تعديل المخاطرة',
            'include_market_cycles' => 'تضمين دورات السوق',
            'cycle_length' => 'طول الدورة',
            'seasonal_adjustment' => 'تعديل موسمي',
            'geographic_factors' => 'العوامل الجغرافية',
            'geographic_factors.urbanization_trend' => 'اتجاه التحضر',
            'geographic_factors.infrastructure_development' => 'تطوير البنية التحتية',
            'geographic_factors.school_quality_impact' => 'تأثير جودة المدارس',
            'geographic_factors.transportation_access' => 'وصول النقل',
            'regulatory_factors' => 'العوامل التنظيمية',
            'regulatory_factors.zoning_changes' => 'تغييرات التقسيم',
            'regulatory_factors.tax_policy_impact' => 'تأثير السياسة الضريبية',
            'regulatory_factors.building_regulations' => 'لوائح البناء',
            'comparable_properties' => 'العقارات المقارنة',
            'comparable_properties.count' => 'عدد العقارات المقارنة',
            'comparable_properties.distance_radius' => 'نصف القطر للمسافة',
            'comparable_properties.time_period' => 'الفترة الزمنية',
            'export_format' => 'تنسيق التصدير',
            'include_projections' => 'تضمين التوقعات',
            'projection_frequency' => 'تكرار التوقع'
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate that projection period is reasonable for the model
            $projectionPeriod = $this->input('projection_period');
            $model = $this->input('appreciation_model');
            
            if ($model === 'market_based' && $projectionPeriod > 20) {
                $validator->errors()->add('projection_period', 'فترة التوقعات الطويلة جداً غير مناسبة للنموذج القائم على السوق');
            }

            // Validate that base appreciation rate is reasonable
            $baseRate = $this->input('base_appreciation_rate');
            if (abs($baseRate) > 0.3 && !$this->input('scenario_analysis')) {
                $validator->errors()->add('base_appreciation_rate', 'معدلات ارتفاع القيمة المتطرفة تتطلب تحليل سيناريوهات');
            }

            // Validate market factors consistency
            $marketFactors = $this->input('market_factors', []);
            if (isset($marketFactors['supply_demand']) && isset($marketFactors['inflation_trend'])) {
                if ($marketFactors['supply_demand'] < -0.2 && $marketFactors['inflation_trend'] > 0.1) {
                    $validator->errors()->add('market_factors.supply_demand', 'عدم تناسق في عوامل السوق');
                }
            }

            // Validate property factors
            $propertyFactors = $this->input('property_factors', []);
            if (isset($propertyFactors['property_age']) && isset($propertyFactors['renovation_value'])) {
                if ($propertyFactors['property_age'] > 0.8 && $propertyFactors['renovation_value'] > 0.5) {
                    $validator->errors()->add('property_factors.renovation_value', 'قيمة التجديد مرتفعة جداً لعمر العقار');
                }
            }

            // Validate that Monte Carlo simulation has sufficient data
            if ($this->input('monte_carlo_simulation') && !$this->input('sensitivity_analysis')) {
                $validator->errors()->add('monte_carlo_simulation', 'محاكاة مونت كارلو تتطلب تحليل حساسية');
            }

            // Validate confidence level for advanced analysis
            if ($this->input('monte_carlo_simulation') && !$this->input('confidence_level')) {
                $validator->errors()->add('confidence_level', 'محاكاة مونت كارلو تتطلب تحديد مستوى الثقة');
            }

            // Validate comparable properties data
            $comparableProperties = $this->input('comparable_properties', []);
            if (isset($comparableProperties['count']) && $comparableProperties['count'] < 5 && $this->input('confidence_level') > 90) {
                $validator->errors()->add('comparable_properties.count', 'عدد العقارات المقارنة غير كافي لمستوى الثقة المطلوب');
            }
        });
    }
}
