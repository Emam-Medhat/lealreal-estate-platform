<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'type' => 'required|in:general,property,lead,client,appointment,commission,certification,license',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled,on_hold',
            'due_date' => 'required|date|after:now',
            'property_id' => 'nullable|exists:properties,id',
            'lead_id' => 'nullable|exists:agent_leads,id',
            'client_id' => 'nullable|exists:agent_clients,id',
            'estimated_hours' => 'nullable|numeric|min:0.1|max:1000',
            'actual_hours' => 'nullable|numeric|min:0|max:1000',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'checklist' => 'nullable|array',
            'checklist.*.id' => 'required|string|max:50',
            'checklist.*.text' => 'required|string|max:255',
            'checklist.*.completed' => 'required|boolean',
            'attachments' => 'nullable|array',
            'attachments.*.name' => 'required|string|max:255',
            'attachments.*.path' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required.',
            'type.required' => 'Task type is required.',
            'priority.required' => 'Task priority is required.',
            'due_date.required' => 'Due date is required.',
            'due_date.after' => 'Due date must be in the future.',
            'property_id.exists' => 'Selected property does not exist.',
            'lead_id.exists' => 'Selected lead does not exist.',
            'client_id.exists' => 'Selected client does not exist.',
            'estimated_hours.min' => 'Estimated hours must be at least 0.1.',
            'estimated_hours.max' => 'Estimated hours cannot exceed 1000.',
            'actual_hours.min' => 'Actual hours cannot be negative.',
            'actual_hours.max' => 'Actual hours cannot exceed 1000.',
            'checklist.*.id.required' => 'Checklist item ID is required.',
            'checklist.*.text.required' => 'Checklist item text is required.',
            'checklist.*.completed.required' => 'Checklist item completion status is required.',
            'attachments.*.name.required' => 'Attachment name is required.',
            'attachments.*.path.required' => 'Attachment path is required.',
        ];
    }
}
