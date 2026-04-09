<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessRentPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card,online,other',
            'payment_date' => 'required|date|before_or_equal:today',
            'transaction_id' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'payment_confirmation' => 'required|boolean',
            'bank_name' => 'required_if:payment_method,bank_transfer,check|string|max:255',
            'account_number' => 'required_if:payment_method,bank_transfer|string|max:255',
            'check_number' => 'required_if:payment_method,check|string|max:255',
            'card_last_four' => 'required_if:payment_method,credit_card|string|max:4',
            'payment_gateway' => 'required_if:payment_method,online|string|max:255',
            'gateway_transaction_id' => 'required_if:payment_method,online|string|max:255',
            'partial_payment' => 'boolean',
            'remaining_amount' => 'required_if:partial_payment,1|numeric|min:0',
            'next_payment_date' => 'required_if:partial_payment,1|date|after:today',
            'late_fee_waived' => 'boolean',
            'late_fee_waiver_reason' => 'required_if:late_fee_waived,1|string|max:500',
            'discount_applied' => 'boolean',
            'discount_amount' => 'required_if:discount_applied,1|numeric|min:0',
            'discount_reason' => 'required_if:discount_applied,1|string|max:500',
            'payment_collected_by' => 'nullable|string|max:255',
            'collection_method' => 'nullable|string|max:255',
            'verification_required' => 'boolean',
            'verification_documents' => 'required_if:verification_required,1|array',
            'verification_documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'حقل المبلغ مطلوب',
            'amount.numeric' => 'المبلغ يجب أن يكون رقماً',
            'amount.min' => 'المبلغ يجب أن يكون 0 أو أكثر',
            'payment_method.required' => 'حقل طريقة الدفع مطلوب',
            'payment_method.in' => 'طريقة الدفع المحددة غير صالحة',
            'payment_date.required' => 'حقل تاريخ الدفع مطلوب',
            'payment_date.before_or_equal' => 'تاريخ الدفع يجب أن يكون اليوم أو قبل',
            'transaction_id.string' => 'رقم المعاملة يجب أن يكون نصاً',
            'transaction_id.max' => 'رقم المعاملة يجب ألا يتجاوز 255 حرفاً',
            'receipt_number.string' => 'رقم الإيصال يجب أن يكون نصاً',
            'receipt_number.max' => 'رقم الإيصال يجب ألا يتجاوز 255 حرفاً',
            'notes.string' => 'الملاحظات يجب أن تكون نصاً',
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرف',
            'payment_confirmation.required' => 'حقل تأكيد الدفع مطلوب',
            'payment_confirmation.boolean' => 'حقل تأكيد الدفع يجب أن يكون صح أو خطأ',
            'bank_name.required_if' => 'حقل اسم البنك مطلوب عند الدفع بالتحويل البنكي أو الشيك',
            'bank_name.string' => 'اسم البنك يجب أن يكون نصاً',
            'bank_name.max' => 'اسم البنك يجب ألا يتجاوز 255 حرفاً',
            'account_number.required_if' => 'حقل رقم الحساب مطلوب عند الدفع بالتحويل البنكي',
            'account_number.string' => 'رقم الحساب يجب أن يكون نصاً',
            'account_number.max' => 'رقم الحساب يجب ألا يتجاوز 255 حرفاً',
            'check_number.required_if' => 'حقل رقم الشيك مطلوب عند الدفع بالشيك',
            'check_number.string' => 'رقم الشيك يجب أن يكون نصاً',
            'check_number.max' => 'رقم الشيك يجب ألا يتجاوز 255 حرفاً',
            'card_last_four.required_if' => 'حقل آخر 4 أرقام للبطاقة مطلوب عند الدفع بالبطاقة الائتمانية',
            'card_last_four.string' => 'آخر 4 أرقام للبطاقة يجب أن تكون نصاً',
            'card_last_four.max' => 'آخر 4 أرقام للبطاقة يجب أن تكون 4 أرقام',
            'payment_gateway.required_if' => 'حقل بوابة الدفع مطلوب عند الدفع عبر الإنترنت',
            'payment_gateway.string' => 'بوابة الدفع يجب أن تكون نصاً',
            'payment_gateway.max' => 'بوابة الدفع يجب ألا تتجاوز 255 حرفاً',
            'gateway_transaction_id.required_if' => 'حقل رقم معاملة البوابة مطلوب عند الدفع عبر الإنترنت',
            'gateway_transaction_id.string' => 'رقم معاملة البوابة يجب أن يكون نصاً',
            'gateway_transaction_id.max' => 'رقم معاملة البوابة يجب ألا يتجاوز 255 حرفاً',
            'partial_payment.boolean' => 'حقل الدفع الجزئي يجب أن يكون صح أو خطأ',
            'remaining_amount.required_if' => 'حقل المبلغ المتبقي مطلوب عند الدفع الجزئي',
            'remaining_amount.numeric' => 'المبلغ المتبقي يجب أن يكون رقماً',
            'remaining_amount.min' => 'المبلغ المتبقي يجب أن يكون 0 أو أكثر',
            'next_payment_date.required_if' => 'حقل تاريخ الدفع التالي مطلوب عند الدفع الجزئي',
            'next_payment_date.after' => 'تاريخ الدفع التالي يجب أن يكون بعد اليوم',
            'late_fee_waived.boolean' => 'حقل إلغاء رسوم التأخير يجب أن يكون صح أو خطأ',
            'late_fee_waiver_reason.required_if' => 'حقل سبب إلغاء رسوم التأخير مطلوب عند الإلغاء',
            'late_fee_waiver_reason.string' => 'سبب إلغاء رسوم التأخير يجب أن يكون نصاً',
            'late_fee_waiver_reason.max' => 'سبب إلغاء رسوم التأخير يجب ألا يتجاوز 500 حرف',
            'discount_applied.boolean' => 'حقل تطبيق الخصم يجب أن يكون صح أو خطأ',
            'discount_amount.required_if' => 'حقل مبلغ الخصم مطلوب عند تطبيق الخصم',
            'discount_amount.numeric' => 'مبلغ الخصم يجب أن يكون رقماً',
            'discount_amount.min' => 'مبلغ الخصم يجب أن يكون 0 أو أكثر',
            'discount_reason.required_if' => 'حقل سبب الخصم مطلوب عند تطبيق الخصم',
            'discount_reason.string' => 'سبب الخصم يجب أن يكون نصاً',
            'discount_reason.max' => 'سبب الخصم يجب ألا يتجاوز 500 حرف',
            'payment_collected_by.string' => 'الشخص الذي جمع الدفع يجب أن يكون نصاً',
            'payment_collected_by.max' => 'الشخص الذي جمع الدفع يجب ألا يتجاوز 255 حرفاً',
            'collection_method.string' => 'طريقة التحصيل يجب أن تكون نصاً',
            'collection_method.max' => 'طريقة التحصيل يجب ألا تتجاوز 255 حرفاً',
            'verification_required.boolean' => 'حقل التحقق المطلوب يجب أن يكون صح أو خطأ',
            'verification_documents.required_if' => 'حقل وثائق التحقق مطلوب عند طلب التحقق',
            'verification_documents.array' => 'وثائق التحقق يجب أن تكون مصفوفة',
            'verification_documents.*.file' => 'كل وثيقة تحقق يجب أن تكون ملفاً',
            'verification_documents.*.mimes' => 'وثائق التحقق يجب أن تكون من نوع: pdf, jpg, jpeg, png',
            'verification_documents.*.max' => 'حجم كل وثيقة تحقق يجب ألا يتجاوز 5 ميجابايت',
        ];
    }

    public function attributes()
    {
        return [
            'amount' => 'المبلغ',
            'payment_method' => 'طريقة الدفع',
            'payment_date' => 'تاريخ الدفع',
            'transaction_id' => 'رقم المعاملة',
            'receipt_number' => 'رقم الإيصال',
            'notes' => 'الملاحظات',
            'payment_confirmation' => 'تأكيد الدفع',
            'bank_name' => 'اسم البنك',
            'account_number' => 'رقم الحساب',
            'check_number' => 'رقم الشيك',
            'card_last_four' => 'آخر 4 أرقام للبطاقة',
            'payment_gateway' => 'بوابة الدفع',
            'gateway_transaction_id' => 'رقم معاملة البوابة',
            'partial_payment' => 'الدفع الجزئي',
            'remaining_amount' => 'المبلغ المتبقي',
            'next_payment_date' => 'تاريخ الدفع التالي',
            'late_fee_waived' => 'إلغاء رسوم التأخير',
            'late_fee_waiver_reason' => 'سبب إلغاء رسوم التأخير',
            'discount_applied' => 'تطبيق الخصم',
            'discount_amount' => 'مبلغ الخصم',
            'discount_reason' => 'سبب الخصم',
            'payment_collected_by' => 'الشخص الذي جمع الدفع',
            'collection_method' => 'طريقة التحصيل',
            'verification_required' => 'التحقق المطلوب',
            'verification_documents' => 'وثائق التحقق',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'payment_confirmation' => $this->boolean('payment_confirmation'),
            'partial_payment' => $this->boolean('partial_payment'),
            'late_fee_waived' => $this->boolean('late_fee_waived'),
            'discount_applied' => $this->boolean('discount_applied'),
            'verification_required' => $this->boolean('verification_required'),
        ]);
    }
}
