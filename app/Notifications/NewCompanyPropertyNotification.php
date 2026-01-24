<?php

namespace App\Notifications;

use App\Models\Company;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCompanyPropertyNotification extends Notification
{
    use Queueable;

    protected $company;
    protected $property;

    /**
     * Create a new notification instance.
     */
    public function __construct(Company $company, Property $property)
    {
        $this->company = $company;
        $this->property = $property;
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
            'title' => 'عقار جديد للشركة',
            'message' => "تم إضافة عقار جديد {$this->property->title} إلى شركة {$this->company->name}",
            'type' => 'new_property',
            'icon' => 'home',
            'color' => 'info',
            'data' => [
                'company_id' => $this->company->id,
                'company_name' => $this->company->name,
                'property_id' => $this->property->id,
                'property_title' => $this->property->title,
                'property_price' => $this->property->price,
                'property_type' => $this->property->type,
                'property_status' => $this->property->status,
                'property_url' => route('properties.show', $this->property->id),
                'created_at' => $this->property->created_at->toDateTimeString(),
                'assigned_to' => $this->property->assigned_agent_id ? $this->property->assignedAgent->name : null
            ]
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('عقار جديد للشركة')
            ->view('emails.new-company-property', [
                'company' => $this->company,
                'property' => $this->property,
                'propertyUrl' => route('properties.show', $this->property->id)
            ]);
    }
}
