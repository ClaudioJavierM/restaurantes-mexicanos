<div>
    @if($restaurant->accepts_reservations && $this->reservationType !== 'none')
        {{-- External Reservation System (OpenTable, Yelp, Resy, etc.) --}}
        @if($this->reservationType === 'external')
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">Hacer una Reservación</h3>
                    <p class="mt-2 text-gray-600">Reserva tu mesa en {{ $restaurant->name }}</p>
                    <a
                        href="{{ $this->externalUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-4 inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Reservar en {{ $this->platformName }}
                    </a>
                    <p class="mt-3 text-xs text-gray-500">Serás redirigido a {{ $this->platformName }} para completar tu reservación</p>
                </div>
            </div>
        @endif

        {{-- Internal Restaurante Famoso System --}}
        @if($this->reservationType === 'restaurante_famoso')
            <!-- Reservation Button -->
            @if(!$showForm && !$showConfirmation)
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Hacer una Reservación</h3>
                        <p class="mt-2 text-gray-600">Reserva tu mesa en {{ $restaurant->name }}</p>
                        <button
                            wire:click="toggleForm"
                            class="mt-4 inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Reservar Ahora
                        </button>
                        <p class="mt-3 text-xs text-gray-500 flex items-center justify-center gap-1">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Sistema de reservaciones verificado
                        </p>
                    </div>
                </div>
            @endif

        <!-- Confirmation Message -->
        @if($showConfirmation)
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="text-center">
                    <svg class="mx-auto h-16 w-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="mt-4 text-xl font-bold text-green-800">¡Reservación Enviada!</h3>
                    <p class="mt-2 text-green-700">Tu solicitud de reservación ha sido enviada.</p>
                    <div class="mt-4 p-4 bg-white rounded-lg inline-block">
                        <p class="text-sm text-gray-600">Código de Confirmación:</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $confirmationCode }}</p>
                    </div>
                    <p class="mt-4 text-sm text-green-600">
                        El restaurante te contactará para confirmar tu reservación.
                    </p>
                    <button
                        wire:click="toggleForm"
                        class="mt-4 text-green-600 hover:text-green-700 font-medium"
                    >
                        Hacer otra reservación
                    </button>
                </div>
            </div>
        @endif

        <!-- Reservation Form -->
        @if($showForm)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Reservar Mesa</h3>
                    <button
                        wire:click="toggleForm"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="submitReservation" class="space-y-4">
                    <!-- Date and Time -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                            <input
                                type="date"
                                wire:model="reservationDate"
                                min="{{ now()->format('Y-m-d') }}"
                                class="w-full rounded-lg bg-white border-gray-300 text-gray-900 placeholder-gray-400 shadow-sm focus:border-[#D4AF37] focus:ring-[#D4AF37]"
                            >
                            @error('reservationDate') <span class="text-danger-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                            <select
                                wire:model="reservationTime"
                                class="w-full rounded-lg bg-white border-gray-300 text-gray-900 placeholder-gray-400 shadow-sm focus:border-[#D4AF37] focus:ring-[#D4AF37]"
                            >
                                <option value="">Seleccionar hora</option>
                                @foreach($availableTimes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('reservationTime') <span class="text-danger-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Party Size and Occasion -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número de Personas</label>
                            <select
                                wire:model="partySize"
                                class="w-full rounded-lg bg-white border-gray-300 text-gray-900 placeholder-gray-400 shadow-sm focus:border-[#D4AF37] focus:ring-[#D4AF37]"
                            >
                                @foreach($partySizes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('partySize') <span class="text-danger-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ocasión (opcional)</label>
                            <select
                                wire:model="occasion"
                                class="w-full rounded-lg bg-white border-gray-300 text-gray-900 placeholder-gray-400 shadow-sm focus:border-[#D4AF37] focus:ring-[#D4AF37]"
                            >
                                @foreach($occasions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Guest Info (if not logged in) -->
                    @guest
                        <div class="border-t pt-4">
                            <p class="text-sm text-gray-600 mb-3">Información de contacto</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                    <input
                                        type="text"
                                        wire:model="guestName"
                                        placeholder="Tu nombre"
                                        class="w-full rounded-lg bg-white border-gray-300 text-gray-900 placeholder-gray-400 shadow-sm focus:border-[#D4AF37] focus:ring-[#D4AF37]"
                                    >
                                    @error('guestName') <span class="text-danger-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input
                                        type="email"
                                        wire:model="guestEmail"
                                        placeholder="tu@email.com"
                                        class="w-full rounded-lg bg-white border-gray-300 text-gray-900 placeholder-gray-400 shadow-sm focus:border-[#D4AF37] focus:ring-[#D4AF37]"
                                    >
                                    @error('guestEmail') <span class="text-danger-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endguest

                    <!-- Phone (always required) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input
                            type="tel"
                            wire:model="guestPhone"
                            placeholder="(555) 123-4567"
                            class="w-full rounded-lg bg-white border-gray-300 text-gray-900 placeholder-gray-400 shadow-sm focus:border-[#D4AF37] focus:ring-[#D4AF37]"
                        >
                        @error('guestPhone') <span class="text-danger-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Special Requests -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Peticiones Especiales (opcional)</label>
                        <textarea
                            wire:model="specialRequests"
                            rows="2"
                            placeholder="Alergias, silla de bebé, etc."
                            class="w-full rounded-lg bg-white border-gray-300 text-gray-900 placeholder-gray-400 shadow-sm focus:border-[#D4AF37] focus:ring-[#D4AF37]"
                        ></textarea>
                    </div>

                    <!-- Submit -->
                    <div class="pt-4">
                        <button
                            type="submit"
                            class="w-full py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="submitReservation">Solicitar Reservación</span>
                            <span wire:loading wire:target="submitReservation">Enviando...</span>
                        </button>
                        <p class="mt-2 text-xs text-gray-500 text-center">
                            La reservación está sujeta a disponibilidad. El restaurante confirmará tu reservación.
                        </p>
                    </div>
                </form>
            </div>
        @endif
        @endif {{-- End restaurante_famoso --}}
    @else
        <!-- Restaurant doesn't accept reservations -->
        <div class="bg-gray-50 rounded-lg p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="mt-4 text-gray-600">Este restaurante no acepta reservaciones en línea.</p>
            <p class="mt-2 text-sm text-gray-500">Contacta directamente al restaurante para más información.</p>
        </div>
    @endif
</div>
