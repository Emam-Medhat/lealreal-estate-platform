<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectTaskRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'project_id' => 'required|exists:projects,id',
            'phase_id' => 'nullable|exists:project_phases,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'assignee_id' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:not_started,in_progress,completed,on_hold,cancelled',
            'start_date' => 'required|date|after_or_equal:today',
            'due_date' => 'required|date|after_or_equal:start_date',
            'estimated_hours' => 'nullable|numeric|min:0|max:1000',
            'tags' => 'nullable|array',
            'tags.*' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:2000',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:project_tasks,id',
            'checklist_items' => 'nullable|array',
            'checklist_items.*.name' => 'required|string|max:255',
            'checklist_items.*.completed' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'project_id.required' => 'المشروع مطلوب',
            'project_id.exists' => 'المشروع المحدد غير موجود',
            'phase_id.exists' => 'المرحلة المحددة غير موجودة',
            'name.required' => 'اسم المهمة مطلوب',
            'assignee_id.exists' => 'المسؤول المحدد غير موجود',
            'priority.required' => 'الأولوية مطلوبة',
            'priority.in' => 'الأولوية المحددة غير صالحة',
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة المحددة غير صالحة',
            'start_date.required' => 'تاريخ البدء مطلوب',
            'start_date.after_or_equal' => 'تاريخ البدء يجب أن يكون اليوم أو في المستقبل',
            'due_date.required' => 'تاريخ الانتهاء مطلوب',
            'due_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد أو يساوي تاريخ البدء',
            'estimated_hours.numeric' => 'الساعات المقدرة يجب أن تكون رقماً',
            'estimated_hours.min' => 'الساعات المقدرة يجب أن تكون 0 أو أكثر',
            'estimated_hours.max' => 'الساعات المقدرة يجب أن لا تتجاوز 1000 ساعة',
            'tags.array' => 'الوسوم يجب أن تكون مصفوفة',
            'tags.*.string' => 'كل وسم يجب أن يكون نصاً',
            'tags.*.max' => 'كل وسم يجب أن لا يتجاوز 50 حرفاً',
            'dependencies.array' => 'الاعتماديات يجب أن تكون مصفوفة',
            'dependencies.*.exists' => 'المهمة المعتمدة غير موجودة',
            'checklist_items.array' => 'قائمة التحقق يجب أن تكون مصفوفة',
            'checklist_items.*.name.required' => 'اسم عنصر قائمة التحقق مطلوب',
            'checklist_items.*.name.max' => 'اسم عنصر قائمة التحقق يجب أن لا يتجاوز 255 حرفاً',
            'checklist_items.*.completed.boolean' => 'حالة إكمال عنصر قائمة التحقق يجب أن تكون قيمة منطقية',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'created_by' => auth()->id(),
        ]);
    }
}
