<?php

namespace App\Http\Requests\Neighborhood;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateNeighborhoodReviewRequest extends FormRequest
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
            'neighborhood_id' => ['required', 'exists:neighborhoods,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:50', 'max:5000'],
            'rating' => ['required', 'numeric', 'between:1,5'],
            'status' => ['required', 'string', 'in:draft,published,hidden,reported'],
            'reviewer_name' => ['required', 'string', 'max:255'],
            'reviewer_email' => ['nullable', 'email', 'max:255'],
            'reviewer_phone' => ['nullable', 'string', 'max:50'],
            'reviewer_type' => ['required', 'string', 'in:resident,owner,visitor,professional'],
            'pros' => ['nullable', 'array'],
            'pros.*' => ['nullable', 'string', 'max:255'],
            'cons' => ['nullable', 'array'],
            'cons.*' => ['nullable', 'string', 'max:255'],
            'recommendation' => ['nullable', 'string', 'in:yes,no,maybe'],
            'experience_period' => ['nullable', 'string', 'in:less_than_6_months,6_months_to_1_year,1_to_3_years,3_to_5_years,more_than_5_years'],
            'property_type' => ['nullable', 'string', 'in:apartment,villa,townhouse,duplex,studio,penthouse,land,commercial,office,other'],
            'property_details' => ['nullable', 'array'],
            'property_details.size' => ['nullable', 'numeric', 'min:0'],
            'property_details.bedrooms' => ['nullable', 'integer', 'min:0'],
            'property_details.bathrooms' => ['nullable', 'integer', 'min:0'],
            'property_details.year_built' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'community_aspects' => ['nullable', 'array'],
            'community_aspects.safety' => ['nullable', 'integer', 'between:1,5'],
            'community_aspects.cleanliness' => ['nullable', 'integer', 'between:1,5'],
            'community_aspects.noise_level' => ['nullable', 'integer', 'between:1,5'],
            'community_aspects.parking' => ['nullable', 'integer', 'between:1,5'],
            'community_aspects.green_spaces' => ['nullable', 'integer', 'between:1,5'],
            'improvement_suggestions' => ['nullable', 'array'],
            'improvement_suggestions.*' => ['nullable', 'string', 'max:500'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'max:5120'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['nullable', 'image', 'max:5120'],
            'videos' => ['nullable', 'array'],
            'videos.*' => ['nullable', 'file', 'mimes:mp4,avi,mov,wmv', 'max:51200'],
            'verified' => ['nullable', 'boolean'],
            'featured' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['nullable', 'string', 'max:50'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'neighborhood_id.required' => 'الحي مطلوب',
            'neighborhood_id.exists' => 'الحي المحدد غير موجود',
            'title.required' => 'عنوان التقييم مطلوب',
            'title.max' => 'يجب ألا يتجاوز عنوان التقييم 255 حرفًا',
            'content.required' => 'محتوى التقييم مطلوب',
            'content.min' => 'يجب أن يحتوي محتوى التقييم على 50 حرف على الأقل',
            'content.max' => 'يجب ألا يتجاوز محتوى التقييم 5000 حرف',
            'rating.required' => 'التقييم مطلوب',
            'rating.numeric' => 'التقييم يجب أن يكون رقمًا',
            'rating.between' => 'التقييم يجب أن يكون بين 1 و 5',
            'status.required' => 'حالة التقييم مطلوبة',
            'status.in' => 'حالة التقييم غير صالحة',
            'reviewer_name.required' => 'اسم المقيم مطلوب',
            'reviewer_name.max' => 'يجب ألا يتجاوز اسم المقيم 255 حرفًا',
            'reviewer_email.email' => 'البريد الإلكتروني غير صالح',
            'reviewer_email.max' => 'يجب ألا يتجاوز البريد الإلكتروني 255 حرفًا',
            'reviewer_phone.max' => 'يجب ألا يتجاوز رقم الهاتف 50 حرفًا',
            'reviewer_type.required' => 'نوع المقيم مطلوب',
            'reviewer_type.in' => 'نوع المقيم غير صالح',
            'pros.*.max' => 'يجب ألا يتجاوز الميزة 255 حرفًا',
            'cons.*.max' => 'يجب ألا يتجاوز العيب 255 حرفًا',
            'recommendation.in' => 'التوصية غير صالحة',
            'experience_period.in' => 'فترة الخبرة غير صالحة',
            'property_type.in' => 'نوع العقار غير صالح',
            'property_details.size.numeric' => 'حجم العقار يجب أن يكون رقمًا',
            'property_details.size.min' => 'حجم العقار يجب أن يكون 0 أو أكثر',
            'property_details.bedrooms.integer' => 'عدد غرف النوم يجب أن يكون رقمًا صحيحًا',
            'property_details.bedrooms.min' => 'عدد غرف النوم يجب أن يكون 0 أو أكثر',
            'property_details.bathrooms.integer' => 'عدد الحمامات يجب أن يكون رقمًا صحيحًا',
            'property_details.bathrooms.min' => 'عدد الحمامات يجب أن يكون 0 أو أكثر',
            'property_details.year_built.integer' => 'سنة البناء يجب أن تكون رقمًا صحيحًا',
            'property_details.year_built.min' => 'سنة البناء يجب أن تكون 1900 أو أكثر',
            'property_details.year_built.max' => 'سنة البناء يجب أن تكون السنة الحالية أو أقل',
            'community_aspects.safety.between' => 'تقييم السلامة يجب أن يكون بين 1 و 5',
            'community_aspects.cleanliness.between' => 'تقييم النظافة يجب أن يكون بين 1 و 5',
            'community_aspects.noise_level.between' => 'تقييم مستوى الضوض يجب أن يكون بين 1 و 5',
            'community_aspects.parking.between' => 'تقييم وقوف السيارات يجب أن يكون بين 1 و 5',
            'community_aspects.green_spaces.between' => 'تقييم المساحات الخضراء يجب أن يكون بين 1 و 5',
            'improvement_suggestions.*.max' => 'يجب ألا يتجاوز الاقتراح 500 حرف',
            'images.*.image' => 'يجب أن يكون الملف صورة',
            'images.*.max' => 'يجب ألا يتجاوز حجم الصورة 5 ميجابايت',
            'photos.*.image' => 'يجب أن يكون الملف صورة',
            'photos.*.max' => 'يجب ألا يتجاوز حجم الصورة 5 ميجابايت',
            'videos.*.file' => 'يجب أن يكون الملف فيديو',
            'videos.*.mimes' => 'يجب أن يكون الفيديو بصيغة mp4, avi, mov, أو wmv',
            'videos.*.max' => 'يجب ألا يتجاوز حجم الفيديو 50 ميجابايت',
            'tags.*.max' => 'يجب ألا يتجاوز العلامة 50 حرفًا',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'neighborhood_id' => 'الحي',
            'title' => 'عنوان التقييم',
            'content' => 'محتوى التقييم',
            'rating' => 'التقييم',
            'status' => 'حالة التقييم',
            'reviewer_name' => 'اسم المقيم',
            'reviewer_email' => 'بريد المقيم الإلكتروني',
            'reviewer_phone' => 'هاتف المقيم',
            'reviewer_type' => 'نوع المقيم',
            'pros' => 'المميزات',
            'cons' => 'العيوب',
            'recommendation' => 'التوصية',
            'experience_period' => 'فترة الخبرة',
            'property_type' => 'نوع العقار',
            'property_details' => 'تفاصيل العقار',
            'property_details.size' => 'حجم العقار',
            'property_details.bedrooms' => 'عدد غرف النوم',
            'property_details.bathrooms' => 'عدد الحمامات',
            'property_details.year_built' => 'سنة البناء',
            'community_aspects' => 'جوانب المجتمع',
            'community_aspects.safety' => 'السلامة',
            'community_aspects.cleanliness' => 'النظافة',
            'community_aspects.noise_level' => 'مستوى الضوض',
            'community_aspects.parking' => 'وقوف السيارات',
            'community_aspects.green_spaces' => 'المساحات الخضراء',
            'improvement_suggestions' => 'اقتراحات التحسين',
            'images' => 'الصور',
            'photos' => 'الصور',
            'videos' => 'الفيديوهات',
            'verified' => 'موثق',
            'featured' => 'مميز',
            'tags' => 'العلامات',
            'metadata' => 'البيانات الوصفية',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate pros and cons
            if ($this->has('pros') && is_array($this->pros)) {
                if (count($this->pros) > 10) {
                    $validator->errors()->add('pros', 'يجب ألا يتجاوز عدد المميزات 10');
                }
            }
            
            if ($this->has('cons') && is_array($this->cons)) {
                if (count($this->cons) > 10) {
                    $validator->errors()->add('cons', 'يجب ألا يتجاوز عدد العيوب 10');
                }
            }
            
            // Validate community aspects
            if ($this->has('community_aspects') && is_array($this->community_aspects)) {
                $aspects = ['safety', 'cleanliness', 'noise_level', 'parking', 'green_spaces'];
                foreach ($aspects as $aspect) {
                    if (isset($this->community_aspects[$aspect])) {
                        $value = $this->community_aspects[$aspect];
                        if (!is_int($value) || $value < 1 || $value > 5) {
                            $validator->errors()->add("community_aspects.{$aspect}", "تقييم {$this->attributes()["community_aspects.{$aspect}"]} يجب أن يكون رقمًا بين 1 و 5");
                        }
                    }
                }
            }
            
            // Validate property details
            if ($this->has('property_details') && is_array($this->property_details)) {
                $details = $this->property_details;
                
                // Validate bedroom and bathroom relationship
                if (isset($details['bedrooms']) && isset($details['bathrooms'])) {
                    $bedrooms = $details['bedrooms'];
                    $bathrooms = $details['bathrooms'];
                    
                    if ($bedrooms > 0 && $bathrooms > 0 && $bathrooms > $bedrooms + 2) {
                        $validator->errors()->add('property_details.bathrooms', 'عدد الحمامات يجب أن يكون معقولًا مقارنة بعدد غرف النوم');
                    }
                }
                
                // Validate size vs bedrooms
                if (isset($details['size']) && isset($details['bedrooms'])) {
                    $size = $details['size'];
                    $bedrooms = $details['bedrooms'];
                    
                    if ($bedrooms > 0 && $size > 0 && $size < ($bedrooms * 20)) {
                        $validator->errors()->add('property_details.size', 'حجم العقار صغير جدًا مقارنة بعدد غرف النوم');
                    }
                }
            }
            
            // Validate media files
            if ($this->has('images') && is_array($this->images)) {
                if (count($this->images) > 10) {
                    $validator->errors()->add('images', 'يجب ألا يتجاوز عدد الصور 10');
                }
            }
            
            if ($this->has('photos') && is_array($this->photos)) {
                if (count($this->photos) > 10) {
                    $validator->errors()->add('photos', 'يجب ألا يتجاوز عدد الصور 10');
                }
            }
            
            if ($this->has('videos') && is_array($this->videos)) {
                if (count($this->videos) > 5) {
                    $validator->errors()->add('videos', 'يجب ألا يتجاوز عدد الفيديوهات 5');
                }
            }
            
            // Validate tags
            if ($this->has('tags') && is_array($this->tags)) {
                if (count($this->tags) > 15) {
                    $validator->errors()->add('tags', 'يجب ألا يتجاوز عدد العلامات 15');
                }
                
                foreach ($this->tags as $index => $tag) {
                    if (strlen($tag) > 50) {
                        $validator->errors()->add("tags.{$index}", 'يجب ألا يتجاوز طول العلامة 50 حرفًا');
                    }
                }
            }
            
            // Validate rating consistency with recommendation
            if ($this->has('rating') && $this->has('recommendation')) {
                $rating = $this->rating;
                $recommendation = $this->recommendation;
                
                if ($rating >= 4 && $recommendation === 'no') {
                    $validator->errors()->add('recommendation', 'التوصية لا تتوافق مع التقييم العالي');
                }
                
                if ($rating <= 2 && $recommendation === 'yes') {
                    $validator->errors()->add('recommendation', 'التوصية لا تتوافق مع التقييم المنخفض');
                }
            }
            
            // Validate content length vs rating
            if ($this->has('content') && $this->has('rating')) {
                $content = $this->content;
                $rating = $this->rating;
                
                if (strlen($content) < 100 && $rating >= 4) {
                    $validator->errors()->add('content', 'التقييمات العالية تتطلب محتوى أكثر تفصيلاً');
                }
            }
        });
    }
}
