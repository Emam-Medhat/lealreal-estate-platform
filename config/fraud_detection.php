<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fraud Detection System Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file controls all thresholds and parameters for the
    | AI-powered Fraud Detection and Smart Duplicate Detection system.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Image Hash Settings
    |--------------------------------------------------------------------------
    */
    'image_hash' => [
        // Algorithm: 'phash' or 'dhash'
        'algorithm' => env('FRAUD_HASH_ALGORITHM', 'dhash'),

        // Hash size in bits (8 = 64-bit hash)
        'hash_size' => env('FRAUD_HASH_SIZE', 8),

        // Similarity threshold (0-100). Images above this are flagged
        'similarity_threshold' => env('FRAUD_IMAGE_SIMILARITY', 90),

        // Maximum Hamming distance for near-duplicate detection
        // Lower value = stricter matching (100% - threshold) * hash_bits / 100
        'max_hamming_distance' => env('FRAUD_MAX_HAMMING', 6),

        // Fraud score contribution (0-100)
        'fraud_score_weight' => 40,
    ],

    /*
    |--------------------------------------------------------------------------
    | Address Validation Settings (Google Maps Geocoding API)
    |--------------------------------------------------------------------------
    */
    'address_validation' => [
        // Google Maps Geocoding API key
        'google_maps_api_key' => env('GOOGLE_MAPS_API_KEY', ''),

        // Allowed countries (ISO 3166-1 alpha-2 codes). Empty = allow all
        'allowed_countries' => env('FRAUD_ALLOWED_COUNTRIES', 'EG,SA,AE,KW,QA,BH,OM,JO,LB'),

        // Minimum accuracy: 'street_address', 'route', 'locality', 'sublocality', 'administrative_area_level_1', 'country'
        'minimum_accuracy' => 'locality',

        // Fraud scores for each geocoding failure type
        'scores' => [
            'invalid_geocode' => 30,
            'country_mismatch' => 25,
            'low_accuracy' => 20,
            'city_mismatch' => 20,
            'coordinate_invalid' => 15,
            'missing_street' => 10,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Price Anomaly Detection Settings
    |--------------------------------------------------------------------------
    */
    'price_anomaly' => [
        // Deviation percentage threshold for suspicious pricing
        'upper_deviation_pct' => env('FRAUD_PRICE_UPPER_DEV', 40),
        'lower_deviation_pct' => env('FRAUD_PRICE_LOWER_DEV', 40),

        // Z-score threshold (values above this are anomalous)
        'z_score_threshold' => env('FRAUD_ZSCORE_THRESHOLD', 2.5),

        // Minimum number of comparable properties needed for stats
        'min_sample_size' => env('FRAUD_MIN_SAMPLE', 5),

        // Fraud score contribution weights
        'scores' => [
            'high_deviation' => 30,
            'extreme_deviation' => 50,
            'zscore_high' => 40,
        ],

        // Context weights for average calculation
        'context_weights' => [
            'neighborhood' => 0.50,
            'city' => 0.30,
            'property_type' => 0.20,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fake Account Detection Settings
    |--------------------------------------------------------------------------
    */
    'fake_account' => [
        // Multiple accounts from same IP
        'max_accounts_per_ip' => env('FRAUD_MAX_ACCOUNTS_IP', 3),
        'ip_window_hours' => env('FRAUD_IP_WINDOW', 24),

        // Rapid posting frequency
        'max_listings_per_day' => env('FRAUD_MAX_LISTINGS_DAY', 5),
        'max_listings_per_hour' => env('FRAUD_MAX_LISTINGS_HOUR', 2),

        // Temporary email domain detection
        'temp_email_domains' => [
            'mailinator.com',
            'guerrillamail.com',
            'tempmail.com',
            'throwaway.email',
            'yopmail.com',
            'sharklasers.com',
            'guerrillamailblock.com',
            'grr.la',
            'guerrillamail.info',
            'spam4.me',
            'trashmail.com',
            'trashmail.me',
            'fakeinbox.com',
            'maildrop.cc',
            '10minutemail.com',
            'dispostable.com',
            'getairmail.com',
            'mailnull.com',
            'spamobox.com',
        ],

        // Risk scores for each factor
        'scores' => [
            'multiple_ip_accounts' => 35,
            'rapid_posting' => 25,
            'repeated_images' => 20,
            'temp_email' => 30,
            'no_phone_verify' => 15,
            'new_account' => 10,
        ],

        // Risk score threshold to auto-restrict posting
        'auto_restrict_threshold' => env('FRAUD_AUTO_RESTRICT', 70),

        // Risk score threshold to require manual verification
        'manual_verify_threshold' => env('FRAUD_MANUAL_VERIFY', 50),
    ],

    /*
    |--------------------------------------------------------------------------
    | Duplicate Property Detection Settings
    |--------------------------------------------------------------------------
    */
    'duplicate_detection' => [
        // Overall similarity threshold to flag as duplicate (0-100)
        'similarity_threshold' => env('FRAUD_DUPLICATE_THRESHOLD', 85),

        // Weighted scoring system
        'weights' => [
            'image_similarity' => 0.40,  // 40%
            'location_similarity' => 0.30,  // 30%
            'title_similarity' => 0.15,  // 15%
            'price_similarity' => 0.15,  // 15%
        ],

        // Location: maximum distance in meters to consider same location
        'max_distance_meters' => env('FRAUD_MAX_DISTANCE', 20),

        // Price: percentage tolerance for same price range
        'price_tolerance_pct' => env('FRAUD_PRICE_TOLERANCE', 10),

        // Title: minimum Levenshtein similarity ratio (0-1)
        'min_title_similarity' => env('FRAUD_TITLE_MIN_SIM', 0.70),

        // How many days back to search for duplicates
        'search_window_days' => env('FRAUD_SEARCH_WINDOW', 90),
    ],

    /*
    |--------------------------------------------------------------------------
    | Overall Fraud Score Settings
    |--------------------------------------------------------------------------
    */
    'scoring' => [
        // Listing is auto-flagged above this score
        'flag_threshold' => env('FRAUD_FLAG_THRESHOLD', 50),

        // Listing is auto-blocked above this score
        'block_threshold' => env('FRAUD_BLOCK_THRESHOLD', 80),

        // Listing requires admin review above this score
        'review_threshold' => env('FRAUD_REVIEW_THRESHOLD', 40),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    */
    'queue' => [
        // Queue to use for fraud detection jobs
        'connection' => env('FRAUD_QUEUE_CONNECTION', 'database'),
        'queue_name' => env('FRAUD_QUEUE_NAME', 'fraud-detection'),

        // Job retry attempts
        'tries' => 3,

        // Timeout in seconds
        'timeout' => 120,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration  
    |--------------------------------------------------------------------------
    */
    'logging' => [
        // Log channel for fraud detection events
        'channel' => env('FRAUD_LOG_CHANNEL', 'daily'),

        // Log level: 'debug', 'info', 'warning', 'error'
        'level' => env('FRAUD_LOG_LEVEL', 'info'),

        // Enable detailed debug logging
        'debug' => env('FRAUD_DEBUG', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        // Admin email to notify on high-risk fraud
        'admin_email' => env('FRAUD_ADMIN_EMAIL', ''),

        // Notify on each fraud event above this score
        'notify_threshold' => 70,

        // Enable real-time notifications
        'realtime' => env('FRAUD_REALTIME_NOTIFY', true),
    ],
];
