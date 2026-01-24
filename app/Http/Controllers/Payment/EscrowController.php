<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\EscrowAccount;
use App\Models\Payment;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EscrowController extends Controller
{
    public function index(Request $request)
    {
        $escrowAccounts = EscrowAccount::with(['buyer', 'seller', 'transaction'])
            ->when($request->search, function ($query, $search) {
                $query->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('buyer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('seller', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('payments.escrow.index', compact('escrowAccounts'));
    }

    public function create()
    {
        return view('payments.escrow.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'buyer_id' => 'required|exists:users,id',
            'seller_id' => 'required|exists:users,id|different:buyer_id',
            'transaction_id' => 'nullable|exists:transactions,id',
            'type' => 'required|in:property,service,goods,milestone',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'terms' => 'required|string|max:2000',
            'release_conditions' => 'required|array|min:1',
            'release_conditions.*' => 'required|string|max:500',
            'dispute_resolution' => 'required|in:automatic,manual,arbitration',
            'fee_structure' => 'required|array',
            'fee_structure.buyer_fee' => 'required|numeric|min:0|max:100',
            'fee_structure.seller_fee' => 'required|numeric|min:0|max:100',
            'fee_structure.escrow_fee' => 'required|numeric|min:0|max:100',
            'milestones' => 'nullable|array',
            'milestones.*.title' => 'required|string|max:255',
            'milestones.*.amount' => 'required|numeric|min:0',
            'milestones.*.description' => 'required|string|max:1000',
            'metadata' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $escrow = EscrowAccount::create([
                'buyer_id' => $request->buyer_id,
                'seller_id' => $request->seller_id,
                'transaction_id' => $request->transaction_id,
                'reference' => $this->generateEscrowReference(),
                'type' => $request->type,
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'terms' => $request->terms,
                'release_conditions' => $request->release_conditions,
                'dispute_resolution' => $request->dispute_resolution,
                'fee_structure' => $request->fee_structure,
                'milestones' => $request->milestones ?? [],
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            // Calculate and set fees
            $buyerFee = $request->amount * ($request->fee_structure['buyer_fee'] / 100);
            $sellerFee = $request->amount * ($request->fee_structure['seller_fee'] / 100);
            $escrowFee = $request->amount * ($request->fee_structure['escrow_fee'] / 100);

            $escrow->update([
                'buyer_fee' => $buyerFee,
                'seller_fee' => $sellerFee,
                'escrow_fee' => $escrowFee,
                'total_fees' => $buyerFee + $sellerFee + $escrowFee,
                'net_amount' => $request->amount - $escrowFee,
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created_escrow',
                'details' => "Created escrow account: {$escrow->reference} for {$request->amount} {$request->currency}",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return redirect()->route('payments.escrow.show', $escrow)
                ->with('success', 'Escrow account created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating escrow account: ' . $e->getMessage());
        }
    }

    public function show(EscrowAccount $escrow)
    {
        $escrow->load(['buyer', 'seller', 'transaction', 'disputes']);
        return view('payments.escrow.show', compact('escrow'));
    }

    public function fund(Request $request, EscrowAccount $escrow): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($escrow->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending escrow accounts can be funded'
            ], 400);
        }

        try {
            // Create payment for escrow funding
            $payment = Payment::create([
                'user_id' => $escrow->buyer_id,
                'escrow_id' => $escrow->id,
                'payment_method_id' => $request->payment_method_id,
                'amount' => $request->amount,
                'currency' => $escrow->currency,
                'reference' => 'ESC-FUND-' . uniqid(),
                'description' => "Funding escrow account: {$escrow->reference}",
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            // Process payment (mock implementation)
            $paymentResult = $this->processPayment($payment);

            if ($paymentResult['success']) {
                $payment->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                // Update escrow status
                $escrow->update([
                    'status' => 'funded',
                    'funded_at' => now(),
                    'funded_amount' => $escrow->funded_amount + $request->amount,
                ]);

                UserActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'funded_escrow',
                    'details' => "Funded escrow {$escrow->reference} with {$request->amount} {$escrow->currency}",
                    'ip_address' => $request->ip(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Escrow funded successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed: ' . $paymentResult['message']
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error funding escrow: ' . $e->getMessage()
            ], 500);
        }
    }

    public function release(Request $request, EscrowAccount $escrow): JsonResponse
    {
        $request->validate([
            'release_amount' => 'required|numeric|min:0.01|max:' . $escrow->net_amount,
            'release_reason' => 'required|string|max:1000',
            'milestone_id' => 'nullable|integer',
        ]);

        if (!in_array($escrow->status, ['funded', 'partially_released'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only funded escrow accounts can have funds released'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create release record
            $release = $escrow->releases()->create([
                'amount' => $request->release_amount,
                'release_reason' => $request->release_reason,
                'milestone_id' => $request->milestone_id,
                'released_by' => Auth::id(),
                'released_at' => now(),
            ]);

            // Update escrow status
            $totalReleased = $escrow->releases()->sum('amount');
            $remainingAmount = $escrow->net_amount - $totalReleased;

            if ($remainingAmount <= 0) {
                $escrow->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'released_amount' => $escrow->released_amount + $request->release_amount,
                ]);
            } else {
                $escrow->update([
                    'status' => 'partially_released',
                    'released_amount' => $escrow->released_amount + $request->release_amount,
                ]);
            }

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'released_escrow',
                'details' => "Released {$request->release_amount} {$escrow->currency} from escrow {$escrow->reference}",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'release' => $release,
                'message' => 'Escrow funds released successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error releasing escrow funds: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createDispute(Request $request, EscrowAccount $escrow): JsonResponse
    {
        $request->validate([
            'dispute_reason' => 'required|string|max:1000',
            'dispute_type' => 'required|in:delivery,quality,non_payment,fraud,other',
            'evidence' => 'nullable|array',
            'evidence.*.type' => 'required|string|max:50',
            'evidence.*.url' => 'required|url|max:500',
            'evidence.*.description' => 'required|string|max:500',
        ]);

        if (!in_array($escrow->status, ['funded', 'partially_released'])) {
            return response()->json([
                'success' => false,
                'message' => 'Disputes can only be created for funded escrow accounts'
            ], 400);
        }

        try {
            $dispute = $escrow->disputes()->create([
                'initiator_id' => Auth::id(),
                'dispute_reason' => $request->dispute_reason,
                'dispute_type' => $request->dispute_type,
                'evidence' => $request->evidence ?? [],
                'status' => 'open',
                'created_at' => now(),
            ]);

            $escrow->update([
                'status' => 'disputed',
                'disputed_at' => now(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created_dispute',
                'details' => "Created dispute for escrow {$escrow->reference}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'dispute' => $dispute,
                'message' => 'Dispute created successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating dispute: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resolveDispute(Request $request, EscrowAccount $escrow): JsonResponse
    {
        $request->validate([
            'resolution' => 'required|in:buyer_favored,seller_favored,split,refund',
            'resolution_notes' => 'required|string|max:1000',
            'release_to_buyer' => 'nullable|numeric|min:0',
            'release_to_seller' => 'nullable|numeric|min:0',
        ]);

        if ($escrow->status !== 'disputed') {
            return response()->json([
                'success' => false,
                'message' => 'Only disputed escrow accounts can be resolved'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $dispute = $escrow->disputes()->where('status', 'open')->first();
            
            if ($dispute) {
                $dispute->update([
                    'status' => 'resolved',
                    'resolution' => $request->resolution,
                    'resolution_notes' => $request->resolution_notes,
                    'resolved_by' => Auth::id(),
                    'resolved_at' => now(),
                ]);
            }

            // Apply resolution
            switch ($request->resolution) {
                case 'buyer_favored':
                    $escrow->update([
                        'status' => 'refunded_to_buyer',
                        'resolved_at' => now(),
                    ]);
                    break;
                case 'seller_favored':
                    $escrow->update([
                        'status' => 'released_to_seller',
                        'resolved_at' => now(),
                    ]);
                    break;
                case 'split':
                    $escrow->update([
                        'status' => 'split_resolution',
                        'buyer_payout' => $request->release_to_buyer,
                        'seller_payout' => $request->release_to_seller,
                        'resolved_at' => now(),
                    ]);
                    break;
                case 'refund':
                    $escrow->update([
                        'status' => 'refunded',
                        'resolved_at' => now(),
                    ]);
                    break;
            }

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'resolved_dispute',
                'details' => "Resolved dispute for escrow {$escrow->reference} with resolution: {$request->resolution}",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dispute resolved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error resolving dispute: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEscrowStats(): JsonResponse
    {
        $stats = [
            'total_escrows' => EscrowAccount::count(),
            'pending_escrows' => EscrowAccount::where('status', 'pending')->count(),
            'funded_escrows' => EscrowAccount::where('status', 'funded')->count(),
            'disputed_escrows' => EscrowAccount::where('status', 'disputed')->count(),
            'completed_escrows' => EscrowAccount::where('status', 'completed')->count(),
            'total_amount' => EscrowAccount::sum('amount'),
            'total_fees' => EscrowAccount::sum('total_fees'),
            'by_type' => EscrowAccount::groupBy('type')
                ->selectRaw('type, COUNT(*) as count, SUM(amount) as total')
                ->get(),
            'by_status' => EscrowAccount::groupBy('status')
                ->selectRaw('status, COUNT(*) as count, SUM(amount) as total')
                ->get(),
            'monthly_stats' => EscrowAccount::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count, SUM(amount) as total')
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

    public function exportEscrows(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:pending,funded,disputed,completed,refunded',
            'type' => 'nullable|in:property,service,goods,milestone',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = EscrowAccount::with(['buyer', 'seller', 'transaction']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $escrows = $query->get();

        $filename = "escrows_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $escrows,
            'filename' => $filename,
            'message' => 'Escrows exported successfully'
        ]);
    }

    private function generateEscrowReference()
    {
        return 'ESC-' . strtoupper(uniqid()) . '-' . time();
    }

    private function processPayment($payment)
    {
        // Mock payment processing - integrate with actual payment gateway
        return [
            'success' => true,
            'message' => 'Payment processed successfully'
        ];
    }
}
