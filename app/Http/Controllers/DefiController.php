<?php

namespace App\Http\Controllers;

use App\Models\DefiLoan;
use App\Models\CryptoWallet;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DefiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $loans = DefiLoan::with(['borrower', 'lender', 'collateral'])->latest()->paginate(20);
        
        return view('blockchain.defi.index', compact('loans'));
    }

    public function createLoan(Request $request)
    {
        $request->validate([
            'borrower_address' => 'required|string|max:255',
            'lender_address' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'loan_term' => 'required|integer|min:1|max:365',
            'collateral_amount' => 'required|numeric|min:0',
            'collateral_type' => 'required|string|in:eth,btc,usdc,usdt,nft',
            'collateral_address' => 'nullable|string|max:255',
            'protocol' => 'required|string|in:aave,compound,uniswap,curve,balancer',
            'interest_type' => 'required|string|in:fixed,variable',
            'payment_frequency' => 'required|string|in:daily,weekly,monthly,quarterly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|string|in:active,completed,defaulted,liquidated',
            'metadata' => 'nullable|array',
            'created_at' => 'now()',
            'updated_at' => 'now()'
        ]);

        $loan = DefiLoan::create([
            'borrower_address' => $request->borrower_address,
            'lender_address' => $request->lender_address,
            'amount' => $request->amount,
            'interest_rate' => $request->interest_rate,
            'loan_term' => $request->loan_term,
            'collateral_amount' => $request->collateral_amount,
            'collateral_type' => $request->collateral_type,
            'collateral_address' => $request->collateral_address,
            'protocol' => $request->protocol,
            'interest_type' => $request->interest_type,
            'payment_frequency' => $request->payment_frequency,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'metadata' => $request->metadata ?? [],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'loan' => $loan
        ]);
    }

    public function getLoans(Request $request)
    {
        $query = DefiLoan::with(['borrower', 'lender', 'collateral']);
        
        if ($request->borrower_address) {
            $query->where('borrower_address', $request->borrower_address);
        }
        
        if ($request->lender_address) {
            $query->where('lender_address', $request->lender_address);
        }
        
        if ($request->protocol) {
            $query->where('protocol', $request->protocol);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->collateral_type) {
            $query->where('collateral_type', $request->collateral_type);
        }
        
        if ($request->amount_min) {
            $query->where('amount', '>=', $request->amount_min);
        }
        
        if ($request->amount_max) {
            $query->where('amount', '<=', $request->amount_max);
        }
        
        if ($request->interest_rate_min) {
            $query->where('interest_rate', '>=', $request->interest_rate_min);
        }
        
        if ($request->interest_rate_max) {
            $query->where('interest_rate', '<=', $request->interest_rate_max);
        }

        $loans = $query->latest()->paginate(20);
        
        return response()->json($loans);
    }

    public function getLoan(Request $request)
    {
        $loan = DefiLoan::with(['borrower', 'lender', 'collateral'])
            ->where('id', $request->id)
            ->first();
        
        if (!$loan) {
            return response()->json(['error' => 'Loan not found'], 404);
        }

        return response()->json($loan);
    }

    public function updateLoan(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:defi_loans,id',
            'status' => 'nullable|string|in:active,completed,defaulted,liquidated',
            'metadata' => 'nullable|array'
        ]);

        $loan = DefiLoan::findOrFail($request->id);
        
        $loan->update([
            'status' => $request->status ?? $loan->status,
            'metadata' => $request->metadata ?? $loan->metadata,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'loan' => $loan
        ]);
    }

    public function repayLoan(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:defi_loans,id',
            'repayment_amount' => 'required|numeric|min:0',
            'repayment_address' => 'required|string|max:255',
            'transaction_hash' => 'nullable|string|max:255'
        ]);

        $loan = DefiLoan::findOrFail($request->id);
        
        if ($loan->status !== 'active') {
            return response()->json(['error' => 'Loan is not active'], 400);
        }

        if ($request->repayment_amount > $loan->amount) {
            return response()->json(['error' => 'Repayment amount exceeds loan amount'], 400);
        }

        $result = $this->processRepayment($loan, $request->all());

        return response()->json([
            'status' => $result['status'],
            'loan' => $result['loan']
        ]);
    }

    public function liquidateLoan(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:defi_loans,id',
            'liquidator_address' => 'required|string|max:255',
            'liquidation_price' => 'nullable|numeric|min:0'
        ]);

        $loan = DefiLoan::findOrFail($request->id);
        
        if ($loan->status !== 'active') {
            return response()->json(['error' => 'Loan is not active'], 400);
        }

        $result = $this->processLiquidation($loan, $request->all());

        return response()->json([
            'status' => $result['status'],
            'loan' => $result['loan']
        ]);
    }

    public function getDefiStats(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $stats = [
            'total_loans' => DefiLoan::count(),
            'active_loans' => DefiLoan::where('status', 'active')->count(),
            'completed_loans' => DefiLoan::where('status', 'completed')->count(),
            'defaulted_loans' => DefiLoan::where('status', 'defaulted')->count(),
            'liquidated_loans' => DefiLoan::where('status', 'liquidated')->count(),
            'total_loan_amount' => DefiLoan::sum('amount'),
            'total_collateral' => DefiLoan::sum('collateral_amount'),
            'average_interest_rate' => DefiLoan::avg('interest_rate'),
            'total_interest_earned' => $this->calculateTotalInterestEarned($startDate),
            'protocol_stats' => $this->getProtocolStats($startDate),
            'collateral_stats' => $this->buildCollateralStats($startDate),
            'borrower_stats' => $this->getBorrowerStats($startDate),
            'lender_stats' => $this->getLenderStats($startDate)
        ];

        return response()->json($stats);
    }

    public function getProtocolStats(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);
        
        $stats = $this->buildProtocolStats($startDate);

        return response()->json($stats);
    }

    public function getBorrowerLoans(Request $request)
    {
        $borrowerAddress = $request->borrower_address;
        
        $loans = DefiLoan::where('borrower_address', $borrowerAddress)
            ->with(['lender', 'collateral'])
            ->latest()
            ->paginate(20);

        return response()->json($loans);
    }

    public function getLenderLoans(Request $request)
    {
        $lenderAddress = $request->lender_address;
        
        $loans = DefiLoan::where('lender_address', $lenderAddress)
            ->with(['borrower', 'collateral'])
            ->latest()
            ->paginate(20);

        return response()->json($loans);
    }

    public function getCollateralStats(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $stats = [
            'total_collateral' => DefiLoan::where('created_at', '>=', $startDate)->sum('collateral_amount'),
            'collateral_by_type' => $this->getCollateralByType($startDate),
            'collateral_utilization' => $this->getCollateralUtilization($startDate),
            'liquidated_collateral' => DefiLoan::where('status', 'liquidated')
                ->where('created_at', '>=', $startDate)
                ->sum('collateral_amount')
        ];

        return response()->json($stats);
    }

    public function getInterestRates(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $rates = [
            'average_rate' => DefiLoan::where('created_at', '>=', $startDate)->avg('interest_rate'),
            'rates_by_protocol' => $this->getInterestRatesByProtocol($startDate),
            'rates_by_collateral' => $this->getInterestRatesByCollateral($startDate),
            'rates_by_term' => $this->getInterestRatesByTerm($startDate),
            'rate_trends' => $this->getInterestRateTrends($startDate)
        ];

        return response()->json($rates);
    }

    public function searchLoans(Request $request)
    {
        $query = DefiLoan::with(['borrower', 'lender', 'collateral']);
        
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('borrower_address', 'like', "%{$search}%")
                  ->orWhere('lender_address', 'like', "%{$search}%")
                  ->orWhere('collateral_address', 'like', "%{$search}%");
            });
        }
        
        if ($request->protocol) {
            $query->where('protocol', $request->protocol);
        }
        
        if ($request->collateral_type) {
            $query->where('collateral_type', $request->collateral_type);
        }
        
        if ($request->interest_rate_min) {
            $query->where('interest_rate', '>=', $request->interest_rate_min);
        }
        
        if ($request->interest_rate_max) {
            $query->where('interest_rate', '<=', $request->interest_rate_max);
        }
        
        if ($request->loan_term_min) {
            $query->where('loan_term', '>=', $request->loan_term_min);
        }
        
        if ($request->loan_term_max) {
            $query->where('loan_term', '<=', $request->loan_term_max);
        }

        $loans = $query->latest()->paginate(20);
        
        return response()->json($loans);
    }

    public function exportLoans(Request $request)
    {
        $format = $request->format ?? 'json';
        $limit = $request->limit ?? 1000;
        
        $loans = DefiLoan::with(['borrower', 'lender', 'collateral'])->latest()->limit($limit)->get();

        if ($format === 'csv') {
            return $this->exportLoansToCsv($loans);
        }

        return response()->json($loans);
    }

    private function processRepayment($loan, $data)
    {
        $newAmount = $loan->amount - $data['repayment_amount'];
        
        $loan->update([
            'amount' => $newAmount,
            'status' => $newAmount <= 0 ? 'completed' : 'active',
            'updated_at' => now()
        ]);

        return [
            'status' => 'success',
            'loan' => $loan
        ];
    }

    private function processLiquidation($loan, $data)
    {
        $liquidationPrice = $data['liquidation_price'] ?? $loan->collateral_amount * 0.8;
        
        $loan->update([
            'status' => 'liquidated',
            'metadata' => array_merge($loan->metadata ?? [], [
                'liquidation_price' => $liquidationPrice,
                'liquidator_address' => $data['liquidator_address'],
                'liquidated_at' => now()
            ]),
            'updated_at' => now()
        ]);

        return [
            'status' => 'success',
            'loan' => $loan
        ];
    }

    private function calculateTotalInterestEarned($startDate)
    {
        // Simplified interest calculation
        $loans = DefiLoan::where('created_at', '>=', $startDate)->get();
        $totalInterest = 0;
        
        foreach ($loans as $loan) {
            $daysElapsed = now()->diffInDays($loan->start_date);
            $dailyInterest = ($loan->amount * $loan->interest_rate / 100) / 365;
            $totalInterest += $dailyInterest * $daysElapsed;
        }
        
        return $totalInterest;
    }

    private function buildProtocolStats($startDate)
    {
        $protocols = ['aave', 'compound', 'uniswap', 'curve', 'balancer'];
        $stats = [];

        foreach ($protocols as $protocol) {
            $stats[$protocol] = $this->getProtocolData($protocol, $startDate);
        }

        return $stats;
    }

    private function getProtocolData($protocol, $startDate)
    {
        return [
            'total_loans' => DefiLoan::where('protocol', $protocol)->where('created_at', '>=', $startDate)->count(),
            'total_volume' => DefiLoan::where('protocol', $protocol)->where('created_at', '>=', $startDate)->sum('amount'),
            'average_rate' => DefiLoan::where('protocol', $protocol)->where('created_at', '>=', $startDate)->avg('interest_rate'),
            'active_loans' => DefiLoan::where('protocol', $protocol)->where('status', 'active')->count(),
            'completed_loans' => DefiLoan::where('protocol', $protocol)->where('status', 'completed')->count()
        ];
    }

    private function buildCollateralStats($startDate)
    {
        return [
            'total_collateral' => DefiLoan::where('created_at', '>=', $startDate)->sum('collateral_amount'),
            'collateral_by_type' => $this->getCollateralByType($startDate),
            'collateral_utilization' => $this->getCollateralUtilization($startDate)
        ];
    }

    private function getBorrowerStats($startDate)
    {
        return [
            'unique_borrowers' => DefiLoan::where('created_at', 'active', '>=', $startDate)
                ->distinct('borrower_address')
                ->count(),
            'total_borrowed' => DefiLoan::where('created_at', '>=', $startDate)->sum('amount'),
            'average_loan_size' => DefiLoan::where('created_at', '>=', $startDate)->avg('amount'),
            'default_rate' => $this->getDefaultRate($startDate)
        ];
    }

    private function getLenderStats($startDate)
    {
        return [
            'unique_lenders' => DefiLoan::where('created_at', '>=', $startDate)
                ->distinct('lender_address')
                ->count(),
            'total_lent' => DefiLoan::where('created_at', '>=', $startDate)->sum('amount'),
            'average_loan_size' => DefiLoan::where('created_at', '>=', $startDate)->avg('amount'),
            'interest_earned' => $this->calculateTotalInterestEarned($startDate)
        ];
    }

    private function getCollateralByType($startDate)
    {
        return DefiLoan::where('created_at', '>=', $startDate)
            ->selectRaw('collateral_type, SUM(collateral_amount) as total')
            ->groupBy('collateral_type')
            ->orderByDesc('total')
            ->get();
    }

    private function getCollateralUtilization($startDate)
    {
        $totalCollateral = DefiLoan::where('created_at', '>=', $startDate)->sum('collateral_amount');
        $totalLoanAmount = DefiLoan::where('created_at', '>=', $startDate)->sum('amount');
        
        return $totalLoanAmount > 0 ? ($totalCollateral / $totalLoanAmount) * 100 : 0;
    }

    private function getDefaultRate($startDate)
    {
        $totalLoans = DefiLoan::where('created_at', '>=', $startDate)->count();
        $defaultedLoans = DefiLoan::where('status', 'defaulted')->where('created_at', '>=', $startDate)->count();
        
        return $totalLoans > 0 ? ($defaultedLoans / $totalLoans) * 100 : 0;
    }

    private function getInterestRatesByProtocol($startDate)
    {
        return DefiLoan::where('created_at', '>=', $startDate)
            ->selectRaw('protocol, AVG(interest_rate) as avg_rate, COUNT(*) as count')
            ->groupBy('protocol')
            ->orderByDesc('avg_rate')
            ->get();
    }

    private function getInterestRatesByCollateral($startDate)
    {
        return DefiLoan::where('created_at', '>=', $startDate)
            ->selectRaw('collateral_type, AVG(interest_rate) as avg_rate, COUNT(*) as count')
            ->groupBy('collateral_type')
            ->orderByDesc('avg_rate')
            ->get();
    }

    private function getInterestRatesByTerm($startDate)
    {
        return DefiLoan::where('created_at', '>=', $startDate)
            ->selectRaw('loan_term, AVG(interest_rate) as avg_rate, COUNT(*) as count')
            ->groupBy('loan_term')
            ->orderBy('loan_term')
            ->get();
    }

    private function getInterestRateTrends($startDate)
    {
        return DefiLoan::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, AVG(interest_rate) as avg_rate')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getStartDate($period)
    {
        return match($period) {
            '1d' => now()->subHour(),
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDay()
        };
    }

    private function exportLoansToCsv($loans)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="defi_loans.csv"'
        ];

        $callback = function() use ($loans) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'ID', 'Borrower Address', 'Lender Address', 'Amount', 'Interest Rate', 'Loan Term',
                'Collateral Amount', 'Collateral Type', 'Protocol', 'Status', 'Start Date', 'End Date'
            ]);
            
            foreach ($loans as $loan) {
                fputcsv($file, [
                    $loan->id,
                    $loan->borrower_address,
                    $loan->lender_address,
                    $loan->amount,
                    $loan->interest_rate,
                    $loan->loan_term,
                    $loan->collateral_amount,
                    $loan->collateral_type,
                    $loan->protocol,
                    $loan->status,
                    $loan->start_date->format('Y-m-d H:i:s'),
                    $loan->end_date->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
