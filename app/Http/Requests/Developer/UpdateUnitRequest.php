<?php

namespace App\Http\Requests\Developer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unit_number' => 'required|string|max:50',
            'unit_type' => 'required|in:apartment,villa,townhouse,penthouse,studio,duplex,office,retail,warehouse',
            'status' => 'required|in:available,reserved,sold,under_construction,ready,maintenance',
            'floor_number' => 'nullable|integer|min:0',
            'block_number' => 'nullable|string|max:50',
            'bedrooms' => 'nullable|integer|min:0|max:20',
            'bathrooms' => 'nullable|integer|min:0|max:20',
            'total_area' => 'required|numeric|min:0',
            'net_area' => 'nullable|numeric|min:0|lte:total_area',
            'price' => 'required|numeric|min:0',
            'price_per_sqm' => 'nullable|numeric|min:0',
            'orientation' => 'nullable|in:north,south,east,west,north_east,north_west,south_east,south_west',
            'view' => 'nullable|string|max:255',
            'balcony_area' => 'nullable|numeric|min:0',
            'garden_area' => 'nullable|numeric|min:0',
            'parking_spaces' => 'nullable|integer|min:0|max:10',
            'storage_rooms' => 'nullable|integer|min:0|max:10',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'finishing_level' => 'nullable|in:basic,standard,premium,luxury',
            'kitchen_type' => 'nullable|in:open,closed,semi_open',
            'furniture_included' => 'nullable|boolean',
            'appliances_included' => 'nullable|boolean',
            'delivery_date' => 'nullable|date',
            'maintenance_fee' => 'nullable|numeric|min:0',
            'service_charges' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:2000',
            'floor_plan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'virtual_tour' => 'nullable|url|max:500',
            'specifications' => 'nullable|array',
            'specifications.*.category' => 'required|string|max:100',
            'specifications.*.items' => 'required|array',
            'specifications.*.items.*.name' => 'required|string|max:255',
            'specifications.*.items.*.value' => 'required|string|max:255',
            'custom_fields' => 'nullable|array',
            'custom_fields.*.name' => 'required|string|max:255',
            'custom_fields.*.value' => 'required|string|max:255',
            'custom_fields.*.type' => 'required|in:text,number,boolean,date,select',
        ];
    }

    public function messages(): array
    {
        return [
            'unit_number.required' => 'Unit number is required.',
            'unit_type.required' => 'Unit type is required.',
            'status.required' => 'Unit status is required.',
            'total_area.required' => 'Total area is required.',
            'total_area.numeric' => 'Total area must be a number.',
            'total_area.min' => 'Total area cannot be negative.',
            'net_area.numeric' => 'Net area must be a number.',
            'net_area.min' => 'Net area cannot be negative.',
            'net_area.lte' => 'Net area cannot be greater than total area.',
            'price.required' => 'Unit price is required.',
            'price.numeric' => 'Unit price must be a number.',
            'price.min' => 'Unit price cannot be negative.',
            'price_per_sqm.numeric' => 'Price per square meter must be a number.',
            'price_per_sqm.min' => 'Price per square meter cannot be negative.',
            'bedrooms.integer' => 'Number of bedrooms must be a whole number.',
            'bedrooms.min' => 'Number of bedrooms cannot be negative.',
            'bedrooms.max' => 'Number of bedrooms cannot exceed 20.',
            'bathrooms.integer' => 'Number of bathrooms must be a whole number.',
            'bathrooms.min' => 'Number of bathrooms cannot be negative.',
            'bathrooms.max' => 'Number of bathrooms cannot exceed 20.',
            'floor_number.integer' => 'Floor number must be a whole number.',
            'floor_number.min' => 'Floor number cannot be negative.',
            'parking_spaces.integer' => 'Number of parking spaces must be a whole number.',
            'parking_spaces.min' => 'Number of parking spaces cannot be negative.',
            'parking_spaces.max' => 'Number of parking spaces cannot exceed 10.',
            'storage_rooms.integer' => 'Number of storage rooms must be a whole number.',
            'storage_rooms.min' => 'Number of storage rooms cannot be negative.',
            'storage_rooms.max' => 'Number of storage rooms cannot exceed 10.',
            'balcony_area.numeric' => 'Balcony area must be a number.',
            'balcony_area.min' => 'Balcony area cannot be negative.',
            'garden_area.numeric' => 'Garden area must be a number.',
            'garden_area.min' => 'Garden area cannot be negative.',
            'maintenance_fee.numeric' => 'Maintenance fee must be a number.',
            'maintenance_fee.min' => 'Maintenance fee cannot be negative.',
            'service_charges.numeric' => 'Service charges must be a number.',
            'service_charges.min' => 'Service charges cannot be negative.',
            'delivery_date.date' => 'Delivery date must be a valid date.',
            'virtual_tour.url' => 'Virtual tour must be a valid URL.',
            'floor_plan.file' => 'Floor plan must be a file.',
            'floor_plan.mimes' => 'Floor plan must be a PDF, JPG, JPEG, or PNG file.',
            'floor_plan.max' => 'Floor plan size cannot exceed 5MB.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Images must be JPEG, PNG, JPG, or GIF files.',
            'images.*.max' => 'Image size cannot exceed 5MB.',
            'specifications.*.category.required' => 'Specification category is required.',
            'specifications.*.items.required' => 'Specification items are required.',
            'specifications.*.items.*.name.required' => 'Specification item name is required.',
            'specifications.*.items.*.value.required' => 'Specification item value is required.',
            'custom_fields.*.name.required' => 'Custom field name is required.',
            'custom_fields.*.value.required' => 'Custom field value is required.',
            'custom_fields.*.type.required' => 'Custom field type is required.',
        ];
    }
}
