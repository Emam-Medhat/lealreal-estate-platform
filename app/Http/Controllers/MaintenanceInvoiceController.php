<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceInvoice;
use App\Models\MaintenanceRequest;
use App\Models\WorkOrder;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenanceInvoiceController extends Controller
{
    public function index()
    {
        $invoices = MaintenanceInvoice::with(['maintenanceRequest.property', 'workOrder', 'serviceProvider'])
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('service_provider_id'), function($query, $providerId) {
                $query->where('service_provider_id', $providerId);
            })
            ->when(request('date_from'), function($query, $dateFrom) {
                $query->whereDate('invoice_date', '>=', $dateFrom);
            })
            ->when(request('date_to'), function($query, $dateTo) {
                $query->whereDate('invoice_date', '<=', $dateTo);
            })
            ->latest()->paginate(15);

        return view('maintenance.invoices', compact('invoices'));
    }

    public function create()
    {
        $maintenanceRequests = MaintenanceRequest::where('status', 'completed')->get();
        $workOrders = WorkOrder::where('status', 'completed')->get();
        $serviceProviders = ServiceProvider::all();

        return view('maintenance.invoices-create', compact('maintenanceRequests', 'workOrders', 'serviceProviders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'maintenance_request_id' => 'nullable|exists:maintenance_requests,id',
            'work_order_id' => 'nullable|exists:work_orders,id',
            'service_provider_id' => 'required|exists:service_providers,id',
            'invoice_number' => 'required|string|max:100|unique:maintenance_invoices,invoice_number',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'tax_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_terms' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $validated['invoice_code'] = 'INV-' . date('Y') . '-' . str_pad(MaintenanceInvoice::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['status'] = 'draft';
        $validated['created_by'] = auth()->id();

        DB::beginTransaction();
        try {
            $invoice = MaintenanceInvoice::create($validated);

            // Create invoice items
            foreach ($validated['items'] as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                ]);
            }

            // Handle attachments if any
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('invoice_attachments', 'public');
                    // You might want to create an attachments table
                }
            }

            DB::commit();

            return redirect()->route('maintenance.invoices.show', $invoice)
                ->with('success', 'تم إنشاء فاتورة الصيانة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء إنشاء فاتورة الصيانة');
        }
    }

    public function show(MaintenanceInvoice $invoice)
    {
        $invoice->load([
            'maintenanceRequest.property', 
            'workOrder', 
            'serviceProvider',
            'items',
            'payments'
        ]);
        
        return view('maintenance.invoices-show', compact('invoice'));
    }

    public function edit(MaintenanceInvoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'لا يمكن تعديل الفاتورة المدفوعة');
        }

        $maintenanceRequests = MaintenanceRequest::where('status', 'completed')->get();
        $workOrders = WorkOrder::where('status', 'completed')->get();
        $serviceProviders = ServiceProvider::all();

        return view('maintenance.invoices-edit', compact('invoice', 'maintenanceRequests', 'workOrders', 'serviceProviders'));
    }

    public function update(Request $request, MaintenanceInvoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'لا يمكن تعديل الفاتورة المدفوعة');
        }

        $validated = $request->validate([
            'maintenance_request_id' => 'nullable|exists:maintenance_requests,id',
            'work_order_id' => 'nullable|exists:work_orders,id',
            'service_provider_id' => 'required|exists:service_providers,id',
            'invoice_number' => 'required|string|max:100|unique:maintenance_invoices,invoice_number,' . $invoice->id,
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'tax_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_terms' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
        ]);

        $invoice->update($validated);

        return redirect()->route('maintenance.invoices.show', $invoice)
            ->with('success', 'تم تحديث فاتورة الصيانة بنجاح');
    }

    public function destroy(MaintenanceInvoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'لا يمكن حذف الفاتورة المدفوعة');
        }

        DB::beginTransaction();
        try {
            // Delete invoice items
            $invoice->items()->delete();
            
            $invoice->delete();
            DB::commit();

            return redirect()->route('maintenance.invoices.index')
                ->with('success', 'تم حذف فاتورة الصيانة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء حذف فاتورة الصيانة');
        }
    }

    public function send(MaintenanceInvoice $invoice, Request $request)
    {
        if ($invoice->status === 'draft') {
            return back()->with('error', 'يجب إرسال الفاتورة أولاً');
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string|max:1000',
        ]);

        $invoice->update([
            'status' => 'sent',
            'sent_at' => now(),
            'sent_to' => $validated['email'],
        ]);

        // Here you would implement email sending logic
        // Mail::to($validated['email'])->send(new MaintenanceInvoiceMail($invoice, $validated['message']));

        return redirect()->route('maintenance.invoices.show', $invoice)
            ->with('success', 'تم إرسال الفاتورة بنجاح');
    }

    public function markAsPaid(MaintenanceInvoice $invoice, Request $request)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'الفاتورة مدفوعة بالفعل');
        }

        $validated = $request->validate([
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,other',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $validated['payment_reference'],
                'payment_notes' => $validated['notes'],
            ]);

            // Create payment record
            $invoice->payments()->create([
                'amount' => $invoice->total_amount,
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'reference' => $validated['payment_reference'],
                'notes' => $validated['notes'],
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('maintenance.invoices.show', $invoice)
                ->with('success', 'تم تسجيل الدفع بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء تسجيل الدفع');
        }
    }

    public function markAsOverdue(MaintenanceInvoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'لا يمكن وضع علامة متأخر على الفاتورة المدفوعة');
        }

        if ($invoice->due_date > now()) {
            return back()->with('error', 'لم يحين وقت استحقاق الفاتورة بعد');
        }

        $invoice->update([
            'status' => 'overdue',
            'overdue_at' => now(),
        ]);

        return redirect()->route('maintenance.invoices.show', $invoice)
            ->with('success', 'تم وضع علامة متأخر على الفاتورة');
    }

    public function cancel(MaintenanceInvoice $invoice, Request $request)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'لا يمكن إلغاء الفاتورة المدفوعة');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $invoice->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_by' => auth()->id(),
        ]);

        return redirect()->route('maintenance.invoices.show', $invoice)
            ->with('success', 'تم إلغاء الفاتورة بنجاح');
    }

    public function duplicate(MaintenanceInvoice $invoice)
    {
        $newInvoice = $invoice->replicate([
            'invoice_number',
            'invoice_code',
            'status',
            'sent_at',
            'sent_to',
            'paid_at',
            'payment_method',
            'payment_reference',
            'payment_notes',
            'overdue_at',
            'cancelled_at',
            'cancellation_reason',
            'cancelled_by',
        ]);

        $newInvoice->invoice_number = 'INV-' . date('Y') . '-' . str_pad(MaintenanceInvoice::count() + 1, 4, '0', STR_PAD_LEFT);
        $newInvoice->invoice_code = 'INV-' . date('Y') . '-' . str_pad(MaintenanceInvoice::count() + 2, 4, '0', STR_PAD_LEFT);
        $newInvoice->status = 'draft';
        $newInvoice->created_by = auth()->id();
        $newInvoice->invoice_date = now();
        $newInvoice->due_date = now()->addDays(30);
        $newInvoice->save();

        // Duplicate items
        foreach ($invoice->items as $item) {
            $newInvoice->items()->create([
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
            ]);
        }

        return redirect()->route('maintenance.invoices.edit', $newInvoice)
            ->with('success', 'تم نسخ الفاتورة بنجاح');
    }

    public function download(MaintenanceInvoice $invoice)
    {
        // Here you would implement PDF generation logic
        // return $invoice->downloadPDF();

        return back()->with('info', 'سيتم تنزيل الفاتورة قريباً');
    }

    public function reports()
    {
        $stats = [
            'total_invoices' => MaintenanceInvoice::count(),
            'draft_invoices' => MaintenanceInvoice::where('status', 'draft')->count(),
            'sent_invoices' => MaintenanceInvoice::where('status', 'sent')->count(),
            'paid_invoices' => MaintenanceInvoice::where('status', 'paid')->count(),
            'overdue_invoices' => MaintenanceInvoice::where('status', 'overdue')->count(),
            'cancelled_invoices' => MaintenanceInvoice::where('status', 'cancelled')->count(),
            'total_amount' => MaintenanceInvoice::sum('total_amount'),
            'paid_amount' => MaintenanceInvoice::where('status', 'paid')->sum('total_amount'),
            'outstanding_amount' => MaintenanceInvoice::where('status', '!=', 'paid')->where('status', '!=', 'cancelled')->sum('total_amount'),
        ];

        $monthlyRevenue = MaintenanceInvoice::where('status', 'paid')
            ->selectRaw('MONTH(paid_at) as month, YEAR(paid_at) as year, SUM(total_amount) as revenue')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        return view('maintenance.invoices-reports', compact('stats', 'monthlyRevenue'));
    }

    public function export(Request $request)
    {
        $invoices = MaintenanceInvoice::with(['maintenanceRequest.property', 'workOrder', 'serviceProvider'])
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->service_provider_id, function($query, $providerId) {
                $query->where('service_provider_id', $providerId);
            })
            ->when($request->date_from, function($query, $dateFrom) {
                $query->whereDate('invoice_date', '>=', $dateFrom);
            })
            ->when($request->date_to, function($query, $dateTo) {
                $query->whereDate('invoice_date', '<=', $dateTo);
            })
            ->get();

        $filename = 'maintenance_invoices_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($invoices) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'رقم الفاتورة',
                'رقم الفاتورة الخارجي',
                'العقار',
                'مقدم الخدمة',
                'تاريخ الفاتورة',
                'تاريخ الاستحقاق',
                'المبلغ الإجمالي',
                'الحالة',
                'طريقة الدفع',
                'تاريخ الدفع',
            ]);

            // CSV Data
            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->invoice_code,
                    $invoice->invoice_number,
                    $invoice->maintenanceRequest->property->title ?? '',
                    $invoice->serviceProvider->name ?? '',
                    $invoice->invoice_date->format('Y-m-d'),
                    $invoice->due_date->format('Y-m-d'),
                    $invoice->total_amount,
                    $this->getStatusLabel($invoice->status),
                    $invoice->payment_method,
                    $invoice->paid_at ? $invoice->paid_at->format('Y-m-d') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'draft' => 'مسودة',
            'sent' => 'مرسلة',
            'paid' => 'مدفوعة',
            'overdue' => 'متأخرة',
            'cancelled' => 'ملغاة',
        ];

        return $labels[$status] ?? $status;
    }
}
