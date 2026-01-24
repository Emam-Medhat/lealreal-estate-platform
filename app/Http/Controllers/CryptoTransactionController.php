<?php

namespace App\Http\Controllers;

use App\Models\CryptoTransaction;
use App\Models\CryptoWallet;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CryptoTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $transactions = CryptoTransaction::with(['user', 'wallet'])->latest()->paginate(50);
        
        return view('blockchain.transactions.index', compact('transactions'));
    }

    public function createTransaction(Request $request)
    {
        $request->validate([
            'from_address' => 'required|string|max:255',
            'to_address' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|in:ETH,BTC,USDC,USDT',
            'gas_price' => 'required|numeric|min:0',
            'gas_limit' => 'required|integer|min:0',
            'gas_used' => 'nullable|integer|min:0',
            'nonce' => 'required|integer|min:0',
            'hash' => 'required|string|max:255',
            'block_hash' => 'nullable|string|max:255',
            'block_number' => 'nullable|integer|min:0',
            'transaction_index' => 'nullable|integer|min:0',
            'status' => 'required|string|in:pending,confirmed,failed,reverted',
            'transaction_type' => 'required|string|in:send,receive,contract_call,token_transfer',
            'contract_address' => 'nullable|string|max:255',
            'function_name' => 'nullable|string|max:255',
            'parameters' => 'nullable|array',
            'memo' => 'nullable|string|max:255',
            'user_id' => 'nullable|integer|exists:users,id',
            'created_at' => 'now()',
            'updated_at' => 'now()'
        ]);

        $transaction = CryptoTransaction::create([
            'from_address' => $request->from_address,
            'to_address' => $request->to_address,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'gas_price' => $request->gas_price,
            'gas_limit' => $request->gas_limit,
            'gas_used' => $request->gas_used ?? 0,
            'nonce' => $request->nonce,
            'hash' => $request->hash,
            'block_hash' => $request->block_hash,
            'block_number' => $request->block_number,
            'transaction_index' => $request->transaction_index,
            'status' => $request->status,
            'transaction_type' => $request->transaction_type,
            'contract_address' => $request->contract_address,
            'function_name' => $request->function_name,
            'parameters' => $request->parameters ?? [],
            'memo' => $request->memo,
            'user_id' => $request->user_id ?? auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'transaction' => $transaction
        ]);
    }

    public function getTransactions(Request $request)
    {
        $query = CryptoTransaction::with(['user', 'wallet']);
        
        if ($request->from_address) {
            $query->where('from_address', $request->from_address);
        }
        
        if ($request->to_address) {
            $query->where('to_address', $request->to_address);
        }
        
        if ($request->hash) {
            $query->where('hash', $request->hash);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->transaction_type) {
            $query->where('transaction_type', $request->transaction_type);
        }
        
        if ($request->currency) {
            $query->where('currency', $request->currency);
        }
        
        if ($request->amount_min) {
            $query->where('amount', '>=', $request->amount_min);
        }
        
        if ($request->amount_max) {
            $query->where('amount', '<=', $request->amount_max);
        }
        
        if ($request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(50);
        
        return response()->json($transactions);
    }

    public function getTransaction(Request $request)
    {
        $transaction = CryptoTransaction::with(['user', 'wallet'])
            ->where('id', $request->id)
            ->orWhere('hash', $request->hash)
            ->first();
        
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        return response()->json($transaction);
    }

    public function updateTransactionStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:crypto_transactions,id',
            'status' => 'required|string|in:pending,confirmed,failed,reverted',
            'block_hash' => 'nullable|string|max:255',
            'block_number' => 'nullable|integer|min:0',
            'transaction_index' => 'nullable|integer|min:0',
            'gas_used' => 'nullable|integer|min:0'
        ]);

        $transaction = CryptoTransaction::findOrFail($request->id);
        
        $transaction->update([
            'status' => $request->status,
            'block_hash' => $request->block_hash ?? $transaction->block_hash,
            'block_number' => $request->block_number ?? $transaction->block_number,
            'transaction_index' => $request->transaction_index ?? $transaction->transaction_index,
            'gas_used' => $request->gas_used ?? $transaction->gas_used,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'transaction' => $transaction
        ]);
    }

    public function getTransactionStats(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $stats = [
            'total_transactions' => CryptoTransaction::where('created_at', '>=', $startDate)->count(),
            'confirmed_transactions' => CryptoTransaction::where('status', 'confirmed')->where('created_at', '>=', $startDate)->count(),
            'failed_transactions' => CryptoTransaction::where('status', 'failed')->where('created_at', '>=', $startDate)->count(),
            'pending_transactions' => CryptoTransaction::where('status', 'pending')->where('created_at', '>=', $startDate)->count(),
            'total_volume' => CryptoTransaction::where('created_at', '>=', $startDate)->sum('amount'),
            'total_gas_used' => CryptoTransaction::where('created_at', '>=', $startDate)->sum('gas_used'),
            'average_gas_price' => CryptoTransaction::where('created_at', '>=', $startDate)->avg('gas_price'),
            'transaction_types' => $this->getTransactionTypes($startDate),
            'currencies' => $this->getCurrencies($startDate),
            'hourly_volume' => $this->getHourlyVolume($startDate),
            'top_addresses' => $this->getTopAddresses($startDate),
            'average_transaction_fee' => $this->getAverageTransactionFee($startDate)
        ];

        return response()->json($stats);
    }

    public function getPendingTransactions()
    {
        $transactions = CryptoTransaction::where('status', 'pending')
            ->with(['user', 'wallet'])
            ->latest()
            ->get();

        return response()->json($transactions);
    }

    public function getConfirmedTransactions(Request $request)
    {
        $limit = $request->limit ?? 100;
        
        $transactions = CryptoTransaction::where('status', 'confirmed')
            ->with(['user', 'wallet'])
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json($transactions);
    }

    public function getFailedTransactions(Request $request)
    {
        $limit = $request->limit ?? 100;
        
        $transactions = CryptoTransaction::where('status', 'failed')
            ->with(['user', 'wallet'])
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json($transactions);
    }

    public function getWalletTransactions(Request $request)
    {
        $address = $request->address;
        
        $transactions = CryptoTransaction::where('from_address', $address)
            ->orWhere('to_address', $address)
            ->with(['user', 'wallet'])
            ->latest()
            ->paginate(50);

        return response()->json($transactions);
    }

    public function getContractTransactions(Request $request)
    {
        $contractAddress = $request->contract_address;
        
        $transactions = CryptoTransaction::where('contract_address', $contractAddress)
            ->with(['user', 'wallet'])
            ->latest()
            ->paginate(50);

        return response()->json($transactions);
    }

    public function getTransactionHistory(Request $request)
    {
        $address = $request->address;
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $transactions = CryptoTransaction::where('from_address', $address)
            ->orWhere('to_address', $address)
            ->where('created_at', '>=', $startDate)
            ->with(['user', 'wallet'])
            ->latest()
            ->get();

        return response()->json($transactions);
    }

    public function searchTransactions(Request $request)
    {
        $query = CryptoTransaction::with(['user', 'wallet']);
        
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('hash', 'like', "%{$search}%")
                  ->orWhere('from_address', 'like', "%{$search}%")
                  ->orWhere('to_address', 'like', "%{$search}%")
                  ->orWhere('memo', 'like', "%{$search}%");
            });
        }
        
        if ($request->amount_min) {
            $query->where('amount', '>=', $request->amount_min);
        }
        
        if ($request->amount_max) {
            $query->where('amount', '<=', $request->amount_max);
        }
        
        if ($request->gas_price_min) {
            $query->where('gas_price', '>=', $request->gas_price_min);
        }
        
        if ($request->gas_price_max) {
            $query->where('gas_price', '<=', $request->gas_price_max);
        }

        $transactions = $query->latest()->paginate(50);
        
        return response()->json($transactions);
    }

    public function exportTransactions(Request $request)
    {
        $format = $request->format ?? 'json';
        $limit = $request->limit ?? 1000;
        
        $transactions = CryptoTransaction::with(['user', 'wallet'])->latest()->limit($limit)->get();

        if ($format === 'csv') {
            return $this->exportTransactionsToCsv($transactions);
        }

        return response()->json($transactions);
    }

    private function getTransactionTypes($startDate)
    {
        return CryptoTransaction::where('created_at', '>=', $startDate)
            ->selectRaw('transaction_type, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('transaction_type')
            ->orderByDesc('total_amount')
            ->get();
    }

    private function getCurrencies($startDate)
    {
        return CryptoTransaction::where('created_at', '>=', $startDate)
            ->selectRaw('currency, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('currency')
            ->orderByDesc('total_amount')
            ->get();
    }

    private function getHourlyVolume($startDate)
    {
        return CryptoTransaction::where('created_at', '>=', $startDate)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour, SUM(amount) as volume, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    private function getTopAddresses($startDate)
    {
        return CryptoTransaction::where('created_at', '>=', $startDate)
            ->selectRaw('from_address as address, SUM(amount) as total_volume, COUNT(*) as transaction_count')
            ->groupBy('from_address')
            ->orderByDesc('total_volume')
            ->limit(10)
            ->get();
    }

    private function getAverageTransactionFee($startDate)
    {
        $transactions = CryptoTransaction::where('created_at', '>=', $startDate)
            ->where('status', 'confirmed')
            ->get();
        
        if ($transactions->isEmpty()) {
            return 0;
        }

        $totalFees = $transactions->sum(function($tx) {
            return ($tx->gas_used ?? 0) * $tx->gas_price;
        });

        return $totalFees / $transactions->count();
    }

    private function exportTransactionsToCsv($transactions)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="crypto_transactions.csv"'
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Hash', 'From Address', 'To Address', 'Amount', 'Currency', 'Gas Price', 
                'Gas Limit', 'Gas Used', 'Status', 'Type', 'Block Number', 'Created At'
            ]);
            
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->hash,
                    $transaction->from_address,
                    $transaction->to_address,
                    $transaction->amount,
                    $transaction->currency,
                    $transaction->gas_price,
                    $transaction->gas_limit,
                    $transaction->gas_used,
                    $transaction->status,
                    $transaction->transaction_type,
                    $transaction->block_number,
                    $transaction->created_at->format('Y-m-d H:i:s')
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

    public function getTransactionReceipt(Request $request)
    {
        $hash = $request->hash;
        
        $transaction = CryptoTransaction::where('hash', $hash)->first();
        
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $receipt = [
            'transaction_hash' => $transaction->hash,
            'block_hash' => $transaction->block_hash,
            'block_number' => $transaction->block_number,
            'transaction_index' => $transaction->transaction_index,
            'from_address' => $transaction->from_address,
            'to_address' => $transaction->to_address,
            'gas_used' => $transaction->gas_used,
            'cumulative_gas_used' => $transaction->gas_used,
            'status' => $transaction->status,
            'logs' => $this->getTransactionLogs($transaction),
            'contract_address' => $transaction->contract_address,
            'function_name' => $transaction->function_name,
            'parameters' => $transaction->parameters
        ];

        return response()->json($receipt);
    }

    private function getTransactionLogs($transaction)
    {
        // Simplified logs generation
        return [
            [
                'address' => $transaction->contract_address,
                'topics' => [
                    '0x' . bin2hex(random_bytes(32)),
                    '0x' . bin2hex(random_bytes(32))
                ],
                'data' => '0x' . bin2hex(random_bytes(64)),
                'block_number' => $transaction->block_number,
                'transaction_hash' => $transaction->hash,
                'log_index' => 0
            ]
        ];
    }

    public function getTransactionTrace(Request $request)
    {
        $hash = $request->hash;
        
        $transaction = CryptoTransaction::where('hash', $hash)->first();
        
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $trace = [
            'transaction_hash' => $transaction->hash,
            'from_address' => $transaction->from_address,
            'to_address' => $transaction->to_address,
            'gas_used' => $transaction->gas_used,
            'gas_limit' => $transaction->gas_limit,
            'input' => '0x' . bin2hex(random_bytes(64)),
            'output' => '0x' . bin2hex(random_bytes(64)),
            'calls' => $this->getTransactionCalls($transaction)
        ];

        return response()->json($trace);
    }

    private function getTransactionCalls($transaction)
    {
        // Simplified calls generation
        return [
            [
                'from' => $transaction->from_address,
                'to' => $transaction->to_address,
                'gas_used' => $transaction->gas_used,
                'input' => '0x' . bin2hex(random_bytes(64)),
                'output' => '0x' . bin2hex(random_bytes(64)),
                'call_type' => 'call'
            ]
        ];
    }
}
