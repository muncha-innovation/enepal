<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chat Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the chat messaging system.
    |
    */

    // Default status for new threads
    'default_thread_status' => 'open',
    
    // Thread statuses
    'thread_statuses' => [
        'open' => 'Open',
        'closed' => 'Closed',
    ],
    
    // Attachment settings
    'attachments' => [
        'max_size' => 10240, // 10MB in KB
        'allowed_types' => [
            'image/jpeg', 
            'image/png', 
            'image/gif',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
        ],
        'storage_disk' => 'public',
        'storage_path' => 'chat_attachments',
    ],
    
    // Message settings
    'messages' => [
        'per_page' => 25,
    ],
    
    // Real-time settings
    'realtime' => [
        'enabled' => true,
        'driver' => env('CHAT_BROADCAST_DRIVER', env('BROADCAST_DRIVER', 'pusher')),
    ],
];