<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Active AI Provider
    |--------------------------------------------------------------------------
    | Supported: "openai", "anthropic", "google"
    */
    'active_provider' => env('AI_PROVIDER', 'openai'),

    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'base_url' => 'https://api.openai.com/v1',
            'models' => [
                'chat' => env('OPENAI_CHAT_MODEL', 'gpt-4o'),
                'embedding' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small'),
                'image' => env('OPENAI_IMAGE_MODEL', 'dall-e-3'),
                'vision' => env('OPENAI_VISION_MODEL', 'gpt-4o'),
            ],
            'timeout' => 60,
            'max_retries' => 3,
        ],

        'anthropic' => [
            'api_key' => env('ANTHROPIC_API_KEY'),
            'base_url' => 'https://api.anthropic.com/v1',
            'models' => [
                'chat' => env('ANTHROPIC_CHAT_MODEL', 'claude-3-5-sonnet-20241022'),
            ],
            'timeout' => 60,
        ],

        'google' => [
            'api_key' => env('GOOGLE_AI_API_KEY'),
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'models' => [
                'chat' => env('GOOGLE_CHAT_MODEL', 'gemini-flash-latest'),
            ],
            'vision_api_key' => env('GOOGLE_VISION_API_KEY'),
            'vision_url' => 'https://vision.googleapis.com/v1/images:annotate',
        ],

        'azure_vision' => [
            'api_key' => env('AZURE_VISION_API_KEY'),
            'endpoint' => env('AZURE_VISION_ENDPOINT'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Price Prediction Microservice
    |--------------------------------------------------------------------------
    */
    'price_prediction' => [
        'url' => env('PRICE_PREDICTION_URL', 'http://localhost:8001'),
        'api_key' => env('PRICE_PREDICTION_API_KEY'),
        'timeout' => 30,
        'fallback_to_ai' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Smart Search (NLP → Filters)
    |--------------------------------------------------------------------------
    */
    'smart_search' => [
        'enabled' => env('AI_SMART_SEARCH_ENABLED', true),
        'cache_ttl' => 900,   // 15 min
        'price_percentiles' => [40, 60], // medium = 40th–60th percentile
        'temperature' => 0.0,
        'max_tokens' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Recommendation Engine
    |--------------------------------------------------------------------------
    */
    'recommendations' => [
        'top_n' => 5,
        'cache_ttl' => 3600,      // 1 hr
        'weights' => [
            'price' => 0.40,
            'location' => 0.30,
            'bedrooms' => 0.15,
            'behavior' => 0.15,
        ],
        'recalc_job_frequency' => 'hourly',
    ],

    /*
    |--------------------------------------------------------------------------
    | Chatbot (Real Estate Advisor)
    |--------------------------------------------------------------------------
    */
    'chatbot' => [
        'session_ttl' => 1800,   // 30 min
        'max_history' => 10,     // messages kept in context
        'max_tokens' => 700,
        'temperature' => 0.4,
        'max_suggestions' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Analysis
    |--------------------------------------------------------------------------
    */
    'image_analysis' => [
        'provider' => env('IMAGE_ANALYSIS_PROVIDER', 'google'),
        'queue' => 'image-analysis',
        'min_quality' => 40,       // below this: flag as low quality
        'cache_ttl' => 86400,    // 24 hr
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting & Cost Control
    |--------------------------------------------------------------------------
    */
    'rate_limits' => [
        'search_per_minute' => 20,
        'chat_per_minute' => 10,
        'image_analysis_per_hour' => 100,
        'price_prediction_per_hour' => 200,
    ],

    'cost_tracking' => [
        // USD per 1k tokens (OpenAI gpt-4o)
        'token_costs' => [
            'gpt-4o' => ['input' => 0.005, 'output' => 0.015],
            'gpt-4o-mini' => ['input' => 0.00015, 'output' => 0.0006],
            'text-embedding-3-small' => ['input' => 0.00002, 'output' => 0],
        ],
        'monthly_budget_usd' => env('AI_MONTHLY_BUDGET', 500),
        'alert_threshold' => 0.80, // alert at 80% of budget
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */
    'security' => [
        'prompt_injection_patterns' => [
            'ignore previous instructions',
            'forget your instructions',
            'you are now',
            'new persona',
            'jailbreak',
            'DAN mode',
            'developer mode',
        ],
        'max_input_length' => 1000,
        'log_all_requests' => env('AI_LOG_REQUESTS', true),
    ],

];
