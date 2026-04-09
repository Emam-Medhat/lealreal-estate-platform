<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'offer_amount' => 'required|numeric|min:0',
            'offer_type' => ['required', Rule::in(['purchase', 'rent', 'lease_option'])],
            'message' => 'nullable|string|max:2000',
            'offer_terms' => 'nullable|array',
            'contingencies' => 'nullable|array',
            'offer_expiration_date' => 'required|date|after:today',
            'earnest_money' => 'nullable|numeric|min:0',
            'proposed_closing_date' => 'nullable|date|after:today',
            'financing_type' => ['nullable', Rule::in(['cash', 'conventional', 'fha', 'va', 'other'])],
            'is_contingent' => 'boolean',
            'contingency_details' => 'nullable|array',
            'buyer_notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'property_id.required' => 'العقار مطلوب',
            'property_id.exists' => 'العقار المحدد غير موجود',
            'offer_amount.required' => 'مبلغ العرض مطلوب',
            'offer_amount.numeric' => 'مبلغ العرض يجب أن يكون رقماً',
            'offer_amount.min' => 'مبلغ العرض يجب أن يكون 0 أو أكثر',
            'offer_type.required' => 'نوع العرض مطلوب',
            'offer_type.in' => 'نوع العرض غير صالح',
            'message.max' => 'رسالة العرض يجب ألا تتجاوز 2000 حرف',
            'offer_expiration_date.required' => 'تاريخ انتهاء العرض مطلوب',
            'offer_expiration_date.after' => 'تاريخ انتهاء العرض يجب أن يكون بعد اليوم',
            'earnest_money.numeric' => 'العربون يجب أن يكون رقماً',
            'earnest_money.min' => 'العربون يجب أن يكون 0 أو أكثر',
            'proposed_closing_date.after' => 'تاريخ الإغلاق المقترح يجب أن يكون بعد اليوم',
            'financing_type.in' => 'نوع التمويل غير صالح',
            'buyer_notes.max' => 'ملاحظات المشتري يجب ألا تتجاوز 1000 حرف',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $property = \App\Models\Property::find($this->property_id);
            
            if ($property) {
                // Check if offer amount is reasonable
                if ($this->offer_amount < $property->price * 0.1) {
                    $validator->errors()->add('offer_amount', 'مبلغ العرض منخفض جداً');
                }

                // Check if earnest money is reasonable
                if ($this->earnest_money && $this->earnest_money > $this->offer_amount * 0.1) {
                    $validator->errors()->add('earnest_money', 'العربون يجب ألا يتجاوز 10% من مبلغ العرض');
                }

                // Check if property is available for offers
                if (!$property->isAvailableForOffer()) {
                    $validator->errors()->add('property_id', 'العقار غير متاح للعروض حالياً');
                }
            }

            // Check if user already has active offer for this property
            $existingOffer = \App\Models\Offer::where('property_id', $this->property_id)
                ->where('buyer_id', $this->user()->id)
                ->whereIn('status', ['submitted', 'under_review'])
                ->first();

            if ($existingOffer) {
                $validator->errors()->add('property_id', 'لديك عرض نشط بالفعل لهذا العقار');
            }
        });
    }
}
