<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects', 'name')->ignore($this->project->id),
            ],
            'description' => 'nullable|string|max:2000',
            'client_id' => 'required|exists:clients,id',
            'manager_id' => 'required|exists:users,id',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'budget' => 'required|numeric|min:0',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'project_type' => 'required|in:residential,commercial,mixed,industrial',
            'total_units' => 'nullable|integer|min:0',
            'total_area' => 'nullable|numeric|min:0',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم المشروع مطلوب',
            'name.unique' => 'اسم المشروع مستخدم بالفعل',
            'client_id.required' => 'العميل مطلوب',
            'client_id.exists' => 'العميل المحدد غير موجود',
            'manager_id.required' => 'مدير المشروع مطلوب',
            'manager_id.exists' => 'المدير المحدد غير موجود',
            'start_date.required' => 'تاريخ البدء مطلوب',
            'end_date.required' => 'تاريخ الانتهاء مطلوب',
            'end_date.after' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء',
            'budget.required' => 'الميزانية مطلوبة',
            'budget.numeric' => 'الميزانية يجب أن تكون رقماً',
            'budget.min' => 'الميزانية يجب أن تكون 0 أو أكثر',
            'priority.required' => 'الأولوية مطلوبة',
            'priority.in' => 'الأولوية المحددة غير صالحة',
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة المحددة غير صالحة',
            'project_type.required' => 'نوع المشروع مطلوب',
            'project_type.in' => 'نوع المشروع المحدد غير صالح',
            'total_units.integer' => 'إجمالي الوحدات يجب أن يكون رقماً صحيحاً',
            'total_units.min' => 'إجمالي الوحدات يجب أن يكون 0 أو أكثر',
            'total_area.numeric' => 'المساحة الإجمالية يجب أن تكون رقماً',
            'total_area.min' => 'المساحة الإجمالية يجب أن تكون 0 أو أكثر',
            'features.array' => 'المميزات يجب أن تكون مصفوفة',
            'features.*.string' => 'كل ميزة يجب أن تكون نصاً',
            'features.*.max' => 'كل ميزة يجب أن لا تتجاوز 255 حرفاً',
        ];
    }
}
