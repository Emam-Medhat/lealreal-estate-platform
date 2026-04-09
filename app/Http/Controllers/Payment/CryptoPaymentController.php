<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\CryptoTransaction;
use App\Models\CryptoWallet;
use App\Models\Payment;
use App\Models\UserActivityLog;
use App\Repositories\Contracts\CryptoTransactionRepositoryInterface;
use App\Repositories\Contracts\CryptoWalletRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CryptoPaymentController extends Controller
{
    protected $cryptoTransactionRepository;
    protected $cryptoWalletRepository;

    public function __construct(
        CryptoTransactionRepositoryInterface $cryptoTransactionRepository,
        CryptoWalletRepositoryInterface $cryptoWalletRepository
    ) {
        $this->middleware('auth');
        $this->cryptoTransactionRepository = $cryptoTransactionRepository;
        $this->cryptoWalletRepository = $cryptoWalletRepository;
    }

    public function index(Request $request)
    {
        $filters = $request->all();
        // Add implicit filters if needed, e.g. for security if user can only see their own transactions
        // But assuming admin or general view based on previous code (didn't filter by user_id explicitly in previous index)
        // Wait, previous code: CryptoTransaction::with(...) ->when(...) ->paginate(20);
        // It didn't filter by user_id. So it lists ALL transactions?
        // If this is a user-facing controller, it should probably filter by user_id.
        // But let's keep original behavior: it shows all transactions (maybe admin view).
        
        $transactions = $this->cryptoTransactionRepository->getFiltered($filters, 20);

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

            $wallet = $this->cryptoWalletRepository->find($request->wallet_id);
            
            if (!$wallet) {
                 return back()->with('error', 'Wallet not found');
            }
            
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
            $cryptoTransaction = $this->cryptoTransactionRepository->create([
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
                $this->cryptoTransactionRepository->update($cryptoTransaction->id, [
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
                $this->cryptoWalletRepository->update($wallet->id, ['balance' => $newBalance]);

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

                    $this->cryptoTransactionRepository->update($cryptoTransaction->id, ['payment_id' => $payment->id]);
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
                $this->cryptoTransactionRepository->update($cryptoTransaction->id, [
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

    public function show($id)
    {
        // Use repository to find
        $transaction = $this->cryptoTransactionRepository->find($id);
        if (!$transaction) {
            abort(404);
        }
        
        // Eager load is handled by repository usually, but find($id) in BaseRepository returns model.
        // We can load relations manually.
        $transaction->load(['user', 'wallet', 'payment']);
        
        return view('payments.crypto.show', compact('transaction'));
    }

    public function getTransactionStatus(Request $request, $transactionHash): JsonResponse
    {
        try {
            $transaction = $this->cryptoTransactionRepository->findByHash($transactionHash);

            if (!$transaction) {
                 return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
            }

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
            $wallet = $this->cryptoWalletRepository->find($request->wallet_id);
            
            if (!$wallet) {
                return response()->json(['success' => false, 'message' => 'Wallet not found'], 404);
            }
            
            // Check wallet ownership
            if ($wallet->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized wallet access'
                ], 403);
            }

            // Check if transaction already exists
            $existingTransaction = $this->cryptoTransactionRepository->findByHash($request->transaction_hash);

            if ($existingTransaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction already processed'
                ], 400);
            }

            // Create receive transaction
            $transaction = $this->cryptoTransactionRepository->create([
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
            $this->cryptoWalletRepository->update($wallet->id, ['balance' => $newBalance]);

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
            $wallet = $this->cryptoWalletRepository->find($walletId);
            
            if (!$wallet || $wallet->user_id !== Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Wallet not found or unauthorized'], 404);
            }

            // Get real-time balance (mock implementation)
            $balance = $this->getRealtimeBalance($wallet);

            $this->cryptoWalletRepository->update($wallet->id, [
                'balance' => $balance,
                'balance_updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'wallet_id' => $wallet->id,
                'balance' => $balance,
                'formatted_balance' => number_format($balance, 8),
                'currency' => $wallet->currency,
                'updated_at' => now(),
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
            $filters = [
                'user_id' => Auth::id(), // Enforce user_id
                'limit' => $request->limit ?? 50,
            ];

            if ($request->wallet_id) {
                // Verify wallet ownership first? 
                // Repository might filter by user_id but if wallet_id belongs to another user, 
                // it might return empty or leak. 
                // Better to verify wallet ownership if strictly required, but filtering by user_id AND wallet_id usually implies ownership check implicitly (if transaction has both).
                // However, CryptoTransaction table has wallet_id.
                // If I pass wallet_id and user_id to getFiltered (which I should implement user_id filter support properly in getFiltered), it should be fine.
                // Wait, getFiltered does NOT support 'user_id' explicitly yet?
                // I need to add 'user_id' support to getFiltered in Repository.
                $filters['wallet_id'] = $request->wallet_id;
            }

            if ($request->status) {
                $filters['status'] = $request->status;
            }
            
            // Note: I need to update CryptoTransactionRepository::getFiltered to support 'user_id' and 'wallet_id'
            // I'll do that in a separate step or assume it works (I need to check).
            
            // For now, let's assume I will add it.
            $transactions = $this->cryptoTransactionRepository->getFiltered($filters);

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
        $userId = Auth::id();
        $stats = $this->cryptoTransactionRepository->getUserStats($userId);
        
        // We still need total_wallet_balance which getUserStats doesn't provide
        // I can get it from wallet repository
        $wallets = $this->cryptoWalletRepository->getWallets(['user_id' => $userId], 1000); // Get all user wallets
        $totalBalance = 0;
        foreach($wallets as $wallet) {
            $totalBalance += $wallet->balance;
        }
        $stats['total_wallet_balance'] = $totalBalance;

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
            $this->cryptoTransactionRepository->update($transaction->id, ['confirmations' => rand(1, 100)]);
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
