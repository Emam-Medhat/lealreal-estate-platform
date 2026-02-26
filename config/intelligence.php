<?php

return [
    'investment' => [
        'weights' => [
            'roi' => env('INVESTMENT_WEIGHT_ROI', 0.35),
            'cash_flow' => env('INVESTMENT_WEIGHT_CASH_FLOW', 0.2),
            'liquidity' => env('INVESTMENT_WEIGHT_LIQUIDITY', 0.15),
            'risk' => env('INVESTMENT_WEIGHT_RISK', 0.15), // lower is better
            'demand' => env('INVESTMENT_WEIGHT_DEMAND', 0.15),
        ],
        'scales' => [
            'roi_max' => 20, // %
            'cash_flow_max' => 10000, // per month
            'liquidity_max_days' => 180,
            'risk_max' => 100,
            'demand_max' => 100,
        ],
    ],

    'fraud' => [
        'thresholds' => [
            'restrict' => env('FRAUD_RESTRICT_THRESHOLD', 80),
            'review' => env('FRAUD_REVIEW_THRESHOLD', 60),
        ],
        'weights' => [
            'duplicate_images' => 30,
            'unrealistic_pricing' => 25,
            'suspicious_account' => 25,
            'duplicate_listing' => 20,
        ],
        'image_hash' => [
            'hash_size' => 8,
            'similarity_threshold' => 0.9, // cosine/Hamming similarity
        ],
        'pricing' => [
            'zscore_threshold' => 3.0,
        ],
        'account' => [
            'ip_velocity_per_hour' => 20,
            'distinct_devices_threshold' => 5,
        ],
    ],

    'pricing' => [
        'overpriced_percent' => 30,
        'regression' => [
            'alpha' => 0.001,
            'iterations' => 5000,
        ],
        'forecast' => [
            'months' => [6, 12],
        ],
        'ml' => [
            'enabled' => env('INTEL_PRICE_ML_ENABLED', false),
            'url' => env('INTEL_PRICE_ML_URL', null),
            'timeout' => env('INTEL_PRICE_ML_TIMEOUT', 3),
        ],
    ],
    'realtime' => [
        'enabled' => env('INTEL_REALTIME_ENABLED', false),
        'channel_prefix' => env('INTEL_ALERT_CHANNEL_PREFIX', 'alerts.user.'),
    ],

    'recommendations' => [
        'weights' => [
            'clicks' => 0.35,
            'time_spent' => 0.25,
            'search_match' => 0.2,
            'favorites' => 0.2,
        ],
        'decay_days' => 30,
    ],

    'maps' => [
        'grid' => [
            'lat_step' => 0.05,
            'lng_step' => 0.05,
        ],
    ],
];
