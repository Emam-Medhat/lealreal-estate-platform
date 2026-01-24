<?php

namespace App\Http\Requests\Sustainability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertySustainabilityRequest extends FormRequest
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
            'property_id' => 'required|exists:properties,id',
            'energy_efficiency_rating' => 'required|numeric|min:0|max:100',
            'water_efficiency_rating' => 'required|numeric|min:0|max:100',
            'waste_management_score' => 'required|numeric|min:0|max:100',
            'green_space_ratio' => 'required|numeric|min:0|max:1',
            'renewable_energy_percentage' => 'required|numeric|min:0|max:100',
            'sustainable_materials_percentage' => 'required|numeric|min:0|max:100',
            'carbon_footprint' => 'required|numeric|min:0',
            'eco_score' => 'nullable|numeric|min:0|max:100',
            'certification_status' => 'required|in:not_certified,in_progress,certified,expired,suspended',
            'last_audit_date' => 'nullable|date|before_or_equal:today',
            'next_audit_date' => 'nullable|date|after:last_audit_date',
            'notes' => 'nullable|string|max:1000',
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
            'property_id.required' => 'يجب اختيار العقار',
            'property_id.exists' => 'العقار المحدد غير موجود',
            'energy_efficiency_rating.required' => 'معدل كفاءة الطاقة مطلوب',
            'energy_efficiency_rating.numeric' => 'معدل كفاءة الطاقة يجب أن يكون رقماً',
            'energy_efficiency_rating.min' => 'معدل كفاءة الطاقة يجب أن يكون 0 على الأقل',
            'energy_efficiency_rating.max' => 'معدل كفاءة الطاقة يجب أن لا يتجاوز 100',
            'water_efficiency_rating.required' => 'معدل كفاءة المياه مطلوب',
            'water_efficiency_rating.numeric' => 'معدل كفاءة المياه يجب أن يكون رقماً',
            'water_efficiency_rating.min' => 'معدل كفاءة المياه يجب أن يكون 0 على الأقل',
            'water_efficiency_rating.max' => 'معدل كفاءة المياه يجب أن لا يتجاوز 100',
            'waste_management_score.required' => 'درجة إدارة النفايات مطلوبة',
            'waste_management_score.numeric' => 'درجة إدارة النفايات يجب أن تكون رقماً',
            'waste_management_score.min' => 'درجة إدارة النفايات يجب أن تكون 0 على الأقل',
            'waste_management_score.max' => 'درجة إدارة النفايات يجب أن لا تتجاوز 100',
            'green_space_ratio.required' => 'نسبة المساحات الخضراء مطلوبة',
            'green_space_ratio.numeric' => 'نسبة المساحات الخضراء يجب أن تكون رقماً',
            'green_space_ratio.min' => 'نسبة المساحات الخضراء يجب أن تكون 0 على الأقل',
            'green_space_ratio.max' => 'نسبة المساحات الخضراء يجب أن لا تتجاوز 1',
            'renewable_energy_percentage.required' => 'نسبة الطاقة المتجددة مطلوبة',
            'renewable_energy_percentage.numeric' => 'نسبة الطاقة المتجددة يجب أن تكون رقماً',
            'renewable_energy_percentage.min' => 'نسبة الطاقة المتجددة يجب أن تكون 0 على الأقل',
            'renewable_energy_percentage.max' => 'نسبة الطاقة المتجددة يجب أن لا تتجاوز 100',
            'sustainable_materials_percentage.required' => 'نسبة المواد المستدامة مطلوبة',
            'sustainable_materials_percentage.numeric' => 'نسبة المواد المستدامة يجب أن تكون رقماً',
            'sustainable_materials_percentage.min' => 'نسبة المواد المستدامة يجب أن تكون 0 على الأقل',
            'sustainable_materials_percentage.max' => 'نسبة المواد المستدامة يجب أن لا تتجاوز 100',
            'carbon_footprint.required' => 'البصمة الكربونية مطلوبة',
            'carbon_footprint.numeric' => 'البصمة الكربونية يجب أن تكون رقماً',
            'carbon_footprint.min' => 'البصمة الكربونية يجب أن تكون 0 على الأقل',
            'eco_score.numeric' => 'الدرجة البيئية يجب أن تكون رقماً',
            'eco_score.min' => 'الدرجة البيئية يجب أن تكون 0 على الأقل',
            'eco_score.max' => 'الدرجة البيئية يجب أن لا تتجاوز 100',
            'certification_status.required' => 'حالة الشهادة مطلوبة',
            'certification_status.in' => 'حالة الشهادة المحددة غير صالحة',
            'last_audit_date.date' => 'تاريخ التدقيق الأخير يجب أن يكون تاريخاً صالحاً',
            'last_audit_date.before_or_equal' => 'تاريخ التدقيق الأخير يجب أن يكون اليوم أو قبل اليوم',
            'next_audit_date.date' => 'تاريخ التدقيق التالي يجب أن يكون تاريخاً صالحاً',
            'next_audit_date.after' => 'تاريخ التدقيق التالي يجب أن يكون بعد تاريخ التدقيق الأخير',
            'notes.string' => 'الملاحظات يجب أن تكون نصاً',
            'notes.max' => 'الملاحظات يجب أن لا تتجاوز 1000 حرف',
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
            'property_id' => 'العقار',
            'energy_efficiency_rating' => 'معدل كفاءة الطاقة',
            'water_efficiency_rating' => 'معدل كفاءة المياه',
            'waste_management_score' => 'درجة إدارة النفايات',
            'green_space_ratio' => 'نسبة المساحات الخضراء',
            'renewable_energy_percentage' => 'نسبة الطاقة المتجددة',
            'sustainable_materials_percentage' => 'نسبة المواد المستدامة',
            'carbon_footprint' => 'البصمة الكربونية',
            'eco_score' => 'الدرجة البيئية',
            'certification_status' => 'حالة الشهادة',
            'last_audit_date' => 'تاريخ التدقيق الأخير',
            'next_audit_date' => 'تاريخ التدقيق التالي',
            'notes' => 'الملاحظات',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if property already has sustainability assessment
            if ($this->property_id) {
                $existing = \App\Models\Sustainability\PropertySustainability::where('property_id', $this->property_id)->first();
                if ($existing && !$this->route('property_sustainability')) {
                    $validator->errors()->add('property_id', 'العقار لديه بالفعل تقييم استدامة. يمكنك تعديل التقييم الحالي.');
                }
            }

            // Validate eco score calculation consistency
            if ($this->has(['energy_efficiency_rating', 'water_efficiency_rating', 'waste_management_score', 'green_space_ratio', 'renewable_energy_percentage', 'sustainable_materials_percentage'])) {
                $calculatedEcoScore = $this->calculateEcoScore();
                if ($this->filled('eco_score') && abs($this->eco_score - $calculatedEcoScore) > 5) {
                    $validator->errors()->add('eco_score', 'الدرجة البيئية المدخلة لا تتطابق مع الحساب التلقائي. القيمة المحسوبة: ' . round($calculatedEcoScore, 1));
                }
            }

            // Validate percentage totals don't exceed reasonable limits
            $totalPercentage = $this->renewable_energy_percentage + $this->sustainable_materials_percentage;
            if ($totalPercentage > 150) {
                $validator->errors()->add('renewable_energy_percentage', 'مجموع نسب الطاقة المتجددة والمواد المستدامة مرتفع جداً');
            }
        });
    }

    /**
     * Calculate eco score based on input values
     *
     * @return float
     */
    private function calculateEcoScore(): float
    {
        $weights = [
            'energy' => 0.25,
            'water' => 0.20,
            'waste' => 0.15,
            'green_space' => 0.15,
            'renewable' => 0.15,
            'materials' => 0.10,
        ];

        $score = (
            $this->energy_efficiency_rating * $weights['energy'] +
            $this->water_efficiency_rating * $weights['water'] +
            $this->waste_management_score * $weights['waste'] +
            ($this->green_space_ratio * 100) * $weights['green_space'] +
            $this->renewable_energy_percentage * $weights['renewable'] +
            $this->sustainable_materials_percentage * $weights['materials']
        );

        // Apply carbon footprint penalty
        if ($this->carbon_footprint > 50) {
            $score -= ($this->carbon_footprint - 50) * 0.1;
        }

        return max(0, min(100, $score));
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Convert green space ratio from percentage to decimal if needed
        if ($this->has('green_space_ratio') && $this->green_space_ratio > 1) {
            $this->merge([
                'green_space_ratio' => $this->green_space_ratio / 100,
            ]);
        }

        // Auto-calculate eco score if not provided
        if (!$this->filled('eco_score') && $this->has(['energy_efficiency_rating', 'water_efficiency_rating', 'waste_management_score'])) {
            $this->merge([
                'eco_score' => $this->calculateEcoScore(),
            ]);
        }
    }
}
