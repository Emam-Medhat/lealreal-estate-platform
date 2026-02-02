<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Models\PropertyTax;
use App\Models\TaxPayment;
use App\Models\TaxFiling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaxReportController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index()
    {
        $taxes = Tax::with(['creator'])
            ->when(request()->type, function($query, $type) {
                $query->where('type', $type);
            })
            ->when(request()->is_active, function($query, $isActive) {
                $query->where('is_active', $isActive);
            })
            ->when(request()->date_from, function($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when(request()->date_to, function($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest()
            ->paginate(20);

        $totalTaxes = Tax::count();
        $activeTaxes = Tax::where('is_active', true)->count();
        $inactiveTaxes = Tax::where('is_active', false)->count();
        $propertyTaxes = Tax::where('type', 'property')->count();

        return view('taxes.reports', compact(
            'taxes',
            'totalTaxes',
            'activeTaxes',
            'inactiveTaxes',
            'propertyTaxes'
        ));
    }

    public function collectionReport(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfYear();
        $endDate = $request->end_date ?? now()->endOfYear();

        $collections = TaxPayment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->with(['propertyTax.property'])
            ->get();

        $totalCollected = $collections->sum('amount');
        $totalProperties = $collections->pluck('property_tax_id')->unique()->count();

        return view('taxes.reports.collection', compact('collections', 'totalCollected', 'totalProperties', 'startDate', 'endDate'));
    }

    public function outstandingReport(Request $request)
    {
        $outstanding = PropertyTax::where('status', 'pending')
            ->with(['property'])
            ->get();

        $totalOutstanding = $outstanding->sum('tax_amount');
        $overdueCount = $outstanding->where('tax_year', '<', now()->year)->count();

        return view('taxes.reports.outstanding', compact('outstanding', 'totalOutstanding', 'overdueCount'));
    }

    public function exemptionReport(Request $request)
    {
        $exemptions = TaxExemption::with(['propertyTax.property', 'user'])
            ->latest()
            ->get();

        $totalExemptions = $exemptions->where('status', 'approved')->sum('approved_amount');
        $pendingCount = $exemptions->where('status', 'pending')->count();

        return view('taxes.reports.exemptions', compact('exemptions', 'totalExemptions', 'pendingCount'));
    }

    public function analytics()
    {
        $monthlyCollections = TaxPayment::where('status', 'completed')
            ->whereYear('payment_date', now()->year)
            ->selectRaw('MONTH(payment_date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $topProperties = Property::withSum('propertyTaxes', 'tax_amount')
            ->orderByDesc('property_taxes_sum_tax_amount')
            ->limit(10)
            ->get();

        return view('taxes.reports.analytics', compact('monthlyCollections', 'topProperties'));
    }

    public function export(Request $request)
    {
        $type = $request->type;
        
        switch ($type) {
            case 'collection':
                return $this->exportCollectionReport($request);
            case 'outstanding':
                return $this->exportOutstandingReport($request);
            case 'exemptions':
                return $this->exportExemptionReport($request);
            default:
                return back()->with('error', 'نوع التقرير غير صالح');
        }
    }

    private function exportCollectionReport($request)
    {
        // Export logic for collection report
        return response()->download('collection-report.xlsx');
    }

    private function exportOutstandingReport($request)
    {
        // Export logic for outstanding report
        return response()->download('outstanding-report.xlsx');
    }

    private function exportExemptionReport($request)
    {
        // Export logic for exemption report
        return response()->download('exemption-report.xlsx');
    }
}
