<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\CryptoTransactionRepositoryInterface;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CryptoTransactionController extends Controller
{
    protected $transactionRepository;

    public function __construct(CryptoTransactionRepositoryInterface $transactionRepository)
    {
        $this->middleware('auth');
        $this->transactionRepository = $transactionRepository;
    }

    public function index()
    {
        $transactions = $this->transactionRepository->getFiltered([], 50);
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

        $data = $request->all();
        $data['user_id'] = $request->user_id ?? auth()->id();
        $data['created_at'] = now();
        $data['updated_at'] = now();
        $data['parameters'] = $request->parameters ?? [];
        $data['gas_used'] = $request->gas_used ?? 0;

        $transaction = $this->transactionRepository->create($data);

        return response()->json([
            'status' => 'success',
            'transaction' => $transaction
        ]);
    }

    public function getTransactions(Request $request)
    {
        $filters = $request->all();
        // Ensure pagination is used unless limit is explicitly set, default perPage is 50.
        $transactions = $this->transactionRepository->getFiltered($filters, 50);
        return response()->json($transactions);
    }

    public function getTransaction(Request $request)
    {
        $transaction = null;
        if ($request->id) {
            $transaction = $this->transactionRepository->findById($request->id, ['*'], ['user', 'wallet']);
        } elseif ($request->hash) {
            $transaction = $this->transactionRepository->findByHash($request->hash);
        } else {
            return response()->json(['error' => 'Transaction identifier not provided'], 400);
        }
        
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

        $data = $request->except(['id']);
        $data['updated_at'] = now();

        $transaction = $this->transactionRepository->update($request->id, $data);

        return response()->json([
            'status' => 'success',
            'transaction' => $transaction
        ]);
    }

    public function getTransactionStats(Request $request)
    {
        $period = $request->period ?? '30d';
        $stats = $this->transactionRepository->getStats($period);
        return response()->json($stats);
    }

    public function getPendingTransactions()
    {
        // Using limit to prevent memory issues, similar to original get() but safer.
        $transactions = $this->transactionRepository->getFiltered(['status' => 'pending', 'limit' => 1000]);
        return response()->json($transactions);
    }

    public function getConfirmedTransactions(Request $request)
    {
        $limit = $request->limit ?? 100;
        $transactions = $this->transactionRepository->getFiltered(['status' => 'confirmed', 'limit' => $limit]);
        return response()->json($transactions);
    }

    public function getFailedTransactions(Request $request)
    {
        $limit = $request->limit ?? 100;
        $transactions = $this->transactionRepository->getFiltered(['status' => 'failed', 'limit' => $limit]);
        return response()->json($transactions);
    }

    public function getWalletTransactions(Request $request)
    {
        // Use getFiltered with 'address' which matches from OR to.
        $transactions = $this->transactionRepository->getFiltered(['address' => $request->address], 50);
        return response()->json($transactions);
    }

    public function getContractTransactions(Request $request)
    {
        $transactions = $this->transactionRepository->getFiltered(['contract_address' => $request->contract_address], 50);
        return response()->json($transactions);
    }

    public function getTransactionHistory(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        // Filter by address (from/to) and date range.
        // Use getFiltered with 'address' and 'date_from'.
        // Limit not specified in original, using get(). So pass 'limit' => 1000 (safe limit).
        
        $filters = [
            'address' => $request->address,
            'date_from' => $startDate,
            'limit' => 1000 // Implicit limit for safety
        ];

        $transactions = $this->transactionRepository->getFiltered($filters);
        return response()->json($transactions);
    }

    public function searchTransactions(Request $request)
    {
        $filters = $request->all();
        // Ensure search filter is passed
        if ($request->search) {
            $filters['search'] = $request->search;
        }
        $transactions = $this->transactionRepository->getFiltered($filters, 50);
        return response()->json($transactions);
    }

    public function exportTransactions(Request $request)
    {
        $limit = $request->limit ?? 1000;
        $filters = ['limit' => $limit];
        $transactions = $this->transactionRepository->getFiltered($filters);

        if ($request->format === 'csv') {
            return $this->exportTransactionsToCsv($transactions);
        }

        return response()->json($transactions);
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
                    $transaction->created_at
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
