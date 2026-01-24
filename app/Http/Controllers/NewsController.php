<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('permission:manage-news')->except(['index', 'show']);
    }

    public function index(Request $request): View
    {
        $news = News::with('author')
            ->when($request->category, function($query, $category) {
                return $query->where('category', $category);
            })
            ->when($request->search, function($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            })
            ->published()
            ->latest()
            ->paginate(12);

        $categories = News::distinct()->pluck('category');

        return view('news.index', compact('news', 'categories'));
    }

    public function show(News $news): View
    {
        $news->increment('views');
        
        $related_news = News::where('category', $news->category)
            ->where('id', '!=', $news->id)
            ->published()
            ->latest()
            ->take(4)
            ->get();

        return view('news.show', compact('news', 'related_news'));
    }

    public function create(): View
    {
        return view('admin.news.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:news',
            'content' => 'required',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'required|string|max:100',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
            'source' => 'nullable|string|max:255',
            'source_url' => 'nullable|url',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['author_id'] = auth()->id();
        
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('news', 'public');
        }

        News::create($validated);

        return redirect()->route('admin.news.index')
            ->with('success', 'تم إنشاء الخبر بنجاح');
    }

    public function edit(News $news): View
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:news,slug,'.$news->getKey(),
            'content' => 'required',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'required|string|max:100',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
            'source' => 'nullable|string|max:255',
            'source_url' => 'nullable|url',
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('news', 'public');
        }

        $news->update($validated);

        return redirect()->route('admin.news.show', $news)
            ->with('success', 'تم تحديث الخبر بنجاح');
    }

    public function destroy(News $news): RedirectResponse
    {
        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'تم حذف الخبر بنجاح');
    }

    public function adminIndex(): View
    {
        $news = News::with('author')
            ->latest()
            ->paginate(20);

        return view('admin.news.index', compact('news'));
    }

    public function adminShow(News $news): View
    {
        $news->load('author');
        
        return view('admin.news.show', compact('news'));
    }
}
