<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $title ?? 'For Owners') - FAMER | Famous Mexican Restaurants</title>
    <meta name="description" content="@yield('meta_description', '')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800|playfair-display:400,700,900&display=swap" rel="stylesheet" />

    <!-- Facebook Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1362912769188775');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" src="https://www.facebook.com/tr?id=1362912769188775&ev=PageView&noscript=1"/></noscript>
    <!-- End Facebook Pixel Code -->

    <!-- Google Analytics 4 -->
    @php
        $host = request()->getHost();
        if (str_contains($host, ".com.mx")) {
            $ga4Id = "G-35N2H2RPVW";
        } elseif (str_contains($host, "famousmexican")) {
            $ga4Id = "G-3Y4S0P66Z6";
        } else {
            $ga4Id = "G-J6S51PLBZM";
        }
    @endphp
    <script data-cfasync="false" async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
    <script data-cfasync="false">
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag("js", new Date());
        gtag("config", "{{ $ga4Id }}");
    </script>

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
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="bg-[#0B0B0B] antialiased text-[#F5F5F5]">

    <!-- Navigation -->
    <header class="bg-[#0B0B0B] sticky top-0 z-50 border-b border-[#D4AF37]/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <img src="/images/branding/famer55.png?v=5" alt="FAMER" class="h-14 md:h-16 w-auto group-hover:scale-105 transition-transform duration-300" style="mix-blend-mode:lighten;">
                    <div class="flex items-center gap-2">
                        <span class="font-display font-black text-lg text-[#D4AF37]">FAMER</span>
                        <span class="text-[#2A2A2A]">|</span>
                        <span class="text-gray-500 text-sm">{{ app()->getLocale() === 'en' ? 'For Owners' : 'Para Duenos' }}</span>
                    </div>
                </a>

                <!-- Center Navigation -->
                <nav class="hidden lg:flex items-center gap-1">
                    <a href="/sugerir" class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->is('sugerir*') ? 'text-[#D4AF37]' : 'text-gray-400 hover:text-[#D4AF37]' }} transition-colors duration-200">
                        {{ app()->getLocale() === 'en' ? 'Add Restaurant' : 'Agregar Restaurante' }}
                    </a>
                    <a href="/claim" class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->is('claim*') ? 'text-[#D4AF37]' : 'text-gray-400 hover:text-[#D4AF37]' }} transition-colors duration-200">
                        {{ app()->getLocale() === 'en' ? 'Claim Free' : 'Reclamar Gratis' }}
                    </a>
                    <a href="/for-owners" class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->is('for-owners*') ? 'text-[#D4AF37]' : 'text-gray-400 hover:text-[#D4AF37]' }} transition-colors duration-200">
                        {{ app()->getLocale() === 'en' ? 'Plans & Pricing' : 'Planes y Precios' }}
                    </a>
                    <a href="/como-funciona-famer" class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->is('como-funciona-famer') || request()->is('how-famer-works') ? 'text-[#D4AF37]' : 'text-gray-400 hover:text-[#D4AF37]' }} transition-colors duration-200">
                        {{ app()->getLocale() === 'en' ? 'How It Works' : 'Cómo Funciona' }}
                    </a>
                </nav>

                <!-- Right Side -->
                <div class="flex items-center gap-4">
                    <a href="/" class="text-gray-500 hover:text-[#D4AF37] text-sm hidden md:flex items-center gap-1 transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        {{ app()->getLocale() === 'en' ? 'Back to Site' : 'Volver al Sitio' }}
                    </a>
                    @auth
                        @if(auth()->user()->ownedRestaurants()->exists())
                            <a href="/owner/{{ auth()->user()->ownedRestaurants()->first()->id }}" class="text-[#D4AF37] hover:text-[#E8C67A] text-sm font-medium transition-colors duration-200">
                                {{ app()->getLocale() === 'en' ? 'My Dashboard' : 'Mi Dashboard' }}
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-[#F5F5F5] text-sm transition-colors duration-200">
                                {{ app()->getLocale() === 'en' ? 'Logout' : 'Salir' }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-400 hover:text-[#F5F5F5] text-sm transition-colors duration-200">Login</a>
                        <a href="{{ route('register') }}" class="bg-[#D4AF37] text-[#0B0B0B] px-4 py-2 rounded-lg text-sm font-bold hover:bg-[#E8C67A] transition-colors duration-200">
                            {{ app()->getLocale() === 'en' ? 'Register' : 'Registrarse' }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-[#0B0B0B] border-t border-[#D4AF37]/20 py-10 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex items-center justify-center gap-3 mb-4">
                <img src="/images/branding/famer55.png?v=5" alt="FAMER" class="h-24 md:h-28 w-auto" style="mix-blend-mode:lighten;">
                <div class="text-left">
                    <span class="font-display font-black text-lg text-[#D4AF37] block leading-tight">FAMER</span>
                    <span class="text-[10px] text-[#CCCCCC] tracking-widest uppercase">Famous Mexican Restaurants</span>
                </div>
            </div>
            <p class="text-gray-600 text-sm">&copy; {{ date('Y') }} FAMER. {{ app()->getLocale() === 'en' ? 'All rights reserved.' : 'Todos los derechos reservados.' }}</p>
            <div class="mt-4 flex justify-center gap-6 text-sm">
                <a href="/" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">{{ app()->getLocale() === 'en' ? 'Home' : 'Inicio' }}</a>
                <a href="/restaurantes" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">{{ app()->getLocale() === 'en' ? 'Restaurants' : 'Restaurantes' }}</a>
                <a href="/famer-awards" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">FAMER Awards</a>
                <a href="/privacy" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">{{ app()->getLocale() === 'en' ? 'Privacy' : 'Privacidad' }}</a>
                <a href="/terms" class="text-gray-500 hover:text-[#D4AF37] transition-colors duration-200">{{ app()->getLocale() === 'en' ? 'Terms' : 'Terminos' }}</a>
            </div>
        </div>
    </footer>

    <script data-cfasync="false">/* Disable Rocket Loader for Livewire */</script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
