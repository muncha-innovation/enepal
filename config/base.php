<?php

/*
 * The default file system disk used is 'Public'.
 * Any path unless specified is relative to 'storage/app/public'.
 */

return [

    /**
    |--------------------------------------------------------------------------
    | Default file upload configurations
    |--------------------------------------------------------------------------
     */

    'uploads' => [
        'image' => [
            'encode' => 'jpg',
            'allowed_mime' => ['jpeg', 'png', 'bmp'],
        ],
    ],

    /**
    |--------------------------------------------------------------------------
    | Image aspect ratio configurations
    |--------------------------------------------------------------------------
     */

    'aspect_ratios' => [
        '16:9' => [
            'dimensions' => [16, 9],
            'use_case' => 'Primary images, cover images, banners',
            'recommended_size' => '1920x1080',
            'description' => 'Widescreen format ideal for hero banners and cover images'
        ],
        '1:1' => [
            'dimensions' => [1, 1],
            'use_case' => 'Thumbnails, profile pictures, listings',
            'recommended_size' => '400x400',
            'description' => 'Square format perfect for profile pictures and thumbnails'
        ],
        '4:3' => [
            'dimensions' => [4, 3],
            'use_case' => 'Traditional photography, card images',
            'recommended_size' => '800x600',
            'description' => 'Traditional format for standard photography'
        ],
        '3:2' => [
            'dimensions' => [3, 2],
            'use_case' => 'Professional photography, article images',
            'recommended_size' => '900x600',
            'description' => 'Professional photography format close to golden ratio'
        ],
    ],

    /**
    |--------------------------------------------------------------------------
    | Image type configurations
    |--------------------------------------------------------------------------
     */

    'image_types' => [
        'primary' => [
            'aspect_ratio' => '16:9',
            'use_case' => 'Primary images, cover images, banners',
            'max_size_mb' => 2,
        ],
        'listing' => [
            'aspect_ratio' => '1:1',
            'use_case' => 'Thumbnails, profile pictures, listings',
            'max_size_mb' => 1,
        ],
        'card' => [
            'aspect_ratio' => '4:3',
            'use_case' => 'Card images, featured content',
            'max_size_mb' => 2,
        ],
        'profile' => [
            'aspect_ratio' => '1:1',
            'use_case' => 'Profile pictures and avatars',
            'max_size_mb' => 1,
        ],
    ],

    /**
    |--------------------------------------------------------------------------
    | User specific configurations
    |--------------------------------------------------------------------------
     */

    'user' => [
        'upload' => [
            'profile-picture' => [
                'path' => 'uploads/user/profile-picture/',
                'image' => [
                    'min_resolution' => 100,
                    'store_resolution' => 150,
                    'max_file_size_kb' => 5000,
                ],
            ],
        ],
    ],

/**
    |--------------------------------------------------------------------------
    | FrontPage images specific configurations
    |--------------------------------------------------------------------------
     */

    'cms' => [
        'upload' => [
            'web-picture' => [
                'path' => 'uploadwebfrontfiles/',
                'image' => [
                    'min_resolution' => 100,
                    'store_resolution' => 150,
                    'max_file_size_kb' => 50000,
                ],

            ],
        ],
    ],

    

    /**
    |--------------------------------------------------------------------------
    | Sytem-Admin specific configurations
    |--------------------------------------------------------------------------
     */

    'system-admin' => [
        'upload' => [
            'logo' => [
                'path' => 'uploads/system-admin/logo/',
                'image' => [
                    'min_resolution' => 1000,
                    'store_resolution' => 150,
                    'max_file_size_kb' => 500,
                ],
            ],
        ],
    ],

    /**
    |--------------------------------------------------------------------------
    | User specific configurations
    |--------------------------------------------------------------------------
     */

    'types' => [
        'upload' => [
            'images' => [
                'path' => 'uploads/types/images/',
                'image' => [
                    'min_resolution' => 100,
                    'store_resolution' => 150,
                    'max_file_size_kb' => 500,
                ],
            ],
        ],
    ],
    /**
    |--------------------------------------------------------------------------
    | APP BUILD specific configurations
    |--------------------------------------------------------------------------
     */

    'app-build' => [
        'upload' => [
            'ios-builds' => [
                'ipa'=>[
                'path' => 'uploads/builds/ios/ipa/',
                ],
                'plist'=>[
                    'path'=>'uploads/builds/ios/plist/'
                ]
            ],
            'android-builds' => [
                'apk'=>[
                'path' => 'uploads/builds/android/apk',
                ],
            ],
        ],
    ],
    /**
    |--------------------------------------------------------------------------
    | Companies specific configurations
    |--------------------------------------------------------------------------
     */

    'company' => [
        'upload' => [
            'images' => [
                'path' => 'uploads/company/icons/',
                'image' => [
                    'min_resolution' => 100,
                    'store_resolution' => 150,
                    'max_file_size_kb' => 500,
                ],
            ],
        ],
    ],

    /**
    * |--------------------------------------------------------------------------
    * | Owner specific configurations
    * |--------------------------------------------------------------------------
    */

    'owner' => [
        'upload' => [
            'profile-picture' => [
                'path' => 'uploads/owner/profile-picture/',
                'image' => [
                    'min_resolution' => 100,
                    'store_resolution' => 150,
                    'max_file_size_kb' => 500,
                ],
            ],
                'documents' => [
                'path' => 'uploads/owner/documents/',
                'image' => [
                    'min_resolution' => 100,
                    'store_resolution' => 1250,
                    'max_file_size_kb' => 10000,
                ],
            ],
        ],
    ],
    /**
    *|--------------------------------------------------------------------------
    *| Country specific configurations
    *|--------------------------------------------------------------------------
    */
    'country' => [
        'upload' => [
            'flag' => [
                'path' => 'images/country/flags/',
                'image' => [
                    'min_resolution' => 100,
                    'store_resolution' => 150,
                    'max_file_size_kb' => 500,
                ],
            ],
        ],
    ],

     /**
    *|--------------------------------------------------------------------------
    *| Fleet Vehicle specific configurations
    *|--------------------------------------------------------------------------
    */

    'fleets' => [
        'upload' => [
            'images' => [
                'path' => 'uploads/fleets/images/',
                'image' => [
                    'min_resolution' => 100,
                    'store_resolution' => 150,
                    'max_file_size_kb' => 500,
                ],
            ],
        ],
    ],


    /**
    *|--------------------------------------------------------------------------
    *| Push Notification configurations
    *|--------------------------------------------------------------------------
    */
    'pushnotification' => [
        'upload' => [
            'images' => [
                'path' => 'uploads/push-notification/images/',
                'image' => [
                    'min_resolution' => 100,
                    'store_resolution' => 1250,
                    'max_file_size_kb' => 500,
                ],
            ],
        ],
    ],


    /**
    |--------------------------------------------------------------------------
    | Default common configurations
    |--------------------------------------------------------------------------
     */

    'default' => [
        /*
                     * The paths are relative to the public folder 'public'.
        */
        'user' => [
            'profile_picture' => '/assets/images/default-profile-picture.jpeg',
        ],

    ],

    'pdf' => [
        'generator' => 'dompdf.wrapper',
    ],

    /**
    |--------------------------------------------------------------------------
    | Web/App configurations
    |--------------------------------------------------------------------------
     */

    'web' => [
        'verification' => [
            'google' => env('GOOGLE_VERIFICATION_KEY'),
            'bing' => env('BING_VERIFICATION_KEY'),
        ],

        'links' => [
            'facebook' => env('FACEBOOK_LINK'),
            'twitter' => env('TWITTER_LINK'),
            'instagram' => env('INSTAGRAM_LINK'),
            'google_plus' => env('GOOGLEPLUS_LINK'),
            'linkedin' => env('LINKEDIN_LINK'),
        ],
    ],

    

];
