<?php

return [
    /*
    |--------------------------------------------------------------------------
    | File Manager V2: isolated storage layout
    |--------------------------------------------------------------------------
    |
    | Never point these paths at the legacy File Manager or CKFinder folders.
    | `files` is the only user-visible root. Cache and runtime are deliberately
    | siblings so neither can be browsed or deleted through the UI.
    |
    */
    'local' => [
        'files_root' => storage_path('app/public/filemanager_v2/files'),
        'metadata_root' => storage_path('app/public/filemanager_v2/metadata'),
        'cache_root' => storage_path('app/public/filemanager_v2/cache'),
        'runtime_root' => storage_path('app/public/filemanager_v2/runtime'),
    ],

    // Connection credentials and editable V2 preferences are intentionally
    // outside public/storage. The service encrypts secret keys before writing.
    'settings' => [
        'root' => storage_path('app/filemanager_v2/settings'),
    ],

    'default_storage' => env('FILEMANAGER_V2_DEFAULT_STORAGE', 'local'),

    'connections' => [
        'local' => [
            'name' => 'Local storage',
            'short_name' => 'LOCAL',
            'icon' => 'bi-device-hdd',
            'enabled' => true,
            'driver' => 'local',
            // Initial default only. Administrators change the actual limit in
            // Storage & upload settings, per connection.
            'quota_bytes' => 1073741824,
        ],
        'r2' => [
            'name' => 'Cloudflare R2',
            'short_name' => 'R2',
            'icon' => 'bi-cloud',
            'enabled' => env('FILEMANAGER_V2_R2_ENABLED', false),
            'driver' => 'disk',
            'disk' => env('FILEMANAGER_V2_R2_DISK', 's3_r2'),
            'quota_bytes' => 1073741824,
        ],
    ],

    'cache' => [
        // File store is deliberate for the current single-server/Laragon setup.
        // Set FILEMANAGER_V2_CACHE_STORE=redis when the deployment is multi-node.
        'store' => env('FILEMANAGER_V2_CACHE_STORE', 'file'),
        'listing_ttl_seconds' => (int) env('FILEMANAGER_V2_LISTING_TTL', 60),
        'preview_ttl_seconds' => (int) env('FILEMANAGER_V2_PREVIEW_TTL', 86400),
    ],

    'uploads' => [
        'max_file_size' => (int) env('FILEMANAGER_V2_MAX_FILE_SIZE', 1073741824), // 1 GB
        'chunk_size' => (int) env('FILEMANAGER_V2_CHUNK_SIZE', 8388608), // 8 MB
        'chunk_threshold' => (int) env('FILEMANAGER_V2_CHUNK_THRESHOLD', 16777216), // 16 MB
        'max_parallel' => (int) env('FILEMANAGER_V2_MAX_PARALLEL', 3),
        'runtime_ttl_hours' => (int) env('FILEMANAGER_V2_RUNTIME_TTL_HOURS', 24),
        'forbidden_extensions' => [
            'php', 'php3', 'php4', 'php5', 'phtml', 'phar',
            'exe', 'sh', 'bat', 'cmd', 'ps1', 'vbs', 'wsf',
        ],
    ],
];
