<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceBidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:1000',
            'is_automatic' => 'boolean',
            'max_automatic_bid' => 'nullable|required_if:is_automatic,true|numeric|min:amount',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'مبلغ المزايدة مطلوب',
            'amount.numeric' => 'مبلغ المزايدة يجب أن يكون رقماً',
            'amount.min' => 'مبلغ المزايدة يجب أن يكون أكبر من 0',
            'notes.max' => 'ملاحظات المزايدة يجب ألا تتجاوز 1000 حرف',
            'max_automatic_bid.required_if' => 'الحد الأقصى للمزايدة التلقائية مطلوب عند تفعيل المزايدة التلقائية',
            'max_automatic_bid.numeric' => 'الحد الأقصى للمزايدة التلقائية يجب أن يكون رقماً',
            'max_automatic_bid.min' => 'الحد الأقصى للمزايدة التلقائية يجب أن يكون أكبر من أو يساوي مبلغ المزايدة الحالي',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $auction = $this->route('auction');
            
            if ($auction) {
                // Check if auction is active
                if (!$auction->isActive()) {
                    $validator->errors()->add('amount', 'المزاد غير نشط حالياً');
                }

                // Check if bid meets minimum requirements
                if ($this->amount <= $auction->current_bid) {
                    $validator->errors()->add('amount', 'المبلغ يجب أن يكون أكبر من المزايدة الحالية');
                }

                // Check bid increment
                $minBid = $auction->current_bid + $auction->bid_increment;
                if ($this->amount < $minBid) {
                    $validator->errors()->add('amount', "المبلغ يجب أن يكون على الأقل {$minBid}");
                }

                // Check if user is verified (if required)
                if ($auction->requires_verification && !$this->user()->isVerified()) {
                    $validator->errors()->add('amount', 'يجب التحقق من حسابك للمشاركة في هذا المزاد');
                }
            }
        });
    }
}
