<?php

namespace App\Listeners;

use App\Events\CompanyMemberAdded;
use App\Models\User;
use App\Models\CompanyMember;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class NotifyCompanyOwner
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMemberAdded $event): void
    {
        $company = $event->company;
        $member = $event->member;
        $user = $event->user;

        try {
            // Get company owner
            $owner = $company->owner;

            if ($owner) {
                // Notify company owner about new member
                $owner->notifications()->create([
                    'title' => 'عضو جديد في الشركة',
                    'message' => "تم إضافة {$member->role} جديد {$member->user->name} إلى شركة {$company->name}",
                    'type' => 'member_added',
                    'data' => [
                        'member_id' => $member->id,
                        'member_name' => $member->user->name,
                        'member_role' => $member->role,
                        'company_id' => $company->id,
                        'added_by' => $user->id
                    ]
                ]);

                // Send email notification to owner
                Mail::to($owner->email)->send(new \App\Mail\CompanyMemberAddedMail($company, $member, $user));
            }

            // Log the event
            Log::info('Company owner notified about new member', [
                'company_id' => $company->id,
                'member_id' => $member->id,
                'owner_id' => $owner->id,
                'added_by' => $user->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to notify company owner about new member', [
                'company_id' => $company->id,
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
