<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class BiometricAuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'biometric_type' => ['required', 'string', 'in:fingerprint,face,voice,iris'],
            'device_name' => ['required', 'string', 'max:255'],
            'biometric_data' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'biometric_type.required' => __('نوع المصادقة البيومترية مطلوب'),
            'biometric_type.string' => __('نوع المصادقة البيومترية يجب أن يكون نصاً'),
            'biometric_type.in' => __('نوع المصادقة البيومترية غير صالح'),
            'device_name.required' => __('اسم الجهاز مطلوب'),
            'device_name.string' => __('اسم الجهاز يجب أن يكون نصاً'),
            'device_name.max' => __('اسم الجهاز يجب ألا يتجاوز 255 حرفاً'),
            'biometric_data.required' => __('البيانات البيومترية مطلوبة'),
            'biometric_data.string' => __('البيانات البيومترية يجب أن تكون نصاً'),
        ];
    }
}
