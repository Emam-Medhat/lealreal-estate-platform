<?php

namespace App\Http\Controllers;

use App\Models\GreenCertification;
use App\Models\SmartProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GreenCertificationController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_certifications' => GreenCertification::count(),
            'active_certifications' => GreenCertification::where('status', 'active')->count(),
            'pending_certifications' => GreenCertification::where('status', 'pending')->count(),
            'expired_certifications' => GreenCertification::where('status', 'expired')->count(),
            'certifications_by_level' => $this->getCertificationsByLevel(),
            'certifications_by_body' => $this->getCertificationsByBody(),
        ];

        $recentCertifications = GreenCertification::with(['property'])
            ->latest()
            ->take(10)
            ->get();

        $expiringSoon = $this->getExpiringSoonCertifications();
        $certificationTrends = $this->getCertificationTrends();

        return view('sustainability.certifications-dashboard', compact(
            'stats', 
            'recentCertifications', 
            'expiringSoon', 
            'certificationTrends'
        ));
    }

    public function index(Request $request)
    {
        $query = GreenCertification::with(['property']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('certification_level')) {
            $query->where('certification_level', $request->certification_level);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('certification_body')) {
            $query->where('certification_body', 'like', '%' . $request->certification_body . '%');
        }

        if ($request->filled('issue_date_from')) {
            $query->whereDate('issue_date', '>=', $request->issue_date_from);
        }

        if ($request->filled('issue_date_to')) {
            $query->whereDate('issue_date', '<=', $request->issue_date_to);
        }

        $certifications = $query->latest()->paginate(12);

        $certificationLevels = ['certified', 'silver', 'gold', 'platinum'];
        $statuses = ['pending', 'active', 'expired', 'suspended', 'revoked'];

        return view('sustainability.certifications-index', compact(
            'certifications', 
            'certificationLevels', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = SmartProperty::all();
        $certificationLevels = ['certified', 'silver', 'gold', 'platinum'];

        return view('sustainability.certifications-create', compact(
            'properties', 
            'certificationLevels'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $certificationData = $request->validate([
                'property_id' => 'required|exists:smart_properties,id',
                'certification_name' => 'required|string|max:255',
                'certification_body' => 'required|string|max:255',
                'certification_level' => 'required|in:certified,silver,gold,platinum',
                'certificate_number' => 'required|string|max:255|unique:green_certifications',
                'issue_date' => 'required|date',
                'expiry_date' => 'nullable|date|after:issue_date',
                'status' => 'required|in:pending,active,expired,suspended,revoked',
                'certification_criteria' => 'nullable|array',
                'assessment_results' => 'nullable|array',
                'notes' => 'nullable|string',
                'certification_score' => 'nullable|numeric|min:0|max:100',
                'renewal_date' => 'nullable|date|after:issue_date',
            ]);

            $certificationData['created_by'] = auth()->id();
            $certificationData['certification_criteria'] = $this->generateCertificationCriteria($request);
            $certificationData['assessment_results'] = $this->generateAssessmentResults($request);

            $certification = GreenCertification::create($certificationData);

            DB::commit();

            return redirect()
                ->route('green-certification.show', $certification)
                ->with('success', 'تم إضافة الشهادة الخضراء بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة الشهادة: ' . $e->getMessage());
        }
    }

    public function show(GreenCertification $certification)
    {
        $certification->load(['property']);
        $certificationDetails = $this->getCertificationDetails($certification);
        $renewalStatus = $this->getRenewalStatus($certification);
        $complianceStatus = $this->getComplianceStatus($certification);

        return view('sustainability.certifications-show', compact(
            'certification', 
            'certificationDetails', 
            'renewalStatus', 
            'complianceStatus'
        ));
    }

    public function edit(GreenCertification $certification)
    {
        $properties = SmartProperty::all();
        $certificationLevels = ['certified', 'silver', 'gold', 'platinum'];

        return view('sustainability.certifications-edit', compact(
            'certification', 
            'properties', 
            'certificationLevels'
        ));
    }

    public function update(Request $request, GreenCertification $certification)
    {
        DB::beginTransaction();
        try {
            $certificationData = $request->validate([
                'certification_name' => 'required|string|max:255',
                'certification_body' => 'required|string|max:255',
                'certification_level' => 'required|in:certified,silver,gold,platinum',
                'certificate_number' => 'required|string|max:255|unique:green_certifications,certificate_number,' . $certification->id,
                'issue_date' => 'required|date',
                'expiry_date' => 'nullable|date|after:issue_date',
                'status' => 'required|in:pending,active,expired,suspended,revoked',
                'certification_criteria' => 'nullable|array',
                'assessment_results' => 'nullable|array',
                'notes' => 'nullable|string',
                'certification_score' => 'nullable|numeric|min:0|max:100',
                'renewal_date' => 'nullable|date|after:issue_date',
            ]);

            $certificationData['updated_by'] = auth()->id();
            $certificationData['certification_criteria'] = $this->generateCertificationCriteria($request);
            $certificationData['assessment_results'] = $this->generateAssessmentResults($request);

            $certification->update($certificationData);

            DB::commit();

            return redirect()
                ->route('green-certification.show', $certification)
                ->with('success', 'تم تحديث الشهادة الخضراء بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الشهادة: ' . $e->getMessage());
        }
    }

    public function destroy(GreenCertification $certification)
    {
        try {
            $certification->delete();

            return redirect()
                ->route('green-certification.index')
                ->with('success', 'تم حذف الشهادة الخضراء بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف الشهادة: ' . $e->getMessage());
        }
    }

    public function applyCertification(Request $request)
    {
        $propertyId = $request->input('property_id');
        $certificationType = $request->input('certification_type');
        $applicationData = $request->input('application_data', []);

        $application = $this->processCertificationApplication($propertyId, $certificationType, $applicationData);

        return response()->json([
            'success' => true,
            'application' => $application
        ]);
    }

    public function renewCertification(GreenCertification $certification)
    {
        try {
            $renewalData = $this->processCertificationRenewal($certification);
            
            return response()->json([
                'success' => true,
                'renewal' => $renewalData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function verifyCertificate(GreenCertification $certification)
    {
        $verification = $this->verifyCertificationValidity($certification);

        return response()->json([
            'success' => true,
            'verification' => $verification
        ]);
    }

    public function generateCertificate(GreenCertification $certification)
    {
        try {
            $certificateData = $this->generateCertificateDocument($certification);
            
            return response()->json([
                'success' => true,
                'certificate' => $certificateData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateCertificationCriteria($request)
    {
        return [
            'energy_efficiency' => $request->input('energy_efficiency_criteria', []),
            'water_conservation' => $request->input('water_conservation_criteria', []),
            'waste_management' => $request->input('waste_management_criteria', []),
            'sustainable_materials' => $request->input('sustainable_materials_criteria', []),
            'indoor_air_quality' => $request->input('indoor_air_quality_criteria', []),
            'site_sustainability' => $request->input('site_sustainability_criteria', []),
            'innovation' => $request->input('innovation_criteria', []),
        ];
    }

    private function generateAssessmentResults($request)
    {
        return [
            'energy_score' => $request->input('energy_score', 0),
            'water_score' => $request->input('water_score', 0),
            'waste_score' => $request->input('waste_score', 0),
            'materials_score' => $request->input('materials_score', 0),
            'air_quality_score' => $request->input('air_quality_score', 0),
            'site_score' => $request->input('site_score', 0),
            'innovation_score' => $request->input('innovation_score', 0),
            'total_score' => $request->input('certification_score', 0),
            'assessment_date' => now()->toDateString(),
            'assessor_notes' => $request->input('assessor_notes', ''),
        ];
    }

    private function getCertificationsByLevel()
    {
        return GreenCertification::select('certification_level', DB::raw('COUNT(*) as count'))
            ->groupBy('certification_level')
            ->get();
    }

    private function getCertificationsByBody()
    {
        return GreenCertification::select('certification_body', DB::raw('COUNT(*) as count'))
            ->groupBy('certification_body')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();
    }

    private function getExpiringSoonCertifications()
    {
        return GreenCertification::with(['property'])
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(90))
            ->where('status', 'active')
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    private function getCertificationTrends()
    {
        return GreenCertification::selectRaw('MONTH(issue_date) as month, COUNT(*) as count')
            ->whereYear('issue_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getCertificationDetails($certification)
    {
        return [
            'days_until_expiry' => $certification->getDaysUntilExpiry(),
            'is_expiring' => $certification->isExpiring(),
            'is_expired' => $certification->isExpired(),
            'certification_age' => $certification->issue_date->diffInDays(now()),
            'score_grade' => $this->getScoreGrade($certification->certification_score),
        ];
    }

    private function getRenewalStatus($certification)
    {
        if (!$certification->renewal_date) {
            return [
                'eligible' => false,
                'reason' => 'No renewal date set',
                'next_renewal' => null,
            ];
        }

        $daysUntilRenewal = $certification->renewal_date->diffInDays(now());
        
        return [
            'eligible' => $daysUntilRenewal <= 30,
            'reason' => $daysUntilRenewal <= 30 ? 'Renewal window open' : 'Not yet eligible',
            'next_renewal' => $certification->renewal_date->toDateString(),
            'days_until_renewal' => $daysUntilRenewal,
        ];
    }

    private function getComplianceStatus($certification)
    {
        $complianceScore = $certification->certification_score ?? 0;
        
        return [
            'compliant' => $complianceScore >= 70,
            'score' => $complianceScore,
            'grade' => $this->getScoreGrade($complianceScore),
            'issues' => $this->getComplianceIssues($certification),
        ];
    }

    private function getScoreGrade($score)
    {
        if ($score >= 90) return 'A+';
        if ($score >= 85) return 'A';
        if ($score >= 80) return 'B+';
        if ($score >= 75) return 'B';
        if ($score >= 70) return 'C+';
        if ($score >= 65) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    private function getComplianceIssues($certification)
    {
        $issues = [];
        $results = $certification->assessment_results ?? [];

        if (($results['energy_score'] ?? 0) < 70) {
            $issues[] = 'Energy efficiency below standard';
        }

        if (($results['water_score'] ?? 0) < 70) {
            $issues[] = 'Water conservation below standard';
        }

        if (($results['waste_score'] ?? 0) < 70) {
            $issues[] = 'Waste management below standard';
        }

        if ($certification->isExpired()) {
            $issues[] = 'Certificate has expired';
        }

        return $issues;
    }

    private function processCertificationApplication($propertyId, $certificationType, $applicationData)
    {
        return [
            'application_id' => uniqid('cert_app_'),
            'property_id' => $propertyId,
            'certification_type' => $certificationType,
            'application_data' => $applicationData,
            'status' => 'submitted',
            'submitted_date' => now()->toDateString(),
            'estimated_completion' => now()->addDays(30)->toDateString(),
        ];
    }

    private function processCertificationRenewal($certification)
    {
        return [
            'renewal_id' => uniqid('cert_renew_'),
            'certification_id' => $certification->id,
            'renewal_date' => now()->toDateString(),
            'new_expiry_date' => now()->addYear()->toDateString(),
            'renewal_fee' => $this->calculateRenewalFee($certification),
            'status' => 'pending',
        ];
    }

    private function calculateRenewalFee($certification)
    {
        $baseFee = 1000;
        $levelMultiplier = match($certification->certification_level) {
            'certified' => 1,
            'silver' => 1.5,
            'gold' => 2,
            'platinum' => 2.5,
            default => 1,
        };

        return $baseFee * $levelMultiplier;
    }

    private function verifyCertificationValidity($certification)
    {
        return [
            'certificate_number' => $certification->certificate_number,
            'property_name' => $certification->property->property_name,
            'certification_level' => $certification->certification_level,
            'issue_date' => $certification->issue_date->toDateString(),
            'expiry_date' => $certification->expiry_date?->toDateString(),
            'status' => $certification->status,
            'is_valid' => $certification->status === 'active' && !$certification->isExpired(),
            'verification_code' => strtoupper(uniqid('VERIFY_')),
        ];
    }

    private function generateCertificateDocument($certification)
    {
        return [
            'certificate_id' => uniqid('cert_doc_'),
            'certificate_number' => $certification->certificate_number,
            'property_name' => $certification->property->property_name,
            'certification_name' => $certification->certification_name,
            'certification_level' => $certification->certification_level,
            'certification_body' => $certification->certification_body,
            'issue_date' => $certification->issue_date->toDateString(),
            'expiry_date' => $certification->expiry_date?->toDateString(),
            'certification_score' => $certification->certification_score,
            'grade' => $this->getScoreGrade($certification->certification_score),
            'generated_at' => now()->toDateTimeString(),
            'digital_signature' => hash('sha256', $certification->certificate_number . now()),
        ];
    }
}
