<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage-blog-categories');
    }

    public function index(): View
    {
        $categories = BlogCategory::withCount('posts')
            ->orderBy('name')
            ->get();

        return view('admin.blog-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.blog-categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_categories',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:blog_categories,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        BlogCategory::create($validated);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'تم إنشاء التصنيف بنجاح');
    }

    public function show(BlogCategory $category): View
    {
        $category->load(['posts' => function($query) {
            $query->latest()->take(10);
        }]);

        return view('admin.blog-categories.show', compact('category'));
    }

    public function edit(BlogCategory $category): View
    {
        return view('admin.blog-categories.edit', compact('category'));
    }

    public function update(Request $request, BlogCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_categories,slug,'.$category->getKey(),
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:blog_categories,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->route('admin.blog-categories.show', $category)
            ->with('success', 'تم تحديث التصنيف بنجاح');
    }

    public function destroy(BlogCategory $category): RedirectResponse
    {
        if ($category->posts()->exists()) {
            return redirect()->route('admin.blog-categories.index')
                ->with('error', 'لا يمكن حذف التصنيف لأنه يحتوي على مقالات');
        }

        $category->delete();

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'تم حذف التصنيف بنجاح');
    }
}
