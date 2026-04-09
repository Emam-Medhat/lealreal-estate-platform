<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Http\Requests\StoreBlogPostRequest;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with(['author', 'category'])
            ->latest()
            ->paginate(20);

        return view('admin.blog.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = BlogCategory::active()->get();
        $tags = BlogTag::all();

        return view('admin.blog.posts.create', compact('categories', 'tags'));
    }

    public function store(StoreBlogPostRequest $request)
    {
        $post = BlogPost::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'featured_image' => $request->featured_image,
            'status' => $request->status,
            'published_at' => $request->published_at,
            'author_id' => auth()->id(),
            'category_id' => $request->category_id,
            'is_featured' => $request->boolean('is_featured'),
            'allow_comments' => $request->boolean('allow_comments'),
        ]);

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Blog post created successfully.');
    }

    public function edit(BlogPost $post)
    {
        $categories = BlogCategory::active()->get();
        $tags = BlogTag::all();
        $post->load('tags');

        return view('admin.blog.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_posts,slug,' . $post->id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,published,scheduled,archived',
            'published_at' => 'nullable|date',
            'category_id' => 'nullable|exists:blog_categories,id',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
        ]);

        $post->update($validated);

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Blog post updated successfully.');
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Blog post deleted successfully.');
    }

    public function toggleStatus(BlogPost $post)
    {
        $newStatus = $post->status === 'published' ? 'draft' : 'published';
        $post->update(['status' => $newStatus]);

        return response()->json(['success' => true, 'status' => $newStatus]);
    }
}
