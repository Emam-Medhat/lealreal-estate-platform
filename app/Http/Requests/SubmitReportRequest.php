<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'overall_condition' => 'required|in:excellent,good,fair,poor',
            'summary' => 'required|string|min:50|max:2000',
            'recommendations' => 'nullable|string|max:2000',
            'next_inspection_date' => 'nullable|date|after:today',
            'estimated_repair_cost' => 'nullable|numeric|min:0',
            'urgent_repairs' => 'boolean',
            'defects' => 'nullable|array',
            'defects.*.description' => 'required|string|max:500',
            'defects.*.location' => 'required|string|max:255',
            'defects.*.severity' => 'required|in:low,medium,high,critical',
            'defects.*.urgency' => 'required|in:low,medium,high,urgent',
            'defects.*.category' => 'required|in:structural,electrical,plumbing,hvac,interior,exterior,safety,other',
            'defects.*.estimated_cost' => 'nullable|numeric|min:0',
            'defects.*.notes' => 'nullable|string|max:1000',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'overall_condition.required' => 'يجب تحديد الحالة العامة',
            'overall_condition.in' => 'الحالة العامة غير صالحة',
            'summary.required' => 'يجب كتابة ملخص التقرير',
            'summary.min' => 'ملخص التقرير يجب أن يكون 50 حرف على الأقل',
            'summary.max' => 'ملخص التقرير يجب أن لا يتجاوز 2000 حرف',
            'recommendations.max' => 'التوصيات يجب أن لا تتجاوز 2000 حرف',
            'next_inspection_date.after' => 'تاريخ الفحص التالي يجب أن يكون بعد اليوم',
            'estimated_repair_cost.numeric' => 'تكلفة الإصلاح التقديرية يجب أن تكون رقماً',
            'estimated_repair_cost.min' => 'تكلفة الإصلاح التقديرية يجب أن تكون 0 أو أكثر',
            'defects.*.description.required' => 'وصف العيب مطلوب',
            'defects.*.description.max' => 'وصف العيب يجب أن لا يتجاوز 500 حرف',
            'defects.*.location.required' => 'موقع العيب مطلوب',
            'defects.*.location.max' => 'موقع العيب يجب أن لا يتجاوز 255 حرف',
            'defects.*.severity.required' => 'شدة العيب مطلوبة',
            'defects.*.severity.in' => 'شدة العيب غير صالحة',
            'defects.*.urgency.required' => 'أولوية العيب مطلوبة',
            'defects.*.urgency.in' => 'أولوية العيب غير صالحة',
            'defects.*.category.required' => 'فئة العيب مطلوبة',
            'defects.*.category.in' => 'فئة العيب غير صالحة',
            'defects.*.estimated_cost.numeric' => 'التكلفة التقديرية للعيب يجب أن تكون رقماً',
            'defects.*.estimated_cost.min' => 'التكلفة التقديرية للعيب يجب أن تكون 0 أو أكثر',
            'defects.*.notes.max' => 'ملاحظات العيب يجب أن لا تتجاوز 1000 حرف',
            'photos.*.image' => 'الملف يجب أن يكون صورة',
            'photos.*.mimes' => 'صيغة الصورة غير مدعومة',
            'photos.*.max' => 'حجم الصورة يجب أن لا يتجاوز 2 ميجابايت',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateDefectsCost($validator);
        });
    }

    protected function validateDefectsCost($validator)
    {
        $defects = $this->defects ?? [];
        $totalCost = 0;

        foreach ($defects as $defect) {
            if (isset($defect['estimated_cost'])) {
                $totalCost += $defect['estimated_cost'];
            }
        }

        if ($totalCost > 1000000) {
            $validator->errors()->add('defects', 'إجمالي تكاليف العيوب مرتفع جداً');
        }

        // Update the estimated_repair_cost if not provided
        if (!$this->has('estimated_repair_cost') && $totalCost > 0) {
            $this->merge([
                'estimated_repair_cost' => $totalCost
            ]);
        }
    }

    public function validated()
    {
        $data = parent::validated();

        // Set urgent_repairs to false if not provided
        if (!isset($data['urgent_repairs'])) {
            $data['urgent_repairs'] = false;
        }

        return $data;
    }
}
