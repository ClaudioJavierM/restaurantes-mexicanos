<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#0B0B0B">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FAMER">
    <link rel="apple-touch-icon" href="/images/branding/icon.png?v=2">
    <link rel="icon" type="image/png" sizes="192x192" href="/images/branding/icon.png?v=2">

    <!-- SEO: Dynamic Title and Description -->
    <title>{{ $title ?? $__env->yieldContent('title', __('app.site_name') . ' - ' . __('app.tagline')) }}</title>
    <meta name="description" content="{{ $metaDescription ?? ($__env->yieldContent('meta_description') ?: (__('app.tagline') . ' - Descubre los mejores restaurantes mexicanos auténticos en Estados Unidos')) }}">

    <!-- SEO: Canonical URL — sin query strings por defecto; páginas pueden overridearlo con @section('canonical', '...') -->
    <link rel="canonical" href="{{ $__env->hasSection('canonical') ? $__env->yieldContent('canonical') : strtok(url()->current(), '?') }}">

    {{-- SEO: Hreflang — 3 domains (es-MX, es-US, en-US) --}}
    @php
        $rawPath = request()->path();
        $hrefPath = $rawPath === '/' ? '' : '/' . $rawPath;
        // EN domain uses /restaurant/ instead of /restaurante/
        $enPath = str_replace('/restaurante/', '/restaurant/', $hrefPath);
    @endphp
    <link rel="alternate" hreflang="es-MX" href="https://restaurantesmexicanosfamosos.com.mx{{ $hrefPath }}" />
    <link rel="alternate" hreflang="es-US" href="https://restaurantesmexicanosfamosos.com{{ $hrefPath }}" />
    <link rel="alternate" hreflang="en-US" href="https://famousmexicanrestaurants.com{{ $enPath }}" />
    <link rel="alternate" hreflang="x-default" href="https://restaurantesmexicanosfamosos.com.mx{{ $hrefPath }}" />

    <!-- Open Graph Locale -->
    @php
        $ogLocale = str_contains(request()->getHost(), 'famousmexicanrestaurants') ? 'en_US'
            : (str_contains(request()->getHost(), '.com.mx') ? 'es_MX' : 'es_US');
    @endphp
    <meta property="og:locale" content="{{ $ogLocale }}" />
    <meta property="og:locale:alternate" content="{{ $ogLocale === 'en_US' ? 'es_MX' : 'en_US' }}" />
    <meta property="og:site_name" content="{{ __('app.site_name') }}" />

    <!-- Dynamic Meta Tags (Open Graph, Twitter Cards) — cada página pushea los suyos via @push('meta') -->
    @stack('meta')

    <!-- Core Web Vitals: Preconnect & DNS Prefetch -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://maps.googleapis.com">

    <!-- Fonts: non-render-blocking async load -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preload" href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800|playfair-display:400,700,900&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800|playfair-display:400,700,900&display=swap" rel="stylesheet"></noscript>

    <!-- GTM moved to body bottom for performance -->

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --color-gold: #D4AF37;
            --color-gold-light: #E8C67A;
            --color-gold-dark: #B08A1E;
            --color-green: #1F3D2B;
            --color-red: #8B1E1E;
            --color-dark: #1A1A1A;
            --color-darker: #0B0B0B;
            --color-gray: #2A2A2A;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0B0B0B;
            color: #F5F5F5;
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

        /* Dark elegant pattern */
        .pattern-dark {
            background-color: var(--color-darker);
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23D4AF37' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
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

    <!-- Conversion Tracking + Clarity moved to body bottom for performance -->
</head>
<body class="bg-[#0B0B0B] antialiased text-[#F5F5F5]">
    <!-- Navigation -->
    @php
        $isOwnerPage = request()->is('claim*') || request()->is('for-owners*') || request()->is('sugerir*') || request()->is('grader*');
    @endphp
    <nav class="bg-[#0B0B0B] sticky top-0 border-b border-[#D4AF37]/20" style="z-index:9999;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-24 items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="flex items-center group">
                        <img src="/images/branding/famer55.png?v=5" alt="FAMER - Famous Mexican Restaurants" class="h-16 md:h-20 w-auto group-hover:scale-105 transition-transform duration-300" style="mix-blend-mode:lighten;">
                    </a>
                    @if($isOwnerPage)
                        <span class="hidden sm:inline-block ml-3 px-2 py-1 bg-[#D4AF37]/10 text-[#D4AF37] text-xs font-semibold rounded border border-[#D4AF37]/20">
                            {{ app()->getLocale() === 'en' ? 'For Owners' : 'Para Duenos' }}
                        </span>
                    @endif
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-1">
                @if($isOwnerPage)
                    {{-- Owner Navigation --}}
                    <a href="/" class="text-gray-400 hover:text-[#D4AF37] px-4 py-2 text-sm font-medium transition-colors duration-200">
                        ← {{ app()->getLocale() === 'en' ? 'Back to Site' : 'Volver al Sitio' }}
                    </a>
                    <a href="/sugerir" class="text-gray-400 hover:text-[#D4AF37] px-4 py-2 text-sm font-medium transition-colors duration-200 {{ request()->is('sugerir*') ? 'text-[#D4AF37]' : '' }}">
                        {{ app()->getLocale() === 'en' ? 'Add Restaurant' : 'Agregar Restaurante' }}
                    </a>
                    <a href="/claim" class="text-gray-400 hover:text-[#D4AF37] px-4 py-2 text-sm font-medium transition-colors duration-200 {{ request()->is('claim*') ? 'text-[#D4AF37]' : '' }}">
                        {{ app()->getLocale() === 'en' ? 'Claim Free' : 'Reclamar Gratis' }}
                    </a>
                    <a href="/como-funciona-famer" class="text-gray-400 hover:text-[#D4AF37] px-4 py-2 text-sm font-medium transition-colors duration-200 {{ request()->is('como-funciona-famer') || request()->is('how-famer-works') ? 'text-[#D4AF37]' : '' }}">
                        {{ app()->getLocale() === 'en' ? 'How It Works' : 'Cómo Funciona' }}
                    </a>
                    <a href="/for-owners#pricing" class="text-gray-400 hover:text-[#D4AF37] px-4 py-2 text-sm font-medium transition-colors duration-200">
                        {{ app()->getLocale() === 'en' ? 'Plans & Pricing' : 'Planes y Precios' }}
                    </a>
                    <a href="/grader" class="text-gray-400 hover:text-[#D4AF37] px-4 py-2 text-sm font-medium transition-colors duration-200 {{ request()->is('grader*') ? 'text-[#D4AF37]' : '' }}">
                        FAMER Score
                    </a>

                    <!-- Language Switcher (Desktop) -->
                    <x-language-switcher />

                    @auth
                        @if(auth()->user()->ownedRestaurants && auth()->user()->ownedRestaurants->first())
                            <a href="/owner/{{ auth()->user()->ownedRestaurants->first()->id }}" class="text-[#D4AF37] hover:text-[#E8C67A] px-3 py-2 text-sm font-medium transition-colors duration-200">
                                {{ app()->getLocale() === 'en' ? 'My Dashboard' : 'Mi Dashboard' }}
                            </a>
                        @endif
                    @else
                        <a href="/login" class="text-gray-400 hover:text-[#F5F5F5] px-3 py-2 text-sm font-medium transition-colors duration-200">
                            Login
                        </a>
                        <a href="/register" class="bg-[#D4AF37] text-[#0B0B0B] px-5 py-2 rounded-lg text-sm font-bold hover:bg-[#E8C67A] transition-colors duration-200 ml-2">
                            {{ app()->getLocale() === 'en' ? 'Register' : 'Registrarse' }}
                        </a>
                    @endauth
                @else
                    {{-- Customer Navigation --}}
                    <a href="/restaurantes" class="text-gray-400 hover:text-[#D4AF37] px-4 py-2 text-sm font-medium transition-colors duration-200">
                        {{ __('app.restaurants') }}
                    </a>
                    <a href="/mejores-restaurantes-mexicanos" class="text-gray-400 hover:text-[#D4AF37] px-4 py-2 text-sm font-medium transition-colors duration-200">
                        Top 10
                    </a>
                    <a href="/guia" class="text-gray-400 hover:text-[#D4AF37] px-4 py-2 text-sm font-medium transition-colors duration-200">
                        {{ app()->getLocale() === 'en' ? 'City Guide' : 'Guia' }}
                    </a>
                    <a href="/famer-awards" class="text-gray-400 hover:text-[#D4AF37] px-4 py-2 text-sm font-medium transition-colors duration-200">
                        FAMER Awards
                    </a>
                    <a href="/blog" class="text-gray-400 hover:text-[#D4AF37] px-4 py-2 text-sm font-medium transition-colors duration-200 {{ request()->is('blog*') ? 'text-[#D4AF37]' : '' }}">
                        Blog
                    </a>

                    <!-- Language Switcher (Desktop) -->
                    <x-language-switcher />

                    <!-- Auth Links (Desktop) -->
                    @auth
                        <a href="/my-favorites" class="text-gray-400 hover:text-[#D4AF37] px-3 py-2 text-sm font-medium transition-colors duration-200 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                            </svg>
                            {{ app()->getLocale() === 'en' ? 'Favorites' : 'Favoritos' }}
                        </a>
                        <a href="/dashboard" class="text-gray-400 hover:text-[#D4AF37] px-3 py-2 text-sm font-medium transition-colors duration-200">
                            Dashboard
                        </a>
                    @else
                        <a href="/login" class="text-gray-400 hover:text-[#F5F5F5] px-3 py-2 text-sm font-medium transition-colors duration-200">
                            Login
                        </a>
                        <a href="/register" class="border border-[#D4AF37] text-[#D4AF37] px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#D4AF37]/10 transition-colors duration-200">
                            {{ app()->getLocale() === 'en' ? 'Register' : 'Registro' }}
                        </a>
                    @endauth

                    <a href="/for-owners" class="text-gray-400 hover:text-[#D4AF37] px-4 py-2 text-sm font-medium transition-colors duration-200">
                        FAMER Business
                    </a>
                @endif
                </div>

                <!-- Mobile menu button -->
                <div class="flex lg:hidden items-center space-x-2">
                    <!-- Language Switcher (Mobile) -->
                    <x-language-switcher />

                    <!-- Hamburger Button -->
                    <button id="mobile-menu-btn" type="button" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-[#D4AF37] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#D4AF37]">
                        <span class="sr-only">{{ app()->getLocale() === 'en' ? 'Open main menu' : 'Abrir menu principal' }}</span>
                        <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden lg:hidden bg-[#0B0B0B] border-t border-[#2A2A2A]">
            <div class="px-4 pt-2 pb-4 space-y-1">
            @if($isOwnerPage)
                {{-- Owner Mobile Menu --}}
                <a href="/" class="block text-gray-400 hover:text-[#D4AF37] px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-200">
                    ← {{ app()->getLocale() === 'en' ? 'Back to Site' : 'Volver al Sitio' }}
                </a>
                <a href="/sugerir" class="block text-gray-400 hover:text-[#D4AF37] px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-200">
                    {{ app()->getLocale() === 'en' ? 'Add Restaurant' : 'Agregar Restaurante' }}
                </a>
                <a href="/claim" class="block text-gray-400 hover:text-[#D4AF37] px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-200">
                    {{ app()->getLocale() === 'en' ? 'Claim Free' : 'Reclamar Gratis' }}
                </a>
                <a href="/como-funciona-famer" class="block text-gray-400 hover:text-[#D4AF37] px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-200">
                    {{ app()->getLocale() === 'en' ? 'How It Works' : 'Cómo Funciona' }}
                </a>
                <a href="/for-owners#pricing" class="block text-gray-400 hover:text-[#D4AF37] px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-200">
                    {{ app()->getLocale() === 'en' ? 'Plans & Pricing' : 'Planes y Precios' }}
                </a>
                <a href="/grader" class="block text-gray-400 hover:text-[#D4AF37] px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-200">
                    FAMER Score
                </a>
                <div class="border-t border-[#2A2A2A] my-2"></div>
                @auth
                    @if(auth()->user()->ownedRestaurants && auth()->user()->ownedRestaurants->first())
                        <a href="/owner/{{ auth()->user()->ownedRestaurants->first()->id }}" class="block text-[#D4AF37] px-3 py-2.5 rounded-lg text-base font-medium">
                            {{ app()->getLocale() === 'en' ? 'My Dashboard' : 'Mi Dashboard' }}
                        </a>
                    @endif
                @else
                    <a href="/login" class="block text-gray-400 hover:text-[#F5F5F5] px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-200">
                        Login
                    </a>
                    <a href="/register" class="block bg-[#D4AF37] text-[#0B0B0B] text-center px-3 py-2.5 rounded-lg text-base font-bold transition-colors duration-200 hover:bg-[#E8C67A] mt-2">
                        {{ app()->getLocale() === 'en' ? 'Register' : 'Registrarse' }}
                    </a>
                @endauth
            @else
                {{-- Customer Mobile Menu --}}
                <a href="/restaurantes" class="block text-gray-400 hover:text-[#D4AF37] px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-200">
                    {{ __('app.restaurants') }}
                </a>
                <a href="/mejores-restaurantes-mexicanos" class="block text-gray-400 hover:text-[#D4AF37] px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-200">
                    Top 10
                </a>
                <a href="/guia" class="block text-gray-400 hover:text-[#D4AF37] px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-200">
                    {{ app()->getLocale() === 'en' ? 'City Guide' : 'Guia' }}
                </a>
                <a href="/famer-awards" class="block text-gray-400 hover:text-[#D4AF37] px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-200">
                    FAMER Awards
                </a>
                <a href="/blog" class="block text-gray-400 hover:text-[#D4AF37] px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-200 {{ request()->is('blog*') ? 'text-[#D4AF37]' : '' }}">
                    Blog
                </a>
                <div class="border-t border-[#2A2A2A] my-2"></div>
                @auth
                    <a href="/my-favorites" class="block text-gray-400 hover:text-[#D4AF37] px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-200">
                        {{ app()->getLocale() === 'en' ? 'My Favorites' : 'Mis Favoritos' }}
                    </a>
                    <a href="/dashboard" class="block text-gray-400 hover:text-[#D4AF37] px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-200">
                        Dashboard
                    </a>
                @else
                    <a href="/login" class="block text-gray-400 hover:text-[#F5F5F5] px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-200">
                        Login
                    </a>
                @endauth
                <a href="/for-owners" class="block text-gray-400 hover:text-[#D4AF37] px-3 py-2.5 rounded-lg text-base font-medium transition-colors duration-200">
                    FAMER Business
                </a>
            @endif
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

    <!-- Footer -->
    <footer class="bg-[#0B0B0B] text-[#F5F5F5] mt-20">
        <!-- Gold top border -->
        <div class="h-px bg-[#D4AF37]/30"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-12">
                <!-- Logo & About -->
                <div class="lg:col-span-1">
                    <div class="mb-4">
                        <img src="/images/branding/famer55.png?v=5" alt="FAMER - Famous Mexican Restaurants" class="h-28 md:h-32 w-auto" style="mix-blend-mode:lighten;">
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        {{ app()->getLocale() === 'en' ? 'The most complete directory of authentic Mexican restaurants in the United States.' : 'El directorio mas completo de restaurantes mexicanos autenticos en Estados Unidos.' }}
                    </p>
                </div>

                <!-- Discovery -->
                <div>
                    <h3 class="text-[#D4AF37] font-bold mb-4 text-sm uppercase tracking-wider">{{ app()->getLocale() === 'en' ? 'Discovery' : 'Descubre' }}</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="/restaurantes" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">{{ app()->getLocale() === 'en' ? 'Explore Restaurants' : 'Explorar Restaurantes' }}</a></li>
                        <li><a href="/mejores-restaurantes-mexicanos" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">Top 10</a></li>
                        <li><a href="/restaurantes" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">{{ app()->getLocale() === 'en' ? 'Categories' : 'Categorias' }}</a></li>
                        <li><a href="/guia" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">{{ app()->getLocale() === 'en' ? 'City Guide' : 'Guia por Ciudad' }}</a></li>
                        <li><a href="/famer-awards" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">FAMER Awards</a></li>
                        <li><a href="/votar" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">{{ app()->getLocale() === 'en' ? 'Vote Now' : 'Votar Ahora' }}</a></li>
                    </ul>
                </div>

                <!-- For Owners -->
                <div>
                    <h3 class="text-[#D4AF37] font-bold mb-4 text-sm uppercase tracking-wider">{{ app()->getLocale() === 'en' ? 'For Owners' : 'Para Duenos' }}</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="/como-funciona-famer" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">{{ app()->getLocale() === 'en' ? 'How It Works' : 'Cómo Funciona' }}</a></li>
                        <li><a href="/claim" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">{{ app()->getLocale() === 'en' ? 'Claim Restaurant' : 'Reclamar Restaurante' }}</a></li>
                        <li><a href="/for-owners#pricing" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">{{ app()->getLocale() === 'en' ? 'Plans & Pricing' : 'Planes y Precios' }}</a></li>
                        <li><a href="/dashboard" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">{{ app()->getLocale() === 'en' ? 'Owner Dashboard' : 'Dashboard' }}</a></li>
                        <li><a href="/grader" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">FAMER Score</a></li>
                    </ul>
                </div>

                <!-- Company -->
                <div>
                    <h3 class="text-[#D4AF37] font-bold mb-4 text-sm uppercase tracking-wider">{{ app()->getLocale() === 'en' ? 'Company' : 'Empresa' }}</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="/contact" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">{{ app()->getLocale() === 'en' ? 'Contact' : 'Contacto' }}</a></li>
                        <li><a href="/preguntas-frecuentes" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">{{ app()->getLocale() === 'en' ? 'FAQ' : 'Preguntas Frecuentes' }}</a></li>
                        <li><a href="/privacy" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">{{ app()->getLocale() === 'en' ? 'Privacy Policy' : 'Privacidad' }}</a></li>
                        <li><a href="/terms" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">{{ app()->getLocale() === 'en' ? 'Terms of Service' : 'Terminos' }}</a></li>
                    </ul>
                </div>

                <!-- MF Group -->
                <div>
                    <h3 class="text-[#D4AF37] font-bold mb-4 text-sm uppercase tracking-wider">MF Group</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="https://mf-imports.com" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200" target="_blank" rel="noopener">MF Imports</a></li>
                        <li><a href="https://mueblesmexicanos.com" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200" target="_blank" rel="noopener">Muebles Mexicanos</a></li>
                        <li><a href="https://tormexpro.com" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200" target="_blank" rel="noopener">TorMex Pro</a></li>
                        <li><a href="https://mftrailers.com" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200" target="_blank" rel="noopener">MF Trailers</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-[#2A2A2A] pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-gray-600 text-sm">
                        &copy; {{ date('Y') }} <span class="text-[#D4AF37]">FAMER</span> &mdash; Famous Mexican Restaurants. {{ app()->getLocale() === 'en' ? 'All rights reserved.' : 'Todos los derechos reservados.' }}
                    </p>
                    <div class="flex items-center gap-5">
                        <!-- Social placeholders -->
                        <a href="#" class="text-gray-600 hover:text-[#D4AF37] transition-colors duration-200" aria-label="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-[#D4AF37] transition-colors duration-200" aria-label="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-[#D4AF37] transition-colors duration-200" aria-label="TikTok">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Subtle disclaimer -->
                <p class="text-gray-700 text-xs text-center mt-6 leading-relaxed max-w-3xl mx-auto">
                    {{ app()->getLocale() === 'en' ? 'Restaurant information compiled from public sources. Owners can' : 'Informacion recopilada de fuentes publicas. Duenos pueden' }}
                    <a href="/claim" class="text-[#D4AF37]/60 hover:text-[#D4AF37] underline">{{ app()->getLocale() === 'en' ? 'claim and verify their listing' : 'reclamar y verificar su perfil' }}</a>.
                </p>
            </div>
        </div>
    </footer>

    @livewire('cart')

    @livewireScripts

    <!-- Dynamic Scripts -->
    @stack('scripts')

    {{-- Carmen AI Chat: Premium/Elite restaurant detail pages only --}}
    @php
        $chatRestaurant = null;
        if(request()->route() && request()->route()->getName() === 'restaurants.show') {
            $slug = request()->route()->parameter('slug');
            if($slug) {
                $chatRestaurant = \App\Models\Restaurant::where('slug', $slug)->first();
            }
        }
    @endphp
    @if($chatRestaurant && in_array($chatRestaurant->subscription_tier, ['premium', 'elite']))
        @include("partials.chat-widget")
    @endif

    <!-- Google Tag Manager (body) -->
    @php $gtmId = app()->getLocale() === 'en' ? 'GTM-NLKPXHKM' : 'GTM-M53NLTND'; @endphp
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $gtmId }}');</script>

    <!-- MF Group Universal Conversion Tracking -->
    <script>
    (function() {
        'use strict';
        function trackEvent(e,d){if(typeof umami!=='undefined'){try{umami.track(e,d);}catch(x){}}if(typeof gtag!=='undefined'){try{gtag('event',e,d);}catch(x){}}}
        document.addEventListener('submit',function(e){var f=e.target;if(f&&f.tagName==='FORM'){trackEvent('form_submit',{form_id:f.id||'',form_name:f.getAttribute('name')||'',form_action:f.getAttribute('action')||window.location.href});}},true);
        document.addEventListener('click',function(e){var l=e.target.closest('a[href]');if(!l)return;var h=l.getAttribute('href')||'',hl=h.toLowerCase();if(hl.indexOf('wa.me')!==-1||hl.indexOf('whatsapp.com')!==-1){trackEvent('whatsapp_click',{whatsapp_url:h});return;}if(hl.indexOf('tel:')===0){trackEvent('phone_click',{phone_number:h.replace(/^tel:/i,'')});return;}if(hl.indexOf('mailto:')===0){trackEvent('email_click',{email_address:h.replace(/^mailto:/i,'').split('?')[0]});return;}try{var u=new URL(h,window.location.origin);if(u.hostname&&u.hostname!==window.location.hostname){trackEvent('external_link',{destination_url:h});}}catch(x){}},true);
    })();
    </script>

    <!-- Microsoft Clarity -->
    <script>
    (function(){var ids={'restaurantesmexicanosfamosos.com.mx':'vhh4lzctxt','restaurantesmexicanosfamosos.com':'vhh5aptees','famousmexicanrestaurants.com':'vhh61fn5an'};var id=ids[window.location.hostname];if(id){(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,"clarity","script",id);}})();
    </script>
</body>
</html>
