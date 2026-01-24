<?php

namespace App\Http\Controllers;

use App\Models\TaxAssessment;
use App\Models\PropertyTax;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxAssessmentController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index(Request $request)
    {
        $query = TaxAssessment::with(['propertyTax.property', 'assessor']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $assessments = $query->latest()->paginate(20);

        return view('taxes.assessments.index', compact('assessments'));
    }

    public function create()
    {
        $properties = Property::all();

        return view('taxes.assessments.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'assessment_value' => 'required|numeric|min:0',
            'market_value' => 'required|numeric|min:0',
            'assessment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $assessment = TaxAssessment::create([
            'property_id' => $request->property_id,
            'assessment_value' => $request->assessment_value,
            'market_value' => $request->market_value,
            'assessment_date' => $request->assessment_date,
            'notes' => $request->notes,
            'assessor_id' => Auth::id(),
            'status' => 'completed',
        ]);

        return redirect()
            ->route('taxes.assessments.show', $assessment)
            ->with('success', 'تم إنشاء التقييم الضريبي بنجاح');
    }

    public function show(TaxAssessment $taxAssessment)
    {
        $taxAssessment->load(['propertyTax.property', 'assessor']);

        return view('taxes.assessments.show', compact('taxAssessment'));
    }
}
