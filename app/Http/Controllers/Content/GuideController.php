<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Http\Requests\CreateGuideRequest;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    public function index(Request $request)
    {
        $guides = Guide::published()
            ->with('author')
            ->when($request->category, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->when($request->difficulty, function ($query, $difficulty) {
                return $query->where('difficulty', $difficulty);
            })
            ->latest()
            ->paginate(12);

        $categories = Guide::published()
            ->distinct()
            ->pluck('category')
            ->filter();

        return view('guides.index', compact('guides', 'categories'));
    }

    public function show($slug)
    {
        $guide = Guide::published()
            ->with('author')
            ->where('slug', $slug)
            ->firstOrFail();

        $guide->incrementViews();

        $relatedGuides = Guide::published()
            ->where('id', '!=', $guide->id)
            ->when($guide->category, function ($query) use ($guide) {
                return $query->where('category', $guide->category);
            })
            ->take(3)
            ->get();

        return view('guides.show', compact('guide', 'relatedGuides'));
    }

    public function adminIndex()
    {
        $guides = Guide::with('author')
            ->latest()
            ->paginate(20);

        return view('admin.guides.index', compact('guides'));
    }

    public function create()
    {
        return view('admin.guides.create');
    }

    public function store(CreateGuideRequest $request)
    {
        $validated = $request->validated();
        
        $guide = Guide::create([
            ...$validated,
            'author_id' => auth()->id(),
        ]);

        return redirect()->route('admin.guides.index')
            ->with('success', 'Guide created successfully.');
    }

    public function edit(Guide $guide)
    {
        return view('admin.guides.edit', compact('guide'));
    }

    public function update(Request $request, Guide $guide)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:guides,slug,' . $guide->id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'category' => 'nullable|string|max:100',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'is_featured' => 'boolean',
        ]);

        $guide->update($validated);

        return redirect()->route('admin.guides.index')
            ->with('success', 'Guide updated successfully.');
    }

    public function destroy(Guide $guide)
    {
        $guide->delete();

        return redirect()->route('admin.guides.index')
            ->with('success', 'Guide deleted successfully.');
    }
}
