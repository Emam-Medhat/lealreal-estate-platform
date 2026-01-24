<?php

namespace App\Http\Controllers;

use App\Models\LeadStatus;
use Illuminate\Http\Request;

class LeadStatusController extends Controller
{
    public function index()
    {
        $statuses = LeadStatus::withCount('leads')
            ->orderBy('order')
            ->paginate(15);
            
        return view('lead-statuses.index', compact('statuses'));
    }
    
    public function create()
    {
        return view('lead-statuses.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:lead_statuses,name',
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|max:7',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'is_converted' => 'boolean',
        ]);
        
        LeadStatus::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'order' => $request->order,
            'is_active' => $request->boolean('is_active', true),
            'is_converted' => $request->boolean('is_converted', false),
        ]);
        
        return redirect()->route('lead-statuses.index')
            ->with('success', 'تم إضافة حالة العميل بنجاح');
    }
    
    public function show(LeadStatus $leadStatus)
    {
        $leadStatus->load(['leads' => function($query) {
            $query->with(['source', 'assignedUser'])
                  ->orderBy('created_at', 'desc')
                  ->take(20);
        }]);
        
        return view('lead-statuses.show', compact('leadStatus'));
    }
    
    public function edit(LeadStatus $leadStatus)
    {
        return view('lead-statuses.edit', compact('leadStatus'));
    }
    
    public function update(Request $request, LeadStatus $leadStatus)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:lead_statuses,name,' . $leadStatus->id,
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|max:7',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'is_converted' => 'boolean',
        ]);
        
        $leadStatus->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'order' => $request->order,
            'is_active' => $request->boolean('is_active', $leadStatus->is_active),
            'is_converted' => $request->boolean('is_converted', $leadStatus->is_converted),
        ]);
        
        return redirect()->route('lead-statuses.show', $leadStatus)
            ->with('success', 'تم تحديث حالة العميل بنجاح');
    }
    
    public function destroy(LeadStatus $leadStatus)
    {
        if ($leadStatus->leads()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف حالة العميل لوجود عملاء مرتبطين بها');
        }
        
        $leadStatus->delete();
        
        return redirect()->route('lead-statuses.index')
            ->with('success', 'تم حذف حالة العميل بنجاح');
    }
    
    public function toggleStatus(LeadStatus $leadStatus)
    {
        $leadStatus->update([
            'is_active' => !$leadStatus->is_active
        ]);
        
        return back()->with('success', 'تم تحديث حالة العميل بنجاح');
    }
    
    public function reorder(Request $request)
    {
        $request->validate([
            'statuses' => 'required|array',
            'statuses.*.id' => 'required|exists:lead_statuses,id',
            'statuses.*.order' => 'required|integer|min:0',
        ]);
        
        foreach ($request->statuses as $statusData) {
            LeadStatus::where('id', $statusData['id'])
                ->update(['order' => $statusData['order']]);
        }
        
        return response()->json(['message' => 'تم تحديث الترتيب بنجاح']);
    }
}
