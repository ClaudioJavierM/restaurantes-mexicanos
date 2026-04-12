<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Promoción activa en modal de planes
    |--------------------------------------------------------------------------
    | active: true/false — mostrar o no el banner
    | code: código de cupón Stripe
    | label: texto del badge (ej: "Black Friday", "Oferta Enero")
    | message: texto del banner
    | expires_at: fecha de expiración ISO (null = sin límite)
    | applies_to: 'premium'|'elite'|'both'
    */
    'active'      => env('FAMER_PROMO_ACTIVE', false),
    'code'        => env('FAMER_PROMO_CODE', ''),
    'label'       => env('FAMER_PROMO_LABEL', 'Oferta Especial'),
    'message'     => env('FAMER_PROMO_MESSAGE', ''),
    'expires_at'  => env('FAMER_PROMO_EXPIRES_AT', null),
    'applies_to'  => env('FAMER_PROMO_APPLIES_TO', 'both'), // 'premium'|'elite'|'both'
];
