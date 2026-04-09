<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lead_id' => 'required|exists:agent_leads,id',
            'property_id' => 'nullable|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'type' => 'required|in:viewing,meeting,call,consultation,inspection,closing',
            'status' => 'nullable|in:scheduled,completed,cancelled,rescheduled,no_show',
            'appointment_date' => 'required|date|after:now',
            'appointment_end_date' => 'required|date|after:appointment_date',
            'location' => 'nullable|string|max:500',
            'meeting_link' => 'nullable|url|max:500',
            'phone_number' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'lead_id.required' => 'Lead is required.',
            'lead_id.exists' => 'Selected lead does not exist.',
            'title.required' => 'Appointment title is required.',
            'type.required' => 'Appointment type is required.',
            'appointment_date.required' => 'Appointment date is required.',
            'appointment_date.after' => 'Appointment date must be in the future.',
            'appointment_end_date.required' => 'Appointment end date is required.',
            'appointment_end_date.after' => 'Appointment end date must be after start date.',
            'meeting_link.url' => 'Please provide a valid meeting link URL.',
        ];
    }
}
