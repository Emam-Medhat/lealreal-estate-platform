<?php

namespace App\Http\Controllers;

use App\Models\SalesReport;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    public function index()
    {
        $reports = SalesReport::with(['property', 'transaction'])
            ->whereHas('property', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->latest()
            ->paginate(15);
            
        return view('reports.sales.index', compact('reports'));
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        // Sales metrics
        $totalSales = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->sum('transactions.amount');
            
        $totalTransactions = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->count();
            
        $averageSalePrice = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;
        
        // Monthly sales trend
        $monthlySales = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->where('transactions.created_at', '>=', now()->subYear())
            ->selectRaw('YEAR(transactions.created_at) as year, MONTH(transactions.created_at) as month, SUM(transactions.amount) as total, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
            
        // Top performing properties
        $topProperties = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->select('properties.id', 'properties.title', 'properties.location', 'transactions.amount')
            ->orderBy('transactions.amount', 'desc')
            ->limit(10)
            ->get();
            
        // Sales by property type
        $salesByType = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->selectRaw('properties.type, COUNT(*) as count, SUM(transactions.amount) as total')
            ->groupBy('properties.type')
            ->orderBy('total', 'desc')
            ->get();
            
        return view('reports.sales.dashboard', compact(
            'totalSales', 'totalTransactions', 'averageSalePrice',
            'monthlySales', 'topProperties', 'salesByType'
        ));
    }

    public function create()
    {
        $properties = Auth::user()->properties()
            ->whereHas('transactions', function($query) {
                $query->where('status', 'completed');
            })
            ->get();
            
        return view('reports.sales.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'property_ids' => 'nullable|array',
            'property_ids.*' => 'exists:properties,id',
            'date_range' => 'nullable|array',
            'date_range.start' => 'nullable|date',
            'date_range.end' => 'nullable|date|after_or_equal:date_range.start',
            'include_charts' => 'nullable|boolean',
            'include_details' => 'nullable|boolean',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        $report = Report::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'user_id' => Auth::id(),
            'template_id' => 1, // Sales report template
            'parameters' => [
                'property_ids' => $validated['property_ids'] ?? [],
                'date_range' => $validated['date_range'] ?? [],
                'include_charts' => $validated['include_charts'] ?? true,
                'include_details' => $validated['include_details'] ?? true
            ],
            'format' => $validated['format'],
            'status' => 'pending'
        ]);

        return redirect()->route('reports.sales.show', $report->id)
            ->with('success', 'تم إنشاء تقرير المبيعات بنجاح');
    }

    public function show(Report $report)
    {
        $this->authorize('view', $report);
        
        if ($report->template_id !== 1) {
            return back()->with('error', 'هذا ليس تقرير مبيعات');
        }
        
        $salesData = $this->getSalesReportData($report);
        
        return view('reports.sales.show', compact('report', 'salesData'));
    }

    public function analytics()
    {
        $user = Auth::user();
        
        // Performance metrics
        $metrics = [
            'revenue_growth' => $this->calculateRevenueGrowth($user),
            'transaction_growth' => $this->calculateTransactionGrowth($user),
            'average_price_change' => $this->calculateAveragePriceChange($user),
            'market_share' => $this->calculateMarketShare($user)
        ];
        
        // Predictions
        $predictions = [
            'next_month_revenue' => $this->predictNextMonthRevenue($user),
            'next_quarter_transactions' => $this->predictNextQuarterTransactions($user),
            'price_trend' => $this->predictPriceTrend($user)
        ];
        
        // Comparisons
        $comparisons = [
            'vs_last_month' => $this->compareWithLastMonth($user),
            'vs_last_quarter' => $this->compareWithLastQuarter($user),
            'vs_last_year' => $this->compareWithLastYear($user)
        ];
        
        return view('reports.sales.analytics', compact('metrics', 'predictions', 'comparisons'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:excel,csv,pdf',
            'date_range' => 'nullable|array',
            'date_range.start' => 'nullable|date',
            'date_range.end' => 'nullable|date|after_or_equal:date_range.start',
            'property_ids' => 'nullable|array',
            'property_ids.*' => 'exists:properties,id',
            'include_charts' => 'nullable|boolean'
        ]);

        $data = $this->getSalesDataForExport($validated);
        
        switch ($validated['format']) {
            case 'excel':
                return $this->exportToExcel($data);
            case 'csv':
                return $this->exportToCsv($data);
            case 'pdf':
                return $this->exportToPdf($data);
        }
    }

    private function getSalesReportData(Report $report)
    {
        $parameters = $report->parameters ?? [];
        $dateRange = $parameters['date_range'] ?? [];
        $propertyIds = $parameters['property_ids'] ?? [];
        
        $query = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', Auth::id())
            ->where('transactions.status', 'completed');
            
        if (!empty($propertyIds)) {
            $query->whereIn('properties.id', $propertyIds);
        }
        
        if (isset($dateRange['start'])) {
            $query->where('transactions.created_at', '>=', $dateRange['start']);
        }
        
        if (isset($dateRange['end'])) {
            $query->where('transactions.created_at', '<=', $dateRange['end']);
        }
        
        $transactions = $query->select(
            'transactions.*',
            'properties.title as property_title',
            'properties.location as property_location',
            'properties.type as property_type'
        )->get();
        
        return [
            'transactions' => $transactions,
            'summary' => [
                'total_sales' => $transactions->sum('amount'),
                'total_count' => $transactions->count(),
                'average_price' => $transactions->avg('amount'),
                'highest_sale' => $transactions->max('amount'),
                'lowest_sale' => $transactions->min('amount')
            ],
            'monthly_breakdown' => $this->getMonthlyBreakdown($transactions),
            'property_breakdown' => $this->getPropertyBreakdown($transactions),
            'type_breakdown' => $this->getTypeBreakdown($transactions)
        ];
    }

    private function getMonthlyBreakdown($transactions)
    {
        return $transactions->groupBy(function($transaction) {
            return Carbon::parse($transaction->created_at)->format('Y-m');
        })->map(function($monthTransactions) {
            return [
                'count' => $monthTransactions->count(),
                'total' => $monthTransactions->sum('amount'),
                'average' => $monthTransactions->avg('amount')
            ];
        });
    }

    private function getPropertyBreakdown($transactions)
    {
        return $transactions->groupBy('property_id')->map(function($propertyTransactions) {
            $first = $propertyTransactions->first();
            return [
                'property_title' => $first->property_title,
                'property_location' => $first->property_location,
                'count' => $propertyTransactions->count(),
                'total' => $propertyTransactions->sum('amount'),
                'average' => $propertyTransactions->avg('amount')
            ];
        });
    }

    private function getTypeBreakdown($transactions)
    {
        return $transactions->groupBy('property_type')->map(function($typeTransactions) {
            return [
                'count' => $typeTransactions->count(),
                'total' => $typeTransactions->sum('amount'),
                'average' => $typeTransactions->avg('amount')
            ];
        });
    }

    private function calculateRevenueGrowth($user)
    {
        $currentMonth = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.created_at', now()->month)
            ->whereYear('transactions.created_at', now()->year)
            ->sum('transactions.amount');
            
        $lastMonth = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.created_at', now()->subMonth()->month)
            ->whereYear('transactions.created_at', now()->subMonth()->year)
            ->sum('transactions.amount');
            
        return $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;
    }

    private function calculateTransactionGrowth($user)
    {
        $currentMonth = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.created_at', now()->month)
            ->whereYear('transactions.created_at', now()->year)
            ->count();
            
        $lastMonth = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.created_at', now()->subMonth()->month)
            ->whereYear('transactions.created_at', now()->subMonth()->year)
            ->count();
            
        return $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;
    }

    private function calculateAveragePriceChange($user)
    {
        $currentMonth = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.created_at', now()->month)
            ->whereYear('transactions.created_at', now()->year)
            ->avg('transactions.amount');
            
        $lastMonth = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.created_at', now()->subMonth()->month)
            ->whereYear('transactions.created_at', now()->subMonth()->year)
            ->avg('transactions.amount');
            
        return $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;
    }

    private function calculateMarketShare($user)
    {
        $userSales = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->sum('transactions.amount');
            
        $totalSales = DB::table('transactions')
            ->where('status', 'completed')
            ->sum('transactions.amount');
            
        return $totalSales > 0 ? ($userSales / $totalSales) * 100 : 0;
    }

    private function predictNextMonthRevenue($user)
    {
        // Simple prediction based on last 3 months average
        $lastThreeMonths = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->where('transactions.created_at', '>=', now()->subMonths(3))
            ->selectRaw('MONTH(transactions.created_at) as month, SUM(transactions.amount) as total')
            ->groupBy('month')
            ->pluck('total')
            ->avg();
            
        return $lastThreeMonths ?? 0;
    }

    private function predictNextQuarterTransactions($user)
    {
        // Simple prediction based on last quarter
        $lastQuarter = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->where('transactions.created_at', '>=', now()->subQuarter())
            ->count();
            
        return $lastQuarter;
    }

    private function predictPriceTrend($user)
    {
        $lastMonthAvg = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.created_at', now()->subMonth()->month)
            ->whereYear('transactions.created_at', now()->subMonth()->year)
            ->avg('transactions.amount');
            
        $currentMonthAvg = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.created_at', now()->month)
            ->whereYear('transactions.created_at', now()->year)
            ->avg('transactions.amount');
            
        if ($lastMonthAvg > 0) {
            $change = (($currentMonthAvg - $lastMonthAvg) / $lastMonthAvg) * 100;
            return $change > 5 ? 'increasing' : ($change < -5 ? 'decreasing' : 'stable');
        }
        
        return 'stable';
    }

    private function compareWithLastMonth($user)
    {
        $current = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.created_at', now()->month)
            ->whereYear('transactions.created_at', now()->year);
            
        $last = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.created_at', now()->subMonth()->month)
            ->whereYear('transactions.created_at', now()->subMonth()->year);
            
        return [
            'revenue' => [
                'current' => $current->sum('transactions.amount'),
                'previous' => $last->sum('transactions.amount')
            ],
            'transactions' => [
                'current' => $current->count(),
                'previous' => $last->count()
            ],
            'average_price' => [
                'current' => $current->avg('transactions.amount'),
                'previous' => $last->avg('transactions.amount')
            ]
        ];
    }

    private function compareWithLastQuarter($user)
    {
        // Similar implementation for quarter comparison
        return [];
    }

    private function compareWithLastYear($user)
    {
        // Similar implementation for year comparison
        return [];
    }

    private function getSalesDataForExport($validated)
    {
        $query = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', Auth::id())
            ->where('transactions.status', 'completed')
            ->select(
                'transactions.id',
                'transactions.amount',
                'transactions.created_at',
                'properties.title',
                'properties.location',
                'properties.type'
            );
            
        if (isset($validated['date_range']['start'])) {
            $query->where('transactions.created_at', '>=', $validated['date_range']['start']);
        }
        
        if (isset($validated['date_range']['end'])) {
            $query->where('transactions.created_at', '<=', $validated['date_range']['end']);
        }
        
        if (!empty($validated['property_ids'])) {
            $query->whereIn('properties.id', $validated['property_ids']);
        }
        
        return $query->get();
    }

    private function exportToExcel($data)
    {
        // Implementation for Excel export
        return response()->download('sales_report.xlsx');
    }

    private function exportToCsv($data)
    {
        // Implementation for CSV export
        return response()->download('sales_report.csv');
    }

    private function exportToPdf($data)
    {
        // Implementation for PDF export
        return response()->download('sales_report.pdf');
    }
}
