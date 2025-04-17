<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard Settings
    |--------------------------------------------------------------------------
    |
    | You can configure the dashboard settings from here.
    |
    */
    'dashboard' => [
        'port' => env('LARAVEL_WEBSOCKETS_PORT', 6001),
        'domain' => env('LARAVEL_WEBSOCKETS_DOMAIN'),
        'path' => env('LARAVEL_WEBSOCKETS_PATH', 'laravel-websockets'),
        'middleware' => [
            'web',
            \App\Http\Middleware\Authenticate::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Clients
    |--------------------------------------------------------------------------
    |
    | Here you can define the clients that can access the websocket server.
    | By default, this is the Pusher configuration from your .env file.
    |
    */
    'clients' => [
        [
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'encrypted' => true,
                'host' => '127.0.0.1',
                'port' => 6001,
                'scheme' => 'http',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Broadcasting Replication
    |--------------------------------------------------------------------------
    |
    | You can enable broadcasting event replication if you're running multiple
    | servers or processes.
    |
    */
    'replication' => [
        'mode' => env('WEBSOCKETS_REPLICATION_MODE', 'local'),
        'modes' => [
            'local' => [
                /*
                 * When using the local driver, events will only be replicated to other processes
                 * on the same server.
                 */
            ],
            'redis' => [
                'connection' => env('WEBSOCKETS_REDIS_REPLICATION_CONNECTION', 'default'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Statistics Settings
    |--------------------------------------------------------------------------
    |
    | Statistics collection can be enabled to see usage data on your dashboard.
    |
    */
    'statistics' => [
        'enabled' => env('WEBSOCKETS_STATISTICS_ENABLED', false),
        'interval_in_seconds' => env('WEBSOCKETS_STATISTICS_INTERVAL', 60),
        'delete_statistics_older_than_days' => env('WEBSOCKETS_STATISTICS_DAYS', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maximum Request Size in Kilobytes
    |--------------------------------------------------------------------------
    */
    'max_request_size_in_kb' => env('LARAVEL_WEBSOCKETS_MAX_REQUEST_SIZE_IN_KB', 250),
];