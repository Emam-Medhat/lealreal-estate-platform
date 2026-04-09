<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantScreening;
use Illuminate\Http\Request;

class TenantScreeningController extends Controller
{
    public function index()
    {
        $screenings = TenantScreening::with(['tenant', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('rentals.screenings.index', compact('screenings'));
    }

    public function create(Tenant $tenant)
    {
        return view('rentals.screenings.create', compact('tenant'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'screening_agency' => 'nullable|string|max:255',
            'report_reference' => 'nullable|string|max:255',
        ]);

        $screening = TenantScreening::create([
            'tenant_id' => $request->tenant_id,
            'screening_number' => 'SCR-' . date('Y') . '-' . str_pad(TenantScreening::count() + 1, 6, '0', STR_PAD_LEFT),
            'status' => 'pending',
            'screening_agency' => $request->screening_agency,
            'report_reference' => $request->report_reference,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('rentals.screenings.show', $screening)
            ->with('success', 'تم إنشاء طلب الفحص بنجاح');
    }

    public function show(TenantScreening $screening)
    {
        $screening->load(['tenant', 'user']);
        return view('rentals.screenings.show', compact('screening'));
    }

    public function edit(TenantScreening $screening)
    {
        if ($screening->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل فحص مكتمل');
        }
        
        return view('rentals.screenings.edit', compact('screening'));
    }

    public function update(Request $request, TenantScreening $screening)
    {
        if ($screening->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل فحص مكتمل');
        }

        $request->validate([
            'screening_agency' => 'nullable|string|max:255',
            'report_reference' => 'nullable|string|max:255',
        ]);

        $screening->update($request->only(['screening_agency', 'report_reference']));

        return redirect()->route('rentals.screenings.show', $screening)
            ->with('success', 'تم تحديث الفحص بنجاح');
    }

    public function startScreening(TenantScreening $screening)
    {
        if ($screening->status !== 'pending') {
            return back()->with('error', 'لا يمكن بدء هذا الفحص');
        }

        $screening->startScreening();

        return redirect()->route('rentals.screenings.show', $screening)
            ->with('success', 'تم بدء الفحص بنجاح');
    }

    public function completeScreening(Request $request, TenantScreening $screening)
    {
        if ($screening->status !== 'processing') {
            return back()->with('error', 'لا يمكن إكمال هذا الفحص');
        }

        $request->validate([
            'credit_check' => 'required|in:clear,flagged,failed',
            'criminal_check' => 'required|in:clear,flagged,convicted',
            'employment_verification' => 'required|in:verified,unverified,partially_verified',
            'rental_history' => 'required|in:positive,negative,neutral',
            'background_check' => 'required|in:clear,flagged,failed',
            'credit_score' => 'nullable|integer|min:300|max:850',
            'screening_notes' => 'nullable|string',
            'documents_verified' => 'boolean',
            'references_checked' => 'boolean',
            'income_verified' => 'boolean',
            'identity_verified' => 'boolean',
        ]);

        $results = [
            'credit_check' => $request->credit_check,
            'criminal_check' => $request->criminal_check,
            'employment_verification' => $request->employment_verification,
            'rental_history' => $request->rental_history,
            'background_check' => $request->background_check,
            'credit_score' => $request->credit_score,
            'screening_notes' => $request->screening_notes,
            'documents_verified' => $request->documents_verified ?? false,
            'references_checked' => $request->references_checked ?? false,
            'income_verified' => $request->income_verified ?? false,
            'identity_verified' => $request->identity_verified ?? false,
        ];

        // Calculate overall score and recommendation
        $results['overall_score'] = $screening->calculateOverallScore();
        $results['risk_level'] = $screening->determineRiskLevel();
        $results['recommendation'] = $screening->determineRecommendation();

        $screening->completeScreening($results);

        return redirect()->route('rentals.screenings.show', $screening)
            ->with('success', 'تم إكمال الفحص بنجاح');
    }

    public function approve(TenantScreening $screening)
    {
        if ($screening->status !== 'completed') {
            return back()->with('error', 'لا يمكن الموافقة على فحص غير مكتمل');
        }

        $screening->approve();

        return redirect()->route('rentals.screenings.show', $screening)
            ->with('success', 'تم الموافقة على الفحص بنجاح');
    }

    public function reject(Request $request, TenantScreening $screening)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if ($screening->status !== 'completed') {
            return back()->with('error', 'لا يمكن رفض فحص غير مكتمل');
        }

        $screening->reject($request->reason);

        return redirect()->route('rentals.screenings.show', $screening)
            ->with('success', 'تم رفض الفحص بنجاح');
    }

    public function destroy(TenantScreening $screening)
    {
        if ($screening->status === 'completed') {
            return back()->with('error', 'لا يمكن حذف فحص مكتمل');
        }

        $screening->delete();

        return redirect()->route('rentals.screenings.index')
            ->with('success', 'تم حذف الفحص بنجاح');
    }

    public function export()
    {
        $screenings = TenantScreening::with(['tenant', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $csvData = [];
        $csvData[] = ['رقم الفحص', 'المستأجر', 'الحالة', 'التوصية', 'مستوى المخاطرة', 'النتيجة الإجمالية', 'تاريخ الفحص'];

        foreach ($screenings as $screening) {
            $csvData[] = [
                $screening->screening_number,
                $screening->tenant->name,
                $screening->status,
                $screening->recommendation,
                $screening->risk_level,
                $screening->overall_score,
                $screening->screening_date?->format('Y-m-d'),
            ];
        }

        $filename = 'tenant_screenings_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, 200, $headers);
    }

    public function dashboard()
    {
        $pendingScreenings = TenantScreening::pending()->count();
        $processingScreenings = TenantScreening::processing()->count();
        $completedScreenings = TenantScreening::completed()->count();
        $passedScreenings = TenantScreening::passed()->count();
        $failedScreenings = TenantScreening::failed()->count();

        $recentScreenings = TenantScreening::with(['tenant'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $screeningAgencyStats = TenantScreening::selectRaw('screening_agency, COUNT(*) as count')
            ->whereNotNull('screening_agency')
            ->groupBy('screening_agency')
            ->get();

        return view('rentals.screenings.dashboard', compact(
            'pendingScreenings',
            'processingScreenings',
            'completedScreenings',
            'passedScreenings',
            'failedScreenings',
            'recentScreenings',
            'screeningAgencyStats'
        ));
    }
}
