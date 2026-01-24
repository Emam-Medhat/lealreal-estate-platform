<?php

namespace App\Http\Requests\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertySeoRequest extends FormRequest
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
            'page_title' => 'required|string|max:60',
            'meta_description' => 'required|string|max:160',
            'meta_keywords' => 'nullable|array',
            'meta_keywords.*' => 'string|max:100',
            'focus_keywords' => 'required|array|min:1',
            'focus_keywords.*' => 'string|max:100',
            'canonical_url' => 'nullable|url|max:500',
            'robots_meta' => 'nullable|array',
            'robots_meta.index' => 'nullable|boolean',
            'robots_meta.follow' => 'nullable|boolean',
            'robots_meta.noindex' => 'nullable|boolean',
            'robots_meta.nofollow' => 'nullable|boolean',
            'robots_meta.noarchive' => 'nullable|boolean',
            'robots_meta.nosnippet' => 'nullable|boolean',
            'og_tags' => 'nullable|array',
            'og_tags.og_title' => 'nullable|string|max:100',
            'og_tags.og_description' => 'nullable|string|max:300',
            'og_tags.og_image' => 'nullable|url|max:500',
            'og_tags.og_type' => 'nullable|string|max:50',
            'og_tags.og_url' => 'nullable|url|max:500',
            'og_tags.og_site_name' => 'nullable|string|max:100',
            'og_tags.og_locale' => 'nullable|string|max:10',
            'twitter_cards' => 'nullable|array',
            'twitter_cards.card' => 'nullable|string|in:summary,summary_large_image,app',
            'twitter_cards.title' => 'nullable|string|max:280',
            'twitter_cards.description' => 'nullable|string|max:280',
            'twitter_cards.image' => 'nullable|url|max:500',
            'twitter_cards.site' => 'nullable|string|max:100',
            'twitter_cards.creator' => 'nullable|string|max:100',
            'structured_data' => 'nullable|array',
            'structured_data.type' => 'nullable|string|max:50',
            'structured_data.name' => 'nullable|string|max:100',
            'structured_data.description' => 'nullable|string|max:300',
            'structured_data.image' => 'nullable|url|max:500',
            'structured_data.url' => 'nullable|url|max:500',
            'structured_data.price' => 'nullable|string|max:50',
            'structured_data.price_currency' => 'nullable|string|max:3',
            'structured_data.availability' => 'nullable|string|max:20',
            'structured_data.brand' => 'nullable|string|max:100',
            'structured_data.aggregate_rating' => 'nullable|numeric|min:0|max:5',
            'structured_data.review_count' => 'nullable|integer|min:0',
            'content_optimization' => 'nullable|array',
            'content_optimization.heading_structure' => 'boolean',
            'content_optimization.keyword_density' => 'boolean',
            'content_optimization.readability_score' => 'boolean',
            'content_optimization.word_count' => 'nullable|integer|min:100',
            'content_optimization.heading_tags' => 'nullable|array',
            'content_optimization.heading_tags.*.level' => 'required|string|in:h1,h2,h3,h4,h5,h6',
            'content_optimization.heading_tags.*.text' => 'required|string|max:200',
            'content_optimization.heading_tags.*.keyword_density' => 'nullable|numeric|min:0|max:100',
            'technical_seo' => 'nullable|array',
            'technical_seo.mobile_friendly' => 'boolean',
            'technical_seo.https_enabled' => 'boolean',
            'technical_seo.xml_sitemap' => 'boolean',
            'technical_seo.robots_txt' => 'boolean',
            'technical_seo.breadcrumb_navigation' => 'boolean',
            'technical_seo.schema_markup' => 'boolean',
            'technical_seo.page_speed' => 'nullable|integer|min:0|max:100',
            'technical_seo.core_web_vitals' => 'nullable|array',
            'technical_seo.core_web_vitals.lcp' => 'nullable|numeric|min:0|max:100',
            'technical_seo.core_web_vitals.fid' => 'nullable|numeric|min:0|max:100',
            'technical_seo.core_web_vitals.cls' => 'nullable|numeric|min:0|max:100',
            'tracking_analytics' => 'nullable|array',
            'tracking_analytics.google_analytics' => 'boolean',
            'tracking_analytics.google_search_console' => 'boolean',
            'tracking_analytics.bing_webmaster_tools' => 'boolean',
            'tracking_analytics.google_tag_manager' => 'boolean',
            'tracking_analytics.facebook_pixel' => 'boolean',
            'tracking_analytics.tracking_code' => 'nullable|string|max:10000',
            'local_seo' => 'nullable|array',
            'local_seo.google_business_profile' => 'boolean',
            'local_seo.local_citations' => 'nullable|array',
            'local_seo.local_citations.*.name' => 'required|string|max:100',
            'local_seo.local_citations.*.url' => 'required|url|max:500',
            'local_seo.local_citations.*.category' => 'required|string|max:50',
            'local_seo.local_reviews' => 'nullable|array',
            'local_seo.local_reviews.platform' => 'nullable|string|max:50',
            'local_seo.local_reviews.rating' => 'nullable|numeric|min:0|max:5',
            'local_seo.local_reviews.review_count' => 'nullable|integer|min:0',
            'keyword_research' => 'nullable|array',
            'keyword_research.primary_keywords' => 'nullable|array',
            'keyword_research.primary_keywords.*.keyword' => 'required|string|max:100',
            'keyword_research.primary_keywords.*.search_volume' => 'nullable|integer|min:0',
            'keyword_research.primary_keywords.*.difficulty' => 'nullable|string|in:easy,medium,hard',
            'keyword_research.primary_keywords.*.intent' => 'nullable|string|in:informational,navigational,transactional,commercial',
            'keyword_research.secondary_keywords' => 'nullable|array',
            'keyword_research.secondary_keywords.*.keyword' => 'required|string|max:100',
            'keyword_research.secondary_keywords.*.search_volume' => 'nullable|integer|min:0',
            'keyword_research.secondary_keywords.*.difficulty' => 'nullable|string|in:easy,medium,hard',
            'keyword_research.secondary_keywords.*.intent' => 'nullable|string|in:informational,navigational,transactional,commercial',
            'competitor_analysis' => 'nullable|array',
            'competitor_analysis.competitors' => 'nullable|array',
            'competitor_analysis.competitors.*.name' => 'required|string|max:100',
            'competitor_analysis.competitors.*.url' => 'required|url|max:500',
            'competitor_analysis.competitors.*.seo_score' => 'nullable|integer|min:0|max:100',
            'competitor_analysis.competitors.*.keywords' => 'nullable|integer|min:0',
            'competitor_analysis.competitors.*.traffic' => 'nullable|integer|min:0',
            'competitor_analysis.keyword_overlap' => 'nullable|numeric|min:0|max:100',
            'competitor_analysis.backlink_gap' => 'nullable|integer|min:0',
            'competitor_analysis.content_gap' => 'nullable|array',
            'competitor_analysis.content_gap.*.topic' => 'required|string|max:200',
            'competitor_analysis.content_gap.*.opportunity' => 'required|string|max:500',
            'performance_tracking' => 'nullable|array',
            'performance_tracking.rankings' => 'nullable|array',
            'performance_tracking.rankings.*.keyword' => 'required|string|max:100',
            'performance_tracking.rankings.*.position' => 'required|integer|min:1',
            'performance_tracking.rankings.*.url' => 'required|url|max:500',
            'performance_tracking.rankings.*.date' => 'required|date',
            'performance_tracking.traffic_metrics' => 'nullable|array',
            'performance_tracking.traffic_metrics.organic_traffic' => 'nullable|integer|min:0',
            'performance_tracking.traffic_metrics.direct_traffic' => 'nullable|integer|min:0',
            'performance_tracking.traffic_metrics.referral_traffic' => 'nullable|integer|min:0',
            'performance_tracking.traffic_metrics.social_traffic' => 'nullable|integer|min:0',
            'performance_tracking.conversion_metrics' => 'nullable|array',
            'performance_tracking.conversion_metrics.conversions' => 'nullable|integer|min:0',
            'performance_tracking.conversion_metrics.conversion_rate' => 'nullable|numeric|min:0|max:100',
            'performance_tracking.conversion_metrics.cost_per_conversion' => 'nullable|numeric|min:0',
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
            'page_title.required' => 'حقل عنوان الصفحة مطلوب',
            'page_title.max' => 'عنوان الصفحة يجب ألا يزيد عن 60 حرف',
            'meta_description.required' => 'حقل الوصف التعريفي مطلوب',
            'meta_description.max' => 'الوصف التعريفي يجب ألا يزيد عن 160 حرف',
            'meta_keywords.*.max' => 'الكلمة المفتاحية يجب ألا تزيد عن 100 حرف',
            'focus_keywords.required' => 'يجب تحديد كلمات رئيسية',
            'focus_keywords.min' => 'يجب تحديد كلمة رئيسية واحدة على الأقل',
            'focus_keywords.*.max' => 'الكلمة الرئيسية يجب ألا تزيد عن 100 حرف',
            'canonical_url.url' => 'الرابط الأساسي يجب أن يكون رابطاً صالحاً',
            'canonical_url.max' => 'الرابط الأساسي يجب ألا يزيد عن 500 حرف',
            'og_tags.og_title.max' => 'عنوان Open Graph يجب ألا يزيد عن 100 حرف',
            'og_tags.og_description.max' => 'وصف Open Graph يجب ألا يزيد عن 300 حرف',
            'og_tags.og_image.url' => 'صورة Open Graph يجب أن تكون رابطاً صالحاً',
            'og_tags.og_image.max' => 'رابط صورة Open Graph يجب ألا يزيد عن 500 حرف',
            'og_tags.og_type.max' => 'نوع Open Graph يجب ألا يزيد عن 50 حرف',
            'og_tags.og_url.url' => 'رابط Open Graph يجب أن يكون رابطاً صالحاً',
            'og_tags.og_url.max' => 'رابط Open Graph يجب ألا يزيد عن 500 حرف',
            'og_tags.og_site_name.max' => 'اسم الموقع Open Graph يجب ألا يزيد عن 100 حرف',
            'og_tags.og_locale.max' => 'لغة Open Graph يجب ألا تزيد عن 10 أحرف',
            'twitter_cards.card.in' => 'نوع بطاقة تويتر يجب أن يكون صالحاً',
            'twitter_cards.title.max' => 'عنوان تويتر يجب ألا يزيد عن 280 حرف',
            'twitter_cards.description.max' => 'وصف تويتر يجب ألا يزيد عن 280 حرف',
            'twitter_cards.image.url' => 'صورة تويتر يجب أن تكون رابطاً صالحاً',
            'twitter_cards.image.max' => 'رابط صورة تويتر يجب ألا يزيد عن 500 حرف',
            'twitter_cards.site.max' => 'موقع تويتر يجب ألا يزيد عن 100 حرف',
            'twitter_cards.creator.max' => 'منشئ تويتر يجب ألا يزيد عن 100 حرف',
            'structured_data.type.max' => 'نوع البيانات المنظمة يجب ألا يزيد عن 50 حرف',
            'structured_data.name.max' => 'اسم البيانات المنظمة يجب ألا يزيد عن 100 حرف',
            'structured_data.description.max' => 'وصف البيانات المنظمة يجب ألا يزيد عن 300 حرف',
            'structured_data.image.url' => 'صورة البيانات المنظمة يجب أن تكون رابطاً صالحاً',
            'structured_data.image.max' => 'رابط صورة البيانات المنظمة يجب ألا يزيد عن 500 حرف',
            'structured_data.url.url' => 'رابط البيانات المنظمة يجب أن يكون رابطاً صالحاً',
            'structured_data.url.max' => 'رابط البيانات المنظمة يجب ألا يزيد عن 500 حرف',
            'structured_data.price.max' => 'السعر يجب ألا يزيد عن 50 حرف',
            'structured_data.price_currency.max' => 'عملة السعر يجب ألا تزيد عن 3 أحرف',
            'structured_data.availability.max' => 'التوفر يجب ألا يزيد عن 20 حرف',
            'structured_data.brand.max' => 'العلامة التجارية يجب ألا تزيد عن 100 حرف',
            'structured_data.aggregate_rating.numeric' => 'التقييم الإجمالي يجب أن يكون رقماً',
            'structured_data.aggregate_rating.min' => 'التقييم الإجمالي يجب أن يكون أكبر من أو يساوي 0',
            'structured_data.aggregate_rating.max' => 'التقييم الإجمالي يجب أن يكون أقل من أو يساوي 5',
            'structured_data.review_count.min' => 'عدد المراجعات يجب أن يكون أكبر من أو يساوي 0',
            'content_optimization.word_count.min' => 'عدد الكلمات يجب أن يكون على الأقل 100',
            'content_optimization.heading_tags.*.level.required' => 'مستوى العنوان مطلوب',
            'content_optimization.heading_tags.*.level.in' => 'مستوى العنوان غير صالح',
            'content_optimization.heading_tags.*.text.required' => 'نص العنوان مطلوب',
            'content_optimization.heading_tags.*.text.max' => 'نص العنوان يجب ألا يزيد عن 200 حرف',
            'content_optimization.heading_tags.*.keyword_density.numeric' => 'كثافة الكلمات المفتاحية يجب أن تكون رقماً',
            'content_optimization.heading_tags.*.keyword_density.min' => 'كثافة الكلمات المفتاحية يجب أن تكون أكبر من أو تساوي 0',
            'content_optimization.heading_tags.*.keyword_density.max' => 'كثافة الكلمات المفتاحية يجب أن تكون أقل من أو تساوي 100',
            'technical_seo.page_speed.min' => 'سرعة الصفحة يجب أن تكون أكبر من أو تساوي 0',
            'technical_seo.page_speed.max' => 'سرعة الصفحة يجب أن تكون أقل من أو تساوي 100',
            'technical_seo.core_web_vitals.lcp.numeric' => 'أكبر رسم للمحتوى يجب أن يكون رقماً',
            'technical_seo.core_web_vitals.lcp.min' => 'أكبر رسم للمحتوى يجب أن يكون أكبر من أو يساوي 0',
            'technical_seo.core_web_vitals.lcp.max' => 'أكبر رسم للمحتوى يجب أن يكون أقل من أو يساوي 100',
            'technical_seo.core_web_vitals.fid.numeric' => 'أول إدخال يجب أن يكون رقماً',
            'technical_seo.core_web_vitals.fid.min' => 'أول إدخال يجب أن يكون أكبر من أو يساوي 0',
            'technical_seo.core_web_vitals.fid.max' => 'أول إدخال يجب أن يكون أقل من أو يساوي 100',
            'technical_seo.core_web_vitals.cls.numeric' => 'تغير التخطأئي التراكمي يجب أن يكون رقماً',
            'technical_seo.core_web_vitals.cls.min' => 'تغير التخطأئي التراكمي يجب أن يكون أكبر من أو يساوي 0',
            'technical_seo.core_web_vitals.cls.max' => 'تغير التخطأئي التراكمي يجب أن يكون أقل من أو يساوي 100',
            'tracking_analytics.tracking_code.max' => 'كود التتبع يجب ألا يزيد عن 10000 حرف',
            'local_seo.local_citations.*.name.required' => 'اسم الاقتباس المحلي مطلوب',
            'local_seo.local_citations.*.name.max' => 'اسم الاقتباس المحلي يجب ألا يزيد عن 100 حرف',
            'local_seo.local_citations.*.url.required' => 'رابط الاقتباس المحلي مطلوب',
            'local_seo.local_citations.*.url.url' => 'رابط الاقتباس المحلي يجب أن يكون رابطاً صالحاً',
            'local_seo.local_citations.*.url.max' => 'رابط الاقتباس المحلي يجب ألا يزيد عن 500 حرف',
            'local_seo.local_citations.*.category.required' => 'فئة الاقتباس المحلي مطلوبة',
            'local_seo.local_citations.*.category.max' => 'فئة الاقتباس المحلي يجب ألا تزيد عن 50 حرف',
            'local_seo.local_reviews.platform.max' => 'منصة المراجعات المحلية يجب ألا تزيد عن 50 حرف',
            'local_seo.local_reviews.rating.numeric' => 'تقييم المراجعات المحلية يجب أن يكون رقماً',
            'local_seo.local_reviews.rating.min' => 'تقييم المراجعات المحلية يجب أن يكون أكبر من أو يساوي 0',
            'local_seo.local_reviews.rating.max' => 'تقييم المراجعات المحلية يجب أن يكون أقل من أو يساوي 5',
            'local_seo.local_reviews.review_count.min' => 'عدد المراجعات المحلية يجب أن يكون أكبر من أو يساوي 0',
            'keyword_research.primary_keywords.*.keyword.required' => 'الكلمة المفتاحية الأولية مطلوبة',
            'keyword_research.primary_keywords.*.keyword.max' => 'الكلمة المفتاحية الأولية يجب ألا تزيد عن 100 حرف',
            'keyword_research.primary_keywords.*.search_volume.min' => 'حجم البحث يجب أن يكون أكبر من أو يساوي 0',
            'keyword_research.primary_keywords.*.difficulty.in' => 'صعوبة الكلمة المفتاحية غير صالحة',
            'keyword_research.primary_keywords.*.intent.in' => 'نية البحث غير صالحة',
            'keyword_research.secondary_keywords.*.keyword.required' => 'الكلمة المفتاحية الثانوية مطلوبة',
            'keyword_research.secondary_keywords.*.keyword.max' => 'الكلمة المفتاحية الثانوية يجب ألا يزيد عن 100 حرف',
            'keyword_research.secondary_keywords.*.search_volume.min' => 'حجم البحث يجب أن يكون أكبر من أو يساوي 0',
            'keyword_research.secondary_keywords.*.difficulty.in' => 'صعوبة الكلمة المفتاحية غير صالحة',
            'keyword_research.secondary_keywords.*.intent.in' => 'نية البحث غير صالحة',
            'competitor_analysis.competitors.*.name.required' => 'اسم المنافس مطلوب',
            'competitor_analysis.competitors.*.name.max' => 'اسم المنافس يجب ألا يزيد عن 100 حرف',
            'competitor_analysis.competitors.*.url.required' => 'رابط المنافس مطلوب',
            'competitor_analysis.competitors.*.url.url' => 'رابط المنافس يجب أن يكون رابطاً صالحاً',
            'competitor_analysis.competitors.*.url.max' => 'رابط المنافس يجب ألا يزيد عن 500 حرف',
            'competitor_analysis.competitors.*.seo_score.min' => 'درجة SEO المنافس يجب أن تكون أكبر من أو تساوي 0',
            'competitor_analysis.competitors.*.seo_score.max' => 'درجة SEO المنافس يجب أن تكون أقل من أو تساوي 100',
            'competitor_analysis.competitors.*.keywords.min' => 'عدد الكلمات المفتاحية للمنافس يجب أن يكون أكبر من أو يساوي 0',
            'competitor_analysis.competitors.*.traffic.min' => 'حركة المرور للمنافس يجب أن تكون أكبر من أو تساوي 0',
            'competitor_analysis.keyword_overlap.numeric' => 'تداخل الكلمات المفتاحية يجب أن يكون رقماً',
            'competitor_analysis.keyword_overlap.min' => 'تداخل الكلمات المفتاحية يجب أن يكون أكبر من أو يساوي 0',
            'competitor_analysis.keyword_overlap.max' => 'تداخل الكلمات المفتاحية يجب أن يكون أقل من أو يساوي 100',
            'competitor_analysis.backlink_gap.min' => 'فجوة الروابط الخلفية يجب أن تكون أكبر من أو تساوي 0',
            'competitor_analysis.content_gap.*.topic.required' => 'موضوع المحتوى مطلوب',
            'competitor_analysis.content_gap.*.topic.max' => 'موضوع المحتوى يجب ألا يزيد عن 200 حرف',
            'competitor_analysis.content_gap.*.opportunity.required' => 'الفرصة مطلوبة',
            'competitor_analysis.content_gap.*.opportunity.max' => 'الفرصة يجب ألا تزيد عن 500 حرف',
            'performance_tracking.rankings.*.keyword.required' => 'الكلمة المفتاحية للترتيب مطلوبة',
            'performance_tracking.rankings.*.keyword.max' => 'الكلمة المفتاحية للترتيب يجب ألا يزيد عن 100 حرف',
            'performance_tracking.rankings.*.position.required' => 'موضع الترتيب مطلوب',
            'performance_tracking.rankings.*.position.min' => 'موضع الترتيب يجب أن يكون على الأقل 1',
            'performance_tracking.rankings.*.url.required' => 'رابط الترتيب مطلوب',
            'performance_tracking.rankings.*.url.url' => 'رابط الترتيب يجب أن يكون رابطاً صالحاً',
            'performance_tracking.rankings.*.url.max' => 'رابط الترتيب يجب ألا يزيد عن 500 حرف',
            'performance_tracking.rankings.*.date.required' => 'تاريخ الترتيب مطلوب',
            'performance_tracking.traffic_metrics.organic_traffic.min' => 'حركة المرور العضوية يجب أن تكون أكبر من أو تساوي 0',
            'performance_tracking.traffic_metrics.direct_traffic.min' => 'حركة المرور المباشرة يجب أن تكون أكبر من أو تساوي 0',
            'performance_tracking.traffic_metrics.referral_traffic.min' => 'حركة المرور بالإحالة يجب أن تكون أكبر من أو تساوي 0',
            'performance_tracking.traffic_metrics.social_traffic.min' => 'حركة المرور الاجتماعي يجب أن تكون أكبر من أو تساوي 0',
            'performance_tracking.conversion_metrics.conversions.min' => 'التحويلات يجب أن تكون أكبر من أو تساوي 0',
            'performance_tracking.conversion_metrics.conversion_rate.numeric' => 'معدل التحويل يجب أن يكون رقماً',
            'performance_tracking.conversion_metrics.conversion_rate.min' => 'معدل التحويل يجب أن يكون أكبر من أو يساوي 0',
            'performance_tracking.conversion_metrics.conversion_rate.max' => 'معدل التحويل يجب أن يكون أقل من أو يساوي 100',
            'performance_tracking.conversion_metrics.cost_per_conversion.numeric' => 'تكلفة التحويل يجب أن تكون رقماً',
            'performance_tracking.conversion_metrics.cost_per_conversion.min' => 'تكلفة التحويل يجب أن تكون أكبر من أو يساوي 0',
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
            'page_title' => 'عنوان الصفحة',
            'meta_description' => 'الوصف التعريفي',
            'meta_keywords' => 'الكلمات المفتاحية التعريفية',
            'focus_keywords' => 'الكلمات الرئيسية',
            'canonical_url' => 'الرابط الأساسي',
            'robots_meta.index' => 'فهرسة الروبوتات',
            'robots_meta.follow' => 'متابعة الروبوتات',
            'robots_meta.noindex' => 'عدم الفهرسة',
            'robots_meta.nofollow' => 'عدم المتابعة',
            'robots_meta.noarchive' => 'عدم الأرشفة',
            'robots_meta.nosnippet' => 'عدم المقتطف',
            'og_tags.og_title' => 'عنوان Open Graph',
            'og_tags.og_description' => 'وصف Open Graph',
            'og_tags.og_image' => 'صورة Open Graph',
            'og_tags.og_type' => 'نوع Open Graph',
            'og_tags.og_url' => 'رابط Open Graph',
            'og_tags.og_site_name' => 'اسم الموقع Open Graph',
            'og_tags.og_locale' => 'لغة Open Graph',
            'twitter_cards.card' => 'نوع بطاقة تويتر',
            'twitter_cards.title' => 'عنوان تويتر',
            'twitter_cards.description' => 'وصف تويتر',
            'twitter_cards.image' => 'صورة تويتر',
            'twitter_cards.site' => 'موقع تويتر',
            'twitter_cards.creator' => 'منشئ تويتر',
            'structured_data.type' => 'نوع البيانات المنظمة',
            'structured_data.name' => 'اسم البيانات المنظمة',
            'structured_data.description' => 'وصف البيانات المنظمة',
            'structured_data.image' => 'صورة البيانات المنظمة',
            'structured_data.url' => 'رابط البيانات المنظمة',
            'structured_data.price' => 'السعر',
            'structured_data.price_currency' => 'عملة السعر',
            'structured_data.availability' => 'التوفر',
            'structured_data.brand' => 'العلامة التجارية',
            'structured_data.aggregate_rating' => 'التقييم الإجمالي',
            'structured_data.review_count' => 'عدد المراجعات',
            'content_optimization.heading_structure' => 'بنية العناوين',
            'content_optimization.keyword_density' => 'كثافة الكلمات المفتاحية',
            'content_optimization.readability_score' => 'درجة القراءة',
            'content_optimization.word_count' => 'عدد الكلمات',
            'content_optimization.heading_tags' => 'علامات العناوين',
            'technical_seo.mobile_friendly' => 'ملائم للجوال',
            'technical_seo.https_enabled' => 'HTTPS مفعال',
            'technical_seo.xml_sitemap' => 'خريطة موقع XML',
            'technical_seo.robots_txt' => 'ملف robots.txt',
            'technical_seo.breadcrumb_navigation' => 'تنقل الخبز',
            'technical_seo.schema_markup' => 'ترميز البيانات المنظمة',
            'technical_seo.page_speed' => 'سرعة الصفحة',
            'technical_seo.core_web_vitals' => 'مؤشرات الويب الأساسية',
            'technical_seo.core_web_vitals.lcp' => 'أكبر رسم للمحتوى',
            'technical_seo.core_web_vitals.fid' => 'أول إدخال',
            'technical_seo.core_web_vitals.cls' => 'تغير التخطأئي التراكمي',
            'tracking_analytics.google_analytics' => 'تحليلات جوجل',
            'tracking_analytics.google_search_console' => 'وحدة تحكم بحث جوجل',
            'tracking_analytics.bing_webmaster_tools' => 'أدوات مشرفي بينج',
            'tracking_analytics.google_tag_manager' => 'مدير العلامات جوجل',
            'tracking_analytics.facebook_pixel' => 'بكسل فيسبوك',
            'tracking_analytics.tracking_code' => 'كود التتبع',
            'local_seo.google_business_profile' => 'ملف عمل جوجل',
            'local_seo.local_citations' => 'الاقتباسات المحلية',
            'local_seo.local_reviews' => 'المراجعات المحلية',
            'keyword_research.primary_keywords' => 'الكلمات المفتاحية الأولية',
            'keyword_research.secondary_keywords' => 'الكلمات المفتاحية الثانوية',
            'competitor_analysis.competitors' => 'المنافسون',
            'competitor_analysis.keyword_overlap' => 'تداخل الكلمات المفتاحية',
            'competitor_analysis.backlink_gap' => 'فجوة الروابط الخلفية',
            'competitor_analysis.content_gap' => 'فجوة المحتوى',
            'performance_tracking.rankings' => 'الترتيبات',
            'performance_tracking.traffic_metrics' => 'مقاييسات حركة المرور',
            'performance_tracking.conversion_metrics' => 'مقاييسات التحويل',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function configure(): void
    {
        $this->errorBag = 'storePropertySeo';
    }

    /**
     * Get the validated data.
     *
     * @return array<string, mixed>
     */
    public function validated(): array
    {
        $validated = parent::validated();

        // Convert arrays to JSON
        if (isset($validated['meta_keywords'])) {
            $validated['meta_keywords'] = json_encode($validated['meta_keywords']);
        }
        if (isset($validated['focus_keywords'])) {
            $validated['focus_keywords'] = json_encode($validated['focus_keywords']);
        }
        if (isset($validated['robots_meta'])) {
            $validated['robots_meta'] = json_encode($validated['robots_meta']);
        }
        if (isset($validated['og_tags'])) {
            $validated['og_tags'] = json_encode($validated['og_tags']);
        }
        if (isset($validated['twitter_cards'])) {
            $validated['twitter_cards'] = json_encode($validated['twitter_cards']);
        }
        if (isset($validated['structured_data'])) {
            $validated['structured_data'] = json_encode($validated['structured_data']);
        }
        if (isset($validated['content_optimization'])) {
            $validated['content_optimization'] = json_encode($validated['content_optimization']);
        }
        if (isset($validated['technical_seo'])) {
            $validated['technical_seo'] = json_encode($validated['technical_seo']);
        }
        if (isset($validated['tracking_analytics'])) {
            $validated['tracking_analytics'] = json_encode($validated['tracking_analytics']);
        }
        if (isset($validated['local_seo'])) {
            $validated['local_seo'] = json_encode($validated['local_seo']);
        }
        if (isset($validated['keyword_research'])) {
            $validated['keyword_research'] = json_encode($validated['keyword_research']);
        }
        if (isset($validated['competitor_analysis'])) {
            $validated['competitor_analysis'] = json_encode($validated['competitor_analysis']);
        }
        if (isset($validated['performance_tracking'])) {
            $validated['performance_tracking'] = json_encode($validated['performance_tracking']);
        }

        return $validated;
    }

    /**
     * Get the after validation hook.
     */
    protected function after(): void
    {
        // Validate page title length
        if ($this->has('page_title') && strlen($this->input('page_title')) > 60) {
            $this->validator->errors()->add('page_title', 'عنوان الصفحة يجب ألا يزيد عن 60 حرف لتحسين SEO');
        }

        // Validate meta description length
        if ($this->has('meta_description') && strlen($this->input('meta_description')) > 160) {
            $this->validator->errors()->add('meta_description', 'الوصف التعريفي يجب ألا يزيد عن 160 حرف لتحسين SEO');
        }

        // Validate focus keywords count
        if ($this->has('focus_keywords') && count($this->input('focus_keywords')) > 10) {
            $this->validator->errors()->add('focus_keywords', 'يجب ألا تزيد عن 10 كلمات رئيسية');
        }

        // Validate structured data
        if ($this->has('structured_data.type') && $this->input('structured_data.type') === 'product' && !$this->has('structured_data.price')) {
            $this->validator->errors()->add('structured_data.price', 'يجب تحديد السعر للمنتج');
        }

        // Validate Open Graph tags
        if ($this->has('og_tags.og_image') && !$this->has('og_tags.og_title')) {
            $this->validator->errors()->add('og_tags.og_title', 'يجب تحديد عنوان Open Graph عند وجود صورة');
        }

        // Validate Twitter cards
        if ($this->has('twitter_cards.image') && !$this->has('twitter_cards.title')) {
            $this->validator->errors()->add('twitter_cards.title', 'يجب تحديد عنوان تويتر عند وجود صورة');
        }
    }
}
