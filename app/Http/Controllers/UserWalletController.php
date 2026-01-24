<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserWallet;
use App\Models\WalletTransaction;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserWalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wallet = $user->wallet;
        
        if (!$wallet) {
            $wallet = UserWallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'currency' => 'USD',
                'status' => 'active',
            ]);
        }

        $transactions = $wallet->transactions()
            ->latest()
            ->paginate(20);

        return view('user.wallet', compact('wallet', 'transactions'));
    }

    public function show()
    {
        $user = Auth::user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return redirect()->route('user.wallet.index')
                ->with('info', 'Your wallet will be created automatically when you make your first transaction.');
        }

        $wallet->load(['transactions' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return view('user.wallet-details', compact('wallet'));
    }

    public function deposit(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
            'payment_method' => 'required|in:credit_card,bank_transfer,paypal,crypto',
            'description' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        
        DB::beginTransaction();
        
        try {
            $wallet = $user->wallet ?? UserWallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'currency' => 'USD',
                'status' => 'active',
            ]);

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $request->amount,
                'balance_before' => $wallet->balance,
                'balance_after' => $wallet->balance + $request->amount,
                'payment_method' => $request->payment_method,
                'description' => $request->description ?? 'Wallet deposit',
                'status' => 'pending',
                'reference_id' => 'DEP_' . strtoupper(uniqid()),
            ]);

            $wallet->update(['balance' => $wallet->balance + $request->amount]);

            UserActivityLog::create([
                'user_id' => $user->id,
                'action' => 'wallet_deposit',
                'details' => "Deposited {$request->amount} {$wallet->currency} to wallet",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Deposit processed successfully',
                'transaction' => $transaction,
                'new_balance' => $wallet->balance
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process deposit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function withdraw(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
            'payment_method' => 'required|in:bank_transfer,paypal,crypto',
            'description' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $wallet = $user->wallet;

        if (!$wallet || $wallet->balance < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance'
            ], 400);
        }

        DB::beginTransaction();
        
        try {
            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'withdrawal',
                'amount' => $request->amount,
                'balance_before' => $wallet->balance,
                'balance_after' => $wallet->balance - $request->amount,
                'payment_method' => $request->payment_method,
                'description' => $request->description ?? 'Wallet withdrawal',
                'status' => 'pending',
                'reference_id' => 'WTH_' . strtoupper(uniqid()),
            ]);

            $wallet->update(['balance' => $wallet->balance - $request->amount]);

            UserActivityLog::create([
                'user_id' => $user->id,
                'action' => 'wallet_withdrawal',
                'details' => "Withdrew {$request->amount} {$wallet->currency} from wallet",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal processed successfully',
                'transaction' => $transaction,
                'new_balance' => $wallet->balance
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process withdrawal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function transfer(Request $request): JsonResponse
    {
        $request->validate([
            'recipient_email' => 'required|email|exists:users,email',
            'amount' => 'required|numeric|min:1|max:10000',
            'description' => 'nullable|string|max:255',
        ]);

        $sender = Auth::user();
        $senderWallet = $sender->wallet;

        if (!$senderWallet || $senderWallet->balance < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance'
            ], 400);
        }

        $recipient = User::where('email', $request->recipient_email)->first();
        $recipientWallet = $recipient->wallet ?? UserWallet::create([
            'user_id' => $recipient->id,
            'balance' => 0,
            'currency' => 'USD',
            'status' => 'active',
        ]);

        if ($sender->id === $recipient->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot transfer to yourself'
            ], 400);
        }

        DB::beginTransaction();
        
        try {
            // Create sender transaction
            $senderTransaction = WalletTransaction::create([
                'wallet_id' => $senderWallet->id,
                'type' => 'transfer_out',
                'amount' => $request->amount,
                'balance_before' => $senderWallet->balance,
                'balance_after' => $senderWallet->balance - $request->amount,
                'description' => $request->description ?? "Transfer to {$recipient->name}",
                'status' => 'completed',
                'reference_id' => 'TRF_OUT_' . strtoupper(uniqid()),
                'recipient_id' => $recipient->id,
            ]);

            // Create recipient transaction
            $recipientTransaction = WalletTransaction::create([
                'wallet_id' => $recipientWallet->id,
                'type' => 'transfer_in',
                'amount' => $request->amount,
                'balance_before' => $recipientWallet->balance,
                'balance_after' => $recipientWallet->balance + $request->amount,
                'description' => $request->description ?? "Transfer from {$sender->name}",
                'status' => 'completed',
                'reference_id' => 'TRF_IN_' . strtoupper(uniqid()),
                'sender_id' => $sender->id,
            ]);

            // Update balances
            $senderWallet->update(['balance' => $senderWallet->balance - $request->amount]);
            $recipientWallet->update(['balance' => $recipientWallet->balance + $request->amount]);

            // Log activities
            UserActivityLog::create([
                'user_id' => $sender->id,
                'action' => 'wallet_transfer_out',
                'details' => "Transferred {$request->amount} {$senderWallet->currency} to {$recipient->name}",
                'ip_address' => $request->ip(),
            ]);

            UserActivityLog::create([
                'user_id' => $recipient->id,
                'action' => 'wallet_transfer_in',
                'details' => "Received {$request->amount} {$recipientWallet->currency} from {$sender->name}",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transfer completed successfully',
                'transaction' => $senderTransaction,
                'new_balance' => $senderWallet->balance
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process transfer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getBalance(): JsonResponse
    {
        $user = Auth::user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return response()->json([
                'success' => true,
                'balance' => 0,
                'currency' => 'USD',
                'status' => 'no_wallet'
            ]);
        }

        return response()->json([
            'success' => true,
            'balance' => $wallet->balance,
            'currency' => $wallet->currency,
            'status' => $wallet->status,
            'last_transaction' => $wallet->transactions()->latest()->first()
        ]);
    }

    public function getTransactions(Request $request): JsonResponse
    {
        $user = Auth::user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return response()->json([
                'success' => true,
                'transactions' => []
            ]);
        }

        $transactions = $wallet->transactions()
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }

    public function getTransaction(WalletTransaction $transaction): JsonResponse
    {
        $user = Auth::user();
        
        if ($transaction->wallet->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'transaction' => $transaction
        ]);
    }

    public function getStats(): JsonResponse
    {
        $user = Auth::user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return response()->json([
                'success' => true,
                'stats' => [
                    'total_deposits' => 0,
                    'total_withdrawals' => 0,
                    'total_transfers_in' => 0,
                    'total_transfers_out' => 0,
                    'net_balance' => 0,
                    'transaction_count' => 0,
                ]
            ]);
        }

        $stats = [
            'total_deposits' => $wallet->transactions()->where('type', 'deposit')->sum('amount'),
            'total_withdrawals' => $wallet->transactions()->where('type', 'withdrawal')->sum('amount'),
            'total_transfers_in' => $wallet->transactions()->where('type', 'transfer_in')->sum('amount'),
            'total_transfers_out' => $wallet->transactions()->where('type', 'transfer_out')->sum('amount'),
            'net_balance' => $wallet->balance,
            'transaction_count' => $wallet->transactions()->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
