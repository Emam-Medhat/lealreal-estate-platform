<?php

namespace App\Notifications;

use App\Models\KycVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class KycRejectedNotification extends Notification
{
    use Queueable;

    protected $kycVerification;
    protected $rejectionReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(KycVerification $kycVerification, string $rejectionReason = null)
    {
        $this->kycVerification = $kycVerification;
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => '❌ تم رفض التحقق من الهوية',
            'message' => $this->getRejectionMessage(),
            'type' => 'kyc_rejected',
            'icon' => 'x-circle',
            'color' => 'danger',
            'data' => [
                'kyc_id' => $this->kycVerification->id,
                'rejection_reason' => $this->rejectionReason,
                'rejected_at' => $this->kycVerification->rejected_at,
                'next_steps' => $this->getNextSteps()
            ]
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('❌ تم رفض التحقق من الهوية')
            ->view('emails.kyc-rejected', [
                'user' => $notifiable,
                'kycVerification' => $this->kycVerification,
                'rejectionReason' => $this->rejectionReason,
                'nextSteps' => $this->getNextSteps()
            ]);
    }

    /**
     * Get rejection message
     */
    private function getRejectionMessage(): string
    {
        if ($this->rejectionReason) {
            return "تم رفض مستندات التحقق من الهوية. السبب: {$this->rejectionReason}";
        }
        
        return 'تم رفض مستندات التحقق من الهوية. يرجى مراجعة المستندات وتقديمها مرة أخرى.';
    }

    /**
     * Get next steps after rejection
     */
    private function getNextSteps(): array
    {
        return [
            'review_documents' => 'مراجعة جميع المستندات المقدمة',
            'fix_issues' => 'تصحيح المشاكل المذكورة في سبب الرفض',
            'resubmit' => 'إعادة تقديم الطلب',
            'contact_support' => 'التواصل مع فريق الدعم إذا احتجت مساعدة',
            'check_requirements' => 'مراجعة متطلبات التحقق من الهوية'
        ];
    }
}
