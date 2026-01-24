<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with(['user', 'payment', 'invoice', 'refund'])
            ->when($request->search, function ($query, $search) {
                $query->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('payments.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'payment', 'invoice', 'refund']);
        return view('payments.transactions.show', compact('transaction'));
    }

    public function create()
    {
        return view('payments.transactions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:payment,refund,withdrawal,deposit,transfer',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'reference' => 'nullable|string|max:255',
            'description' => 'required|string|max:500',
            'metadata' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $transaction = Transaction::create([
                'user_id' => $request->user_id,
                'type' => $request->type,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'reference' => $request->reference ?? $this->generateReference(),
                'description' => $request->description,
                'metadata' => $request->metadata ?? [],
                'notes' => $request->notes,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created_transaction',
                'details' => "Created transaction: {$transaction->reference} - {$transaction->type}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('payments.transactions.show', $transaction)
                ->with('success', 'Transaction created successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error creating transaction: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Transaction $transaction): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,failed,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $transaction->update([
                'status' => $request->status,
                'notes' => $request->notes,
                'updated_by' => Auth::id(),
            ]);

            // Update completion timestamp
            if ($request->status === 'completed') {
                $transaction->update(['completed_at' => now()]);
            }

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated_transaction_status',
                'details' => "Updated transaction {$transaction->reference} status to {$request->status}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'status' => $request->status,
                'message' => 'Transaction status updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reverse(Request $request, Transaction $transaction): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'reverse_amount' => 'required|numeric|min:0.01|max:' . $transaction->amount,
        ]);

        if ($transaction->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Only completed transactions can be reversed'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create reversal transaction
            $reversal = Transaction::create([
                'user_id' => $transaction->user_id,
                'original_transaction_id' => $transaction->id,
                'type' => 'reversal',
                'amount' => $request->reverse_amount,
                'currency' => $transaction->currency,
                'reference' => $this->generateReference('REV'),
                'description' => "Reversal of transaction: {$transaction->reference}",
                'metadata' => [
                    'original_transaction' => $transaction->reference,
                    'reversal_reason' => $request->reason,
                ],
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            // Update original transaction
            $transaction->update([
                'status' => 'reversed',
                'reversed_at' => now(),
                'reversal_reason' => $request->reason,
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'reversed_transaction',
                'details' => "Reversed transaction {$transaction->reference} for amount {$request->reverse_amount}",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'reversal' => $reversal,
                'message' => 'Transaction reversed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error reversing transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTransactionStats(): JsonResponse
    {
        $stats = [
            'total_transactions' => Transaction::count(),
            'completed_transactions' => Transaction::where('status', 'completed')->count(),
            'failed_transactions' => Transaction::where('status', 'failed')->count(),
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
            'total_amount' => Transaction::sum('amount'),
            'completed_amount' => Transaction::where('status', 'completed')->sum('amount'),
            'by_type' => Transaction::groupBy('type')
                ->selectRaw('type, COUNT(*) as count, SUM(amount) as total')
                ->get(),
            'by_status' => Transaction::groupBy('status')
                ->selectRaw('status, COUNT(*) as count, SUM(amount) as total')
                ->get(),
            'by_currency' => Transaction::groupBy('currency')
                ->selectRaw('currency, COUNT(*) as count, SUM(amount) as total')
                ->get(),
            'daily_stats' => Transaction::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getUserTransactions(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $transactions = Transaction::where('user_id', $request->user_id)
            ->with(['payment', 'invoice'])
            ->latest('created_at')
            ->limit($request->limit ?? 20)
            ->get();

        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }

    public function exportTransactions(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'type' => 'nullable|in:payment,refund,withdrawal,deposit,transfer',
            'status' => 'nullable|in:pending,processing,completed,failed,cancelled',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = Transaction::with(['user', 'payment', 'invoice']);

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->get();

        $filename = "transactions_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $transactions,
            'filename' => $filename,
            'message' => 'Transactions exported successfully'
        ]);
    }

    public function searchTransactions(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $transactions = Transaction::with(['user', 'payment'])
            ->where(function ($query) use ($request) {
                $query->where('reference', 'like', "%{$request->query}%")
                      ->orWhere('description', 'like', "%{$request->query}%")
                      ->orWhereHas('user', function ($q) use ($request) {
                          $q->where('name', 'like', "%{$request->query}%")
                            ->orWhere('email', 'like', "%{$request->query}%");
                      });
            })
            ->latest('created_at')
            ->limit($request->limit ?? 10)
            ->get();

        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }

    public function getTransactionDetails(Request $request, $reference): JsonResponse
    {
        $transaction = Transaction::with(['user', 'payment', 'invoice', 'refund'])
            ->where('reference', $reference)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'transaction' => $transaction
        ]);
    }

    private function generateReference($prefix = 'TXN')
    {
        return $prefix . '-' . strtoupper(uniqid()) . '-' . time();
    }
}
