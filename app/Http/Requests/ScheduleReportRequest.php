<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScheduleReportRequest extends FormRequest
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
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly,custom',
            'schedule_time' => 'required|date_format:H:i',
            'timezone' => 'nullable|timezone',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|email',
            'next_run_at' => 'nullable|date|after:now',
            'end_date' => 'nullable|date|after:next_run_at',
            'max_runs' => 'nullable|integer|min:1|max:365',
            'retry_on_failure' => 'nullable|boolean',
            'max_retries' => 'nullable|integer|min:1|max:10',
            'retry_delay' => 'nullable|integer|min:1|max:1440',
            'notification_settings' => 'nullable|array',
            'notification_settings.email_on_start' => 'nullable|boolean',
            'notification_settings.email_on_complete' => 'nullable|boolean',
            'notification_settings.email_on_failure' => 'nullable|boolean',
            'notification_settings.email_on_skip' => 'nullable|boolean',
            'notification_settings.sms_on_complete' => 'nullable|boolean',
            'notification_settings.sms_on_failure' => 'nullable|boolean',
            'notification_settings.webhook_url' => 'nullable|url',
            'notification_settings.webhook_events' => 'nullable|array',
            'notification_settings.webhook_events.*' => 'in:start,complete,failure,skip',
            'delivery_settings' => 'nullable|array',
            'delivery_settings.method' => 'required|in:email,ftp,cloud,webhook',
            'delivery_settings.email_subject' => 'required_if:delivery_settings.method,email|string|max:255',
            'delivery_settings.email_body' => 'nullable|string',
            'delivery_settings.ftp_host' => 'required_if:delivery_settings.method,ftp|string|max:255',
            'delivery_settings.ftp_port' => 'nullable|integer|min:1|max:65535',
            'delivery_settings.ftp_username' => 'required_if:delivery_settings.method,ftp|string|max:255',
            'delivery_settings.ftp_password' => 'required_if:delivery_settings.method,ftp|string|max:255',
            'delivery_settings.ftp_path' => 'nullable|string|max:255',
            'delivery_settings.ftp_passive' => 'nullable|boolean',
            'delivery_settings.cloud_provider' => 'required_if:delivery_settings.method,cloud|in:aws,azure,google',
            'delivery_settings.cloud_bucket' => 'required_if:delivery_settings.method,cloud|string|max:255',
            'delivery_settings.cloud_path' => 'nullable|string|max:255',
            'delivery_settings.webhook_url' => 'required_if:delivery_settings.method,webhook|url',
            'security_settings' => 'nullable|array',
            'security_settings.encrypt_report' => 'nullable|boolean',
            'security_settings.password_protect' => 'nullable|boolean',
            'security_settings.password' => 'required_if:security_settings.password_protect,true|string|min:8',
            'security_settings.expire_after_days' => 'nullable|integer|min:1|max:365',
            'security_settings.max_downloads' => 'nullable|integer|min:1|max:100',
            'custom_schedule' => 'nullable|array',
            'custom_schedule.cron_expression' => 'required_if:frequency,custom|string|max:100',
            'custom_schedule.description' => 'nullable|string|max:255',
            'conditions' => 'nullable|array',
            'conditions.min_data_count' => 'nullable|integer|min:1',
            'conditions.max_execution_time' => 'nullable|integer|min:1|max:3600',
            'conditions.skip_on_holidays' => 'nullable|boolean',
            'conditions.skip_on_weekends' => 'nullable|boolean',
            'conditions.business_hours_only' => 'nullable|boolean',
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
            'title.required' => 'عنوان التقرير المجدول مطلوب',
            'title.max' => 'العنوان يجب ألا يتجاوز 255 حرف',
            'description.max' => 'الوصف يجب ألا يتجاوز 1000 حرف',
            'template_id.required' => 'اختيار القالب مطلوب',
            'template_id.exists' => 'القالب المحدد غير موجود',
            'frequency.required' => 'اختيار التكرار مطلوب',
            'frequency.in' => 'التكرار المحدد غير صالح',
            'schedule_time.required' => 'وقت الجدولة مطلوب',
            'schedule_time.date_format' => 'الوقت يجب أن يكون بالتنسيق HH:MM',
            'timezone.timezone' => 'المنطقة الزمنية غير صالحة',
            'recipients.required' => 'قائمة المستلمين مطلوبة',
            'recipients.min' => 'يجب إضافة مستلم واحد على الأقل',
            'recipients.*.required' => 'عنوان البريد الإلكتروني مطلوب',
            'recipients.*.email' => 'عنوان البريد الإلكتروني غير صالح',
            'next_run_at.after' => 'وقت التشغيل التالي يجب أن يكون في المستقبل',
            'end_date.after' => 'تاريخ الانتهاء يجب أن يكون بعد وقت التشغيل التالي',
            'max_runs.min' => 'الحد الأقصى للتشغيل يجب أن يكون 1 أو أكثر',
            'max_runs.max' => 'الحد الأقصى للتشغيل يجب ألا يتجاوز 365',
            'retry_on_failure.boolean' => 'قيمة إعادة المحاولة غير صالحة',
            'max_retries.min' => 'الحد الأقصى لإعادة المحاولة يجب أن يكون 1 أو أكثر',
            'max_retries.max' => 'الحد الأقصى لإعادة المحاولة يجب ألا يتجاوز 10',
            'retry_delay.min' => 'تأخير إعادة المحاولة يجب أن يكون 1 أو أكثر',
            'retry_delay.max' => 'تأخير إعادة المحاولة يجب ألا يتجاوز 1440 دقيقة',
            'notification_settings.webhook_url.url' => 'رابط webhook غير صالح',
            'notification_settings.webhook_events.*.in' => 'حدث webhook غير صالح',
            'delivery_settings.method.required' => 'طريقة التسليم مطلوبة',
            'delivery_settings.method.in' => 'طريقة التسليم غير صالحة',
            'delivery_settings.email_subject.required_if' => 'عنوان البريد الإلكتروني مطلوب عند اختيار التسليم عبر البريد',
            'delivery_settings.email_subject.max' => 'عنوان البريد الإلكتروني يجب ألا يتجاوز 255 حرف',
            'delivery_settings.ftp_host.required_if' => 'مضيف FTP مطلوب عند اختيار التسليم عبر FTP',
            'delivery_settings.ftp_host.max' => 'مضيف FTP يجب ألا يتجاوز 255 حرف',
            'delivery_settings.ftp_port.min' => 'منفذ FTP يجب أن يكون 1 أو أكثر',
            'delivery_settings.ftp_port.max' => 'منفذ FTP يجب ألا يتجاوز 65535',
            'delivery_settings.ftp_username.required_if' => 'اسم مستخدم FTP مطلوب عند اختيار التسليم عبر FTP',
            'delivery_settings.ftp_username.max' => 'اسم مستخدم FTP يجب ألا يتجاوز 255 حرف',
            'delivery_settings.ftp_password.required_if' => 'كلمة مرور FTP مطلوبة عند اختيار التسليم عبر FTP',
            'delivery_settings.ftp_password.max' => 'كلمة مرور FTP يجب ألا تتجاوز 255 حرف',
            'delivery_settings.ftp_path.max' => 'مسار FTP يجب ألا يتجاوز 255 حرف',
            'delivery_settings.cloud_provider.required_if' => 'مزود السحابة مطلوب عند اختيار التسليم السحابي',
            'delivery_settings.cloud_provider.in' => 'مزود السحابة غير صالح',
            'delivery_settings.cloud_bucket.required_if' => 'حاوية السحابة مطلوبة عند اختيار التسليم السحابي',
            'delivery_settings.cloud_bucket.max' => 'حاوية السحابة يجب ألا تتجاوز 255 حرف',
            'delivery_settings.cloud_path.max' => 'مسار السحابة يجب ألا يتجاوز 255 حرف',
            'delivery_settings.webhook_url.required_if' => 'رابط webhook مطلوب عند اختيار التسليم عبر webhook',
            'security_settings.encrypt_report.boolean' => 'قيمة التشفير غير صالحة',
            'security_settings.password_protect.boolean' => 'قيمة حماية كلمة المرور غير صالحة',
            'security_settings.password.required_if' => 'كلمة المرور مطلوبة عند تفعيل حماية كلمة المرور',
            'security_settings.password.min' => 'كلمة المرور يجب أن تتكون من 8 أحرف على الأقل',
            'security_settings.expire_after_days.min' => 'مدة انتهاء الصلاحية يجب أن تكون 1 يوم على الأقل',
            'security_settings.expire_after_days.max' => 'مدة انتهاء الصلاحية يجب ألا تتجاوز 365 يوم',
            'security_settings.max_downloads.min' => 'الحد الأقصى للتحميل يجب أن يكون 1 أو أكثر',
            'security_settings.max_downloads.max' => 'الحد الأقصى للتحميل يجب ألا يتجاوز 100',
            'custom_schedule.cron_expression.required_if' => 'تعبير cron مطلوب عند اختيار جدولة مخصصة',
            'custom_schedule.cron_expression.max' => 'تعبير cron يجب ألا يتجاوز 100 حرف',
            'custom_schedule.description.max' => 'وصف الجدولة المخصصة يجب ألا يتجاوز 255 حرف',
            'conditions.min_data_count.min' => 'الحد الأدنى لعدد البيانات يجب أن يكون 1 أو أكثر',
            'conditions.max_execution_time.min' => 'الحد الأقصى لوقت التنفيذ يجب أن يكون 1 دقيقة على الأقل',
            'conditions.max_execution_time.max' => 'الحد الأقصى لوقت التنفيذ يجب ألا يتجاوز 3600 دقيقة',
            'custom_fields.*.name.required' => 'اسم الحقل المخصص مطلوب',
            'custom_fields.*.name.max' => 'اسم الحقل المخصص يجب ألا يتجاوز 255 حرف',
            'custom_fields.*.value.required' => 'قيمة الحقل المخصصة مطلوبة',
            'custom_fields.*.type.required' => 'نوع الحقل المخصص مطلوب',
            'custom_fields.*.type.in' => 'نوع الحقل المخصص غير صالح',
            'custom_fields.*.options.required_if' => 'خيارات الحقل المخصصة مطلوبة عند اختيار نوع select',
            'custom_fields.*.options.*.string' => 'خيارات الحقل المخصصة يجب أن تكون نصية',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'عنوان التقرير المجدول',
            'description' => 'الوصف',
            'template_id' => 'القالب',
            'parameters' => 'المعلمات',
            'filters' => 'الفلاتر',
            'frequency' => 'التكرار',
            'schedule_time' => 'وقت الجدولة',
            'timezone' => 'المنطقة الزمنية',
            'recipients' => 'المستلمون',
            'next_run_at' => 'وقت التشغيل التالي',
            'end_date' => 'تاريخ الانتهاء',
            'max_runs' => 'الحد الأقصى للتشغيل',
            'retry_on_failure' => 'إعادة المحاولة عند الفشل',
            'max_retries' => 'الحد الأقصى لإعادة المحاولة',
            'retry_delay' => 'تأخير إعادة المحاولة',
            'notification_settings' => 'إعدادات الإشعارات',
            'notification_settings.email_on_start' => 'إرسال بريد عند البدء',
            'notification_settings.email_on_complete' => 'إرسال بريد عند الإكتمال',
            'notification_settings.email_on_failure' => 'إرسال بريد عند الفشل',
            'notification_settings.email_on_skip' => 'إرسال بريد عند التخطي',
            'notification_settings.sms_on_complete' => 'إرسال رسالة عند الإكتمال',
            'notification_settings.sms_on_failure' => 'إرسال رسالة عند الفشل',
            'notification_settings.webhook_url' => 'رابط webhook',
            'notification_settings.webhook_events' => 'أحداث webhook',
            'delivery_settings' => 'إعدادات التسليم',
            'delivery_settings.method' => 'طريقة التسليم',
            'delivery_settings.email_subject' => 'عنوان البريد الإلكتروني',
            'delivery_settings.email_body' => 'نص البريد الإلكتروني',
            'delivery_settings.ftp_host' => 'مضيف FTP',
            'delivery_settings.ftp_port' => 'منفذ FTP',
            'delivery_settings.ftp_username' => 'اسم مستخدم FTP',
            'delivery_settings.ftp_password' => 'كلمة مرور FTP',
            'delivery_settings.ftp_path' => 'مسار FTP',
            'delivery_settings.ftp_passive' => 'FTP سلبي',
            'delivery_settings.cloud_provider' => 'مزود السحابة',
            'delivery_settings.cloud_bucket' => 'حاوية السحابة',
            'delivery_settings.cloud_path' => 'مسار السحابة',
            'delivery_settings.webhook_url' => 'رابط webhook للتسليم',
            'security_settings' => 'إعدادات الأمان',
            'security_settings.encrypt_report' => 'تشفير التقرير',
            'security_settings.password_protect' => 'حماية كلمة المرور',
            'security_settings.password' => 'كلمة المرور',
            'security_settings.expire_after_days' => 'انتهاء الصلاحية بالأيام',
            'security_settings.max_downloads' => 'الحد الأقصى للتحميل',
            'custom_schedule' => 'جدولة مخصصة',
            'custom_schedule.cron_expression' => 'تعبير cron',
            'custom_schedule.description' => 'وصف الجدولة المخصصة',
            'conditions' => 'الشروط',
            'conditions.min_data_count' => 'الحد الأدنى لعدد البيانات',
            'conditions.max_execution_time' => 'الحد الأقصى لوقت التنفيذ',
            'conditions.skip_on_holidays' => 'التخطي في العطلات',
            'conditions.skip_on_weekends' => 'التخطي في عطلات نهاية الأسبوع',
            'conditions.business_hours_only' => 'ساعات العمل فقط',
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
            'retry_on_failure' => $this->boolean('retry_on_failure', false),
            'delivery_settings.ftp_passive' => $this->boolean('delivery_settings.ftp_passive', true),
            'security_settings.encrypt_report' => $this->boolean('security_settings.encrypt_report', false),
            'security_settings.password_protect' => $this->boolean('security_settings.password_protect', false),
            'conditions.skip_on_holidays' => $this->boolean('conditions.skip_on_holidays', false),
            'conditions.skip_on_weekends' => $this->boolean('conditions.skip_on_weekends', false),
            'conditions.business_hours_only' => $this->boolean('conditions.business_hours_only', false),
            'notification_settings.email_on_start' => $this->boolean('notification_settings.email_on_start', false),
            'notification_settings.email_on_complete' => $this->boolean('notification_settings.email_on_complete', true),
            'notification_settings.email_on_failure' => $this->boolean('notification_settings.email_on_failure', true),
            'notification_settings.email_on_skip' => $this->boolean('notification_settings.email_on_skip', false),
            'notification_settings.sms_on_complete' => $this->boolean('notification_settings.sms_on_complete', false),
            'notification_settings.sms_on_failure' => $this->boolean('notification_settings.sms_on_failure', false),
        ]);

        // Set default values
        if (!$this->has('timezone')) {
            $this->merge(['timezone' => config('app.timezone')]);
        }

        if (!$this->has('max_retries')) {
            $this->merge(['max_retries' => 3]);
        }

        if (!$this->has('retry_delay')) {
            $this->merge(['retry_delay' => 5]);
        }

        // Calculate next run time if not provided
        if (!$this->has('next_run_at')) {
            $this->merge([
                'next_run_at' => $this->calculateNextRunTime()
            ]);
        }
    }

    private function calculateNextRunTime()
    {
        $frequency = $this->input('frequency');
        $scheduleTime = $this->input('schedule_time');
        $timezone = $this->input('timezone', config('app.timezone'));
        
        $now = now($timezone);
        $time = explode(':', $scheduleTime);
        
        switch ($frequency) {
            case 'daily':
                $nextRun = $now->copy()->setTime($time[0], $time[1]);
                if ($nextRun->isPast()) {
                    $nextRun->addDay();
                }
                break;
                
            case 'weekly':
                $nextRun = $now->copy()->setTime($time[0], $time[1])->next('monday');
                if ($nextRun->isPast()) {
                    $nextRun->addWeek();
                }
                break;
                
            case 'monthly':
                $nextRun = $now->copy()->setTime($time[0], $time[1])->addMonth()->startOfMonth();
                break;
                
            case 'quarterly':
                $nextRun = $now->copy()->setTime($time[0], $time[1])->addQuarter();
                break;
                
            case 'yearly':
                $nextRun = $now->copy()->setTime($time[0], $time[1])->addYear();
                break;
                
            default:
                $nextRun = $now->copy()->addDay();
        }
        
        return $nextRun->toDateTimeString();
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $validated = $validator->getData();

            // Additional validation logic
            if (isset($validated['filters']['min_price']) && isset($validated['filters']['max_price'])) {
                if ($validated['filters']['min_price'] > $validated['filters']['max_price']) {
                    $validator->errors()->add('filters.max_price', 'الحد الأقصى للسعر يجب أن يكون أكبر من أو يساوي الحد الأدنى');
                }
            }

            // Validate cron expression for custom frequency
            if ($validated['frequency'] === 'custom' && isset($validated['custom_schedule']['cron_expression'])) {
                if (!$this->isValidCronExpression($validated['custom_schedule']['cron_expression'])) {
                    $validator->errors()->add('custom_schedule.cron_expression', 'تعبير cron غير صالح');
                }
            }

            // Validate template-specific requirements
            if (isset($validated['template_id'])) {
                $template = \App\Models\ReportTemplate::find($validated['template_id']);
                if ($template) {
                    $requiredParams = $template->getRequiredParameters();
                    foreach ($requiredParams as $param) {
                        if (!isset($validated['parameters'][$param]) || empty($validated['parameters'][$param])) {
                            $validator->errors()->add('parameters.' . $param, "المعلمة '{$param}' مطلوبة لهذا القالب");
                        }
                    }
                }
            }
        });
    }

    private function isValidCronExpression($expression)
    {
        // Basic cron validation (can be enhanced with a proper cron parser)
        $parts = explode(' ', $expression);
        if (count($parts) !== 5) {
            return false;
        }

        // Validate each part (minute, hour, day, month, day_of_week)
        $validRanges = [
            0 => ['min' => 0, 'max' => 59],    // minute
            1 => ['min' => 0, 'max' => 23],    // hour
            2 => ['min' => 1, 'max' => 31],    // day
            3 => ['min' => 1, 'max' => 12],    // month
            4 => ['min' => 0, 'max' => 6],     // day of week
        ];

        foreach ($parts as $index => $part) {
            if (!$this->validateCronPart($part, $validRanges[$index])) {
                return false;
            }
        }

        return true;
    }

    private function validateCronPart($part, $range)
    {
        // Handle wildcards
        if ($part === '*') {
            return true;
        }

        // Handle ranges (e.g., 1-5)
        if (strpos($part, '-') !== false) {
            list($min, $max) = explode('-', $part);
            return is_numeric($min) && is_numeric($max) && 
                   $min >= $range['min'] && $max <= $range['max'] && $min <= $max;
        }

        // Handle step values (e.g., */5)
        if (strpos($part, '/') !== false) {
            list($base, $step) = explode('/', $part);
            if ($base !== '*') {
                return false;
            }
            return is_numeric($step) && $step > 0;
        }

        // Handle comma-separated values (e.g., 1,3,5)
        if (strpos($part, ',') !== false) {
            $values = explode(',', $part);
            foreach ($values as $value) {
                if (!is_numeric($value) || $value < $range['min'] || $value > $range['max']) {
                    return false;
                }
            }
            return true;
        }

        // Handle single value
        return is_numeric($part) && $part >= $range['min'] && $part <= $range['max'];
    }
}
