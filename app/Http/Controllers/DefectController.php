<?php

namespace App\Http\Controllers;

use App\Models\Defect;
use App\Models\InspectionReport;
use Illuminate\Http\Request;

class DefectController extends Controller
{
    public function index()
    {
        $defects = Defect::with(['inspectionReport.inspection.property', 'repairEstimate'])
            ->latest()
            ->paginate(10);
            
        return view('defects.index', compact('defects'));
    }

    public function create(InspectionReport $report)
    {
        return view('defects.create', compact('report'));
    }

    public function store(Request $request, InspectionReport $report)
    {
        $validated = $request->validate([
            'description' => 'required|string|min:10',
            'severity' => 'required|in:low,medium,high,critical',
            'location' => 'required|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'urgency' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:structural,electrical,plumbing,hvac,interior,exterior,safety,other',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string',
        ]);

        $defect = Defect::create([
            'inspection_report_id' => $report->id,
            'description' => $validated['description'],
            'severity' => $validated['severity'],
            'location' => $validated['location'],
            'estimated_cost' => $validated['estimated_cost'] ?? 0,
            'urgency' => $validated['urgency'],
            'category' => $validated['category'],
            'status' => 'pending',
            'notes' => $validated['notes'],
        ]);

        // Handle photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('defect_photos', 'public');
                $defect->photos()->create([
                    'file_path' => $path,
                    'file_name' => $photo->getClientOriginalName(),
                    'file_size' => $photo->getSize(),
                ]);
            }
        }

        return redirect()
            ->route('defects.show', $defect)
            ->with('success', 'تم إضافة العيب بنجاح');
    }

    public function show(Defect $defect)
    {
        $defect->load(['inspectionReport.inspection.property', 'repairEstimate', 'photos']);
        
        return view('defects.show', compact('defect'));
    }

    public function edit(Defect $defect)
    {
        return view('defects.edit', compact('defect'));
    }

    public function update(Request $request, Defect $defect)
    {
        $validated = $request->validate([
            'description' => 'required|string|min:10',
            'severity' => 'required|in:low,medium,high,critical',
            'location' => 'required|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'urgency' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:structural,electrical,plumbing,hvac,interior,exterior,safety,other',
            'status' => 'required|in:pending,in_progress,completed,deferred',
            'notes' => 'nullable|string',
        ]);

        $defect->update($validated);

        return redirect()
            ->route('defects.show', $defect)
            ->with('success', 'تم تحديث العيب بنجاح');
    }

    public function destroy(Defect $defect)
    {
        $defect->delete();

        return redirect()
            ->route('defects.index')
            ->with('success', 'تم حذف العيب بنجاح');
    }

    public function updateStatus(Request $request, Defect $defect)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,deferred',
            'notes' => 'nullable|string',
        ]);

        $defect->update($validated);

        return back()->with('success', 'تم تحديث حالة العيب بنجاح');
    }

    public function assign(Request $request, Defect $defect)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'assigned_by' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $defect->update([
            'assigned_to' => $validated['assigned_to'],
            'assigned_by' => $validated['assigned_by'],
            'assigned_at' => now(),
            'assignment_notes' => $validated['notes'],
        ]);

        return back()->with('success', 'تم تخصيص العيب بنجاح');
    }

    public function report(InspectionReport $report)
    {
        $defects = $report->defects()
            ->with('repairEstimate')
            ->get();

        $stats = [
            'total' => $defects->count(),
            'by_severity' => $defects->groupBy('severity')->map->count(),
            'by_category' => $defects->groupBy('category')->map->count(),
            'total_cost' => $defects->sum('estimated_cost'),
            'critical_count' => $defects->where('severity', 'critical')->count(),
            'urgent_count' => $defects->where('urgency', 'urgent')->count(),
        ];

        return view('defects.report', compact('report', 'defects', 'stats'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'severity' => 'nullable|in:low,medium,high,critical',
            'status' => 'nullable|in:pending,in_progress,completed,deferred',
        ]);

        $query = Defect::with(['inspectionReport.inspection.property']);

        if ($validated['start_date'] && $validated['end_date']) {
            $query->whereHas('inspectionReport', function($q) use ($validated) {
                $q->whereBetween('report_date', [$validated['start_date'], $validated['end_date']]);
            });
        }

        if (isset($validated['severity'])) {
            $query->where('severity', $validated['severity']);
        }

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $defects = $query->get();

        $csvData = [];
        $csvData[] = ['العقار', 'الوصف', 'الشدة', 'الموقع', 'التكلفة', 'الحالة', 'الفئة'];

        foreach ($defects as $defect) {
            $csvData[] = [
                $defect->inspectionReport->inspection->property->title,
                $defect->description,
                $defect->severity,
                $defect->location,
                $defect->estimated_cost,
                $defect->status,
                $defect->category,
            ];
        }

        $filename = "defects_report.csv";
        $handle = fopen($filename, 'w');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function dashboard()
    {
        $stats = [
            'total' => Defect::count(),
            'pending' => Defect::where('status', 'pending')->count(),
            'in_progress' => Defect::where('status', 'in_progress')->count(),
            'completed' => Defect::where('status', 'completed')->count(),
            'critical' => Defect::where('severity', 'critical')->count(),
            'total_cost' => Defect::sum('estimated_cost'),
        ];

        $recentDefects = Defect::with(['inspectionReport.inspection.property'])
            ->latest()
            ->take(5)
            ->get();

        $severityStats = Defect::selectRaw('severity, COUNT(*) as count')
            ->groupBy('severity')
            ->get();

        $categoryStats = Defect::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        return view('defects.dashboard', compact('stats', 'recentDefects', 'severityStats', 'categoryStats'));
    }

    public function addPhoto(Request $request, Defect $defect)
    {
        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
        ]);

        $path = $validated['photo']->store('defect_photos', 'public');
        
        $defect->photos()->create([
            'file_path' => $path,
            'file_name' => $validated['photo']->getClientOriginalName(),
            'file_size' => $validated['photo']->getSize(),
            'description' => $validated['description'],
        ]);

        return back()->with('success', 'تم إضافة الصورة بنجاح');
    }

    public function removePhoto(Defect $defect, $photoId)
    {
        $photo = $defect->photos()->findOrFail($photoId);
        $photo->delete();

        return back()->with('success', 'تم حذف الصورة بنجاح');
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'defect_ids' => 'required|array',
            'defect_ids.*' => 'exists:defects,id',
            'status' => 'required|in:pending,in_progress,completed,deferred',
            'notes' => 'nullable|string',
        ]);

        Defect::whereIn('id', $validated['defect_ids'])->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        return back()->with('success', 'تم تحديث العيوب بنجاح');
    }
}
