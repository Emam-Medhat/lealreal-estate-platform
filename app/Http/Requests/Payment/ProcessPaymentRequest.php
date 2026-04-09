<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'currency' => 'required|string|size:3|in:USD,EUR,GBP,JPY,CNY,AUD,CAD',
            'description' => 'required|string|max:500',
            'metadata' => 'nullable|array',
            'gateway_id' => 'nullable|exists:payment_gateways,id',
            'save_payment_method' => 'boolean',
            'payment_method_data' => 'required_if:save_payment_method,true|array',
            'payment_method_data.type' => 'required_with:save_payment_method|string|in:card,bank,crypto',
            'payment_method_data.card_number' => 'required_if:payment_method_data.type,card|string|digits_between:13,19',
            'payment_method_data.card_expiry' => 'required_if:payment_method_data.type,card|string|regex:/^(0[1-9]|1[0-2])\/\d{2}$/',
            'payment_method_data.card_cvv' => 'required_if:payment_method_data.type,card|string|digits:3',
            'payment_method_data.card_holder' => 'required_if:payment_method_data.type,card|string|max:255',
            'payment_method_data.bank_account' => 'required_if:payment_method_data.type,bank|string|max:255',
            'payment_method_data.bank_routing' => 'required_if:payment_method_data.type,bank|string|max:255',
            'payment_method_data.wallet_address' => 'required_if:payment_method_data.type,crypto|string|max:255',
            'payment_method_data.wallet_network' => 'required_if:payment_method_data.type,crypto|string|in:ethereum,polygon,binance,avalanche,solana,bitcoin',
            'billing_address' => 'required|array',
            'billing_address.street' => 'required|string|max:255',
            'billing_address.city' => 'required|string|max:100',
            'billing_address.state' => 'required|string|max:100',
            'billing_address.postal_code' => 'required|string|max:20',
            'billing_address.country' => 'required|string|size:2',
            'customer_ip' => 'required|ip',
            'user_agent' => 'required|string|max:500',
            'reference' => 'nullable|string|max:255|unique:payments,reference',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'amount.min' => 'Payment amount must be at least 0.01',
            'amount.max' => 'Payment amount cannot exceed 999,999.99',
            'currency.in' => 'Selected currency is not supported',
            'payment_method_data.card_expiry.regex' => 'Card expiry must be in MM/YY format',
            'payment_method_data.card_number.digits_between' => 'Card number must be between 13 and 19 digits',
            'payment_method_data.card_cvv.digits' => 'CVV must be exactly 3 digits',
            'billing_address.required' => 'Billing address is required',
            'customer_ip.ip' => 'Invalid IP address format',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'amount' => number_format($this->amount, 2, '.', ''),
        ]);
    }
}
