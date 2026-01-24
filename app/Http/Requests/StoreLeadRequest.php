<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:leads,email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'source_id' => 'nullable|exists:lead_sources,id',
            'status_id' => 'required|exists:lead_statuses,id',
            'campaign_id' => 'nullable|exists:lead_campaigns,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,critical',
            'estimated_value' => 'nullable|numeric|min:0',
            'expected_close_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:2000',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:lead_tags,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'حقل الاسم مطلوب',
            'name.max' => 'حقل الاسم يجب ألا يزيد عن 255 حرف',
            'email.required' => 'حقل البريد الإلكتروني مطلوب',
            'email.email' => 'يجب إدخال بريد إلكتروني صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'status_id.required' => 'حقل الحالة مطلوب',
            'status_id.exists' => 'الحالة المحددة غير موجودة',
            'priority.required' => 'حقل الأولوية مطلوب',
            'priority.in' => 'الأولوية يجب أن تكون: منخفضة، متوسطة، عالية، أو حرجة',
            'estimated_value.numeric' => 'القيمة المقدرة يجب أن تكون رقم',
            'estimated_value.min' => 'القيمة المقدرة يجب أن تكون أكبر من أو تساوي صفر',
            'expected_close_date.after' => 'تاريخ الإغلاق المتوقع يجب أن يكون بعد اليوم',
        ];
    }
}
