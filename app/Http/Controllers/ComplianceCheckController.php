<?php

namespace App\Http\Controllers;

use App\Models\ComplianceCheck;
use App\Models\Property;
use Illuminate\Http\Request;

class ComplianceCheckController extends Controller
{
    public function index()
    {
        $checks = ComplianceCheck::with(['property', 'inspector'])
            ->latest()
            ->paginate(10);
            
        return view('compliance-checks.index', compact('checks'));
    }

    public function create()
    {
        $properties = Property::all();
        
        return view('compliance-checks.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'inspector_id' => 'required|exists:inspectors,id',
            'check_type' => 'required|in:safety,building_code,environmental,accessibility,fire,electrical,plumbing',
            'scheduled_date' => 'required|date|after:today',
            'priority' => 'required|in:low,medium,high,urgent',
            'check_items' => 'required|array|min:1',
            'check_items.*.category' => 'required|string',
            'check_items.*.description' => 'required|string',
            'check_items.*.requirement' => 'required|string',
            'check_items.*.status' => 'required|in:compliant,non_compliant,not_applicable',
            'check_items.*.notes' => 'nullable|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string',
        ]);

        $check = ComplianceCheck::create([
            'property_id' => $validated['property_id'],
            'inspector_id' => $validated['inspector_id'],
            'check_type' => $validated['check_type'],
            'scheduled_date' => $validated['scheduled_date'],
            'priority' => $validated['priority'],
            'check_items' => json_encode($validated['check_items']),
            'status' => 'scheduled',
            'notes' => $validated['notes'],
        ]);

        // Handle photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('compliance_photos', 'public');
                $check->photos()->create([
                    'file_path' => $path,
                    'file_name' => $photo->getClientOriginalName(),
                    'file_size' => $photo->getSize(),
                ]);
            }
        }

        return redirect()
            ->route('compliance-checks.show', $check)
            ->with('success', 'تم إنشاء فحص الامتثال بنجاح');
    }

    public function show(ComplianceCheck $check)
    {
        $check->load(['property', 'inspector', 'photos']);
        
        return view('compliance-checks.show', compact('check'));
    }

    public function edit(ComplianceCheck $check)
    {
        if ($check->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل فحص مكتمل');
        }

        return view('compliance-checks.edit', compact('check'));
    }

    public function update(Request $request, ComplianceCheck $check)
    {
        if ($check->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل فحص مكتمل');
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'inspector_id' => 'required|exists:inspectors,id',
            'check_type' => 'required|in:safety,building_code,environmental,accessibility,fire,electrical,plumbing',
            'scheduled_date' => 'required|date|after:today',
            'priority' => 'required|in:low,medium,high,urgent',
            'check_items' => 'required|array|min:1',
            'check_items.*.category' => 'required|string',
            'check_items.*.description' => 'required|string',
            'check_items.*.requirement' => 'required|string',
            'check_items.*.status' => 'required|in:compliant,non_compliant,not_applicable',
            'check_items.*.notes' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $check->update($validated);

        return redirect()
            ->route('compliance-checks.show', $check)
            ->with('success', 'تم تحديث فحص الامتثال بنجاح');
    }

    public function destroy(ComplianceCheck $check)
    {
        $check->delete();

        return redirect()
            ->route('compliance-checks.index')
            ->with('success', 'تم حذف فحص الامتثال بنجاح');
    }

    public function start(ComplianceCheck $check)
    {
        if ($check->status !== 'scheduled') {
            return back()->with('error', 'لا يمكن بدء فحص غير مجدول');
        }

        $check->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return back()->with('success', 'تم بدء الفحص بنجاح');
    }

    public function complete(ComplianceCheck $check, Request $request)
    {
        if ($check->status !== 'in_progress') {
            return back()->with('error', 'لا يمكن إكمال فحص غير مبدوء');
        }

        $validated = $request->validate([
            'check_items' => 'required|array|min:1',
            'check_items.*.category' => 'required|string',
            'check_items.*.description' => 'required|string',
            'check_items.*.requirement' => 'required|string',
            'check_items.*.status' => 'required|in:compliant,non_compliant,not_applicable',
            'check_items.*.notes' => 'nullable|string',
            'summary' => 'required|string|min:50',
            'recommendations' => 'nullable|string',
            'next_check_date' => 'nullable|date|after:today',
        ]);

        $check->update([
            'check_items' => json_encode($validated['check_items']),
            'summary' => $validated['summary'],
            'recommendations' => $validated['recommendations'],
            'next_check_date' => $validated['next_check_date'],
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'تم إكمال الفحص بنجاح');
    }

    public function report(ComplianceCheck $check)
    {
        $check->load(['property', 'inspector', 'photos']);
        
        // Generate compliance report PDF
        $pdf = \PDF::loadView('compliance-checks.report', compact('check'));
        
        return $pdf->download('compliance_check_report_' . $check->id . '.pdf');
    }

    public function certificate(ComplianceCheck $check)
    {
        if ($check->status !== 'completed') {
            return back()->with('error', 'لا يمكن إنشاء شهادة لفحص غير مكتمل');
        }

        $check->load(['property', 'inspector']);
        
        // Generate compliance certificate PDF
        $pdf = \PDF::loadView('compliance-checks.certificate', compact('check'));
        
        return $pdf->download('compliance_certificate_' . $check->id . '.pdf');
    }

    public function dashboard()
    {
        $stats = [
            'total' => ComplianceCheck::count(),
            'scheduled' => ComplianceCheck::where('status', 'scheduled')->count(),
            'in_progress' => ComplianceCheck::where('status', 'in_progress')->count(),
            'completed' => ComplianceCheck::where('status', 'completed')->count(),
            'this_month' => ComplianceCheck::whereMonth('scheduled_date', now()->month)->count(),
            'compliance_rate' => $this->calculateComplianceRate(),
        ];

        $recentChecks = ComplianceCheck::with(['property', 'inspector'])
            ->latest()
            ->take(5)
            ->get();

        $checkTypes = ComplianceCheck::selectRaw('check_type, COUNT(*) as count')
            ->groupBy('check_type')
            ->get();

        return view('compliance-checks.dashboard', compact('stats', 'recentChecks', 'checkTypes'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:scheduled,in_progress,completed',
            'check_type' => 'nullable|in:safety,building_code,environmental,accessibility,fire,electrical,plumbing',
        ]);

        $query = ComplianceCheck::with(['property', 'inspector'])
            ->whereBetween('scheduled_date', [$validated['start_date'], $validated['end_date']]);

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (isset($validated['check_type'])) {
            $query->where('check_type', $validated['check_type']);
        }

        $checks = $query->get();

        $csvData = [];
        $csvData[] = ['العقار', 'نوع الفحص', 'الحالة', 'المفتش', 'تاريخ الفحص'];

        foreach ($checks as $check) {
            $csvData[] = [
                $check->property->title,
                $check->check_type,
                $check->status,
                $check->inspector->name,
                $check->scheduled_date->format('Y-m-d'),
            ];
        }

        $filename = "compliance_checks_report.csv";
        $handle = fopen($filename, 'w');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    private function calculateComplianceRate()
    {
        $completedChecks = ComplianceCheck::where('status', 'completed')->get();
        
        if ($completedChecks->isEmpty()) {
            return 0;
        }

        $totalItems = 0;
        $compliantItems = 0;

        foreach ($completedChecks as $check) {
            $items = json_decode($check->check_items, true) ?? [];
            foreach ($items as $item) {
                $totalItems++;
                if ($item['status'] === 'compliant') {
                    $compliantItems++;
                }
            }
        }

        return $totalItems > 0 ? round(($compliantItems / $totalItems) * 100, 2) : 0;
    }

    public function addPhoto(Request $request, ComplianceCheck $check)
    {
        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
        ]);

        $path = $validated['photo']->store('compliance_photos', 'public');
        
        $check->photos()->create([
            'file_path' => $path,
            'file_name' => $validated['photo']->getClientOriginalName(),
            'file_size' => $validated['photo']->getSize(),
            'description' => $validated['description'],
        ]);

        return back()->with('success', 'تم إضافة الصورة بنجاح');
    }

    public function removePhoto(ComplianceCheck $check, $photoId)
    {
        $photo = $check->photos()->findOrFail($photoId);
        $photo->delete();

        return back()->with('success', 'تم حذف الصورة بنجاح');
    }
}
