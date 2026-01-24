<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionRenewal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionInvoiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $invoices = SubscriptionInvoice::where('user_id', $user->id)
            ->with(['subscription.plan', 'renewal'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('subscriptions.invoices.index', compact('invoices'));
    }

    public function show(SubscriptionInvoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load(['subscription.plan', 'subscription.user', 'renewal']);

        return view('subscriptions.invoices.show', compact('invoice'));
    }

    public function create(Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        return view('subscriptions.invoices.create', compact('subscription'));
    }

    public function store(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'description' => 'required|string',
            'due_date' => 'required|date|after:today',
            'billing_date' => 'required|date',
            'items' => 'array',
            'items.*.description' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $invoice = SubscriptionInvoice::create([
                'subscription_id' => $subscription->id,
                'user_id' => Auth::id(),
                'amount' => $validated['amount'],
                'currency' => $validated['currency'],
                'billing_date' => $validated['billing_date'],
                'due_date' => $validated['due_date'],
                'status' => 'pending',
                'description' => $validated['description'],
                'items' => $validated['items'] ?? []
            ]);

            DB::commit();

            return redirect()->route('subscriptions.invoices.show', $invoice)
                ->with('success', 'Invoice created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(SubscriptionInvoice $invoice)
    {
        $this->authorize('update', $invoice);

        if ($invoice->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Cannot edit paid invoice.');
        }

        return view('subscriptions.invoices.edit', compact('invoice'));
    }

    public function update(Request $request, SubscriptionInvoice $invoice)
    {
        $this->authorize('update', $invoice);

        if ($invoice->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Cannot edit paid invoice.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'due_date' => 'required|date|after:today',
            'items' => 'array',
            'items.*.description' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        try {
            $invoice->update([
                'amount' => $validated['amount'],
                'description' => $validated['description'],
                'due_date' => $validated['due_date'],
                'items' => $validated['items'] ?? []
            ]);

            return redirect()->route('subscriptions.invoices.show', $invoice)
                ->with('success', 'Invoice updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(SubscriptionInvoice $invoice)
    {
        $this->authorize('delete', $invoice);

        if ($invoice->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Cannot delete paid invoice.');
        }

        try {
            $invoice->delete();

            return redirect()->route('subscriptions.invoices.index')
                ->with('success', 'Invoice deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }

    public function pay(Request $request, SubscriptionInvoice $invoice)
    {
        $this->authorize('pay', $invoice);

        if ($invoice->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Invoice is already paid.');
        }

        try {
            $paymentResult = $this->processPayment($request, $invoice);

            if ($paymentResult['success']) {
                $invoice->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'payment_method' => $request->payment_method,
                    'transaction_id' => $paymentResult['transaction_id']
                ]);

                // Update subscription status if needed
                $this->updateSubscriptionStatus($invoice);

                return redirect()->route('subscriptions.invoices.show', $invoice)
                    ->with('success', 'Payment processed successfully!');
            } else {
                return redirect()->back()
                    ->with('error', $paymentResult['message']);
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    public function download(SubscriptionInvoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load(['subscription.plan', 'subscription.user']);

        // Generate PDF invoice
        $pdf = $this->generateInvoicePDF($invoice);

        return $pdf->download("invoice_{$invoice->id}.pdf");
    }

    public function sendEmail(Request $request, SubscriptionInvoice $invoice)
    {
        $this->authorize('view', $invoice);

        try {
            $email = $request->email ?? $invoice->subscription->user->email;
            
            // Send invoice email
            $this->sendInvoiceEmail($invoice, $email);

            $invoice->update([
                'last_sent_at' => now()
            ]);

            return redirect()->back()
                ->with('success', 'Invoice sent successfully to ' . $email);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to send invoice: ' . $e->getMessage());
        }
    }

    public function markAsPaid(Request $request, SubscriptionInvoice $invoice)
    {
        $this->authorize('update', $invoice);

        $validated = $request->validate([
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
            'payment_notes' => 'nullable|string'
        ]);

        try {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $validated['transaction_id'],
                'payment_notes' => $validated['payment_notes']
            ]);

            // Update subscription status
            $this->updateSubscriptionStatus($invoice);

            return redirect()->back()
                ->with('success', 'Invoice marked as paid successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to mark invoice as paid: ' . $e->getMessage());
        }
    }

    public function void(Request $request, SubscriptionInvoice $invoice)
    {
        $this->authorize('update', $invoice);

        if ($invoice->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Cannot void paid invoice.');
        }

        try {
            $invoice->update([
                'status' => 'void',
                'voided_at' => now(),
                'void_reason' => $request->reason ?? 'User requested void'
            ]);

            return redirect()->back()
                ->with('success', 'Invoice voided successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to void invoice: ' . $e->getMessage());
        }
    }

    public function getInvoiceStats()
    {
        $user = Auth::user();
        
        $stats = [
            'total_invoices' => SubscriptionInvoice::where('user_id', $user->id)->count(),
            'paid_invoices' => SubscriptionInvoice::where('user_id', $user->id)->where('status', 'paid')->count(),
            'pending_invoices' => SubscriptionInvoice::where('user_id', $user->id)->where('status', 'pending')->count(),
            'overdue_invoices' => SubscriptionInvoice::where('user_id', $user->id)
                ->where('status', 'pending')
                ->where('due_date', '<', now())
                ->count(),
            'total_amount' => SubscriptionInvoice::where('user_id', $user->id)->sum('amount'),
            'paid_amount' => SubscriptionInvoice::where('user_id', $user->id)->where('status', 'paid')->sum('amount'),
            'pending_amount' => SubscriptionInvoice::where('user_id', $user->id)->where('status', 'pending')->sum('amount'),
            'this_month' => SubscriptionInvoice::where('user_id', $user->id)
                ->whereMonth('billing_date', now()->month)
                ->whereYear('billing_date', now()->year)
                ->sum('amount')
        ];

        return response()->json($stats);
    }

    public function exportInvoices(Request $request)
    {
        $user = Auth::user();
        
        $format = $request->get('format', 'csv');
        $status = $request->get('status');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $query = SubscriptionInvoice::where('user_id', $user->id)
            ->with(['subscription.plan']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($fromDate) {
            $query->whereDate('billing_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('billing_date', '<=', $toDate);
        }

        $invoices = $query->orderBy('created_at', 'desc')->get();

        $filename = "invoices_export_" . now()->format('Y-m-d') . "." . $format;

        switch ($format) {
            case 'csv':
                return $this->exportCSV($invoices, $filename);
            case 'xlsx':
                return $this->exportExcel($invoices, $filename);
            case 'json':
                return $this->exportJSON($invoices, $filename);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    private function processPayment(Request $request, SubscriptionInvoice $invoice)
    {
        $paymentMethod = $request->payment_method;

        switch ($paymentMethod) {
            case 'stripe':
                return $this->processStripePayment($request, $invoice);
            case 'paypal':
                return $this->processPayPalPayment($request, $invoice);
            case 'bank_transfer':
                return $this->processBankTransfer($request, $invoice);
            default:
                return ['success' => false, 'message' => 'Invalid payment method'];
        }
    }

    private function processStripePayment(Request $request, SubscriptionInvoice $invoice)
    {
        // Implement Stripe payment processing
        return ['success' => true, 'transaction_id' => 'stripe_' . uniqid()];
    }

    private function processPayPalPayment(Request $request, SubscriptionInvoice $invoice)
    {
        // Implement PayPal payment processing
        return ['success' => true, 'transaction_id' => 'paypal_' . uniqid()];
    }

    private function processBankTransfer(Request $request, SubscriptionInvoice $invoice)
    {
        // Implement bank transfer processing
        return ['success' => false, 'message' => 'Bank transfer requires manual confirmation'];
    }

    private function updateSubscriptionStatus(SubscriptionInvoice $invoice)
    {
        $subscription = $invoice->subscription;

        // Update subscription status based on payment
        if ($subscription->status === 'pending' && $invoice->status === 'paid') {
            $subscription->update([
                'status' => 'active',
                'activated_at' => now()
            ]);
        }
    }

    private function generateInvoicePDF(SubscriptionInvoice $invoice)
    {
        // Generate PDF using your preferred PDF library
        // This is a placeholder implementation
        return response()->make('PDF content', 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="invoice.pdf"'
        ]);
    }

    private function sendInvoiceEmail(SubscriptionInvoice $invoice, string $email)
    {
        // Send invoice email using your email system
        // This is a placeholder implementation
    }

    private function exportCSV($invoices, $filename)
    {
        return response()->streamDownload(function () use ($invoices) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Invoice ID', 'Amount', 'Currency', 'Status', 'Billing Date', 'Due Date', 'Plan']);
            
            foreach ($invoices as $invoice) {
                fputcsv($handle, [
                    $invoice->id,
                    $invoice->amount,
                    $invoice->currency,
                    $invoice->status,
                    $invoice->billing_date->format('Y-m-d'),
                    $invoice->due_date->format('Y-m-d'),
                    $invoice->subscription->plan->name
                ]);
            }
            
            fclose($handle);
        }, $filename);
    }

    private function exportExcel($invoices, $filename)
    {
        // Implement Excel export using Laravel Excel
        return response()->json(['message' => 'Excel export not implemented'], 501);
    }

    private function exportJSON($invoices, $filename)
    {
        return response()->streamDownload(function () use ($invoices) {
            echo $invoices->toJson(JSON_PRETTY_PRINT);
        }, $filename);
    }
}
