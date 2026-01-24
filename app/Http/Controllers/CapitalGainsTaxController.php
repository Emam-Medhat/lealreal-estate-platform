<?php

namespace App\Http\Controllers;

use App\Models\CapitalGainsTax;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CapitalGainsTaxController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index(Request $request)
    {
        $query = CapitalGainsTax::with(['property', 'seller', 'buyer']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $taxes = $query->latest()->paginate(20);

        return view('taxes.capital-gains.index', compact('taxes'));
    }

    public function create()
    {
        $properties = Property::all();

        return view('taxes.capital-gains.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'sale_date' => 'required|date|after:purchase_date',
            'improvement_costs' => 'nullable|numeric|min:0',
            'selling_costs' => 'nullable|numeric|min:0',
        ]);

        $gainAmount = $request->sale_price - $request->purchase_price - $request->improvement_costs - $request->selling_costs;
        $holdingPeriod = Carbon::parse($request->purchase_date)->diffInYears(Carbon::parse($request->sale_date));
        
        $taxRate = $holdingPeriod >= 1 ? 0.20 : 0.30; // 20% for long-term, 30% for short-term
        $taxAmount = max(0, $gainAmount * $taxRate);

        $capitalGainsTax = CapitalGainsTax::create([
            'property_id' => $request->property_id,
            'seller_id' => Auth::id(),
            'purchase_price' => $request->purchase_price,
            'sale_price' => $request->sale_price,
            'purchase_date' => $request->purchase_date,
            'sale_date' => $request->sale_date,
            'improvement_costs' => $request->improvement_costs ?? 0,
            'selling_costs' => $request->selling_costs ?? 0,
            'gain_amount' => $gainAmount,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('taxes.capital-gains.show', $capitalGainsTax)
            ->with('success', 'تم حساب ضريبة الأرباح الرأسمالية بنجاح');
    }

    public function show(CapitalGainsTax $capitalGainsTax)
    {
        $capitalGainsTax->load(['property', 'seller', 'buyer', 'payments']);

        return view('taxes.capital-gains.show', compact('capitalGainsTax'));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'sale_date' => 'required|date|after:purchase_date',
            'improvement_costs' => 'nullable|numeric|min:0',
            'selling_costs' => 'nullable|numeric|min:0',
        ]);

        $gainAmount = $request->sale_price - $request->purchase_price - $request->improvement_costs - $request->selling_costs;
        $holdingPeriod = Carbon::parse($request->purchase_date)->diffInYears(Carbon::parse($request->sale_date));
        
        $taxRate = $holdingPeriod >= 1 ? 0.20 : 0.30;
        $taxAmount = max(0, $gainAmount * $taxRate);

        return response()->json([
            'gain_amount' => $gainAmount,
            'holding_period_years' => $holdingPeriod,
            'tax_rate' => $taxRate * 100,
            'tax_amount' => $taxAmount,
            'net_gain' => $gainAmount - $taxAmount,
        ]);
    }
}
