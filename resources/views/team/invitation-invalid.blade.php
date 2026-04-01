<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Invitacion No Valida - {{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col items-center bg-gray-100">
            <!-- Gradient Header Bar -->
            <div class="w-full py-6" style="background: linear-gradient(135deg, #059669 0%, #C9A84C 100%);">
                <div class="flex justify-center">
                    <a href="/">
                        <img src="{{ asset('images/branding/logo-famer-full.png') }}" alt="FAMER" class="h-12">
                    </a>
                </div>
            </div>

            <div class="w-full sm:max-w-md mt-8 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full
                    @if($reason === 'expired') bg-yellow-100
                    @else bg-red-100 @endif mb-4">
                    @if($reason === 'expired')
                        <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @else
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    @endif
                </div>

                <h2 class="text-xl font-bold text-gray-900 mb-2">
                    @if($reason === 'expired')
                        Invitacion Expirada
                    @elseif($reason === 'already_used')
                        Invitacion Ya Utilizada
                    @else
                        Invitacion No Valida
                    @endif
                </h2>

                <p class="text-gray-600 mb-6">{{ $message }}</p>

                <div class="space-y-3">
                    @if($reason === 'expired')
                        <p class="text-sm text-gray-500">
                            Contacta al propietario del restaurante para solicitar una nueva invitacion.
                        </p>
                    @endif

                    <a href="{{ route('home') }}"
                       class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                        Ir al Inicio
                    </a>

                    @auth
                        <div class="mt-4">
                            <a href="{{ route('filament.owner.pages.dashboard') }}"
                               class="text-sm text-red-600 hover:text-red-800">
                                Ir a mi Panel
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Footer -->
            <div class="w-full mt-8 py-6 text-center">
                <img src="{{ asset('images/branding/logo-famer-full.png') }}" alt="FAMER" class="h-8 mx-auto mb-2 opacity-60">
                <p class="text-xs text-gray-400">FAMER - Restaurantes Mexicanos Famosos</p>
                <p class="text-xs text-gray-400 mt-1">&copy; {{ date('Y') }} Todos los derechos reservados.</p>
            </div>
        </div>
    </body>
</html>
