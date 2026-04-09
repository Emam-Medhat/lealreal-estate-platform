<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserWallet;
use App\Models\UserTransaction;
use App\Models\UserActivityLog;
use App\Services\NotificationService;
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

            $transaction = UserTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'transaction_id' => 'TXN_' . strtoupper(uniqid()),
                'transaction_type' => 'deposit',
                'amount' => $request->amount,
                'currency' => $wallet->currency,
                'fee' => 0,
                'total_amount' => $request->amount,
                'status' => 'completed',
                'description' => $request->description ?? 'Deposit to wallet',
                'completed_at' => now(),
            ]);

            $wallet->update(['balance' => $wallet->balance + $request->amount]);

            UserActivityLog::create([
                'user_id' => $user->id,
                'action' => 'wallet_deposit',
                'description' => "Deposited {$request->amount} {$wallet->currency} to wallet",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'amount' => $request->amount,
                    'currency' => $wallet->currency,
                    'payment_method' => $request->payment_method,
                    'transaction_id' => $transaction->id,
                ],
            ]);

            // Send notification
            NotificationService::walletNotification($user, 'deposit', [
                'amount' => $request->amount,
                'currency' => $wallet->currency,
                'payment_method' => $request->payment_method,
                'transaction_id' => $transaction->id,
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
            
            \Log::error('Deposit failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'amount' => $request->amount ?? null,
                'payment_method' => $request->payment_method ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process deposit: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function withdraw(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
            'payment_method' => 'required|in:bank_transfer,paypal,crypto,check',
            'description' => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:255',
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
            $transaction = UserTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'transaction_id' => 'TXN_' . strtoupper(uniqid()),
                'transaction_type' => 'withdrawal',
                'amount' => $request->amount,
                'currency' => $wallet->currency,
                'fee' => 0,
                'total_amount' => $request->amount,
                'status' => 'completed',
                'description' => $request->reason ?? $request->description ?? 'Withdrawal from wallet',
                'completed_at' => now(),
            ]);

            $wallet->update(['balance' => $wallet->balance - $request->amount]);

            UserActivityLog::create([
                'user_id' => $user->id,
                'action' => 'wallet_withdrawal',
                'description' => "Withdrew {$request->amount} {$wallet->currency} from wallet",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'amount' => $request->amount,
                    'currency' => $wallet->currency,
                    'payment_method' => $request->payment_method,
                    'transaction_id' => $transaction->id,
                ],
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
            $senderTransaction = UserTransaction::create([
                'user_id' => $sender->id,
                'wallet_id' => $senderWallet->id,
                'transaction_id' => 'TXN_' . strtoupper(uniqid()),
                'transaction_type' => 'transfer',
                'amount' => $request->amount,
                'currency' => $senderWallet->currency,
                'fee' => 0,
                'total_amount' => $request->amount,
                'status' => 'completed',
                'description' => $request->description ?? "Transfer to {$recipient->name}",
                'completed_at' => now(),
            ]);

            // Create recipient transaction
            $recipientTransaction = UserTransaction::create([
                'user_id' => $recipient->id,
                'wallet_id' => $recipientWallet->id,
                'transaction_id' => 'TXN_' . strtoupper(uniqid()),
                'transaction_type' => 'transfer',
                'amount' => $request->amount,
                'currency' => $recipientWallet->currency,
                'fee' => 0,
                'total_amount' => $request->amount,
                'status' => 'completed',
                'description' => $request->description ?? "Transfer from {$sender->name}",
                'completed_at' => now(),
            ]);

            // Update balances
            $senderWallet->update(['balance' => $senderWallet->balance - $request->amount]);
            $recipientWallet->update(['balance' => $recipientWallet->balance + $request->amount]);

            // Log activities
            UserActivityLog::create([
                'user_id' => $sender->id,
                'action' => 'wallet_transfer_out',
                'description' => "Transferred {$request->amount} {$senderWallet->currency} to {$recipient->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'amount' => $request->amount,
                    'currency' => $senderWallet->currency,
                    'recipient_id' => $recipient->id,
                    'recipient_name' => $recipient->name,
                    'transaction_id' => $senderTransaction->id,
                ],
            ]);

            UserActivityLog::create([
                'user_id' => $recipient->id,
                'action' => 'wallet_transfer_in',
                'description' => "Received {$request->amount} {$recipientWallet->currency} from {$sender->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'amount' => $request->amount,
                    'currency' => $recipientWallet->currency,
                    'sender_id' => $sender->id,
                    'sender_name' => $sender->name,
                    'transaction_id' => $recipientTransaction->id,
                ],
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
                $query->where('transaction_type', $type);
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

    public function getTransaction(UserTransaction $transaction): JsonResponse
    {
        $user = Auth::user();
        
        if ($transaction->user_id !== $user->id) {
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

    public function exportTransactions(Request $request)
    {
        $user = Auth::user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'No wallet found'
            ], 404);
        }

        $transactions = $wallet->transactions()
            ->when($request->type, function ($query, $type) {
                $query->where('transaction_type', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->date_from, function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest()
            ->get();

        $filename = 'wallet_transactions_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'Transaction ID',
                'Type',
                'Amount',
                'Currency',
                'Status',
                'Description',
                'Created At',
                'Completed At'
            ]);
            
            // CSV Data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_id,
                    $transaction->transaction_type,
                    $transaction->amount,
                    $transaction->currency,
                    $transaction->status,
                    $transaction->description,
                    $transaction->created_at,
                    $transaction->completed_at
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
            'total_deposits' => $wallet->transactions()->where('transaction_type', 'deposit')->sum('amount'),
            'total_withdrawals' => $wallet->transactions()->where('transaction_type', 'withdrawal')->sum('amount'),
            'total_transfers_in' => $wallet->transactions()->where('transaction_type', 'transfer')->sum('amount'),
            'total_transfers_out' => $wallet->transactions()->where('transaction_type', 'transfer')->sum('amount'),
            'net_balance' => $wallet->balance,
            'transaction_count' => $wallet->transactions()->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
