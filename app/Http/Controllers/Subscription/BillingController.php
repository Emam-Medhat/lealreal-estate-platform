<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BillingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $subscriptions = Subscription::where('user_id', $user->id)
            ->with(['plan', 'invoices'])
            ->orderBy('created_at', 'desc')
            ->get();

        $invoices = SubscriptionInvoice::where('user_id', $user->id)
            ->with(['subscription.plan'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $billingStats = $this->getBillingStats($user);

        return view('billing.index', compact('subscriptions', 'invoices', 'billingStats'));
    }

    public function paymentMethods()
    {
        $user = Auth::user();
        
        // Get user's payment methods (this would integrate with your payment system)
        $paymentMethods = $this->getUserPaymentMethods($user);

        return view('billing.payment-methods', compact('paymentMethods'));
    }

    public function addPaymentMethod(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:card,bank_account,paypal',
            'card_number' => 'required_if:type,card|string',
            'card_expiry' => 'required_if:type,card|string',
            'card_cvv' => 'required_if:type,card|string',
            'cardholder_name' => 'required_if:type,card|string',
            'bank_account_number' => 'required_if:type,bank_account|string',
            'bank_routing_number' => 'required_if:type,bank_account|string',
            'bank_account_name' => 'required_if:type,bank_account|string',
            'paypal_email' => 'required_if:type,paypal|email',
            'is_default' => 'boolean'
        ]);

        try {
            $user = Auth::user();
            
            // Add payment method to payment gateway
            $paymentMethod = $this->addPaymentMethodToGateway($user, $validated);

            return redirect()->back()
                ->with('success', 'Payment method added successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to add payment method: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function removePaymentMethod(Request $request, $paymentMethodId)
    {
        try {
            $user = Auth::user();
            
            // Remove payment method from payment gateway
            $this->removePaymentMethodFromGateway($user, $paymentMethodId);

            return redirect()->back()
                ->with('success', 'Payment method removed successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to remove payment method: ' . $e->getMessage());
        }
    }

    public function setDefaultPaymentMethod(Request $request, $paymentMethodId)
    {
        try {
            $user = Auth::user();
            
            // Set default payment method in payment gateway
            $this->setDefaultPaymentMethodInGateway($user, $paymentMethodId);

            return redirect()->back()
                ->with('success', 'Default payment method updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update default payment method: ' . $e->getMessage());
        }
    }

    public function billingHistory()
    {
        $user = Auth::user();
        
        $invoices = SubscriptionInvoice::where('user_id', $user->id)
            ->with(['subscription.plan', 'subscription.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('billing.history', compact('invoices'));
    }

    public function upcomingInvoices()
    {
        $user = Auth::user();
        
        $upcomingInvoices = $this->getUpcomingInvoices($user);

        return view('billing.upcoming', compact('upcomingInvoices'));
    }

    public function billingSettings()
    {
        $user = Auth::user();
        
        $settings = $this->getBillingSettings($user);

        return view('billing.settings', compact('settings'));
    }

    public function updateBillingSettings(Request $request)
    {
        $validated = $request->validate([
            'auto_renew' => 'boolean',
            'payment_reminders' => 'boolean',
            'reminder_days_before' => 'nullable|integer|min:1|max:30',
            'invoice_delivery' => 'required|in:email,both',
            'tax_exempt' => 'boolean',
            'tax_id' => 'nullable|string|max:50',
            'billing_address' => 'nullable|array',
            'billing_address.line1' => 'required_with:billing_address|string',
            'billing_address.line2' => 'nullable|string',
            'billing_address.city' => 'required_with:billing_address|string',
            'billing_address.state' => 'required_with:billing_address|string',
            'billing_address.postal_code' => 'required_with:billing_address|string',
            'billing_address.country' => 'required_with:billing_address|string'
        ]);

        try {
            $user = Auth::user();
            
            // Update billing settings
            $this->updateBillingSettingsForUser($user, $validated);

            return redirect()->back()
                ->with('success', 'Billing settings updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update billing settings: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function downloadInvoice(SubscriptionInvoice $invoice)
    {
        $this->authorize('view', $invoice);

        // Generate and download invoice PDF
        $pdf = $this->generateBillingInvoicePDF($invoice);

        return $pdf->download("billing_invoice_{$invoice->id}.pdf");
    }

    public function paymentHistory()
    {
        $user = Auth::user();
        
        $payments = $this->getPaymentHistory($user);

        return view('billing.payments', compact('payments'));
    }

    public function taxInfo()
    {
        $user = Auth::user();
        
        $taxInfo = $this->getTaxInfo($user);

        return view('billing.tax', compact('taxInfo'));
    }

    public function updateTaxInfo(Request $request)
    {
        $validated = $request->validate([
            'tax_exempt' => 'boolean',
            'tax_id_type' => 'required|in:vat,ein,ss',
            'tax_id_number' => 'required_if:tax_exempt,false|string|max:50',
            'tax_address' => 'required_if:tax_exempt,false|string|max:255',
            'tax_city' => 'required_if:tax_exempt,false|string|max:100',
            'tax_country' => 'required_if:tax_exempt,false|string|max:100',
            'tax_postal_code' => 'nullable|string|max:20',
        ]);

        try {
            $user = Auth::user();
            
            // Update tax information
            $this->updateTaxInfoForUser($user, $validated);

            return redirect()->back()
                ->with('success', 'Tax information updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update tax information: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function exportBillingData(Request $request)
    {
        $user = Auth::user();
        
        $format = $request->get('format', 'csv');
        $period = $request->get('period', 'year');
        $fromDate = $this->getFromDate($period);

        $invoices = SubscriptionInvoice::where('user_id', $user->id)
            ->whereDate('billing_date', '>=', $fromDate)
            ->with(['subscription.plan'])
            ->orderBy('billing_date', 'desc')
            ->get();

        $filename = "billing_export_" . $period . "_" . now()->format('Y-m-d') . "." . $format;

        switch ($format) {
            case 'csv':
                return $this->exportBillingCSV($invoices, $filename);
            case 'xlsx':
                return $this->exportBillingExcel($invoices, $filename);
            case 'json':
                return $this->exportBillingJSON($invoices, $filename);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    private function getBillingStats($user)
    {
        $currentMonth = SubscriptionInvoice::where('user_id', $user->id)
            ->whereMonth('billing_date', now()->month)
            ->whereYear('billing_date', now()->year);

        return [
            'current_month_spending' => $currentMonth->sum('amount'),
            'current_month_invoices' => $currentMonth->count(),
            'total_spending' => SubscriptionInvoice::where('user_id', $user->id)->sum('amount'),
            'total_invoices' => SubscriptionInvoice::where('user_id', $user->id)->count(),
            'paid_amount' => SubscriptionInvoice::where('user_id', $user->id)->where('status', 'paid')->sum('amount'),
            'pending_amount' => SubscriptionInvoice::where('user_id', $user->id)->where('status', 'pending')->sum('amount'),
            'overdue_amount' => SubscriptionInvoice::where('user_id', $user->id)
                ->where('status', 'pending')
                ->where('due_date', '<', now())
                ->sum('amount'),
            'active_subscriptions' => Subscription::where('user_id', $user->id)->where('status', 'active')->count(),
            'next_billing_date' => $this->getNextBillingDate($user)
        ];
    }

    private function getUserPaymentMethods($user)
    {
        // This would integrate with your payment gateway (Stripe, PayPal, etc.)
        // Return user's payment methods
        return [];
    }

    private function addPaymentMethodToGateway($user, $data)
    {
        // Add payment method to payment gateway
        // Return payment method details
        return [];
    }

    private function removePaymentMethodFromGateway($user, $paymentMethodId)
    {
        // Remove payment method from payment gateway
    }

    private function setDefaultPaymentMethodInGateway($user, $paymentMethodId)
    {
        // Set default payment method in payment gateway
    }

    private function getUpcomingInvoices($user)
    {
        return Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('auto_renew', true)
            ->where('ends_at', '<=', now()->addDays(30))
            ->with(['plan'])
            ->get()
            ->map(function ($subscription) {
                return [
                    'subscription_id' => $subscription->id,
                    'plan_name' => $subscription->plan->name,
                    'next_billing_date' => $subscription->ends_at,
                    'amount' => $subscription->plan->price,
                    'currency' => $subscription->plan->currency
                ];
            });
    }

    private function getBillingSettings($user)
    {
        // Get user's billing settings from database
        return [
            'auto_renew' => true,
            'payment_reminders' => true,
            'reminder_days_before' => 7,
            'invoice_delivery' => 'email',
            'tax_exempt' => false,
            'tax_id' => null,
            'billing_address' => null
        ];
    }

    private function updateBillingSettingsForUser($user, $settings)
    {
        // Update billing settings in database
    }

    private function generateBillingInvoicePDF($invoice)
    {
        // Generate PDF invoice
        return response()->make('PDF content', 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="invoice.pdf"'
        ]);
    }

    private function getPaymentHistory($user)
    {
        // Get payment history from payment gateway
        return [];
    }

    private function getTaxInfo($user)
    {
        // Get tax information from database
        return [
            'tax_exempt' => false,
            'tax_id' => null,
            'tax_registration_name' => null,
            'tax_address' => null
        ];
    }

    private function updateTaxInfoForUser($user, $taxInfo)
    {
        // Update tax information in database
    }

    private function getNextBillingDate($user)
    {
        $nextSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('auto_renew', true)
            ->orderBy('ends_at', 'asc')
            ->first();

        return $nextSubscription ? $nextSubscription->ends_at : null;
    }

    private function getFromDate($period)
    {
        switch ($period) {
            case 'month':
                return now()->startOfMonth();
            case 'quarter':
                return now()->startOfQuarter();
            case 'year':
                return now()->startOfYear();
            default:
                return now()->subYear();
        }
    }

    private function exportBillingCSV($invoices, $filename)
    {
        return response()->streamDownload(function () use ($invoices) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Invoice ID', 'Amount', 'Currency', 'Status', 'Billing Date', 'Due Date', 'Plan', 'Paid Date']);
            
            foreach ($invoices as $invoice) {
                fputcsv($handle, [
                    $invoice->id,
                    $invoice->amount,
                    $invoice->currency,
                    $invoice->status,
                    $invoice->billing_date->format('Y-m-d'),
                    $invoice->due_date->format('Y-m-d'),
                    $invoice->subscription->plan->name,
                    $invoice->paid_at ? $invoice->paid_at->format('Y-m-d') : ''
                ]);
            }
            
            fclose($handle);
        }, $filename);
    }

    private function exportBillingExcel($invoices, $filename)
    {
        // Implement Excel export using Laravel Excel
        return response()->json(['message' => 'Excel export not implemented'], 501);
    }

    private function exportBillingJSON($invoices, $filename)
    {
        return response()->streamDownload(function () use ($invoices) {
            echo $invoices->toJson(JSON_PRETTY_PRINT);
        }, $filename);
    }
}
