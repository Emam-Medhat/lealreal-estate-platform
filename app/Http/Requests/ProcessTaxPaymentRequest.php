<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessTaxPaymentRequest extends FormRequest
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
        $rules = [
            'property_tax_id' => 'nullable|exists:property_taxes,id',
            'tax_filing_id' => 'nullable|exists:tax_filings,id',
            'amount' => 'required|numeric|min:0.01|max:999999999.99',
            'payment_method' => 'required|string|in:cash,bank_transfer,credit_card,online',
            'payment_date' => 'required|date|before_or_equal:today',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];

        // Add additional rules for completion
        if ($this->isMethod('PUT') && $this->route('taxPayment')) {
            $rules = array_merge($rules, [
                'transaction_id' => 'required|string|max:255',
                'processing_fee' => 'nullable|numeric|min:0|max:99999.99',
            ]);
        }

        // Add rules for completion step
        if ($this->has('confirmation_number')) {
            $rules = array_merge($rules, [
                'confirmation_number' => 'required|string|max:255',
                'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'property_tax_id.exists' => 'الضريبة العقارية المحددة غير موجودة',
            'tax_filing_id.exists' => 'الإقرار الضريبي المحدد غير موجود',
            'amount.required' => 'المبلغ مطلوب',
            'amount.numeric' => 'المبلغ يجب أن يكون رقماً',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من 0',
            'amount.max' => 'المبلغ يجب أن لا يزيد عن 999,999,999.99',
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_method.in' => 'طريقة الدفع يجب أن تكون: نقدي، تحويل بنكي، بطاقة ائتمان، أو إلكتروني',
            'payment_date.required' => 'تاريخ الدفع مطلوب',
            'payment_date.date' => 'تاريخ الدفع يجب أن يكون تاريخاً صالحاً',
            'payment_date.before_or_equal' => 'تاريخ الدفع لا يمكن أن يكون في المستقبل',
            'reference_number.string' => 'رقم المرجع يجب أن يكون نصاً',
            'reference_number.max' => 'رقم المرجع يجب أن لا يزيد عن 255 حرفاً',
            'notes.string' => 'الملاحظات يجب أن تكون نصاً',
            'notes.max' => 'الملاحظات يجب أن لا تزيد عن 1000 حرف',
            'transaction_id.required' => 'رقم المعاملة مطلوب',
            'transaction_id.string' => 'رقم المعاملة يجب أن يكون نصاً',
            'transaction_id.max' => 'رقم المعاملة يجب أن لا يزيد عن 255 حرفاً',
            'processing_fee.numeric' => 'رسوم المعالجة يجب أن تكون رقماً',
            'processing_fee.min' => 'رسوم المعالجة يجب أن تكون 0 أو أكثر',
            'processing_fee.max' => 'رسوم المعالجة يجب أن لا تزيد عن 99,999.99',
            'confirmation_number.required' => 'رقم التأكيد مطلوب',
            'confirmation_number.string' => 'رقم التأكيد يجب أن يكون نصاً',
            'confirmation_number.max' => 'رقم التأكيد يجب أن لا يزيد عن 255 حرفاً',
            'receipt_file.file' => 'ملف الإيصال يجب أن يكون ملفاً',
            'receipt_file.mimes' => 'ملف الإيصال يجب أن يكون من نوع: pdf, jpg, jpeg, png',
            'receipt_file.max' => 'حجم ملف الإيصال يجب أن لا يزيد عن 5 ميجابايت',
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
            'tax_filing_id' => 'الإقرار الضريبي',
            'amount' => 'المبلغ',
            'payment_method' => 'طريقة الدفع',
            'payment_date' => 'تاريخ الدفع',
            'reference_number' => 'رقم المرجع',
            'notes' => 'الملاحظات',
            'transaction_id' => 'رقم المعاملة',
            'processing_fee' => 'رسوم المعالجة',
            'confirmation_number' => 'رقم التأكيد',
            'receipt_file' => 'ملف الإيصال',
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
            // Ensure at least one of property_tax_id or tax_filing_id is provided
            if (!$this->property_tax_id && !$this->tax_filing_id) {
                $validator->errors()->add('property_tax_id', 'يج تحديد ضريبة عقارية أو إقرار ضريبي');
            }

            // Validate amount against related tax
            if ($this->property_tax_id) {
                $propertyTax = \App\Models\PropertyTax::find($this->property_tax_id);
                if ($propertyTax && $this->amount > $propertyTax->remaining_amount) {
                    $validator->errors()->add('amount', 'المبلغ يتجاوز المبلغ المتبقي للضريبة');
                }
            }

            // Validate payment date is not too far in the past
            if ($this->payment_date && $this->payment_date < now()->subMonths(6)) {
                $validator->errors()->add('payment_date', 'تاريخ الدفع لا يمكن أن يكون أقدم من 6 أشهر');
            }
        });
    }
}
