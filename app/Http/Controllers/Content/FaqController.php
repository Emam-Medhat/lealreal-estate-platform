<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $faqs = Faq::active()
            ->when($request->category, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->ordered()
            ->get();

        $categories = Faq::active()
            ->distinct()
            ->pluck('category')
            ->filter();

        return view('faqs.index', compact('faqs', 'categories'));
    }

    public function adminIndex()
    {
        $faqs = Faq::latest()->paginate(20);

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        Faq::create($validated);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ created successfully.');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $faq->update($validated);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ updated successfully.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ deleted successfully.');
    }

    public function toggleStatus(Faq $faq)
    {
        $faq->update(['is_active' => !$faq->is_active]);

        return response()->json(['success' => true, 'is_active' => $faq->is_active]);
    }

    public function reorder(Request $request)
    {
        $items = $request->input('items', []);

        foreach ($items as $index => $itemId) {
            Faq::where('id', $itemId)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
