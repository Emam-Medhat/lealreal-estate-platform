<?php

namespace App\Http\Controllers;

use App\Services\InvoiceService;
use App\Services\PaymentService;
use App\Services\PropertyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;
    protected PaymentService $paymentService;

    public function __construct(InvoiceService $invoiceService, PaymentService $paymentService)
    {
        $this->invoiceService = $invoiceService;
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of invoices.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->all();
            $invoices = $this->invoiceService->generateInvoiceReport($filters);
            
            return response()->json([
                'success' => true,
                'data' => $invoices,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created invoice.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'client_id' => 'required|exists:clients,id',
                'property_id' => 'nullable|exists:properties,id',
                'company_id' => 'nullable|exists:companies,id',
                'agent_id' => 'nullable|exists:agents,id',
                'amount' => 'required|numeric|min:0',
                'due_date' => 'required|date',
                'items' => 'nullable|array',
                'tax_rate' => 'nullable|numeric|min:0|max:1',
                'discount_rate' => 'nullable|numeric|min:0|max:1',
                'notes' => 'nullable|string',
            ]);

            $invoice = $this->invoiceService->createInvoice($data);
            
            return response()->json([
                'success' => true,
                'data' => $invoice->load(['client', 'property', 'company', 'agent']),
                'message' => 'Invoice created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice): JsonResponse
    {
        try {
            $invoice->load([
                'client',
                'property', 
                'company',
                'agent',
                'payments',
                'documents'
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $invoice,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified invoice.
     */
    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        try {
            $data = $request->validate([
                'title' => 'sometimes|string|max:255',
                'client_id' => 'sometimes|exists:clients,id',
                'property_id' => 'sometimes|exists:properties,id',
                'company_id' => 'sometimes|exists:companies,id',
                'agent_id' => 'sometimes|exists:agents,id',
                'amount' => 'sometimes|numeric|min:0',
                'due_date' => 'sometimes|date',
                'items' => 'sometimes|array',
                'tax_rate' => 'sometimes|numeric|min:0|max:1',
                'discount_rate' => 'sometimes|numeric|min:0|max:1',
                'notes' => 'sometimes|string',
            ]);

            $updatedInvoice = $this->invoiceService->updateInvoice($invoice, $data);
            
            return response()->json([
                'success' => true,
                'data' => $updatedInvoice->load(['client', 'property', 'company', 'agent']),
                'message' => 'Invoice updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy(Invoice $invoice): JsonResponse
    {
        try {
            $this->invoiceService->deleteInvoice($invoice);
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve an invoice.
     */
    public function approve(Request $request, Invoice $invoice): JsonResponse
    {
        try {
            $data = $request->validate([
                'notes' => 'nullable|string',
            ]);

            $this->invoiceService->approveInvoice($invoice, auth()->user(), $data['notes'] ?? '');
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice approved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject an invoice.
     */
    public function reject(Request $request, Invoice $invoice): JsonResponse
    {
        try {
            $data = $request->validate([
                'reason' => 'required|string',
            ]);

            $this->invoiceService->rejectInvoice($invoice, auth()->user(), $data['reason']);
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice rejected successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send invoice to client.
     */
    public function send(Invoice $invoice): JsonResponse
    {
        try {
            $this->invoiceService->sendInvoice($invoice);
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice sent successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add payment to invoice.
     */
    public function addPayment(Request $request, Invoice $invoice): JsonResponse
    {
        try {
            $data = $request->validate([
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'payment_gateway' => 'nullable|string',
                'description' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            $payment = $this->invoiceService->addPayment($invoice, $data);
            
            return response()->json([
                'success' => true,
                'data' => $payment,
                'message' => 'Payment added successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get invoice statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $filters = $request->all();
            $statistics = $this->invoiceService->getInvoiceStatistics($filters);
            
            return response()->json([
                'success' => true,
                'data' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get overdue invoices.
     */
    public function overdue(Request $request): JsonResponse
    {
        try {
            $filters = $request->all();
            $invoices = $this->invoiceService->getOverdueInvoices($filters);
            
            return response()->json([
                'success' => true,
                'data' => $invoices,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get client invoice history.
     */
    public function clientHistory(Request $request, $clientId): JsonResponse
    {
        try {
            $client = \App\Models\Client::findOrFail($clientId);
            $filters = $request->all();
            $invoices = $this->invoiceService->getClientInvoiceHistory($client, $filters);
            
            return response()->json([
                'success' => true,
                'data' => $invoices,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get property invoice history.
     */
    public function propertyHistory(Request $request, $propertyId): JsonResponse
    {
        try {
            $property = Property::findOrFail($propertyId);
            $filters = $request->all();
            $invoices = $this->invoiceService->getPropertyInvoiceHistory($property, $filters);
            
            return response()->json([
                'success' => true,
                'data' => $invoices,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create recurring invoices.
     */
    public function createRecurring(): JsonResponse
    {
        try {
            $invoices = $this->invoiceService->createRecurringInvoices();
            
            return response()->json([
                'success' => true,
                'data' => $invoices,
                'message' => 'Recurring invoices created successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send payment reminders.
     */
    public function sendReminders(): JsonResponse
    {
        try {
            $reminders = $this->invoiceService->sendPaymentReminders();
            
            return response()->json([
                'success' => true,
                'data' => $reminders,
                'message' => 'Payment reminders sent successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
