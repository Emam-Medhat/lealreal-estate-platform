<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewCompanyRegisteredNotification extends Notification
{
    use Queueable;

    public $company;

    /**
     * Create a new notification instance.
     */
    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'company_id' => $this->company->id,
            'company_name' => $this->company->name,
            'owner_name' => $this->company->owner ? ($this->company->owner->full_name ?? $this->company->owner->name) : 'N/A',
            'created_at' => now(),
            'title' => 'شركة جديدة',
            'message' => 'شركة جديدة سجلت في المنصة: ' . $this->company->name,
            'url' => route('admin.companies.show', $this->company->id),
            'icon' => 'building',
            'color' => 'blue'
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'company_id' => $this->company->id,
            'title' => 'شركة جديدة',
            'message' => 'شركة جديدة سجلت في المنصة: ' . $this->company->name,
            'url' => route('admin.companies.show', $this->company->id),
            'icon' => 'building',
            'color' => 'blue',
            'time' => now()->diffForHumans(),
        ]);
    }
}
