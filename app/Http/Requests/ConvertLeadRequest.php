<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConvertLeadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'converted_to_type' => 'required|in:client,opportunity,property',
            'converted_to_id' => 'nullable|integer',
            'conversion_value' => 'required|numeric|min:0',
            'conversion_date' => 'required|date',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages()
    {
        return [
            'converted_to_type.required' => 'حقل نوع التحويل مطلوب',
            'converted_to_type.in' => 'نوع التحويل يجب أن يكون: عميل، فرصة، أو عقار',
            'conversion_value.required' => 'حقل قيمة التحويل مطلوب',
            'conversion_value.numeric' => 'قيمة التحويل يجب أن تكون رقم',
            'conversion_value.min' => 'قيمة التحويل يجب أن تكون أكبر من أو تساوي صفر',
            'conversion_date.required' => 'حقل تاريخ التحويل مطلوب',
            'conversion_date.date' => 'يجب إدخال تاريخ صحيح',
        ];
    }
}
