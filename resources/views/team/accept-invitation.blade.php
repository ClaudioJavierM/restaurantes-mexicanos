<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Aceptar Invitacion - {{ config('app.name') }}</title>

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

            <div class="w-full sm:max-w-lg mt-8 px-6 py-6 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Invitacion de Equipo</h2>
                    <p class="mt-2 text-gray-600">Has sido invitado a unirte al equipo</p>
                </div>

                <!-- Restaurant Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center space-x-4">
                        @if($member->restaurant->photos->first())
                            <img src="{{ $member->restaurant->photos->first()->url }}"
                                 alt="{{ $member->restaurant->name }}"
                                 class="w-16 h-16 rounded-lg object-cover">
                        @else
                            <div class="w-16 h-16 rounded-lg bg-red-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-bold text-lg text-gray-900">{{ $member->restaurant->name }}</h3>
                            <p class="text-gray-600 text-sm">{{ $member->restaurant->city }}, {{ $member->restaurant->state?->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Invitee Email -->
                <div class="bg-emerald-50 rounded-lg p-4 mb-6 text-center">
                    <p class="text-sm text-gray-500">Invitacion para:</p>
                    <p class="font-medium text-gray-900">{{ $member->user->email }}</p>
                </div>

                <!-- Role Info -->
                <div class="mb-6">
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-gray-600">Tu rol:</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($member->role === 'owner') bg-red-100 text-red-800
                            @elseif($member->role === 'manager') bg-yellow-100 text-yellow-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ $member->getRoleLabel() }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-gray-600">Invitado por:</span>
                        <span class="text-gray-900">{{ $member->inviter?->name ?? 'El propietario' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Expira:</span>
                        <span class="text-gray-900">{{ $member->invitation_expires_at->format('d/m/Y') }}</span>
                    </div>
                </div>

                <!-- Role Description -->
                <div class="mb-6 p-4 rounded-lg
                    @if($member->role === 'owner') bg-red-50
                    @elseif($member->role === 'manager') bg-yellow-50
                    @else bg-blue-50 @endif">
                    @if($member->role === 'owner')
                        <p class="text-sm text-red-800">
                            <strong>Como propietario</strong> tendras acceso completo al restaurante, incluyendo la gestion del equipo, finanzas y todas las configuraciones.
                        </p>
                    @elseif($member->role === 'manager')
                        <p class="text-sm text-yellow-800">
                            <strong>Como gerente</strong> podras gestionar reservaciones, responder resenas, actualizar el menu y ver estadisticas del restaurante.
                        </p>
                    @else
                        <p class="text-sm text-blue-800">
                            <strong>Como staff</strong> podras ver y gestionar las reservaciones del dia asignadas.
                        </p>
                    @endif
                </div>

                <!-- Accept Form -->
                <form method="POST" action="{{ route('team.invitation.accept', $token) }}">
                    @csrf

                    @if($needsPassword)
                        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-4">
                                Parece que esta es tu primera vez. Por favor crea una contrasena para acceder a tu cuenta.
                            </p>

                            <div class="space-y-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">Contrasena</label>
                                    <input type="password" name="password" id="password" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                           minlength="8">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contrasena</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                           minlength="8">
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-col space-y-3">
                        <button type="submit"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Aceptar Invitacion
                        </button>
                    </div>
                </form>

                <form method="POST" action="{{ route('team.invitation.decline', $token) }}" class="mt-3">
                    @csrf
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Declinar
                    </button>
                </form>

                <p class="mt-4 text-center text-xs text-gray-500">
                    Al aceptar, tendras acceso al panel de administracion del restaurante con los permisos de tu rol.
                </p>
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
