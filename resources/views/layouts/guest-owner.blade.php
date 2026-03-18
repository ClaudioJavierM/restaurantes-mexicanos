<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Panel de Negocios — {{ config('app.name', 'Restaurantes Mexicanos Famosos') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-amber-50 to-orange-50">
            <!-- Logo y Branding -->
            <div class="mb-6">
                <a href="/" class="flex flex-col items-center">
                    <img src="/images/branding/sombrero-icon.png" alt="FAMER" class="w-16 h-16 drop-shadow-lg mb-3">
                    <h1 class="text-2xl font-bold text-gray-900">Restaurantes Mexicanos</h1>
                    <p class="text-sm text-amber-600 font-semibold">Para Dueños</p>
                </a>
            </div>

            <!-- Main Form Container -->
            <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-xl overflow-hidden sm:rounded-2xl border border-amber-100">
                {{ $slot }}
            </div>

            <!-- Footer text -->
            <p class="mt-6 text-center text-xs text-gray-500">
                Administra tu restaurante y hazlo crecer con las herramientas de FAMER
            </p>
        </div>
    </body>
</html>
