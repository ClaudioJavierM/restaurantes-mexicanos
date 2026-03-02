@extends('layouts.app')

@section('title', __('app.site_name') . ' - Contacto')
@section('meta_description', 'Contacta con el equipo de Restaurantes Mexicanos Famosos. Estamos aquí para ayudarte.')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-display font-bold bg-gradient-to-r from-emerald-600 via-red-600 to-orange-600 bg-clip-text text-transparent mb-4">
            Contacto
        </h1>
        <p class="text-gray-600 text-lg">Estamos aquí para ayudarte</p>
    </div>

    <div class="grid md:grid-cols-2 gap-8">
        <!-- Contact Options -->
        <div class="space-y-6">
            <!-- For Restaurant Owners -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-emerald-500">
                <h2 class="text-xl font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="text-2xl">🏪</span>
                    ¿Eres dueño de un restaurante?
                </h2>
                <p class="text-gray-600 mb-4">
                    Si quieres reclamar y gestionar el perfil de tu restaurante, o tienes preguntas sobre nuestros planes premium:
                </p>
                <a href="/claim" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-green-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Reclamar mi Restaurante
                </a>
                <p class="text-sm text-gray-500 mt-3">
                    O escríbenos a: <a href="mailto:owners@restaurantesmexicanosfamosos.com" class="text-emerald-600 hover:underline">owners@restaurantesmexicanosfamosos.com</a>
                </p>
            </div>

            <!-- Suggest a Restaurant -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-orange-500">
                <h2 class="text-xl font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="text-2xl">🌮</span>
                    ¿Conoces un restaurante que deberíamos incluir?
                </h2>
                <p class="text-gray-600 mb-4">
                    Ayúdanos a hacer crecer nuestro directorio sugiriendo restaurantes mexicanos auténticos.
                </p>
                <a href="/sugerir" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-red-500 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Sugerir Restaurante
                </a>
            </div>

            <!-- General Inquiries -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-blue-500">
                <h2 class="text-xl font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="text-2xl">💬</span>
                    Consultas Generales
                </h2>
                <p class="text-gray-600 mb-4">
                    Para preguntas generales, sugerencias o comentarios sobre el sitio:
                </p>
                <a href="mailto:info@restaurantesmexicanosfamosos.com" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    info@restaurantesmexicanosfamosos.com
                </a>
            </div>
        </div>

        <!-- Contact Info & FAQ -->
        <div class="space-y-6">
            <!-- Quick FAQ -->
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Preguntas Frecuentes</h2>

                <div class="space-y-4">
                    <div>
                        <h3 class="font-semibold text-gray-800">¿Cómo puedo actualizar la información de mi restaurante?</h3>
                        <p class="text-gray-600 text-sm mt-1">Primero necesitas <a href="/claim" class="text-emerald-600 hover:underline">reclamar tu restaurante</a> para verificar que eres el propietario.</p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-800">¿De dónde viene la información de los restaurantes?</h3>
                        <p class="text-gray-600 text-sm mt-1">Usamos datos públicos de Yelp y Google, además de información proporcionada por propietarios verificados.</p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-800">¿Cuánto cuesta aparecer en el directorio?</h3>
                        <p class="text-gray-600 text-sm mt-1">El listado básico es gratuito. Ofrecemos planes premium para propietarios que quieran destacar su restaurante.</p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-800">¿Cómo reporto información incorrecta?</h3>
                        <p class="text-gray-600 text-sm mt-1">Escríbenos a <a href="mailto:corrections@restaurantesmexicanosfamosos.com" class="text-emerald-600 hover:underline">corrections@restaurantesmexicanosfamosos.com</a> con los detalles.</p>
                    </div>
                </div>
            </div>

            <!-- Social & Response Time -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Tiempo de Respuesta</h2>
                <p class="text-gray-600 mb-4">
                    Normalmente respondemos en <strong>24-48 horas hábiles</strong>. Para asuntos urgentes relacionados con tu restaurante, incluye "URGENTE" en el asunto del correo.
                </p>

                <div class="pt-4 border-t border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-3">Síguenos</h3>
                    <div class="flex gap-4">
                        <a href="#" class="text-gray-400 hover:text-blue-600 transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-pink-600 transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Link -->
    <div class="text-center mt-12">
        <a href="/" class="inline-flex items-center gap-2 text-gray-600 hover:text-emerald-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver al inicio
        </a>
    </div>
</div>
@endsection
