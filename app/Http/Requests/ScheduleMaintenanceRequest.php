<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScheduleMaintenanceRequest extends FormRequest
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
            'maintenance_type' => 'required|in:preventive,corrective,emergency,inspection',
            'priority' => 'required|in:low,medium,high,emergency',
            'category' => 'required|in:plumbing,electrical,hvac,structural,general,other',
            'scheduled_date' => 'required|date|after:today',
            'scheduled_time' => 'required|string|max:255',
            'estimated_duration' => 'required|integer|min:1|max:480',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_team_id' => 'nullable|exists:maintenance_teams,id',
            'service_provider_id' => 'nullable|exists:service_providers,id',
            'estimated_cost' => 'nullable|numeric|min:0',
            'budget_approved' => 'boolean',
            'requires_permit' => 'boolean',
            'permit_details' => 'nullable|string|max:1000',
            'access_requirements' => 'nullable|string|max:1000',
            'safety_requirements' => 'nullable|string|max:1000',
            'materials_needed' => 'nullable|array',
            'materials_needed.*.name' => 'required|string|max:255',
            'materials_needed.*.quantity' => 'required|integer|min:1',
            'materials_needed.*.unit' => 'required|string|max:50',
            'tools_needed' => 'nullable|array',
            'tools_needed.*' => 'string|max:255',
            'special_instructions' => 'nullable|string|max:2000',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'recurring' => 'boolean',
            'recurring_frequency' => 'nullable|required_if:recurring,true|in:daily,weekly,monthly,quarterly,yearly',
            'recurring_interval' => 'nullable|required_if:recurring,true|integer|min:1',
            'recurring_end_date' => 'nullable|required_if:recurring,true|date|after:scheduled_date',
            'notification_settings' => 'nullable|array',
            'notification_settings.email' => 'boolean',
            'notification_settings.sms' => 'boolean',
            'notification_settings.advance_notice' => 'nullable|integer|min:0|max:30',
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
            'maintenance_type.required' => 'حقل نوع الصيانة مطلوب',
            'maintenance_type.in' => 'نوع الصيانة غير صالح',
            'priority.required' => 'حقل الأولوية مطلوب',
            'priority.in' => 'قيمة الأولوية غير صالحة',
            'category.required' => 'حقل الفئة مطلوب',
            'category.in' => 'قيمة الفئة غير صالحة',
            'scheduled_date.required' => 'حقل التاريخ المجدول مطلوب',
            'scheduled_date.date' => 'التاريخ المجدول يجب أن يكون تاريخ صالح',
            'scheduled_date.after' => 'التاريخ المجدول يجب أن يكون بعد اليوم',
            'scheduled_time.required' => 'حقل الوقت المجدول مطلوب',
            'scheduled_time.max' => 'الوقت المجدول يجب ألا يزيد عن 255 حرف',
            'estimated_duration.required' => 'حقل المدة المقدرة مطلوب',
            'estimated_duration.integer' => 'المدة المقدرة يجب أن تكون رقم صحيح',
            'estimated_duration.min' => 'المدة المقدرة يجب أن تكون على الأقل 1 دقيقة',
            'estimated_duration.max' => 'المدة المقدرة يجب ألا تزيد عن 480 دقيقة (8 ساعات)',
            'assigned_to.exists' => 'المستخدم المحدد غير موجود',
            'assigned_team_id.exists' => 'الفريق المحدد غير موجود',
            'service_provider_id.exists' => 'مقدم الخدمة المحدد غير موجود',
            'estimated_cost.numeric' => 'التكلفة المقدرة يجب أن تكون رقم',
            'estimated_cost.min' => 'التكلفة المقدرة يجب أن تكون 0 أو أكثر',
            'permit_details.max' => 'تفاصيل التصريح يجب ألا تزيد عن 1000 حرف',
            'access_requirements.max' => 'متطلبات الدخول يجب ألا تزيد عن 1000 حرف',
            'safety_requirements.max' => 'متطلبات السلامة يجب ألا تزيد عن 1000 حرف',
            'materials_needed.*.name.required' => 'اسم المادة مطلوب',
            'materials_needed.*.name.max' => 'اسم المادة يجب ألا يزيد عن 255 حرف',
            'materials_needed.*.quantity.required' => 'كمية المادة مطلوبة',
            'materials_needed.*.quantity.integer' => 'كمية المادة يجب أن تكون رقم صحيح',
            'materials_needed.*.quantity.min' => 'كمية المادة يجب أن تكون على الأقل 1',
            'materials_needed.*.unit.required' => 'وحدة المادة مطلوبة',
            'materials_needed.*.unit.max' => 'وحدة المادة يجب ألا تزيد عن 50 حرف',
            'tools_needed.*.max' => 'اسم الأداة يجب ألا يزيد عن 255 حرف',
            'special_instructions.max' => 'التعليمات الخاصة يجب ألا تزيد عن 2000 حرف',
            'contact_person.max' => 'اسم الشخص للتواصل يجب ألا يزيد عن 255 حرف',
            'contact_phone.max' => 'رقم الهاتف يجب ألا يزيد عن 20 حرف',
            'attachments.*.file' => 'يجب أن يكون المرفق ملف',
            'attachments.*.mimes' => 'نوع الملف غير مدعوع. الأنواع المدعوعة: pdf, doc, docx, jpg, jpeg, png',
            'attachments.*.max' => 'حجم الملف يجب ألا يزيد عن 2 ميجابايت',
            'recurring_frequency.required_if' => 'حقل التكرار مطلوب عند اختيار الصيانة المتكررة',
            'recurring_frequency.in' => 'قيمة التكرار غير صالحة',
            'recurring_interval.required_if' => 'حقل الفاصل الزمني مطلوب عند اختيار الصيانة المتكررة',
            'recurring_interval.integer' => 'الفاصل الزمني يجب أن يكون رقم صحيح',
            'recurring_interval.min' => 'الفاصل الزمني يجب أن يكون على الأقل 1',
            'recurring_end_date.required_if' => 'حقل تاريخ انتهاء التكرار مطلوب عند اختيار الصيانة المتكررة',
            'recurring_end_date.date' => 'تاريخ انتهاء التكرار يجب أن يكون تاريخ صالح',
            'recurring_end_date.after' => 'تاريخ انتهاء التكرار يجب أن يكون بعد التاريخ المجدول',
            'notification_settings.advance_notice.integer' => 'الإشعار المسبق يجب أن يكون رقم صحيح',
            'notification_settings.advance_notice.min' => 'الإشعار المسبق يجب أن يكون 0 أو أكثر',
            'notification_settings.advance_notice.max' => 'الإشعار المسبق يجب ألا يزيد عن 30 يوم',
            'notes.max' => 'الملاحظات يجب ألا تزيد عن 2000 حرف',
        ];
    }
}
