<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\SecurityDeposit;
use Illuminate\Http\Request;

class SecurityDepositController extends Controller
{
    public function index()
    {
        $deposits = SecurityDeposit::with(['lease', 'tenant', 'lease.property'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('rentals.deposits.index', compact('deposits'));
    }

    public function create()
    {
        $leases = Lease::where('status', 'active')->get();
        return view('rentals.deposits.create', compact('leases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lease_id' => 'required|exists:leases,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        $deposit = SecurityDeposit::create([
            'lease_id' => $request->lease_id,
            'tenant_id' => Lease::find($request->lease_id)->tenant_id,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'deposit_number' => 'DEP-' . date('Y') . '-' . str_pad(SecurityDeposit::count() + 1, 6, '0', STR_PAD_LEFT),
            'status' => 'pending',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('rentals.deposits.show', $deposit)
            ->with('success', 'تم إنشاء الوديعة بنجاح');
    }

    public function show(SecurityDeposit $deposit)
    {
        $deposit->load(['lease', 'tenant', 'lease.property']);
        return view('rentals.deposits.show', compact('deposit'));
    }

    public function edit(SecurityDeposit $deposit)
    {
        if ($deposit->status === 'refunded') {
            return back()->with('error', 'لا يمكن تعديل وديعة مستردة');
        }
        
        return view('rentals.deposits.edit', compact('deposit'));
    }

    public function update(Request $request, SecurityDeposit $deposit)
    {
        if ($deposit->status === 'refunded') {
            return back()->with('error', 'لا يمكن تعديل وديعة مستردة');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        $deposit->update($request->only(['amount', 'due_date']));

        return redirect()->route('rentals.deposits.show', $deposit)
            ->with('success', 'تم تحديث الوديعة بنجاح');
    }

    public function receive(Request $request, SecurityDeposit $deposit)
    {
        if ($deposit->status !== 'pending') {
            return back()->with('error', 'لا يمكن استلام هذه الوديعة');
        }

        $request->validate([
            'received_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'receipt_number' => 'nullable|string',
        ]);

        $deposit->update([
            'status' => 'received',
            'received_amount' => $request->received_amount,
            'received_date' => now(),
            'payment_method' => $request->payment_method,
            'receipt_number' => $request->receipt_number ?: 'REC-' . date('Y') . '-' . str_pad(SecurityDeposit::count() + 1, 6, '0', STR_PAD_LEFT),
        ]);

        return redirect()->route('rentals.deposits.show', $deposit)
            ->with('success', 'تم استلام الوديعة بنجاح');
    }

    public function refund(Request $request, SecurityDeposit $deposit)
    {
        if ($deposit->status !== 'received') {
            return back()->with('error', 'لا يمكن استرداد وديعة غير مستلمة');
        }

        $request->validate([
            'refund_amount' => 'required|numeric|min:0|max:' . $deposit->received_amount,
            'refund_method' => 'required|string',
            'bank_account' => 'required|string',
            'deductions' => 'nullable|numeric|min:0',
            'deduction_reasons' => 'nullable|array',
        ]);

        $deposit->update([
            'status' => $request->refund_amount < $deposit->received_amount ? 'partially_refunded' : 'refunded',
            'refund_amount' => $request->refund_amount,
            'deductions' => $request->deductions ?? 0,
            'deduction_reasons' => $request->deduction_reasons ?? [],
            'refund_date' => now(),
            'refund_method' => $request->refund_method,
            'bank_account' => $request->bank_account,
            'refund_receipt_number' => 'REF-' . date('Y') . '-' . str_pad(SecurityDeposit::count() + 1, 6, '0', STR_PAD_LEFT),
        ]);

        return redirect()->route('rentals.deposits.show', $deposit)
            ->with('success', 'تم استرداد الوديعة بنجاح');
    }

    public function receipt(SecurityDeposit $deposit)
    {
        if ($deposit->status === 'pending') {
            return back()->with('error', 'لا يمكن طباعة إيصال لوديعة غير مستلمة');
        }

        return view('rentals.deposits.receipt', compact('deposit'));
    }

    public function refundReceipt(SecurityDeposit $deposit)
    {
        if (!in_array($deposit->status, ['refunded', 'partially_refunded'])) {
            return back()->with('error', 'لا يمكن طباعة إيصال استرداد لوديعة غير مستردة');
        }

        return view('rentals.deposits.refund-receipt', compact('deposit'));
    }

    public function destroy(SecurityDeposit $deposit)
    {
        if ($deposit->status === 'refunded') {
            return back()->with('error', 'لا يمكن حذف وديعة مستردة');
        }

        $deposit->delete();

        return redirect()->route('rentals.deposits.index')
            ->with('success', 'تم حذف الوديعة بنجاح');
    }

    public function export()
    {
        $deposits = SecurityDeposit::with(['lease', 'tenant', 'lease.property'])
            ->orderBy('created_at', 'desc')
            ->get();

        $csvData = [];
        $csvData[] = ['رقم الوديعة', 'العقد', 'المستأجر', 'العقار', 'المبلغ', 'المستلم', 'المسترد', 'الحالة', 'تاريخ الاستحقاق'];

        foreach ($deposits as $deposit) {
            $csvData[] = [
                $deposit->deposit_number,
                $deposit->lease->lease_number,
                $deposit->tenant->name,
                $deposit->lease->property->title,
                $deposit->amount,
                $deposit->received_amount,
                $deposit->refund_amount,
                $deposit->status,
                $deposit->due_date->format('Y-m-d'),
            ];
        }

        $filename = 'security_deposits_' . date('Y-m-d') . '.csv';
        
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
        $totalDeposits = SecurityDeposit::sum('amount');
        $totalReceived = SecurityDeposit::where('status', 'received')->sum('received_amount');
        $totalRefunded = SecurityDeposit::where('status', 'refunded')->sum('refund_amount');
        $pendingDeposits = SecurityDeposit::where('status', 'pending')->count();

        $overdueDeposits = SecurityDeposit::with(['lease', 'tenant'])
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $recentDeposits = SecurityDeposit::with(['lease', 'tenant'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('rentals.deposits.dashboard', compact(
            'totalDeposits',
            'totalReceived',
            'totalRefunded',
            'pendingDeposits',
            'overdueDeposits',
            'recentDeposits'
        ));
    }
}
