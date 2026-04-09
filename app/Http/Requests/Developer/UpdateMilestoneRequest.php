<?php

namespace App\Http\Requests\Developer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => 'required|exists:developer_projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'milestone_type' => 'required|string|max:100',
            'target_date' => 'required|date',
            'actual_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,overdue,cancelled',
            'priority_level' => 'nullable|in:low,medium,high,critical',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'budget_allocated' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'deliverables' => 'nullable|array',
            'dependencies' => 'nullable|array',
            'assigned_team' => 'nullable|array',
            'stakeholders' => 'nullable|array',
            'success_criteria' => 'nullable|array',
            'risk_factors' => 'nullable|array',
            'mitigation_strategies' => 'nullable|array',
            'quality_standards' => 'nullable|array',
            'approval_required' => 'nullable|boolean',
            'approved_by' => 'nullable|string|max:255',
            'approval_date' => 'nullable|date',
            'completion_notes' => 'nullable|string|max:2000',
            'lessons_learned' => 'nullable|string|max:2000',
            'next_milestones' => 'nullable|array',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'Project is required.',
            'project_id.exists' => 'Selected project does not exist.',
            'title.required' => 'Milestone title is required.',
            'milestone_type.required' => 'Milestone type is required.',
            'status.required' => 'Milestone status is required.',
            'target_date.required' => 'Target date is required.',
        ];
    }
}
