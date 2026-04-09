<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestimonialRequest extends FormRequest
{
    public function authorize()
    {
        return \Illuminate\Support\Facades\Auth::check();
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:50|max:2000',
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'client_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'project_type' => 'nullable|string|in:residential,commercial,industrial,land,villa,apartment,office,warehouse,retail,other',
            'project_location' => 'nullable|string|max:255',
            'rating' => 'nullable|integer|min:1|max:5',
            'video_url' => 'nullable|url|max:500'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'حقل العنوان مطلوب',
            'title.max' => 'يجب ألا يزيد العنوان عن 255 حرفاً',
            'content.required' => 'حقل المحتوى مطلوب',
            'content.min' => 'يجب أن يحتوي المحتوى على الأقل 50 حرفاً',
            'content.max' => 'يجب ألا يزيد المحتوى عن 2000 حرف',
            'client_name.required' => 'حقل اسم العميل مطلوب',
            'client_name.max' => 'يجب ألا يزيد اسم العميل عن 255 حرفاً',
            'client_position.max' => 'يجب ألا تزيد منصب العميل عن 255 حرفاً',
            'client_company.max' => 'يجب ألا تزيد شركة العميل عن 255 حرفاً',
            'client_image.image' => 'يجب أن يكون الملف صورة',
            'client_image.mimes' => 'يجب أن تكون الصورة من نوع: jpeg, png, jpg, gif',
            'client_image.max' => 'يجب ألا يزيد حجم الصورة عن 2 ميجابايت',
            'project_type.in' => 'نوع المشروع غير صالح',
            'project_location.max' => 'يجب ألا يزيد موقع المشروع عن 255 حرفاً',
            'rating.integer' => 'التقييم يجب أن يكون رقماً',
            'rating.min' => 'أقل تقييم هو 1',
            'rating.max' => 'أعلى تقييم هو 5',
            'video_url.url' => 'رابط الفيديو غير صالح',
            'video_url.max' => 'يجب ألا يزيد رابط الفيديو عن 500 حرف'
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'العنوان',
            'content' => 'المحتوى',
            'client_name' => 'اسم العميل',
            'client_position' => 'منصب العميل',
            'client_company' => 'شركة العميل',
            'client_image' => 'صورة العميل',
            'project_type' => 'نوع المشروع',
            'project_location' => 'موقع المشروع',
            'rating' => 'التقييم',
            'video_url' => 'رابط الفيديو'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate video URL if provided
            if ($this->video_url) {
                if (!$this->isValidVideoUrl($this->video_url)) {
                    $validator->errors()->add('video_url', 'رابط الفيديو يجب أن يكون من YouTube أو Vimeo');
                }
            }
        });
    }

    private function isValidVideoUrl($url)
    {
        $patterns = [
            'youtube' => '/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)/',
            'vimeo' => '/^(https?:\/\/)?(www\.)?vimeo\.com\//'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }

        return false;
    }
}
