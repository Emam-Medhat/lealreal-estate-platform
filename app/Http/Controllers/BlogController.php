<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('permission:manage-blog')->except(['index', 'show']);
    }

    public function index(Request $request): View
    {
        $posts = BlogPost::with(['category', 'tags', 'author'])
            ->when($request->category, function($query, $category) {
                return $query->whereHas('category', function($q) use ($category) {
                    $q->where('slug', $category);
                });
            })
            ->when($request->tag, function($query, $tag) {
                return $query->whereHas('tags', function($q) use ($tag) {
                    $q->where('slug', $tag);
                });
            })
            ->when($request->search, function($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            })
            ->published()
            ->latest()
            ->paginate(12);

        $featuredPosts = BlogPost::with(['category', 'tags', 'author'])
            ->featured()
            ->published()
            ->latest()
            ->take(4)
            ->get();

        $categories = BlogCategory::withCount('posts')->get();
        $popular_tags = BlogTag::withCount('posts')->orderBy('posts_count', 'desc')->take(10)->get();

        return view('blog.index', compact('posts', 'featuredPosts', 'categories', 'popular_tags'));
    }

    public function show(BlogPost $post): View
    {
        $post->increment('views');
        
        $related_posts = BlogPost::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->published()
            ->latest()
            ->take(4)
            ->get();

        return view('blog.show', compact('post', 'related_posts'));
    }

    public function create(): View
    {
        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        
        return view('blog.create', compact('categories', 'tags'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_posts',
            'content' => 'required',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:blog_categories,id',
            'tags' => 'array',
            'tags.*' => 'exists:blog_tags,id',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $validated['author_id'] = 1;
        
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        $post = BlogPost::create($validated);
        
        if (!empty($validated['tags'])) {
            $post->tags()->attach($validated['tags']);
        }

        return redirect()->route('blog.show', $post)
            ->with('success', 'تم إنشاء المقال بنجاح');
    }

    public function edit(BlogPost $post): View
    {
        $this->authorize('update', $post);
        
        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        
        return view('blog.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, BlogPost $post): RedirectResponse
    {
        $this->authorize('update', $post);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_posts,slug,'.$post->id,
            'content' => 'required',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:blog_categories,id',
            'tags' => 'array',
            'tags.*' => 'exists:blog_tags,id',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        $post->update($validated);
        
        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return redirect()->route('blog.show', $post)
            ->with('success', 'تم تحديث المقال بنجاح');
    }

    public function search(Request $request): View
    {
        $query = $request->get('q');
        
        $posts = BlogPost::with(['category', 'tags', 'author'])
            ->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%")
                  ->orWhere('excerpt', 'LIKE', "%{$query}%");
            })
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('blog.search', compact('posts', 'query'));
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        $this->authorize('delete', $post);
        
        $post->delete();

        return redirect()->route('blog.index')
            ->with('success', 'تم حذف المقال بنجاح');
    }
}
