<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteQuestRequest extends FormRequest
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
            'quest_id' => 'required|exists:property_quests,id',
            'objective_index' => 'required|integer|min:0',
            'completed' => 'required|boolean',
            'progress_data' => 'nullable|array',
            'notes' => 'nullable|string|max:500',
            'screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            'quest_id.required' => 'حقل المهمة مطلوب',
            'quest_id.exists' => 'المهمة المحددة غير موجودة',
            'objective_index.required' => 'حقل فهرس الهدف مطلوب',
            'objective_index.integer' => 'يجب أن يكون فهرس الهدف رقماً',
            'objective_index.min' => 'فهرس الهدف يجب أن يكون 0 أو أكبر',
            'completed.required' => 'حقل الإكمال مطلوب',
            'completed.boolean' => 'يجب أن يكون الإكمال قيمة منطقية',
            'progress_data.array' => 'بيانات التقدم يجب أن تكون مصفوفة',
            'notes.max' => 'الحد الأقصى للملاحظات هو 500 حرف',
            'screenshot.image' => 'يجب أن يكون لقطة صورة',
            'screenshot.mimes' => 'يجب أن يكون الملف من نوع: jpeg, png, jpg, gif',
            'screenshot.max' => 'الحد الأقصى لحجم اللقطة هو 2 ميجابايت',
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
            'quest_id' => 'المهمة',
            'objective_index' => 'فهرس الهدف',
            'completed' => 'الإكمال',
            'progress_data' => 'بيانات التقدم',
            'notes' => 'الملاحظات',
            'screenshot' => 'لقطة الشاشة',
        ];
    }
}
