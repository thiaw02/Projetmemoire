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

    'wave' => [
        'api_key' => env('WAVE_API_KEY'),
        'merchant_id' => env('WAVE_MERCHANT_ID'),
        'webhook_secret' => env('WAVE_WEBHOOK_SECRET'),
    ],

    'orangemoney' => [
        'api_key' => env('OM_API_KEY'),
        'merchant_id' => env('OM_MERCHANT_ID'),
        'secret' => env('OM_SECRET'),
        'webhook_secret' => env('OM_WEBHOOK_SECRET'),
    ],

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

];
