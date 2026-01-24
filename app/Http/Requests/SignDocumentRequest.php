<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'signature_data' => 'required|string',
            'signature_type' => 'required|in:draw,type,upload',
            'acceptance' => 'required|accepted',
            'signer_name' => 'required|string|max:255',
            'signer_email' => 'required|email',
            'ip_address' => 'nullable|string',
            'user_agent' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'signature_data.required' => 'بيانات التوقيع مطلوبة',
            'signature_type.required' => 'نوع التوقيع مطلوب',
            'signature_type.in' => 'نوع التوقيع يجب أن يكون: رسم، كتابة، أو رفع',
            'acceptance.required' => 'يجب الموافقة على الشروط والأحكام',
            'acceptance.accepted' => 'يجب الموافقة على الشروط والأحكام',
            'signer_name.required' => 'اسم الموقّع مطلوب',
            'signer_email.required' => 'بريد الموقّع الإلكتروني مطلوب',
            'signer_email.email' => 'يجب إدخال بريد إلكتروني صحيح',
        ];
    }
}
