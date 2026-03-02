<div>
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="max-w-3xl mx-auto">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Sugerir un Restaurante</h1>
                    <p class="text-gray-600">Ayúdanos a crecer nuestra base de datos de restaurantes mexicanos en USA</p>
                </div>

                <!-- Step Indicator -->
                <div class="mb-8">
                    <div class="flex items-center justify-center space-x-4">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step >= 1 ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                                1
                            </div>
                            <span class="ml-2 text-sm font-medium {{ $step >= 1 ? 'text-red-600' : 'text-gray-500' }}">Buscar</span>
                        </div>
                        <div class="w-16 h-1 {{ $step >= 2 ? 'bg-red-600' : 'bg-gray-200' }}"></div>
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step >= 2 ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                                2
                            </div>
                            <span class="ml-2 text-sm font-medium {{ $step >= 2 ? 'text-red-600' : 'text-gray-500' }}">Confirmar</span>
                        </div>
                    </div>
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

                @if (session()->has('warning'))
                    <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('warning') }}</span>
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if (session()->has('info'))
                    <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('info') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Step 1: Search -->
                @if ($step === 1)
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Buscar Restaurante</h2>
                        <p class="text-gray-600 mb-6">
                            Primero buscaremos si el restaurante ya existe en nuestra base de datos, Yelp o Google.
                        </p>

                        <div class="space-y-4">
                            <div>
                                <label for="searchName" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre del Restaurante <span class="text-red-600">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="searchName"
                                    wire:model="searchName"
                                    wire:keydown.enter="search"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    placeholder="Ej: Taqueria El Mexicano"
                                    autofocus
                                >
                                @error('searchName')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="searchCity" class="block text-sm font-medium text-gray-700 mb-1">
                                        Ciudad <span class="text-red-600">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="searchCity"
                                        wire:model="searchCity"
                                        wire:keydown.enter="search"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="Ej: Los Angeles"
                                    >
                                    @error('searchCity')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="searchState" class="block text-sm font-medium text-gray-700 mb-1">
                                        Estado
                                    </label>
                                    <select
                                        id="searchState"
                                        wire:model="searchState"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                        <option value="">Todos los estados</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state->code }}">{{ $state->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-4">
                                <button
                                    type="button"
                                    wire:click="enterManually"
                                    class="text-gray-600 hover:text-gray-800 underline text-sm"
                                >
                                    Ingresar manualmente
                                </button>

                                <button
                                    type="button"
                                    wire:click="search"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
                                >
                                    <svg wire:loading.remove wire:target="search" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <svg wire:loading wire:target="search" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="search">Buscar</span>
                                    <span wire:loading wire:target="search">Buscando...</span>
                                </button>
                            </div>
                        </div>

                        <!-- Search Results -->
                        @if ($hasSearched && !$isSearching)
                            <div class="mt-6 border-t pt-6">
                                @if (count($searchResults) > 0)
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                        Resultados encontrados ({{ count($searchResults) }})
                                    </h3>

                                    <div class="space-y-3">
                                        @foreach ($searchResults as $index => $result)
                                            <div
                                                wire:click="selectResult({{ $index }})"
                                                class="p-4 border rounded-lg cursor-pointer transition-all hover:border-red-300 hover:shadow-md {{ $result['is_existing'] ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200' }}"
                                            >
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div class="flex items-center space-x-2 mb-1">
                                                            <h4 class="font-semibold text-gray-900">{{ $result['name'] }}</h4>
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                                @if($result['source'] === 'database') bg-green-100 text-green-800
                                                                @elseif($result['source'] === 'yelp') bg-red-100 text-red-800
                                                                @else bg-blue-100 text-blue-800
                                                                @endif
                                                            ">
                                                                {{ $result['source_label'] }}
                                                            </span>
                                                        </div>
                                                        <p class="text-sm text-gray-600">
                                                            {{ $result['address'] }}, {{ $result['city'] }}, {{ $result['state'] }}
                                                        </p>
                                                        @if ($result['rating'])
                                                            <div class="flex items-center mt-1 text-sm">
                                                                <span class="text-yellow-500">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        @if ($i <= $result['rating'])
                                                                            ★
                                                                        @else
                                                                            ☆
                                                                        @endif
                                                                    @endfor
                                                                </span>
                                                                <span class="ml-1 text-gray-500">
                                                                    {{ $result['rating'] }}
                                                                    @if ($result['review_count'])
                                                                        ({{ $result['review_count'] }} reseñas)
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @if ($result['image_url'])
                                                        <img src="{{ $result['image_url'] }}" alt="{{ $result['name'] }}" class="w-16 h-16 object-cover rounded-lg ml-4">
                                                    @endif
                                                </div>
                                                @if ($result['is_existing'])
                                                    <p class="mt-2 text-sm text-green-700">
                                                        Este restaurante ya está en FAMER. Haz clic para verlo.
                                                    </p>
                                                @else
                                                    <p class="mt-2 text-sm text-gray-500">
                                                        Haz clic para agregar este restaurante.
                                                    </p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-4 text-center">
                                        <button
                                            type="button"
                                            wire:click="enterManually"
                                            class="text-red-600 hover:text-red-800 font-medium"
                                        >
                                            ¿No encuentras el restaurante? Ingrésalo manualmente
                                        </button>
                                    </div>
                                @else
                                    <div class="text-center py-6">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No encontramos resultados</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            No encontramos "{{ $searchName }}" en {{ $searchCity }}.
                                        </p>
                                        <div class="mt-6">
                                            <button
                                                type="button"
                                                wire:click="enterManually"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700"
                                            >
                                                Ingresar manualmente
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Step 2: Form -->
                @if ($step === 2)
                    <form wire:submit="submit" class="space-y-6">
                        <!-- Selected Source Badge -->
                        @if ($selectedResult)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-blue-800">
                                            Datos cargados desde
                                            <span class="font-semibold">
                                                @if ($resultSource === 'yelp') Yelp
                                                @elseif ($resultSource === 'google') Google
                                                @endif
                                            </span>
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="backToSearch"
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                    >
                                        Volver a buscar
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-end">
                                <button
                                    type="button"
                                    wire:click="backToSearch"
                                    class="text-gray-600 hover:text-gray-800 text-sm font-medium"
                                >
                                    ← Volver a buscar
                                </button>
                            </div>
                        @endif

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
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Restaurante -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-gray-900">Información del Restaurante</h2>
                                @if ($yelp_id || $google_place_id)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Verificado
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="restaurant_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Nombre del Restaurante <span class="text-red-600">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="restaurant_name"
                                        wire:model="restaurant_name"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="Nombre del restaurante"
                                    >
                                    @error('restaurant_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                                                <option value="{{ $state->code }}">{{ $state->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('restaurant_state')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                                    </div>
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
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                        Descripción
                                    </label>
                                    <textarea
                                        id="description"
                                        wire:model="description"
                                        rows="3"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="Cuéntanos sobre este restaurante..."
                                    ></textarea>
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                        Notas Adicionales
                                    </label>
                                    <textarea
                                        id="notes"
                                        wire:model="notes"
                                        rows="2"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                        placeholder="Cualquier información adicional..."
                                    ></textarea>
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
                                <span wire:loading wire:target="submit">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Enviando...
                                </span>
                            </button>
                        </div>
                    </form>
                @endif

                <!-- Info Box -->
                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex">
                        <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">¿Por qué buscamos primero?</p>
                            <p>Al buscar en Yelp y Google, podemos pre-llenar la información del restaurante automáticamente, lo que nos ayuda a verificar que el negocio es legítimo y acelera el proceso de aprobación.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
