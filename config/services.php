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

    'bellbank' => [
        'base_url' => env('BELLBANK_BASE_URL', 'https://sandbox-baas-api.bellmfb.com'),
        'consumer_key' => env('BELLBANK_CONSUMER_KEY'),
        'consumer_secret' => env('BELLBANK_CONSUMER_SECRET'),
        'validity_time' => env('BELLBANK_VALIDITY_TIME', 1440), // Default 24 hours in minutes
        'webhook_secret' => env('BELLBANK_WEBHOOK_SECRET'),
        'director_bvn' => env('BELLBANK_DIRECTOR_BVN'),
        'director_firstname' => env('BELLBANK_DIRECTOR_FIRSTNAME'),
        'director_lastname' => env('BELLBANK_DIRECTOR_LASTNAME'),
        'director_date_of_birth' => env('BELLBANK_DIRECTOR_DATE_OF_BIRTH', '1990/01/01'), // Default DOB for director BVN accounts
    ],

];
