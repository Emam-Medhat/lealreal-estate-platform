<?php

namespace App\Http\Controllers;

use App\Models\Appraiser;
use App\Models\Appraisal;
use Illuminate\Http\Request;

class AppraiserController extends Controller
{
    public function index()
    {
        $appraisers = Appraiser::withCount(['appraisals' => function($query) {
                $query->whereMonth('scheduled_date', now()->month);
            }])
            ->latest()
            ->paginate(10);

        return view('appraisers.index', compact('appraisers'));
    }

    public function create()
    {
        return view('appraisers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:appraisers,email',
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|unique:appraisers,license_number',
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
            'education' => 'nullable|string',
            'professional_memberships' => 'nullable|array',
            'professional_memberships.*' => 'string',
        ]);

        $validated['specializations'] = json_encode($validated['specializations']);
        $validated['certifications'] = json_encode($validated['certifications'] ?? []);
        $validated['professional_memberships'] = json_encode($validated['professional_memberships'] ?? []);

        Appraiser::create($validated);

        return redirect()
            ->route('appraisers.index')
            ->with('success', 'تم إضافة المقيم بنجاح');
    }

    public function show(Appraiser $appraiser)
    {
        $appraiser->load(['appraisals' => function($query) {
            $query->with('property')->latest()->take(10);
        }]);

        $stats = [
            'total_appraisals' => $appraiser->appraisals()->count(),
            'completed_appraisals' => $appraiser->appraisals()->where('status', 'completed')->count(),
            'this_month' => $appraiser->appraisals()->whereMonth('scheduled_date', now()->month)->count(),
            'average_rating' => $appraiser->appraisals()->avg('rating') ?? 0,
            'total_value_appraised' => $appraiser->appraisals()->whereHas('report')->sum('estimated_value') ?? 0,
        ];

        return view('appraisers.show', compact('appraiser', 'stats'));
    }

    public function edit(Appraiser $appraiser)
    {
        return view('appraisers.edit', compact('appraiser'));
    }

    public function update(Request $request, Appraiser $appraiser)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:appraisers,email,' . $appraiser->id,
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|unique:appraisers,license_number,' . $appraiser->id,
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
            'education' => 'nullable|string',
            'professional_memberships' => 'nullable|array',
            'professional_memberships.*' => 'string',
        ]);

        $validated['specializations'] = json_encode($validated['specializations']);
        $validated['certifications'] = json_encode($validated['certifications'] ?? []);
        $validated['professional_memberships'] = json_encode($validated['professional_memberships'] ?? []);

        $appraiser->update($validated);

        return redirect()
            ->route('appraisers.show', $appraiser)
            ->with('success', 'تم تحديث بيانات المقيم بنجاح');
    }

    public function destroy(Appraiser $appraiser)
    {
        if ($appraiser->appraisals()->where('status', '!=', 'completed')->exists()) {
            return back()->with('error', 'لا يمكن حذف المقيم لديه تقييمات نشطة');
        }

        $appraiser->delete();

        return redirect()
            ->route('appraisers.index')
            ->with('success', 'تم حذف المقيم بنجاح');
    }

    public function schedule(Appraiser $appraiser)
    {
        $appraisals = $appraiser->appraisals()
            ->with('property')
            ->where('status', 'scheduled')
            ->orderBy('scheduled_date')
            ->paginate(15);

        return view('appraisers.schedule', compact('appraiser', 'appraisals'));
    }

    public function performance(Appraiser $appraiser)
    {
        $stats = [
            'total_appraisals' => $appraiser->appraisals()->count(),
            'completed_appraisals' => $appraiser->appraisals()->where('status', 'completed')->count(),
            'cancelled_appraisals' => $appraiser->appraisals()->where('status', 'cancelled')->count(),
            'average_duration' => $appraiser->appraisals()->avg('estimated_duration') ?? 0,
            'total_revenue' => $appraiser->appraisals()->sum('estimated_cost') ?? 0,
            'monthly_appraisals' => $appraiser->appraisals()
                ->whereMonth('scheduled_date', now()->month)
                ->count(),
        ];

        $monthlyData = $appraiser->appraisals()
            ->selectRaw('MONTH(scheduled_date) as month, COUNT(*) as count')
            ->whereYear('scheduled_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $appraisalTypes = $appraiser->appraisals()
            ->selectRaw('appraisal_type, COUNT(*) as count')
            ->groupBy('appraisal_type')
            ->get();

        return view('appraisers.performance', compact('appraiser', 'stats', 'monthlyData', 'appraisalTypes'));
    }

    public function toggleActive(Appraiser $appraiser)
    {
        $appraiser->update(['is_active' => !$appraiser->is_active]);

        $status = $appraiser->is_active ? 'تفعيل' : 'إلغاء تفعيل';
        
        return back()->with('success', "تم {$status} المقيم بنجاح");
    }

    public function export(Appraiser $appraiser)
    {
        $appraisals = $appraiser->appraisals()
            ->with('property', 'report')
            ->latest()
            ->get();

        $csvData = [];
        $csvData[] = ['تاريخ التقييم', 'العقار', 'الحالة', 'القيمة المقدرة', 'نوع التقييم'];

        foreach ($appraisals as $appraisal) {
            $csvData[] = [
                $appraisal->scheduled_date->format('Y-m-d'),
                $appraisal->property->title,
                $appraisal->status,
                $appraisal->report?->estimated_value ?? 0,
                $appraisal->appraisal_type,
            ];
        }

        $filename = "appraiser_{$appraiser->id}_report.csv";
        $handle = fopen($filename, 'w');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function availability(Appraiser $appraiser, Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $appraisals = $appraiser->appraisals()
            ->whereDate('scheduled_date', $validated['date'])
            ->where('status', '!=', 'cancelled')
            ->get();

        $workingHours = [
            'start' => '09:00',
            'end' => '17:00',
            'available_slots' => [],
        ];

        // Calculate available time slots
        foreach ($appraisals as $appraisal) {
            $startTime = $appraisal->scheduled_date->format('H:i');
            $endTime = $appraisal->scheduled_date->addMinutes($appraisal->estimated_duration ?? 120)->format('H:i');
            
            $workingHours['available_slots'][] = [
                'start' => $startTime,
                'end' => $endTime,
                'appraisal_id' => $appraisal->id,
            ];
        }

        return response()->json([
            'available' => $appraisals->count() < 6, // Max 6 appraisals per day
            'appraisals' => $appraisals,
            'working_hours' => $workingHours,
        ]);
    }

    public function certifications(Appraiser $appraiser)
    {
        $certifications = json_decode($appraiser->certifications, true) ?? [];
        
        return view('appraisers.certifications', compact('appraiser', 'certifications'));
    }

    public function updateCertifications(Request $request, Appraiser $appraiser)
    {
        $validated = $request->validate([
            'certifications' => 'required|array',
            'certifications.*.name' => 'required|string',
            'certifications.*.issuer' => 'required|string',
            'certifications.*.date' => 'required|date',
            'certifications.*.expiry_date' => 'nullable|date|after:certifications.*.date',
        ]);

        $appraiser->update(['certifications' => json_encode($validated['certifications'])]);

        return back()->with('success', 'تم تحديث الشهادات بنجاح');
    }
}
