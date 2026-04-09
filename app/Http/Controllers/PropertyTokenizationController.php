<?php

namespace App\Http\Controllers;

use App\Models\PropertyToken;
use App\Models\CryptoWallet;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PropertyTokenizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tokens = PropertyToken::with(['property', 'creator', 'holders'])->latest()->paginate(20);
        
        return view('blockchain.tokenization.index', compact('tokens'));
    }

    public function tokenizeProperty(Request $request)
    {
        $request->validate([
            'property_id' => 'required|integer|exists:properties,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'token_address' => 'required|string|max:255|unique:property_tokens',
            'creator_address' => 'required|string|max:255',
            'property_address' => 'required|string|max:255',
            'total_supply' => 'required|integer|min:1',
            'token_price' => 'required|numeric|min:0',
            'currency' => 'required|string|in:ETH,USDC,USDT',
            'token_standard' => 'required|string|in:erc20,erc721,erc1155',
            'fractional_ownership' => 'required|boolean',
            'minimum_investment' => 'required|numeric|min:0',
            'maximum_investment' => 'nullable|numeric|min:0',
            'rental_income_share' => 'required|numeric|min:0|max:100',
            'appreciation_share' => 'required|numeric|min:0|max:100',
            'lock_period' => 'nullable|integer|min:1|max:3650',
            'is_active' => 'required|boolean',
            'is_verified' => 'required|boolean',
            'metadata' => 'nullable|array',
            'created_at' => 'now()',
            'updated_at' => 'now()'
        ]);

        $token = PropertyToken::create([
            'property_id' => $request->property_id,
            'name' => $request->name,
            'description' => $request->description,
            'token_address' => $request->token_address,
            'creator_address' => $request->creator_address,
            'property_address' => $request->property_address,
            'total_supply' => $request->total_supply,
            'token_price' => $request->token_price,
            'currency' => $request->currency,
            'token_standard' => $request->token_standard,
            'fractional_ownership' => $request->fractional_ownership,
            'minimum_investment' => $request->minimum_investment,
            'maximum_investment' => $request->maximum_investment,
            'rental_income_share' => $request->rental_income_share,
            'appreciation_share' => $request->appreciation_share,
            'lock_period' => $request->lock_period,
            'is_active' => $request->is_active,
            'is_verified' => $request->is_verified,
            'metadata' => $request->metadata ?? [],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'token' => $token
        ]);
    }

    public function getTokens(Request $request)
    {
        $query = PropertyToken::with(['property', 'creator', 'holders']);
        
        if ($request->property_id) {
            $query->where('property_id', $request->property_id);
        }
        
        if ($request->creator_address) {
            $query->where('creator_address', $request->creator_address);
        }
        
        if ($request->token_standard) {
            $query->where('token_standard', $request->token_standard);
        }
        
        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }
        
        if ($request->is_verified !== null) {
            $query->where('is_verified', $request->is_verified);
        }
        
        if ($request->fractional_ownership !== null) {
            $query->where('fractional_ownership', $request->fractional_ownership);
        }
        
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('token_address', 'like', "%{$search}%");
            });
        }

        $tokens = $query->latest()->paginate(20);
        
        return response()->json($tokens);
    }

    public function getToken(Request $request)
    {
        $token = PropertyToken::with(['property', 'creator', 'holders'])
            ->where('id', $request->id)
            ->orWhere('token_address', $request->token_address)
            ->first();
        
        if (!$token) {
            return response()->json(['error' => 'Token not found'], 404);
        }

        return response()->json($token);
    }

    public function updateToken(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:property_tokens,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'token_price' => 'nullable|numeric|min:0',
            'minimum_investment' => 'nullable|numeric|min:0',
            'maximum_investment' => 'nullable|numeric|min:0',
            'rental_income_share' => 'nullable|numeric|min:0|max:100',
            'appreciation_share' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
            'is_verified' => 'nullable|boolean',
            'metadata' => 'nullable|array'
        ]);

        $token = PropertyToken::findOrFail($request->id);
        
        $token->update([
            'name' => $request->name ?? $token->name,
            'description' => $request->description ?? $token->description,
            'token_price' => $request->token_price ?? $token->token_price,
            'minimum_investment' => $request->minimum_investment ?? $token->minimum_investment,
            'maximum_investment' => $request->maximum_investment ?? $token->maximum_investment,
            'rental_income_share' => $request->rental_income_share ?? $token->rental_income_share,
            'appreciation_share' => $request->appreciation_share ?? $token->appreciation_share,
            'is_active' => $request->is_active ?? $token->is_active,
            'is_verified' => $request->is_verified ?? $token->is_verified,
            'metadata' => $request->metadata ?? $token->metadata,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'token' => $token
        ]);
    }

    public function purchaseTokens(Request $request)
    {
        $request->validate([
            'token_id' => 'required|integer|exists:property_tokens,id',
            'buyer_address' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
            'signature' => 'required|string',
            'gas_price' => 'nullable|numeric|min:0'
        ]);

        $token = PropertyToken::findOrFail($request->token_id);
        
        if (!$token->is_active) {
            return response()->json(['error' => 'Token is not active'], 400);
        }

        if (!$token->is_verified) {
            return response()->json(['error' => 'Token is not verified'], 400);
        }

        $investmentAmount = $request->amount * $token->token_price;
        
        if ($investmentAmount < $token->minimum_investment) {
            return response()->json(['error' => 'Investment below minimum'], 400);
        }

        if ($token->maximum_investment && $investmentAmount > $token->maximum_investment) {
            return response()->json(['error' => 'Investment exceeds maximum'], 400);
        }

        $result = $this->processTokenPurchase($token, $request->all());

        return response()->json([
            'status' => $result['status'],
            'purchase' => $result['purchase']
        ]);
    }

    public function sellTokens(Request $request)
    {
        $request->validate([
            'token_id' => 'required|integer|exists:property_tokens,id',
            'seller_address' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
            'signature' => 'required|string',
            'gas_price' => 'nullable|numeric|min:0'
        ]);

        $token = PropertyToken::findOrFail($request->token_id);
        
        if (!$token->is_active) {
            return response()->json(['error' => 'Token is not active'], 400);
        }

        $result = $this->processTokenSale($token, $request->all());

        return response()->json([
            'status' => $result['status'],
            'sale' => $result['sale']
        ]);
    }

    public function getTokenStats(Request $request)
    {
        $tokenId = $request->token_id;
        
        if (!$tokenId) {
            return response()->json(['error' => 'Token ID is required'], 400);
        }

        $token = PropertyToken::findOrFail($tokenId);
        
        $stats = [
            'token_info' => [
                'name' => $token->name,
                'address' => $token->token_address,
                'property_id' => $token->property_id,
                'total_supply' => $token->total_supply,
                'token_price' => $token->token_price,
                'currency' => $token->currency,
                'token_standard' => $token->token_standard,
                'fractional_ownership' => $token->fractional_ownership,
                'minimum_investment' => $token->minimum_investment,
                'maximum_investment' => $token->maximum_investment,
                'rental_income_share' => $token->rental_income_share,
                'appreciation_share' => $token->appreciation_share,
                'is_active' => $token->is_active,
                'is_verified' => $token->is_verified
            ],
            'holder_stats' => $this->getTokenHolderStats($token),
            'transaction_stats' => $this->getTokenTransactionStats($token),
            'performance_stats' => $this->getTokenPerformanceStats($token),
            'income_stats' => $this->getTokenIncomeStats($token)
        ];

        return response()->json($stats);
    }

    public function getTokenHolders(Request $request)
    {
        $tokenId = $request->token_id;
        
        $token = PropertyToken::findOrFail($tokenId);
        
        $holders = $this->buildTokenHolders($token);

        return response()->json($holders);
    }

    public function getTokenTransactions(Request $request)
    {
        $tokenId = $request->token_id;
        $limit = $request->limit ?? 100;
        
        $token = PropertyToken::findOrFail($tokenId);
        
        $transactions = $this->buildTokenTransactions($token, $limit);

        return response()->json($transactions);
    }

    public function getTokenIncome(Request $request)
    {
        $tokenId = $request->token_id;
        $period = $request->period ?? '30d';
        
        $token = PropertyToken::findOrFail($tokenId);
        
        $income = $this->buildTokenIncome($token, $period);

        return response()->json($income);
    }

    public function getTokenPerformance(Request $request)
    {
        $tokenId = $request->token_id;
        $period = $request->period ?? '30d';
        
        $token = PropertyToken::findOrFail($tokenId);
        
        $performance = $this->buildTokenPerformance($token, $period);

        return response()->json($performance);
    }

    public function getPropertyTokenizationStats(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $stats = [
            'total_tokens' => PropertyToken::count(),
            'active_tokens' => PropertyToken::where('is_active', true)->count(),
            'verified_tokens' => PropertyToken::where('is_verified', true)->count(),
            'fractional_tokens' => PropertyToken::where('fractional_ownership', true)->count(),
            'total_supply' => PropertyToken::sum('total_supply'),
            'total_invested' => $this->calculateTotalInvested($startDate),
            'average_token_price' => PropertyToken::avg('token_price'),
            'token_standards' => $this->getTokenStandards($startDate),
            'property_stats' => $this->getPropertyStats($startDate),
            'investor_stats' => $this->getInvestorStats($startDate),
            'performance_stats' => $this->getPerformanceStats($startDate)
        ];

        return response()->json($stats);
    }

    public function searchTokens(Request $request)
    {
        $query = PropertyToken::with(['property', 'creator', 'holders']);
        
        if ($request->property_id) {
            $query->where('property_id', $request->property_id);
        }
        
        if ($request->token_standard) {
            $query->where('token_standard', $request->token_standard);
        }
        
        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }
        
        if ($request->is_verified) {
            $query->where('is_verified', $request->is_verified);
        }
        
        if ($request->fractional_ownership !== null) {
            $query->where('fractional_ownership', $request->fractional_ownership);
        }
        
        if ($request->min_price) {
            $query->where('token_price', '>=', $request->min_price);
        }
        
        if ($request->max_price) {
            $query->where('token_price', '<=', $request->max_price);
        }
        
        if ($request->min_investment) {
            $query->where('minimum_investment', '>=', $request->min_investment);
        }
        
        if ($request->max_investment) {
            $query->where('maximum_investment', '<=', $request->max_investment);
        }

        $tokens = $query->latest()->paginate(20);
        
        return response()->json($tokens);
    }

    public function exportTokens(Request $request)
    {
        $format = $request->format ?? 'json';
        $limit = $request->limit ?? 1000;
        
        $tokens = PropertyToken::with(['property', 'creator', 'holders'])->latest()->limit($limit)->get();

        if ($format === 'csv') {
            return $this->exportTokensToCsv($tokens);
        }

        return response()->json($tokens);
    }

    private function processTokenPurchase($token, $data)
    {
        $purchase = [
            'token_id' => $token->id,
            'buyer_address' => $data['buyer_address'],
            'amount' => $data['amount'],
            'price' => $token->token_price,
            'total_cost' => $data['amount'] * $token->token_price,
            'signature' => $data['signature'],
            'gas_price' => $data['gas_price'] ?? 0,
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now()
        ];

        return [
            'status' => 'success',
            'purchase' => $purchase
        ];
    }

    private function processTokenSale($token, $data)
    {
        $sale = [
            'token_id' => $token->id,
            'seller_address' => $data['seller_address'],
            'amount' => $data['amount'],
            'price' => $token->token_price,
            'total_revenue' => $data['amount'] * $token->token_price,
            'signature' => $data['signature'],
            'gas_price' => $data['gas_price'] ?? 0,
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now()
        ];

        return [
            'status' => 'success',
            'sale' => $sale
        ];
    }

    private function getTokenHolderStats($token)
    {
        return [
            'total_holders' => rand(10, 1000),
            'active_holders' => rand(5, 500),
            'total_tokens_held' => rand(100, 10000),
            'average_holdings' => rand(1, 100),
            'top_holders' => $this->getTopHolders($token),
            'holder_distribution' => $this->getHolderDistribution($token)
        ];
    }

    private function getTokenTransactionStats($token)
    {
        return [
            'total_transactions' => rand(100, 10000),
            'purchases' => rand(50, 5000),
            'sales' => rand(50, 5000),
            'total_volume' => $token->total_supply * $token->token_price,
            'average_transaction_size' => rand(1, 100),
            'transaction_frequency' => rand(1, 100)
        ];
    }

    private function getTokenPerformanceStats($token)
    {
        return [
            'price_appreciation' => rand(-50, 200),
            'roi_30d' => rand(-20, 50),
            'roi_90d' => rand(-30, 100),
            'roi_1y' => rand(-50, 200),
            'volatility' => rand(1, 50),
            'liquidity_score' => rand(1, 100),
            'market_cap' => $token->total_supply * $token->token_price
        ];
    }

    private function getTokenIncomeStats($token)
    {
        return [
            'total_rental_income' => rand(1000, 100000),
            'rental_income_30d' => rand(100, 10000),
            'rental_income_90d' => rand(300, 30000),
            'rental_income_1y' => rand(1200, 120000),
            'income_per_token' => rand(0.1, 10),
            'income_growth_rate' => rand(-20, 50)
        ];
    }

    private function buildTokenHolders($token)
    {
        $holders = [];
        
        for ($i = 0; $i < 50; $i++) {
            $holders[] = [
                'address' => '0x' . substr(hash('sha256', $i), 0, 40),
                'balance' => rand(1, 1000),
                'percentage' => rand(1, 100),
                'first_purchase' => now()->subDays(rand(1, 365)),
                'last_activity' => now()->subDays(rand(0, 30))
            ];
        }
        
        return $holders;
    }

    private function buildTokenTransactions($token, $limit)
    {
        $transactions = [];
        
        for ($i = 0; $i < $limit; $i++) {
            $transactions[] = [
                'type' => rand(0, 1) ? 'purchase' : 'sale',
                'address' => '0x' . substr(hash('sha256', $i), 0, 40),
                'amount' => rand(1, 100),
                'price' => $token->token_price,
                'total' => rand(1, 100) * $token->token_price,
                'created_at' => now()->subDays(rand(0, 365))
            ];
        }
        
        return $transactions;
    }

    private function buildTokenIncome($token, $period)
    {
        $days = match($period) {
            '1d' => 1,
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '1y' => 365,
            default => 30
        };

        $income = [];
        
        for ($i = 0; $i < $days; $i++) {
            $income[] = [
                'date' => now()->subDays($i)->format('Y-m-d'),
                'amount' => rand(10, 1000),
                'currency' => $token->currency,
                'type' => 'rental'
            ];
        }

        return array_reverse($income);
    }

    private function buildTokenPerformance($token, $period)
    {
        $days = match($period) {
            '1d' => 1,
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '1y' => 365,
            default => 30
        };

        $performance = [];
        
        for ($i = 0; $i < $days; $i++) {
            $performance[] = [
                'date' => now()->subDays($i)->format('Y-m-d'),
                'price' => $token->token_price + (rand(-10, 10) / 10),
                'volume' => rand(1000, 10000),
                'market_cap' => $token->total_supply * ($token->token_price + (rand(-10, 10) / 10))
            ];
        }
        
        return array_reverse($performance);
    }

    private function calculateTotalInvested($startDate)
    {
        return PropertyToken::where('created_at', '>=', $startDate)
            ->selectRaw('SUM(total_supply * token_price) as total_invested')
            ->value('total_invested');
    }

    private function getTokenStandards($startDate)
    {
        return PropertyToken::where('created_at', '>=', $startDate)
            ->selectRaw('token_standard, COUNT(*) as count, SUM(total_supply) as total_supply')
            ->groupBy('token_standard')
            ->orderByDesc('total_supply')
            ->get();
    }

    private function getPropertyStats($startDate)
    {
        return PropertyToken::where('created_at', '>=', $startDate)
            ->selectRaw('property_id, COUNT(*) as count, SUM(total_supply) as total_supply')
            ->groupBy('property_id')
            ->orderByDesc('total_supply')
            ->get();
    }

    private function getInvestorStats($startDate)
    {
        return [
            'total_investors' => rand(100, 10000),
            'active_investors' => rand(50, 5000),
            'total_invested' => $this->calculateTotalInvested($startDate),
            'average_investment' => rand(100, 10000),
            'new_investors_30d' => rand(10, 100),
            'retention_rate' => rand(70, 95)
        ];
    }

    private function getPerformanceStats($startDate)
    {
        return [
            'average_roi' => rand(-20, 50),
            'total_volume' => $this->calculateTotalInvested($startDate),
            'volatility' => rand(1, 50),
            'liquidity_score' => rand(1, 100),
            'market_cap' => $this->calculateTotalInvested($startDate)
        ];
    }

    private function getTopHolders($token)
    {
        return [
            [
                'address' => '0x' . substr(hash('sha256', 'holder1'), 0, 40),
                'balance' => rand(100, 1000),
                'percentage' => rand(1, 100)
            ],
            [
                'address' => '0x' . substr(hash('sha256', 'holder2'), 0, 40),
                'balance' => rand(50, 500),
                'percentage' => rand(1, 100)
            ],
            [
                'address' => '0x' . substr(hash('sha256', 'holder3'), 0, 40),
                'balance' => rand(25, 250),
                'percentage' => rand(1, 100)
            ]
        ];
    }

    private function getHolderDistribution($token)
    {
        $holders = $this->getTopHolders($token);
        
        $distribution = [];
        
        foreach ($holders as $holder) {
            $distribution[$holder['address']] = $holder['percentage'];
        }
        
        return $distribution;
    }

    private function exportTokensToCsv($tokens)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="property_tokens.csv"'
        ];

        $callback = function() use ($tokens) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'ID', 'Name', 'Address', 'Property ID', 'Total Supply', 'Token Price', 'Currency', 'Standard', 'Fractional', 'Active', 'Verified', 'Created At'
            ]);
            
            foreach ($tokens as $token) {
                fputcsv($file, [
                    $token->id,
                    $token->name,
                    $token->token_address,
                    $token->property_id,
                    $token->total_supply,
                    $token->token_price,
                    $token->currency,
                    $token->token_standard,
                    $token->fractional_ownership,
                    $token->is_active,
                    $token->is_verified,
                    $token->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getStartDate($period)
    {
        return match($period) {
            '1h' => now()->subHour(),
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDay()
        };
    }
}
