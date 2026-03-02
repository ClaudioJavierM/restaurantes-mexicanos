<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe API Keys
    |--------------------------------------------------------------------------
    |
    | The Stripe publishable key and secret key give you access to Stripe's
    | API. The "public" key is used for client-side operations while the
    | "secret" key is used for server-side operations.
    |
    */

    'key' => env('STRIPE_KEY'),

    'secret' => env('STRIPE_SECRET'),

    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Stripe Price IDs
    |--------------------------------------------------------------------------
    |
    | These are the Price IDs created in your Stripe Dashboard for each
    | subscription tier. You need to create recurring products and prices
    | in Stripe Dashboard first.
    |
    */

    'prices' => [
        'free' => null, // Free plan doesn't need a Stripe price
        'premium' => env('STRIPE_PRICE_PREMIUM'),
        'elite' => env('STRIPE_PRICE_ELITE'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    |
    | Configuration for each subscription plan including pricing and features.
    |
    */

    'plans' => [
        'free' => [
            'name' => 'Gratis',
            'price' => 0,
            'currency' => 'usd',
            'interval' => 'month',
            'features' => [
                'Perfil verificado con badge',
                'Editar información básica',
                'Responder a reseñas',
                'Horarios y contacto',
                'Hasta 5 fotos',
            ],
        ],
        'premium' => [
            'name' => 'Premium',
            'price' => 29.00,
            'currency' => 'usd',
            'interval' => 'month',
            'features' => [
                'Todo en Gratis +',
                '⭐ Insignia Destacada',
                '📊 Analytics Completos',
                '📸 Hasta 25 fotos',
                '🍽️ Menú Digital + QR Code',
                '🛒 Widget de Pedidos Online',
                '📅 Sistema de Reservas',
                '🤖 Chatbot IA Bilingüe',
                '📧 Email Marketing Básico',
            ],
        ],
        'elite' => [
            'name' => 'Elite',
            'price' => 79.00,
            'currency' => 'usd',
            'interval' => 'month',
            'features' => [
                'Todo en Premium +',
                '🏆 Posición #1 en tu Ciudad',
                '📱 App Móvil Marca Blanca',
                '🌐 Sitio Web Completo',
                '📸 Fotografía Profesional',
                '👨‍💼 Gerente de Cuenta Dedicado',
                '🔌 Integración POS',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe API Version
    |--------------------------------------------------------------------------
    |
    | The Stripe API version to use. This is set automatically by the SDK
    | but you can override it here if needed.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | MF Imports Integration Coupons
    |--------------------------------------------------------------------------
    */
    'coupon_3_months' => env('STRIPE_COUPON_3_MONTHS'),
    'coupon_6_months' => env('STRIPE_COUPON_6_MONTHS'),

    'api_version' => '2024-11-20.acacia',

];
