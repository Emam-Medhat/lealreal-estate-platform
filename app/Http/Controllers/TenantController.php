<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Lease;
use App\Models\RentalApplication;
use App\Models\TenantScreening;
use App\Models\SecurityDeposit;
use App\Models\RentPayment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('national_id', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('property_id')) {
            $query->whereHas('leases', function($q) use ($request) {
                $q->where('property_id', $request->property_id)
                  ->where('status', 'active');
            });
        }

        $tenants = $query->with(['currentLease.property', 'screenings'])->paginate(15);

        return view('rentals.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('rentals.tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email',
            'phone' => 'required|string|max:20',
            'national_id' => 'required|string|unique:tenants,national_id',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'required|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'employment_status' => 'required|string',
            'employer_name' => 'nullable|string|max:255',
            'monthly_income' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'references' => 'nullable|array',
            'documents' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'active';
        $validated['user_id'] = auth()->id();

        $tenant = Tenant::create($validated);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('tenant_documents', 'public');
                $tenant->documents()->create([
                    'file_name' => $document->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $document->getClientMimeType(),
                    'user_id' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('rentals.tenants.show', $tenant)
            ->with('success', 'تم إضافة المستأجر بنجاح');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load([
            'leases.property',
            'currentLease.property',
            'rentPayments',
            'securityDeposits',
            'screenings',
            'documents',
            'applications.property'
        ]);

        $stats = [
            'total_leases' => $tenant->leases()->count(),
            'active_leases' => $tenant->leases()->where('status', 'active')->count(),
            'total_payments' => $tenant->rentPayments()->where('status', 'paid')->sum('amount'),
            'pending_payments' => $tenant->rentPayments()->where('status', 'pending')->count(),
            'overdue_payments' => $tenant->rentPayments()->where('status', 'overdue')->count(),
            'security_deposits' => $tenant->securityDeposits()->sum('amount'),
        ];

        return view('rentals.tenants.show', compact('tenant', 'stats'));
    }

    public function edit(Tenant $tenant)
    {
        $tenant->load('documents');
        return view('rentals.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'phone' => 'required|string|max:20',
            'national_id' => 'required|string|unique:tenants,national_id,' . $tenant->id,
            'date_of_birth' => 'required|date|before:today',
            'address' => 'required|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'employment_status' => 'required|string',
            'employer_name' => 'nullable|string|max:255',
            'monthly_income' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'references' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $tenant->update($validated);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('tenant_documents', 'public');
                $tenant->documents()->create([
                    'file_name' => $document->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $document->getClientMimeType(),
                    'user_id' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('rentals.tenants.show', $tenant)
            ->with('success', 'تم تحديث المستأجر بنجاح');
    }

    public function destroy(Tenant $tenant)
    {
        if ($tenant->currentLease) {
            return redirect()->back()->with('error', 'لا يمكن حذف مستأجر لديه عقد نشط');
        }

        $tenant->delete();

        return redirect()->route('rentals.tenants.index')
            ->with('success', 'تم حذف المستأجر بنجاح');
    }

    public function screen(Tenant $tenant)
    {
        $screening = $tenant->screenings()->create([
            'status' => 'pending',
            'screening_date' => Carbon::now(),
            'user_id' => auth()->id(),
        ]);

        // Trigger background screening process
        $this->performBackgroundCheck($screening);

        return redirect()->back()->with('success', 'تم بدء فحص المستأجر');
    }

    public function verify(Tenant $tenant)
    {
        $tenant->update([
            'verified' => true,
            'verified_at' => Carbon::now(),
            'verified_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'تم التحقق من المستأجر بنجاح');
    }

    public function blacklist(Tenant $tenant)
    {
        $validated = request()->validate([
            'blacklist_reason' => 'required|string',
            'blacklist_notes' => 'nullable|string',
        ]);

        $tenant->update([
            'blacklisted' => true,
            'blacklist_reason' => $validated['blacklist_reason'],
            'blacklist_notes' => $validated['blacklist_notes'],
            'blacklisted_at' => Carbon::now(),
            'blacklisted_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'تم إضافة المستأجر إلى القائمة السوداء');
    }

    public function history(Tenant $tenant)
    {
        $history = [
            'leases' => $tenant->leases()->with('property')->orderBy('created_at', 'desc')->get(),
            'payments' => $tenant->rentPayments()->orderBy('payment_date', 'desc')->get(),
            'applications' => $tenant->applications()->with('property')->orderBy('created_at', 'desc')->get(),
            'screenings' => $tenant->screenings()->orderBy('created_at', 'desc')->get(),
        ];

        return view('rentals.tenants.history', compact('tenant', 'history'));
    }

    public function documents(Tenant $tenant)
    {
        $documents = $tenant->documents()->orderBy('created_at', 'desc')->get();
        return view('rentals.tenants.documents', compact('tenant', 'documents'));
    }

    public function uploadDocument(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'document' => 'required|file|max:10240',
            'document_type' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $file = $validated['document'];
        $path = $file->store('tenant_documents', 'public');

        $tenant->documents()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'document_type' => $validated['document_type'],
            'description' => $validated['description'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'تم رفع المستند بنجاح');
    }

    public function export(Request $request)
    {
        $tenants = Tenant::with(['currentLease.property'])->get();

        $csvData = [];
        $csvData[] = ['الاسم', 'البريد الإلكتروني', 'الهاتف', 'الرقم الوطني', 'الحالة', 'العقار الحالي', 'تاريخ البدء'];

        foreach ($tenants as $tenant) {
            $csvData[] = [
                $tenant->name,
                $tenant->email,
                $tenant->phone,
                $tenant->national_id,
                $tenant->status,
                $tenant->currentLease?->property?->title ?? '-',
                $tenant->currentLease?->start_date ?? '-',
            ];
        }

        $filename = 'tenants_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    private function performBackgroundCheck(TenantScreening $screening)
    {
        // Simulate background check process
        $screening->update([
            'status' => 'processing',
            'credit_check' => 'good',
            'criminal_check' => 'clear',
            'employment_verification' => 'verified',
            'rental_history' => 'positive',
            'completed_at' => Carbon::now(),
        ]);

        $screening->tenant->update([
            'screening_status' => 'passed',
            'screening_completed_at' => Carbon::now(),
        ]);
    }
}
