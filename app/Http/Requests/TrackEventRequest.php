<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackEventRequest extends FormRequest
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
            'event_name' => 'required|string|max:100',
            'page_url' => 'required|string|max:2048',
            'user_agent' => 'nullable|string|max:500',
            'ip_address' => 'nullable|string|max:45',
            'properties' => 'nullable|array',
            'properties.*' => 'nullable|string',
            'session_id' => 'nullable|string|max:255',
            'user_id' => 'nullable|integer|exists:users,id',
            'timestamp' => 'nullable|date',
            'referrer' => 'nullable|string|max:2048',
            'utm_source' => 'nullable|string|max:100',
            'utm_medium' => 'nullable|string|max:100',
            'utm_campaign' => 'nullable|string|max:100',
            'utm_term' => 'nullable|string|max:100',
            'utm_content' => 'nullable|string|max:100',
            'device_type' => 'nullable|string|in:desktop,mobile,tablet,unknown',
            'browser' => 'nullable|string|max:50',
            'os' => 'nullable|string|max:50',
            'screen_resolution' => 'nullable|string|max:20',
            'viewport_size' => 'nullable|string|max:20',
            'language' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:2',
            'city' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'duration' => 'nullable|integer|min:0',
            'scroll_depth' => 'nullable|numeric|min:0|max:100',
            'click_position' => 'nullable|array',
            'click_position.x' => 'nullable|integer|min:0',
            'click_position.y' => 'nullable|integer|min:0',
            'mouse_position' => 'nullable|array',
            'mouse_position.x' => 'nullable|integer|min:0',
            'mouse_position.y' => 'nullable|integer|min:0',
            'element_id' => 'nullable|string|max:100',
            'element_class' => 'nullable|string|max:100',
            'element_text' => 'nullable|string|max:255',
            'form_id' => 'nullable|string|max:100',
            'form_name' => 'nullable|string|max:100',
            'form_field' => 'nullable|string|max:100',
            'conversion_value' => 'nullable|numeric|min:0',
            'conversion_currency' => 'nullable|string|max:3',
            'product_id' => 'nullable|integer',
            'product_name' => 'nullable|string|max:255',
            'product_category' => 'nullable|string|max:100',
            'product_price' => 'nullable|numeric|min:0',
            'search_query' => 'nullable|string|max:255',
            'search_results_count' => 'nullable|integer|min:0',
            'error_message' => 'nullable|string|max:500',
            'error_code' => 'nullable|string|max:50',
            'load_time' => 'nullable|numeric|min:0',
            'response_time' => 'nullable|numeric|min:0',
            'is_mobile' => 'nullable|boolean',
            'is_tablet' => 'nullable|boolean',
            'is_bot' => 'nullable|boolean',
            'is_first_visit' => 'nullable|boolean',
            'is_returning_visitor' => 'nullable|boolean',
            'custom_attributes' => 'nullable|array',
            'custom_attributes.*' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'event_name.required' => 'اسم الحدث مطلوب',
            'event_name.max' => 'اسم الحدث يجب ألا يزيد عن 100 حرف',
            'page_url.required' => 'رابط الصفحة مطلوب',
            'page_url.max' => 'رابط الصفحة يجب ألا يزيد عن 2048 حرف',
            'user_agent.max' => 'وكيل المستخدم يجب ألا يزيد عن 500 حرف',
            'ip_address.max' => 'عنوان IP يجب ألا يزيد عن 45 حرف',
            'user_id.exists' => 'المستخدم المحدد غير موجود',
            'timestamp.date' => 'الطابع الزمني يجب أن يكون تاريخاً صالحاً',
            'referrer.max' => 'المرجع يجب ألا يزيد عن 2048 حرف',
            'utm_source.max' => 'مصدر UTM يجب ألا يزيد عن 100 حرف',
            'utm_medium.max' => 'وسيلة UTM يجب ألا تزيد عن 100 حرف',
            'utm_campaign.max' => 'حملة UTM يجب ألا تزيد عن 100 حرف',
            'utm_term.max' => 'مصطلح UTM يجب ألا يزيد عن 100 حرف',
            'utm_content.max' => 'محتوى UTM يجب ألا يزيد عن 100 حرف',
            'device_type.in' => 'نوع الجهاز يجب أن يكون: desktop, mobile, tablet, أو unknown',
            'browser.max' => 'المتصفح يجب ألا يزيد عن 50 حرف',
            'os.max' => 'نظام التشغيل يجب ألا يزيد عن 50 حرف',
            'screen_resolution.max' => 'دقة الشاشة يجب ألا تزيد عن 20 حرف',
            'viewport_size.max' => 'حجم إطار العرض يجب ألا يزيد عن 20 حرف',
            'language.max' => 'اللغة يجب ألا تزيد عن 10 أحرف',
            'country.max' => 'الدولة يجب ألا تزيد عن حرفين',
            'city.max' => 'المدينة يجب ألا تزيد عن 100 حرف',
            'latitude.between' => 'خط العرض يجب أن يكون بين -90 و 90',
            'longitude.between' => 'خط الطول يجب أن يكون بين -180 و 180',
            'duration.min' => 'المدة يجب أن تكون 0 أو أكثر',
            'scroll_depth.min' => 'عمق التمرير يجب أن يكون بين 0 و 100',
            'scroll_depth.max' => 'عمق التمرير يجب أن يكون بين 0 و 100',
            'click_position.x.min' => 'إحداثي X للنقرة يجب أن يكون 0 أو أكثر',
            'click_position.y.min' => 'إحداثي Y للنقرة يجب أن يكون 0 أو أكثر',
            'mouse_position.x.min' => 'إحداثي X للماوس يجب أن يكون 0 أو أكثر',
            'mouse_position.y.min' => 'إحداثي Y للماوس يجب أن يكون 0 أو أكثر',
            'element_id.max' => 'معرف العنصر يجب ألا يزيد عن 100 حرف',
            'element_class.max' => 'فئة العنصر يجب ألا تزيد عن 100 حرف',
            'element_text.max' => 'نص العنصر يجب ألا يزيد عن 255 حرف',
            'form_id.max' => 'معرف النموذج يجب ألا يزيد عن 100 حرف',
            'form_name.max' => 'اسم النموذج يجب ألا يزيد عن 100 حرف',
            'form_field.max' => 'حقل النموذج يجب ألا يزيد عن 100 حرف',
            'conversion_value.min' => 'قيمة التحويل يجب أن تكون 0 أو أكثر',
            'conversion_currency.max' => 'عملة التحويل يجب ألا تزيد عن 3 أحرف',
            'product_name.max' => 'اسم المنتج يجب ألا يزيد عن 255 حرف',
            'product_category.max' => 'فئة المنتج يجب ألا تزيد عن 100 حرف',
            'product_price.min' => 'سعر المنتج يجب أن يكون 0 أو أكثر',
            'search_query.max' => 'استعلام البحث يجب ألا يزيد عن 255 حرف',
            'search_results_count.min' => 'عدد نتائج البحث يجب أن يكون 0 أو أكثر',
            'error_message.max' => 'رسالة الخطأ يجب ألا تزيد عن 500 حرف',
            'error_code.max' => 'رمز الخطأ يجب ألا يزيد عن 50 حرف',
            'load_time.min' => 'وقت التحميل يجب أن يكون 0 أو أكثر',
            'response_time.min' => 'وقت الاستجابة يجب أن يكون 0 أو أكثر',
            'custom_attributes.*.max' => 'السمة المخصصة يجب ألا تزيد عن 255 حرف',
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
            'event_name' => 'اسم الحدث',
            'page_url' => 'رابط الصفحة',
            'user_agent' => 'وكيل المستخدم',
            'ip_address' => 'عنوان IP',
            'properties' => 'الخصائص',
            'session_id' => 'معرف الجلسة',
            'user_id' => 'معرف المستخدم',
            'timestamp' => 'الطابع الزمني',
            'referrer' => 'المرجع',
            'utm_source' => 'مصدر UTM',
            'utm_medium' => 'وسيلة UTM',
            'utm_campaign' => 'حملة UTM',
            'utm_term' => 'مصطلح UTM',
            'utm_content' => 'محتوى UTM',
            'device_type' => 'نوع الجهاز',
            'browser' => 'المتصفح',
            'os' => 'نظام التشغيل',
            'screen_resolution' => 'دقة الشاشة',
            'viewport_size' => 'حجم إطار العرض',
            'language' => 'اللغة',
            'country' => 'الدولة',
            'city' => 'المدينة',
            'latitude' => 'خط العرض',
            'longitude' => 'خط الطول',
            'duration' => 'المدة',
            'scroll_depth' => 'عمق التمرير',
            'click_position' => 'موضع النقرة',
            'click_position.x' => 'إحداثي X للنقرة',
            'click_position.y' => 'إحداثي Y للنقرة',
            'mouse_position' => 'موضع الماوس',
            'mouse_position.x' => 'إحداثي X للماوس',
            'mouse_position.y' => 'إحداثي Y للماوس',
            'element_id' => 'معرف العنصر',
            'element_class' => 'فئة العنصر',
            'element_text' => 'نص العنصر',
            'form_id' => 'معرف النموذج',
            'form_name' => 'اسم النموذج',
            'form_field' => 'حقل النموذج',
            'conversion_value' => 'قيمة التحويل',
            'conversion_currency' => 'عملة التحويل',
            'product_id' => 'معرف المنتج',
            'product_name' => 'اسم المنتج',
            'product_category' => 'فئة المنتج',
            'product_price' => 'سعر المنتج',
            'search_query' => 'استعلام البحث',
            'search_results_count' => 'عدد نتائج البحث',
            'error_message' => 'رسالة الخطأ',
            'error_code' => 'رمز الخطأ',
            'load_time' => 'وقت التحميل',
            'response_time' => 'وقت الاستجابة',
            'is_mobile' => 'هل هو جوال',
            'is_tablet' => 'هل هو لوحي',
            'is_bot' => 'هل هو بوت',
            'is_first_visit' => 'هل هي الزيارة الأولى',
            'is_returning_visitor' => 'هل هو زائر عائد',
            'custom_attributes' => 'السمات المخصصة',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_mobile' => $this->boolean('is_mobile', false),
            'is_tablet' => $this->boolean('is_tablet', false),
            'is_bot' => $this->boolean('is_bot', false),
            'is_first_visit' => $this->boolean('is_first_visit', false),
            'is_returning_visitor' => $this->boolean('is_returning_visitor', false),
        ]);

        // Auto-detect device type if not provided
        if (!$this->input('device_type')) {
            $userAgent = $this->input('user_agent', '');
            $deviceType = $this->detectDeviceType($userAgent);
            $this->merge(['device_type' => $deviceType]);
        }

        // Auto-detect browser if not provided
        if (!$this->input('browser')) {
            $userAgent = $this->input('user_agent', '');
            $browser = $this->detectBrowser($userAgent);
            $this->merge(['browser' => $browser]);
        }

        // Auto-detect OS if not provided
        if (!$this->input('os')) {
            $userAgent = $this->input('user_agent', '');
            $os = $this->detectOS($userAgent);
            $this->merge(['os' => $os]);
        }

        // Set default timestamp if not provided
        if (!$this->input('timestamp')) {
            $this->merge(['timestamp' => now()]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function configure(): void
    {
        // Add conditional validation based on event type
        $eventName = $this->input('event_name');
        
        if ($eventName === 'click') {
            $this->addRules([
                'click_position' => 'required|array',
                'click_position.x' => 'required|integer|min:0',
                'click_position.y' => 'required|integer|min:0',
                'element_id' => 'nullable|string|max:100',
                'element_class' => 'nullable|string|max:100',
                'element_text' => 'nullable|string|max:255',
            ]);
        }

        if ($eventName === 'form_submit') {
            $this->addRules([
                'form_id' => 'nullable|string|max:100',
                'form_name' => 'nullable|string|max:100',
                'form_field' => 'nullable|string|max:100',
            ]);
        }

        if ($eventName === 'purchase') {
            $this->addRules([
                'product_id' => 'nullable|integer',
                'product_name' => 'nullable|string|max:255',
                'product_category' => 'nullable|string|max:100',
                'product_price' => 'nullable|numeric|min:0',
                'conversion_value' => 'required|numeric|min:0',
                'conversion_currency' => 'required|string|max:3',
            ]);
        }

        if ($eventName === 'search') {
            $this->addRules([
                'search_query' => 'required|string|max:255',
                'search_results_count' => 'nullable|integer|min:0',
            ]);
        }

        if ($eventName === 'error') {
            $this->addRules([
                'error_message' => 'required|string|max:500',
                'error_code' => 'nullable|string|max:50',
            ]);
        }

        if ($eventName === 'page_view') {
            $this->addRules([
                'load_time' => 'nullable|numeric|min:0',
                'response_time' => 'nullable|numeric|min:0',
            ]);
        }

        if ($eventName === 'scroll') {
            $this->addRules([
                'scroll_depth' => 'required|numeric|min:0|max:100',
            ]);
        }

        // Validate coordinates if both are provided
        if ($this->has(['latitude', 'longitude'])) {
            $this->addRules([
                'latitude' => 'required_with:longitude|numeric|between:-90,90',
                'longitude' => 'required_with:latitude|numeric|between:-180,180',
            ]);
        }
    }

    /**
     * Detect device type from user agent.
     */
    private function detectDeviceType(string $userAgent): string
    {
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod/', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/Tablet|iPad/', $userAgent)) {
            return 'tablet';
        } elseif (preg_match('/Windows|Mac|Linux|X11/', $userAgent)) {
            return 'desktop';
        }
        
        return 'unknown';
    }

    /**
     * Detect browser from user agent.
     */
    private function detectBrowser(string $userAgent): string
    {
        if (preg_match('/Chrome/', $userAgent)) return 'chrome';
        if (preg_match('/Firefox/', $userAgent)) return 'firefox';
        if (preg_match('/Safari/', $userAgent)) return 'safari';
        if (preg_match('/Edge/', $userAgent)) return 'edge';
        if (preg_match('/Opera/', $userAgent)) return 'opera';
        if (preg_match('/IE/', $userAgent)) return 'ie';
        
        return 'unknown';
    }

    /**
     * Detect OS from user agent.
     */
    private function detectOS(string $userAgent): string
    {
        if (preg_match('/Windows/', $userAgent)) return 'windows';
        if (preg_match('/Mac/', $userAgent)) return 'macos';
        if (preg_match('/Linux/', $userAgent)) return 'linux';
        if (preg_match('/Android/', $userAgent)) return 'android';
        if (preg_match('/iOS|iPhone|iPad/', $userAgent)) return 'ios';
        
        return 'unknown';
    }

    /**
     * Get the validated data with additional processing.
     */
    protected function getValidatedData(): array
    {
        $data = parent::getValidatedData();

        // Process properties
        if (isset($data['properties']) && is_array($data['properties'])) {
            $data['properties'] = $this->processProperties($data['properties']);
        }

        // Process custom attributes
        if (isset($data['custom_attributes']) && is_array($data['custom_attributes'])) {
            $data['custom_attributes'] = $this->processCustomAttributes($data['custom_attributes']);
        }

        // Add session ID if not provided
        if (!isset($data['session_id'])) {
            $data['session_id'] = session()->getId();
        }

        // Add user ID if authenticated and not provided
        if (!isset($data['user_id']) && auth()->check()) {
            $data['user_id'] = auth()->id();
        }

        // Add IP address if not provided
        if (!isset($data['ip_address'])) {
            $data['ip_address'] = $this->ip();
        }

        // Add user agent if not provided
        if (!isset($data['user_agent'])) {
            $data['user_agent'] = $this->userAgent();
        }

        return $data;
    }

    /**
     * Process properties array.
     */
    private function processProperties(array $properties): array
    {
        $processed = [];

        foreach ($properties as $key => $value) {
            // Clean and validate property values
            if (is_string($value)) {
                $value = trim($value);
                $value = strlen($value) > 1000 ? substr($value, 0, 1000) : $value;
            }

            $processed[$key] = $value;
        }

        return $processed;
    }

    /**
     * Process custom attributes array.
     */
    private function processCustomAttributes(array $attributes): array
    {
        $processed = [];

        foreach ($attributes as $key => $value) {
            // Clean and validate custom attribute values
            if (is_string($value)) {
                $value = trim($value);
                $value = strlen($value) > 255 ? substr($value, 0, 255) : $value;
            }

            $processed[$key] = $value;
        }

        return $processed;
    }

    /**
     * Check if this event should be tracked based on rate limiting.
     */
    public function shouldTrack(): bool
    {
        // Skip tracking for bots
        if ($this->input('is_bot', false)) {
            return false;
        }

        // Skip tracking for internal IPs (localhost, etc.)
        $ip = $this->ip();
        if (in_array($ip, ['127.0.0.1', '::1']) || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return false;
        }

        // Skip tracking for certain event types if rate limited
        $eventName = $this->input('event_name');
        if (in_array($eventName, ['page_view', 'scroll', 'mouse_move'])) {
            return $this->checkRateLimit($eventName);
        }

        return true;
    }

    /**
     * Check rate limit for event tracking.
     */
    private function checkRateLimit(string $eventName): bool
    {
        // This would typically use a rate limiting service
        // For now, return true (no rate limiting)
        return true;
    }

    /**
     * Get the priority level for this event.
     */
    public function getPriority(): string
    {
        $eventName = $this->input('event_name');

        return match($eventName) {
            'purchase', 'conversion', 'signup' => 'high',
            'form_submit', 'contact_form' => 'medium',
            'click', 'page_view' => 'low',
            'scroll', 'mouse_move' => 'very_low',
            default => 'medium',
        };
    }

    /**
     * Get the event category.
     */
    public function getCategory(): string
    {
        $eventName = $this->input('event_name');

        return match($eventName) {
            'page_view' => 'navigation',
            'click' => 'interaction',
            'form_submit', 'contact_form' => 'conversion',
            'purchase', 'signup' => 'conversion',
            'search' => 'search',
            'error' => 'error',
            'scroll', 'mouse_move' => 'engagement',
            default => 'general',
        };
    }

    /**
     * Get the event description for logging.
     */
    public function getDescription(): string
    {
        $eventName = $this->input('event_name');
        $pageUrl = $this->input('page_url', '');
        
        return "Event: {$eventName} on page: {$pageUrl}";
    }
}
