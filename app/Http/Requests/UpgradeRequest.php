<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpgradeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'plan_id' => 'required|exists:subscription_plans,id',
            'reason' => 'nullable|string|max:500',
            'payment_method' => 'required|string|in:stripe,paypal,bank_transfer'
        ];
    }

    public function messages()
    {
        return [
            'plan_id.required' => 'Please select a plan to upgrade to.',
            'plan_id.exists' => 'The selected plan is not available.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected.'
        ];
    }
}
