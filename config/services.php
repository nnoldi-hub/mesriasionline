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

    /*
    |--------------------------------------------------------------------------
    | Google Calendar Integration
    |--------------------------------------------------------------------------
    */
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'calendar_redirect_uri' => env('GOOGLE_CALENDAR_REDIRECT_URI', env('APP_URL') . '/craftsman/calendar/google/callback'),
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
        'places_api_key' => env('GOOGLE_PLACES_API_KEY', env('GOOGLE_MAPS_API_KEY')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Microsoft Outlook Calendar Integration
    |--------------------------------------------------------------------------
    */
    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'tenant_id' => env('MICROSOFT_TENANT_ID', 'common'),
        'calendar_redirect_uri' => env('MICROSOFT_CALENDAR_REDIRECT_URI', env('APP_URL') . '/craftsman/calendar/outlook/callback'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Service Configuration
    |--------------------------------------------------------------------------
    */
    'sms' => [
        'enabled' => env('SMS_ENABLED', false),
        'provider' => env('SMS_PROVIDER', 'twilio'), // twilio, vonage, netopia
    ],

    /*
    |--------------------------------------------------------------------------
    | Twilio SMS
    |--------------------------------------------------------------------------
    */
    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'from' => env('TWILIO_FROM'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Payment
    |--------------------------------------------------------------------------
    */
    'stripe' => [
        'key'            => env('STRIPE_KEY'),
        'secret'         => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Vonage (Nexmo) SMS
    |--------------------------------------------------------------------------
    */
    'vonage' => [
        'key' => env('VONAGE_API_KEY'),
        'secret' => env('VONAGE_API_SECRET'),
        'from' => env('VONAGE_FROM', 'Meseriasi'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Netopia SMS (Romanian Provider)
    |--------------------------------------------------------------------------
    */
    'netopia' => [
        'api_key' => env('NETOPIA_API_KEY'),
        'sender' => env('NETOPIA_SENDER', 'Meseriasi'),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Business API
    |--------------------------------------------------------------------------
    */
    'whatsapp' => [
        'business_api_key' => env('WHATSAPP_BUSINESS_API_KEY'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN'),
        'webhook_secret' => env('WHATSAPP_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI API
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key'    => env('OPENAI_API_KEY'),
        'model'      => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 500),
    ],

];
