<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'client_type' => 'required|in:individual,company,investor',
            'status' => 'nullable|in:active,inactive,prospect,closed,lost',
            'source' => 'nullable|string|max:100',
            'referral_source' => 'nullable|string|max:255',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gt:budget_min',
            'preferred_areas' => 'nullable|array',
            'preferred_areas.*' => 'string|max:255',
            'preferred_property_types' => 'nullable|array',
            'preferred_property_types.*' => 'string|max:100',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string|max:255',
            'timeline' => 'nullable|string|max:100',
            'financing_status' => 'nullable|string|max:100',
            'pre_approved_amount' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'custom_fields' => 'nullable|array',
            'next_follow_up' => 'nullable|date|after:now',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Client name is required.',
            'phone.required' => 'Phone number is required.',
            'client_type.required' => 'Client type is required.',
            'email.email' => 'Please provide a valid email address.',
            'budget_max.gt' => 'Maximum budget must be greater than minimum budget.',
            'next_follow_up.after' => 'Follow-up date must be in the future.',
        ];
    }
}
