<?php

namespace App\Http\Controllers;

use App\Models\RepairEstimate;
use App\Models\Defect;
use Illuminate\Http\Request;

class RepairEstimateController extends Controller
{
    public function index()
    {
        $estimates = RepairEstimate::with(['defect.inspectionReport.inspection.property', 'contractor'])
            ->latest()
            ->paginate(10);
            
        return view('repair-estimates.index', compact('estimates'));
    }

    public function create(Defect $defect)
    {
        return view('repair-estimates.create', compact('defect'));
    }

    public function store(Request $request, Defect $defect)
    {
        $validated = $request->validate([
            'contractor_id' => 'required|exists:contractors,id',
            'estimated_cost' => 'required|numeric|min:0',
            'estimated_duration' => 'required|integer|min:1',
            'materials_cost' => 'required|numeric|min:0',
            'labor_cost' => 'required|numeric|min:0',
            'other_costs' => 'nullable|numeric|min:0',
            'description' => 'required|string|min:20',
            'materials' => 'required|array|min:1',
            'materials.*.name' => 'required|string',
            'materials.*.quantity' => 'required|numeric|min:0',
            'materials.*.unit_price' => 'required|numeric|min:0',
            'labor_items' => 'required|array|min:1',
            'labor_items.*.description' => 'required|string',
            'labor_items.*.hours' => 'required|numeric|min:0',
            'labor_items.*.hourly_rate' => 'required|numeric|min:0',
            'warranty_period' => 'nullable|integer|min:0',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
            'valid_until' => 'required|date|after:today',
        ]);

        $estimate = RepairEstimate::create([
            'defect_id' => $defect->id,
            'contractor_id' => $validated['contractor_id'],
            'estimated_cost' => $validated['estimated_cost'],
            'estimated_duration' => $validated['estimated_duration'],
            'materials_cost' => $validated['materials_cost'],
            'labor_cost' => $validated['labor_cost'],
            'other_costs' => $validated['other_costs'] ?? 0,
            'description' => $validated['description'],
            'materials' => json_encode($validated['materials']),
            'labor_items' => json_encode($validated['labor_items']),
            'warranty_period' => $validated['warranty_period'],
            'priority' => $validated['priority'],
            'notes' => $validated['notes'],
            'valid_until' => $validated['valid_until'],
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('repair-estimates.show', $estimate)
            ->with('success', 'تم إنشاء تقدير الإصلاح بنجاح');
    }

    public function show(RepairEstimate $estimate)
    {
        $estimate->load(['defect.inspectionReport.inspection.property', 'contractor']);
        
        return view('repair-estimates.show', compact('estimate'));
    }

    public function edit(RepairEstimate $estimate)
    {
        if ($estimate->status === 'approved') {
            return back()->with('error', 'لا يمكن تعديل تقدير معتمد');
        }

        return view('repair-estimates.edit', compact('estimate'));
    }

    public function update(Request $request, RepairEstimate $estimate)
    {
        if ($estimate->status === 'approved') {
            return back()->with('error', 'لا يمكن تعديل تقدير معتمد');
        }

        $validated = $request->validate([
            'contractor_id' => 'required|exists:contractors,id',
            'estimated_cost' => 'required|numeric|min:0',
            'estimated_duration' => 'required|integer|min:1',
            'materials_cost' => 'required|numeric|min:0',
            'labor_cost' => 'required|numeric|min:0',
            'other_costs' => 'nullable|numeric|min:0',
            'description' => 'required|string|min:20',
            'materials' => 'required|array|min:1',
            'materials.*.name' => 'required|string',
            'materials.*.quantity' => 'required|numeric|min:0',
            'materials.*.unit_price' => 'required|numeric|min:0',
            'labor_items' => 'required|array|min:1',
            'labor_items.*.description' => 'required|string',
            'labor_items.*.hours' => 'required|numeric|min:0',
            'labor_items.*.hourly_rate' => 'required|numeric|min:0',
            'warranty_period' => 'nullable|integer|min:0',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
            'valid_until' => 'required|date|after:today',
        ]);

        $estimate->update($validated);

        return redirect()
            ->route('repair-estimates.show', $estimate)
            ->with('success', 'تم تحديث تقدير الإصلاح بنجاح');
    }

    public function destroy(RepairEstimate $estimate)
    {
        if ($estimate->status === 'approved') {
            return back()->with('error', 'لا يمكن حذف تقدير معتمد');
        }

        $estimate->delete();

        return redirect()
            ->route('repair-estimates.index')
            ->with('success', 'تم حذف تقدير الإصلاح بنجاح');
    }

    public function approve(RepairEstimate $estimate, Request $request)
    {
        $validated = $request->validate([
            'approved_by' => 'required|exists:users,id',
            'approval_notes' => 'nullable|string',
        ]);

        $estimate->update([
            'status' => 'approved',
            'approved_by' => $validated['approved_by'],
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'],
        ]);

        return back()->with('success', 'تم اعتماد التقدير بنجاح');
    }

    public function reject(RepairEstimate $estimate, Request $request)
    {
        $validated = $request->validate([
            'rejected_by' => 'required|exists:users,id',
            'rejection_reason' => 'required|string',
        ]);

        $estimate->update([
            'status' => 'rejected',
            'rejected_by' => $validated['rejected_by'],
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'تم رفض التقدير بنجاح');
    }

    public function download(RepairEstimate $estimate)
    {
        $estimate->load(['defect.inspectionReport.inspection.property', 'contractor']);
        
        // Generate PDF estimate
        $pdf = \PDF::loadView('repair-estimates.pdf', compact('estimate'));
        
        return $pdf->download('repair_estimate_' . $estimate->id . '.pdf');
    }

    public function sendEmail(RepairEstimate $estimate, Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string',
        ]);

        $estimate->load(['defect.inspectionReport.inspection.property', 'contractor']);

        \Mail::to($validated['email'])->send(new \App\Mail\RepairEstimateMail($estimate, $validated['message']));

        return back()->with('success', 'تم إرسال التقدير بنجاح');
    }

    public function compare(Request $request)
    {
        $validated = $request->validate([
            'defect_id' => 'required|exists:defects,id',
            'estimate_ids' => 'required|array|min:2|max:5',
            'estimate_ids.*' => 'exists:repair_estimates,id',
        ]);

        $defect = Defect::with(['inspectionReport.inspection.property'])->findOrFail($validated['defect_id']);
        $estimates = RepairEstimate::with(['contractor'])
            ->whereIn('id', $validated['estimate_ids'])
            ->get();

        return view('repair-estimates.compare', compact('defect', 'estimates'));
    }

    public function dashboard()
    {
        $stats = [
            'total' => RepairEstimate::count(),
            'pending' => RepairEstimate::where('status', 'pending')->count(),
            'approved' => RepairEstimate::where('status', 'approved')->count(),
            'rejected' => RepairEstimate::where('status', 'rejected')->count(),
            'total_value' => RepairEstimate::where('status', 'approved')->sum('estimated_cost'),
            'this_month' => RepairEstimate::whereMonth('created_at', now()->month)->count(),
        ];

        $recentEstimates = RepairEstimate::with(['defect.inspectionReport.inspection.property', 'contractor'])
            ->latest()
            ->take(5)
            ->get();

        $contractorStats = RepairEstimate::selectRaw('contractor_id, COUNT(*) as count, AVG(estimated_cost) as avg_cost')
            ->with('contractor')
            ->groupBy('contractor_id')
            ->get();

        return view('repair-estimates.dashboard', compact('stats', 'recentEstimates', 'contractorStats'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);

        $query = RepairEstimate::with(['defect.inspectionReport.inspection.property', 'contractor'])
            ->whereBetween('created_at', [$validated['start_date'], $validated['end_date']]);

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $estimates = $query->get();

        $csvData = [];
        $csvData[] = ['العقار', 'الوصف', 'المقاول', 'التكلفة', 'الحالة', 'تاريخ الإنشاء'];

        foreach ($estimates as $estimate) {
            $csvData[] = [
                $estimate->defect->inspectionReport->inspection->property->title,
                $estimate->description,
                $estimate->contractor->name,
                $estimate->estimated_cost,
                $estimate->status,
                $estimate->created_at->format('Y-m-d'),
            ];
        }

        $filename = "repair_estimates_report.csv";
        $handle = fopen($filename, 'w');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'materials' => 'required|array',
            'materials.*.quantity' => 'required|numeric|min:0',
            'materials.*.unit_price' => 'required|numeric|min:0',
            'labor_items' => 'required|array',
            'labor_items.*.hours' => 'required|numeric|min:0',
            'labor_items.*.hourly_rate' => 'required|numeric|min:0',
            'other_costs' => 'nullable|numeric|min:0',
        ]);

        $materialsCost = 0;
        foreach ($validated['materials'] as $material) {
            $materialsCost += $material['quantity'] * $material['unit_price'];
        }

        $laborCost = 0;
        foreach ($validated['labor_items'] as $labor) {
            $laborCost += $labor['hours'] * $labor['hourly_rate'];
        }

        $totalCost = $materialsCost + $laborCost + ($validated['other_costs'] ?? 0);

        return response()->json([
            'materials_cost' => $materialsCost,
            'labor_cost' => $laborCost,
            'total_cost' => $totalCost,
        ]);
    }
}
