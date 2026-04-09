@php $today = date('Y-m-d'); @endphp

<div style="background:#0B0B0B; min-height:100vh; font-family:'Poppins',sans-serif; color:#F5F5F5;">

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- HERO                                                        --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 50%,#0B0B0B 100%); padding:4rem 1.5rem 3rem; text-align:center; border-bottom:1px solid #2A2A2A; position:relative; overflow:hidden;">
        {{-- decorative background accent --}}
        <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:600px; height:600px; background:radial-gradient(circle, rgba(212,175,55,0.04) 0%, transparent 70%); pointer-events:none;"></div>

        <div style="font-size:3rem; margin-bottom:1rem; position:relative;">🍽️</div>
        <h1 style="font-family:'Playfair Display',Georgia,serif; font-size:clamp(2rem,5vw,3rem); font-weight:700; color:#D4AF37; margin:0 0 1rem; line-height:1.2; position:relative;">
            Solicitar Catering
        </h1>
        <p style="font-size:1.0625rem; color:#9CA3AF; max-width:520px; margin:0 auto; line-height:1.7; position:relative;">
            Conectamos tu evento con los mejores restaurantes mexicanos auténticos
            @if($restaurant)
                — <span style="color:#D4AF37;">{{ $restaurant->name }}</span>
            @endif
        </p>
    </section>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- WHY FAMER CATERING — 3 feature cards                       --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section style="padding:2.5rem 1.5rem; max-width:900px; margin:0 auto;">
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:1.25rem;">

            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:1rem; padding:1.5rem 1.25rem; text-align:center;">
                <div style="font-size:2rem; margin-bottom:0.75rem;">🌮</div>
                <h3 style="font-size:1rem; font-weight:600; color:#F5F5F5; margin:0 0 0.5rem;">Cocina Auténtica</h3>
                <p style="font-size:0.8125rem; color:#6B7280; margin:0; line-height:1.6;">Restaurantes verificados con cocina 100% mexicana y sabores genuinos</p>
            </div>

            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:1rem; padding:1.5rem 1.25rem; text-align:center;">
                <div style="font-size:2rem; margin-bottom:0.75rem;">📋</div>
                <h3 style="font-size:1rem; font-weight:600; color:#F5F5F5; margin:0 0 0.5rem;">Menú Personalizado</h3>
                <p style="font-size:0.8125rem; color:#6B7280; margin:0; line-height:1.6;">Adaptamos el menú a tu evento, preferencias y presupuesto disponible</p>
            </div>

            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:1rem; padding:1.5rem 1.25rem; text-align:center;">
                <div style="font-size:2rem; margin-bottom:0.75rem;">✅</div>
                <h3 style="font-size:1rem; font-weight:600; color:#F5F5F5; margin:0 0 0.5rem;">Sin Complicaciones</h3>
                <p style="font-size:0.8125rem; color:#6B7280; margin:0; line-height:1.6;">Coordinamos todo directamente con el restaurante para que solo disfrutes</p>
            </div>

        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- MAIN CONTENT                                                --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section style="padding:0 1.5rem 4rem; max-width:760px; margin:0 auto;">

        @if($submitted)
        {{-- ─── SUCCESS STATE ─── --}}
        <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:1.25rem; padding:3rem 2rem; text-align:center; margin-top:1rem;">
            <div style="width:72px; height:72px; background:rgba(34,197,94,0.12); border:2px solid rgba(34,197,94,0.4); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; font-size:2rem;">✅</div>
            <h2 style="font-family:'Playfair Display',Georgia,serif; font-size:1.875rem; font-weight:700; color:#D4AF37; margin:0 0 0.75rem;">¡Solicitud enviada!</h2>
            <p style="font-size:1rem; color:#9CA3AF; max-width:420px; margin:0 auto 2rem; line-height:1.7;">
                Nos pondremos en contacto contigo en menos de 24 horas para coordinar todos los detalles de tu evento.
            </p>
            <a href="/" style="display:inline-block; background:#D4AF37; color:#0B0B0B; font-weight:700; font-size:0.9375rem; padding:0.875rem 2rem; border-radius:0.625rem; text-decoration:none; font-family:'Poppins',sans-serif; letter-spacing:0.02em;">
                Volver al inicio
            </a>
        </div>

        @else
        {{-- ─── FORM ─── --}}
        <form wire:submit.prevent="submit" style="margin-top:0.5rem;">

            {{-- Validation errors --}}
            @if($errors->any())
            <div style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); border-radius:0.75rem; padding:1rem 1.25rem; margin-bottom:1.5rem;">
                <ul style="margin:0; padding-left:1.25rem; color:#FCA5A5; font-size:0.875rem; line-height:1.8;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- ── SECTION: Tu Información ── --}}
            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:1.25rem; padding:1.75rem; margin-bottom:1.25rem;">
                <h2 style="font-family:'Playfair Display',Georgia,serif; font-size:1.25rem; font-weight:600; color:#F5F5F5; margin:0 0 1.5rem; padding-bottom:0.75rem; border-bottom:1px solid #2A2A2A;">
                    Tu Información
                </h2>

                <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:1.25rem;">

                    {{-- Nombre --}}
                    <div>
                        <label for="contactName" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">
                            Nombre completo *
                        </label>
                        <input
                            id="contactName"
                            type="text"
                            wire:model="contactName"
                            placeholder="Tu nombre"
                            autocomplete="name"
                            style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                            onfocus="this.style.borderColor='rgba(212,175,55,0.5)'"
                            onblur="this.style.borderColor='#2A2A2A'"
                        >
                        @error('contactName') <span style="color:#FCA5A5; font-size:0.8125rem;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="contactEmail" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">
                            Correo electrónico *
                        </label>
                        <input
                            id="contactEmail"
                            type="email"
                            wire:model="contactEmail"
                            placeholder="tu@correo.com"
                            autocomplete="email"
                            style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                            onfocus="this.style.borderColor='rgba(212,175,55,0.5)'"
                            onblur="this.style.borderColor='#2A2A2A'"
                        >
                        @error('contactEmail') <span style="color:#FCA5A5; font-size:0.8125rem;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Teléfono --}}
                    <div>
                        <label for="contactPhone" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">
                            Teléfono
                        </label>
                        <input
                            id="contactPhone"
                            type="tel"
                            wire:model="contactPhone"
                            placeholder="+1 (555) 000-0000"
                            autocomplete="tel"
                            style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                            onfocus="this.style.borderColor='rgba(212,175,55,0.5)'"
                            onblur="this.style.borderColor='#2A2A2A'"
                        >
                    </div>

                </div>
            </div>

            {{-- ── SECTION: Tu Evento ── --}}
            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:1.25rem; padding:1.75rem; margin-bottom:1.25rem;">
                <h2 style="font-family:'Playfair Display',Georgia,serif; font-size:1.25rem; font-weight:600; color:#F5F5F5; margin:0 0 1.5rem; padding-bottom:0.75rem; border-bottom:1px solid #2A2A2A;">
                    Tu Evento
                </h2>

                <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:1.25rem; margin-bottom:1.25rem;">

                    {{-- Tipo de evento --}}
                    <div>
                        <label for="eventType" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">
                            Tipo de evento *
                        </label>
                        <select
                            id="eventType"
                            wire:model="eventType"
                            style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif; appearance:none; cursor:pointer;"
                            onfocus="this.style.borderColor='rgba(212,175,55,0.5)'"
                            onblur="this.style.borderColor='#2A2A2A'"
                        >
                            <option value="boda">Boda</option>
                            <option value="quinceañera">Quinceañera</option>
                            <option value="corporativo">Evento Corporativo</option>
                            <option value="cumpleaños">Cumpleaños</option>
                            <option value="graduacion">Graduación</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    {{-- Fecha --}}
                    <div>
                        <label for="eventDate" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">
                            Fecha del evento
                        </label>
                        <input
                            id="eventDate"
                            type="date"
                            wire:model="eventDate"
                            min="{{ $today }}"
                            style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif; color-scheme:dark;"
                            onfocus="this.style.borderColor='rgba(212,175,55,0.5)'"
                            onblur="this.style.borderColor='#2A2A2A'"
                        >
                        @error('eventDate') <span style="color:#FCA5A5; font-size:0.8125rem;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Invitados --}}
                    <div>
                        <label for="guestCount" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">
                            Número de invitados
                        </label>
                        <input
                            id="guestCount"
                            type="number"
                            wire:model="guestCount"
                            placeholder="Ej. 150"
                            min="1"
                            style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                            onfocus="this.style.borderColor='rgba(212,175,55,0.5)'"
                            onblur="this.style.borderColor='#2A2A2A'"
                        >
                    </div>

                    {{-- Lugar --}}
                    <div>
                        <label for="eventLocation" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">
                            Lugar del evento
                        </label>
                        <input
                            id="eventLocation"
                            type="text"
                            wire:model="eventLocation"
                            placeholder="Ciudad, salón o venue"
                            style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                            onfocus="this.style.borderColor='rgba(212,175,55,0.5)'"
                            onblur="this.style.borderColor='#2A2A2A'"
                        >
                    </div>

                    {{-- Presupuesto --}}
                    <div>
                        <label for="budgetRange" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">
                            Presupuesto estimado
                        </label>
                        <select
                            id="budgetRange"
                            wire:model="budgetRange"
                            style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif; appearance:none; cursor:pointer;"
                            onfocus="this.style.borderColor='rgba(212,175,55,0.5)'"
                            onblur="this.style.borderColor='#2A2A2A'"
                        >
                            <option value="">Selecciona un rango</option>
                            <option value="Menos de $500">Menos de $500</option>
                            <option value="$500–$1,000">$500–$1,000</option>
                            <option value="$1,000–$2,500">$1,000–$2,500</option>
                            <option value="$2,500–$5,000">$2,500–$5,000</option>
                            <option value="Más de $5,000">Más de $5,000</option>
                            <option value="Por definir">Por definir</option>
                        </select>
                    </div>

                </div>

                {{-- Mensaje --}}
                <div>
                    <label for="message" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">
                        Detalles adicionales *
                    </label>
                    <textarea
                        id="message"
                        wire:model="message"
                        placeholder="Cuéntanos sobre tu evento: tipo de cocina que deseas, restricciones alimentarias, horario, estilo del servicio, etc."
                        rows="5"
                        style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif; resize:vertical; min-height:120px; line-height:1.6;"
                        onfocus="this.style.borderColor='rgba(212,175,55,0.5)'"
                        onblur="this.style.borderColor='#2A2A2A'"
                    ></textarea>
                    @error('message') <span style="color:#FCA5A5; font-size:0.8125rem;">{{ $message }}</span> @enderror
                </div>

            </div>

            {{-- ── SUBMIT ── --}}
            <div style="text-align:center; padding-top:0.5rem;">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    style="background:#D4AF37; color:#0B0B0B; font-weight:700; font-size:1rem; padding:1rem 3rem; border-radius:0.75rem; border:none; cursor:pointer; font-family:'Poppins',sans-serif; letter-spacing:0.03em; transition:opacity 0.2s;"
                    onmouseover="this.style.opacity='0.88'"
                    onmouseout="this.style.opacity='1'"
                >
                    <span wire:loading.remove>Enviar Solicitud</span>
                    <span wire:loading style="display:none;">Enviando…</span>
                </button>
                <p style="font-size:0.8125rem; color:#6B7280; margin-top:0.875rem;">
                    Te contactaremos en menos de 24 horas
                </p>
            </div>

        </form>
        @endif

    </section>

</div>
