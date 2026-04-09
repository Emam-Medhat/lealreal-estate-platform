<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'reason' => 'required|string|in:too_expensive,missing_features,found_alternative,technical_issues,no_longer_needed,business_closed,temporary_pause,other',
            'cancellation_type' => 'required|in:immediate,end_of_period',
            'effective_date' => 'nullable|date|after:today',
            'custom_date' => 'nullable|date|after:today|required_if:cancellation_type,custom',
            'process_refund' => 'boolean',
            'refund_method' => 'required_if:process_refund,true|in:original_payment,bank_transfer,credit',
            'feedback' => 'nullable|string|max:1000',
            'would_recommend' => 'nullable|boolean',
            'alternative_solution' => 'nullable|string|max:500'
        ];
    }

    public function messages()
    {
        return [
            'reason.required' => 'Please select a reason for cancellation.',
            'reason.in' => 'Invalid reason selected.',
            'cancellation_type.required' => 'Please select when to cancel.',
            'effective_date.after' => 'Effective date must be in the future.',
            'custom_date.required_if' => 'Please specify a custom date.',
            'custom_date.after' => 'Custom date must be in the future.',
            'refund_method.required_if' => 'Please select a refund method.',
            'feedback.max' => 'Feedback must not exceed 1000 characters.',
            'alternative_solution.max' => 'Alternative solution must not exceed 500 characters.'
        ];
    }
}
