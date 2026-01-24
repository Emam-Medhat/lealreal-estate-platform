<?php

namespace App\Http\Controllers;

use App\Models\LeadSource;
use Illuminate\Http\Request;

class LeadSourceController extends Controller
{
    public function index()
    {
        $sources = LeadSource::withCount('leads')
            ->orderBy('name')
            ->paginate(15);
            
        return view('lead-sources.index', compact('sources'));
    }
    
    public function create()
    {
        return view('lead-sources.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:lead_sources,name',
            'description' => 'nullable|string|max:1000',
            'weight' => 'required|integer|min:0|max:100',
            'is_active' => 'boolean',
        ]);
        
        LeadSource::create([
            'name' => $request->name,
            'description' => $request->description,
            'weight' => $request->weight,
            'is_active' => $request->boolean('is_active', true),
        ]);
        
        return redirect()->route('lead-sources.index')
            ->with('success', 'تم إضافة مصدر العملاء بنجاح');
    }
    
    public function show(LeadSource $leadSource)
    {
        $leadSource->load(['leads' => function($query) {
            $query->with(['status', 'assignedUser'])
                  ->orderBy('created_at', 'desc')
                  ->take(20);
        }]);
        
        return view('lead-sources.show', compact('leadSource'));
    }
    
    public function edit(LeadSource $leadSource)
    {
        return view('lead-sources.edit', compact('leadSource'));
    }
    
    public function update(Request $request, LeadSource $leadSource)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:lead_sources,name,' . $leadSource->id,
            'description' => 'nullable|string|max:1000',
            'weight' => 'required|integer|min:0|max:100',
            'is_active' => 'boolean',
        ]);
        
        $leadSource->update([
            'name' => $request->name,
            'description' => $request->description,
            'weight' => $request->weight,
            'is_active' => $request->boolean('is_active', $leadSource->is_active),
        ]);
        
        return redirect()->route('lead-sources.show', $leadSource)
            ->with('success', 'تم تحديث مصدر العملاء بنجاح');
    }
    
    public function destroy(LeadSource $leadSource)
    {
        if ($leadSource->leads()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف مصدر العملاء لوجود عملاء مرتبطين به');
        }
        
        $leadSource->delete();
        
        return redirect()->route('lead-sources.index')
            ->with('success', 'تم حذف مصدر العملاء بنجاح');
    }
    
    public function toggleStatus(LeadSource $leadSource)
    {
        $leadSource->update([
            'is_active' => !$leadSource->is_active
        ]);
        
        return back()->with('success', 'تم تحديث حالة مصدر العملاء بنجاح');
    }
}
