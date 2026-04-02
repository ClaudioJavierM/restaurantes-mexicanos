<div class="min-h-screen" style="background:#0B0B0B; color:#F5F5F5; font-family:'Poppins',sans-serif;">

    {{-- ═══════════════════════════════════════ --}}
    {{-- HERO                                    --}}
    {{-- ═══════════════════════════════════════ --}}
    <section style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 50%,#0B0B0B 100%); border-bottom:1px solid rgba(212,175,55,0.25); padding:5rem 1.5rem 4rem; position:relative; overflow:hidden;">
        {{-- Subtle gold radial glow --}}
        <div style="position:absolute; inset:0; background:radial-gradient(ellipse 70% 60% at 50% 40%, rgba(212,175,55,0.07) 0%, transparent 70%); pointer-events:none;"></div>

        <div style="max-width:860px; margin:0 auto; text-align:center; position:relative; z-index:1;">
            {{-- Trophy --}}
            <div style="display:inline-flex; align-items:center; justify-content:center; width:6rem; height:6rem; border-radius:50%; background:rgba(212,175,55,0.12); border:2px solid rgba(212,175,55,0.4); margin-bottom:1.75rem; font-size:3rem; animation:pulse 2s infinite;">
                🏆
            </div>

            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2.8rem,7vw,5rem); font-weight:800; line-height:1.1; margin:0 0 0.5rem;">
                FAMER Awards
            </h1>
            <div style="font-family:'Playfair Display',serif; font-size:clamp(2.5rem,6vw,4.5rem); font-weight:800; color:#D4AF37; margin-bottom:1.5rem; line-height:1;">
                2026
            </div>

            <p style="font-size:1.125rem; color:#CCCCCC; max-width:640px; margin:0 auto 2.5rem; line-height:1.7;">
                Buscamos a los <strong style="color:#F5F5F5;">Mejores Restaurantes Mexicanos</strong> de Estados Unidos.
                12 meses de evaluación. Tu voto decide.
            </p>

            {{-- Countdown / Live badge --}}
            @if($daysUntilStart > 0)
            <div style="display:inline-flex; align-items:center; gap:0.5rem; background:rgba(212,175,55,0.1); border:1px solid rgba(212,175,55,0.35); border-radius:999px; padding:0.6rem 1.5rem; margin-bottom:2.5rem; font-size:1rem;">
                <span>⏰</span>
                <span style="font-weight:700; color:#D4AF37;">{{ $daysUntilStart }} días</span>
                <span style="color:#CCCCCC;">para el inicio</span>
            </div>
            @else
            <div style="display:inline-flex; align-items:center; gap:0.5rem; background:rgba(74,222,128,0.1); border:1px solid rgba(74,222,128,0.35); border-radius:999px; padding:0.6rem 1.5rem; margin-bottom:2.5rem; font-size:1rem;">
                <span>🎉</span>
                <span style="font-weight:700; color:#4ade80;">¡Ya comenzó!</span>
            </div>
            @endif

            {{-- CTAs --}}
            <div style="display:flex; flex-wrap:wrap; gap:1rem; justify-content:center;">
                <button wire:click="toggleNominationForm"
                        style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.875rem 2rem; background:#D4AF37; color:#0B0B0B; font-weight:700; font-size:1rem; border-radius:0.75rem; border:none; cursor:pointer; transition:background 0.2s;"
                        onmouseover="this.style.background='#B8962E'" onmouseout="this.style.background='#D4AF37'">
                    📝 Nominar un Restaurante
                </button>
                <a href="{{ url('/votar') }}"
                   style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.875rem 2rem; background:rgba(212,175,55,0.12); border:1px solid rgba(212,175,55,0.5); color:#D4AF37; font-weight:700; font-size:1rem; border-radius:0.75rem; text-decoration:none; transition:all 0.2s;"
                   onmouseover="this.style.background='rgba(212,175,55,0.2)'" onmouseout="this.style.background='rgba(212,175,55,0.12)'">
                    🗳️ Votar Ahora
                </a>
                <a href="{{ url('/guia') }}"
                   style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.875rem 2rem; border:1px solid rgba(212,175,55,0.3); color:#9CA3AF; font-weight:600; font-size:1rem; border-radius:0.75rem; text-decoration:none; transition:border-color 0.2s;"
                   onmouseover="this.style.borderColor='rgba(212,175,55,0.5)';this.style.color='#D4AF37'" onmouseout="this.style.borderColor='rgba(212,175,55,0.3)';this.style.color='#9CA3AF'">
                    📊 Ver Rankings
                </a>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════ --}}
    {{-- STATS                                   --}}
    {{-- ═══════════════════════════════════════ --}}
    <section style="padding:3rem 1.5rem; background:#111111; border-bottom:1px solid #1E1E1E;">
        <div style="max-width:800px; margin:0 auto; display:grid; grid-template-columns:repeat(3,1fr); gap:1.25rem; text-align:center;">
            <div style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.2); border-radius:1rem; padding:1.5rem 1rem;">
                <div style="font-family:'Playfair Display',serif; font-size:2.5rem; font-weight:800; color:#D4AF37;">{{ number_format($totalRestaurants) }}</div>
                <div style="font-size:0.875rem; color:#9CA3AF; margin-top:0.25rem;">Restaurantes Evaluados</div>
            </div>
            <div style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.2); border-radius:1rem; padding:1.5rem 1rem;">
                <div style="font-family:'Playfair Display',serif; font-size:2.5rem; font-weight:800; color:#D4AF37;">{{ number_format($totalCities) }}</div>
                <div style="font-size:0.875rem; color:#9CA3AF; margin-top:0.25rem;">Ciudades Cubiertas</div>
            </div>
            <div style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.2); border-radius:1rem; padding:1.5rem 1rem;">
                <div style="font-family:'Playfair Display',serif; font-size:2.5rem; font-weight:800; color:#D4AF37;">{{ $totalStates }}</div>
                <div style="font-size:0.875rem; color:#9CA3AF; margin-top:0.25rem;">Estados Participantes</div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════ --}}
    {{-- HOW IT WORKS                            --}}
    {{-- ═══════════════════════════════════════ --}}
    <section style="padding:5rem 1.5rem; background:#0B0B0B;">
        <div style="max-width:1024px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:3.5rem;">
                <p style="font-size:0.75rem; font-weight:600; color:#D4AF37; letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.75rem;">El Proceso</p>
                <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.75rem,4vw,2.5rem); font-weight:700; margin:0;">¿Cómo Funciona?</h2>
                <p style="color:#9CA3AF; margin-top:0.75rem; font-size:1rem;">Un proceso transparente de 12 meses</p>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1.5rem;">
                @foreach([
                    ['📊','1','Recopilamos Datos','Integramos calificaciones de Google, Yelp, Facebook y nuestra comunidad FAMER.'],
                    ['🗳️','2','Tú Votas','Cada mes puedes votar por tus restaurantes favoritos en tu ciudad.'],
                    ['🏅','3','Premiamos Mensual','Cada mes anunciamos al "Restaurante del Mes" de cada ciudad.'],
                    ['🏆','4','Gran Final','En diciembre certificamos a los mejores del año por ciudad, estado y nacional.'],
                ] as [$icon,$num,$title,$desc])
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:14px; padding:2rem 1.5rem; text-align:center; position:relative; transition:border-color 0.2s;"
                     onmouseover="this.style.borderColor='rgba(212,175,55,0.4)'" onmouseout="this.style.borderColor='#2A2A2A'">
                    <div style="display:inline-flex; align-items:center; justify-content:center; width:3rem; height:3rem; background:#D4AF37; border-radius:50%; font-size:1rem; font-weight:800; color:#0B0B0B; margin-bottom:1rem;">{{ $num }}</div>
                    <div style="font-size:2rem; margin-bottom:0.75rem;">{{ $icon }}</div>
                    <h3 style="font-size:1rem; font-weight:700; color:#F5F5F5; margin:0 0 0.5rem;">{{ $title }}</h3>
                    <p style="font-size:0.875rem; color:#9CA3AF; margin:0; line-height:1.6;">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════ --}}
    {{-- AWARD CATEGORIES                        --}}
    {{-- ═══════════════════════════════════════ --}}
    <section style="padding:5rem 1.5rem; background:#111111;">
        <div style="max-width:1024px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:3.5rem;">
                <p style="font-size:0.75rem; font-weight:600; color:#D4AF37; letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.75rem;">Reconocimientos</p>
                <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.75rem,4vw,2.5rem); font-weight:700; margin:0;">Categorías de Premios</h2>
                <p style="color:#9CA3AF; margin-top:0.75rem; font-size:1rem;">Reconocemos a los mejores en cada nivel</p>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:1.5rem;">
                {{-- Ciudad --}}
                <div style="background:#1A1A1A; border:2px solid rgba(212,175,55,0.5); border-radius:16px; padding:2rem; position:relative; overflow:hidden;">
                    <div style="position:absolute; top:0; right:0; width:6rem; height:6rem; background:radial-gradient(circle, rgba(212,175,55,0.1) 0%, transparent 70%);"></div>
                    <div style="font-size:2.5rem; margin-bottom:1rem;">🏙️</div>
                    <div style="font-size:0.7rem; font-weight:700; color:#D4AF37; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.5rem;">Nivel Ciudad</div>
                    <h3 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#F5F5F5; margin:0 0 0.75rem;">Top 10 por Ciudad</h3>
                    <p style="font-size:0.875rem; color:#9CA3AF; margin:0 0 1.25rem; line-height:1.6;">Los 10 mejores restaurantes mexicanos de cada ciudad. Competencia local.</p>
                    <div style="display:inline-block; background:rgba(212,175,55,0.1); border:1px solid rgba(212,175,55,0.3); border-radius:999px; padding:0.25rem 0.875rem; font-size:0.75rem; color:#D4AF37; font-weight:600;">
                        +1,500 ciudades participando
                    </div>
                </div>

                {{-- Estado --}}
                <div style="background:#1A1A1A; border:2px solid rgba(212,175,55,0.3); border-radius:16px; padding:2rem; position:relative; overflow:hidden;">
                    <div style="position:absolute; top:0; right:0; width:6rem; height:6rem; background:radial-gradient(circle, rgba(212,175,55,0.08) 0%, transparent 70%);"></div>
                    <div style="font-size:2.5rem; margin-bottom:1rem;">📍</div>
                    <div style="font-size:0.7rem; font-weight:700; color:#D4AF37; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.5rem;">Nivel Estado</div>
                    <h3 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#F5F5F5; margin:0 0 0.75rem;">Top 100 por Estado</h3>
                    <p style="font-size:0.875rem; color:#9CA3AF; margin:0 0 1.25rem; line-height:1.6;">Los 100 mejores de cada estado. Competencia estatal.</p>
                    <div style="display:inline-block; background:rgba(212,175,55,0.1); border:1px solid rgba(212,175,55,0.3); border-radius:999px; padding:0.25rem 0.875rem; font-size:0.75rem; color:#D4AF37; font-weight:600;">
                        50 estados evaluados
                    </div>
                </div>

                {{-- Nacional --}}
                <div style="background:linear-gradient(135deg,#1A1A1A 0%,#1F1600 100%); border:2px solid #D4AF37; border-radius:16px; padding:2rem; position:relative; overflow:hidden; box-shadow:0 0 40px rgba(212,175,55,0.1);">
                    <div style="position:absolute; top:-1rem; right:-1rem; width:8rem; height:8rem; background:radial-gradient(circle, rgba(212,175,55,0.15) 0%, transparent 70%);"></div>
                    <div style="position:absolute; top:0.75rem; right:1rem; background:#D4AF37; color:#0B0B0B; font-size:0.65rem; font-weight:800; padding:0.15rem 0.6rem; border-radius:999px; text-transform:uppercase; letter-spacing:0.05em;">
                        ⭐ Premio Mayor
                    </div>
                    <div style="font-size:2.5rem; margin-bottom:1rem;">🇺🇸</div>
                    <div style="font-size:0.7rem; font-weight:700; color:#D4AF37; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.5rem;">Nivel Nacional</div>
                    <h3 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#F5F5F5; margin:0 0 0.75rem;">Top 100 Nacional</h3>
                    <p style="font-size:0.875rem; color:#9CA3AF; margin:0 0 1.25rem; line-height:1.6;">Los 100 restaurantes mexicanos que DEBES visitar en USA.</p>
                    <div style="display:inline-block; background:rgba(212,175,55,0.15); border:1px solid rgba(212,175,55,0.5); border-radius:999px; padding:0.25rem 0.875rem; font-size:0.75rem; color:#D4AF37; font-weight:600;">
                        La élite nacional
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════ --}}
    {{-- FOR RESTAURANT OWNERS                   --}}
    {{-- ═══════════════════════════════════════ --}}
    <section style="padding:5rem 1.5rem; background:#0B0B0B;">
        <div style="max-width:1024px; margin:0 auto; display:grid; grid-template-columns:1fr 1fr; gap:3rem; align-items:center;">
            <div>
                <p style="font-size:0.75rem; font-weight:600; color:#D4AF37; letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.75rem;">Para Dueños</p>
                <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.75rem,4vw,2.5rem); font-weight:700; margin:0 0 1rem;">¿Eres dueño de un restaurante?</h2>
                <p style="color:#CCCCCC; font-size:1.0625rem; line-height:1.7; margin:0 0 2rem;">
                    Inscríbete en FAMER Awards 2026 y obtén visibilidad, credibilidad y más clientes.
                </p>
                <ul style="list-style:none; padding:0; margin:0 0 2rem; display:flex; flex-direction:column; gap:0.875rem;">
                    @foreach(['Badge verificado en tu perfil','Certificado descargable si ganas','Promoción en nuestras redes sociales','Dashboard con tu posición en tiempo real'] as $item)
                    <li style="display:flex; align-items:center; gap:0.75rem; font-size:0.9375rem; color:#CCCCCC;">
                        <span style="display:inline-flex; align-items:center; justify-content:center; width:1.5rem; height:1.5rem; background:rgba(212,175,55,0.15); border:1px solid rgba(212,175,55,0.4); border-radius:50%; color:#D4AF37; font-size:0.75rem; flex-shrink:0;">✓</span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}"
                   style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.875rem 2rem; background:#D4AF37; color:#0B0B0B; font-weight:700; font-size:1rem; border-radius:0.75rem; text-decoration:none; transition:background 0.2s;"
                   onmouseover="this.style.background='#B8962E'" onmouseout="this.style.background='#D4AF37'">
                    Registrar mi Restaurante
                    <svg style="width:1.25rem; height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            {{-- Certificate preview --}}
            <div style="background:linear-gradient(135deg,#1A1A1A 0%,#1F1600 100%); border:2px solid #D4AF37; border-radius:20px; padding:2.5rem; text-align:center; box-shadow:0 0 60px rgba(212,175,55,0.15);">
                <div style="font-size:4rem; margin-bottom:1rem;">🏆</div>
                <div style="font-size:0.7rem; font-weight:700; color:#D4AF37; text-transform:uppercase; letter-spacing:0.12em; margin-bottom:0.5rem;">Certificado Oficial</div>
                <div style="font-family:'Playfair Display',serif; font-size:1.625rem; font-weight:700; color:#F5F5F5; margin-bottom:0.5rem;">FAMER Awards 2026</div>
                <div style="font-size:1rem; color:#D4AF37; margin-bottom:1.5rem;">Top 10 Dallas, TX</div>
                <div style="border-top:1px solid rgba(212,175,55,0.2); padding-top:1.25rem;">
                    <div style="font-size:0.75rem; color:#9CA3AF; margin-bottom:0.5rem;">Basado en evaluaciones de</div>
                    <div style="display:flex; justify-content:center; gap:1rem; font-size:1.5rem;">
                        <span title="Google">📍</span>
                        <span title="Yelp">⭐</span>
                        <span title="Facebook">👍</span>
                        <span title="FAMER">🏆</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════ --}}
    {{-- FAQ                                     --}}
    {{-- ═══════════════════════════════════════ --}}
    <section style="padding:5rem 1.5rem; background:#111111;">
        <div style="max-width:740px; margin:0 auto;">
            <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.75rem,4vw,2.25rem); font-weight:700; text-align:center; margin:0 0 3rem;">Preguntas Frecuentes</h2>

            <div style="display:flex; flex-direction:column; gap:0.75rem;">
                @foreach([
                    ['¿Cómo se calculan los rankings?','Combinamos calificaciones de Google (12%), Yelp (10%), Facebook (8%), TripAdvisor (10%) y métricas propias de FAMER (60%) incluyendo votos de usuarios, reseñas verificadas y actividad del negocio.'],
                    ['¿Cuántas veces puedo votar?','Puedes votar una vez por restaurante por mes. Esto significa que puedes votar por varios restaurantes diferentes cada mes.'],
                    ['¿Qué ganan los restaurantes?','Los ganadores reciben un certificado oficial, badge en su perfil, promoción en nuestras redes sociales y reconocimiento como uno de los mejores de su categoría.'],
                    ['¿Cómo nomino un restaurante?','Haz clic en "Nominar un Restaurante" arriba y llena el formulario. Revisaremos la nominación y agregaremos el restaurante si cumple los criterios.'],
                ] as [$q,$a])
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; overflow:hidden;">
                    <button onclick="const ans=this.nextElementSibling; const ico=this.querySelector('span:last-child'); ans.style.display=ans.style.display==='block'?'none':'block'; ico.textContent=ans.style.display==='block'?'−':'+';"
                            style="width:100%; text-align:left; padding:1.125rem 1.25rem; background:transparent; border:none; cursor:pointer; display:flex; justify-content:space-between; align-items:center; gap:1rem;">
                        <span style="font-size:0.9375rem; font-weight:600; color:#F5F5F5;">{{ $q }}</span>
                        <span style="color:#D4AF37; font-size:1.25rem; font-weight:700; flex-shrink:0; line-height:1;">+</span>
                    </button>
                    <div style="display:none; padding:0 1.25rem 1.25rem; font-size:0.875rem; color:#9CA3AF; line-height:1.7;">{{ $a }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════ --}}
    {{-- FINAL CTA                               --}}
    {{-- ═══════════════════════════════════════ --}}
    <section style="padding:5rem 1.5rem; background:linear-gradient(135deg,#1A1A1A 0%,#0B0B0B 50%,#1A1A1A 100%); border-top:1px solid rgba(212,175,55,0.2); text-align:center;">
        <div style="max-width:680px; margin:0 auto;">
            <div style="font-size:3rem; margin-bottom:1rem;">🌮</div>
            <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.75rem,4vw,2.5rem); font-weight:700; margin:0 0 1.25rem;">
                ¿Listo para encontrar los mejores restaurantes mexicanos?
            </h2>
            <div style="display:flex; flex-wrap:wrap; gap:1rem; justify-content:center; margin-top:2rem;">
                <button wire:click="toggleNominationForm"
                        style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.875rem 2rem; background:#D4AF37; color:#0B0B0B; font-weight:700; font-size:1rem; border-radius:0.75rem; border:none; cursor:pointer; transition:background 0.2s; box-shadow:0 4px 24px rgba(212,175,55,0.25);"
                        onmouseover="this.style.background='#B8962E'" onmouseout="this.style.background='#D4AF37'">
                    📝 Nominar Restaurante
                </button>
                <a href="{{ url('/guia') }}"
                   style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.875rem 2rem; border:1px solid rgba(212,175,55,0.4); color:#D4AF37; font-weight:600; font-size:1rem; border-radius:0.75rem; text-decoration:none; transition:border-color 0.2s;"
                   onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='rgba(212,175,55,0.4)'">
                    📊 Ver Rankings
                </a>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════ --}}
    {{-- NOMINATION MODAL (Livewire)             --}}
    {{-- ═══════════════════════════════════════ --}}
    @if($showNominationForm)
    <div style="position:fixed; inset:0; background:rgba(0,0,0,0.75); backdrop-filter:blur(4px); z-index:50; display:flex; align-items:center; justify-content:center; padding:1rem;"
         wire:click.self="toggleNominationForm">
        <div style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.3); border-radius:20px; width:100%; max-width:520px; max-height:90vh; overflow-y:auto; box-shadow:0 25px 50px rgba(0,0,0,0.6);">

            @if($nominationSubmitted)
            <div style="padding:3rem; text-align:center;">
                <div style="font-size:4rem; margin-bottom:1rem;">🎉</div>
                <h3 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#F5F5F5; margin:0 0 0.75rem;">¡Gracias por tu nominación!</h3>
                <p style="color:#9CA3AF; margin:0 0 2rem;">Revisaremos el restaurante y te notificaremos cuando sea agregado.</p>
                <button wire:click="toggleNominationForm"
                        style="padding:0.75rem 2rem; background:#D4AF37; color:#0B0B0B; font-weight:700; border-radius:0.75rem; border:none; cursor:pointer;">
                    Cerrar
                </button>
            </div>
            @else
            {{-- Header --}}
            <div style="padding:1.5rem 1.5rem 1rem; border-bottom:1px solid #2A2A2A; display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <h3 style="font-size:1.125rem; font-weight:700; color:#F5F5F5; margin:0;">📝 Nominar un Restaurante</h3>
                    <p style="font-size:0.875rem; color:#9CA3AF; margin:0.25rem 0 0;">¿Conoces un restaurante mexicano que debemos incluir?</p>
                </div>
                <button wire:click="toggleNominationForm"
                        style="background:transparent; border:none; color:#6B7280; cursor:pointer; padding:0.25rem;"
                        onmouseover="this.style.color='#F5F5F5'" onmouseout="this.style.color='#6B7280'">
                    <svg style="width:1.5rem;height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Form --}}
            <form wire:submit="submitNomination" style="padding:1.5rem; display:flex; flex-direction:column; gap:1.25rem;">
                @php $inputStyle = "width:100%; padding:0.625rem 0.875rem; background:#0B0B0B; border:1px solid #2A2A2A; border-radius:0.5rem; color:#F5F5F5; font-size:0.875rem; box-sizing:border-box; outline:none;"; @endphp

                <div>
                    <label style="display:block; font-size:0.8125rem; font-weight:600; color:#CCCCCC; margin-bottom:0.375rem;">Nombre del Restaurante *</label>
                    <input type="text" wire:model="restaurantName" style="{{ $inputStyle }}" placeholder="Ej: Taquería El Güero"
                           onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'">
                    @error('restaurantName') <span style="color:#F87171; font-size:0.75rem;">{{ $message }}</span> @enderror
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div>
                        <label style="display:block; font-size:0.8125rem; font-weight:600; color:#CCCCCC; margin-bottom:0.375rem;">Ciudad *</label>
                        <input type="text" wire:model="city" style="{{ $inputStyle }}" placeholder="Dallas"
                               onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'">
                        @error('city') <span style="color:#F87171; font-size:0.75rem;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display:block; font-size:0.8125rem; font-weight:600; color:#CCCCCC; margin-bottom:0.375rem;">Estado *</label>
                        <select wire:model="stateCode"
                                style="{{ $inputStyle }} appearance:none;"
                                onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'">
                            <option value="" style="background:#1A1A1A;">Seleccionar</option>
                            @foreach($this->states as $state)
                                <option value="{{ $state->code }}" style="background:#1A1A1A;">{{ $state->name }}</option>
                            @endforeach
                        </select>
                        @error('stateCode') <span style="color:#F87171; font-size:0.75rem;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label style="display:block; font-size:0.8125rem; font-weight:600; color:#CCCCCC; margin-bottom:0.375rem;">Dirección</label>
                    <input type="text" wire:model="address" style="{{ $inputStyle }}" placeholder="123 Main St"
                           onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'">
                </div>

                <div>
                    <label style="display:block; font-size:0.8125rem; font-weight:600; color:#CCCCCC; margin-bottom:0.375rem;">Link de Google Maps</label>
                    <input type="url" wire:model="googleMapsUrl" style="{{ $inputStyle }}" placeholder="https://maps.google.com/..."
                           onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'">
                </div>

                <div>
                    <label style="display:block; font-size:0.8125rem; font-weight:600; color:#CCCCCC; margin-bottom:0.375rem;">¿Por qué lo nominás?</label>
                    <textarea wire:model="whyNominate" rows="3"
                              style="{{ $inputStyle }} resize:vertical;"
                              placeholder="Cuéntanos qué lo hace especial..."
                              onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'"></textarea>
                </div>

                <hr style="border:none; border-top:1px solid #2A2A2A; margin:0;">

                <div>
                    <label style="display:block; font-size:0.8125rem; font-weight:600; color:#CCCCCC; margin-bottom:0.375rem;">Tu nombre</label>
                    <input type="text" wire:model="nominatorName" style="{{ $inputStyle }}" placeholder="Tu nombre"
                           onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'">
                </div>

                <div>
                    <label style="display:block; font-size:0.8125rem; font-weight:600; color:#CCCCCC; margin-bottom:0.375rem;">Tu email *</label>
                    <input type="email" wire:model="nominatorEmail" style="{{ $inputStyle }}" placeholder="tu@email.com"
                           onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'">
                    @error('nominatorEmail') <span style="color:#F87171; font-size:0.75rem;">{{ $message }}</span> @enderror
                </div>

                <button type="submit"
                        style="width:100%; padding:0.875rem; background:#D4AF37; color:#0B0B0B; font-weight:700; font-size:1rem; border-radius:0.75rem; border:none; cursor:pointer; transition:background 0.2s;"
                        onmouseover="this.style.background='#B8962E'" onmouseout="this.style.background='#D4AF37'">
                    Enviar Nominación
                </button>
            </form>
            @endif
        </div>
    </div>
    @endif

</div>
