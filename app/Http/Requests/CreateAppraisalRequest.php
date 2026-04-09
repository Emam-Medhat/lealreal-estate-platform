<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppraisalRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'appraiser_id' => 'required|exists:appraisers,id',
            'client_id' => 'nullable|exists:clients,id',
            'appraisal_type' => 'required|in:market_value,insurance,tax,refinance',
            'purpose' => 'required|string|max:500',
            'scheduled_date' => 'required|date|after:now',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_cost' => 'nullable|numeric|min:0',
            'assignment_reason' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'property_id.required' => 'يجب اختيار العقار',
            'property_id.exists' => 'العقار المحدد غير موجود',
            'appraiser_id.required' => 'يجب اختيار المقيم',
            'appraiser_id.exists' => 'المقيم المحدد غير موجود',
            'client_id.exists' => 'العميل المحدد غير موجود',
            'appraisal_type.required' => 'يجب تحديد نوع التقييم',
            'appraisal_type.in' => 'نوع التقييم غير صالح',
            'purpose.required' => 'يجب تحديد الغرض من التقييم',
            'purpose.max' => 'الغرض من التقييم يجب أن لا يتجاوز 500 حرف',
            'scheduled_date.required' => 'يجب تحديد موعد التقييم',
            'scheduled_date.after' => 'يجب أن يكون موعد التقييم في المستقبل',
            'priority.required' => 'يجب تحديد الأولوية',
            'priority.in' => 'الأولوية غير صالحة',
            'estimated_cost.numeric' => 'التكلفة التقديرية يجب أن تكون رقماً',
            'estimated_cost.min' => 'التكلفة التقديرية يجب أن تكون 0 أو أكثر',
            'assignment_reason.max' => 'سبب التكليف يجب أن لا يتجاوز 1000 حرف',
            'notes.max' => 'الملاحظات يجب أن لا تتجاوز 1000 حرف',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateAppraiserAvailability($validator);
            $this->validateAppraiserSpecialization($validator);
        });
    }

    protected function validateAppraiserAvailability($validator)
    {
        $appraiserId = $this->appraiser_id;
        $scheduledDate = $this->scheduled_date;

        // Check if appraiser is available on the scheduled date
        $existingAppraisals = \App\Models\Appraisal::where('appraiser_id', $appraiserId)
            ->whereDate('scheduled_date', $scheduledDate)
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($existingAppraisals >= 6) {
            $validator->errors()->add('appraiser_id', 'المقيم غير متاح في هذا التاريخ (الحد الأقصى 6 تقييمات يومياً)');
        }
    }

    protected function validateAppraiserSpecialization($validator)
    {
        $appraiserId = $this->appraiser_id;
        $propertyId = $this->property_id;
        $appraisalType = $this->appraisal_type;

        $appraiser = \App\Models\Appraiser::find($appraiserId);
        $property = \App\Models\Property::find($propertyId);

        if (!$appraiser || !$property) {
            return;
        }

        // Map appraisal types to required specializations
        $typeSpecialization = [
            'market_value' => 'residential',
            'insurance' => 'residential',
            'tax' => 'residential',
            'refinance' => 'residential',
        ];

        $requiredSpecialization = $typeSpecialization[$appraisalType] ?? null;

        if ($requiredSpecialization && !$appraiser->hasSpecialization($requiredSpecialization)) {
            $validator->errors()->add('appraiser_id', 'المقيم ليس لديه التخصص المطلوب لهذا النوع من التقييم');
        }
    }

    public function getEstimatedCost()
    {
        // Calculate estimated cost based on appraisal type and property value
        $baseCosts = [
            'market_value' => 2000,
            'insurance' => 1500,
            'tax' => 1000,
            'refinance' => 2500,
        ];

        $baseCost = $baseCosts[$this->appraisal_type] ?? 1500;
        $property = \App\Models\Property::find($this->property_id);

        if ($property && $property->price) {
            // Add 0.1% of property value for high-value properties
            if ($property->price > 1000000) {
                $baseCost += $property->price * 0.001;
            }
        }

        return round($baseCost, 2);
    }
}
