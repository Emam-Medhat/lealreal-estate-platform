<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\RentPayment;
use App\Models\SecurityDeposit;
use App\Models\LeaseRenewal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use PDF;

class LeaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Lease::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('lease_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('tenant', function($subQ) use ($request) {
                      $subQ->where('name', 'like', '%' . $request->search . '%');
                  })
                  ->orWhereHas('property', function($subQ) use ($request) {
                      $subQ->where('title', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        $leases = $query->with(['tenant', 'property', 'rentPayments'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('rentals.leases.index', compact('leases'));
    }

    public function create()
    {
        $properties = Property::where('is_rental', true)
            ->where('status', 'vacant')
            ->get();
        
        $tenants = Tenant::where('status', 'active')->get();

        return view('rentals.leases.create', compact('properties', 'tenants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'tenant_id' => 'required|exists:tenants,id',
            'lease_number' => 'required|string|unique:leases,lease_number',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'rent_amount' => 'required|numeric|min:0',
            'security_deposit' => 'required|numeric|min:0',
            'rent_frequency' => 'required|string|in:monthly,quarterly,annually',
            'payment_due_day' => 'required|integer|min:1|max:31',
            'late_fee' => 'nullable|numeric|min:0',
            'late_fee_type' => 'nullable|string|in:fixed,percentage',
            'terms_and_conditions' => 'required|string',
            'special_terms' => 'nullable|string',
            'utilities_included' => 'nullable|array',
            'amenities_included' => 'nullable|array',
            'maintenance_responsibility' => 'required|string',
            'renewal_option' => 'boolean',
            'renewal_terms' => 'nullable|string',
            'termination_notice_days' => 'required|integer|min:1',
            'documents' => 'nullable|array',
        ]);

        $validated['status'] = 'active';
        $validated['user_id'] = auth()->id();

        $lease = Lease::create($validated);

        // Update property status
        $lease->property->update(['status' => 'occupied']);

        // Create security deposit record
        if ($validated['security_deposit'] > 0) {
            $lease->securityDeposits()->create([
                'amount' => $validated['security_deposit'],
                'status' => 'pending',
                'due_date' => $validated['start_date'],
                'user_id' => auth()->id(),
            ]);
        }

        // Generate rent payment schedule
        $this->generateRentSchedule($lease);

        // Upload documents if any
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('lease_documents', 'public');
                $lease->documents()->create([
                    'file_name' => $document->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $document->getClientMimeType(),
                    'user_id' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('rentals.leases.show', $lease)
            ->with('success', 'تم إنشاء عقد الإيجار بنجاح');
    }

    public function show(Lease $lease)
    {
        $lease->load([
            'tenant',
            'property',
            'rentPayments',
            'securityDeposits',
            'renewals',
            'documents',
            'violations'
        ]);

        $stats = [
            'total_paid' => $lease->rentPayments()->where('status', 'paid')->sum('amount'),
            'total_pending' => $lease->rentPayments()->where('status', 'pending')->sum('amount'),
            'total_overdue' => $lease->rentPayments()->where('status', 'overdue')->sum('amount'),
            'security_deposit_received' => $lease->securityDeposits()->where('status', 'received')->sum('amount'),
            'days_remaining' => Carbon::now()->diffInDays($lease->end_date, false),
        ];

        return view('rentals.leases.show', compact('lease', 'stats'));
    }

    public function edit(Lease $lease)
    {
        $lease->load('documents');
        $properties = Property::where('is_rental', true)->get();
        $tenants = Tenant::where('status', 'active')->get();

        return view('rentals.leases.edit', compact('lease', 'properties', 'tenants'));
    }

    public function update(Request $request, Lease $lease)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'tenant_id' => 'required|exists:tenants,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'rent_amount' => 'required|numeric|min:0',
            'security_deposit' => 'required|numeric|min:0',
            'rent_frequency' => 'required|string|in:monthly,quarterly,annually',
            'payment_due_day' => 'required|integer|min:1|max:31',
            'late_fee' => 'nullable|numeric|min:0',
            'late_fee_type' => 'nullable|string|in:fixed,percentage',
            'terms_and_conditions' => 'required|string',
            'special_terms' => 'nullable|string',
            'utilities_included' => 'nullable|array',
            'amenities_included' => 'nullable|array',
            'maintenance_responsibility' => 'required|string',
            'renewal_option' => 'boolean',
            'renewal_terms' => 'nullable|string',
            'termination_notice_days' => 'required|integer|min:1',
        ]);

        $lease->update($validated);

        return redirect()->route('rentals.leases.show', $lease)
            ->with('success', 'تم تحديث عقد الإيجار بنجاح');
    }

    public function destroy(Lease $lease)
    {
        // Update property status to vacant
        $lease->property->update(['status' => 'vacant']);

        $lease->delete();

        return redirect()->route('rentals.leases.index')
            ->with('success', 'تم حذف عقد الإيجار بنجاح');
    }

    public function activate(Lease $lease)
    {
        $lease->update([
            'status' => 'active',
            'activated_at' => Carbon::now(),
            'activated_by' => auth()->id(),
        ]);

        $lease->property->update(['status' => 'occupied']);

        return redirect()->back()->with('success', 'تم تفعيل العقد بنجاح');
    }

    public function terminate(Lease $lease)
    {
        $validated = request()->validate([
            'termination_reason' => 'required|string',
            'termination_date' => 'required|date|after_or_equal:today',
            'termination_notes' => 'nullable|string',
        ]);

        $lease->update([
            'status' => 'terminated',
            'termination_reason' => $validated['termination_reason'],
            'termination_date' => $validated['termination_date'],
            'termination_notes' => $validated['termination_notes'],
            'terminated_at' => Carbon::now(),
            'terminated_by' => auth()->id(),
        ]);

        $lease->property->update(['status' => 'vacant']);

        return redirect()->back()->with('success', 'تم إنهاء العقد بنجاح');
    }

    public function suspend(Lease $lease)
    {
        $validated = request()->validate([
            'suspension_reason' => 'required|string',
            'suspension_notes' => 'nullable|string',
        ]);

        $lease->update([
            'status' => 'suspended',
            'suspension_reason' => $validated['suspension_reason'],
            'suspension_notes' => $validated['suspension_notes'],
            'suspended_at' => Carbon::now(),
            'suspended_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'تم تعليق العقد بنجاح');
    }

    public function resume(Lease $lease)
    {
        $lease->update([
            'status' => 'active',
            'resumed_at' => Carbon::now(),
            'resumed_by' => auth()->id(),
        ]);

        $lease->property->update(['status' => 'occupied']);

        return redirect()->back()->with('success', 'تم استئناف العقد بنجاح');
    }

    public function download(Lease $lease)
    {
        $lease->load(['tenant', 'property']);
        
        $pdf = PDF::loadView('rentals.leases.pdf', compact('lease'));
        
        return $pdf->download('lease_' . $lease->lease_number . '.pdf');
    }

    public function sendReminder(Lease $lease)
    {
        $validated = request()->validate([
            'reminder_type' => 'required|string|in:payment,renewal,termination',
            'message' => 'required|string',
        ]);

        // Send reminder logic here
        // This would typically send an email or SMS

        $lease->reminders()->create([
            'type' => $validated['reminder_type'],
            'message' => $validated['message'],
            'sent_at' => Carbon::now(),
            'sent_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'تم إرسال التذكير بنجاح');
    }

    public function payments(Lease $lease)
    {
        $payments = $lease->rentPayments()
            ->with(['lease.tenant', 'lease.property'])
            ->orderBy('due_date', 'desc')
            ->paginate(20);

        return view('rentals.leases.payments', compact('lease', 'payments'));
    }

    public function export(Request $request)
    {
        $leases = Lease::with(['tenant', 'property'])->get();

        $csvData = [];
        $csvData[] = ['رقم العقد', 'المستأجر', 'العقار', 'تاريخ البدء', 'تاريخ الانتهاء', 'الإيجار', 'الحالة'];

        foreach ($leases as $lease) {
            $csvData[] = [
                $lease->lease_number,
                $lease->tenant->name,
                $lease->property->title,
                $lease->start_date,
                $lease->end_date,
                $lease->rent_amount,
                $lease->status,
            ];
        }

        $filename = 'leases_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    private function generateRentSchedule(Lease $lease)
    {
        $startDate = Carbon::parse($lease->start_date);
        $endDate = Carbon::parse($lease->end_date);
        $dueDay = $lease->payment_due_day;

        $currentDate = $startDate->copy()->day($dueDay);
        if ($currentDate < $startDate) {
            $currentDate->addMonth();
        }

        while ($currentDate <= $endDate) {
            $lease->rentPayments()->create([
                'due_date' => $currentDate,
                'amount' => $lease->rent_amount,
                'status' => 'pending',
                'user_id' => auth()->id(),
            ]);

            $currentDate->addMonth();
        }
    }
}
