<?php

return [
    'name' => 'LaravelPWA',
    'manifest' => [
        'name' => env('APP_NAME', 'Brutal Budget'),
        'short_name' => 'Brutal',
        'start_url' => '/',
        'background_color' => '#818cf8',
        'theme_color' => '#000000',
        'display' => 'standalone',
        'orientation' => 'any',
        'status_bar' => 'black',
        'icons' => [
            '72x72' => [
                'path' => '/images/icons/icon-72x72.png',
                'purpose' => 'any',
            ],
            '96x96' => [
                'path' => '/images/icons/icon-96x96.png',
                'purpose' => 'any',
            ],
            '128x128' => [
                'path' => '/images/icons/icon-128x128.png',
                'purpose' => 'any',
            ],
            '144x144' => [
                'path' => '/images/icons/icon-144x144.png',
                'purpose' => 'any',
            ],
            '152x152' => [
                'path' => '/images/icons/icon-152x152.png',
                'purpose' => 'any',
            ],
            '192x192' => [
                'path' => '/images/icons/icon-192x192.png',
                'purpose' => 'any',
            ],
            '384x384' => [
                'path' => '/images/icons/icon-384x384.png',
                'purpose' => 'any',
            ],
            '512x512' => [
                'path' => '/images/icons/icon-512x512.png',
                'purpose' => 'any',
            ],
        ],
        'splash' => [
            '640x1136' => '/images/icons/apple-splash-640-1136.jpg',
            '750x1334' => '/images/icons/apple-splash-750-1334.jpg',
            '828x1792' => '/images/icons/apple-splash-828-1792.jpg',
            '1125x2436' => '/images/icons/apple-splash-1125-2436.jpg',
            '1242x2208' => '/images/icons/apple-splash-1242-2208.jpg',
            '1242x2688' => '/images/icons/apple-splash-1242-2688.jpg',
            '1536x2048' => '/images/icons/apple-splash-1536-2048.jpg',
            '1668x2224' => '/images/icons/apple-splash-1668-2224.jpg',
            '1668x2388' => '/images/icons/apple-splash-1668-2388.jpg',
            '2048x2732' => '/images/icons/apple-splash-2048-2732.jpg',
        ],
        'shortcuts' => [
            [
                'name' => 'Dashboard',
                'description' => 'High level view of your expenses',
                'url' => '/dashboard',
                'icons' => [
                    'src' => '/images/icons/icon-72x72.png',
                    'purpose' => 'any',
                ],
            ],
            [
                'name' => 'Expenses',
                'description' => 'See all your expenses in details',
                'url' => '/expenses',
            ],
        ],
        'custom' => [],
    ],
];
