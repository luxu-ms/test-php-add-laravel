<?php

declare(strict_types=1);

return [
    'default' => 'file',
    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache'),
            'lock_path' => storage_path('framework/cache'),
        ],
    ],
    'prefix' => 'taskboard_cache_',
    'serializable_classes' => false,
];
