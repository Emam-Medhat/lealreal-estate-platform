<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClaimRewardRequest extends FormRequest
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
            'reward_id' => 'required|exists:property_rewards,id',
            'notes' => 'nullable|string|max:500',
            'delivery_address' => 'nullable|string|max:255',
            'delivery_instructions' => 'nullable|string|max:1000',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reward_id.required' => 'حقل المكافأة مطلوب',
            'reward_id.exists' => 'المكافأة المحددة غير موجودة',
            'notes.max' => 'الحد الأقصى للملاحظات هو 500 حرف',
            'delivery_address.max' => 'الحد الأقصى لعنوان التسليم هو 255 حرف',
            'delivery_instructions.max' => 'الحد الأقصى لتعليمات التسليم هو 1000 حرف',
            'contact_phone.max' => 'الحد الأقصى لرقم الهاتف هو 20 حرف',
            'contact_email.email' => 'يجب إدخال بريد إلكتروني صالح',
            'contact_email.max' => 'الحد الأقصى للبريد الإلكتروني هو 255 حرف',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'reward_id' => 'المكافأة',
            'notes' => 'الملاحظات',
            'delivery_address' => 'عنوان التسليم',
            'delivery_instructions' => 'تعليمات التسليم',
            'contact_phone' => 'رقم الهاتف',
            'contact_email' => 'البريد الإلكتروني',
        ];
    }
}
