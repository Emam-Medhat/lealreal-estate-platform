<?php

namespace App\Notifications;

use App\Models\KycVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class KycApprovedNotification extends Notification
{
    use Queueable;

    protected $kycVerification;

    /**
     * Create a new notification instance.
     */
    public function __construct(KycVerification $kycVerification)
    {
        $this->kycVerification = $kycVerification;
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
            'title' => '✅ تم قبول التحقق من الهوية',
            'message' => 'تهانينا! تم قبول مستندات التحقق من الهوية الخاصة بك. يمكنك الآن الاستفادة من جميع ميزات المنصة.',
            'type' => 'kyc_approved',
            'icon' => 'check-shield',
            'color' => 'success',
            'data' => [
                'kyc_id' => $this->kycVerification->id,
                'kyc_level' => $this->kycVerification->level,
                'approved_at' => $this->kycVerification->approved_at,
                'new_features' => $this->getUnlockedFeatures($this->kycVerification->level)
            ]
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('✅ تم قبول التحقق من الهوية')
            ->view('emails.kyc-approved', [
                'user' => $notifiable,
                'kycVerification' => $this->kycVerification,
                'new_features' => $this->getUnlockedFeatures($this->kycVerification->level)
            ]);
    }

    /**
     * Get unlocked features based on KYC level
     */
    private function getUnlockedFeatures(string $level): array
    {
        $features = [
            'basic' => [
                'property_listings' => 'عرض العقارات',
                'basic_search' => 'بحث أساسي',
                'profile_viewing' => 'عرض الملفات الشخصية'
            ],
            'standard' => [
                'advanced_search' => 'بحث متقدم',
                'property_comparisons' => 'مقارنة العقارات',
                'contact_agents' => 'التواصل مع الوكلاء',
                'saved_searches' => 'حفظ البحوث'
            ],
            'enhanced' => [
                'property_investment_analysis' => 'تحليل الاستثمار العقاري',
                'market_insights' => 'رؤى السوق',
                'priority_support' => 'دعم أولوي',
                'exclusive_listings' => 'عقارات حصرية',
                'investment_tools' => 'أدوات الاستثمار'
            ]
        ];

        return $features[$level] ?? $features['basic'];
    }
}
