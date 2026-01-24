<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::with(['parent', 'children'])
            ->withCount('activePosts')
            ->latest()
            ->paginate(20);

        return view('admin.blog.categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = BlogCategory::active()->get();

        return view('admin.blog.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_categories,slug',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:blog_categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        BlogCategory::create($validated);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(BlogCategory $category)
    {
        $parentCategories = BlogCategory::active()
            ->where('id', '!=', $category->id)
            ->get();

        return view('admin.blog.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, BlogCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:blog_categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(BlogCategory $category)
    {
        if ($category->posts()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category with posts.');
        }

        $category->delete();

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function toggleStatus(BlogCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        return response()->json(['success' => true, 'is_active' => $category->is_active]);
    }
}
