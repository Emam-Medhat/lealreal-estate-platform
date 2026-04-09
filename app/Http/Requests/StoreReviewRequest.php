<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReviewRequest extends FormRequest
{
    public function authorize()
    {
        return \Illuminate\Support\Facades\Auth::check();
    }

    public function rules()
    {
        return [
            'reviewable_type' => 'required|string|in:App\Models\Property,App\Models\Agent',
            'reviewable_id' => 'required|integer|exists:reviewable_type,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10|max:2000',
            'rating' => 'required|integer|min:1|max:5',
            'pros' => 'nullable|string|max:1000',
            'cons' => 'nullable|string|max:1000',
            'recommendation' => 'nullable|boolean',
            'is_anonymous' => 'nullable|boolean'
        ];
    }

    public function messages()
    {
        return [
            'reviewable_type.required' => 'يجب تحديد نوع العنصر المراد تقييمه',
            'reviewable_type.in' => 'نوع العنصر غير صالح',
            'reviewable_id.required' => 'يجب تحديد معرف العنصر',
            'reviewable_id.exists' => 'العنصر المحدد غير موجود',
            'title.required' => 'حقل العنوان مطلوب',
            'title.max' => 'يجب ألا يزيد العنوان عن 255 حرفاً',
            'content.required' => 'حقل المحتوى مطلوب',
            'content.min' => 'يجب أن يحتوي المحتوى على الأقل 10 أحرف',
            'content.max' => 'يجب ألا يزيد المحتوى عن 2000 حرف',
            'rating.required' => 'حقل التقييم مطلوب',
            'rating.integer' => 'التقييم يجب أن يكون رقماً',
            'rating.min' => 'أقل تقييم هو 1',
            'rating.max' => 'أعلى تقييم هو 5',
            'pros.max' => 'يجب ألا تزيد الإيجابيات عن 1000 حرف',
            'cons.max' => 'يجب ألا تزيد السلبيات عن 1000 حرف',
            'recommendation.boolean' => 'حقل التوصية يجب أن يكون نعم أو لا',
            'is_anonymous.boolean' => 'حقل الإخفاء يجب أن يكون نعم أو لا'
        ];
    }

    public function attributes()
    {
        return [
            'reviewable_type' => 'نوع العنصر',
            'reviewable_id' => 'معرف العنصر',
            'title' => 'العنوان',
            'content' => 'المحتوى',
            'rating' => 'التقييم',
            'pros' => 'الإيجابيات',
            'cons' => 'السلبيات',
            'recommendation' => 'التوصية',
            'is_anonymous' => 'الإخفاء'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_anonymous' => $this->has('is_anonymous') ? true : false,
            'recommendation' => $this->has('recommendation') ? true : null,
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if user already reviewed this item
            if (\Illuminate\Support\Facades\Auth::check()) {
                $existingReview = \App\Models\Review::where('user_id', \Illuminate\Support\Facades\Auth::id())
                    ->where('reviewable_type', $this->reviewable_type)
                    ->where('reviewable_id', $this->reviewable_id)
                    ->first();

                if ($existingReview) {
                    $validator->errors()->add('reviewable_id', 'لقد قمت بتقييم هذا العنصر بالفعل');
                }
            }

            // Validate reviewable exists and is reviewable
            $reviewableClass = $this->reviewable_type;
            if (class_exists($reviewableClass)) {
                $reviewable = $reviewableClass::find($this->reviewable_id);
                if (!$reviewable) {
                    $validator->errors()->add('reviewable_id', 'العنصر المحدد غير موجود');
                }
            }
        });
    }
}
