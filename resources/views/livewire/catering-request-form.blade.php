<div>
    @if($submitted)
    {{-- Success state --}}
    <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
        <div class="text-4xl mb-3">🎉</div>
        <h3 class="text-green-800 font-bold text-lg mb-1">¡Solicitud Enviada!</h3>
        <p class="text-green-700 text-sm">El restaurante revisará tu solicitud y te contactará pronto a <strong>{{ $contact_email }}</strong></p>
    </div>
    @elseif(!$showForm)
    {{-- CTA Button --}}
    <button wire:click="$set('showForm', true)"
        class="w-full flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold py-3 px-6 rounded-xl transition text-sm">
        🍽️ Solicitar Cotización de Catering
    </button>
    @else
    {{-- Form --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="bg-amber-500 px-4 py-3 flex items-center justify-between">
            <h3 class="text-white font-bold text-sm">Solicitar Cotización de Catering</h3>
            <button wire:click="$set('showForm', false)" class="text-white/80 hover:text-white text-lg leading-none">&times;</button>
        </div>

        <form wire:submit.prevent="submitRequest" class="p-4 space-y-4">
            {{-- Contacto --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tu Nombre *</label>
                    <input wire:model="contact_name" type="text"
                        class="w-full border border-gray-300 rounded-lg p-2 text-sm @error('contact_name') border-red-400 @enderror"
                        placeholder="María García">
                    @error('contact_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Email *</label>
                    <input wire:model="contact_email" type="email"
                        class="w-full border border-gray-300 rounded-lg p-2 text-sm @error('contact_email') border-red-400 @enderror"
                        placeholder="maria@ejemplo.com">
                    @error('contact_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Teléfono</label>
                    <input wire:model="contact_phone" type="tel"
                        class="w-full border border-gray-300 rounded-lg p-2 text-sm"
                        placeholder="+52 33 1234 5678">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Presupuesto (USD)</label>
                    <input wire:model="budget" type="number"
                        class="w-full border border-gray-300 rounded-lg p-2 text-sm"
                        placeholder="5000">
                </div>
            </div>

            {{-- Evento --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tipo de Evento *</label>
                    <select wire:model="event_type"
                        class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                        @foreach($eventTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Fecha del Evento *</label>
                    <input wire:model="event_date" type="date"
                        class="w-full border border-gray-300 rounded-lg p-2 text-sm @error('event_date') border-red-400 @enderror"
                        min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    @error('event_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Número de Invitados *</label>
                    <input wire:model="guest_count" type="number"
                        class="w-full border border-gray-300 rounded-lg p-2 text-sm @error('guest_count') border-red-400 @enderror"
                        min="5" max="10000">
                    @error('guest_count') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Lugar del Evento</label>
                <input wire:model="event_location" type="text"
                    class="w-full border border-gray-300 rounded-lg p-2 text-sm"
                    placeholder="Centro de eventos, domicilio, etc.">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Notas Adicionales</label>
                <textarea wire:model="notes"
                    class="w-full border border-gray-300 rounded-lg p-2 text-sm resize-none"
                    rows="3"
                    placeholder="Menciona preferencias de menú, restricciones dietéticas, servicios adicionales..."></textarea>
            </div>

            <button type="submit"
                class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-3 rounded-xl text-sm transition">
                <span wire:loading.remove wire:target="submitRequest">Enviar Solicitud de Cotización</span>
                <span wire:loading wire:target="submitRequest">Enviando...</span>
            </button>
            <p class="text-xs text-gray-400 text-center">El restaurante te contactará directamente con su cotización</p>
        </form>
    </div>
    @endif
</div>
