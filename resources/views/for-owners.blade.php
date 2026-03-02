<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Para Dueños de Restaurantes - {{ config('app.name') }}</title>
    <meta name="description" content="Reclama tu restaurante gratis y aumenta tu visibilidad. Accede a análisis, gestiona reseñas y atrae más clientes.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center text-red-600 hover:text-red-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="font-semibold">Volver al Directorio</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#benefits" class="text-gray-700 hover:text-red-600 transition">Beneficios</a>
                    <a href="#pricing" class="text-gray-700 hover:text-red-600 transition">Precios</a>
                    <a href="{{ route('claim.restaurant') }}" class="bg-red-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-700 transition">
                        Reclamar Ahora
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-red-600 to-orange-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    ¿Es Tu Restaurante?<br>
                    <span class="text-yellow-300">Reclámalo Gratis</span>
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-gray-100">
                    Toma control de tu presencia online y atrae más clientes
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('claim.restaurant') }}" class="bg-white text-red-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition shadow-lg">
                        Reclamar Mi Restaurante →
                    </a>
                    <a href="#benefits" class="border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-red-600 transition">
                        Ver Beneficios
                    </a>
                </div>
                <p class="mt-6 text-gray-200">
                    ✓ 100% Gratis para empezar &nbsp; ✓ Sin tarjeta de crédito &nbsp; ✓ Configuración en 5 minutos
                </p>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold text-red-600">1,970+</div>
                    <div class="text-gray-600 mt-2">Restaurantes Listados</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-red-600">100K+</div>
                    <div class="text-gray-600 mt-2">Visitantes Mensuales</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-red-600">50+</div>
                    <div class="text-gray-600 mt-2">Estados Cubiertos</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-red-600">24/7</div>
                    <div class="text-gray-600 mt-2">Visibilidad Online</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">¿Por Qué Reclamar Tu Restaurante?</h2>
                <p class="text-xl text-gray-600">Todo lo que necesitas para crecer tu negocio online</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Benefit 1 -->
                <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Análisis en Tiempo Real</h3>
                    <p class="text-gray-600 mb-4">Ve cuántas personas visitan tu perfil, hacen clic en tu teléfono, abren tu menú y más.</p>
                    <ul class="text-gray-600 space-y-2">
                        <li>✓ Visitas a tu página</li>
                        <li>✓ Clics en teléfono</li>
                        <li>✓ Clics en dirección</li>
                        <li>✓ Visitas a tu sitio web</li>
                    </ul>
                </div>

                <!-- Benefit 2 -->
                <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Galería de Fotos</h3>
                    <p class="text-gray-600 mb-4">Muestra lo mejor de tu restaurante con fotos profesionales de tus platillos y ambiente.</p>
                    <ul class="text-gray-600 space-y-2">
                        <li>✓ Hasta 10 fotos gratis</li>
                        <li>✓ Fotos ilimitadas (Premium)</li>
                        <li>✓ Actualiza cuando quieras</li>
                        <li>✓ Optimización automática</li>
                    </ul>
                </div>

                <!-- Benefit 3 -->
                <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Gestión de Reseñas</h3>
                    <p class="text-gray-600 mb-4">Responde a reseñas de clientes y construye tu reputación online.</p>
                    <ul class="text-gray-600 space-y-2">
                        <li>✓ Responde a reseñas</li>
                        <li>✓ Notificaciones instantáneas</li>
                        <li>✓ Mejora tu rating</li>
                        <li>✓ Construye confianza</li>
                    </ul>
                </div>

                <!-- Benefit 4 -->
                <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Perfil Verificado</h3>
                    <p class="text-gray-600 mb-4">Obtén una insignia de verificación que genera confianza en tus clientes.</p>
                    <ul class="text-gray-600 space-y-2">
                        <li>✓ Insignia verificada</li>
                        <li>✓ Mayor credibilidad</li>
                        <li>✓ Destaca sobre competencia</li>
                        <li>✓ Información actualizada</li>
                    </ul>
                </div>

                <!-- Benefit 5 -->
                <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Cupones y Promociones</h3>
                    <p class="text-gray-600 mb-4">Crea ofertas especiales para atraer nuevos clientes y aumentar ventas.</p>
                    <ul class="text-gray-600 space-y-2">
                        <li>✓ Cupones digitales</li>
                        <li>✓ Ofertas por tiempo limitado</li>
                        <li>✓ Tracking de conversiones</li>
                        <li>✓ Atrae nuevos clientes</li>
                    </ul>
                </div>

                <!-- Benefit 6 -->
                <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Email Marketing</h3>
                    <p class="text-gray-600 mb-4">Mantén contacto con tus clientes mediante campañas de email profesionales.</p>
                    <ul class="text-gray-600 space-y-2">
                        <li>✓ Campañas automatizadas</li>
                        <li>✓ Plantillas profesionales</li>
                        <li>✓ Segmentación de audiencia</li>
                        <li>✓ Análisis de resultados</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Planes Flexibles Para Tu Negocio</h2>
                <p class="text-xl text-gray-600">Empieza gratis y actualiza cuando estés listo</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Free Plan -->
                <div class="bg-gray-50 p-8 rounded-xl shadow-lg">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Gratuito</h3>
                        <div class="text-4xl font-bold text-gray-900">$0<span class="text-lg text-gray-600">/mes</span></div>
                        <p class="text-gray-600 mt-2">Perfecto para empezar</p>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Dashboard con análisis básicos</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Hasta 10 fotos</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Responder a reseñas</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Perfil verificado</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">Actualizar información</span>
                        </li>

                        {{-- Cupón Plan Gratuito --}}
                        <li class="flex items-start bg-green-50 p-3 rounded-lg border border-green-200">
                            <svg class="w-5 h-5 text-green-600 mr-2 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <strong class="text-green-800">🎟️ 1 Cupón Anual (5% descuento)</strong>
                                <p class="text-xs text-green-700 mt-1">Ahorra hasta $300/año en muebles mexicanos, decoración, vajillas, artesanía, equipo de tortillería, equipo de paletería mexicana, traílas de comida y más...</p>
                            </div>
                        </li>
                    </ul>
                    <a href="{{ route('claim.restaurant') }}" class="block text-center bg-gray-200 text-gray-900 px-6 py-3 rounded-lg font-bold hover:bg-gray-300 transition">
                        Empezar Gratis
                    </a>
                </div>

                <!-- Premium Plan -->
                <div class="bg-gradient-to-br from-red-600 to-orange-600 p-8 rounded-xl shadow-2xl transform scale-105 relative">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-yellow-400 text-gray-900 px-4 py-1 rounded-full text-sm font-bold">
                        MÁS POPULAR
                    </div>
                    <div class="text-center mb-6 text-white">
                        <h3 class="text-2xl font-bold mb-2">Premium</h3>
                        <div class="text-4xl font-bold">$39<span class="text-lg">/mes</span></div>
                        <p class="mt-2">Para restaurantes en crecimiento</p>
                    </div>
                    <ul class="space-y-3 mb-8 text-white">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span><strong>Todo en Gratuito, más:</strong></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Fotos ilimitadas</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Análisis avanzados</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Cupones y promociones</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Posición destacada</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Soporte prioritario</span>
                        </li>

                        {{-- Cupón Plan Premium --}}
                        <li class="flex items-start bg-white/20 p-3 rounded-lg border-2 border-white/50 backdrop-blur-sm">
                            <svg class="w-5 h-5 text-yellow-300 mr-2 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <strong class="text-white">💰 4 Cupones Trimestrales (10% descuento)</strong>
                                <p class="text-xs text-white/90 mt-1">Ahorra hasta $500 por cupón ($2,000/año total) en muebles mexicanos, decoración, vajillas, artesanía, equipo de tortillería, equipo de paletería mexicana, traílas de comida y más...</p>
                            </div>
                        </li>
                    </ul>
                    <a href="{{ route('claim.restaurant') }}" class="block text-center bg-white text-red-600 px-6 py-3 rounded-lg font-bold hover:bg-gray-100 transition">
                        Empezar Ahora
                    </a>
                </div>

                <!-- Elite Plan -->
                <div class="bg-gray-900 p-8 rounded-xl shadow-lg">
                    <div class="text-center mb-6 text-white">
                        <h3 class="text-2xl font-bold mb-2">Elite</h3>
                        <div class="text-4xl font-bold">$79<span class="text-lg">/mes</span></div>
                        <p class="text-gray-400 mt-2">Máxima exposición</p>
                    </div>
                    <ul class="space-y-3 mb-8 text-white">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-400 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span><strong>Todo en Premium, más:</strong></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-400 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Posición TOP en búsquedas</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-400 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Email marketing ilimitado</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-400 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Insignia Elite dorada</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-400 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Anuncios destacados homepage</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-400 mr-2 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Gerente de cuenta dedicado</span>
                        </li>

                        {{-- Cupón Plan Elite --}}
                        <li class="flex items-start bg-gradient-to-r from-yellow-400/20 to-orange-500/20 p-3 rounded-lg border-2 border-yellow-400 backdrop-blur-sm">
                            <svg class="w-5 h-5 text-yellow-400 mr-2 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <strong class="text-yellow-300">💎 Paquete Elite de Ahorros:</strong>
                                <p class="text-xs text-white mt-1"><strong>Opción A:</strong> 6 cupones (15% desc.) - $750 c/u ($4,500/año)</p>
                                <p class="text-xs text-white"><strong>Opción B:</strong> 1 cupón proyecto (15% desc., tope $1,500) + 4 trimestrales (10% desc., $500 c/u)</p>
                                <p class="text-xs text-yellow-300 font-semibold mt-1">Válido en muebles mexicanos, decoración, vajillas, artesanía, equipo de tortillería, equipo de paletería mexicana, traílas de comida y más...</p>
                            </div>
                        </li>
                    </ul>
                    <a href="{{ route('claim.restaurant') }}" class="block text-center bg-yellow-400 text-gray-900 px-6 py-3 rounded-lg font-bold hover:bg-yellow-300 transition">
                        Contactar Ventas
                    </a>
                </div>
            </div>

            <p class="text-center text-gray-600 mt-8">
                Todos los planes incluyen 30 días de garantía de devolución de dinero
            </p>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">¿Cómo Funciona?</h2>
                <p class="text-xl text-gray-600">Reclama tu restaurante en 3 simples pasos</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 bg-red-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6">
                        1
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Busca Tu Restaurante</h3>
                    <p class="text-gray-600">Encuentra tu restaurante en nuestra base de datos de más de 1,970 establecimientos.</p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-orange-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6">
                        2
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Verifica Tu Identidad</h3>
                    <p class="text-gray-600">Recibe un código de verificación por email o teléfono para confirmar que eres el dueño.</p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-yellow-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6">
                        3
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Empieza a Gestionar</h3>
                    <p class="text-gray-600">Accede a tu panel y empieza a gestionar tu presencia online inmediatamente.</p>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('claim.restaurant') }}" class="inline-block bg-red-600 text-white px-10 py-4 rounded-lg font-bold text-lg hover:bg-red-700 transition shadow-lg">
                    Reclamar Mi Restaurante Ahora →
                </a>
            </div>
        </div>
    </section>

    <!-- Restaurant Not Listed Section -->
    <section class="py-20 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h2 class="text-4xl font-bold mb-4">¿No Encuentras Tu Restaurante?</h2>
                <p class="text-xl mb-8 text-blue-100">¡No te preocupes! Lo agregamos GRATIS en menos de 24 horas</p>

                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-8 text-left max-w-2xl mx-auto">
                    <h3 class="text-2xl font-bold mb-6 text-center">Agrega Tu Restaurante Ahora</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="font-bold mb-3 text-lg">✓ Información que necesitamos:</h4>
                            <ul class="space-y-2 text-blue-100">
                                <li>• Nombre del restaurante</li>
                                <li>• Dirección completa</li>
                                <li>• Teléfono de contacto</li>
                                <li>• Email del negocio</li>
                                <li>• Sitio web (opcional)</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-bold mb-3 text-lg">✓ Qué obtienes:</h4>
                            <ul class="space-y-2 text-blue-100">
                                <li>• Perfil completo creado</li>
                                <li>• Fotos de Google Maps</li>
                                <li>• Info de Google Business</li>
                                <li>• Listo para reclamar</li>
                                <li>• Notificación cuando esté listo</li>
                            </ul>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('suggestions.create') }}" class="inline-block bg-white text-blue-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition shadow-lg">
                            Agregar Mi Restaurante Gratis →
                        </a>
                        <p class="mt-4 text-sm text-blue-200">
                            📧 Te enviaremos un email cuando tu restaurante esté listo para reclamar
                        </p>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                    <div>
                        <div class="text-3xl font-bold">24-48h</div>
                        <div class="text-blue-200">Tiempo de activación</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold">100%</div>
                        <div class="text-blue-200">Gratis para siempre</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold">0</div>
                        <div class="text-blue-200">Costo de listado</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Preguntas Frecuentes</h2>
            </div>

            <div class="space-y-6">
                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-lg cursor-pointer text-gray-900">¿Es realmente gratis?</summary>
                    <p class="mt-4 text-gray-600">Sí, nuestro plan gratuito es 100% gratis para siempre. Incluye todas las funciones básicas que necesitas para gestionar tu restaurante online. Puedes actualizar a un plan premium cuando estés listo para más funciones.</p>
                </details>

                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-lg cursor-pointer text-gray-900">¿Cuánto tiempo toma la verificación?</summary>
                    <p class="mt-4 text-gray-600">La verificación es instantánea. Recibirás un código por email que puedes ingresar inmediatamente. Una vez verificado, tendrás acceso completo a tu panel en menos de 5 minutos.</p>
                </details>

                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-lg cursor-pointer text-gray-900">¿Qué pasa si mi restaurante no está listado?</summary>
                    <p class="mt-4 text-gray-600">Si tu restaurante no está en nuestra base de datos, usa el formulario "Agregar Mi Restaurante" arriba. Lo agregaremos gratuitamente en 24-48 horas y te notificaremos por email cuando esté listo para que lo reclames.</p>
                </details>

                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-lg cursor-pointer text-gray-900">¿De dónde sacan la información de mi restaurante?</summary>
                    <p class="mt-4 text-gray-600">Obtenemos información pública de Google Business Profile, incluyendo dirección, teléfono, horarios y fotos. Una vez que reclames tu restaurante, podrás actualizar y agregar más información.</p>
                </details>

                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-lg cursor-pointer text-gray-900">¿Puedo cancelar en cualquier momento?</summary>
                    <p class="mt-4 text-gray-600">Sí, puedes cancelar tu suscripción en cualquier momento sin penalización. Tu cuenta seguirá activa hasta el final del período de facturación. El plan gratuito nunca expira.</p>
                </details>

                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-lg cursor-pointer text-gray-900">¿Ofrecen soporte técnico?</summary>
                    <p class="mt-4 text-gray-600">Sí, todos los planes incluyen soporte por email. Los planes Premium y Elite incluyen soporte prioritario con respuestas en menos de 24 horas.</p>
                </details>

                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-lg cursor-pointer text-gray-900">¿Puedo tener múltiples restaurantes?</summary>
                    <p class="mt-4 text-gray-600">Sí, puedes gestionar múltiples restaurantes desde la misma cuenta. Cada restaurante requiere su propia verificación y suscripción si eliges un plan premium.</p>
                </details>

                <details class="bg-gray-50 p-6 rounded-lg">
                    <summary class="font-bold text-lg cursor-pointer text-gray-900">¿Cómo me ayudan a conseguir más clientes?</summary>
                    <p class="mt-4 text-gray-600">Tu restaurante aparecerá en búsquedas locales, podrás crear cupones y promociones, responder a reseñas para construir confianza, y con planes premium, obtener posición destacada en los resultados de búsqueda.</p>
                </details>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="bg-gradient-to-r from-red-600 to-orange-600 text-white py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">
                ¿Listo Para Crecer Tu Negocio?
            </h2>
            <p class="text-xl mb-8 text-gray-100">
                Únete a cientos de restaurantes que ya están atrayendo más clientes
            </p>
            <a href="{{ route('claim.restaurant') }}" class="inline-block bg-white text-red-600 px-10 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition shadow-lg">
                Reclamar Mi Restaurante Gratis →
            </a>
            <p class="mt-6 text-sm text-gray-200">
                Sin tarjeta de crédito requerida • Configuración en 5 minutos • Soporte en español
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-gray-400">&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
                <div class="mt-4 space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition">Términos</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Privacidad</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Contacto</a>
                </div>
            </div>
        </div>
    </footer>
    @include('partials.chat-widget')
</body>
</html>
