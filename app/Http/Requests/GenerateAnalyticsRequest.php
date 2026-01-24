<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateAnalyticsRequest extends FormRequest
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
            'data_source' => 'required|string|in:events,sessions,conversions,all',
            'analysis_type' => 'required|string|in:overview,real_time,trends,behavior,conversion',
            'time_range' => 'required|string|in:1d,7d,30d,90d,1y',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'filters' => 'nullable|array',
            'filters.event_type' => 'nullable|string',
            'filters.page_url' => 'nullable|string',
            'filters.device_type' => 'nullable|string|in:desktop,mobile,tablet',
            'filters.user_type' => 'nullable|string|in:new,returning,all',
            'granularity' => 'nullable|string|in:hourly,daily,weekly,monthly',
            'include_predictions' => 'nullable|boolean',
            'include_recommendations' => 'nullable|boolean',
            'export_format' => 'nullable|string|in:json,csv,xlsx',
            'limit' => 'nullable|integer|min:1|max:10000',
            'offset' => 'nullable|integer|min:0',
            'sort_by' => 'nullable|string|in:date,events,conversions,engagement',
            'sort_order' => 'nullable|string|in:asc,desc',
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
            'data_source.required' => 'مصدر البيانات مطلوب',
            'data_source.in' => 'مصدر البيانات يجب أن يكون: events, sessions, conversions, أو all',
            'analysis_type.required' => 'نوع التحليل مطلوب',
            'analysis_type.in' => 'نوع التحليل يجب أن يكون: overview, real_time, trends, behavior, أو conversion',
            'time_range.required' => 'الفترة الزمنية مطلوبة',
            'time_range.in' => 'الفترة الزمنية يجب أن تكون: 1d, 7d, 30d, 90d, أو 1y',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
            'filters.device_type.in' => 'نوع الجهاز يجب أن يكون: desktop, mobile, أو tablet',
            'filters.user_type.in' => 'نوع المستخدم يجب أن يكون: new, returning, أو all',
            'granularity.in' => 'الدقة الزمنية يجب أن تكون: hourly, daily, weekly, أو monthly',
            'export_format.in' => 'تنسيق التصدير يجب أن يكون: json, csv, أو xlsx',
            'limit.min' => 'الحد الأدنى للنتائج هو 1',
            'limit.max' => 'الحد الأقصى للنتائج هو 10000',
            'offset.min' => 'الإزاحة يجب أن تكون 0 أو أكثر',
            'sort_by.in' => 'الفرز يجب أن يكون: date, events, conversions, أو engagement',
            'sort_order.in' => 'ترتيب الفرز يجب أن يكون: asc أو desc',
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
            'data_source' => 'مصدر البيانات',
            'analysis_type' => 'نوع التحليل',
            'time_range' => 'الفترة الزمنية',
            'start_date' => 'تاريخ البداية',
            'end_date' => 'تاريخ النهاية',
            'filters' => 'الفلاتر',
            'filters.event_type' => 'نوع الحدث',
            'filters.page_url' => 'رابط الصفحة',
            'filters.device_type' => 'نوع الجهاز',
            'filters.user_type' => 'نوع المستخدم',
            'granularity' => 'الدقة الزمنية',
            'include_predictions' => 'تضمين التنبؤات',
            'include_recommendations' => 'تضمين التوصيات',
            'export_format' => 'تنسيق التصدير',
            'limit' => 'الحد',
            'offset' => 'الإزاحة',
            'sort_by' => 'الفرز حسب',
            'sort_order' => 'ترتيب الفرز',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'include_predictions' => $this->boolean('include_predictions', false),
            'include_recommendations' => $this->boolean('include_recommendations', false),
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function configure(): void
    {
        $this->addRules([
            'start_date' => [
                'required_if:time_range,custom',
                function ($attribute, $value, $fail) {
                    if ($this->input('time_range') === 'custom' && !$value) {
                        $fail('تاريخ البداية مطلوب عند استخدام فترة مخصصة');
                    }
                },
            ],
            'end_date' => [
                'required_if:time_range,custom',
                function ($attribute, $value, $fail) {
                    if ($this->input('time_range') === 'custom' && !$value) {
                        $fail('تاريخ النهاية مطلوب عند استخدام فترة مخصصة');
                    }
                },
            ],
        ]);

        // Add conditional validation for specific analysis types
        if ($this->input('analysis_type') === 'behavior') {
            $this->addRules([
                'granularity' => 'required|string|in:hourly,daily,weekly,monthly',
            ]);
        }

        if ($this->input('analysis_type') === 'conversion') {
            $this->addRules([
                'filters.conversion_type' => 'nullable|string|in:purchase,signup,lead,contact',
            ]);
        }

        if ($this->input('include_predictions')) {
            $this->addRules([
                'prediction_horizon' => 'nullable|integer|min:1|max:365',
                'prediction_confidence' => 'nullable|numeric|min:0|max:100',
            ]);
        }
    }

    /**
     * Get the validated data with additional processing.
     */
    protected function getValidatedData(): array
    {
        $data = parent::getValidatedData();

        // Process time range
        if ($this->input('time_range') !== 'custom') {
            $data['start_date'] = $this->calculateStartDate($this->input('time_range'));
            $data['end_date'] = now();
        }

        // Set default values
        $data['limit'] = $data['limit'] ?? 1000;
        $data['offset'] = $data['offset'] ?? 0;
        $data['sort_by'] = $data['sort_by'] ?? 'date';
        $data['sort_order'] = $data['sort_order'] ?? 'desc';

        return $data;
    }

    /**
     * Calculate start date based on time range.
     */
    private function calculateStartDate(string $timeRange): \Carbon\Carbon
    {
        return match($timeRange) {
            '1d' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDays(30),
        };
    }

    /**
     * Get the analysis description for logging.
     */
    public function getAnalysisDescription(): string
    {
        $type = match($this->input('analysis_type')) {
            'overview' => 'نظرة عامة',
            'real_time' => 'التحليل الفوري',
            'trends' => 'تحليل الاتجاهات',
            'behavior' => 'سلوك المستخدمين',
            'conversion' => 'تحليل التحويلات',
            default => 'تحليل عام'
        };

        $source = match($this->input('data_source')) {
            'events' => 'الأحداث',
            'sessions' => 'الجلسات',
            'conversions' => 'التحويلات',
            'all' => 'جميع البيانات',
            default => 'البيانات'
        };

        $range = match($this->input('time_range')) {
            '1d' => '24 ساعة',
            '7d' => '7 أيام',
            '30d' => '30 يوم',
            '90d' => '90 يوم',
            '1y' => 'سنة',
            'custom' => 'فترة مخصصة',
            default => 'فترة افتراضية'
        };

        return "{$type} لـ {$source} خلال {$range}";
    }

    /**
     * Get the cache key for this request.
     */
    public function getCacheKey(): string
    {
        $key = 'analytics_' . md5(json_encode($this->validated()));
        
        // Add user context if authenticated
        if (auth()->check()) {
            $key .= '_user_' . auth()->id();
        }
        
        return $key;
    }

    /**
     * Get the cache TTL for this request.
     */
    public function getCacheTtl(): int
    {
        return match($this->input('time_range')) {
            '1d' => 300, // 5 minutes
            '7d' => 1800, // 30 minutes
            '30d' => 3600, // 1 hour
            '90d' => 7200, // 2 hours
            '1y' => 14400, // 4 hours
            default => 3600,
        };
    }

    /**
     * Check if this request should be cached.
     */
    public function shouldCache(): bool
    {
        return !$this->input('include_predictions') && 
               !$this->input('include_recommendations') && 
               $this->input('analysis_type') !== 'real_time';
    }

    /**
     * Get the rate limit key for this request.
     */
    public function getRateLimitKey(): string
    {
        return 'analytics_requests:' . ($this->ip() ?? 'unknown');
    }

    /**
     * Get the rate limit for this request.
     */
    public function getRateLimit(): int
    {
        return match($this->input('analysis_type')) {
            'real_time' => 60, // 60 requests per minute
            'overview' => 30, // 30 requests per minute
            'trends' => 20, // 20 requests per minute
            'behavior' => 15, // 15 requests per minute
            'conversion' => 10, // 10 requests per minute
            default => 30,
        };
    }
}
