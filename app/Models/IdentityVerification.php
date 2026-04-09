<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class IdentityVerification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'verification_type',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'national_id',
        'address',
        'city',
        'country',
        'postal_code',
        'id_document_path',
        'proof_of_address_path',
        'additional_documents',
        'biometric_data',
        'verification_method',
        'verification_steps',
        'verification_results',
        'verification_score',
        'verification_status',
        'submitted_at',
        'verified_at',
        'expires_at',
        'verification_level',
        'reference_number',
        'verification_purpose',
        'urgency_level',
        'contact_information',
        'additional_notes',
        'auto_detected',
        'detection_method',
        'verification_provider',
        'verification_cost',
        'verification_currency',
        'compliance_status',
        'regulatory_requirements',
        'audit_trail',
        'verification_history',
        'reverification_requested',
        'reverification_reason',
        'updated_documents',
        'quality_metrics',
        'risk_assessment',
        'fraud_indicators',
        'verification_confidence',
        'biometric_confidence',
        'document_authenticity',
        'data_integrity',
        'verification_metadata',
        'created_by',
        'updated_by',
        'verified_by',
    ];

    protected $casts = [
        'additional_documents' => 'array',
        'biometric_data' => 'array',
        'verification_steps' => 'array',
        'verification_results' => 'array',
        'verification_score' => 'integer',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'auto_detected' => 'boolean',
        'verification_cost' => 'decimal:2',
        'compliance_status' => 'array',
        'regulatory_requirements' => 'array',
        'audit_trail' => 'array',
        'verification_history' => 'array',
        'reverification_requested' => 'boolean',
        'updated_documents' => 'array',
        'quality_metrics' => 'array',
        'risk_assessment' => 'array',
        'fraud_indicators' => 'array',
        'verification_confidence' => 'float',
        'biometric_confidence' => 'float',
        'document_authenticity' => 'float',
        'data_integrity' => 'float',
        'verification_metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'submitted_at',
        'verified_at',
        'expires_at',
        'date_of_birth',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function verificationSteps(): HasMany
    {
        return $this->hasMany(VerificationStep::class, 'identity_verification_id');
    }

    public function verificationDocuments(): HasMany
    {
        return $this->hasMany(VerificationDocument::class, 'identity_verification_id');
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function fraudAlerts(): MorphMany
    {
        return $this->morphMany(FraudAlert::class, 'auditable');
    }

    public function complianceRecords(): MorphMany
    {
        return $this->morphMany(ComplianceRecord::class, 'auditable');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('verification_status', 'rejected');
    }

    public function scopeFailed($query)
    {
        return $query->where('verification_status', 'failed');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('verification_type', $type);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('verification_level', $level);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('verification_method', $method);
    }

    public function scopeAutoDetected($query)
    {
        return $query->where('auto_detected', true);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    // Methods
    public function calculateVerificationScore(): int
    {
        $score = 0;

        // Base score by verification level
        switch ($this->verification_level) {
            case 'premium':
                $score += 40;
                break;
            case 'enhanced':
                $score += 30;
                break;
            case 'standard':
                $score += 20;
                break;
            case 'basic':
                $score += 10;
                break;
        }

        // Additional score by verification method
        switch ($this->verification_method) {
            case 'automated':
                $score += 15;
                break;
            case 'hybrid':
                $score += 10;
                break;
            case 'manual':
                $score += 5;
                break;
        }

        // Score by biometric data quality
        if ($this->biometric_data && isset($this->biometric_data['quality_score'])) {
            $score += $this->biometric_data['quality_score'] * 0.25;
        }

        // Score by document authenticity
        if ($this->document_authenticity) {
            $score += $this->document_authenticity * 0.15;
        }

        // Score by data integrity
        if ($this->data_integrity) {
            $score += $this->data_integrity * 0.10;
        }

        // Score by verification confidence
        if ($this->verification_confidence) {
            $score += $this->verification_confidence * 0.10;
        }

        return min(100, round($score));
    }

    public function assessRisk(): array
    {
        $riskFactors = [];
        $riskScore = 0;

        // Check fraud indicators
        if ($this->fraud_indicators && !empty($this->fraud_indicators)) {
            foreach ($this->fraud_indicators as $indicator) {
                $riskScore += $indicator['risk_score'] ?? 0;
                $riskFactors[] = $indicator['description'];
            }
        }

        // Check verification confidence
        if ($this->verification_confidence < 70) {
            $riskFactors[] = 'مستوى ثقة منخفض';
            $riskScore += 20;
        }

        // Check document authenticity
        if ($this->document_authenticity < 80) {
            $riskFactors[] = 'مستوى أصالة الوثائق منخفض';
            $riskScore += 15;
        }

        // Check data integrity
        if ($this->data_integrity < 80) {
            $riskFactors[] = 'مشاكل في سلامة البيانات';
            $riskScore += 10;
        }

        // Check for reverification requests
        if ($this->reverification_requested) {
            $riskFactors[] = 'طلب إعادة التحقق';
            $riskScore += 5;
        }

        return [
            'risk_level' => $this->determineRiskLevel($riskScore, $riskFactors),
            'risk_score' => $riskScore,
            'risk_factors' => $riskFactors,
            'recommendations' => $this->generateRiskRecommendations($riskFactors),
            'confidence_level' => $this->verification_confidence,
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return $this->verification_status === 'verified' && !$this->isExpired();
    }

    public function canReverify(): bool
    {
        return $this->verification_status === 'rejected' && 
               ($this->can_resubmit ?? true) &&
               !$this->isExpired();
    }

    public function startVerification(): void
    {
        $this->update([
            'verification_status' => 'in_progress',
            'verification_steps' => $this->initializeVerificationSteps(),
            'verification_results' => [],
            'audit_trail' => $this->initializeAuditTrail(),
        ]);

        // Create initial verification step
        $this->createVerificationStep('document_validation', 'التحقق من صحة الوثائق');
    }

    public function addVerificationStep(string $step, string $description, array $data = []): void
    {
        $step = VerificationStep::create([
            'identity_verification_id' => $this->id,
            'step' => $step,
            'description' => $description,
            'status' => 'pending',
            'data' => $data,
            'created_at' => now(),
        ]);

        $this->update([
            'verification_steps' => $this->verification_steps,
            'audit_trail' => $this->addToAuditTrail('step_added', $step, $description),
        ]);
    }

    public function completeVerificationStep(int $stepId, string $status, array $results = []): void
    {
        $step = VerificationStep::find($stepId);
        if ($step) {
            $step->update([
                'status' => $status,
                'results' => $results,
                'completed_at' => now(),
            ]);

            $this->update([
                'verification_results' => $this->verification_results,
                'audit_trail' => $this->completeAuditTrail($step->step, $status, $results),
            ]);
        }
    }

    public function completeVerification(array $finalResults): void
    {
        $finalScore = $this->calculateVerificationScore();
        $finalStatus = $this->determineFinalStatus($finalScore);

        $this->update([
            'verification_status' => $finalStatus,
            'verification_score' => $finalScore,
            'verification_results' => array_merge($this->verification_results, $finalResults),
            'verified_at' => $finalStatus === 'verified' ? now() : null,
            'verification_confidence' => $this->calculateConfidence($finalResults),
            'document_authenticity' => $this->assessDocumentAuthenticity(),
            'data_integrity' => $this->assessDataIntegrity(),
            'audit_trail' => $this->completeAuditTrail('verification_completed', $finalStatus, $finalResults),
        ]);

        // Log verification completion
        \Log::info('Identity verification completed', [
            'verification_id' => $this->id,
            'status' => $finalStatus,
            'score' => $finalScore,
            'method' => $this->verification_method,
        ]);
    }

    public function rejectVerification(string $reason, array $details = [], bool $canResubmit = true): void
    {
        $this->update([
            'verification_status' => 'rejected',
            'reverification_requested' => $canResubmit,
            'reverification_reason' => $reason,
            'updated_documents' => $details,
            'audit_trail' => $this->addToAuditTrail('verification_rejected', $reason, $details),
        ]);
    }

    public function requestReverification(string $reason): void
    {
        $this->update([
            'reverification_requested' => true,
            'reverification_reason' => $reason,
            'verification_status' => 'pending',
            'audit_trail' => $this->addToAuditTrail('reverification_requested', $reason),
        ]);
    }

    public function updateBiometricData(array $biometricData): void
    {
        $this->update([
            'biometric_data' => $biometricData,
            'biometric_confidence' => $this->calculateBiometricConfidence($biometricData),
            'audit_trail' => $this->updateAuditTrail('biometric_data_updated'),
        ]);
    }

    public function updateDocuments(array $documents): void
    {
        $this->update([
            'additional_documents' => $documents,
            'document_authenticity' => $this->assessDocumentAuthenticity(),
            'data_integrity' => $this->assessDataIntegrity(),
            'audit_trail' => $this->updateAuditTrail('documents_updated'),
        ]);
    }

    public function extendExpiration(\Carbon\Carbon $newExpiryDate): void
    {
        $this->update([
            'expires_at' => $newExpiryDate,
            'audit_trail' => $this->addToAuditTrail('expiry_extended', "Extended to {$newExpiryDate}"),
        ]);
    }

    public function generateVerificationCertificate(): array
    {
        return [
            'certificate_id' => Str::uuid(),
            'verification_id' => $this->id,
            'reference_number' => $this->reference_number,
            'verified_name' => $this->first_name . ' ' . $this->last_name,
            'verification_type' => $this->verification_type,
            'verification_level' => $this->verification_level,
            'verification_method' => $this->verification_method,
            'verified_at' => $this->verified_at,
            'expires_at' => $this->expires_at,
            'verification_score' => $this->verification_score,
            'verification_confidence' => $this->verification_confidence,
            'certificate_data' => [
                'digital_signature' => $this->generateDigitalSignature(),
                'issuer' => config('app.name'),
                'valid_from' => $this->verified_at,
                'valid_until' => $this->expires_at,
                'certificate_hash' => $this->generateCertificateHash(),
            ],
            'created_at' => now(),
        ];
    }

    public function performBiometricVerification(array $biometricData): array
    {
        $verificationResult = [
            'success' => false,
            'match_score' => 0,
            'confidence' => 0,
            'details' => [],
        ];

        // Perform biometric verification based on stored data
        if ($this->biometric_data && isset($this->biometric_data['template'])) {
            $matchScore = $this->compareBiometricData(
                $this->biometric_data['template'],
                $biometricData
            );

            $verificationResult = [
                'success' => $matchScore >= 80,
                'match_score' => $matchScore,
                'confidence' => $this->calculateBiometricConfidence($biometricData),
                'details' => [
                    'template_match' => $matchScore >= 80,
                    'quality_score' => $biometricData['quality_score'] ?? 0,
                    'verification_time' => rand(100, 2000), // milliseconds
                ],
            ];
        }

        return $verificationResult;
    }

    public function validateDocuments(): array
    {
        $validationResults = [
            'id_document' => $this->validateDocument($this->id_document_path),
            'proof_of_address' => $this->validateDocument($this->proof_of_address_path),
            'additional_documents' => [],
        ];

        // Validate additional documents
        if ($this->additional_documents) {
            foreach ($this->additional_documents as $document) {
                $validationResults['additional_documents'][] = $this->validateDocument($document['path']);
            }
        }

        return $validationResults;
    }

    public function checkCompliance(): array
    {
        $complianceIssues = [];
        $complianceScore = 100;

        // Check regulatory requirements
        if ($this->regulatory_requirements) {
            foreach ($this->regulatory_requirements as $requirement) {
                if (!$this->meetsRequirement($requirement)) {
                    $complianceIssues[] = $requirement['description'];
                    $complianceScore -= 10;
                }
            }
        }

        // Check verification level compliance
        $requiredLevels = [
            'financial_services' => 'enhanced',
            'government_id' => 'standard',
            'corporate' => 'basic',
        ];

        if (isset($requiredLevels[$this->verification_type]) && 
            $this->verification_level !== $requiredLevels[$this->verification_type]) {
            $complianceIssues[] = 'مستوى التحقق لا يلبي المتطلبات';
            $complianceScore -= 15;
        }

        return [
            'compliant' => $complianceScore >= 80,
            'score' => $complianceScore,
            'issues' => $complianceIssues,
            'recommendations' => $this->generateComplianceRecommendations($complianceIssues),
        ];
    }

    public function generateVerificationReport(): array
    {
        return [
            'verification_id' => $this->id,
            'reference_number' => $this->reference_number,
            'verification_type' => $this->verification_type,
            'verification_level' => $this->verification_level,
            'verification_method' => $this->verification_method,
            'verification_status' => $this->verification_status,
            'verification_score' => $this->verification_score,
            'confidence_level' => $this->verification_confidence,
            'submitted_at' => $this->submitted_at,
            'verified_at' => $this->verified_at,
            'expires_at' => $this->expires_at,
            'personal_info' => [
                'name' => $this->first_name . ' ' . $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'date_of_birth' => $this->date_ofirth,
                'national_id' => $this->national_id,
                'address' => $this->address,
                'city' => $this->city,
                'country' => $this->country,
                'postal_code' => $this->postal_code,
            ],
            'verification_details' => [
                'steps' => $this->verification_steps,
                'results' => $this->verification_results,
                'quality_metrics' => $this->quality_metrics,
                'risk_assessment' => $this->assessRisk(),
            ],
            'biometric_info' => $this->biometric_data,
            'document_info' => [
                'id_document' => $this->id_document_path,
                'proof_of_address' => $this->proof_of_address_path,
                'additional_documents' => $this->additional_documents,
            ],
            'compliance_info' => $this->checkCompliance(),
            'audit_trail' => $this->audit_trail,
            'generated_at' => now(),
        ];
    }

    // Private methods
    private function initializeVerificationSteps(): array
    {
        $steps = [
            ['step' => 'document_validation', 'description' => 'التحقق من صحة الوثائق', 'status' => 'pending', 'order' => 1],
            ['step' => 'identity_check', 'description' => 'التحقق من الهوية', 'status' => 'pending', 'order' => 2],
            ['step' => 'address_verification', 'description' => 'التحقق من العنوان', 'status' => 'pending', 'order' => 3],
            ['step' => 'biometric_verification', 'description' => 'التحقق البيومتري', 'status' => 'pending', 'order' => 4],
            ['step' => 'final_review', 'description' => 'المراجعة النهائي', 'status' => 'pending', 'order' => 5],
        ];

        if ($this->verification_method === 'automated') {
            // Adjust steps for automated verification
            $steps = array_filter($steps, function ($step) {
                return !in_array($step['step'], ['biometric_verification']);
            });
        }

        return $steps;
    }

    private function initializeAuditTrail(): array
    {
        return [
            [
                'action' => 'verification_started',
                'timestamp' => now()->toISOString(),
                'user_id' => auth()->id(),
                'details' => 'Identity verification process started',
            ],
        ];
    }

    private function initializeAuditTrail(): array
    {
        return $this->audit_trail ?? [];
    }

    private function addToAuditTrail(string $action, string $description, array $details = []): array
    {
        $trail = $this->audit_trail ?? [];
        
        $trail[] = [
            'action' => $action,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'description' => $description,
            'details' => $details,
        ];

        return $trail;
    }

    private function completeAuditTrail(string $step, string $status, array $results): array
    {
        $trail = $this->audit_trail ?? [];
        
        $trail[] = [
            'action' => $step,
            'status' => $status,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'details' => "Step {$step} {$status}",
            'results' => $results,
        ];

        return $trail;
    }

    private function updateAuditTrail(string $action, string $description): array
    {
        $trail = $this->audit_trail ?? [];
        
        $trail[] = [
            'action' => $action,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'description' => $description,
        ];

        return $trail;
    }

    private function determineFinalStatus(int $score): string
    {
        if ($score >= 90) {
            return 'verified';
        } elseif ($score >= 70) {
            return 'verified';
        } elseif ($score >= 50) {
            return 'pending_review';
        } else {
            return 'failed';
        }
    }

    private function calculateConfidence(array $results): float
    {
        // Calculate confidence based on verification results
        $confidence = 0;
        $totalSteps = count($results);

        foreach ($results as $result) {
            if (isset($result['success']) {
                $confidence += $result['success'] ? 20 : 0;
            }
        }

        return $totalSteps > 0 ? ($confidence / $totalSteps) : 0;
    }

    private function assessDocumentAuthenticity(): float
    {
        // Implement document authenticity assessment
        $authenticity = 0;
        $totalDocuments = 0;

        if ($this->id_document_path) {
            $authenticity += $this->validateDocument($this->id_document_path)['score'];
            $totalDocuments++;
        }

        if ($this->proof_of_address_path) {
            $authenticity += $this->validateDocument($this->proof_of_address_path)['score'];
            $totalDocuments++;
        }

        if ($this->additional_documents) {
            foreach ($this->additional_documents as $document) {
                $authenticity += $this->validateDocument($document['path'])['score'];
                $totalDocuments++;
            }
        }

        return $totalDocuments > 0 ? ($authenticity / $totalDocuments) : 0;
    }

    private function assessDataIntegrity(): float
    {
        // Implement data integrity assessment
        $integrity = 100;

        // Check for data consistency
        if (!$this->first_name || !$this->last_name) {
            $integrity -= 20;
        }

        if (!$this->email || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $integrity -= 15;
        }

        if (!$this->national_id || strlen($this->national_id) < 10) {
            $integrity -= 25;
        }

        return max(0, $integrity);
    }

    private function compareBiometricData(array $template, array $data): int
    {
        // Implement biometric data comparison
        $matchScore = 0;
        $totalFeatures = count($template);

        foreach ($template as $key => $value) {
            if (isset($data[$key])) {
                $matchScore += $this->compareBiometricFeature($value, $data[$key]);
            }
        }

        return $totalFeatures > 0 ? ($matchScore / $totalFeatures) * 100 : 0;
    }

    private function compareBiometricFeature($template, $data): int
    {
        // Implement biometric feature comparison
        // This would use actual biometric comparison algorithms
        return rand(70, 100); // Placeholder
    }

    private function calculateBiometricConfidence(array $biometricData): float
    {
        if (!isset($biometricData['quality_score'])) {
            return 0;
        }

        $confidence = $biometricData['quality_score'];

        // Adjust confidence based on multiple factors
        if (isset($biometric_data['consistency_score'])) {
            $confidence = ($confidence + $biometric_data['consistency_score']) / 2;
        }

        if (isset($biometric_data['uniqueness_score'])) {
            $confidence = ($confidence + $biometric_data['uniqueness_score']) / 2;
        }

        return min(100, $confidence);
    }

    private function validateDocument(string $path): array
    {
        // Implement document validation logic
        return [
            'valid' => true,
            'score' => rand(70, 95),
            'issues' => [],
            'file_size' => filesize($path),
            'mime_type' => mime_content_type($path),
            'checksum' => hash_file('sha256', $path),
        ];
    }

    private function meetsRequirement(array $requirement): bool
    {
        // Implement requirement checking logic
        return true; // Placeholder
    }

    private function generateComplianceRecommendations(array $issues): array
    {
        $recommendations = [];

        foreach ($issues as $issue) {
            switch ($issue) {
                case 'مستوى التحقق لا يلبي المتطلبات':
                    $recommendations[] = 'ترقية مستوى التحقق لتلبية المتطلبات';
                    break;
                case 'مستوى أصالة الوثائق منخفض':
                    $recommendations[] = 'تحسين جودة الوثائق المقدمة';
                    break;
                case 'مستوى ثقة منخفض':
                    $recommendations[] = 'تحسين جودة البيانات البيومترية';
                    break;
                case 'مشاكل في سلامة البيانات':
                    $recommendations[] = 'ضمان سلامة واتساق البيانات';
                    break;
            }
        }

        return $recommendations;
    }

    private function generateRiskRecommendations(array $riskFactors): array
    {
        $recommendations = [];

        foreach ($riskFactors as $factor) {
            switch ($factor) {
                case 'مستوى ثقة منخفض':
                    $recommendations[] = 'إعادة التحقق باستخدام طرق أكثر أماناً';
                    break;
                case 'مستوى أصالة الوثائق منخفض':
                    $recommendations[] = 'طلب وثائق إضافية ذات جودة أعلى';
                    break;
                case 'مشاكل في سلامة البيانات':
                    $recommendations[] = 'التحقق من سلامة البيانات وإصلاح أي مشاكل';
                    break;
                case 'طلب إعادة التحقق':
                    $recommendations[] = 'مراجعة البيانات وتقديم طلب إعادة التحقق';
                    break;
            }
        }

        return $recommendations;
    }

    private function determineRiskLevel(int $score, array $factors): string
    {
        if ($score >= 80 || in_array('مستوى ثقة منخفض', $factors)) {
            return 'high';
        } elseif ($score >= 60 || in_array('مستوى أصالة الوثائق منخفض', $factors)) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    private function generateDigitalSignature(): string
    {
        // Generate digital signature for verification certificate
        return hash('sha256', $this->id . $this->reference_number . $this->verified_at);
    }

    private function generateCertificateHash(): string
    {
        return hash('sha256', json_encode([
            'verification_id' => $this->id,
            'reference_number' => $this->reference_number,
            'verified_at' => $this->verified_at,
            'expires_at' => $this->expires_at,
        ]));
    }

    private function getImmediateActions(int $riskScore): array
    {
        $actions = [];

        if ($riskScore > 80) {
            $actions[] = 'إيقاف الوصول الفوري';
            $actions[] = 'إبلاغ فرق الأمني';
        } elseif ($riskScore > 60) {
            $actions[] = 'تقييد الوصول';
            $actions[] = 'مراجعة إضافية';
        } elseif ($riskScore > 40) {
            $actions[] = 'مراجعة شاملة';
        }

        return $actions;
    }
}
