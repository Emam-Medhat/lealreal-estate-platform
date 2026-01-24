<?php

namespace App\Http\Controllers;

use App\Models\TaxExemption;
use App\Models\PropertyTax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxExemptionController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index(Request $request)
    {
        $query = TaxExemption::with(['propertyTax.property', 'user']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->exemption_type) {
            $query->where('exemption_type', $request->exemption_type);
        }

        $exemptions = $query->latest()->paginate(20);

        return view('taxes.exemptions.index', compact('exemptions'));
    }

    public function create()
    {
        $propertyTaxes = PropertyTax::where('status', 'pending')->get();
        $exemptionTypes = ['senior_citizen', 'disability', 'veteran', 'primary_residence', 'agricultural', 'charitable'];

        return view('taxes.exemptions.create', compact('propertyTaxes', 'exemptionTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_tax_id' => 'required|exists:property_taxes,id',
            'exemption_type' => 'required|string',
            'exemption_amount' => 'required|numeric|min:0',
            'reason' => 'required|string',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $exemption = TaxExemption::create([
            'property_tax_id' => $request->property_tax_id,
            'user_id' => Auth::id(),
            'exemption_type' => $request->exemption_type,
            'exemption_amount' => $request->exemption_amount,
            'reason' => $request->reason,
            'status' => 'pending',
            'application_date' => now(),
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('taxes.exemptions.show', $exemption)
            ->with('success', 'تم تقديم طلب الإعفاء الضريبي بنجاح');
    }

    public function show(TaxExemption $taxExemption)
    {
        $taxExemption->load(['propertyTax.property', 'user', 'reviewer']);

        return view('taxes.exemptions.show', compact('taxExemption'));
    }

    public function approve(Request $request, TaxExemption $taxExemption)
    {
        $request->validate([
            'approved_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $taxExemption->update([
            'status' => 'approved',
            'approved_amount' => $request->approved_amount,
            'approved_date' => now(),
            'approved_by' => Auth::id(),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'تم اعتماد الإعفاء الضريبي');
    }

    public function reject(Request $request, TaxExemption $taxExemption)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $taxExemption->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejected_date' => now(),
            'rejected_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم رفض طلب الإعفاء');
    }
}
