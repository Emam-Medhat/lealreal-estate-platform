<?php

namespace App\Listeners;

use App\Events\ProfileUpdated;
use App\Notifications\ProfileCompletedNotification;
use Illuminate\Support\Facades\Notification;

class NotifyProfileCompletion
{
    /**
     * Handle the event.
     */
    public function handle(ProfileUpdated $event): void
    {
        $user = $event->user;
        
        // Check if profile reached completion threshold
        if ($user->profile_completion_percentage >= 100) {
            // Send notification for complete profile
            Notification::send($user, new ProfileCompletedNotification());
            
            // Award achievement or points if you have a gamification system
            $this->awardProfileCompletionAchievement($user);
        } elseif ($user->profile_completion_percentage >= 80) {
            // Send notification for significant completion
            $this->sendProfileImprovementNotification($user, 'high');
        } elseif ($user->profile_completion_percentage >= 50) {
            // Send notification for moderate completion
            $this->sendProfileImprovementNotification($user, 'moderate');
        }
    }
    
    /**
     * Send profile improvement notification
     */
    private function sendProfileImprovementNotification($user, string $level): void
    {
        $messages = [
            'high' => 'أحسنت! ملفك الشخصي مكتمل بنسبة ' . $user->profile_completion_percentage . '%',
            'moderate' => 'ملفك الشخصي مكتمل بنسبة ' . $user->profile_completion_percentage . '%. استمر في إكمال البيانات'
        ];
        
        $message = $messages[$level] ?? 'تم تحديث ملفك الشخصي';
        
        // Create notification (you might want to create a specific notification class)
        $user->notifications()->create([
            'title' => 'تحديث الملف الشخصي',
            'message' => $message,
            'type' => 'profile_update',
            'data' => [
                'completion_percentage' => $user->profile_completion_percentage,
                'level' => $level
            ]
        ]);
    }
    
    /**
     * Award profile completion achievement
     */
    private function awardProfileCompletionAchievement($user): void
    {
        // Implementation depends on your achievement system
        try {
            // Example: Create achievement record
            // Achievement::create([
            //     'user_id' => $user->id,
            //     'type' => 'profile_completion',
            //     'title' => 'ملف شخصي مكتمل',
            //     'description' => 'لقد أكملت ملفك الشخصي بنسبة 100%',
            //     'points' => 50,
            //     'earned_at' => now()
            // ]);
            
            // Add points to user if you have a points system
            // $user->increment('points', 50);
        } catch (\Exception $e) {
            // Log error but don't fail the main process
            \Log::error('Failed to award profile completion achievement', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
