<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => 'nullable|exists:properties,id',
            'source_id' => 'nullable|exists:lead_sources,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'status' => 'nullable|in:new,contacted,qualified,converted,closed,lost',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gt:budget_min',
            'preferred_areas' => 'nullable|array',
            'preferred_areas.*' => 'string|max:255',
            'preferred_property_types' => 'nullable|array',
            'preferred_property_types.*' => 'string|max:100',
            'message' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:1000',
            'next_follow_up' => 'nullable|date|after:now',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Lead name is required.',
            'phone.required' => 'Phone number is required.',
            'email.email' => 'Please provide a valid email address.',
            'budget_max.gt' => 'Maximum budget must be greater than minimum budget.',
            'next_follow_up.after' => 'Follow-up date must be in the future.',
        ];
    }
}
