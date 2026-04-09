<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\Inspector;
use App\Models\Property;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    public function index()
    {
        $inspections = Inspection::with(['property', 'inspector', 'report'])
            ->latest()
            ->paginate(10);
            
        return view('inspections.index', compact('inspections'));
    }

    public function create()
    {
        $properties = Property::all();
        $inspectors = Inspector::all();
        
        return view('inspections.create', compact('properties', 'inspectors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'inspector_id' => 'required|exists:inspectors,id',
            'scheduled_date' => 'required|date|after:now',
            'inspection_type' => 'required|in:routine,detailed,pre_sale,post_repair',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        $inspection = Inspection::create($validated);

        return redirect()
            ->route('inspections.show', $inspection)
            ->with('success', 'تم حجز الفحص بنجاح');
    }

    public function show(Inspection $inspection)
    {
        $inspection->load(['property', 'inspector', 'report.defects', 'photos']);
        
        return view('inspections.show', compact('inspection'));
    }

    public function edit(Inspection $inspection)
    {
        $properties = Property::all();
        $inspectors = Inspector::all();
        
        return view('inspections.edit', compact('inspection', 'properties', 'inspectors'));
    }

    public function update(Request $request, Inspection $inspection)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'inspector_id' => 'required|exists:inspectors,id',
            'scheduled_date' => 'required|date|after:now',
            'inspection_type' => 'required|in:routine,detailed,pre_sale,post_repair',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        $inspection->update($validated);

        return redirect()
            ->route('inspections.show', $inspection)
            ->with('success', 'تم تحديث الفحص بنجاح');
    }

    public function destroy(Inspection $inspection)
    {
        $inspection->delete();

        return redirect()
            ->route('inspections.index')
            ->with('success', 'تم حذف الفحص بنجاح');
    }

    public function calendar()
    {
        $inspections = Inspection::with(['property', 'inspector'])
            ->whereMonth('scheduled_date', now()->month)
            ->get();

        return view('inspections.calendar', compact('inspections'));
    }

    public function dashboard()
    {
        $stats = [
            'total' => Inspection::count(),
            'scheduled' => Inspection::where('status', 'scheduled')->count(),
            'in_progress' => Inspection::where('status', 'in_progress')->count(),
            'completed' => Inspection::where('status', 'completed')->count(),
            'this_month' => Inspection::whereMonth('scheduled_date', now()->month)->count(),
        ];

        $recentInspections = Inspection::with(['property', 'inspector'])
            ->latest()
            ->take(5)
            ->get();

        return view('inspections.dashboard', compact('stats', 'recentInspections'));
    }
}
