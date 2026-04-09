<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'report_id' => 'required|exists:reports,id',
            'cron_expression' => 'required|string|max:100',
            'parameters' => 'nullable|array',
            'recipients' => 'required|array',
            'recipients.*' => 'required|email',
            'delivery_method' => 'required|string|in:email,download,webhook',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم الجدول مطلوب',
            'name.max' => 'اسم الجدول يجب ألا يتجاوز 255 حرفًا',
            'description.max' => 'الوصف يجب ألا يتجاوز 1000 حرف',
            'report_id.required' => 'التقرير مطلوب',
            'report_id.exists' => 'التقرير المحدد غير موجود',
            'cron_expression.required' => 'تعبئة كرون مطلوبة',
            'recipients.required' => 'المستلمون مطلوبون',
            'recipients.*.email' => 'جميع المستلمين يجب أن يكونوا بريدًا إلكترونيًا صحيحًا',
            'delivery_method.required' => 'طريقة التسليم مطلوبة',
            'delivery_method.in' => 'طريقة التسليم يجب أن تكون: email, download, أو webhook',
        ];
    }
}
