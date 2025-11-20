<?php
return [
    'sample' => [
        'enabled' => env('DB_HEALTH_SAMPLE', true),
        'sample_size' => env('DB_HEALTH_SAMPLE_SIZE', 50),
        'min_duration_ms' => env('DB_HEALTH_MIN_DURATION_MS', 5),
    ],
    'report' => [
        'out' => storage_path('app/db-health-report.json'),
    ],
];
