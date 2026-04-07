<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white py-12">
    <style>
    .elite-card * { color: white !important; }
    .elite-card .elite-price { color: white !important; font-size: 2.25rem !important; font-weight: bold !important; }
    .elite-card .elite-subtitle { color: #e9d5ff !important; }
    .elite-card li { color: white !important; }
    .elite-card strong { color: white !important; }
    .elite-card .elite-support { color: #e9d5ff !important; }
    .elite-card button { color: #1f2937 !important; }
    </style>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Progress Steps --}}
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-4">
                {{-- Step 1: Search --}}
                <div class="flex items-center {{ $step !== 'search' ? 'cursor-pointer' : '' }}" @if($step !== 'search') wire:click="backToSearch" @endif>
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step === 'search' ? 'bg-red-600 text-white' : ($step === 'verify' || $step === 'verify_code' || $step === 'select_plan' || $step === 'payment' ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-gray-300 text-gray-600') }} transition-colors">
                        @if($step === 'verify' || $step === 'verify_code' || $step === 'select_plan' || $step === 'payment')
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            1
                        @endif
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $step === 'search' ? 'text-red-600' : 'text-gray-600' }}">{{ __('app.search') }}</span>
                </div>

                <div class="w-16 h-1 {{ $step === 'verify' || $step === 'verify_code' || $step === 'select_plan' || $step === 'payment' ? 'bg-green-600' : 'bg-gray-300' }}"></div>

                {{-- Step 2: Verify --}}
                <div class="flex items-center {{ $step === 'select_plan' || $step === 'payment' ? 'cursor-pointer' : '' }}" @if($step === 'select_plan' || $step === 'payment') wire:click="backToVerify" @endif>
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step === 'verify' || $step === 'verify_code' ? 'bg-red-600 text-white' : ($step === 'select_plan' || $step === 'payment' ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-gray-300 text-gray-600') }} transition-colors">
                        @if($step === 'select_plan' || $step === 'payment')
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            2
                        @endif
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $step === 'verify' ? 'text-red-600' : 'text-gray-600' }}">{{ __('app.claim_step_verify') }}</span>
                </div>

                <div class="w-16 h-1 {{ $step === 'select_plan' || $step === 'payment' ? 'bg-green-600' : 'bg-gray-300' }}"></div>

                {{-- Step 3: Select Plan --}}
                <div class="flex items-center {{ $step === 'payment' ? 'cursor-pointer' : '' }}" @if($step === 'payment') wire:click="backToSelectPlan" @endif>
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step === 'select_plan' ? 'bg-red-600 text-white' : ($step === 'payment' ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-gray-300 text-gray-600') }} transition-colors">
                        @if($step === 'payment')
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            3
                        @endif
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $step === 'select_plan' ? 'text-red-600' : 'text-gray-600' }}">{{ __('app.claim_step_plan') }}</span>
                </div>

                <div class="w-16 h-1 {{ $step === 'payment' ? 'bg-green-600' : 'bg-gray-300' }}"></div>

                {{-- Step 4: Payment --}}
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $step === 'payment' ? 'bg-red-600 text-white' : 'bg-gray-300 text-gray-600' }}">
                        4
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $step === 'payment' ? 'text-red-600' : 'text-gray-600' }}">{{ __('app.claim_step_payment') }}</span>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('error'))
            <div class="mb-6 bg-danger-50 border border-danger-200 text-danger-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('success') && session('success') === '¡Felicidades! Tu restaurante ha sido reclamado exitosamente.')
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center max-w-2xl mx-auto">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-4">¡Felicidades!</h1>
                <p class="text-xl text-gray-600 mb-6">Tu restaurante ha sido reclamado exitosamente.</p>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-green-800">
                        <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Ahora puedes acceder a tu Dashboard para administrar tu restaurante.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/owner/dashboard" class="bg-red-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        Ir al Dashboard
                    </a>
                    <a href="/" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors inline-flex items-center justify-center">
                        Volver al Inicio
                    </a>
                </div>
            </div>
        @elseif (session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- STEP 1: SEARCH --}}
        @if($step === 'search')
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('app.claim_title') }}</h1>
                    <p class="text-gray-600">{{ __('app.claim_search_subtitle') }}</p>
                </div>

                <form wire:submit.prevent="searchRestaurants" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('app.claim_search_label') }}
                            </label>
                            <input
                                type="text"
                                wire:model="search"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                placeholder="{{ __('app.claim_search_placeholder') }}"
                            >
                            @error('search')
                                <span class="text-danger-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('app.claim_state_label') }}
                            </label>
                            <select
                                wire:model="selectedState"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            >
                                <option value="">{{ __('app.all_states') }}</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-red-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-red-700 transition-colors"
                    >
                        {{ __('app.claim_search_button') }}
                    </button>
                </form>

                {{-- Search Results --}}
                @if($searchResults->isNotEmpty())
                    <div class="mt-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">
                            {{ __('app.claim_results_found') }} ({{ $searchResults->count() }})
                        </h3>
                        <div class="space-y-4">
                            @foreach($searchResults as $restaurant)
                                <div class="border border-gray-200 rounded-lg p-6 hover:border-red-300 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $restaurant->name }}</h4>
                                            <p class="text-gray-600 mt-1">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ $restaurant->address }}, {{ $restaurant->city }}, {{ $restaurant->state->name }}
                                            </p>
                                            @if($restaurant->phone)
                                                <p class="text-gray-600 mt-1">
                                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                    </svg>
                                                    {{ $restaurant->phone }}
                                                </p>
                                            @endif
                                            @if($restaurant->category)
                                                <span class="inline-block mt-2 px-3 py-1 bg-red-100 text-red-700 text-sm rounded-full">
                                                    {{ $restaurant->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                        <button
                                            wire:click="selectRestaurant({{ $restaurant->id }})"
                                            class="ml-4 bg-red-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors whitespace-nowrap"
                                        >
                                            Claim
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Always-visible Add Restaurant Link --}}
                        <div class="mt-6 text-center p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <p class="text-sm text-gray-700">
                                {{ __('app.claim_cant_find_restaurant') }}
                                <a href="{{ route('suggestions.create') }}" class="text-red-600 hover:text-red-700 font-semibold underline ml-1">
                                    {{ __('app.claim_add_it_here') }}
                                </a>
                            </p>
                        </div>
                    </div>
                @elseif($search)
                    <div class="mt-8 text-center p-8 bg-gray-50 rounded-lg">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('app.claim_not_found_title') }}</h3>
                        <p class="text-gray-600 mb-4">{{ __('app.claim_not_found_text') }}</p>
                        <a href="{{ route('suggestions.create') }}" class="inline-block bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            {{ __('app.claim_register_button') }}
                        </a>
                    </div>
                @endif
            </div>
        @endif

        {{-- STEP 2: VERIFY --}}
        @if($step === 'verify' && $selectedRestaurant)
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <button
                    wire:click="backToSearch"
                    class="mb-6 text-red-600 hover:text-red-700 font-medium flex items-center"
                >
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('app.claim_back_to_search') }}
                </button>

                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('app.claim_verify_title') }}</h1>
                    <p class="text-gray-600">{{ __('app.claim_verify_subtitle') }}</p>
                </div>

                {{-- Selected Restaurant Info --}}
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $selectedRestaurant->name }}</h3>
                    <p class="text-gray-600">
                        {{ $selectedRestaurant->address }}, {{ $selectedRestaurant->city }}, {{ $selectedRestaurant->state->name }}
                    </p>
                </div>

                {{-- Verification Form --}}
                <form wire:submit.prevent="submitVerification" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('app.claim_owner_name') }} *
                        </label>
                        <input
                            type="text"
                            wire:model="ownerName"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            placeholder="John Smith"
                        >
                        @error('ownerName')
                            <span class="text-danger-600 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('app.claim_owner_email') }} *
                        </label>
                        <input
                            type="email"
                            wire:model="ownerEmail"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            placeholder="john@restaurant.com"
                        >
                        @error('ownerEmail')
                            <span class="text-danger-600 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('app.claim_owner_phone') }} *
                        </label>
                        <input
                            type="tel"
                            wire:model="ownerPhone"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            placeholder="(555) 123-4567"
                        >
                        @error('ownerPhone')
                            <span class="text-danger-600 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Verification Method Selection --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Metodo de verificacion
                        </label>

                        @if(count($availableMethods) === 0)
                            {{-- No methods available --}}
                            <div class="bg-danger-50 border border-danger-200 rounded-lg p-4">
                                <p class="text-sm text-danger-800">
                                    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Este restaurante no tiene correo electronico ni telefono registrado. Por favor contacta a soporte.
                                </p>
                            </div>
                        @elseif(count($availableMethods) === 1)
                            {{-- Only one method available - show info --}}
                            @if($availableMethods[0] === 'email')
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start">
                                    <svg class="w-6 h-6 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-blue-900">Verificacion por correo electronico</p>
                                        <p class="text-xs text-blue-700 mt-1">Se enviara un codigo de 6 digitos al correo registrado del restaurante.</p>
                                    </div>
                                </div>
                                <input type="hidden" wire:model="verificationMethod" value="email">
                            @else
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start">
                                    <svg class="w-6 h-6 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-blue-900">Verificacion por llamada telefonica</p>
                                        <p class="text-xs text-blue-700 mt-1">Recibirás una llamada al telefono del restaurante con un codigo de 6 digitos.</p>
                                    </div>
                                </div>
                                <input type="hidden" wire:model="verificationMethod" value="phone">
                            @endif
                        @else
                            {{-- Both methods available - clickable card selector --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                {{-- Email option --}}
                                <div
                                    wire:click="$set('verificationMethod', 'email')"
                                    class="border-2 rounded-lg p-4 transition-all cursor-pointer {{ $verificationMethod === 'email' ? 'border-red-600 bg-red-50' : 'border-gray-200 hover:border-gray-300' }}"
                                >
                                    <div class="flex items-start">
                                        <div class="w-10 h-10 rounded-full {{ $verificationMethod === 'email' ? 'bg-red-100' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0 mr-3">
                                            <svg class="w-5 h-5 {{ $verificationMethod === 'email' ? 'text-red-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">Correo electronico</p>
                                            <p class="text-xs text-gray-500 mt-1">Codigo enviado al email del restaurante</p>
                                        </div>
                                        @if($verificationMethod === 'email')
                                            <svg class="w-5 h-5 text-red-600 ml-auto flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>

                                {{-- Phone option --}}
                                <div
                                    wire:click="$set('verificationMethod', 'phone')"
                                    class="border-2 rounded-lg p-4 transition-all cursor-pointer {{ $verificationMethod === 'phone' ? 'border-red-600 bg-red-50' : 'border-gray-200 hover:border-gray-300' }}"
                                >
                                    <div class="flex items-start">
                                        <div class="w-10 h-10 rounded-full {{ $verificationMethod === 'phone' ? 'bg-red-100' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0 mr-3">
                                            <svg class="w-5 h-5 {{ $verificationMethod === 'phone' ? 'text-red-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">Llamada telefonica</p>
                                            <p class="text-xs text-gray-500 mt-1">Llamada con codigo al telefono del restaurante</p>
                                        </div>
                                        @if($verificationMethod === 'phone')
                                            <svg class="w-5 h-5 text-red-600 ml-auto flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            El codigo de verificacion se enviara a los datos de contacto registrados del restaurante para confirmar que eres el propietario.
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-red-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-red-700 transition-colors"
                    >
                        {{ __('app.claim_continue_plan') }}
                    </button>
                </form>
            </div>
        @endif


        {{-- STEP 2.5: VERIFY CODE --}}
        @if($step === 'verify_code' && $selectedRestaurant)
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <button
                    wire:click="backToVerify"
                    class="mb-6 text-red-600 hover:text-red-700 font-medium flex items-center"
                >
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </button>

                <div class="text-center mb-8">
                    @if($verificationMethod === 'phone')
                        {{-- Phone call verification header --}}
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Verifica tu identidad</h1>
                        <p class="text-gray-600">Estamos llamando al telefono del restaurante con tu codigo de verificacion:</p>
                        @php
                            $rPhone = $selectedRestaurant->phone ?? '';
                            $rPhoneCleaned = preg_replace('/[^0-9]/', '', $rPhone);
                            $rPhoneLast4 = substr($rPhoneCleaned, -4);
                            $rPhoneMasked = '(***) ***-' . $rPhoneLast4;
                        @endphp
                        <p class="text-gray-900 font-semibold mt-1">{{ $rPhoneMasked }}</p>
                        <div class="mt-3 bg-green-50 border border-green-200 rounded-lg p-3 inline-block">
                            <p class="text-green-800 text-sm flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                Contesta la llamada para escuchar el codigo
                            </p>
                        </div>
                    @else
                        {{-- Email verification header --}}
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Verifica tu identidad</h1>
                        <p class="text-gray-600">Hemos enviado un codigo de 6 digitos al correo registrado del restaurante:</p>
                        @php
                            $rEmail = $selectedRestaurant->email ?? $ownerEmail;
                            $rParts = explode('@', $rEmail);
                            $rMasked = substr($rParts[0], 0, 2) . str_repeat('*', max(3, strlen($rParts[0]) - 2)) . '@' . ($rParts[1] ?? '');
                        @endphp
                        <p class="text-gray-900 font-semibold mt-1">{{ $rMasked }}</p>
                        <p class="text-gray-500 text-sm mt-2">Si no tienes acceso a este correo, contacta a soporte.</p>
                    @endif
                </div>

                @if (session()->has('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <form wire:submit.prevent="verifyCode" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 text-center">
                            Ingresa el codigo de verificacion
                        </label>
                        <input
                            type="text"
                            wire:model="verificationCode"
                            class="w-full px-4 py-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-center text-2xl tracking-widest font-mono"
                            placeholder="000000"
                            maxlength="6"
                            inputmode="numeric"
                        >
                        @error('verificationCode')
                            <span class="text-danger-600 text-sm mt-1 block text-center">{{ $message }}</span>
                        @enderror
                        @if($codeError)
                            <span class="text-danger-600 text-sm mt-1 block text-center">{{ $codeError }}</span>
                        @endif
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-red-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-red-700 transition-colors"
                    >
                        Verificar codigo
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600 text-sm">
                        @if($verificationMethod === 'phone')
                            No recibiste la llamada?
                            <button wire:click="resendCode" class="text-red-600 hover:text-red-700 font-medium ml-1">
                                Volver a llamar
                            </button>
                        @else
                            No recibiste el codigo?
                            <button wire:click="resendCode" class="text-red-600 hover:text-red-700 font-medium ml-1">
                                Reenviar codigo
                            </button>
                        @endif
                    </p>
                    <p class="text-gray-500 text-xs mt-2">El codigo expira en 15 minutos</p>
                </div>
            </div>
        @endif

        {{-- STEP 3: SELECT PLAN --}}
        @if($step === 'select_plan')
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <button
                    wire:click="backToVerify"
                    class="mb-6 text-red-600 hover:text-red-700 font-medium flex items-center"
                >
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </button>

                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Selecciona tu Plan</h1>
                    <p class="text-gray-600">Elige el plan que mejor se adapte a tu restaurante</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- FREE PLAN --}}
                    <div class="border-2 {{ $selectedPlan === 'free' ? 'border-red-600' : 'border-gray-200' }} rounded-xl p-6 hover:border-red-300 transition-colors">
                        <div class="text-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Listado Gratis</h3>
                            <div class="text-4xl font-bold text-gray-900 mb-1">
                                $0
                            </div>
                            <p class="text-sm text-gray-600">Listado básico</p>
                        </div>

                        <ul class="space-y-3 mb-6">
                            <li class="flex items-center text-sm text-gray-700">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Aparece en el directorio
                            </li>
                            <li class="flex items-center text-sm text-gray-700">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Info básica (nombre, dirección, teléfono)
                            </li>
                            <li class="flex items-center text-sm text-gray-700">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Integración con Google Maps
                            </li>
                            <li class="flex items-center text-sm text-gray-700">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Verificar propiedad del restaurante
                            </li>
                            <li class="flex items-center text-sm text-gray-400">
                                <svg class="w-5 h-5 text-gray-300 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                Sin prioridad en búsquedas
                            </li>
                        </ul>

                        <button wire:click="selectPlan('free')" class="w-full bg-gray-800 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-900 transition-colors">
                            Reclamar Gratis
                        </button>
                    </div>

                    {{-- PREMIUM PLAN --}}
                    <div class="border-2 border-yellow-400 rounded-xl p-6 relative bg-gradient-to-b from-yellow-50 to-white">
                        <div class="absolute -top-3 left-4">
                            <span class="bg-red-600 text-white px-3 py-1 rounded-full text-xs font-bold">MÁS POPULAR</span>
                        </div>
                        <div class="absolute -top-3 right-4">
                            <span class="bg-yellow-500 text-white px-2 py-1 rounded text-xs font-bold">OFERTA PRIMER MES</span>
                        </div>

                        <div class="text-center mb-6 mt-2">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Premium</h3>
                            <div class="flex items-center justify-center gap-2">
                                <span class="text-lg text-gray-400 line-through">$39</span>
                                <span class="text-4xl font-bold text-gray-900">$9.99</span>
                            </div>
                            <p class="text-sm text-yellow-600 font-semibold">primer mes</p>
                            <p class="text-xs text-gray-500">Después $39/mes</p>
                        </div>

                        <ul class="space-y-2 mb-6 text-sm">
                            <li class="flex items-center text-gray-700">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Todo lo de Free PLUS:
                            </li>
                            <li class="flex items-center text-gray-700">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Badge Destacado
                            </li>
                            <li class="flex items-center text-gray-700">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <strong>Top 3 en búsquedas</strong> locales
                            </li>
                            <li class="flex items-center text-gray-700">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Menú Digital + QR Code
                            </li>
                            <li class="flex items-center text-gray-700">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Sistema de Reservaciones
                            </li>
                            <li class="flex items-center text-gray-700">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Dashboard de Analíticas
                            </li>
                            <li class="flex items-center text-gray-700">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Chatbot AI (ES/EN) 24/7
                            </li>
                        </ul>

                        <button wire:click="selectPlan('premium')" class="w-full bg-yellow-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-yellow-600 transition-colors">
                            Suscribirse por $9.99
                        </button>
                        <p class="text-xs text-center text-gray-500 mt-2">Cancela cuando quieras</p>
                    </div>

                    {{-- ELITE PLAN --}}
                    <div class="border-2 border-purple-500 rounded-xl p-6 relative" style="background: linear-gradient(to bottom, #581c87, #6b21a8);">
                        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                            <span class="bg-purple-500 text-white px-3 py-1 rounded-full text-xs font-bold">ELITE</span>
                        </div>

                        <div class="text-center mb-6 mt-2">
                            <h3 class="text-xl font-bold mb-2" style="color: #ffffff;">Elite</h3>
                            <div class="text-4xl font-bold" style="color: #ffffff;">$79</div>
                            <p class="text-sm" style="color: #e9d5ff;">por mes</p>
                        </div>

                        <ul class="space-y-2 mb-6 text-sm">
                            <li class="flex items-center" style="color: #ffffff;">
                                <svg class="w-4 h-4 mr-2" style="color: #4ade80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span style="color: #ffffff;">Todo lo de Premium PLUS:</span>
                            </li>
                            <li class="flex items-center" style="color: #ffffff;">
                                <svg class="w-4 h-4 mr-2" style="color: #4ade80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span style="color: #ffffff;">App Móvil White Label</span>
                            </li>
                            <li class="flex items-center" style="color: #ffffff;">
                                <svg class="w-4 h-4 mr-2" style="color: #4ade80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span style="color: #ffffff;">Website Builder Completo</span>
                            </li>
                            <li class="flex items-center" style="color: #ffffff;">
                                <svg class="w-4 h-4 mr-2" style="color: #4ade80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span style="color: #ffffff; font-weight: bold;">Posición #1 Garantizada</span>
                            </li>
                            <li class="flex items-center" style="color: #ffffff;">
                                <svg class="w-4 h-4 mr-2" style="color: #4ade80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span style="color: #ffffff;">Account Manager Dedicado</span>
                            </li>
                            <li class="flex items-center" style="color: #ffffff;">
                                <svg class="w-4 h-4 mr-2" style="color: #4ade80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span style="color: #ffffff;">Fotografía Profesional trimestral</span>
                            </li>
                            <li class="flex items-center" style="color: #ffffff;">
                                <svg class="w-4 h-4 mr-2" style="color: #4ade80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span style="color: #ffffff;">Cobertura de Medios y PR</span>
                            </li>
                        </ul>

                        <button wire:click="selectPlan('elite')" class="w-full bg-yellow-500 text-gray-900 px-6 py-3 rounded-lg font-bold hover:bg-yellow-400 transition-colors">
                            Comenzar Elite
                        </button>
                        <p class="text-xs text-center mt-2" style="color: #e9d5ff;">Soporte premium incluido</p>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <button wire:click="backToSearch" class="text-gray-600 hover:text-gray-900 text-sm">
                        ← Buscar otro restaurante
                    </button>
                </div>
            </div>
        @endif

        @if($step === 'payment')
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <button
                    wire:click="backToSelectPlan"
                    class="mb-6 text-red-600 hover:text-red-700 font-medium flex items-center"
                >
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </button>

                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('app.claim_payment_title') }}</h1>
                    <p class="text-gray-600">{{ __('app.claim_payment_subtitle') }}</p>
                </div>

                {{-- Order Summary --}}
                <div class="max-w-2xl mx-auto">
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('app.claim_order_summary') }}</h3>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('app.claim_restaurant_label') }}</span>
                                <span class="font-medium text-gray-900">{{ $selectedRestaurant->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('app.claim_plan_label') }}</span>
                                <span class="font-medium text-gray-900">{{ ucfirst($selectedPlan) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('app.claim_price_label') }}</span>
                                <span class="font-medium text-gray-900">
                                    @if($selectedPlan === 'premium')
                                        $9.99 primer mes, después $39{{ __('app.claim_plan_month') }}
                                    @elseif($selectedPlan === 'elite')
                                        $79{{ __('app.claim_plan_month') }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between text-lg font-bold">
                                <span>{{ __('app.claim_monthly_total') }}</span>
                                <span>
                                    @if($selectedPlan === 'premium')
                                        $9.99 <span class='text-sm font-normal text-gray-500'>(primer mes)</span>
                                    @elseif($selectedPlan === 'elite')
                                        $79
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Coupon Code Section --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-6" x-data="{ showCoupon: false }">
                        <button type="button" @click="showCoupon = !showCoupon" class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-red-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            {{ __('app.claim_coupon_question') }}
                            <svg class="w-4 h-4 transition-transform" :class="showCoupon ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="showCoupon" x-collapse x-cloak class="mt-4">
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    wire:model="couponCode"
                                    placeholder="{{ __('app.claim_coupon_placeholder') }}"
                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent uppercase"
                                >
                                <button
                                    wire:click="applyCoupon"
                                    class="px-6 py-3 bg-gray-700 hover:bg-gray-800 text-white rounded-lg font-medium transition-colors"
                                >
                                    {{ __('app.claim_coupon_apply') }}
                                </button>
                            </div>

                            @if($couponMessage)
                                <div class="mt-3 p-3 rounded-lg {{ $couponApplied ? 'bg-green-50 border border-green-200' : 'bg-danger-50 border border-danger-200' }}">
                                    <p class="text-sm {{ $couponApplied ? 'text-green-800' : 'text-danger-800' }} flex items-center gap-2">
                                        @if($couponApplied)
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                        {{ $couponMessage }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Stripe Payment Button --}}
                    <div class="bg-white border border-gray-200 rounded-lg p-8">
                        <div class="text-center mb-6">
                            <svg class="w-20 h-20 mx-auto text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('app.claim_payment_stripe_title') }}</h3>
                            <p class="text-gray-600">{{ __('app.claim_payment_redirect_text') }}</p>
                        </div>

                        <button
                            wire:click="processPayment"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            {{ __('app.claim_payment_button') }}
                        </button>

                        <div class="mt-6 flex items-center justify-center gap-4">
                            <svg class="h-8" viewBox="0 0 468 222.5" xmlns="http://www.w3.org/2000/svg"><path fill="#635BFF" fill-rule="evenodd" d="M414 113.4c0-25.6-12.4-45.8-36.1-45.8-23.8 0-38.2 20.2-38.2 45.6 0 30.1 17 45.3 41.4 45.3 11.9 0 20.9-2.7 27.7-6.5v-20c-6.8 3.4-14.6 5.5-24.5 5.5-9.7 0-18.3-3.4-19.4-15.2h48.9c0-1.3.2-6.5.2-8.9zm-49.4-9.5c0-11.3 6.9-16 13.2-16 6.1 0 12.6 4.7 12.6 16h-25.8zm-63.5-36.3c-9.8 0-16.1 4.6-19.6 7.8l-1.3-6.2h-22v116.6l25-5.3.1-28.3c3.6 2.6 8.9 6.3 17.7 6.3 17.9 0 34.2-14.4 34.2-46.1-.1-29-16.6-44.8-34.1-44.8zm-6 68.9c-5.9 0-9.4-2.1-11.8-4.7l-.1-37.1c2.6-2.9 6.2-4.9 11.9-4.9 9.1 0 15.4 10.2 15.4 23.3 0 13.4-6.2 23.4-15.4 23.4zm-71.3-74.8l25.1-5.4V36l-25.1 5.3v20.4zm0 7.6h25.1v87.5h-25.1v-87.5zm-26.7 7.4l-1.6-7.4h-21.6v87.5h25V97.5c5.9-7.7 15.9-6.3 19-5.2v-23c-3.2-1.2-14.9-3.4-20.8 7.4zm-48.1-39.9l-24.4 5.2-.1 80.1c0 14.8 11.1 25.7 25.9 25.7 8.2 0 14.2-1.5 17.5-3.3V135c-3.2 1.3-19-2.6-19-17.6V89h19V69.3h-19l.1-32.5zm-70.8 66.5c0-3.9 3.2-5.4 8.5-5.4 7.6 0 17.2 2.3 24.8 6.4V72.2c-8.3-3.3-16.5-4.6-24.8-4.6C58.5 67.6 41 81.8 41 103.8c0 34.2 47.1 28.7 47.1 43.4 0 4.6-4 6.1-9.6 6.1-8.3 0-18.9-3.4-27.3-8v23.8c9.3 4 18.7 5.7 27.3 5.7 23.8 0 40.2-11.8 40.2-34.2-.1-36.9-47.4-30.3-47.4-44.1z"/></svg>
                        </div>
                    </div>

                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('app.claim_payment_secure_notice') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- Support Link --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 text-center">
        <a href="mailto:owners@restaurantesmexicanosfamosos.com?subject={{ urlencode('Problema al reclamar restaurante') }}&body={{ urlencode('Hola, tengo problemas para reclamar mi restaurante. Necesito ayuda con lo siguiente:') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-red-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            ¿Problemas para reclamar? Contáctenos
        </a>
    </div>

<script>
    document.addEventListener("livewire:updated", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
    document.addEventListener("livewire:morph", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => {
            setTimeout(() => window.scrollTo({ top: 0, behavior: "smooth" }), 100);
        });
    });
</script>
</div>