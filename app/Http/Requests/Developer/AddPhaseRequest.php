<?php

namespace App\Http\Requests\Developer;

use Illuminate\Foundation\Http\FormRequest;

class AddPhaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'phase_type' => 'required|in:foundation,structure,finishing,landscaping,infrastructure,utilities,interior,exterior',
            'status' => 'nullable|in:planned,in_progress,completed,on_hold,cancelled',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'duration_days' => 'required|integer|min:1',
            'budget' => 'required|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'completion_percentage' => 'nullable|integer|min:0|max:100',
            'contractor_id' => 'nullable|exists:developer_contractors,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'deliverables' => 'nullable|array',
            'deliverables.*' => 'string|max:255',
            'materials' => 'nullable|array',
            'materials.*.name' => 'required|string|max:255',
            'materials.*.quantity' => 'required|numeric|min:0',
            'materials.*.unit' => 'required|string|max:50',
            'materials.*.cost' => 'required|numeric|min:0',
            'equipment' => 'nullable|array',
            'equipment.*.name' => 'required|string|max:255',
            'equipment.*.type' => 'required|string|max:100',
            'equipment.*.rental_cost' => 'nullable|numeric|min:0',
            'equipment.*.rental_period_days' => 'nullable|integer|min:1',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string|max:500',
            'risks' => 'nullable|array',
            'risks.*.description' => 'required|string|max:500',
            'risks.*.probability' => 'required|in:low,medium,high',
            'risks.*.impact' => 'required|in:low,medium,high',
            'risks.*.mitigation' => 'required|string|max:500',
            'mitigation_plan' => 'nullable|array',
            'mitigation_plan.*' => 'string|max:500',
            'quality_standards' => 'nullable|array',
            'quality_standards.*' => 'string|max:255',
            'safety_measures' => 'nullable|array',
            'safety_measures.*' => 'string|max:255',
            'inspections' => 'nullable|array',
            'inspections.*.type' => 'required|string|max:100',
            'inspections.*.date' => 'required|date',
            'inspections.*.inspector' => 'required|string|max:255',
            'inspections.*.status' => 'required|in:scheduled,completed,failed,passed',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:developer_project_phases,id',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Phase name is required.',
            'phase_type.required' => 'Phase type is required.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'end_date.required' => 'End date is required.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after' => 'End date must be after start date.',
            'duration_days.required' => 'Duration is required.',
            'duration_days.integer' => 'Duration must be a whole number.',
            'duration_days.min' => 'Duration must be at least 1 day.',
            'budget.required' => 'Budget is required.',
            'budget.numeric' => 'Budget must be a number.',
            'budget.min' => 'Budget cannot be negative.',
            'actual_cost.numeric' => 'Actual cost must be a number.',
            'actual_cost.min' => 'Actual cost cannot be negative.',
            'completion_percentage.integer' => 'Completion percentage must be a whole number.',
            'completion_percentage.min' => 'Completion percentage cannot be negative.',
            'completion_percentage.max' => 'Completion percentage cannot exceed 100%.',
            'contractor_id.exists' => 'Selected contractor does not exist.',
            'supervisor_id.exists' => 'Selected supervisor does not exist.',
            'materials.*.name.required' => 'Material name is required.',
            'materials.*.quantity.required' => 'Material quantity is required.',
            'materials.*.quantity.numeric' => 'Material quantity must be a number.',
            'materials.*.quantity.min' => 'Material quantity cannot be negative.',
            'materials.*.unit.required' => 'Material unit is required.',
            'materials.*.cost.required' => 'Material cost is required.',
            'materials.*.cost.numeric' => 'Material cost must be a number.',
            'materials.*.cost.min' => 'Material cost cannot be negative.',
            'equipment.*.name.required' => 'Equipment name is required.',
            'equipment.*.type.required' => 'Equipment type is required.',
            'equipment.*.rental_cost.numeric' => 'Rental cost must be a number.',
            'equipment.*.rental_cost.min' => 'Rental cost cannot be negative.',
            'equipment.*.rental_period_days.integer' => 'Rental period must be a whole number.',
            'equipment.*.rental_period_days.min' => 'Rental period must be at least 1 day.',
            'risks.*.description.required' => 'Risk description is required.',
            'risks.*.probability.required' => 'Risk probability is required.',
            'risks.*.impact.required' => 'Risk impact is required.',
            'risks.*.mitigation.required' => 'Risk mitigation is required.',
            'inspections.*.type.required' => 'Inspection type is required.',
            'inspections.*.date.required' => 'Inspection date is required.',
            'inspections.*.date.date' => 'Inspection date must be a valid date.',
            'inspections.*.inspector.required' => 'Inspector name is required.',
            'inspections.*.status.required' => 'Inspection status is required.',
            'dependencies.*.exists' => 'One or more selected dependencies do not exist.',
            'documents.*.file' => 'Each document must be a file.',
            'documents.*.mimes' => 'Documents must be PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, or PNG files.',
            'documents.*.max' => 'Document size cannot exceed 10MB.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Images must be JPEG, PNG, JPG, or GIF files.',
            'images.*.max' => 'Image size cannot exceed 5MB.',
        ];
    }
}
