<?php

namespace App\Http\Requests\Developer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContractorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'specialization' => 'required|string|max:100',
            'services_offered' => 'nullable|array',
            'services_offered.*' => 'string|max:255',
            'company_size' => 'nullable|string|max:50',
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'license_number' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'insurance_details' => 'nullable|array',
            'certifications' => 'nullable|array',
            'experience_years' => 'nullable|integer|min:0|max:100',
            'completed_projects' => 'nullable|array',
            'ongoing_projects' => 'nullable|array',
            'team_members' => 'nullable|array',
            'equipment_available' => 'nullable|array',
            'payment_terms' => 'nullable|string|max:500',
            'hourly_rate' => 'nullable|numeric|min:0',
            'project_rate' => 'nullable|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'status' => 'required|in:active,inactive,suspended,blacklisted',
            'notes' => 'nullable|string|max:1000',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required.',
            'contact_person.required' => 'Contact person is required.',
            'contact_email.required' => 'Contact email is required.',
            'contact_email.email' => 'Please provide a valid email address.',
            'contact_phone.required' => 'Contact phone is required.',
            'address.required' => 'Address is required.',
            'city.required' => 'City is required.',
            'country.required' => 'Country is required.',
            'specialization.required' => 'Specialization is required.',
            'status.required' => 'Contractor status is required.',
        ];
    }
}
