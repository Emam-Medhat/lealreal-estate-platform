<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAgentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // User fields
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'nullable|string|min:8',
            
            // Agent fields
            'company_id' => 'required|exists:companies,id',
            'license_number' => 'required|string|max:50|unique:agents,license_number',
            'experience_years' => 'required|integer|min:0|max:50',
            'status' => 'nullable|in:active,inactive,suspended',
            'commission_rate' => 'required|numeric|min:0|max:100',
            
            // Profile fields
            'bio' => 'nullable|string|max:1000',
            'specializations' => 'nullable|array',
            'languages' => 'nullable|array',
            'service_areas' => 'nullable|array',
            'achievements' => 'nullable|array',
            'education' => 'nullable|array',
            'certifications' => 'nullable|array',
            'social_links' => 'nullable|array',
            'office_address' => 'nullable|string|max:500',
            'office_phone' => 'nullable|string|max:20',
            'working_hours' => 'nullable|string|max:200',
            
            // File upload
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Agent name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'This phone number is already registered.',
            'company_id.required' => 'Please select a company.',
            'company_id.exists' => 'Selected company does not exist.',
            'license_number.required' => 'License number is required.',
            'license_number.unique' => 'License number already exists.',
            'experience_years.required' => 'Experience years is required.',
            'experience_years.integer' => 'Experience years must be a number.',
            'experience_years.min' => 'Experience years cannot be negative.',
            'experience_years.max' => 'Experience years cannot exceed 50.',
            'commission_rate.required' => 'Commission rate is required.',
            'commission_rate.numeric' => 'Commission rate must be a number.',
            'commission_rate.min' => 'Commission rate cannot be negative.',
            'commission_rate.max' => 'Commission rate cannot exceed 100%.',
            'profile_photo.image' => 'Profile photo must be an image.',
            'profile_photo.mimes' => 'Profile photo must be a jpeg, png, jpg, or gif file.',
            'profile_photo.max' => 'Profile photo cannot be larger than 2MB.',
        ];
    }
    
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        \Log::error('Agent validation failed', [
            'errors' => $validator->errors()->toArray(),
            'request_data' => $this->all()
        ]);
        
        throw new HttpResponseException(
            redirect()->back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Please fix the errors below.')
        );
    }
}
