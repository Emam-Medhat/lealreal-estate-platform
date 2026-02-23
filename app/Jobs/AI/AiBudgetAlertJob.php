<?php

namespace App\Jobs\AI;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * AI Budget Alert Job
 *
 * Dispatched hourly alongside RecalculatePropertyScoresJob.
 * Checks month-to-date AI spend against the configured budget.
 *
 * Sends an admin notification at ALERT_THRESHOLD (default 80%) and
 * a CRITICAL notification at 95%.
 *
 * Uses a cache flag so alerts fire at most once per level per day.
 */
class AiBudgetAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 60;

    public function handle(): void
    {
        $budget = (float) config('ai.cost_tracking.monthly_budget_usd', 500);
        $warnAt = (float) config('ai.cost_tracking.alert_threshold', 0.80);
        $criticalAt = 0.95;

        $spent = (float) DB::table('ai_usage_logs')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('cost_usd');

        $ratio = $budget > 0 ? ($spent / $budget) : 0;

        if ($ratio >= $criticalAt) {
            $this->fireAlert('critical', $spent, $budget, $ratio);
        } elseif ($ratio >= $warnAt) {
            $this->fireAlert('warning', $spent, $budget, $ratio);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function fireAlert(string $level, float $spent, float $budget, float $ratio): void
    {
        // Throttle: fire at most once per level per calendar day
        $cacheKey = "ai_budget_alert_{$level}_" . now()->format('Y_m_d');

        if (Cache::has($cacheKey)) {
            return; // already alerted today
        }

        Cache::put($cacheKey, true, now()->endOfDay());

        $percent = round($ratio * 100, 1);

        $adminEmail = config('mail.admin_address', env('MAIL_FROM_ADDRESS'));
        $subject = ($level === 'critical')
            ? "[CRITICAL] AI Budget {$percent}% consumed this month"
            : "[WARNING] AI Budget {$percent}% consumed this month";

        $body = "
        Monthly AI Budget Alert
        ========================
        Level:    {$level}
        Spent:    \${$spent} USD
        Budget:   \${$budget} USD
        Usage:    {$percent}%

        Month:    " . now()->format('F Y') . "
        ";

        Log::warning("AiBudgetAlertJob: [{$level}] {$percent}% of AI budget consumed", [
            'spent' => $spent,
            'budget' => $budget,
        ]);

        // Log to DB for audit trail
        DB::table('ai_usage_logs')->insert([
            'feature' => 'budget_alert',
            'model' => null,
            'input_tokens' => 0,
            'output_tokens' => 0,
            'cost_usd' => 0,
            'user_id' => null,
            'ip_address' => null,
            'meta' => json_encode([
                'level' => $level,
                'spent' => $spent,
                'budget' => $budget,
                'percent' => $percent,
            ]),
            'created_at' => now(),
        ]);

        // Send email (if mail is configured)
        if ($adminEmail) {
            try {
                \Illuminate\Support\Facades\Mail::raw($body, function ($m) use ($adminEmail, $subject) {
                    $m->to($adminEmail)->subject($subject);
                });
            } catch (\Throwable $e) {
                Log::error('AiBudgetAlertJob: failed to send email', ['err' => $e->getMessage()]);
            }
        }
    }
}
