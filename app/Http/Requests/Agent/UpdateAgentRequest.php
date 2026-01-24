<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAgentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->agent->user_id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'license_number' => ['required', 'string', 'max:255', Rule::unique('agents', 'license_number')->ignore($this->agent->id)],
            'status' => ['required', 'in:active,inactive,suspended'],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            
            // Profile fields
            'bio' => ['nullable', 'string', 'max:2000'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:50'],
            'specializations' => ['nullable', 'array'],
            'specializations.*' => ['string', 'max:100'],
            'languages' => ['nullable', 'array'],
            'languages.*' => ['string', 'max:50'],
            'service_areas' => ['nullable', 'array'],
            'service_areas.*' => ['string', 'max:100'],
            'achievements' => ['nullable', 'array'],
            'achievements.*' => ['string', 'max:255'],
            'education' => ['nullable', 'array'],
            'education.*' => ['string', 'max:255'],
            'certifications' => ['nullable', 'array'],
            'certifications.*' => ['string', 'max:255'],
            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['url', 'max:255'],
            'office_address' => ['nullable', 'string', 'max:500'],
            'office_phone' => ['nullable', 'string', 'max:20'],
            'working_hours' => ['nullable', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The agent name is required.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'license_number.required' => 'The license number is required.',
            'license_number.unique' => 'This license number is already registered.',
            'commission_rate.numeric' => 'The commission rate must be a number.',
            'commission_rate.min' => 'The commission rate cannot be negative.',
            'commission_rate.max' => 'The commission rate cannot exceed 100%.',
            'experience_years.integer' => 'Experience years must be a whole number.',
            'experience_years.min' => 'Experience years cannot be negative.',
            'experience_years.max' => 'Experience years seem unrealistic.',
            'profile_photo.image' => 'The profile photo must be an image file.',
            'profile_photo.mimes' => 'The profile photo must be a JPEG, PNG, JPG, or GIF file.',
            'profile_photo.max' => 'The profile photo may not be larger than 2MB.',
        ];
    }
}
