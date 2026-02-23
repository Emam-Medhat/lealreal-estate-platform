<?php

namespace App\Services\AI\Concerns;

/**
 * Prompt injection prevention and input sanitisation.
 */
trait SanitizesPrompt
{
    /**
     * Strip prompt-injection patterns and enforce maximum length.
     *
     * @throws \InvalidArgumentException If injection pattern detected.
     */
    private function sanitizeInput(string $input, int $maxLength = 1000): string
    {
        $patterns = config('ai.security.prompt_injection_patterns', []);

        $lc = mb_strtolower($input);
        foreach ($patterns as $pattern) {
            if (str_contains($lc, mb_strtolower($pattern))) {
                throw new \InvalidArgumentException(
                    'Input contains disallowed content.'
                );
            }
        }

        // Strip HTML tags and null bytes
        $clean = strip_tags($input);
        $clean = str_replace("\0", '', $clean);

        // Truncate
        return mb_substr(trim($clean), 0, $maxLength);
    }
}
