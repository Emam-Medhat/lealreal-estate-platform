<?php

namespace App\Notifications;

use App\Models\AgentReview;
use App\Models\Agent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AgentReviewReceivedNotification extends Notification
{
    use Queueable;

    protected $review;
    protected $agent;

    /**
     * Create a new notification instance.
     */
    public function __construct(AgentReview $review, Agent $agent)
    {
        $this->review = $review;
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
            'title' => 'استلام تقييم جديد',
            'message' => "قام {$this->review->reviewer->name} بتقييم أدائك. التقييم: {$this->review->rating}",
            'type' => 'agent_review_received',
            'icon' => 'star',
            'color' => 'info',
            'data' => [
                'review_id' => $this->review->id,
                'rating' => $this->review->rating,
                'review_date' => $this->review->review_date,
                'reviewer_name' => $this->review->reviewer->name,
                'reviewer_comments' => $this->review->comments,
                'performance_score' => $this->review->performance_score,
                'next_steps' => $this->getNextSteps($this->review->rating)
            ]
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('استلام تقييم جديد')
            ->view('emails.agent-review-received', [
                'agent' => $this->agent,
                'review' => $this->review
            ]);
    }

    /**
     * Get next steps based on review rating
     */
    private function getNextSteps(string $rating): array
    {
        $steps = [
            'excellent' => [
                'حافظ على مستواكك الممتاز',
                'focus_on' => 'تطوير المهارات',
                'training_opportunities' => 'برامج تدريب متقدمة'
            ],
            'very_good' => [
                'استمر في تحسين الأداء',
                'focus_on' => 'تطوير المهارات الضعيفة',
                'training_opportunities' => 'برامج تدريب أساسية'
            ],
            'good' => [
                'تحسين الأداء الأساسية',
                'focus_on' => 'تطوير المهارات المعتدلة',
                'training_opportunities' => 'برامج تدريب متوسطة'
            ],
            'average' => [
                'التركز على الأداء الأساسية',
                'focus_on' => 'تطوير المهارات الأساسية',
                'training_opportunities' => 'برامج تدريب متوسطة'
            ],
            'poor' => [
                'تحسين جميع جوانب الأداء',
                'focus_on' => 'تطوير شامل',
                'training_opportunities' => 'برامج تدريب متقدمة',
                'mentorship_program' => 'الانضمام لبرنامج إرشاد'
            ]
        ];

        return $steps[$rating] ?? $steps['average'];
    }
}
