<?php

namespace App\Services\AI\Concerns;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait CallsOpenAI
{
    /**
     * Call Chat Completions (auto-detects provider: OpenAI or Google Gemini).
     */
    private function callChatCompletion(array $params): string
    {
        $provider = config('ai.active_provider', 'openai');

        return match ($provider) {
            'google' => $this->callGemini($params),
            default => $this->callOpenAI($params),
        };
    }

    // ─── OpenAI ──────────────────────────────────────────────────────────────

    private function callOpenAI(array $params): string
    {
        $cfg = config('ai.providers.openai');
        $apiKey = $cfg['api_key'] ?? null;

        if (!$apiKey) {
            throw new \RuntimeException('OpenAI API key not configured (OPENAI_API_KEY).');
        }

        $messages = $params['messages'] ?? [
            ['role' => 'system', 'content' => $params['system'] ?? ''],
            ['role' => 'user', 'content' => $params['user'] ?? ''],
        ];

        $body = [
            'model' => $cfg['models']['chat'] ?? 'gpt-4o',
            'messages' => $messages,
            'temperature' => $params['temperature'] ?? 0.7,
            'max_tokens' => $params['max_tokens'] ?? 1000,
        ];

        $response = Http::withToken($apiKey)
            ->timeout($cfg['timeout'] ?? 60)
            ->retry($cfg['max_retries'] ?? 3, 1500)
            ->post($cfg['base_url'] . '/chat/completions', $body);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'OpenAI API error ' . $response->status() . ': ' . $response->body()
            );
        }

        return trim($response->json('choices.0.message.content', ''));
    }

    // ─── Google Gemini (FREE TIER) ───────────────────────────────────────────

    private function callGemini(array $params): string
    {
        $cfg = config('ai.providers.google');
        $apiKey = $cfg['api_key'] ?? null;

        if (!$apiKey) {
            throw new \RuntimeException('Google AI API key not configured (GOOGLE_AI_API_KEY).');
        }

        $model = $cfg['models']['chat'] ?? 'gemini-2.0-flash';
        $baseUrl = $cfg['base_url'] ?? 'https://generativelanguage.googleapis.com/v1beta';
        $url = "{$baseUrl}/models/{$model}:generateContent?key={$apiKey}";

        // Convert OpenAI-style messages → Gemini format
        $messages = $params['messages'] ?? [
            ['role' => 'system', 'content' => $params['system'] ?? ''],
            ['role' => 'user', 'content' => $params['user'] ?? ''],
        ];

        $systemInstruction = null;
        $contents = [];

        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                $systemInstruction = $msg['content'];
            } else {
                $role = $msg['role'] === 'assistant' ? 'model' : 'user';
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $msg['content']]],
                ];
            }
        }

        $body = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $params['temperature'] ?? 0.7,
                'maxOutputTokens' => $params['max_tokens'] ?? 1000,
            ],
        ];

        if ($systemInstruction) {
            $body['system_instruction'] = [
                'parts' => [['text' => $systemInstruction]],
            ];
        }

        Log::debug('Gemini request', ['url' => preg_replace('/key=[^&]+/', 'key=***', $url)]);

        $response = Http::timeout($cfg['timeout'] ?? 60)
            ->retry(2, 1000)
            ->post($url, $body);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'Gemini API error ' . $response->status() . ': ' . $response->body()
            );
        }

        $text = $response->json('candidates.0.content.parts.0.text', '');

        if (empty($text)) {
            Log::warning('Gemini returned empty response', ['body' => $response->json()]);
            throw new \RuntimeException('Gemini returned empty response.');
        }

        return trim($text);
    }

    // ─── Embeddings (OpenAI only) ────────────────────────────────────────────

    private function callEmbedding(string $text): array
    {
        $cfg = config('ai.providers.openai');
        $apiKey = $cfg['api_key'] ?? null;

        if (!$apiKey) {
            throw new \RuntimeException('OpenAI API key not configured.');
        }

        $response = Http::withToken($apiKey)
            ->timeout($cfg['timeout'] ?? 60)
            ->post($cfg['base_url'] . '/embeddings', [
                'model' => $cfg['models']['embedding'] ?? 'text-embedding-3-small',
                'input' => $text,
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('OpenAI Embedding error: ' . $response->body());
        }

        return $response->json('data.0.embedding', []);
    }

    // ─── Vision (supports both providers) ────────────────────────────────────

    private function callVision(string $imageUrl, string $prompt): string
    {
        $provider = config('ai.active_provider', 'openai');

        if ($provider === 'google') {
            return $this->callGemini([
                'messages' => [
                    ['role' => 'user', 'content' => $prompt . "\n\n[Image URL: {$imageUrl}]"],
                ],
                'max_tokens' => 500,
            ]);
        }

        $cfg = config('ai.providers.openai');
        $apiKey = $cfg['api_key'] ?? null;

        if (!$apiKey) {
            throw new \RuntimeException('OpenAI API key not configured.');
        }

        $response = Http::withToken($apiKey)
            ->timeout($cfg['timeout'] ?? 60)
            ->post($cfg['base_url'] . '/chat/completions', [
                'model' => $cfg['models']['vision'] ?? 'gpt-4o',
                'max_tokens' => 500,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => $prompt],
                            ['type' => 'image_url', 'image_url' => ['url' => $imageUrl]],
                        ],
                    ]
                ],
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('OpenAI Vision error: ' . $response->body());
        }

        return trim($response->json('choices.0.message.content', ''));
    }
}
