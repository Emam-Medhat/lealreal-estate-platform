<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Http\Requests\UpdatePageRequest;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::with('author')
            ->latest()
            ->paginate(20);

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'template' => 'required|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'show_in_menu' => 'boolean',
            'menu_title' => 'nullable|string|max:255',
        ]);

        $page = Page::create([
            ...$validated,
            'author_id' => auth()->id(),
        ]);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully.');
    }

    public function show($slug)
    {
        $page = Page::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.show', compact('page'));
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(UpdatePageRequest $request, Page $page)
    {
        $page->update($request->validated());

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    public function toggleStatus(Page $page)
    {
        $newStatus = $page->status === 'published' ? 'draft' : 'published';
        $page->update(['status' => $newStatus]);

        return response()->json(['success' => true, 'status' => $newStatus]);
    }
}
