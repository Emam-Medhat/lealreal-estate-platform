<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewCompanyNotification extends Notification implements ShouldQueue
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
            'title' => 'شركة جديدة انضمت',
            'message' => 'تم تسجيل شركة جديدة باسم: ' . $this->company->name,
            'url' => route('admin.companies.show', $this->company->id),
            'icon' => 'fa-briefcase',
            'color' => '#9b59b6', // Purple color for companies
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'title' => 'شركة جديدة انضمت',
            'message' => 'تم تسجيل شركة جديدة باسم: ' . $this->company->name,
            'url' => route('admin.companies.show', $this->company->id),
            'icon' => 'fa-briefcase',
            'color' => '#9b59b6',
            'time' => now()->diffForHumans(),
        ]);
    }
}
