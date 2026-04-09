<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MintNftRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'image_url' => 'nullable|url',
            'animation_url' => 'nullable|url',
            'external_url' => 'nullable|url',
            'contract_address' => 'required|string|size:42',
            'category' => 'required|string|in:art,collectible,music,photography,gaming,sports,utility,metaverse',
            'attributes' => 'nullable|array',
            'properties' => 'nullable|array',
            'supply' => 'required|integer|min:1|max:10000',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'required|string|in:ETH,BNB,USDT,USDC',
            'royalty_percentage' => 'nullable|numeric|min:0|max:10',
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
            'name.required' => 'اسم NFT مطلوب',
            'name.max' => 'اسم NFT يجب ألا يزيد عن 255 حرف',
            'description.max' => 'الوصف يجب ألا يزيد عن 2000 حرف',
            'contract_address.required' => 'عنوان العقد الذكي مطلوب',
            'contract_address.size' => 'عنوان العقد يجب أن يكون 42 حرف',
            'category.required' => 'فئة NFT مطلوبة',
            'category.in' => 'فئة NFT غير صالحة',
            'supply.required' => 'الكمية مطلوبة',
            'supply.min' => 'الكمية يجب أن تكون على الأقل 1',
            'supply.max' => 'الكمية يجب ألا تزيد عن 10000',
            'price.min' => 'السعر يجب أن يكون رقم موجب',
            'currency.required' => 'العملة مطلوبة',
            'currency.in' => 'العملة غير صالحة',
            'royalty_percentage.min' => 'نسبة الرسوم يجب أن تكون رقم موجب',
            'royalty_percentage.max' => 'نسبة الرسوم يجب ألا تزيد عن 10%',
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
            'name' => 'اسم NFT',
            'description' => 'الوصف',
            'image_url' => 'رابط الصورة',
            'animation_url' => 'رابط الأنيميشن',
            'external_url' => 'الرابط الخارجي',
            'contract_address' => 'عنوان العقد',
            'category' => 'الفئة',
            'attributes' => 'الخصائص',
            'properties' => 'الخصائص',
            'supply' => 'الكمية',
            'price' => 'السعر',
            'currency' => 'العملة',
            'royalty_percentage' => 'نسبة الرسوم',
            'metadata' => 'البيانات الوصفية',
        ];
    }
}
