<?php

namespace App\Listeners;

use App\Events\KycVerificationSubmitted;
use App\Notifications\KycSubmittedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;

class SendKycVerificationEmail
{
    /**
     * Handle the event.
     */
    public function handle(KycVerificationSubmitted $event): void
    {
        $user = $event->user;
        $kycVerification = $event->kycVerification;
        
        // Send notification to user
        Notification::send($user, new KycSubmittedNotification($kycVerification));
        
        // Send email confirmation
        try {
            Mail::to($user->email)->send(new \App\Mail\KycSubmittedMail($user, $kycVerification));
        } catch (\Exception $e) {
            \Log::error('Failed to send KYC submission email', [
                'user_id' => $user->id,
                'kyc_id' => $kycVerification->id,
                'error' => $e->getMessage()
            ]);
        }
        
        // Notify KYC reviewers/admins
        $this->notifyKycReviewers($user, $kycVerification);
        
        // Create internal notification for admins
        $this->createAdminNotification($user, $kycVerification);
    }
    
    /**
     * Notify KYC reviewers
     */
    private function notifyKycReviewers($user, $kycVerification): void
    {
        $reviewers = \App\Models\User::where('role', 'kyc_reviewer')
            ->orWhere('role', 'admin')
            ->get();
            
        foreach ($reviewers as $reviewer) {
            $reviewer->notifications()->create([
                'title' => 'طلب تحقق جديد',
                'message' => "قام المستخدم {$user->name} بتقديم طلب تحقق من الهوية",
                'type' => 'kyc_submission',
                'data' => [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'kyc_id' => $kycVerification->id,
                    'submitted_at' => $kycVerification->created_at
                ]
            ]);
        }
    }
    
    /**
     * Create admin notification
     */
    private function createAdminNotification($user, $kycVerification): void
    {
        \App\Models\Notification::create([
            'title' => 'طلب KYC جديد',
            'message' => "تم تقديم طلب تحقق من الهوية جديد من قبل {$user->name}",
            'type' => 'kyc_admin',
            'is_global' => true,
            'data' => [
                'user_id' => $user->id,
                'kyc_id' => $kycVerification->id,
                'priority' => 'medium'
            ]
        ]);
    }
}
