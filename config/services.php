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

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],

    'onlinepay' => [
        'paymob_key' => env('PAYMOB_API_KEY'),
        'fawry_key' => env('FAWRY_API_KEY'),
    ],

    'whatsapp' => [
        'enabled' => env('WHATSAPP_ENABLED', false),
        'provider' => env('WHATSAPP_PROVIDER', 'meta'), // meta | callmebot | gateway | evolution
        'token' => env('WHATSAPP_TOKEN'),
        'phone_id' => env('WHATSAPP_PHONE_ID'),
        'callmebot_apikey' => env('WHATSAPP_CALLMEBOT_KEY'), // مفتاح افتراضي (CallMeBot مجاني)
        'gateway_url' => env('WHATSAPP_GATEWAY_URL', 'http://127.0.0.1:3100'),
        'gateway_key' => env('WHATSAPP_GATEWAY_KEY'),
        'evolution_url' => env('WHATSAPP_EVOLUTION_URL', 'http://localhost:8080'),
        'evolution_instance' => env('WHATSAPP_EVOLUTION_INSTANCE', 'lms'),
        'evolution_key' => env('WHATSAPP_EVOLUTION_KEY'),
    ],

];
