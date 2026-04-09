<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitComplaintRequest extends FormRequest
{
    public function authorize()
    {
        return \Illuminate\Support\Facades\Auth::check();
    }

    public function rules()
    {
        return [
            'complaintable_type' => 'required|string|in:App\Models\Property,App\Models\Agent,App\Models\User',
            'complaintable_id' => 'required|integer|exists:complaintable_type,id',
            'type' => 'required|string|in:service_quality,property_issue,payment_dispute,communication,contract_violation,safety_concern,discrimination,fraud,other',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50|max:5000',
            'urgency_level' => 'required|string|in:low,medium,high,critical',
            'expected_resolution' => 'nullable|string|max:1000',
            'contact_preference' => 'required|string|in:email,phone,sms,whatsapp,in_person',
            'contact_details' => 'required|string|max:255',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:5120'
        ];
    }

    public function messages()
    {
        return [
            'complaintable_type.required' => 'يجب تحديد نوع العنصر المراد الإبلاغ عنه',
            'complaintable_type.in' => 'نوع العنصر غير صالح',
            'complaintable_id.required' => 'يجب تحديد معرف العنصر',
            'complaintable_id.exists' => 'العنصر المحدد غير موجود',
            'type.required' => 'يجب تحديد نوع الشكوى',
            'type.in' => 'نوع الشكوى غير صالح',
            'title.required' => 'حقل العنوان مطلوب',
            'title.max' => 'يجب ألا يزيد العنوان عن 255 حرفاً',
            'description.required' => 'حقل الوصف مطلوب',
            'description.min' => 'يجب أن يحتوي الوصف على الأقل 50 حرفاً',
            'description.max' => 'يجب ألا يزيد الوصف عن 5000 حرف',
            'urgency_level.required' => 'يجب تحديد مستوى الإلحاح',
            'urgency_level.in' => 'مستوى الإلحاح غير صالح',
            'expected_resolution.max' => 'يجب ألا يزيد الحل المتوقع عن 1000 حرف',
            'contact_preference.required' => 'يجب تحديد طريقة التواصل المفضلة',
            'contact_preference.in' => 'طريقة التواصل غير صالحة',
            'contact_details.required' => 'يجب إدخال تفاصيل التواصل',
            'contact_details.max' => 'يجب ألا تزيد تفاصيل التواصل عن 255 حرفاً',
            'attachments.*.file' => 'يجب أن يكون المرفق ملفاً',
            'attachments.*.mimes' => 'يجب أن يكون المرفق من نوع: jpeg, png, jpg, gif, pdf, doc, docx',
            'attachments.*.max' => 'يجب ألا يزيد حجم المرفق عن 5 ميجابايت'
        ];
    }

    public function attributes()
    {
        return [
            'complaintable_type' => 'نوع العنصر',
            'complaintable_id' => 'معرف العنصر',
            'type' => 'نوع الشكوى',
            'title' => 'العنوان',
            'description' => 'الوصف',
            'urgency_level' => 'مستوى الإلحاح',
            'expected_resolution' => 'الحل المتوقع',
            'contact_preference' => 'طريقة التواصل المفضلة',
            'contact_details' => 'تفاصيل التواصل',
            'attachments' => 'المرفقات'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate complaintable exists
            $complaintableClass = $this->complaintable_type;
            if (class_exists($complaintableClass)) {
                $complaintable = $complaintableClass::find($this->complaintable_id);
                if (!$complaintable) {
                    $validator->errors()->add('complaintable_id', 'العنصر المحدد غير موجود');
                }
            }

            // Validate contact details based on preference
            if ($this->contact_preference === 'email') {
                if (!filter_var($this->contact_details, FILTER_VALIDATE_EMAIL)) {
                    $validator->errors()->add('contact_details', 'البريد الإلكتروني غير صالح');
                }
            } elseif ($this->contact_preference === 'phone' || $this->contact_preference === 'sms' || $this->contact_preference === 'whatsapp') {
                if (!preg_match('/^[0-9\+\-\s\(\)]+$/', $this->contact_details)) {
                    $validator->errors()->add('contact_details', 'رقم الهاتف غير صالح');
                }
            }

            // Check for duplicate complaints
            if (\Illuminate\Support\Facades\Auth::check()) {
                $existingComplaint = \App\Models\Complaint::where('user_id', \Illuminate\Support\Facades\Auth::id())
                    ->where('complaintable_type', $this->complaintable_type)
                    ->where('complaintable_id', $this->complaintable_id)
                    ->where('type', $this->type)
                    ->where('description', $this->description)
                    ->where('status', 'pending')
                    ->first();

                if ($existingComplaint) {
                    $validator->errors()->add('description', 'لقد قمت بتقديم هذه الشكوى بالفعل');
                }
            }
        });
    }

    protected function prepareForValidation()
    {
        // Clean and normalize contact details
        if ($this->has('contact_details')) {
            $this->merge([
                'contact_details' => trim($this->contact_details)
            ]);
        }
    }
}
