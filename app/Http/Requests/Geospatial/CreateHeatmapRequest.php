<?php

namespace App\Http\Requests\Geospatial;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateHeatmapRequest extends FormRequest
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
            'heatmap_type' => 'required|string|in:price_density,price_appreciation,investment_hotspot,risk_assessment,market_activity,demand_supply,accessibility,development_potential',
            'bounds' => 'required|array',
            'bounds.north' => 'required|numeric|between:-90,90',
            'bounds.south' => 'required|numeric|between:-90,90',
            'bounds.east' => 'required|numeric|between:-180,180',
            'bounds.west' => 'required|numeric|between:-180,180',
            'zoom_level' => 'required|integer|min:1|max:20',
            'grid_size' => 'required|integer|min:10|max:100',
            'color_scheme' => 'required|string|in:viridis,plasma,inferno,magma,cool,warm,hot,rainbow',
            'analysis_parameters' => 'nullable|array',
            'analysis_parameters.data_source' => 'nullable|string|max:255',
            'analysis_parameters.interpolation_method' => 'nullable|string|in:linear,cubic,nearest',
            'analysis_parameters.smoothing_factor' => 'nullable|numeric|min:0|max:1',
            'analysis_parameters.intensity_range' => 'nullable|array',
            'analysis_parameters.intensity_range.min' => 'nullable|numeric',
            'analysis_parameters.intensity_range.max' => 'nullable|numeric',
            'metadata' => 'nullable|array',
            'metadata.source' => 'nullable|string|max:255',
            'metadata.version' => 'nullable|string|max:50',
            'metadata.description' => 'nullable|string|max:1000',
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
            'heatmap_type.required' => 'حقل نوع الخريطة الحرارية مطلوب',
            'heatmap_type.in' => 'نوع الخريطة الحرارية يجب أن يكون من القيم المسموح بها',
            'bounds.required' => 'حقل الحدود مطلوب',
            'bounds.north.required' => 'حقل الحد الشمالي مطلوب',
            'bounds.north.numeric' => 'الحد الشمالي يجب أن يكون رقماً',
            'bounds.north.between' => 'الحد الشمالي يجب أن يكون بين -90 و 90',
            'bounds.south.required' => 'حقل الحد الجنوبي مطلوب',
            'bounds.south.numeric' => 'الحد الجنوبي يجب أن يكون رقماً',
            'bounds.south.between' => 'الحد الجنوبي يجب أن يكون بين -90 و 90',
            'bounds.east.required' => 'حقل الحد الشرقي مطلوب',
            'bounds.east.numeric' => 'الحد الشرقي يجب أن يكون رقماً',
            'bounds.east.between' => 'الحد الشرقي يجب أن يكون بين -180 و 180',
            'bounds.west.required' => 'حقل الحد الغربي مطلوب',
            'bounds.west.numeric' => 'الحد الغربي يجب أن يكون رقماً',
            'bounds.west.between' => 'الحد الغربي يجب أن يكون بين -180 و 180',
            'zoom_level.required' => 'حقل مستوى التكبير مطلوب',
            'zoom_level.integer' => 'مستوى التكبير يجب أن يكون عدداً صحيحاً',
            'zoom_level.min' => 'مستوى التكبير يجب أن يكون على الأقل 1',
            'zoom_level.max' => 'مستوى التكبير يجب أن لا يتجاوز 20',
            'grid_size.required' => 'حقل حجم الشبكة مطلوب',
            'grid_size.integer' => 'حجم الشبكة يجب أن يكون عدداً صحيحاً',
            'grid_size.min' => 'حجم الشبكة يجب أن يكون على الأقل 10',
            'grid_size.max' => 'حجم الشبكة يجب أن لا يتجاوز 100',
            'color_scheme.required' => 'حقل مخطط الألوان مطلوب',
            'color_scheme.in' => 'مخطط الألوان يجب أن يكون من القيم المسموح بها',
            'analysis_parameters.interpolation_method.in' => 'طريقة الاستيفاء يجب أن تكون من القيم المسموح بها',
            'analysis_parameters.smoothing_factor.numeric' => 'عامل التنعيم يجب أن يكون رقماً',
            'analysis_parameters.smoothing_factor.min' => 'عامل التنعيم يجب أن يكون بين 0 و 1',
            'analysis_parameters.smoothing_factor.max' => 'عامل التنعيم يجب أن يكون بين 0 و 1',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'property_id' => 'العقار',
            'heatmap_type' => 'نوع الخريطة الحرارية',
            'bounds' => 'الحدود',
            'bounds.north' => 'الحد الشمالي',
            'bounds.south' => 'الحد الجنوبي',
            'bounds.east' => 'الحد الشرقي',
            'bounds.west' => 'الحد الغربي',
            'zoom_level' => 'مستوى التكبير',
            'grid_size' => 'حجم الشبكة',
            'color_scheme' => 'مخطط الألوان',
            'analysis_parameters' => 'معلمات التحليل',
            'analysis_parameters.data_source' => 'مصدر البيانات',
            'analysis_parameters.interpolation_method' => 'طريقة الاستيفاء',
            'analysis_parameters.smoothing_factor' => 'عامل التنعيم',
            'analysis_parameters.intensity_range' => 'نطاق الشدة',
            'analysis_parameters.intensity_range.min' => 'الحد الأدنى للشدة',
            'analysis_parameters.intensity_range.max' => 'الحد الأقصى للشدة',
            'metadata' => 'البيانات الوصفية',
            'metadata.source' => 'المصدر',
            'metadata.version' => 'الإصدار',
            'metadata.description' => 'الوصف',
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
            // Validate bounds
            if ($this->has('bounds')) {
                $bounds = $this->input('bounds');
                
                if ($bounds['north'] <= $bounds['south']) {
                    $validator->errors()->add('bounds.north', 'الحد الشمالي يجب أن يكون أكبر من الحد الجنوبي');
                }
                
                if ($bounds['east'] <= $bounds['west']) {
                    $validator->errors()->add('bounds.east', 'الحد الشرقي يجب أن يكون أكبر من الحد الغربي');
                }
            }
            
            // Validate intensity range
            if ($this->has('analysis_parameters.intensity_range')) {
                $range = $this->input('analysis_parameters.intensity_range');
                
                if (isset($range['min']) && isset($range['max']) && $range['min'] >= $range['max']) {
                    $validator->errors()->add('analysis_parameters.intensity_range.min', 'الحد الأدنى للشدة يجب أن يكون أصغر من الحد الأقصى');
                }
            }
            
            // Validate grid size based on zoom level
            if ($this->zoom_level < 10 && $this->grid_size > 50) {
                $validator->errors()->add('grid_size', 'حجم الشبكة يجب أن يكون 50 أو أقل عند مستوى التكبير المنخفض');
            }
            
            if ($this->zoom_level > 15 && $this->grid_size < 30) {
                $validator->errors()->add('grid_size', 'حجم الشبكة يجب أن يكون 30 أو أكثر عند مستوى التكبير العالي');
            }
        });
    }
}
