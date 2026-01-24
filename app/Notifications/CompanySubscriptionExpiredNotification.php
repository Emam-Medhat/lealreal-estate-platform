<?php

namespace App\Notifications;

use App\Models\Company;
use App\Models\CompanySubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CompanySubscriptionExpiredNotification extends Notification
{
    use Queueable;

    protected $company;
    protected $subscription;

    /**
     * Create a new notification instance.
     */
    public function __construct(Company $company, CompanySubscription $subscription)
    {
        $this->company = $company;
        $this->subscription = $subscription;
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
            'title' => 'انتهاء صلاحية الاشتراك',
            'message' => "انتهت صلاحية اشتراك شركة {$this->company->name} في {$this->subscription->expires_at->toDateString()}",
            'type' => 'subscription_expired',
            'icon' => 'alert-triangle',
            'color' => 'warning',
            'data' => [
                'company_id' => $this->company->id,
                'company_name' => $this->company->name,
                'subscription_id' => $this->subscription->id,
                'subscription_plan' => $this->subscription->plan->name,
                'expired_at' => $this->subscription->expires_at->toDateString(),
                'grace_period_ends' => $this->subscription->expires_at->addDays(7)->toDateString(),
                'renewal_url' => route('companies.subscription.renew'),
                'upgrade_url' => route('companies.subscription.upgrade')
            ]
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('انتهاء صلاحية الاشتراك')
            ->view('emails.company-subscription-expired', [
                'company' => $this->company,
                'subscription' => $this->subscription,
                'renewalUrl' => route('companies.subscription.renew'),
                'upgradeUrl' => route('companies.subscription.upgrade')
            ]);
    }
}
