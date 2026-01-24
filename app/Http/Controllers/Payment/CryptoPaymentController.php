<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\CryptoTransaction;
use App\Models\CryptoWallet;
use App\Models\Payment;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CryptoPaymentController extends Controller
{
    public function index(Request $request)
    {
        $transactions = CryptoTransaction::with(['user', 'wallet', 'payment'])
            ->when($request->search, function ($query, $search) {
                $query->where('transaction_hash', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->blockchain, function ($query, $blockchain) {
                $query->where('blockchain', $blockchain);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('payments.crypto.index', compact('transactions'));
    }

    public function create()
    {
        return view('payments.crypto.create');
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'wallet_id' => 'required|exists:crypto_wallets,id',
            'amount' => 'required|numeric|min:0.00000001',
            'currency' => 'required|string|max:10',
            'recipient_address' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'gas_price' => 'nullable|numeric|min:0',
            'gas_limit' => 'nullable|integer|min:21000',
            'payment_purpose' => 'required|in:purchase,transfer,investment,other',
            'metadata' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $wallet = CryptoWallet::findOrFail($request->wallet_id);
            
            // Check wallet ownership
            if ($wallet->user_id !== Auth::id()) {
                return back()->with('error', 'Unauthorized wallet access');
            }

            // Check balance
            if ($wallet->balance < $request->amount) {
                return back()->with('error', 'Insufficient balance');
            }

            // Validate recipient address
            if (!$this->isValidAddress($request->recipient_address, $wallet->blockchain)) {
                return back()->with('error', 'Invalid recipient address');
            }

            // Create crypto transaction
            $cryptoTransaction = CryptoTransaction::create([
                'user_id' => Auth::id(),
                'wallet_id' => $wallet->id,
                'from_address' => $wallet->address,
                'to_address' => $request->recipient_address,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'blockchain' => $wallet->blockchain,
                'gas_price' => $request->gas_price,
                'gas_limit' => $request->gas_limit,
                'description' => $request->description,
                'payment_purpose' => $request->payment_purpose,
                'metadata' => $request->metadata ?? [],
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            // Process transaction (mock implementation)
            $result = $this->processCryptoPayment($cryptoTransaction);

            if ($result['success']) {
                $cryptoTransaction->update([
                    'status' => 'completed',
                    'transaction_hash' => $result['transaction_hash'],
                    'block_number' => $result['block_number'],
                    'gas_used' => $result['gas_used'],
                    'gas_fee' => $result['gas_fee'],
                    'confirmations' => 1,
                    'completed_at' => now(),
                ]);

                // Update wallet balance
                $newBalance = $wallet->balance - $request->amount - $result['gas_fee'];
                $wallet->update(['balance' => $newBalance]);

                // Create payment record if applicable
                if ($request->payment_purpose === 'purchase') {
                    $payment = Payment::create([
                        'user_id' => Auth::id(),
                        'crypto_transaction_id' => $cryptoTransaction->id,
                        'amount' => $request->amount,
                        'currency' => $request->currency,
                        'reference' => 'CRYPTO-' . uniqid(),
                        'description' => $request->description,
                        'status' => 'completed',
                        'completed_at' => now(),
                        'created_by' => Auth::id(),
                    ]);

                    $cryptoTransaction->update(['payment_id' => $payment->id]);
                }

                UserActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'processed_crypto_payment',
                    'details' => "Processed crypto payment of {$request->amount} {$request->currency}",
                    'ip_address' => $request->ip(),
                ]);

                DB::commit();

                return redirect()->route('payments.crypto.show', $cryptoTransaction)
                    ->with('success', 'Crypto payment processed successfully.');
            } else {
                $cryptoTransaction->update([
                    'status' => 'failed',
                    'error_message' => $result['message'],
                    'failed_at' => now(),
                ]);

                DB::rollBack();

                return back()->with('error', 'Crypto payment failed: ' . $result['message']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing payment: ' . $e->getMessage());
        }
    }

    public function show(CryptoTransaction $transaction)
    {
        $transaction->load(['user', 'wallet', 'payment']);
        return view('payments.crypto.show', compact('transaction'));
    }

    public function getTransactionStatus(Request $request, $transactionHash): JsonResponse
    {
        try {
            $transaction = CryptoTransaction::where('transaction_hash', $transactionHash)
                ->with(['user', 'wallet'])
                ->firstOrFail();

            // Update confirmations from blockchain (mock implementation)
            $this->updateConfirmations($transaction);

            return response()->json([
                'success' => true,
                'transaction' => $transaction,
                'status' => $transaction->status,
                'confirmations' => $transaction->confirmations,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    public function receivePayment(Request $request): JsonResponse
    {
        $request->validate([
            'wallet_id' => 'required|exists:crypto_wallets,id',
            'amount' => 'required|numeric|min:0.00000001',
            'currency' => 'required|string|max:10',
            'sender_address' => 'required|string|max:255',
            'transaction_hash' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $wallet = CryptoWallet::findOrFail($request->wallet_id);
            
            // Check wallet ownership
            if ($wallet->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized wallet access'
                ], 403);
            }

            // Check if transaction already exists
            $existingTransaction = CryptoTransaction::where('transaction_hash', $request->transaction_hash)
                ->first();

            if ($existingTransaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction already processed'
                ], 400);
            }

            // Create receive transaction
            $transaction = CryptoTransaction::create([
                'user_id' => Auth::id(),
                'wallet_id' => $wallet->id,
                'from_address' => $request->sender_address,
                'to_address' => $wallet->address,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'blockchain' => $wallet->blockchain,
                'transaction_hash' => $request->transaction_hash,
                'description' => $request->description,
                'type' => 'receive',
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            // Update wallet balance
            $newBalance = $wallet->balance + $request->amount;
            $wallet->update(['balance' => $newBalance]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'received_crypto_payment',
                'details' => "Received {$request->amount} {$request->currency} in wallet {$wallet->name}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'transaction' => $transaction,
                'new_balance' => $newBalance,
                'message' => 'Crypto payment received successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error receiving payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getExchangeRates(Request $request): JsonResponse
    {
        try {
            $currencies = $request->currencies ?? ['BTC', 'ETH', 'USDT', 'USDC'];
            $baseCurrency = $request->base_currency ?? 'USD';
            
            $rates = [];
            foreach ($currencies as $currency) {
                $rates[$currency] = $this->getExchangeRate($baseCurrency, $currency);
            }
            
            return response()->json([
                'success' => true,
                'base_currency' => $baseCurrency,
                'rates' => $rates,
                'timestamp' => now(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting exchange rates: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getWalletBalance(Request $request, $walletId): JsonResponse
    {
        try {
            $wallet = CryptoWallet::where('id', $walletId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Get real-time balance (mock implementation)
            $balance = $this->getRealtimeBalance($wallet);

            $wallet->update([
                'balance' => $balance,
                'balance_updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'wallet_id' => $wallet->id,
                'balance' => $balance,
                'formatted_balance' => number_format($balance, 8),
                'currency' => $wallet->currency,
                'updated_at' => $wallet->balance_updated_at,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching balance: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTransactionHistory(Request $request): JsonResponse
    {
        $request->validate([
            'wallet_id' => 'nullable|exists:crypto_wallets,id',
            'limit' => 'nullable|integer|min:1|max:100',
            'status' => 'nullable|in:pending,completed,failed',
        ]);

        try {
            $query = CryptoTransaction::where('user_id', Auth::id())
                ->with(['wallet']);

            if ($request->wallet_id) {
                $query->where('wallet_id', $request->wallet_id);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $transactions = $query->latest('created_at')
                ->limit($request->limit ?? 50)
                ->get();

            return response()->json([
                'success' => true,
                'transactions' => $transactions,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching transactions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCryptoStats(): JsonResponse
    {
        $transactions = CryptoTransaction::where('user_id', Auth::id())->get();
        $wallets = CryptoWallet::where('user_id', Auth::id())->get();

        $stats = [
            'total_transactions' => $transactions->count(),
            'sent_transactions' => $transactions->where('type', 'send')->count(),
            'received_transactions' => $transactions->where('type', 'receive')->count(),
            'completed_transactions' => $transactions->where('status', 'completed')->count(),
            'pending_transactions' => $transactions->where('status', 'pending')->count(),
            'failed_transactions' => $transactions->where('status', 'failed')->count(),
            'total_sent' => $transactions->where('type', 'send')->sum('amount'),
            'total_received' => $transactions->where('type', 'receive')->sum('amount'),
            'total_gas_fees' => $transactions->sum('gas_fee'),
            'total_wallet_balance' => $wallets->sum('balance'),
            'by_currency' => $transactions->groupBy('currency')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_amount' => $group->sum('amount'),
                    ];
                }),
            'by_blockchain' => $transactions->groupBy('blockchain')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_amount' => $group->sum('amount'),
                    ];
                }),
            'monthly_stats' => $transactions->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count, SUM(amount) as total')
                ->where('created_at', '>=', now()->subYear())
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    private function isValidAddress($address, $blockchain)
    {
        // Mock validation - implement proper validation for each blockchain
        $patterns = [
            'ethereum' => '/^0x[a-fA-F0-9]{40}$/',
            'polygon' => '/^0x[a-fA-F0-9]{40}$/',
            'binance' => '/^0x[a-fA-F0-9]{40}$/',
            'avalanche' => '/^0x[a-fA-F0-9]{40}$/',
            'solana' => '/^[1-9A-HJ-NP-Za-km-z]{32,44}$/',
            'bitcoin' => '/^[13][a-km-zA-HJ-NP-Z1-9]{25,34}$|^bc1[a-z0-9]{39,59}$/',
        ];

        return preg_match($patterns[$blockchain] ?? $patterns['ethereum'], $address);
    }

    private function processCryptoPayment($transaction)
    {
        // Mock implementation - integrate with actual blockchain API
        return [
            'success' => true,
            'transaction_hash' => '0x' . bin2hex(random_bytes(32)),
            'block_number' => rand(1000000, 9999999),
            'gas_used' => rand(21000, 100000),
            'gas_fee' => 0.001,
        ];
    }

    private function updateConfirmations($transaction)
    {
        // Mock implementation - check blockchain for confirmations
        if ($transaction->status === 'completed') {
            $transaction->update(['confirmations' => rand(1, 100)]);
        }
    }

    private function getExchangeRate($fromCurrency, $toCurrency)
    {
        // Mock implementation - use real API like CoinGecko, CoinMarketCap
        $rates = [
            'BTC-USD' => 45000,
            'ETH-USD' => 3000,
            'USDT-USD' => 1.0,
            'USDC-USD' => 1.0,
        ];

        $key = strtoupper($fromCurrency . '-' . $toCurrency);
        return $rates[$key] ?? 1.0;
    }

    private function getRealtimeBalance($wallet)
    {
        // Mock implementation - use blockchain API
        return $wallet->balance; // Return current balance for demo
    }
}
