<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePromotedListingRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'promotion_type' => 'required|in:featured,premium,spotlight',
            'duration' => 'required|integer|min:1|max:365',
            'daily_budget' => 'required|numeric|min:1',
            'target_audience' => 'nullable|array',
            'promotion_text' => 'nullable|string|max:500',
            'highlight_features' => 'nullable|array',
            'highlight_features.*' => 'string|max:100',
            'call_to_action' => 'nullable|string|max:100',
            'priority_level' => 'required|integer|min:1|max:10'
        ];
    }

    public function messages()
    {
        return [
            'property_id.required' => 'حقل العقار مطلوب',
            'property_id.exists' => 'العقار المحدد غير موجود',
            'promotion_type.required' => 'حقل نوع الترويج مطلوب',
            'promotion_type.in' => 'نوع الترويج غير صالح',
            'duration.required' => 'حقل مدة الترويج مطلوب',
            'duration.min' => 'مدة الترويج يجب أن تكون على الأقل يوم واحد',
            'duration.max' => 'مدة الترويج يجب ألا تزيد عن 365 يوم',
            'daily_budget.required' => 'حقل الميزانية اليومية مطلوب',
            'daily_budget.min' => 'الميزانية اليومية يجب أن تكون على الأقل 1 ريال',
            'promotion_text.max' => 'نص الترويج يجب ألا يزيد عن 500 حرف',
            'highlight_features.*.max' => 'كل ميزة مميزة يجب ألا تزيد عن 100 حرف',
            'call_to_action.max' => 'دعوة العمل يجب ألا تزيد عن 100 حرف',
            'priority_level.required' => 'حقل مستوى الأولوية مطلوب',
            'priority_level.min' => 'مستوى الأولوية يجب أن يكون بين 1 و 10',
            'priority_level.max' => 'مستوى الأولوية يجب أن يكون بين 1 و 10'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if property belongs to authenticated user
            $property = \App\Models\Property::find($this->property_id);
            if ($property && $property->user_id !== Auth::id()) {
                $validator->errors()->add('property_id', 'لا يمكنك ترويج عقار لا يملكه');
            }

            // Check if property is already promoted
            if ($property && $property->promotedListing) {
                $validator->errors()->add('property_id', 'العقار تم ترويجه بالفعل');
            }

            // Validate total budget
            $totalBudget = $this->daily_budget * $this->duration;
            if ($totalBudget > 100000) {
                $validator->errors()->add('daily_budget', 'الميزانية الإجمالية لا يمكن أن تتجاوز 100,000 ريال');
            }
        });
    }
}
