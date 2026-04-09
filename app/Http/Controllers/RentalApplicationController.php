<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Tenant;
use App\Models\RentalApplication;
use App\Models\Lease;
use Illuminate\Http\Request;

class RentalApplicationController extends Controller
{
    public function index()
    {
        $applications = RentalApplication::with(['property', 'tenant', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('rentals.applications.index', compact('applications'));
    }

    public function create()
    {
        $properties = Property::where('status', 'available')->get();
        return view('rentals.applications.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email|max:255',
            'applicant_phone' => 'required|string|max:20',
            'applicant_address' => 'required|string|max:500',
            'applicant_income' => 'nullable|numeric|min:0',
            'applicant_employment' => 'nullable|string|max:255',
            'move_in_date' => 'required|date|after_or_equal:today',
            'lease_duration' => 'required|integer|min:1|max:60',
            'offered_rent' => 'required|numeric|min:0',
            'special_requests' => 'nullable|string|max:1000',
            'priority' => 'required|in:low,medium,high',
        ]);

        $application = RentalApplication::create([
            'property_id' => $request->property_id,
            'application_number' => 'APP-' . date('Y') . '-' . str_pad(RentalApplication::count() + 1, 6, '0', STR_PAD_LEFT),
            'applicant_name' => $request->applicant_name,
            'applicant_email' => $request->applicant_email,
            'applicant_phone' => $request->applicant_phone,
            'applicant_address' => $request->applicant_address,
            'applicant_income' => $request->applicant_income,
            'applicant_employment' => $request->applicant_employment,
            'move_in_date' => $request->move_in_date,
            'lease_duration' => $request->lease_duration,
            'offered_rent' => $request->offered_rent,
            'special_requests' => $request->special_requests,
            'priority' => $request->priority,
            'status' => 'pending',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('rentals.applications.show', $application)
            ->with('success', 'تم إنشاء طلب الإيجار بنجاح');
    }

    public function show(RentalApplication $application)
    {
        $application->load(['property', 'tenant', 'user', 'lease']);
        return view('rentals.applications.show', compact('application'));
    }

    public function edit(RentalApplication $application)
    {
        if (!in_array($application->status, ['pending', 'reviewing'])) {
            return back()->with('error', 'لا يمكن تعديل طلب الإيجار');
        }
        
        $properties = Property::where('status', 'available')->get();
        return view('rentals.applications.edit', compact('application', 'properties'));
    }

    public function update(Request $request, RentalApplication $application)
    {
        if (!in_array($application->status, ['pending', 'reviewing'])) {
            return back()->with('error', 'لا يمكن تعديل طلب الإيجار');
        }

        $request->validate([
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email|max:255',
            'applicant_phone' => 'required|string|max:20',
            'applicant_address' => 'required|string|max:500',
            'applicant_income' => 'nullable|numeric|min:0',
            'applicant_employment' => 'nullable|string|max:255',
            'move_in_date' => 'required|date|after_or_equal:today',
            'lease_duration' => 'required|integer|min:1|max:60',
            'offered_rent' => 'required|numeric|min:0',
            'special_requests' => 'nullable|string|max:1000',
            'priority' => 'required|in:low,medium,high',
        ]);

        $application->update($request->only([
            'applicant_name', 'applicant_email', 'applicant_phone', 'applicant_address',
            'applicant_income', 'applicant_employment', 'move_in_date', 'lease_duration',
            'offered_rent', 'special_requests', 'priority'
        ]));

        return redirect()->route('rentals.applications.show', $application)
            ->with('success', 'تم تحديث طلب الإيجار بنجاح');
    }

    public function startReview(RentalApplication $application)
    {
        if ($application->status !== 'pending') {
            return back()->with('error', 'لا يمكن بدء مراجعة هذا الطلب');
        }

        $application->startReview();

        return redirect()->route('rentals.applications.show', $application)
            ->with('success', 'تم بدء مراجعة الطلب بنجاح');
    }

    public function approve(Request $request, RentalApplication $application)
    {
        if ($application->status !== 'reviewing') {
            return back()->with('error', 'لا يمكن الموافقة على طلب غير قيد المراجعة');
        }

        $request->validate([
            'create_lease' => 'boolean',
            'lease_start_date' => 'required_if:create_lease,1|date|after_or_equal:today',
            'lease_end_date' => 'required_if:create_lease,1|date|after:lease_start_date',
            'rent_amount' => 'required_if:create_lease,1|numeric|min:0',
        ]);

        $application->approve();

        // Create lease if requested
        if ($request->create_lease) {
            $lease = $application->createLease([
                'start_date' => $request->lease_start_date,
                'end_date' => $request->lease_end_date,
                'rent_amount' => $request->rent_amount,
            ]);

            return redirect()->route('rentals.leases.show', $lease)
                ->with('success', 'تم الموافقة على الطلب وإنشاء العقد بنجاح');
        }

        return redirect()->route('rentals.applications.show', $application)
            ->with('success', 'تم الموافقة على الطلب بنجاح');
    }

    public function reject(Request $request, RentalApplication $application)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($application->status !== 'reviewing') {
            return back()->with('error', 'لا يمكن رفض طلب غير قيد المراجعة');
        }

        $application->reject($request->rejection_reason);

        return redirect()->route('rentals.applications.show', $application)
            ->with('success', 'تم رفض الطلب بنجاح');
    }

    public function cancel(Request $request, RentalApplication $application)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        if (!in_array($application->status, ['pending', 'reviewing'])) {
            return back()->with('error', 'لا يمكن إلغاء هذا الطلب');
        }

        $application->update([
            'status' => 'cancelled',
            'notes' => ($application->notes ?? '') . "\n\nCancelled: " . $request->cancellation_reason . " (" . now()->toDateString() . ")",
        ]);

        return redirect()->route('rentals.applications.show', $application)
            ->with('success', 'تم إلغاء الطلب بنجاح');
    }

    public function createLease(RentalApplication $application)
    {
        if ($application->status !== 'approved') {
            return back()->with('error', 'لا يمكن إنشاء عقد من طلب غير موافق عليه');
        }

        if ($application->lease) {
            return back()->with('error', 'تم إنشاء عقد لهذا الطلب بالفعل');
        }

        return view('rentals.applications.create-lease', compact('application'));
    }

    public function storeLease(Request $request, RentalApplication $application)
    {
        if ($application->status !== 'approved') {
            return back()->with('error', 'لا يمكن إنشاء عقد من طلب غير موافق عليه');
        }

        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'rent_amount' => 'required|numeric|min:0',
            'security_deposit' => 'required|numeric|min:0',
            'payment_due_day' => 'required|integer|min:1|max:31',
        ]);

        $lease = $application->createLease($request->all());

        return redirect()->route('rentals.leases.show', $lease)
            ->with('success', 'تم إنشاء العقد بنجاح');
    }

    public function destroy(RentalApplication $application)
    {
        if (!in_array($application->status, ['pending', 'cancelled'])) {
            return back()->with('error', 'لا يمكن حذف طلب الإيجار');
        }

        $application->delete();

        return redirect()->route('rentals.applications.index')
            ->with('success', 'تم حذف طلب الإيجار بنجاح');
    }

    public function export()
    {
        $applications = RentalApplication::with(['property', 'tenant', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $csvData = [];
        $csvData[] = ['رقم الطلب', 'العقار', 'المتقدم', 'البريد الإلكتروني', 'الهاتف', 'الحالة', 'الأولوية', 'تاريخ الإدخال'];

        foreach ($applications as $application) {
            $csvData[] = [
                $application->application_number,
                $application->property->title,
                $application->applicant_name,
                $application->applicant_email,
                $application->applicant_phone,
                $application->status,
                $application->priority,
                $application->created_at->format('Y-m-d'),
            ];
        }

        $filename = 'rental_applications_' . date('Y-m-d') . '.csv';
        
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
        $pendingApplications = RentalApplication::pending()->count();
        $reviewingApplications = RentalApplication::where('status', 'reviewing')->count();
        $approvedApplications = RentalApplication::approved()->count();
        $rejectedApplications = RentalApplication::rejected()->count();

        $highPriorityApplications = RentalApplication::where('priority', 'high')
            ->whereIn('status', ['pending', 'reviewing'])
            ->count();

        $recentApplications = RentalApplication::with(['property'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $applicationsByProperty = RentalApplication::selectRaw('property_id, COUNT(*) as count')
            ->with(['property'])
            ->groupBy('property_id')
            ->get();

        return view('rentals.applications.dashboard', compact(
            'pendingApplications',
            'reviewingApplications',
            'approvedApplications',
            'rejectedApplications',
            'highPriorityApplications',
            'recentApplications',
            'applicationsByProperty'
        ));
    }
}
