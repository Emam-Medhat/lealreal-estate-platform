<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class RequestRefundRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'payment_id' => 'required|exists:payments,id',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'reason' => 'required|string|max:500',
            'refund_method' => 'required|string|max:100',
            'type' => 'required|in:full,partial,dispute',
            'evidence' => 'nullable|array|max:10',
            'evidence.*.type' => 'required_with:evidence|string|in:photo,video,document,screenshot,email',
            'evidence.*.description' => 'required_with:evidence|string|max:500',
            'evidence.*.file' => 'required_with:evidence|file|mimes:jpg,jpeg,png,pdf,doc,docx,mp4,mov|max:10240',
            'refund_to' => 'required|in:original_method,alternative_method',
            'alternative_method' => 'required_if:refund_to,alternative_method|array',
            'alternative_method.type' => 'required_if:refund_to,alternative_method|string|in:card,bank,crypto',
            'alternative_method.card_number' => 'required_if:alternative_method.type,card|string|digits_between:13,19',
            'alternative_method.bank_account' => 'required_if:alternative_method.type,bank|string|max:255',
            'alternative_method.wallet_address' => 'required_if:alternative_method.type,crypto|string|max:255',
            'alternative_method.wallet_network' => 'required_if:alternative_method.type,crypto|string|in:ethereum,polygon,binance,avalanche,solana,bitcoin',
            'refund_address' => 'nullable|array',
            'refund_address.street' => 'required_with:refund_address|string|max:255',
            'refund_address.city' => 'required_with:refund_address|string|max:100',
            'refund_address.state' => 'required_with:refund_address|string|max:100',
            'refund_address.postal_code' => 'required_with:refund_address|string|max:20',
            'refund_address.country' => 'required_with:refund_address|string|size:2',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'urgent' => 'boolean',
            'dispute_details' => 'required_if:type,dispute|array',
            'dispute_details.category' => 'required_if:type,dispute|string|in:product_not_received,product_different,product_damaged,service_not_provided,unauthorized_charge,fraud,other',
            'dispute_details.description' => 'required_if:type,dispute|string|max:1000',
            'dispute_details.resolution_requested' => 'required_if:type,dispute|string|in:full_refund,partial_refund,exchange,repair',
            'dispute_details.timeline' => 'nullable|array',
            'dispute_details.timeline.*.date' => 'required_with:dispute_details.timeline|date',
            'dispute_details.timeline.*.event' => 'required_with:dispute_details.timeline|string|max:500',
            'dispute_details.timeline.*.description' => 'required_with:dispute_details.timeline|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'payment_id.required' => 'Payment ID is required',
            'payment_id.exists' => 'Selected payment does not exist',
            'amount.min' => 'Refund amount must be at least 0.01',
            'amount.max' => 'Refund amount cannot exceed 999,999.99',
            'reason.required' => 'Refund reason is required',
            'reason.max' => 'Refund reason cannot exceed 500 characters',
            'type.in' => 'Refund type must be one of: full, partial, dispute',
            'refund_method.required' => 'Refund method is required',
            'refund_to.in' => 'Refund destination must be either original method or alternative method',
            'contact_email.required' => 'Contact email is required',
            'contact_email.email' => 'Please provide a valid email address',
            'evidence.*.file.mimes' => 'Evidence files must be images, documents, or videos',
            'evidence.*.file.max' => 'Evidence files cannot exceed 10MB',
            'dispute_details.category.in' => 'Dispute category is invalid',
            'dispute_details.resolution_requested.in' => 'Resolution requested is invalid',
            'alternative_method.type.in' => 'Alternative method type must be card, bank, or crypto',
            'alternative_method.wallet_network.in' => 'Wallet network must be one of: ethereum, polygon, binance, avalanche, solana, bitcoin',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'amount' => number_format($this->amount, 2, '.', ''),
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if refund amount exceeds payment amount
            if ($this->payment_id && $this->amount) {
                $payment = \App\Models\Payment::find($this->payment_id);
                if ($payment) {
                    $totalRefunded = $payment->refunds()->where('status', 'completed')->sum('amount');
                    $availableRefund = $payment->amount - $totalRefunded;
                    
                    if ($this->amount > $availableRefund) {
                        $validator->errors()->add('amount', 'Refund amount cannot exceed available refund amount of ' . number_format($availableRefund, 2));
                    }
                }
            }

            // Validate alternative card number
            if ($this->refund_to === 'alternative_method' && 
                $this->alternative_method && 
                $this->alternative_method['type'] === 'card' && 
                $this->alternative_method['card_number']) {
                
                if (!$this->isValidCardNumber($this->alternative_method['card_number'])) {
                    $validator->errors()->add('alternative_method.card_number', 'Invalid card number');
                }
            }

            // Validate alternative wallet address
            if ($this->refund_to === 'alternative_method' && 
                $this->alternative_method && 
                $this->alternative_method['type'] === 'crypto' && 
                $this->alternative_method['wallet_address'] && 
                $this->alternative_method['wallet_network']) {
                
                if (!$this->isValidWalletAddress($this->alternative_method['wallet_address'], $this->alternative_method['wallet_network'])) {
                    $validator->errors()->add('alternative_method.wallet_address', 'Invalid wallet address format for selected network');
                }
            }

            // Check for duplicate refund requests
            if ($this->payment_id) {
                $existingRefund = \App\Models\Refund::where('payment_id', $this->payment_id)
                    ->where('status', 'pending')
                    ->first();
                
                if ($existingRefund) {
                    $validator->errors()->add('payment_id', 'A refund request for this payment is already pending');
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
