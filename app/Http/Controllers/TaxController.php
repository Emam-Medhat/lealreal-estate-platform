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
     * Show the form for creating a new tax.
     */
    public function create()
    {
        return view('taxes.create');
    }

    /**
     * Store a newly created tax in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'tax_type' => 'required|string|in:property,income,capital_gains,vat,stamp_duty,municipality,other',
            'rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string|max:1000',
            'effective_date' => 'required|date',
            'is_active' => 'required|boolean',
        ]);

        try {
            $tax = Tax::create([
                'name' => $request->name,
                'tax_type' => $request->tax_type,
                'rate' => $request->rate,
                'description' => $request->description,
                'effective_date' => $request->effective_date,
                'is_active' => $request->is_active,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('taxes.index')
                ->with('success', 'تم إضافة الضريبة بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة الضريبة: ' . $e->getMessage());
        }
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
        
        $recentTaxes = Tax::with(['creator'])
            ->latest()
            ->limit(10)
            ->get();

        $upcomingDeadlines = PropertyTax::where('due_date', '>', now())
            ->where('due_date', '<=', now()->addDays(30))
            ->with(['property', 'owner'])
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        // Calculate tax counts for dashboard
        $paidTaxes = PropertyTax::where('status', 'paid')->count();
        $pendingTaxes = PropertyTax::where('status', 'pending')->count();
        $overdueTaxes = PropertyTax::where('status', 'overdue')->count();

        // Create recent activities from recent taxes for display
        $recentActivities = $recentTaxes->map(function($tax) {
            return (object) [
                'title' => $tax->name,
                'description' => 'ضريبة جديدة: ' . $tax->tax_type,
                'created_at' => $tax->created_at
            ];
        });

        return view('taxes.index', compact(
            'totalTaxes',
            'totalPropertyTaxes', 
            'totalPayments',
            'pendingPayments',
            'recentTaxes',
            'upcomingDeadlines',
            'paidTaxes',
            'pendingTaxes',
            'overdueTaxes',
            'recentActivities'
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
        $query = Tax::with(['creator']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('name_ar', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
                  ->orWhere('description_ar', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->tax_type) {
            $query->where('tax_type', $request->tax_type);
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
                return Tax::with(['creator'])
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->get();
                
            case 'payments':
                return TaxPayment::with(['propertyTax.property', 'propertyTax.creator'])
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
        $taxes = Tax::with(['creator'])
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
