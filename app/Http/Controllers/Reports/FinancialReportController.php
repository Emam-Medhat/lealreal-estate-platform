<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\FinancialReport;
use App\Models\Report;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialReportController extends Controller
{
    protected $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index()
    {
        // For now, listing reports is fine, but we need to ensure the view exists
        $reports = Report::where('type', 'financial')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
            
        return view('reports.financial.index', compact('reports'));
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        // REAL Data from FinancialService
        $totalRevenue = $this->financialService->getTotalRevenue($user->id);
        $revenueByProperty = $this->financialService->getRevenueByProperty($user->id);
        $revenueByCompany = $this->financialService->getRevenueByCompany($user->id);
        $monthlyRevenue = $this->financialService->getMonthlyRevenue($user->id);
        
        // Placeholders for now (to be implemented in Service later)
        $totalExpenses = 0; 
        $netProfit = $totalRevenue - $totalExpenses;
        
        return view('reports.financial.dashboard', compact(
            'totalRevenue', 
            'revenueByProperty', 
            'revenueByCompany',
            'monthlyRevenue',
            'totalExpenses',
            'netProfit'
        ));
    }

    public function create()
    {
        return view('reports.financial.create');
    }

    public function store(Request $request)
    {
        // Implementation for creating a saved report
        // ... (Keep existing logic or simplify)
        return redirect()->route('reports.financial.index');
    }

    public function show(Report $report)
    {
        return view('reports.financial.show', compact('report'));
    }

    public function incomeStatement()
    {
        return view('reports.placeholder', [
            'title' => 'Income Statement',
            'backRoute' => route('reports.financial.index'),
            'backLabel' => 'Return to Financial Reports'
        ]);
    }

    public function balanceSheet()
    {
        return view('reports.placeholder', [
            'title' => 'Balance Sheet',
            'backRoute' => route('reports.financial.index'),
            'backLabel' => 'Return to Financial Reports'
        ]);
    }

    public function cashFlow()
    {
        return view('reports.placeholder', [
            'title' => 'Cash Flow',
            'backRoute' => route('reports.financial.index'),
            'backLabel' => 'Return to Financial Reports'
        ]);
    }

    public function analytics(Report $report)
    {
        return view('reports.placeholder', [
            'title' => 'Financial Analytics: ' . $report->title,
            'backRoute' => route('reports.financial.show', $report),
            'backLabel' => 'Return to Report'
        ]);
    }
}
