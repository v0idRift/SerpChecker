<?php

return [
    'base_url' => env('SERP_API_BASE_URL', 'https://api.dataforseo.com/v3'),

    'login' => env('SERP_API_LOGIN'),
    'password' => env('SERP_API_PASSWORD'),

    'timeout' => (int) env('SERP_API_TIMEOUT', 90),
    'connect_timeout' => (int) env('SERP_API_CONNECT_TIMEOUT', 10),

    'retry' => [
        'times' => (int) env('SERP_API_RETRY_TIMES', 2),
        'sleep_ms' => (int) env('SERP_API_RETRY_SLEEP_MS', 250),
    ],

    'google' => [
        'device' => env('SERP_DEVICE', 'desktop'),
        'depth' => (int) env('SERP_DEPTH', 100),

        // Optional: domain override
        'se_domain' => env('SERP_SE_DOMAIN'),
    ],
];
