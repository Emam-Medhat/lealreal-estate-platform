<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Client;
use App\Models\Agent;
use App\Models\Company;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentService
{
    /**
     * Create a new payment
     */
    public function createPayment(array $data): Payment
    {
        try {
            DB::beginTransaction();

            $payment = Payment::create($data);

            // Update invoice payment status
            if ($payment->invoice) {
                $payment->invoice->updatePaymentStatus();
            }

            // Update related entity balances
            $this->updateRelatedEntityBalances($payment);

            DB::commit();

            Log::info('Payment created successfully', ['payment_id' => $payment->id]);

            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create payment', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update an existing payment
     */
    public function updatePayment(Payment $payment, array $data): Payment
    {
        try {
            DB::beginTransaction();

            $payment->update($data);

            // Update invoice payment status if status changed
            if ($payment->isDirty('status') && $payment->invoice) {
                $payment->invoice->updatePaymentStatus();
            }

            // Update related entity balances
            $this->updateRelatedEntityBalances($payment);

            DB::commit();

            Log::info('Payment updated successfully', ['payment_id' => $payment->id]);

            return $payment->refresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update payment', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Delete a payment
     */
    public function deletePayment(Payment $payment): bool
    {
        try {
            DB::beginTransaction();

            // Check if payment can be deleted
            if ($payment->status === 'completed') {
                throw new \Exception('Cannot delete completed payments');
            }

            $payment->delete();

            // Update invoice payment status
            if ($payment->invoice) {
                $payment->invoice->updatePaymentStatus();
            }

            DB::commit();

            Log::info('Payment deleted successfully', ['payment_id' => $payment->id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete payment', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Approve a payment
     */
    public function approvePayment(Payment $payment, $approver, string $notes = ''): bool
    {
        try {
            DB::beginTransaction();

            $payment->approvePayment($approver, $notes);

            // Update invoice payment status
            if ($payment->invoice) {
                $payment->invoice->updatePaymentStatus();
            }

            // Send notification
            $this->sendPaymentNotification($payment, 'approved');

            DB::commit();

            Log::info('Payment approved successfully', ['payment_id' => $payment->id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve payment', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Reject a payment
     */
    public function rejectPayment(Payment $payment, $rejecter, string $reason): bool
    {
        try {
            DB::beginTransaction();

            $payment->rejectPayment($rejecter, $reason);

            // Send notification
            $this->sendPaymentNotification($payment, 'rejected');

            DB::commit();

            Log::info('Payment rejected successfully', ['payment_id' => $payment->id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject payment', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Process a payment completion
     */
    public function processPaymentCompletion(Payment $payment, array $gatewayData = []): bool
    {
        try {
            DB::beginTransaction();

            $payment->markAsCompleted();

            // Update invoice payment status
            if ($payment->invoice) {
                $payment->invoice->updatePaymentStatus();
            }

            // Update related entity balances
            $this->updateRelatedEntityBalances($payment);

            // Send notification
            $this->sendPaymentNotification($payment, 'completed');

            DB::commit();

            Log::info('Payment completed successfully', ['payment_id' => $payment->id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process payment completion', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Process a payment failure
     */
    public function processPaymentFailure(Payment $payment, string $reason, string $code = null): bool
    {
        try {
            DB::beginTransaction();

            $payment->markAsFailed($reason);

            // Send notification
            $this->sendPaymentNotification($payment, 'failed');

            DB::commit();

            Log::info('Payment failed', ['payment_id' => $payment->id, 'reason' => $reason]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process payment failure', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Process a refund
     */
    public function processRefund(Payment $payment, float $amount, string $reason): bool
    {
        try {
            DB::beginTransaction();

            $payment->processRefund($amount, $reason);

            // Update invoice payment status
            if ($payment->invoice) {
                $payment->invoice->updatePaymentStatus();
            }

            // Update related entity balances
            $this->updateRelatedEntityBalances($payment);

            // Send notification
            $this->sendPaymentNotification($payment, 'refunded');

            DB::commit();

            Log::info('Payment refunded successfully', [
                'payment_id' => $payment->id,
                'refund_amount' => $amount
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process refund', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Verify a payment
     */
    public function verifyPayment(Payment $payment, $verifier): bool
    {
        try {
            DB::beginTransaction();

            $payment->verifyPayment($verifier);

            // Send notification
            $this->sendPaymentNotification($payment, 'verified');

            DB::commit();

            Log::info('Payment verified successfully', ['payment_id' => $payment->id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to verify payment', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Flag payment as suspicious
     */
    public function flagAsSuspicious(Payment $payment, string $reason): bool
    {
        try {
            DB::beginTransaction();

            $payment->flagAsSuspicious($reason);

            // Send notification to admin
            $this->sendPaymentNotification($payment, 'flagged');

            DB::commit();

            Log::warning('Payment flagged as suspicious', [
                'payment_id' => $payment->id,
                'reason' => $reason
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to flag payment', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStatistics(array $filters = []): array
    {
        $query = Payment::query();

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
        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $totalPayments = $query->count();
        $totalAmount = $query->sum('amount');
        $completedAmount = $query->where('status', 'completed')->sum('amount');
        $failedAmount = $query->where('status', 'failed')->sum('amount');
        $refundedAmount = $query->where('status', 'refunded')->sum('amount');
        $pendingAmount = $query->where('status', 'pending')->sum('amount');

        return [
            'total_payments' => $totalPayments,
            'total_amount' => $totalAmount,
            'completed_amount' => $completedAmount,
            'failed_amount' => $failedAmount,
            'refunded_amount' => $refundedAmount,
            'pending_amount' => $pendingAmount,
            'success_rate' => $totalAmount > 0 ? ($completedAmount / $totalAmount) * 100 : 0,
            'failure_rate' => $totalAmount > 0 ? ($failedAmount / $totalAmount) * 100 : 0,
        ];
    }

    /**
     * Get high-risk payments
     */
    public function getHighRiskPayments(array $filters = []): Collection
    {
        $query = Payment::where('risk_score', '>=', 7)
                       ->where('status', '!=', 'failed')
                       ->where('status', '!=', 'cancelled');

        // Apply filters
        if (isset($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }
        if (isset($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        return $query->with(['client', 'property', 'company'])
                    ->orderBy('risk_score', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Get pending payments
     */
    public function getPendingPayments(array $filters = []): Collection
    {
        $query = Payment::where('status', 'pending')
                       ->orWhere('status', 'processing');

        // Apply filters
        if (isset($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }
        if (isset($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        return $query->with(['client', 'property', 'company', 'invoice'])
                    ->orderBy('created_at', 'asc')
                    ->get();
    }

    /**
     * Create recurring payments
     */
    public function createRecurringPayments(): Collection
    {
        $recurringPayments = Payment::where('is_recurring', true)
                                   ->where('recurring_next_date', '<=', now())
                                   ->where('recurring_remaining', '>', 0)
                                   ->get();

        $createdPayments = collect();

        foreach ($recurringPayments as $template) {
            try {
                $newPayment = $template->createRecurringPayment();
                $createdPayments->push($newPayment);

                // Update template next date
                $template->update([
                    'recurring_next_date' => $this->calculateNextRecurringDate($template),
                    'recurring_remaining' => $template->recurring_remaining - 1,
                ]);

                Log::info('Recurring payment created', [
                    'template_id' => $template->id,
                    'new_payment_id' => $newPayment->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create recurring payment', [
                    'template_id' => $template->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $createdPayments;
    }

    /**
     * Process pending payments
     */
    public function processPendingPayments(): Collection
    {
        $pendingPayments = Payment::where('status', 'pending')
                                ->where('created_at', '<', now()->subHours(1))
                                ->get();

        $processedPayments = collect();

        foreach ($pendingPayments as $payment) {
            try {
                // This would integrate with payment gateway
                // For now, just mark as processing
                $payment->update(['status' => 'processing']);
                $processedPayments->push($payment);

                Log::info('Payment moved to processing', ['payment_id' => $payment->id]);
            } catch (\Exception $e) {
                Log::error('Failed to process pending payment', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $processedPayments;
    }

    /**
     * Update related entity balances
     */
    private function updateRelatedEntityBalances(Payment $payment): void
    {
        // Update client balance
        if ($payment->client) {
            $clientTotalInvoices = $payment->client->invoices()->sum('total');
            $clientPaidAmount = $payment->client->invoices()->sum('paid_amount');
            $clientBalance = $clientTotalInvoices - $clientPaidAmount;
            
            $payment->client->update(['balance' => $clientBalance]);
        }

        // Update property financials if applicable
        if ($payment->property) {
            $propertyTotalInvoices = $payment->property->invoices()->sum('total');
            $propertyPaidAmount = $payment->property->invoices()->sum('paid_amount');
            $propertyBalance = $propertyTotalInvoices - $propertyPaidAmount;
            
            $payment->property->update(['outstanding_payments' => $propertyBalance]);
        }
    }

    /**
     * Send payment notification
     */
    private function sendPaymentNotification(Payment $payment, string $type): void
    {
        // This would integrate with notification service
        // For now, just log the action
        Log::info("Payment {$type} notification", [
            'payment_id' => $payment->id,
            'client_id' => $payment->client_id,
            'type' => $type
        ]);
    }

    /**
     * Calculate next recurring date
     */
    private function calculateNextRecurringDate(Payment $payment): Carbon
    {
        switch ($payment->recurring_frequency) {
            case 'daily':
                return $payment->recurring_next_date->addDay();
            case 'weekly':
                return $payment->recurring_next_date->addWeek();
            case 'monthly':
                return $payment->recurring_next_date->addMonth();
            case 'quarterly':
                return $payment->recurring_next_date->addQuarter();
            case 'yearly':
                return $payment->recurring_next_date->addYear();
            default:
                return $payment->recurring_next_date->addMonth();
        }
    }

    /**
     * Generate payment report
     */
    public function generatePaymentReport(array $filters = []): array
    {
        $query = Payment::with(['client', 'property', 'company', 'invoice']);

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
        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        return [
            'payments' => $payments,
            'summary' => $this->getPaymentStatistics($filters),
            'filters' => $filters
        ];
    }

    /**
     * Get client payment history
     */
    public function getClientPaymentHistory(Client $client, array $filters = []): Collection
    {
        $query = $client->payments()->with(['property', 'company', 'invoice']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get property payment history
     */
    public function getPropertyPaymentHistory(Property $property, array $filters = []): Collection
    {
        $query = $property->payments()->with(['client', 'company', 'invoice']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
