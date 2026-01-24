<?php

namespace App\Http\Requests\Developer;

use Illuminate\Foundation\Http\FormRequest;

class StoreFinancingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => 'required|exists:developer_projects,id',
            'loan_number' => 'required|string|max:100',
            'lender_name' => 'required|string|max:255',
            'lender_type' => 'required|string|max:100',
            'description' => 'nullable|string|max:2000',
            'financing_type' => 'required|string|max:100',
            'loan_amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'loan_term_years' => 'required|integer|min:1|max:50',
            'application_date' => 'required|date',
            'approval_date' => 'nullable|date|after_or_equal:application_date',
            'disbursement_date' => 'nullable|date|after_or_equal:approval_date',
            'first_payment_date' => 'nullable|date|after:disbursement_date',
            'maturity_date' => 'nullable|date|after:first_payment_date',
            'status' => 'nullable|in:pending,approved,disbursed,active,paid_off,defaulted,cancelled',
            'collateral_details' => 'nullable|array',
            'guarantees' => 'nullable|array',
            'payment_schedule' => 'nullable|array',
            'fees_and_charges' => 'nullable|array',
            'conditions' => 'nullable|array',
            'covenants' => 'nullable|array',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'loan_agreement' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'supporting_documents' => 'nullable|array',
            'supporting_documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'Project is required.',
            'project_id.exists' => 'Selected project does not exist.',
            'loan_number.required' => 'Loan number is required.',
            'lender_name.required' => 'Lender name is required.',
            'lender_type.required' => 'Lender type is required.',
            'financing_type.required' => 'Financing type is required.',
            'loan_amount.required' => 'Loan amount is required.',
            'interest_rate.required' => 'Interest rate is required.',
            'loan_term_years.required' => 'Loan term is required.',
            'application_date.required' => 'Application date is required.',
            'contact_person.required' => 'Contact person is required.',
            'contact_email.required' => 'Contact email is required.',
            'contact_email.email' => 'Please provide a valid email address.',
            'contact_phone.required' => 'Contact phone is required.',
            'loan_agreement.mimes' => 'Loan agreement must be PDF, DOC, or DOCX file.',
            'loan_agreement.max' => 'Loan agreement size cannot exceed 10MB.',
        ];
    }
}
