<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'license_number' => 'required|string|max:100',
            'type' => 'required|string|max:100',
            'issuing_authority' => 'required|string|max:255',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'issued_date' => 'required|date|before_or_equal:expiry_date',
            'expiry_date' => 'required|date|after:issued_date',
            'status' => 'required|in:active,expired,suspended,revoked,renewal_pending',
            'restrictions' => 'nullable|array',
            'restrictions.*' => 'string|max:255',
            'endorsements' => 'nullable|array',
            'endorsements.*' => 'string|max:255',
            'specializations' => 'nullable|array',
            'specializations.*' => 'string|max:255',
            'notes' => 'nullable|string|max:1000',
            'verification_code' => 'nullable|string|max:100',
            'verification_url' => 'nullable|url|max:500',
            'license_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'verification_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'license_number.required' => 'License number is required.',
            'type.required' => 'License type is required.',
            'issuing_authority.required' => 'Issuing authority is required.',
            'state.required' => 'State is required.',
            'country.required' => 'Country is required.',
            'issued_date.required' => 'Issue date is required.',
            'issued_date.before_or_equal' => 'Issue date must be before or equal to expiry date.',
            'expiry_date.required' => 'Expiry date is required.',
            'expiry_date.after' => 'Expiry date must be after issue date.',
            'status.required' => 'License status is required.',
            'license_document.mimes' => 'License document must be a PDF, JPG, JPEG, or PNG file.',
            'license_document.max' => 'License document size cannot exceed 5MB.',
            'verification_document.mimes' => 'Verification document must be a PDF, JPG, JPEG, or PNG file.',
            'verification_document.max' => 'Verification document size cannot exceed 5MB.',
            'verification_url.url' => 'Please provide a valid verification URL.',
        ];
    }
}
