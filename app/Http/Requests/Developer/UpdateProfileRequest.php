<?php

namespace App\Http\Requests\Developer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => 'nullable|string|max:2000',
            'mission' => 'nullable|string|max:1000',
            'vision' => 'nullable|string|max:1000',
            'values' => 'nullable|array',
            'values.*' => 'string|max:255',
            'specializations' => 'nullable|array',
            'specializations.*' => 'string|max:255',
            'services' => 'nullable|array',
            'services.*' => 'string|max:255',
            'awards' => 'nullable|array',
            'awards.*.name' => 'required|string|max:255',
            'awards.*.year' => 'required|integer|min:1900|max:' . date('Y'),
            'awards.*.description' => 'nullable|string|max:500',
            'certifications' => 'nullable|array',
            'certifications.*.name' => 'required|string|max:255',
            'certifications.*.issuer' => 'required|string|max:255',
            'certifications.*.year' => 'required|integer|min:1900|max:' . date('Y'),
            'social_links' => 'nullable|array',
            'social_links.*.platform' => 'required|string|max:50',
            'social_links.*.url' => 'required|url|max:255',
            'contact_info' => 'nullable|array',
            'contact_info.*.type' => 'required|string|max:50',
            'contact_info.*.value' => 'required|string|max:255',
            'business_hours' => 'nullable|array',
            'business_hours.*.day' => 'required|string|max:20',
            'business_hours.*.open_time' => 'required|string|max:10',
            'business_hours.*.close_time' => 'required|string|max:10',
            'business_hours.*.is_closed' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ];
    }

    public function messages(): array
    {
        return [
            'values.*.string' => 'Each value must be a string.',
            'specializations.*.string' => 'Each specialization must be a string.',
            'services.*.string' => 'Each service must be a string.',
            'awards.*.name.required' => 'Award name is required.',
            'awards.*.year.required' => 'Award year is required.',
            'awards.*.year.integer' => 'Award year must be a number.',
            'awards.*.year.min' => 'Award year cannot be before 1900.',
            'awards.*.year.max' => 'Award year cannot be in the future.',
            'certifications.*.name.required' => 'Certification name is required.',
            'certifications.*.issuer.required' => 'Certification issuer is required.',
            'certifications.*.year.required' => 'Certification year is required.',
            'certifications.*.year.integer' => 'Certification year must be a number.',
            'certifications.*.year.min' => 'Certification year cannot be before 1900.',
            'certifications.*.year.max' => 'Certification year cannot be in the future.',
            'social_links.*.platform.required' => 'Social platform is required.',
            'social_links.*.url.required' => 'Social URL is required.',
            'social_links.*.url.url' => 'Please provide a valid URL.',
            'contact_info.*.type.required' => 'Contact type is required.',
            'contact_info.*.value.required' => 'Contact value is required.',
            'business_hours.*.day.required' => 'Day is required.',
            'business_hours.*.open_time.required' => 'Open time is required.',
            'business_hours.*.close_time.required' => 'Close time is required.',
            'logo.image' => 'Logo must be an image file.',
            'logo.mimes' => 'Logo must be a JPEG, PNG, JPG, or GIF file.',
            'logo.max' => 'Logo size cannot exceed 2MB.',
            'cover_image.image' => 'Cover image must be an image file.',
            'cover_image.mimes' => 'Cover image must be a JPEG, PNG, JPG, or GIF file.',
            'cover_image.max' => 'Cover image size cannot exceed 4MB.',
        ];
    }
}
