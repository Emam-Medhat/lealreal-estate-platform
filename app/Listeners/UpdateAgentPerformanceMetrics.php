<?php

namespace App\Listeners;

use App\Events\AgentReviewReceived;
use App\Models\Agent;
use App\Models\AgentReview;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UpdateAgentPerformanceMetrics
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
            // Update agent's performance metrics
            $this->updatePerformanceMetrics($agent, $review);

            // Create notification for performance update
            $agent->notifications()->create([
                'title' => 'تحديث مؤشر الأداء',
                'message' => "تم تحديث مؤشراتك بناءً على التقييم الأخير.",
                'type' => 'performance_updated',
                'data' => [
                    'review_id' => $review->id,
                    'new_score' => $review->performance_score,
                    'previous_score' => $agent->monthly_performance_score,
                    'performance_trend' => $this->calculatePerformanceTrend($agent->monthly_performance_score, $review->performance_score)
                ]
            ]);

            Log::info('Agent performance metrics updated', [
                'agent_id' => $agent->id,
                'review_id' => $review->id,
                'new_score' => $review->performance_score,
                'previous_score' => $agent->monthly_performance_score
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update agent performance metrics', [
                'agent_id' => $agent->id,
                'review_id' => $review->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update agent's performance metrics
     */
    private function updatePerformanceMetrics(Agent $agent, AgentReview $review): void
    {
        // Update monthly performance score
        $currentScore = $agent->monthly_performance_score ?? 0;
        $newScore = $this->calculatePerformanceScore($review);

        $agent->update([
            'monthly_performance_score' => $newScore,
            'last_review_date' => $review->review_date,
            'performance_trend' => $this->calculatePerformanceTrend($currentScore, $newScore)
        ]);

        // Update ranking if needed
        $this->updateAgentRanking($agent);
    }

    /**
     * Calculate performance score based on review
     */
    private function calculatePerformanceScore(AgentReview $review): int
    {
        $score = 0;

        // Rating contribution (40%)
        $ratingScores = [
            'excellent' => 40,
            'very_good' => 35,
            'good' => 30,
            'average' => 20,
            'poor' => 10,
            'needs_improvement' => 5
        ];

        $score += $ratingScores[$review->rating] ?? 0;

        // Communication skills (20%)
        $communicationScores = [
            'excellent' => 20,
            'very_good' => 17,
            'good' => 15,
            'average' => 10,
            'poor' => 5,
            'needs_improvement' => 2
        ];

        $score += $communicationScores[$review->communication] ?? 0;

        // Professionalism (20%)
        $professionalismScores = [
            'excellent' => 20,
            'very_good' => 17,
            'good' => 15,
            'average' => 10,
            'poor' => 5,
            'needs_improvement' => 2
        ];

        $score += $professionalismScores[$review->professionalism] ?? 0;

        // Productivity (20%)
        $productivityScores = [
            'excellent' => 20,
            'very_good' => 17,
            'good' => 15,
            'average' => 10,
            'poor' => 5,
            'needs_improvement' => 2
        ];

        $score += $productivityScores[$review->productivity] ?? 0;

        return $score;
    }

    /**
     * Calculate performance trend
     */
    private function calculatePerformanceTrend(int $currentScore, int $newScore): string
    {
        if ($newScore > $currentScore) {
            return 'improving';
        } elseif ($newScore < $currentScore) {
            return 'declining';
        } else {
            return 'stable';
        }
    }

    /**
     * Update agent ranking
     */
    private function updateAgentRanking(Agent $agent): void
    {
        // This would update agent ranking in the company
        // Placeholder implementation
        
        Log::info('Agent ranking updated', [
            'agent_id' => $agent->id,
            'performance_score' => $agent->monthly_performance_score
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
