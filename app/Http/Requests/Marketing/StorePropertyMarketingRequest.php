<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyMarketingRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'campaign_type' => 'required|string|in:brand_awareness,lead_generation,property_promotion,neighborhood_showcase,event_marketing',
            'status' => 'nullable|string|in:draft,scheduled,active,paused,completed',
            'budget' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'target_audience' => 'nullable|array',
            'target_audience.age_range' => 'nullable|string|in:18-24,25-34,35-44,45-54,55+',
            'target_audience.genders' => 'nullable|array',
            'target_audience.genders.*' => 'string|in:male,female',
            'target_audience.locations' => 'nullable|array',
            'target_audience.locations.*' => 'string|max:100',
            'target_audience.income_range' => 'nullable|string|max:50',
            'target_audience.interests' => 'nullable|array',
            'target_audience.interests.*' => 'string|max:100',
            'marketing_channels' => 'nullable|array',
            'marketing_channels.*' => 'string|in:social_media,email,search,display,video,mobile',
            'content_strategy' => 'nullable|string|max:2000',
            'creative_assets' => 'nullable|array',
            'creative_assets.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,pdf|max:10240',
            'performance_goals' => 'nullable|array',
            'performance_goals.click_through_rate' => 'nullable|numeric|min:0|max:100',
            'performance_goals.conversion_rate' => 'nullable|numeric|min:0|max:100',
            'performance_goals.cost_per_conversion' => 'nullable|numeric|min:0',
            'performance_goals.return_on_ad_spend' => 'nullable|numeric|min:0',
            'performance_goals.impression_share' => 'nullable|numeric|min:0|max:100',
            'tracking_settings' => 'nullable|array',
            'tracking_settings.google_analytics' => 'boolean',
            'tracking_settings.facebook_pixel' => 'boolean',
            'tracking_settings.google_tag_manager' => 'boolean',
            'tracking_settings.conversion_tracking' => 'boolean',
            'automation_settings' => 'nullable|array',
            'automation_settings.auto_optimization' => 'boolean',
            'automation_settings.bid_adjustment' => 'boolean',
            'automation_settings.audience_expansion' => 'boolean',
            'launch_settings' => 'nullable|array',
            'launch_settings.launch_immediately' => 'boolean',
            'launch_settings.launch_date' => 'nullable|date|after_or_equal:today',
            'launch_settings.launch_time' => 'nullable|date_format:H:i',
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
            'title.required' => 'حقل العنوان مطلوب',
            'title.max' => 'العنوان يجب ألا يزيد عن 255 حرف',
            'description.max' => 'الوصف يجب ألا يزيد عن 2000 حرف',
            'campaign_type.required' => 'يجب اختيار نوع الحملة',
            'campaign_type.in' => 'نوع الحملة غير صالح',
            'status.in' => 'الحالة غير صالحة',
            'budget.numeric' => 'الميزانية يجب أن تكون رقماً',
            'budget.min' => 'الميزانية يجب أن تكون أكبر من أو تساوي 0',
            'currency.max' => 'رمز العملة يجب ألا يزيد عن 3 أحرف',
            'start_date.after_or_equal' => 'تاريخ البدء يجب أن يكون اليوم أو في المستقبل',
            'end_date.after' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء',
            'target_audience.age_range.in' => 'الفئة العمرية غير صالحة',
            'target_audience.genders.*.in' => 'الجنس غير صالح',
            'target_audience.locations.*.max' => 'اسم الموقع يجب ألا يزيد عن 100 حرف',
            'marketing_channels.*.in' => 'قناة التسويق غير صالحة',
            'creative_assets.*.mimes' => 'ملف المرفق يجب أن يكون من نوع: jpg, jpeg, png, gif, mp4, pdf',
            'creative_assets.*.max' => 'حجم الملف يجب ألا يزيد عن 10 ميجابايت',
            'performance_goals.click_through_rate.numeric' => 'معدل النقرات يجب أن يكون رقماً',
            'performance_goals.click_through_rate.min' => 'معدل النقرات يجب أن يكون أكبر من أو يساوي 0',
            'performance_goals.click_through_rate.max' => 'معدل النقرات يجب أن يكون أقل من أو يساوي 100',
            'performance_goals.conversion_rate.numeric' => 'معدل التحويل يجب أن يكون رقماً',
            'performance_goals.conversion_rate.min' => 'معدل التحويل يجب أن يكون أكبر من أو يساوي 0',
            'performance_goals.conversion_rate.max' => 'معدل التحويل يجب أن يكون أقل من أو يساوي 100',
            'performance_goals.cost_per_conversion.numeric' => 'تكلفة التحويل يجب أن تكون رقماً',
            'performance_goals.cost_per_conversion.min' => 'تكلفة التحويل يجب أن تكون أكبر من أو تساوي 0',
            'performance_goals.return_on_ad_spend.numeric' => 'العائد على الإنفاق يجب أن يكون رقماً',
            'performance_goals.return_on_ad_spend.min' => 'العائد على الإنفاق يجب أن يكون أكبر من أو يساوي 0',
            'performance_goals.impression_share.numeric' => 'حصة الانطباع يجب أن تكون رقماً',
            'performance_goals.impression_share.min' => 'حصة الانطباع يجب أن تكون أكبر من أو تساوي 0',
            'performance_goals.impression_share.max' => 'حصة الانطباع يجب أن تكون أقل من أو يساوي 100',
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
            'title' => 'العنوان',
            'description' => 'الوصف',
            'campaign_type' => 'نوع الحملة',
            'status' => 'الحالة',
            'budget' => 'الميزانية',
            'currency' => 'العملة',
            'start_date' => 'تاريخ البدء',
            'end_date' => 'تاريخ الانتهاء',
            'target_audience.age_range' => 'الفئة العمرية',
            'target_audience.genders' => 'الجنس',
            'target_audience.locations' => 'المناطق',
            'target_audience.income_range' => 'نطاق الدخل',
            'target_audience.interests' => 'الاهتمامات',
            'marketing_channels' => 'قنوات التسويق',
            'content_strategy' => 'استراتيجية المحتوى',
            'creative_assets' => 'المواد الإبداعية',
            'performance_goals.click_through_rate' => 'معدل النقرات',
            'performance_goals.conversion_rate' => 'معدل التحويل',
            'performance_goals.cost_per_conversion' => 'تكلفة التحويل',
            'performance_goals.return_on_ad_spend' => 'العائد على الإنفاق',
            'performance_goals.impression_share' => 'حصة الانطباع',
            'tracking_settings.google_analytics' => 'تحليلات جوجل',
            'tracking_settings.facebook_pixel' => 'بكسل فيسبوك',
            'tracking_settings.google_tag_manager' => 'مدير العلامات جوجل',
            'tracking_settings.conversion_tracking' => 'تتبع التحويل',
            'automation_settings.auto_optimization' => 'التحسين التلقائي',
            'automation_settings.bid_adjustment' => 'تعديل المزايدة',
            'automation_settings.audience_expansion' => 'توسيع الجمهور',
            'launch_settings.launch_immediately' => 'الإطلاق الفوري',
            'launch_settings.launch_date' => 'تاريخ الإطلاق',
            'launch_settings.launch_time' => 'وقت الإطلاق',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function configure(): void
    {
        $this->errorBag = 'storePropertyMarketing';
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
        $validated['currency'] = $validated['currency'] ?? 'SAR';

        // Convert arrays to JSON
        if (isset($validated['target_audience'])) {
            $validated['target_audience'] = json_encode($validated['target_audience']);
        }
        if (isset($validated['marketing_channels'])) {
            $validated['marketing_channels'] = json_encode($validated['marketing_channels']);
        }
        if (isset($validated['content_strategy'])) {
            $validated['content_strategy'] = json_encode(['strategy' => $validated['content_strategy']]);
        }
        if (isset($validated['performance_goals'])) {
            $validated['performance_goals'] = json_encode($validated['performance_goals']);
        }
        if (isset($validated['tracking_settings'])) {
            $validated['tracking_settings'] = json_encode($validated['tracking_settings']);
        }
        if (isset($validated['automation_settings'])) {
            $validated['automation_settings'] = json_encode($validated['automation_settings']);
        }
        if (isset($validated['launch_settings'])) {
            $validated['launch_settings'] = json_encode($validated['launch_settings']);
        }

        return $validated;
    }
}
