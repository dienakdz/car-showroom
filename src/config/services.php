<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'car_search' => [
        'url' => env('CAR_SEARCH_URL', 'http://car-search:8000'),
        'token' => env('CAR_SEARCH_API_TOKEN', 'local-car-search-token'),
        'connect_timeout' => (int) env('CAR_SEARCH_CONNECT_TIMEOUT', 1),
        'timeout' => (int) env('CAR_SEARCH_TIMEOUT', 3),
        'result_limit' => (int) env('CAR_SEARCH_RESULT_LIMIT', 100),
    ],

];
