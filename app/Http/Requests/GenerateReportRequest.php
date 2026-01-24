<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'template_id' => 'required|exists:report_templates,id',
            'parameters' => 'nullable|array',
            'parameters.*' => 'string',
            'filters' => 'nullable|array',
            'filters.property_type' => 'nullable|in:apartment,house,villa,land,commercial',
            'filters.status' => 'nullable|in:active,sold,pending,inactive',
            'filters.min_price' => 'nullable|numeric|min:0',
            'filters.max_price' => 'nullable|numeric|min:0',
            'filters.location' => 'nullable|string|max:255',
            'filters.features' => 'nullable|array',
            'filters.features.*' => 'string',
            'date_range' => 'nullable|array',
            'date_range.start' => 'nullable|date|required_with:date_range.end',
            'date_range.end' => 'nullable|date|required_with:date_range.start|after_or_equal:date_range.start',
            'format' => ['required', Rule::in(['pdf', 'excel', 'csv', 'html'])],
            'include_charts' => 'nullable|boolean',
            'include_details' => 'nullable|boolean',
            'include_summary' => 'nullable|boolean',
            'include_recommendations' => 'nullable|boolean',
            'include_forecasts' => 'nullable|boolean',
            'include_comparisons' => 'nullable|boolean',
            'schedule' => 'nullable|array',
            'schedule.frequency' => 'nullable|in:daily,weekly,monthly,quarterly,yearly',
            'schedule.time' => 'nullable|date_format:H:i',
            'schedule.recipients' => 'nullable|array',
            'schedule.recipients.*' => 'email',
            'export_options' => 'nullable|array',
            'export_options.page_size' => 'nullable|in:A4,A3,Letter,Legal',
            'export_options.orientation' => 'nullable|in:portrait,landscape',
            'export_options.margin_top' => 'nullable|numeric|min:0|max:100',
            'export_options.margin_bottom' => 'nullable|numeric|min:0|max:100',
            'export_options.margin_left' => 'nullable|numeric|min:0|max:100',
            'export_options.margin_right' => 'nullable|numeric|min:0|max:100',
            'export_options.include_header' => 'nullable|boolean',
            'export_options.include_footer' => 'nullable|boolean',
            'export_options.include_page_numbers' => 'nullable|boolean',
            'export_options.watermark' => 'nullable|string|max:100',
            'notification_settings' => 'nullable|array',
            'notification_settings.email_on_complete' => 'nullable|boolean',
            'notification_settings.email_on_failure' => 'nullable|boolean',
            'notification_settings.sms_on_complete' => 'nullable|boolean',
            'notification_settings.webhook_url' => 'nullable|url',
            'access_permissions' => 'nullable|array',
            'access_permissions.can_view' => 'nullable|array',
            'access_permissions.can_view.*' => 'exists:users,id',
            'access_permissions.can_edit' => 'nullable|array',
            'access_permissions.can_edit.*' => 'exists:users,id',
            'access_permissions.can_download' => 'nullable|array',
            'access_permissions.can_download.*' => 'exists:users,id',
            'access_permissions.expires_at' => 'nullable|date|after:now',
            'custom_fields' => 'nullable|array',
            'custom_fields.*.name' => 'required|string|max:255',
            'custom_fields.*.value' => 'required|string',
            'custom_fields.*.type' => 'required|in:text,number,date,boolean,select',
            'custom_fields.*.options' => 'required_if:custom_fields.*.type,select|array',
            'custom_fields.*.options.*' => 'string',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'عنوان التقرير مطلوب',
            'title.max' => 'العنوان يجب ألا يتجاوز 255 حرف',
            'description.max' => 'الوصف يجب ألا يتجاوز 1000 حرف',
            'template_id.required' => 'اختيار القالب مطلوب',
            'template_id.exists' => 'القالب المحدد غير موجود',
            'date_range.start.required_with' => 'تاريخ البداية مطلوب عند تحديد تاريخ النهاية',
            'date_range.end.required_with' => 'تاريخ النهاية مطلوب عند تحديد تاريخ البداية',
            'date_range.end.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
            'format.required' => 'اختيار التنسيق مطلوب',
            'format.in' => 'التنسيق المحدد غير صالح',
            'filters.property_type.in' => 'نوع العقار غير صالح',
            'filters.status.in' => 'الحالة غير صالحة',
            'filters.min_price.numeric' => 'الحد الأدنى للسعر يجب أن يكون رقماً',
            'filters.min_price.min' => 'الحد الأدنى للسعر يجب أن يكون 0 أو أكثر',
            'filters.max_price.numeric' => 'الحد الأقصى للسعر يجب أن يكون رقماً',
            'filters.max_price.min' => 'الحد الأقصى للسعر يجب أن يكون 0 أو أكثر',
            'schedule.frequency.in' => 'التكرار غير صالح',
            'schedule.time.date_format' => 'الوقت يجب أن يكون بالتنسيق HH:MM',
            'schedule.recipients.*.email' => 'عنوان البريد الإلكتروني غير صالح',
            'export_options.page_size.in' => 'حجم الصفحة غير صالح',
            'export_options.orientation.in' => 'الاتجاه غير صالح',
            'export_options.margin_top.numeric' => 'الهامش العلوي يجب أن يكون رقماً',
            'export_options.margin_top.min' => 'الهامش العلوي يجب أن يكون 0 أو أكثر',
            'export_options.margin_top.max' => 'الهامش العلوي يجب ألا يتجاوز 100',
            'export_options.margin_bottom.numeric' => 'الهامش السفلي يجب أن يكون رقماً',
            'export_options.margin_bottom.min' => 'الهامش السفلي يجب أن يكون 0 أو أكثر',
            'export_options.margin_bottom.max' => 'الهامش السفلي يجب ألا يتجاوز 100',
            'export_options.margin_left.numeric' => 'الهامش الأيسر يجب أن يكون رقماً',
            'export_options.margin_left.min' => 'الهامش الأيسر يجب أن يكون 0 أو أكثر',
            'export_options.margin_left.max' => 'الهامش الأيسر يجب ألا يتجاوز 100',
            'export_options.margin_right.numeric' => 'الهامش الأيمن يجب أن يكون رقماً',
            'export_options.margin_right.min' => 'الهامش الأيمن يجب أن يكون 0 أو أكثر',
            'export_options.margin_right.max' => 'الهامش الأيمن يجب ألا يتجاوز 100',
            'notification_settings.webhook_url.url' => 'رابط webhook غير صالح',
            'access_permissions.can_view.*.exists' => 'المستخدم المحدد للعرض غير موجود',
            'access_permissions.can_edit.*.exists' => 'المستخدم المحدد للتعديل غير موجود',
            'access_permissions.can_download.*.exists' => 'المستخدم المحدد للتحميل غير موجود',
            'access_permissions.expires_at.after' => 'تاريخ انتهاء الصلاحية يجب أن يكون في المستقبل',
            'custom_fields.*.name.required' => 'اسم الحقل المخصص مطلوب',
            'custom_fields.*.name.max' => 'اسم الحقل المخصص يجب ألا يتجاوز 255 حرف',
            'custom_fields.*.value.required' => 'قيمة الحقل المخصص مطلوبة',
            'custom_fields.*.type.required' => 'نوع الحقل المخصص مطلوب',
            'custom_fields.*.type.in' => 'نوع الحقل المخصص غير صالح',
            'custom_fields.*.options.required_if' => 'خيارات الحقل المخصصة مطلوبة عند اختيار نوع select',
            'custom_fields.*.options.*.string' => 'خيارات الحقل المخصصة يجب أن تكون نصية',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'عنوان التقرير',
            'description' => 'الوصف',
            'template_id' => 'القالب',
            'parameters' => 'المعلمات',
            'filters' => 'الفلاتر',
            'filters.property_type' => 'نوع العقار',
            'filters.status' => 'الحالة',
            'filters.min_price' => 'الحد الأدنى للسعر',
            'filters.max_price' => 'الحد الأقصى للسعر',
            'filters.location' => 'الموقع',
            'filters.features' => 'المميزات',
            'date_range' => 'نطاق التاريخ',
            'date_range.start' => 'تاريخ البداية',
            'date_range.end' => 'تاريخ النهاية',
            'format' => 'التنسيق',
            'include_charts' => 'تضمين الرسوم البيانية',
            'include_details' => 'تضمين التفاصيل',
            'include_summary' => 'تضمين الملخص',
            'include_recommendations' => 'تضمين التوصيات',
            'include_forecasts' => 'تضمين التنبؤات',
            'include_comparisons' => 'تضمين المقارنات',
            'schedule' => 'الجدولة',
            'schedule.frequency' => 'التكرار',
            'schedule.time' => 'الوقت',
            'schedule.recipients' => 'المستلمون',
            'export_options' => 'خيارات التصدير',
            'export_options.page_size' => 'حجم الصفحة',
            'export_options.orientation' => 'الاتجاه',
            'export_options.margin_top' => 'الهامش العلوي',
            'export_options.margin_bottom' => 'الهامش السفلي',
            'export_options.margin_left' => 'الهامش الأيسر',
            'export_options.margin_right' => 'الهامش الأيمن',
            'export_options.include_header' => 'تضمين الرأس',
            'export_options.include_footer' => 'تضمين التذييل',
            'export_options.include_page_numbers' => 'تضمين أرقام الصفحات',
            'export_options.watermark' => 'العلامة المائية',
            'notification_settings' => 'إعدادات الإشعارات',
            'notification_settings.email_on_complete' => 'إرسال بريد عند الإكتمال',
            'notification_settings.email_on_failure' => 'إرسال بريد عند الفشل',
            'notification_settings.sms_on_complete' => 'إرسال رسالة عند الإكتمال',
            'notification_settings.webhook_url' => 'رابط webhook',
            'access_permissions' => 'صلاحيات الوصول',
            'access_permissions.can_view' => 'صلاحية العرض',
            'access_permissions.can_edit' => 'صلاحية التعديل',
            'access_permissions.can_download' => 'صلاحية التحميل',
            'access_permissions.expires_at' => 'تاريخ انتهاء الصلاحية',
            'custom_fields' => 'الحقول المخصصة',
            'custom_fields.*.name' => 'اسم الحقل',
            'custom_fields.*.value' => 'قيمة الحقل',
            'custom_fields.*.type' => 'نوع الحقل',
            'custom_fields.*.options' => 'خيارات الحقل',
        ];
    }

    protected function prepareForValidation()
    {
        // Convert boolean values
        $this->merge([
            'include_charts' => $this->boolean('include_charts', false),
            'include_details' => $this->boolean('include_details', false),
            'include_summary' => $this->boolean('include_summary', false),
            'include_recommendations' => $this->boolean('include_recommendations', false),
            'include_forecasts' => $this->boolean('include_forecasts', false),
            'include_comparisons' => $this->boolean('include_comparisons', false),
            'export_options.include_header' => $this->boolean('export_options.include_header', true),
            'export_options.include_footer' => $this->boolean('export_options.include_footer', true),
            'export_options.include_page_numbers' => $this->boolean('export_options.include_page_numbers', true),
            'notification_settings.email_on_complete' => $this->boolean('notification_settings.email_on_complete', true),
            'notification_settings.email_on_failure' => $this->boolean('notification_settings.email_on_failure', true),
            'notification_settings.sms_on_complete' => $this->boolean('notification_settings.sms_on_complete', false),
        ]);

        // Set default export options if not provided
        if (!$this->has('export_options')) {
            $this->merge([
                'export_options' => [
                    'page_size' => 'A4',
                    'orientation' => 'portrait',
                    'margin_top' => 20,
                    'margin_bottom' => 20,
                    'margin_left' => 20,
                    'margin_right' => 20,
                    'include_header' => true,
                    'include_footer' => true,
                    'include_page_numbers' => true,
                ]
            ]);
        }

        // Set default notification settings if not provided
        if (!$this->has('notification_settings')) {
            $this->merge([
                'notification_settings' => [
                    'email_on_complete' => true,
                    'email_on_failure' => true,
                    'sms_on_complete' => false,
                ]
            ]);
        }
    }

    public function validated()
    {
        $validated = parent::validated();

        // Additional validation logic
        if (isset($validated['filters']['min_price']) && isset($validated['filters']['max_price'])) {
            if ($validated['filters']['min_price'] > $validated['filters']['max_price']) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'filters.max_price' => 'الحد الأقصى للسعر يجب أن يكون أكبر من أو يساوي الحد الأدنى'
                ]);
            }
        }

        // Validate template-specific requirements
        if (isset($validated['template_id'])) {
            $template = \App\Models\ReportTemplate::find($validated['template_id']);
            if ($template) {
                $requiredParams = $template->getRequiredParameters();
                foreach ($requiredParams as $param) {
                    if (!isset($validated['parameters'][$param]) || empty($validated['parameters'][$param])) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'parameters.' . $param => "المعلمة '{$param}' مطلوبة لهذا القالب"
                        ]);
                    }
                }
            }
        }

        return $validated;
    }
}
