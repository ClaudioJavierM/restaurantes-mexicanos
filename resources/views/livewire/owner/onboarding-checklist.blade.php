@if(!$this->isDismissed)
    <div
        style="background:#1A1A1A; border:1px solid #2A2A2A;"
        class="rounded-2xl p-6"
    >
        {{-- 100% complete — celebration state --}}
        @if($this->completionPercentage === 100)
            <div class="rounded-xl p-5 text-center" style="background:#1F3D2B; border:1px solid #2d5a3d;">
                <p class="text-2xl mb-1">🎉</p>
                <p class="font-bold text-lg" style="color:#F5F5F5;">¡Perfil completo!</p>
                <p class="text-sm mt-1" style="color:#86efac;">Tu restaurante está listo para atraer más clientes.</p>
                <button
                    wire:click="dismiss"
                    class="mt-4 text-xs px-4 py-1.5 rounded-lg transition-colors"
                    style="background:#2d5a3d; color:#86efac;"
                    onmouseover="this.style.background='#3a7050'"
                    onmouseout="this.style.background='#2d5a3d'"
                >
                    Cerrar
                </button>
            </div>

        {{-- Normal state --}}
        @else
            {{-- Header --}}
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-base" style="color:#F5F5F5;">Completa tu perfil</h3>
                <button
                    wire:click="dismiss"
                    class="transition-colors"
                    style="color:#6B7280;"
                    onmouseover="this.style.color='#9CA3AF'"
                    onmouseout="this.style.color='#6B7280'"
                    title="Cerrar"
                    aria-label="Cerrar checklist"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            {{-- Progress bar --}}
            <div class="mb-5">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs" style="color:#9CA3AF;">Progreso del perfil</span>
                    <span class="text-xs font-semibold" style="color:#D4AF37;">{{ $this->completionPercentage }}%</span>
                </div>
                <div class="w-full rounded-full h-2" style="background:#2A2A2A;">
                    <div
                        class="h-2 rounded-full transition-all duration-500"
                        style="background:#D4AF37; width:{{ $this->completionPercentage }}%;"
                    ></div>
                </div>
            </div>

            {{-- Task list --}}
            <ul class="space-y-3">
                @foreach($this->tasks as $task)
                    <li class="flex items-center gap-3">
                        {{-- Status icon --}}
                        @if($task['done'])
                            <span
                                class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold"
                                style="background:#1F3D2B; color:#4ade80;"
                            >✓</span>
                        @else
                            <span
                                class="flex-shrink-0 w-5 h-5 rounded-full border-2"
                                style="border-color:#4A4A4A;"
                            ></span>
                        @endif

                        {{-- Label --}}
                        <span
                            class="flex-1 text-sm {{ $task['done'] ? 'line-through' : '' }}"
                            style="color:{{ $task['done'] ? '#6B7280' : '#D1D5DB' }};"
                        >
                            {{ $task['label'] }}
                        </span>

                        {{-- Action link (only if not done) --}}
                        @if(!$task['done'])
                            <a
                                href="{{ route('owner.restaurant.edit', $restaurant) }}"
                                class="flex-shrink-0 text-xs font-medium transition-opacity hover:opacity-75"
                                style="color:#D4AF37;"
                            >
                                {{ $task['action'] }}
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
