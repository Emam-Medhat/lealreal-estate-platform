<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignTicketRequest extends FormRequest
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
            'ticket_id' => 'required|exists:maintenance_tickets,id',
            'assigned_to' => 'required|exists:users,id',
            'assigned_team_id' => 'nullable|exists:maintenance_teams,id',
            'service_provider_id' => 'nullable|exists:service_providers,id',
            'priority' => 'nullable|in:low,medium,high,emergency',
            'estimated_cost' => 'nullable|numeric|min:0',
            'estimated_duration' => 'nullable|integer|min:1|max:480',
            'scheduled_date' => 'nullable|date|after_or_equal:today',
            'scheduled_time' => 'nullable|string|max:255',
            'assignment_notes' => 'nullable|string|max:1000',
            'special_requirements' => 'nullable|string|max:1000',
            'access_instructions' => 'nullable|string|max:1000',
            'materials_needed' => 'nullable|array',
            'materials_needed.*.name' => 'required|string|max:255',
            'materials_needed.*.quantity' => 'required|integer|min:1',
            'materials_needed.*.unit' => 'required|string|max:50',
            'tools_needed' => 'nullable|array',
            'tools_needed.*' => 'string|max:255',
            'notify_assignee' => 'boolean',
            'notify_requester' => 'boolean',
            'auto_schedule' => 'boolean',
            'escalation_rules' => 'nullable|array',
            'escalation_rules.escalate_after' => 'nullable|integer|min:1',
            'escalation_rules.escalate_to' => 'nullable|exists:users,id',
            'escalation_rules.escalation_reason' => 'nullable|string|max:500',
            'follow_up_required' => 'boolean',
            'follow_up_date' => 'nullable|required_if:follow_up_required,true|date|after:today',
            'follow_up_notes' => 'nullable|string|max:1000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'internal_notes' => 'nullable|string|max:2000',
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
            'ticket_id.required' => 'حقل التذكرة مطلوب',
            'ticket_id.exists' => 'التذكرة المحددة غير موجودة',
            'assigned_to.required' => 'حقل الموظف المسند إليه مطلوب',
            'assigned_to.exists' => 'الموظف المحدد غير موجود',
            'assigned_team_id.exists' => 'الفريق المحدد غير موجود',
            'service_provider_id.exists' => 'مقدم الخدمة المحدد غير موجود',
            'priority.in' => 'قيمة الأولوية غير صالحة',
            'estimated_cost.numeric' => 'التكلفة المقدرة يجب أن تكون رقم',
            'estimated_cost.min' => 'التكلفة المقدرة يجب أن تكون 0 أو أكثر',
            'estimated_duration.integer' => 'المدة المقدرة يجب أن تكون رقم صحيح',
            'estimated_duration.min' => 'المدة المقدرة يجب أن تكون على الأقل 1 دقيقة',
            'estimated_duration.max' => 'المدة المقدرة يجب ألا تزيد عن 480 دقيقة (8 ساعات)',
            'scheduled_date.date' => 'التاريخ المجدول يجب أن يكون تاريخ صالح',
            'scheduled_date.after_or_equal' => 'التاريخ المجدول يجب أن يكون اليوم أو بعد',
            'scheduled_time.max' => 'الوقت المجدول يجب ألا يزيد عن 255 حرف',
            'assignment_notes.max' => 'ملاحظات الإسناد يجب ألا تزيد عن 1000 حرف',
            'special_requirements.max' => 'المتطلبات الخاصة يجب ألا تزيد عن 1000 حرف',
            'access_instructions.max' => 'تعليمات الدخول يجب ألا تزيد عن 1000 حرف',
            'materials_needed.*.name.required' => 'اسم المادة مطلوب',
            'materials_needed.*.name.max' => 'اسم المادة يجب ألا يزيد عن 255 حرف',
            'materials_needed.*.quantity.required' => 'كمية المادة مطلوبة',
            'materials_needed.*.quantity.integer' => 'كمية المادة يجب أن تكون رقم صحيح',
            'materials_needed.*.quantity.min' => 'كمية المادة يجب أن تكون على الأقل 1',
            'materials_needed.*.unit.required' => 'وحدة المادة مطلوبة',
            'materials_needed.*.unit.max' => 'وحدة المادة يجب ألا تزيد عن 50 حرف',
            'tools_needed.*.max' => 'اسم الأداة يجب ألا يزيد عن 255 حرف',
            'escalation_rules.escalate_after.integer' => 'فترة التصعيد يجب أن تكون رقم صحيح',
            'escalation_rules.escalate_after.min' => 'فترة التصعيد يجب أن تكون على الأقل 1',
            'escalation_rules.escalate_to.exists' => 'المستخدم المحدد للتصعيد غير موجود',
            'escalation_rules.escalation_reason.max' => 'سبب التصعيد يجب ألا يزيد عن 500 حرف',
            'follow_up_date.required_if' => 'حقل تاريخ المتابعة مطلوب عند اختيار المتابعة المطلوبة',
            'follow_up_date.date' => 'تاريخ المتابعة يجب أن يكون تاريخ صالح',
            'follow_up_date.after' => 'تاريخ المتابعة يجب أن يكون بعد اليوم',
            'follow_up_notes.max' => 'ملاحظات المتابعة يجب ألا تزيد عن 1000 حرف',
            'attachments.*.file' => 'يجب أن يكون المرفق ملف',
            'attachments.*.mimes' => 'نوع الملف غير مدعوع. الأنواع المدعوعة: pdf, doc, docx, jpg, jpeg, png',
            'attachments.*.max' => 'حجم الملف يجب ألا يزيد عن 2 ميجابايت',
            'internal_notes.max' => 'الملاحظات الداخلية يجب ألا تزيد عن 2000 حرف',
        ];
    }
}
