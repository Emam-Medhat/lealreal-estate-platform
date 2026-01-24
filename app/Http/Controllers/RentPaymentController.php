<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\RentPayment;
use App\Http\Requests\ProcessRentPaymentRequest;
use Illuminate\Http\Request;

class RentPaymentController extends Controller
{
    public function index()
    {
        $payments = RentPayment::with(['lease', 'lease.tenant', 'lease.property'])
            ->orderBy('due_date', 'desc')
            ->paginate(15);
            
        return view('rentals.payments.index', compact('payments'));
    }

    public function create()
    {
        $leases = Lease::where('status', 'active')->get();
        return view('rentals.payments.create', compact('leases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lease_id' => 'required|exists:leases,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        $payment = RentPayment::create([
            'lease_id' => $request->lease_id,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'payment_number' => 'PAY-' . date('Y') . '-' . str_pad(RentPayment::count() + 1, 6, '0', STR_PAD_LEFT),
            'payment_number_sequence' => RentPayment::where('lease_id', $request->lease_id)->count() + 1,
            'status' => 'pending',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('rentals.payments.show', $payment)
            ->with('success', 'تم إنشاء الدفعة بنجاح');
    }

    public function show(RentPayment $payment)
    {
        $payment->load(['lease', 'lease.tenant', 'lease.property']);
        return view('rentals.payments.show', compact('payment'));
    }

    public function edit(RentPayment $payment)
    {
        if ($payment->status === 'paid') {
            return back()->with('error', 'لا يمكن تعديل دفعة مدفوعة');
        }
        
        return view('rentals.payments.edit', compact('payment'));
    }

    public function update(Request $request, RentPayment $payment)
    {
        if ($payment->status === 'paid') {
            return back()->with('error', 'لا يمكن تعديل دفعة مدفوعة');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        $payment->update($request->only(['amount', 'due_date']));

        return redirect()->route('rentals.payments.show', $payment)
            ->with('success', 'تم تحديث الدفعة بنجاح');
    }

    public function processPayment(ProcessRentPaymentRequest $request, RentPayment $payment)
    {
        if ($payment->status === 'paid') {
            return back()->with('error', 'هذه الدفعة مدفوعة بالفعل');
        }

        $payment->update([
            'status' => 'paid',
            'paid_amount' => $request->amount,
            'payment_date' => now(),
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'receipt_number' => 'REC-' . date('Y') . '-' . str_pad(RentPayment::count() + 1, 6, '0', STR_PAD_LEFT),
        ]);

        return redirect()->route('rentals.payments.show', $payment)
            ->with('success', 'تم تسجيل الدفعة بنجاح');
    }

    public function applyLateFee(RentPayment $payment)
    {
        if ($payment->status !== 'overdue' || $payment->late_fee_applied) {
            return back()->with('error', 'لا يمكن تطبيق رسوم التأخير');
        }

        $lateFee = $payment->amount * 0.05; // 5% late fee

        $payment->update([
            'late_fee' => $lateFee,
            'late_fee_applied' => true,
        ]);

        return redirect()->route('rentals.payments.show', $payment)
            ->with('success', 'تم تطبيق رسوم التأخير بنجاح');
    }

    public function sendReminder(RentPayment $payment)
    {
        if ($payment->status === 'paid') {
            return back()->with('error', 'هذه الدفعة مدفوعة بالفعل');
        }

        // Send reminder logic here
        $payment->update(['reminder_sent' => true]);

        return redirect()->route('rentals.payments.show', $payment)
            ->with('success', 'تم إرسال التذكير بنجاح');
    }

    public function receipt(RentPayment $payment)
    {
        if ($payment->status !== 'paid') {
            return back()->with('error', 'لا يمكن طباعة إيصال لدفعة غير مدفوعة');
        }

        return view('rentals.payments.receipt', compact('payment'));
    }

    public function destroy(RentPayment $payment)
    {
        if ($payment->status === 'paid') {
            return back()->with('error', 'لا يمكن حذف دفعة مدفوعة');
        }

        $payment->delete();

        return redirect()->route('rentals.payments.index')
            ->with('success', 'تم حذف الدفعة بنجاح');
    }

    public function generateSchedule(Lease $lease)
    {
        $payments = [];
        $startDate = $lease->start_date;
        $endDate = $lease->end_date;
        $paymentDay = $lease->payment_due_day ?? 1;
        
        $currentDate = $startDate->copy()->day($paymentDay);
        
        while ($currentDate <= $endDate) {
            $payments[] = [
                'due_date' => $currentDate->copy(),
                'amount' => $lease->rent_amount,
            ];
            
            $currentDate->addMonth();
        }

        foreach ($payments as $paymentData) {
            RentPayment::create([
                'lease_id' => $lease->id,
                'amount' => $paymentData['amount'],
                'due_date' => $paymentData['due_date'],
                'payment_number' => 'PAY-' . date('Y') . '-' . str_pad(RentPayment::count() + 1, 6, '0', STR_PAD_LEFT),
                'payment_number_sequence' => RentPayment::where('lease_id', $lease->id)->count() + 1,
                'status' => 'pending',
                'user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('rentals.leases.show', $lease)
            ->with('success', 'تم إنشاء جدول المدفوعات بنجاح');
    }

    public function export()
    {
        $payments = RentPayment::with(['lease', 'lease.tenant', 'lease.property'])
            ->orderBy('due_date', 'desc')
            ->get();

        $csvData = [];
        $csvData[] = ['رقم الدفعة', 'العقد', 'المستأجر', 'العقار', 'تاريخ الاستحقاق', 'المبلغ', 'المدفوع', 'الحالة', 'طريقة الدفع'];

        foreach ($payments as $payment) {
            $csvData[] = [
                $payment->payment_number,
                $payment->lease->lease_number,
                $payment->lease->tenant->name,
                $payment->lease->property->title,
                $payment->due_date->format('Y-m-d'),
                $payment->amount,
                $payment->paid_amount,
                $payment->status,
                $payment->payment_method,
            ];
        }

        $filename = 'rent_payments_' . date('Y-m-d') . '.csv';
        
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
        $totalPaid = RentPayment::where('status', 'paid')->sum('amount');
        $totalPending = RentPayment::where('status', 'pending')->sum('amount');
        $overdueCount = RentPayment::where('status', 'overdue')->count();
        $thisMonthPaid = RentPayment::whereMonth('payment_date', now()->month)
            ->where('status', 'paid')
            ->sum('amount');

        $overduePayments = RentPayment::with(['lease', 'lease.tenant'])
            ->where('status', 'overdue')
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $upcomingPayments = RentPayment::with(['lease', 'lease.tenant'])
            ->where('status', 'pending')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(30))
            ->orderBy('due_date')
            ->take(5)
            ->get();

        return view('rentals.payments.dashboard', compact(
            'totalPaid',
            'totalPending',
            'overdueCount',
            'thisMonthPaid',
            'overduePayments',
            'upcomingPayments'
        ));
    }
}
