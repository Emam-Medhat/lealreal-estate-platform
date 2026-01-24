<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\ContentRevision;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage-blog-posts');
    }

    public function index(Request $request): View
    {
        $posts = BlogPost::with(['category', 'author'])
            ->when($request->status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->category, function($query, $category) {
                return $query->where('category_id', $category);
            })
            ->when($request->search, function($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20);

        $categories = BlogCategory::all();
        
        return view('admin.blog-posts.index', compact('posts', 'categories'));
    }

    public function create(): View
    {
        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        
        return view('admin.blog-posts.create', compact('categories', 'tags'));
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
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['author_id'] = auth()->id();
        
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        $post = BlogPost::create($validated);
        
        if (!empty($validated['tags'])) {
            $post->tags()->attach($validated['tags']);
        }

        // Create initial revision
        ContentRevision::create([
            'content_type' => 'blog_post',
            'content_id' => $post->id,
            'content_data' => $post->toArray(),
            'author_id' => auth()->id(),
            'revision_notes' => 'إنشاء المقال',
        ]);

        return redirect()->route('admin.blog-posts.show', $post)
            ->with('success', 'تم إنشاء المقال بنجاح');
    }

    public function show(BlogPost $post): View
    {
        $post->load(['category', 'tags', 'author', 'revisions' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('admin.blog-posts.show', compact('post'));
    }

    public function edit(BlogPost $post): View
    {
        $post->load(['tags']);
        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        
        return view('admin.blog-posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, BlogPost $post): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_posts,slug,'.$post->getKey(),
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
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        // Create revision before updating
        ContentRevision::create([
            'content_type' => 'blog_post',
            'content_id' => $post->getKey(),
            'content_data' => $post->toArray(),
            'author_id' => auth()->id(),
            'revision_notes' => $request->revision_notes ?? 'تحديث المقال',
        ]);

        $post->update($validated);
        
        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return redirect()->route('admin.blog-posts.show', $post)
            ->with('success', 'تم تحديث المقال بنجاح');
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.blog-posts.index')
            ->with('success', 'تم حذف المقال بنجاح');
    }

    public function duplicate(BlogPost $post): RedirectResponse
    {
        $newPost = $post->replicate();
        $newPost->title = $post->title . ' (نسخة)';
        $newPost->slug = $post->slug . '-copy-' . time();
        $newPost->status = 'draft';
        $newPost->author_id = auth()->id();
        $newPost->published_at = null;
        $newPost->save();

        // Copy tags
        $newPost->tags()->attach($post->tags->pluck('id'));

        return redirect()->route('admin.blog-posts.edit', $newPost)
            ->with('success', 'تم نسخ المقال بنجاح');
    }

    public function restore(BlogPost $post): RedirectResponse
    {
        $post->restore();

        return redirect()->route('admin.blog-posts.show', $post)
            ->with('success', 'تم استعادة المقال بنجاح');
    }
}
