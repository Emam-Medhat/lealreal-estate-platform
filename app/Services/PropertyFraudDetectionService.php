<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyMedia;
use App\Models\User;
use App\Models\FraudDetectionLog;
use App\Models\AiFraudAlert;
use App\Jobs\Fraud\AnalyzePropertyFraudJob;
use App\Jobs\Fraud\AnalyzeUserFraudJob;
use Illuminate\Support\Facades\Log;

/**
 * PropertyFraudDetectionService
 *
 * Main orchestrator for the fraud detection pipeline.
 * Coordinates all 5 detection modules:
 *  1. Image Hash Fraud Detection
 *  2. Address Validation
 *  3. Price Anomaly Detection
 *  4. Fake Account Detection
 *  5. Duplicate Property Detection
 *
 * Usage:
 *   $service = app(PropertyFraudDetectionService::class);
 *   $result  = $service->analyzeProperty($property, $user);
 */
class PropertyFraudDetectionService
{
    private ImageHashService $imageHashService;
    private AddressValidationService $addressService;
    private PriceAnomalyService $priceAnomalyService;
    private FakeAccountDetectionService $fakeAccountService;
    private DuplicateDetectionService $duplicateService;

    // Fraud score flag/block thresholds
    private int $flagThreshold;
    private int $blockThreshold;
    private int $reviewThreshold;

    public function __construct(
        ImageHashService $imageHashService,
        AddressValidationService $addressService,
        PriceAnomalyService $priceAnomalyService,
        FakeAccountDetectionService $fakeAccountService,
        DuplicateDetectionService $duplicateService,
    ) {
        $this->imageHashService = $imageHashService;
        $this->addressService = $addressService;
        $this->priceAnomalyService = $priceAnomalyService;
        $this->fakeAccountService = $fakeAccountService;
        $this->duplicateService = $duplicateService;

        $scoring = config('fraud_detection.scoring');
        $this->flagThreshold = $scoring['flag_threshold'] ?? 50;
        $this->blockThreshold = $scoring['block_threshold'] ?? 80;
        $this->reviewThreshold = $scoring['review_threshold'] ?? 40;
    }

    // =========================================================================
    // PUBLIC API
    // =========================================================================

    /**
     * Run full synchronous fraud analysis on a property.
     * (Use dispatchAnalysis() for async queue-based analysis)
     *
     * @param Property $property
     * @param User|null $user        The user submitting the property
     * @param string $currentIp      The submitter's IP address
     * @return array  Comprehensive fraud analysis result
     */
    public function analyzeProperty(
        Property $property,
        ?User $user = null,
        string $currentIp = ''
    ): array {
        $startTime = microtime(true);
        Log::channel(config('fraud_detection.logging.channel', 'daily'))
            ->info("FraudDetection: Starting analysis for property #{$property->id}");

        $results = [
            'property_id' => $property->id,
            'timestamp' => now()->toISOString(),
            'modules' => [],
            'total_fraud_score' => 0,
            'fraud_flags' => [],
            'final_status' => 'clean',
            'action' => 'allow',
            'execution_ms' => 0,
        ];

        // ── Module 1: Image Hash Analysis ─────────────────────────────────────
        $imageResult = $this->runImageHashAnalysis($property);
        $results['modules']['image_hash'] = $imageResult;
        $results['total_fraud_score'] += $imageResult['fraud_score'];
        if (!empty($imageResult['flags'])) {
            $results['fraud_flags'] = array_merge($results['fraud_flags'], $imageResult['flags']);
        }

        // ── Module 2: Address Validation ──────────────────────────────────────
        $addressResult = $this->addressService->validateProperty($property);
        $results['modules']['address_validation'] = $addressResult;
        $results['total_fraud_score'] += $addressResult['fraud_score'];
        if (!empty($addressResult['flags'])) {
            $results['fraud_flags'] = array_merge($results['fraud_flags'], $addressResult['flags']);
        }

        // ── Module 3: Price Anomaly Detection ─────────────────────────────────
        $priceResult = $this->priceAnomalyService->analyzeProperty($property);
        $results['modules']['price_anomaly'] = $priceResult;
        $results['total_fraud_score'] += $priceResult['anomaly_score'];
        if ($priceResult['is_anomalous']) {
            $results['fraud_flags'][] = 'price_anomaly';
        }

        // ── Module 4: Fake Account Detection (if user provided) ───────────────
        if ($user) {
            $accountResult = $this->fakeAccountService->analyzeUser($user, $currentIp);
            $results['modules']['fake_account'] = $accountResult;
            $results['total_fraud_score'] += (int) ($accountResult['risk_score'] * 0.5); // 50% weight
            if ($accountResult['risk_score'] > 40) {
                $results['fraud_flags'][] = 'suspicious_account';
            }
        }

        // ── Module 5: Duplicate Detection ─────────────────────────────────────
        $duplicateResult = $this->duplicateService->detectDuplicates($property);
        $results['modules']['duplicate_detection'] = $duplicateResult;
        if ($duplicateResult['has_duplicates']) {
            $results['total_fraud_score'] += min(40, $duplicateResult['highest_score'] * 0.4);
            $results['fraud_flags'][] = 'potential_duplicate';
        }

        // ── Module 6: Graph Network Analysis ──────────────────────────────────
        $graph = $this->analyzeGraphNetwork($property);
        $results['modules']['graph_network'] = $graph;
        if (($graph['cluster_size'] ?? 0) >= 3 && ($graph['cities_count'] ?? 0) >= 2) {
            $results['total_fraud_score'] = min(100, $results['total_fraud_score'] + 15);
            $results['fraud_flags'][] = 'network_anomaly';
        }

        // ── Finalize Scores ───────────────────────────────────────────────────
        $results['total_fraud_score'] = min(100, (int) $results['total_fraud_score']);

        // Determine final status and action
        $finalAction = $this->determineFinalAction($results['total_fraud_score'], $results);
        $results['final_status'] = $finalAction['status'];
        $results['action'] = $finalAction['action'];

        // Persist fraud scores to property
        $this->persistFraudScore($property, $results);

        // Create AI fraud alert if score is high enough
        $this->createAlertIfNeeded($property, $user, $results);

        $results['execution_ms'] = (int) round((microtime(true) - $startTime) * 1000);

        Log::info("FraudDetection: Completed analysis for property #{$property->id}", [
            'score' => $results['total_fraud_score'],
            'status' => $results['final_status'],
            'ms' => $results['execution_ms'],
        ]);

        return $results;
    }

    /**
     * Dispatch asynchronous (queued) fraud analysis for a property.
     * Use this in controllers to avoid blocking the request.
     */
    public function dispatchPropertyAnalysis(Property $property, ?User $user = null, string $ip = ''): void
    {
        dispatch(new AnalyzePropertyFraudJob($property->id, $user?->id, $ip))
            ->onQueue(config('fraud_detection.queue.queue_name', 'fraud-detection'));
    }

    private function analyzeGraphNetwork(Property $property): array
    {
        $recent = now()->subDays(30);
        $byUser = Property::query()
            ->select(['id', 'city', 'created_at'])
            ->where('user_id', $property->user_id)
            ->where('id', '!=', $property->id)
            ->where('created_at', '>=', $recent)
            ->get();

        $cities = $byUser->pluck('city')->unique()->filter()->values();
        $clusterSize = $byUser->count() + 1;

        return [
            'cluster_size' => $clusterSize,
            'cities_count' => $cities->count(),
            'sample_nodes' => $byUser->take(5)->pluck('id'),
        ];
    }
    /**
     * Dispatch asynchronous user risk scoring.
     */
    public function dispatchUserAnalysis(User $user, string $ip = ''): void
    {
        dispatch(new AnalyzeUserFraudJob($user->id, $ip))
            ->onQueue(config('fraud_detection.queue.queue_name', 'fraud-detection'));
    }

    /**
     * Process a single image for hash generation and duplicate detection.
     * Called immediately when an image is uploaded.
     *
     * @param PropertyMedia $media
     * @return array{hash: string|null, duplicates: array, fraud_score: int}
     */
    public function processUploadedImage(PropertyMedia $media): array
    {
        $result = [
            'hash' => null,
            'duplicates' => [],
            'fraud_score' => 0,
            'is_flagged' => false,
        ];

        // Generate hash
        $hashGenerated = $this->imageHashService->storeHashForMedia($media);
        if (!$hashGenerated || !$media->image_hash) {
            return $result;
        }

        $result['hash'] = $media->image_hash;

        // Find duplicates
        $duplicates = $this->imageHashService->findDuplicates($media->image_hash, $media->id);

        if ($duplicates->isNotEmpty()) {
            $result['duplicates'] = $duplicates->map(fn($d) => [
                'id' => $d->id,
                'property_id' => $d->property_id,
                'similarity' => $d->_similarity ?? 0,
                'file_path' => $d->file_path,
            ])->toArray();

            $maxSimilarity = max(array_column($result['duplicates'], 'similarity'));
            $result['fraud_score'] = (int) min(100, $maxSimilarity);
            $result['is_flagged'] = $result['fraud_score'] >= config('fraud_detection.image_hash.similarity_threshold', 90);

            // Update media record
            $media->update([
                'fraud_score' => $result['fraud_score'],
                'is_duplicate' => $result['is_flagged'],
                'duplicate_of' => $result['is_flagged']
                    ? ($duplicates->first()->id ?? null)
                    : null,
            ]);

            // Update property fraud score
            if ($result['is_flagged'] && $media->property_id) {
                $property = Property::find($media->property_id);
                if ($property) {
                    $newScore = min(100, $property->fraud_score + ($this->imageHashService ? 25 : 0));
                    $flags = array_merge($property->fraud_flags ?? [], ['duplicate_image']);
                    $property->update([
                        'fraud_score' => $newScore,
                        'fraud_flags' => $flags,
                        'fraud_status' => $newScore >= $this->blockThreshold ? 'blocked' : 'suspicious',
                    ]);
                }
            }

            // Log
            FraudDetectionLog::create([
                'entity_type' => 'property_media',
                'entity_id' => $media->id,
                'detection_type' => FraudDetectionLog::TYPE_IMAGE_HASH,
                'fraud_score' => $result['fraud_score'],
                'details' => $result,
                'action_taken' => $result['is_flagged']
                    ? FraudDetectionLog::ACTION_FLAGGED
                    : FraudDetectionLog::ACTION_NONE,
            ]);
        }

        return $result;
    }

    /**
     * Get aggregated fraud statistics for the admin dashboard.
     */
    public function getDashboardStats(): array
    {
        $last24h = now()->subHours(24);
        $last7d = now()->subDays(7);
        $last30d = now()->subDays(30);

        return [
            'flagged_properties' => [
                'last_24h' => Property::where('fraud_status', '!=', 'clean')
                    ->where('updated_at', '>=', $last24h)->count(),
                'last_7d' => Property::where('fraud_status', '!=', 'clean')
                    ->where('updated_at', '>=', $last7d)->count(),
            ],
            'blocked_properties' => [
                'total' => Property::where('fraud_status', 'blocked')->count(),
            ],
            'duplicate_pairs' => [
                'pending' => \App\Models\PropertyDuplicate::where('status', 'pending')->count(),
                'confirmed' => \App\Models\PropertyDuplicate::where('status', 'confirmed')->count(),
            ],
            'high_risk_users' => [
                'count' => User::where('risk_score', '>=', 70)->count(),
            ],
            'restricted_users' => [
                'count' => User::where('is_restricted', true)->count(),
            ],
            'detection_logs_by_type' => FraudDetectionLog::flaggedCountByType(7),
            'recent_alerts' => AiFraudAlert::orderByDesc('created_at')
                ->where('investigation_status', 'pending')
                ->limit(10)
                ->get(['id', 'alert_type', 'risk_level', 'description', 'created_at']),
        ];
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function runImageHashAnalysis(Property $property): array
    {
        $result = ['fraud_score' => 0, 'flags' => [], 'duplicates' => []];

        $images = PropertyMedia::where('property_id', $property->id)
            ->where('media_type', 'image')
            ->get();

        if ($images->isEmpty()) {
            return $result;
        }

        $maxScore = 0;
        $anyDuplicate = false;

        foreach ($images as $media) {
            // Generate hash if not already generated
            if (!$media->image_hash) {
                $this->imageHashService->storeHashForMedia($media);
                $media->refresh();
            }

            if ($media->image_hash) {
                $duplicates = $this->imageHashService->findDuplicates($media->image_hash, $media->id);
                if ($duplicates->isNotEmpty()) {
                    $anyDuplicate = true;
                    $topSimilarity = $duplicates->max('_similarity');
                    $maxScore = max($maxScore, (int) $topSimilarity);

                    $result['duplicates'][] = [
                        'media_id' => $media->id,
                        'similarity' => $topSimilarity,
                        'matches' => $duplicates->take(3)->toArray(),
                    ];
                }
            }
        }

        if ($anyDuplicate) {
            $result['fraud_score'] = min(100, $maxScore);
            $result['flags'][] = 'duplicate_image_detected';
        }

        return $result;
    }

    private function determineFinalAction(int $score, array $results): array
    {
        $hasDuplicate = $results['modules']['duplicate_detection']['has_duplicates'] ?? false;
        $highestDuplicateScore = $results['modules']['duplicate_detection']['highest_score'] ?? 0;

        if ($score >= $this->blockThreshold || ($hasDuplicate && $highestDuplicateScore >= 95)) {
            return ['status' => 'blocked', 'action' => 'block'];
        }

        if ($score >= $this->flagThreshold || ($hasDuplicate && $highestDuplicateScore >= 85)) {
            return ['status' => 'suspicious', 'action' => 'flag_for_review'];
        }

        if ($score >= $this->reviewThreshold) {
            return ['status' => 'suspicious', 'action' => 'soft_flag'];
        }

        return ['status' => 'clean', 'action' => 'allow'];
    }

    private function persistFraudScore(Property $property, array $results): void
    {
        try {
            $property->update([
                'fraud_score' => $results['total_fraud_score'],
                'fraud_flags' => $results['fraud_flags'],
                'fraud_status' => $results['final_status'],
                'anomaly_score' => $results['modules']['price_anomaly']['anomaly_score'] ?? 0,
            ]);
        } catch (\Throwable $e) {
            Log::error('PropertyFraudDetection: Failed to persist fraud score', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function createAlertIfNeeded(Property $property, ?User $user, array $results): void
    {
        $score = $results['total_fraud_score'];
        $notifyThreshold = config('fraud_detection.notifications.notify_threshold', 70);

        if ($score < $notifyThreshold) {
            return;
        }

        try {
            AiFraudAlert::create([
                'property_id' => $property->id,
                'user_id' => $user?->id,
                'alert_type' => 'property_fraud',
                'risk_level' => $score >= 80 ? 'critical' : ($score >= 60 ? 'high' : 'medium'),
                'description' => "Fraud detected: Score {$score}/100. Flags: " . implode(', ', $results['fraud_flags']),
                'evidence' => $results['modules'],
                'confidence_score' => $score,
                'investigation_status' => 'pending',
                'created_by' => 1, // System
            ]);
        } catch (\Throwable $e) {
            Log::error('PropertyFraudDetection: Failed to create fraud alert', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
