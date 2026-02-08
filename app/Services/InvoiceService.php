<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Client;
use App\Models\Agent;
use App\Models\Company;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InvoiceService
{
    /**
     * Create a new invoice
     */
    public function createInvoice(array $data): Invoice
    {
        try {
            DB::beginTransaction();

            $invoice = Invoice::create($data);

            // Calculate totals if items provided
            if (isset($data['items']) && is_array($data['items'])) {
                $this->calculateInvoiceTotals($invoice);
            }

            // Update related entity balances if needed
            $this->updateRelatedEntityBalances($invoice);

            DB::commit();

            Log::info('Invoice created successfully', ['invoice_id' => $invoice->id]);

            return $invoice;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create invoice', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update an existing invoice
     */
    public function updateInvoice(Invoice $invoice, array $data): Invoice
    {
        try {
            DB::beginTransaction();

            $invoice->update($data);

            // Recalculate totals if items changed
            if (isset($data['items']) && is_array($data['items'])) {
                $this->calculateInvoiceTotals($invoice);
            }

            // Update related entity balances if needed
            $this->updateRelatedEntityBalances($invoice);

            DB::commit();

            Log::info('Invoice updated successfully', ['invoice_id' => $invoice->id]);

            return $invoice->refresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update invoice', ['invoice_id' => $invoice->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Delete an invoice
     */
    public function deleteInvoice(Invoice $invoice): bool
    {
        try {
            DB::beginTransaction();

            // Check if invoice has payments
            if ($invoice->payments()->exists()) {
                throw new \Exception('Cannot delete invoice with existing payments');
            }

            $invoice->delete();

            DB::commit();

            Log::info('Invoice deleted successfully', ['invoice_id' => $invoice->id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete invoice', ['invoice_id' => $invoice->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Approve an invoice
     */
    public function approveInvoice(Invoice $invoice, $approver, string $notes = ''): bool
    {
        try {
            DB::beginTransaction();

            $invoice->approveInvoice($approver, $notes);

            // Send notification to client
            $this->sendInvoiceNotification($invoice, 'approved');

            DB::commit();

            Log::info('Invoice approved successfully', ['invoice_id' => $invoice->id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve invoice', ['invoice_id' => $invoice->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Reject an invoice
     */
    public function rejectInvoice(Invoice $invoice, $rejecter, string $reason): bool
    {
        try {
            DB::beginTransaction();

            $invoice->rejectInvoice($rejecter, $reason);

            // Send notification to relevant parties
            $this->sendInvoiceNotification($invoice, 'rejected');

            DB::commit();

            Log::info('Invoice rejected successfully', ['invoice_id' => $invoice->id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject invoice', ['invoice_id' => $invoice->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Send invoice to client
     */
    public function sendInvoice(Invoice $invoice): bool
    {
        try {
            DB::beginTransaction();

            $invoice->sendInvoice();

            // Send email notification
            $this->sendInvoiceNotification($invoice, 'sent');

            DB::commit();

            Log::info('Invoice sent successfully', ['invoice_id' => $invoice->id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to send invoice', ['invoice_id' => $invoice->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Add payment to invoice
     */
    public function addPayment(Invoice $invoice, array $paymentData): Payment
    {
        try {
            DB::beginTransaction();

            $paymentData['invoice_id'] = $invoice->id;
            $payment = Payment::create($paymentData);

            // Update invoice payment status
            $invoice->updatePaymentStatus();

            // Update related entity balances
            $this->updateRelatedEntityBalances($invoice);

            DB::commit();

            Log::info('Payment added to invoice successfully', [
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id
            ]);

            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add payment to invoice', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get invoice statistics
     */
    public function getInvoiceStatistics(array $filters = []): array
    {
        $query = Invoice::query();

        // Apply filters
        if (isset($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }
        if (isset($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }
        if (isset($filters['agent_id'])) {
            $query->where('agent_id', $filters['agent_id']);
        }
        if (isset($filters['property_id'])) {
            $query->where('property_id', $filters['property_id']);
        }
        if (isset($filters['date_from'])) {
            $query->where('issue_date', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('issue_date', '<=', $filters['date_to']);
        }

        $totalInvoices = $query->count();
        $totalAmount = $query->sum('total');
        $paidAmount = $query->sum('paid_amount');
        $outstandingAmount = $totalAmount - $paidAmount;
        $overdueAmount = $query->where('due_date', '<', now())
                              ->where('status', '!=', 'paid')
                              ->sum('balance_due');

        return [
            'total_invoices' => $totalInvoices,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'outstanding_amount' => $outstandingAmount,
            'overdue_amount' => $overdueAmount,
            'payment_rate' => $totalAmount > 0 ? ($paidAmount / $totalAmount) * 100 : 0,
        ];
    }

    /**
     * Get overdue invoices
     */
    public function getOverdueInvoices(array $filters = []): Collection
    {
        $query = Invoice::where('due_date', '<', now())
                       ->where('status', '!=', 'paid')
                       ->where('status', '!=', 'cancelled');

        // Apply filters
        if (isset($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }
        if (isset($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }
        if (isset($filters['agent_id'])) {
            $query->where('agent_id', $filters['agent_id']);
        }

        return $query->with(['client', 'property', 'company'])
                    ->orderBy('due_date', 'asc')
                    ->get();
    }

    /**
     * Create recurring invoices
     */
    public function createRecurringInvoices(): Collection
    {
        $recurringInvoices = Invoice::where('is_recurring', true)
                                   ->where('recurring_next_date', '<=', now())
                                   ->where('recurring_remaining', '>', 0)
                                   ->get();

        $createdInvoices = collect();

        foreach ($recurringInvoices as $template) {
            try {
                $newInvoice = $template->createRecurringInvoice();
                $createdInvoices->push($newInvoice);

                // Update template next date
                $template->update([
                    'recurring_next_date' => $this->calculateNextRecurringDate($template),
                    'recurring_remaining' => $template->recurring_remaining - 1,
                ]);

                Log::info('Recurring invoice created', [
                    'template_id' => $template->id,
                    'new_invoice_id' => $newInvoice->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create recurring invoice', [
                    'template_id' => $template->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $createdInvoices;
    }

    /**
     * Send payment reminders
     */
    public function sendPaymentReminders(): Collection
    {
        $reminders = collect();

        // Get invoices due in 3 days
        $upcomingInvoices = Invoice::where('due_date', '>=', now())
                                   ->where('due_date', '<=', now()->addDays(3))
                                   ->where('status', 'pending')
                                   ->where('reminder_sent_at', null)
                                   ->get();

        foreach ($upcomingInvoices as $invoice) {
            try {
                $this->sendInvoiceNotification($invoice, 'reminder');
                $invoice->update(['reminder_sent_at' => now()]);
                $reminders->push($invoice);
            } catch (\Exception $e) {
                Log::error('Failed to send payment reminder', [
                    'invoice_id' => $invoice->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $reminders;
    }

    /**
     * Calculate invoice totals
     */
    private function calculateInvoiceTotals(Invoice $invoice): void
    {
        $subtotal = 0;
        
        if ($invoice->items && is_array($invoice->items)) {
            foreach ($invoice->items as $item) {
                $quantity = $item['quantity'] ?? 1;
                $unitPrice = $item['unit_price'] ?? 0;
                $subtotal += $quantity * $unitPrice;
            }
        }

        $invoice->subtotal = $subtotal;
        $invoice->tax_amount = $invoice->calculateTaxAmount();
        $invoice->total = $invoice->calculateTotal();
        $invoice->outstanding_amount = $invoice->calculateOutstandingAmount();
        $invoice->balance_due = $invoice->calculateOutstandingAmount();
        
        $invoice->save();
    }

    /**
     * Update related entity balances
     */
    private function updateRelatedEntityBalances(Invoice $invoice): void
    {
        // Update client balance
        if ($invoice->client) {
            $clientTotalInvoices = $invoice->client->invoices()->sum('total');
            $clientPaidAmount = $invoice->client->invoices()->sum('paid_amount');
            $clientBalance = $clientTotalInvoices - $clientPaidAmount;
            
            $invoice->client->update(['balance' => $clientBalance]);
        }

        // Update property financials if applicable
        if ($invoice->property) {
            $propertyTotalInvoices = $invoice->property->invoices()->sum('total');
            $propertyPaidAmount = $invoice->property->invoices()->sum('paid_amount');
            $propertyBalance = $propertyTotalInvoices - $propertyPaidAmount;
            
            $invoice->property->update(['outstanding_payments' => $propertyBalance]);
        }
    }

    /**
     * Send invoice notification
     */
    private function sendInvoiceNotification(Invoice $invoice, string $type): void
    {
        // This would integrate with notification service
        // For now, just log the action
        Log::info("Invoice {$type} notification", [
            'invoice_id' => $invoice->id,
            'client_id' => $invoice->client_id,
            'type' => $type
        ]);
    }

    /**
     * Calculate next recurring date
     */
    private function calculateNextRecurringDate(Invoice $invoice): Carbon
    {
        switch ($invoice->recurring_frequency) {
            case 'daily':
                return $invoice->recurring_next_date->addDay();
            case 'weekly':
                return $invoice->recurring_next_date->addWeek();
            case 'monthly':
                return $invoice->recurring_next_date->addMonth();
            case 'quarterly':
                return $invoice->recurring_next_date->addQuarter();
            case 'yearly':
                return $invoice->recurring_next_date->addYear();
            default:
                return $invoice->recurring_next_date->addMonth();
        }
    }

    /**
     * Generate invoice report
     */
    public function generateInvoiceReport(array $filters = []): array
    {
        $query = Invoice::with(['client', 'property', 'company', 'agent']);

        // Apply filters
        if (isset($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }
        if (isset($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }
        if (isset($filters['agent_id'])) {
            $query->where('agent_id', $filters['agent_id']);
        }
        if (isset($filters['property_id'])) {
            $query->where('property_id', $filters['property_id']);
        }
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (isset($filters['date_from'])) {
            $query->where('issue_date', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('issue_date', '<=', $filters['date_to']);
        }

        $invoices = $query->orderBy('issue_date', 'desc')->get();

        return [
            'invoices' => $invoices,
            'summary' => $this->getInvoiceStatistics($filters),
            'filters' => $filters
        ];
    }

    /**
     * Get client invoice history
     */
    public function getClientInvoiceHistory(Client $client, array $filters = []): Collection
    {
        $query = $client->invoices()->with(['property', 'company']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['date_from'])) {
            $query->where('issue_date', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('issue_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('issue_date', 'desc')->get();
    }

    /**
     * Get property invoice history
     */
    public function getPropertyInvoiceHistory(Property $property, array $filters = []): Collection
    {
        $query = $property->invoices()->with(['client', 'company']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['date_from'])) {
            $query->where('issue_date', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('issue_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('issue_date', 'desc')->get();
    }
}
