<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class GuideController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('permission:manage-guides')->except(['index', 'show']);
    }

    public function index(Request $request): View
    {
        $guides = Guide::with('author')
            ->when($request->category, function($query, $category) {
                return $query->where('category', $category);
            })
            ->when($request->difficulty, function($query, $difficulty) {
                return $query->where('difficulty', $difficulty);
            })
            ->when($request->search, function($query, $search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            })
            ->published()
            ->latest()
            ->paginate(12);

        $categories = Guide::distinct()->pluck('category');
        $difficulties = ['مبتدئ', 'متوسط', 'متقدم'];

        return view('guides.index', compact('guides', 'categories', 'difficulties'));
    }

    public function show(Guide $guide): View
    {
        $guide->increment('views');
        
        $related_guides = Guide::where('category', $guide->category)
            ->where('id', '!=', $guide->id)
            ->published()
            ->latest()
            ->take(4)
            ->get();

        return view('guides.show', compact('guide', 'related_guides'));
    }

    public function create(): View
    {
        return view('admin.guides.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:guides',
            'content' => 'required',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'required|string|max:100',
            'difficulty' => 'required|in:مبتدئ,متوسط,متقدم',
            'estimated_time' => 'nullable|integer|min:1',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
            'prerequisites' => 'nullable|string',
            'learning_objectives' => 'nullable|string',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['author_id'] = auth()->id();
        
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('guides', 'public');
        }

        Guide::create($validated);

        return redirect()->route('admin.guides.index')
            ->with('success', 'تم إنشاء الدليل بنجاح');
    }

    public function edit(Guide $guide): View
    {
        return view('admin.guides.edit', compact('guide'));
    }

    public function update(Request $request, Guide $guide): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:guides,slug,'.$guide->getKey(),
            'content' => 'required',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'required|string|max:100',
            'difficulty' => 'required|in:مبتدئ,متوسط,متقدم',
            'estimated_time' => 'nullable|integer|min:1',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
            'prerequisites' => 'nullable|string',
            'learning_objectives' => 'nullable|string',
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('guides', 'public');
        }

        $guide->update($validated);

        return redirect()->route('admin.guides.show', $guide)
            ->with('success', 'تم تحديث الدليل بنجاح');
    }

    public function destroy(Guide $guide): RedirectResponse
    {
        $guide->delete();

        return redirect()->route('admin.guides.index')
            ->with('success', 'تم حذف الدليل بنجاح');
    }

    public function adminIndex(): View
    {
        $guides = Guide::with('author')
            ->latest()
            ->paginate(20);

        return view('admin.guides.index', compact('guides'));
    }

    public function adminShow(Guide $guide): View
    {
        $guide->load('author');
        
        return view('admin.guides.show', compact('guide'));
    }
}
