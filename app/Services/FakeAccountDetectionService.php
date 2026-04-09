<?php

namespace App\Services;

use App\Models\User;
use App\Models\Property;
use App\Models\IpHistory;
use App\Models\FraudDetectionLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * FakeAccountDetectionService
 *
 * Detects fake or suspicious user accounts based on:
 *  1. Multiple accounts from same IP
 *  2. Rapid listing posting frequency
 *  3. Repeated image usage
 *  4. Temporary/disposable email domains
 *  5. Missing phone verification
 */
class FakeAccountDetectionService
{
    private array $config;
    private array $scores;
    private array $tempEmailDomains;
    private int $autoRestrictThreshold;
    private int $manualVerifyThreshold;

    public function __construct()
    {
        $this->config = config('fraud_detection.fake_account');
        $this->scores = $this->config['scores'] ?? [];
        $this->tempEmailDomains = $this->config['temp_email_domains'] ?? [];
        $this->autoRestrictThreshold = $this->config['auto_restrict_threshold'] ?? 70;
        $this->manualVerifyThreshold = $this->config['manual_verify_threshold'] ?? 50;
    }

    /**
     * Full risk analysis for a user account.
     *
     * @param User   $user
     * @param string $currentIp  The IP address of the current request
     * @return array{
     *   risk_score: int,
     *   risk_level: string,
     *   risk_factors: array,
     *   action: string,
     *   should_restrict: bool,
     *   should_verify: bool
     * }
     */
    public function analyzeUser(User $user, string $currentIp = ''): array
    {
        $riskFactors = [];
        $totalScore = 0;

        // 1. Multiple accounts from same IP
        if ($currentIp) {
            $ipRisk = $this->checkMultipleAccountsFromIp($user, $currentIp);
            if ($ipRisk['detected']) {
                $riskFactors[] = $ipRisk;
                $totalScore += $ipRisk['score'];
            }

            // Track IP
            $this->recordIpHistory($user, $currentIp);
        }

        // 2. Rapid posting frequency
        $postingRisk = $this->checkRapidPosting($user);
        if ($postingRisk['detected']) {
            $riskFactors[] = $postingRisk;
            $totalScore += $postingRisk['score'];
        }

        // 3. Repeated image usage
        $imageRisk = $this->checkRepeatedImages($user);
        if ($imageRisk['detected']) {
            $riskFactors[] = $imageRisk;
            $totalScore += $imageRisk['score'];
        }

        // 4. Temporary email domain
        $emailRisk = $this->checkTempEmail($user);
        if ($emailRisk['detected']) {
            $riskFactors[] = $emailRisk;
            $totalScore += $emailRisk['score'];
        }

        // 5. No phone verification
        $phoneRisk = $this->checkPhoneVerification($user);
        if ($phoneRisk['detected']) {
            $riskFactors[] = $phoneRisk;
            $totalScore += $phoneRisk['score'];
        }

        // 6. New account bonus
        $newAccountRisk = $this->checkNewAccount($user);
        if ($newAccountRisk['detected']) {
            $riskFactors[] = $newAccountRisk;
            $totalScore += $newAccountRisk['score'];
        }

        $riskScore = min(100, $totalScore);
        $riskLevel = $this->classifyRiskLevel($riskScore);
        $action = $this->determineAction($riskScore);

        $result = [
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'risk_factors' => $riskFactors,
            'action' => $action,
            'should_restrict' => $riskScore >= $this->autoRestrictThreshold,
            'should_verify' => $riskScore >= $this->manualVerifyThreshold,
        ];

        // Apply consequences
        $this->applyConsequences($user, $result, $riskFactors);

        // Log detection
        $this->logDetection($user, $result);

        return $result;
    }

    // -------------------------------------------------------------------------
    // Individual risk checks
    // -------------------------------------------------------------------------

    /**
     * Check if multiple accounts have been created or active from the same IP.
     */
    private function checkMultipleAccountsFromIp(User $user, string $ip): array
    {
        $maxAccounts = $this->config['max_accounts_per_ip'] ?? 3;
        $windowHours = $this->config['ip_window_hours'] ?? 24;

        // Check ip_history table
        $count = IpHistory::where('ip_address', $ip)
            ->where('user_id', '!=', $user->id)
            ->where('created_at', '>=', now()->subHours($windowHours))
            ->distinct('user_id')
            ->count('user_id');

        // Also check registration IPs in users table
        $registrationCount = User::where('registration_ip', $ip)
            ->where('id', '!=', $user->id)
            ->where('created_at', '>=', now()->subHours($windowHours * 7))
            ->count();

        $total = $count + $registrationCount;

        if ($total >= $maxAccounts) {
            return [
                'detected' => true,
                'type' => 'multiple_ip_accounts',
                'message' => "{$total} accounts detected from IP {$ip} in the last {$windowHours} hours",
                'score' => $this->scores['multiple_ip_accounts'] ?? 35,
                'data' => ['ip' => $ip, 'count' => $total],
            ];
        }

        return ['detected' => false];
    }

    /**
     * Check if user is posting listings too rapidly.
     */
    private function checkRapidPosting(User $user): array
    {
        $maxPerDay = $this->config['max_listings_per_day'] ?? 5;
        $maxPerHour = $this->config['max_listings_per_hour'] ?? 2;

        $dailyCount = Property::where('user_id', $user->id)
            ->orWhere('agent_id', $user->id)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        $hourlyCount = Property::where('user_id', $user->id)
            ->orWhere('agent_id', $user->id)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($hourlyCount >= $maxPerHour) {
            return [
                'detected' => true,
                'type' => 'rapid_posting_hourly',
                'message' => "User posted {$hourlyCount} listings in the last hour",
                'score' => $this->scores['rapid_posting'] ?? 25,
                'data' => ['hourly_count' => $hourlyCount, 'threshold' => $maxPerHour],
            ];
        }

        if ($dailyCount >= $maxPerDay) {
            return [
                'detected' => true,
                'type' => 'rapid_posting_daily',
                'message' => "User posted {$dailyCount} listings today",
                'score' => (int) (($this->scores['rapid_posting'] ?? 25) * 0.6),
                'data' => ['daily_count' => $dailyCount, 'threshold' => $maxPerDay],
            ];
        }

        return ['detected' => false];
    }

    /**
     * Check if the user's properties reuse the same images (hash-based).
     */
    private function checkRepeatedImages(User $user): array
    {
        // Fetch all image hashes for this user's properties
        $hashes = DB::table('property_media as pm')
            ->join('properties as p', 'pm.property_id', '=', 'p.id')
            ->where(function ($q) use ($user) {
                $q->where('p.user_id', $user->id)->orWhere('p.agent_id', $user->id);
            })
            ->where('pm.media_type', 'image')
            ->whereNotNull('pm.image_hash')
            ->pluck('pm.image_hash');

        if ($hashes->count() < 2) {
            return ['detected' => false];
        }

        // Count duplicates
        $duplicateCount = $hashes->count() - $hashes->unique()->count();

        if ($duplicateCount > 0) {
            return [
                'detected' => true,
                'type' => 'repeated_images',
                'message' => "User reuses {$duplicateCount} duplicate images across listings",
                'score' => $this->scores['repeated_images'] ?? 20,
                'data' => ['duplicate_count' => $duplicateCount],
            ];
        }

        return ['detected' => false];
    }

    /**
     * Detect temporary/disposable email addresses.
     */
    private function checkTempEmail(User $user): array
    {
        if (!$user->email) {
            return ['detected' => false];
        }

        $domain = strtolower(substr(strrchr($user->email, '@'), 1));

        if (in_array($domain, $this->tempEmailDomains)) {
            return [
                'detected' => true,
                'type' => 'temp_email',
                'message' => "User email domain ({$domain}) is a known temporary email provider",
                'score' => $this->scores['temp_email'] ?? 30,
                'data' => ['domain' => $domain],
            ];
        }

        return ['detected' => false];
    }

    /**
     * Check if phone number is verified.
     */
    private function checkPhoneVerification(User $user): array
    {
        $noPhone = empty($user->phone);
        $notVerified = is_null($user->phone_verified_at);

        if ($noPhone) {
            return [
                'detected' => true,
                'type' => 'no_phone',
                'message' => 'User has not provided a phone number',
                'score' => $this->scores['no_phone_verify'] ?? 15,
                'data' => [],
            ];
        }

        if ($notVerified) {
            return [
                'detected' => true,
                'type' => 'unverified_phone',
                'message' => 'User phone number has not been verified',
                'score' => (int) (($this->scores['no_phone_verify'] ?? 15) * 0.5),
                'data' => [],
            ];
        }

        return ['detected' => false];
    }

    /**
     * Check if account is very new (created within last 24 hours).
     */
    private function checkNewAccount(User $user): array
    {
        $ageHours = now()->diffInHours($user->created_at);

        if ($ageHours <= 24) {
            return [
                'detected' => true,
                'type' => 'new_account',
                'message' => "Account created {$ageHours} hours ago",
                'score' => $this->scores['new_account'] ?? 10,
                'data' => ['account_age_hours' => $ageHours],
            ];
        }

        return ['detected' => false];
    }

    // -------------------------------------------------------------------------
    // Consequences and helpers
    // -------------------------------------------------------------------------

    private function applyConsequences(User $user, array $result, array $riskFactors): void
    {
        $updateData = [
            'risk_score' => $result['risk_score'],
            'risk_factors' => $riskFactors,
        ];

        if ($result['should_restrict']) {
            $updateData['is_restricted'] = true;
            $updateData['restricted_at'] = now();
            $updateData['restriction_reason'] = 'Auto-restricted: High fraud risk score (' . $result['risk_score'] . ')';

            Log::warning('FakeAccountDetection: User auto-restricted', [
                'user_id' => $user->id,
                'risk_score' => $result['risk_score'],
            ]);
        }

        if ($result['should_verify']) {
            $updateData['requires_manual_verification'] = true;
        }

        try {
            $user->update($updateData);
        } catch (\Throwable $e) {
            // Columns might not exist yet if migration hasn't run
            Log::error('FakeAccountDetection: Failed to update user', ['error' => $e->getMessage()]);
        }
    }

    private function recordIpHistory(User $user, string $ip): void
    {
        try {
            IpHistory::create([
                'ip_address' => $ip,
                'user_id' => $user->id,
                'action' => 'listing_check',
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            Log::debug('FakeAccountDetection: Failed to record IP', ['error' => $e->getMessage()]);
        }
    }

    private function classifyRiskLevel(int $score): string
    {
        if ($score >= 80)
            return 'critical';
        if ($score >= 60)
            return 'high';
        if ($score >= 40)
            return 'medium';
        return 'low';
    }

    private function determineAction(int $score): string
    {
        if ($score >= $this->autoRestrictThreshold) {
            return 'restrict_and_verify';
        }
        if ($score >= $this->manualVerifyThreshold) {
            return 'require_verification';
        }
        if ($score >= 30) {
            return 'flag_for_review';
        }
        return 'none';
    }

    private function logDetection(User $user, array $result): void
    {
        try {
            FraudDetectionLog::create([
                'entity_type' => 'user',
                'entity_id' => $user->id,
                'detection_type' => FraudDetectionLog::TYPE_FAKE_ACCOUNT,
                'fraud_score' => $result['risk_score'],
                'details' => $result,
                'action_taken' => $result['action'],
            ]);
        } catch (\Throwable $e) {
            Log::error('FakeAccountDetection: Failed to write log', ['error' => $e->getMessage()]);
        }
    }
}
