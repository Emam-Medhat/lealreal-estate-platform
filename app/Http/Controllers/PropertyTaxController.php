<?php

namespace App\Http\Controllers;

use App\Models\PropertyTax;
use App\Models\Property;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PropertyTaxController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index(Request $request)
    {
        $query = PropertyTax::with(['property', 'owner', 'taxRate']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('tax_number', 'like', "%{$request->search}%")
                  ->orWhereHas('property', function ($subQ) use ($request) {
                      $subQ->where('title', 'like', "%{$request->search}%");
                  });
            });
        }

        $propertyTaxes = $query->latest()->paginate(20);

        return view('taxes.property.index', compact('propertyTaxes'));
    }

    public function create()
    {
        $properties = Property::all();
        $taxRates = TaxRate::all();

        return view('taxes.property.create', compact('properties', 'taxRates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'tax_rate_id' => 'required|exists:tax_rates,id',
            'assessment_value' => 'required|numeric|min:0',
            'tax_year' => 'required|integer',
        ]);

        $propertyTax = PropertyTax::create([
            'property_id' => $request->property_id,
            'tax_rate_id' => $request->tax_rate_id,
            'assessment_value' => $request->assessment_value,
            'tax_year' => $request->tax_year,
            'tax_amount' => $this->calculateTax($request->assessment_value, $request->tax_rate_id),
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('taxes.property.show', $propertyTax)
            ->with('success', 'تم إنشاء ضريبة العقار بنجاح');
    }

    public function show(PropertyTax $propertyTax)
    {
        $propertyTax->load(['property', 'owner', 'taxRate', 'payments']);

        return view('taxes.property.show', compact('propertyTax'));
    }

    public function edit(PropertyTax $propertyTax)
    {
        $properties = Property::all();
        $taxRates = TaxRate::all();

        return view('taxes.property.edit', compact('propertyTax', 'properties', 'taxRates'));
    }

    public function update(Request $request, PropertyTax $propertyTax)
    {
        $request->validate([
            'assessment_value' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid,overdue,exempt',
        ]);

        $propertyTax->update([
            'assessment_value' => $request->assessment_value,
            'tax_amount' => $this->calculateTax($request->assessment_value, $propertyTax->tax_rate_id),
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('taxes.property.show', $propertyTax)
            ->with('success', 'تم تحديث ضريبة العقار بنجاح');
    }

    public function destroy(PropertyTax $propertyTax)
    {
        $propertyTax->delete();

        return redirect()
            ->route('taxes.property.index')
            ->with('success', 'تم حذف ضريبة العقار بنجاح');
    }

    private function calculateTax($assessmentValue, $taxRateId)
    {
        $taxRate = TaxRate::find($taxRateId);
        return ($assessmentValue * $taxRate->rate) / 100;
    }
}
