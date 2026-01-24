<?php

namespace App\Http\Requests\Reports;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExportReportRequest extends FormRequest
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
            'format' => ['required', Rule::in(['pdf', 'excel', 'csv', 'json'])],
            'include_charts' => ['nullable', 'boolean'],
            'include_raw_data' => ['nullable', 'boolean'],
            'custom_filename' => ['nullable', 'string', 'max:255'],
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
            'format.required' => 'Please select an export format.',
            'format.in' => 'Invalid format selected. Choose from PDF, Excel, CSV, or JSON.',
            'custom_filename.max' => 'Custom filename cannot exceed 255 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'format' => 'export format',
            'include_charts' => 'include charts',
            'include_raw_data' => 'include raw data',
            'custom_filename' => 'custom filename',
        ];
    }

    /**
     * Get the validated data and prepare export options.
     */
    public function getExportOptions(): array
    {
        return [
            'format' => $this->validated()['format'],
            'include_charts' => $this->boolean('include_charts', false),
            'include_raw_data' => $this->boolean('include_raw_data', false),
            'custom_filename' => $this->validated()['custom_filename'] ?? null,
        ];
    }
}
