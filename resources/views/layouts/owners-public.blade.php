<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Para Dueños' }} - Restaurantes Mexicanos Famosos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    {{-- Owner-Specific Header --}}
    <header class="bg-gray-900 text-white sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <picture>
                        <source srcset="/images/branding/icon.webp" type="image/webp">
                        <img src="/images/branding/icon.png" alt="FAMER" class="h-10 w-10 rounded-full border-2 border-yellow-600/50">
                    </picture>
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-black text-red-500">FAMER</span>
                        <span class="text-gray-600">|</span>
                        <span class="text-gray-400 text-sm">Para Dueños</span>
                    </div>
                </a>
                
                {{-- Center Navigation --}}
                <nav class="hidden lg:flex items-center gap-1">
                    <a href="/sugerir" class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->is('sugerir*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} transition">
                        Agregar Restaurante
                    </a>
                    <a href="/claim" class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->is('claim*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} transition">
                        Reclamar Gratis
                    </a>
                    <a href="/for-owners" class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->is('for-owners*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} transition">
                        Planes y Precios
                    </a>
                </nav>
                
                {{-- Right Side --}}
                <div class="flex items-center gap-4">
                    <a href="/" class="text-gray-400 hover:text-white text-sm hidden md:block">
                        ← Volver al Sitio
                    </a>
                    @auth
                        @if(auth()->user()->restaurants()->exists() || auth()->user()->activeTeamMemberships()->exists())
                            <a href="/owner" class="text-amber-400 hover:text-amber-300 text-sm font-medium">
                                Mi Panel
                            </a>
                        @else
                            <a href="/dashboard" class="text-amber-400 hover:text-amber-300 text-sm font-medium">
                                Mi Cuenta
                            </a>
                        @endif
                        <span class="text-gray-600 text-xs hidden md:block truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-white text-sm">
                                Salir
                            </button>
                        </form>
                    @else
                        <a href="{{ route('owner.login') }}" class="text-gray-300 hover:text-white text-sm">Login</a>
                        <a href="{{ route('register') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            Registrarse
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main>
        {{ $slot }}
    </main>

    {{-- Simple Footer --}}
    <footer class="bg-gray-900 text-gray-400 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm">© {{ date('Y') }} Restaurantes Mexicanos Famosos. Todos los derechos reservados.</p>
            <div class="mt-4 flex justify-center gap-6 text-sm">
                <a href="/" class="hover:text-white">Inicio</a>
                <a href="/restaurantes" class="hover:text-white">Restaurantes</a>
                <a href="/famer-awards" class="hover:text-white">FAMER Awards</a>
            </div>
        </div>
    </footer>

    <script data-cfasync="false">/* Disable Rocket Loader for Livewire */</script>
    @livewireScripts
    @stack('scripts')
    
    {{-- {{-- @include("partials.chat-widget") --}} --}}
</body>
</html>
