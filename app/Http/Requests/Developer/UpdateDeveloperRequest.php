<?php

namespace App\Http\Requests\Developer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeveloperRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
            'developer_type' => 'required|in:residential,commercial,mixed_use,industrial',
            'establishment_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'ceo_name' => 'nullable|string|max:255',
            'headquarters_address' => 'required|string|max:500',
            'headquarters_city' => 'required|string|max:100',
            'headquarters_country' => 'required|string|max:100',
            'status' => 'required|in:active,inactive,suspended',
            'is_verified' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required.',
            'contact_email.required' => 'Contact email is required.',
            'contact_email.email' => 'Please provide a valid email address.',
            'contact_phone.required' => 'Contact phone is required.',
            'developer_type.required' => 'Developer type is required.',
            'headquarters_address.required' => 'Headquarters address is required.',
            'headquarters_city.required' => 'Headquarters city is required.',
            'headquarters_country.required' => 'Headquarters country is required.',
            'website.url' => 'Please provide a valid website URL.',
            'establishment_year.integer' => 'Establishment year must be a number.',
            'establishment_year.min' => 'Establishment year cannot be before 1900.',
            'establishment_year.max' => 'Establishment year cannot be in the future.',
            'status.required' => 'Status is required.',
        ];
    }
}
