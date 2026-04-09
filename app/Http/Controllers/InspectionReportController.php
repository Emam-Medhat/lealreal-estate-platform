<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\InspectionReport;
use App\Models\Defect;
use Illuminate\Http\Request;

class InspectionReportController extends Controller
{
    public function index()
    {
        $reports = InspectionReport::with(['inspection.property', 'inspector'])
            ->latest()
            ->paginate(10);
            
        return view('inspections.reports.index', compact('reports'));
    }

    public function create(Inspection $inspection)
    {
        if ($inspection->status !== 'completed') {
            return back()->with('error', 'لا يمكن إنشاء تقرير لفحص غير مكتمل');
        }

        return view('inspections.reports.create', compact('inspection'));
    }

    public function store(Request $request, Inspection $inspection)
    {
        $validated = $request->validate([
            'overall_condition' => 'required|in:excellent,good,fair,poor',
            'summary' => 'required|string|min:50',
            'recommendations' => 'required|string|min:50',
            'next_inspection_date' => 'nullable|date|after:today',
            'estimated_repair_cost' => 'nullable|numeric|min:0',
            'urgent_repairs' => 'nullable|string',
            'defects' => 'nullable|array',
            'defects.*.description' => 'required|string',
            'defects.*.severity' => 'required|in:low,medium,high,critical',
            'defects.*.location' => 'required|string',
            'defects.*.estimated_cost' => 'nullable|numeric|min:0',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $report = InspectionReport::create([
            'inspection_id' => $inspection->id,
            'inspector_id' => $inspection->inspector_id,
            'overall_condition' => $validated['overall_condition'],
            'summary' => $validated['summary'],
            'recommendations' => $validated['recommendations'],
            'next_inspection_date' => $validated['next_inspection_date'],
            'estimated_repair_cost' => $validated['estimated_repair_cost'],
            'urgent_repairs' => $validated['urgent_repairs'],
            'report_date' => now(),
        ]);

        // Create defects
        if (isset($validated['defects'])) {
            foreach ($validated['defects'] as $defectData) {
                Defect::create([
                    'inspection_report_id' => $report->id,
                    'description' => $defectData['description'],
                    'severity' => $defectData['severity'],
                    'location' => $defectData['location'],
                    'estimated_cost' => $defectData['estimated_cost'] ?? 0,
                ]);
            }
        }

        // Handle photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('inspection_photos', 'public');
                $report->photos()->create([
                    'file_path' => $path,
                    'file_name' => $photo->getClientOriginalName(),
                    'file_size' => $photo->getSize(),
                ]);
            }
        }

        return redirect()
            ->route('inspections.reports.show', $report)
            ->with('success', 'تم إنشاء تقرير الفحص بنجاح');
    }

    public function show(InspectionReport $report)
    {
        $report->load(['inspection.property', 'inspector', 'defects', 'photos']);
        
        return view('inspections.reports.show', compact('report'));
    }

    public function edit(InspectionReport $report)
    {
        $report->load(['inspection', 'defects', 'photos']);
        
        return view('inspections.reports.edit', compact('report'));
    }

    public function update(Request $request, InspectionReport $report)
    {
        $validated = $request->validate([
            'overall_condition' => 'required|in:excellent,good,fair,poor',
            'summary' => 'required|string|min:50',
            'recommendations' => 'required|string|min:50',
            'next_inspection_date' => 'nullable|date|after:today',
            'estimated_repair_cost' => 'nullable|numeric|min:0',
            'urgent_repairs' => 'nullable|string',
        ]);

        $report->update($validated);

        return redirect()
            ->route('inspections.reports.show', $report)
            ->with('success', 'تم تحديث تقرير الفحص بنجاح');
    }

    public function destroy(InspectionReport $report)
    {
        $report->delete();

        return redirect()
            ->route('inspections.reports.index')
            ->with('success', 'تم حذف تقرير الفحص بنجاح');
    }

    public function download(InspectionReport $report)
    {
        $report->load(['inspection.property', 'inspector', 'defects', 'photos']);
        
        // Generate PDF report
        $pdf = \PDF::loadView('inspections.reports.pdf', compact('report'));
        
        return $pdf->download('inspection_report_' . $report->id . '.pdf');
    }

    public function sendEmail(InspectionReport $report, Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string',
        ]);

        $report->load(['inspection.property', 'inspector']);

        \Mail::to($validated['email'])->send(new \App\Mail\InspectionReportMail($report, $validated['message']));

        return back()->with('success', 'تم إرسال التقرير بنجاح');
    }

    public function addDefect(Request $request, InspectionReport $report)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical',
            'location' => 'required|string',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        Defect::create([
            'inspection_report_id' => $report->id,
            'description' => $validated['description'],
            'severity' => $validated['severity'],
            'location' => $validated['location'],
            'estimated_cost' => $validated['estimated_cost'] ?? 0,
        ]);

        return back()->with('success', 'تم إضافة العيب بنجاح');
    }

    public function updateDefect(Request $request, Defect $defect)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical',
            'location' => 'required|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $defect->update($validated);

        return back()->with('success', 'تم تحديث العيب بنجاح');
    }

    public function deleteDefect(Defect $defect)
    {
        $defect->delete();

        return back()->with('success', 'تم حذف العيب بنجاح');
    }
}
