<?php

namespace App\Http\Controllers;

use App\Models\TaxPayment;
use App\Models\PropertyTax;
use App\Models\TaxFiling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaxPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = TaxPayment::with(['propertyTax.property', 'taxFiling', 'user']);

        // Search functionality
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('payment_number', 'like', '%' . $request->search . '%')
                  ->orWhere('reference_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('propertyTax', function ($subQuery) use ($request) {
                      $subQuery->whereHas('property', function ($propertyQuery) use ($request) {
                          $propertyQuery->where('title', 'like', '%' . $request->search . '%');
                      });
                  });
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(20);

        return view('taxes.payments.index', compact('payments'));
    }

    public function create()
    {
        $propertyTaxes = PropertyTax::where('status', 'pending')->get();
        $taxFilings = TaxFiling::where('status', 'approved')->get();

        return view('taxes.payments.create', compact('propertyTaxes', 'taxFilings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_tax_id' => 'nullable|exists:property_taxes,id',
            'tax_filing_id' => 'nullable|exists:tax_filings,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,online',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $payment = TaxPayment::create([
            'property_tax_id' => $request->property_tax_id,
            'tax_filing_id' => $request->tax_filing_id,
            'user_id' => Auth::id(),
            'payment_number' => 'PAY-' . date('Y') . '-' . str_pad(TaxPayment::count() + 1, 6, '0', STR_PAD_LEFT),
            'amount' => $request->amount,
            'total_amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'reference_number' => $request->reference_number,
            'status' => 'pending',
            'notes' => $request->notes ? ['text' => $request->notes] : null,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('taxes.payments.show', $payment)
            ->with('success', 'تم إنشاء الدفعة الضريبية بنجاح');
    }

    public function show(TaxPayment $taxPayment)
    {
        $taxPayment->load(['propertyTax.property', 'taxFiling', 'user']);

        return view('taxes.payments.show', compact('taxPayment'));
    }

    public function confirm(TaxPayment $taxPayment)
    {
        if ($taxPayment->status !== 'pending') {
            return back()->with('error', 'لا يمكن تأكيد الدفعة التي ليست معلقة');
        }

        $taxPayment->update([
            'status' => 'paid',
            'completed_at' => now(),
            'completed_by' => Auth::id(),
        ]);

        return redirect()
            ->route('taxes.payments.show', $taxPayment)
            ->with('success', 'تم تأكيد الدفعة بنجاح');
    }

    public function process(Request $request, TaxPayment $taxPayment)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'processing_fee' => 'nullable|numeric|min:0',
        ]);

        $taxPayment->update([
            'status' => 'processing',
            'transaction_id' => $request->transaction_id,
            'processing_fee' => $request->processing_fee,
            'processed_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم بدء معالجة الدفعة');
    }

    public function complete(Request $request, TaxPayment $taxPayment)
    {
        $request->validate([
            'confirmation_number' => 'required|string',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = [
            'status' => 'completed',
            'confirmation_number' => $request->confirmation_number,
            'completed_at' => now(),
            'updated_by' => Auth::id(),
        ];

        // Handle receipt file upload
        if ($request->hasFile('receipt_file')) {
            $file = $request->file('receipt_file');
            $path = $file->store('receipts', 'public');
            $data['receipt_path'] = $path;
        }

        $taxPayment->update($data);

        // Update related property tax status if exists
        if ($taxPayment->propertyTax) {
            $taxPayment->propertyTax->update(['status' => 'paid']);
        }

        return back()->with('success', 'تم إكمال الدفعة الضريبية بنجاح');
    }

    public function cancel(TaxPayment $taxPayment)
    {
        if ($taxPayment->status === 'completed') {
            return back()->with('error', 'لا يمكن إلغاء الدفعة المكتملة');
        }

        $taxPayment->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم إلغاء الدفعة الضريبية');
    }

    public function receipt(TaxPayment $taxPayment)
    {
        if (!$taxPayment->receipt_path) {
            return back()->with('error', 'لا يوجد إيصال متاح');
        }

        return Storage::download($taxPayment->receipt_path);
    }

    public function generateReceipt(TaxPayment $taxPayment)
    {
        // Generate PDF receipt
        $pdf = \PDF::loadView('taxes.payments.receipt', compact('taxPayment'));
        
        return $pdf->download('tax-receipt-' . $taxPayment->id . '.pdf');
    }

    public function edit(TaxPayment $taxPayment)
    {
        if ($taxPayment->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل الدفعة المكتملة');
        }

        return view('taxes.payments.edit', compact('taxPayment'));
    }

    public function update(Request $request, TaxPayment $taxPayment)
    {
        if ($taxPayment->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل الدفعة المكتملة');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,online',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $taxPayment->update([
            'amount' => $request->amount,
            'total_amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'reference_number' => $request->reference_number,
            'notes' => $request->notes ? ['text' => $request->notes] : null,
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('taxes.payments.show', $taxPayment)
            ->with('success', 'تم تحديث الدفعة الضريبية بنجاح');
    }

    public function destroy(TaxPayment $taxPayment)
    {
        $taxPayment->delete();

        return redirect()
            ->route('taxes.payments.index')
            ->with('success', 'تم حذف الدفعة الضريبية');
    }

    public function pending()
    {
        $payments = TaxPayment::where('status', 'pending')
            ->with(['propertyTax.property', 'taxFiling', 'user'])
            ->latest()
            ->paginate(20);

        return view('taxes.payments.index', compact('payments'));
    }

    public function overdue()
    {
        $payments = TaxPayment::where('status', 'overdue')
            ->with(['propertyTax.property', 'taxFiling', 'user'])
            ->latest()
            ->paginate(20);

        return view('taxes.payments.index', compact('payments'));
    }

    public function paid()
    {
        $payments = TaxPayment::where('status', 'paid')
            ->with(['propertyTax.property', 'taxFiling', 'user'])
            ->latest()
            ->paginate(20);

        return view('taxes.payments.index', compact('payments'));
    }
}
