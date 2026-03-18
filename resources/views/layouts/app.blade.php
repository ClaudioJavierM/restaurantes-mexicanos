<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#1F2937">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FAMER">
    <link rel="apple-touch-icon" href="/images/branding/icon.png?v=2">
    <link rel="icon" type="image/png" sizes="192x192" href="/images/branding/icon.png?v=2">

    <!-- SEO: Dynamic Title and Description -->
    <title>@yield('title', ($title ?? __('app.site_name')) . ' - ' . __('app.tagline'))</title>
    <meta name="description" content="@yield('meta_description', __('app.tagline') . ' - Descubre los mejores restaurantes mexicanos auténticos en Estados Unidos')">

    <!-- SEO: Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- SEO: Hreflang Tags for Bilingual Support -->
    <link rel="alternate" hreflang="en" href="https://famousmexicanrestaurants.com{{ request()->path() === '/' ? '' : '/' . request()->path() }}" />
    <link rel="alternate" hreflang="es" href="https://restaurantesmexicanosfamosos.com{{ request()->path() === '/' ? '' : '/' . request()->path() }}" />
    <link rel="alternate" hreflang="x-default" href="https://famousmexicanrestaurants.com{{ request()->path() === '/' ? '' : '/' . request()->path() }}" />

    <!-- Open Graph Locale -->
    <meta property="og:locale" content="{{ app()->getLocale() === 'en' ? 'en_US' : 'es_US' }}" />
    <meta property="og:site_name" content="{{ __('app.site_name') }}" />

    <!-- Dynamic Meta Tags (Open Graph, Twitter Cards) -->
    @stack('meta')

    <!-- Fonts - Elegantes -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800|playfair-display:400,700,900&display=swap" rel="stylesheet" />

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ app()->getLocale() === "en" ? "GTM-NLKPXHKM" : "GTM-M53NLTND" }}');</script>
    <!-- End Google Tag Manager -->

    <!-- Facebook Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
    n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
    document,'script','https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1362912769188775');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=1362912769188775&ev=PageView&noscript=1"/></noscript>
    <!-- End Facebook Pixel Code -->

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --color-gold: #D4A54A;
            --color-gold-light: #E8C67A;
            --color-gold-dark: #B8892E;
            --color-cream: #F5E6C8;
            --color-red: #DC2626;
            --color-green: #166534;
            --color-dark: #1F2937;
            --color-darker: #111827;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }
        .font-display {
            font-family: 'Playfair Display', serif;
        }

        /* Gold gradient text */
        .text-gold-gradient {
            background: linear-gradient(135deg, var(--color-gold-light) 0%, var(--color-gold) 50%, var(--color-gold-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Gold border effect */
        .border-gold {
            border-color: var(--color-gold);
        }

        /* Elegant button shine */
        .btn-gold {
            background: linear-gradient(135deg, var(--color-gold) 0%, var(--color-gold-dark) 100%);
            color: var(--color-darker);
            position: relative;
            overflow: hidden;
        }
        .btn-gold::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        .btn-gold:hover::before {
            left: 100%;
        }

        /* Mexican flag ribbon */
        .mexican-ribbon {
            background: linear-gradient(90deg, var(--color-green) 33.33%, white 33.33%, white 66.66%, var(--color-red) 66.66%);
        }

        /* Dark elegant pattern */
        .pattern-dark {
            background-color: var(--color-darker);
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23D4A54A' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        /* iOS Install Banner Styles */
        .ios-install-banner {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            transform: translateY(100%);
            transition: transform 0.3s ease-out;
            padding: 0 1rem 1rem;
        }

        .ios-install-banner.show {
            transform: translateY(0);
        }

        .ios-banner-content {
            background: linear-gradient(135deg, var(--color-dark) 0%, var(--color-darker) 100%);
            border-radius: 1rem 1rem 0 0;
            padding: 1.25rem;
            box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.3);
            border-top: 3px solid var(--color-gold);
            position: relative;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .ios-install-banner {
                padding: 0 2rem 2rem;
            }

            .ios-banner-content {
                max-width: 32rem;
                margin: 0 auto;
                border-radius: 1rem;
            }
        }
    
        /* Ensure Para Dueños dropdown appears above everything */
        nav [x-show] {
            z-index: 99999 !important;
        }
        /* Hide Alpine elements until initialized */
        [x-cloak] {
            display: none !important;
        }
</style>
        <!-- Google Analytics 4 -->
        @php
            $host = request()->getHost();
            if (str_contains($host, ".com.mx")) {
                $ga4Id = "G-35N2H2RPVW"; // Mexico
            } elseif (str_contains($host, "famousmexican")) {
                $ga4Id = "G-3Y4S0P66Z6"; // USA English
            } else {
                $ga4Id = "G-J6S51PLBZM"; // USA Spanish
            }
        @endphp
        <script data-cfasync="false" async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
        <script data-cfasync="false">
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag("js", new Date());
            gtag("config", "{{ $ga4Id }}");
        </script>
        <!-- Umami Analytics -->
        @php
            $umamiHost = request()->getHost();
            if (str_contains($umamiHost, '.com.mx')) {
                $umamiId = '4a52fdfa-33a3-4264-9259-43a4e843fe23'; // Mexico
            } elseif (str_contains($umamiHost, 'famousmexican')) {
                $umamiId = '5fe98c6a-4370-4c81-8e2f-9ccbaf6f8324'; // Famous MX Restaurants EN
            } else {
                $umamiId = 'f88d7ac0-a825-409f-aa5c-c484d9106e7b'; // USA Spanish
            }
        @endphp
        <script data-cfasync="false">
            // Load Umami directly, bypassing Rocket Loader
            (function() {
                var s = document.createElement("script");
                s.defer = true;
                s.dataset.websiteId = "{{ $umamiId }}";
                s.src = "https://analytics.mefimports.com/script.js";
                document.head.appendChild(s);
            })();
        </script>
    <!-- MF Group Universal Conversion Tracking -->
    <script data-cfasync="false">
    (function() {
        'use strict';

        function trackEvent(eventName, eventData) {
            if (typeof umami !== 'undefined') {
                try { umami.track(eventName, eventData); } catch(e) {}
            }
            if (typeof gtag !== 'undefined') {
                try { gtag('event', eventName, eventData); } catch(e) {}
            }
        }

        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (form && form.tagName === 'FORM') {
                trackEvent('form_submit', {
                    form_id: form.id || '',
                    form_name: form.getAttribute('name') || '',
                    form_action: form.getAttribute('action') || window.location.href
                });
            }
        }, true);

        document.addEventListener('click', function(e) {
            var link = e.target.closest('a[href]');
            if (!link) return;

            var href = link.getAttribute('href') || '';
            var hrefLower = href.toLowerCase();

            if (hrefLower.indexOf('wa.me') !== -1 || hrefLower.indexOf('whatsapp.com') !== -1 || hrefLower.indexOf('api.whatsapp.com') !== -1) {
                trackEvent('whatsapp_click', { whatsapp_url: href });
                return;
            }

            if (hrefLower.indexOf('tel:') === 0) {
                trackEvent('phone_click', { phone_number: href.replace(/^tel:/i, '') });
                return;
            }

            if (hrefLower.indexOf('mailto:') === 0) {
                trackEvent('email_click', { email_address: href.replace(/^mailto:/i, '').split('?')[0] });
                return;
            }

            try {
                var linkUrl = new URL(href, window.location.origin);
                if (linkUrl.hostname && linkUrl.hostname !== window.location.hostname) {
                    trackEvent('external_link', { destination_url: href });
                }
            } catch(e) {}
        }, true);
    })();
    </script>

    <!-- Microsoft Clarity -->
    <script type="text/javascript">
        (function(){
            var clarityIds = {
                'restaurantesmexicanosfamosos.com.mx': 'vhh4lzctxt',
                'restaurantesmexicanosfamosos.com': 'vhh5aptees',
                'famousmexicanrestaurants.com': 'vhh61fn5an'
            };
            var id = clarityIds[window.location.hostname];
            if (id) {
                (function(c,l,a,r,i,t,y){
                    c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
                    t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
                    y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
                })(window, document, "clarity", "script", id);
            }
        })();
    </script>
</head>
<body class="bg-gray-100 antialiased">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ app()->getLocale() === 'en' ? 'GTM-NLKPXHKM' : 'GTM-M53NLTND' }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- Mexican Flag Ribbon Top -->
    <div class="h-1.5 mexican-ribbon"></div>

    <!-- Navigation - Dark & Elegant -->
    <nav class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 shadow-2xl sticky top-0 border-b border-yellow-600/30" style="z-index:9999; overflow:visible;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 overflow-visible">
            <div class="flex justify-between h-20 overflow-visible">
                <div class="flex items-center">
                    <a href="/" class="flex items-center gap-3 group">
                        <picture>
                            <source srcset="/images/branding/logo.webp?v=3" type="image/webp">
                            <img src="/images/branding/logo.png?v=3" alt="FAMER USA" class="h-14 w-auto group-hover:scale-105 transition-all duration-300" style="max-height: 56px;">
                        </picture>
                        <div class="hidden sm:flex flex-col leading-none" style="gap: 2px;">
                            <span class="text-yellow-500 font-bold text-sm tracking-wide" style="line-height: 1;">Restaurantes</span>
                            <span class="text-white font-bold text-sm tracking-wide" style="line-height: 1;">Mexicanos</span>
                            <span class="text-yellow-500 font-bold text-sm tracking-wide" style="line-height: 1;">Famosos</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-1 overflow-visible">
                    <a href="/" class="text-gray-300 hover:text-yellow-500 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition-all">
                        {{ __('app.home') }}
                    </a>
                    <a href="/restaurantes" class="text-gray-300 hover:text-yellow-500 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition-all">
                        {{ __('app.restaurants') }}
                    </a>
                    <a href="/sugerir" class="text-gray-300 hover:text-yellow-500 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition-all">
                        {{ __('app.suggest') }}
                    </a>
                    <!-- Para Dueños Dropdown -->
                    <div class="relative" style="z-index:99999;" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @click.outside="open = false">
                        <button @click="open = !open" class="text-gray-300 hover:text-yellow-500 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition-all flex items-center gap-1">
                            {{ app()->getLocale() === 'en' ? 'For Owners' : 'Para Dueños' }}
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute left-0 top-full w-64 bg-white rounded-xl shadow-2xl border border-gray-100 py-2" style="z-index:99999;">
                            
                            <!-- Add Restaurant -->
                            <a href="/sugerir" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ app()->getLocale() === 'en' ? 'Add Restaurant' : 'Agregar Restaurante' }}</div>
                                    <div class="text-xs text-gray-500">{{ app()->getLocale() === 'en' ? 'List your business' : 'Registra tu negocio' }}</div>
                                </div>
                            </a>
                            
                            <!-- Claim Restaurant -->
                            <a href="/claim" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ app()->getLocale() === 'en' ? 'Claim for Free' : 'Reclamar Gratis' }}</div>
                                    <div class="text-xs text-gray-500">{{ app()->getLocale() === 'en' ? 'Verify your listing' : 'Verifica tu perfil' }}</div>
                                </div>
                            </a>
                            
                            <!-- Owner Dashboard -->
                            <a href="{{ auth()->check() && auth()->user()->restaurants->first() ? url('/owner') : '/for-owners' }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ app()->getLocale() === 'en' ? 'Owner Dashboard' : 'Mi Dashboard' }}</div>
                                    <div class="text-xs text-gray-500">{{ app()->getLocale() === 'en' ? 'Manage your restaurant' : 'Administra tu restaurante' }}</div>
                                </div>
                            </a>
                            
                            <!-- Divider -->
                            <div class="border-t border-gray-100 my-2"></div>
                            
                            
                            <!-- FAMER Grader -->
                            <a href="/grader" class="flex items-center gap-3 px-4 py-3 hover:bg-red-50 transition-colors">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">FAMER Score</div>
                                    <div class="text-xs text-gray-500">Conoce tu puntuacion</div>
                                </div>
                            </a>
                            
<!-- Explore Plans -->
                            <a href="/for-owners" class="flex items-center gap-3 px-4 py-3 hover:bg-amber-50 transition-colors">
                                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ app()->getLocale() === 'en' ? 'Explore Plans' : 'Explorar Planes' }}</div>
                                    <div class="text-xs text-gray-500">{{ app()->getLocale() === 'en' ? 'Free, Premium & Elite' : 'Gratis, Premium y Elite' }}</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <a href="/guia" class="text-gray-300 hover:text-green-400 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition-all flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ app()->getLocale() === 'en' ? 'City Guide' : 'Guía' }}
                    </a>
                    
                    <!-- FAMER Awards Link -->
                    <a href="/famer-awards" class="text-gray-300 hover:text-amber-400 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition-all flex items-center gap-1">
                        <span class="text-amber-500">🏆</span>
                        FAMER Awards
                    </a>

                    <!-- Language Switcher (Desktop) -->
                    <x-language-switcher />

                    <!-- Auth Links (Desktop) -->
                    @auth
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white transition-all">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-yellow-500">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-yellow-600 flex items-center justify-center text-white text-sm font-bold ring-2 ring-yellow-500">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="hidden xl:block max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 top-full mt-1 w-64 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-50">
                                <!-- User info -->
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                </div>

                                <!-- Links -->
                                @php $isOwnerUser = auth()->user()->restaurants()->exists() || auth()->user()->activeTeamMemberships()->exists(); @endphp
                                <a href="{{ $isOwnerUser ? '/owner' : '/dashboard' }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <div class="w-7 h-7 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                    </div>
                                    {{ $isOwnerUser ? 'Mi Panel' : 'Mi Dashboard' }}
                                </a>
                                <a href="/my-favorites" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <div class="w-7 h-7 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    Mis Favoritos
                                </a>
                                <a href="/my-reservations" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    Mis Reservaciones
                                </a>
                                <a href="/my-orders" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <div class="w-7 h-7 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    Mis Pedidos
                                </a>
                                <a href="/my-reviews" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <div class="w-7 h-7 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </div>
                                    Mis Reseñas
                                </a>
                                <a href="/profile" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <div class="w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    Mi Perfil
                                </a>

                                <!-- Separator + Logout -->
                                <div class="border-t border-gray-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <div class="w-7 h-7 bg-red-50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                        </div>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="/login" class="text-gray-300 hover:text-white px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 transition-all">
                            Login
                        </a>
                        <a href="/register" class="bg-gradient-to-r from-red-600 to-red-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:shadow-xl hover:scale-105 transition-all duration-300">
                            Register
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="flex lg:hidden items-center space-x-2">
                    <!-- Language Switcher (Mobile) -->
                    <x-language-switcher />

                    <!-- Hamburger Button -->
                    <button id="mobile-menu-btn" type="button" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-300 hover:text-yellow-500 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-yellow-500">
                        <span class="sr-only">{{ app()->getLocale() === 'en' ? 'Open main menu' : 'Abrir menú principal' }}</span>
                        <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden lg:hidden bg-gray-900 border-t border-gray-700">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="/" class="block text-gray-300 hover:text-yellow-500 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all">
                    {{ __('app.home') }}
                </a>
                <a href="/restaurantes" class="block text-gray-300 hover:text-yellow-500 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all">
                    {{ __('app.restaurants') }}
                </a>
                <a href="/sugerir" class="block text-gray-300 hover:text-yellow-500 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all">
                    {{ __('app.suggest') }}
                </a>
                <!-- Para Dueños Section (Mobile) -->
                <div class="border-t border-gray-700 pt-2 mt-2">
                    <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        {{ app()->getLocale() === 'en' ? 'For Owners' : 'Para Dueños' }}
                    </div>
                    <a href="/sugerir" class="flex items-center gap-3 text-gray-300 hover:text-yellow-500 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ app()->getLocale() === 'en' ? 'Add Restaurant' : 'Agregar Restaurante' }}
                    </a>
                    <a href="/claim" class="flex items-center gap-3 text-gray-300 hover:text-yellow-500 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ app()->getLocale() === 'en' ? 'Claim for Free' : 'Reclamar Gratis' }}
                    </a>
                    <a href="{{ auth()->check() && auth()->user()->restaurants->first() ? url('/owner') : '/for-owners' }}" class="flex items-center gap-3 text-gray-300 hover:text-yellow-500 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ app()->getLocale() === 'en' ? 'My Dashboard' : 'Mi Dashboard' }}
                    </a>
                                        <a href="/grader" class="flex items-center gap-3 text-gray-300 hover:text-red-500 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        FAMER Score
                    </a>
                    <a href="/for-owners" class="flex items-center gap-3 text-gray-300 hover:text-amber-500 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        {{ app()->getLocale() === 'en' ? 'Explore Plans' : 'Ver Planes' }}
                    </a>
                </div>
                <a href="/guia" class="block text-gray-300 hover:text-green-400 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ app()->getLocale() === 'en' ? 'City Guide' : 'Guía por Ciudad' }}
                </a>
                <a href="/famer-awards" class="block text-gray-300 hover:text-amber-400 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all flex items-center gap-2">
                    <span class="text-xl">🏆</span>
                    FAMER Awards
                </a>
                @auth
                    <!-- Mi Cuenta Section (Mobile) -->
                    <div class="border-t border-gray-700 pt-2 mt-2">
                        <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Mi Cuenta
                        </div>
                        <div class="px-3 py-2 flex items-center gap-3 border-b border-gray-700 mb-1">
                            @if(auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-yellow-500">
                            @else
                                <div class="w-9 h-9 rounded-full bg-yellow-600 flex items-center justify-center text-white font-bold ring-2 ring-yellow-500">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-semibold text-gray-200 truncate max-w-[180px]">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-400 truncate max-w-[180px]">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                        <a href="{{ $isOwnerUser ? '/owner' : '/dashboard' }}" class="flex items-center gap-3 text-gray-300 hover:text-yellow-500 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            {{ $isOwnerUser ? 'Mi Panel' : 'Mi Dashboard' }}
                        </a>
                        <a href="/my-favorites" class="flex items-center gap-3 text-gray-300 hover:text-yellow-500 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all">
                            <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                            </svg>
                            Mis Favoritos
                        </a>
                        <a href="/my-reservations" class="flex items-center gap-3 text-gray-300 hover:text-yellow-500 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Mis Reservaciones
                        </a>
                        <a href="/my-orders" class="flex items-center gap-3 text-gray-300 hover:text-yellow-500 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            Mis Pedidos
                        </a>
                        <a href="/my-reviews" class="flex items-center gap-3 text-gray-300 hover:text-yellow-500 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all">
                            <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            Mis Reseñas
                        </a>
                        <a href="/profile" class="flex items-center gap-3 text-gray-300 hover:text-yellow-500 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Mi Perfil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="px-0">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full text-red-400 hover:text-red-300 hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                @else
                    <a href="/login" class="block text-gray-300 hover:text-white hover:bg-gray-800 px-3 py-2 rounded-lg text-base font-medium transition-all">
                        Login
                    </a>
                    <a href="/register" class="block bg-gradient-to-r from-red-600 to-red-700 text-white text-center px-3 py-2 rounded-lg text-base font-semibold transition-all">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Close dropdown on Livewire navigation
        document.addEventListener('livewire:navigated', function() {
            document.querySelectorAll('[x-data]').forEach(function(el) {
                if (el.__x) {
                    el.__x.$data.open = false;
                } else if (el._x_dataStack) {
                    el._x_dataStack[0].open = false;
                }
            });
        });
    </script>

    <!-- PWA Install Button -->
    <div id="pwa-install-btn" class="hidden fixed bottom-6 right-6 z-50 animate-bounce">
        <button onclick="installPWA()" class="btn-gold px-6 py-4 rounded-2xl shadow-2xl hover:shadow-3xl hover:scale-105 transition-all duration-300 flex items-center gap-3 font-semibold">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <span>{{ app()->getLocale() === 'en' ? 'Install App' : 'Instalar App' }}</span>
        </button>
    </div>

    <!-- Main Content -->
    <main class="min-h-screen" style="position:relative; z-index:1;">
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>

    <!-- Footer - Dark & Elegant -->
    <footer class="relative pattern-dark text-white mt-20">
        <!-- Gold top border -->
        <div class="h-1 bg-gradient-to-r from-transparent via-yellow-600 to-transparent"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Logo & About -->
                <div class="md:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <picture>
                            <source srcset="/images/branding/logo.webp?v=3" type="image/webp">
                            <img src="/images/branding/logo.png?v=3" alt="FAMER USA" class="h-16 w-auto" style="max-height: 64px;">
                        </picture>
                        <div class="flex flex-col leading-none" style="gap: 2px;">
                            <span class="text-yellow-500 font-bold text-sm tracking-wide" style="line-height: 1;">Restaurantes</span>
                            <span class="text-white font-bold text-sm tracking-wide" style="line-height: 1;">Mexicanos</span>
                            <span class="text-yellow-500 font-bold text-sm tracking-wide" style="line-height: 1;">Famosos</span>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        El directorio más completo de restaurantes mexicanos auténticos en Estados Unidos.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-yellow-500 font-bold mb-4 text-sm uppercase tracking-wider">{{ app()->getLocale() === 'en' ? 'Quick Links' : 'Enlaces' }}</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/" class="text-gray-400 hover:text-yellow-500 transition-colors">{{ app()->getLocale() === 'en' ? 'Home' : 'Inicio' }}</a></li>
                        <li><a href="/restaurantes" class="text-gray-400 hover:text-yellow-500 transition-colors">{{ app()->getLocale() === 'en' ? 'All Restaurants' : 'Restaurantes' }}</a></li>
                        <li><a href="/guia" class="text-gray-400 hover:text-yellow-500 transition-colors">{{ app()->getLocale() === 'en' ? 'City Guides' : 'Guía por Ciudad' }}</a></li>
                        <li><a href="/sugerir" class="text-gray-400 hover:text-yellow-500 transition-colors">{{ app()->getLocale() === 'en' ? 'Suggest' : 'Sugerir' }}</a></li>
                    </ul>
                </div>

                <!-- For Business -->
                <div>
                    <h3 class="text-yellow-500 font-bold mb-4 text-sm uppercase tracking-wider">{{ app()->getLocale() === 'en' ? 'For Business' : 'Negocios' }}</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/for-owners" class="text-gray-400 hover:text-yellow-500 transition-colors">{{ app()->getLocale() === 'en' ? 'For Owners' : 'Para Dueños' }}</a></li>
                        <li><a href="/claim" class="text-gray-400 hover:text-yellow-500 transition-colors">{{ app()->getLocale() === 'en' ? 'Claim Restaurant' : 'Reclamar' }}</a></li>
                    </ul>
                </div>

                <!-- MF Imports Family -->
                <div>
                    <h3 class="text-yellow-500 font-bold mb-4 text-sm uppercase tracking-wider">Nuestros Negocios</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="https://mf-imports.com" class="text-gray-400 hover:text-yellow-500 transition-colors" target="_blank">MF Imports</a></li>
                        <li><a href="https://mueblesmexicanos.com" class="text-gray-400 hover:text-yellow-500 transition-colors" target="_blank">Muebles Mexicanos</a></li>
                        <li><a href="https://tormexpro.com" class="text-gray-400 hover:text-yellow-500 transition-colors" target="_blank">TorMex Pro</a></li>
                        <li><a href="https://mftrailers.com" class="text-gray-400 hover:text-yellow-500 transition-colors" target="_blank">MF Trailers</a></li>
                    </ul>
                </div>
            </div>

            <!-- Mexican Flag Divider -->
            <div class="h-0.5 mexican-ribbon rounded-full mb-8"></div>

            <!-- Bottom Bar -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-sm">
                    &copy; {{ date('Y') }} <span class="text-yellow-600 font-semibold">Restaurantes Mexicanos Famosos</span>. All rights reserved.
                </p>
                <div class="flex items-center gap-4 text-sm">
                    <a href="/privacy" class="text-gray-500 hover:text-yellow-500 transition-colors">Privacy</a>
                    <a href="/terms" class="text-gray-500 hover:text-yellow-500 transition-colors">Terms</a>
                    <a href="/contact" class="text-gray-500 hover:text-yellow-500 transition-colors">Contact</a>
                </div>
            </div>

            <!-- Legal Disclaimer -->
            <div class="mt-6 pt-4 border-t border-gray-800">
                <p class="text-gray-600 text-xs text-center leading-relaxed max-w-4xl mx-auto">
                    <strong class="text-gray-500">Disclaimer:</strong> Restaurant information is compiled from public sources including
                    <a href="https://www.yelp.com" target="_blank" rel="noopener" class="text-red-400 hover:text-red-300">Yelp</a> and
                    <a href="https://www.google.com/maps" target="_blank" rel="noopener" class="text-blue-400 hover:text-blue-300">Google</a>.
                    Restaurant owners can <a href="/claim" class="text-yellow-500 hover:text-yellow-400 underline">claim and verify their listing</a>.
                </p>
            </div>
        </div>
    </footer>

        @livewire('cart')

    @livewireScripts

    <!-- Dynamic Scripts -->
    @stack('scripts')
    {{-- Carmen AI Chat: only on Premium/Elite restaurant detail pages --}}
    @php
        $chatRestaurant = null;
        if(request()->route() && request()->route()->getName() === 'restaurants.show') {
            $chatRestaurant = request()->route()->parameter('restaurant');
            if(is_string($chatRestaurant)) {
                $chatRestaurant = \App\Models\Restaurant::where('slug', $chatRestaurant)->first();
            }
        }
    @endphp
    @if($chatRestaurant && in_array($chatRestaurant->subscription_tier, ['premium', 'elite']))
        @include("partials.chat-widget")
    @endif
</body>
</html>
