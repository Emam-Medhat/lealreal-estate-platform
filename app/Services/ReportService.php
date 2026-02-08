<?php

namespace App\Services;

use App\Repositories\InvoiceRepositoryInterface;
use App\Repositories\PaymentRepositoryInterface;
use App\Repositories\PropertyRepositoryInterface;
use App\Repositories\ClientRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportService
{
    protected InvoiceRepositoryInterface $invoiceRepository;
    protected PaymentRepositoryInterface $paymentRepository;
    protected PropertyRepositoryInterface $propertyRepository;
    protected ClientRepositoryInterface $clientRepository;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        PaymentRepositoryInterface $paymentRepository,
        PropertyRepositoryInterface $propertyRepository,
        ClientRepositoryInterface $clientRepository
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->paymentRepository = $paymentRepository;
        $this->propertyRepository = $propertyRepository;
        $this->clientRepository = $clientRepository;
    }

    /**
     * Generate comprehensive financial report
     */
    public function generateFinancialReport(array $filters = []): array
    {
        $cacheKey = 'financial_report_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, 300, function () use ($filters) {
            return [
                'revenue' => $this->getRevenueReport($filters),
                'payments' => $this->getPaymentsReport($filters),
                'outstanding' => $this->getOutstandingReport($filters),
                'aging' => $this->getAgingReport($filters),
                'summary' => $this->getFinancialSummary($filters),
                'generated_at' => now(),
            ];
        });
    }

    /**
     * Generate property performance report
     */
    public function generatePropertyPerformanceReport(int $propertyId = null): array
    {
        $cacheKey = 'property_performance_' . ($propertyId ?? 'all');
        
        return Cache::remember($cacheKey, 600, function () use ($propertyId) {
            if ($propertyId) {
                return $this->getSinglePropertyPerformance($propertyId);
            }
            
            return $this->getAllPropertiesPerformance();
        });
    }

    /**
     * Generate agent performance report
     */
    public function generateAgentPerformanceReport(int $agentId = null): array
    {
        $cacheKey = 'agent_performance_' . ($agentId ?? 'all');
        
        return Cache::remember($cacheKey, 600, function () use ($agentId) {
            if ($agentId) {
                return $this->getSingleAgentPerformance($agentId);
            }
            
            return $this->getAllAgentsPerformance();
        });
    }

    /**
     * Generate client analytics report
     */
    public function generateClientAnalyticsReport(array $filters = []): array
    {
        $cacheKey = 'client_analytics_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, 300, function () use ($filters) {
            return [
                'acquisition' => $this->getClientAcquisitionReport($filters),
                'retention' => $this->getClientRetentionReport($filters),
                'segmentation' => $this->getClientSegmentationReport($filters),
                'lifetime_value' => $this->getClientLifetimeValueReport($filters),
                'top_clients' => $this->getTopClientsReport($filters),
                'generated_at' => now(),
            ];
        });
    }

    /**
     * Generate monthly cash flow report
     */
    public function generateCashFlowReport(int $year = null): array
    {
        $year = $year ?? date('Y');
        $cacheKey = "cash_flow_{$year}";
        
        return Cache::remember($cacheKey, 3600, function () use ($year) {
            return [
                'monthly_cash_flow' => $this->getMonthlyCashFlow($year),
                'quarterly_cash_flow' => $this->getQuarterlyCashFlow($year),
                'yearly_summary' => $this->getYearlyCashFlowSummary($year),
                'projections' => $this->getCashFlowProjections($year),
                'generated_at' => now(),
            ];
        });
    }

    /**
     * Generate sold vs available properties report
     */
    public function generatePropertyInventoryReport(): array
    {
        return Cache::remember('property_inventory_report', 900, function () {
            return [
                'inventory_summary' => $this->getInventorySummary(),
                'sold_properties' => $this->getSoldPropertiesReport(),
                'available_properties' => $this->getAvailablePropertiesReport(),
                'time_to_sell' => $this->getTimeToSellAnalysis(),
                'price_trends' => $this->getPriceTrendsAnalysis(),
                'generated_at' => now(),
            ];
        });
    }

    // Private Methods for Financial Reports
    private function getRevenueReport(array $filters): array
    {
        $revenueByMonth = $this->invoiceRepository->getRevenueByMonth(date('Y'));
        $revenueByProperty = [];
        $revenueByCompany = [];
        $revenueByType = [];

        // Get revenue by property
        $properties = $this->propertyRepository->all();
        foreach ($properties as $property) {
            $revenueByProperty[$property->id] = $this->invoiceRepository->getRevenueByProperty($property->id);
        }

        // Get revenue by company
        $companies = $this->propertyRepository->getAllCompanies();
        foreach ($companies as $company) {
            $revenueByCompany[$company->id] = $this->invoiceRepository->getRevenueByCompany($company->id);
        }

        // Get revenue by invoice type
        $revenueByType = DB::table('invoices')
            ->select('type', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as revenue'))
            ->where('status', 'paid')
            ->when(isset($filters['date_from']), function ($query) use ($filters) {
                $query->whereDate('paid_date', '>=', $filters['date_from']);
            })
            ->when(isset($filters['date_to']), function ($query) use ($filters) {
                $query->whereDate('paid_date', '<=', $filters['date_to']);
            })
            ->groupBy('type')
            ->get();

        return [
            'monthly' => $revenueByMonth,
            'by_property' => $revenueByProperty,
            'by_company' => $revenueByCompany,
            'by_type' => $revenueByType,
            'total' => array_sum($revenueByMonth),
        ];
    }

    private function getPaymentsReport(array $filters): array
    {
        $payments = DB::table('payments')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->where('status', 'completed')
            ->when(isset($filters['date_from']), function ($query) use ($filters) {
                $query->whereDate('paid_at', '>=', $filters['date_from']);
            })
            ->when(isset($filters['date_to']), function ($query) use ($filters) {
                $query->whereDate('paid_at', '<=', $filters['date_to']);
            })
            ->groupBy('payment_method')
            ->get();

        $paymentTrends = DB::table('payments')
            ->select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(amount) as daily_total'))
            ->where('status', 'completed')
            ->where('paid_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'by_method' => $payments,
            'trends' => $paymentTrends,
            'total_amount' => $payments->sum('total'),
            'total_count' => $payments->sum('count'),
        ];
    }

    private function getOutstandingReport(array $filters): array
    {
        $outstanding = $this->invoiceRepository->getOutstandingAmount($filters);
        $overdueInvoices = $this->invoiceRepository->getOverdueInvoicesList($filters);

        $overdueByAge = [
            '0_30_days' => 0,
            '31_60_days' => 0,
            '61_90_days' => 0,
            '90_plus_days' => 0,
        ];

        foreach ($overdueInvoices as $invoice) {
            $daysOverdue = abs($invoice->getDaysUntilDueAttribute());
            $amount = $invoice->getOutstandingAmountAttribute();

            if ($daysOverdue <= 30) {
                $overdueByAge['0_30_days'] += $amount;
            } elseif ($daysOverdue <= 60) {
                $overdueByAge['31_60_days'] += $amount;
            } elseif ($daysOverdue <= 90) {
                $overdueByAge['61_90_days'] += $amount;
            } else {
                $overdueByAge['90_plus_days'] += $amount;
            }
        }

        return [
            'total_outstanding' => $outstanding,
            'overdue_amount' => $overdueInvoices->sum(function ($invoice) {
                return $invoice->getOutstandingAmountAttribute();
            }),
            'overdue_count' => $overdueInvoices->count(),
            'overdue_by_age' => $overdueByAge,
        ];
    }

    private function getAgingReport(array $filters): array
    {
        return $this->invoiceService->getOverdueInvoicesWithAging();
    }

    private function getFinancialSummary(array $filters): array
    {
        $stats = $this->invoiceRepository->getInvoiceStats($filters);
        
        return [
            'total_invoices' => $stats['total'],
            'paid_invoices' => $stats['paid'],
            'pending_invoices' => $stats['pending'],
            'overdue_invoices' => $stats['overdue'],
            'total_revenue' => $stats['revenue'],
            'outstanding_amount' => $stats['outstanding'],
            'conversion_rate' => $stats['total'] > 0 ? ($stats['paid'] / $stats['total']) * 100 : 0,
            'average_invoice_value' => $stats['paid'] > 0 ? $stats['revenue'] / $stats['paid'] : 0,
        ];
    }

    // Private Methods for Property Performance
    private function getSinglePropertyPerformance(int $propertyId): array
    {
        $property = $this->propertyRepository->findById($propertyId);
        $revenueData = $this->invoiceRepository->getRevenueByProperty($propertyId);
        
        return [
            'property' => $property,
            'revenue' => $revenueData,
            'performance_metrics' => $this->calculatePropertyMetrics($propertyId),
            'generated_at' => now(),
        ];
    }

    private function getAllPropertiesPerformance(): array
    {
        $properties = $this->propertyRepository->all();
        $performanceData = [];

        foreach ($properties as $property) {
            $performanceData[$property->id] = [
                'property' => $property,
                'revenue' => $this->invoiceRepository->getRevenueByProperty($property->id),
                'metrics' => $this->calculatePropertyMetrics($property->id),
            ];
        }

        return [
            'properties' => $performanceData,
            'summary' => $this->summarizePropertiesPerformance($performanceData),
            'generated_at' => now(),
        ];
    }

    private function calculatePropertyMetrics(int $propertyId): array
    {
        $invoices = $this->invoiceRepository->getInvoicesByProperty($propertyId);
        $totalRevenue = $invoices->where('status', 'paid')->sum('total');
        $totalInvoices = $invoices->count();
        $paidInvoices = $invoices->where('status', 'paid')->count();

        return [
            'total_revenue' => $totalRevenue,
            'total_invoices' => $totalInvoices,
            'paid_invoices' => $paidInvoices,
            'conversion_rate' => $totalInvoices > 0 ? ($paidInvoices / $totalInvoices) * 100 : 0,
            'average_invoice_value' => $paidInvoices > 0 ? $totalRevenue / $paidInvoices : 0,
        ];
    }

    private function summarizePropertiesPerformance(array $performanceData): array
    {
        $totalRevenue = 0;
        $totalInvoices = 0;
        $totalPaidInvoices = 0;

        foreach ($performanceData as $data) {
            $totalRevenue += $data['revenue']['paid_revenue'];
            $totalInvoices += $data['metrics']['total_invoices'];
            $totalPaidInvoices += $data['metrics']['paid_invoices'];
        }

        return [
            'total_properties' => count($performanceData),
            'total_revenue' => $totalRevenue,
            'total_invoices' => $totalInvoices,
            'total_paid_invoices' => $totalPaidInvoices,
            'overall_conversion_rate' => $totalInvoices > 0 ? ($totalPaidInvoices / $totalInvoices) * 100 : 0,
            'average_revenue_per_property' => count($performanceData) > 0 ? $totalRevenue / count($performanceData) : 0,
        ];
    }

    // Private Methods for Cash Flow
    private function getMonthlyCashFlow(int $year): array
    {
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($year, $month, 1);
            $monthEnd = $monthStart->copy()->endOfMonth();
            
            $inflow = DB::table('payments')
                ->where('status', 'completed')
                ->whereYear('paid_at', $year)
                ->whereMonth('paid_at', $month)
                ->sum('amount');
                
            $outflow = DB::table('expenses')
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->sum('amount');
                
            $monthlyData[$month] = [
                'month' => $monthStart->format('F Y'),
                'inflow' => $inflow,
                'outflow' => $outflow,
                'net_flow' => $inflow - $outflow,
            ];
        }
        
        return $monthlyData;
    }

    private function getQuarterlyCashFlow(int $year): array
    {
        $quarterlyData = [];
        
        for ($quarter = 1; $quarter <= 4; $quarter++) {
            $quarterStart = Carbon::create($year, (($quarter - 1) * 3) + 1, 1);
            $quarterEnd = $quarterStart->copy()->addMonths(2)->endOfMonth();
            
            $inflow = DB::table('payments')
                ->where('status', 'completed')
                ->whereBetween('paid_at', [$quarterStart, $quarterEnd])
                ->sum('amount');
                
            $outflow = DB::table('expenses')
                ->whereBetween('date', [$quarterStart, $quarterEnd])
                ->sum('amount');
                
            $quarterlyData[$quarter] = [
                'quarter' => "Q{$quarter} {$year}",
                'inflow' => $inflow,
                'outflow' => $outflow,
                'net_flow' => $inflow - $outflow,
            ];
        }
        
        return $quarterlyData;
    }

    private function getYearlyCashFlowSummary(int $year): array
    {
        $totalInflow = DB::table('payments')
            ->where('status', 'completed')
            ->whereYear('paid_at', $year)
            ->sum('amount');
            
        $totalOutflow = DB::table('expenses')
            ->whereYear('date', $year)
            ->sum('amount');
            
        return [
            'year' => $year,
            'total_inflow' => $totalInflow,
            'total_outflow' => $totalOutflow,
            'net_cash_flow' => $totalInflow - $totalOutflow,
            'average_monthly_flow' => ($totalInflow - $totalOutflow) / 12,
        ];
    }

    private function getCashFlowProjections(int $year): array
    {
        // Simple projection based on historical data
        $lastYearData = $this->getMonthlyCashFlow($year - 1);
        $currentYearData = $this->getMonthlyCashFlow($year);
        
        $projections = [];
        $currentMonth = now()->month;
        
        for ($month = $currentMonth + 1; $month <= 12; $month++) {
            // Use last year's data as projection
            $projectedInflow = $lastYearData[$month]['inflow'] * 1.1; // 10% growth assumption
            $projectedOutflow = $lastYearData[$month]['outflow'] * 1.05; // 5% growth assumption
            
            $projections[$month] = [
                'month' => Carbon::create($year, $month, 1)->format('F Y'),
                'projected_inflow' => $projectedInflow,
                'projected_outflow' => $projectedOutflow,
                'projected_net_flow' => $projectedInflow - $projectedOutflow,
            ];
        }
        
        return $projections;
    }

    // Additional private methods for other report types...
    // (Agent Performance, Client Analytics, Property Inventory, etc.)
}
