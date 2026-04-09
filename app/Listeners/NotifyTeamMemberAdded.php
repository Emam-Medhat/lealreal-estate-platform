<?php

namespace App\Listeners;

use App\Events\CompanyMemberAdded;
use App\Models\User;
use App\Models\CompanyMember;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotifyTeamMemberAdded
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
            // Get existing team members
            $teamMembers = $company->members()
                ->where('company_id', $company->id)
                ->where('status', 'active')
                ->get();

            // Notify team members about new member
            foreach ($teamMembers as $teamMember) {
                if ($teamMember->id !== $member->id) {
                    $teamMember->notifications()->create([
                        'title' => 'عضو جديد في الفريق',
                        'message' => "انضم {$member->user->name} كعضو جديد في فريق شركة {$company->name}",
                        'type' => 'team_member_added',
                        'data' => [
                            'member_id' => $member->id,
                            'member_name' => $member->user->name,
                            'member_role' => $member->role,
                            'added_by' => $user->id,
                            'company_id' => $company->id
                        ]
                    ]);
                }
            }

            // Send notification to new member
            $member->user->notifications()->create([
                'title' => 'مرحباً بك في فريق شركة {$company->name}',
                'message' => "تم إضافتك كعضو في فريق شركة {$company->name}. نرحب بك لتحقيق أهداف الشركة معاً",
                'type' => 'team_welcome',
                'data' => [
                    'company_id' => $company->id,
                    'company_name' => $company->name,
                    'team_member_count' => $teamMembers->count(),
                    'your_role' => $member->role
                ]
            ]);

            // Log the event
            Log::info('Team member added and team notified', [
                'company_id' => $company->id,
                'member_id' => $member->id,
                'user_id' => $user->id,
                'team_member_count' => $teamMembers->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to notify team about new member', [
                'company_id' => $company->id,
                'member_id' => $member->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
