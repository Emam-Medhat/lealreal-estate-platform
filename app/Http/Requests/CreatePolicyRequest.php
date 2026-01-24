<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePolicyRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'description_ar' => 'nullable|string|max:2000',
            'insurance_provider_id' => 'required|exists:insurance_providers,id',
            'property_id' => 'required|exists:properties,id',
            'policy_type' => 'required|in:property,liability,comprehensive,fire,flood,earthquake,theft,tenant,landlord,builder_risk,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'premium_amount' => 'required|numeric|min:0',
            'coverage_amount' => 'required|numeric|min:0',
            'deductible' => 'nullable|numeric|min:0',
            'payment_frequency' => 'required|in:monthly,quarterly,semi_annually,annually',
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card,online,auto_debit',
            'auto_renewal' => 'boolean',
            'renewal_terms' => 'nullable|string|max:1000',
            'special_conditions' => 'nullable|array',
            'special_conditions.*' => 'string|max:500',
            'exclusions' => 'nullable|array',
            'exclusions.*' => 'string|max:500',
            'coverages' => 'required|array|min:1',
            'coverages.*.type' => 'required|string|max:255',
            'coverages.*.amount' => 'required|numeric|min:0',
            'coverages.*.premium' => 'nullable|numeric|min:0',
            'coverages.*.deductible' => 'nullable|numeric|min:0',
            'risk_factors' => 'nullable|array',
            'risk_factors.*' => 'string|max:255',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string|max:2000',
            'notes_ar' => 'nullable|string|max:2000',
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
            'title.required' => 'حقل العنوان مطلوب',
            'title.max' => 'العنوان يجب ألا يزيد عن 255 حرف',
            'title_ar.max' => 'العنوان بالعربية يجب ألا يزيد عن 255 حرف',
            'description.max' => 'الوصف يجب ألا يزيد عن 2000 حرف',
            'description_ar.max' => 'الوصف بالعربية يجب ألا يزيد عن 2000 حرف',
            'insurance_provider_id.required' => 'حقل شركة التأمين مطلوب',
            'insurance_provider_id.exists' => 'شركة التأمين المحددة غير موجودة',
            'property_id.required' => 'حقل العقار مطلوب',
            'property_id.exists' => 'العقار المحدد غير موجود',
            'policy_type.required' => 'حقل نوع البوليصة مطلوب',
            'policy_type.in' => 'نوع البوليصة غير صالح',
            'start_date.required' => 'حقل تاريخ البدء مطلوب',
            'start_date.after_or_equal' => 'تاريخ البدء يجب أن يكون اليوم أو بعد',
            'end_date.required' => 'حقل تاريخ الانتهاء مطلوب',
            'end_date.after' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء',
            'premium_amount.required' => 'حقل قسط التأمين مطلوب',
            'premium_amount.numeric' => 'قسط التأمين يجب أن يكون رقم',
            'premium_amount.min' => 'قسط التأمين يجب أن يكون 0 أو أكثر',
            'coverage_amount.required' => 'حقل مبلغ التغطية مطلوب',
            'coverage_amount.numeric' => 'مبلغ التغطية يجب أن يكون رقم',
            'coverage_amount.min' => 'مبلغ التغطية يجب أن يكون 0 أو أكثر',
            'deductible.numeric' => 'الخصم يجب أن يكون رقم',
            'deductible.min' => 'الخصم يجب أن يكون 0 أو أكثر',
            'payment_frequency.required' => 'حقل تكرار الدفع مطلوب',
            'payment_frequency.in' => 'تكرار الدفع غير صالح',
            'payment_method.required' => 'حقل طريقة الدفع مطلوب',
            'payment_method.in' => 'طريقة الدفع غير صالحة',
            'auto_renewal.boolean' => 'التجديد التلقائي يجب أن يكون true أو false',
            'renewal_terms.max' => 'شروط التجديد يجب ألا تزيد عن 1000 حرف',
            'special_conditions.array' => 'الشروط الخاصة يجب أن تكون مصفوفة',
            'special_conditions.*.max' => 'الشرط الخاص يجب ألا يزيد عن 500 حرف',
            'exclusions.array' => 'الاستثناءات يجب أن تكون مصفوفة',
            'exclusions.*.max' => 'الاستثناء يجب ألا يزيد عن 500 حرف',
            'coverages.required' => 'حقل التغطيات مطلوب',
            'coverages.array' => 'التغطيات يجب أن تكون مصفوفة',
            'coverages.min' => 'يجب إضافة تغطية واحدة على الأقل',
            'coverages.*.type.required' => 'نوع التغطية مطلوب',
            'coverages.*.type.max' => 'نوع التغطية يجب ألا يزيد عن 255 حرف',
            'coverages.*.amount.required' => 'مبلغ التغطية مطلوب',
            'coverages.*.amount.numeric' => 'مبلغ التغطية يجب أن يكون رقم',
            'coverages.*.amount.min' => 'مبلغ التغطية يجب أن يكون 0 أو أكثر',
            'coverages.*.premium.numeric' => 'قسط التغطية يجب أن يكون رقم',
            'coverages.*.premium.min' => 'قسط التغطية يجب أن يكون 0 أو أكثر',
            'coverages.*.deductible.numeric' => 'خصم التغطية يجب أن يكون رقم',
            'coverages.*.deductible.min' => 'خصم التغطية يجب أن يكون 0 أو أكثر',
            'risk_factors.array' => 'عوامل الخطر يجب أن تكون مصفوفة',
            'risk_factors.*.max' => 'عامل الخطر يجب ألا يزيد عن 255 حرف',
            'documents.array' => 'المستندات يجب أن تكون مصفوفة',
            'documents.*.file' => 'يجب أن يكون المستند ملف',
            'documents.*.mimes' => 'نوع ملف المستند غير مدعوع. الأنواع المدعوعة: pdf, doc, docx, jpg, jpeg, png',
            'documents.*.max' => 'حجم ملف المستند يجب ألا يزيد عن 5 ميجابايت',
            'photos.array' => 'الصور يجب أن تكون مصفوفة',
            'photos.*.image' => 'يجب أن يكون الملف صورة',
            'photos.*.mimes' => 'نوع الصورة غير مدعوع. الأنواع المدعوعة: jpg, jpeg, png',
            'photos.*.max' => 'حجم الصورة يجب ألا يزيد عن 2 ميجابايت',
            'notes.max' => 'الملاحظات يجب ألا تزيد عن 2000 حرف',
            'notes_ar.max' => 'الملاحظات بالعربية يجب ألا تزيد عن 2000 حرف',
        ];
    }
}
