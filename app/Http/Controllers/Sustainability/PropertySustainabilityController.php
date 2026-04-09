<?php

namespace App\Http\Controllers\Sustainability;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Sustainability\PropertySustainability;
use App\Models\Sustainability\EcoScore;
use App\Models\Sustainability\SustainabilityReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PropertySustainabilityController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_sustainability')->except(['index', 'show']);
    }

    public function index()
    {
        $sustainabilityRecords = PropertySustainability::with(['property', 'ecoScore'])
            ->when(Auth::user()->hasRole('agent'), function($query) {
                $query->whereHas('property', function($q) {
                    $q->where('agent_id', Auth::id());
                });
            })
            ->latest()
            ->paginate(12);

        $stats = [
            'total_properties' => PropertySustainability::count(),
            'certified_properties' => PropertySustainability::where('certification_status', 'certified')->count(),
            'average_eco_score' => PropertySustainability::avg('eco_score'),
            'high_performance' => PropertySustainability::where('eco_score', '>=', 80)->count(),
        ];

        return view('sustainability.property-dashboard', compact('sustainabilityRecords', 'stats'));
    }

    public function create()
    {
        $properties = Property::whereDoesntHave('sustainability')
            ->when(Auth::user()->hasRole('agent'), function($query) {
                $query->where('agent_id', Auth::id());
            })
            ->get();

        return view('sustainability.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id|unique:property_sustainability,property_id',
            'energy_efficiency_rating' => 'required|integer|min:1|max:100',
            'water_efficiency_rating' => 'required|integer|min:1|max:100',
            'waste_management_score' => 'required|integer|min:1|max:100',
            'green_space_ratio' => 'required|numeric|min:0|max:1',
            'renewable_energy_percentage' => 'required|integer|min:0|max:100',
            'sustainable_materials_percentage' => 'required|integer|min:0|max:100',
            'carbon_footprint' => 'required|numeric|min:0',
            'certification_status' => 'required|in:not_certified,in_progress,certified,expired',
            'last_audit_date' => 'nullable|date',
            'next_audit_date' => 'nullable|date|after:last_audit_date',
            'notes' => 'nullable|string',
        ]);

        // Calculate overall eco score
        $ecoScore = $this->calculateEcoScore($validated);

        $validated['eco_score'] = $ecoScore;
        $validated['created_by'] = Auth::id();

        $sustainability = PropertySustainability::create($validated);

        // Create initial eco score record
        EcoScore::create([
            'property_sustainability_id' => $sustainability->id,
            'overall_score' => $ecoScore,
            'energy_score' => $validated['energy_efficiency_rating'],
            'water_score' => $validated['water_efficiency_rating'],
            'waste_score' => $validated['waste_management_score'],
            'materials_score' => $validated['sustainable_materials_percentage'],
            'calculated_at' => now(),
        ]);

        return redirect()
            ->route('sustainability.show', $sustainability)
            ->with('success', 'تم إنشاء تقييم الاستدامة بنجاح');
    }

    public function show(PropertySustainability $sustainability)
    {
        $sustainability->load(['property', 'ecoScores', 'carbonFootprints', 'greenCertifications', 'energyEfficiency']);
        
        $recentScores = $sustainability->ecoScores()->latest()->take(12)->get();
        $certifications = $sustainability->greenCertifications()->orderBy('issued_date', 'desc')->get();
        
        return view('sustainability.show', compact('sustainability', 'recentScores', 'certifications'));
    }

    public function edit(PropertySustainability $sustainability)
    {
        $this->authorize('update', $sustainability);
        
        return view('sustainability.edit', compact('sustainability'));
    }

    public function update(Request $request, PropertySustainability $sustainability)
    {
        $this->authorize('update', $sustainability);

        $validated = $request->validate([
            'energy_efficiency_rating' => 'required|integer|min:1|max:100',
            'water_efficiency_rating' => 'required|integer|min:1|max:100',
            'waste_management_score' => 'required|integer|min:1|max:100',
            'green_space_ratio' => 'required|numeric|min:0|max:1',
            'renewable_energy_percentage' => 'required|integer|min:0|max:100',
            'sustainable_materials_percentage' => 'required|integer|min:0|max:100',
            'carbon_footprint' => 'required|numeric|min:0',
            'certification_status' => 'required|in:not_certified,in_progress,certified,expired',
            'last_audit_date' => 'nullable|date',
            'next_audit_date' => 'nullable|date|after:last_audit_date',
            'notes' => 'nullable|string',
        ]);

        // Calculate new eco score
        $ecoScore = $this->calculateEcoScore($validated);
        $validated['eco_score'] = $ecoScore;

        $sustainability->update($validated);

        // Create new eco score record
        EcoScore::create([
            'property_sustainability_id' => $sustainability->id,
            'overall_score' => $ecoScore,
            'energy_score' => $validated['energy_efficiency_rating'],
            'water_score' => $validated['water_efficiency_rating'],
            'waste_score' => $validated['waste_management_score'],
            'materials_score' => $validated['sustainable_materials_percentage'],
            'calculated_at' => now(),
        ]);

        return redirect()
            ->route('sustainability.show', $sustainability)
            ->with('success', 'تم تحديث تقييم الاستدامة بنجاح');
    }

    public function destroy(PropertySustainability $sustainability)
    {
        $this->authorize('delete', $sustainability);

        $sustainability->delete();

        return redirect()
            ->route('sustainability.index')
            ->with('success', 'تم حذف تقييم الاستدامة بنجاح');
    }

    public function dashboard()
    {
        $stats = [
            'total_properties' => PropertySustainability::count(),
            'certified_properties' => PropertySustainability::where('certification_status', 'certified')->count(),
            'average_eco_score' => round(PropertySustainability::avg('eco_score'), 1),
            'properties_with_renewable_energy' => PropertySustainability::where('renewable_energy_percentage', '>', 0)->count(),
            'low_carbon_properties' => PropertySustainability::where('carbon_footprint', '<', 50)->count(),
            'high_performance_properties' => PropertySustainability::where('eco_score', '>=', 80)->count(),
        ];

        // Chart data
        $ecoScoreDistribution = PropertySustainability::selectRaw('CASE 
            WHEN eco_score >= 90 THEN "ممتاز (90-100)"
            WHEN eco_score >= 80 THEN "جيد جداً (80-89)"
            WHEN eco_score >= 70 THEN "جيد (70-79)"
            WHEN eco_score >= 60 THEN "متوسط (60-69)"
            ELSE "ضعيف (أقل من 60)"
            END as score_range, COUNT(*) as count')
            ->groupBy('score_range')
            ->orderBy('score_range')
            ->pluck('count', 'score_range');

        $certificationStatus = PropertySustainability::selectRaw('certification_status, COUNT(*) as count')
            ->groupBy('certification_status')
            ->pluck('count', 'certification_status');

        $recentImprovements = EcoScore::with('propertySustainability.property')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('overall_score', 'desc')
            ->take(10)
            ->get();

        return view('sustainability.dashboard', compact('stats', 'ecoScoreDistribution', 'certificationStatus', 'recentImprovements'));
    }

    public function analytics()
    {
        $monthlyTrends = PropertySustainability::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, AVG(eco_score) as avg_score, COUNT(*) as count')
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $performanceByPropertyType = PropertySustainability::join('properties', 'property_sustainability.property_id', '=', 'properties.id')
            ->selectRaw('properties.type, AVG(property_sustainability.eco_score) as avg_score, COUNT(*) as count')
            ->groupBy('properties.type')
            ->get();

        $topPerformingProperties = PropertySustainability::with('property')
            ->orderBy('eco_score', 'desc')
            ->take(10)
            ->get();

        $improvementOpportunities = PropertySustainability::with('property')
            ->where('eco_score', '<', 70)
            ->orderBy('eco_score', 'asc')
            ->take(10)
            ->get();

        return view('sustainability.analytics', compact(
            'monthlyTrends',
            'performanceByPropertyType',
            'topPerformingProperties',
            'improvementOpportunities'
        ));
    }

    public function compare(Request $request)
    {
        $propertyIds = $request->input('properties', []);
        
        if (count($propertyIds) < 2) {
            return back()->with('error', 'يرجى اختيار عقارين على الأقل للمقارنة');
        }

        $properties = PropertySustainability::with(['property', 'ecoScores'])
            ->whereIn('property_id', $propertyIds)
            ->get();

        $comparisonData = $properties->map(function($sustainability) {
            return [
                'property' => $sustainability->property,
                'eco_score' => $sustainability->eco_score,
                'energy_efficiency' => $sustainability->energy_efficiency_rating,
                'water_efficiency' => $sustainability->water_efficiency_rating,
                'waste_management' => $sustainability->waste_management_score,
                'renewable_energy' => $sustainability->renewable_energy_percentage,
                'carbon_footprint' => $sustainability->carbon_footprint,
                'certification_status' => $sustainability->certification_status,
            ];
        });

        return view('sustainability.compare', compact('comparisonData'));
    }

    public function export(Request $request)
    {
        $format = $request->input('format', 'excel');
        
        $sustainabilityData = PropertySustainability::with(['property'])
            ->when($request->input('certification_status'), function($query, $status) {
                $query->where('certification_status', $status);
            })
            ->when($request->input('min_eco_score'), function($query, $score) {
                $query->where('eco_score', '>=', $score);
            })
            ->get();

        // Export logic based on format
        switch ($format) {
            case 'excel':
                return $this->exportToExcel($sustainabilityData);
            case 'pdf':
                return $this->exportToPDF($sustainabilityData);
            default:
                return back()->with('error', 'تنسيق التصدير غير مدعوم');
        }
    }

    private function calculateEcoScore($data)
    {
        $weights = [
            'energy' => 0.25,
            'water' => 0.20,
            'waste' => 0.15,
            'materials' => 0.15,
            'green_space' => 0.10,
            'renewable_energy' => 0.15,
        ];

        $energyScore = $data['energy_efficiency_rating'] * $weights['energy'];
        $waterScore = $data['water_efficiency_rating'] * $weights['water'];
        $wasteScore = $data['waste_management_score'] * $weights['waste'];
        $materialsScore = $data['sustainable_materials_percentage'] * $weights['materials'];
        $greenSpaceScore = $data['green_space_ratio'] * 100 * $weights['green_space'];
        $renewableScore = $data['renewable_energy_percentage'] * $weights['renewable_energy'];

        return round($energyScore + $waterScore + $wasteScore + $materialsScore + $greenSpaceScore + $renewableScore, 1);
    }

    private function exportToExcel($data)
    {
        // Excel export implementation
        return response()->json(['message' => 'Excel export would be implemented here']);
    }

    private function exportToPDF($data)
    {
        // PDF export implementation
        return response()->json(['message' => 'PDF export would be implemented here']);
    }
}
