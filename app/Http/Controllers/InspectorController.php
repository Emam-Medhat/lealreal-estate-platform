<?php

namespace App\Http\Controllers;

use App\Models\Inspector;
use App\Models\Inspection;
use Illuminate\Http\Request;

class InspectorController extends Controller
{
    public function index()
    {
        $inspectors = Inspector::withCount(['inspections' => function($query) {
                $query->whereMonth('scheduled_date', now()->month);
            }])
            ->latest()
            ->paginate(10);

        return view('inspectors.index', compact('inspectors'));
    }

    public function create()
    {
        return view('inspectors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:inspectors,email',
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|unique:inspectors,license_number',
            'specializations' => 'required|array|min:1',
            'specializations.*' => 'string',
            'experience_years' => 'required|integer|min:0',
            'certifications' => 'nullable|array',
            'certifications.*' => 'string',
            'bio' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
        ]);

        $validated['specializations'] = json_encode($validated['specializations']);
        $validated['certifications'] = json_encode($validated['certifications'] ?? []);

        Inspector::create($validated);

        return redirect()
            ->route('inspectors.index')
            ->with('success', 'تم إضافة المفتش بنجاح');
    }

    public function show(Inspector $inspector)
    {
        $inspector->load(['inspections' => function($query) {
            $query->with('property')->latest()->take(10);
        }]);

        $stats = [
            'total_inspections' => $inspector->inspections()->count(),
            'completed_inspections' => $inspector->inspections()->where('status', 'completed')->count(),
            'this_month' => $inspector->inspections()->whereMonth('scheduled_date', now()->month)->count(),
            'average_rating' => $inspector->inspections()->avg('rating') ?? 0,
        ];

        return view('inspectors.show', compact('inspector', 'stats'));
    }

    public function edit(Inspector $inspector)
    {
        return view('inspectors.edit', compact('inspector'));
    }

    public function update(Request $request, Inspector $inspector)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:inspectors,email,' . $inspector->id,
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|unique:inspectors,license_number,' . $inspector->id,
            'specializations' => 'required|array|min:1',
            'specializations.*' => 'string',
            'experience_years' => 'required|integer|min:0',
            'certifications' => 'nullable|array',
            'certifications.*' => 'string',
            'bio' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
        ]);

        $validated['specializations'] = json_encode($validated['specializations']);
        $validated['certifications'] = json_encode($validated['certifications'] ?? []);

        $inspector->update($validated);

        return redirect()
            ->route('inspectors.show', $inspector)
            ->with('success', 'تم تحديث بيانات المفتش بنجاح');
    }

    public function destroy(Inspector $inspector)
    {
        if ($inspector->inspections()->where('status', '!=', 'completed')->exists()) {
            return back()->with('error', 'لا يمكن حذف المفتش لديه فحوصات نشطة');
        }

        $inspector->delete();

        return redirect()
            ->route('inspectors.index')
            ->with('success', 'تم حذف المفتش بنجاح');
    }

    public function schedule(Inspector $inspector)
    {
        $inspections = $inspector->inspections()
            ->with('property')
            ->where('status', 'scheduled')
            ->orderBy('scheduled_date')
            ->paginate(15);

        return view('inspectors.schedule', compact('inspector', 'inspections'));
    }

    public function availability(Inspector $inspector, Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $inspections = $inspector->inspections()
            ->whereDate('scheduled_date', $validated['date'])
            ->where('status', '!=', 'cancelled')
            ->get();

        $workingHours = [
            'start' => '09:00',
            'end' => '17:00',
            'available_slots' => [],
        ];

        // Calculate available time slots
        foreach ($inspections as $inspection) {
            $startTime = $inspection->scheduled_date->format('H:i');
            $endTime = $inspection->scheduled_date->addMinutes($inspection->estimated_duration ?? 60)->format('H:i');
            
            $workingHours['available_slots'][] = [
                'start' => $startTime,
                'end' => $endTime,
                'inspection_id' => $inspection->id,
            ];
        }

        return response()->json([
            'available' => $inspections->count() < 8, // Max 8 inspections per day
            'inspections' => $inspections,
            'working_hours' => $workingHours,
        ]);
    }

    public function performance(Inspector $inspector)
    {
        $stats = [
            'total_inspections' => $inspector->inspections()->count(),
            'completed_inspections' => $inspector->inspections()->where('status', 'completed')->count(),
            'cancelled_inspections' => $inspector->inspections()->where('status', 'cancelled')->count(),
            'average_duration' => $inspector->inspections()->avg('estimated_duration') ?? 0,
            'total_revenue' => $inspector->inspections()->sum('estimated_cost') ?? 0,
            'monthly_inspections' => $inspector->inspections()
                ->whereMonth('scheduled_date', now()->month)
                ->count(),
        ];

        $monthlyData = $inspector->inspections()
            ->selectRaw('MONTH(scheduled_date) as month, COUNT(*) as count')
            ->whereYear('scheduled_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('inspectors.performance', compact('inspector', 'stats', 'monthlyData'));
    }

    public function toggleActive(Inspector $inspector)
    {
        $inspector->update(['is_active' => !$inspector->is_active]);

        $status = $inspector->is_active ? 'تفعيل' : 'إلغاء تفعيل';
        
        return back()->with('success', "تم {$status} المفتش بنجاح");
    }

    public function export(Inspector $inspector)
    {
        $inspections = $inspector->inspections()
            ->with('property', 'report')
            ->latest()
            ->get();

        $csvData = [];
        $csvData[] = ['تاريخ الفحص', 'العقار', 'الحالة', 'التكلفة', 'التقييم'];

        foreach ($inspections as $inspection) {
            $csvData[] = [
                $inspection->scheduled_date->format('Y-m-d'),
                $inspection->property->title,
                $inspection->status,
                $inspection->estimated_cost ?? 0,
                $inspection->report?->overall_condition ?? '-',
            ];
        }

        $filename = "inspector_{$inspector->id}_report.csv";
        $handle = fopen($filename, 'w');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }
}
