<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Property;
use App\Models\ComplianceCheck;
use Illuminate\Http\Request;

class CertificationController extends Controller
{
    public function index()
    {
        $certifications = Certification::with(['property', 'inspector', 'complianceCheck'])
            ->latest()
            ->paginate(10);
            
        return view('certifications.index', compact('certifications'));
    }

    public function create()
    {
        $properties = Property::all();
        $complianceChecks = ComplianceCheck::where('status', 'completed')->get();
        
        return view('certifications.create', compact('properties', 'complianceChecks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'compliance_check_id' => 'required|exists:compliance_checks,id',
            'inspector_id' => 'required|exists:inspectors,id',
            'certification_type' => 'required|in:occupancy,safety,environmental,accessibility,fire,structural',
            'certificate_number' => 'required|string|unique:certifications,certificate_number',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'scope' => 'required|string|min:20',
            'conditions' => 'nullable|array',
            'conditions.*.description' => 'required|string',
            'conditions.*.requirement' => 'required|string',
            'conditions.*.compliance_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx|max:5120',
        ]);

        $certification = Certification::create([
            'property_id' => $validated['property_id'],
            'compliance_check_id' => $validated['compliance_check_id'],
            'inspector_id' => $validated['inspector_id'],
            'certification_type' => $validated['certification_type'],
            'certificate_number' => $validated['certificate_number'],
            'issue_date' => $validated['issue_date'],
            'expiry_date' => $validated['expiry_date'],
            'scope' => $validated['scope'],
            'conditions' => json_encode($validated['conditions'] ?? []),
            'notes' => $validated['notes'],
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $attachment) {
                $path = $attachment->store('certification_attachments', 'public');
                $certification->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $attachment->getClientOriginalName(),
                    'file_size' => $attachment->getSize(),
                ]);
            }
        }

        return redirect()
            ->route('certifications.show', $certification)
            ->with('success', 'تم إنشاء الشهادة بنجاح');
    }

    public function show(Certification $certification)
    {
        $certification->load(['property', 'inspector', 'complianceCheck', 'attachments']);
        
        return view('certifications.show', compact('certification'));
    }

    public function edit(Certification $certification)
    {
        if ($certification->status === 'expired') {
            return back()->with('error', 'لا يمكن تعديل شهادة منتهية');
        }

        return view('certifications.edit', compact('certification'));
    }

    public function update(Request $request, Certification $certification)
    {
        if ($certification->status === 'expired') {
            return back()->with('error', 'لا يمكن تعديل شهادة منتهية');
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'compliance_check_id' => 'required|exists:compliance_checks,id',
            'inspector_id' => 'required|exists:inspectors,id',
            'certification_type' => 'required|in:occupancy,safety,environmental,accessibility,fire,structural',
            'certificate_number' => 'required|string|unique:certifications,certificate_number,' . $certification->id,
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'scope' => 'required|string|min:20',
            'conditions' => 'nullable|array',
            'conditions.*.description' => 'required|string',
            'conditions.*.requirement' => 'required|string',
            'conditions.*.compliance_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $certification->update($validated);

        return redirect()
            ->route('certifications.show', $certification)
            ->with('success', 'تم تحديث الشهادة بنجاح');
    }

    public function destroy(Certification $certification)
    {
        $certification->delete();

        return redirect()
            ->route('certifications.index')
            ->with('success', 'تم حذف الشهادة بنجاح');
    }

    public function renew(Certification $certification, Request $request)
    {
        $validated = $request->validate([
            'new_expiry_date' => 'required|date|after:expiry_date',
            'renewal_reason' => 'required|string',
            'renewal_notes' => 'nullable|string',
        ]);

        $certification->update([
            'expiry_date' => $validated['new_expiry_date'],
            'renewal_reason' => $validated['renewal_reason'],
            'renewal_notes' => $validated['renewal_notes'],
            'renewed_at' => now(),
            'renewed_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم تجديد الشهادة بنجاح');
    }

    public function suspend(Certification $certification, Request $request)
    {
        $validated = $request->validate([
            'suspension_reason' => 'required|string',
            'suspension_notes' => 'nullable|string',
        ]);

        $certification->update([
            'status' => 'suspended',
            'suspension_reason' => $validated['suspension_reason'],
            'suspension_notes' => $validated['suspension_notes'],
            'suspended_at' => now(),
            'suspended_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم إيقاف الشهادة بنجاح');
    }

    public function reactivate(Certification $certification, Request $request)
    {
        $validated = $request->validate([
            'reactivation_reason' => 'required|string',
            'reactivation_notes' => 'nullable|string',
        ]);

        $certification->update([
            'status' => 'active',
            'reactivation_reason' => $validated['reactivation_reason'],
            'reactivation_notes' => $validated['reactivation_notes'],
            'reactivated_at' => now(),
            'reactivated_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم إعادة تفعيل الشهادة بنجاح');
    }

    public function revoke(Certification $certification, Request $request)
    {
        $validated = $request->validate([
            'revocation_reason' => 'required|string',
            'revocation_notes' => 'nullable|string',
        ]);

        $certification->update([
            'status' => 'revoked',
            'revocation_reason' => $validated['revocation_reason'],
            'revocation_notes' => $validated['revocation_notes'],
            'revoked_at' => now(),
            'revoked_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم إلغاء الشهادة بنجاح');
    }

    public function download(Certification $certification)
    {
        $certification->load(['property', 'inspector', 'complianceCheck']);
        
        // Generate certificate PDF
        $pdf = \PDF::loadView('certifications.pdf', compact('certification'));
        
        return $pdf->download('certificate_' . $certification->certificate_number . '.pdf');
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'certificate_number' => 'required|string',
        ]);

        $certification = Certification::with(['property', 'inspector'])
            ->where('certificate_number', $validated['certificate_number'])
            ->first();

        if (!$certification) {
            return back()->with('error', 'الشهادة غير موجودة');
        }

        return view('certifications.verify', compact('certification'));
    }

    public function dashboard()
    {
        $stats = [
            'total' => Certification::count(),
            'active' => Certification::where('status', 'active')->count(),
            'expired' => Certification::where('status', 'expired')->count(),
            'suspended' => Certification::where('status', 'suspended')->count(),
            'expiring_soon' => Certification::where('expiry_date', '<=', now()->addDays(30))->count(),
            'this_month' => Certification::whereMonth('issue_date', now()->month)->count(),
        ];

        $recentCertifications = Certification::with(['property', 'inspector'])
            ->latest()
            ->take(5)
            ->get();

        $certificationTypes = Certification::selectRaw('certification_type, COUNT(*) as count')
            ->groupBy('certification_type')
            ->get();

        return view('certifications.dashboard', compact('stats', 'recentCertifications', 'certificationTypes'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:active,expired,suspended,revoked',
            'certification_type' => 'nullable|in:occupancy,safety,environmental,accessibility,fire,structural',
        ]);

        $query = Certification::with(['property', 'inspector'])
            ->whereBetween('issue_date', [$validated['start_date'], $validated['end_date']]);

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (isset($validated['certification_type'])) {
            $query->where('certification_type', $validated['certification_type']);
        }

        $certifications = $query->get();

        $csvData = [];
        $csvData[] = ['رقم الشهادة', 'العقار', 'النوع', 'الحالة', 'تاريخ الإصدار', 'تاريخ الانتهاء'];

        foreach ($certifications as $certification) {
            $csvData[] = [
                $certification->certificate_number,
                $certification->property->title,
                $certification->certification_type,
                $certification->status,
                $certification->issue_date->format('Y-m-d'),
                $certification->expiry_date->format('Y-m-d'),
            ];
        }

        $filename = "certifications_report.csv";
        $handle = fopen($filename, 'w');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function addAttachment(Request $request, Certification $certification)
    {
        $validated = $request->validate([
            'attachment' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'description' => 'nullable|string',
        ]);

        $path = $validated['attachment']->store('certification_attachments', 'public');
        
        $certification->attachments()->create([
            'file_path' => $path,
            'file_name' => $validated['attachment']->getClientOriginalName(),
            'file_size' => $validated['attachment']->getSize(),
            'description' => $validated['description'],
        ]);

        return back()->with('success', 'تم إضافة المرفق بنجاح');
    }

    public function removeAttachment(Certification $certification, $attachmentId)
    {
        $attachment = $certification->attachments()->findOrFail($attachmentId);
        $attachment->delete();

        return back()->with('success', 'تم حذف المرفق بنجاح');
    }
}
