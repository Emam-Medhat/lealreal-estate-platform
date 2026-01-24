<?php

namespace App\Notifications;

use App\Models\Commission;
use App\Models\Agent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommissionPaidNotification extends Notification
{
    use Queueable;

    protected $commission;
    protected $agent;

    /**
     * Create a new notification instance.
     */
    public function __construct(Commission $commission, Agent $agent)
    {
        $this->commission = $commission;
        $this->agent = $agent;
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
            'title' => 'دفعة عمولة',
            'message' => "تم دفع عمولة بقيمة {$this->commission->amount} بنجاح. سيتم إضافتها إلى رصيدك.",
            'type' => 'commission_paid',
            'icon' => 'dollar-sign',
            'color' => 'success',
            'data' => [
                'commission_id' => $this->commission->id,
                'commission_amount' => $this->commission->amount,
                'sale_id' => $this->commission->sale->id,
                'property_title' => $this->commission->sale->property->title,
                'client_name' => $this->commission->sale->client->name,
                'payment_date' => $this->commission->paid_at,
                'wallet_balance' => $this->agent->wallet ? $this->agent->wallet->balance : 0,
                'paid_at' => now()
            ]
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('دفعة عمولة')
            ->view('emails.commission-paid', [
                'agent' => $this->agent,
                'commission' => $this->commission
            ]);
    }
}
