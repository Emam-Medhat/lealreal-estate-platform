<?php

namespace App\Http\Controllers;

use App\Models\VatRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VatController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index(Request $request)
    {
        $query = VatRecord::with(['user']);

        if ($request->period) {
            $query->where('period', $request->period);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $records = $query->latest()->paginate(20);

        return view('taxes.vat.index', compact('records'));
    }

    public function create()
    {
        return view('taxes.vat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'period' => 'required|string',
            'taxable_sales' => 'required|numeric|min:0',
            'taxable_purchases' => 'required|numeric|min:0',
            'vat_rate' => 'required|numeric|min:0|max:100',
            'vat_collected' => 'required|numeric|min:0',
            'vat_paid' => 'required|numeric|min:0',
        ]);

        $vatPayable = $request->vat_collected - $request->vat_paid;

        $vatRecord = VatRecord::create([
            'period' => $request->period,
            'taxable_sales' => $request->taxable_sales,
            'taxable_purchases' => $request->taxable_purchases,
            'vat_rate' => $request->vat_rate,
            'vat_collected' => $request->vat_collected,
            'vat_paid' => $request->vat_paid,
            'vat_payable' => $vatPayable,
            'status' => $vatPayable > 0 ? 'payable' : 'refundable',
            'user_id' => Auth::id(),
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('taxes.vat.show', $vatRecord)
            ->with('success', 'تم إنشاء سجل ضريبة القيمة المضافة بنجاح');
    }

    public function show(VatRecord $vatRecord)
    {
        $vatRecord->load(['user']);

        return view('taxes.vat.show', compact('vatRecord'));
    }

    public function calculator()
    {
        return view('taxes.vat.calculator');
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'vat_rate' => 'required|numeric|min:0|max:100',
            'calculation_type' => 'required|in:inclusive,exclusive',
        ]);

        $amount = $request->amount;
        $vatRate = $request->vat_rate / 100;

        if ($request->calculation_type === 'inclusive') {
            // Amount includes VAT
            $netAmount = $amount / (1 + $vatRate);
            $vatAmount = $amount - $netAmount;
        } else {
            // Amount excludes VAT
            $vatAmount = $amount * $vatRate;
            $netAmount = $amount;
            $totalAmount = $amount + $vatAmount;
        }

        return response()->json([
            'net_amount' => $netAmount ?? $totalAmount,
            'vat_amount' => $vatAmount,
            'total_amount' => $totalAmount ?? $amount,
            'vat_rate' => $request->vat_rate,
        ]);
    }

    public function submit(VatRecord $vatRecord)
    {
        $vatRecord->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم تقديم إقرار ضريبة القيمة المضافة');
    }

    public function pay(Request $request, VatRecord $vatRecord)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
        ]);

        $vatRecord->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم سداد ضريبة القيمة المضافة');
    }
}
