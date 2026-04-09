<?php

namespace App\Services\AI;

use App\Services\AI\Concerns\CallsOpenAI;
use App\Services\AI\Concerns\LogsAiUsage;
use App\Services\AI\Concerns\SanitizesPrompt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Smart Search Service
 *
 * Converts free-form Arabic/English text into structured property search filters.
 * Uses GPT-4o with a strict JSON-only prompt.  Falls back to an empty filter set
 * on any failure so the regular search still runs.
 */
class SmartSearchService
{
    use CallsOpenAI, LogsAiUsage, SanitizesPrompt;

    // ─────────────────────────────────────────────────────────────────────────
    // System prompt (bilingual)
    // ─────────────────────────────────────────────────────────────────────────

    private const SYSTEM_PROMPT = <<<'PROMPT'
You are a real estate search filter extractor that works in Arabic and English.

Your ONLY job is to read the user's free-text property search query and return a
single valid JSON object with these exact keys:

{
  "property_type":     string|null,   // "apartment","villa","office","land","shop","warehouse" or null
  "city":              string|null,
  "district":          string|null,
  "compound":          string|null,
  "bedrooms":          int|null,
  "bathrooms":         int|null,
  "min_price":         float|null,
  "max_price":         float|null,
  "furnished":         bool|null,
  "special_features":  string[]|null, // e.g. ["pool","gym","view"]
  "price_category":    "low"|"medium"|"high"|null,
  "listing_type":      "sale"|"rent"|null
}

Rules:
- Extract ONLY what the user explicitly stated or strongly implied.
- If a value cannot be determined, set it to null.
- Numbers in Arabic (٢ ثلاث خمسة) must be converted to integers.
- "متوسط" or "medium" → price_category = "medium" (do NOT invent numbers).
- "رخيص","cheap","affordable" → price_category = "low".
- "فاخر","luxury","high-end" → price_category = "high".
- "مفروش","furnished" → furnished = true;  "غير مفروش","unfurnished" → false.
- Return ONLY valid JSON. No explanation, no markdown, no code block.
PROMPT;

    // ─────────────────────────────────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Parse free text into a structured filter array ready for the query builder.
     *
     * @param  string  $text  User's natural-language search input
     * @return array{filters: array, raw: array, cached: bool}
     */
    public function parse(string $text): array
    {
        $text = $this->sanitizeInput($text, config('ai.security.max_input_length', 1000));

        $cacheKey = 'ai_search_' . md5($text);

        if ($cached = Cache::get($cacheKey)) {
            return ['filters' => $this->resolveFilters($cached), 'raw' => $cached, 'cached' => true];
        }

        try {
            $raw = $this->callChatCompletion([
                'system' => self::SYSTEM_PROMPT,
                'user' => $text,
                'temperature' => config('ai.smart_search.temperature', 0.0),
                'max_tokens' => config('ai.smart_search.max_tokens', 500),
            ]);

            $parsed = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('AI returned invalid JSON: ' . $raw);
            }

            $this->logUsage('smart_search', $text, $raw);
            $this->persistSearchLog($parsed, false);

            Cache::put($cacheKey, $parsed, config('ai.smart_search.cache_ttl', 900));

            return ['filters' => $this->resolveFilters($parsed), 'raw' => $parsed, 'cached' => false];

        } catch (\Throwable $e) {
            Log::warning('SmartSearchService::parse failed', ['error' => $e->getMessage()]);
            return ['filters' => [], 'raw' => [], 'cached' => false];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Resolve filters (price_category → actual min/max from DB)
    // ─────────────────────────────────────────────────────────────────────────

    private function resolveFilters(array $raw): array
    {
        $filters = array_filter($raw, fn($v) => $v !== null);

        if (isset($filters['price_category']) && !isset($filters['min_price']) && !isset($filters['max_price'])) {
            $range = $this->getPriceRangeByCategory($filters['price_category']);
            unset($filters['price_category']);
            $filters = array_merge($filters, $range);
        }

        return $filters;
    }

    /**
     * Derive price bounds from the 40th / 60th percentile of active listings.
     * Results are cached for 1 hour to avoid repeated heavy DB queries.
     */
    private function getPriceRangeByCategory(string $category): array
    {
        return Cache::remember("price_percentiles_{$category}", 3600, function () use ($category) {
            // MySQL percentile via a subquery (works without window functions)
            $prices = DB::table('property_prices')
                ->whereExists(function ($q) {
                    $q->from('properties')
                        ->whereColumn('properties.id', 'property_prices.property_id')
                        ->where('properties.status', 'active');
                })
                ->orderBy('price')
                ->pluck('price');

            if ($prices->isEmpty()) {
                return [];
            }

            $count = $prices->count();
            $p20 = $prices->get((int) floor($count * 0.20));
            $p40 = $prices->get((int) floor($count * 0.40));
            $p60 = $prices->get((int) floor($count * 0.60));
            $p80 = $prices->get((int) floor($count * 0.80));

            return match ($category) {
                'low' => ['max_price' => $p40],
                'medium' => ['min_price' => $p40, 'max_price' => $p60],
                'high' => ['min_price' => $p80],
                default => [],
            };
        });
    }

    /**
     * Persist the parsed filter fields into ai_search_logs for
     * the RecommendationService behavior engine.
     */
    private function persistSearchLog(array $parsed, bool $cached): void
    {
        try {
            DB::table('ai_search_logs')->insert([
                'user_id' => auth()->id(),
                'listing_type' => $parsed['listing_type'] ?? null,
                'property_type' => $parsed['property_type'] ?? null,
                'city' => $parsed['city'] ?? null,
                'district' => $parsed['district'] ?? null,
                'bedrooms' => isset($parsed['bedrooms']) ? (int) $parsed['bedrooms'] : null,
                'min_price' => isset($parsed['min_price']) ? (float) $parsed['min_price'] : null,
                'max_price' => isset($parsed['max_price']) ? (float) $parsed['max_price'] : null,
                'price_category' => $parsed['price_category'] ?? null,
                'furnished' => isset($parsed['furnished']) ? (bool) $parsed['furnished'] : null,
                'special_features' => isset($parsed['special_features'])
                    ? json_encode($parsed['special_features'])
                    : null,
                'ip_address' => request()->ip(),
                'cached' => $cached,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('SmartSearchService: failed to persist search log', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
