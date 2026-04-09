<?php

namespace App\Http\Requests\Neighborhood;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateNeighborhoodRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'property_type' => ['required', 'string', 'in:residential,commercial,mixed,professional,educational,religious,cultural,sports,recreational'],
            'status' => ['required', 'string', 'in:active,inactive,pending,suspended'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'boundaries' => ['nullable', 'array'],
            'boundaries.coordinates' => ['nullable', 'array'],
            'boundaries.bounds' => ['nullable', 'array'],
            'features' => ['nullable', 'array'],
            'amenities' => ['nullable', 'array'],
            'transportation' => ['nullable', 'array'],
            'schools' => ['nullable', 'array'],
            'healthcare' => ['nullable', 'array'],
            'shopping' => ['nullable', 'array'],
            'recreation' => ['nullable', 'array'],
            'safety_rating' => ['nullable', 'numeric', 'between:0,5'],
            'walkability_score' => ['nullable', 'numeric', 'between:0,100'],
            'transit_score' => ['nullable', 'numeric', 'between:0,100'],
            'green_space_ratio' => ['nullable', 'numeric', 'between:0,1'],
            'average_price' => ['nullable', 'numeric', 'min:0'],
            'price_range' => ['nullable', 'array'],
            'price_range.min' => ['nullable', 'numeric', 'min:0'],
            'price_range.max' => ['nullable', 'numeric', 'min:0'],
            'property_count' => ['nullable', 'integer', 'min:0'],
            'resident_count' => ['nullable', 'integer', 'min:0'],
            'population_density' => ['nullable', 'numeric', 'min:0'],
            'development_status' => ['nullable', 'string', 'max:50'],
            'infrastructure_quality' => ['nullable', 'string', 'max:50'],
            'community_engagement' => ['nullable', 'string', 'max:50'],
            'metadata' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'max:5120'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم الحقل مطلوب',
            'name.max' => 'يجب ألا يتجاوز اسم الحي 255 حرفًا',
            'city.required' => 'المدينة مطلوبة',
            'city.max' => 'يجب ألا يتجاوز اسم المدينة 100 حرف',
            'district.required' => 'الحي مطلوب',
            'district.max' => 'يجب ألا يتجاوز اسم الحي 100 حرف',
            'description.max' => 'يجب ألا يتجاوز الوصف 2000 حرف',
            'property_type.required' => 'نوع العقار مطلوب',
            'property_type.in' => 'نوع العقار غير صالح',
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة غير صالحة',
            'latitude.numeric' => 'خط العرض يجب أن يكون رقمًا',
            'latitude.between' => 'خط العرض يجب أن يكون بين -90 و 90',
            'longitude.numeric' => 'خط الطول يجب أن يكون رقمًا',
            'longitude.between' => 'خط الطول يجب أن يكون بين -180 و 180',
            'safety_rating.numeric' => 'تقييم السلامة يجب أن يكون رقمًا',
            'safety_rating.between' => 'تقييم السلامة يجب أن يكون بين 0 و 5',
            'walkability_score.numeric' => 'درجة المشي يجب أن تكون رقمًا',
            'walkability_score.between' => 'درجة المشي يجب أن تكون بين 0 و 100',
            'transit_score.numeric' => 'درجة المواصلات يجب أن تكون رقمًا',
            'transit_score.between' => 'درجة المواصلات يجب أن تكون بين 0 و 100',
            'green_space_ratio.numeric' => 'نسبة المساحات الخضراء يجب أن تكون رقمًا',
            'green_space_ratio.between' => 'نسبة المساحات الخضراء يجب أن تكون بين 0 و 1',
            'average_price.numeric' => 'متوسط السعر يجب أن يكون رقمًا',
            'average_price.min' => 'متوسط السعر يجب أن يكون 0 أو أكثر',
            'price_range.min.numeric' => 'الحد الأدنى للسعر يجب أن يكون رقمًا',
            'price_range.min.min' => 'الحد الأدنى للسعر يجب أن يكون 0 أو أكثر',
            'price_range.max.numeric' => 'الحد الأقصى للسعر يجب أن يكون رقمًا',
            'price_range.max.min' => 'الحد الأقصى للسعر يجب أن يكون 0 أو أكثر',
            'property_count.integer' => 'عدد العقارات يجب أن يكون رقمًا صحيحًا',
            'property_count.min' => 'عدد العقارات يجب أن يكون 0 أو أكثر',
            'resident_count.integer' => 'عدد السكان يجب أن يكون رقمًا صحيحًا',
            'resident_count.min' => 'عدد السكان يجب أن يكون 0 أو أكثر',
            'population_density.numeric' => 'كثافة السكان يجب أن تكون رقمًا',
            'population_density.min' => 'كثافة السكان يجب أن تكون 0 أو أكثر',
            'development_status.max' => 'يجب ألا يتجاوز حالة التطوير 50 حرفًا',
            'infrastructure_quality.max' => 'يجب ألا يتجاوز جودة البنية التحتية 50 حرفًا',
            'community_engagement.max' => 'يجب ألا يتجاوز مشاركة المجتمع 50 حرفًا',
            'images.*.image' => 'يجب أن يكون الملف صورة',
            'images.*.max' => 'يجب ألا يتجاوز حجم الصورة 5 ميجابايت',
            'cover_image.image' => 'يجب أن يكون الملف صورة',
            'cover_image.max' => 'يجب ألا يتجاوز حجم الصورة 5 ميجابايت',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'اسم الحي',
            'city' => 'المدينة',
            'district' => 'الحي',
            'description' => 'الوصف',
            'property_type' => 'نوع العقار',
            'status' => 'الحالة',
            'latitude' => 'خط العرض',
            'longitude' => 'خط الطول',
            'boundaries' => 'الحدود',
            'features' => 'المميزات',
            'amenities' => 'المرافق',
            'transportation' => 'المواصلات',
            'schools' => 'المدارس',
            'healthcare' => 'الرعاية الصحية',
            'shopping' => 'التسوق',
            'recreation' => 'الترفيه',
            'safety_rating' => 'تقييم السلامة',
            'walkability_score' => 'درجة المشي',
            'transit_score' => 'درجة المواصلات',
            'green_space_ratio' => 'نسبة المساحات الخضراء',
            'average_price' => 'متوسط السعر',
            'price_range' => 'نطاق السعر',
            'price_range.min' => 'الحد الأدنى للسعر',
            'price_range.max' => 'الحد الأقصى للسعر',
            'property_count' => 'عدد العقارات',
            'resident_count' => 'عدد السكان',
            'population_density' => 'كثافة السكان',
            'development_status' => 'حالة التطوير',
            'infrastructure_quality' => 'جودة البنية التحتية',
            'community_engagement' => 'مشاركة المجتمع',
            'metadata' => 'البيانات الوصفية',
            'images' => 'الصور',
            'cover_image' => 'الصورة الرئيسية',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate price range
            if ($this->has('price_range') && is_array($this->price_range)) {
                $min = $this->price_range['min'] ?? null;
                $max = $this->price_range['max'] ?? null;
                
                if ($min !== null && $max !== null && $min >= $max) {
                    $validator->errors()->add('price_range.max', 'الحد الأقصى للسعر يجب أن يكون أكبر من الحد الأدنى');
                }
            }
            
            // Validate coordinates
            if ($this->has('latitude') && $this->has('longitude')) {
                if ($this->latitude && !$this->longitude) {
                    $validator->errors()->add('longitude', 'يجب إدخال خط الطول عند إدخال خط العرض');
                }
                if ($this->longitude && !$this->latitude) {
                    $validator->errors()->add('latitude', 'يجب إدخال خط العرض عند إدخال خط الطول');
                }
            }
            
            // Validate boundaries
            if ($this->has('boundaries') && is_array($this->boundaries)) {
                if (isset($this->boundaries['coordinates']) && !is_array($this->boundaries['coordinates'])) {
                    $validator->errors()->add('boundaries.coordinates', 'إحداثيات الحدود يجب أن تكون مصفوفة');
                }
                
                if (isset($this->boundaries['bounds']) && is_array($this->boundaries['bounds'])) {
                    $bounds = $this->boundaries['bounds'];
                    
                    if (isset($bounds['north']) && isset($bounds['south']) && $bounds['north'] <= $bounds['south']) {
                        $validator->errors()->add('boundaries.bounds.north', 'الحد الشمالي يجب أن يكون أكبر من الحد الجنوبي');
                    }
                    
                    if (isset($bounds['east']) && isset($bounds['west']) && $bounds['east'] <= $bounds['west']) {
                        $validator->errors()->add('boundaries.bounds.east', 'الحد الشرقي يجب أن يكون أكبر من الحد الغربي');
                    }
                }
            }
            
            // Validate scores
            if ($this->has('walkability_score') && $this->has('transit_score')) {
                if ($this->walkability_score < 0 || $this->walkability_score > 100) {
                    $validator->errors()->add('walkability_score', 'درجة المشي يجب أن تكون بين 0 و 100');
                }
                if ($this->transit_score < 0 || $this->transit_score > 100) {
                    $validator->errors()->add('transit_score', 'درجة المواصلات يجب أن تكون بين 0 و 100');
                }
            }
        });
    }
}
