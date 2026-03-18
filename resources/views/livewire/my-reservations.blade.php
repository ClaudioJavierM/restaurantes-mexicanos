<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🗓️ Mis Reservaciones</h1>
                <p class="mt-2 text-gray-600">Gestiona tus reservaciones en restaurantes mexicanos</p>
            </div>
            <a href="/restaurantes" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                Hacer una reservación
            </a>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if($reservations->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($reservations as $reservation)
                    @php
                        $isUpcoming = in_array($reservation->status, ['pending', 'confirmed'])
                            && $reservation->reservation_date >= now()->toDateString();

                        $statusColors = [
                            'pending'   => 'bg-yellow-100 text-yellow-800',
                            'confirmed' => 'bg-blue-100 text-blue-800',
                            'cancelled' => 'bg-red-100 text-red-700',
                            'completed' => 'bg-green-100 text-green-800',
                            'no_show'   => 'bg-gray-100 text-gray-600',
                        ];
                        $badgeClass = $statusColors[$reservation->status] ?? 'bg-gray-100 text-gray-600';
                    @endphp

                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 {{ $isUpcoming ? 'ring-2 ring-blue-300' : '' }}">
                        <!-- Restaurant image -->
                        <a href="{{ $reservation->restaurant ? route('restaurants.show', $reservation->restaurant->slug) : '#' }}" class="block">
                            @if($reservation->restaurant && $reservation->restaurant->image)
                                <img src="{{ Storage::url($reservation->restaurant->image) }}"
                                     alt="{{ $reservation->restaurant->name }}"
                                     class="w-full h-40 object-cover">
                            @else
                                <div class="w-full h-40 bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </a>

                        <div class="p-4">
                            <!-- Restaurant name + status -->
                            <div class="flex items-start justify-between mb-3">
                                <a href="{{ $reservation->restaurant ? route('restaurants.show', $reservation->restaurant->slug) : '#' }}">
                                    <h3 class="text-base font-bold text-gray-900 hover:text-red-600 transition-colors leading-tight">
                                        {{ $reservation->restaurant->name ?? 'Restaurante' }}
                                    </h3>
                                </a>
                                <span class="ml-2 flex-shrink-0 text-xs font-semibold px-2 py-1 rounded-full {{ $badgeClass }}">
                                    {{ $reservation->getStatusLabel() }}
                                </span>
                            </div>

                            <!-- Date & time -->
                            <div class="flex items-center text-sm text-gray-700 mb-2">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $reservation->getFormattedDateTime() }}
                            </div>

                            <!-- Party size -->
                            <div class="flex items-center text-sm text-gray-700 mb-2">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $reservation->party_size }} {{ $reservation->party_size == 1 ? 'persona' : 'personas' }}
                            </div>

                            <!-- Occasion -->
                            @if($reservation->occasion && $reservation->occasion !== 'none')
                                <div class="text-xs text-indigo-600 mb-2 font-medium">
                                    🎉 {{ $reservation->getOccasionLabel() }}
                                </div>
                            @endif

                            <!-- Confirmation code -->
                            <div class="bg-gray-50 rounded-md px-3 py-2 mb-3">
                                <p class="text-xs text-gray-500">Código de confirmación</p>
                                <p class="font-mono font-bold text-gray-800 text-sm tracking-widest">{{ $reservation->confirmation_code }}</p>
                            </div>

                            <!-- Cancel button -->
                            @if($isUpcoming)
                                <button
                                    wire:click="cancelReservation({{ $reservation->id }})"
                                    wire:confirm="¿Estás seguro de que quieres cancelar esta reservación?"
                                    class="w-full text-center text-sm text-red-600 border border-red-300 py-2 rounded-lg hover:bg-red-50 transition-colors font-medium"
                                >
                                    Cancelar reservación
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $reservations->links() }}
            </div>

        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No tienes reservaciones</h3>
                <p class="text-gray-600 mb-6">Reserva en tu restaurante mexicano favorito y aparecerá aquí.</p>
                <a href="/restaurantes" class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Explorar Restaurantes
                </a>
            </div>
        @endif
    </div>
</div>
