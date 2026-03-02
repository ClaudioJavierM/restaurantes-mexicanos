@extends('layouts.app')

@section('title', __('app.site_name') . ' - Términos de Uso')
@section('meta_description', 'Términos y condiciones de uso de Restaurantes Mexicanos Famosos.')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-display font-bold bg-gradient-to-r from-emerald-600 via-red-600 to-orange-600 bg-clip-text text-transparent mb-4">
            Términos de Uso
        </h1>
        <p class="text-gray-600">Última actualización: {{ date('d/m/Y') }}</p>
    </div>

    <!-- Content -->
    <div class="bg-white rounded-2xl shadow-xl p-8 space-y-8">
        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-red-600">1.</span> Aceptación de Términos
            </h2>
            <div class="text-gray-600 space-y-3">
                <p>Al acceder y utilizar Restaurantes Mexicanos Famosos ("el Sitio"), aceptas estos términos de uso. Si no estás de acuerdo con alguna parte de estos términos, no debes usar el sitio.</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-red-600">2.</span> Descripción del Servicio
            </h2>
            <div class="text-gray-600 space-y-3">
                <p>Somos un directorio de restaurantes mexicanos en Estados Unidos que:</p>
                <ul class="list-disc list-inside ml-4 space-y-2">
                    <li>Recopila información pública de restaurantes</li>
                    <li>Permite a los usuarios buscar y descubrir restaurantes</li>
                    <li>Ofrece a los propietarios la posibilidad de gestionar sus perfiles</li>
                    <li>Proporciona guías de restaurantes por ciudad y estado</li>
                </ul>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-red-600">3.</span> Información de Restaurantes
            </h2>
            <div class="text-gray-600 space-y-3">
                <p><strong>Precisión:</strong> Hacemos esfuerzos razonables para mantener la información actualizada, pero no garantizamos su exactitud. La información puede cambiar sin previo aviso.</p>
                <p><strong>Fuentes:</strong> Los datos provienen de APIs públicas (Yelp, Google) y de los propios propietarios. Las calificaciones y reseñas son de terceros.</p>
                <p><strong>Responsabilidad:</strong> No somos responsables de la calidad del servicio o productos de los restaurantes listados.</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-red-600">4.</span> Cuentas de Usuario
            </h2>
            <div class="text-gray-600 space-y-3">
                <p>Al crear una cuenta, te comprometes a:</p>
                <ul class="list-disc list-inside ml-4 space-y-2">
                    <li>Proporcionar información veraz y actualizada</li>
                    <li>Mantener la confidencialidad de tu contraseña</li>
                    <li>Notificarnos de cualquier uso no autorizado</li>
                    <li>Ser responsable de toda actividad en tu cuenta</li>
                </ul>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-red-600">5.</span> Propietarios de Restaurantes
            </h2>
            <div class="text-gray-600 space-y-3">
                <p>Si reclamas un perfil de restaurante:</p>
                <ul class="list-disc list-inside ml-4 space-y-2">
                    <li>Debes ser el propietario legítimo o representante autorizado</li>
                    <li>Eres responsable de la información que publiques</li>
                    <li>Te comprometes a mantener la información actualizada</li>
                    <li>No debes publicar contenido falso o engañoso</li>
                </ul>
                <p>Nos reservamos el derecho de verificar la propiedad y remover perfiles fraudulentos.</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-red-600">6.</span> Uso Prohibido
            </h2>
            <div class="text-gray-600 space-y-3">
                <p>No debes:</p>
                <ul class="list-disc list-inside ml-4 space-y-2">
                    <li>Usar el sitio para actividades ilegales</li>
                    <li>Extraer datos de forma automatizada sin autorización (scraping)</li>
                    <li>Interferir con el funcionamiento del sitio</li>
                    <li>Suplantar la identidad de otros</li>
                    <li>Publicar contenido ofensivo, difamatorio o falso</li>
                </ul>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-red-600">7.</span> Propiedad Intelectual
            </h2>
            <div class="text-gray-600 space-y-3">
                <p>El contenido del sitio (diseño, código, logotipos) es propiedad de Restaurantes Mexicanos Famosos o sus licenciantes.</p>
                <p>Las marcas Yelp® y Google® pertenecen a sus respectivos propietarios.</p>
                <p>Las fotos y contenido de restaurantes pertenecen a sus respectivos dueños o proveedores de datos.</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-red-600">8.</span> Limitación de Responsabilidad
            </h2>
            <div class="text-gray-600 space-y-3">
                <p>El sitio se proporciona "tal cual". No garantizamos:</p>
                <ul class="list-disc list-inside ml-4 space-y-2">
                    <li>Disponibilidad ininterrumpida del servicio</li>
                    <li>Exactitud de la información de restaurantes</li>
                    <li>Calidad de los restaurantes listados</li>
                </ul>
                <p>No seremos responsables por daños indirectos derivados del uso del sitio.</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-red-600">9.</span> Modificaciones
            </h2>
            <div class="text-gray-600 space-y-3">
                <p>Nos reservamos el derecho de modificar estos términos en cualquier momento. Los cambios serán efectivos al publicarse en el sitio. El uso continuado del sitio constituye aceptación de los términos modificados.</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="text-red-600">10.</span> Contacto
            </h2>
            <div class="text-gray-600 space-y-3">
                <p>Para preguntas sobre estos términos:</p>
                <p class="font-semibold">
                    <a href="mailto:legal@restaurantesmexicanosfamosos.com" class="text-red-600 hover:underline">
                        legal@restaurantesmexicanosfamosos.com
                    </a>
                </p>
            </div>
        </section>
    </div>

    <!-- Back Link -->
    <div class="text-center mt-8">
        <a href="/" class="inline-flex items-center gap-2 text-gray-600 hover:text-red-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver al inicio
        </a>
    </div>
</div>
@endsection
