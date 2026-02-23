<?php

namespace App\Services\AI\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait LogsAiUsage
{
    /**
     * Persist an AI call record for cost tracking and auditing.
     */
    private function logUsage(
        string $feature,
        string $input,
        string $output,
        array $meta = []
    ): void {
        try {
            // Estimate tokens (rough: 4 chars ≈ 1 token)
            $inputTokens = (int) ceil(mb_strlen($input) / 4);
            $outputTokens = (int) ceil(mb_strlen($output) / 4);

            $model = $meta['model'] ?? config('ai.providers.openai.models.chat', 'gpt-4o');
            $costs = config(
                "ai.cost_tracking.token_costs.{$model}",
                ['input' => 0.005, 'output' => 0.015]
            );

            $costUsd = (($inputTokens / 1000) * $costs['input'])
                + (($outputTokens / 1000) * $costs['output']);

            DB::table('ai_usage_logs')->insert([
                'feature' => $feature,
                'model' => $model,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'cost_usd' => round($costUsd, 6),
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'meta' => json_encode($meta),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to log AI usage', ['error' => $e->getMessage()]);
        }
    }
}
