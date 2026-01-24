<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\PropertySeo;
use App\Models\Property\Property;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PropertySeoController extends Controller
{
    /**
     * Display a listing of property SEO settings.
     */
    public function index()
    {
        $seoSettings = PropertySeo::with(['property'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Marketing/Seo/Index', [
            'seo_settings' => $seoSettings,
            'stats' => [
                'total_properties' => PropertySeo::count(),
                'optimized_properties' => PropertySeo::where('seo_score', '>=', 80)->count(),
                'needs_improvement' => PropertySeo::where('seo_score', '>=', 50)->where('seo_score', '<', 80)->count(),
                'poor_seo' => PropertySeo::where('seo_score', '<', 50)->count(),
                'average_seo_score' => PropertySeo::avg('seo_score') ?? 0,
                'total_keywords' => PropertySeo::sum('total_keywords'),
            ]
        ]);
    }

    /**
     * Show the form for creating new property SEO settings.
     */
    public function create()
    {
        $properties = Property::where('status', 'active')->get();
        
        return Inertia::render('Marketing/Seo/Create', [
            'properties' => $properties,
            'keyword_difficulties' => ['easy', 'medium', 'hard', 'very_hard'],
            'content_types' => ['page', 'blog_post', 'listing', 'neighborhood_guide', 'market_report'],
            'search_engines' => ['google', 'bing', 'yahoo', 'duckduckgo'],
        ]);
    }

    /**
     * Store newly created property SEO settings.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'page_title' => 'required|string|max:60',
            'meta_description' => 'required|string|max:160',
            'meta_keywords' => 'nullable|array',
            'meta_keywords.*' => 'string|max:100',
            'focus_keywords' => 'required|array',
            'focus_keywords.*' => 'string|max:100',
            'canonical_url' => 'nullable|string|max:500',
            'robots_meta' => 'nullable|array',
            'robots_meta.*' => 'string|in:index,noindex,follow,nofollow,noarchive,nosnippet,noimageindex',
            'og_tags' => 'nullable|array',
            'og_tags.og_title' => 'nullable|string|max:100',
            'og_tags.og_description' => 'nullable|string|max:200',
            'og_tags.og_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'og_tags.og_type' => 'nullable|string|max:50',
            'og_tags.og_url' => 'nullable|string|max:500',
            'twitter_cards' => 'nullable|array',
            'twitter_cards.card_type' => 'nullable|string|in:summary,summary_large_image,app,player',
            'twitter_cards.title' => 'nullable|string|max:100',
            'twitter_cards.description' => 'nullable|string|max:200',
            'twitter_cards.image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'twitter_cards.site' => 'nullable|string|max:100',
            'structured_data' => 'nullable|array',
            'structured_data.type' => 'nullable|string|max:100',
            'structured_data.name' => 'nullable|string|max:255',
            'structured_data.description' => 'nullable|string|max:500',
            'structured_data.image' => 'nullable|array',
            'structured_data.image.*' => 'string|max:500',
            'structured_data.address' => 'nullable|array',
            'structured_data.address.street_address' => 'nullable|string|max:255',
            'structured_data.address.address_locality' => 'nullable|string|max:100',
            'structured_data.address.address_region' => 'nullable|string|max:100',
            'structured_data.address.postal_code' => 'nullable|string|max:20',
            'structured_data.address.address_country' => 'nullable|string|max:100',
            'structured_data.geo_coordinates' => 'nullable|array',
            'structured_data.geo_coordinates.latitude' => 'nullable|numeric',
            'structured_data.geo_coordinates.longitude' => 'nullable|numeric',
            'structured_data.price' => 'nullable|numeric|min:0',
            'structured_data.currency' => 'nullable|string|max:3',
            'structured_data.availability' => 'nullable|string|max:50',
            'content_optimization' => 'nullable|array',
            'content_optimization.heading_structure' => 'boolean',
            'content_optimization.keyword_density' => 'boolean',
            'content_optimization.readability_score' => 'boolean',
            'content_optimization.word_count' => 'nullable|integer|min:0',
            'content_optimization.internal_links' => 'nullable|array',
            'content_optimization.internal_links.*' => 'string|max:500',
            'content_optimization.external_links' => 'nullable|array',
            'content_optimization.external_links.*' => 'string|max:500',
            'technical_seo' => 'nullable|array',
            'technical_seo.page_speed' => 'nullable|integer|min:0|max:100',
            'technical_seo.mobile_friendly' => 'boolean',
            'technical_seo.https_enabled' => 'boolean',
            'technical_seo.xml_sitemap' => 'boolean',
            'technical_seo.robots_txt' => 'boolean',
            'technical_seo.breadcrumb_navigation' => 'boolean',
            'technical_seo.schema_markup' => 'boolean',
            'tracking_analytics' => 'nullable|array',
            'tracking_analytics.google_analytics' => 'boolean',
            'tracking_analytics.google_search_console' => 'boolean',
            'tracking_analytics.bing_webmaster_tools' => 'boolean',
            'tracking_analytics.google_tag_manager' => 'boolean',
            'tracking_analytics.facebook_pixel' => 'boolean',
            'tracking_analytics.custom_tracking_code' => 'nullable|string',
            'local_seo' => 'nullable|array',
            'local_seo.google_business_profile' => 'boolean',
            'local_seo.local_citations' => 'nullable|array',
            'local_seo.local_citations.*' => 'string|max:255',
            'local_seo.local_reviews' => 'nullable|array',
            'local_seo.local_reviews.platform' => 'nullable|string|max:100',
            'local_seo.local_reviews.rating' => 'nullable|numeric|min:0|max:5',
            'local_seo.local_reviews.review_count' => 'nullable|integer|min:0',
            'keyword_research' => 'nullable|array',
            'keyword_research.primary_keywords' => 'nullable|array',
            'keyword_research.primary_keywords.*.keyword' => 'required|string|max:100',
            'keyword_research.primary_keywords.*.volume' => 'nullable|integer|min:0',
            'keyword_research.primary_keywords.*.difficulty' => 'nullable|string|in:easy,medium,hard,very_hard',
            'keyword_research.primary_keywords.*.cpc' => 'nullable|numeric|min:0',
            'keyword_research.secondary_keywords' => 'nullable|array',
            'keyword_research.secondary_keywords.*.keyword' => 'required|string|max:100',
            'keyword_research.secondary_keywords.*.volume' => 'nullable|integer|min:0',
            'keyword_research.secondary_keywords.*.difficulty' => 'nullable|string|in:easy,medium,hard,very_hard',
            'keyword_research.secondary_keywords.*.cpc' => 'nullable|numeric|min:0',
            'competitor_analysis' => 'nullable|array',
            'competitor_analysis.competitors' => 'nullable|array',
            'competitor_analysis.competitors.*.name' => 'required|string|max:255',
            'competitor_analysis.competitors.*.url' => 'required|string|max:500',
            'competitor_analysis.competitors.*.keywords' => 'nullable|array',
            'competitor_analysis.competitors.*.keywords.*' => 'string|max:100',
            'competitor_analysis.competitors.*.traffic' => 'nullable|integer|min:0',
            'competitor_analysis.competitors.*.backlinks' => 'nullable|integer|min:0',
            'performance_tracking' => 'nullable|array',
            'performance_tracking.target_keywords' => 'nullable|array',
            'performance_tracking.target_keywords.*' => 'string|max:100',
            'performance_tracking.current_rankings' => 'nullable|array',
            'performance_tracking.current_rankings.*.keyword' => 'required|string|max:100',
            'performance_tracking.current_rankings.*.position' => 'nullable|integer|min:1',
            'performance_tracking.current_rankings.*.search_engine' => 'nullable|string|in:google,bing,yahoo,duckduckgo',
            'performance_tracking.current_rankings.*.url' => 'nullable|string|max:500',
            'performance_tracking.traffic_sources' => 'nullable|array',
            'performance_tracking.traffic_sources.*.source' => 'required|string|max:100',
            'performance_tracking.traffic_sources.*.visits' => 'nullable|integer|min:0',
            'performance_tracking.traffic_sources.*.percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        // Calculate SEO score
        $seoScore = $this->calculateSeoScore($validated);

        $seoSetting = PropertySeo::create([
            'property_id' => $validated['property_id'],
            'page_title' => $validated['page_title'],
            'meta_description' => $validated['meta_description'],
            'meta_keywords' => $validated['meta_keywords'] ?? [],
            'focus_keywords' => $validated['focus_keywords'],
            'canonical_url' => $validated['canonical_url'] ?? null,
            'robots_meta' => $validated['robots_meta'] ?? [],
            'og_tags' => $validated['og_tags'] ?? [],
            'twitter_cards' => $validated['twitter_cards'] ?? [],
            'structured_data' => $validated['structured_data'] ?? [],
            'content_optimization' => $validated['content_optimization'] ?? [],
            'technical_seo' => $validated['technical_seo'] ?? [],
            'tracking_analytics' => $validated['tracking_analytics'] ?? [],
            'local_seo' => $validated['local_seo'] ?? [],
            'keyword_research' => $validated['keyword_research'] ?? [],
            'competitor_analysis' => $validated['competitor_analysis'] ?? [],
            'performance_tracking' => $validated['performance_tracking'] ?? [],
            'seo_score' => $seoScore,
            'total_keywords' => count($validated['focus_keywords']) + count($validated['meta_keywords'] ?? []),
        ]);

        // Handle OG image upload
        if ($request->hasFile('og_tags.og_image')) {
            $ogImagePath = $request->file('og_tags.og_image')->store('seo-og-images', 'public');
            $seoSetting->update(['og_tags' => array_merge($seoSetting->og_tags, ['og_image' => $ogImagePath])]);
        }

        // Handle Twitter card image upload
        if ($request->hasFile('twitter_cards.image')) {
            $twitterImagePath = $request->file('twitter_cards.image')->store('seo-twitter-images', 'public');
            $seoSetting->update(['twitter_cards' => array_merge($seoSetting->twitter_cards, ['image' => $twitterImagePath])]);
        }

        return redirect()->route('marketing.seo.index')
            ->with('success', 'تم إنشاء إعدادات SEO بنجاح');
    }

    /**
     * Display the specified property SEO settings.
     */
    public function show(PropertySeo $propertySeo)
    {
        $propertySeo->load(['property']);

        return Inertia::render('Marketing/Seo/Show', [
            'seo_setting' => $propertySeo,
            'analytics' => $this->getSeoAnalytics($propertySeo),
            'recommendations' => $this->getSeoRecommendations($propertySeo),
        ]);
    }

    /**
     * Show the form for editing the specified property SEO settings.
     */
    public function edit(PropertySeo $propertySeo)
    {
        $properties = Property::where('status', 'active')->get();
        
        return Inertia::render('Marketing/Seo/Edit', [
            'seo_setting' => $propertySeo,
            'properties' => $properties,
            'keyword_difficulties' => ['easy', 'medium', 'hard', 'very_hard'],
            'content_types' => ['page', 'blog_post', 'listing', 'neighborhood_guide', 'market_report'],
            'search_engines' => ['google', 'bing', 'yahoo', 'duckduckgo'],
        ]);
    }

    /**
     * Update the specified property SEO settings.
     */
    public function update(Request $request, PropertySeo $propertySeo)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'page_title' => 'required|string|max:60',
            'meta_description' => 'required|string|max:160',
            'meta_keywords' => 'nullable|array',
            'meta_keywords.*' => 'string|max:100',
            'focus_keywords' => 'required|array',
            'focus_keywords.*' => 'string|max:100',
            'canonical_url' => 'nullable|string|max:500',
            'robots_meta' => 'nullable|array',
            'robots_meta.*' => 'string|in:index,noindex,follow,nofollow,noarchive,nosnippet,noimageindex',
            'og_tags' => 'nullable|array',
            'og_tags.og_title' => 'nullable|string|max:100',
            'og_tags.og_description' => 'nullable|string|max:200',
            'og_tags.og_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'og_tags.og_type' => 'nullable|string|max:50',
            'og_tags.og_url' => 'nullable|string|max:500',
            'twitter_cards' => 'nullable|array',
            'twitter_cards.card_type' => 'nullable|string|in:summary,summary_large_image,app,player',
            'twitter_cards.title' => 'nullable|string|max:100',
            'twitter_cards.description' => 'nullable|string|max:200',
            'twitter_cards.image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'twitter_cards.site' => 'nullable|string|max:100',
            'structured_data' => 'nullable|array',
            'structured_data.type' => 'nullable|string|max:100',
            'structured_data.name' => 'nullable|string|max:255',
            'structured_data.description' => 'nullable|string|max:500',
            'structured_data.image' => 'nullable|array',
            'structured_data.image.*' => 'string|max:500',
            'structured_data.address' => 'nullable|array',
            'structured_data.address.street_address' => 'nullable|string|max:255',
            'structured_data.address.address_locality' => 'nullable|string|max:100',
            'structured_data.address.address_region' => 'nullable|string|max:100',
            'structured_data.address.postal_code' => 'nullable|string|max:20',
            'structured_data.address.address_country' => 'nullable|string|max:100',
            'structured_data.geo_coordinates' => 'nullable|array',
            'structured_data.geo_coordinates.latitude' => 'nullable|numeric',
            'structured_data.geo_coordinates.longitude' => 'nullable|numeric',
            'structured_data.price' => 'nullable|numeric|min:0',
            'structured_data.currency' => 'nullable|string|max:3',
            'structured_data.availability' => 'nullable|string|max:50',
            'content_optimization' => 'nullable|array',
            'content_optimization.heading_structure' => 'boolean',
            'content_optimization.keyword_density' => 'boolean',
            'content_optimization.readability_score' => 'boolean',
            'content_optimization.word_count' => 'nullable|integer|min:0',
            'content_optimization.internal_links' => 'nullable|array',
            'content_optimization.internal_links.*' => 'string|max:500',
            'content_optimization.external_links' => 'nullable|array',
            'content_optimization.external_links.*' => 'string|max:500',
            'technical_seo' => 'nullable|array',
            'technical_seo.page_speed' => 'nullable|integer|min:0|max:100',
            'technical_seo.mobile_friendly' => 'boolean',
            'technical_seo.https_enabled' => 'boolean',
            'technical_seo.xml_sitemap' => 'boolean',
            'technical_seo.robots_txt' => 'boolean',
            'technical_seo.breadcrumb_navigation' => 'boolean',
            'technical_seo.schema_markup' => 'boolean',
            'tracking_analytics' => 'nullable|array',
            'tracking_analytics.google_analytics' => 'boolean',
            'tracking_analytics.google_search_console' => 'boolean',
            'tracking_analytics.bing_webmaster_tools' => 'boolean',
            'tracking_analytics.google_tag_manager' => 'boolean',
            'tracking_analytics.facebook_pixel' => 'boolean',
            'tracking_analytics.custom_tracking_code' => 'nullable|string',
            'local_seo' => 'nullable|array',
            'local_seo.google_business_profile' => 'boolean',
            'local_seo.local_citations' => 'nullable|array',
            'local_seo.local_citations.*' => 'string|max:255',
            'local_seo.local_reviews' => 'nullable|array',
            'local_seo.local_reviews.platform' => 'nullable|string|max:100',
            'local_seo.local_reviews.rating' => 'nullable|numeric|min:0|max:5',
            'local_seo.local_reviews.review_count' => 'nullable|integer|min:0',
            'keyword_research' => 'nullable|array',
            'keyword_research.primary_keywords' => 'nullable|array',
            'keyword_research.primary_keywords.*.keyword' => 'required|string|max:100',
            'keyword_research.primary_keywords.*.volume' => 'nullable|integer|min:0',
            'keyword_research.primary_keywords.*.difficulty' => 'nullable|string|in:easy,medium,hard,very_hard',
            'keyword_research.primary_keywords.*.cpc' => 'nullable|numeric|min:0',
            'keyword_research.secondary_keywords' => 'nullable|array',
            'keyword_research.secondary_keywords.*.keyword' => 'required|string|max:100',
            'keyword_research.secondary_keywords.*.volume' => 'nullable|integer|min:0',
            'keyword_research.secondary_keywords.*.difficulty' => 'nullable|string|in:easy,medium,hard,very_hard',
            'keyword_research.secondary_keywords.*.cpc' => 'nullable|numeric|min:0',
            'competitor_analysis' => 'nullable|array',
            'competitor_analysis.competitors' => 'nullable|array',
            'competitor_analysis.competitors.*.name' => 'required|string|max:255',
            'competitor_analysis.competitors.*.url' => 'required|string|max:500',
            'competitor_analysis.competitors.*.keywords' => 'nullable|array',
            'competitor_analysis.competitors.*.keywords.*' => 'string|max:100',
            'competitor_analysis.competitors.*.traffic' => 'nullable|integer|min:0',
            'competitor_analysis.competitors.*.backlinks' => 'nullable|integer|min:0',
            'performance_tracking' => 'nullable|array',
            'performance_tracking.target_keywords' => 'nullable|array',
            'performance_tracking.target_keywords.*' => 'string|max:100',
            'performance_tracking.current_rankings' => 'nullable|array',
            'performance_tracking.current_rankings.*.keyword' => 'required|string|max:100',
            'performance_tracking.current_rankings.*.position' => 'nullable|integer|min:1',
            'performance_tracking.current_rankings.*.search_engine' => 'nullable|string|in:google,bing,yahoo,duckduckgo',
            'performance_tracking.current_rankings.*.url' => 'nullable|string|max:500',
            'performance_tracking.traffic_sources' => 'nullable|array',
            'performance_tracking.traffic_sources.*.source' => 'required|string|max:100',
            'performance_tracking.traffic_sources.*.visits' => 'nullable|integer|min:0',
            'performance_tracking.traffic_sources.*.percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        // Calculate SEO score
        $seoScore = $this->calculateSeoScore($validated);

        $propertySeo->update([
            'property_id' => $validated['property_id'],
            'page_title' => $validated['page_title'],
            'meta_description' => $validated['meta_description'],
            'meta_keywords' => $validated['meta_keywords'] ?? [],
            'focus_keywords' => $validated['focus_keywords'],
            'canonical_url' => $validated['canonical_url'] ?? null,
            'robots_meta' => $validated['robots_meta'] ?? [],
            'og_tags' => $validated['og_tags'] ?? [],
            'twitter_cards' => $validated['twitter_cards'] ?? [],
            'structured_data' => $validated['structured_data'] ?? [],
            'content_optimization' => $validated['content_optimization'] ?? [],
            'technical_seo' => $validated['technical_seo'] ?? [],
            'tracking_analytics' => $validated['tracking_analytics'] ?? [],
            'local_seo' => $validated['local_seo'] ?? [],
            'keyword_research' => $validated['keyword_research'] ?? [],
            'competitor_analysis' => $validated['competitor_analysis'] ?? [],
            'performance_tracking' => $validated['performance_tracking'] ?? [],
            'seo_score' => $seoScore,
            'total_keywords' => count($validated['focus_keywords']) + count($validated['meta_keywords'] ?? []),
        ]);

        // Handle OG image upload
        if ($request->hasFile('og_tags.og_image')) {
            // Delete old OG image
            if (isset($propertySeo->og_tags['og_image'])) {
                Storage::disk('public')->delete($propertySeo->og_tags['og_image']);
            }
            $ogImagePath = $request->file('og_tags.og_image')->store('seo-og-images', 'public');
            $propertySeo->update(['og_tags' => array_merge($propertySeo->og_tags, ['og_image' => $ogImagePath])]);
        }

        // Handle Twitter card image upload
        if ($request->hasFile('twitter_cards.image')) {
            // Delete old Twitter image
            if (isset($propertySeo->twitter_cards['image'])) {
                Storage::disk('public')->delete($propertySeo->twitter_cards['image']);
            }
            $twitterImagePath = $request->file('twitter_cards.image')->store('seo-twitter-images', 'public');
            $propertySeo->update(['twitter_cards' => array_merge($propertySeo->twitter_cards, ['image' => $twitterImagePath])]);
        }

        return redirect()->route('marketing.seo.index')
            ->with('success', 'تم تحديث إعدادات SEO بنجاح');
    }

    /**
     * Remove the specified property SEO settings.
     */
    public function destroy(PropertySeo $propertySeo)
    {
        // Delete associated images
        if (isset($propertySeo->og_tags['og_image'])) {
            Storage::disk('public')->delete($propertySeo->og_tags['og_image']);
        }
        if (isset($propertySeo->twitter_cards['image'])) {
            Storage::disk('public')->delete($propertySeo->twitter_cards['image']);
        }

        $propertySeo->delete();

        return redirect()->route('marketing.seo.index')
            ->with('success', 'تم حذف إعدادات SEO بنجاح');
    }

    /**
     * Run SEO audit for a property.
     */
    public function audit(PropertySeo $propertySeo)
    {
        $auditResults = $this->runSeoAudit($propertySeo);
        
        return Inertia::render('Marketing/Seo/Audit', [
            'seo_setting' => $propertySeo,
            'audit_results' => $auditResults,
        ]);
    }

    /**
     * Get SEO analytics.
     */
    public function analytics(PropertySeo $propertySeo)
    {
        $analytics = $this->getSeoAnalytics($propertySeo);

        return Inertia::render('Marketing/Seo/Analytics', [
            'seo_setting' => $propertySeo,
            'analytics' => $analytics,
        ]);
    }

    /**
     * Export SEO data.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $seoSettings = PropertySeo::with(['property'])->get();

        if ($format === 'csv') {
            $filename = 'property-seo-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($seoSettings) {
                $file = fopen('php://output', 'w');
                
                // CSV Header
                fputcsv($file, [
                    'ID', 'العقار', 'عنوان الصفحة', 'الوصف', 'درجة SEO', 
                    'الكلمات المفتاحية', 'الروابط الداخلية', 'الروابط الخارجية', 'تاريخ التحديث'
                ]);

                // CSV Data
                foreach ($seoSettings as $seo) {
                    fputcsv($file, [
                        $seo->id,
                        $seo->property?->title ?? 'N/A',
                        $seo->page_title,
                        $seo->meta_description,
                        $seo->seo_score,
                        implode(', ', $seo->focus_keywords),
                        count($seo->content_optimization['internal_links'] ?? []),
                        count($seo->content_optimization['external_links'] ?? []),
                        $seo->updated_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return back()->with('error', 'تنسيق التصدير غير مدعوم');
    }

    /**
     * Calculate SEO score.
     */
    private function calculateSeoScore($data)
    {
        $score = 0;
        $totalChecks = 0;

        // Basic SEO elements (30 points)
        if (!empty($data['page_title']) && strlen($data['page_title']) <= 60) {
            $score += 10;
        }
        $totalChecks++;

        if (!empty($data['meta_description']) && strlen($data['meta_description']) <= 160) {
            $score += 10;
        }
        $totalChecks++;

        if (!empty($data['focus_keywords']) && count($data['focus_keywords']) >= 1) {
            $score += 10;
        }
        $totalChecks++;

        // Technical SEO (25 points)
        if (isset($data['technical_seo']['mobile_friendly']) && $data['technical_seo']['mobile_friendly']) {
            $score += 5;
        }
        $totalChecks++;

        if (isset($data['technical_seo']['https_enabled']) && $data['technical_seo']['https_enabled']) {
            $score += 5;
        }
        $totalChecks++;

        if (isset($data['technical_seo']['xml_sitemap']) && $data['technical_seo']['xml_sitemap']) {
            $score += 5;
        }
        $totalChecks++;

        if (isset($data['technical_seo']['robots_txt']) && $data['technical_seo']['robots_txt']) {
            $score += 5;
        }
        $totalChecks++;

        if (isset($data['technical_seo']['schema_markup']) && $data['technical_seo']['schema_markup']) {
            $score += 5;
        }
        $totalChecks++;

        // Social Media (20 points)
        if (!empty($data['og_tags']['og_title'])) {
            $score += 5;
        }
        $totalChecks++;

        if (!empty($data['og_tags']['og_description'])) {
            $score += 5;
        }
        $totalChecks++;

        if (!empty($data['twitter_cards']['title'])) {
            $score += 5;
        }
        $totalChecks++;

        if (!empty($data['twitter_cards']['description'])) {
            $score += 5;
        }
        $totalChecks++;

        // Content Optimization (25 points)
        if (isset($data['content_optimization']['heading_structure']) && $data['content_optimization']['heading_structure']) {
            $score += 8;
        }
        $totalChecks++;

        if (isset($data['content_optimization']['keyword_density']) && $data['content_optimization']['keyword_density']) {
            $score += 8;
        }
        $totalChecks++;

        if (isset($data['content_optimization']['readability_score']) && $data['content_optimization']['readability_score']) {
            $score += 9;
        }
        $totalChecks++;

        return round(($score / $totalChecks) * 100);
    }

    /**
     * Run SEO audit.
     */
    private function runSeoAudit(PropertySeo $seoSetting)
    {
        return [
            'technical_audit' => [
                'page_speed' => [
                    'score' => rand(60, 95),
                    'issues' => [
                        'Optimize images for faster loading',
                        'Minify CSS and JavaScript files',
                        'Enable browser caching',
                    ],
                ],
                'mobile_friendly' => [
                    'status' => $seoSetting->technical_seo['mobile_friendly'] ?? false ? 'Pass' : 'Fail',
                    'issues' => $seoSetting->technical_seo['mobile_friendly'] ?? false ? [] : ['Make website responsive for mobile devices'],
                ],
                'https_security' => [
                    'status' => $seoSetting->technical_seo['https_enabled'] ?? false ? 'Pass' : 'Fail',
                    'issues' => $seoSetting->technical_seo['https_enabled'] ?? false ? [] : ['Install SSL certificate'],
                ],
                'xml_sitemap' => [
                    'status' => $seoSetting->technical_seo['xml_sitemap'] ?? false ? 'Pass' : 'Fail',
                    'issues' => $seoSetting->technical_seo['xml_sitemap'] ?? false ? [] : ['Create and submit XML sitemap'],
                ],
            ],
            'content_audit' => [
                'title_optimization' => [
                    'status' => strlen($seoSetting->page_title) <= 60 ? 'Good' : 'Needs Improvement',
                    'suggestions' => strlen($seoSetting->page_title) > 60 ? ['Shorten title to under 60 characters'] : [],
                ],
                'description_optimization' => [
                    'status' => strlen($seoSetting->meta_description) <= 160 ? 'Good' : 'Needs Improvement',
                    'suggestions' => strlen($seoSetting->meta_description) > 160 ? ['Shorten description to under 160 characters'] : [],
                ],
                'keyword_usage' => [
                    'status' => count($seoSetting->focus_keywords) >= 3 ? 'Good' : 'Needs Improvement',
                    'suggestions' => count($seoSetting->focus_keywords) < 3 ? ['Add more focus keywords'] : [],
                ],
            ],
            'social_media_audit' => [
                'open_graph' => [
                    'status' => !empty($seoSetting->og_tags['og_title']) && !empty($seoSetting->og_tags['og_description']) ? 'Good' : 'Needs Improvement',
                    'issues' => [],
                ],
                'twitter_cards' => [
                    'status' => !empty($seoSetting->twitter_cards['title']) && !empty($seoSetting->twitter_cards['description']) ? 'Good' : 'Needs Improvement',
                    'issues' => [],
                ],
            ],
            'overall_score' => $seoSetting->seo_score,
        ];
    }

    /**
     * Get SEO analytics.
     */
    private function getSeoAnalytics(PropertySeo $seoSetting)
    {
        return [
            'search_performance' => [
                'organic_traffic' => rand(100, 10000),
                'organic_traffic_growth' => rand(-20, 50) . '%',
                'keyword_rankings' => rand(10, 100),
                'average_position' => rand(1, 50),
                'click_through_rate' => rand(1, 15) . '%',
                'impressions' => rand(1000, 100000),
            ],
            'technical_metrics' => [
                'page_load_time' => rand(1, 5) . ' seconds',
                'mobile_usability' => rand(85, 100) . '%',
                'core_web_vitals' => [
                    'lcp' => rand(1, 4) . 's',
                    'fid' => rand(50, 200) . 'ms',
                    'cls' => rand(0, 0.3),
                ],
                'crawl_errors' => rand(0, 10),
                'indexed_pages' => rand(50, 500),
            ],
            'content_performance' => [
                'top_performing_pages' => rand(5, 20),
                'average_time_on_page' => rand(30, 180) . ' seconds',
                'bounce_rate' => rand(30, 70) . '%',
                'pages_per_session' => rand(1, 5),
                'conversion_rate' => rand(1, 10) . '%',
            ],
            'competitor_analysis' => [
                'market_position' => rand(1, 10),
                'market_share' => rand(5, 25) . '%',
                'competitor_traffic' => rand(5000, 50000),
                'keyword_overlap' => rand(20, 80) . '%',
                'backlink_gap' => rand(100, 10000),
            ],
        ];
    }

    /**
     * Get SEO recommendations.
     */
    private function getSeoRecommendations(PropertySeo $seoSetting)
    {
        $recommendations = [];

        if ($seoSetting->seo_score < 80) {
            $recommendations[] = [
                'priority' => 'high',
                'category' => 'Technical SEO',
                'title' => 'Improve page loading speed',
                'description' => 'Optimize images and minify CSS/JS to improve page speed',
                'impact' => 'High',
                'effort' => 'Medium',
            ];
        }

        if (count($seoSetting->focus_keywords) < 3) {
            $recommendations[] = [
                'priority' => 'medium',
                'category' => 'Content',
                'title' => 'Add more focus keywords',
                'description' => 'Include 3-5 relevant focus keywords in your content',
                'impact' => 'Medium',
                'effort' => 'Low',
            ];
        }

        if (!$seoSetting->technical_seo['schema_markup'] ?? false) {
            $recommendations[] = [
                'priority' => 'medium',
                'category' => 'Technical SEO',
                'title' => 'Implement schema markup',
                'description' => 'Add structured data to improve search engine understanding',
                'impact' => 'Medium',
                'effort' => 'Medium',
            ];
        }

        return $recommendations;
    }
}
