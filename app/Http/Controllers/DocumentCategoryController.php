<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use Illuminate\Http\Request;

class DocumentCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = DocumentCategory::all();
        return view('documents.categories.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        DocumentCategory::create($validated);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentCategory $category)
    {
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}
