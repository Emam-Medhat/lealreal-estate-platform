<?php

namespace App\Http\Controllers;

use App\Models\Appraisal;
use App\Models\AppraisalReport;
use Illuminate\Http\Request;

class AppraisalReportController extends Controller
{
    public function index()
    {
        $reports = AppraisalReport::with(['appraisal.property', 'appraiser'])
            ->latest()
            ->paginate(10);
            
        return view('appraisals.reports.index', compact('reports'));
    }

    public function create(Appraisal $appraisal)
    {
        if ($appraisal->status !== 'completed') {
            return back()->with('error', 'لا يمكن إنشاء تقرير لتقييم غير مكتمل');
        }

        return view('appraisals.reports.create', compact('appraisal'));
    }

    public function store(Request $request, Appraisal $appraisal)
    {
        $validated = $request->validate([
            'estimated_value' => 'required|numeric|min:0',
            'value_per_sqm' => 'required|numeric|min:0',
            'market_analysis' => 'required|string|min:100',
            'property_condition' => 'required|string|min:50',
            'comparable_properties' => 'required|array|min:3',
            'comparable_properties.*.address' => 'required|string',
            'comparable_properties.*.value' => 'required|numeric|min:0',
            'comparable_properties.*.size' => 'required|numeric|min:0',
            'comparable_properties.*.distance' => 'required|numeric|min:0',
            'adjustments' => 'nullable|array',
            'adjustments.*.type' => 'required|string',
            'adjustments.*.amount' => 'required|numeric',
            'adjustments.*.reason' => 'required|string',
            'conclusion' => 'required|string|min:100',
            'recommendations' => 'nullable|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx|max:5120',
        ]);

        $report = AppraisalReport::create([
            'appraisal_id' => $appraisal->id,
            'appraiser_id' => $appraisal->appraiser_id,
            'estimated_value' => $validated['estimated_value'],
            'value_per_sqm' => $validated['value_per_sqm'],
            'market_analysis' => $validated['market_analysis'],
            'property_condition' => $validated['property_condition'],
            'comparable_properties' => json_encode($validated['comparable_properties']),
            'adjustments' => json_encode($validated['adjustments'] ?? []),
            'conclusion' => $validated['conclusion'],
            'recommendations' => $validated['recommendations'],
            'report_date' => now(),
        ]);

        // Handle photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('appraisal_photos', 'public');
                $report->photos()->create([
                    'file_path' => $path,
                    'file_name' => $photo->getClientOriginalName(),
                    'file_size' => $photo->getSize(),
                ]);
            }
        }

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $attachment) {
                $path = $attachment->store('appraisal_attachments', 'public');
                $report->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $attachment->getClientOriginalName(),
                    'file_size' => $attachment->getSize(),
                ]);
            }
        }

        return redirect()
            ->route('appraisals.reports.show', $report)
            ->with('success', 'تم إنشاء تقرير التقييم بنجاح');
    }

    public function show(AppraisalReport $report)
    {
        $report->load(['appraisal.property', 'appraiser', 'photos', 'attachments']);
        
        return view('appraisals.reports.show', compact('report'));
    }

    public function edit(AppraisalReport $report)
    {
        $report->load(['appraisal', 'photos', 'attachments']);
        
        return view('appraisals.reports.edit', compact('report'));
    }

    public function update(Request $request, AppraisalReport $report)
    {
        $validated = $request->validate([
            'estimated_value' => 'required|numeric|min:0',
            'value_per_sqm' => 'required|numeric|min:0',
            'market_analysis' => 'required|string|min:100',
            'property_condition' => 'required|string|min:50',
            'conclusion' => 'required|string|min:100',
            'recommendations' => 'nullable|string',
        ]);

        $report->update($validated);

        return redirect()
            ->route('appraisals.reports.show', $report)
            ->with('success', 'تم تحديث تقرير التقييم بنجاح');
    }

    public function destroy(AppraisalReport $report)
    {
        $report->delete();

        return redirect()
            ->route('appraisals.reports.index')
            ->with('success', 'تم حذف تقرير التقييم بنجاح');
    }

    public function download(AppraisalReport $report)
    {
        $report->load(['appraisal.property', 'appraiser', 'photos', 'attachments']);
        
        // Generate PDF report
        $pdf = \PDF::loadView('appraisals.reports.pdf', compact('report'));
        
        return $pdf->download('appraisal_report_' . $report->id . '.pdf');
    }

    public function sendEmail(AppraisalReport $report, Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string',
        ]);

        $report->load(['appraisal.property', 'appraiser']);

        \Mail::to($validated['email'])->send(new \App\Mail\AppraisalReportMail($report, $validated['message']));

        return back()->with('success', 'تم إرسال التقرير بنجاح');
    }

    public function approve(AppraisalReport $report, Request $request)
    {
        $validated = $request->validate([
            'approved_by' => 'required|exists:users,id',
            'approval_notes' => 'nullable|string',
        ]);

        $report->update([
            'status' => 'approved',
            'approved_by' => $validated['approved_by'],
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'],
        ]);

        return back()->with('success', 'تم اعتماد التقرير بنجاح');
    }

    public function reject(AppraisalReport $report, Request $request)
    {
        $validated = $request->validate([
            'rejected_by' => 'required|exists:users,id',
            'rejection_reason' => 'required|string',
        ]);

        $report->update([
            'status' => 'rejected',
            'rejected_by' => $validated['rejected_by'],
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'تم رفض التقرير بنجاح');
    }

    public function addComparable(Request $request, AppraisalReport $report)
    {
        $validated = $request->validate([
            'address' => 'required|string',
            'value' => 'required|numeric|min:0',
            'size' => 'required|numeric|min:0',
            'distance' => 'required|numeric|min:0',
        ]);

        $comparables = json_decode($report->comparable_properties, true) ?? [];
        $comparables[] = $validated;

        $report->update(['comparable_properties' => json_encode($comparables)]);

        return back()->with('success', 'تم إضافة العقار المقارن بنجاح');
    }

    public function removeComparable(AppraisalReport $report, $index)
    {
        $comparables = json_decode($report->comparable_properties, true) ?? [];
        
        if (isset($comparables[$index])) {
            unset($comparables[$index]);
            $comparables = array_values($comparables);
            
            $report->update(['comparable_properties' => json_encode($comparables)]);
        }

        return back()->with('success', 'تم حذف العقار المقارن بنجاح');
    }

    public function calculateValue(AppraisalReport $report, Request $request)
    {
        $validated = $request->validate([
            'base_value' => 'required|numeric|min:0',
            'adjustments' => 'required|array',
            'adjustments.*.amount' => 'required|numeric',
        ]);

        $adjustedValue = $validated['base_value'];
        
        foreach ($validated['adjustments'] as $adjustment) {
            $adjustedValue += $adjustment['amount'];
        }

        return response()->json([
            'adjusted_value' => $adjustedValue,
        ]);
    }

    public function generateCertificate(AppraisalReport $report)
    {
        if ($report->status !== 'approved') {
            return back()->with('error', 'لا يمكن إنشاء شهادة لتقرير غير معتمد');
        }

        $report->load(['appraisal.property', 'appraiser']);
        
        // Generate certificate PDF
        $pdf = \PDF::loadView('appraisals.reports.certificate', compact('report'));
        
        return $pdf->download('appraisal_certificate_' . $report->id . '.pdf');
    }
}
