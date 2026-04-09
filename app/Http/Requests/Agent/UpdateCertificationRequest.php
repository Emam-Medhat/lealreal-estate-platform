<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCertificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'certificate_number' => 'required|string|max:100',
            'issued_date' => 'required|date|before_or_equal:expiry_date',
            'expiry_date' => 'required|date|after:issued_date',
            'status' => 'required|in:active,expired,suspended,renewal_pending',
            'certificate_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'certificate_number.required' => 'Certificate number is required.',
            'issued_date.required' => 'Issue date is required.',
            'issued_date.before_or_equal' => 'Issue date must be before or equal to expiry date.',
            'expiry_date.required' => 'Expiry date is required.',
            'expiry_date.after' => 'Expiry date must be after issue date.',
            'status.required' => 'Status is required.',
            'certificate_document.mimes' => 'Certificate document must be a PDF, JPG, JPEG, or PNG file.',
            'certificate_document.max' => 'Certificate document size cannot exceed 5MB.',
        ];
    }
}
