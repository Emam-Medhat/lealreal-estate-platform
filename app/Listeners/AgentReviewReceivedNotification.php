<?php

namespace App\Listeners;

use App\Events\AgentReviewReceived;
use App\Models\Agent;
use App\Models\AgentReview;
use Illuminate\Support\Facades\Notification;

class AgentReviewReceivedNotification
{
    /**
     * Handle the event.
     */
    public function handle(AgentReviewReceived $event): void
    {
        $review = $event->review;
        $agent = $event->agent;
        $reviewedBy = $event->reviewedBy;

        try {
            // Create notification for agent
            $agent->notifications()->create([
                'title' => 'استلام تقييم جديد',
                'message' => "قام {$reviewedBy->name} بتقييم أدائك. التقييم: {$review->rating}",
                'type' => 'agent_review_received',
                'data' => [
                    'review_id' => $review->id,
                    'rating' => $review->rating,
                    'review_date' => $review->review_date,
                    'reviewer_name' => $reviewedBy->name,
                    'reviewer_comments' => $review->comments,
                    'performance_score' => $review->performance_score,
                    'next_steps' => $this->getNextSteps($review->rating)
                ]
            ]);

            // Send notification to reviewer if different from agent
            if ($reviewedBy->id !== $agent->id) {
                $reviewedBy->notifications()->create([
                    'title' => 'تقييم تم إرساله',
                    'message' => "تم إرسال تقييم الوكيل {$agent->name} إلى {$reviewedBy->name}",
                    'type' => 'review_sent',
                    'data' => [
                        'review_id' => $review->id,
                        'agent_id' => $agent->id,
                        'reviewer_id' => $reviewedBy->id,
                        'reviewed_agent_name' => $agent->name
                    ]
                ]);
            }

            Log::info('Agent review notification sent', [
                'agent_id' => $agent->id,
                'review_id' => $review->id,
                'reviewed_by_id' => $reviewedBy->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send agent review notification', [
                'agent_id' => $agent->id,
                'review_id' => $review->id,
                'reviewed_by_id' => $reviewedBy->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
