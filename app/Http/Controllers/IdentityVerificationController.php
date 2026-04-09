<?php

namespace App\Http\Controllers;

use App\Models\IdentityVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class IdentityVerificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $verificationStats = [
            'total_verifications' => IdentityVerification::where('user_id', $user->id)->count(),
            'pending_verifications' => IdentityVerification::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'verified_accounts' => IdentityVerification::where('user_id', $user->id)
                ->where('status', 'verified')
                ->count(),
            'failed_verifications' => IdentityVerification::where('user_id', $user->id)
                ->where('status', 'failed')
                ->count(),
        ];

        $recentVerifications = IdentityVerification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('security.identity-verification.index', compact('verificationStats', 'recentVerifications'));
    }

    public function create()
    {
        return view('security.identity-verification.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'verification_type' => 'required|in:individual,corporate,property_owner,agent,tenant',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'national_id' => 'required|string|max:50',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'id_document' => 'required|file|max:5120|mimes:jpg,jpeg,png,pdf',
            'proof_of_address' => 'required|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx',
            'biometric_data' => 'nullable|array',
            'biometric_data.fingerprint' => 'nullable|string',
            'biometric_data.face_scan' => 'nullable|string',
            'additional_documents' => 'nullable|array',
            'additional_documents.*' => 'file|max:5120',
            'reference_number' => 'nullable|string|max:100',
            'verification_purpose' => 'required|string|max:500',
            'urgency_level' => 'required|in:normal,urgent,expedited',
        ]);

        // Process documents
        $idDocumentPath = $this->storeDocument($request->file('id_document'), 'id_documents');
        $proofOfAddressPath = $this->storeDocument($request->file('proof_of_address'), 'proof_of_address');
        $additionalDocumentsPaths = $this->processAdditionalDocuments($request->file('additional_documents'));

        // Generate verification reference
        $referenceNumber = $validated['reference_number'] ?? $this->generateReferenceNumber();

        $verification = IdentityVerification::create([
            'user_id' => Auth::id(),
            'verification_type' => $validated['verification_type'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'date_of_birth' => $validated['date_of_birth'],
            'national_id' => $validated['national_id'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'postal_code' => $validated['postal_code'],
            'id_document_path' => $idDocumentPath,
            'proof_of_address_path' => $proofOfAddressPath,
            'additional_documents' => json_encode($additionalDocumentsPaths),
            'biometric_data' => json_encode($validated['biometric_data'] ?? []),
            'reference_number' => $referenceNumber,
            'verification_purpose' => $validated['verification_purpose'],
            'urgency_level' => $validated['urgency_level'],
            'status' => 'pending',
            'submitted_at' => now(),
            'verification_score' => 0,
        ]);

        // Start verification process
        $this->initiateVerificationProcess($verification);

        return redirect()->route('security.identity-verification.show', $verification)
            ->with('success', 'تم تقديم طلب التحقق من الهوية بنجاح');
    }

    public function show(IdentityVerification $verification)
    {
        $this->authorize('view', $verification);
        
        $verification->load(['verificationSteps', 'verificationDocuments']);
        
        return view('security.identity-verification.show', compact('verification'));
    }

    public function verify(Request $request, IdentityVerification $verification)
    {
        $this->authorize('verify', $verification);

        $validated = $request->validate([
            'verification_method' => 'required|in:automated,manual,biometric,document_analysis',
            'verification_data' => 'required|array',
            'notes' => 'nullable|string|max:1000',
        ]);

        $result = $this->performVerification($verification, $validated);

        $verification->update([
            'status' => $result['status'],
            'verification_score' => $result['score'],
            'verified_at' => $result['status'] === 'verified' ? now() : null,
            'verification_method' => $validated['verification_method'],
            'verification_notes' => $validated['notes'],
            'verified_by' => Auth::id(),
        ]);

        return redirect()->route('security.identity-verification.show', $verification)
            ->with('success', 'تم إجراء التحقق بنجاح');
    }

    public function approve(Request $request, IdentityVerification $verification)
    {
        $this->authorize('approve', $verification);

        $validated = $request->validate([
            'approval_notes' => 'required|string|max:1000',
            'approval_level' => 'required|in:standard,enhanced,premium',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $verification->update([
            'status' => 'verified',
            'verified_at' => now(),
            'approved_by' => Auth::id(),
            'approval_notes' => $validated['approval_notes'],
            'approval_level' => $validated['approval_level'],
            'expiry_date' => $validated['expiry_date'] ?? now()->addYear(),
        ]);

        // Generate verification certificate
        $certificate = $this->generateVerificationCertificate($verification);

        return redirect()->route('security.identity-verification.show', $verification)
            ->with('success', 'تمت الموافقة على التحقق بنجاح');
    }

    public function reject(Request $request, IdentityVerification $verification)
    {
        $this->authorize('reject', $verification);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'rejection_details' => 'nullable|string|max:2000',
            'can_resubmit' => 'boolean',
        ]);

        $verification->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),
            'rejection_reason' => $validated['rejection_reason'],
            'rejection_details' => $validated['rejection_details'],
            'can_resubmit' => $validated['can_resubmit'] ?? true,
        ]);

        return redirect()->route('security.identity-verification.show', $verification)
            ->with('success', 'تم رفض طلب التحقق');
    }

    public function biometricVerification(Request $request)
    {
        $validated = $request->validate([
            'verification_id' => 'required|exists:identity_verifications,id',
            'biometric_type' => 'required|in:fingerprint,face_scan,iris_scan,voice_recognition',
            'biometric_data' => 'required|string',
            'quality_score' => 'required|numeric|min:0|max:100',
        ]);

        $verification = IdentityVerification::findOrFail($validated['verification_id']);
        $this->authorize('verify', $verification);

        $result = $this->performBiometricVerification($verification, $validated);

        return response()->json([
            'success' => $result['success'],
            'match_score' => $result['match_score'],
            'verification_id' => $verification->id,
            'message' => $result['message'],
        ]);
    }

    public function documentAnalysis(Request $request)
    {
        $validated = $request->validate([
            'verification_id' => 'required|exists:identity_verifications,id',
            'document_type' => 'required|in:id_document,proof_of_address,additional',
            'document_path' => 'required|string',
        ]);

        $verification = IdentityVerification::findOrFail($validated['verification_id']);
        $this->authorize('verify', $verification);

        $analysis = $this->analyzeDocument($verification, $validated);

        return response()->json($analysis);
    }

    public function reverify(Request $request, IdentityVerification $verification)
    {
        $this->authorize('reverify', $verification);

        $validated = $request->validate([
            'reverification_reason' => 'required|string|max:500',
            'updated_documents' => 'nullable|array',
            'updated_documents.*' => 'file|max:5120',
        ]);

        // Process updated documents
        $updatedDocumentsPaths = $this->processAdditionalDocuments($validated['updated_documents'] ?? []);

        $verification->update([
            'status' => 'pending',
            'reverification_requested_at' => now(),
            'reverification_reason' => $validated['reverification_reason'],
            'updated_documents' => json_encode($updatedDocumentsPaths),
        ]);

        // Restart verification process
        $this->initiateVerificationProcess($verification);

        return redirect()->route('security.identity-verification.show', $verification)
            ->with('success', 'تم بدء عملية إعادة التحقق');
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,pdf',
            'date_range' => 'required|in:last_week,last_month,last_quarter,last_year,custom',
            'start_date' => 'nullable|date|required_if:date_range,custom',
            'end_date' => 'nullable|date|required_if:date_range,custom|after_or_equal:start_date',
            'status' => 'nullable|in:pending,verified,rejected,failed',
            'verification_type' => 'nullable|in:individual,corporate,property_owner,agent,tenant',
        ]);

        $verifications = $this->getFilteredVerifications($validated);

        switch ($validated['format']) {
            case 'csv':
                return $this->exportCSV($verifications);
            case 'xlsx':
                return $this->exportExcel($verifications);
            case 'pdf':
                return $this->exportPDF($verifications);
        }
    }

    private function storeDocument($file, $folder)
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($folder, $filename, 'public');
    }

    private function processAdditionalDocuments($documents)
    {
        if (!$documents) {
            return [];
        }

        $paths = [];
        foreach ($documents as $document) {
            $paths[] = $this->storeDocument($document, 'additional_documents');
        }

        return $paths;
    }

    private function generateReferenceNumber()
    {
        return 'VER-' . Str::upper(Str::random(8)) . '-' . date('Ymd');
    }

    private function initiateVerificationProcess(IdentityVerification $verification)
    {
        // Create verification steps
        $steps = [
            ['step' => 'document_validation', 'status' => 'pending', 'order' => 1],
            ['step' => 'identity_check', 'status' => 'pending', 'order' => 2],
            ['step' => 'address_verification', 'status' => 'pending', 'order' => 3],
            ['step' => 'biometric_verification', 'status' => 'pending', 'order' => 4],
            ['step' => 'final_review', 'status' => 'pending', 'order' => 5],
        ];

        foreach ($steps as $stepData) {
            $verification->verificationSteps()->create($stepData);
        }

        // Start automated verification if possible
        $this->startAutomatedVerification($verification);
    }

    private function startAutomatedVerification(IdentityVerification $verification)
    {
        // Simulate automated verification process
        $verification->update([
            'verification_method' => 'automated',
            'automated_verification_started_at' => now(),
        ]);

        // In a real implementation, this would integrate with external verification services
        // like ID verification APIs, biometric services, etc.
    }

    private function performVerification(IdentityVerification $verification, $data)
    {
        $score = 0;
        $status = 'pending';

        // Document validation
        $documentScore = $this->validateDocuments($verification);
        $score += $documentScore * 0.3;

        // Identity check
        $identityScore = $this->checkIdentity($verification);
        $score += $identityScore * 0.3;

        // Address verification
        $addressScore = $this->verifyAddress($verification);
        $score += $addressScore * 0.2;

        // Biometric verification (if available)
        if ($verification->biometric_data) {
            $biometricScore = $this->verifyBiometric($verification);
            $score += $biometricScore * 0.2;
        } else {
            $score += 20; // Default score if no biometric data
        }

        // Determine status based on score
        if ($score >= 80) {
            $status = 'verified';
        } elseif ($score >= 60) {
            $status = 'pending_review';
        } else {
            $status = 'failed';
        }

        return [
            'status' => $status,
            'score' => round($score, 2),
        ];
    }

    private function performBiometricVerification(IdentityVerification $verification, $data)
    {
        // Simulate biometric verification
        $matchScore = rand(70, 100);
        
        return [
            'success' => $matchScore >= 80,
            'match_score' => $matchScore,
            'message' => $matchScore >= 80 ? 'تمت مطابقة البيانات البيومترية بنجاح' : 'فشلت مطابقة البيانات البيومترية',
        ];
    }

    private function analyzeDocument(IdentityVerification $verification, $data)
    {
        // Simulate document analysis
        return [
            'authenticity_score' => rand(80, 100),
            'quality_score' => rand(70, 95),
            'expiry_check' => 'valid',
            'tampering_detected' => false,
            'document_type_verified' => true,
            'analysis_details' => [
                'document_number' => 'Verified',
                'issue_date' => 'Valid',
                'expiry_date' => 'Valid',
                'photo_quality' => 'Good',
                'security_features' => 'Present',
            ],
        ];
    }

    private function validateDocuments(IdentityVerification $verification)
    {
        // Simulate document validation
        return rand(70, 100);
    }

    private function checkIdentity(IdentityVerification $verification)
    {
        // Simulate identity check
        return rand(75, 100);
    }

    private function verifyAddress(IdentityVerification $verification)
    {
        // Simulate address verification
        return rand(70, 95);
    }

    private function verifyBiometric(IdentityVerification $verification)
    {
        // Simulate biometric verification
        return rand(80, 100);
    }

    private function generateVerificationCertificate(IdentityVerification $verification)
    {
        // Generate verification certificate
        $certificateData = [
            'certificate_id' => Str::uuid(),
            'verification_id' => $verification->id,
            'reference_number' => $verification->reference_number,
            'verified_name' => $verification->first_name . ' ' . $verification->last_name,
            'verification_type' => $verification->verification_type,
            'verified_at' => $verification->verified_at,
            'expiry_date' => $verification->expiry_date,
            'approval_level' => $verification->approval_level,
        ];

        // In a real implementation, this would generate a PDF certificate
        return $certificateData;
    }

    private function getFilteredVerifications($validated)
    {
        $query = IdentityVerification::where('user_id', Auth::id());

        // Apply date range filter
        switch ($validated['date_range']) {
            case 'last_week':
                $query->where('created_at', '>=', now()->subWeek());
                break;
            case 'last_month':
                $query->where('created_at', '>=', now()->subMonth());
                break;
            case 'last_quarter':
                $query->where('created_at', '>=', now()->subQuarter());
                break;
            case 'last_year':
                $query->where('created_at', '>=', now()->subYear());
                break;
            case 'custom':
                $query->whereBetween('created_at', [$validated['start_date'], $validated['end_date']]);
                break;
        }

        // Apply status filter
        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        // Apply verification type filter
        if (isset($validated['verification_type'])) {
            $query->where('verification_type', $validated['verification_type']);
        }

        return $query->get();
    }

    private function exportCSV($verifications)
    {
        $filename = 'identity_verifications_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($verifications) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID', 'Reference Number', 'Name', 'Email', 'Type', 'Status',
                'Submitted At', 'Verified At', 'Score'
            ]);

            // Data
            foreach ($verifications as $verification) {
                fputcsv($file, [
                    $verification->id,
                    $verification->reference_number,
                    $verification->first_name . ' ' . $verification->last_name,
                    $verification->email,
                    $verification->verification_type,
                    $verification->status,
                    $verification->created_at,
                    $verification->verified_at,
                    $verification->verification_score,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel($verifications)
    {
        // Implementation for Excel export
        return response()->download('identity_verifications.xlsx');
    }

    private function exportPDF($verifications)
    {
        // Implementation for PDF export
        return response()->download('identity_verifications.pdf');
    }

    // KYC-specific methods for user routes
    public function status()
    {
        $user = Auth::user();
        $verification = IdentityVerification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'status' => $verification ? $verification->status : 'not_started',
            'verification' => $verification,
            'can_submit' => !$verification || in_array($verification->status, ['rejected', 'failed']),
        ]);
    }

    public function downloadDocument($document)
    {
        // Implementation for downloading KYC documents
        // This would need to be implemented based on your document storage structure
        return response()->download(storage_path('app/public/kyc_documents/' . $document));
    }

    public function upgrade(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'upgrade_type' => 'required|in:individual,corporate,property_owner,agent,tenant',
            'additional_documents' => 'nullable|array',
            'additional_documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Create new verification with upgrade type
        $verification = IdentityVerification::create([
            'user_id' => $user->id,
            'verification_type' => $validated['upgrade_type'],
            'status' => 'pending',
            'reference_number' => 'KYC-' . strtoupper(Str::random(10)),
        ]);

        // Process additional documents if provided
        if (isset($validated['additional_documents'])) {
            $documentPaths = $this->processAdditionalDocuments($validated['additional_documents']);
            $verification->update(['additional_documents' => $documentPaths]);
        }

        return redirect()->route('kyc.index')
            ->with('success', 'تم تقديم طلب ترقية التحقق بنجاح');
    }

    public function resubmit(Request $request)
    {
        $user = Auth::user();
        
        $verification = IdentityVerification::where('user_id', $user->id)
            ->whereIn('status', ['rejected', 'failed'])
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$verification) {
            return redirect()->route('kyc.index')
                ->with('error', 'لا يوجد طلب تحقق يمكن إعادة تقديمه');
        }

        $validated = $request->validate([
            'corrected_documents' => 'nullable|array',
            'corrected_documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Update verification with resubmitted data
        $verification->update([
            'status' => 'pending',
            'resubmitted_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Process corrected documents if provided
        if (isset($validated['corrected_documents'])) {
            $documentPaths = $this->processAdditionalDocuments($validated['corrected_documents']);
            $verification->update(['corrected_documents' => $documentPaths]);
        }

        return redirect()->route('kyc.index')
            ->with('success', 'تم إعادة تقديم طلب التحقق بنجاح');
    }

    // API methods
    public function apiStatus()
    {
        $user = Auth::user();
        $verification = IdentityVerification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'status' => $verification ? $verification->status : 'not_started',
            'verification' => $verification,
            'can_submit' => !$verification || in_array($verification->status, ['rejected', 'failed']),
            'pending_count' => IdentityVerification::where('user_id', $user->id)
                ->where('status', 'pending')->count(),
        ]);
    }

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'verification_type' => 'required|in:individual,corporate,property_owner,agent,tenant',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'documents' => 'required|array',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $user = Auth::user();
        
        $verification = IdentityVerification::create([
            'user_id' => $user->id,
            'verification_type' => $validated['verification_type'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'status' => 'pending',
            'reference_number' => 'KYC-' . strtoupper(Str::random(10)),
        ]);

        // Process documents
        $documentPaths = [];
        foreach ($validated['documents'] as $document) {
            $documentPaths[] = $this->storeDocument($document, 'kyc_documents');
        }
        
        $verification->update(['documents' => $documentPaths]);

        return response()->json([
            'success' => true,
            'message' => 'تم تقديم طلب التحقق بنجاح',
            'verification' => $verification,
        ]);
    }

    public function apiUploadDocuments(Request $request)
    {
        $validated = $request->validate([
            'verification_id' => 'required|exists:identity_verifications,id',
            'documents' => 'required|array',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $verification = IdentityVerification::find($validated['verification_id']);
        $this->authorize('update', $verification);

        // Process new documents
        $documentPaths = [];
        foreach ($validated['documents'] as $document) {
            $documentPaths[] = $this->storeDocument($document, 'kyc_documents');
        }

        // Merge with existing documents
        $existingDocuments = $verification->documents ?? [];
        $allDocuments = array_merge($existingDocuments, $documentPaths);
        
        $verification->update(['documents' => $allDocuments]);

        return response()->json([
            'success' => true,
            'message' => 'تم رفع المستندات بنجاح',
            'documents' => $documentPaths,
        ]);
    }
}
