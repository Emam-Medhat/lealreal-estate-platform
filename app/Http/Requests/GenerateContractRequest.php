<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateContractRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'template_id' => 'required|exists:document_templates,id',
            'title' => 'required|string|max:255',
            'parties' => 'required|array|min:2',
            'parties.*.name' => 'required|string|max:255',
            'parties.*.email' => 'required|email',
            'parties.*.role' => 'required|string|max:100',
            'terms' => 'required|array',
            'terms.*.title' => 'required|string|max:255',
            'terms.*.content' => 'required|string',
            'terms.*.order' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'requires_signature' => 'boolean',
            'signature_deadline' => 'nullable|date|after:today',
        ];
    }

    public function messages()
    {
        return [
            'template_id.required' => 'حقل القالب مطلوب',
            'template_id.exists' => 'القالب المحدد غير موجود',
            'title.required' => 'حقل العنوان مطلوب',
            'parties.required' => 'حقل الأطراف مطلوب',
            'parties.min' => 'يجب تحديد طرفين على الأقل',
            'parties.*.name.required' => 'اسم الطرف مطلوب',
            'parties.*.email.required' => 'بريد الطرف الإلكتروني مطلوب',
            'parties.*.email.email' => 'يجب إدخال بريد إلكتروني صحيح',
            'parties.*.role.required' => 'دور الطرف مطلوب',
            'terms.required' => 'حقل البنود مطلوب',
            'terms.*.title.required' => 'عنوان البند مطلوب',
            'terms.*.content.required' => 'محتوى البند مطلوب',
            'terms.*.order.required' => 'ترتيب البند مطلوب',
            'start_date.required' => 'حقل تاريخ البدء مطلوب',
            'end_date.after' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء',
            'signature_deadline.after' => 'موعد التوقيع يجب أن يكون بعد اليوم',
        ];
    }
}
