<?php

namespace App\Http\Requests\Reports;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScheduleReportRequest extends FormRequest
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
            'template_id' => ['required', 'exists:report_templates,id'],
            'parameters' => ['nullable', 'array'],
            'filters' => ['nullable', 'array'],
            'frequency' => ['required', Rule::in(['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])],
            'schedule_config.time' => ['required', 'date_format:H:i'],
            'schedule_config.day_of_week' => ['nullable', 'integer', 'between:0,6'],
            'schedule_config.day_of_month' => ['nullable', 'integer', 'between:1,31'],
            'format' => ['required', Rule::in(['pdf', 'excel', 'csv'])],
            'recipients' => ['nullable', 'array'],
            'recipients.*' => ['email'],
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
            'name.required' => 'Schedule name is required.',
            'template_id.required' => 'Please select a report template.',
            'frequency.required' => 'Please select a frequency.',
            'frequency.in' => 'Invalid frequency selected.',
            'schedule_config.time.required' => 'Schedule time is required.',
            'schedule_config.time.date_format' => 'Invalid time format. Use HH:MM format.',
            'schedule_config.day_of_week.between' => 'Day of week must be between 0 (Sunday) and 6 (Saturday).',
            'schedule_config.day_of_month.between' => 'Day of month must be between 1 and 31.',
            'format.required' => 'Please select an export format.',
            'recipients.*.email' => 'All recipients must be valid email addresses.',
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
            'name' => 'schedule name',
            'template_id' => 'report template',
            'frequency' => 'frequency',
            'schedule_config.time' => 'schedule time',
            'schedule_config.day_of_week' => 'day of week',
            'schedule_config.day_of_month' => 'day of month',
            'format' => 'export format',
            'recipients.*' => 'recipient email',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $frequency = $this->input('frequency');
            $scheduleConfig = $this->input('schedule_config', []);

            if ($frequency === 'weekly' && !isset($scheduleConfig['day_of_week'])) {
                $validator->errors()->add('schedule_config.day_of_week', 'Day of week is required for weekly schedules.');
            }

            if ($frequency === 'monthly' && !isset($scheduleConfig['day_of_month'])) {
                $validator->errors()->add('schedule_config.day_of_month', 'Day of month is required for monthly schedules.');
            }

            if ($frequency === 'weekly' && isset($scheduleConfig['day_of_week']) && ($scheduleConfig['day_of_week'] < 0 || $scheduleConfig['day_of_week'] > 6)) {
                $validator->errors()->add('schedule_config.day_of_week', 'Day of week must be between 0 (Sunday) and 6 (Saturday).');
            }

            if ($frequency === 'monthly' && isset($scheduleConfig['day_of_month']) && ($scheduleConfig['day_of_month'] < 1 || $scheduleConfig['day_of_month'] > 31)) {
                $validator->errors()->add('schedule_config.day_of_month', 'Day of month must be between 1 and 31.');
            }
        });
    }
}
