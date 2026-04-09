<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Company;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Exception;

class FinancialService
{
    /**
     * Create a new invoice linked to real estate entities.
     *
     * @param array $data
     * @return Invoice
     * @throws Exception
     */
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            // Validate ownership/linkage
            if (empty($data['property_id']) && empty($data['company_id'])) {
                throw new Exception("Invoice must be linked to a Property or Company.");
            }

            $invoice = Invoice::create([
                'user_id' => auth()->id(), // Creator
                'property_id' => $data['property_id'] ?? null,
                'company_id' => $data['company_id'] ?? null,
                'client_id' => $data['client_id'] ?? null,
                'invoice_number' => $this->generateInvoiceNumber(),
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'items' => $data['items'] ?? [],
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'USD',
                'due_date' => $data['due_date'] ?? now()->addDays(30),
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            return $invoice;
        });
    }

    /**
     * Process a payment for an invoice.
     *
     * @param Invoice $invoice
     * @param array $paymentData
     * @return Payment
     * @throws Exception
     */
    public function processPayment(Invoice $invoice, array $paymentData): Payment
    {
        return DB::transaction(function () use ($invoice, $paymentData) {
            if ($invoice->status === 'paid') {
                throw new Exception("Invoice is already paid.");
            }

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'user_id' => auth()->id(),
                'amount' => $paymentData['amount'],
                'currency' => $invoice->currency,
                'payment_method_id' => $paymentData['payment_method_id'] ?? null,
                'reference_id' => $paymentData['reference_id'] ?? null, // e.g., Stripe ID
                'status' => 'completed', // Assuming immediate success for now
                'paid_at' => now(),
                'created_by' => auth()->id(),
            ]);

            // Update Invoice Status
            $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
            
            if ($totalPaid >= $invoice->amount) {
                $invoice->update(['status' => 'paid', 'paid_date' => now()]);
            } else {
                $invoice->update(['status' => 'partial']);
            }

            return $payment;
        });
    }

    private function generateInvoiceNumber(): string
    {
        return 'INV-' . strtoupper(uniqid());
    }

    /**
     * Calculate total revenue for a user/organization.
     */
    public function getTotalRevenue($userId = null)
    {
        $query = Invoice::where('status', 'paid');
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return $query->sum('amount');
    }

    /**
     * Get revenue grouped by property.
     */
    public function getRevenueByProperty($userId = null)
    {
        $query = Invoice::where('status', 'paid')
            ->whereNotNull('property_id')
            ->with('property:id,title'); // Assuming Property has title

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->select('property_id', DB::raw('SUM(amount) as total_revenue'))
            ->groupBy('property_id')
            ->get()
            ->map(function ($item) {
                return [
                    'property_name' => $item->property ? $item->property->title : 'Unknown Property',
                    'revenue' => $item->total_revenue,
                ];
            });
    }

    /**
     * Get revenue grouped by company.
     */
    public function getRevenueByCompany($userId = null)
    {
        $query = Invoice::where('status', 'paid')
            ->whereNotNull('company_id')
            ->with('company:id,name');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->select('company_id', DB::raw('SUM(amount) as total_revenue'))
            ->groupBy('company_id')
            ->get()
            ->map(function ($item) {
                return [
                    'company_name' => $item->company ? $item->company->name : 'Unknown Company',
                    'revenue' => $item->total_revenue,
                ];
            });
    }

    /**
     * Get monthly revenue for the last 12 months.
     */
    public function getMonthlyRevenue($userId = null)
    {
        $query = Invoice::where('status', 'paid')
            ->where('paid_date', '>=', now()->subYear());

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->select(
                DB::raw('YEAR(paid_date) as year'),
                DB::raw('MONTH(paid_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }
}
