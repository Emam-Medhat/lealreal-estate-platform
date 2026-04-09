<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLeaseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'tenant_id' => 'required|exists:tenants,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'rent_amount' => 'required|numeric|min:0',
            'security_deposit' => 'required|numeric|min:0',
            'payment_due_day' => 'required|integer|min:1|max:31',
            'late_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'grace_period_days' => 'nullable|integer|min:0|max:30',
            'lease_terms' => 'nullable|string',
            'special_conditions' => 'nullable|string',
            'utilities_included' => 'nullable|array',
            'amenities_included' => 'nullable|array',
            'maintenance_responsibility' => 'nullable|string',
            'subletting_allowed' => 'boolean',
            'pet_policy' => 'nullable|string',
            'parking_spaces' => 'nullable|integer|min:0',
            'storage_units' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'property_id.required' => 'حقل العقار مطلوب',
            'property_id.exists' => 'العقار المحدد غير موجود',
            'tenant_id.required' => 'حقل المستأجر مطلوب',
            'tenant_id.exists' => 'المستأجر المحدد غير موجود',
            'start_date.required' => 'حقل تاريخ البدء مطلوب',
            'start_date.after_or_equal' => 'تاريخ البدء يجب أن يكون اليوم أو بعد',
            'end_date.required' => 'حقل تاريخ الانتهاء مطلوب',
            'end_date.after' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء',
            'rent_amount.required' => 'حقل مبلغ الإيجار مطلوب',
            'rent_amount.numeric' => 'مبلغ الإيجار يجب أن يكون رقماً',
            'rent_amount.min' => 'مبلغ الإيجار يجب أن يكون 0 أو أكثر',
            'security_deposit.required' => 'حقل الوديعة الأمنية مطلوب',
            'security_deposit.numeric' => 'الوديعة الأمنية يجب أن تكون رقماً',
            'security_deposit.min' => 'الوديعة الأمنية يجب أن تكون 0 أو أكثر',
            'payment_due_day.required' => 'حقل يوم الاستحقاق مطلوب',
            'payment_due_day.integer' => 'يوم الاستحقاق يجب أن يكون رقماً صحيحاً',
            'payment_due_day.min' => 'يوم الاستحقاق يجب أن يكون بين 1 و 31',
            'payment_due_day.max' => 'يوم الاستحقاق يجب أن يكون بين 1 و 31',
            'late_fee_percentage.numeric' => 'نسبة رسوم التأخير يجب أن تكون رقماً',
            'late_fee_percentage.min' => 'نسبة رسوم التأخير يجب أن تكون 0 أو أكثر',
            'late_fee_percentage.max' => 'نسبة رسوم التأخير يجب أن تكون 100 أو أقل',
            'grace_period_days.integer' => 'فترة السماح يجب أن تكون رقماً صحيحاً',
            'grace_period_days.min' => 'فترة السماح يجب أن تكون 0 أو أكثر',
            'grace_period_days.max' => 'فترة السماح يجب أن تكون 30 يوماً أو أقل',
            'subletting_allowed.boolean' => 'حقل السماح بالإيجار الفرعي يجب أن يكون صح أو خطأ',
            'parking_spaces.integer' => 'عدد مواقف السيارات يجب أن يكون رقماً صحيحاً',
            'parking_spaces.min' => 'عدد مواقف السيارات يجب أن يكون 0 أو أكثر',
            'storage_units.integer' => 'عدد وحدات التخزين يجب أن يكون رقماً صحيحاً',
            'storage_units.min' => 'عدد وحدات التخزين يجب أن يكون 0 أو أكثر',
        ];
    }

    public function attributes()
    {
        return [
            'property_id' => 'العقار',
            'tenant_id' => 'المستأجر',
            'start_date' => 'تاريخ البدء',
            'end_date' => 'تاريخ الانتهاء',
            'rent_amount' => 'مبلغ الإيجار',
            'security_deposit' => 'الوديعة الأمنية',
            'payment_due_day' => 'يوم الاستحقاق',
            'late_fee_percentage' => 'نسبة رسوم التأخير',
            'grace_period_days' => 'فترة السماح',
            'lease_terms' => 'شروط العقد',
            'special_conditions' => 'شروط خاصة',
            'utilities_included' => 'المرافق المشمولة',
            'amenities_included' => 'الخدمات المشمولة',
            'maintenance_responsibility' => 'مسؤولية الصيانة',
            'subletting_allowed' => 'السماح بالإيجار الفرعي',
            'pet_policy' => 'سياسة الحيوانات الأليفة',
            'parking_spaces' => 'مواقف السيارات',
            'storage_units' => 'وحدات التخزين',
            'notes' => 'ملاحظات',
        ];
    }
}
