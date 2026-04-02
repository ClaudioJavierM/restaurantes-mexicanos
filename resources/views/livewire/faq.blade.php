<div style="min-height:100vh; background:#0B0B0B; color:#F5F5F5; font-family:'Poppins',sans-serif;">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        {{-- Header --}}
        <div class="text-center mb-14">
            <p class="text-xs font-bold tracking-widest uppercase mb-3" style="color:#D4AF37;">FAMER</p>
            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3rem); font-weight:800; color:#F5F5F5; margin:0 0 1rem;">
                Preguntas Frecuentes
            </h1>
            <p style="color:#9CA3AF; font-size:1.125rem; max-width:520px; margin:0 auto;">
                Encuentra respuestas a las preguntas más comunes sobre FAMER
            </p>
        </div>

        {{-- FAQ Sections --}}
        <div class="space-y-6">
            @foreach($faqs as $sectionKey => $section)
            <div style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.15); border-radius:1rem; overflow:hidden;">
                {{-- Section header --}}
                <div style="background:linear-gradient(135deg,rgba(212,175,55,0.12) 0%,rgba(212,175,55,0.06) 100%); border-bottom:1px solid rgba(212,175,55,0.15); padding:1rem 1.5rem;">
                    <h2 style="font-size:1rem; font-weight:700; color:#D4AF37; margin:0; letter-spacing:0.05em; text-transform:uppercase;">
                        {{ $section['title'] }}
                    </h2>
                </div>

                <div x-data="{ open: null }">
                    @foreach($section['items'] as $index => $item)
                    <div style="border-bottom:1px solid rgba(255,255,255,0.05);" class="last:border-0">
                        <button
                            @click="open = open === {{ $index }} ? null : {{ $index }}"
                            class="w-full text-left flex justify-between items-center transition-colors duration-200"
                            style="padding:1.125rem 1.5rem; background:transparent; border:none; cursor:pointer; color:#F5F5F5;"
                            onmouseover="this.style.background='rgba(212,175,55,0.04)'" onmouseout="this.style.background='transparent'">
                            <span style="font-weight:500; font-size:0.9375rem;">{{ $item['question'] }}</span>
                            <svg class="w-5 h-5 flex-shrink-0 ml-4 transform transition-transform duration-200"
                                 :class="{ 'rotate-180': open === {{ $index }} }"
                                 style="color:#D4AF37;"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open === {{ $index }}" x-collapse style="padding:0 1.5rem 1.25rem;">
                            <p style="color:#9CA3AF; line-height:1.7; font-size:0.9375rem; margin:0;">{{ $item['answer'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Contact CTA --}}
        <div class="mt-14" style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.15); border-radius:1rem; padding:2.5rem; text-align:center;">
            <div class="text-3xl mb-4">💬</div>
            <h3 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#F5F5F5; margin:0 0 0.5rem;">
                ¿Tienes más preguntas?
            </h3>
            <p style="color:#9CA3AF; margin:0 0 2rem;">Estamos aquí para ayudarte</p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center max-w-lg mx-auto">
                <a href="https://wa.me/14155238886?text=Hola%20Carmen,%20tengo%20una%20pregunta%20sobre%20FAMER"
                   target="_blank"
                   class="flex items-center justify-center gap-3 px-6 py-4 rounded-xl font-semibold transition-colors duration-200"
                   style="background:#22C55E; color:#fff;"
                   onmouseover="this.style.background='#16A34A'" onmouseout="this.style.background='#22C55E'">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    <div class="text-left">
                        <div class="font-bold text-sm">Carmen IA — WhatsApp</div>
                        <div class="text-xs opacity-80">Respuesta inmediata 24/7</div>
                    </div>
                </a>

                <a href="mailto:info@restaurantesmexicanosfamosos.com"
                   class="flex items-center justify-center gap-3 px-6 py-4 rounded-xl font-semibold transition-colors duration-200"
                   style="background:rgba(212,175,55,0.1); border:1px solid rgba(212,175,55,0.3); color:#D4AF37;"
                   onmouseover="this.style.background='rgba(212,175,55,0.18)'" onmouseout="this.style.background='rgba(212,175,55,0.1)'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <div class="text-left">
                        <div class="font-bold text-sm">Enviar Email</div>
                        <div class="text-xs" style="color:#9CA3AF;">Respuesta en 24 horas</div>
                    </div>
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
            "name": "Como reclamo mi restaurante?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Busca tu restaurante en FAMER, haz clic en Reclamar este restaurante y sigue el proceso de verificacion. Necesitaras demostrar que eres el propietario o gerente autorizado."
            }
        },
        {
            "@@type": "Question",
            "name": "Es gratis reclamar mi restaurante?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Si, reclamar y gestionar tu restaurante en FAMER es completamente gratis. No hay cargos ocultos ni suscripciones obligatorias."
            }
        }
    ]
}
</script>
@endpush
