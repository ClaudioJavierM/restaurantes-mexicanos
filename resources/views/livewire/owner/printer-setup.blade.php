{{-- Printer Setup — CloudPRNT Manager --}}
<div
    class="min-h-screen bg-[#0B0B0B] p-4 md:p-6"
    wire:poll.30s
>

    {{-- ═══════════════════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════════════════ --}}
    <div class="mb-6">
        <h1 class="font-['Playfair_Display'] text-2xl font-bold text-white">
            Impresora de Tickets
        </h1>
        <p class="mt-1 text-sm text-gray-400">
            Gestión de impresora CloudPRNT para pedidos automáticos
        </p>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         SIN IMPRESORA REGISTRADA
    ═══════════════════════════════════════════════════════ --}}
    @if (!$this->printer)
        <div class="mx-auto max-w-lg">
            <div class="rounded-2xl border border-white/10 bg-[#1A1A1A] p-8">

                {{-- Icono --}}
                <div class="mb-6 flex justify-center">
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-[#D4AF37]/10 ring-1 ring-[#D4AF37]/30">
                        <svg class="h-10 w-10 text-[#D4AF37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.75 19.5m10.56-5.671L17.25 19.5M3 8.688c0-.864.933-1.406 1.683-.977l7.108 4.061a1.125 1.125 0 010 1.954l-7.108 4.061A1.125 1.125 0 013 16.811V8.69zM12.75 8.688c0-.864.933-1.406 1.683-.977l7.108 4.061a1.125 1.125 0 010 1.954l-7.108 4.061a1.125 1.125 0 01-1.683-.977V8.69z" />
                        </svg>
                    </div>
                </div>

                <h2 class="mb-2 text-center font-['Playfair_Display'] text-xl font-bold text-white">
                    Conecta tu impresora
                </h2>
                <p class="mb-8 text-center text-sm text-gray-400">
                    Configura tu impresora Star Micronics con la URL CloudPRNT que se generará. Cada nuevo pedido se imprimirá automáticamente.
                </p>

                {{-- Form --}}
                <form wire:submit="createPrinter" class="space-y-4">
                    <div>
                        <label for="printerName" class="mb-2 block text-sm font-medium text-gray-300">
                            Nombre de la impresora
                        </label>
                        <input
                            id="printerName"
                            type="text"
                            wire:model="printerName"
                            placeholder="Cocina Principal"
                            class="w-full rounded-xl border border-white/10 bg-[#0B0B0B] px-4 py-3 text-white placeholder-gray-600 outline-none ring-0 transition focus:border-[#D4AF37]/50 focus:ring-1 focus:ring-[#D4AF37]/30"
                        />
                        @error('printerName')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-[#D4AF37] px-6 py-3 font-semibold text-[#0B0B0B] transition hover:bg-[#C9A227] active:scale-[0.98]"
                    >
                        <span wire:loading.remove wire:target="createPrinter">Registrar impresora</span>
                        <span wire:loading wire:target="createPrinter">Registrando...</span>
                    </button>
                </form>
            </div>

            {{-- Info box --}}
            <div class="mt-4 rounded-xl border border-white/5 bg-[#1A1A1A]/50 p-4">
                <p class="text-xs leading-relaxed text-gray-500">
                    <span class="font-medium text-gray-400">Compatible con:</span> Star TSP100IV, TSP143IV, TSP654II y otros modelos con soporte CloudPRNT. La impresora debe estar conectada a internet y a la misma red Wi-Fi que tu local.
                </p>
            </div>
        </div>

    {{-- ═══════════════════════════════════════════════════════
         CON IMPRESORA REGISTRADA
    ═══════════════════════════════════════════════════════ --}}
    @else
        <div class="mx-auto max-w-lg space-y-4">

            {{-- Success flash --}}
            @if ($showSuccess)
                <div class="flex items-center gap-3 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3">
                    <svg class="h-5 w-5 flex-shrink-0 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-emerald-300">Impresora registrada correctamente. Copia la URL y configúrala en la app Star CloudPRNT.</p>
                </div>
            @endif

            {{-- Printer card --}}
            <div class="rounded-2xl border border-white/10 bg-[#1A1A1A] p-6">

                {{-- Header de la card --}}
                <div class="mb-6 flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#D4AF37]/10 ring-1 ring-[#D4AF37]/20">
                            <svg class="h-6 w-6 text-[#D4AF37]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.75 19.5m10.56-5.671L17.25 19.5M3 8.688c0-.864.933-1.406 1.683-.977l7.108 4.061a1.125 1.125 0 010 1.954l-7.108 4.061A1.125 1.125 0 013 16.811V8.69zM12.75 8.688c0-.864.933-1.406 1.683-.977l7.108 4.061a1.125 1.125 0 010 1.954l-7.108 4.061a1.125 1.125 0 01-1.683-.977V8.69z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white">{{ $this->printer->name }}</h2>
                            <p class="text-xs text-gray-500">Star Micronics CloudPRNT</p>
                        </div>
                    </div>

                    {{-- Estado --}}
                    @if ($this->printer->isOnline())
                        <div class="flex items-center gap-2 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                            </span>
                            <span class="text-xs font-medium text-emerald-400">Online</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1">
                            <span class="h-2 w-2 rounded-full bg-gray-600"></span>
                            <span class="text-xs font-medium text-gray-500">Offline</span>
                        </div>
                    @endif
                </div>

                {{-- URL CloudPRNT --}}
                <div class="mb-6">
                    <label class="mb-2 block text-xs font-medium uppercase tracking-wider text-gray-500">
                        URL CloudPRNT
                    </label>
                    <div class="flex items-center gap-2">
                        <input
                            id="cloudprnt-url"
                            type="text"
                            value="{{ $this->printer->getCloudprntUrl() }}"
                            readonly
                            class="flex-1 rounded-xl border border-white/10 bg-[#0B0B0B] px-4 py-3 font-mono text-sm text-[#D4AF37] outline-none"
                            onclick="this.select()"
                        />
                        <button
                            type="button"
                            onclick="
                                var el = document.getElementById('cloudprnt-url');
                                el.select();
                                document.execCommand('copy');
                                var btn = this;
                                btn.textContent = '✓';
                                btn.classList.add('bg-emerald-500/20', 'border-emerald-500/30', 'text-emerald-400');
                                btn.classList.remove('border-white/10', 'text-gray-400');
                                setTimeout(function(){ btn.textContent = 'Copiar'; btn.classList.remove('bg-emerald-500/20','border-emerald-500/30','text-emerald-400'); btn.classList.add('border-white/10','text-gray-400'); }, 2000);
                            "
                            class="flex-shrink-0 rounded-xl border border-white/10 bg-[#0B0B0B] px-4 py-3 text-sm font-medium text-gray-400 transition hover:border-[#D4AF37]/30 hover:text-[#D4AF37]"
                        >
                            Copiar
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-600">
                        Pega esta URL en la app Star CloudPRNT bajo "Service URL". La impresora recibirá automáticamente cada pedido nuevo.
                    </p>
                </div>

                {{-- Last poll --}}
                @if ($this->printer->last_poll_at)
                    <div class="mb-6 rounded-xl border border-white/5 bg-[#0B0B0B]/50 px-4 py-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Último contacto</span>
                            <span class="text-gray-300">{{ $this->printer->last_poll_at->diffForHumans() }}</span>
                        </div>
                        @if ($this->printer->last_job_at)
                            <div class="mt-1 flex items-center justify-between text-xs">
                                <span class="text-gray-500">Último ticket impreso</span>
                                <span class="text-gray-300">{{ $this->printer->last_job_at->diffForHumans() }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Acciones --}}
                <div class="flex gap-3">
                    <button
                        wire:click="togglePrinter"
                        wire:confirm="{{ $this->printer->is_active ? '¿Desactivar la impresora? Los nuevos pedidos no se imprimirán.' : '¿Activar la impresora?' }}"
                        class="flex-1 rounded-xl border px-4 py-2.5 text-sm font-medium transition active:scale-[0.98]
                            {{ $this->printer->is_active
                                ? 'border-amber-500/20 bg-amber-500/10 text-amber-400 hover:bg-amber-500/20'
                                : 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20' }}"
                    >
                        {{ $this->printer->is_active ? 'Desactivar' : 'Activar' }}
                    </button>

                    <button
                        wire:click="deletePrinter"
                        wire:confirm="¿Eliminar la impresora? Se perderá el token y tendrás que reconfigurar la app CloudPRNT."
                        class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-2.5 text-sm font-medium text-red-400 transition hover:bg-red-500/20 active:scale-[0.98]"
                    >
                        Eliminar
                    </button>
                </div>
            </div>

            {{-- Instructions card --}}
            <div class="rounded-2xl border border-white/5 bg-[#1A1A1A]/60 p-5">
                <h3 class="mb-3 text-sm font-semibold text-gray-300">Cómo configurar tu impresora</h3>
                <ol class="space-y-2 text-xs text-gray-500">
                    <li class="flex gap-2">
                        <span class="flex-shrink-0 font-bold text-[#D4AF37]">1.</span>
                        Descarga la app <span class="text-gray-300">Star CloudPRNT</span> en el dispositivo conectado a tu impresora.
                    </li>
                    <li class="flex gap-2">
                        <span class="flex-shrink-0 font-bold text-[#D4AF37]">2.</span>
                        En la app, selecciona tu impresora Star Micronics por Bluetooth o Wi-Fi.
                    </li>
                    <li class="flex gap-2">
                        <span class="flex-shrink-0 font-bold text-[#D4AF37]">3.</span>
                        Pega la <span class="text-gray-300">URL CloudPRNT</span> de arriba en el campo "Service URL".
                    </li>
                    <li class="flex gap-2">
                        <span class="flex-shrink-0 font-bold text-[#D4AF37]">4.</span>
                        Guarda la configuración. La impresora comenzará a recibir pedidos automáticamente.
                    </li>
                </ol>
            </div>

        </div>
    @endif

</div>
