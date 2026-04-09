<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class AddPaymentMethodRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'type' => 'required|in:card,bank,crypto,digital_wallet',
            'provider' => 'required|string|max:100',
            'nickname' => 'required|string|max:100',
            'is_default' => 'boolean',
            'metadata' => 'nullable|array',
        ];

        // Card-specific rules
        if ($this->type === 'card') {
            $rules = array_merge($rules, [
                'card_number' => 'required|string|digits_between:13,19',
                'card_expiry_month' => 'required|integer|between:1,12',
                'card_expiry_year' => 'required|integer|between:' . date('Y') . ',' . (date('Y') + 20),
                'card_cvv' => 'required|string|digits:3',
                'card_holder' => 'required|string|max:255',
                'billing_address' => 'required|array',
                'billing_address.street' => 'required|string|max:255',
                'billing_address.city' => 'required|string|max:100',
                'billing_address.state' => 'required|string|max:100',
                'billing_address.postal_code' => 'required|string|max:20',
                'billing_address.country' => 'required|string|size:2',
            ]);
        }

        // Bank account rules
        if ($this->type === 'bank') {
            $rules = array_merge($rules, [
                'bank_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:255',
                'routing_number' => 'required|string|max:255',
                'account_type' => 'required|in:checking,savings',
                'account_holder' => 'required|string|max:255',
                'bank_address' => 'required|array',
                'bank_address.street' => 'required|string|max:255',
                'bank_address.city' => 'required|string|max:100',
                'bank_address.state' => 'required|string|max:100',
                'bank_address.postal_code' => 'required|string|max:20',
                'bank_address.country' => 'required|string|size:2',
            ]);
        }

        // Crypto wallet rules
        if ($this->type === 'crypto') {
            $rules = array_merge($rules, [
                'wallet_address' => 'required|string|max:255',
                'wallet_network' => 'required|in:ethereum,polygon,binance,avalanche,solana,bitcoin',
                'wallet_type' => 'required|in:hot,cold,hardware,exchange',
                'private_key' => 'nullable|string|max:1000',
                'mnemonic' => 'nullable|string|max:1000',
            ]);
        }

        // Digital wallet rules
        if ($this->type === 'digital_wallet') {
            $rules = array_merge($rules, [
                'wallet_provider' => 'required|string|max:100',
                'wallet_email' => 'required|email|max:255',
                'wallet_phone' => 'nullable|string|max:20',
                'wallet_id' => 'required|string|max:255',
            ]);
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'type.in' => 'Payment method type must be one of: card, bank, crypto, digital_wallet',
            'card_number.digits_between' => 'Card number must be between 13 and 19 digits',
            'card_expiry_month.between' => 'Expiry month must be between 1 and 12',
            'card_expiry_year.between' => 'Expiry year must be current year or future',
            'card_cvv.digits' => 'CVV must be exactly 3 digits',
            'account_type.in' => 'Account type must be either checking or savings',
            'wallet_network.in' => 'Wallet network must be one of: ethereum, polygon, binance, avalanche, solana, bitcoin',
            'wallet_type.in' => 'Wallet type must be one of: hot, cold, hardware, exchange',
            'billing_address.required' => 'Billing address is required',
            'bank_address.required' => 'Bank address is required',
        ];
    }

    protected function prepareForValidation()
    {
        // Sanitize card number (remove spaces and dashes)
        if ($this->has('card_number')) {
            $this->merge([
                'card_number' => preg_replace('/[\s-]/', '', $this->card_number),
            ]);
        }

        // Sanitize account number
        if ($this->has('account_number')) {
            $this->merge([
                'account_number' => preg_replace('/[\s-]/', '', $this->account_number),
            ]);
        }

        // Sanitize wallet address
        if ($this->has('wallet_address')) {
            $this->merge([
                'wallet_address' => trim($this->wallet_address),
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate card expiry
            if ($this->type === 'card' && $this->card_expiry_month && $this->card_expiry_year) {
                $expiryDate = \Carbon\Carbon::createFromDate($this->card_expiry_year, $this->card_expiry_month, 1)->endOfMonth();
                if ($expiryDate->isPast()) {
                    $validator->errors()->add('card_expiry', 'Card has expired');
                }
            }

            // Validate card number using Luhn algorithm
            if ($this->type === 'card' && $this->card_number) {
                if (!$this->isValidCardNumber($this->card_number)) {
                    $validator->errors()->add('card_number', 'Invalid card number');
                }
            }

            // Validate wallet address format
            if ($this->type === 'crypto' && $this->wallet_address && $this->wallet_network) {
                if (!$this->isValidWalletAddress($this->wallet_address, $this->wallet_network)) {
                    $validator->errors()->add('wallet_address', 'Invalid wallet address format for selected network');
                }
            }
        });
    }

    private function isValidCardNumber($number)
    {
        // Luhn algorithm
        $sum = 0;
        $alternate = false;
        
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $digit = intval($number[$i]);
            
            if ($alternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit = ($digit % 10) + 1;
                }
            }
            
            $sum += $digit;
            $alternate = !$alternate;
        }
        
        return ($sum % 10) == 0;
    }

    private function isValidWalletAddress($address, $network)
    {
        $patterns = [
            'ethereum' => '/^0x[a-fA-F0-9]{40}$/',
            'polygon' => '/^0x[a-fA-F0-9]{40}$/',
            'binance' => '/^0x[a-fA-F0-9]{40}$/',
            'avalanche' => '/^0x[a-fA-F0-9]{40}$/',
            'solana' => '/^[1-9A-HJ-NP-Za-km-z]{32,44}$/',
            'bitcoin' => '/^[13][a-km-zA-HJ-NP-Z1-9]{25,34}$|^bc1[a-z0-9]{39,59}$/',
        ];

        return preg_match($patterns[$network] ?? '/^.+$/', $address);
    }
}
