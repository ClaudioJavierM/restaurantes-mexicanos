<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Promotion Sites Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración de sitios para promoción cruzada entre empresas del grupo.
    | Cada sitio tiene: nombre, logo, URL, descripción, CTA y color de marca.
    |
    */

    'sites' => [
        'mexartcraft' => [
            'name' => 'Mexican Arts and Crafts',
            'logo' => '/images/logos/mexartcraft-logo.png',
            'url' => 'https://www.mexartcraft.com/',
            'description' => 'Artesanías mexicanas auténticas y decoración tradicional',
            'cta' => 'Ver Artesanías',
            'color' => '#D97706', // Amber
            'active' => true,
        ],

        'muebleyarte' => [
            'name' => 'Mueble y Arte de Tonalá',
            'logo' => '/images/logos/muebleyarte-logo.png',
            'url' => 'https://www.muebleyarte.com/',
            'description' => 'Muebles artesanales de Tonalá, Jalisco con diseños únicos',
            'cta' => 'Explorar Muebles',
            'color' => '#92400E', // Brown
            'active' => true,
        ],

        'refrimex' => [
            'name' => 'Refrimex Paletería',
            'logo' => '/images/logos/refrimex-logo.png',
            'url' => 'https://refrimexpaleteria.com/tromexpro',
            'description' => 'Equipos de refrigeración profesional para paleterías mexicanas',
            'cta' => 'Ver Equipos',
            'color' => '#1E40AF', // Blue
            'active' => true,
        ],

        'tormex' => [
            'name' => 'TorMex Pro',
            'logo' => '/images/logos/tormex-logo.png',
            'url' => 'https://tormexpro.com/',
            'description' => 'Tortilladoras profesionales y maquinaria para tortillerías',
            'cta' => 'Ver Maquinaria',
            'color' => '#EA580C', // Orange
            'active' => true,
        ],

        'mftrailers' => [
            'name' => 'MF Trailers',
            'logo' => '/images/logos/mftrailers-logo.png',
            'url' => 'https://mftrailers.com/',
            'description' => 'Food trucks y remolques personalizados para tu negocio',
            'cta' => 'Ver Trailers',
            'color' => '#DC2626', // Red
            'active' => true,
        ],

        'decorarmex' => [
            'name' => 'DecorarMex',
            'logo' => '/images/logos/decorarmex-logo.png',
            'url' => 'https://decorarmex.com/',
            'description' => 'Platos, decoración y novedades para restaurantes mexicanos',
            'cta' => 'Ver Catálogo',
            'color' => '#C026D3', // Fuchsia
            'active' => true,
        ],

        'mueblesmexicanos' => [
            'name' => 'Muebles Mexicanos',
            'logo' => '/images/logos/mueblesmexicanos-logo.png',
            'url' => 'https://mueblesmexicanos.com',
            'description' => 'Muebles rústicos y coloniales mexicanos para tu hogar',
            'cta' => 'Próximamente',
            'color' => '#78350F', // Brown
            'active' => false, // En construcción
        ],

        'mf-imports' => [
            'name' => 'MF Imports',
            'logo' => '/images/logos/mf-imports-logo.png',
            'url' => 'https://mf-imports.com',
            'description' => 'Mobiliario mexicano premium para restaurantes y negocios',
            'cta' => 'Explorar Catálogo',
            'color' => '#8B1538', // Vino
            'active' => true,
        ],

        'restaurantes' => [
            'name' => 'Restaurantes Mexicanos Famosos',
            'logo' => '/images/logos/rmf-logo.png',
            'url' => 'https://restaurantesmexicanosfamosos.com',
            'description' => 'Directorio de los mejores restaurantes mexicanos en USA',
            'cta' => 'Descubre Restaurantes',
            'color' => '#059669', // Emerald
            'active' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Display Settings
    |--------------------------------------------------------------------------
    */

    'settings' => [
        'max_items' => 3, // Máximo de sitios a mostrar
        'show_inactive' => false, // Mostrar sitios en construcción
        'shuffle' => true, // Mostrar sitios en orden aleatorio
        'exclude_current' => true, // Excluir el sitio actual de la lista
    ],
];
