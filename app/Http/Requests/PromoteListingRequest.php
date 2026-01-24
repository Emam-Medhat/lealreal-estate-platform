<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PromoteListingRequest extends FormRequest
{
    public function authorize()
    {
        $property = $this->route('property');
        return Auth::check() && Auth::id() === $property->user_id;
    }

    public function rules()
    {
        return [
            'promotion_type' => 'required|in:featured,premium,spotlight',
            'duration' => 'required|integer|min:1|max:365',
            'daily_budget' => 'required|numeric|min:1',
            'promotion_text' => 'nullable|string|max:500',
            'highlight_features' => 'nullable|array',
            'highlight_features.*' => 'string|max:100',
            'call_to_action' => 'nullable|string|max:100',
            'priority_level' => 'required|integer|min:1|max:10',
            'target_audience' => 'nullable|array',
            'target_audience.age_range' => 'nullable|array',
            'target_audience.age_range.min' => 'nullable|integer|min:18',
            'target_audience.age_range.max' => 'nullable|integer|max:100',
            'target_audience.locations' => 'nullable|array',
            'target_audience.locations.*' => 'string|max:100',
            'target_audience.interests' => 'nullable|array',
            'target_audience.interests.*' => 'string|max:50',
            'auto_renew' => 'nullable|boolean',
            'start_immediately' => 'nullable|boolean',
            'custom_settings' => 'nullable|array'
        ];
    }

    public function messages()
    {
        return [
            'promotion_type.required' => 'حقل نوع الترويج مطلوب',
            'promotion_type.in' => 'نوع الترويج غير صالح',
            'duration.required' => 'حقل مدة الترويج مطلوب',
            'duration.min' => 'مدة الترويج يجب أن تكون على الأقل يوم واحد',
            'duration.max' => 'مدة الترويج يجب ألا تزيد عن 365 يوم',
            'daily_budget.required' => 'حقل الميزانية اليومية مطلوب',
            'daily_budget.min' => 'الميزانية اليومية يجب أن تكون على الأقل 1 ريال',
            'promotion_text.max' => 'نص الترويج يجب ألا يزيد عن 500 حرف',
            'highlight_features.array' => 'الميزات المميزة يجب أن تكون مصفوفة',
            'highlight_features.*.max' => 'كل ميزة مميزة يجب ألا تزيد عن 100 حرف',
            'call_to_action.max' => 'دعوة العمل يجب ألا تزيد عن 100 حرف',
            'priority_level.required' => 'حقل مستوى الأولوية مطلوب',
            'priority_level.min' => 'مستوى الأولوية يجب أن يكون بين 1 و 10',
            'priority_level.max' => 'مستوى الأولوية يجب أن يكون بين 1 و 10',
            'target_audience.array' => 'الجمهور المستهدف يجب أن يكون مصفوفة',
            'target_audience.age_range.min' => 'الحد الأدنى للعمر يجب أن يكون 18 سنة',
            'target_audience.age_range.max' => 'الحد الأقصى للعمر يجب أن يكون 100 سنة',
            'target_audience.locations.*.max' => 'كل موقع يجب ألا يزيد عن 100 حرف',
            'target_audience.interests.*.max' => 'كل اهتمام يجب ألا يزيد عن 50 حرف'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $property = $this->route('property');
            
            // Check if property is already promoted
            if ($property && $property->promotedListing) {
                $validator->errors()->add('property_id', 'العقار تم ترويجه بالفعل');
            }
            
            // Validate total budget
            $dailyBudget = $this->input('daily_budget');
            $duration = $this->input('duration');
            $totalBudget = $dailyBudget * $duration;
            
            if ($totalBudget > 50000) {
                $validator->errors()->add('daily_budget', 'الميزانية الإجمالية لا يمكن أن تتجاوز 50,000 ريال');
            }
            
            // Validate age range
            $ageRange = $this->input('target_audience.age_range');
            if ($ageRange && isset($ageRange['min']) && isset($ageRange['max'])) {
                if ($ageRange['min'] >= $ageRange['max']) {
                    $validator->errors()->add('target_audience.age_range', 'الحد الأدنى للعمر يجب أن يكون أقل من الحد الأقصى');
                }
            }
            
            // Validate promotion type specific requirements
            $promotionType = $this->input('promotion_type');
            $dailyBudget = $this->input('daily_budget');
            
            switch ($promotionType) {
                case 'featured':
                    if ($dailyBudget < 10) {
                        $validator->errors()->add('daily_budget', 'الميزانية اليومية للترويج المميز يجب أن تكون على الأقل 10 ريال');
                    }
                    break;
                    
                case 'premium':
                    if ($dailyBudget < 25) {
                        $validator->errors()->add('daily_budget', 'الميزانية اليومية للترويج المميز يجب أن تكون على الأقل 25 ريال');
                    }
                    if (!$this->input('promotion_text')) {
                        $validator->errors()->add('promotion_text', 'نص الترويج مطلوب للترويج المميز');
                    }
                    break;
                    
                case 'spotlight':
                    if ($dailyBudget < 50) {
                        $validator->errors()->add('daily_budget', 'الميزانية اليومية للترويج المميز يجب أن تكون على الأقل 50 ريال');
                    }
                    if (!$this->input('highlight_features') || count($this->input('highlight_features')) < 3) {
                        $validator->errors()->add('highlight_features', 'يجب تحديد على الأقل 3 ميزات مميزة للترويج المميز');
                    }
                    break;
            }
            
            // Validate priority level based on budget
            $priorityLevel = $this->input('priority_level');
            
            if ($priorityLevel > 7 && $dailyBudget < 30) {
                $validator->errors()->add('priority_level', 'مستوى الأولوية العالي يتطلب ميزانية يومية على الأقل 30 ريال');
            }
            
            if ($priorityLevel > 9 && $dailyBudget < 50) {
                $validator->errors()->add('priority_level', 'مستوى الأولوية الأعلى يتطلب ميزانية يومية على الأقل 50 ريال');
            }
        });
    }
}
