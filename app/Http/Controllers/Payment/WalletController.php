<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\CryptoWallet;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $wallets = CryptoWallet::where('user_id', Auth::id())
            ->when($request->blockchain, function ($query, $blockchain) {
                $query->where('blockchain', $blockchain);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('payments.wallets.index', compact('wallets'));
    }

    public function create()
    {
        return view('payments.wallets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'blockchain' => 'required|in:ethereum,polygon,binance,avalanche,solana,bitcoin',
            'address' => 'required|string|max:255',
            'private_key' => 'nullable|string|max:1000',
            'mnemonic' => 'nullable|string|max:1000',
            'wallet_type' => 'required|in:hot,cold,hardware,exchange',
            'currency' => 'required|string|max:10',
            'is_default' => 'boolean',
            'description' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
        ]);

        try {
            // Validate wallet address format based on blockchain
            if (!$this->isValidWalletAddress($request->address, $request->blockchain)) {
                return back()->with('error', 'Invalid wallet address format for selected blockchain.');
            }

            $wallet = CryptoWallet::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'blockchain' => $request->blockchain,
                'address' => $request->address,
                'private_key' => $request->private_key ? encrypt($request->private_key) : null,
                'mnemonic' => $request->mnemonic ? encrypt($request->mnemonic) : null,
                'wallet_type' => $request->wallet_type,
                'currency' => $request->currency,
                'is_default' => $request->is_default ?? false,
                'description' => $request->description,
                'metadata' => $request->metadata ?? [],
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);

            // If this is set as default, unset other defaults
            if ($wallet->is_default) {
                CryptoWallet::where('user_id', Auth::id())
                    ->where('id', '!=', $wallet->id)
                    ->update(['is_default' => false]);
            }

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created_crypto_wallet',
                'details' => "Created crypto wallet: {$wallet->name} ({$wallet->blockchain})",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('payments.wallets.show', $wallet)
                ->with('success', 'Crypto wallet created successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error creating wallet: ' . $e->getMessage());
        }
    }

    public function show(CryptoWallet $wallet)
    {
        $this->authorize('view', $wallet);
        return view('payments.wallets.show', compact('wallet'));
    }

    public function edit(CryptoWallet $wallet)
    {
        $this->authorize('update', $wallet);
        return view('payments.wallets.edit', compact('wallet'));
    }

    public function update(Request $request, CryptoWallet $wallet)
    {
        $this->authorize('update', $wallet);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_default' => 'boolean',
            'status' => 'required|in:active,inactive,frozen',
        ]);

        try {
            $wallet->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_default' => $request->is_default ?? false,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);

            // If this is set as default, unset other defaults
            if ($wallet->is_default) {
                CryptoWallet::where('user_id', Auth::id())
                    ->where('id', '!=', $wallet->id)
                    ->update(['is_default' => false]);
            }

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated_crypto_wallet',
                'details' => "Updated crypto wallet: {$wallet->name}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('payments.wallets.index')
                ->with('success', 'Wallet updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating wallet: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, CryptoWallet $wallet)
    {
        $this->authorize('delete', $wallet);

        try {
            $wallet->update([
                'status' => 'deleted',
                'deleted_at' => now(),
                'deleted_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted_crypto_wallet',
                'details' => "Deleted crypto wallet: {$wallet->name}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wallet deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting wallet: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBalance(Request $request, CryptoWallet $wallet): JsonResponse
    {
        $this->authorize('view', $wallet);

        try {
            // Get balance from blockchain (mock implementation)
            $balance = $this->getWalletBalance($wallet);
            
            $wallet->update([
                'balance' => $balance,
                'balance_updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
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

    public function getTransactionHistory(Request $request, CryptoWallet $wallet): JsonResponse
    {
        $this->authorize('view', $wallet);

        $request->validate([
            'limit' => 'nullable|integer|min:1|max:100',
            'from_block' => 'nullable|integer|min:0',
            'to_block' => 'nullable|integer|min:from_block',
        ]);

        try {
            // Get transaction history from blockchain (mock implementation)
            $transactions = $this->getWalletTransactions($wallet, $request->limit ?? 50);
            
            return response()->json([
                'success' => true,
                'transactions' => $transactions,
                'wallet_address' => $wallet->address,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching transactions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendTransaction(Request $request, CryptoWallet $wallet): JsonResponse
    {
        $this->authorize('update', $wallet);

        $request->validate([
            'to_address' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.00000001',
            'gas_price' => 'nullable|numeric|min:0',
            'gas_limit' => 'nullable|integer|min:21000',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            // Validate recipient address
            if (!$this->isValidWalletAddress($request->to_address, $wallet->blockchain)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid recipient address format'
                ], 400);
            }

            // Check balance
            if ($wallet->balance < $request->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance'
                ], 400);
            }

            DB::beginTransaction();

            // Create transaction record
            $transaction = $wallet->transactions()->create([
                'from_address' => $wallet->address,
                'to_address' => $request->to_address,
                'amount' => $request->amount,
                'gas_price' => $request->gas_price,
                'gas_limit' => $request->gas_limit,
                'notes' => $request->notes,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            // Send transaction (mock implementation)
            $result = $this->sendCryptoTransaction($wallet, $transaction);

            if ($result['success']) {
                $transaction->update([
                    'status' => 'completed',
                    'transaction_hash' => $result['transaction_hash'],
                    'block_number' => $result['block_number'],
                    'gas_used' => $result['gas_used'],
                    'completed_at' => now(),
                ]);

                // Update wallet balance
                $newBalance = $wallet->balance - $request->amount - $result['gas_fee'];
                $wallet->update(['balance' => $newBalance]);

                UserActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'sent_crypto_transaction',
                    'details' => "Sent {$request->amount} {$wallet->currency} from wallet {$wallet->name}",
                    'ip_address' => $request->ip(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'transaction' => $transaction,
                    'new_balance' => $newBalance,
                    'message' => 'Transaction sent successfully'
                ]);
            } else {
                $transaction->update([
                    'status' => 'failed',
                    'error_message' => $result['message'],
                    'failed_at' => now(),
                ]);

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Transaction failed: ' . $result['message']
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error sending transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getWalletStats(): JsonResponse
    {
        $wallets = CryptoWallet::where('user_id', Auth::id())->get();
        
        $stats = [
            'total_wallets' => $wallets->count(),
            'active_wallets' => $wallets->where('status', 'active')->count(),
            'total_balance' => $wallets->sum('balance'),
            'by_blockchain' => $wallets->groupBy('blockchain')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'balance' => $group->sum('balance'),
                    ];
                }),
            'by_currency' => $wallets->groupBy('currency')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'balance' => $group->sum('balance'),
                    ];
                }),
            'by_type' => $wallets->groupBy('wallet_type')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'balance' => $group->sum('balance'),
                    ];
                }),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function setDefault(Request $request, CryptoWallet $wallet): JsonResponse
    {
        $this->authorize('update', $wallet);

        try {
            // Unset all other defaults for this user
            CryptoWallet::where('user_id', Auth::id())
                ->update(['is_default' => false]);

            // Set this as default
            $wallet->update([
                'is_default' => true,
                'updated_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'set_default_wallet',
                'details' => "Set wallet {$wallet->name} as default",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Default wallet updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error setting default wallet: ' . $e->getMessage()
            ], 500);
        }
    }

    private function isValidWalletAddress($address, $blockchain)
    {
        // Mock validation - in real implementation, use proper validation for each blockchain
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

    private function getWalletBalance($wallet)
    {
        // Mock implementation - in real implementation, use blockchain API
        return rand(0.0001, 1000); // Random balance for demo
    }

    private function getWalletTransactions($wallet, $limit = 50)
    {
        // Mock implementation - in real implementation, use blockchain API
        return [];
    }

    private function sendCryptoTransaction($wallet, $transaction)
    {
        // Mock implementation - in real implementation, use blockchain API
        return [
            'success' => true,
            'transaction_hash' => '0x' . bin2hex(random_bytes(32)),
            'block_number' => rand(1000000, 9999999),
            'gas_used' => rand(21000, 100000),
            'gas_fee' => 0.001,
        ];
    }
}
