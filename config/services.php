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

    /* Set up payment */
    'paystack' => [
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        'payment_url' => 'https://api.paystack.co',
    ],

    'flutterwave' => [
        'public_key' => env('FLW_PUBLIC_KEY'),
        'secret_key' => env('FLW_SECRET_KEY'),
        'encryption_key' => env('FLW_ENCRYPTION_KEY'),
    ],

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

    'exchange_rate' => [
        'key' => env('EXCHANGE_RATE_API_KEY'),
    ],

    'ipapi' => [
        'enabled' => env('IPAPI_ENABLED', true),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'dhl' => [
        'base_url' => env('DHL_BASE_URL', 'https://express.api.dhl.com/mydhlapi/test/'),
        'api_username' => env('DHL_API_USERNAME'),
        'api_password' => env('DHL_API_PASSWORD'),
        'account_number' => env('DHL_ACCOUNT_NUMBER'),

        'origin' => [
            'address' => '22 Latifat Salami Street',
            'city' => 'Lagos',
            'state' => 'Lagos',
            'country' => 'Nigeria',
            'country_code' => 'NG',
            'postal_code' => '100001',
        ],
    ],

];
