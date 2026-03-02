<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Exitoso! - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl p-12 text-center">
            {{-- Success Icon --}}
            <div class="mb-8">
                <div class="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>

            {{-- Success Message --}}
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Pago Exitoso!
            </h1>
            <p class="text-xl text-gray-600 mb-8">
                Tu suscripcion ha sido activada correctamente
            </p>

            {{-- Details --}}
            <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Que sigue?</h2>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">Verificacion completada</p>
                            <p class="text-sm text-gray-600">Tu restaurante ha sido verificado y reclamado</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">Suscripcion activada</p>
                            <p class="text-sm text-gray-600">Todas las caracteristicas {{ isset($plan) ? ucfirst($plan) : 'premium' }} estan ahora disponibles</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">Correo de confirmacion enviado</p>
                            <p class="text-sm text-gray-600">Revisa tu bandeja de entrada para ver tus datos de acceso</p>
                        </div>
                    </li>
                </ul>
            </div>

            {{-- Call to Action --}}
            <div class="space-y-4">
                @if(isset($restaurant))
                <a
                    href="{{ url('/owner') }}"
                    class="inline-block w-full bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-lg font-semibold transition-colors"
                >
                    Ir a Mi Dashboard de Propietario
                </a>
                @else
                <a
                    href="{{ route('home') }}"
                    class="inline-block w-full bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-lg font-semibold transition-colors"
                >
                    Ir a la Pagina Principal
                </a>
                @endif
                <p class="text-sm text-gray-600">
                    Tambien te enviamos un correo con las instrucciones y datos de acceso a tu dashboard
                </p>
            </div>

            {{-- Support --}}
            <div class="mt-8 pt-8 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    Necesitas ayuda?
                    <a href="mailto:support@restaurantesmexicanosfamosos.com" class="text-red-600 hover:text-red-700 font-medium">
                        Contacta a soporte
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
