<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitFilingRequest extends FormRequest
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
            'property_tax_id' => 'required|exists:property_taxes,id',
            'filing_type' => 'required|string|in:annual,quarterly,amended',
            'tax_year' => 'required|integer|min:2020|max:' . now()->year,
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
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
            'property_tax_id.required' => 'الضريبة العقارية مطلوبة',
            'property_tax_id.exists' => 'الضريبة العقارية المحددة غير موجودة',
            'filing_type.required' => 'نوع الإقرار مطلوب',
            'filing_type.in' => 'نوع الإقرار يجب أن يكون سنوي، ربع سنوي، أو معدل',
            'tax_year.required' => 'السنة الضريبية مطلوبة',
            'tax_year.integer' => 'السنة الضريبية يجب أن تكون رقماً',
            'tax_year.min' => 'السنة الضريبية يجب أن تكون 2020 أو أكثر',
            'tax_year.max' => 'السنة الضريبية لا يمكن أن تكون في المستقبل',
            'attachments.array' => 'المرفقات يجب أن تكون مصفوفة',
            'attachments.*.file' => 'كل مرفق يجب أن يكون ملفاً',
            'attachments.*.mimes' => 'المرفقات يجب أن تكون من نوع: pdf, doc, docx, jpg, jpeg, png',
            'attachments.*.max' => 'حجم كل مرفق يجب أن لا يزيد عن 10 ميجابايت',
            'notes.string' => 'الملاحظات يجب أن تكون نصاً',
            'notes.max' => 'الملاحظات يجب أن لا تزيد عن 1000 حرف',
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
            'property_tax_id' => 'الضريبة العقارية',
            'filing_type' => 'نوع الإقرار',
            'tax_year' => 'السنة الضريبية',
            'attachments' => 'المرفقات',
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
            if ($this->filing_type === 'amended') {
                // Check if there's an original filing for this property and year
                $originalFiling = \App\Models\TaxFiling::where('property_tax_id', $this->property_tax_id)
                    ->where('tax_year', $this->tax_year)
                    ->where('filing_type', '!=', 'amended')
                    ->first();

                if (!$originalFiling) {
                    $validator->errors()->add('filing_type', 'لا يمكن تقديم إقرار معدل بدون وجود إقرار أصلي');
                }
            }
        });
    }
}
