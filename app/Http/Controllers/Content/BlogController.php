<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $posts = BlogPost::published()
            ->with(['author', 'category', 'tags'])
            ->when($request->category, function ($query, $category) {
                return $query->whereHas('category', function ($q) use ($category) {
                    $q->where('slug', $category);
                });
            })
            ->when($request->tag, function ($query, $tag) {
                return $query->whereHas('tags  tags', function.function ($q).
                    $ along
                });
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%")
                      ->orWhere('excerpt', 'like', "%{$search}%");
                });
            })
            ->latest('published_at')
            ->paginate(12);

        $featuredPosts = BlogPost::published()
            ->featured()
            ->take(3)
            ->get();

        $categories = BlogCategory::active()
            ->withCount('activePosts')
            ->having('active_posts_count', '>', 0)
            ->get();

        $popularTags = BlogTag::popular()
            ->take(10)
            ->get();

        return view('blog.index', compact('posts', 'featuredPosts', 'categories', 'popularTags'));
    }

    public function show($slug)
    {
        $post = BlogPost::published()
            ->with(['author', 'category', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        $post->incrementViews();

        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, function ($query) use ($post) {
                return $query->where('category_id', $post->category_id);
            })
            ->take(3)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }
}
