<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordService
{
    public function hash(string $password): string
    {
        return Hash::make($password);
    }

    public function verify(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }

    public function generateResetToken(): string
    {
        return Str::random(60);
    }

    public function validateToken(string $token): bool
    {
        // Check if token exists and is not expired
        $resetToken = \App\Models\PasswordResetToken::where('token', $token)
            ->where('created_at', '>', now()->subHours(1))
            ->first();

        return $resetToken !== null;
    }

    public function generateStrongPassword(int $length = 12): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        $password = '';

        // Ensure at least one character from each category
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        // Fill the rest
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        return str_shuffle($password);
    }

    public function checkPasswordStrength(string $password): array
    {
        $score = 0;
        $feedback = [];

        // Length check
        if (strlen($password) >= 8) {
            $score += 1;
        } else {
            $feedback[] = __('كلمة المرور يجب أن تكون 8 أحرف على الأقل');
        }

        // Uppercase check
        if (preg_match('/[A-Z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = __('كلمة المرور يجب أن تحتوي على حرف كبير واحد على الأقل');
        }

        // Lowercase check
        if (preg_match('/[a-z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = __('كلمة المرور يجب أن تحتوي على حرف صغير واحد على الأقل');
        }

        // Number check
        if (preg_match('/[0-9]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = __('كلمة المرور يجب أن تحتوي على رقم واحد على الأقل');
        }

        // Special character check
        if (preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = __('كلمة المرور يجب أن تحتوي على رمز خاص واحد على الأقل');
        }

        $strength = 'weak';
        if ($score >= 4) {
            $strength = 'strong';
        } elseif ($score >= 3) {
            $strength = 'medium';
        }

        return [
            'score' => $score,
            'strength' => $strength,
            'feedback' => $feedback,
        ];
    }
}
