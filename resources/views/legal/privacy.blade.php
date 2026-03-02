@extends('layouts.app')

@section('title', __('app.site_name') . ' - Política de Privacidad')
@section('meta_description', 'Política de privacidad de Restaurantes Mexicanos Famosos. Conoce cómo recopilamos y protegemos tu información.')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-display font-bold bg-gradient-to-r from-emerald-600 via-red-600 to-orange-600 bg-clip-text text-transparent mb-4">
            Política de Privacidad
        </h1>
        <p class="text-gray-600">Última actualización: {{ date('d/m/Y') }}</p>
    </div>

    <!-- Content -->
    <div class="bg-white rounded-2xl shadow-xl p-8 space-y-8">
        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-emerald-600">1.</span> Información que Recopilamos
            </h2>
            <div class="text-gray-600 space-y-3">
                <p><strong>Información de Restaurantes:</strong> Recopilamos información pública de restaurantes mexicanos a través de APIs oficiales de Yelp y Google Places, incluyendo nombre, dirección, horarios, calificaciones y reseñas.</p>
                <p><strong>Información de Usuarios:</strong> Cuando te registras, recopilamos tu nombre, correo electrónico y contraseña (encriptada). Si reclamas un restaurante, también recopilamos información de verificación del negocio.</p>
                <p><strong>Datos de Navegación:</strong> Utilizamos cookies y herramientas de análisis (Google Analytics) para entender cómo se utiliza nuestro sitio.</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-emerald-600">2.</span> Uso de la Información
            </h2>
            <div class="text-gray-600 space-y-3">
                <p>Utilizamos la información recopilada para:</p>
                <ul class="list-disc list-inside ml-4 space-y-2">
                    <li>Proporcionar un directorio completo de restaurantes mexicanos</li>
                    <li>Permitir a los propietarios gestionar sus perfiles</li>
                    <li>Mejorar la experiencia del usuario</li>
                    <li>Enviar comunicaciones relevantes (si has dado tu consentimiento)</li>
                    <li>Analizar el uso del sitio para mejoras</li>
                </ul>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-emerald-600">3.</span> Fuentes de Datos
            </h2>
            <div class="text-gray-600 space-y-3">
                <p>La información de restaurantes proviene de:</p>
                <ul class="list-disc list-inside ml-4 space-y-2">
                    <li><strong>Yelp:</strong> A través de su API Fusion, obtenemos datos públicos de negocios</li>
                    <li><strong>Google Places:</strong> Utilizamos la API de Places para verificar y enriquecer información</li>
                    <li><strong>Propietarios:</strong> Los dueños verificados pueden actualizar su información directamente</li>
                    <li><strong>Usuarios:</strong> Sugerencias de restaurantes enviadas por nuestra comunidad</li>
                </ul>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-emerald-600">4.</span> Compartir Información
            </h2>
            <div class="text-gray-600 space-y-3">
                <p><strong>No vendemos</strong> tu información personal a terceros.</p>
                <p>Podemos compartir información con:</p>
                <ul class="list-disc list-inside ml-4 space-y-2">
                    <li>Proveedores de servicios que nos ayudan a operar el sitio</li>
                    <li>Autoridades legales cuando sea requerido por ley</li>
                </ul>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-emerald-600">5.</span> Tus Derechos
            </h2>
            <div class="text-gray-600 space-y-3">
                <p>Tienes derecho a:</p>
                <ul class="list-disc list-inside ml-4 space-y-2">
                    <li>Acceder a tu información personal</li>
                    <li>Corregir información inexacta</li>
                    <li>Solicitar la eliminación de tu cuenta</li>
                    <li>Optar por no recibir comunicaciones de marketing</li>
                </ul>
                <p>Los propietarios de restaurantes pueden <a href="/claim" class="text-emerald-600 hover:underline">reclamar su perfil</a> para gestionar su información.</p>
            </div>
        </section>

        <section>
            <h2 class=text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2>
                <span class="text-emerald-600">📱</span> Comunicaciones SMS
            </h2>
            <div class=text-gray-600 space-y-3>
                <p><strong>Verificación de Restaurantes:</strong> Durante el proceso de reclamar tu restaurante, podemos enviar códigos de verificación por SMS al número de teléfono registrado del negocio.</p>
                
                <p><strong>Tipos de Mensajes SMS:</strong></p>
                <ul class=list-disc list-inside ml-4 space-y-2>
                    <li>Códigos de verificación de 6 dígitos para confirmar la propiedad del restaurante</li>
                    <li>Códigos de autenticación para inicio de sesión (2FA)</li>
                    <li>Notificaciones de seguridad de la cuenta</li>
                </ul>
                
                <p><strong>Frecuencia:</strong> Los mensajes SMS se envían únicamente cuando tú inicias una acción que requiere verificación. No enviamos mensajes de marketing por SMS.</p>
                
                <p><strong>Costos:</strong> Pueden aplicar tarifas de mensajes y datos según tu plan con tu operador móvil.</p>
                
                <div class=bg-emerald-50 rounded-lg p-4 mt-4>
                    <p class="font-semibold text-emerald-800">Cancelar SMS</p>
                    <p class="text-emerald-700 text-sm">Responde <strong>STOP</strong> a cualquier mensaje para dejar de recibir verificaciones SMS. También puedes usar verificación por email como alternativa.</p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-emerald-600">6.</span> Cookies
            </h2>
            <div class="text-gray-600 space-y-3">
                <p>Utilizamos cookies para:</p>
                <ul class="list-disc list-inside ml-4 space-y-2">
                    <li>Mantener tu sesión activa</li>
                    <li>Recordar tus preferencias de idioma</li>
                    <li>Analizar el tráfico del sitio (Google Analytics)</li>
                </ul>
                <p>Puedes configurar tu navegador para rechazar cookies, aunque esto puede afectar la funcionalidad del sitio.</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-emerald-600">7.</span> Contacto
            </h2>
            <div class="text-gray-600 space-y-3">
                <p>Para preguntas sobre esta política de privacidad, contáctanos en:</p>
                <p class="font-semibold">
                    <a href="mailto:privacy@restaurantesmexicanosfamosos.com" class="text-emerald-600 hover:underline">
                        privacy@restaurantesmexicanosfamosos.com
                    </a>
                </p>
            </div>
        </section>
    </div>

    <!-- Back Link -->
    <div class="text-center mt-8">
        <a href="/" class="inline-flex items-center gap-2 text-gray-600 hover:text-emerald-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver al inicio
        </a>
    </div>
</div>
@endsection
