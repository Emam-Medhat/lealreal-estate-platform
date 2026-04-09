<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScheduleInspectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'inspector_id' => 'required|exists:inspectors,id',
            'client_id' => 'nullable|exists:clients,id',
            'scheduled_date' => 'required|date|after:now',
            'inspection_type' => 'required|in:routine,detailed,pre_sale,post_repair',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_duration' => 'required|integer|min:30|max:480',
            'estimated_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'property_id.required' => 'يجب اختيار العقار',
            'property_id.exists' => 'العقار المحدد غير موجود',
            'inspector_id.required' => 'يجب اختيار المفتش',
            'inspector_id.exists' => 'المفتش المحدد غير موجود',
            'client_id.exists' => 'العميل المحدد غير موجود',
            'scheduled_date.required' => 'يجب تحديد موعد الفحص',
            'scheduled_date.after' => 'يجب أن يكون موعد الفحص في المستقبل',
            'inspection_type.required' => 'يجب تحديد نوع الفحص',
            'inspection_type.in' => 'نوع الفحص غير صالح',
            'priority.required' => 'يجب تحديد الأولوية',
            'priority.in' => 'الأولوية غير صالحة',
            'estimated_duration.required' => 'يجب تحديد المدة التقديرية',
            'estimated_duration.min' => 'المدة التقديرية يجب أن تكون 30 دقيقة على الأقل',
            'estimated_duration.max' => 'المدة التقديرية يجب أن لا تتجاوز 480 دقيقة',
            'estimated_cost.numeric' => 'التكلفة التقديرية يجب أن تكون رقماً',
            'estimated_cost.min' => 'التكلفة التقديرية يجب أن تكون 0 أو أكثر',
            'notes.max' => 'الملاحظات يجب أن لا تتجاوز 1000 حرف',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateInspectorAvailability($validator);
        });
    }

    protected function validateInspectorAvailability($validator)
    {
        $inspectorId = $this->inspector_id;
        $scheduledDate = $this->scheduled_date;

        // Check if inspector is available on the scheduled date
        $existingInspections = \App\Models\Inspection::where('inspector_id', $inspectorId)
            ->whereDate('scheduled_date', $scheduledDate)
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($existingInspections >= 8) {
            $validator->errors()->add('inspector_id', 'المفتش غير متاح في هذا التاريخ (الحد الأقصى 8 فحوصات يومياً)');
        }
    }
}
