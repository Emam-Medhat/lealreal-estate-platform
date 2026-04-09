<?php

namespace App\Services;

use App\Models\User;
use App\Models\KycVerification;
use App\Models\KycDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class KycService
{
    /**
     * Submit KYC verification
     */
    public function submitVerification(int $userId, array $data): KycVerification
    {
        $user = User::findOrFail($userId);
        
        DB::beginTransaction();
        try {
            // Create or update KYC verification
            $kycVerification = KycVerification::updateOrCreate(
                ['user_id' => $userId],
                [
                    'status' => 'pending',
                    'level' => $data['level'] ?? 'basic',
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'date_of_birth' => $data['date_of_birth'],
                    'nationality' => $data['nationality'],
                    'national_id' => $data['national_id'],
                    'address' => $data['address'],
                    'city' => $data['city'],
                    'country' => $data['country'],
                    'postal_code' => $data['postal_code'],
                    'phone' => $data['phone'],
                    'occupation' => $data['occupation'] ?? null,
                    'annual_income' => $data['annual_income'] ?? null,
                    'purpose' => $data['purpose'] ?? 'real_estate',
                    'submitted_at' => now()
                ]
            );
            
            // Upload documents
            if (isset($data['documents'])) {
                $this->uploadDocuments($kycVerification, $data['documents']);
            }
            
            // Update user KYC status
            $user->update([
                'kyc_status' => 'pending',
                'kyc_submitted_at' => now()
            ]);
            
            // Fire event
            event(new \App\Events\KycVerificationSubmitted($user, $kycVerification));
            
            DB::commit();
            
            return $kycVerification->refresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit KYC verification', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Verify documents
     */
    public function verifyDocuments(int $kycId, array $documentData): bool
    {
        $kycVerification = KycVerification::findOrFail($kycId);
        
        DB::beginTransaction();
        try {
            foreach ($documentData as $docId => $verification) {
                $document = KycDocument::find($docId);
                
                if ($document) {
                    $document->update([
                        'verification_status' => $verification['status'],
                        'verified_by' => $verification['verified_by'],
                        'verified_at' => now(),
                        'verification_notes' => $verification['notes'] ?? null,
                        'rejection_reason' => $verification['rejection_reason'] ?? null
                    ]);
                }
            }
            
            // Check if all documents are verified
            $allVerified = $kycVerification->documents()
                ->where('verification_status', 'verified')
                ->count() === $kycVerification->documents()->count();
                
            if ($allVerified) {
                $this->approveVerification($kycVerification->id, $verification['verified_by']);
            }
            
            DB::commit();
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to verify KYC documents', [
                'kyc_id' => $kycId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Approve verification
     */
    public function approveVerification(int $kycId, int $approvedBy): KycVerification
    {
        $kycVerification = KycVerification::findOrFail($kycId);
        $user = $kycVerification->user;
        
        DB::beginTransaction();
        try {
            $kycVerification->update([
                'status' => 'approved',
                'approved_by' => $approvedBy,
                'approved_at' => now()
            ]);
            
            // Update user KYC status
            $user->update([
                'kyc_verified' => true,
                'kyc_status' => 'approved',
                'kyc_approved_at' => now(),
                'kyc_level' => $kycVerification->level
            ]);
            
            // Fire event
            event(new \App\Events\KycVerificationApproved($user, $kycVerification, User::find($approvedBy)));
            
            DB::commit();
            
            return $kycVerification->refresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve KYC verification', [
                'kyc_id' => $kycId,
                'approved_by' => $approvedBy,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Reject verification
     */
    public function rejectVerification(int $kycId, int $rejectedBy, string $rejectionReason): KycVerification
    {
        $kycVerification = KycVerification::findOrFail($kycId);
        $user = $kycVerification->user;
        
        DB::beginTransaction();
        try {
            $kycVerification->update([
                'status' => 'rejected',
                'rejected_by' => $rejectedBy,
                'rejected_at' => now(),
                'rejection_reason' => $rejectionReason
            ]);
            
            // Update user KYC status
            $user->update([
                'kyc_verified' => false,
                'kyc_status' => 'rejected',
                'kyc_rejected_at' => now()
            ]);
            
            // Fire event
            event(new \App\Events\KycVerificationRejected($user, $kycVerification, User::find($rejectedBy), $rejectionReason));
            
            DB::commit();
            
            return $kycVerification->refresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject KYC verification', [
                'kyc_id' => $kycId,
                'rejected_by' => $rejectedBy,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Upload KYC documents
     */
    private function uploadDocuments(KycVerification $kycVerification, array $documents): void
    {
        foreach ($documents as $type => $file) {
            if ($file instanceof UploadedFile) {
                $path = $file->store('kyc-documents', 'private');
                
                KycDocument::create([
                    'kyc_verification_id' => $kycVerification->id,
                    'document_type' => $type,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'verification_status' => 'pending'
                ]);
            }
        }
    }
    
    /**
     * Get KYC requirements for level
     */
    public function getKycRequirements(string $level): array
    {
        $requirements = [
            'basic' => [
                'documents' => [
                    'national_id' => 'بطاقة الهوية الوطنية',
                    'selfie' => 'صورة شخصية'
                ],
                'fields' => [
                    'first_name', 'last_name', 'date_of_birth', 'nationality',
                    'national_id', 'address', 'city', 'country', 'phone'
                ]
            ],
            'standard' => [
                'documents' => [
                    'national_id' => 'بطاقة الهوية الوطنية',
                    'passport' => 'جواز السفر',
                    'proof_of_address' => 'إثبات العنوان',
                    'selfie' => 'صورة شخصية'
                ],
                'fields' => [
                    'first_name', 'last_name', 'date_of_birth', 'nationality',
                    'national_id', 'address', 'city', 'country', 'phone',
                    'occupation', 'annual_income'
                ]
            ],
            'enhanced' => [
                'documents' => [
                    'national_id' => 'بطاقة الهوية الوطنية',
                    'passport' => 'جواز السفر',
                    'proof_of_address' => 'إثبات العنوان',
                    'bank_statement' => 'كشف حساب بنكي',
                    'tax_return' => 'إقرار ضريبي',
                    'selfie' => 'صورة شخصية'
                ],
                'fields' => [
                    'first_name', 'last_name', 'date_of_birth', 'nationality',
                    'national_id', 'address', 'city', 'country', 'phone',
                    'occupation', 'annual_income', 'purpose'
                ]
            ]
        ];
        
        return $requirements[$level] ?? $requirements['basic'];
    }
    
    /**
     * Check if user can upgrade KYC level
     */
    public function canUpgradeLevel(User $user, string $targetLevel): bool
    {
        $currentLevel = $user->kyc_level ?? 'none';
        $levels = ['none' => 0, 'basic' => 1, 'standard' => 2, 'enhanced' => 3];
        
        return $levels[$targetLevel] > $levels[$currentLevel];
    }
}
