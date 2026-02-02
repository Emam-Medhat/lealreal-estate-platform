<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Models\PropertyTax;
use App\Models\TaxPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaxController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    /**
     * Display tax dashboard with overview.
     */
    public function index(Request $request)
    {
        $totalTaxes = Tax::count();
        $totalPropertyTaxes = PropertyTax::count();
        $totalPayments = TaxPayment::sum('amount');
        $pendingPayments = TaxPayment::where('status', 'pending')->sum('amount');
        
        $recentTaxes = Tax::with(['property', 'owner'])
            ->latest()
            ->limit(10)
            ->get();

        $upcomingDeadlines = PropertyTax::where('due_date', '>', now())
            ->where('due_date', '<=', now()->addDays(30))
            ->with(['property', 'owner'])
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        return view('taxes.index', compact(
            'totalTaxes',
            'totalPropertyTaxes', 
            'totalPayments',
            'pendingPayments',
            'recentTaxes',
            'upcomingDeadlines'
        ));
    }

    /**
     * Show tax analytics and reports.
     */
    public function analytics(Request $request)
    {
        $year = $request->year ?? Carbon::now()->year;
        
        $monthlyTaxes = Tax::whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $taxByType = Tax::join('tax_rates', 'taxes.tax_rate_id', '=', 'tax_rates.id')
            ->selectRaw('tax_rates.name as type, SUM(taxes.amount) as total, COUNT(*) as count')
            ->groupBy('tax_rates.name')
            ->get();

        $paymentsByStatus = TaxPayment::selectRaw('status, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $topProperties = PropertyTax::with(['property'])
            ->selectRaw('property_id, SUM(amount) as total')
            ->groupBy('property_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('taxes.analytics', compact(
            'year',
            'monthlyTaxes',
            'taxByType',
            'paymentsByStatus',
            'topProperties'
        ));
    }

    /**
     * Display all taxes with filtering.
     */
    public function list(Request $request)
    {
        $query = Tax::with(['property', 'owner', 'taxRate']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('tax_number', 'like', "%{$request->search}%")
                  ->orWhereHas('property', function ($subQ) use ($request) {
                      $subQ->where('title', 'like', "%{$request->search}%");
                  })
                  ->orWhereHas('owner', function ($subQ) use ($request) {
                      $subQ->where('name', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->tax_type) {
            $query->whereHas('taxRate', function ($q) use ($request) {
                $q->where('name', $request->tax_type);
            });
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $taxes = $query->latest()->paginate(20);

        return view('taxes.list', compact('taxes'));
    }

    /**
     * Show tax details.
     */
    public function show(Tax $tax)
    {
        $tax->load(['property', 'owner', 'taxRate', 'payments', 'documents']);

        return view('taxes.show', compact('tax'));
    }

    /**
     * Generate tax report.
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:summary,detailed,payments',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'required|in:pdf,excel'
        ]);

        $data = $this->getReportData($request->type, $request->date_from, $request->date_to);

        if ($request->format === 'pdf') {
            return $this->generatePdfReport($data, $request->type);
        } else {
            return $this->generateExcelReport($data, $request->type);
        }
    }

    /**
     * Get report data based on type.
     */
    private function getReportData($type, $dateFrom, $dateTo)
    {
        switch ($type) {
            case 'summary':
                return Tax::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->selectRaw('
                        COUNT(*) as total_taxes,
                        SUM(amount) as total_amount,
                        AVG(amount) as average_amount,
                        SUM(CASE WHEN status = "paid" THEN amount ELSE 0 END) as paid_amount,
                        SUM(CASE WHEN status = "pending" THEN amount ELSE 0 END) as pending_amount
                    ')
                    ->first();
                
            case 'detailed':
                return Tax::with(['property', 'owner', 'taxRate'])
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->get();
                
            case 'payments':
                return TaxPayment::with(['tax.property', 'tax.owner'])
                    ->whereBetween('payment_date', [$dateFrom, $dateTo])
                    ->get();
                
            default:
                return collect();
        }
    }

    /**
     * Generate PDF report.
     */
    private function generatePdfReport($data, $type)
    {
        // Implementation would depend on your PDF library
        return response()->json(['message' => 'PDF report generation would be implemented here']);
    }

    /**
     * Generate Excel report.
     */
    private function generateExcelReport($data, $type)
    {
        // Implementation would depend on your Excel library
        return response()->json(['message' => 'Excel report generation would be implemented here']);
    }

    /**
     * Get tax statistics for dashboard.
     */
    public function getStats()
    {
        $stats = [
            'total_taxes' => Tax::count(),
            'total_amount' => Tax::sum('amount'),
            'paid_amount' => Tax::where('status', 'paid')->sum('amount'),
            'pending_amount' => Tax::where('status', 'pending')->sum('amount'),
            'overdue_amount' => Tax::where('status', 'overdue')->sum('amount'),
            'this_month' => Tax::whereMonth('created_at', now()->month)->sum('amount'),
            'last_month' => Tax::whereMonth('created_at', now()->subMonth()->month)->sum('amount'),
        ];

        return response()->json($stats);
    }

    /**
     * Display tax reports page.
     */
    public function reports(Request $request)
    {
        $taxes = Tax::with(['property', 'owner'])
            ->when($request->type, function($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->date_from, function($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest()
            ->paginate(20);

        $totalAmount = Tax::sum('amount');
        $paidAmount = Tax::where('status', 'paid')->sum('amount');
        $pendingAmount = Tax::where('status', 'pending')->sum('amount');
        $overdueAmount = Tax::where('status', 'overdue')->sum('amount');

        return view('taxes.reports', compact(
            'taxes',
            'totalAmount',
            'paidAmount',
            'pendingAmount',
            'overdueAmount'
        ));
    }
}
