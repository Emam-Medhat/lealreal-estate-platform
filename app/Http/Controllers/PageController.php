<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\ContentRevision;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show']);
        $this->middleware('permission:manage-pages')->except(['show']);
    }

    public function index(): View
    {
        $pages = Page::with('author')
            ->orderBy('title')
            ->paginate(20);

        return view('admin.pages.index', compact('pages'));
    }

    public function show(Page $page): View
    {
        if ($page->status !== 'published' && !auth()->check()) {
            abort(404);
        }

        return view('pages.show', compact('page'));
    }

    public function create(): View
    {
        return view('admin.pages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages',
            'content' => 'required',
            'excerpt' => 'nullable|string|max:500',
            'template' => 'nullable|string|max:100',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_homepage' => 'boolean',
            'parent_id' => 'nullable|exists:pages,id',
            'order' => 'nullable|integer|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['author_id'] = auth()->id();
        
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('pages', 'public');
        }

        // If this is set as homepage, unset other homepage
        if (!empty($validated['is_homepage'])) {
            Page::where('is_homepage', true)->update(['is_homepage' => false]);
        }

        $page = Page::create($validated);

        // Create initial revision
        ContentRevision::create([
            'content_type' => 'page',
            'content_id' => $page->getKey(),
            'content_data' => $page->toArray(),
            'author_id' => auth()->id(),
            'revision_notes' => 'إنشاء الصفحة',
        ]);

        return redirect()->route('admin.pages.show', $page)
            ->with('success', 'تم إنشاء الصفحة بنجاح');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,'.$page->getKey(),
            'content' => 'required',
            'excerpt' => 'nullable|string|max:500',
            'template' => 'nullable|string|max:100',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_homepage' => 'boolean',
            'parent_id' => 'nullable|exists:pages,id',
            'order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('pages', 'public');
        }

        // If this is set as homepage, unset other homepage
        if (!empty($validated['is_homepage'])) {
            Page::where('is_homepage', true)->where('id', '!=', $page->getKey())->update(['is_homepage' => false]);
        }

        // Create revision before updating
        ContentRevision::create([
            'content_type' => 'page',
            'content_id' => $page->getKey(),
            'content_data' => $page->toArray(),
            'author_id' => auth()->id(),
            'revision_notes' => $request->revision_notes ?? 'تحديث الصفحة',
        ]);

        $page->update($validated);

        return redirect()->route('admin.pages.show', $page)
            ->with('success', 'تم تحديث الصفحة بنجاح');
    }

    public function destroy(Page $page): RedirectResponse
    {
        if ($page->is_homepage) {
            return redirect()->route('admin.pages.index')
                ->with('error', 'لا يمكن حذف الصفحة الرئيسية');
        }

        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'تم حذف الصفحة بنجاح');
    }

    public function duplicate(Page $page): RedirectResponse
    {
        $newPage = $page->replicate();
        $newPage->title = $page->title . ' (نسخة)';
        $newPage->slug = $page->slug . '-copy-' . time();
        $newPage->status = 'draft';
        $newPage->author_id = auth()->id();
        $newPage->published_at = null;
        $newPage->is_homepage = false;
        $newPage->save();

        return redirect()->route('admin.pages.edit', $newPage)
            ->with('success', 'تم نسخ الصفحة بنجاح');
    }
}
