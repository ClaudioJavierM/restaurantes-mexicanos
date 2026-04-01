<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Preguntas Frecuentes</h1>
            <p class="text-xl text-gray-600">
                Encuentra respuestas a las preguntas mas comunes sobre FAMER
            </p>
        </div>

        <!-- FAQ Sections -->
        <div class="space-y-8">
            @foreach($faqs as $sectionKey => $section)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">{{ $section['title'] }}</h2>
                </div>
                
                <div class="divide-y divide-gray-100" x-data="{ open: null }">
                    @foreach($section['items'] as $index => $item)
                    <div class="border-b border-gray-100 last:border-0">
                        <button 
                            @click="open = open === {{ $index }} ? null : {{ $index }}"
                            class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition"
                        >
                            <span class="font-medium text-gray-900">{{ $item['question'] }}</span>
                            <svg 
                                class="w-5 h-5 text-gray-500 transform transition-transform" 
                                :class="{ 'rotate-180': open === {{ $index }} }"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div 
                            x-show="open === {{ $index }}" 
                            x-collapse
                            class="px-6 pb-4"
                        >
                            <p class="text-gray-600 leading-relaxed">{{ $item['answer'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <!-- Contact Section -->
        <div class="mt-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-8 text-white">
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold mb-2">Tienes mas preguntas?</h3>
                <p class="text-blue-100">Estamos aqui para ayudarte</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6 max-w-2xl mx-auto">
                <!-- WhatsApp Carmen IA -->
                <a href="https://wa.me/14155238886?text=Hola%20Carmen,%20tengo%20una%20pregunta%20sobre%20FAMER" 
                   target="_blank"
                   class="flex items-center justify-center gap-3 px-6 py-4 bg-green-500 hover:bg-green-600 text-white rounded-xl font-medium transition transform hover:scale-105 shadow-lg">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    <div class="text-left">
                        <div class="font-bold">Chatea con Carmen IA</div>
                        <div class="text-sm text-green-100">Respuesta inmediata 24/7</div>
                    </div>
                </a>
                
                <!-- Email -->
                <a href="mailto:info@restaurantesmexicanosfamosos.com" 
                   class="flex items-center justify-center gap-3 px-6 py-4 bg-white/20 hover:bg-white/30 text-white rounded-xl font-medium transition transform hover:scale-105 backdrop-blur-sm border border-white/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <div class="text-left">
                        <div class="font-bold">Enviar Email</div>
                        <div class="text-sm text-blue-100">Respuesta en 24 horas</div>
                    </div>
                </a>
            </div>
            
            <!-- Phone number -->
            <div class="mt-6 text-center">
                <p class="text-blue-100 text-sm">O llamanos directamente:</p>
                <a href="tel:+18556066855" class="text-xl font-bold hover:text-yellow-300 transition">
                    +1 (855) 606-6855
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {
            "@@type": "Question",
            "name": "Que es FAMER?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "FAMER (Famous Mexican Restaurants) es el directorio mas completo de restaurantes mexicanos en Estados Unidos. Recopilamos informacion de multiples fuentes para ayudar a los comensales a encontrar autentica comida mexicana cerca de ellos."
            }
        },
        {
            "@@type": "Question",
            "name": "Que son los FAMER Awards?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Los FAMER Awards son reconocimientos anuales que destacan a los mejores restaurantes mexicanos. Se basan en calificaciones de Yelp, Google, votos de la comunidad y otros factores de calidad. Hay premios a nivel ciudad, estado y nacional."
            }
        },
        {
            "@@type": "Question",
            "name": "Por que mi restaurante ya aparece en FAMER?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "FAMER recopila informacion de fuentes publicas como Yelp, Google Maps y otros directorios. Si tu restaurante esta listado en estas plataformas, es probable que aparezca automaticamente en nuestro directorio. Esto nos permite ofrecer un catalogo completo sin requerir que cada restaurante se registre manualmente."
            }
        },
        {
            "@@type": "Question",
            "name": "De donde obtienen la informacion de los restaurantes?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Agregamos datos de Yelp, Google Places, y otras fuentes publicas. Combinamos calificaciones y resenas de multiples plataformas para dar una vision mas completa de cada restaurante."
            }
        },
        {
            "@@type": "Question",
            "name": "Puedo eliminar mi restaurante del directorio?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Si deseas que tu restaurante no aparezca en FAMER, puedes contactarnos. Sin embargo, te recomendamos reclamar tu perfil en lugar de eliminarlo, ya que aparecer en directorios ayuda a que mas clientes te encuentren."
            }
        },
        {
            "@@type": "Question",
            "name": "Por que mi calificacion es diferente a Yelp o Google?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "FAMER combina calificaciones de multiples fuentes (Yelp, Google, votos de usuarios) para crear un puntaje unificado. Esto puede resultar en una calificacion ligeramente diferente a la de una sola plataforma."
            }
        },
        {
            "@@type": "Question",
            "name": "Como reclamo mi restaurante?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Busca tu restaurante en FAMER, haz clic en Reclamar este restaurante y sigue el proceso de verificacion. Necesitaras demostrar que eres el propietario o gerente autorizado."
            }
        },
        {
            "@@type": "Question",
            "name": "Que beneficios tiene reclamar mi restaurante?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Al reclamar tu restaurante puedes: actualizar fotos e informacion, responder a resenas, acceder a estadisticas de visitas, generar codigos QR para votacion, y participar activamente en los FAMER Awards."
            }
        },
        {
            "@@type": "Question",
            "name": "Es gratis reclamar mi restaurante?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Si, reclamar y gestionar tu restaurante en FAMER es completamente gratis. No hay cargos ocultos ni suscripciones obligatorias."
            }
        },
        {
            "@@type": "Question",
            "name": "Como actualizo la informacion de mi restaurante?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Una vez que hayas reclamado tu restaurante, puedes acceder a tu dashboard y editar toda la informacion: horarios, menu, fotos, descripcion, y mas."
            }
        },
        {
            "@@type": "Question",
            "name": "Como funcionan los rankings FAMER?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Los rankings se calculan usando un algoritmo que considera: calificacion promedio de Yelp y Google, numero total de resenas, votos de la comunidad FAMER, y otros factores de calidad. Se actualizan periodicamente."
            }
        },
        {
            "@@type": "Question",
            "name": "Como puedo obtener mas votos para mi restaurante?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Reclama tu restaurante y usa el codigo QR que te proporcionamos. Colocalo en tu local para que tus clientes puedan escanearlo y votar facilmente. Tambien puedes compartir el enlace en tus redes sociales."
            }
        },
        {
            "@@type": "Question",
            "name": "Cada cuanto pueden votar los clientes?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Cada cliente puede votar una vez al mes por cada restaurante. Esto asegura que los votos reflejen experiencias recientes y evita manipulacion."
            }
        }
    ]
}
</script>
@endpush
