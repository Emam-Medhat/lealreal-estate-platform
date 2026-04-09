<?php

namespace App\Http\Requests\Developer;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => 'required|exists:developer_projects,id',
            'permit_number' => 'required|string|max:100',
            'permit_type' => 'required|string|max:100',
            'description' => 'nullable|string|max:2000',
            'issuing_authority' => 'required|string|max:255',
            'application_date' => 'required|date',
            'issue_date' => 'nullable|date|after_or_equal:application_date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'status' => 'required|in:pending,approved,issued,rejected,expired,renewed',
            'priority_level' => 'nullable|in:low,medium,high,critical',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'validity_period' => 'nullable|string|max:100',
            'renewal_required' => 'nullable|boolean',
            'conditions' => 'nullable|array',
            'requirements' => 'nullable|array',
            'inspections_required' => 'nullable|array',
            'approvals_needed' => 'nullable|array',
            'contact_person' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'notes' => 'nullable|string|max:1000',
            'permit_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'supporting_documents' => 'nullable|array',
            'supporting_documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'Project is required.',
            'project_id.exists' => 'Selected project does not exist.',
            'permit_number.required' => 'Permit number is required.',
            'permit_type.required' => 'Permit type is required.',
            'issuing_authority.required' => 'Issuing authority is required.',
            'status.required' => 'Permit status is required.',
            'application_date.required' => 'Application date is required.',
            'contact_person.required' => 'Contact person is required.',
            'contact_phone.required' => 'Contact phone is required.',
            'contact_email.required' => 'Contact email is required.',
            'contact_email.email' => 'Please provide a valid email address.',
        ];
    }
}
