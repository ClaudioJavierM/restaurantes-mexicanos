<div>
    <div style="background-color:#0B0B0B;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="max-w-3xl mx-auto">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold mb-2" style="color:#F5F5F5;">Sugerir un Restaurante</h1>
                    <p style="color:#9CA3AF;">Ayúdanos a crecer nuestra base de datos de restaurantes mexicanos en USA</p>
                </div>

                <!-- Step Indicator -->
                <div class="mb-8">
                    <div class="flex items-center justify-center space-x-4">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full font-bold" style="{{ $step >= 1 ? 'background-color:#D4AF37;color:#0B0B0B;' : 'background-color:#2A2A2A;color:#9CA3AF;' }}">
                                1
                            </div>
                            <span class="ml-2 text-sm font-medium" style="{{ $step >= 1 ? 'color:#D4AF37;' : 'color:#9CA3AF;' }}">Buscar</span>
                        </div>
                        <div class="w-16 h-1" style="{{ $step >= 2 ? 'background-color:#D4AF37;' : 'background-color:#2A2A2A;' }}"></div>
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full font-bold" style="{{ $step >= 2 ? 'background-color:#D4AF37;color:#0B0B0B;' : 'background-color:#2A2A2A;color:#9CA3AF;' }}">
                                2
                            </div>
                            <span class="ml-2 text-sm font-medium" style="{{ $step >= 2 ? 'color:#D4AF37;' : 'color:#9CA3AF;' }}">Confirmar</span>
                        </div>
                    </div>
                </div>

                @if (session()->has('success'))
                    <div class="mb-6 px-4 py-3 rounded-lg" style="background-color:#1A2B1A;border:1px solid rgba(212,175,55,0.3);color:#6EE7B7;">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session()->has('warning'))
                    <div class="mb-6 px-4 py-3 rounded-lg" style="background-color:#2B2200;border:1px solid rgba(212,175,55,0.4);color:#D4AF37;">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('warning') }}</span>
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-6 px-4 py-3 rounded-lg" style="background-color:#2B0000;border:1px solid rgba(139,30,30,0.5);color:#FCA5A5;">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if (session()->has('info'))
                    <div class="mb-6 px-4 py-3 rounded-lg" style="background-color:#1A1A2B;border:1px solid rgba(212,175,55,0.2);color:#D4AF37;">
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
                    <div class="rounded-lg p-6" style="background-color:#1A1A1A;border:1px solid #2A2A2A;">
                        <h2 class="text-xl font-bold mb-4" style="color:#F5F5F5;">Buscar Restaurante</h2>
                        <p class="mb-6" style="color:#9CA3AF;">
                            Primero buscaremos si el restaurante ya existe en nuestra base de datos, Yelp o Google.
                        </p>

                        <div class="space-y-4">
                            <div>
                                <label for="searchName" class="block text-sm font-medium mb-1" style="color:#9CA3AF;">
                                    Nombre del Restaurante <span style="color:#D4AF37;">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="searchName"
                                    wire:model="searchName"
                                    wire:keydown.enter="search"
                                    class="w-full rounded-md shadow-sm"
                                    style="background-color:#111111;border:1px solid #2A2A2A;color:#F5F5F5;padding:0.5rem 0.75rem;outline:none;"
                                    placeholder="Ej: Taqueria El Mexicano"
                                    autofocus
                                    onfocus="this.style.borderColor='#D4AF37';this.style.boxShadow='0 0 0 1px #D4AF37';"
                                    onblur="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';"
                                >
                                @error('searchName')
                                    <p class="mt-1 text-sm" style="color:#FCA5A5;">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="searchCity" class="block text-sm font-medium mb-1" style="color:#9CA3AF;">
                                        Ciudad <span style="color:#D4AF37;">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="searchCity"
                                        wire:model="searchCity"
                                        wire:keydown.enter="search"
                                        class="w-full rounded-md shadow-sm"
                                        style="background-color:#111111;border:1px solid #2A2A2A;color:#F5F5F5;padding:0.5rem 0.75rem;outline:none;"
                                        placeholder="Ej: Los Angeles"
                                        onfocus="this.style.borderColor='#D4AF37';this.style.boxShadow='0 0 0 1px #D4AF37';"
                                        onblur="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';"
                                    >
                                    @error('searchCity')
                                        <p class="mt-1 text-sm" style="color:#FCA5A5;">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="searchState" class="block text-sm font-medium mb-1" style="color:#9CA3AF;">
                                        Estado
                                    </label>
                                    <select
                                        id="searchState"
                                        wire:model="searchState"
                                        class="w-full rounded-md shadow-sm"
                                        style="background-color:#111111;border:1px solid #2A2A2A;color:#F5F5F5;padding:0.5rem 0.75rem;outline:none;"
                                        onfocus="this.style.borderColor='#D4AF37';this.style.boxShadow='0 0 0 1px #D4AF37';"
                                        onblur="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';"
                                    >
                                        <option value="" style="background-color:#111111;">Todos los estados</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state->code }}" style="background-color:#111111;">{{ $state->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-4">
                                <button
                                    type="button"
                                    wire:click="enterManually"
                                    class="text-sm underline transition-colors"
                                    style="color:#9CA3AF;"
                                    onmouseover="this.style.color='#D4AF37';"
                                    onmouseout="this.style.color='#9CA3AF';"
                                >
                                    Ingresar manualmente
                                </button>

                                <button
                                    type="button"
                                    wire:click="search"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 transition-colors"
                                    style="background-color:#D4AF37;color:#0B0B0B;focus-ring-color:#D4AF37;"
                                    onmouseover="this.style.backgroundColor='#B8941F';"
                                    onmouseout="this.style.backgroundColor='#D4AF37';"
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
                            <div class="mt-6 pt-6" style="border-top:1px solid #2A2A2A;">
                                @if (count($searchResults) > 0)
                                    <h3 class="text-lg font-semibold mb-4" style="color:#F5F5F5;">
                                        Resultados encontrados ({{ count($searchResults) }})
                                    </h3>

                                    <div class="space-y-3">
                                        @foreach ($searchResults as $index => $result)
                                            <div
                                                wire:click="selectResult({{ $index }})"
                                                class="p-4 rounded-lg cursor-pointer transition-all"
                                                style="{{ $result['is_existing'] ? 'background-color:#1A2B1A;border:1px solid rgba(110,231,183,0.3);' : 'background-color:#111111;border:1px solid #2A2A2A;' }}"
                                                onmouseover="this.style.borderColor='rgba(212,175,55,0.5)';this.style.boxShadow='0 4px 12px rgba(212,175,55,0.1)';"
                                                onmouseout="this.style.borderColor='{{ $result['is_existing'] ? 'rgba(110,231,183,0.3)' : '#2A2A2A' }}';this.style.boxShadow='none';"
                                            >
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div class="flex items-center space-x-2 mb-1">
                                                            <h4 class="font-semibold" style="color:#F5F5F5;">{{ $result['name'] }}</h4>
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                                                style="
                                                                    @if($result['source'] === 'database') background-color:rgba(110,231,183,0.15);color:#6EE7B7;
                                                                    @elseif($result['source'] === 'yelp') background-color:rgba(175,6,6,0.15);color:#AF0606;
                                                                    @else background-color:rgba(212,175,55,0.15);color:#D4AF37;
                                                                    @endif
                                                                "
                                                            >
                                                                {{ $result['source_label'] }}
                                                            </span>
                                                        </div>
                                                        <p class="text-sm" style="color:#9CA3AF;">
                                                            {{ $result['address'] }}, {{ $result['city'] }}, {{ $result['state'] }}
                                                        </p>
                                                        @if ($result['rating'])
                                                            <div class="flex items-center mt-1 text-sm">
                                                                <span style="color:#D4AF37;">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        @if ($i <= $result['rating'])
                                                                            ★
                                                                        @else
                                                                            ☆
                                                                        @endif
                                                                    @endfor
                                                                </span>
                                                                <span class="ml-1" style="color:#9CA3AF;">
                                                                    {{ $result['rating'] }}
                                                                    @if ($result['review_count'])
                                                                        ({{ $result['review_count'] }} reseñas)
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @if ($result['image_url'])
                                                        <img src="{{ $result['image_url'] }}" alt="{{ $result['name'] }}" class="w-16 h-16 object-cover rounded-lg ml-4" style="border:1px solid #2A2A2A;">
                                                    @endif
                                                </div>
                                                @if ($result['is_existing'])
                                                    <p class="mt-2 text-sm" style="color:#6EE7B7;">
                                                        Este restaurante ya está en FAMER. Haz clic para verlo.
                                                    </p>
                                                @else
                                                    <p class="mt-2 text-sm" style="color:#9CA3AF;">
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
                                            class="font-medium transition-colors"
                                            style="color:#D4AF37;"
                                            onmouseover="this.style.color='#B8941F';"
                                            onmouseout="this.style.color='#D4AF37';"
                                        >
                                            ¿No encuentras el restaurante? Ingrésalo manualmente
                                        </button>
                                    </div>
                                @else
                                    <div class="text-center py-6">
                                        <svg class="mx-auto h-12 w-12" style="color:#2A2A2A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium" style="color:#F5F5F5;">No encontramos resultados</h3>
                                        <p class="mt-1 text-sm" style="color:#9CA3AF;">
                                            No encontramos "{{ $searchName }}" en {{ $searchCity }}.
                                        </p>
                                        <div class="mt-6">
                                            <button
                                                type="button"
                                                wire:click="enterManually"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md transition-colors"
                                                style="background-color:#D4AF37;color:#0B0B0B;"
                                                onmouseover="this.style.backgroundColor='#B8941F';"
                                                onmouseout="this.style.backgroundColor='#D4AF37';"
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
                            <div class="rounded-lg p-4" style="background-color:#1A1A2B;border:1px solid rgba(212,175,55,0.3);">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" style="color:#D4AF37;" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span style="color:#D4AF37;">
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
                                        class="text-sm font-medium transition-colors"
                                        style="color:#D4AF37;"
                                        onmouseover="this.style.color='#B8941F';"
                                        onmouseout="this.style.color='#D4AF37';"
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
                                    class="text-sm font-medium transition-colors"
                                    style="color:#9CA3AF;"
                                    onmouseover="this.style.color='#F5F5F5';"
                                    onmouseout="this.style.color='#9CA3AF';"
                                >
                                    ← Volver a buscar
                                </button>
                            </div>
                        @endif

                        <!-- Tu Información -->
                        <div class="rounded-lg p-6" style="background-color:#1A1A1A;border:1px solid #2A2A2A;">
                            <h2 class="text-xl font-bold mb-4" style="color:#F5F5F5;">Tu Información</h2>
                            <div class="space-y-4">
                                <div>
                                    <label for="submitter_name" class="block text-sm font-medium mb-1" style="color:#9CA3AF;">
                                        Nombre <span style="color:#D4AF37;">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="submitter_name"
                                        wire:model="submitter_name"
                                        class="w-full rounded-md shadow-sm"
                                        style="background-color:#111111;border:1px solid #2A2A2A;color:#F5F5F5;padding:0.5rem 0.75rem;outline:none;"
                                        placeholder="Tu nombre completo"
                                        onfocus="this.style.borderColor='#D4AF37';this.style.boxShadow='0 0 0 1px #D4AF37';"
                                        onblur="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';"
                                    >
                                    @error('submitter_name')
                                        <p class="mt-1 text-sm" style="color:#FCA5A5;">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="submitter_email" class="block text-sm font-medium mb-1" style="color:#9CA3AF;">
                                            Email <span style="color:#D4AF37;">*</span>
                                        </label>
                                        <input
                                            type="email"
                                            id="submitter_email"
                                            wire:model="submitter_email"
                                            class="w-full rounded-md shadow-sm"
                                            style="background-color:#111111;border:1px solid #2A2A2A;color:#F5F5F5;padding:0.5rem 0.75rem;outline:none;"
                                            placeholder="tu@email.com"
                                            onfocus="this.style.borderColor='#D4AF37';this.style.boxShadow='0 0 0 1px #D4AF37';"
                                            onblur="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';"
                                        >
                                        @error('submitter_email')
                                            <p class="mt-1 text-sm" style="color:#FCA5A5;">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="submitter_phone" class="block text-sm font-medium mb-1" style="color:#9CA3AF;">
                                            Teléfono (opcional)
                                        </label>
                                        <input
                                            type="tel"
                                            id="submitter_phone"
                                            wire:model="submitter_phone"
                                            class="w-full rounded-md shadow-sm"
                                            style="background-color:#111111;border:1px solid #2A2A2A;color:#F5F5F5;padding:0.5rem 0.75rem;outline:none;"
                                            placeholder="(123) 456-7890"
                                            onfocus="this.style.borderColor='#D4AF37';this.style.boxShadow='0 0 0 1px #D4AF37';"
                                            onblur="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Restaurante -->
                        <div class="rounded-lg p-6" style="background-color:#1A1A1A;border:1px solid #2A2A2A;">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold" style="color:#F5F5F5;">Información del Restaurante</h2>
                                @if ($yelp_id || $google_place_id)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" style="background-color:rgba(110,231,183,0.15);color:#6EE7B7;">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Verificado
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="restaurant_name" class="block text-sm font-medium mb-1" style="color:#9CA3AF;">
                                        Nombre del Restaurante <span style="color:#D4AF37;">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="restaurant_name"
                                        wire:model="restaurant_name"
                                        class="w-full rounded-md shadow-sm"
                                        style="background-color:#111111;border:1px solid #2A2A2A;color:#F5F5F5;padding:0.5rem 0.75rem;outline:none;"
                                        placeholder="Nombre del restaurante"
                                        onfocus="this.style.borderColor='#D4AF37';this.style.boxShadow='0 0 0 1px #D4AF37';"
                                        onblur="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';"
                                    >
                                    @error('restaurant_name')
                                        <p class="mt-1 text-sm" style="color:#FCA5A5;">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="restaurant_address" class="block text-sm font-medium mb-1" style="color:#9CA3AF;">
                                        Dirección <span style="color:#D4AF37;">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="restaurant_address"
                                        wire:model="restaurant_address"
                                        class="w-full rounded-md shadow-sm"
                                        style="background-color:#111111;border:1px solid #2A2A2A;color:#F5F5F5;padding:0.5rem 0.75rem;outline:none;"
                                        placeholder="123 Main Street"
                                        onfocus="this.style.borderColor='#D4AF37';this.style.boxShadow='0 0 0 1px #D4AF37';"
                                        onblur="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';"
                                    >
                                    @error('restaurant_address')
                                        <p class="mt-1 text-sm" style="color:#FCA5A5;">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="restaurant_city" class="block text-sm font-medium mb-1" style="color:#9CA3AF;">
                                            Ciudad <span style="color:#D4AF37;">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            id="restaurant_city"
                                            wire:model="restaurant_city"
                                            class="w-full rounded-md shadow-sm"
                                            style="background-color:#111111;border:1px solid #2A2A2A;color:#F5F5F5;padding:0.5rem 0.75rem;outline:none;"
                                            placeholder="Los Angeles"
                                            onfocus="this.style.borderColor='#D4AF37';this.style.boxShadow='0 0 0 1px #D4AF37';"
                                            onblur="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';"
                                        >
                                        @error('restaurant_city')
                                            <p class="mt-1 text-sm" style="color:#FCA5A5;">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="restaurant_state" class="block text-sm font-medium mb-1" style="color:#9CA3AF;">
                                            Estado <span style="color:#D4AF37;">*</span>
                                        </label>
                                        <select
                                            id="restaurant_state"
                                            wire:model="restaurant_state"
                                            class="w-full rounded-md shadow-sm"
                                            style="background-color:#111111;border:1px solid #2A2A2A;color:#F5F5F5;padding:0.5rem 0.75rem;outline:none;"
                                            onfocus="this.style.borderColor='#D4AF37';this.style.boxShadow='0 0 0 1px #D4AF37';"
                                            onblur="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';"
                                        >
                                            <option value="" style="background-color:#111111;">Selecciona...</option>
                                            @foreach($states as $state)
                                                <option value="{{ $state->code }}" style="background-color:#111111;">{{ $state->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('restaurant_state')
                                            <p class="mt-1 text-sm" style="color:#FCA5A5;">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="restaurant_zip_code" class="block text-sm font-medium mb-1" style="color:#9CA3AF;">
                                            Código Postal
                                        </label>
                                        <input
                                            type="text"
                                            id="restaurant_zip_code"
                                            wire:model="restaurant_zip_code"
                                            class="w-full rounded-md shadow-sm"
                                            style="background-color:#111111;border:1px solid #2A2A2A;color:#F5F5F5;padding:0.5rem 0.75rem;outline:none;"
                                            placeholder="90001"
                                            onfocus="this.style.borderColor='#D4AF37';this.style.boxShadow='0 0 0 1px #D4AF37';"
                                            onblur="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';"
                                        >
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="restaurant_phone" class="block text-sm font-medium mb-1" style="color:#9CA3AF;">
                                            Teléfono
                                        </label>
                                        <input
                                            type="tel"
                                            id="restaurant_phone"
                                            wire:model="restaurant_phone"
                                            class="w-full rounded-md shadow-sm"
                                            style="background-color:#111111;border:1px solid #2A2A2A;color:#F5F5F5;padding:0.5rem 0.75rem;outline:none;"
                                            placeholder="(123) 456-7890"
                                            onfocus="this.style.borderColor='#D4AF37';this.style.boxShadow='0 0 0 1px #D4AF37';"
                                            onblur="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';"
                                        >
                                        @error('restaurant_phone')
                                            <p class="mt-1 text-sm" style="color:#FCA5A5;">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="restaurant_website" class="block text-sm font-medium mb-1" style="color:#9CA3AF;">
                                            Sitio Web
                                        </label>
                                        <input
                                            type="url"
                                            id="restaurant_website"
                                            wire:model="restaurant_website"
                                            class="w-full rounded-md shadow-sm"
                                            style="background-color:#111111;border:1px solid #2A2A2A;color:#F5F5F5;padding:0.5rem 0.75rem;outline:none;"
                                            placeholder="https://ejemplo.com"
                                            onfocus="this.style.borderColor='#D4AF37';this.style.boxShadow='0 0 0 1px #D4AF37';"
                                            onblur="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';"
                                        >
                                        @error('restaurant_website')
                                            <p class="mt-1 text-sm" style="color:#FCA5A5;">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="category_id" class="block text-sm font-medium mb-1" style="color:#9CA3AF;">
                                        Tipo de Comida <span style="color:#D4AF37;">*</span>
                                    </label>
                                    <select
                                        id="category_id"
                                        wire:model="category_id"
                                        class="w-full rounded-md shadow-sm"
                                        style="background-color:#111111;border:1px solid #2A2A2A;color:#F5F5F5;padding:0.5rem 0.75rem;outline:none;"
                                        onfocus="this.style.borderColor='#D4AF37';this.style.boxShadow='0 0 0 1px #D4AF37';"
                                        onblur="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';"
                                    >
                                        <option value="" style="background-color:#111111;">Selecciona una categoría...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" style="background-color:#111111;">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <p class="mt-1 text-sm" style="color:#FCA5A5;">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium mb-1" style="color:#9CA3AF;">
                                        Descripción
                                    </label>
                                    <textarea
                                        id="description"
                                        wire:model="description"
                                        rows="3"
                                        class="w-full rounded-md shadow-sm"
                                        style="background-color:#111111;border:1px solid #2A2A2A;color:#F5F5F5;padding:0.5rem 0.75rem;outline:none;resize:vertical;"
                                        placeholder="Cuéntanos sobre este restaurante..."
                                        onfocus="this.style.borderColor='#D4AF37';this.style.boxShadow='0 0 0 1px #D4AF37';"
                                        onblur="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';"
                                    ></textarea>
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-medium mb-1" style="color:#9CA3AF;">
                                        Notas Adicionales
                                    </label>
                                    <textarea
                                        id="notes"
                                        wire:model="notes"
                                        rows="2"
                                        class="w-full rounded-md shadow-sm"
                                        style="background-color:#111111;border:1px solid #2A2A2A;color:#F5F5F5;padding:0.5rem 0.75rem;outline:none;resize:vertical;"
                                        placeholder="Cualquier información adicional..."
                                        onfocus="this.style.borderColor='#D4AF37';this.style.boxShadow='0 0 0 1px #D4AF37';"
                                        onblur="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';"
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Validation Error Summary -->
                        @if($errors->any())
                            <div class="rounded-md p-4" style="background-color:#2B0000;border:1px solid rgba(139,30,30,0.5);">
                                <p class="text-sm font-medium" style="color:#FCA5A5;">Por favor corrige los siguientes campos:</p>
                                <ul class="mt-2 text-sm list-disc list-inside" style="color:#FCA5A5;">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between">
                            <p class="text-sm" style="color:#9CA3AF;">
                                <span style="color:#D4AF37;">*</span> Campos requeridos
                            </p>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 transition-colors"
                                style="background-color:#D4AF37;color:#0B0B0B;"
                                onmouseover="this.style.backgroundColor='#B8941F';"
                                onmouseout="this.style.backgroundColor='#D4AF37';"
                            >
                                <span wire:loading.remove wire:target="submit">Enviar Sugerencia</span>
                                <span wire:loading wire:target="submit">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5" style="color:#0B0B0B;" fill="none" viewBox="0 0 24 24">
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
                <div class="mt-8 rounded-lg p-6" style="background-color:#1A1A1A;border:1px solid rgba(212,175,55,0.2);">
                    <div class="flex">
                        <svg class="w-6 h-6 mr-3 flex-shrink-0" style="color:#D4AF37;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm" style="color:#9CA3AF;">
                            <p class="font-semibold mb-1" style="color:#D4AF37;">¿Por qué buscamos primero?</p>
                            <p>Al buscar en Yelp y Google, podemos pre-llenar la información del restaurante automáticamente, lo que nos ayuda a verificar que el negocio es legítimo y acelera el proceso de aprobación.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
