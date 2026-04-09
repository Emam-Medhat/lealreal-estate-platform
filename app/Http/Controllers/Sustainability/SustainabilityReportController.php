<?php

namespace App\Http\Controllers\Sustainability;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Sustainability\PropertySustainability;
use App\Models\Sustainability\SustainabilityReport;
use App\Http\Requests\Sustainability\GenerateSustainabilityReportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PDF;

class SustainabilityReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_sustainability');
    }

    public function index()
    {
        $reports = SustainabilityReport::with(['propertySustainability.property', 'generatedBy'])
            ->when(Auth::user()->hasRole('agent'), function($query) {
                $query->whereHas('propertySustainability.property', function($q) {
                    $q->where('agent_id', Auth::id());
                });
            })
            ->latest('generated_at')
            ->paginate(15);

        $stats = [
            'total_reports' => SustainabilityReport::count(),
            'reports_this_month' => SustainabilityReport::whereMonth('generated_at', now()->month)->count(),
            'comprehensive_reports' => SustainabilityReport::where('report_type', 'comprehensive')->count(),
            'certification_reports' => SustainabilityReport::where('report_type', 'certification')->count(),
        ];

        return view('sustainability.reports.index', compact('reports', 'stats'));
    }

    public function create()
    {
        $properties = PropertySustainability::with('property')
            ->when(Auth::user()->hasRole('agent'), function($query) {
                $query->whereHas('property', function($q) {
                    $q->where('agent_id', Auth::id());
                });
            })
            ->get();

        $reportTypes = [
            'comprehensive' => 'تقرير شامل للاستدامة',
            'certification' => 'تقرير شهادة خضراء',
            'carbon_footprint' => 'تقرير البصمة الكربونية',
            'energy_efficiency' => 'تقرير كفاءة الطاقة',
            'water_conservation' => 'تقرير حفظ المياه',
            'materials_assessment' => 'تقرير تقييم المواد',
            'climate_impact' => 'تقرير التأثير المناخي',
            'performance' => 'تقرير الأداء',
            'compliance' => 'تقرير الامتثال',
            'benchmarking' => 'تقرير المقارنة المعيارية',
        ];

        return view('sustainability.reports.create', compact('properties', 'reportTypes'));
    }

    public function store(GenerateSustainabilityReportRequest $request)
    {
        $validated = $request->validated();

        // Generate report data
        $reportData = $this->generateReportData($validated);

        // Create report record
        $report = SustainabilityReport::create([
            'property_sustainability_id' => $validated['property_sustainability_id'],
            'report_type' => $validated['report_type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'report_period_start' => $validated['report_period_start'],
            'report_period_end' => $validated['report_period_end'],
            'data_sources' => $validated['data_sources'] ?? [],
            'methodology' => $validated['methodology'] ?? 'Standard assessment methodology',
            'key_findings' => $reportData['key_findings'],
            'recommendations' => $reportData['recommendations'],
            'performance_scores' => $reportData['performance_scores'],
            'compliance_status' => $reportData['compliance_status'],
            'benchmark_comparison' => $reportData['benchmark_comparison'],
            'trend_analysis' => $reportData['trend_analysis'],
            'improvement_opportunities' => $reportData['improvement_opportunities'],
            'risk_assessment' => $reportData['risk_assessment'],
            'certification_eligibility' => $reportData['certification_eligibility'],
            'executive_summary' => $reportData['executive_summary'],
            'detailed_analysis' => $reportData['detailed_analysis'],
            'appendices' => $reportData['appendices'],
            'generated_by' => Auth::id(),
            'status' => 'generated',
            'file_path' => null, // Will be set after file generation
            'file_size' => 0,
            'download_count' => 0,
        ]);

        // Generate PDF report
        $pdfPath = $this->generatePDFReport($report, $reportData);
        
        // Update report with file path
        $report->update([
            'file_path' => $pdfPath,
            'file_size' => Storage::disk('public')->size($pdfPath),
        ]);

        return redirect()
            ->route('sustainability-reports.show', $report)
            ->with('success', 'تم إنشاء تقرير الاستدامة بنجاح');
    }

    public function show(SustainabilityReport $report)
    {
        $report->load(['propertySustainability.property', 'generatedBy']);
        
        // Get report statistics
        $reportStats = $this->getReportStatistics($report);

        return view('sustainability.reports.show', compact('report', 'reportStats'));
    }

    public function edit(SustainabilityReport $report)
    {
        $report->load('propertySustainability.property');
        
        $reportTypes = [
            'comprehensive' => 'تقرير شامل للاستدامة',
            'certification' => 'تقرير شهادة خضراء',
            'carbon_footprint' => 'تقرير البصمة الكربونية',
            'energy_efficiency' => 'تقرير كفاءة الطاقة',
            'water_conservation' => 'تقرير حفظ المياه',
            'materials_assessment' => 'تقرير تقييم المواد',
            'climate_impact' => 'تقرير التأثير المناخي',
            'performance' => 'تقرير الأداء',
            'compliance' => 'تقرير الامتثال',
            'benchmarking' => 'تقرير المقارنة المعيارية',
        ];

        return view('sustainability.reports.edit', compact('report', 'reportTypes'));
    }

    public function update(Request $request, SustainabilityReport $report)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'report_period_start' => 'required|date',
            'report_period_end' => 'required|date|after:report_period_start',
            'data_sources' => 'nullable|array',
            'data_sources.*' => 'string',
            'methodology' => 'nullable|string|max:1000',
            'key_findings' => 'nullable|array',
            'key_findings.*' => 'string',
            'recommendations' => 'nullable|array',
            'recommendations.*' => 'string',
            'notes' => 'nullable|string',
        ]);

        $report->update($validated);

        return redirect()
            ->route('sustainability-reports.show', $report)
            ->with('success', 'تم تحديث تقرير الاستدامة بنجاح');
    }

    public function destroy(SustainabilityReport $report)
    {
        // Delete report file
        if ($report->file_path) {
            Storage::disk('public')->delete($report->file_path);
        }

        $report->delete();

        return redirect()
            ->route('sustainability-reports.index')
            ->with('success', 'تم حذف تقرير الاستدامة بنجاح');
    }

    public function download(SustainabilityReport $report)
    {
        if (!$report->file_path || !Storage::disk('public')->exists($report->file_path)) {
            return back()->with('error', 'ملف التقرير غير متاح');
        }

        // Increment download count
        $report->increment('download_count');

        return Storage::disk('public')->download($report->file_path, $report->title . '.pdf');
    }

    public function preview(SustainabilityReport $report)
    {
        $report->load(['propertySustainability.property']);
        
        // Get preview data
        $previewData = $this->getPreviewData($report);

        return view('sustainability.reports.preview', compact('report', 'previewData'));
    }

    public function regenerate(SustainabilityReport $report)
    {
        // Generate new report data
        $reportData = $this->generateReportData([
            'property_sustainability_id' => $report->property_sustainability_id,
            'report_type' => $report->report_type,
            'report_period_start' => $report->report_period_start,
            'report_period_end' => $report->report_period_end,
        ]);

        // Update report data
        $report->update([
            'key_findings' => $reportData['key_findings'],
            'recommendations' => $reportData['recommendations'],
            'performance_scores' => $reportData['performance_scores'],
            'compliance_status' => $reportData['compliance_status'],
            'benchmark_comparison' => $reportData['benchmark_comparison'],
            'trend_analysis' => $reportData['trend_analysis'],
            'improvement_opportunities' => $reportData['improvement_opportunities'],
            'risk_assessment' => $reportData['risk_assessment'],
            'certification_eligibility' => $reportData['certification_eligibility'],
            'executive_summary' => $reportData['executive_summary'],
            'detailed_analysis' => $reportData['detailed_analysis'],
            'appendices' => $reportData['appendices'],
        ]);

        // Generate new PDF
        if ($report->file_path) {
            Storage::disk('public')->delete($report->file_path);
        }
        
        $pdfPath = $this->generatePDFReport($report, $reportData);
        
        $report->update([
            'file_path' => $pdfPath,
            'file_size' => Storage::disk('public')->size($pdfPath),
            'generated_at' => now(),
        ]);

        return redirect()
            ->route('sustainability-reports.show', $report)
            ->with('success', 'تم إعادة إنشاء تقرير الاستدامة بنجاح');
    }

    public function analytics()
    {
        $reportTypeStats = SustainabilityReport::selectRaw('report_type, COUNT(*) as count, AVG(file_size) as avg_size')
            ->groupBy('report_type')
            ->get();

        $monthlyTrends = SustainabilityReport::selectRaw('DATE_FORMAT(generated_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('generated_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $downloadStats = SustainabilityReport::selectRaw('report_type, SUM(download_count) as total_downloads, AVG(download_count) as avg_downloads')
            ->groupBy('report_type')
            ->get();

        $topProperties = SustainabilityReport::join('property_sustainability', 'sustainability_reports.property_sustainability_id', '=', 'property_sustainability.id')
            ->join('properties', 'property_sustainability.property_id', '=', 'properties.id')
            ->selectRaw('properties.name, properties.address, COUNT(*) as report_count')
            ->groupBy('properties.id', 'properties.name', 'properties.address')
            ->orderBy('report_count', 'desc')
            ->take(10)
            ->get();

        return view('sustainability.reports.analytics', compact(
            'reportTypeStats',
            'monthlyTrends',
            'downloadStats',
            'topProperties'
        ));
    }

    public function templates()
    {
        $templates = [
            'comprehensive' => [
                'name' => 'تقرير الاستدامة الشامل',
                'description' => 'تقرير شامل يغطي جميع جوانب الاستدامة',
                'sections' => ['ملخص تنفيذي', 'تحليل الأداء', 'المقارنة المعيارية', 'التوصيات'],
            ],
            'certification' => [
                'name' => 'تقرير الشهادة الخضراء',
                'description' => 'تقرير مخصص للحصول على شهادات خضراء',
                'sections' => ['متطلبات الشهادة', 'التقييم', 'الامتثال', 'التوصيات'],
            ],
            'performance' => [
                'name' => 'تقرير الأداء',
                'description' => 'تقرير يركز على أداء الاستدامة',
                'sections' => ['مؤشرات الأداء', 'التحليل', 'الاتجاهات', 'الأهداف'],
            ],
        ];

        return view('sustainability.reports.templates', compact('templates'));
    }

    private function generateReportData($data)
    {
        $propertySustainability = PropertySustainability::find($data['property_sustainability_id']);
        
        $reportData = [
            'key_findings' => $this->generateKeyFindings($propertySustainability, $data['report_type']),
            'recommendations' => $this->generateRecommendations($propertySustainability, $data['report_type']),
            'performance_scores' => $this->getPerformanceScores($propertySustainability),
            'compliance_status' => $this->getComplianceStatus($propertySustainability),
            'benchmark_comparison' => $this->getBenchmarkComparison($propertySustainability),
            'trend_analysis' => $this->getTrendAnalysis($propertySustainability, $data),
            'improvement_opportunities' => $this->getImprovementOpportunities($propertySustainability),
            'risk_assessment' => $this->getRiskAssessment($propertySustainability),
            'certification_eligibility' => $this->getCertificationEligibility($propertySustainability),
            'executive_summary' => $this->generateExecutiveSummary($propertySustainability),
            'detailed_analysis' => $this->generateDetailedAnalysis($propertySustainability, $data['report_type']),
            'appendices' => $this->generateAppendices($propertySustainability),
        ];

        return $reportData;
    }

    private function generatePDFReport($report, $reportData)
    {
        $filename = 'sustainability-reports/' . $report->id . '_' . time() . '.pdf';
        
        $pdf = PDF::loadView('sustainability.reports.pdf', compact('report', 'reportData'));
        
        Storage::disk('public')->put($filename, $pdf->output());
        
        return $filename;
    }

    private function generateKeyFindings($propertySustainability, $reportType)
    {
        $findings = [];
        
        if ($propertySustainability->eco_score >= 80) {
            $findings[] = 'أداء استدامة ممتاز مع درجة بيئية عالية';
        } elseif ($propertySustainability->eco_score >= 60) {
            $findings[] = 'أداء استدامة جيد مع مجال للتحسين';
        } else {
            $findings[] = 'يحتاج إلى تحسينات كبيرة في أداء الاستدامة';
        }
        
        if ($propertySustainability->certification_status === 'certified') {
            $findings[] = 'العقار حاصل على شهادة خضراء معتمدة';
        }
        
        return $findings;
    }

    private function generateRecommendations($propertySustainability, $reportType)
    {
        $recommendations = [];
        
        if ($propertySustainability->energy_efficiency_rating < 70) {
            $recommendations[] = 'تحسين كفاءة الطاقة من خلال تركيب ألواح شمسية وتحسين العزل';
        }
        
        if ($propertySustainability->water_efficiency_rating < 70) {
            $recommendations[] = 'تحسين كفاءة المياه من خلال أنظمة تجميع المياه وإعادة التدوير';
        }
        
        if ($propertySustainability->carbon_footprint > 50) {
            $recommendations[] = 'تقليل البصمة الكربونية من خلال مصادر الطاقة المتجددة';
        }
        
        return $recommendations;
    }

    private function getPerformanceScores($propertySustainability)
    {
        return [
            'eco_score' => $propertySustainability->eco_score,
            'energy_efficiency' => $propertySustainability->energy_efficiency_rating,
            'water_efficiency' => $propertySustainability->water_efficiency_rating,
            'waste_management' => $propertySustainability->waste_management_score,
            'materials_percentage' => $propertySustainability->sustainable_materials_percentage,
            'carbon_footprint' => $propertySustainability->carbon_footprint,
        ];
    }

    private function getComplianceStatus($propertySustainability)
    {
        return [
            'certified' => $propertySustainability->certification_status === 'certified',
            'compliance_score' => $propertySustainability->eco_score >= 70 ? 'متوافق' : 'غير متوافق',
            'next_audit' => $propertySustainability->next_audit_date,
        ];
    }

    private function getBenchmarkComparison($propertySustainability)
    {
        // Simplified benchmark comparison
        return [
            'industry_average' => 65,
            'property_score' => $propertySustainability->eco_score,
            'percentile' => min(95, max(5, ($propertySustainability->eco_score / 100) * 100)),
        ];
    }

    private function getTrendAnalysis($propertySustainability, $data)
    {
        // Simplified trend analysis
        return [
            'trend' => 'improving',
            'change_percentage' => '+15%',
            'period_comparison' => 'مقارنة بالفترة السابقة',
        ];
    }

    private function getImprovementOpportunities($propertySustainability)
    {
        $opportunities = [];
        
        if ($propertySustainability->renewable_energy_percentage < 50) {
            $opportunities[] = 'زيادة الطاقة المتجددة لتقليل الاعتماد على الشبكة';
        }
        
        if ($propertySustainability->green_space_ratio < 0.3) {
            $opportunities[] = 'زيادة المساحات الخضراء لتحسين البيئة المحلية';
        }
        
        return $opportunities;
    }

    private function getRiskAssessment($propertySustainability)
    {
        return [
            'overall_risk' => 'low',
            'climate_risk' => 'moderate',
            'regulatory_risk' => 'low',
            'market_risk' => 'low',
        ];
    }

    private function getCertificationEligibility($propertySustainability)
    {
        $eligibility = [];
        
        if ($propertySustainability->eco_score >= 90) {
            $eligibility[] = 'LEED Platinum';
            $eligibility[] = 'BREEAM Outstanding';
        } elseif ($propertySustainability->eco_score >= 80) {
            $eligibility[] = 'LEED Gold';
            $eligibility[] = 'BREEAM Excellent';
        } elseif ($propertySustainability->eco_score >= 70) {
            $eligibility[] = 'LEED Silver';
            $eligibility[] = 'BREEAM Very Good';
        }
        
        return $eligibility;
    }

    private function generateExecutiveSummary($propertySustainability)
    {
        return "هذا التقرير يقدم تقييماً شاملاً لأداء الاستدامة للعقار مع درجة بيئية إجمالية تبلغ " . 
               $propertySustainability->eco_score . " من 100. العقار يظهر " . 
               ($propertySustainability->eco_score >= 70 ? 'أداءً جيداً' : 'حاجة إلى تحسينات') . 
               " في مجالات الاستدامة المختلفة.";
    }

    private function generateDetailedAnalysis($propertySustainability, $reportType)
    {
        return [
            'energy_analysis' => 'تحليل مفصل لاستهلاك الطاقة وكفاءتها',
            'water_analysis' => 'تحليل استهلاك المياه وكفاءة استخدامها',
            'materials_analysis' => 'تحليل المواد المستدامة المستخدمة',
            'carbon_analysis' => 'تحليل البصمة الكربونية وتأثيرها',
        ];
    }

    private function generateAppendices($propertySustainability)
    {
        return [
            'data_sources' => ['سجلات العقار', 'قياسات الأداء', 'بيانات الطقس'],
            'methodology' => 'منهجية التقييم القياسية للاستدامة',
            'references' => ['معايير LEED', 'معايير BREEAM', 'إرشادات الاستدامة المحلية'],
        ];
    }

    private function getReportStatistics($report)
    {
        return [
            'file_size_mb' => round($report->file_size / 1024 / 1024, 2),
            'download_count' => $report->download_count,
            'days_since_generation' => $report->generated_at->diffInDays(now()),
            'report_age' => $report->generated_at->diffForHumans(),
        ];
    }

    private function getPreviewData($report)
    {
        return [
            'executive_summary' => substr($report->executive_summary, 0, 500) . '...',
            'key_findings_count' => count($report->key_findings ?? []),
            'recommendations_count' => count($report->recommendations ?? []),
            'performance_summary' => $this->getPerformanceScores($report->propertySustainability),
        ];
    }
}
