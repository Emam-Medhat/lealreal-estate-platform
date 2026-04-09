<?php

namespace App\Http\Requests\Developer;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeveloperRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'commercial_register' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
            'developer_type' => 'required|in:residential,commercial,mixed,industrial',
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'address' => 'required|string|max:500',
            'status' => 'nullable|in:active,inactive,suspended',
            'is_verified' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required.',
            'email.required' => 'Contact email is required.',
            'email.email' => 'Please provide a valid email address.',
            'phone.required' => 'Contact phone is required.',
            'developer_type.required' => 'Developer type is required.',
            'address.required' => 'Address is required.',
            'website.url' => 'Please provide a valid website URL.',
            'established_year.integer' => 'Establishment year must be a number.',
            'established_year.min' => 'Establishment year cannot be before 1900.',
            'established_year.max' => 'Establishment year cannot be in the future.',
        ];
    }
}
