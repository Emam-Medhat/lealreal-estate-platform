<?php

namespace App\Http\Controllers;

use App\Models\PropertyCondition;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyConditionController extends Controller
{
    public function index()
    {
        $conditions = PropertyCondition::with(['property'])
            ->latest()
            ->paginate(10);
            
        return view('property-conditions.index', compact('conditions'));
    }

    public function create()
    {
        $properties = Property::all();
        
        return view('property-conditions.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'overall_condition' => 'required|in:excellent,good,fair,poor',
            'structural_condition' => 'required|in:excellent,good,fair,poor',
            'roof_condition' => 'required|in:excellent,good,fair,poor',
            'foundation_condition' => 'required|in:excellent,good,fair,poor',
            'electrical_condition' => 'required|in:excellent,good,fair,poor',
            'plumbing_condition' => 'required|in:excellent,good,fair,poor',
            'hvac_condition' => 'required|in:excellent,good,fair,poor',
            'interior_condition' => 'required|in:excellent,good,fair,poor',
            'exterior_condition' => 'required|in:excellent,good,fair,poor',
            'age_years' => 'required|integer|min:0',
            'last_renovation_year' => 'nullable|integer|min:1900|max:' . now()->year,
            'maintenance_level' => 'required|in:low,medium,high',
            'energy_efficiency' => 'required|in:excellent,good,fair,poor',
            'accessibility_features' => 'nullable|array',
            'accessibility_features.*' => 'string',
            'safety_features' => 'nullable|array',
            'safety_features.*' => 'string',
            'notes' => 'nullable|string',
            'inspection_date' => 'required|date',
            'inspector_id' => 'required|exists:inspectors,id',
        ]);

        $validated['accessibility_features'] = json_encode($validated['accessibility_features'] ?? []);
        $validated['safety_features'] = json_encode($validated['safety_features'] ?? []);

        PropertyCondition::create($validated);

        return redirect()
            ->route('property-conditions.index')
            ->with('success', 'تم إضافة حالة العقار بنجاح');
    }

    public function show(PropertyCondition $condition)
    {
        $condition->load(['property', 'inspector']);
        
        return view('property-conditions.show', compact('condition'));
    }

    public function edit(PropertyCondition $condition)
    {
        $properties = Property::all();
        
        return view('property-conditions.edit', compact('condition', 'properties'));
    }

    public function update(Request $request, PropertyCondition $condition)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'overall_condition' => 'required|in:excellent,good,fair,poor',
            'structural_condition' => 'required|in:excellent,good,fair,poor',
            'roof_condition' => 'required|in:excellent,good,fair,poor',
            'foundation_condition' => 'required|in:excellent,good,fair,poor',
            'electrical_condition' => 'required|in:excellent,good,fair,poor',
            'plumbing_condition' => 'required|in:excellent,good,fair,poor',
            'hvac_condition' => 'required|in:excellent,good,fair,poor',
            'interior_condition' => 'required|in:excellent,good,fair,poor',
            'exterior_condition' => 'required|in:excellent,good,fair,poor',
            'age_years' => 'required|integer|min:0',
            'last_renovation_year' => 'nullable|integer|min:1900|max:' . now()->year,
            'maintenance_level' => 'required|in:low,medium,high',
            'energy_efficiency' => 'required|in:excellent,good,fair,poor',
            'accessibility_features' => 'nullable|array',
            'accessibility_features.*' => 'string',
            'safety_features' => 'nullable|array',
            'safety_features.*' => 'string',
            'notes' => 'nullable|string',
            'inspection_date' => 'required|date',
            'inspector_id' => 'required|exists:inspectors,id',
        ]);

        $validated['accessibility_features'] = json_encode($validated['accessibility_features'] ?? []);
        $validated['safety_features'] = json_encode($validated['safety_features'] ?? []);

        $condition->update($validated);

        return redirect()
            ->route('property-conditions.show', $condition)
            ->with('success', 'تم تحديث حالة العقار بنجاح');
    }

    public function destroy(PropertyCondition $condition)
    {
        $condition->delete();

        return redirect()
            ->route('property-conditions.index')
            ->with('success', 'تم حذف حالة العقار بنجاح');
    }

    public function property(Property $property)
    {
        $conditions = $property->conditions()
            ->with('inspector')
            ->latest()
            ->get();

        return view('property-conditions.property', compact('property', 'conditions'));
    }

    public function report(PropertyCondition $condition)
    {
        $condition->load(['property', 'inspector']);
        
        // Generate condition report PDF
        $pdf = \PDF::loadView('property-conditions.report', compact('condition'));
        
        return $pdf->download('property_condition_report_' . $condition->id . '.pdf');
    }

    public function compare(Request $request)
    {
        $validated = $request->validate([
            'condition_ids' => 'required|array|min:2|max:5',
            'condition_ids.*' => 'exists:property_conditions,id',
        ]);

        $conditions = PropertyCondition::with(['property', 'inspector'])
            ->whereIn('id', $validated['condition_ids'])
            ->get();

        return view('property-conditions.compare', compact('conditions'));
    }

    public function score(PropertyCondition $condition)
    {
        $score = 0;
        $maxScore = 0;

        // Calculate condition score
        $conditionFields = [
            'overall_condition',
            'structural_condition',
            'roof_condition',
            'foundation_condition',
            'electrical_condition',
            'plumbing_condition',
            'hvac_condition',
            'interior_condition',
            'exterior_condition',
        ];

        foreach ($conditionFields as $field) {
            $maxScore += 4;
            switch ($condition->$field) {
                case 'excellent':
                    $score += 4;
                    break;
                case 'good':
                    $score += 3;
                    break;
                case 'fair':
                    $score += 2;
                    break;
                case 'poor':
                    $score += 1;
                    break;
            }
        }

        // Add energy efficiency score
        $maxScore += 4;
        switch ($condition->energy_efficiency) {
            case 'excellent':
                $score += 4;
                break;
            case 'good':
                $score += 3;
                break;
            case 'fair':
                $score += 2;
                break;
            case 'poor':
                $score += 1;
                break;
        }

        // Calculate percentage
        $percentage = ($score / $maxScore) * 100;

        return response()->json([
            'score' => $score,
            'max_score' => $maxScore,
            'percentage' => round($percentage, 2),
            'grade' => $this->getGrade($percentage),
        ]);
    }

    private function getGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 85) return 'A';
        if ($percentage >= 80) return 'B+';
        if ($percentage >= 75) return 'B';
        if ($percentage >= 70) return 'C+';
        if ($percentage >= 65) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $conditions = PropertyCondition::with(['property', 'inspector'])
            ->whereBetween('inspection_date', [$validated['start_date'], $validated['end_date']])
            ->get();

        $csvData = [];
        $csvData[] = ['العقار', 'الحالة العامة', 'تاريخ الفحص', 'المفتش', 'الدرجة'];

        foreach ($conditions as $condition) {
            $csvData[] = [
                $condition->property->title,
                $condition->overall_condition,
                $condition->inspection_date->format('Y-m-d'),
                $condition->inspector->name,
                $this->calculateOverallScore($condition),
            ];
        }

        $filename = "property_conditions_report.csv";
        $handle = fopen($filename, 'w');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    private function calculateOverallScore($condition)
    {
        $score = 0;
        $fields = ['overall_condition', 'structural_condition', 'roof_condition'];
        
        foreach ($fields as $field) {
            switch ($condition->$field) {
                case 'excellent':
                    $score += 4;
                    break;
                case 'good':
                    $score += 3;
                    break;
                case 'fair':
                    $score += 2;
                    break;
                case 'poor':
                    $score += 1;
                    break;
            }
        }
        
        return round(($score / 12) * 100, 1);
    }
}
