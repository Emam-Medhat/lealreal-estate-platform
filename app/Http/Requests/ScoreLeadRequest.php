<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScoreLeadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'score' => 'required|integer|min:0|max:100',
            'factors' => 'required|array',
            'factors.*.name' => 'required|string|max:255',
            'factors.*.weight' => 'required|integer|min:0|max:100',
            'factors.*.value' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'score.required' => 'حقل التقييم مطلوب',
            'score.integer' => 'التقييم يجب أن يكون رقم صحيح',
            'score.min' => 'التقييم يجب أن يكون بين 0 و 100',
            'score.max' => 'التقييم يجب أن يكون بين 0 و 100',
            'factors.required' => 'حقل العوامل مطلوب',
            'factors.array' => 'العوامل يجب أن تكون مصفوفة',
            'factors.*.name.required' => 'اسم العامل مطلوب',
            'factors.*.weight.required' => 'وزن العامل مطلوب',
            'factors.*.weight.min' => 'وزن العامل يجب أن يكون بين 0 و 100',
            'factors.*.weight.max' => 'وزن العامل يجب أن يكون بين 0 و 100',
            'factors.*.value.required' => 'قيمة العامل مطلوبة',
            'factors.*.value.min' => 'قيمة العامل يجب أن تكون بين 0 و 100',
            'factors.*.value.max' => 'قيمة العامل يجب أن تكون بين 0 و 100',
        ];
    }
}
