<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\KycVerification;
use App\Models\KycDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessKycDocuments implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $tries = 3;
    public $backoff = [60, 300, 900];
    public $timeout = 600;

    protected $kycVerificationId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $kycVerificationId)
    {
        $this->kycVerificationId = $kycVerificationId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $kycVerification = KycVerification::with(['user', 'documents'])->find($this->kycVerificationId);
        
        if (!$kycVerification) {
            Log::error('KYC verification not found', ['kyc_id' => $this->kycVerificationId]);
            return;
        }
        
        try {
            // Process each document
            foreach ($kycVerification->documents as $document) {
                $this->processDocument($document);
            }
            
            // Run automated checks
            $this->runAutomatedChecks($kycVerification);
            
            // Update status
            if ($this->allDocumentsVerified($kycVerification)) {
                $kycVerification->update(['status' => 'verified']);
            }
            
            Log::info('KYC documents processed successfully', [
                'kyc_id' => $this->kycVerificationId,
                'user_id' => $kycVerification->user_id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to process KYC documents', [
                'kyc_id' => $this->kycVerificationId,
                'error' => $e->getMessage()
            ]);
            
            // Update status to failed
            $kycVerification->update([
                'status' => 'processing_failed',
                'processing_error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Process individual document
     */
    private function processDocument(KycDocument $document): void
    {
        // Perform OCR on document if it's an image
        if ($this->isImageDocument($document)) {
            $this->performOcr($document);
        }
        
        // Validate document format and quality
        $this->validateDocument($document);
        
        // Check for duplicates
        $this->checkForDuplicates($document);
        
        // Update document status
        $document->update([
            'processing_status' => 'processed',
            'processed_at' => now()
        ]);
    }
    
    /**
     * Run automated checks
     */
    private function runAutomatedChecks(KycVerification $kycVerification): void
    {
        $checks = [];
        
        // Check if all required documents are present
        $requiredDocs = $this->getRequiredDocuments($kycVerification->level);
        $submittedDocs = $kycVerification->documents->pluck('document_type')->toArray();
        
        $missingDocs = array_diff($requiredDocs, $submittedDocs);
        if (!empty($missingDocs)) {
            $checks['missing_documents'] = $missingDocs;
        }
        
        // Check document expiration
        foreach ($kycVerification->documents as $document) {
            if ($this->isExpiredDocument($document)) {
                $checks['expired_documents'][] = $document->document_type;
            }
        }
        
        // Check for suspicious patterns
        $suspiciousPatterns = $this->checkSuspiciousPatterns($kycVerification);
        if (!empty($suspiciousPatterns)) {
            $checks['suspicious_patterns'] = $suspiciousPatterns;
        }
        
        // Update KYC with check results
        $kycVerification->update([
            'automated_checks' => $checks,
            'automated_checks_passed' => empty($checks)
        ]);
    }
    
    /**
     * Perform OCR on document
     */
    private function performOcr(KycDocument $document): void
    {
        try {
            // This would integrate with an OCR service
            // Example implementation with Tesseract or cloud service
            
            $filePath = Storage::disk('private')->path($document->file_path);
            
            // Placeholder for OCR processing
            $ocrResult = $this->extractTextFromImage($filePath);
            
            if ($ocrResult) {
                $document->update([
                    'ocr_text' => $ocrResult,
                    'ocr_processed_at' => now()
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('OCR processing failed', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Validate document
     */
    private function validateDocument(KycDocument $document): void
    {
        $validation = [
            'file_size_valid' => $this->validateFileSize($document),
            'file_format_valid' => $this->validateFileFormat($document),
            'image_quality_valid' => $this->validateImageQuality($document),
            'content_valid' => $this->validateContent($document)
        ];
        
        $document->update([
            'validation_results' => $validation,
            'validation_passed' => !in_array(false, $validation)
        ]);
    }
    
    /**
     * Check for duplicate documents
     */
    private function checkForDuplicates(KycDocument $document): void
    {
        $duplicates = KycDocument::where('document_type', $document->document_type)
            ->where('file_hash', $document->file_hash ?? null)
            ->where('id', '!=', $document->id)
            ->count();
            
        $document->update([
            'is_duplicate' => $duplicates > 0,
            'duplicate_count' => $duplicates
        ]);
    }
    
    /**
     * Check if all documents are verified
     */
    private function allDocumentsVerified(KycVerification $kycVerification): bool
    {
        return $kycVerification->documents()
            ->where('verification_status', 'verified')
            ->count() === $kycVerification->documents()->count();
    }
    
    /**
     * Get required documents for KYC level
     */
    private function getRequiredDocuments(string $level): array
    {
        $requirements = [
            'basic' => ['national_id', 'selfie'],
            'standard' => ['national_id', 'passport', 'proof_of_address', 'selfie'],
            'enhanced' => ['national_id', 'passport', 'proof_of_address', 'bank_statement', 'tax_return', 'selfie']
        ];
        
        return $requirements[$level] ?? $requirements['basic'];
    }
    
    /**
     * Check if document is expired
     */
    private function isExpiredDocument(KycDocument $document): bool
    {
        // Check expiration based on document type
        $expiryFields = [
            'passport' => 'passport_expiry_date',
            'national_id' => 'id_expiry_date',
            'driver_license' => 'license_expiry_date'
        ];
        
        $expiryField = $expiryFields[$document->document_type] ?? null;
        
        if ($expiryField && isset($document->meta[$expiryField])) {
            $expiryDate = $document->meta[$expiryField];
            return now()->greaterThan($expiryDate);
        }
        
        return false;
    }
    
    /**
     * Check for suspicious patterns
     */
    private function checkSuspiciousPatterns(KycVerification $kycVerification): array
    {
        $patterns = [];
        
        // Check for multiple submissions in short time
        $recentSubmissions = KycVerification::where('user_id', $kycVerification->user_id)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();
            
        if ($recentSubmissions > 3) {
            $patterns[] = 'multiple_submissions';
        }
        
        // Check for mismatched information
        if ($this->hasMismatchedInfo($kycVerification)) {
            $patterns[] = 'mismatched_info';
        }
        
        // Check for disposable email
        if ($this->isDisposableEmail($kycVerification->user->email)) {
            $patterns[] = 'disposable_email';
        }
        
        return $patterns;
    }
    
    /**
     * Helper methods
     */
    private function isImageDocument(KycDocument $document): bool
    {
        $imageTypes = ['national_id', 'passport', 'selfie', 'driver_license'];
        return in_array($document->document_type, $imageTypes);
    }
    
    private function extractTextFromImage(string $filePath): ?string
    {
        // Placeholder for OCR implementation
        // Would integrate with Tesseract, Google Vision API, or similar
        return null;
    }
    
    private function validateFileSize(KycDocument $document): bool
    {
        $maxSize = 10 * 1024 * 1024; // 10MB
        return $document->file_size <= $maxSize;
    }
    
    private function validateFileFormat(KycDocument $document): bool
    {
        $allowedFormats = [
            'image/jpeg', 'image/png', 'image/jpg',
            'application/pdf'
        ];
        
        return in_array($document->mime_type, $allowedFormats);
    }
    
    private function validateImageQuality(KycDocument $document): bool
    {
        // Placeholder for image quality validation
        // Would check resolution, clarity, etc.
        return true;
    }
    
    private function validateContent(KycDocument $document): bool
    {
        // Placeholder for content validation
        // Would check if document contains expected information
        return true;
    }
    
    private function hasMismatchedInfo(KycVerification $kycVerification): bool
    {
        // Check for mismatches between documents and provided info
        // This is a simplified check
        return false;
    }
    
    private function isDisposableEmail(string $email): bool
    {
        // Check against disposable email domains
        $disposableDomains = ['tempmail.org', '10minutemail.com', 'guerrillamail.com'];
        $domain = substr(strrchr($email, '@'), 1);
        
        return in_array($domain, $disposableDomains);
    }
    
    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('KYC document processing job failed', [
            'kyc_id' => $this->kycVerificationId,
            'error' => $exception->getMessage()
        ]);
        
        // Update KYC status
        $kycVerification = KycVerification::find($this->kycVerificationId);
        if ($kycVerification) {
            $kycVerification->update([
                'status' => 'processing_failed',
                'processing_error' => $exception->getMessage()
            ]);
        }
    }
}
