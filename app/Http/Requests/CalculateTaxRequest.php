<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CalculateTaxRequest extends FormRequest
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
            'property_value' => 'required|numeric|min:0',
            'property_type' => 'required|string|in:residential,commercial,industrial,agricultural',
            'location' => 'required|string|in:riyadh,jeddah,dammam,mecca,medina',
            'ownership_type' => 'required|string|in:primary_residence,investment,rental',
            'is_senior_citizen' => 'sometimes|boolean',
            'is_disabled' => 'sometimes|boolean',
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
            'property_value.required' => 'قيمة العقار مطلوبة',
            'property_value.numeric' => 'قيمة العقار يجب أن تكون رقماً',
            'property_value.min' => 'قيمة العقار يجب أن تكون 0 أو أكثر',
            'property_type.required' => 'نوع العقار مطلوب',
            'property_type.in' => 'نوع العقار يجب أن يكون من القيم المحددة',
            'location.required' => 'الموقع مطلوب',
            'location.in' => 'الموقع يجب أن يكون من المدن المحددة',
            'ownership_type.required' => 'نوع الملكية مطلوب',
            'ownership_type.in' => 'نوع الملكية يجب أن يكون من القيم المحددة',
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
            'property_value' => 'قيمة العقار',
            'property_type' => 'نوع العقار',
            'location' => 'الموقع',
            'ownership_type' => 'نوع الملكية',
            'is_senior_citizen' => 'مواطن كبير السن',
            'is_disabled' => 'شخص من ذوي الاحتياجات الخاصة',
        ];
    }
}
