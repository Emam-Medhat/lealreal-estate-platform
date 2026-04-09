<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateMaintenanceRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'priority' => 'required|in:low,medium,high,emergency',
            'category' => 'required|in:plumbing,electrical,hvac,structural,general,other',
            'requested_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'access_instructions' => 'nullable|string|max:1000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'estimated_cost' => 'nullable|numeric|min:0',
            'budget_approved' => 'boolean',
            'urgent' => 'boolean',
            'recurring_issue' => 'boolean',
            'previous_work_order_id' => 'nullable|exists:work_orders,id',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'property_id.required' => 'حقل العقار مطلوب',
            'property_id.exists' => 'العقار المحدد غير موجود',
            'title.required' => 'حقل العنوان مطلوب',
            'title.max' => 'العنوان يجب ألا يزيد عن 255 حرف',
            'description.required' => 'حقل الوصف مطلوب',
            'description.min' => 'الوصف يجب أن يحتوي على 10 أحرف على الأقل',
            'priority.required' => 'حقل الأولوية مطلوب',
            'priority.in' => 'قيمة الأولوية غير صالحة',
            'category.required' => 'حقل الفئة مطلوب',
            'category.in' => 'قيمة الفئة غير صالحة',
            'requested_date.required' => 'حقل التاريخ المطلوب مطلوب',
            'requested_date.date' => 'التاريخ المطلوب يجب أن يكون تاريخ صالح',
            'requested_date.after_or_equal' => 'التاريخ المطلوب يجب أن يكون اليوم أو بعد',
            'preferred_time.max' => 'الوقت المفضل يجب ألا يزيد عن 255 حرف',
            'contact_person.max' => 'اسم الشخص للتواصل يجب ألا يزيد عن 255 حرف',
            'contact_phone.max' => 'رقم الهاتف يجب ألا يزيد عن 20 حرف',
            'access_instructions.max' => 'تعليمات الدخول يجب ألا تزيد عن 1000 حرف',
            'attachments.*.file' => 'يجب أن يكون المرفق ملف',
            'attachments.*.mimes' => 'نوع الملف غير مدعوع. الأنواع المدعوعة: pdf, doc, docx, jpg, jpeg, png',
            'attachments.*.max' => 'حجم الملف يجب ألا يزيد عن 2 ميجابايت',
            'estimated_cost.numeric' => 'التكلفة المقدرة يجب أن تكون رقم',
            'estimated_cost.min' => 'التكلفة المقدرة يجب أن تكون 0 أو أكثر',
            'previous_work_order_id.exists' => 'أمر العمل السابق غير موجود',
            'notes.max' => 'الملاحظات يجب ألا تزيد عن 2000 حرف',
        ];
    }
}
