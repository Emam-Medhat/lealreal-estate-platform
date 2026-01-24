<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\RentPayment;
use App\Models\SecurityDeposit;
use Illuminate\Http\Request;

class RentCollectionController extends Controller
{
    public function dashboard()
    {
        $activeLeases = Lease::where('status', 'active')->count();
        $totalMonthlyRent = Lease::where('status', 'active')->sum('rent_amount');
        
        $thisMonthPayments = RentPayment::whereMonth('payment_date', now()->month)
            ->where('status', 'paid')
            ->sum('paid_amount');
            
        $overduePayments = RentPayment::where('status', 'overdue')->count();
        
        $pendingDeposits = SecurityDeposit::where('status', 'pending')->count();
        
        $upcomingPayments = RentPayment::with(['lease', 'lease.tenant'])
            ->where('status', 'pending')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->orderBy('due_date')
            ->take(10)
            ->get();
            
        $overdueList = RentPayment::with(['lease', 'lease.tenant'])
            ->where('status', 'overdue')
            ->orderBy('due_date')
            ->take(10)
            ->get();

        return view('rentals.collection.dashboard', compact(
            'activeLeases',
            'totalMonthlyRent',
            'thisMonthPayments',
            'overduePayments',
            'pendingDeposits',
            'upcomingPayments',
            'overdueList'
        ));
    }

    public function payments()
    {
        $payments = RentPayment::with(['lease', 'lease.tenant', 'lease.property'])
            ->orderBy('due_date', 'desc')
            ->paginate(20);
            
        return view('rentals.collection.payments', compact('payments'));
    }

    public function overdue()
    {
        $overduePayments = RentPayment::with(['lease', 'lease.tenant', 'lease.property'])
            ->where('status', 'overdue')
            ->orderBy('due_date')
            ->paginate(20);
            
        return view('rentals.collection.overdue', compact('overduePayments'));
    }

    public function processBulkPayments(Request $request)
    {
        $request->validate([
            'payments' => 'required|array',
            'payments.*' => 'exists:rent_payments,id',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
        ]);

        $processedCount = 0;
        
        foreach ($request->payments as $paymentId) {
            $payment = RentPayment::find($paymentId);
            
            if ($payment && $payment->status !== 'paid') {
                $payment->update([
                    'status' => 'paid',
                    'paid_amount' => $payment->amount + $payment->late_fee,
                    'payment_date' => $request->payment_date,
                    'payment_method' => $request->payment_method,
                    'receipt_number' => 'REC-' . date('Y') . '-' . str_pad(RentPayment::count() + 1, 6, '0', STR_PAD_LEFT),
                ]);
                $processedCount++;
            }
        }

        return back()->with('success', "تم معالجة {$processedCount} دفعة بنجاح");
    }

    public function sendReminders()
    {
        $upcomingPayments = RentPayment::with(['lease', 'lease.tenant'])
            ->where('status', 'pending')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->where('reminder_sent', false)
            ->get();

        $sentCount = 0;
        
        foreach ($upcomingPayments as $payment) {
            // Send reminder logic here (email, SMS, etc.)
            $payment->update(['reminder_sent' => true]);
            $sentCount++;
        }

        return back()->with('success', "تم إرسال {$sentCount} تذكير بنجاح");
    }

    public function applyLateFees()
    {
        $overduePayments = RentPayment::where('status', 'overdue')
            ->where('late_fee_applied', false)
            ->get();

        $appliedCount = 0;
        
        foreach ($overduePayments as $payment) {
            $lateFee = $payment->amount * 0.05; // 5% late fee
            
            $payment->update([
                'late_fee' => $lateFee,
                'late_fee_applied' => true,
            ]);
            $appliedCount++;
        }

        return back()->with('success', "تم تطبيق رسوم التأخير على {$appliedCount} دفعة");
    }

    public function generateReports()
    {
        $monthlyIncome = RentPayment::whereMonth('payment_date', now()->month)
            ->where('status', 'paid')
            ->sum('paid_amount');
            
        $yearlyIncome = RentPayment::whereYear('payment_date', now()->year)
            ->where('status', 'paid')
            ->sum('paid_amount');
            
        $collectionRate = $this->calculateCollectionRate();
        
        $averagePaymentDelay = $this->calculateAverageDelay();

        return view('rentals.collection.reports', compact(
            'monthlyIncome',
            'yearlyIncome',
            'collectionRate',
            'averagePaymentDelay'
        ));
    }

    private function calculateCollectionRate()
    {
        $totalDue = RentPayment::whereMonth('due_date', now()->month)->sum('amount');
        $totalPaid = RentPayment::whereMonth('payment_date', now()->month)
            ->where('status', 'paid')
            ->sum('paid_amount');
            
        return $totalDue > 0 ? ($totalPaid / $totalDue) * 100 : 0;
    }

    private function calculateAverageDelay()
    {
        $paidPayments = RentPayment::where('status', 'paid')
            ->whereNotNull('payment_date')
            ->get();
            
        if ($paidPayments->isEmpty()) {
            return 0;
        }
        
        $totalDelay = $paidPayments->sum(function($payment) {
            return $payment->payment_date->diffInDays($payment->due_date);
        });
        
        return $totalDelay / $paidPayments->count();
    }

    public function exportPayments(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth();
        $endDate = $request->end_date ?? now()->endOfMonth();
        
        $payments = RentPayment::with(['lease', 'lease.tenant', 'lease.property'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->orderBy('payment_date')
            ->get();

        $csvData = [];
        $csvData[] = ['تاريخ الدفع', 'رقم الدفعة', 'العقد', 'المستأجر', 'العقار', 'المبلغ', 'الرسوم', 'الإجمالي', 'طريقة الدفع'];

        foreach ($payments as $payment) {
            $csvData[] = [
                $payment->payment_date->format('Y-m-d'),
                $payment->payment_number,
                $payment->lease->lease_number,
                $payment->lease->tenant->name,
                $payment->lease->property->title,
                $payment->amount,
                $payment->late_fee,
                $payment->paid_amount,
                $payment->payment_method,
            ];
        }

        $filename = 'rent_payments_export_' . date('Y-m-d') . '.csv';
        
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

    public function analytics()
    {
        $monthlyTrends = $this->getMonthlyTrends();
        $paymentMethods = $this->getPaymentMethodsDistribution();
        $propertyPerformance = $this->getPropertyPerformance();
        $tenantPaymentHistory = $this->getTenantPaymentHistory();

        return view('rentals.collection.analytics', compact(
            'monthlyTrends',
            'paymentMethods',
            'propertyPerformance',
            'tenantPaymentHistory'
        ));
    }

    private function getMonthlyTrends()
    {
        $trends = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $paid = RentPayment::whereMonth('payment_date', $month)
                ->whereYear('payment_date', $month)
                ->where('status', 'paid')
                ->sum('paid_amount');
                
            $due = RentPayment::whereMonth('due_date', $month)
                ->whereYear('due_date', $month)
                ->sum('amount');
                
            $trends[] = [
                'month' => $month->format('M Y'),
                'paid' => $paid,
                'due' => $due,
                'rate' => $due > 0 ? ($paid / $due) * 100 : 0,
            ];
        }
        
        return $trends;
    }

    private function getPaymentMethodsDistribution()
    {
        return RentPayment::where('status', 'paid')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(paid_amount) as total')
            ->groupBy('payment_method')
            ->get();
    }

    private function getPropertyPerformance()
    {
        return Lease::with(['property', 'rentPayments'])
            ->where('status', 'active')
            ->get()
            ->map(function($lease) {
                $totalPaid = $lease->rentPayments->where('status', 'paid')->sum('paid_amount');
                $totalDue = $lease->rentPayments->sum('amount');
                
                return [
                    'property' => $lease->property->title,
                    'total_paid' => $totalPaid,
                    'total_due' => $totalDue,
                    'collection_rate' => $totalDue > 0 ? ($totalPaid / $totalDue) * 100 : 0,
                ];
            });
    }

    private function getTenantPaymentHistory()
    {
        return Lease::with(['tenant', 'rentPayments'])
            ->where('status', 'active')
            ->get()
            ->map(function($lease) {
                $latePayments = $lease->rentPayments->where('status', 'overdue')->count();
                $onTimePayments = $lease->rentPayments->where('status', 'paid')->count();
                
                return [
                    'tenant' => $lease->tenant->name,
                    'on_time_payments' => $onTimePayments,
                    'late_payments' => $latePayments,
                    'payment_score' => $this->calculatePaymentScore($onTimePayments, $latePayments),
                ];
            });
    }

    private function calculatePaymentScore($onTime, $late)
    {
        $total = $onTime + $late;
        return $total > 0 ? ($onTime / $total) * 100 : 0;
    }
}
