<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\Inspector;
use App\Models\Property;
use Illuminate\Http\Request;

class InspectionScheduleController extends Controller
{
    public function index()
    {
        $inspections = Inspection::with(['property', 'inspector'])
            ->where('status', 'scheduled')
            ->orderBy('scheduled_date')
            ->paginate(15);

        return view('inspections.schedule.index', compact('inspections'));
    }

    public function create()
    {
        $properties = Property::all();
        $inspectors = Inspector::where('is_active', true)->get();
        
        return view('inspections.schedule.create', compact('properties', 'inspectors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'inspector_id' => 'required|exists:inspectors,id',
            'scheduled_date' => 'required|date|after:now',
            'inspection_type' => 'required|in:routine,detailed,pre_sale,post_repair',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_duration' => 'required|integer|min:30',
            'notes' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        // Check inspector availability
        $conflict = Inspection::where('inspector_id', $validated['inspector_id'])
            ->where('scheduled_date', $validated['scheduled_date'])
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($conflict) {
            return back()
                ->withInput()
                ->withErrors(['inspector_id' => 'المفتش غير متاح في هذا التاريخ']);
        }

        $inspection = Inspection::create($validated);

        return redirect()
            ->route('inspections.schedule.show', $inspection)
            ->with('success', 'تم جدولة الفحص بنجاح');
    }

    public function show(Inspection $inspection)
    {
        $inspection->load(['property', 'inspector', 'client']);
        
        return view('inspections.schedule.show', compact('inspection'));
    }

    public function edit(Inspection $inspection)
    {
        if ($inspection->status !== 'scheduled') {
            return back()->with('error', 'لا يمكن تعديل فحص تم بدأه');
        }

        $properties = Property::all();
        $inspectors = Inspector::where('is_active', true)->get();
        
        return view('inspections.schedule.edit', compact('inspection', 'properties', 'inspectors'));
    }

    public function update(Request $request, Inspection $inspection)
    {
        if ($inspection->status !== 'scheduled') {
            return back()->with('error', 'لا يمكن تعديل فحص تم بدأه');
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'inspector_id' => 'required|exists:inspectors,id',
            'scheduled_date' => 'required|date|after:now',
            'inspection_type' => 'required|in:routine,detailed,pre_sale,post_repair',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_duration' => 'required|integer|min:30',
            'notes' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        $inspection->update($validated);

        return redirect()
            ->route('inspections.schedule.show', $inspection)
            ->with('success', 'تم تحديث الجدولة بنجاح');
    }

    public function reschedule(Request $request, Inspection $inspection)
    {
        $validated = $request->validate([
            'scheduled_date' => 'required|date|after:now',
            'reason' => 'required|string',
        ]);

        // Check inspector availability
        $conflict = Inspection::where('inspector_id', $inspection->inspector_id)
            ->where('scheduled_date', $validated['scheduled_date'])
            ->where('id', '!=', $inspection->id)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($conflict) {
            return back()
                ->withInput()
                ->withErrors(['scheduled_date' => 'المفتش غير متاح في هذا التاريخ']);
        }

        $inspection->update([
            'scheduled_date' => $validated['scheduled_date'],
            'reschedule_reason' => $validated['reason'],
            'rescheduled_at' => now(),
        ]);

        return redirect()
            ->route('inspections.schedule.show', $inspection)
            ->with('success', 'تم إعادة جدولة الفحص بنجاح');
    }

    public function cancel(Inspection $inspection)
    {
        if ($inspection->status === 'completed') {
            return back()->with('error', 'لا يمكن إلغاء فحص مكتمل');
        }

        $inspection->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()
            ->route('inspections.schedule.index')
            ->with('success', 'تم إلغاء الفحص بنجاح');
    }

    public function calendar()
    {
        $inspections = Inspection::with(['property', 'inspector'])
            ->where('status', 'scheduled')
            ->get();

        return view('inspections.schedule.calendar', compact('inspections'));
    }

    public function inspectorAvailability(Request $request)
    {
        $validated = $request->validate([
            'inspector_id' => 'required|exists:inspectors,id',
            'date' => 'required|date',
        ]);

        $inspections = Inspection::where('inspector_id', $validated['inspector_id'])
            ->whereDate('scheduled_date', $validated['date'])
            ->where('status', '!=', 'cancelled')
            ->get();

        return response()->json([
            'available' => $inspections->isEmpty(),
            'inspections' => $inspections,
        ]);
    }
}
