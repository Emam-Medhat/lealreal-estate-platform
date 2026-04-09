<?php

namespace App\Http\Controllers;

use App\Models\CarbonFootprint;
use App\Models\SmartProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CarbonFootprintController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_properties' => CarbonFootprint::count(),
            'active_tracking' => CarbonFootprint::where('status', 'active')->count(),
            'average_carbon_footprint' => CarbonFootprint::avg('total_carbon'),
            'total_carbon_reduction' => $this->getTotalCarbonReduction(),
            'properties_by_status' => $this->getPropertiesByStatus(),
            'monthly_trends' => $this->getMonthlyCarbonTrends(),
        ];

        $recentFootprints = CarbonFootprint::with(['property'])
            ->latest()
            ->take(10)
            ->get();

        $reductionTargets = $this->getReductionTargets();
        $topPerformers = $this->getTopPerformers();

        return view('sustainability.carbon-dashboard', compact(
            'stats', 
            'recentFootprints', 
            'reductionTargets', 
            'topPerformers'
        ));
    }

    public function index(Request $request)
    {
        $query = CarbonFootprint::with(['property']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('assessment_date_from')) {
            $query->whereDate('assessment_date', '>=', $request->assessment_date_from);
        }

        if ($request->filled('assessment_date_to')) {
            $query->whereDate('assessment_date', '<=', $request->assessment_date_to);
        }

        if ($request->filled('carbon_min')) {
            $query->where('total_carbon', '>=', $request->carbon_min);
        }

        if ($request->filled('carbon_max')) {
            $query->where('total_carbon', '<=', $request->carbon_max);
        }

        $footprints = $query->latest()->paginate(12);

        $statuses = ['active', 'reducing', 'target_met', 'exceeded'];

        return view('sustainability.carbon-index', compact(
            'footprints', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = SmartProperty::all();

        return view('sustainability.carbon-create', compact(
            'properties'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $carbonData = $request->validate([
                'property_id' => 'required|exists:smart_properties,id',
                'total_carbon' => 'required|numeric|min:0',
                'energy_carbon' => 'required|numeric|min:0',
                'transport_carbon' => 'required|numeric|min:0',
                'waste_carbon' => 'required|numeric|min:0',
                'water_carbon' => 'required|numeric|min:0',
                'materials_carbon' => 'required|numeric|min:0',
                'carbon_sources' => 'nullable|array',
                'reduction_measures' => 'nullable|array',
                'baseline_carbon' => 'required|numeric|min:0',
                'reduction_target' => 'required|numeric|min:0|max:100',
                'assessment_date' => 'required|date',
                'next_assessment_date' => 'nullable|date|after:assessment_date',
                'status' => 'required|in:active,reducing,target_met,exceeded',
            ]);

            $carbonData['created_by'] = auth()->id();
            $carbonData['carbon_sources'] = $this->generateCarbonSources($request);
            $carbonData['reduction_measures'] = $this->generateReductionMeasures($request);

            $footprint = CarbonFootprint::create($carbonData);

            DB::commit();

            return redirect()
                ->route('carbon-footprint.show', $footprint)
                ->with('success', 'تم إضافة البصمة الكربونية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة البصمة: ' . $e->getMessage());
        }
    }

    public function show(CarbonFootprint $footprint)
    {
        $footprint->load(['property', 'reductionMeasures']);
        $carbonBreakdown = $this->getCarbonBreakdown($footprint);
        $reductionProgress = $this->getReductionProgress($footprint);
        $recommendations = $this->getCarbonReductionRecommendations($footprint);

        return view('sustainability.carbon-show', compact(
            'footprint', 
            'carbonBreakdown', 
            'reductionProgress', 
            'recommendations'
        ));
    }

    public function edit(CarbonFootprint $footprint)
    {
        $properties = SmartProperty::all();

        return view('sustainability.carbon-edit', compact(
            'footprint', 
            'properties'
        ));
    }

    public function update(Request $request, CarbonFootprint $footprint)
    {
        DB::beginTransaction();
        try {
            $carbonData = $request->validate([
                'total_carbon' => 'required|numeric|min:0',
                'energy_carbon' => 'required|numeric|min:0',
                'transport_carbon' => 'required|numeric|min:0',
                'waste_carbon' => 'required|numeric|min:0',
                'water_carbon' => 'required|numeric|min:0',
                'materials_carbon' => 'required|numeric|min:0',
                'carbon_sources' => 'nullable|array',
                'reduction_measures' => 'nullable|array',
                'reduction_target' => 'required|numeric|min:0|max:100',
                'assessment_date' => 'required|date',
                'next_assessment_date' => 'nullable|date|after:assessment_date',
                'status' => 'required|in:active,reducing,target_met,exceeded',
            ]);

            $carbonData['updated_by'] = auth()->id();
            $carbonData['carbon_sources'] = $this->generateCarbonSources($request);
            $carbonData['reduction_measures'] = $this->generateReductionMeasures($request);

            $footprint->update($carbonData);

            DB::commit();

            return redirect()
                ->route('carbon-footprint.show', $footprint)
                ->with('success', 'تم تحديث البصمة الكربونية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث البصمة: ' . $e->getMessage());
        }
    }

    public function destroy(CarbonFootprint $footprint)
    {
        try {
            $footprint->delete();

            return redirect()
                ->route('carbon-footprint.index')
                ->with('success', 'تم حذف البصمة الكربونية بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف البصمة: ' . $e->getMessage());
        }
    }

    public function calculateCarbon(Request $request)
    {
        $propertyId = $request->input('property_id');
        $calculationData = $request->input('calculation_data', []);

        $carbonFootprint = $this->performCarbonCalculation($propertyId, $calculationData);

        return response()->json([
            'success' => true,
            'carbon_footprint' => $carbonFootprint
        ]);
    }

    public function getReductionPlan(CarbonFootprint $footprint)
    {
        $plan = $this->generateReductionPlan($footprint);

        return response()->json([
            'success' => true,
            'reduction_plan' => $plan
        ]);
    }

    public function trackProgress(CarbonFootprint $footprint)
    {
        $progress = $this->calculateReductionProgress($footprint);

        return response()->json([
            'success' => true,
            'progress' => $progress
        ]);
    }

    public function generateReport(CarbonFootprint $footprint)
    {
        try {
            $reportData = $this->generateCarbonReport($footprint);
            
            return response()->json([
                'success' => true,
                'report' => $reportData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateCarbonSources($request)
    {
        return [
            'energy_consumption' => $request->input('energy_consumption_kwh', 0),
            'transportation' => $request->input('transportation_km', 0),
            'waste_disposal' => $request->input('waste_disposal_kg', 0),
            'water_usage' => $request->input('water_usage_m3', 0),
            'materials_usage' => $request->input('materials_usage_kg', 0),
            'last_updated' => now()->toDateTimeString(),
        ];
    }

    private function generateReductionMeasures($request)
    {
        return [
            'energy_efficiency' => $request->input('energy_efficiency_measures', []),
            'renewable_energy' => $request->input('renewable_energy_measures', []),
            'transport_alternatives' => $request->input('transport_alternatives', []),
            'waste_reduction' => $request->input('waste_reduction_measures', []),
            'water_conservation' => $request->input('water_conservation_measures', []),
            'sustainable_materials' => $request->input('sustainable_materials_measures', []),
        ];
    }

    private function getTotalCarbonReduction()
    {
        return CarbonFootprint::sum(DB::raw('baseline_carbon - total_carbon'));
    }

    private function getPropertiesByStatus()
    {
        return CarbonFootprint::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
    }

    private function getMonthlyCarbonTrends()
    {
        return CarbonFootprint::selectRaw('MONTH(assessment_date) as month, AVG(total_carbon) as avg_carbon')
            ->whereYear('assessment_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getReductionTargets()
    {
        return CarbonFootprint::select('property_id', 'reduction_target', DB::raw('AVG(total_carbon) as avg_carbon'))
            ->groupBy('property_id')
            ->having('reduction_target', '>', 0)
            ->get();
    }

    private function getTopPerformers()
    {
        return CarbonFootprint::with(['property'])
            ->select('property_id', DB::raw('AVG(total_carbon) as avg_carbon'))
            ->groupBy('property_id')
            ->orderBy('avg_carbon', 'asc')
            ->take(5)
            ->get();
    }

    private function getCarbonBreakdown($footprint)
    {
        return [
            'energy_percentage' => $footprint->energy_carbon > 0 ? ($footprint->energy_carbon / $footprint->total_carbon) * 100 : 0,
            'transport_percentage' => $footprint->transport_carbon > 0 ? ($footprint->transport_carbon / $footprint->total_carbon) * 100 : 0,
            'waste_percentage' => $footprint->waste_carbon > 0 ? ($footprint->waste_carbon / $footprint->total_carbon) * 100 : 0,
            'water_percentage' => $footprint->water_carbon > 0 ? ($footprint->water_carbon / $footprint->total_carbon) * 100 : 0,
            'materials_percentage' => $footprint->materials_carbon > 0 ? ($footprint->materials_carbon / $footprint->total_carbon) * 100 : 0,
        ];
    }

    private function getReductionProgress($footprint)
    {
        $targetReduction = $footprint->baseline_carbon * ($footprint->reduction_target / 100);
        $actualReduction = $footprint->baseline_carbon - $footprint->total_carbon;
        
        return [
            'target_reduction' => $targetReduction,
            'actual_reduction' => $actualReduction,
            'progress_percentage' => $targetReduction > 0 ? ($actualReduction / $targetReduction) * 100 : 0,
            'on_track' => $actualReduction >= $targetReduction,
        ];
    }

    private function getCarbonReductionRecommendations($footprint)
    {
        $recommendations = [];

        if ($footprint->energy_carbon > ($footprint->total_carbon * 0.4)) {
            $recommendations[] = 'تحسين كفاءة الطاقة واستخدام مصادر الطاقة المتجددة';
        }

        if ($footprint->transport_carbon > ($footprint->total_carbon * 0.2)) {
            $recommendations[] = 'تشجيع وسائل النقل المستدامة والعمل عن بعد';
        }

        if ($footprint->waste_carbon > ($footprint->total_carbon * 0.15)) {
            $recommendations[] = 'تطبيق برامج إعادة التدوير وتقليل النفايات';
        }

        if ($footprint->water_carbon > ($footprint->total_carbon * 0.1)) {
            $recommendations[] = 'تركيب أجهزة حفظ المياه وإصلاح التسريبات';
        }

        if ($footprint->materials_carbon > ($footprint->total_carbon * 0.15)) {
            $recommendations[] = 'استخدام المواد المستدامة والمحلية';
        }

        return $recommendations;
    }

    private function performCarbonCalculation($propertyId, $data)
    {
        $energyFactor = 0.5; // kg CO2 per kWh
        $transportFactor = 0.2; // kg CO2 per km
        $wasteFactor = 0.3; // kg CO2 per kg waste
        $waterFactor = 0.0003; // kg CO2 per liter water

        $energyCarbon = ($data['energy_consumption'] ?? 0) * $energyFactor;
        $transportCarbon = ($data['transport_distance'] ?? 0) * $transportFactor;
        $wasteCarbon = ($data['waste_amount'] ?? 0) * $wasteFactor;
        $waterCarbon = ($data['water_consumption'] ?? 0) * $waterFactor;

        return [
            'property_id' => $propertyId,
            'total_carbon' => $energyCarbon + $transportCarbon + $wasteCarbon + $waterCarbon,
            'energy_carbon' => $energyCarbon,
            'transport_carbon' => $transportCarbon,
            'waste_carbon' => $wasteCarbon,
            'water_carbon' => $waterCarbon,
            'calculation_date' => now()->toDateString(),
        ];
    }

    private function generateReductionPlan($footprint)
    {
        return [
            'current_footprint' => $footprint->total_carbon,
            'target_footprint' => $footprint->baseline_carbon * (1 - $footprint->reduction_target / 100),
            'reduction_needed' => $footprint->total_carbon - ($footprint->baseline_carbon * (1 - $footprint->reduction_target / 100)),
            'timeline_months' => 12,
            'monthly_reduction_target' => ($footprint->baseline_carbon * ($footprint->reduction_target / 100)) / 12,
            'measures' => $footprint->reduction_measures ?? [],
        ];
    }

    private function calculateReductionProgress($footprint)
    {
        return $this->getReductionProgress($footprint);
    }

    private function generateCarbonReport($footprint)
    {
        return [
            'report_id' => uniqid('carbon_report_'),
            'property_name' => $footprint->property->property_name,
            'assessment_date' => $footprint->assessment_date->toDateString(),
            'total_carbon' => $footprint->total_carbon,
            'baseline_carbon' => $footprint->baseline_carbon,
            'reduction_target' => $footprint->reduction_target,
            'reduction_achieved' => $footprint->getReductionPercentage(),
            'carbon_sources' => $footprint->carbon_sources,
            'reduction_measures' => $footprint->reduction_measures,
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
