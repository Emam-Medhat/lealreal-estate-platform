<?php

namespace App\Http\Controllers;

use App\Models\BiometricRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BiometricSecurityController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $biometricStats = [
            'total_records' => BiometricRecord::where('user_id', $user->id)->count(),
            'active_records' => BiometricRecord::where('user_id', $user->id)
                ->where('status', 'active')
                ->count(),
            'verified_records' => BiometricRecord::where('user_id', $user->id)
                ->where('verification_status', 'verified')
                ->count(),
            'failed_attempts' => BiometricRecord::where('user_id', $user->id)
                ->where('status', 'failed')
                ->count(),
        ];

        $recentRecords = BiometricRecord::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('security.biometric.index', compact('biometricStats', 'recentRecords'));
    }

    public function create()
    {
        return view('security.biometric.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'biometric_type' => 'required|in:fingerprint,face_scan,iris_scan,voice_pattern,signature,dna',
            'property_id' => 'nullable|exists:properties,id',
            'biometric_data' => 'required|string',
            'biometric_template' => 'required|string',
            'quality_score' => 'required|numeric|min:0|max:100',
            'capture_device' => 'required|string|max:255',
            'capture_method' => 'required|in:scanner,camera,microphone,specialized_device',
            'encryption_method' => 'required|in:aes-256,rsa-2048,ecdsa,quantum_safe',
            'hash_algorithm' => 'required|in:sha-256,sha-512,blake2,keccak',
            'storage_location' => 'required|in:local,cloud,hybrid,blockchain',
            'access_level' => 'required|in:low,medium,high,critical',
            'purpose' => 'required|string|max:500',
            'expiry_date' => 'nullable|date|after:today',
            'retention_period' => 'required|integer|min:1|max:3650', // days
            'consent_given' => 'required|boolean',
            'consent_date' => 'required|date',
            'consent_purpose' => 'required|string|max:1000',
            'data_processing_purpose' => 'required|string|max:1000',
            'security_level' => 'required|in:basic,standard,enhanced,premium',
            'verification_threshold' => 'required|numeric|min:0|max:100',
            'false_acceptance_rate' => 'required|numeric|min:0|max:1',
            'false_rejection_rate' => 'required|numeric|min:0|max:1',
            'environmental_conditions' => 'nullable|array',
            'calibration_data' => 'nullable|array',
            'metadata' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Generate biometric record
        $biometricRecord = BiometricRecord::create([
            'user_id' => Auth::id(),
            'property_id' => $validated['property_id'],
            'biometric_type' => $validated['biometric_type'],
            'biometric_data' => $this->encryptBiometricData($validated['biometric_data'], $validated['encryption_method']),
            'biometric_template' => $this->generateBiometricTemplate($validated['biometric_template']),
            'quality_score' => $validated['quality_score'],
            'capture_device' => $validated['capture_device'],
            'capture_method' => $validated['capture_method'],
            'encryption_method' => $validated['encryption_method'],
            'hash_algorithm' => $validated['hash_algorithm'],
            'storage_location' => $validated['storage_location'],
            'access_level' => $validated['access_level'],
            'purpose' => $validated['purpose'],
            'expiry_date' => $validated['expiry_date'],
            'retention_period' => $validated['retention_period'],
            'consent_given' => $validated['consent_given'],
            'consent_date' => $validated['consent_date'],
            'consent_purpose' => $validated['consent_purpose'],
            'data_processing_purpose' => $validated['data_processing_purpose'],
            'security_level' => $validated['security_level'],
            'verification_threshold' => $validated['verification_threshold'],
            'false_acceptance_rate' => $validated['false_acceptance_rate'],
            'false_rejection_rate' => $validated['false_rejection_rate'],
            'environmental_conditions' => json_encode($validated['environmental_conditions'] ?? []),
            'calibration_data' => json_encode($validated['calibration_data'] ?? []),
            'metadata' => json_encode($validated['metadata'] ?? []),
            'notes' => $validated['notes'],
            'status' => 'active',
            'verification_status' => 'pending',
            'biometric_id' => $this->generateBiometricId(),
            'data_hash' => hash($validated['hash_algorithm'], $validated['biometric_data']),
            'template_hash' => hash($validated['hash_algorithm'], $validated['biometric_template']),
            'last_verified_at' => null,
            'verification_count' => 0,
            'failed_verification_count' => 0,
        ]);

        // Perform initial verification
        $verificationResult = $this->performBiometricVerification($biometricRecord);

        $biometricRecord->update([
            'verification_status' => $verificationResult['status'],
            'verification_score' => $verificationResult['score'],
            'verification_result' => json_encode($verificationResult),
            'last_verified_at' => now(),
        ]);

        // Log biometric record creation
        Log::info('Biometric record created', [
            'user_id' => Auth::id(),
            'biometric_id' => $biometricRecord->id,
            'biometric_type' => $validated['biometric_type'],
            'quality_score' => $validated['quality_score'],
        ]);

        return redirect()->route('security.biometric.show', $biometricRecord)
            ->with('success', 'تم إنشاء السجل البيومتري بنجاح');
    }

    public function show(BiometricRecord $biometricRecord)
    {
        $this->authorize('view', $biometricRecord);
        
        return view('security.biometric.show', compact('biometricRecord'));
    }

    public function verify(Request $request, BiometricRecord $biometricRecord)
    {
        $this->authorize('verify', $biometricRecord);

        $validated = $request->validate([
            'biometric_data' => 'required|string',
            'verification_method' => 'required|in:1:1,1:N,many:many',
            'security_level' => 'required|in:basic,standard,enhanced',
            'environmental_data' => 'nullable|array',
            'device_info' => 'nullable|array',
        ]);

        $verificationResult = $this->performDetailedVerification($biometricRecord, $validated);

        // Update verification statistics
        $biometricRecord->update([
            'verification_count' => $biometricRecord->verification_count + 1,
            'failed_verification_count' => $verificationResult['success'] ? 
                $biometricRecord->failed_verification_count : 
                $biometricRecord->failed_verification_count + 1,
            'last_verified_at' => now(),
            'verification_result' => json_encode($verificationResult),
        ]);

        return response()->json($verificationResult);
    }

    public function enroll(Request $request)
    {
        $validated = $request->validate([
            'biometric_type' => 'required|in:fingerprint,face_scan,iris_scan,voice_pattern,signature',
            'capture_device' => 'required|string|max:255',
            'quality_threshold' => 'required|numeric|min:0|max:100',
            'enrollment_samples' => 'required|array|min:3|max:10',
            'enrollment_samples.*.data' => 'required|string',
            'enrollment_samples.*.quality' => 'required|numeric|min:0|max:100',
            'enrollment_samples.*.timestamp' => 'required|date',
        ]);

        $enrollmentResult = $this->performBiometricEnrollment($validated);

        return response()->json($enrollmentResult);
    }

    public function authenticate(Request $request)
    {
        $validated = $request->validate([
            'biometric_type' => 'required|in:fingerprint,face_scan,iris_scan,voice_pattern,signature',
            'biometric_data' => 'required|string',
            'authentication_method' => 'required|in:1:1,1:N',
            'security_level' => 'required|in:basic,standard,enhanced',
            'timeout' => 'nullable|integer|min:5|max:300',
        ]);

        $authenticationResult = $this->performBiometricAuthentication($validated);

        return response()->json($authenticationResult);
    }

    public function updateTemplate(Request $request, BiometricRecord $biometricRecord)
    {
        $this->authorize('update', $biometricRecord);

        $validated = $request->validate([
            'new_template' => 'required|string',
            'update_reason' => 'required|string|max:500',
            'quality_score' => 'required|numeric|min:0|max:100',
            'verification_required' => 'boolean',
        ]);

        // Verify the new template if required
        if ($validated['verification_required']) {
            $verificationResult = $this->verifyTemplateUpdate($biometricRecord, $validated);
            if (!$verificationResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق من القالب الجديد',
                ]);
            }
        }

        $biometricRecord->update([
            'biometric_template' => $this->generateBiometricTemplate($validated['new_template']),
            'template_hash' => hash($biometricRecord->hash_algorithm, $validated['new_template']),
            'quality_score' => $validated['quality_score'],
            'template_updated_at' => now(),
            'template_update_reason' => $validated['update_reason'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث القالب البيومتري بنجاح',
        ]);
    }

    public function revoke(Request $request, BiometricRecord $biometricRecord)
    {
        $this->authorize('revoke', $biometricRecord);

        $validated = $request->validate([
            'revocation_reason' => 'required|string|max:500',
            'immediate_effect' => 'boolean',
            'grace_period' => 'nullable|integer|min:0|max:30', // days
        ]);

        $biometricRecord->update([
            'status' => $validated['immediate_effect'] ? 'revoked' : 'pending_revocation',
            'revocation_reason' => $validated['revocation_reason'],
            'revoked_at' => $validated['immediate_effect'] ? now() : null,
            'revocation_grace_period' => $validated['grace_period'],
            'revoked_by' => Auth::id(),
        ]);

        return redirect()->route('security.biometric.show', $biometricRecord)
            ->with('success', 'تم إلغاء السجل البيومتري بنجاح');
    }

    public function analytics()
    {
        $user = Auth::user();
        
        $analytics = [
            'verification_trends' => $this->getVerificationTrends($user->id),
            'biometric_distribution' => $this->getBiometricDistribution($user->id),
            'quality_analysis' => $this->getQualityAnalysis($user->id),
            'security_metrics' => $this->getSecurityMetrics($user->id),
            'performance_analysis' => $this->getPerformanceAnalysis($user->id),
        ];

        return view('security.biometric.analytics', compact('analytics'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,pdf',
            'date_range' => 'required|in:last_week,last_month,last_quarter,last_year,custom',
            'start_date' => 'nullable|date|required_if:date_range,custom',
            'end_date' => 'nullable|date|required_if:date_range,custom|after_or_equal:start_date',
            'biometric_type' => 'nullable|in:fingerprint,face_scan,iris_scan,voice_pattern,signature,dna',
            'status' => 'nullable|in:active,inactive,revoked,expired',
        ]);

        $records = $this->getFilteredBiometricRecords($validated);

        switch ($validated['format']) {
            case 'csv':
                return $this->exportCSV($records);
            case 'xlsx':
                return $this->exportExcel($records);
            case 'pdf':
                return $this->exportPDF($records);
        }
    }

    private function generateBiometricId()
    {
        return 'BIO-' . Str::upper(Str::random(8)) . '-' . time();
    }

    private function encryptBiometricData($data, $method)
    {
        // Simulate biometric data encryption
        switch ($method) {
            case 'aes-256':
                return openssl_encrypt($data, 'aes-256-cbc', Str::random(32), 0, Str::random(16));
            case 'rsa-2048':
                return 'RSA_ENCRYPTED:' . base64_encode($data);
            case 'ecdsa':
                return 'ECDSA_ENCRYPTED:' . base64_encode($data);
            case 'quantum_safe':
                return 'QUANTUM_SAFE:' . base64_encode($data);
            default:
                return base64_encode($data);
        }
    }

    private function generateBiometricTemplate($template)
    {
        // Simulate biometric template generation
        return [
            'template_data' => hash('sha-256', $template),
            'features' => $this->extractBiometricFeatures($template),
            'minutiae' => $this->extractMinutiae($template),
            'quality_metrics' => $this->calculateQualityMetrics($template),
            'created_at' => now(),
        ];
    }

    private function extractBiometricFeatures($template)
    {
        // Simulate feature extraction
        return [
            'feature_1' => rand(0, 1000),
            'feature_2' => rand(0, 1000),
            'feature_3' => rand(0, 1000),
            'feature_4' => rand(0, 1000),
            'feature_5' => rand(0, 1000),
        ];
    }

    private function extractMinutiae($template)
    {
        // Simulate minutiae extraction
        return [
            'minutiae_count' => rand(20, 100),
            'minutiae_points' => array_fill(0, rand(20, 100), ['x' => rand(0, 500), 'y' => rand(0, 500), 'angle' => rand(0, 360)]),
        ];
    }

    private function calculateQualityMetrics($template)
    {
        // Simulate quality metrics calculation
        return [
            'signal_to_noise_ratio' => rand(20, 60),
            'clarity_score' => rand(60, 100),
            'completeness' => rand(70, 100),
            'consistency' => rand(80, 100),
        ];
    }

    private function performBiometricVerification(BiometricRecord $record)
    {
        // Simulate biometric verification
        $success = rand(70, 100) > 30; // 70% success rate
        $score = $success ? rand(80, 100) : rand(20, 70);

        return [
            'success' => $success,
            'score' => $score,
            'confidence' => $score / 100,
            'verification_time' => rand(100, 2000), // milliseconds
            'match_details' => [
                'similarity_score' => $score,
                'feature_matches' => rand(10, 20),
                'minutiae_matches' => rand(15, 25),
                'quality_match' => $score > 70,
            ],
            'security_checks' => [
                'template_valid' => true,
                'data_integrity' => true,
                'encryption_valid' => true,
                'timestamp_valid' => true,
            ],
        ];
    }

    private function performDetailedVerification(BiometricRecord $record, $data)
    {
        // Perform detailed verification based on method
        switch ($data['verification_method']) {
            case '1:1':
                return $this->performOneToOneVerification($record, $data);
            case '1:N':
                return $this->performOneToManyVerification($record, $data);
            case 'many:many':
                return $this->performManyToManyVerification($record, $data);
            default:
                return $this->performBiometricVerification($record);
        }
    }

    private function performOneToOneVerification(BiometricRecord $record, $data)
    {
        // Simulate 1:1 verification
        $matchScore = $this->calculateMatchScore($record->biometric_template, $data['biometric_data']);
        $success = $matchScore >= $record->verification_threshold;

        return [
            'success' => $success,
            'score' => $matchScore,
            'method' => '1:1',
            'verification_time' => rand(50, 500),
            'confidence' => $matchScore / 100,
        ];
    }

    private function performOneToManyVerification(BiometricRecord $record, $data)
    {
        // Simulate 1:N verification
        $candidates = $this->findMatchingCandidates($data['biometric_data'], $record->biometric_type);
        $bestMatch = $candidates[0] ?? null;
        
        return [
            'success' => $bestMatch && $bestMatch['score'] >= $record->verification_threshold,
            'score' => $bestMatch['score'] ?? 0,
            'method' => '1:N',
            'candidates_found' => count($candidates),
            'verification_time' => rand(200, 1000),
            'confidence' => $bestMatch ? $bestMatch['score'] / 100 : 0,
        ];
    }

    private function performManyToManyVerification(BiometricRecord $record, $data)
    {
        // Simulate many:many verification
        $matches = $this->findMultipleMatches($data['biometric_data'], $record->biometric_type);
        
        return [
            'success' => count($matches) > 0,
            'score' => $matches[0]['score'] ?? 0,
            'method' => 'many:many',
            'matches_found' => count($matches),
            'verification_time' => rand(500, 2000),
            'confidence' => $matches[0]['score'] / 100 ?? 0,
        ];
    }

    private function performBiometricEnrollment($data)
    {
        // Simulate biometric enrollment
        $qualityScores = array_column($data['enrollment_samples'], 'quality');
        $averageQuality = array_sum($qualityScores) / count($qualityScores);
        
        if ($averageQuality < $data['quality_threshold']) {
            return [
                'success' => false,
                'message' => 'جودة العينات غير كافية',
                'average_quality' => $averageQuality,
            ];
        }

        $template = $this->generateEnrollmentTemplate($data['enrollment_samples']);
        
        return [
            'success' => true,
            'template' => $template,
            'quality_score' => $averageQuality,
            'enrollment_id' => Str::uuid(),
        ];
    }

    private function performBiometricAuthentication($data)
    {
        // Simulate biometric authentication
        $records = BiometricRecord::where('biometric_type', $data['biometric_type'])
            ->where('status', 'active')
            ->get();

        $bestMatch = null;
        $bestScore = 0;

        foreach ($records as $record) {
            $score = $this->calculateMatchScore($record->biometric_template, $data['biometric_data']);
            if ($score > $bestScore && $score >= $record->verification_threshold) {
                $bestScore = $score;
                $bestMatch = $record;
            }
        }

        return [
            'success' => $bestMatch !== null,
            'user_id' => $bestMatch ? $bestMatch->user_id : null,
            'score' => $bestScore,
            'verification_time' => rand(100, 1000),
            'confidence' => $bestScore / 100,
        ];
    }

    private function calculateMatchScore($template, $data)
    {
        // Simulate match score calculation
        return rand(0, 100);
    }

    private function findMatchingCandidates($data, $type)
    {
        // Simulate candidate finding
        $candidates = [];
        for ($i = 0; $i < rand(0, 5); $i++) {
            $candidates[] = [
                'user_id' => rand(1, 1000),
                'score' => rand(0, 100),
                'confidence' => rand(0, 100) / 100,
            ];
        }
        
        return $candidates;
    }

    private function findMultipleMatches($data, $type)
    {
        // Simulate multiple matches finding
        return $this->findMatchingCandidates($data, $type);
    }

    private function generateEnrollmentTemplate($samples)
    {
        // Simulate enrollment template generation
        return [
            'template_id' => Str::uuid(),
            'samples_count' => count($samples),
            'average_quality' => array_sum(array_column($samples, 'quality')) / count($samples),
            'features' => $this->extractBiometricFeatures(json_encode($samples)),
            'created_at' => now(),
        ];
    }

    private function verifyTemplateUpdate(BiometricRecord $record, $data)
    {
        // Simulate template verification
        $similarity = $this->calculateMatchScore($record->biometric_template, $data['new_template']);
        
        return [
            'success' => $similarity > 70,
            'similarity' => $similarity,
            'message' => $similarity > 70 ? 'القالب الجديد متوافق' : 'القالب الجديد غير متوافق',
        ];
    }

    private function getVerificationTrends($userId)
    {
        return BiometricRecord::where('user_id', $userId)
            ->selectRaw('DATE(last_verified_at) as date, COUNT(*) as count')
            ->whereNotNull('last_verified_at')
            ->where('last_verified_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getBiometricDistribution($userId)
    {
        return BiometricRecord::where('user_id', $userId)
            ->selectRaw('biometric_type, COUNT(*) as count')
            ->groupBy('biometric_type')
            ->get();
    }

    private function getQualityAnalysis($userId)
    {
        return [
            'average_quality' => BiometricRecord::where('user_id', $userId)
                ->avg('quality_score'),
            'quality_distribution' => BiometricRecord::where('user_id', $userId)
                ->selectRaw('CASE WHEN quality_score >= 90 THEN "excellent" WHEN quality_score >= 70 THEN "good" WHEN quality_score >= 50 THEN "fair" ELSE "poor" END as quality_level, COUNT(*) as count')
                ->groupBy('quality_level')
                ->get(),
        ];
    }

    private function getSecurityMetrics($userId)
    {
        return [
            'total_verifications' => BiometricRecord::where('user_id', $userId)
                ->sum('verification_count'),
            'failed_verifications' => BiometricRecord::where('user_id', $userId)
                ->sum('failed_verification_count'),
            'success_rate' => $this->calculateSuccessRate($userId),
            'average_verification_time' => $this->calculateAverageVerificationTime($userId),
        ];
    }

    private function getPerformanceAnalysis($userId)
    {
        return [
            'false_acceptance_rate' => BiometricRecord::where('user_id', $userId)
                ->avg('false_acceptance_rate'),
            'false_rejection_rate' => BiometricRecord::where('user_id', $userId)
                ->avg('false_rejection_rate'),
            'verification_speed' => 'fast',
            'reliability_score' => 95.5, // Example value
        ];
    }

    private function calculateSuccessRate($userId)
    {
        $total = BiometricRecord::where('user_id', $userId)
            ->sum('verification_count');
        $failed = BiometricRecord::where('user_id', $userId)
            ->sum('failed_verification_count');

        return $total > 0 ? (($total - $failed) / $total) * 100 : 0;
    }

    private function calculateAverageVerificationTime($userId)
    {
        // Simulate average verification time calculation
        return rand(100, 500); // milliseconds
    }

    private function getFilteredBiometricRecords($validated)
    {
        $query = BiometricRecord::where('user_id', Auth::id());

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

        if (isset($validated['biometric_type'])) {
            $query->where('biometric_type', $validated['biometric_type']);
        }

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        return $query->get();
    }

    private function exportCSV($records)
    {
        $filename = 'biometric_records_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID', 'Biometric ID', 'Type', 'Quality Score', 'Status',
                'Created At', 'Last Verified', 'Verification Count'
            ]);

            // Data
            foreach ($records as $record) {
                fputcsv($file, [
                    $record->id,
                    $record->biometric_id,
                    $record->biometric_type,
                    $record->quality_score,
                    $record->status,
                    $record->created_at,
                    $record->last_verified_at,
                    $record->verification_count,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel($records)
    {
        // Implementation for Excel export
        return response()->download('biometric_records.xlsx');
    }

    private function exportPDF($records)
    {
        // Implementation for PDF export
        return response()->download('biometric_records.pdf');
    }
}
