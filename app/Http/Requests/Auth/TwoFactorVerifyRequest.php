<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class TwoFactorVerifyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'regex:/^[0-9]{6}$/'],
            'remember_device' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => __('رمز المصادقة مطلوب'),
            'code.string' => __('رمز المصادقة يجب أن يكون نصاً'),
            'code.regex' => __('رمز المصادقة يجب أن يكون مكوناً من 6 أرقام'),
            'remember_device.boolean' => __('خيار تذكر الجهاز يجب أن يكون قيمة منطقية'),
        ];
    }
}
