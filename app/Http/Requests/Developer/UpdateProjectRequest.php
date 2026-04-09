<?php

namespace App\Http\Requests\Developer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'project_type' => 'required|in:residential,commercial,mixed_use,industrial,infrastructure',
            'status' => 'required|in:planning,under_construction,completed,on_hold,cancelled',
            'location' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'land_area' => 'required|numeric|min:0',
            'total_units' => 'required|integer|min:1',
            'total_value' => 'required|numeric|min:0',
            'total_investment' => 'required|numeric|min:0',
            'expected_roi' => 'nullable|numeric|min:0|max:100',
            'start_date' => 'nullable|date',
            'completion_date' => 'nullable|date|after:start_date',
            'handover_date' => 'nullable|date|after_or_equal:completion_date',
            'architecture_style' => 'nullable|string|max:100',
            'building_materials' => 'nullable|array',
            'building_materials.*' => 'string|max:100',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:255',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|max:255',
            'nearby_places' => 'nullable|array',
            'nearby_places.*.name' => 'required|string|max:255',
            'nearby_places.*.type' => 'required|string|max:100',
            'nearby_places.*.distance' => 'required|numeric|min:0',
            'payment_plans' => 'nullable|array',
            'payment_plans.*.name' => 'required|string|max:255',
            'payment_plans.*.down_payment' => 'required|numeric|min:0|max:100',
            'payment_plans.*.installment_months' => 'required|integer|min:1',
            'payment_plans.*.interest_rate' => 'nullable|numeric|min:0|max:100',
            'financing_options' => 'nullable|array',
            'financing_options.*.bank' => 'required|string|max:255',
            'financing_options.*.loan_percentage' => 'required|numeric|min:0|max:100',
            'financing_options.*.interest_rate' => 'required|numeric|min:0|max:100',
            'financing_options.*.loan_term_years' => 'required|integer|min:1|max:30',
            'legal_documents' => 'nullable|array',
            'legal_documents.*.name' => 'required|string|max:255',
            'legal_documents.*.document_number' => 'required|string|max:100',
            'legal_documents.*.issue_date' => 'required|date',
            'legal_documents.*.expiry_date' => 'nullable|date|after:issue_date',
            'permits' => 'nullable|array',
            'permits.*.type' => 'required|string|max:100',
            'permits.*.permit_number' => 'required|string|max:100',
            'permits.*.issue_date' => 'required|date',
            'permits.*.expiry_date' => 'nullable|date|after:issue_date',
            'contractors' => 'nullable|array',
            'contractors.*.name' => 'required|string|max:255',
            'contractors.*.type' => 'required|string|max:100',
            'contractors.*.contact_person' => 'required|string|max:255',
            'contractors.*.contact_phone' => 'required|string|max:20',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'master_plan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Project name is required.',
            'description.required' => 'Project description is required.',
            'project_type.required' => 'Project type is required.',
            'status.required' => 'Project status is required.',
            'location.required' => 'Project location is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'country.required' => 'Country is required.',
            'land_area.required' => 'Land area is required.',
            'land_area.numeric' => 'Land area must be a number.',
            'land_area.min' => 'Land area cannot be negative.',
            'total_units.required' => 'Total units is required.',
            'total_units.integer' => 'Total units must be a whole number.',
            'total_units.min' => 'Total units must be at least 1.',
            'total_value.required' => 'Total value is required.',
            'total_value.numeric' => 'Total value must be a number.',
            'total_value.min' => 'Total value cannot be negative.',
            'total_investment.required' => 'Total investment is required.',
            'total_investment.numeric' => 'Total investment must be a number.',
            'total_investment.min' => 'Total investment cannot be negative.',
            'expected_roi.numeric' => 'Expected ROI must be a number.',
            'expected_roi.min' => 'Expected ROI cannot be negative.',
            'expected_roi.max' => 'Expected ROI cannot exceed 100%.',
            'completion_date.after' => 'Completion date must be after start date.',
            'handover_date.after_or_equal' => 'Handover date must be on or after completion date.',
            'latitude.between' => 'Latitude must be between -90 and 90 degrees.',
            'longitude.between' => 'Longitude must be between -180 and 180 degrees.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Images must be JPEG, PNG, JPG, or GIF files.',
            'images.*.max' => 'Image size cannot exceed 5MB.',
            'master_plan.file' => 'Master plan must be a file.',
            'master_plan.mimes' => 'Master plan must be a PDF, JPG, JPEG, or PNG file.',
            'master_plan.max' => 'Master plan size cannot exceed 10MB.',
        ];
    }
}
