<?php

namespace App\Http\Requests\Investor;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvestorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:investors,email',
            'phone' => 'required|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'investor_type' => 'required|in:individual,company,fund,bank,government,institution',
            'status' => 'nullable|in:active,inactive,suspended,verified',
            'total_invested' => 'nullable|numeric|min:0',
            'total_returns' => 'nullable|numeric|min:0',
            'risk_tolerance' => 'required|in:conservative,moderate,aggressive,very_aggressive',
            'investment_goals' => 'nullable|array',
            'investment_goals.*' => 'string|max:255',
            'preferred_sectors' => 'nullable|array',
            'preferred_sectors.*' => 'string|max:100',
            'experience_years' => 'nullable|integer|min:0|max:100',
            'accredited_investor' => 'nullable|boolean',
            'verification_status' => 'nullable|in:pending,verified,rejected',
            'address' => 'nullable|array',
            'address.street' => 'nullable|string|max:255',
            'address.city' => 'nullable|string|max:100',
            'address.state' => 'nullable|string|max:100',
            'address.country' => 'nullable|string|max:100',
            'address.postal_code' => 'nullable|string|max:20',
            'social_links' => 'nullable|array',
            'social_links.*.platform' => 'required|string|max:100',
            'social_links.*.url' => 'required|url|max:500',
            'bio' => 'nullable|string|max:2000',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered.',
            'phone.required' => 'Phone number is required.',
            'investor_type.required' => 'Investor type is required.',
            'risk_tolerance.required' => 'Risk tolerance is required.',
            'experience_years.integer' => 'Experience years must be a whole number.',
            'experience_years.min' => 'Experience years cannot be negative.',
            'experience_years.max' => 'Experience years cannot exceed 100.',
            'profile_picture.image' => 'Profile picture must be an image file.',
            'profile_picture.mimes' => 'Profile picture must be JPEG, PNG, JPG, or GIF file.',
            'profile_picture.max' => 'Profile picture size cannot exceed 5MB.',
            'social_links.*.platform.required' => 'Social platform is required.',
            'social_links.*.url.required' => 'Social URL is required.',
            'social_links.*.url.url' => 'Please provide a valid URL.',
        ];
    }
}
