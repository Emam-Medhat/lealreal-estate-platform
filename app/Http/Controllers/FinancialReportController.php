<?php

namespace App\Http\Controllers;

use App\Models\FinancialReport;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    public function index()
    {
        $reports = FinancialReport::with(['user'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
            
        return view('reports.financial.index', compact('reports'));
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        // Revenue metrics
        $totalRevenue = $this->calculateTotalRevenue($user);
        $monthlyRevenue = $this->calculateMonthlyRevenue($user);
        $quarterlyRevenue = $this->calculateQuarterlyRevenue($user);
        $yearlyRevenue = $this->calculateYearlyRevenue($user);
        
        // Expense metrics
        $totalExpenses = $this->calculateTotalExpenses($user);
        $monthlyExpenses = $this->calculateMonthlyExpenses($user);
        
        // Profit metrics
        $netProfit = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
        
        // Cash flow
        $cashFlow = $this->calculateCashFlow($user);
        $receivables = $this->calculateReceivables($user);
        $payables = $this->calculatePayables($user);
        
        // Growth metrics
        $revenueGrowth = $this->calculateRevenueGrowth($user);
        $profitGrowth = $this->calculateProfitGrowth($user);
        
        // Revenue breakdown
        $revenueBySource = $this->getRevenueBySource($user);
        $revenueByMonth = $this->getRevenueByMonth($user);
        $revenueByProperty = $this->getRevenueByProperty($user);
        
        return view('reports.financial.dashboard', compact(
            'totalRevenue', 'monthlyRevenue', 'quarterlyRevenue', 'yearlyRevenue',
            'totalExpenses', 'monthlyExpenses',
            'netProfit', 'profitMargin',
            'cashFlow', 'receivables', 'payables',
            'revenueGrowth', 'profitGrowth',
            'revenueBySource', 'revenueByMonth', 'revenueByProperty'
        ));
    }

    public function create()
    {
        return view('reports.financial.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'report_type' => 'required|in:income_statement,balance_sheet,cash_flow,profit_loss,revenue_analysis',
            'date_range' => 'required|array',
            'date_range.start' => 'required|date',
            'date_range.end' => 'required|date|after_or_equal:date_range.start',
            'include_charts' => 'nullable|boolean',
            'include_details' => 'nullable|boolean',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        $report = Report::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'user_id' => Auth::id(),
            'template_id' => 3, // Financial report template
            'parameters' => [
                'report_type' => $validated['report_type'],
                'date_range' => $validated['date_range'],
                'include_charts' => $validated['include_charts'] ?? true,
                'include_details' => $validated['include_details'] ?? true
            ],
            'format' => $validated['format'],
            'status' => 'pending'
        ]);

        return redirect()->route('reports.financial.show', $report->id)
            ->with('success', 'تم إنشاء التقرير المالي بنجاح');
    }

    public function show(Report $report)
    {
        $this->authorize('view', $report);
        
        if ($report->template_id !== 3) {
            return back()->with('error', 'هذا ليس تقرير مالي');
        }
        
        $financialData = $this->getFinancialReportData($report);
        
        return view('reports.financial.show', compact('report', 'financialData'));
    }

    public function incomeStatement(Request $request)
    {
        $user = Auth::user();
        $dateRange = $this->getDateRangeFromRequest($request);
        
        $revenue = $this->getRevenueDetails($user, $dateRange);
        $expenses = $this->getExpenseDetails($user, $dateRange);
        $netIncome = $revenue['total'] - $expenses['total'];
        
        return view('reports.financial.income-statement', compact(
            'revenue', 'expenses', 'netIncome', 'dateRange'
        ));
    }

    public function balanceSheet(Request $request)
    {
        $user = Auth::user();
        $asOf = $request->get('as_of', now()->format('Y-m-d'));
        
        $assets = $this->getAssets($user, $asOf);
        $liabilities = $this->getLiabilities($user, $asOf);
        $equity = $this->getEquity($user, $asOf);
        
        $totalAssets = $assets['total'];
        $totalLiabilities = $liabilities['total'];
        $totalEquity = $equity['total'];
        
        return view('reports.financial.balance-sheet', compact(
            'assets', 'liabilities', 'equity',
            'totalAssets', 'totalLiabilities', 'totalEquity', 'asOf'
        ));
    }

    public function cashFlow(Request $request)
    {
        $user = Auth::user();
        $dateRange = $this->getDateRangeFromRequest($request);
        
        $operatingCashFlow = $this->getOperatingCashFlow($user, $dateRange);
        $investingCashFlow = $this->getInvestingCashFlow($user, $dateRange);
        $financingCashFlow = $this->getFinancingCashFlow($user, $dateRange);
        
        $netCashFlow = $operatingCashFlow + $investingCashFlow + $financingCashFlow;
        
        return view('reports.financial.cash-flow', compact(
            'operatingCashFlow', 'investingCashFlow', 'financingCashFlow',
            'netCashFlow', 'dateRange'
        ));
    }

    public function analytics()
    {
        $user = Auth::user();
        
        // Financial ratios
        $ratios = [
            'profit_margin' => $this->calculateProfitMargin($user),
            'return_on_assets' => $this->calculateReturnOnAssets($user),
            'debt_to_equity' => $this->calculateDebtToEquity($user),
            'current_ratio' => $this->calculateCurrentRatio($user),
            'quick_ratio' => $this->calculateQuickRatio($user)
        ];
        
        // Trends
        $trends = [
            'revenue_trend' => $this->getRevenueTrend($user),
            'profit_trend' => $this->getProfitTrend($user),
            'expense_trend' => $this->getExpenseTrend($user)
        ];
        
        // Forecasts
        $forecasts = [
            'next_month_revenue' => $this->forecastRevenue($user, 1),
            'next_quarter_revenue' => $this->forecastRevenue($user, 3),
            'next_year_revenue' => $this->forecastRevenue($user, 12),
            'profit_forecast' => $this->forecastProfit($user)
        ];
        
        return view('reports.financial.analytics', compact(
            'ratios', 'trends', 'forecasts'
        ));
    }

    private function getFinancialReportData(Report $report)
    {
        $parameters = $report->parameters ?? [];
        $reportType = $parameters['report_type'] ?? 'income_statement';
        $dateRange = $parameters['date_range'] ?? [];
        
        $data = [
            'report_type' => $reportType,
            'date_range' => $dateRange,
            'generated_at' => now()
        ];
        
        switch ($reportType) {
            case 'income_statement':
                $data = array_merge($data, $this->generateIncomeStatementData($dateRange));
                break;
            case 'balance_sheet':
                $data = array_merge($data, $this->generateBalanceSheetData($dateRange));
                break;
            case 'cash_flow':
                $data = array_merge($data, $this->generateCashFlowData($dateRange));
                break;
            case 'profit_loss':
                $data = array_merge($data, $this->generateProfitLossData($dateRange));
                break;
            case 'revenue_analysis':
                $data = array_merge($data, $this->generateRevenueAnalysisData($dateRange));
                break;
        }
        
        return $data;
    }

    private function generateIncomeStatementData($dateRange)
    {
        $user = Auth::user();
        
        $revenue = $this->getRevenueDetails($user, $dateRange);
        $expenses = $this->getExpenseDetails($user, $dateRange);
        
        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'gross_profit' => $revenue['total'] - ($expenses['cost_of_goods_sold'] ?? 0),
            'operating_income' => $revenue['total'] - ($expenses['operating_expenses'] ?? 0),
            'net_income' => $revenue['total'] - $expenses['total']
        ];
    }

    private function generateBalanceSheetData($dateRange)
    {
        $user = Auth::user();
        $asOf = $dateRange['end'] ?? now();
        
        return [
            'assets' => $this->getAssets($user, $asOf),
            'liabilities' => $this->getLiabilities($user, $asOf),
            'equity' => $this->getEquity($user, $asOf)
        ];
    }

    private function generateCashFlowData($dateRange)
    {
        $user = Auth::user();
        
        return [
            'operating_cash_flow' => $this->getOperatingCashFlow($user, $dateRange),
            'investing_cash_flow' => $this->getInvestingCashFlow($user, $dateRange),
            'financing_cash_flow' => $this->getFinancingCashFlow($user, $dateRange)
        ];
    }

    private function generateProfitLossData($dateRange)
    {
        $user = Auth::user();
        
        return [
            'revenue_breakdown' => $this->getRevenueBreakdown($user, $dateRange),
            'expense_breakdown' => $this->getExpenseBreakdown($user, $dateRange),
            'profit_analysis' => $this->getProfitAnalysis($user, $dateRange)
        ];
    }

    private function generateRevenueAnalysisData($dateRange)
    {
        $user = Auth::user();
        
        return [
            'revenue_streams' => $this->getRevenueStreams($user, $dateRange),
            'customer_analysis' => $this->getCustomerAnalysis($user, $dateRange),
            'product_analysis' => $this->getProductAnalysis($user, $dateRange)
        ];
    }

    private function calculateTotalRevenue($user)
    {
        return DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->sum('transactions.amount');
    }

    private function calculateMonthlyRevenue($user)
    {
        return DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.created_at', now()->month)
            ->whereYear('transactions.created_at', now()->year)
            ->sum('transactions.amount');
    }

    private function calculateQuarterlyRevenue($user)
    {
        return DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->where('transactions.created_at', '>=', now()->startOfQuarter())
            ->sum('transactions.amount');
    }

    private function calculateYearlyRevenue($user)
    {
        return DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->whereYear('transactions.created_at', now()->year)
            ->sum('transactions.amount');
    }

    private function calculateTotalExpenses($user)
    {
        // Implementation for calculating total expenses
        return 0;
    }

    private function calculateMonthlyExpenses($user)
    {
        // Implementation for calculating monthly expenses
        return 0;
    }

    private function calculateCashFlow($user)
    {
        // Implementation for calculating cash flow
        return 0;
    }

    private function calculateReceivables($user)
    {
        // Implementation for calculating receivables
        return 0;
    }

    private function calculatePayables($user)
    {
        // Implementation for calculating payables
        return 0;
    }

    private function calculateRevenueGrowth($user)
    {
        $currentMonth = $this->calculateMonthlyRevenue($user);
        $lastMonth = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.created_at', now()->subMonth()->month)
            ->whereYear('transactions.created_at', now()->subMonth()->year)
            ->sum('transactions.amount');
            
        return $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;
    }

    private function calculateProfitGrowth($user)
    {
        // Implementation for calculating profit growth
        return 0;
    }

    private function getRevenueBySource($user)
    {
        return DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->selectRaw('properties.type as source, COUNT(*) as count, SUM(transactions.amount) as total')
            ->groupBy('properties.type')
            ->get();
    }

    private function getRevenueByMonth($user)
    {
        return DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->where('transactions.created_at', '>=', now()->subYear())
            ->selectRaw('YEAR(transactions.created_at) as year, MONTH(transactions.created_at) as month, SUM(transactions.amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    private function getRevenueByProperty($user)
    {
        return DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed')
            ->select('properties.title', 'properties.location', 'transactions.amount')
            ->orderBy('transactions.amount', 'desc')
            ->limit(10)
            ->get();
    }

    private function getDateRangeFromRequest($request)
    {
        return [
            'start' => $request->get('start_date', now()->subMonth()->format('Y-m-d')),
            'end' => $request->get('end_date', now()->format('Y-m-d'))
        ];
    }

    private function getRevenueDetails($user, $dateRange)
    {
        $query = DB::table('transactions')
            ->join('properties', 'transactions.property_id', '=', 'properties.id')
            ->where('properties.user_id', $user->id)
            ->where('transactions.status', 'completed');
            
        if (isset($dateRange['start'])) {
            $query->where('transactions.created_at', '>=', $dateRange['start']);
        }
        
        if (isset($dateRange['end'])) {
            $query->where('transactions.created_at', '<=', $dateRange['end']);
        }
        
        $total = $query->sum('transactions.amount');
        
        return [
            'total' => $total,
            'details' => $query->select(
                'properties.title',
                'properties.type',
                'transactions.amount',
                'transactions.created_at'
            )->get()
        ];
    }

    private function getExpenseDetails($user, $dateRange)
    {
        // Implementation for expense details
        return [
            'total' => 0,
            'cost_of_goods_sold' => 0,
            'operating_expenses' => 0,
            'details' => collect([])
        ];
    }

    private function getAssets($user, $asOf)
    {
        // Implementation for assets calculation
        return [
            'current_assets' => 0,
            'fixed_assets' => 0,
            'total' => 0
        ];
    }

    private function getLiabilities($user, $asOf)
    {
        // Implementation for liabilities calculation
        return [
            'current_liabilities' => 0,
            'long_term_liabilities' => 0,
            'total' => 0
        ];
    }

    private function getEquity($user, $asOf)
    {
        // Implementation for equity calculation
        return [
            'share_capital' => 0,
            'retained_earnings' => 0,
            'total' => 0
        ];
    }

    private function getOperatingCashFlow($user, $dateRange)
    {
        // Implementation for operating cash flow
        return 0;
    }

    private function getInvestingCashFlow($user, $dateRange)
    {
        // Implementation for investing cash flow
        return 0;
    }

    private function getFinancingCashFlow($user, $dateRange)
    {
        // Implementation for financing cash flow
        return 0;
    }

    private function calculateProfitMargin($user)
    {
        $revenue = $this->calculateTotalRevenue($user);
        $expenses = $this->calculateTotalExpenses($user);
        
        return $revenue > 0 ? (($revenue - $expenses) / $revenue) * 100 : 0;
    }

    private function calculateReturnOnAssets($user)
    {
        // Implementation for ROA calculation
        return 0;
    }

    private function calculateDebtToEquity($user)
    {
        // Implementation for debt-to-equity ratio
        return 0;
    }

    private function calculateCurrentRatio($user)
    {
        // Implementation for current ratio
        return 0;
    }

    private function calculateQuickRatio($user)
    {
        // Implementation for quick ratio
        return 0;
    }

    private function getRevenueTrend($user)
    {
        // Implementation for revenue trend
        return [];
    }

    private function getProfitTrend($user)
    {
        // Implementation for profit trend
        return [];
    }

    private function getExpenseTrend($user)
    {
        // Implementation for expense trend
        return [];
    }

    private function forecastRevenue($user, $months)
    {
        // Implementation for revenue forecasting
        return 0;
    }

    private function forecastProfit($user)
    {
        // Implementation for profit forecasting
        return 0;
    }

    private function getRevenueBreakdown($user, $dateRange)
    {
        // Implementation for revenue breakdown
        return [];
    }

    private function getExpenseBreakdown($user, $dateRange)
    {
        // Implementation for expense breakdown
        return [];
    }

    private function getProfitAnalysis($user, $dateRange)
    {
        // Implementation for profit analysis
        return [];
    }

    private function getRevenueStreams($user, $dateRange)
    {
        // Implementation for revenue streams
        return [];
    }

    private function getCustomerAnalysis($user, $dateRange)
    {
        // Implementation for customer analysis
        return [];
    }

    private function getProductAnalysis($user, $dateRange)
    {
        // Implementation for product analysis
        return [];
    }
}
