<?php

namespace App\Http\Requests\Geospatial;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateGeospatialAnalysisRequest extends FormRequest
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
            'analysis_type' => 'required|string|in:market,location,demographic,risk,investment',
            'analysis_radius' => 'nullable|numeric|min:0.5|max:50',
            'analysis_parameters' => 'nullable|array',
            'analysis_parameters.include_economic_factors' => 'nullable|boolean',
            'analysis_parameters.include_market_sentiment' => 'nullable|boolean',
            'analysis_parameters.include_historical_data' => 'nullable|boolean',
            'analysis_parameters.time_period' => 'nullable|string|in:1_year,3_years,5_years,10_years',
            'analysis_parameters.weight_factors' => 'nullable|array',
            'analysis_parameters.weight_factors.location' => 'nullable|numeric|min:0|max:1',
            'analysis_parameters.weight_factors.market' => 'nullable|numeric|min:0|max:1',
            'analysis_parameters.weight_factors.demographic' => 'nullable|numeric|min:0|max:1',
            'analysis_parameters.weight_factors.economic' => 'nullable|numeric|min:0|max:1',
            'metadata' => 'nullable|array',
            'metadata.source' => 'nullable|string|max:255',
            'metadata.version' => 'nullable|string|max:50',
            'metadata.notes' => 'nullable|string|max:1000',
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
            'analysis_type.required' => 'حقل نوع التحليل مطلوب',
            'analysis_type.in' => 'نوع التحليل يجب أن يكون من القيم المسموح بها',
            'analysis_radius.numeric' => 'نصف القطر يجب أن يكون رقماً',
            'analysis_radius.min' => 'نصف القطر يجب أن يكون على الأقل 0.5 كم',
            'analysis_radius.max' => 'نصف القطر يجب أن لا يتجاوز 50 كم',
            'analysis_parameters.time_period.in' => 'الفترة الزمنية يجب أن تكون من القيم المسموح بها',
            'analysis_parameters.weight_factors.location.numeric' => 'وزن الموقع يجب أن يكون رقماً',
            'analysis_parameters.weight_factors.location.min' => 'وزن الموقع يجب أن يكون بين 0 و 1',
            'analysis_parameters.weight_factors.location.max' => 'وزن الموقع يجب أن يكون بين 0 و 1',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'property_id' => 'العقار',
            'analysis_type' => 'نوع التحليل',
            'analysis_radius' => 'نصف القطر',
            'analysis_parameters' => 'معلمات التحليل',
            'analysis_parameters.include_economic_factors' => 'تضمين العوامل الاقتصادية',
            'analysis_parameters.include_market_sentiment' => 'تضمين مشاعر السوق',
            'analysis_parameters.include_historical_data' => 'تضمين البيانات التاريخية',
            'analysis_parameters.time_period' => 'الفترة الزمنية',
            'analysis_parameters.weight_factors' => 'عوامل الترجيح',
            'analysis_parameters.weight_factors.location' => 'وزن الموقع',
            'analysis_parameters.weight_factors.market' => 'وزن السوق',
            'analysis_parameters.weight_factors.demographic' => 'وزن الديموغرافيا',
            'analysis_parameters.weight_factors.economic' => 'وزن الاقتصاد',
            'metadata' => 'البيانات الوصفية',
            'metadata.source' => 'المصدر',
            'metadata.version' => 'الإصدار',
            'metadata.notes' => 'ملاحظات',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'analysis_parameters' => $this->analysis_parameters ?? [],
            'metadata' => $this->metadata ?? [],
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate weight factors sum
            if ($this->has('analysis_parameters.weight_factors')) {
                $weightFactors = $this->input('analysis_parameters.weight_factors');
                $sum = 0;
                
                foreach ($weightFactors as $factor => $value) {
                    if (is_numeric($value)) {
                        $sum += $value;
                    }
                }
                
                if ($sum > 1) {
                    $validator->errors()->add('analysis_parameters.weight_factors', 'مجموع عوامل الترجيح يجب أن لا يتجاوز 1');
                }
            }
            
            // Validate analysis radius based on analysis type
            if ($this->analysis_type === 'demographic' && $this->analysis_radius < 1) {
                $validator->errors()->add('analysis_radius', 'نصف القطر للتحليل الديموغرافي يجب أن يكون على الأقل 1 كم');
            }
            
            if ($this->analysis_type === 'risk' && $this->analysis_radius > 25) {
                $validator->errors()->add('analysis_radius', 'نصف القطر لتحليل المخاطر يجب أن لا يتجاوز 25 كم');
            }
        });
    }
}
