<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAgentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'agency_id' => 'nullable|exists:agencies,id',
            'license_number' => 'required|string|max:50|unique:agents,license_number',
            'specialization' => 'nullable|string|max:100',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'rating' => 'nullable|numeric|min:0|max:5',
            'total_sales' => 'nullable|numeric|min:0',
            'total_properties' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive,suspended',
            'is_verified' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'territory_id' => 'nullable|exists:territories,id',
            'join_date' => 'nullable|date',
            'verified_at' => 'nullable|date',
            'suspended_at' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User is required.',
            'user_id.exists' => 'Selected user does not exist.',
            'license_number.required' => 'License number is required.',
            'license_number.unique' => 'License number already exists.',
            'experience_years.integer' => 'Experience years must be a number.',
            'experience_years.min' => 'Experience years cannot be negative.',
            'experience_years.max' => 'Experience years cannot exceed 50.',
            'rating.numeric' => 'Rating must be a number.',
            'rating.min' => 'Rating cannot be negative.',
            'rating.max' => 'Rating cannot exceed 5.',
            'commission_rate.numeric' => 'Commission rate must be a number.',
            'commission_rate.min' => 'Commission rate cannot be negative.',
            'commission_rate.max' => 'Commission rate cannot exceed 100%.',
        ];
    }
}
