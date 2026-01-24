<?php

namespace App\Http\Controllers\Sustainability;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Sustainability\PropertySustainability;
use App\Models\Sustainability\GreenCertification;
use App\Http\Requests\Sustainability\ApplyGreenCertificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GreenCertificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_sustainability');
    }

    public function index()
    {
        $certifications = GreenCertification::with(['propertySustainability.property', 'issuer'])
            ->when(Auth::user()->hasRole('agent'), function($query) {
                $query->whereHas('propertySustainability.property', function($q) {
                    $q->where('agent_id', Auth::id());
                });
            })
            ->latest('issued_date')
            ->paginate(15);

        $stats = [
            'total_certifications' => GreenCertification::count(),
            'active_certifications' => GreenCertification::where('status', 'active')->count(),
            'expired_certifications' => GreenCertification::where('status', 'expired')->count(),
            'pending_certifications' => GreenCertification::where('status', 'pending')->count(),
        ];

        return view('sustainability.certifications.index', compact('certifications', 'stats'));
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

        $certificationTypes = [
            'leed' => 'LEED (Leadership in Energy and Environmental Design)',
            'breeam' => 'BREEAM (Building Research Establishment Environmental Assessment Method)',
            'estidama' => 'Estidama (Abu Dhabi Sustainability Rating System)',
            'green_globes' => 'Green Globes',
            'energy_star' => 'ENERGY STAR',
            'passive_house' => 'Passive House',
            'living_building' => 'Living Building Challenge',
            'well' => 'WELL Building Standard',
            'local_green' => 'شهادة خضراء محلية',
        ];

        return view('sustainability.certifications.create', compact('properties', 'certificationTypes'));
    }

    public function store(ApplyGreenCertificationRequest $request)
    {
        $validated = $request->validated();

        // Handle document uploads
        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('certifications', 'public');
                $documents[] = $path;
            }
        }

        $certification = GreenCertification::create([
            'property_sustainability_id' => $validated['property_sustainability_id'],
            'certification_type' => $validated['certification_type'],
            'certification_level' => $validated['certification_level'],
            'issuer_id' => $validated['issuer_id'] ?? Auth::id(),
            'application_date' => now(),
            'issued_date' => $validated['issued_date'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'status' => 'pending',
            'score' => $validated['score'] ?? null,
            'requirements_met' => $validated['requirements_met'] ?? [],
            'documents' => $documents,
            'certificate_number' => $validated['certificate_number'] ?? null,
            'verification_url' => $validated['verification_url'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        // Update property sustainability certification status
        $propertySustainability = PropertySustainability::find($validated['property_sustainability_id']);
        $propertySustainability->update(['certification_status' => 'in_progress']);

        return redirect()
            ->route('green-certifications.show', $certification)
            ->with('success', 'تم تقديم طلب الشهادة الخضراء بنجاح');
    }

    public function show(GreenCertification $certification)
    {
        $certification->load(['propertySustainability.property', 'issuer', 'audits']);
        
        return view('sustainability.certifications.show', compact('certification'));
    }

    public function edit(GreenCertification $certification)
    {
        $this->authorize('update', $certification);
        
        $certificationTypes = [
            'leed' => 'LEED (Leadership in Energy and Environmental Design)',
            'breeam' => 'BREEAM (Building Research Establishment Environmental Assessment Method)',
            'estidama' => 'Estidama (Abu Dhabi Sustainability Rating System)',
            'green_globes' => 'Green Globes',
            'energy_star' => 'ENERGY STAR',
            'passive_house' => 'Passive House',
            'living_building' => 'Living Building Challenge',
            'well' => 'WELL Building Standard',
            'local_green' => 'شهادة خضراء محلية',
        ];

        return view('sustainability.certifications.edit', compact('certification', 'certificationTypes'));
    }

    public function update(ApplyGreenCertificationRequest $request, GreenCertification $certification)
    {
        $this->authorize('update', $certification);

        $validated = $request->validated();

        // Handle document uploads
        $documents = $certification->documents ?? [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('certifications', 'public');
                $documents[] = $path;
            }
        }

        $certification->update([
            'certification_type' => $validated['certification_type'],
            'certification_level' => $validated['certification_level'],
            'issued_date' => $validated['issued_date'] ?? $certification->issued_date,
            'expiry_date' => $validated['expiry_date'] ?? $certification->expiry_date,
            'score' => $validated['score'] ?? $certification->score,
            'requirements_met' => $validated['requirements_met'] ?? $certification->requirements_met,
            'documents' => $documents,
            'certificate_number' => $validated['certificate_number'] ?? $certification->certificate_number,
            'verification_url' => $validated['verification_url'] ?? $certification->verification_url,
            'notes' => $validated['notes'] ?? $certification->notes,
        ]);

        return redirect()
            ->route('green-certifications.show', $certification)
            ->with('success', 'تم تحديث الشهادة الخضراء بنجاح');
    }

    public function destroy(GreenCertification $certification)
    {
        $this->authorize('delete', $certification);

        // Delete associated documents
        if ($certification->documents) {
            foreach ($certification->documents as $document) {
                Storage::disk('public')->delete($document);
            }
        }

        $certification->delete();

        return redirect()
            ->route('green-certifications.index')
            ->with('success', 'تم حذف الشهادة الخضراء بنجاح');
    }

    public function approve(GreenCertification $certification)
    {
        $this->authorize('approve', $certification);

        $certification->update([
            'status' => 'active',
            'issued_date' => now(),
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Update property sustainability certification status
        $propertySustainability = $certification->propertySustainability;
        $propertySustainability->update(['certification_status' => 'certified']);

        return redirect()
            ->route('green-certifications.show', $certification)
            ->with('success', 'تم اعتماد الشهادة الخضراء بنجاح');
    }

    public function reject(GreenCertification $certification, Request $request)
    {
        $this->authorize('approve', $certification);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $certification->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
        ]);

        // Update property sustainability certification status
        $propertySustainability = $certification->propertySustainability;
        $propertySustainability->update(['certification_status' => 'not_certified']);

        return redirect()
            ->route('green-certifications.show', $certification)
            ->with('success', 'تم رفض الشهادة الخضراء');
    }

    public function renew(GreenCertification $certification, Request $request)
    {
        $validated = $request->validate([
            'new_expiry_date' => 'required|date|after:today',
            'renewal_notes' => 'nullable|string',
        ]);

        $certification->update([
            'expiry_date' => $validated['new_expiry_date'],
            'renewal_notes' => $validated['renewal_notes'],
            'renewed_at' => now(),
            'renewed_by' => Auth::id(),
        ]);

        return redirect()
            ->route('green-certifications.show', $certification)
            ->with('success', 'تم تجديد الشهادة الخضراء بنجاح');
    }

    public function verify(GreenCertification $certification)
    {
        // External verification logic
        $verificationData = [
            'certificate_number' => $certification->certificate_number,
            'verification_url' => $certification->verification_url,
            'status' => $certification->status,
            'issued_date' => $certification->issued_date,
            'expiry_date' => $certification->expiry_date,
        ];

        return response()->json($verificationData);
    }

    public function downloadCertificate(GreenCertification $certification)
    {
        if (!$certification->documents || empty($certification->documents)) {
            return back()->with('error', 'لا يوجد شهادة متاحة للتحميل');
        }

        $certificatePath = $certification->documents[0]; // First document assumed to be certificate
        
        if (!Storage::disk('public')->exists($certificatePath)) {
            return back()->with('error', 'الشهادة غير موجودة');
        }

        return Storage::disk('public')->download($certificatePath);
    }

    public function analytics()
    {
        $certificationStats = GreenCertification::selectRaw('certification_type, COUNT(*) as count, AVG(score) as avg_score')
            ->groupBy('certification_type')
            ->get();

        $levelDistribution = GreenCertification::selectRaw('certification_level, COUNT(*) as count')
            ->groupBy('certification_level')
            ->get();

        $monthlyTrends = GreenCertification::selectRaw('DATE_FORMAT(issued_date, "%Y-%m") as month, COUNT(*) as count')
            ->where('issued_date', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $expiringSoon = GreenCertification::with(['propertySustainability.property'])
            ->where('expiry_date', '<=', now()->addDays(90))
            ->where('expiry_date', '>', now())
            ->where('status', 'active')
            ->get();

        return view('sustainability.certifications.analytics', compact(
            'certificationStats',
            'levelDistribution',
            'monthlyTrends',
            'expiringSoon'
        ));
    }

    public function requirements($certificationType)
    {
        $requirements = $this->getCertificationRequirements($certificationType);
        
        return response()->json($requirements);
    }

    public function checklist(GreenCertification $certification)
    {
        $requirements = $this->getCertificationRequirements($certification->certification_type);
        $metRequirements = $certification->requirements_met ?? [];
        
        return view('sustainability.certifications.checklist', compact('certification', 'requirements', 'metRequirements'));
    }

    public function updateChecklist(GreenCertification $certification, Request $request)
    {
        $validated = $request->validate([
            'requirements_met' => 'required|array',
            'requirements_met.*' => 'string',
        ]);

        $certification->update([
            'requirements_met' => $validated['requirements_met'],
        ]);

        return redirect()
            ->route('green-certifications.checklist', $certification)
            ->with('success', 'تم تحديث قائمة المتطلبات بنجاح');
    }

    private function getCertificationRequirements($type)
    {
        $requirements = [
            'leed' => [
                'sustainable_sites' => [
                    'site_selection' => 'اختيار الموقع المستدام',
                    'development_density' => 'كثافة التطوير',
                    'brownfield_redevelopment' => 'إعادة تطوير المواقع البنية',
                    'alternative_transportation' => 'وسائل النقل البديلة',
                    'site_development' => 'تطوير الموقع',
                ],
                'water_efficiency' => [
                    'water_efficient_landscaping' => 'كفاءة استخدام المياه في landscaping',
                    'innovative_wastewater_technologies' => 'تقنيات معالجة مياه الصرف الصحي المبتكرة',
                    'water_use_reduction' => 'تقليل استخدام المياه',
                ],
                'energy_atmosphere' => [
                    'fundamental_commissioning' => 'التكليف الأساسي',
                    'minimum_energy_performance' => 'أداء الطاقة الأدنى',
                    'fundamental_refrigerant_management' => 'إدارة المبردات الأساسية',
                    'optimize_energy_performance' => 'تحسين أداء الطاقة',
                    'on_site_renewable_energy' => 'الطاقة المتجددة في الموقع',
                ],
            ],
            'breeam' => [
                'management' => [
                    'commissioning' => 'التكليف',
                    'construction_site_impacts' => 'تأثيرات موقع البناء',
                    'operational_management' => 'الإدارة التشغيلية',
                ],
                'health_and_wellbeing' => [
                    'visual_comfort' => 'الراحة البصرية',
                    'indoor_air_quality' => 'جودة الهواء الداخلي',
                    'thermal_comfort' => 'الراحة الحرارية',
                ],
                'energy' => [
                    'energy_performance' => 'أداء الطاقة',
                    'energy_reduction' => 'تقليل الطاقة',
                    'low_carbon_design' => 'تصميم منخفض الكربون',
                ],
            ],
            'estidama' => [
                'integrated_development' => [
                    'site_analysis' => 'تحليل الموقع',
                    'integrated_design' => 'التصميم المتكامل',
                    'stakeholder_participation' => 'مشاركة أصحاب المصلحة',
                ],
                'natural_resources' => [
                    'water_conservation' => 'حفظ المياه',
                    'energy_efficiency' => 'كفاءة الطاقة',
                    'materials' => 'المواد',
                ],
                'livable_communities' => [
                    'connectivity' => 'الربطية',
                    'open_spaces' => 'المساحات المفتوحة',
                    'cultural_identity' => 'الهوية الثقافية',
                ],
            ],
        ];

        return $requirements[$type] ?? [];
    }
}
