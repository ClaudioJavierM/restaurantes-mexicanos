<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta --}}
    @php
        $seoTitle = $restaurant->name . ' — ' . $restaurant->city . ', ' . ($restaurant->state?->name ?? '');
        $seoDesc = Str::limit(strip_tags($restaurant->description), 160);
        $seoUrl = url('/sitio/' . $restaurant->slug);
        $seoImage = $restaurant->image
            ? (str_starts_with($restaurant->image, 'http') ? $restaurant->image : asset('storage/' . $restaurant->image))
            : asset('images/restaurant-placeholder.jpg');
    @endphp
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDesc }}">
    <link rel="canonical" href="{{ $seoUrl }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="restaurant">
    <meta property="og:title" content="{{ $restaurant->name }} - Mexican Food in {{ $restaurant->city }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($restaurant->description), 200) }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="es_MX">
    <meta property="og:site_name" content="Restaurantes Mexicanos Famosos">

    {{-- Restaurant-specific OG --}}
    <meta property="restaurant:contact_info:street_address" content="{{ $restaurant->address }}">
    <meta property="restaurant:contact_info:locality" content="{{ $restaurant->city }}">
    @if($restaurant->phone)
        <meta property="restaurant:contact_info:phone_number" content="{{ $restaurant->phone }}">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $restaurant->name }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($restaurant->description), 200) }}">
    <meta name="twitter:image" content="{{ $seoImage }}">

    {{-- Theme --}}
    <meta name="theme-color" content="{{ $branding->primary_color ?? '#D4A54A' }}">

    {{-- Favicon --}}
    @if($branding && $branding->icon_192_url)
        <link rel="icon" type="image/png" href="{{ $branding->icon_192_url }}">
        <link rel="apple-touch-icon" href="{{ $branding->icon_192_url }}">
    @else
        <link rel="icon" href="/favicon.ico">
    @endif

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700&family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Dynamic Branding CSS --}}
    <style>
        :root {
            --brand-primary: {{ $branding->primary_color ?? '#D4A54A' }};
            --brand-secondary: {{ $branding->secondary_color ?? '#B8892E' }};
            --brand-accent: {{ $branding->accent_color ?? '#f59e0b' }};
            --brand-text: {{ $branding->text_color ?? '#1f2937' }};
            --brand-bg: {{ $branding->background_color ?? '#ffffff' }};
        }

        .font-display { font-family: 'Playfair Display', serif; }
        .font-body { font-family: 'Inter', sans-serif; }

        .brand-gradient { background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)); }
        .brand-text { color: var(--brand-primary); }
        .brand-bg { background-color: var(--brand-primary); }
        .brand-border { border-color: var(--brand-primary); }

        /* Smooth scroll */
        html { scroll-behavior: smooth; }

        /* Hero parallax */
        .hero-bg {
            background-attachment: fixed;
            background-position: center;
            background-size: cover;
        }
        @media (max-width: 768px) {
            .hero-bg { background-attachment: scroll; }
        }

        /* Lightbox */
        .lightbox-overlay {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,0.9);
            display: flex; align-items: center; justify-content: center;
        }
    </style>

    {{-- Schema.org JSON-LD --}}
    @php
        $schemaData = [
            'name' => $restaurant->name,
            'description' => Str::limit(strip_tags($restaurant->description), 250),
            'url' => $seoUrl,
            'image' => $seoImage,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $restaurant->address,
                'addressLocality' => $restaurant->city,
                'addressRegion' => $restaurant->state?->code,
                'postalCode' => $restaurant->zip_code,
                'addressCountry' => 'US',
            ],
            'telephone' => $restaurant->phone,
            'priceRange' => $restaurant->price_range,
            'servesCuisine' => 'Mexican',
        ];
        if ($restaurant->average_rating > 0) {
            $schemaData['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => number_format($restaurant->average_rating, 1),
                'reviewCount' => $restaurant->total_reviews ?? 0,
                'bestRating' => '5',
            ];
        }
        if ($restaurant->latitude && $restaurant->longitude) {
            $schemaData['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => $restaurant->latitude,
                'longitude' => $restaurant->longitude,
            ];
        }
    @endphp
    <x-schema-org type="Restaurant" :data="$schemaData" />

    @livewireStyles
</head>
<body class="font-body antialiased" style="color: var(--brand-text); background-color: var(--brand-bg);">
    {{ $slot }}

    {{-- Powered by FAMER --}}
    @if(!($branding && $branding->hide_famer_branding))
        <div class="bg-gray-950 text-center py-3">
            <a href="https://www.restaurantesmexicanosfamosos.com" target="_blank" rel="noopener"
               class="text-xs text-gray-500 hover:text-gray-400 transition">
                {{ $branding->powered_by_text ?? 'Powered by FAMER — Restaurantes Mexicanos Famosos' }}
            </a>
        </div>
    @endif

    @livewireScripts

    {{-- Google Analytics --}}
    @if($branding && $branding->google_analytics_id)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $branding->google_analytics_id }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $branding->google_analytics_id }}');
        </script>
    @endif

    {{-- Facebook Pixel --}}
    @if($branding && $branding->facebook_pixel_id)
        <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{!! $branding->facebook_pixel_id !!}');
            fbq('track', 'PageView');
        </script>
    @endif

    {{-- Lightbox JS --}}
    <script>
        function openLightbox(src) {
            const overlay = document.createElement('div');
            overlay.className = 'lightbox-overlay';
            overlay.onclick = () => overlay.remove();
            overlay.innerHTML = `
                <button onclick="this.parentElement.remove()" class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300">&times;</button>
                <img src="${src}" class="max-w-[90vw] max-h-[90vh] object-contain rounded-lg shadow-2xl" alt="">
            `;
            document.body.appendChild(overlay);
        }
    </script>
</body>
</html>
