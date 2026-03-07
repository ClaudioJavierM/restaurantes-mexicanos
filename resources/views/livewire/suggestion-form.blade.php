<div>
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="max-w-3xl mx-auto">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Sugerir un Restaurante</h1>
                    <p class="text-gray-600">Ayúdanos a crecer nuestra base de datos de restaurantes mexicanos en USA</p>
                </div>

                @if (session()->has('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <form wire:submit="submit" class="space-y-6">
                    <!-- Tu Información -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Tu Información</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="submitter_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre <span class="text-red-600">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="submitter_name"
                                    wire:model="submitter_name"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Tu nombre completo"
                                >
                                @error('submitter_name')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="submitter_email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Email <span class="text-red-600">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="submitter_email"
                                    wire:model="submitter_email"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="tu@email.com"
                                >
                                @error('submitter_email')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="submitter_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                    Teléfono (opcional)
                                </label>
                                <input
                                    type="tel"
                                    id="submitter_phone"
                                    wire:model="submitter_phone"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="(123) 456-7890"
                                >
                                @error('submitter_phone')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Información del Restaurante -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-gray-900">Información del Restaurante</h2>
                            @if($google_verified)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Verificado con Google
                                </span>
                            @endif
                        </div>

                        @if (session()->has('google_success'))
                            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                                {{ session('google_success') }}
                            </div>
                        @endif

                        @if (session()->has('google_error'))
                            <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg text-sm">
                                {{ session('google_error') }}
                            </div>
                        @endif

                        <div class="space-y-4">
                            <div>
                                <label for="restaurant_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre del Restaurante <span class="text-red-600">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="restaurant_autocomplete"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="Empieza a escribir el nombre del restaurante..."
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <input
                                    type="hidden"
                                    id="restaurant_name"
                                    wire:model="restaurant_name"
                                >
                                <p class="mt-1 text-xs text-gray-500">
                                    💡 <strong>Tip:</strong> Usa el autocompletado de Google para rellenar automáticamente todos los campos
                                </p>
                                @error('restaurant_name')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="restaurant_address" class="block text-sm font-medium text-gray-700 mb-1">
                                    Dirección <span class="text-red-600">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="restaurant_address"
                                    wire:model="restaurant_address"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="123 Main Street"
                                >
                                @error('restaurant_address')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="restaurant_city" class="block text-sm font-medium text-gray-700 mb-1">
                                        Ciudad <span class="text-red-600">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="restaurant_city"
                                        wire:model="restaurant_city"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="Los Angeles"
                                    >
                                    @error('restaurant_city')
                                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="restaurant_state" class="block text-sm font-medium text-gray-700 mb-1">
                                        Estado <span class="text-red-600">*</span>
                                    </label>
                                    <select
                                        id="restaurant_state"
                                        wire:model="restaurant_state"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                        <option value="">Selecciona...</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state->name }}">{{ $state->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('restaurant_state')
                                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="restaurant_zip_code" class="block text-sm font-medium text-gray-700 mb-1">
                                        Código Postal
                                    </label>
                                    <input
                                        type="text"
                                        id="restaurant_zip_code"
                                        wire:model="restaurant_zip_code"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="90001"
                                    >
                                    @error('restaurant_zip_code')
                                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Google Verification Button -->
                            <div>
                                <button
                                    type="button"
                                    wire:click="verifyWithGoogle"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span wire:loading.remove wire:target="verifyWithGoogle">Verificar con Google</span>
                                    <span wire:loading wire:target="verifyWithGoogle">Verificando...</span>
                                </button>
                                <p class="mt-2 text-sm text-gray-500">
                                    Verifica que el restaurante existe en Google Maps para autocompletar información
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="restaurant_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        Teléfono
                                    </label>
                                    <input
                                        type="tel"
                                        id="restaurant_phone"
                                        wire:model="restaurant_phone"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="(123) 456-7890"
                                    >
                                    @error('restaurant_phone')
                                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="restaurant_website" class="block text-sm font-medium text-gray-700 mb-1">
                                        Sitio Web
                                    </label>
                                    <input
                                        type="url"
                                        id="restaurant_website"
                                        wire:model="restaurant_website"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="https://ejemplo.com"
                                    >
                                    @error('restaurant_website')
                                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tipo de Comida <span class="text-red-600">*</span>
                                </label>
                                <select
                                    id="category_id"
                                    wire:model="category_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                >
                                    <option value="">Selecciona una categoría...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Descripción
                                </label>
                                <textarea
                                    id="description"
                                    wire:model="description"
                                    rows="4"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Cuéntanos sobre este restaurante, sus especialidades, ambiente, etc."
                                ></textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                    Notas Adicionales
                                </label>
                                <textarea
                                    id="notes"
                                    wire:model="notes"
                                    rows="3"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Cualquier información adicional que quieras compartir..."
                                ></textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-600">
                            <span class="text-red-600">*</span> Campos requeridos
                        </p>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="submit">Enviar Sugerencia</span>
                            <span wire:loading wire:target="submit">Enviando...</span>
                        </button>
                    </div>
                </form>

                <!-- Info Box -->
                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex">
                        <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">¿Por qué pedimos tu información?</p>
                            <p>Tu información de contacto nos permite verificar la autenticidad de la sugerencia y contactarte si necesitamos más detalles. No compartiremos tu información con terceros.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&callback=initAutocomplete" async defer></script>
    <script>
        let autocomplete;
        let place;

        function initAutocomplete() {
            const input = document.getElementById('restaurant_autocomplete');

            if (!input) return;

            // Create autocomplete instance - restricted to establishments (restaurants, bars, etc.)
            autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['establishment'],
                componentRestrictions: { country: 'us' },
                fields: ['name', 'formatted_address', 'address_components', 'formatted_phone_number', 'website', 'geometry', 'place_id', 'types']
            });

            // When place is selected from autocomplete
            autocomplete.addListener('place_changed', function() {
                place = autocomplete.getPlace();

                if (!place.geometry) {
                    console.error("No details available for input: '" + place.name + "'");
                    return;
                }

                console.log('Place selected:', place);

                // Extract address components
                let street_number = '';
                let route = '';
                let city = '';
                let state = '';
                let zip = '';
                let country = '';

                for (const component of place.address_components) {
                    const componentType = component.types[0];

                    switch (componentType) {
                        case 'street_number':
                            street_number = component.long_name;
                            break;
                        case 'route':
                            route = component.long_name;
                            break;
                        case 'locality':
                            city = component.long_name;
                            break;
                        case 'administrative_area_level_1':
                            state = component.long_name;
                            break;
                        case 'postal_code':
                            zip = component.long_name;
                            break;
                        case 'country':
                            country = component.short_name;
                            break;
                    }
                }

                const address = street_number && route ? `${street_number} ${route}` : place.formatted_address;

                // Update Livewire component
                @this.set('restaurant_name', place.name || '');
                @this.set('restaurant_address', address || '');
                @this.set('restaurant_city', city || '');
                @this.set('restaurant_state', state || '');
                @this.set('restaurant_zip_code', zip || '');
                @this.set('restaurant_phone', place.formatted_phone_number || '');
                @this.set('restaurant_website', place.website || '');
                @this.set('google_verified', true);
                @this.set('google_place_data', {
                    place_id: place.place_id,
                    name: place.name,
                    formatted_address: place.formatted_address,
                    lat: place.geometry.location.lat(),
                    lng: place.geometry.location.lng(),
                    types: place.types
                });

                // Update visible input
                document.getElementById('restaurant_autocomplete').value = place.name;

                // Show success message
                @this.dispatch('google-place-selected');

                // Smooth scroll to show filled fields
                setTimeout(() => {
                    document.getElementById('restaurant_address').scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 300);
            });

            // Update Livewire when user types manually (no selection)
            input.addEventListener('blur', function() {
                if (!place || input.value !== place.name) {
                    @this.set('restaurant_name', input.value);
                }
            });
        }

        // Reinitialize on Livewire updates
        document.addEventListener('livewire:init', () => {
            Livewire.on('google-place-selected', () => {
                // Show success notification
                const successDiv = document.createElement('div');
                successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
                successDiv.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>✅ Información cargada desde Google Places!</span>
                    </div>
                `;
                document.body.appendChild(successDiv);

                setTimeout(() => {
                    successDiv.remove();
                }, 3000);
            });
        });
    </script>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        /* Style Google Places autocomplete dropdown */
        .pac-container {
            border-top: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-radius: 0.5rem;
            margin-top: 4px;
            font-family: inherit;
            z-index: 9999;
        }
        .pac-item {
            padding: 10px;
            cursor: pointer;
            border-top: 1px solid #e5e7eb;
        }
        .pac-item:hover {
            background-color: #f3f4f6;
        }
        .pac-item-query {
            font-size: 14px;
            color: #1f2937;
        }
        .pac-icon {
            display: none;
        }
    </style>
    @endpush
</div>
