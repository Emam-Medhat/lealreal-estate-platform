<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScreenTenantRequest extends FormRequest
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
            'tenant_id' => 'required|exists:tenants,id',
            'screening_type' => 'required|in:basic,comprehensive,enhanced',
            'credit_check' => 'required|boolean',
            'criminal_check' => 'required|boolean',
            'employment_verification' => 'required|boolean',
            'rental_history' => 'required|boolean',
            'background_check' => 'required|boolean',
            'identity_verification' => 'required|boolean',
            'reference_checks' => 'required|array',
            'reference_checks.*.name' => 'required|string|max:255',
            'reference_checks.*.relationship' => 'required|string|max:255',
            'reference_checks.*.phone' => 'required|string|max:20',
            'reference_checks.*.email' => 'nullable|email|max:255',
            'income_verification' => 'required|boolean',
            'income_documents' => 'nullable|array',
            'income_documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'identity_documents' => 'nullable|array',
            'identity_documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'employment_documents' => 'nullable|array',
            'employment_documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'rental_documents' => 'nullable|array',
            'rental_documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'previous_landlords' => 'nullable|array',
            'previous_landlords.*.name' => 'required|string|max:255',
            'previous_landlords.*.property' => 'required|string|max:255',
            'previous_landlords.*.phone' => 'required|string|max:20',
            'previous_landlords.*.email' => 'nullable|email|max:255',
            'previous_landlords.*.rent_amount' => 'required|numeric|min:0',
            'previous_landlords.*.duration' => 'required|integer|min:1',
            'previous_landlords.*.reason_leaving' => 'nullable|string|max:500',
            'special_requirements' => 'nullable|array',
            'special_requirements.*' => 'string|max:255',
            'risk_factors' => 'nullable|array',
            'risk_factors.*' => 'string|max:255',
            'screening_notes' => 'nullable|string|max:2000',
            'priority_level' => 'nullable|in:low,medium,high,urgent',
            'expected_move_in_date' => 'nullable|date|after_or_equal:today',
            'property_preferences' => 'nullable|array',
            'property_preferences.min_rent' => 'nullable|numeric|min:0',
            'property_preferences.max_rent' => 'nullable|numeric|min:0',
            'property_preferences.property_type' => 'nullable|string|max:255',
            'property_preferences.location' => 'nullable|string|max:255',
            'property_preferences.bedrooms' => 'nullable|integer|min:0',
            'property_preferences.bathrooms' => 'nullable|integer|min:0',
            'property_preferences.amenities' => 'nullable|array',
            'property_preferences.amenities.*' => 'string|max:255',
            'consent_background_check' => 'required|boolean',
            'consent_credit_check' => 'required|boolean',
            'consent_contact_references' => 'required|boolean',
            'consent_data_processing' => 'required|boolean',
            'screening_fee' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,credit_card,bank_transfer,online',
            'urgent_screening' => 'boolean',
            'additional_notes' => 'nullable|string|max:2000',
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
            'tenant_id.required' => 'حقل المستأجر مطلوب',
            'tenant_id.exists' => 'المستأجر المحدد غير موجود',
            'screening_type.required' => 'حقل نوع الفحص مطلوب',
            'screening_type.in' => 'نوع الفحص غير صالح',
            'credit_check.required' => 'حقل فحص الائتمان مطلوب',
            'credit_check.boolean' => 'فحص الائتمان يجب أن يكون true أو false',
            'criminal_check.required' => 'حقل فحص السجل الجنائي مطلوب',
            'criminal_check.boolean' => 'فحص السجل الجنائي يجب أن يكون true أو false',
            'employment_verification.required' => 'حقل التحقق من التوظيف مطلوب',
            'employment_verification.boolean' => 'التحقق من التوظيف يجب أن يكون true أو false',
            'rental_history.required' => 'حقل سجل الإيجار مطلوب',
            'rental_history.boolean' => 'سجل الإيجار يجب أن يكون true أو false',
            'background_check.required' => 'حقل الفحص الخلفي مطلوب',
            'background_check.boolean' => 'الفحص الخلفي يجب أن يكون true أو false',
            'identity_verification.required' => 'حقل التحقق من الهوية مطلوب',
            'identity_verification.boolean' => 'التحقق من الهوية يجب أن يكون true أو false',
            'reference_checks.required' => 'حقل المراجع مطلوب',
            'reference_checks.array' => 'المراجع يجب أن تكون مصفوفة',
            'reference_checks.*.name.required' => 'اسم المرجع مطلوب',
            'reference_checks.*.name.max' => 'اسم المرجع يجب ألا يزيد عن 255 حرف',
            'reference_checks.*.relationship.required' => 'علاقة المرجع مطلوبة',
            'reference_checks.*.relationship.max' => 'علاقة المرجع يجب ألا تزيد عن 255 حرف',
            'reference_checks.*.phone.required' => 'هاتف المرجع مطلوب',
            'reference_checks.*.phone.max' => 'هاتف المرجع يجب ألا يزيد عن 20 حرف',
            'reference_checks.*.email.email' => 'بريد المرجع الإلكتروني يجب أن يكون صالح',
            'reference_checks.*.email.max' => 'بريد المرجع الإلكتروني يجب ألا يزيد عن 255 حرف',
            'income_verification.required' => 'حقل التحقق من الدخل مطلوب',
            'income_verification.boolean' => 'التحقق من الدخل يجب أن يكون true أو false',
            'income_documents.array' => 'مستندات الدخل يجب أن تكون مصفوفة',
            'income_documents.*.file' => 'يجب أن يكون مستند الدخل ملف',
            'income_documents.*.mimes' => 'نوع ملف الدخل غير مدعوع. الأنواع المدعوعة: pdf, doc, docx, jpg, jpeg, png',
            'income_documents.*.max' => 'حجم ملف الدخل يجب ألا يزيد عن 2 ميجابايت',
            'identity_documents.array' => 'مستندات الهوية يجب أن تكون مصفوفة',
            'identity_documents.*.file' => 'يجب أن يكون مستند الهوية ملف',
            'identity_documents.*.mimes' => 'نوع ملف الهوية غير مدعوع. الأنواع المدعوعة: pdf, doc, docx, jpg, jpeg, png',
            'identity_documents.*.max' => 'حجم ملف الهوية يجب ألا يزيد عن 2 ميجابايت',
            'employment_documents.array' => 'مستندات التوظيف يجب أن تكون مصفوفة',
            'employment_documents.*.file' => 'يجب أن يكون مستند التوظيف ملف',
            'employment_documents.*.mimes' => 'نوع ملف التوظيف غير مدعوع. الأنواع المدعوعة: pdf, doc, docx, jpg, jpeg, png',
            'employment_documents.*.max' => 'حجم ملف التوظيف يجب ألا يزيد عن 2 ميجابايت',
            'rental_documents.array' => 'مستندات الإيجار يجب أن تكون مصفوفة',
            'rental_documents.*.file' => 'يجب أن يكون مستند الإيجار ملف',
            'rental_documents.*.mimes' => 'نوع ملف الإيجار غير مدعوع. الأنواع المدعوعة: pdf, doc, docx, jpg, jpeg, png',
            'rental_documents.*.max' => 'حجم ملف الإيجار يجب ألا يزيد عن 2 ميجابايت',
            'previous_landlords.array' => 'الملاك السابقون يجب أن يكونوا مصفوفة',
            'previous_landlords.*.name.required' => 'اسم المالك السابق مطلوب',
            'previous_landlords.*.name.max' => 'اسم المالك السابق يجب ألا يزيد عن 255 حرف',
            'previous_landlords.*.property.required' => 'عقار المالك السابق مطلوب',
            'previous_landlords.*.property.max' => 'عقار المالك السابق يجب ألا يزيد عن 255 حرف',
            'previous_landlords.*.phone.required' => 'هاتف المالك السابق مطلوب',
            'previous_landlords.*.phone.max' => 'هاتف المالك السابق يجب ألا يزيد عن 20 حرف',
            'previous_landlords.*.email.email' => 'بريد المالك السابق الإلكتروني يجب أن يكون صالح',
            'previous_landlords.*.email.max' => 'بريد المالك السابق الإلكتروني يجب ألا يزيد عن 255 حرف',
            'previous_landlords.*.rent_amount.required' => 'مبلغ الإيجار السابق مطلوب',
            'previous_landlords.*.rent_amount.numeric' => 'مبلغ الإيجار السابق يجب أن يكون رقم',
            'previous_landlords.*.rent_amount.min' => 'مبلغ الإيجار السابق يجب أن يكون 0 أو أكثر',
            'previous_landlords.*.duration.required' => 'مدة الإيجار السابقة مطلوبة',
            'previous_landlords.*.duration.integer' => 'مدة الإيجار السابقة يجب أن تكون رقم صحيح',
            'previous_landlords.*.duration.min' => 'مدة الإيجار السابقة يجب أن تكون على الأقل 1',
            'previous_landlords.*.reason_leaving.max' => 'سبب المغادرة يجب ألا يزيد عن 500 حرف',
            'special_requirements.array' => 'المتطلبات الخاصة يجب أن تكون مصفوفة',
            'special_requirements.*.max' => 'المتطلب الخاص يجب ألا يزيد عن 255 حرف',
            'risk_factors.array' => 'عوامل الخطر يجب أن تكون مصفوفة',
            'risk_factors.*.max' => 'عامل الخطر يجب ألا يزيد عن 255 حرف',
            'screening_notes.max' => 'ملاحظات الفحص يجب ألا تزيد عن 2000 حرف',
            'priority_level.in' => 'مستوى الأولوية غير صالح',
            'expected_move_in_date.date' => 'تاريخ الانتقال المتوقع يجب أن يكون تاريخ صالح',
            'expected_move_in_date.after_or_equal' => 'تاريخ الانتقال المتوقع يجب أن يكون اليوم أو بعد',
            'property_preferences.min_rent.numeric' => 'الحد الأدنى للإيجار يجب أن يكون رقم',
            'property_preferences.min_rent.min' => 'الحد الأدنى للإيجار يجب أن يكون 0 أو أكثر',
            'property_preferences.max_rent.numeric' => 'الحد الأقصى للإيجار يجب أن يكون رقم',
            'property_preferences.max_rent.min' => 'الحد الأقصى للإيجار يجب أن يكون 0 أو أكثر',
            'property_preferences.property_type.max' => 'نوع العقار يجب ألا يزيد عن 255 حرف',
            'property_preferences.location.max' => 'الموقع يجب ألا يزيد عن 255 حرف',
            'property_preferences.bedrooms.integer' => 'عدد غرف النوم يجب أن يكون رقم صحيح',
            'property_preferences.bedrooms.min' => 'عدد غرف النوم يجب أن يكون 0 أو أكثر',
            'property_preferences.bathrooms.integer' => 'عدد الحمامات يجب أن يكون رقم صحيح',
            'property_preferences.bathrooms.min' => 'عدد الحمامات يجب أن يكون 0 أو أكثر',
            'property_preferences.amenities.array' => 'المرافق يجب أن تكون مصفوفة',
            'property_preferences.amenities.*.max' => 'المرفق يجب ألا يزيد عن 255 حرف',
            'consent_background_check.required' => 'موافقة الفحص الخلفي مطلوبة',
            'consent_background_check.boolean' => 'موافقة الفحص الخلفي يجب أن تكون true أو false',
            'consent_credit_check.required' => 'موافقة فحص الائتمان مطلوبة',
            'consent_credit_check.boolean' => 'موافقة فحص الائتمان يجب أن تكون true أو false',
            'consent_contact_references.required' => 'موافقة الاتصال بالمراجع مطلوبة',
            'consent_contact_references.boolean' => 'موافقة الاتصال بالمراجع يجب أن تكون true أو false',
            'consent_data_processing.required' => 'موافقة معالجة البيانات مطلوبة',
            'consent_data_processing.boolean' => 'موافقة معالجة البيانات يجب أن تكون true أو false',
            'screening_fee.numeric' => 'رسوم الفحص يجب أن تكون رقم',
            'screening_fee.min' => 'رسوم الفحص يجب أن تكون 0 أو أكثر',
            'payment_method.in' => 'طريقة الدفع غير صالحة',
            'additional_notes.max' => 'الملاحظات الإضافية يجب ألا تزيد عن 2000 حرف',
        ];
    }
}
