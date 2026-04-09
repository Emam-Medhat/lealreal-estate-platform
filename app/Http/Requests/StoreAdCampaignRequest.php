<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAdCampaignRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'objective' => 'required|in:awareness,traffic,conversions,engagement',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'total_budget' => 'required|numeric|min:10',
            'daily_budget' => 'required|numeric|min:1',
            'target_audience_size' => 'nullable|integer|min:1',
            'estimated_reach' => 'nullable|integer|min:1',
            'auto_renew' => 'nullable|boolean',
            'renewal_amount' => 'nullable|numeric|min:1',
            'renewal_trigger' => 'nullable|in:exhausted,below_threshold',
            'alert_threshold' => 'nullable|numeric|min:1|max:100'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'حقل اسم الحملة مطلوب',
            'name.max' => 'اسم الحملة يجب ألا يزيد عن 255 حرف',
            'description.max' => 'وصف الحملة يجب ألا يزيد عن 500 حرف',
            'objective.required' => 'حقل هدف الحملة مطلوب',
            'objective.in' => 'هدف الحملة غير صالح',
            'start_date.required' => 'حقل تاريخ البدء مطلوب',
            'start_date.after_or_equal' => 'تاريخ البدء يجب أن يكون اليوم أو تاريخ لاحق',
            'end_date.required' => 'حقل تاريخ الانتهاء مطلوب',
            'end_date.after' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء',
            'total_budget.required' => 'حقل الميزانية الإجمالية مطلوب',
            'total_budget.min' => 'الميزانية الإجمالية يجب أن تكون على الأقل 10 ريال',
            'daily_budget.required' => 'حقل الميزانية اليومية مطلوب',
            'daily_budget.min' => 'الميزانية اليومية يجب أن تكون على الأقل 1 ريال',
            'target_audience_size.min' => 'حجم الجمهور المستهدف يجب أن يكون على الأقل 1',
            'estimated_reach.min' => 'الوصول المقدر يجب أن يكون على الأقل 1',
            'renewal_amount.min' => 'مبلغ التجديد يجب أن يكون على الأقل 1 ريال',
            'renewal_trigger.in' => 'محفز التجديد غير صالح',
            'alert_threshold.min' => 'عتبة التنبيه يجب أن تكون بين 1 و 100',
            'alert_threshold.max' => 'عتبة التنبيه يجب أن تكون بين 1 و 100'
        ];
    }
}
