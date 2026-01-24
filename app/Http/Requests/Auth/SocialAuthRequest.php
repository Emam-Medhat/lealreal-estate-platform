<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SocialAuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => ['required', 'string', 'in:google,facebook,twitter,linkedin,github'],
            'code' => ['required', 'string'],
            'state' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'provider.required' => __('مزود الخدمة الاجتماعية مطلوب'),
            'provider.string' => __('مزود الخدمة الاجتماعية يجب أن يكون نصاً'),
            'provider.in' => __('مزود الخدمة الاجتماعية غير صالح'),
            'code.required' => __('رمز المصادقة مطلوب'),
            'code.string' => __('رمز المصادقة يجب أن يكون نصاً'),
            'state.required' => __('الحالة مطلوبة'),
            'state.string' => __('الحالة يجب أن تكون نصاً'),
        ];
    }
}
