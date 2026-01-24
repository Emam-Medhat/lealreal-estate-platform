<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkUploadPropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules(): array
    {
        return [
            // File Upload
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
            'data_format' => 'required|in:excel,csv,json',
            
            // Import Settings
            'skip_duplicates' => 'boolean',
            'update_existing' => 'boolean',
            'validate_only' => 'boolean',
            'send_notifications' => 'boolean',
            
            // Field Mapping (for CSV/Excel)
            'field_mapping' => 'nullable|array',
            'field_mapping.title' => 'required|string',
            'field_mapping.description' => 'required|string',
            'field_mapping.property_type' => 'required|string',
            'field_mapping.listing_type' => 'required|string',
            'field_mapping.price' => 'required|string',
            'field_mapping.currency' => 'nullable|string',
            'field_mapping.area' => 'required|string',
            'field_mapping.area_unit' => 'nullable|string',
            'field_mapping.bedrooms' => 'nullable|string',
            'field_mapping.bathrooms' => 'nullable|string',
            'field_mapping.address' => 'required|string',
            'field_mapping.city' => 'required|string',
            'field_mapping.country' => 'required|string',
            'field_mapping.postal_code' => 'nullable|string',
            'field_mapping.latitude' => 'nullable|string',
            'field_mapping.longitude' => 'nullable|string',
            'field_mapping.status' => 'nullable|string',
            'field_mapping.featured' => 'nullable|string',
            'field_mapping.premium' => 'nullable|string',
            'field_mapping.year_built' => 'nullable|string',
            'field_mapping.floors' => 'nullable|string',
            'field_mapping.parking_spaces' => 'nullable|string',
            'field_mapping.land_area' => 'nullable|string',
            'field_mapping.land_area_unit' => 'nullable|string',
            'field_mapping.amenities' => 'nullable|string',
            'field_mapping.features' => 'nullable|string',
            'field_mapping.nearby_places' => 'nullable|string',
            'field_mapping.virtual_tour_url' => 'nullable|string',
            'field_mapping.agent_name' => 'nullable|string',
            'field_mapping.agent_email' => 'nullable|string',
            'field_mapping.agent_phone' => 'nullable|string',
            
            // Default Values
            'default_values' => 'nullable|array',
            'default_values.currency' => 'nullable|string|in:SAR,USD,EUR,GBP,AED',
            'default_values.area_unit' => 'nullable|string|in:sq_m,sq_ft',
            'default_values.land_area_unit' => 'nullable|string|in:sq_m,sq_ft,acre,hectare',
            'default_values.status' => 'nullable|string|in:draft,active,inactive',
            'default_values.listing_type' => 'nullable|string|in:sale,rent,lease',
            'default_values.featured' => 'nullable|boolean',
            'default_values.premium' => 'nullable|boolean',
            'default_values.is_negotiable' => 'nullable|boolean',
            'default_values.includes_vat' => 'nullable|boolean',
            'default_values.vat_rate' => 'nullable|numeric|min:0|max:100',
            
            // Validation Rules
            'validation_rules' => 'nullable|array',
            'validation_rules.required_fields' => 'nullable|array',
            'validation_rules.required_fields.*' => 'string',
            'validation_rules.min_price' => 'nullable|numeric|min:0',
            'validation_rules.max_price' => 'nullable|numeric|min:0',
            'validation_rules.min_area' => 'nullable|numeric|min:1',
            'validation_rules.max_area' => 'nullable|numeric|min:1',
            'validation_rules.min_bedrooms' => 'nullable|integer|min:0',
            'validation_rules.max_bedrooms' => 'nullable|integer|min:0',
            'validation_rules.min_bathrooms' => 'nullable|integer|min:0',
            'validation_rules.max_bathrooms' => 'nullable|integer|min:0',
            'validation_rules.min_year_built' => 'nullable|integer|min:1900',
            'validation_rules.max_year_built' => 'nullable|integer|max:' . date('Y'),
            
            // Processing Options
            'process_images' => 'boolean',
            'image_folder' => 'nullable|string|max:255',
            'image_url_column' => 'nullable|string',
            'process_documents' => 'boolean',
            'document_folder' => 'nullable|string|max:255',
            'document_url_column' => 'nullable|string',
            
            // Notification Settings
            'notification_email' => 'nullable|email',
            'notification_settings' => 'nullable|array',
            'notification_settings.on_success' => 'boolean',
            'notification_settings.on_error' => 'boolean',
            'notification_settings.on_duplicate' => 'boolean',
            'notification_settings.include_summary' => 'boolean',
            'notification_settings.include_errors' => 'boolean',
            
            // Batch Processing
            'batch_size' => 'nullable|integer|min:1|max:1000',
            'delay_between_batches' => 'nullable|integer|min:0|max:60', // seconds
            
            // Data Transformation
            'transformations' => 'nullable|array',
            'transformations.*.field' => 'required|string',
            'transformations.*.type' => 'required|in:,uppercase,lowercase,trim,replace,format_date,format_number,format_currency',
            'transformations.*.value' => 'nullable|string',
            'transformations.*.pattern' => 'nullable|string',
            'transformations.*.replacement' => 'nullable|string',
            
            // Import Categories
            'category_mapping' => 'nullable|array',
            'category_mapping.*.source' => 'required|string',
            'category_mapping.*.target' => 'required|string',
            'category_mapping.*.type' => 'required|in:property_type,listing_type,status,currency,area_unit',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please select a file to upload.',
            'file.file' => 'The uploaded file must be a valid file.',
            'file.mimes' => 'The file must be an Excel (xlsx, xls) or CSV file.',
            'file.max' => 'The file may not be larger than 10MB.',
            'data_format.required' => 'Data format is required.',
            'data_format.in' => 'Data format must be excel, csv, or json.',
            'field_mapping.title.required' => 'Title field mapping is required.',
            'field_mapping.description.required' => 'Description field mapping is required.',
            'field_mapping.property_type.required' => 'Property type field mapping is required.',
            'field_mapping.listing_type.required' => 'Listing type field mapping is required.',
            'field_mapping.price.required' => 'Price field mapping is required.',
            'field_mapping.area.required' => 'Area field mapping is required.',
            'field_mapping.address.required' => 'Address field mapping is required.',
            'field_mapping.city.required' => 'City field mapping is required.',
            'field_mapping.country.required' => 'Country field mapping is required.',
            'default_values.currency.in' => 'Default currency must be SAR, USD, EUR, GBP, or AED.',
            'default_values.area_unit.in' => 'Default area unit must be sq_m or sq_ft.',
            'default_values.land_area_unit.in' => 'Default land area unit must be sq_m, sq_ft, acre, or hectare.',
            'default_values.status.in' => 'Default status must be draft, active, or inactive.',
            'default_values.listing_type.in' => 'Default listing type must be sale, rent, or lease.',
            'validation_rules.min_price.numeric' => 'Minimum price must be a number.',
            'validation_rules.min_price.min' => 'Minimum price must be at least 0.',
            'validation_rules.max_price.numeric' => 'Maximum price must be a number.',
            'validation_rules.max_price.min' => 'Maximum price must be at least 0.',
            'validation_rules.min_area.numeric' => 'Minimum area must be a number.',
            'validation_rules.min_area.min' => 'Minimum area must be greater than 0.',
            'validation_rules.max_area.numeric' => 'Maximum area must be a number.',
            'validation_rules.max_area.min' => 'Maximum area must be greater than 0.',
            'validation_rules.min_bedrooms.integer' => 'Minimum bedrooms must be an integer.',
            'validation_rules.min_bedrooms.min' => 'Minimum bedrooms must be at least 0.',
            'validation_rules.max_bedrooms.integer' => 'Maximum bedrooms must be an integer.',
            'validation_rules.max_bedrooms.min' => 'Maximum bedrooms must be at least 0.',
            'validation_rules.min_bathrooms.integer' => 'Minimum bathrooms must be an integer.',
            'validation_rules.min_bathrooms.min' => 'Minimum bathrooms must be at least 0.',
            'validation_rules.max_bathrooms.integer' => 'Maximum bathrooms must be an integer.',
            'validation_rules.max_bathrooms.min' => 'Maximum bathrooms must be at least 0.',
            'validation_rules.min_year_built.integer' => 'Minimum year built must be an integer.',
            'validation_rules.min_year_built.min' => 'Minimum year built must be 1900 or later.',
            'validation_rules.max_year_built.integer' => 'Maximum year built must be an integer.',
            'validation_rules.max_year_built.max' => 'Maximum year built cannot be in the future.',
            'batch_size.integer' => 'Batch size must be an integer.',
            'batch_size.min' => 'Batch size must be at least 1.',
            'batch_size.max' => 'Batch size may not be greater than 1000.',
            'delay_between_batches.integer' => 'Delay between batches must be an integer.',
            'delay_between_batches.min' => 'Delay between batches must be at least 0.',
            'delay_between_batches.max' => 'Delay between batches may not be greater than 60.',
            'notification_email.email' => 'Notification email must be a valid email address.',
            'transformations.*.field.required' => 'Field name is required for transformations.',
            'transformations.*.type.required' => 'Transformation type is required.',
            'transformations.*.type.in' => 'Transformation type must be one of: uppercase, lowercase, trim, replace, format_date, format_number, format_currency.',
            'category_mapping.*.source.required' => 'Source value is required for category mapping.',
            'category_mapping.*.target.required' => 'Target value is required for category mapping.',
            'category_mapping.*.type.required' => 'Mapping type is required for category mapping.',
            'category_mapping.*.type.in' => 'Mapping type must be property_type, listing_type, status, currency, or area_unit.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'skip_duplicates' => $this->boolean('skip_duplicates', true),
            'update_existing' => $this->boolean('update_existing', false),
            'validate_only' => $this->boolean('validate_only', false),
            'send_notifications' => $this->boolean('send_notifications', false),
            'process_images' => $this->boolean('process_images', false),
            'process_documents' => $this->boolean('process_documents', false),
            'batch_size' => $this->get('batch_size', 100),
            'delay_between_batches' => $this->get('delay_between_batches', 1),
        ]);

        // Set default notification settings
        if ($this->has('notification_settings')) {
            $this->merge([
                'notification_settings' => array_merge([
                    'on_success' => true,
                    'on_error' => true,
                    'on_duplicate' => false,
                    'include_summary' => true,
                    'include_errors' => true,
                ], $this->notification_settings)
            ]);
        }

        // Clean field mapping
        if ($this->has('field_mapping')) {
            $fieldMapping = $this->field_mapping;
            foreach ($fieldMapping as $key => $value) {
                $fieldMapping[$key] = trim($value);
            }
            $this->merge(['field_mapping' => $fieldMapping]);
        }

        // Validate validation rules ranges
        if ($this->has('validation_rules')) {
            $rules = $this->validation_rules;
            
            // Ensure min <= max for range validations
            if (isset($rules['min_price']) && isset($rules['max_price'])) {
                if ($rules['min_price'] > $rules['max_price']) {
                    $rules['max_price'] = $rules['min_price'];
                }
            }
            
            if (isset($rules['min_area']) && isset($rules['max_area'])) {
                if ($rules['min_area'] > $rules['max_area']) {
                    $rules['max_area'] = $rules['min_area'];
                }
            }
            
            if (isset($rules['min_bedrooms']) && isset($rules['max_bedrooms'])) {
                if ($rules['min_bedrooms'] > $rules['max_bedrooms']) {
                    $rules['max_bedrooms'] = $rules['min_bedrooms'];
                }
            }
            
            if (isset($rules['min_bathrooms']) && isset($rules['max_bathrooms'])) {
                if ($rules['min_bathrooms'] > $rules['max_bathrooms']) {
                    $rules['max_bathrooms'] = $rules['min_bathrooms'];
                }
            }
            
            if (isset($rules['min_year_built']) && isset($rules['max_year_built'])) {
                if ($rules['min_year_built'] > $rules['max_year_built']) {
                    $rules['max_year_built'] = $rules['min_year_built'];
                }
            }
            
            $this->merge(['validation_rules' => $rules]);
        }
    }
}
