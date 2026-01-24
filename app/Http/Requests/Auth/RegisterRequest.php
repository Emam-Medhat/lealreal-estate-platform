<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            // User Type
            'user_type' => ['required', 'string', 'in:user,agent,company,developer,investor,admin'],
            
            // Personal Information
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users', 'regex:/^[a-zA-Z0-9_]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20', 'unique:users', 'regex:/^[+]?[0-9\s\-\(\)]+$/'],
            'whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^[+]?[0-9\s\-\(\)]+$/'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif', 'max:2048'],
            
            // Location Information
            'country' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            
            // Preferences
            'language' => ['required', 'string', 'in:ar,en,fr'],
            'currency' => ['required', 'string', 'in:EGP,SAR,AED,USD,EUR'],
            
            // Password
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required', 'string'],
            
            // Marketing Preferences
            'marketing_consent' => ['sometimes', 'boolean'],
            'newsletter_subscribed' => ['sometimes', 'boolean'],
            'sms_notifications' => ['sometimes', 'boolean'],
            
            // Terms
            'terms' => ['required', 'accepted'],
        ];

        // Add role-specific validation rules
        $userType = $this->input('user_type');
        
        if ($userType === 'agent') {
            $rules = array_merge($rules, [
                'agent_license_number' => ['required', 'string', 'max:50'],
                'agent_license_expiry' => ['required', 'date', 'after:today'],
                'agent_company' => ['nullable', 'string', 'max:200'],
                'agent_commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            ]);
        }

        if ($userType === 'company') {
            $rules = array_merge($rules, [
                'agent_company' => ['required', 'string', 'max:200'], // Changed from company_name to match form
                'company_registration_number' => ['required', 'string', 'max:50'], // Changed from company_registration
                'company_tax_number' => ['nullable', 'string', 'max:50'],
                'company_employees_count' => ['nullable', 'integer', 'min:1'], // Changed from company_employees
            ]);
        }

        if ($userType === 'developer') {
            $rules = array_merge($rules, [
                'developer_name' => ['required', 'string', 'max:200'],
                'developer_certification' => ['nullable', 'string', 'max:100'],
            ]);
        }

        if ($userType === 'investor') {
            $rules = array_merge($rules, [
                'investor_type' => ['required', 'string', 'in:individual,institutional,fund'],
                'investment_portfolio_value' => ['nullable', 'numeric', 'min:0'],
            ]);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            // User Type
            'user_type.required' => 'يرجى اختيار نوع الحساب',
            'user_type.in' => 'نوع الحساب المختار غير صالح',
            
            // Personal Information
            'first_name.required' => 'الاسم الأول مطلوب',
            'first_name.max' => 'الاسم الأول يجب ألا يتجاوز 100 حرف',
            'last_name.required' => 'الاسم الأخير مطلوب',
            'last_name.max' => 'الاسم الأخير يجب ألا يتجاوز 100 حرف',
            'username.required' => 'اسم المستخدم مطلوب',
            'username.max' => 'اسم المستخدم يجب ألا يتجاوز 50 حرفاً',
            'username.unique' => 'اسم المستخدم هذا مستخدم بالفعل',
            'username.regex' => 'اسم المستخدم يمكن أن يحتوي على أحرف وأرقام وشرطات سفلية فقط',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'يرجى تقديم بريد إلكتروني صالح',
            'email.max' => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرفاً',
            'email.unique' => 'هذا البريد الإلكتروني مسجل بالفعل',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرفاً',
            'phone.unique' => 'رقم الهاتف هذا مستخدم بالفعل',
            'phone.regex' => 'يرجى تقديم رقم هاتف صالح',
            'whatsapp.regex' => 'يرجى تقديم رقم واتساب صالح',
            'date_of_birth.required' => 'تاريخ الميلاد مطلوب',
            'date_of_birth.date' => 'يرجى تقديم تاريخ صالح',
            'date_of_birth.before' => 'تاريخ الميلاد يجب أن يكون في الماضي',
            'gender.required' => 'النوع مطلوب',
            'gender.in' => 'النوع المختار غير صالح',
            'avatar.image' => 'الملف يجب أن يكون صورة',
            'avatar.mimes' => 'الصورة يجب أن تكون من نوع: jpeg, jpg, png, gif',
            'avatar.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
            
            // Location Information
            'country.required' => 'الدولة مطلوبة',
            'country.max' => 'اسم الدولة يجب ألا يتجاوز 100 حرف',
            'city.required' => 'المدينة مطلوبة',
            'city.max' => 'اسم المدينة يجب ألا يتجاوز 100 حرف',
            'state.max' => 'اسم المحافظة يجب ألا يتجاوز 100 حرف',
            'postal_code.max' => 'الرمز البريدي يجب ألا يتجاوز 20 حرفاً',
            'address.max' => 'العنوان يجب ألا يتجاوز 500 حرف',
            
            // Preferences
            'language.required' => 'اللغة مطلوبة',
            'language.in' => 'اللغة المختارة غير صالحة',
            'currency.required' => 'العملة مطلوبة',
            'currency.in' => 'العملة المختارة غير صالحة',
            
            // Password
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور لا يتطابق',
            'password_confirmation.required' => 'تأكيد كلمة المرور مطلوب',
            
            // Marketing Preferences
            'marketing_consent.boolean' => 'قيمة موافقة التسويق غير صالحة',
            'newsletter_subscribed.boolean' => 'قيمة الاشتراك في النشرة غير صالحة',
            'sms_notifications.boolean' => 'قيمة إشعارات SMS غير صالحة',
            
            // Terms
            'terms.required' => 'يجب الموافقة على الشروط والأحكام',
            'terms.accepted' => 'يجب الموافقة على الشروط والأحكام',
            
            // Agent Specific
            'agent_license_number.required' => 'رقم ترخيص الوكيل مطلوب',
            'agent_license_number.max' => 'رقم الترخيص يجب ألا يتجاوز 50 حرفاً',
            'agent_license_expiry.required' => 'تاريخ انتهاء الترخيص مطلوب',
            'agent_license_expiry.date' => 'يرجى تقديم تاريخ صالح',
            'agent_license_expiry.after' => 'تاريخ انتهاء الترخيص يجب أن يكون في المستقبل',
            'agent_company.max' => 'اسم الشركة يجب ألا يتجاوز 200 حرف',
            'agent_commission_rate.numeric' => 'نسبة العمولة يجب أن تكون رقماً',
            'agent_commission_rate.min' => 'نسبة العمولة يجب أن تكون 0 على الأقل',
            'agent_commission_rate.max' => 'نسبة العمولة يجب ألا تتجاوز 100%',
            
            // Company Specific
            'company_name.required' => 'اسم الشركة مطلوب',
            'company_name.max' => 'اسم الشركة يجب ألا يتجاوز 200 حرف',
            'company_registration.required' => 'السجل التجاري مطلوب',
            'company_registration.max' => 'السجل التجاري يجب ألا يتجاوز 50 حرفاً',
            'company_tax_number.max' => 'الرقم الضريبي يجب ألا يتجاوز 50 حرفاً',
            'company_employees.integer' => 'عدد الموظفين يجب أن يكون رقماً صحيحاً',
            'company_employees.min' => 'عدد الموظفين يجب أن يكون 1 على الأقل',
            
            // Developer Specific
            'developer_name.required' => 'اسم المطور مطلوب',
            'developer_name.max' => 'اسم المطور يجب ألا يتجاوز 200 حرف',
            'developer_certification.max' => 'شهادة المطور يجب ألا تتجاوز 100 حرف',
            
            // Investor Specific
            'investor_type.required' => 'نوع المستثمر مطلوب',
            'investor_type.in' => 'نوع المستثمر غير صالح',
            'investment_portfolio_value.numeric' => 'قيمة المحفظة الاستثمارية يجب أن تكون رقماً',
            'investment_portfolio_value.min' => 'قيمة المحفظة الاستثمارية يجب أن تكون 0 على الأقل',
        ];
    }

    public function createUserData(): array
    {
        return [
            'username' => $this->validated('username'),
            'first_name' => $this->validated('first_name'),
            'last_name' => $this->validated('last_name'),
            'full_name' => trim($this->validated('first_name') . ' ' . $this->validated('last_name')),
            'email' => $this->validated('email'),
            'phone' => $this->validated('phone'),
            'password' => Hash::make($this->validated('password')),
            'user_type' => $this->validated('user_type'),
            'email_verified_at' => null,
        ];
    }
}
