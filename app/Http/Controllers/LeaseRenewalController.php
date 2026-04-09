<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\LeaseRenewal;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\StoreLeaseRenewalRequest;

class LeaseRenewalController extends Controller
{
    public function index()
    {
        $renewals = LeaseRenewal::with(['lease', 'lease.tenant', 'lease.property'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('rentals.renewals.index', compact('renewals'));
    }

    public function create(Lease $lease)
    {
        return view('rentals.renewals.create', compact('lease'));
    }

    public function store(StoreLeaseRenewalRequest $request)
    {
        $renewal = LeaseRenewal::create([
            'lease_id' => $request->lease_id,
            'renewal_number' => 'LRN-' . date('Y') . '-' . str_pad(LeaseRenewal::count() + 1, 6, '0', STR_PAD_LEFT),
            'old_end_date' => $request->old_end_date,
            'new_end_date' => $request->new_end_date,
            'old_rent_amount' => $request->old_rent_amount,
            'new_rent_amount' => $request->new_rent_amount,
            'renewal_type' => $request->renewal_type,
            'renewal_terms' => $request->renewal_terms,
            'notes' => $request->notes,
            'requested_at' => now(),
            'requested_by' => auth()->id(),
            'effective_date' => $request->effective_date,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('rentals.renewals.show', $renewal)
            ->with('success', 'تم إنشاء طلب التجديد بنجاح');
    }

    public function show(LeaseRenewal $renewal)
    {
        $renewal->load(['lease', 'lease.tenant', 'lease.property', 'requestedBy', 'approvedBy', 'rejectedBy']);
        
        return view('rentals.renewals.show', compact('renewal'));
    }

    public function edit(LeaseRenewal $renewal)
    {
        if ($renewal->status !== 'pending') {
            return back()->with('error', 'لا يمكن تعديل طلب التجديد');
        }
        
        return view('rentals.renewals.edit', compact('renewal'));
    }

    public function update(StoreLeaseRenewalRequest $request, LeaseRenewal $renewal)
    {
        if ($renewal->status !== 'pending') {
            return back()->with('error', 'لا يمكن تعديل طلب التجديد');
        }

        $renewal->update($request->validated());

        return redirect()->route('rentals.renewals.show', $renewal)
            ->with('success', 'تم تحديث طلب التجديد بنجاح');
    }

    public function approve(LeaseRenewal $renewal)
    {
        if ($renewal->status !== 'pending') {
            return back()->with('error', 'لا يمكن الموافقة على هذا الطلب');
        }

        $renewal->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        // Update lease end date and rent amount
        $lease = $renewal->lease;
        $lease->update([
            'end_date' => $renewal->new_end_date,
            'rent_amount' => $renewal->new_rent_amount,
        ]);

        return redirect()->route('rentals.renewals.show', $renewal)
            ->with('success', 'تم الموافقة على طلب التجديد بنجاح');
    }

    public function reject(Request $request, LeaseRenewal $renewal)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($renewal->status !== 'pending') {
            return back()->with('error', 'لا يمكن رفض هذا الطلب');
        }

        $renewal->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('rentals.renewals.show', $renewal)
            ->with('success', 'تم رفض طلب التجديد بنجاح');
    }

    public function complete(LeaseRenewal $renewal)
    {
        if ($renewal->status !== 'approved') {
            return back()->with('error', 'لا يمكن إكمال هذا الطلب');
        }

        $renewal->update([
            'status' => 'completed',
        ]);

        return redirect()->route('rentals.renewals.show', $renewal)
            ->with('success', 'تم إكمال التجديد بنجاح');
    }

    public function destroy(LeaseRenewal $renewal)
    {
        if ($renewal->status !== 'pending') {
            return back()->with('error', 'لا يمكن حذف طلب التجديد');
        }

        $renewal->delete();

        return redirect()->route('rentals.renewals.index')
            ->with('success', 'تم حذف طلب التجديد بنجاح');
    }

    public function export()
    {
        $renewals = LeaseRenewal::with(['lease', 'lease.tenant', 'lease.property'])
            ->orderBy('created_at', 'desc')
            ->get();

        $csvData = [];
        $csvData[] = ['رقم التجديد', 'العقد', 'المستأجر', 'العقار', 'نوع التجديد', 'الإيجار القديم', 'الإيجار الجديد', 'الحالة', 'تاريخ الطلب'];

        foreach ($renewals as $renewal) {
            $csvData[] = [
                $renewal->renewal_number,
                $renewal->lease->lease_number,
                $renewal->lease->tenant->name,
                $renewal->lease->property->title,
                $renewal->renewal_type,
                $renewal->old_rent_amount,
                $renewal->new_rent_amount,
                $renewal->status,
                $renewal->created_at->format('Y-m-d'),
            ];
        }

        $filename = 'lease_renewals_' . date('Y-m-d') . '.csv';
        
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
        $pendingRenewals = LeaseRenewal::pending()->count();
        $approvedRenewals = LeaseRenewal::approved()->count();
        $completedRenewals = LeaseRenewal::completed()->count();
        $rejectedRenewals = LeaseRenewal::rejected()->count();

        $recentRenewals = LeaseRenewal::with(['lease', 'lease.tenant'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $upcomingRenewals = LeaseRenewal::with(['lease', 'lease.tenant'])
            ->where('status', 'approved')
            ->where('effective_date', '>=', now())
            ->where('effective_date', '<=', now()->addDays(30))
            ->orderBy('effective_date')
            ->get();

        return view('rentals.renewals.dashboard', compact(
            'pendingRenewals',
            'approvedRenewals', 
            'completedRenewals',
            'rejectedRenewals',
            'recentRenewals',
            'upcomingRenewals'
        ));
    }
}
