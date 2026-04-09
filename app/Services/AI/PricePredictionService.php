<?php

namespace App\Services\AI;

use App\Services\AI\Concerns\CallsOpenAI;
use App\Services\AI\Concerns\LogsAiUsage;
use App\Services\AI\Concerns\SanitizesPrompt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Price Prediction Service
 *
 * Primary path:  calls the Python/FastAPI microservice (scikit-learn model).
 * Fallback path: calls OpenAI GPT-4o as a quantitative estimator.
 *
 * Returns: predicted_price_range, confidence_score, investment_score, risk_level.
 */
class PricePredictionService
{
    use CallsOpenAI, LogsAiUsage, SanitizesPrompt;

    private const SYSTEM_PROMPT = <<<'PROMPT'
You are a quantitative real estate valuation assistant.

Given a property's attributes, return ONLY a valid JSON object with these fields:

{
  "predicted_price_min": float,
  "predicted_price_max": float,
  "predicted_price_mid": float,
  "confidence_score":    float,   // 0.0–1.0
  "investment_score":    int,     // 1–10
  "risk_level":          "low"|"medium"|"high",
  "valuation_rationale": string   // 2–3 sentence justification
}

Rules:
- Base estimates on area (m²), location tier, bedrooms, finishing, building_age, floor.
- confidence_score: high (>0.75) only when all inputs are specific.
- investment_score: 10 = excellent ROI, 1 = poor.
- Return ONLY JSON. No markdown, no explanation, no code blocks.
PROMPT;

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Predict price for a property spec.
     *
     * @param  array{
     *   area: float,
     *   city: string,
     *   district?: string,
     *   bedrooms: int,
     *   floor?: int,
     *   finishing?: string,
     *   building_age?: int,
     *   listing_type?: string
     * }  $input
     */
    public function predict(array $input): array
    {
        $cacheKey = 'price_pred_' . md5(json_encode($input));

        if ($cached = Cache::get($cacheKey)) {
            return array_merge($cached, ['cached' => true]);
        }

        // Try microservice first
        $result = $this->callMicroservice($input);

        // Fallback to AI
        if (!$result['success']) {
            $result = $this->callAiFallback($input);
        }

        if ($result['success']) {
            Cache::put($cacheKey, $result, 3600);
        }

        return $result;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Python microservice integration
    // ─────────────────────────────────────────────────────────────────────────

    private function callMicroservice(array $input): array
    {
        $cfg = config('ai.price_prediction');
        $url = $cfg['url'] ?? null;

        if (!$url) {
            return ['success' => false, 'message' => 'Microservice URL not configured'];
        }

        try {
            $response = Http::withToken($cfg['api_key'] ?? '')
                ->timeout($cfg['timeout'] ?? 30)
                ->post("{$url}/predict", $this->normalizeInput($input));

            if (!$response->successful()) {
                throw new \RuntimeException('Microservice HTTP error: ' . $response->status());
            }

            $data = $response->json();

            return [
                'success' => true,
                'source' => 'microservice',
                'predicted_price_min' => $data['predicted_price_min'],
                'predicted_price_max' => $data['predicted_price_max'],
                'predicted_price_mid' => $data['predicted_price_mid'],
                'confidence_score' => $data['confidence_score'],
                'investment_score' => $data['investment_score'],
                'risk_level' => $data['risk_level'],
                'valuation_rationale' => $data['valuation_rationale'] ?? null,
                'cached' => false,
            ];
        } catch (\Throwable $e) {
            Log::warning('PricePrediction microservice failed', ['err' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AI fallback
    // ─────────────────────────────────────────────────────────────────────────

    private function callAiFallback(array $input): array
    {
        try {
            $userMsg = "Property input:\n" . json_encode($this->normalizeInput($input), JSON_PRETTY_PRINT);

            $raw = $this->callChatCompletion([
                'system' => self::SYSTEM_PROMPT,
                'user' => $userMsg,
                'temperature' => 0.1,
                'max_tokens' => 400,
            ]);

            $data = json_decode($raw, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('AI returned invalid JSON: ' . $raw);
            }

            $this->logUsage('price_prediction', $userMsg, $raw);

            return array_merge($data, [
                'success' => true,
                'source' => 'ai_fallback',
                'cached' => false,
            ]);
        } catch (\Throwable $e) {
            Log::error('PricePrediction AI fallback failed', ['err' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function normalizeInput(array $input): array
    {
        return [
            'area' => (float) ($input['area'] ?? 0),
            'city' => $input['city'] ?? '',
            'district' => $input['district'] ?? null,
            'bedrooms' => (int) ($input['bedrooms'] ?? 0),
            'floor' => (int) ($input['floor'] ?? 0),
            'finishing' => $input['finishing'] ?? 'standard',
            'building_age' => (int) ($input['building_age'] ?? 0),
            'listing_type' => $input['listing_type'] ?? 'sale',
        ];
    }
}
