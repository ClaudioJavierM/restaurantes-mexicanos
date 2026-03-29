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

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'google' => [
        // OAuth for Social Login
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
        // Places & Maps API
        'places_api_key' => env('GOOGLE_PLACES_API_KEY'),
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY', env('GOOGLE_PLACES_API_KEY')),
        // Vision API for image moderation (uses same key by default)
        'vision_api_key' => env('GOOGLE_VISION_API_KEY', env('GOOGLE_PLACES_API_KEY')),
        'analytics' => [
            'en' => env('GOOGLE_ANALYTICS_EN'), // famousmexicanrestaurants.com
            'es' => env('GOOGLE_ANALYTICS_ES'), // restaurantesmexicanosfamosos.com
        ],
        // API Usage Limits to stay within free tier ($200/month)
        'daily_request_limit' => env('GOOGLE_DAILY_REQUEST_LIMIT', 200), // ~200 requests/day = 6000/month
        'monthly_budget_limit' => env('GOOGLE_MONTHLY_BUDGET_LIMIT', 180), // $180 to be safe (20% buffer)
        'alert_threshold' => env('GOOGLE_ALERT_THRESHOLD', 150), // Alert at $150 (75% of budget)
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI', '/auth/facebook/callback'),
    ],

    'yelp' => [
        'client_id'     => env('YELP_CLIENT_ID'),
        'api_key'       => env('YELP_API_KEY'),
        'monthly_limit' => env('YELP_MONTHLY_LIMIT', 5000),
        'api_keys' => array_values(array_filter([
            env('YELP_API_KEY'),
            env('YELP_API_KEY_MEXICO'),
            env('YELP_API_KEY_US_2'),
            env('YELP_API_KEY_US_3'),
            env('YELP_API_KEY_US_4'),
        ])),
    ],

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'from' => env('TWILIO_PHONE', env('TWILIO_FROM')),
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),
        'messaging_service_sid' => env('TWILIO_MESSAGING_SERVICE_SID'),
    ],

    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    ],

    'elevenlabs' => [
        'api_key' => env('ELEVENLABS_API_KEY'),
        'voice_id' => env('ELEVENLABS_VOICE_ID', 'XB0fDUnXU5powFXDhCwa'), // Charlotte multilingual
        'model_id' => env('ELEVENLABS_MODEL_ID', 'eleven_multilingual_v2'),
    ],

    'n8n' => [
        'webhook_url' => env('N8N_WEBHOOK_URL'),
        'api_key' => env('N8N_API_KEY'),
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],

];
