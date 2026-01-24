<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PDF;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with(['user', 'payments'])
            ->when($request->search, function ($query, $search) {
                $query->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
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
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('payments.invoices.index', compact('invoices'));
    }

    public function create()
    {
        return view('payments.invoices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:subscription,property,service,penalty,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'due_date' => 'required|date|after:today',
            'notes' => 'nullable|string|max:1000',
            'terms' => 'nullable|string|max:2000',
            'metadata' => 'nullable|array',
        ]);

        try {
            $invoice = Invoice::create([
                'user_id' => $request->user_id,
                'invoice_number' => $this->generateInvoiceNumber(),
                'type' => $request->type,
                'title' => $request->title,
                'description' => $request->description,
                'items' => $request->items,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount,
                'discount_amount' => $request->discount_amount ?? 0,
                'total_amount' => $request->total_amount,
                'currency' => $request->currency,
                'due_date' => $request->due_date,
                'status' => 'draft',
                'notes' => $request->notes,
                'terms' => $request->terms,
                'metadata' => $request->metadata ?? [],
                'created_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created_invoice',
                'details' => "Created invoice: {$invoice->invoice_number} for {$request->total_amount} {$request->currency}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('payments.invoices.show', $invoice)
                ->with('success', 'Invoice created successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error creating invoice: ' . $e->getMessage());
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['user', 'payments']);
        return view('payments.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->status === 'paid' || $invoice->status === 'cancelled') {
            return back()->with('error', 'Cannot edit paid or cancelled invoices.');
        }

        return view('payments.invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'paid' || $invoice->status === 'cancelled') {
            return back()->with('error', 'Cannot update paid or cancelled invoices.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after:today',
            'notes' => 'nullable|string|max:1000',
            'terms' => 'nullable|string|max:2000',
        ]);

        try {
            $invoice->update([
                'title' => $request->title,
                'description' => $request->description,
                'items' => $request->items,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount,
                'discount_amount' => $request->discount_amount ?? 0,
                'total_amount' => $request->total_amount,
                'due_date' => $request->due_date,
                'notes' => $request->notes,
                'terms' => $request->terms,
                'updated_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated_invoice',
                'details' => "Updated invoice: {$invoice->invoice_number}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('payments.invoices.show', $invoice)
                ->with('success', 'Invoice updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating invoice: ' . $e->getMessage());
        }
    }

    public function send(Request $request, Invoice $invoice): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'nullable|string|max:2000',
        ]);

        try {
            // Send invoice email (mock implementation)
            // In real implementation, use Laravel's Mail facade
            
            $invoice->update([
                'status' => 'sent',
                'sent_at' => now(),
                'sent_to' => $request->email,
                'updated_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'sent_invoice',
                'details' => "Sent invoice {$invoice->invoice_number} to {$request->email}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice sent successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAsPaid(Request $request, Invoice $invoice): JsonResponse
    {
        $request->validate([
            'payment_method' => 'required|string|max:100',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'updated_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'marked_invoice_paid',
                'details' => "Marked invoice {$invoice->invoice_number} as paid",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice marked as paid successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Request $request, Invoice $invoice): JsonResponse
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        if ($invoice->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel paid invoices'
            ], 400);
        }

        try {
            $invoice->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->cancellation_reason,
                'updated_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'cancelled_invoice',
                'details' => "Cancelled invoice {$invoice->invoice_number}: {$request->cancellation_reason}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadPDF(Invoice $invoice)
    {
        $invoice->load(['user', 'payments']);
        
        $pdf = PDF::loadView('payments.invoices.pdf', compact('invoice'));
        
        return $pdf->download("invoice_{$invoice->invoice_number}.pdf");
    }

    public function getInvoiceStats(): JsonResponse
    {
        $stats = [
            'total_invoices' => Invoice::count(),
            'draft_invoices' => Invoice::where('status', 'draft')->count(),
            'sent_invoices' => Invoice::where('status', 'sent')->count(),
            'paid_invoices' => Invoice::where('status', 'paid')->count(),
            'overdue_invoices' => Invoice::where('due_date', '<', now())
                ->where('status', '!=', 'paid')
                ->where('status', '!=', 'cancelled')
                ->count(),
            'total_amount' => Invoice::sum('total_amount'),
            'paid_amount' => Invoice::where('status', 'paid')->sum('total_amount'),
            'outstanding_amount' => Invoice::where('status', '!=', 'paid')
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount'),
            'by_type' => Invoice::groupBy('type')
                ->selectRaw('type, COUNT(*) as count, SUM(total_amount) as total')
                ->get(),
            'by_status' => Invoice::groupBy('status')
                ->selectRaw('status, COUNT(*) as count, SUM(total_amount) as total')
                ->get(),
            'monthly_stats' => Invoice::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count, SUM(total_amount) as total')
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

    public function exportInvoices(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:draft,sent,paid,overdue,cancelled',
            'type' => 'nullable|in:subscription,property,service,penalty,other',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = Invoice::with(['user', 'payments']);

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

        $invoices = $query->get();

        $filename = "invoices_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $invoices,
            'filename' => $filename,
            'message' => 'Invoices exported successfully'
        ]);
    }

    private function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $year = date('Y');
        $sequence = Invoice::whereYear('created_at', $year)->count() + 1;
        
        return sprintf('%s-%s-%06d', $prefix, $year, $sequence);
    }
}
