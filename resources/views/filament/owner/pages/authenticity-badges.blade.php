<x-filament-panels::page>

    {{-- ── Header explainer ── --}}
    <x-filament::section>
        <div class="flex items-start gap-4">
            <div class="text-4xl">🛡️</div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Insignias de Autenticidad Cultural</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Las insignias de autenticidad distinguen tu restaurante como un negocio genuinamente mexicano.
                    Solicita las insignias que aplican a tu restaurante — el equipo de FAMER verificará y aprobará cada solicitud.
                </p>
                <p class="mt-2 text-xs text-amber-600 dark:text-amber-400 font-medium">
                    Las insignias aprobadas aparecen en tu perfil público y en los resultados de búsqueda.
                </p>
            </div>
        </div>
    </x-filament::section>

    {{-- ── Badge Grid ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($this->badges as $badgeId => $badge)
            <div
                class="relative rounded-xl border p-5 flex flex-col gap-3 transition-all
                    {{ $badge['status'] === 'approved'
                        ? 'bg-amber-50 dark:bg-amber-950/30 border-amber-300 dark:border-amber-700'
                        : ($badge['status'] === 'pending'
                            ? 'bg-blue-50 dark:bg-blue-950/20 border-blue-200 dark:border-blue-800'
                            : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600') }}"
            >
                {{-- Icon + Status indicator --}}
                <div class="flex items-start justify-between">
                    <span class="text-4xl leading-none select-none">{{ $badge['icon'] }}</span>

                    @if($badge['status'] === 'approved')
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-sm">
                            ✓ Verificado
                        </span>
                    @elseif($badge['status'] === 'pending')
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">
                            ⏳ En revisión
                        </span>
                    @elseif($badge['status'] === 'rejected')
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400">
                            ✗ Rechazado
                        </span>
                    @endif
                </div>

                {{-- Name + Description --}}
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $badge['name'] }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $badge['description'] }}</p>
                </div>

                {{-- Action button --}}
                <div class="mt-auto">
                    @if($badge['status'] === 'approved')
                        <div class="text-xs text-amber-700 dark:text-amber-400 font-medium flex items-center gap-1">
                            🏅 Insignia activa en tu perfil
                        </div>
                    @elseif($badge['status'] === 'pending')
                        <div class="text-xs text-blue-600 dark:text-blue-400">
                            Tu solicitud está siendo revisada.
                        </div>
                    @elseif($badge['status'] === 'rejected')
                        <x-filament::button
                            wire:click="openRequestModal('{{ $badgeId }}')"
                            size="sm"
                            color="gray"
                            outlined
                        >
                            Volver a solicitar
                        </x-filament::button>
                    @else
                        <x-filament::button
                            wire:click="openRequestModal('{{ $badgeId }}')"
                            size="sm"
                            color="primary"
                            outlined
                        >
                            Solicitar insignia
                        </x-filament::button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Request Modal ── --}}
    @if($requestingBadgeId && isset($this->badges[$requestingBadgeId]))
        @php $modalBadge = $this->badges[$requestingBadgeId]; @endphp
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            x-data
        >
            {{-- Backdrop --}}
            <div
                class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                wire:click="closeModal"
            ></div>

            {{-- Panel --}}
            <div class="relative z-10 w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">{{ $modalBadge['icon'] }}</span>
                        <div>
                            <h2 class="font-bold text-gray-900 dark:text-white text-base">Solicitar: {{ $modalBadge['name'] }}</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $modalBadge['description'] }}</p>
                        </div>
                    </div>
                    <button
                        wire:click="closeModal"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            ¿Por qué merece esta insignia tu restaurante?
                            <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            wire:model="evidence"
                            rows="5"
                            placeholder="Describe con detalle cómo tu restaurante cumple con los criterios de esta insignia. Cuanto más específico seas, más rápido podremos verificarlo."
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm px-4 py-3 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 resize-none placeholder-gray-400 dark:placeholder-gray-500"
                        ></textarea>
                        @error('evidence')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">Mínimo 20 caracteres. Puedes incluir años de operación, nombres de proveedores, origen del chef, etc.</p>
                    </div>

                    <div class="rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 p-3">
                        <p class="text-xs text-amber-700 dark:text-amber-400">
                            <strong>Proceso de verificación:</strong> El equipo de FAMER revisará tu solicitud en un plazo de 3-5 días hábiles.
                            Si es aprobada, la insignia aparecerá automáticamente en tu perfil público.
                        </p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <x-filament::button
                        wire:click="closeModal"
                        color="gray"
                        outlined
                    >
                        Cancelar
                    </x-filament::button>
                    <x-filament::button
                        wire:click="submitRequest"
                        wire:loading.attr="disabled"
                        color="primary"
                    >
                        <span wire:loading.remove wire:target="submitRequest">Enviar solicitud</span>
                        <span wire:loading wire:target="submitRequest">Enviando...</span>
                    </x-filament::button>
                </div>

            </div>
        </div>
    @endif

    {{-- ── Info footer ── --}}
    <x-filament::section>
        <div class="text-center py-2">
            <p class="text-xs text-gray-400 dark:text-gray-500">
                Las insignias son verificadas manualmente por el equipo editorial de FAMER.
                Proporcionar información falsa puede resultar en la remoción de las insignias y suspensión de la cuenta.
            </p>
        </div>
    </x-filament::section>

</x-filament-panels::page>
