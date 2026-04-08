<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    {{-- PWA Meta Tags --}}
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ $branding->app_name ?? $restaurant->name ?? 'FAMER' }}">
    <meta name="application-name" content="{{ $branding->app_name ?? $restaurant->name ?? 'FAMER' }}">
    <meta name="msapplication-TileColor" content="{{ $branding->primary_color ?? '#dc2626' }}">
    <meta name="theme-color" content="{{ $branding->primary_color ?? '#dc2626' }}">

    <title>{{ $title ?? ($branding->app_name ?? $restaurant->name ?? 'FAMER') }}</title>

    {{-- Favicon & Icons --}}
    @if($branding && $branding->favicon_url)
        <link rel="icon" type="image/png" href="{{ $branding->favicon_url }}">
        <link rel="apple-touch-icon" href="{{ $branding->favicon_url }}">
    @else
        <link rel="icon" type="image/png" href="/images/icons/icon-192x192.png">
        <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">
    @endif

    {{-- Dynamic Manifest --}}
    @if($branding && $branding->manifest_url)
        <link rel="manifest" href="{{ $branding->manifest_url }}">
    @else
        <link rel="manifest" href="/manifest.json">
    @endif

    {{-- Splash Screens for iOS --}}
    <link rel="apple-touch-startup-image" href="{{ $branding->splash_screen_url ?? '/images/splash/splash-640x1136.png' }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Custom Branding CSS --}}
    <style>
        :root {
            --pwa-primary: {{ $branding->primary_color ?? '#dc2626' }};
            --pwa-secondary: {{ $branding->secondary_color ?? '#991b1b' }};
            --pwa-accent: {{ $branding->accent_color ?? '#fbbf24' }};
            --pwa-background: {{ $branding->background_color ?? '#ffffff' }};
            --pwa-text: {{ $branding->text_color ?? '#111827' }};
        }

        /* Safe area padding for notched devices */
        .safe-top { padding-top: env(safe-area-inset-top); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }
        .safe-left { padding-left: env(safe-area-inset-left); }
        .safe-right { padding-right: env(safe-area-inset-right); }

        /* PWA-specific styles */
        body {
            background-color: var(--pwa-background);
            color: var(--pwa-text);
            overscroll-behavior-y: contain;
            -webkit-overflow-scrolling: touch;
        }

        .pwa-header {
            background: linear-gradient(135deg, var(--pwa-primary), var(--pwa-secondary));
        }

        .pwa-btn-primary {
            background-color: var(--pwa-primary);
        }

        .pwa-btn-primary:hover {
            background-color: var(--pwa-secondary);
        }

        .pwa-accent {
            color: var(--pwa-accent);
        }

        /* Hide scrollbars but keep functionality */
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Bottom navigation safe area */
        .pwa-bottom-nav {
            padding-bottom: max(0.75rem, env(safe-area-inset-bottom));
        }

        /* Pull to refresh indicator */
        .pull-indicator {
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
        }
    </style>

    {{-- Custom CSS from branding --}}
    @if($branding && $branding->custom_css)
        <style>{!! $branding->custom_css !!}</style>
    @endif

    @livewireStyles
</head>
<body class="font-sans antialiased min-h-screen flex flex-col">
    {{-- Pull to Refresh Indicator --}}
    <div id="pull-indicator" class="pull-indicator hidden">
        <div class="bg-white shadow-lg rounded-full p-2 mt-2">
            <svg class="w-6 h-6 animate-spin text-gray-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </div>
    </div>

    {{-- Main Content --}}
    <main class="flex-1 flex flex-col safe-top safe-bottom">
        {{ $slot }}
    </main>

    {{-- Offline Indicator --}}
    <div id="offline-indicator" class="fixed top-0 left-0 right-0 bg-yellow-500 text-white text-center py-2 text-sm font-medium z-50 safe-top hidden">
        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3"></path>
        </svg>
        Sin conexión - Modo offline
    </div>

    @livewireScripts

    {{-- PWA Install Prompt --}}
    <div id="pwa-install-prompt" class="fixed bottom-20 left-4 right-4 bg-white rounded-2xl shadow-2xl p-4 z-50 hidden safe-bottom">
        <div class="flex items-center gap-4">
            <img src="{{ $branding->logo_url ?? '/images/logo.png' }}" alt="Logo" class="w-12 h-12 rounded-xl">
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900">Instalar App</h3>
                <p class="text-sm text-gray-500">Accede más rápido desde tu pantalla de inicio</p>
            </div>
            <button id="pwa-install-btn" class="pwa-btn-primary text-white px-4 py-2 rounded-lg font-medium">
                Instalar
            </button>
        </div>
        <button id="pwa-dismiss-btn" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <script>
        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('SW registered:', reg.scope))
                .catch(err => console.error('SW registration failed:', err));
        }

        // Offline detection
        function updateOnlineStatus() {
            const indicator = document.getElementById('offline-indicator');
            if (navigator.onLine) {
                indicator.classList.add('hidden');
            } else {
                indicator.classList.remove('hidden');
            }
        }

        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);
        updateOnlineStatus();

        // PWA Install Prompt
        let deferredPrompt;
        const installPrompt = document.getElementById('pwa-install-prompt');
        const installBtn = document.getElementById('pwa-install-btn');
        const dismissBtn = document.getElementById('pwa-dismiss-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            // Check if user has dismissed before
            if (!localStorage.getItem('pwa-install-dismissed')) {
                setTimeout(() => {
                    installPrompt.classList.remove('hidden');
                }, 3000);
            }
        });

        if (installBtn) {
            installBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log('Install prompt outcome:', outcome);
                    deferredPrompt = null;
                    installPrompt.classList.add('hidden');
                }
            });
        }

        if (dismissBtn) {
            dismissBtn.addEventListener('click', () => {
                installPrompt.classList.add('hidden');
                localStorage.setItem('pwa-install-dismissed', 'true');
            });
        }

        // Detect if running as installed PWA
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
            document.body.classList.add('pwa-standalone');
        }

        // Pull to refresh (simple implementation)
        let touchStartY = 0;
        let touchEndY = 0;

        document.addEventListener('touchstart', e => {
            touchStartY = e.touches[0].clientY;
        }, { passive: true });

        document.addEventListener('touchmove', e => {
            touchEndY = e.touches[0].clientY;
            const pullDistance = touchEndY - touchStartY;

            if (window.scrollY === 0 && pullDistance > 50) {
                document.getElementById('pull-indicator').classList.remove('hidden');
            }
        }, { passive: true });

        document.addEventListener('touchend', () => {
            const pullDistance = touchEndY - touchStartY;

            if (window.scrollY === 0 && pullDistance > 100) {
                location.reload();
            }

            document.getElementById('pull-indicator').classList.add('hidden');
            touchStartY = 0;
            touchEndY = 0;
        }, { passive: true });
    </script>

    {{-- Custom JS from branding --}}
    @if($branding && $branding->custom_js)
        <script>{!! $branding->custom_js !!}</script>
    @endif

    {{-- Google Analytics if configured --}}
    @if($branding && $branding->google_analytics_id)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $branding->google_analytics_id }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $branding->google_analytics_id }}');
        </script>
    @endif
</body>
</html>
