<?php

namespace App\Http\Requests\Geospatial;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateLocationIntelligenceRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'property_id' => 'required|exists:metaverse_properties,id',
            'intelligence_type' => 'required|string|in:market,competitive,location,investment,demographic,infrastructure,amenity,future_growth',
            'analysis_radius' => 'nullable|numeric|min:0.5|max:50',
            'market_analysis' => 'nullable|array',
            'market_analysis.market_trend' => 'nullable|string|in:bullish,bearish,stable,volatile',
            'market_analysis.price_trend' => 'nullable|string|in:increasing,decreasing,stable',
            'market_analysis.demand_level' => 'nullable|string|in:high,medium,low',
            'market_analysis.supply_level' => 'nullable|string|in:high,medium,low',
            'market_analysis.key_insights' => 'nullable|array',
            'market_analysis.key_insights.*' => 'nullable|string|max:500',
            'competitive_analysis' => 'nullable|array',
            'competitive_analysis.competition_level' => 'nullable|string|in:high,medium,low',
            'competitive_analysis.market_share' => 'nullable|numeric|min:0|max:100',
            'competitive_analysis.competitor_count' => 'nullable|integer|min:0',
            'competitive_analysis.competitive_advantages' => 'nullable|array',
            'competitive_analysis.competitive_advantages.*' => 'nullable|string|max:200',
            'growth_indicators' => 'nullable|array',
            'growth_indicators.population_growth' => 'nullable|numeric|min:-100|max:100',
            'growth_indicators.economic_growth' => 'nullable|numeric|min:-100|max:100',
            'growth_indicators.infrastructure_development' => 'nullable|string|in:high,medium,low',
            'growth_indicators.development_projects' => 'nullable|integer|min:0',
            'risk_factors' => 'nullable|array',
            'risk_factors.*' => 'nullable|string|max:200',
            'recommendations' => 'nullable|array',
            'recommendations.*' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
            'metadata.source' => 'nullable|string|max:255',
            'metadata.version' => 'nullable|string|max:50',
            'metadata.analysis_date' => 'nullable|date',
            'metadata.analyst' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'property_id.required' => 'حقل العقار مطلوب',
            'property_id.exists' => 'العقار المحدد غير موجود',
            'intelligence_type.required' => 'حقل نوع الذكاء مطلوب',
            'intelligence_type.in' => 'نوع الذكاء يجب أن يكون من القيم المسموح بها',
            'analysis_radius.numeric' => 'نصف القطر يجب أن يكون رقماً',
            'analysis_radius.min' => 'نصف القطر يجب أن يكون على الأقل 0.5 كم',
            'analysis_radius.max' => 'نصف القطر يجب أن لا يتجاوز 50 كم',
            'market_analysis.market_trend.in' => 'اتجاه السوق يجب أن يكون من القيم المسموح بها',
            'market_analysis.price_trend.in' => 'اتجاه السعر يجب أن يكون من القيم المسموح بها',
            'market_analysis.demand_level.in' => 'مستوى الطلب يجب أن يكون من القيم المسموح بها',
            'market_analysis.supply_level.in' => 'مستوى المعروض يجب أن يكون من القيم المسموح بها',
            'market_analysis.key_insights.*.string' => 'كل رؤية يجب أن تكون نصاً',
            'market_analysis.key_insights.*.max' => 'كل رؤية يجب أن لا تتجاوز 500 حرف',
            'competitive_analysis.competition_level.in' => 'مستوى المنافسة يجب أن يكون من القيم المسموح بها',
            'competitive_analysis.market_share.numeric' => 'حصة السوق يجب أن تكون رقماً',
            'competitive_analysis.market_share.min' => 'حصة السوق يجب أن تكون على الأقل 0%',
            'competitive_analysis.market_share.max' => 'حصة السوق يجب أن لا تتجاوز 100%',
            'competitive_analysis.competitor_count.integer' => 'عدد المنافسين يجب أن يكون عدداً صحيحاً',
            'competitive_analysis.competitor_count.min' => 'عدد المنافسين يجب أن يكون على الأقل 0',
            'competitive_analysis.competitive_advantages.*.string' => 'كل ميزة تنافسية يجب أن تكون نصاً',
            'competitive_analysis.competitive_advantages.*.max' => 'كل ميزة تنافسية يجب أن لا تتجاوز 200 حرف',
            'growth_indicators.population_growth.numeric' => 'نمو السكان يجب أن يكون رقماً',
            'growth_indicators.population_growth.min' => 'نمو السكان يجب أن يكون بين -100 و 100%',
            'growth_indicators.population_growth.max' => 'نمو السكان يجب أن يكون بين -100 و 100%',
            'growth_indicators.economic_growth.numeric' => 'النمو الاقتصادي يجب أن يكون رقماً',
            'growth_indicators.economic_growth.min' => 'النمو الاقتصادي يجب أن يكون بين -100 و 100%',
            'growth_indicators.economic_growth.max' => 'النمو الاقتصادي يجب أن يكون بين -100 و 100%',
            'growth_indicators.infrastructure_development.in' => 'تطوير البنية التحتية يجب أن يكون من القيم المسموح بها',
            'growth_indicators.development_projects.integer' => 'عدد مشاريع التطوير يجب أن يكون عدداً صحيحاً',
            'growth_indicators.development_projects.min' => 'عدد مشاريع التطوير يجب أن يكون على الأقل 0',
            'risk_factors.*.string' => 'كل عامل مخاطر يجب أن يكون نصاً',
            'risk_factors.*.max' => 'كل عامل مخاطر يجب أن لا يتجاوز 200 حرف',
            'recommendations.*.string' => 'كل توصية يجب أن تكون نصاً',
            'recommendations.*.max' => 'كل توصية يجب أن لا تتجاوز 500 حرف',
            'metadata.analysis_date.date' => 'تاريخ التحليل يجب أن يكون تاريخاً صالحاً',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'property_id' => 'العقار',
            'intelligence_type' => 'نوع الذكاء',
            'analysis_radius' => 'نصف القطر',
            'market_analysis' => 'تحليل السوق',
            'market_analysis.market_trend' => 'اتجاه السوق',
            'market_analysis.price_trend' => 'اتجاه السعر',
            'market_analysis.demand_level' => 'مستوى الطلب',
            'market_analysis.supply_level' => 'مستوى المعروض',
            'market_analysis.key_insights' => 'الرؤى الرئيسية',
            'competitive_analysis' => 'التحليل التنافسي',
            'competitive_analysis.competition_level' => 'مستوى المنافسة',
            'competitive_analysis.market_share' => 'حصة السوق',
            'competitive_analysis.competitor_count' => 'عدد المنافسين',
            'competitive_analysis.competitive_advantages' => 'الميزات التنافسية',
            'growth_indicators' => 'مؤشرات النمو',
            'growth_indicators.population_growth' => 'نمو السكان',
            'growth_indicators.economic_growth' => 'النمو الاقتصادي',
            'growth_indicators.infrastructure_development' => 'تطوير البنية التحتية',
            'growth_indicators.development_projects' => 'مشاريع التطوير',
            'risk_factors' => 'عوامل المخاطر',
            'recommendations' => 'التوصيات',
            'metadata' => 'البيانات الوصفية',
            'metadata.source' => 'المصدر',
            'metadata.version' => 'الإصدار',
            'metadata.analysis_date' => 'تاريخ التحليل',
            'metadata.analyst' => 'المحلل',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'market_analysis' => $this->market_analysis ?? [],
            'competitive_analysis' => $this->competitive_analysis ?? [],
            'growth_indicators' => $this->growth_indicators ?? [],
            'risk_factors' => $this->risk_factors ?? [],
            'recommendations' => $this->recommendations ?? [],
            'metadata' => $this->metadata ?? [],
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate analysis radius based on intelligence type
            if ($this->intelligence_type === 'demographic' && $this->analysis_radius < 2) {
                $validator->errors()->add('analysis_radius', 'نصف القطر للتحليل الديموغرافي يجب أن يكون على الأقل 2 كم');
            }
            
            if ($this->intelligence_type === 'infrastructure' && $this->analysis_radius > 20) {
                $validator->errors()->add('analysis_radius', 'نصف القطر لتحليل البنية التحتية يجب أن لا يتجاوز 20 كم');
            }
            
            // Validate market analysis consistency
            if ($this->has('market_analysis')) {
                $market = $this->input('market_analysis');
                
                if (isset($market['demand_level']) && isset($market['supply_level'])) {
                    if ($market['demand_level'] === 'high' && $market['supply_level'] === 'high') {
                        $validator->errors()->add('market_analysis.demand_level', 'لا يمكن أن يكون الطلب والمعروض مرتفعين في نفس الوقت');
                    }
                }
                
                if (isset($market['market_trend']) && isset($market['price_trend'])) {
                    if ($market['market_trend'] === 'bullish' && $market['price_trend'] === 'decreasing') {
                        $validator->errors()->add('market_analysis.price_trend', 'اتجاه السعر يجب أن يكون متزاياً عندما يكون اتجاه السوق صاعداً');
                    }
                }
            }
            
            // Validate competitive analysis
            if ($this->has('competitive_analysis')) {
                $competitive = $this->input('competitive_analysis');
                
                if (isset($competitive['competition_level']) && isset($competitive['competitor_count'])) {
                    if ($competitive['competition_level'] === 'high' && $competitive['competitor_count'] < 5) {
                        $validator->errors()->add('competitive_analysis.competitor_count', 'عدد المنافسين يجب أن يكون 5 أو أكثر عند مستوى منافسة عالي');
                    }
                }
            }
            
            // Validate growth indicators
            if ($this->has('growth_indicators')) {
                $growth = $this->input('growth_indicators');
                
                if (isset($growth['infrastructure_development']) && isset($growth['development_projects'])) {
                    if ($growth['infrastructure_development'] === 'high' && $growth['development_projects'] < 3) {
                        $validator->errors()->add('growth_indicators.development_projects', 'عدد مشاريع التطوير يجب أن يكون 3 أو أكثر عند مستوى تطوير بنية تحتية عالي');
                    }
                }
            }
        });
    }
}
