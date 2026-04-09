<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailCampaignRequest extends FormRequest
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
            'property_id' => 'required|exists:properties,id',
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'preheader' => 'nullable|string|max:150',
            'from_name' => 'required|string|max:100',
            'from_email' => 'required|email|max:255',
            'reply_to_email' => 'nullable|email|max:255',
            'campaign_type' => 'required|string|in:newsletter,promotion,announcement,follow_up,welcome,abandoned_cart,re_engagement',
            'status' => 'nullable|string|in:draft,scheduled,active,sent,completed,paused',
            'template_id' => 'nullable|exists:email_templates,id',
            'content' => 'nullable|array',
            'content.main_message' => 'nullable|string|max:5000',
            'content.call_to_action' => 'nullable|string|max:500',
            'content.personalization_tokens' => 'nullable|array',
            'content.personalization_tokens.*' => 'string|max:50',
            'html_content' => 'required|string',
            'text_content' => 'nullable|string',
            'target_audience' => 'nullable|array',
            'target_audience.segments' => 'nullable|array',
            'target_audience.segments.*' => 'string|max:100',
            'target_audience.filters' => 'nullable|array',
            'target_audience.filters.*.field' => 'required|string|max:100',
            'target_audience.filters.*.operator' => 'required|string|in:equals,not_equals,contains,not_contains,greater_than,less_than,between',
            'target_audience.filters.*.value' => 'required|string|max:255',
            'segment_criteria' => 'nullable|array',
            'segment_criteria.inclusion_rules' => 'nullable|array',
            'segment_criteria.inclusion_rules.*.field' => 'required|string|max:100',
            'segment_criteria.inclusion_rules.*.operator' => 'required|string|in:equals,not_equals,contains,not_contains,greater_than,less_than,between',
            'segment_criteria.inclusion_rules.*.value' => 'required|string|max:255',
            'segment_criteria.exclusion_rules' => 'nullable|array',
            'segment_criteria.exclusion_rules.*.field' => 'required|string|max:100',
            'segment_criteria.exclusion_rules.*.operator' => 'required|string|in:equals,not_equals,contains,not_contains,greater_than,less_than,between',
            'segment_criteria.exclusion_rules.*.value' => 'required|string|max:255',
            'personalization_settings' => 'nullable|array',
            'personalization_settings.use_first_name' => 'boolean',
            'personalization_settings.use_last_name' => 'boolean',
            'personalization_settings.use_company' => 'boolean',
            'personalization_settings.use_location' => 'boolean',
            'personalization_settings.use_preferences' => 'boolean',
            'schedule_settings' => 'nullable|array',
            'schedule_settings.send_immediately' => 'boolean',
            'schedule_settings.scheduled_at' => 'nullable|date|after:now',
            'schedule_settings.timezone' => 'nullable|string|max:50',
            'schedule_settings.send_in_local_time' => 'boolean',
            'sending_settings' => 'nullable|array',
            'sending_settings.batch_size' => 'nullable|integer|min:1|max:10000',
            'sending_settings.send_interval' => 'nullable|integer|min:1|max:3600',
            'sending_settings.throttle_rate' => 'nullable|integer|min:1|max:1000',
            'sending_settings.retry_failed' => 'boolean',
            'sending_settings.max_retries' => 'nullable|integer|min:1|max:10',
            'tracking_settings' => 'nullable|array',
            'tracking_settings.open_tracking' => 'boolean',
            'tracking_settings.click_tracking' => 'boolean',
            'tracking_settings.conversion_tracking' => 'boolean',
            'tracking_settings.google_analytics' => 'boolean',
            'tracking_settings.custom_tracking' => 'nullable|array',
            'tracking_settings.custom_tracking.*.name' => 'required|string|max:100',
            'tracking_settings.custom_tracking.*.value' => 'required|string|max:255',
            'automation_settings' => 'nullable|array',
            'automation_settings.auto_resend' => 'boolean',
            'automation_settings.resend_after_hours' => 'nullable|integer|min:1|max:720',
            'automation_settings.resend_to_non_openers' => 'boolean',
            'automation_settings.trigger_follow_up' => 'boolean',
            'automation_settings.follow_up_delay_hours' => 'nullable|integer|min:1|max:720',
            'test_settings' => 'nullable|array',
            'test_settings.enable_a_b_testing' => 'boolean',
            'test_settings.test_group_size' => 'nullable|integer|min:1|max:100',
            'test_settings.test_duration_hours' => 'nullable|integer|min:1|max:168',
            'test_settings.test_subject_variants' => 'nullable|array',
            'test_settings.test_subject_variants.*' => 'string|max:255',
        ];
    }

    /**
     * Get the custom error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'property_id.required' => 'يجب اختيار العقار',
            'property_id.exists' => 'العقار المحدد غير موجود',
            'name.required' => 'حقل اسم الحملة مطلوب',
            'name.max' => 'اسم الحملة يجب ألا يزيد عن 255 حرف',
            'subject.required' => 'حقل الموضوع مطلوب',
            'subject.max' => 'الموضوع يجب ألا يزيد عن 255 حرف',
            'preheader.max' => 'المقدمة يجب ألا تزيد عن 150 حرف',
            'from_name.required' => 'حقل اسم المرسل مطلوب',
            'from_name.max' => 'اسم المرسل يجب ألا يزيد عن 100 حرف',
            'from_email.required' => 'حقل بريد المرسل مطلوب',
            'from_email.email' => 'بريد المرسل يجب أن يكون بريداً إلكترونياً صالحاً',
            'from_email.max' => 'بريد المرسل يجب ألا يزيد عن 255 حرف',
            'reply_to_email.email' => 'بريد الرد يجب أن يكون بريداً إلكترونياً صالحاً',
            'reply_to_email.max' => 'بريد الرد يجب ألا يزيد عن 255 حرف',
            'campaign_type.required' => 'يجب اختيار نوع الحملة',
            'campaign_type.in' => 'نوع الحملة غير صالح',
            'status.in' => 'الحالة غير صالحة',
            'template_id.exists' => 'القالب المحدد غير موجود',
            'html_content.required' => 'محتوى HTML مطلوب',
            'content.main_message.max' => 'الرسالة الرئيسية يجب ألا تزيد عن 5000 حرف',
            'content.call_to_action.max' => 'دعوة العمل يجب ألا تزيد عن 500 حرف',
            'content.personalization_tokens.*.max' => 'رمز التخصيص يجب ألا يزيد عن 50 حرف',
            'target_audience.segments.*.max' => 'اسم القطعة يجب ألا يزيد عن 100 حرف',
            'target_audience.filters.*.field.required' => 'حقل الفلتر مطلوب',
            'target_audience.filters.*.field.max' => 'حقل الفلتر يجب ألا يزيد عن 100 حرف',
            'target_audience.filters.*.operator.required' => 'عامل التشغيل مطلوب',
            'target_audience.filters.*.operator.in' => 'عامل التشغيل غير صالح',
            'target_audience.filters.*.value.required' => 'قيمة الفلتر مطلوبة',
            'target_audience.filters.*.value.max' => 'قيمة الفلتر يجب ألا تزيد عن 255 حرف',
            'segment_criteria.inclusion_rules.*.field.required' => 'حقل قاعدة التضمين مطلوب',
            'segment_criteria.inclusion_rules.*.field.max' => 'حقل قاعدة التضمين يجب ألا يزيد عن 100 حرف',
            'segment_criteria.inclusion_rules.*.operator.required' => 'عامل التشغيل مطلوب',
            'segment_criteria.inclusion_rules.*.operator.in' => 'عامل التشغيل غير صالح',
            'segment_criteria.inclusion_rules.*.value.required' => 'قيمة القاعدة مطلوبة',
            'segment_criteria.inclusion_rules.*.value.max' => 'قيمة القاعدة يجب ألا تزيد عن 255 حرف',
            'segment_criteria.exclusion_rules.*.field.required' => 'حقل قاعدة الاستبعاد مطلوب',
            'segment_criteria.exclusion_rules.*.field.max' => 'حقل قاعدة الاستبعاد يجب ألا يزيد عن 100 حرف',
            'segment_criteria.exclusion_rules.*.operator.required' => 'عامل التشغيل مطلوب',
            'segment_criteria.exclusion_rules.*.operator.in' => 'عامل التشغيل غير صالح',
            'segment_criteria.exclusion_rules.*.value.required' => 'قيمة القاعدة مطلوبة',
            'segment_criteria.exclusion_rules.*.value.max' => 'قيمة القاعدة يجب ألا تزيد عن 255 حرف',
            'schedule_settings.scheduled_at.after' => 'وقت الجدولة يجب أن يكون في المستقبل',
            'schedule_settings.timezone.max' => 'المنطقة الزمنية يجب ألا تزيد عن 50 حرف',
            'sending_settings.batch_size.min' => 'حجم الدفعة يجب أن يكون على الأقل 1',
            'sending_settings.batch_size.max' => 'حجم الدفعة يجب ألا يزيد عن 10000',
            'sending_settings.send_interval.min' => 'فترة الإرسال يجب أن تكون على الأقل 1 ثانية',
            'sending_settings.send_interval.max' => 'فترة الإرسال يجب ألا تزيد عن 3600 ثانية',
            'sending_settings.throttle_rate.min' => 'معدل التقييد يجب أن يكون على الأقل 1',
            'sending_settings.throttle_rate.max' => 'معدل التقييد يجب ألا يزيد عن 1000',
            'sending_settings.max_retries.min' => 'أقصى عدد المحاولات يجب أن يكون على الأقل 1',
            'sending_settings.max_retries.max' => 'أقصى عدد المحاولات يجب ألا يزيد عن 10',
            'tracking_settings.custom_tracking.*.name.required' => 'اسم التتبع المخصص مطلوب',
            'tracking_settings.custom_tracking.*.name.max' => 'اسم التتبع المخصص يجب ألا يزيد عن 100 حرف',
            'tracking_settings.custom_tracking.*.value.required' => 'قيمة التتبع المخصص مطلوبة',
            'tracking_settings.custom_tracking.*.value.max' => 'قيمة التتبع المخصص يجب ألا تزيد عن 255 حرف',
            'automation_settings.resend_after_hours.min' => 'فترة إعادة الإرسال يجب أن تكون على الأقل 1 ساعة',
            'automation_settings.resend_after_hours.max' => 'فترة إعادة الإرسال يجب ألا تزيد عن 720 ساعة',
            'automation_settings.follow_up_delay_hours.min' => 'فترة المتابعة يجب أن تكون على الأقل 1 ساعة',
            'automation_settings.follow_up_delay_hours.max' => 'فترة المتابعة يجب ألا تزيد عن 720 ساعة',
            'test_settings.test_group_size.min' => 'حجم مجموعة الاختبار يجب أن يكون على الأقل 1',
            'test_settings.test_group_size.max' => 'حجم مجموعة الاختبار يجب ألا يزيد عن 100',
            'test_settings.test_duration_hours.min' => 'مدة الاختبار يجب أن تكون على الأقل 1 ساعة',
            'test_settings.test_duration_hours.max' => 'مدة الاختبار يجب ألا تزيد عن 168 ساعة',
            'test_settings.test_subject_variants.*.max' => 'متغير الموضوع يجب ألا يزيد عن 255 حرف',
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
            'property_id' => 'العقار',
            'name' => 'اسم الحملة',
            'subject' => 'الموضوع',
            'preheader' => 'المقدمة',
            'from_name' => 'اسم المرسل',
            'from_email' => 'بريد المرسل',
            'reply_to_email' => 'بريد الرد',
            'campaign_type' => 'نوع الحملة',
            'status' => 'الحالة',
            'template_id' => 'القالب',
            'content.main_message' => 'الرسالة الرئيسية',
            'content.call_to_action' => 'دعوة العمل',
            'content.personalization_tokens' => 'رموز التخصيص',
            'html_content' => 'محتوى HTML',
            'text_content' => 'محتوى نصي',
            'target_audience.segments' => 'قطع الجمهور',
            'target_audience.filters' => 'فلاتر الجمهور',
            'segment_criteria.inclusion_rules' => 'قواعد التضمين',
            'segment_criteria.exclusion_rules' => 'قواعد الاستبعاد',
            'personalization_settings.use_first_name' => 'استخدام الاسم الأول',
            'personalization_settings.use_last_name' => 'استخدام الاسم الأخير',
            'personalization_settings.use_company' => 'استخدام الشركة',
            'personalization_settings.use_location' => 'استخدام الموقع',
            'personalization_settings.use_preferences' => 'استخدام التفضيلات',
            'schedule_settings.send_immediately' => 'الإرسال الفوري',
            'schedule_settings.scheduled_at' => 'وقت الجدولة',
            'schedule_settings.timezone' => 'المنطقة الزمنية',
            'schedule_settings.send_in_local_time' => 'الإرسال في الوقت المحلي',
            'sending_settings.batch_size' => 'حجم الدفعة',
            'sending_settings.send_interval' => 'فترة الإرسال',
            'sending_settings.throttle_rate' => 'معدل التقييد',
            'sending_settings.retry_failed' => 'إعادة المحاولات الفاشلة',
            'sending_settings.max_retries' => 'أقصى عدد المحاولات',
            'tracking_settings.open_tracking' => 'تتبع الفتح',
            'tracking_settings.click_tracking' => 'تتبع النقر',
            'tracking_settings.conversion_tracking' => 'تتبع التحويل',
            'tracking_settings.google_analytics' => 'تحليلات جوجل',
            'tracking_settings.custom_tracking' => 'التتبع المخصص',
            'automation_settings.auto_resend' => 'إعادة الإرسال التلقائي',
            'automation_settings.resend_after_hours' => 'فترة إعادة الإرسال',
            'automation_settings.resend_to_non_openers' => 'إعادة الإرسال لغير الفاتحين',
            'automation_settings.trigger_follow_up' => 'تشغيل المتابعة',
            'automation_settings.follow_up_delay_hours' => 'فترة المتابعة',
            'test_settings.enable_a_b_testing' => 'تفعيل اختبار A/B',
            'test_settings.test_group_size' => 'حجم مجموعة الاختبار',
            'test_settings.test_duration_hours' => 'مدة الاختبار',
            'test_settings.test_subject_variants' => 'متغيرات الموضوع',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function configure(): void
    {
        $this->errorBag = 'storeEmailCampaign';
    }

    /**
     * Get the validated data.
     *
     * @return array<string, mixed>
     */
    public function validated(): array
    {
        $validated = parent::validated();

        // Set default values
        $validated['status'] = $validated['status'] ?? 'draft';

        // Convert arrays to JSON
        if (isset($validated['content'])) {
            $validated['content'] = json_encode($validated['content']);
        }
        if (isset($validated['target_audience'])) {
            $validated['target_audience'] = json_encode($validated['target_audience']);
        }
        if (isset($validated['segment_criteria'])) {
            $validated['segment_criteria'] = json_encode($validated['segment_criteria']);
        }
        if (isset($validated['personalization_settings'])) {
            $validated['personalization_settings'] = json_encode($validated['personalization_settings']);
        }
        if (isset($validated['schedule_settings'])) {
            $validated['schedule_settings'] = json_encode($validated['schedule_settings']);
        }
        if (isset($validated['sending_settings'])) {
            $validated['sending_settings'] = json_encode($validated['sending_settings']);
        }
        if (isset($validated['tracking_settings'])) {
            $validated['tracking_settings'] = json_encode($validated['tracking_settings']);
        }
        if (isset($validated['automation_settings'])) {
            $validated['automation_settings'] = json_encode($validated['automation_settings']);
        }
        if (isset($validated['test_settings'])) {
            $validated['test_settings'] = json_encode($validated['test_settings']);
        }

        return $validated;
    }
}
