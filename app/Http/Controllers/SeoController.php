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
        $seoMetas = SeoMeta::with(['seoable'])
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
        $validated = $request->validate([
            'seoable_type' => 'required|string|max:255',
            'seoable_id' => 'required|integer',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'keywords' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string|max:500',
            'twitter_image' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:500',
            'robots' => 'nullable|string|max:500',
            'json_ld' => 'nullable|json',
        ]);

        SeoMeta::create($validated);

        return redirect()->route('admin.seo.index')
            ->with('success', 'تم إنشاء إعدادات SEO بنجاح');
    }

    public function show(SeoMeta $seoMeta): View
    {
        $seoMeta->load('seoable');
        
        return view('admin.seo.show', compact('seoMeta'));
    }

    public function edit(SeoMeta $seoMeta): View
    {
        return view('admin.seo.edit', compact('seoMeta'));
    }

    public function update(Request $request, SeoMeta $seoMeta): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'keywords' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string|max:500',
            'twitter_image' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:500',
            'robots' => 'nullable|string|max:500',
            'json_ld' => 'nullable|json',
        ]);

        $seoMeta->update($validated);

        return redirect()->route('admin.seo.show', $seoMeta)
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
            'seo_data.*.title' => 'nullable|string|max:255',
            'seo_data.*.description' => 'nullable|string|max:500',
            'seo_data.*.keywords' => 'nullable|string|max:500',
        ]);

        foreach ($validated['seo_data'] as $data) {
            $seoMeta = SeoMeta::find($data['id']);
            if ($seoMeta) {
                $seoMeta->update([
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'keywords' => $data['keywords'],
                ]);
            }
        }

        return redirect()->route('admin.seo.index')
            ->with('success', 'تم تحديث إعدادات SEO بنجاح');
    }

    public function generateSitemap()
    {
        // This would generate a sitemap.xml file
        // Implementation depends on your specific requirements
        
        return response()->json(['message' => 'Sitemap generated successfully']);
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
        // This is a placeholder for SEO analysis
        // In a real implementation, you would:
        // 1. Fetch the URL content
        // 2. Analyze title, meta tags, headings, etc.
        // 3. Check for SEO best practices
        // 4. Generate recommendations
        
        return [
            'title_length' => 0,
            'description_length' => 0,
            'h1_count' => 0,
            'image_alt_count' => 0,
            'internal_links' => 0,
            'external_links' => 0,
            'word_count' => 0,
            'recommendations' => [],
        ];
    }
}
