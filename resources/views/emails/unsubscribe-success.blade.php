<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripcion Cancelada - {{ $restaurant->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">Te has dado de baja</h1>

        <p class="text-gray-600 mb-6">
            <strong>{{ $email }}</strong> ya no recibira emails promocionales de <strong>{{ $restaurant->name }}</strong>.
        </p>

        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-500">
                Si esto fue un error, puedes volver a suscribirte visitando el restaurante o contactandolos directamente.
            </p>
        </div>

        <a href="{{ route('restaurant.show', $restaurant->slug) }}" 
           class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-red-700 transition-colors">
            Ver {{ $restaurant->name }}
        </a>
    </div>
</body>
</html>
