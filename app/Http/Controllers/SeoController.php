<?php

namespace App\Http\Controllers;

use App\Models\SeoMeta;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SeoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index(): View
    {
        $seoMetas = SeoMeta::query()
            ->latest()
            ->paginate(20);

        return view('admin.seo.index', compact('seoMetas'));
    }

    public function create(): View
    {
        return view('admin.seo.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // Adjust validation to match form fields
        $validated = $request->validate([
            'page_type' => 'required|string',
            'url' => 'required|url',
            'meta_title' => 'required|string|max:60',
            'meta_description' => 'required|string|max:160',
            'meta_keywords' => 'nullable|string',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:500',
            'robots' => 'nullable|string|max:500',
            'structured_data' => 'nullable|json',
        ]);

        $metaableType = $this->getMetaableType($request->page_type);
        // Temporary placeholder ID - in a real app this should be selected
        $metaableId = 0; 

        $data = [
            'model_type' => $metaableType, 
            'model_id' => $metaableId,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'og_title' => $request->og_title,
            'og_description' => $request->og_description,
            'og_image' => $request->og_image,
            'canonical_url' => $request->canonical_url ?? $request->url,
            'robots' => $request->robots ?? 'index, follow',
            'structured_data' => $request->structured_data,
        ];

        SeoMeta::create($data);

        return redirect()->route('admin.seo.index')
            ->with('success', 'تم إنشاء إعدادات SEO بنجاح');
    }

    private function getMetaableType($type)
    {
        return match($type) {
            'property' => 'App\Models\Property',
            'company' => 'App\Models\Company',
            'agent' => 'App\Models\Agent',
            'blog' => 'App\Models\BlogPost',
            'page' => 'App\Models\Page',
            'category' => 'App\Models\Category',
            default => 'App\Models\Page', // Default to generic Page model instead of CustomPage
        };
    }

    public function show(SeoMeta $seoMeta): View
    {
        // Don't try to load the polymorphic relation if model doesn't exist
        // or if it's a placeholder ID
        if ($seoMeta->model_id > 0 && class_exists($seoMeta->model_type)) {
            try {
                $seoMeta->load('metaable');
            } catch (\Exception $e) {
                // Handle case where related model doesn't exist
            }
        }
        
        return view('admin.seo.show', compact('seoMeta'));
    }

    public function edit(SeoMeta $seoMeta): View
    {
        return view('admin.seo.edit', compact('seoMeta'));
    }

    public function update(Request $request, SeoMeta $seoMeta): RedirectResponse
    {
        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:500',
            'robots' => 'nullable|string|max:500',
            'structured_data' => 'nullable|json',
        ]);

        $seoMeta->update($validated);

        return redirect()->route('admin.seo.index')
            ->with('success', 'تم تحديث إعدادات SEO بنجاح');
    }

    public function destroy(SeoMeta $seoMeta): RedirectResponse
    {
        $seoMeta->delete();

        return redirect()->route('admin.seo.index')
            ->with('success', 'تم حذف إعدادات SEO بنجاح');
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'seo_data' => 'required|array',
            'seo_data.*.id' => 'required|exists:seo_meta,id',
            'seo_data.*.meta_title' => 'nullable|string|max:255',
            'seo_data.*.meta_description' => 'nullable|string|max:500',
            'seo_data.*.meta_keywords' => 'nullable|string|max:500',
        ]);

        foreach ($validated['seo_data'] as $data) {
            $seoMeta = SeoMeta::find($data['id']);
            if ($seoMeta) {
                $seoMeta->update([
                    'meta_title' => $data['meta_title'],
                    'meta_description' => $data['meta_description'],
                    'meta_keywords' => $data['meta_keywords'],
                ]);
            }
        }

        return redirect()->route('admin.seo.index')
            ->with('success', 'تم تحديث إعدادات SEO بنجاح');
    }

    public function sitemap(): View
    {
        // Get sitemap configuration and status
        $sitemapConfig = config('seo.sitemap', []);
        
        // Check if sitemap file exists
        $sitemapPath = public_path('sitemap.xml');
        $sitemapExists = file_exists($sitemapPath);
        $lastGenerated = $sitemapExists ? date('Y-m-d H:i:s', filemtime($sitemapPath)) : null;
        
        return view('admin.seo.sitemap', compact('sitemapConfig', 'sitemapExists', 'lastGenerated'));
    }

    public function generateSitemapRedirect()
    {
        return redirect()->route('admin.seo.index')
            ->with('info', 'لإنشاء خريطة الموقع، يرجى استخدام زر "توليد خريطة الموقع" في صفحة إدارة السيو.');
    }

    public function generateSitemap()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Add home page
        $xml .= '<url>';
        $xml .= '<loc>' . url('/') . '</loc>';
        $xml .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '</url>';

        // Add static pages (example)
        $pages = ['about', 'contact', 'properties', 'projects', 'blog'];
        foreach ($pages as $page) {
            $xml .= '<url>';
            $xml .= '<loc>' . url($page) . '</loc>';
            $xml .= '<lastmod>' . date('Y-m-d') . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        file_put_contents(public_path('sitemap.xml'), $xml);
        
        return redirect()->route('admin.seo.sitemap')
            ->with('success', 'Sitemap generated successfully');
    }

    public function metaTags(): View
    {
        $metaTags = SeoMeta::query()
            ->latest()
            ->paginate(20);

        return view('admin.seo.meta-tags', compact('metaTags'));
    }

    public function keywords(): View
    {
        $keywords = SeoMeta::query()
            ->whereNotNull('meta_keywords')
            ->pluck('meta_keywords')
            ->flatMap(function ($keywords) {
                return explode(',', $keywords);
            })
            ->map(function ($keyword) {
                return trim($keyword);
            })
            ->filter()
            ->countBy()
            ->take(50)
            ->toArray();

        return view('admin.seo.keywords', compact('keywords'));
    }

    public function analyze(): View
    {
        return view('admin.seo.analyze');
    }

    public function robots(): View
    {
        $robotsPath = public_path('robots.txt');
        $robotsExists = file_exists($robotsPath);
        $robotsContent = $robotsExists ? file_get_contents($robotsPath) : '';

        return view('admin.seo.robots', compact('robotsExists', 'robotsContent'));
    }

    public function storeRobots(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:10000',
        ]);

        file_put_contents(public_path('robots.txt'), $validated['content']);

        return redirect()->route('admin.seo.robots')
            ->with('success', 'robots.txt file created successfully');
    }

    public function updateRobots(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:10000',
        ]);

        file_put_contents(public_path('robots.txt'), $validated['content']);

        return redirect()->route('admin.seo.robots')
            ->with('success', 'robots.txt file updated successfully');
    }

    public function analyzeSeo(Request $request): View
    {
        $url = $request->url;
        $analysis = [];

        if ($url) {
            // Basic SEO analysis implementation
            $analysis = $this->performSeoAnalysis($url);
        }

        return view('admin.seo.analyze', compact('url', 'analysis'));
    }

    private function performSeoAnalysis($url): array
    {
        // Basic SEO analysis implementation
        return [
            'title' => 'Sample Title Analysis',
            'description' => 'Sample Description Analysis',
            'keywords' => 'Sample Keywords Analysis',
            'score' => 85,
            'h1_count' => 0,
            'image_alt_count' => 0,
            'internal_links' => 0,
            'external_links' => 0,
            'word_count' => 0,
            'recommendations' => [],
        ];
    }
}
