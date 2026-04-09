<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Investor\InvestRequest;
use App\Models\Investor;
use App\Models\InvestorPortfolio;
use App\Models\InvestorTransaction;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvestorTransactionController extends Controller
{
    public function index(Request $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $transactions = $investor->transactions()
            ->with(['portfolio'])
            ->when($request->search, function ($query, $search) {
                $query->where('description', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('transaction_date', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('transaction_date', '<=', $date);
            })
            ->latest('transaction_date')
            ->paginate(20);

        return view('investor.transactions.index', compact('transactions'));
    }

    public function create()
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        $portfolios = $investor->portfolios()->pluck('investment_name', 'id');
        
        return view('investor.transactions.create', compact('portfolios'));
    }

    public function store(InvestRequest $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $transaction = InvestorTransaction::create([
            'investor_id' => $investor->id,
            'portfolio_id' => $request->portfolio_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'currency' => $request->currency ?? 'USD',
            'description' => $request->description,
            'reference' => $request->reference,
            'transaction_date' => $request->transaction_date,
            'status' => $request->status ?? 'completed',
            'fee' => $request->fee ?? 0,
            'tax' => $request->tax ?? 0,
            'net_amount' => $request->amount - ($request->fee ?? 0) - ($request->tax ?? 0),
            'payment_method' => $request->payment_method,
            'payment_details' => $request->payment_details ?? [],
            'transaction_hash' => $request->transaction_hash,
            'blockchain_confirmations' => $request->blockchain_confirmations,
            'exchange_rate' => $request->exchange_rate,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('transaction-receipts', 'public');
            $transaction->update(['receipt' => $receiptPath]);
        }

        // Handle supporting documents
        if ($request->hasFile('supporting_documents')) {
            $documents = [];
            foreach ($request->file('supporting_documents') as $document) {
                $path = $document->store('transaction-documents', 'public');
                $documents[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'type' => $document->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $transaction->update(['supporting_documents' => $documents]);
        }

        // Update investor totals
        if ($request->type === 'investment') {
            $investor->increment('total_invested', $request->amount);
        } elseif ($request->type === 'return') {
            $investor->increment('total_returns', $request->amount);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_investor_transaction',
            'details' => "Created {$request->type} transaction: {$transaction->description}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('investor.transactions.show', $transaction)
            ->with('success', 'Transaction created successfully.');
    }

    public function show(InvestorTransaction $transaction)
    {
        $this->authorize('view', $transaction);
        
        $transaction->load(['investor', 'portfolio']);
        
        return view('investor.transactions.show', compact('transaction'));
    }

    public function edit(InvestorTransaction $transaction)
    {
        $this->authorize('update', $transaction);
        
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        $portfolios = $investor->portfolios()->pluck('investment_name', 'id');
        
        return view('investor.transactions.edit', compact('transaction', 'portfolios'));
    }

    public function update(Request $request, InvestorTransaction $transaction)
    {
        $this->authorize('update', $transaction);
        
        $request->validate([
            'type' => 'required|in:investment,return,withdrawal,fee,refund,bonus',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:500',
            'transaction_date' => 'required|date',
            'status' => 'required|in:pending,completed,failed,cancelled',
            'payment_method' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $oldAmount = $transaction->amount;
        $oldType = $transaction->type;
        $newAmount = $request->amount;
        $newType = $request->type;

        $transaction->update([
            'portfolio_id' => $request->portfolio_id,
            'type' => $newType,
            'amount' => $newAmount,
            'description' => $request->description,
            'reference' => $request->reference,
            'transaction_date' => $request->transaction_date,
            'status' => $request->status,
            'fee' => $request->fee ?? 0,
            'tax' => $request->tax ?? 0,
            'net_amount' => $newAmount - ($request->fee ?? 0) - ($request->tax ?? 0),
            'payment_method' => $request->payment_method,
            'payment_details' => $request->payment_details ?? [],
            'transaction_hash' => $request->transaction_hash,
            'blockchain_confirmations' => $request->blockchain_confirmations,
            'exchange_rate' => $request->exchange_rate,
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        // Handle receipt update
        if ($request->hasFile('receipt')) {
            if ($transaction->receipt) {
                Storage::disk('public')->delete($transaction->receipt);
            }
            $receiptPath = $request->file('receipt')->store('transaction-receipts', 'public');
            $transaction->update(['receipt' => $receiptPath]);
        }

        // Update investor totals if amount or type changed
        if ($oldAmount != $newAmount || $oldType != $newType) {
            $investor = $transaction->investor;
            
            // Recalculate totals
            $totalInvested = $investor->transactions()
                ->where('type', 'investment')
                ->sum('amount');
            $totalReturns = $investor->transactions()
                ->where('type', 'return')
                ->sum('amount');
            
            $investor->update([
                'total_invested' => $totalInvested,
                'total_returns' => $totalReturns,
            ]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_investor_transaction',
            'details' => "Updated transaction: {$transaction->description}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('investor.transactions.show', $transaction)
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(InvestorTransaction $transaction)
    {
        $this->authorize('delete', $transaction);
        
        $transactionDescription = $transaction->description;
        $amount = $transaction->amount;
        $type = $transaction->type;
        
        // Delete receipt
        if ($transaction->receipt) {
            Storage::disk('public')->delete($transaction->receipt);
        }
        
        // Delete supporting documents
        if ($transaction->supporting_documents) {
            foreach ($transaction->supporting_documents as $document) {
                Storage::disk('public')->delete($document['path']);
            }
        }
        
        $transaction->delete();

        // Update investor totals
        $investor = $transaction->investor;
        if ($type === 'investment') {
            $investor->decrement('total_invested', $amount);
        } elseif ($type === 'return') {
            $investor->decrement('total_returns', $amount);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_investor_transaction',
            'details' => "Deleted transaction: {$transactionDescription}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('investor.transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    public function updateStatus(Request $request, InvestorTransaction $transaction): JsonResponse
    {
        $this->authorize('update', $transaction);
        
        $request->validate([
            'status' => 'required|in:pending,completed,failed,cancelled',
        ]);

        $transaction->update(['status' => $request->status]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_transaction_status',
            'details' => "Updated transaction '{$transaction->description}' status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Transaction status updated successfully'
        ]);
    }

    public function getTransactionStats(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $stats = [
            'total_transactions' => $investor->transactions()->count(),
            'investments' => $investor->transactions()->where('type', 'investment')->count(),
            'returns' => $investor->transactions()->where('type', 'return')->count(),
            'withdrawals' => $investor->transactions()->where('type', 'withdrawal')->count(),
            'total_invested' => $investor->transactions()->where('type', 'investment')->sum('amount'),
            'total_returns' => $investor->transactions()->where('type', 'return')->sum('amount'),
            'total_withdrawn' => $investor->transactions()->where('type', 'withdrawal')->sum('amount'),
            'net_invested' => $investor->total_invested - $investor->total_returns,
            'by_type' => $investor->transactions()
                ->groupBy('type')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_amount' => $group->sum('amount'),
                    ];
                }),
            'by_status' => $investor->transactions()
                ->groupBy('status')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_amount' => $group->sum('amount'),
                    ];
                }),
            'monthly_summary' => $investor->transactions()
                ->selectRaw('MONTH(transaction_date) as month, YEAR(transaction_date) as year, type, SUM(amount) as total')
                ->where('transaction_date', '>=', now()->subMonths(12))
                ->groupByRaw('YEAR(transaction_date), MONTH(transaction_date), type')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportTransactions(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'type' => 'nullable|in:investment,return,withdrawal,fee,refund,bonus',
            'status' => 'nullable|in:pending,completed,failed,cancelled',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $query = $investor->transactions()->with(['portfolio']);

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->get();

        $filename = "investor_transactions_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $transactions,
            'filename' => $filename,
            'message' => 'Transactions exported successfully'
        ]);
    }

    public function downloadReceipt(InvestorTransaction $transaction)
    {
        $this->authorize('view', $transaction);
        
        if (!$transaction->receipt) {
            return back()->with('error', 'No receipt available for download.');
        }

        $filePath = storage_path('app/public/' . $transaction->receipt);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'Receipt file not found.');
        }

        return response()->download($filePath, 'receipt_' . $transaction->id . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
    }
}
