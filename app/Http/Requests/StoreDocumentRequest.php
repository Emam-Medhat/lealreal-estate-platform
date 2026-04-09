<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'category_id' => 'required|exists:document_categories,id',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:100',
            'is_confidential' => 'boolean',
            'expires_at' => 'nullable|date|after:today',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'حقل العنوان مطلوب',
            'title.max' => 'حقل العنوان يجب ألا يزيد عن 255 حرف',
            'category_id.required' => 'حقل الفئة مطلوب',
            'category_id.exists' => 'الفئة المحددة غير موجودة',
            'file.required' => 'حقل الملف مطلوب',
            'file.mimes' => 'يجب أن يكون الملف من نوع: pdf, doc, docx, xls, xlsx, ppt, pptx',
            'file.max' => 'حجم الملف يجب ألا يزيد عن 10 ميجابايت',
            'tags.array' => 'الوسوم يجب أن تكون مصفوفة',
            'tags.*.max' => 'كل وسم يجب ألا يزيد عن 100 حرف',
            'expires_at.after' => 'تاريخ الانتهاء يجب أن يكون بعد اليوم',
        ];
    }
}
