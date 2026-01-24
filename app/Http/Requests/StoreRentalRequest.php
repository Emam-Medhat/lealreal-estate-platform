<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'tenant_id' => 'required|exists:tenants,id',
            'rental_number' => 'required|string|unique:rentals,rental_number',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'rent_amount' => 'required|numeric|min:0',
            'security_deposit' => 'required|numeric|min:0',
            'rent_frequency' => 'required|string|in:monthly,quarterly,annually',
            'payment_due_day' => 'required|integer|min:1|max:31',
            'late_fee' => 'nullable|numeric|min:0',
            'late_fee_type' => 'nullable|string|in:fixed,percentage',
            'terms_and_conditions' => 'required|string|min:10',
            'special_terms' => 'nullable|string',
            'utilities_included' => 'nullable|array',
            'amenities_included' => 'nullable|array',
            'maintenance_responsibility' => 'required|string',
            'renewal_option' => 'boolean',
            'renewal_terms' => 'nullable|string',
            'termination_notice_days' => 'required|integer|min:1|max:365',
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ];
    }

    public function messages(): array
    {
        return [
            'property_id.required' => 'حقل العقار مطلوب',
            'property_id.exists' => 'العقار المحدد غير موجود',
            'tenant_id.required' => 'حقل المستأجر مطلوب',
            'tenant_id.exists' => 'المستأجر المحدد غير موجود',
            'rental_number.required' => 'حقل رقم الإيجار مطلوب',
            'rental_number.unique' => 'رقم الإيجار مستخدم بالفعل',
            'start_date.required' => 'حقل تاريخ البدء مطلوب',
            'start_date.after_or_equal' => 'تاريخ البدء يجب أن يكون اليوم أو في المستقبل',
            'end_date.required' => 'حقل تاريخ الانتهاء مطلوب',
            'end_date.after' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء',
            'rent_amount.required' => 'حقل مبلغ الإيجار مطلوب',
            'rent_amount.numeric' => 'مبلغ الإيجار يجب أن يكون رقماً',
            'rent_amount.min' => 'مبلغ الإيجار يجب أن يكون 0 أو أكثر',
            'security_deposit.required' => 'حقل التأمين مطلوب',
            'security_deposit.numeric' => 'مبلغ التأمين يجب أن يكون رقماً',
            'security_deposit.min' => 'مبلغ التأمين يجب أن يكون 0 أو أكثر',
            'rent_frequency.required' => 'حقل تكرار الإيجار مطلوب',
            'rent_frequency.in' => 'تكرار الإيجار يجب أن يكون شهري أو ربع سنوي أو سنوي',
            'payment_due_day.required' => 'حقل يوم الاستحقاق مطلوب',
            'payment_due_day.integer' => 'يوم الاستحقاق يجب أن يكون رقماً',
            'payment_due_day.min' => 'يوم الاستحقاق يجب أن يكون بين 1 و 31',
            'payment_due_day.max' => 'يوم الاستحقاق يجب أن يكون بين 1 و 31',
            'late_fee.numeric' => 'رسوم التأخير يجب أن تكون رقماً',
            'late_fee.min' => 'رسوم التأخير يجب أن تكون 0 أو أكثر',
            'late_fee_type.in' => 'نوع رسوم التأخير يجب أن يكون ثابت أو نسبة مئوية',
            'terms_and_conditions.required' => 'حقل الشروط والأحكام مطلوب',
            'terms_and_conditions.min' => 'الشروط والأحكام يجب أن تحتوي على 10 أحرف على الأقل',
            'maintenance_responsibility.required' => 'حقل مسؤولية الصيانة مطلوب',
            'termination_notice_days.required' => 'حقل أيام الإشعار مطلوب',
            'termination_notice_days.integer' => 'أيام الإشعار يجب أن تكون رقماً',
            'termination_notice_days.min' => 'أيام الإشعار يجب أن تكون على الأقل يوم واحد',
            'termination_notice_days.max' => 'أيام الإشعار يجب أن لا تتجاوز 365 يوم',
            'documents.*.file' => 'يجب أن يكون الملف ملفاً صالحاً',
            'documents.*.max' => 'حجم الملف يجب أن لا يتجاوز 10 ميجابايت',
            'documents.*.mimes' => 'نوع الملف غير مدعوم',
        ];
    }
}
