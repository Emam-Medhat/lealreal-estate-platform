<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization usually checked in controller via Policy, but we can verify ownership here if needed
        return $this->user()->can('update', $this->route('company'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:companies,email,' . $this->route('company')->id,
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'type' => 'sometimes|string|in:agency,developer,property_management,investment,construction,architecture,legal,mortgage,insurance,inspection,other',
            'registration_number' => 'nullable|string|max:50',
            'tax_id' => 'nullable|string|max:50',
            'status' => 'sometimes|string|in:pending,active,suspended,inactive', // Restricted usually
            'description' => 'nullable|string',
            'founded_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'logo' => 'nullable|image|max:2048',
        ];
    }
}
