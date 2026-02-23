<?php

namespace App\Services\AI;

use App\Services\AI\Concerns\CallsOpenAI;
use App\Services\AI\Concerns\LogsAiUsage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Image Analysis Service
 *
 * Two analysis modes:
 *  1. Google Vision API  – label detection, safe-search, web detection
 *  2. GPT-4o Vision      – semantic room analysis, quality scoring
 *
 * Output:
 *  - room_type          (living_room, bedroom, kitchen, bathroom, exterior, other)
 *  - furniture_quality  (none, basic, standard, luxury)
 *  - brightness_level   (dark, moderate, bright)
 *  - quality_score      0–100
 *  - issues             array  (e.g. "blurry", "dark", "cluttered")
 */
class ImageAnalysisService
{
    use CallsOpenAI, LogsAiUsage;

    private const VISION_PROMPT = <<<'PROMPT'
Analyse this real estate property photograph and return ONLY a valid JSON object:

{
  "room_type":         "living_room"|"bedroom"|"kitchen"|"bathroom"|"exterior"|"garden"|"pool"|"other",
  "furniture_quality": "none"|"basic"|"standard"|"luxury",
  "brightness_level":  "dark"|"moderate"|"bright",
  "quality_score":     integer 0-100,
  "issues":            string[]   // e.g. ["blurry","dark","cluttered","watermark"]
}

Scoring guide:
  100 – Professional HDR photo, perfect lighting, staged, no issues
   80 – Good photo, minor issues
   60 – Acceptable, some problems
   40 – Poor quality, multiple problems
  <40 – Unusable photo

Return ONLY JSON. No explanation.
PROMPT;

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Analyse a single image.
     *
     * @param  string  $imagePath   Filesystem path or public URL
     * @return array{
     *   success: bool,
     *   room_type: string,
     *   furniture_quality: string,
     *   brightness_level: string,
     *   quality_score: int,
     *   issues: string[],
     *   source: string
     * }
     */
    public function analyze(string $imagePath): array
    {
        $provider = config('ai.image_analysis.provider', 'google');

        return match ($provider) {
            'google' => $this->analyzeWithGoogle($imagePath),
            'azure' => $this->analyzeWithAzure($imagePath),
            default => $this->analyzeWithVision($imagePath),
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GPT-4o Vision (primary fallback + semantic analysis)
    // ─────────────────────────────────────────────────────────────────────────

    private function analyzeWithVision(string $imagePath): array
    {
        try {
            $imageUrl = $this->resolveUrl($imagePath);

            $raw = $this->callVision($imageUrl, self::VISION_PROMPT);

            $data = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Vision returned invalid JSON: ' . $raw);
            }

            $this->logUsage('image_analysis', $imagePath, $raw);

            return array_merge($data, ['success' => true, 'source' => 'openai_vision']);

        } catch (\Throwable $e) {
            Log::error('ImageAnalysis (Vision) failed', ['error' => $e->getMessage()]);
            return $this->fallbackResult();
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Google Vision API
    // ─────────────────────────────────────────────────────────────────────────

    private function analyzeWithGoogle(string $imagePath): array
    {
        try {
            $apiKey = config('ai.providers.google.vision_api_key');
            $visionUrl = config('ai.providers.google.vision_url');

            if (!$apiKey) {
                throw new \RuntimeException('Google Vision API key not configured.');
            }

            $imageContent = $this->getBase64Content($imagePath);

            $payload = [
                'requests' => [
                    [
                        'image' => ['content' => $imageContent],
                        'features' => [
                            ['type' => 'LABEL_DETECTION', 'maxResults' => 15],
                            ['type' => 'IMAGE_PROPERTIES'],
                            ['type' => 'SAFE_SEARCH_DETECTION'],
                            ['type' => 'OBJECT_LOCALIZATION', 'maxResults' => 10],
                        ],
                    ]
                ],
            ];

            $response = Http::timeout(30)
                ->post("{$visionUrl}?key={$apiKey}", $payload);

            if (!$response->successful()) {
                throw new \RuntimeException('Google Vision API error: ' . $response->body());
            }

            return $this->parseGoogleVisionResponse(
                $response->json('responses.0', [])
            );

        } catch (\Throwable $e) {
            Log::warning('Google Vision failed, falling back to GPT Vision', ['error' => $e->getMessage()]);
            return $this->analyzeWithVision($imagePath);
        }
    }

    private function parseGoogleVisionResponse(array $response): array
    {
        $labels = array_column($response['labelAnnotations'] ?? [], 'description');

        $roomType = $this->detectRoomType($labels);
        $furnitureQuality = $this->detectFurnitureQuality($labels);
        $brightness = $this->detectBrightness($response['imagePropertiesAnnotation'] ?? []);
        $qualityScore = $this->computeQualityScore($labels, $brightness);

        return [
            'success' => true,
            'room_type' => $roomType,
            'furniture_quality' => $furnitureQuality,
            'brightness_level' => $brightness,
            'quality_score' => $qualityScore,
            'issues' => $qualityScore < 40 ? ['low quality detected'] : [],
            'raw_labels' => $labels,
            'source' => 'google_vision',
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Azure Computer Vision
    // ─────────────────────────────────────────────────────────────────────────

    private function analyzeWithAzure(string $imagePath): array
    {
        try {
            $apiKey = config('ai.providers.azure_vision.api_key');
            $endpoint = config('ai.providers.azure_vision.endpoint');

            if (!$apiKey || !$endpoint) {
                throw new \RuntimeException('Azure Vision not configured.');
            }

            $imageUrl = $this->resolveUrl($imagePath);

            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => $apiKey,
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->post("{$endpoint}/computervision/imageanalysis:analyze?api-version=2023-10-01&features=tags,objects,color", [
                    'url' => $imageUrl,
                ]);

            if (!$response->successful()) {
                throw new \RuntimeException('Azure Vision error: ' . $response->body());
            }

            return $this->parseAzureResponse($response->json());

        } catch (\Throwable $e) {
            Log::warning('Azure Vision failed, falling back to GPT Vision', ['error' => $e->getMessage()]);
            return $this->analyzeWithVision($imagePath);
        }
    }

    private function parseAzureResponse(array $data): array
    {
        $tags = array_column($data['tagsResult']['values'] ?? [], 'name');
        $roomType = $this->detectRoomType($tags);
        $quality = $this->computeQualityScore($tags, 'moderate');

        return [
            'success' => true,
            'room_type' => $roomType,
            'furniture_quality' => $this->detectFurnitureQuality($tags),
            'brightness_level' => 'moderate',
            'quality_score' => $quality,
            'issues' => [],
            'source' => 'azure_vision',
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Classification helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function detectRoomType(array $labels): string
    {
        $map = [
            'bedroom' => ['bedroom', 'bed', 'master bedroom', 'غرفة نوم'],
            'kitchen' => ['kitchen', 'cooking', 'stove', 'مطبخ'],
            'bathroom' => ['bathroom', 'toilet', 'shower', 'bath', 'حمام'],
            'living_room' => ['living room', 'sofa', 'couch', 'lounge', 'صالة'],
            'exterior' => ['facade', 'building', 'exterior', 'front', 'واجهة'],
            'garden' => ['garden', 'grass', 'plant', 'outdoor', 'حديقة'],
            'pool' => ['pool', 'swimming pool', 'حمام سباحة'],
        ];

        $lower = array_map('strtolower', $labels);

        foreach ($map as $type => $keywords) {
            foreach ($keywords as $kw) {
                if (in_array($kw, $lower, true) || $this->partialMatch($lower, $kw)) {
                    return $type;
                }
            }
        }

        return 'other';
    }

    private function detectFurnitureQuality(array $labels): string
    {
        $lower = array_map('strtolower', $labels);

        if ($this->partialMatch($lower, 'luxury') || $this->partialMatch($lower, 'premium')) {
            return 'luxury';
        }
        if ($this->partialMatch($lower, 'furniture') || $this->partialMatch($lower, 'furnished')) {
            return 'standard';
        }
        if ($this->partialMatch($lower, 'basic') || $this->partialMatch($lower, 'simple')) {
            return 'basic';
        }

        return 'none';
    }

    private function detectBrightness(array $props): string
    {
        if (empty($props))
            return 'moderate';

        $dominant = $props['dominantColors']['colors'][0]['pixelFraction'] ?? 0.5;

        // Very rough heuristic based on pixel fraction of dominant color
        if ($dominant > 0.6)
            return 'bright';
        if ($dominant < 0.2)
            return 'dark';

        return 'moderate';
    }

    private function computeQualityScore(array $labels, string $brightness): int
    {
        $score = 60;

        if ($brightness === 'bright')
            $score += 15;
        if ($brightness === 'dark')
            $score -= 20;

        // Penalise poor labels
        $negatives = ['blurry', 'dark', 'cluttered', 'messy', 'noise', 'low quality'];
        foreach ($negatives as $neg) {
            if ($this->partialMatch(array_map('strtolower', $labels), $neg)) {
                $score -= 15;
            }
        }

        return max(0, min(100, $score));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Utilities
    // ─────────────────────────────────────────────────────────────────────────

    private function partialMatch(array $haystack, string $needle): bool
    {
        foreach ($haystack as $item) {
            if (str_contains($item, $needle))
                return true;
        }
        return false;
    }

    private function resolveUrl(string $imagePath): string
    {
        if (str_starts_with($imagePath, 'http')) {
            return $imagePath;
        }

        return Storage::url($imagePath);
    }

    private function getBase64Content(string $imagePath): string
    {
        if (str_starts_with($imagePath, 'http')) {
            $content = Http::get($imagePath)->body();
        } else {
            $content = Storage::get($imagePath);
        }

        return base64_encode($content);
    }

    private function fallbackResult(): array
    {
        return [
            'success' => false,
            'room_type' => 'other',
            'furniture_quality' => 'none',
            'brightness_level' => 'moderate',
            'quality_score' => 0,
            'issues' => ['analysis_failed'],
            'source' => 'fallback',
        ];
    }
}
