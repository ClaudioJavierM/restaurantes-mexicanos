<div>
    @if($joined && $myEntry)
    {{-- En la lista --}}
    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-2xl font-bold text-green-700">{{ $myEntry->position }}</span>
            </div>
            <div class="flex-1">
                <p class="font-bold text-green-800 text-sm">¡Estás en la lista!</p>
                <p class="text-green-700 text-xs mt-0.5">Posición #{{ $myEntry->position }} · {{ $myEntry->party_size }} personas</p>
                @if($myEntry->status === 'called')
                <div class="mt-2 bg-blue-100 border border-blue-300 rounded p-2">
                    <p class="text-blue-800 font-semibold text-sm">🔔 ¡Tu mesa está lista! Preséntate con el mesero.</p>
                </div>
                @endif
            </div>
        </div>
        <button wire:click="cancelMySpot"
            class="mt-3 w-full text-xs text-red-500 hover:text-red-700 underline">
            Cancelar mi lugar
        </button>
    </div>
    @elseif(!$showForm)
    {{-- CTA --}}
    <div class="text-center">
        @if($queueCount > 0)
        <p class="text-xs text-gray-500 mb-2">{{ $queueCount }} {{ $queueCount === 1 ? 'persona' : 'personas' }} esperando</p>
        @else
        <p class="text-xs text-green-600 mb-2">Sin espera en este momento</p>
        @endif
        <button wire:click="$set('showForm', true)"
            class="w-full flex items-center justify-center gap-2 bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 px-4 rounded-xl transition text-sm">
            📋 Unirse a la Lista de Espera
        </button>
    </div>
    @else
    {{-- Formulario --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="bg-gray-900 px-4 py-3 flex items-center justify-between">
            <h3 class="text-white font-bold text-sm">Lista de Espera</h3>
            <button wire:click="$set('showForm', false)" class="text-gray-400 hover:text-white text-lg leading-none">&times;</button>
        </div>
        <form wire:submit.prevent="joinWaitlist" class="p-4 space-y-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tu Nombre *</label>
                <input wire:model="name" type="text"
                    class="w-full border border-gray-300 rounded-lg p-2 text-sm @error('name') border-red-400 @enderror"
                    placeholder="María García">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Personas *</label>
                    <input wire:model="party_size" type="number"
                        class="w-full border border-gray-300 rounded-lg p-2 text-sm"
                        min="1" max="20">
                    @error('party_size') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Teléfono</label>
                    <input wire:model="phone" type="tel"
                        class="w-full border border-gray-300 rounded-lg p-2 text-sm"
                        placeholder="Opcional">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Nota Especial</label>
                <input wire:model="special_request" type="text"
                    class="w-full border border-gray-300 rounded-lg p-2 text-sm"
                    placeholder="Silla alta, cumpleaños, alergia...">
            </div>

            <button type="submit"
                class="w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-2.5 rounded-xl text-sm transition">
                <span wire:loading.remove wire:target="joinWaitlist">Agregar a la Lista</span>
                <span wire:loading wire:target="joinWaitlist">Agregando...</span>
            </button>
        </form>
    </div>
    @endif
</div>
