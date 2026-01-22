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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'chpter' => [
        'client_domain' => env('CLIENT_DOMAIN'),
        'chpter_token' => env('CHPTER_TOKEN'),

        'callback_url' => env('CHPTER_CALLBACK_FRONT_CALLBACK_URL'),
        'success_url' => env('CHPTER_CALLBACK_FRONT_SUCCESS_URL'),
        'failed_url' => env('CHPTER_CALLBACK_FRONT_FAILED_URL'),

        'callback_back_url' => env('CHPTER_CALLBACK_BACK_CALLBACK_URL'),
        'success_back_url' => env('CHPTER_CALLBACK_BACK_FAILED_URL'),
        'failed_back_url' => env('CHPTER_FAILED_URL'),
    ],
    'currency' => [
        'kes' => 1500,
        'name' =>'KES',
    ],

    'retes'=> [
        'rate' => env('RATE'),
        'min_amount' => env('MIN_AMOUNT'),
    ]

];

