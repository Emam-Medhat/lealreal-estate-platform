<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PDF;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $receipts = Receipt::with(['user', 'payment', 'invoice'])
            ->when($request->search, function ($query, $search) {
                $query->where('receipt_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('payments.receipts.index', compact('receipts'));
    }

    public function show(Receipt $receipt)
    {
        $receipt->load(['user', 'payment', 'invoice']);
        return view('payments.receipts.show', compact('receipt'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'type' => 'required|in:payment,refund,deposit',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $payment = Payment::findOrFail($request->payment_id);

            // Check if receipt already exists
            $existingReceipt = Receipt::where('payment_id', $payment->id)
                ->where('type', $request->type)
                ->first();

            if ($existingReceipt) {
                return back()->with('error', 'Receipt already exists for this payment.');
            }

            $receipt = Receipt::create([
                'user_id' => $payment->user_id,
                'payment_id' => $payment->id,
                'invoice_id' => $payment->invoice_id,
                'receipt_number' => $this->generateReceiptNumber(),
                'type' => $request->type,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'payment_method' => $payment->paymentMethod->type ?? 'unknown',
                'transaction_reference' => $payment->reference,
                'description' => $payment->description,
                'notes' => $request->notes,
                'issued_at' => now(),
                'created_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'generated_receipt',
                'details' => "Generated receipt {$receipt->receipt_number} for payment {$payment->reference}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('payments.receipts.show', $receipt)
                ->with('success', 'Receipt generated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error generating receipt: ' . $e->getMessage());
        }
    }

    public function downloadPDF(Receipt $receipt)
    {
        $receipt->load(['user', 'payment', 'invoice']);
        
        $pdf = PDF::loadView('payments.receipts.pdf', compact('receipt'));
        
        return $pdf->download("receipt_{$receipt->receipt_number}.pdf");
    }

    public function sendEmail(Request $request, Receipt $receipt): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'nullable|string|max:2000',
        ]);

        try {
            // Send receipt email (mock implementation)
            // In real implementation, use Laravel's Mail facade
            
            $receipt->update([
                'sent_at' => now(),
                'sent_to' => $request->email,
                'updated_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'sent_receipt',
                'details' => "Sent receipt {$receipt->receipt_number} to {$request->email}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Receipt sent successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    public function void(Request $request, Receipt $receipt): JsonResponse
    {
        $request->validate([
            'void_reason' => 'required|string|max:500',
        ]);

        try {
            $receipt->update([
                'status' => 'voided',
                'voided_at' => now(),
                'void_reason' => $request->void_reason,
                'updated_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'voided_receipt',
                'details' => "Voided receipt {$receipt->receipt_number}: {$request->void_reason}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Receipt voided successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error voiding receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    public function duplicate(Request $request, Receipt $receipt): JsonResponse
    {
        try {
            $newReceipt = $receipt->replicate([
                'receipt_number',
                'issued_at',
                'sent_at',
                'status',
                'voided_at',
                'void_reason',
            ]);

            $newReceipt->receipt_number = $this->generateReceiptNumber();
            $newReceipt->issued_at = now();
            $newReceipt->status = 'active';
            $newReceipt->save();

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'duplicated_receipt',
                'details' => "Duplicated receipt {$receipt->receipt_number} to {$newReceipt->receipt_number}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'receipt' => $newReceipt,
                'message' => 'Receipt duplicated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error duplicating receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getReceiptStats(): JsonResponse
    {
        $stats = [
            'total_receipts' => Receipt::count(),
            'active_receipts' => Receipt::where('status', 'active')->count(),
            'voided_receipts' => Receipt::where('status', 'voided')->count(),
            'total_amount' => Receipt::sum('amount'),
            'by_type' => Receipt::groupBy('type')
                ->selectRaw('type, COUNT(*) as count, SUM(amount) as total')
                ->get(),
            'by_status' => Receipt::groupBy('status')
                ->selectRaw('status, COUNT(*) as count, SUM(amount) as total')
                ->get(),
            'by_payment_method' => Receipt::groupBy('payment_method')
                ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
                ->get(),
            'daily_stats' => Receipt::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
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

    public function getUserReceipts(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $receipts = Receipt::where('user_id', $request->user_id)
            ->with(['payment', 'invoice'])
            ->latest('created_at')
            ->limit($request->limit ?? 20)
            ->get();

        return response()->json([
            'success' => true,
            'receipts' => $receipts
        ]);
    }

    public function exportReceipts(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'type' => 'nullable|in:payment,refund,deposit',
            'status' => 'nullable|in:active,voided',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = Receipt::with(['user', 'payment', 'invoice']);

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

        $receipts = $query->get();

        $filename = "receipts_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $receipts,
            'filename' => $filename,
            'message' => 'Receipts exported successfully'
        ]);
    }

    public function searchReceipts(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $receipts = Receipt::with(['user', 'payment'])
            ->where(function ($query) use ($request) {
                $query->where('receipt_number', 'like', "%{$request->query}%")
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
            'receipts' => $receipts
        ]);
    }

    public function getReceiptDetails(Request $request, $receiptNumber): JsonResponse
    {
        $receipt = Receipt::with(['user', 'payment', 'invoice'])
            ->where('receipt_number', $receiptNumber)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'receipt' => $receipt
        ]);
    }

    private function generateReceiptNumber()
    {
        $prefix = 'RCP';
        $year = date('Y');
        $sequence = Receipt::whereYear('created_at', $year)->count() + 1;
        
        return sprintf('%s-%s-%06d', $prefix, $year, $sequence);
    }
}
