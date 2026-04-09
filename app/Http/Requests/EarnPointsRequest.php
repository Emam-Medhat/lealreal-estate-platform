<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EarnPointsRequest extends FormRequest
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
            'points' => 'required|integer|min:1|max:10000',
            'type' => 'required|in:earned,bonus,penalty',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'property_id' => 'nullable|exists:properties,id',
            'expires_at' => 'nullable|date|after:now',
            'metadata' => 'nullable|array',
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
            'points.required' => 'حقل النقاط مطلوب',
            'points.integer' => 'يجب أن يكون حقل النقاط رقماً',
            'points.min' => 'يجب أن تكون النقاط على الأقل 1',
            'points.max' => 'الحد الأقصى للنقاط هو 10000',
            'type.required' => 'حقل النوع مطلوب',
            'type.in' => 'نوع النقاط غير صالح',
            'reason.required' => 'حقل السبب مطلوب',
            'reason.max' => 'الحد الأقصى للسبب هو 255 حرف',
            'description.max' => 'الحد الأقصى للوصف هو 1000 حرف',
            'property_id.exists' => 'العقار المحدد غير موجود',
            'expires_at.after' => 'يجب أن يكون تاريخ الانتهاء في المستقبل',
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
            'points' => 'النقاط',
            'type' => 'النوع',
            'reason' => 'السبب',
            'description' => 'الوصف',
            'property_id' => 'العقار',
            'expires_at' => 'تاريخ الانتهاء',
            'metadata' => 'البيانات الإضافية',
        ];
    }
}
