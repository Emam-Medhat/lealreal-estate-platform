<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Property;
use App\Models\Company;
use App\Services\InvoiceService;
use App\Repositories\InvoiceRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use PDF;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;
    protected InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(InvoiceService $invoiceService, InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceService = $invoiceService;
        $this->invoiceRepository = $invoiceRepository;
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'status', 'type', 'client_id', 'property_id', 'company_id', 
            'agent_id', 'date_from', 'date_to', 'min_amount', 'max_amount', 'search'
        ]);

        $invoices = $this->invoiceRepository->paginate($filters, 20);
        $stats = $this->invoiceService->getInvoiceStats($filters);

        return view('payments.invoices.index', compact('invoices', 'stats'));
    }

    public function create()
    {
        $clients = Client::orderBy('full_name')->get();
        $properties = Property::orderBy('title')->get();
        $companies = Company::orderBy('name')->get();

        return view('payments.invoices.create', compact('clients', 'properties', 'companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'property_id' => 'nullable|exists:properties,id',
            'company_id' => 'nullable|exists:companies,id',
            'type' => 'required|in:subscription,property,service,penalty,other,maintenance,consultation,commission',
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
            'total' => 'required|numeric|min:0',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $invoice = $this->invoiceService->createInvoice($request->all());

            return redirect()
                ->route('payments.invoices.show', $invoice)
                ->with('success', 'تم إنشاء الفاتورة بنجاح');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'property', 'company', 'agent', 'payments']);
        return view('payments.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->status === 'paid' || $invoice->status === 'cancelled') {
            return back()->with('error', 'Cannot edit paid or cancelled invoices.');
        }

        $invoice->load(['client', 'property', 'company']);
        $clients = Client::orderBy('full_name')->get();
        $properties = Property::orderBy('title')->get();
        $companies = Company::orderBy('name')->get();

        return view('payments.invoices.edit', compact('invoice', 'clients', 'properties', 'companies'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'paid' || $invoice->status === 'cancelled') {
            return back()->with('error', 'Cannot update paid or cancelled invoices.');
        }

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'property_id' => 'nullable|exists:properties,id',
            'company_id' => 'nullable|exists:companies,id',
            'type' => 'required|in:subscription,property,service,penalty,other,maintenance,consultation,commission',
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
            'total' => 'required|numeric|min:0',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $updatedInvoice = $this->invoiceService->updateInvoice($invoice->id, $request->all());

            return redirect()
                ->route('payments.invoices.show', $updatedInvoice)
                ->with('success', 'تم تحديث الفاتورة بنجاح');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الفاتورة: ' . $e->getMessage());
        }
    }

    public function destroy(Invoice $invoice)
    {
        try {
            if ($invoice->status === 'paid') {
                return back()->with('error', 'Cannot delete paid invoices.');
            }

            $this->invoiceRepository->delete($invoice->id);

            return redirect()
                ->route('payments.invoices.index')
                ->with('success', 'تم حذف الفاتورة بنجاح');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف الفاتورة: ' . $e->getMessage());
        }
    }

    // Approval Actions
    public function approve(Request $request, Invoice $invoice)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $approvedInvoice = $this->invoiceService->approveInvoice(
                $invoice->id, 
                $request->reason
            );

            return back()
                ->with('success', 'تم اعتماد الفاتورة بنجاح');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء اعتماد الفاتورة: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Invoice $invoice)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $rejectedInvoice = $this->invoiceService->rejectInvoice(
                $invoice->id, 
                $request->reason
            );

            return back()
                ->with('success', 'تم رفض الفاتورة بنجاح');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء رفض الفاتورة: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request, Invoice $invoice)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $cancelledInvoice = $this->invoiceService->cancelInvoice(
                $invoice->id, 
                $request->reason
            );

            return back()
                ->with('success', 'تم إلغاء الفاتورة بنجاح');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء إلغاء الفاتورة: ' . $e->getMessage());
        }
    }

    // Payment Actions
    public function addPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:50',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $payment = $this->invoiceService->addPayment(
                $invoice->id,
                $request->amount,
                $request->payment_method,
                ['notes' => $request->notes]
            );

            return back()
                ->with('success', 'تم إضافة الدفعة بنجاح');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء إضافة الدفعة: ' . $e->getMessage());
        }
    }

    // Reports and Analytics
    public function analytics(Request $request)
    {
        $filters = $request->only([
            'date_from', 'date_to', 'client_id', 'property_id', 'company_id'
        ]);

        $analytics = $this->invoiceService->getRevenueAnalytics($filters);
        $overdueAging = $this->invoiceService->getOverdueInvoicesWithAging();

        return view('payments.invoices.analytics', compact('analytics', 'overdueAging'));
    }

    public function report(Request $request)
    {
        $filters = $request->only([
            'status', 'type', 'client_id', 'property_id', 'company_id', 
            'agent_id', 'date_from', 'date_to', 'min_amount', 'max_amount'
        ]);

        $report = $this->invoiceService->generateInvoiceReport($filters);

        if ($request->has('download')) {
            return $this->downloadReport($report);
        }

        return view('payments.invoices.report', compact('report'));
    }

    // API Endpoints
    public function apiStats(Request $request): JsonResponse
    {
        $filters = $request->only([
            'date_from', 'date_to', 'client_id', 'property_id', 'company_id'
        ]);

        $stats = $this->invoiceService->getInvoiceStats($filters);

        return response()->json($stats);
    }

    public function apiOverdue(): JsonResponse
    {
        $overdueAging = $this->invoiceService->getOverdueInvoicesWithAging();

        return response()->json($overdueAging);
    }

    // Private Methods
    private function downloadReport(array $report): \Illuminate\Http\Response
    {
        $pdf = PDF::loadView('payments.invoices.pdf-report', $report);
        
        return $pdf->download('invoice-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
