<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $news = News::published()
            ->with('author')
            ->when($request->category, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->latest('published_at')
            ->paginate(12);

        $categories = News::published()
            ->distinct()
            ->pluck('category')
            ->filter();

        return view('news.index', compact('news', 'categories'));
    }

    public function show($slug)
    {
        $news = News::published()
            ->with('author')
            ->where('slug', $slug)
            ->firstOrFail();

        $news->incrementViews();

        $relatedNews = News::published()
            ->where('id', '!=', $news->id)
            ->when($news->category, function ($query) use ($news) {
                return $query->where('category', $news->category);
            })
            ->take(3)
            ->get();

        return view('news.show', compact('news', 'relatedNews'));
    }

    public function adminIndex()
    {
        $news = News::with('author')
            ->latest()
            ->paginate(20);

        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:news,slug',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'category' => 'nullable|string|max:100',
            'is_featured' => 'boolean',
        ]);

        $news = News::create([
            ...$validated,
            'author_id' => auth()->id(),
        ]);

        return redirect()->route('admin.news.index')
            ->with('success', 'News article created successfully.');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:news,slug,' . $news->id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'category' => 'nullable|string|max:100',
            'is_featured' => 'boolean',
        ]);

        $news->update($validated);

        return redirect()->route('admin.news.index')
            ->with('success', 'News article updated successfully.');
    }

    public function destroy(News $news)
    {
        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'News article deleted successfully.');
    }
}
