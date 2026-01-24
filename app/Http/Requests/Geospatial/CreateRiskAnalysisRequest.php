<?php

namespace App\Http\Requests\Geospatial;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRiskAnalysisRequest extends FormRequest
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
            'risk_type' => 'required|string|in:flood,earthquake,crime,environmental,structural',
            'risk_level' => 'required|string|in:low,moderate,high,very_high',
            'risk_score' => 'required|numeric|min:0|max:10',
            'analysis_radius' => 'nullable|numeric|min:0.5|max:50',
            'flood_data' => 'nullable|array|required_if:risk_type,flood',
            'flood_data.flood_zone' => 'nullable|string|max:100',
            'flood_data.elevation' => 'nullable|numeric',
            'flood_data.flood_probability' => 'nullable|numeric|min:0|max:100',
            'flood_data.historical_floods' => 'nullable|integer|min:0',
            'flood_data.mitigation_measures' => 'nullable|array',
            'flood_data.mitigation_measures.*' => 'nullable|string|max:200',
            'earthquake_data' => 'nullable|array|required_if:risk_type,earthquake',
            'earthquake_data.seismic_zone' => 'nullable|string|max:100',
            'earthquake_data.fault_line_distance' => 'nullable|numeric|min:0',
            'earthquake_data.soil_type' => 'nullable|string|max:100',
            'earthquake_data.building_code_compliance' => 'nullable|string|in:compliant,partially_compliant,non_compliant',
            'earthquake_data.structural_assessment' => 'nullable|string|in:safe,needs_reinforcement,unsafe',
            'earthquake_data.mitigation_recommendations' => 'nullable|array',
            'earthquake_data.mitigation_recommendations.*' => 'nullable|string|max:200',
            'crime_data' => 'nullable|array|required_if:risk_type,crime',
            'crime_data.safety_score' => 'nullable|numeric|min:0|max:10',
            'crime_data.crime_rate' => 'nullable|numeric|min:0',
            'crime_data.crime_types' => 'nullable|array',
            'crime_data.crime_types.*' => 'nullable|string|max:100',
            'crime_data.police_presence' => 'nullable|string|in:high,medium,low',
            'crime_data.neighborhood_watch' => 'nullable|boolean',
            'crime_data.security_measures' => 'nullable|array',
            'crime_data.security_measures.*' => 'nullable|string|max:200',
            'environmental_data' => 'nullable|array|required_if:risk_type,environmental',
            'environmental_data.pollution_level' => 'nullable|string|in:low,medium,high',
            'environmental_data.air_quality_index' => 'nullable|integer|min:0|max:500',
            'environmental_data.noise_level' => 'nullable|numeric|min:0',
            'environmental_data.green_space_ratio' => 'nullable|numeric|min:0|max:100',
            'environmental_data.environmental_factors' => 'nullable|array',
            'environmental_data.environmental_factors.*' => 'nullable|string|max:200',
            'structural_data' => 'nullable|array|required_if:risk_type,structural',
            'structural_data.building_age' => 'nullable|integer|min:0|max:200',
            'structural_data.building_condition' => 'nullable|string|in:excellent,good,fair,poor',
            'structural_data.foundation_type' => 'nullable|string|max:100',
            'structural_data.roof_condition' => 'nullable|string|in:excellent,good,fair,poor',
            'structural_data.structural_issues' => 'nullable|array',
            'structural_data.structural_issues.*' => 'nullable|string|max:200',
            'recommendations' => 'nullable|array',
            'recommendations.*' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
            'metadata.source' => 'nullable|string|max:255',
            'metadata.version' => 'nullable|string|max:50',
            'metadata.analysis_date' => 'nullable|date',
            'metadata.assessor' => 'nullable|string|max:255',
            'metadata.certification_level' => 'nullable|string|max:100',
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
            'risk_type.required' => 'حقل نوع المخاطر مطلوب',
            'risk_type.in' => 'نوع المخاطر يجب أن يكون من القيم المسموح بها',
            'risk_level.required' => 'حقل مستوى المخاطر مطلوب',
            'risk_level.in' => 'مستوى المخاطر يجب أن يكون من القيم المسموح بها',
            'risk_score.required' => 'حقل درجة المخاطر مطلوب',
            'risk_score.numeric' => 'درجة المخاطر يجب أن تكون رقماً',
            'risk_score.min' => 'درجة المخاطر يجب أن تكون بين 0 و 10',
            'risk_score.max' => 'درجة المخاطر يجب أن تكون بين 0 و 10',
            'analysis_radius.numeric' => 'نصف القطر يجب أن يكون رقماً',
            'analysis_radius.min' => 'نصف القطر يجب أن يكون على الأقل 0.5 كم',
            'analysis_radius.max' => 'نصف القطر يجب أن لا يتجاوز 50 كم',
            'flood_data.flood_probability.numeric' => 'احتمالية الفيضان يجب أن تكون رقماً',
            'flood_data.flood_probability.min' => 'احتمالية الفيضان يجب أن تكون بين 0 و 100%',
            'flood_data.flood_probability.max' => 'احتمالية الفيضان يجب أن تكون بين 0 و 100%',
            'flood_data.historical_floods.integer' => 'عدد الفيضانات التاريخية يجب أن يكون عدداً صحيحاً',
            'flood_data.historical_floods.min' => 'عدد الفيضانات التاريخية يجب أن يكون على الأقل 0',
            'earthquake_data.fault_line_distance.numeric' => 'مسافة خط الصدع يجب أن تكون رقماً',
            'earthquake_data.fault_line_distance.min' => 'مسافة خط الصدع يجب أن تكون على الأقل 0 كم',
            'earthquake_data.building_code_compliance.in' => 'الامتثال لقوانين البناء يجب أن يكون من القيم المسموح بها',
            'earthquake_data.structural_assessment.in' => 'التقييم الهيكلي يجب أن يكون من القيم المسموح بها',
            'crime_data.safety_score.numeric' => 'درجة الأمان يجب أن تكون رقماً',
            'crime_data.safety_score.min' => 'درجة الأمان يجب أن تكون بين 0 و 10',
            'crime_data.safety_score.max' => 'درجة الأمان يجب أن تكون بين 0 و 10',
            'crime_data.crime_rate.numeric' => 'معدل الجريمة يجب أن يكون رقماً',
            'crime_data.crime_rate.min' => 'معدل الجريمة يجب أن يكون على الأقل 0',
            'crime_data.police_presence.in' => 'وجود الشرطة يجب أن يكون من القيم المسموح بها',
            'environmental_data.air_quality_index.integer' => 'مؤشر جودة الهواء يجب أن يكون عدداً صحيحاً',
            'environmental_data.air_quality_index.min' => 'مؤشر جودة الهواء يجب أن يكون بين 0 و 500',
            'environmental_data.air_quality_index.max' => 'مؤشر جودة الهواء يجب أن يكون بين 0 و 500',
            'environmental_data.noise_level.numeric' => 'مستوى الضوضاء يجب أن يكون رقماً',
            'environmental_data.noise_level.min' => 'مستوى الضوضاء يجب أن يكون على الأقل 0',
            'environmental_data.green_space_ratio.numeric' => 'نسبة المساحات الخضراء يجب أن تكون رقماً',
            'environmental_data.green_space_ratio.min' => 'نسبة المساحات الخضراء يجب أن تكون بين 0 و 100%',
            'environmental_data.green_space_ratio.max' => 'نسبة المساحات الخضراء يجب أن تكون بين 0 و 100%',
            'environmental_data.pollution_level.in' => 'مستوى التلوث يجب أن يكون من القيم المسموح بها',
            'structural_data.building_age.integer' => 'عمر المبنى يجب أن يكون عدداً صحيحاً',
            'structural_data.building_age.min' => 'عمر المبنى يجب أن يكون بين 0 و 200 سنة',
            'structural_data.building_age.max' => 'عمر المبنى يجب أن يكون بين 0 و 200 سنة',
            'structural_data.building_condition.in' => 'حالة المبنى يجب أن تكون من القيم المسموح بها',
            'structural_data.roof_condition.in' => 'حالة السقف يجب أن تكون من القيم المسموح بها',
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
            'risk_type' => 'نوع المخاطر',
            'risk_level' => 'مستوى المخاطر',
            'risk_score' => 'درجة المخاطر',
            'analysis_radius' => 'نصف القطر',
            'flood_data' => 'بيانات الفيضان',
            'flood_data.flood_zone' => 'منطقة الفيضان',
            'flood_data.elevation' => 'الارتفاع',
            'flood_data.flood_probability' => 'احتمالية الفيضان',
            'flood_data.historical_floods' => 'الفيضانات التاريخية',
            'flood_data.mitigation_measures' => 'تدابير التخفيف',
            'earthquake_data' => 'بيانات الزلازل',
            'earthquake_data.seismic_zone' => 'المنطقة الزلزالية',
            'earthquake_data.fault_line_distance' => 'مسافة خط الصدع',
            'earthquake_data.soil_type' => 'نوع التربة',
            'earthquake_data.building_code_compliance' => 'الامتثال لقوانين البناء',
            'earthquake_data.structural_assessment' => 'التقييم الهيكلي',
            'earthquake_data.mitigation_recommendations' => 'توصيات التخفيف',
            'crime_data' => 'بيانات الجريمة',
            'crime_data.safety_score' => 'درجة الأمان',
            'crime_data.crime_rate' => 'معدل الجريمة',
            'crime_data.crime_types' => 'أنواع الجريمة',
            'crime_data.police_presence' => 'وجود الشرطة',
            'crime_data.neighborhood_watch' => 'مراقبة الجوار',
            'crime_data.security_measures' => 'إجراءات الأمان',
            'environmental_data' => 'البيانات البيئية',
            'environmental_data.pollution_level' => 'مستوى التلوث',
            'environmental_data.air_quality_index' => 'مؤشر جودة الهواء',
            'environmental_data.noise_level' => 'مستوى الضوضاء',
            'environmental_data.green_space_ratio' => 'نسبة المساحات الخضراء',
            'environmental_data.environmental_factors' => 'العوامل البيئية',
            'structural_data' => 'البيانات الهيكلية',
            'structural_data.building_age' => 'عمر المبنى',
            'structural_data.building_condition' => 'حالة المبنى',
            'structural_data.foundation_type' => 'نوع الأساس',
            'structural_data.roof_condition' => 'حالة السقف',
            'structural_data.structural_issues' => 'المشاكل الهيكلية',
            'recommendations' => 'التوصيات',
            'metadata' => 'البيانات الوصفية',
            'metadata.source' => 'المصدر',
            'metadata.version' => 'الإصدار',
            'metadata.analysis_date' => 'تاريخ التحليل',
            'metadata.assessor' => 'المقيّم',
            'metadata.certification_level' => 'مستوى الشهادة',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'flood_data' => $this->flood_data ?? [],
            'earthquake_data' => $this->earthquake_data ?? [],
            'crime_data' => $this->crime_data ?? [],
            'environmental_data' => $this->environmental_data ?? [],
            'structural_data' => $this->structural_data ?? [],
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
            // Validate risk score matches risk level
            if ($this->has('risk_score') && $this->has('risk_level')) {
                $score = $this->input('risk_score');
                $level = $this->input('risk_level');
                
                $validRanges = [
                    'low' => [0, 3],
                    'moderate' => [3, 6],
                    'high' => [6, 8],
                    'very_high' => [8, 10]
                ];
                
                if (isset($validRanges[$level])) {
                    $range = $validRanges[$level];
                    if ($score < $range[0] || $score > $range[1]) {
                        $validator->errors()->add('risk_score', "درجة المخاطر يجب أن تكون بين {$range[0]} و {$range[1]} لمستوى المخاطر {$level}");
                    }
                }
            }
            
            // Validate flood data
            if ($this->risk_type === 'flood' && $this->has('flood_data')) {
                $flood = $this->input('flood_data');
                
                if (isset($flood['elevation']) && isset($flood['flood_probability'])) {
                    if ($flood['elevation'] > 100 && $flood['flood_probability'] > 50) {
                        $validator->errors()->add('flood_data.flood_probability', 'احتمالية الفيضان يجب أن تكون منخفضة عند الارتفاعات العالية');
                    }
                }
            }
            
            // Validate earthquake data
            if ($this->risk_type === 'earthquake' && $this->has('earthquake_data')) {
                $earthquake = $this->input('earthquake_data');
                
                if (isset($earthquake['fault_line_distance']) && isset($earthquake['seismic_zone'])) {
                    if ($earthquake['fault_line_distance'] < 5 && $earthquake['seismic_zone'] === 'high') {
                        $validator->errors()->add('earthquake_data.seismic_zone', 'يجب توخي الحذر عند القرب من خطوط الصدع في المناطق الزلزالية العالية');
                    }
                }
            }
            
            // Validate crime data
            if ($this->risk_type === 'crime' && $this->has('crime_data')) {
                $crime = $this->input('crime_data');
                
                if (isset($crime['safety_score']) && isset($crime['crime_rate'])) {
                    if ($crime['safety_score'] > 8 && $crime['crime_rate'] > 10) {
                        $validator->errors()->add('crime_data.crime_rate', 'معدل الجريمة يجب أن يكون منخفضاً عند درجة الأمان العالية');
                    }
                }
            }
            
            // Validate environmental data
            if ($this->risk_type === 'environmental' && $this->has('environmental_data')) {
                $environmental = $this->input('environmental_data');
                
                if (isset($environmental['air_quality_index']) && isset($environmental['pollution_level'])) {
                    if ($environmental['air_quality_index'] > 100 && $environmental['pollution_level'] === 'low') {
                        $validator->errors()->add('environmental_data.pollution_level', 'مستوى التلوث يجب أن يكون مرتفعاً عند مؤشر جودة الهواء المنخفض');
                    }
                }
            }
            
            // Validate structural data
            if ($this->risk_type === 'structural' && $this->has('structural_data')) {
                $structural = $this->input('structural_data');
                
                if (isset($structural['building_age']) && isset($structural['building_condition'])) {
                    if ($structural['building_age'] > 50 && $structural['building_condition'] === 'excellent') {
                        $validator->errors()->add('structural_data.building_condition', 'حالة المبنى يجب أن لا تكون ممتازة للأعمار التي تتجاوز 50 سنة');
                    }
                }
            }
        });
    }
}
