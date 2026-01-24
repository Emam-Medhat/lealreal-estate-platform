<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\CryptoTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TokenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tokens = Token::with(['creator', 'transactions'])->latest()->paginate(20);
        
        return view('blockchain.tokens.index', compact('tokens'));
    }

    public function createToken(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'address' => 'required|string|max:255|unique:tokens',
            'total_supply' => 'required|numeric|min:0',
            'decimals' => 'required|integer|min:0|max:18',
            'token_type' => 'required|string|in:erc20,erc721,erc1155,custom',
            'contract_address' => 'required|string|max:255',
            'creator_address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'website' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'telegram' => 'nullable|string|max:255',
            'discord' => 'nullable|string|max:255',
            'is_verified' => 'required|boolean',
            'is_active' => 'required|boolean',
            'metadata' => 'nullable|array',
            'created_at' => 'now()',
            'updated_at' => 'now()'
        ]);

        // Handle logo upload
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('tokens', 'public');
        }

        $token = Token::create([
            'name' => $request->name,
            'symbol' => $request->symbol,
            'address' => $request->address,
            'total_supply' => $request->total_supply,
            'decimals' => $request->decimals,
            'token_type' => $request->token_type,
            'contract_address' => $request->contract_address,
            'creator_address' => $request->creator_address,
            'description' => $request->description,
            'logo' => $logoPath,
            'website' => $request->website,
            'twitter' => $request->twitter,
            'telegram' => $request->telegram,
            'discord' => $request->discord,
            'is_verified' => $request->is_verified,
            'is_active' => $request->is_active,
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
        $query = Token::with(['creator', 'transactions']);
        
        if ($request->token_type) {
            $query->where('token_type', $request->token_type);
        }
        
        if ($request->is_verified !== null) {
            $query->where('is_verified', $request->is_verified);
        }
        
        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }
        
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $tokens = $query->latest()->paginate(20);
        
        return response()->json($tokens);
    }

    public function getToken(Request $request)
    {
        $token = Token::with(['creator', 'transactions'])
            ->where('id', $request->id)
            ->orWhere('address', $request->address)
            ->first();
        
        if (!$token) {
            return response()->json(['error' => 'Token not found'], 404);
        }

        return response()->json($token);
    }

    public function updateToken(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:tokens,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'telegram' => 'nullable|string|max:255',
            'discord' => 'nullable|string|max:255',
            'is_verified' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'metadata' => 'nullable|array'
        ]);

        $token = Token::findOrFail($request->id);
        
        // Handle logo upload
        $logoPath = $token->logo;
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('tokens', 'public');
        }

        $token->update([
            'name' => $request->name ?? $token->name,
            'description' => $request->description ?? $token->description,
            'logo' => $logoPath,
            'website' => $request->website ?? $token->website,
            'twitter' => $request->twitter ?? $token->twitter,
            'telegram' => $request->telegram ?? $token->telegram,
            'discord' => $request->discord ?? $token->discord,
            'is_verified' => $request->is_verified ?? $token->is_verified,
            'is_active' => $request->is_active ?? $token->is_active,
            'metadata' => $request->metadata ?? $token->metadata,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'token' => $token
        ]);
    }

    public function getTokenBalance(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'token_address' => 'required|string|max:255',
            'wallet_address' => 'required|string|max:255'
        ]);

        $token = Token::where('address', $request->token_address)->first();
        
        if (!$token) {
            return response()->json(['error' => 'Token not found'], 404);
        }

        $balance = $this->getTokenBalanceForAddress($token, $request->wallet_address);
        
        return response()->json([
            'address' => $request->wallet_address,
            'token_address' => $request->token_address,
            'token_name' => $token->name,
            'token_symbol' => $token->symbol,
            'balance' => $balance['balance'],
            'formatted_balance' => $balance['formatted_balance'],
            'usd_value' => $balance['usd_value']
        ]);
    }

    public function getTokenStats(Request $request)
    {
        $address = $request->address;
        
        $token = Token::where('address', $address)->first();
        
        if (!$token) {
            return response()->json(['error' => 'Token not found'], 404);
        }

        $stats = [
            'token_info' => [
                'name' => $token->name,
                'symbol' => $token->symbol,
                'address' => $token->address,
                'total_supply' => $token->total_supply,
                'decimals' => $token->decimals,
                'token_type' => $token->token_type,
                'is_verified' => $token->is_verified,
                'is_active' => $token->is_active
            ],
            'market_stats' => $this->getTokenMarketStats($token),
            'holder_stats' => $this->getTokenHolderStats($token),
            'transaction_stats' => $this->getTokenTransactionStats($token),
            'price_stats' => $this->getTokenPriceStats($token)
        ];

        return response()->json($stats);
    }

    public function getTokenHolders(Request $request)
    {
        $address = $request->address;
        $limit = $request->limit ?? 100;
        
        $token = Token::where('address', $address)->first();
        
        if (!$token) {
            return response()->json(['error' => 'Token not found'], 404);
        }

        $holders = $this->buildTokenHolders($token, $limit);
        
        return response()->json($holders);
    }

    public function getTokenTransactions(Request $request)
    {
        $address = $request->address;
        $limit = $request->limit ?? 100;
        
        $token = Token::where('address', $address)->first();
        
        if (!$token) {
            return response()->json(['error' => 'Token not found'], 404);
        }

        $transactions = CryptoTransaction::where('contract_address', $address)
            ->with(['user', 'wallet'])
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json($transactions);
    }

    public function searchTokens(Request $request)
    {
        $query = Token::with(['creator', 'transactions']);
        
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->token_type) {
            $query->where('token_type', $request->token_type);
        }
        
        if ($request->is_verified) {
            $query->where('is_verified', $request->is_verified);
        }
        
        if ($request->min_supply) {
            $query->where('total_supply', '>=', $request->min_supply);
        }
        
        if ($request->max_supply) {
            $query->where('total_supply', '<=', $request->max_supply);
        }

        $tokens = $query->latest()->paginate(20);
        
        return response()->json($tokens);
    }

    public function getTopTokens(Request $request)
    {
        $limit = $request->limit ?? 50;
        $period = $request->period ?? '24h';
        
        $tokens = Token::where('is_active', true)
            ->with(['creator', 'transactions'])
            ->orderByDesc('total_supply')
            ->limit($limit)
            ->get();

        return response()->json($tokens);
    }

    public function getVerifiedTokens(Request $request)
    {
        $limit = $request->limit ?? 100;
        
        $tokens = Token::where('is_verified', true)
            ->where('is_active', true)
            ->with(['creator', 'transactions'])
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json($tokens);
    }

    public function getTokenPrice(Request $request)
    {
        $address = $request->address;
        
        $token = Token::where('address', $address)->first();
        
        if (!$token) {
            return response()->json(['error' => 'Token not found'], 404);
        }

        $price = $this->getTokenCurrentPrice($token);
        
        return response()->json([
            'address' => $address,
            'price_usd' => $price['price_usd'],
            'price_change_24h' => $price['price_change_24h'],
            'volume_24h' => $price['volume_24h'],
            'market_cap' => $price['market_cap'],
            'last_updated' => $price['last_updated']
        ]);
    }

    public function getTokenChart(Request $request)
    {
        $address = $request->address;
        $period = $request->period ?? '7d';
        
        $token = Token::where('address', $address)->first();
        
        if (!$token) {
            return response()->json(['error' => 'Token not found'], 404);
        }

        $chartData = $this->getTokenChartData($token, $period);
        
        return response()->json($chartData);
    }

    private function getTokenBalanceForAddress($token, $address)
    {
        // Simplified balance calculation
        $balance = rand(0, 1000000);
        $formattedBalance = number_format($balance / pow(10, $token->decimals), $token->decimals);
        $usdValue = $this->getTokenCurrentPrice($token)['price_usd'] * $balance / pow(10, $token->decimals);
        
        return [
            'balance' => $balance,
            'formatted_balance' => $formattedBalance,
            'usd_value' => $usdValue
        ];
    }

    private function getTokenMarketStats($token)
    {
        return [
            'current_price' => $this->getTokenCurrentPrice($token)['price_usd'],
            'price_change_24h' => $this->getTokenPriceChange($token, '24h'),
            'volume_24h' => $this->getTokenVolume($token, '24h'),
            'market_cap' => $this->getTokenMarketCap($token),
            'circulating_supply' => $token->total_supply,
            'total_supply' => $token->total_supply
        ];
    }

    private function getTokenHolderStats($token)
    {
        return [
            'total_holders' => rand(100, 10000),
            'top_10_holders_percentage' => rand(10, 80),
            'new_holders_24h' => rand(0, 100),
            'average_balance' => $token->total_supply / rand(100, 10000)
        ];
    }

    private function getTokenTransactionStats($token)
    {
        return [
            'total_transactions' => CryptoTransaction::where('contract_address', $token->address)->count(),
            'transactions_24h' => CryptoTransaction::where('contract_address', $token->address)
                ->where('created_at', '>=', now()->subDay())->count(),
            'unique_addresses' => CryptoTransaction::where('contract_address', $token->address)
                ->selectRaw('COUNT(DISTINCT from_address) as unique_addresses')
                ->value('unique_addresses')
        ];
    }

    private function getTokenPriceStats($token)
    {
        return [
            'all_time_high' => $this->getTokenAllTimeHigh($token),
            'all_time_low' => $this->getTokenAllTimeLow($token),
            'price_change_7d' => $this->getTokenPriceChange($token, '7d'),
            'price_change_30d' => $this->getTokenPriceChange($token, '30d'),
            'volatility_30d' => $this->getTokenVolatility($token, '30d')
        ];
    }

    private function buildTokenHolders($token, $limit)
    {
        // Simplified holders generation
        $holders = [];
        
        for ($i = 0; $i < $limit; $i++) {
            $holders[] = [
                'address' => '0x' . substr(hash('sha256', $i), 0, 40),
                'balance' => rand(0, 1000000),
                'percentage' => rand(1, 100),
                'first_transaction' => now()->subDays(rand(1, 365)),
                'last_transaction' => now()->subDays(rand(0, 30))
            ];
        }
        
        return $holders;
    }

    private function getTokenCurrentPrice($token)
    {
        // Simplified price calculation
        return [
            'price_usd' => rand(0.01, 1000),
            'price_change_24h' => rand(-50, 50),
            'volume_24h' => rand(1000, 1000000),
            'market_cap' => $token->total_supply * rand(0.01, 1000),
            'last_updated' => now()
        ];
    }

    private function getTokenPriceChange($token, $period)
    {
        return rand(-50, 50);
    }

    private function getTokenVolume($token, $period)
    {
        return rand(1000, 1000000);
    }

    private function getTokenMarketCap($token)
    {
        return $token->total_supply * rand(0.01, 1000);
    }

    private function getTokenAllTimeHigh($token)
    {
        return rand(1, 10000);
    }

    private function getTokenAllTimeLow($token)
    {
        return rand(0.001, 100);
    }

    private function getTokenVolatility($token, $period)
    {
        return rand(1, 100);
    }

    private function getTokenChartData($token, $period)
    {
        $dataPoints = match($period) {
            '1d' => 24,
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '1y' => 365,
            default => 7
        };

        $chartData = [];
        $basePrice = rand(1, 100);
        
        for ($i = 0; $i < $dataPoints; $i++) {
            $timestamp = now()->subHours($i);
            $price = $basePrice + (rand(-10, 10) / 10);
            
            $chartData[] = [
                'timestamp' => $timestamp->format('Y-m-d H:i:s'),
                'price' => $price,
                'volume' => rand(1000, 100000)
            ];
        }
        
        return array_reverse($chartData);
    }

    public function exportTokens(Request $request)
    {
        $format = $request->format ?? 'json';
        $limit = $request->limit ?? 1000;
        
        $tokens = Token::with(['creator', 'transactions'])->latest()->limit($limit)->get();

        if ($format === 'csv') {
            return $this->exportTokensToCsv($tokens);
        }

        return response()->json($tokens);
    }

    private function exportTokensToCsv($tokens)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tokens.csv"'
        ];

        $callback = function() use ($tokens) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'ID', 'Name', 'Symbol', 'Address', 'Type', 'Total Supply', 'Decimals', 
                'Contract Address', 'Creator Address', 'Verified', 'Active', 'Created At'
            ]);
            
            foreach ($tokens as $token) {
                fputcsv($file, [
                    $token->id,
                    $token->name,
                    $token->symbol,
                    $token->address,
                    $token->token_type,
                    $token->total_supply,
                    $token->decimals,
                    $token->contract_address,
                    $token->creator_address,
                    $token->is_verified,
                    $token->is_active,
                    $token->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
