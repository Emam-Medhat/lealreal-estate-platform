<?php

namespace App\Http\Controllers;

use App\Models\Appraisal;
use App\Models\AppraisalReport;
use App\Models\Appraiser;
use App\Models\Property;
use Illuminate\Http\Request;

class AppraisalController extends Controller
{
    public function index()
    {
        $appraisals = Appraisal::with(['property', 'appraiser', 'report'])
            ->latest()
            ->paginate(10);
            
        return view('appraisals.index', compact('appraisals'));
    }

    public function create()
    {
        $properties = Property::all();
        $appraisers = Appraiser::where('is_active', true)->get();
        
        return view('appraisals.create', compact('properties', 'appraisers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'appraiser_id' => 'required|exists:appraisers,id',
            'appraisal_type' => 'required|in:market_value,insurance,tax,refinance',
            'purpose' => 'required|string',
            'scheduled_date' => 'required|date|after:now',
            'priority' => 'required|in:low,medium,high,urgent',
            'client_id' => 'nullable|exists:clients,id',
            'notes' => 'nullable|string',
        ]);

        $appraisal = Appraisal::create($validated);

        return redirect()
            ->route('appraisals.show', $appraisal)
            ->with('success', 'تم حجز التقييم بنجاح');
    }

    public function show(Appraisal $appraisal)
    {
        $appraisal->load(['property', 'appraiser', 'report', 'client']);
        
        return view('appraisals.show', compact('appraisal'));
    }

    public function edit(Appraisal $appraisal)
    {
        if ($appraisal->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل تقييم مكتمل');
        }

        $properties = Property::all();
        $appraisers = Appraiser::where('is_active', true)->get();
        
        return view('appraisals.edit', compact('appraisal', 'properties', 'appraisers'));
    }

    public function update(Request $request, Appraisal $appraisal)
    {
        if ($appraisal->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل تقييم مكتمل');
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'appraiser_id' => 'required|exists:appraisers,id',
            'appraisal_type' => 'required|in:market_value,insurance,tax,refinance',
            'purpose' => 'required|string',
            'scheduled_date' => 'required|date|after:now',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'client_id' => 'nullable|exists:clients,id',
            'notes' => 'nullable|string',
        ]);

        $appraisal->update($validated);

        return redirect()
            ->route('appraisals.show', $appraisal)
            ->with('success', 'تم تحديث التقييم بنجاح');
    }

    public function destroy(Appraisal $appraisal)
    {
        $appraisal->delete();

        return redirect()
            ->route('appraisals.index')
            ->with('success', 'تم حذف التقييم بنجاح');
    }

    public function start(Appraisal $appraisal)
    {
        if ($appraisal->status !== 'scheduled') {
            return back()->with('error', 'لا يمكن بدء تقييم غير مجدول');
        }

        $appraisal->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return back()->with('success', 'تم بدء التقييم بنجاح');
    }

    public function complete(Appraisal $appraisal)
    {
        if ($appraisal->status !== 'in_progress') {
            return back()->with('error', 'لا يمكن إكمال تقييم غير مبدوء');
        }

        $appraisal->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('appraisals.reports.create', $appraisal)
            ->with('success', 'تم إكمال التقييم بنجاح');
    }

    public function cancel(Appraisal $appraisal)
    {
        if ($appraisal->status === 'completed') {
            return back()->with('error', 'لا يمكن إلغاء تقييم مكتمل');
        }

        $appraisal->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()
            ->route('appraisals.index')
            ->with('success', 'تم إلغاء التقييم بنجاح');
    }

    public function dashboard()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // ⚡ Bolt Optimization: Consolidate 5 separate COUNT queries into a single selectRaw query with conditional aggregations,
        // reducing total queries and leveraging indexes on scheduled_date with range filtering instead of whereMonth.
        $agg = Appraisal::selectRaw("
            COUNT(*) as total,
            COUNT(CASE WHEN status = 'scheduled' THEN 1 END) as scheduled,
            COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
            COUNT(CASE WHEN scheduled_date >= ? AND scheduled_date <= ? THEN 1 END) as this_month
        ", [$startOfMonth, $endOfMonth])->first();

        $stats = [
            'total' => (int) ($agg->total ?? 0),
            'scheduled' => (int) ($agg->scheduled ?? 0),
            'in_progress' => (int) ($agg->in_progress ?? 0),
            'completed' => (int) ($agg->completed ?? 0),
            'this_month' => (int) ($agg->this_month ?? 0),
            'total_value' => AppraisalReport::sum('market_value') ?? 0,
        ];

        $recentAppraisals = Appraisal::with(['property', 'appraiser'])
            ->latest()
            ->take(5)
            ->get();

        $appraisalTypes = Appraisal::selectRaw('appraisal_type, COUNT(*) as count')
            ->groupBy('appraisal_type')
            ->get();

        return view('appraisals.dashboard', compact('stats', 'recentAppraisals', 'appraisalTypes'));
    }

    public function calendar()
    {
        $appraisals = Appraisal::with(['property', 'appraiser'])
            ->where('status', 'scheduled')
            ->get();

        return view('appraisals.calendar', compact('appraisals'));
    }

    public function assignAppraiser(Request $request, Appraisal $appraisal)
    {
        $validated = $request->validate([
            'appraiser_id' => 'required|exists:appraisers,id',
            'reason' => 'required|string',
        ]);

        $appraisal->update([
            'appraiser_id' => $validated['appraiser_id'],
            'assignment_reason' => $validated['reason'],
            'assigned_at' => now(),
        ]);

        return back()->with('success', 'تم تعيين المقيم بنجاح');
    }

    public function reschedule(Request $request, Appraisal $appraisal)
    {
        $validated = $request->validate([
            'scheduled_date' => 'required|date|after:now',
            'reason' => 'required|string',
        ]);

        $appraisal->update([
            'scheduled_date' => $validated['scheduled_date'],
            'reschedule_reason' => $validated['reason'],
            'rescheduled_at' => now(),
        ]);

        return back()->with('success', 'تم إعادة جدولة التقييم بنجاح');
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:scheduled,in_progress,completed,cancelled',
        ]);

        $query = Appraisal::with(['property', 'appraiser', 'report'])
            ->whereBetween('scheduled_date', [$validated['start_date'], $validated['end_date']]);

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $appraisals = $query->get();

        $csvData = [];
        $csvData[] = ['التاريخ', 'العقار', 'المقيم', 'نوع التقييم', 'الحالة', 'القيمة المقدرة'];

        foreach ($appraisals as $appraisal) {
            $csvData[] = [
                $appraisal->scheduled_date->format('Y-m-d'),
                $appraisal->property->title,
                $appraisal->appraiser->name,
                $appraisal->appraisal_type,
                $appraisal->status,
                $appraisal->report?->estimated_value ?? 0,
            ];
        }

        $filename = "appraisals_report.csv";
        $handle = fopen($filename, 'w');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }
}
