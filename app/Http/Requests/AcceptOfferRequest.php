<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AcceptOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'acceptance_notes' => 'nullable|string|max:1000',
            'modifications' => 'nullable|array',
            'proposed_closing_date' => 'nullable|date|after:today',
            'earnest_money_required' => 'nullable|numeric|min:0',
            'additional_terms' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'acceptance_notes.max' => 'ملاحظات القبول يجب ألا تتجاوز 1000 حرف',
            'proposed_closing_date.after' => 'تاريخ الإغلاق المقترح يجب أن يكون بعد اليوم',
            'earnest_money_required.numeric' => 'العربون المطلوب يجب أن يكون رقماً',
            'earnest_money_required.min' => 'العربون المطلوب يجب أن يكون 0 أو أكثر',
            'additional_terms.max' => 'الشروط الإضافية يجب ألا تتجاوز 2000 حرف',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $offer = $this->route('offer');
            
            if ($offer) {
                // Check if user is the seller
                if ($offer->seller_id !== $this->user()->id) {
                    $validator->errors()->add('unauthorized', 'غير مصرح لك بقبول هذا العرض');
                }

                // Check if offer can be accepted
                if (!$offer->canBeAccepted()) {
                    $validator->errors()->add('status', 'العرض لا يمكن قبوله في حالته الحالية');
                }

                // Check if earnest money is reasonable
                if ($this->earnest_money_required && $this->earnest_money_required > $offer->offer_amount * 0.15) {
                    $validator->errors()->add('earnest_money_required', 'العربون المطلوب يجب ألا يتجاوز 15% من مبلغ العرض');
                }
            }
        });
    }
}
