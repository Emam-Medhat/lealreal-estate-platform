<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
        $this->middleware('permission:manage-faqs')->except(['index']);
    }

    public function index(Request $request): View
    {
        $faqs = Faq::when($request->category, function($query, $category) {
                return $query->where('category', $category);
            })
            ->when($request->search, function($query, $search) {
                return $query->where('question', 'like', "%{$search}%")
                    ->orWhere('answer', 'like', "%{$search}%");
            })
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('question')
            ->get();

        $categories = Faq::distinct()->where('is_active', true)->pluck('category');

        return view('faqs.index', compact('faqs', 'categories'));
    }

    public function create(): View
    {
        $categories = Faq::distinct()->pluck('category');
        
        return view('admin.faqs.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required',
            'category' => 'required|string|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $validated['order'] = $validated['order'] ?? 0;

        Faq::create($validated);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'تم إنشاء السؤال الشائع بنجاح');
    }

    public function edit(Faq $faq): View
    {
        $categories = Faq::distinct()->pluck('category');
        
        return view('admin.faqs.edit', compact('faq', 'categories'));
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required',
            'category' => 'required|string|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $faq->update($validated);

        return redirect()->route('admin.faqs.show', $faq)
            ->with('success', 'تم تحديث السؤال الشائع بنجاح');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')
            ->with('success', 'تم حذف السؤال الشائع بنجاح');
    }

    public function adminIndex(): View
    {
        $faqs = Faq::latest()->paginate(20);

        return view('admin.faqs.index', compact('faqs'));
    }

    public function show(Faq $faq): View
    {
        return view('admin.faqs.show', compact('faq'));
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'faqs' => 'required|array',
            'faqs.*.id' => 'required|exists:faqs,id',
            'faqs.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['faqs'] as $faqData) {
            Faq::where('id', $faqData['id'])->update(['order' => $faqData['order']]);
        }

        return redirect()->route('admin.faqs.index')
            ->with('success', 'تم إعادة ترتيب الأسئلة بنجاح');
    }
}
