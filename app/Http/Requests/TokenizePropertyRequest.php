<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TokenizePropertyRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'token_name' => 'required|string|max:255',
            'token_symbol' => 'required|string|max:10|regex:/^[A-Z0-9]+$/',
            'description' => 'nullable|string|max:2000',
            'logo_url' => 'nullable|url',
            'total_tokens' => 'required|integer|min:100|max:1000000',
            'max_tokens' => 'required|integer|min:100|max:1000000',
            'token_price' => 'required|numeric|min:1|max:1000000',
            'min_token_price' => 'nullable|numeric|min:1|max:1000000',
            'max_token_price' => 'nullable|numeric|min:1|max:1000000',
            'annual_rental_yield' => 'nullable|numeric|min:0|max:100',
            'expected_appreciation' => 'nullable|numeric|min:-100|max:100',
            'sale_start_date' => 'nullable|date|after:today',
            'sale_end_date' => 'nullable|date|after:sale_start_date',
            'tokenomics' => 'nullable|array',
            'legal_docs' => 'nullable|array',
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
            'property_id.required' => 'معرف العقار مطلوب',
            'property_id.exists' => 'العقار المحدد غير موجود',
            'token_name.required' => 'اسم الرمز مطلوب',
            'token_name.max' => 'اسم الرمز يجب ألا يزيد عن 255 حرف',
            'token_symbol.required' => 'رمز الرمز مطلوب',
            'token_symbol.max' => 'رمز الرمز يجب ألا يزيد عن 10 أحرف',
            'token_symbol.regex' => 'رمز الرمز يجب أن يحتوي على أحرف كبيرة وأرقام فقط',
            'description.max' => 'الوصف يجب ألا يزيد عن 2000 حرف',
            'total_tokens.required' => 'إجمالي الرموز مطلوب',
            'total_tokens.min' => 'إجمالي الرموز يجب أن يكون على الأقل 100',
            'total_tokens.max' => 'إجمالي الرموز يجب ألا يزيد عن 1000000',
            'max_tokens.required' => 'أقصى عدد من الرموز مطلوب',
            'max_tokens.min' => 'أقصى عدد من الرموز يجب أن يكون على الأقل 100',
            'max_tokens.max' => 'أقصى عدد من الرموز يجب ألا يزيد عن 1000000',
            'token_price.required' => 'سعر الرمز مطلوب',
            'token_price.min' => 'سعر الرمز يجب أن يكون على الأقل 1',
            'token_price.max' => 'سعر الرمز يجب ألا يزيد عن 1000000',
            'min_token_price.min' => 'أدنى سعر للرمز يجب أن يكون على الأقل 1',
            'min_token_price.max' => 'أدنى سعر للرمز يجب ألا يزيد عن 1000000',
            'max_token_price.min' => 'أقصى سعر للرمز يجب أن يكون على الأقل 1',
            'max_token_price.max' => 'أقصى سعر للرمز يجب ألا يزيد عن 1000000',
            'annual_rental_yield.min' => 'العائد الإيجاري السنوي يجب أن يكون بين 0 و 100',
            'annual_rental_yield.max' => 'العائد الإيجاري السنوي يجب أن يكون بين 0 و 100',
            'expected_appreciation.min' => 'التوقع المتوقع للنمو يجب أن يكون بين -100 و 100',
            'expected_appreciation.max' => 'التوقع المتوقع للنمو يجب أن يكون بين -100 و 100',
            'sale_start_date.after' => 'تاريخ بدء البيع يجب أن يكون بعد اليوم',
            'sale_end_date.after' => 'تاريخ انتهاء البيع يجب أن يكون بعد تاريخ البدء',
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
            'property_id' => 'معرف العقار',
            'token_name' => 'اسم الرمز',
            'token_symbol' => 'رمز الرمز',
            'description' => 'الوصف',
            'logo_url' => 'رابط الشعار',
            'total_tokens' => 'إجمالي الرموز',
            'max_tokens' => 'أقصى عدد من الرموز',
            'token_price' => 'سعر الرمز',
            'min_token_price' => 'أدنى سعر للرمز',
            'max_token_price' => 'أقصى سعر للرمز',
            'annual_rental_yield' => 'العائد الإيجاري السنوي',
            'expected_appreciation' => 'التوقع المتوقع للنمو',
            'sale_start_date' => 'تاريخ بدء البيع',
            'sale_end_date' => 'تاريخ انتهاء البيع',
            'tokenomics' => 'الاقتصصات الرمزية',
            'legal_docs' => 'المستندات القانونية',
            'metadata' => 'البيانات الوصفية',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->failed()) {
                return;
            }

            $totalTokens = $this->input('total_tokens');
            $maxTokens = $this->input('max_tokens');
            
            if ($totalTokens > $maxTokens) {
                $validator->errors()->add('total_tokens', 'إجمالي الرموز يجب ألا يتجاوز أقصى عدد الرموز');
            }

            $minPrice = $this->input('min_token_price');
            $maxPrice = $this->input('max_token_price');
            $tokenPrice = $this->input('token_price');
            
            if ($minPrice && $maxPrice && $minPrice > $maxPrice) {
                $validator->errors()->add('min_token_price', 'أدنى سعر يجب أن يكون أقل من أو يساوي أقصى سعر');
            }

            if ($tokenPrice && $minPrice && $tokenPrice < $minPrice) {
                $validator->errors()->add('token_price', 'سعر الرمز يجب أن يكون على الأقل أدنى سعر');
            }

            if ($tokenPrice && $maxPrice && $tokenPrice > $maxPrice) {
                $validator->errors()->add('token_price', 'سعر الرمز يجب ألا يتجاوز أقصى سعر');
            }

            // Calculate total value
            $totalValue = $totalTokens * $tokenPrice;
            $propertyValue = $this->getPropertyValue();
            
            if ($totalValue > $propertyValue * 1.2) {
                $validator->errors()->add('total_tokens', 'إجمالي قيمة الرموز يجب ألا يتجاوز 120% من قيمة العقار');
            }
        });
    }

    /**
     * Get property value from database.
     */
    private function getPropertyValue(): float
    {
        $propertyId = $this->input('property_id');
        $property = \App\Models\Property::find($propertyId);
        
        return $property ? $property->price : 0;
    }
}
