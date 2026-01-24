<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:sale,rental,referral,bonus',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'description' => 'required|string|max:1000',
            'commission_date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Commission amount is required.',
            'amount.numeric' => 'Commission amount must be a number.',
            'amount.min' => 'Commission amount cannot be negative.',
            'type.required' => 'Commission type is required.',
            'percentage.numeric' => 'Percentage must be a number.',
            'percentage.min' => 'Percentage cannot be negative.',
            'percentage.max' => 'Percentage cannot exceed 100%.',
            'description.required' => 'Description is required.',
            'commission_date.required' => 'Commission date is required.',
        ];
    }
}
