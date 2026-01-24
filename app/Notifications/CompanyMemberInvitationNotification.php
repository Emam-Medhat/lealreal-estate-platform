<?php

namespace App\Notifications;

use App\Models\Company;
use App\Models\CompanyMember;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CompanyMemberInvitationNotification extends Notification
{
    use Queueable;

    protected $company;
    protected $member;
    protected $invitedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Company $company, CompanyMember $member, User $invitedBy)
    {
        $this->company = $company;
        $this->member = $member;
        $this->invitedBy = $invitedBy;
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
            'title' => 'دعوة للانضمام للشركة',
            'message' => "تمت دعوتك للانضمام كعضو في شركة {$this->company->name}",
            'type' => 'company_invitation',
            'icon' => 'mail',
            'color' => 'info',
            'data' => [
                'company_id' => $this->company->id,
                'company_name' => $this->company->name,
                'member_id' => $this->member->id,
                'member_name' => $this->member->user->name,
                'member_email' => $this->member->user->email,
                'invited_by' => $this->invitedBy->name,
                'invited_by_id' => $this->invitedBy->id,
                'invitation_token' => $this->member->invitation_token,
                'role' => $this->member->role,
                'expires_at' => now()->addDays(7)->toDateTimeString()
            ]
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('دعوة للانضمام للشركة')
            ->view('emails.company-member-invitation', [
                'company' => $this->company,
                'member' => $this->member,
                'invitedBy' => $this->invitedBy,
                'invitationUrl' => route('companies.invitations.accept', [
                    'token' => $this->member->invitation_token
                ])
            ]);
    }
}
