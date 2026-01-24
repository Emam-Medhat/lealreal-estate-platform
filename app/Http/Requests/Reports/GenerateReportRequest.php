<?php

namespace App\Http\Requests\Reports;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateReportRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'template_id' => ['required', 'exists:report_templates,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'parameters' => ['nullable', 'array'],
            'parameters.*' => ['string'],
            'filters' => ['nullable', 'array'],
            'filters.date_range.start' => ['nullable', 'date'],
            'filters.date_range.end' => ['nullable', 'date', 'after:filters.date_range.start'],
            'format' => ['required', Rule::in(['pdf', 'excel', 'csv', 'json'])],
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
            'title.required' => 'Report title is required.',
            'template_id.required' => 'Please select a report template.',
            'template_id.exists' => 'Selected template does not exist.',
            'format.required' => 'Please select an export format.',
            'format.in' => 'Invalid format selected.',
            'filters.date_range.end.after' => 'End date must be after start date.',
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
            'title' => 'report title',
            'template_id' => 'report template',
            'description' => 'description',
            'format' => 'export format',
            'filters.date_range.start' => 'start date',
            'filters.date_range.end' => 'end date',
        ];
    }
}
