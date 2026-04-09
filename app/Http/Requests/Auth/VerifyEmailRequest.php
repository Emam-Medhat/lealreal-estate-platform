<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'size:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => __('رمز التحقق مطلوب'),
            'code.string' => __('رمز التحقق يجب أن يكون نصاً'),
            'code.size' => __('رمز التحقق يجب أن يكون مكوناً من 6 أرقام'),
        ];
    }
}
