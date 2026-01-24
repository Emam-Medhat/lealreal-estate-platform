<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'issuing_organization' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:100',
            'validity_period_years' => 'nullable|integer|min:1|max:50',
            'is_required' => 'nullable|boolean',
            'certificate_number' => 'required|string|max:100',
            'issued_date' => 'required|date|before_or_equal:expiry_date',
            'expiry_date' => 'required|date|after:issued_date',
            'certificate_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Certification name is required.',
            'issuing_organization.required' => 'Issuing organization is required.',
            'certificate_number.required' => 'Certificate number is required.',
            'issued_date.required' => 'Issue date is required.',
            'issued_date.before_or_equal' => 'Issue date must be before or equal to expiry date.',
            'expiry_date.required' => 'Expiry date is required.',
            'expiry_date.after' => 'Expiry date must be after issue date.',
            'validity_period_years.integer' => 'Validity period must be a number.',
            'validity_period_years.min' => 'Validity period must be at least 1 year.',
            'validity_period_years.max' => 'Validity period cannot exceed 50 years.',
            'certificate_document.mimes' => 'Certificate document must be a PDF, JPG, JPEG, or PNG file.',
            'certificate_document.max' => 'Certificate document size cannot exceed 5MB.',
        ];
    }
}
