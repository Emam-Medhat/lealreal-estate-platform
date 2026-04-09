<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Auth handled by middleware/controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:companies',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'type' => 'required|string|in:agency,developer,property_management,investment,construction,architecture,legal,mortgage,insurance,inspection,other',
            'registration_number' => 'nullable|string|max:50',
            'tax_id' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'founded_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'logo' => 'nullable|image|max:2048', // 2MB Max
            // Add other validated fields matching controller usage
        ];
    }
}
