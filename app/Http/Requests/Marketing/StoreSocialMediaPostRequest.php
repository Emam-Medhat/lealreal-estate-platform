<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class StoreSocialMediaPostRequest extends FormRequest
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
            'content' => 'required|string|max:2000',
            'platform' => 'required|string|in:facebook,twitter,instagram,linkedin,youtube,tiktok',
            'post_type' => 'required|string|in:image,video,carousel,story,reel,live_stream',
            'status' => 'nullable|string|in:draft,scheduled,published,archived,failed',
            'scheduled_at' => 'nullable|date|after:now',
            'published_at' => 'nullable|date',
            'hashtags' => 'nullable|array',
            'hashtags.*' => 'string|max:100',
            'mentions' => 'nullable|array',
            'mentions.*' => 'string|max:100',
            'call_to_action' => 'nullable|array',
            'call_to_action.text' => 'nullable|string|max:255',
            'call_to_action.url' => 'nullable|url|max:500',
            'call_to_action.button_text' => 'nullable|string|max:50',
            'target_audience' => 'nullable|array',
            'target_audience.age_range' => 'nullable|string|in:18-24,25-34,35-44,45-54,55+',
            'target_audience.genders' => 'nullable|array',
            'target_audience.genders.*' => 'string|in:male,female',
            'target_audience.locations' => 'nullable|array',
            'target_audience.locations.*' => 'string|max:100',
            'target_audience.interests' => 'nullable|array',
            'target_audience.interests.*' => 'string|max:100',
            'budget' => 'nullable|numeric|min:0',
            'boost_post' => 'boolean',
            'media_files' => 'nullable|array',
            'media_files.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:10240',
            'thumbnail' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:2048',
            'video_url' => 'nullable|url|max:500',
            'link_url' => 'nullable|url|max:500',
            'location_tag' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:10',
            'engagement_settings' => 'nullable|array',
            'engagement_settings.allow_comments' => 'boolean',
            'engagement_settings.allow_shares' => 'boolean',
            'engagement_settings.allow_likes' => 'boolean',
            'engagement_settings.comment_filtering' => 'boolean',
            'promotion_settings' => 'nullable|array',
            'promotion_settings.promote_post' => 'boolean',
            'promotion_settings.boost_budget' => 'nullable|numeric|min:0',
            'promotion_settings.boost_duration' => 'nullable|integer|min:1|max:365',
            'promotion_settings.targeting_options' => 'nullable|array',
            'promotion_settings.targeting_options.age_range' => 'nullable|string|in:18-24,25-34,35-44,45-54,55+',
            'promotion_settings.targeting_options.gender' => 'nullable|string|in:male,female,all',
            'promotion_settings.targeting_options.location' => 'nullable|string|max:255',
            'promotion_settings.targeting_options.interests' => 'nullable|array',
            'promotion_settings.targeting_options.interests.*' => 'string|max:100',
            'analytics_settings' => 'nullable|array',
            'analytics_settings.track_engagement' => 'boolean',
            'analytics_settings.track_clicks' => 'boolean',
            'analytics_settings.track_conversions' => 'boolean',
            'analytics_settings.custom_events' => 'nullable|array',
            'analytics_settings.custom_events.*.name' => 'required|string|max:100',
            'analytics_settings.custom_events.*.value' => 'nullable|string|max:255',
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
            'content.required' => 'حقل المحتوى مطلوب',
            'content.max' => 'المحتوى يجب ألا يزيد عن 2000 حرف',
            'platform.required' => 'يجب اختيار المنصة',
            'platform.in' => 'المنصة غير صالحة',
            'post_type.required' => 'يجب اختيار نوع المنشور',
            'post_type.in' => 'نوع المنشور غير صالح',
            'status.in' => 'الحالة غير صالحة',
            'scheduled_at.after' => 'وقت الجدولة يجب أن يكون في المستقبل',
            'hashtags.*.max' => 'الهاشتاج يجب ألا يزيد عن 100 حرف',
            'mentions.*.max' => 'الإشارة يجب ألا تزيد عن 100 حرف',
            'call_to_action.text.max' => 'نص دعوة العمل يجب ألا يزيد عن 255 حرف',
            'call_to_action.url.url' => 'رابط دعوة العمل يجب أن يكون رابطاً صالحاً',
            'call_to_action.url.max' => 'رابط دعوة العمل يجب ألا يزيد عن 500 حرف',
            'call_to_action.button_text.max' => 'نص الزر يجب ألا يزيد عن 50 حرف',
            'target_audience.age_range.in' => 'الفئة العمرية غير صالحة',
            'target_audience.genders.*.in' => 'الجنس غير صالح',
            'target_audience.locations.*.max' => 'اسم الموقع يجب ألا يزيد عن 100 حرف',
            'target_audience.interests.*.max' => 'الاهتمام يجب ألا يزيد عن 100 حرف',
            'budget.numeric' => 'الميزانية يجب أن تكون رقماً',
            'budget.min' => 'الميزانية يجب أن تكون أكبر من أو تساوي 0',
            'media_files.*.mimes' => 'ملف الوسائط يجب أن يكون من نوع: jpg, jpeg, png, gif, mp4, mov, avi',
            'media_files.*.max' => 'حجم الملف يجب ألا يزيد عن 10 ميجابايت',
            'thumbnail.mimes' => 'الصورة المصغرة يجب أن تكون من نوع: jpg, jpeg, png, gif',
            'thumbnail.max' => 'حجم الصورة المصغرة يجب ألا يزيد عن 2 ميجابايت',
            'video_url.url' => 'رابط الفيديو يجب أن يكون رابطاً صالحاً',
            'video_url.max' => 'رابط الفيديو يجب ألا يزيد عن 500 حرف',
            'link_url.url' => 'رابط الرابط يجب أن يكون رابطاً صالحاً',
            'link_url.max' => 'رابط الرابط يجب ألا يزيد عن 500 حرف',
            'location_tag.max' => 'وسم الموقع يجب ألا يزيد عن 255 حرف',
            'language.max' => 'اللغة يجب ألا تزيد عن 10 أحرف',
            'promotion_settings.boost_budget.numeric' => 'ميزانية الترويج يجب أن تكون رقماً',
            'promotion_settings.boost_budget.min' => 'ميزانية الترويج يجب أن تكون أكبر من أو تساوي 0',
            'promotion_settings.boost_duration.min' => 'مدة الترويج يجب أن تكون على الأقل 1 يوم',
            'promotion_settings.boost_duration.max' => 'مدة الترويج يجب ألا تزيد عن 365 يوم',
            'promotion_settings.targeting_options.age_range.in' => 'الفئة العمرية المستهدفة غير صالحة',
            'promotion_settings.targeting_options.gender.in' => 'الجنس المستهدف غير صالح',
            'promotion_settings.targeting_options.location.max' => 'الموقع المستهدف يجب ألا يزيد عن 255 حرف',
            'promotion_settings.targeting_options.interests.*.max' => 'الاهتمام المستهدف يجب ألا يزيد عن 100 حرف',
            'analytics_settings.custom_events.*.name.required' => 'اسم الحدث المخصص مطلوب',
            'analytics_settings.custom_events.*.name.max' => 'اسم الحدث المخصص يجب ألا يزيد عن 100 حرف',
            'analytics_settings.custom_events.*.value.max' => 'قيمة الحدث المخصص يجب ألا تزيد عن 255 حرف',
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
            'content' => 'المحتوى',
            'platform' => 'المنصة',
            'post_type' => 'نوع المنشور',
            'status' => 'الحالة',
            'scheduled_at' => 'وقت الجدولة',
            'published_at' => 'وقت النشر',
            'hashtags' => 'الهاشتاجات',
            'mentions' => 'الإشارات',
            'call_to_action.text' => 'نص دعوة العمل',
            'call_to_action.url' => 'رابط دعوة العمل',
            'call_to_action.button_text' => 'نص الزر',
            'target_audience.age_range' => 'الفئة العمرية المستهدفة',
            'target_audience.genders' => 'الجنس المستهدف',
            'target_audience.locations' => 'المناطق المستهدفة',
            'target_audience.interests' => 'الاهتمامات المستهدفة',
            'budget' => 'الميزانية',
            'boost_post' => 'ترويج المنشور',
            'media_files' => 'ملفات الوسائط',
            'thumbnail' => 'الصورة المصغرة',
            'video_url' => 'رابط الفيديو',
            'link_url' => 'رابط الرابط',
            'location_tag' => 'وسم الموقع',
            'language' => 'اللغة',
            'engagement_settings.allow_comments' => 'السماح بالتعليقات',
            'engagement_settings.allow_shares' => 'السماح بالمشاركة',
            'engagement_settings.allow_likes' => 'السماح بالإعجاب',
            'engagement_settings.comment_filtering' => 'فلترة التعليقات',
            'promotion_settings.promote_post' => 'ترويج المنشور',
            'promotion_settings.boost_budget' => 'ميزانية الترويج',
            'promotion_settings.boost_duration' => 'مدة الترويج',
            'promotion_settings.targeting_options.age_range' => 'الفئة العمرية المستهدفة',
            'promotion_settings.targeting_options.gender' => 'الجنس المستهدف',
            'promotion_settings.targeting_options.location' => 'الموقع المستهدف',
            'promotion_settings.targeting_options.interests' => 'الاهتمامات المستهدفة',
            'analytics_settings.track_engagement' => 'تتبع التفاعل',
            'analytics_settings.track_clicks' => 'تتبع النقرات',
            'analytics_settings.track_conversions' => 'تتبع التحويلات',
            'analytics_settings.custom_events' => 'الأحداث المخصصة',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function configure(): void
    {
        $this->errorBag = 'storeSocialMediaPost';
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
        $validated['boost_post'] = $validated['boost_post'] ?? false;
        $validated['language'] = $validated['language'] ?? 'ar';

        // Convert arrays to JSON
        if (isset($validated['hashtags'])) {
            $validated['hashtags'] = json_encode($validated['hashtags']);
        }
        if (isset($validated['mentions'])) {
            $validated['mentions'] = json_encode($validated['mentions']);
        }
        if (isset($validated['call_to_action'])) {
            $validated['call_to_action'] = json_encode($validated['call_to_action']);
        }
        if (isset($validated['target_audience'])) {
            $validated['target_audience'] = json_encode($validated['target_audience']);
        }
        if (isset($validated['engagement_settings'])) {
            $validated['engagement_settings'] = json_encode($validated['engagement_settings']);
        }
        if (isset($validated['promotion_settings'])) {
            $validated['promotion_settings'] = json_encode($validated['promotion_settings']);
        }
        if (isset($validated['analytics_settings'])) {
            $validated['analytics_settings'] = json_encode($validated['analytics_settings']);
        }

        return $validated;
    }

    /**
     * Get the after validation hook.
     */
    protected function after(): void
    {
        // Validate media files based on post type
        if ($this->has('post_type')) {
            $postType = $this->input('post_type');
            
            if (in_array($postType, ['image', 'carousel']) && !$this->has('media_files')) {
                $this->validator->errors()->add('media_files', 'يجب رفع صور للمنشور من نوع ' . $postType);
            }
            
            if ($postType === 'video' && !$this->has('video_url') && !$this->has('media_files')) {
                $this->validator->errors()->add('video_url', 'يجب رفع فيديو أو رابط فيديو للمنشور من نوع فيديو');
            }
            
            if ($postType === 'story' && !$this->has('media_files')) {
                $this->validator->errors()->add('media_files', 'يجب رفع صورة أو فيديو للمنشور من نوع قصة');
            }
        }

        // Validate boost settings
        if ($this->input('boost_post') && !$this->input('budget')) {
            $this->validator->errors()->add('budget', 'يجب تحديد ميزانية عند تفعيل الترويج');
        }

        // Validate call to action
        if ($this->has('call_to_action.url') && !$this->has('call_to_action.text')) {
            $this->validator->errors()->add('call_to_action.text', 'يجب تحديد نص دعوة العمل عند وجود رابط');
        }
    }
}
