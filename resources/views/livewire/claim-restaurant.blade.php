<div class="min-h-screen py-12" style="background:#0B0B0B;" x-data x-init="$watch('$wire.step', () => setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 50))">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Progress Steps --}}
        <div class="mb-8">
            <div class="flex items-center justify-center space-x-4">
                {{-- Step 1: Search --}}
                <div class="flex items-center {{ $step !== 'search' ? 'cursor-pointer' : '' }}" @if($step !== 'search') wire:click="backToSearch" @endif>
                    <div class="flex items-center justify-center w-10 h-10 rounded-full transition-colors" style="{{ $step === 'search' ? 'background:#D4AF37;color:#0B0B0B;' : ($step === 'verify' || $step === 'verify_code' || $step === 'select_role' || $step === 'create_account' || $step === 'select_plan' || $step === 'payment' ? 'background:#4ADE80;color:#0B0B0B;' : 'background:#2A2A2A;color:#9CA3AF;') }}">
                        @if($step === 'verify' || $step === 'verify_code' || $step === 'select_role' || $step === 'create_account' || $step === 'select_plan' || $step === 'payment')
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            1
                        @endif
                    </div>
                    <span class="ml-2 text-sm font-medium" style="{{ $step === 'search' ? 'color:#D4AF37;' : 'color:#9CA3AF;' }}">{{ __('app.search') }}</span>
                </div>

                <div class="w-16 h-1" style="{{ $step === 'verify' || $step === 'verify_code' || $step === 'select_role' || $step === 'create_account' || $step === 'select_plan' || $step === 'payment' ? 'background:#4ADE80;' : 'background:#2A2A2A;' }}"></div>

                {{-- Step 2: Verify --}}
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full text-white transition-colors" style="{{ $step === 'verify' || $step === 'verify_code' || $step === 'select_role' || $step === 'create_account' ? 'background:#DC2626;' : ($step === 'select_plan' || $step === 'payment' ? 'background:#4ADE80;' : 'background:#2A2A2A;') }}">
                        @if($step === 'select_plan' || $step === 'payment')
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            2
                        @endif
                    </div>
                    <span class="ml-2 text-sm font-medium" style="{{ $step === 'verify' || $step === 'verify_code' || $step === 'select_role' || $step === 'create_account' ? 'color:#D4AF37;' : 'color:#9CA3AF;' }}">{{ __('app.claim_step_verify') }}</span>
                </div>

                <div class="w-16 h-1" style="{{ $step === 'select_plan' || $step === 'payment' ? 'background:#4ADE80;' : 'background:#2A2A2A;' }}"></div>

                {{-- Step 3: Select Plan --}}
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full text-white transition-colors" style="{{ $step === 'select_plan' ? 'background:#DC2626;' : ($step === 'payment' ? 'background:#4ADE80;' : 'background:#2A2A2A;') }}">
                        @if($step === 'payment')
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            3
                        @endif
                    </div>
                    <span class="ml-2 text-sm font-medium" style="{{ $step === 'select_plan' ? 'color:#D4AF37;' : 'color:#9CA3AF;' }}">{{ __('app.claim_step_plan') }}</span>
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
            <div class="rounded-2xl p-8 text-center max-w-2xl mx-auto" style="background:#1A1A1A; border:1px solid #2A2A2A; box-shadow:0 4px 20px rgba(0,0,0,0.4);">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6" style="background:rgba(74,222,128,0.1);">
                    <svg class="w-10 h-10" style="color:#4ADE80;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold mb-4" style="color:#F5F5F5; font-family:'Playfair Display',serif;">¡Felicidades!</h1>
                <p class="text-xl mb-6" style="color:#9CA3AF;">Tu restaurante ha sido reclamado exitosamente.</p>
                <div class="rounded-lg p-4 mb-6" style="background:rgba(74,222,128,0.1); border:1px solid rgba(74,222,128,0.3);">
                    <p style="color:#4ADE80;">
                        <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Ahora puedes acceder a tu Dashboard para administrar tu restaurante.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/owner/dashboard" class="px-8 py-3 rounded-lg font-semibold transition-colors inline-flex items-center justify-center" style="background:#DC2626; color:#F5F5F5;">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        Ir al Dashboard
                    </a>
                    <a href="/" class="px-8 py-3 rounded-lg font-semibold transition-colors inline-flex items-center justify-center" style="background:#1A1A1A; border:1px solid #2A2A2A; color:#9CA3AF;">
                        Volver al Inicio
                    </a>
                </div>
            </div>
        @elseif (session()->has('success'))
            <div class="mb-6 px-4 py-3 rounded-lg" style="background:rgba(74,222,128,0.1); border:1px solid rgba(74,222,128,0.3); color:#4ADE80;">
                {{ session('success') }}
            </div>
        @endif

        {{-- STEP 1: SEARCH --}}
        @if($step === 'search')
            <div class="rounded-2xl p-8" style="background:#1A1A1A; border:1px solid #2A2A2A; box-shadow:0 4px 20px rgba(0,0,0,0.4);">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold mb-2" style="color:#F5F5F5; font-family:'Playfair Display',serif;">{{ __('app.claim_title') }}</h1>
                    <p style="color:#9CA3AF;">{{ __('app.claim_search_subtitle') }}</p>
                </div>

                <form wire:submit.prevent="searchRestaurants" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2" style="color:#CCCCCC;">
                                {{ __('app.claim_search_label') }}
                            </label>
                            <input
                                type="text"
                                wire:model="search"
                                class="w-full px-4 py-3 rounded-lg focus:outline-none"
                                style="background:#111111; border:1px solid #2A2A2A; color:#F5F5F5;"
                                onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'"
                                placeholder="{{ __('app.claim_search_placeholder') }}"
                            >
                            @error('search')
                                <span class="text-danger-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color:#CCCCCC;">
                                {{ __('app.claim_state_label') }}
                            </label>
                            <select
                                wire:model="selectedState"
                                class="w-full px-4 py-3 rounded-lg focus:outline-none"
                                style="background:#111111; border:1px solid #2A2A2A; color:#F5F5F5;"
                                onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'"
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
                        class="w-full px-8 py-4 rounded-lg font-semibold transition-colors" style="background:#D4AF37;color:#0B0B0B;" onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'"
                    >
                        {{ __('app.claim_search_button') }}
                    </button>
                </form>

                {{-- Search Results --}}
                @if($searchResults->isNotEmpty())
                    <div class="mt-8">
                        <h3 class="text-xl font-semibold mb-4" style="color:#F5F5F5;">
                            {{ __('app.claim_results_found') }} ({{ $searchResults->count() }})
                        </h3>
                        <div class="space-y-4">
                            @foreach($searchResults as $restaurant)
                                <div class="rounded-lg p-6 transition-colors" style="border:1px solid #2A2A2A; background:#111111;" onmouseover="this.style.borderColor='rgba(212,175,55,0.4)'" onmouseout="this.style.borderColor='#2A2A2A'">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold" style="color:#F5F5F5;">{{ $restaurant->name }}</h4>
                                            <p class="mt-1" style="color:#9CA3AF;">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ $restaurant->address }}, {{ $restaurant->city }}, {{ $restaurant->state->name }}
                                            </p>
                                            @if($restaurant->phone)
                                                <p class="mt-1" style="color:#9CA3AF;">
                                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                    </svg>
                                                    {{ $restaurant->phone }}
                                                </p>
                                            @endif
                                            @if($restaurant->category)
                                                <span class="inline-block mt-2 px-3 py-1 text-sm rounded-full" style="background:rgba(212,175,55,0.15); color:#D4AF37;">
                                                    {{ $restaurant->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                        <button
                                            wire:click="selectRestaurant({{ $restaurant->id }})"
                                            class="ml-4 px-6 py-2 rounded-lg font-semibold transition-colors whitespace-nowrap" style="background:#D4AF37;color:#0B0B0B;" onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'"
                                        >
                                            Claim
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Always-visible Add Restaurant Link --}}
                        <div class="mt-6 text-center p-4 rounded-lg" style="background:#111111; border:1px solid #2A2A2A;">
                            <p class="text-sm" style="color:#CCCCCC;">
                                {{ __('app.claim_cant_find_restaurant') }}
                                <a href="{{ route('suggestions.create') }}" class="font-semibold underline ml-1" style="color:#D4AF37;">
                                    {{ __('app.claim_add_it_here') }}
                                </a>
                            </p>
                        </div>
                    </div>
                @elseif($search)
                    <div class="mt-8 text-center p-8 rounded-lg" style="background:#111111; border:1px solid #2A2A2A;">
                        <svg class="w-16 h-16 mx-auto mb-4" style="color:#6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 class="text-lg font-semibold mb-2" style="color:#F5F5F5;">{{ __('app.claim_not_found_title') }}</h3>
                        <p class="mb-4" style="color:#9CA3AF;">{{ __('app.claim_not_found_text') }}</p>
                        <a href="{{ route('suggestions.create') }}" class="inline-block px-6 py-3 rounded-lg font-semibold transition-colors" style="background:#DC2626; color:#F5F5F5;">
                            {{ __('app.claim_register_button') }}
                        </a>
                    </div>
                @endif
            </div>
        @endif

        {{-- STEP 2: VERIFY --}}
        @if($step === 'verify' && $selectedRestaurant)
            <div class="rounded-2xl p-8" style="background:#1A1A1A; border:1px solid #2A2A2A; box-shadow:0 4px 20px rgba(0,0,0,0.4);">
                <button
                    wire:click="backToSearch"
                    class="mb-6 font-medium flex items-center" style="color:#D4AF37;"
                >
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('app.claim_back_to_search') }}
                </button>

                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold mb-2" style="color:#F5F5F5; font-family:'Playfair Display',serif;">{{ __('app.claim_verify_title') }}</h1>
                    <p style="color:#9CA3AF;">{{ __('app.claim_verify_subtitle') }}</p>
                </div>

                {{-- Selected Restaurant Info --}}
                <div class="rounded-lg p-6 mb-8" style="background:#111111; border:1px solid #2A2A2A;">
                    <h3 class="text-xl font-semibold mb-2" style="color:#F5F5F5;">{{ $selectedRestaurant->name }}</h3>
                    <p style="color:#9CA3AF;">
                        {{ $selectedRestaurant->address }}, {{ $selectedRestaurant->city }}, {{ $selectedRestaurant->state->name }}
                    </p>
                </div>

                {{-- Verification Form --}}
                <form wire:submit.prevent="submitVerification" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color:#CCCCCC;">
                            {{ __('app.claim_owner_name') }} *
                        </label>
                        <input
                            type="text"
                            wire:model="ownerName"
                            class="w-full px-4 py-3 rounded-lg focus:outline-none"
                            style="background:#111111; border:1px solid #2A2A2A; color:#F5F5F5;"
                            onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'"
                            placeholder="John Smith"
                        >
                        @error('ownerName')
                            <span class="text-danger-600 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color:#CCCCCC;">
                            {{ __('app.claim_owner_email') }} *
                        </label>
                        <input
                            type="email"
                            wire:model="ownerEmail"
                            class="w-full px-4 py-3 rounded-lg focus:outline-none"
                            style="background:#111111; border:1px solid #2A2A2A; color:#F5F5F5;"
                            onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'"
                            placeholder="john@restaurant.com"
                        >
                        @error('ownerEmail')
                            <span class="text-danger-600 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color:#CCCCCC;">
                            {{ __('app.claim_owner_phone') }} *
                        </label>
                        <input
                            type="tel"
                            wire:model="ownerPhone"
                            class="w-full px-4 py-3 rounded-lg focus:outline-none"
                            style="background:#111111; border:1px solid #2A2A2A; color:#F5F5F5;"
                            onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'"
                            placeholder="(555) 123-4567"
                        >
                        @error('ownerPhone')
                            <span class="text-danger-600 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Verification Method Selection --}}
                    <div>
                        <label class="block text-sm font-medium mb-3" style="color:#CCCCCC;">
                            Método de verificación
                        </label>

                        @if(count($availableMethods) === 0)
                            {{-- No methods available --}}
                            <div class="bg-danger-50 border border-danger-200 rounded-lg p-4">
                                <p class="text-sm text-danger-800">
                                    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Este restaurante no tiene correo electrónico ni teléfono registrado. Por favor contacta a soporte.
                                </p>
                            </div>
                        @elseif(count($availableMethods) === 1)
                            {{-- Only one method available - show info --}}
                            @if($availableMethods[0] === 'email')
                                <div class="rounded-lg p-4 flex items-start" style="background:rgba(212,175,55,0.08); border:1px solid rgba(212,175,55,0.2);">
                                    <svg class="w-6 h-6 mr-3 mt-0.5 flex-shrink-0" style="color:#D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium" style="color:#F5F5F5;">Verificación por correo electrónico</p>
                                        <p class="text-xs mt-1" style="color:#9CA3AF;">Se enviará un código de 6 dígitos al correo registrado del restaurante.</p>
                                    </div>
                                </div>
                            @else
                                <div class="rounded-lg p-4 flex items-start" style="background:rgba(212,175,55,0.08); border:1px solid rgba(212,175,55,0.2);">
                                    <svg class="w-6 h-6 mr-3 mt-0.5 flex-shrink-0" style="color:#D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium" style="color:#F5F5F5;">Verificación por llamada telefónica</p>
                                        <p class="text-xs mt-1" style="color:#9CA3AF;">Recibirás una llamada al teléfono del restaurante con un código de 6 dígitos.</p>
                                    </div>
                                </div>
                            @endif
                        @else
                            {{-- Both methods available - clickable card selector --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                {{-- Email option --}}
                                <div
                                    wire:click="$set('verificationMethod', 'email')"
                                    class="rounded-lg p-4 transition-all cursor-pointer"
                                    style="{{ $verificationMethod === 'email' ? 'border:2px solid #D4AF37; background:rgba(212,175,55,0.08);' : 'border:2px solid #2A2A2A; background:#111111;' }}"
                                >
                                    <div class="flex items-start">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 mr-3" style="{{ $verificationMethod === 'email' ? 'background:rgba(212,175,55,0.15);' : 'background:#2A2A2A;' }}">
                                            <svg class="w-5 h-5" style="{{ $verificationMethod === 'email' ? 'color:#D4AF37;' : 'color:#9CA3AF;' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold" style="color:#F5F5F5;">Correo electrónico</p>
                                            <p class="text-xs mt-1" style="color:#9CA3AF;">Código enviado al email del restaurante</p>
                                        </div>
                                        @if($verificationMethod === 'email')
                                            <svg class="w-5 h-5 ml-auto flex-shrink-0" style="color:#D4AF37;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>

                                {{-- Phone option --}}
                                <div
                                    wire:click="$set('verificationMethod', 'phone')"
                                    class="rounded-lg p-4 transition-all cursor-pointer"
                                    style="{{ $verificationMethod === 'phone' ? 'border:2px solid #D4AF37; background:rgba(212,175,55,0.08);' : 'border:2px solid #2A2A2A; background:#111111;' }}"
                                >
                                    <div class="flex items-start">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 mr-3" style="{{ $verificationMethod === 'phone' ? 'background:rgba(212,175,55,0.15);' : 'background:#2A2A2A;' }}">
                                            <svg class="w-5 h-5" style="{{ $verificationMethod === 'phone' ? 'color:#D4AF37;' : 'color:#9CA3AF;' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold" style="color:#F5F5F5;">Llamada telefónica</p>
                                            <p class="text-xs mt-1" style="color:#9CA3AF;">Llamada con código al teléfono del restaurante</p>
                                        </div>
                                        @if($verificationMethod === 'phone')
                                            <svg class="w-5 h-5 ml-auto flex-shrink-0" style="color:#D4AF37;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="rounded-lg p-4" style="background:rgba(212,175,55,0.08); border:1px solid rgba(212,175,55,0.2);">
                        <p class="text-sm" style="color:#D4AF37;">
                            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            El código de verificación se enviará a los datos de contacto registrados del restaurante para confirmar que eres el propietario.
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="w-full px-8 py-4 rounded-lg font-semibold transition-colors" style="background:#D4AF37;color:#0B0B0B;" onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'"
                    >
                        {{ __('app.claim_continue_plan') }}
                    </button>
                </form>
            </div>
        @endif


        {{-- STEP 2.5: VERIFY CODE --}}
        @if($step === 'verify_code' && $selectedRestaurant)
            <div class="rounded-2xl p-8" style="background:#1A1A1A; border:1px solid #2A2A2A; box-shadow:0 4px 20px rgba(0,0,0,0.4);">
                <button
                    wire:click="backToVerify"
                    class="mb-6 font-medium flex items-center" style="color:#D4AF37;"
                >
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </button>

                <div class="text-center mb-8">
                    @if($verificationMethod === 'phone')
                        {{-- Phone call verification header --}}
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background:rgba(74,222,128,0.1);">
                            <svg class="w-8 h-8" style="color:#4ADE80;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold mb-2" style="color:#F5F5F5; font-family:'Playfair Display',serif;">Verifica tu identidad</h1>
                        <p style="color:#9CA3AF;">Estamos llamando al teléfono del restaurante con tu código de verificación:</p>
                        @php
                            $rPhone = $selectedRestaurant->phone ?? '';
                            $rPhoneCleaned = preg_replace('/[^0-9]/', '', $rPhone);
                            $rPhoneLast4 = substr($rPhoneCleaned, -4);
                            $rPhoneMasked = '(***) ***-' . $rPhoneLast4;
                        @endphp
                        <p class="font-semibold mt-1" style="color:#F5F5F5;">{{ $rPhoneMasked }}</p>
                        <div class="mt-3 inline-block rounded-lg p-3" style="background:rgba(74,222,128,0.1); border:1px solid rgba(74,222,128,0.3);">
                            <p class="text-sm flex items-center justify-center" style="color:#4ADE80;">
                                <svg class="w-4 h-4 mr-2 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                Contesta la llamada para escuchar el codigo
                            </p>
                        </div>
                    @else
                        {{-- Email verification header --}}
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background:rgba(220,38,38,0.15);">
                            <svg class="w-8 h-8" style="color:#DC2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold mb-2" style="color:#F5F5F5; font-family:'Playfair Display',serif;">Verifica tu identidad</h1>
                        <p style="color:#9CA3AF;">Hemos enviado un codigo de 6 digitos al correo registrado del restaurante:</p>
                        @php
                            $rEmail = $selectedRestaurant->email ?? $ownerEmail;
                            $rParts = explode('@', $rEmail);
                            $rMasked = substr($rParts[0], 0, 2) . str_repeat('*', max(3, strlen($rParts[0]) - 2)) . '@' . ($rParts[1] ?? '');
                        @endphp
                        <p class="font-semibold mt-1" style="color:#F5F5F5;">{{ $rMasked }}</p>
                        <p class="text-sm mt-2" style="color:#9CA3AF;">Si no tienes acceso a este correo, contacta a soporte.</p>
                    @endif
                </div>

                @if (session()->has('success'))
                    <div class="mb-6 px-4 py-3 rounded-lg" style="background:rgba(74,222,128,0.1); border:1px solid rgba(74,222,128,0.3); color:#4ADE80;">
                        {{ session('success') }}
                    </div>
                @endif

                <form wire:submit.prevent="verifyCode" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-center" style="color:#CCCCCC;">
                            Ingresa el codigo de verificacion
                        </label>
                        <input
                            type="text"
                            wire:model="verificationCode"
                            class="w-full px-4 py-4 rounded-lg focus:outline-none text-center text-2xl tracking-widest font-mono"
                            style="background:#111111; border:1px solid #2A2A2A; color:#F5F5F5;"
                            onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'"
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
                        class="w-full px-8 py-4 rounded-lg font-semibold transition-colors" style="background:#D4AF37;color:#0B0B0B;" onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'"
                    >
                        Verificar codigo
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm" style="color:#9CA3AF;">
                        @if($verificationMethod === 'phone')
                            No recibiste la llamada?
                            <button wire:click="resendCode" class="font-medium ml-1" style="color:#D4AF37;">
                                Volver a llamar
                            </button>
                        @else
                            No recibiste el codigo?
                            <button wire:click="resendCode" class="font-medium ml-1" style="color:#D4AF37;">
                                Reenviar codigo
                            </button>
                        @endif
                    </p>
                    <p class="text-xs mt-2" style="color:#6B7280;">El codigo expira en 15 minutos</p>
                </div>
            </div>
        @endif

        {{-- STEP 2.75: SELECT ROLE --}}
        @if($step === 'select_role')
            <div class="rounded-2xl p-8" style="background:#1A1A1A; border:1px solid #2A2A2A; box-shadow:0 4px 20px rgba(0,0,0,0.4);">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <h2 style="font-size: 1.5rem; font-weight: bold; color: #F5F5F5; font-family: 'Playfair Display', serif; margin-bottom: 0.5rem;">¿Cuál es tu relación con el restaurante?</h2>
                    <p style="color: #9CA3AF; font-size: 0.95rem;">Esto nos ayuda a personalizar tu experiencia</p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; max-width: 480px; margin: 0 auto 2rem auto;">
                    @foreach([
                        ['role' => 'owner', 'label' => 'Soy el Dueño', 'desc' => 'Tengo el control total del negocio', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                        ['role' => 'partner', 'label' => 'Soy Socio', 'desc' => 'Tengo participación en el negocio', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['role' => 'manager', 'label' => 'Soy Gerente', 'desc' => 'Administro las operaciones del lugar', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        ['role' => 'other', 'label' => 'Otro', 'desc' => 'Familiar, representante u otro', 'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ] as $option)
                    <button wire:click="selectRole('{{ $option['role'] }}')"
                        style="display: flex; flex-direction: column; align-items: flex-start; gap: 0.5rem; background: #1A1A1A; border: 1px solid #2A2A2A; border-radius: 0.75rem; padding: 1.25rem; cursor: pointer; text-align: left; transition: all 0.2s; width: 100%;"
                        onmouseover="this.style.borderColor='#D4AF37'"
                        onmouseout="this.style.borderColor='#2A2A2A'">
                        <div style="background: #2A2A2A; width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $option['icon'] }}"/>
                            </svg>
                        </div>
                        <span style="font-weight: 600; color: #F5F5F5; font-size: 0.95rem;">{{ $option['label'] }}</span>
                        <span style="font-size: 0.8rem; color: #6B7280; line-height: 1.4;">{{ $option['desc'] }}</span>
                    </button>
                    @endforeach
                </div>

                <div style="text-align: center;">
                    <button wire:click="$set('step', 'select_plan')" style="color: #6B7280; font-size: 0.875rem; background: none; border: none; cursor: pointer; text-decoration: underline;">
                        Omitir este paso
                    </button>
                </div>
            </div>
        @endif

        {{-- STEP 2.9: CREATE ACCOUNT --}}
        @if ($step === 'create_account')
        <div style="max-width:480px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:2rem;">
                <div style="width:4rem;height:4rem;background:linear-gradient(135deg,#D4AF37,#F4E4A6);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <svg style="width:2rem;height:2rem;color:#0B0B0B;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <h2 style="font-size:1.75rem;font-weight:bold;color:#F5F5F5;font-family:'Playfair Display',serif;margin-bottom:0.5rem;">Crea tu cuenta</h2>
                <p style="color:#9CA3AF;font-size:0.95rem;">Ya verificamos que el restaurante es tuyo. Ahora crea tu acceso.</p>
            </div>

            <div style="background:#111111;border:1px solid #2A2A2A;border-radius:0.75rem;padding:1.5rem;display:flex;flex-direction:column;gap:1rem;">
                {{-- Password --}}
                <div>
                    <label style="display:block;font-size:0.875rem;font-weight:500;color:#D4AF37;margin-bottom:0.5rem;">Contraseña</label>
                    <input type="password" wire:model="password" placeholder="Mínimo 8 caracteres"
                        style="width:100%;background:#1A1A1A;border:1px solid #2A2A2A;border-radius:0.5rem;padding:0.75rem 1rem;color:#F5F5F5;font-size:0.95rem;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'"/>
                    @error('password')<p style="color:#EF4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>
                {{-- Confirm Password --}}
                <div>
                    <label style="display:block;font-size:0.875rem;font-weight:500;color:#D4AF37;margin-bottom:0.5rem;">Confirmar contraseña</label>
                    <input type="password" wire:model="passwordConfirmation" placeholder="Repite tu contraseña"
                        style="width:100%;background:#1A1A1A;border:1px solid #2A2A2A;border-radius:0.5rem;padding:0.75rem 1rem;color:#F5F5F5;font-size:0.95rem;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'"/>
                    @error('passwordConfirmation')<p style="color:#EF4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>

                {{-- Divider --}}
                <hr style="border:none;border-top:1px solid #2A2A2A;margin:0.5rem 0;"/>

                {{-- Email consent --}}
                <label style="display:flex;align-items:flex-start;gap:0.75rem;cursor:pointer;">
                    <input type="checkbox" wire:model="emailConsent"
                        style="width:1.125rem;height:1.125rem;accent-color:#D4AF37;margin-top:0.125rem;flex-shrink:0;cursor:pointer;"/>
                    <span style="font-size:0.875rem;color:#CCCCCC;line-height:1.5;">
                        <strong style="color:#F5F5F5;">Comunicaciones por email</strong><br/>
                        <span style="color:#6B7280;font-size:0.8rem;">Recibe actualizaciones, reportes de desempeño y tips para mejorar tu perfil. Puedes cancelar en cualquier momento.</span>
                    </span>
                </label>

                {{-- SMS consent --}}
                <label style="display:flex;align-items:flex-start;gap:0.75rem;cursor:pointer;">
                    <input type="checkbox" wire:model="smsConsent"
                        style="width:1.125rem;height:1.125rem;accent-color:#D4AF37;margin-top:0.125rem;flex-shrink:0;cursor:pointer;"/>
                    <span style="font-size:0.875rem;color:#CCCCCC;line-height:1.5;">
                        <strong style="color:#F5F5F5;">Notificaciones por SMS</strong><br/>
                        <span style="color:#6B7280;font-size:0.8rem;">Alertas importantes sobre nuevas reseñas y actividad de tu restaurante.</span>
                    </span>
                </label>

                {{-- Submit --}}
                <button wire:click="submitCreateAccount" wire:loading.attr="disabled"
                    style="width:100%;padding:0.875rem;background:linear-gradient(135deg,#D4AF37,#F4E4A6);color:#0B0B0B;font-weight:700;font-size:1rem;border:none;border-radius:0.5rem;cursor:pointer;margin-top:0.5rem;font-family:'Poppins',sans-serif;">
                    <span wire:loading.remove wire:target="submitCreateAccount">Crear cuenta y continuar →</span>
                    <span wire:loading wire:target="submitCreateAccount">Creando cuenta...</span>
                </button>
            </div>
        </div>
        @endif

        {{-- STEP 3: SELECT PLAN --}}
        @if($step === 'select_plan')
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.85);backdrop-filter:blur(4px);z-index:50;display:flex;align-items:center;justify-content:center;padding:1rem;overflow-y:auto;">
            <div style="width:100%;max-width:900px;background:#0B0B0B;border:1px solid #2A2A2A;border-radius:1rem;padding:2rem;position:relative;">

                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold mb-2" style="color:#F5F5F5; font-family:'Playfair Display',serif;">Selecciona tu Plan</h1>
                    <p style="color:#9CA3AF;">Elige el plan que mejor se adapte a tu restaurante</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- FREE PLAN --}}
                    <div class="rounded-xl p-6 transition-colors" style="{{ $selectedPlan === 'free' ? 'border:2px solid #D4AF37; background:#111111;' : 'border:2px solid #2A2A2A; background:#111111;' }}">
                        <div class="text-center mb-6">
                            <h3 class="text-xl font-bold mb-2" style="color:#F5F5F5; font-family:'Playfair Display',serif;">Listado Gratis</h3>
                            <div class="text-4xl font-bold mb-1" style="color:#F5F5F5;">
                                $0
                            </div>
                            <p class="text-sm" style="color:#9CA3AF;">Listado básico</p>
                        </div>

                        <ul class="space-y-3 mb-6">
                            <li class="flex items-center text-sm" style="color:#CCCCCC;">
                                <svg class="w-5 h-5 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Aparece en el directorio
                            </li>
                            <li class="flex items-center text-sm" style="color:#CCCCCC;">
                                <svg class="w-5 h-5 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Info básica (nombre, dirección, teléfono)
                            </li>
                            <li class="flex items-center text-sm" style="color:#CCCCCC;">
                                <svg class="w-5 h-5 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Integración con Google Maps
                            </li>
                            <li class="flex items-center text-sm" style="color:#CCCCCC;">
                                <svg class="w-5 h-5 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Verificar propiedad del restaurante
                            </li>
                            <li class="flex items-center text-sm" style="color:#6B7280;">
                                <svg class="w-5 h-5 mr-2" style="color:#3A3A3A;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                Sin prioridad en búsquedas
                            </li>
                        </ul>

                        <button wire:click="selectPlan('free')" class="w-full px-6 py-3 rounded-lg font-semibold transition-colors" style="background:#2A2A2A; color:#F5F5F5;">
                            Reclamar Gratis
                        </button>
                    </div>

                    {{-- PREMIUM PLAN --}}
                    <div class="rounded-xl p-6 relative" style="border:2px solid #D4AF37; background:linear-gradient(to bottom, rgba(212,175,55,0.08), #111111);">
                        <div class="absolute -top-3 left-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold" style="background:#DC2626; color:#F5F5F5;">MÁS POPULAR</span>
                        </div>
                        <div class="absolute -top-3 right-4">
                            <span class="px-2 py-1 rounded text-xs font-bold" style="background:#D4AF37; color:#0B0B0B;">OFERTA PRIMER MES</span>
                        </div>

                        <div class="text-center mb-6 mt-2">
                            <h3 class="text-xl font-bold mb-2" style="color:#F5F5F5; font-family:'Playfair Display',serif;">Premium</h3>
                            <div class="flex items-center justify-center gap-2">
                                <span class="text-lg line-through" style="color:#6B7280;">$39</span>
                                <span class="text-4xl font-bold" style="color:#D4AF37;">$9.99</span>
                            </div>
                            <p class="text-sm font-semibold" style="color:#D4AF37;">primer mes</p>
                            <p class="text-xs" style="color:#9CA3AF;">Después $39/mes</p>
                        </div>

                        <ul class="space-y-2 mb-6 text-sm">
                            <li class="flex items-center" style="color:#CCCCCC;">
                                <svg class="w-4 h-4 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Todo lo de Free PLUS:
                            </li>
                            <li class="flex items-center" style="color:#CCCCCC;">
                                <svg class="w-4 h-4 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Badge Destacado
                            </li>
                            <li class="flex items-center" style="color:#CCCCCC;">
                                <svg class="w-4 h-4 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <strong style="color:#F5F5F5;">Top 3 en búsquedas</strong>&nbsp;locales
                            </li>
                            <li class="flex items-center" style="color:#CCCCCC;">
                                <svg class="w-4 h-4 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Menú Digital + QR Code
                            </li>
                            <li class="flex items-center" style="color:#CCCCCC;">
                                <svg class="w-4 h-4 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Sistema de Reservaciones
                            </li>
                            <li class="flex items-center" style="color:#CCCCCC;">
                                <svg class="w-4 h-4 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Dashboard de Analíticas
                            </li>
                            <li class="flex items-center" style="color:#CCCCCC;">
                                <svg class="w-4 h-4 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Chatbot AI (ES/EN) 24/7
                            </li>
                        </ul>

                        <button wire:click="selectPlan('premium')" class="w-full px-6 py-3 rounded-lg font-semibold transition-colors" style="background:#D4AF37; color:#0B0B0B;">
                            Suscribirse por $9.99
                        </button>
                        <p class="text-xs text-center mt-2" style="color:#9CA3AF;">Cancela cuando quieras</p>
                    </div>

                    {{-- ELITE PLAN --}}
                    <div class="rounded-xl p-6 relative" style="border:2px solid rgba(212,175,55,0.4); background:linear-gradient(to bottom, rgba(212,175,55,0.05), #111111);">
                        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                            <span class="px-3 py-1 rounded-full text-xs font-bold" style="background:#1A1A1A; border:1px solid #D4AF37; color:#D4AF37;">ELITE</span>
                        </div>

                        <div class="text-center mb-6 mt-2">
                            <h3 class="text-xl font-bold mb-2" style="color:#F5F5F5; font-family:'Playfair Display',serif;">Elite</h3>
                            <div class="flex items-center justify-center gap-2">
                                <span class="text-lg line-through" style="color:#6B7280;">$79</span>
                                <span class="text-4xl font-bold" style="color:#D4AF37;">$29</span>
                            </div>
                            <p class="text-sm font-semibold" style="color:#D4AF37;">$29 primer mes</p>
                            <p class="text-xs" style="color:#9CA3AF;">Después $79/mes</p>
                        </div>

                        <ul class="space-y-2 mb-6 text-sm">
                            <li class="flex items-center" style="color:#CCCCCC;">
                                <svg class="w-4 h-4 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span style="color:#F5F5F5;">Todo lo de Premium PLUS:</span>
                            </li>
                            <li class="flex items-center" style="color:#CCCCCC;">
                                <svg class="w-4 h-4 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span style="color:#CCCCCC;">App Móvil White Label</span>
                            </li>
                            <li class="flex items-center" style="color:#CCCCCC;">
                                <svg class="w-4 h-4 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span style="color:#CCCCCC;">Website Builder Completo</span>
                            </li>
                            <li class="flex items-center" style="color:#CCCCCC;">
                                <svg class="w-4 h-4 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <strong style="color:#F5F5F5;">Posición #1 Garantizada</strong>
                            </li>
                            <li class="flex items-center" style="color:#CCCCCC;">
                                <svg class="w-4 h-4 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span style="color:#CCCCCC;">Account Manager Dedicado</span>
                            </li>
                            <li class="flex items-center" style="color:#CCCCCC;">
                                <svg class="w-4 h-4 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span style="color:#CCCCCC;">Fotografía Profesional trimestral</span>
                            </li>
                            <li class="flex items-center" style="color:#CCCCCC;">
                                <svg class="w-4 h-4 mr-2" style="color:#4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span style="color:#CCCCCC;">Cobertura de Medios y PR</span>
                            </li>
                        </ul>

                        <button wire:click="selectPlan('elite')" class="w-full px-6 py-3 rounded-lg font-bold transition-colors" style="background:linear-gradient(135deg,#D4AF37,#F4E4A6); color:#0B0B0B;">
                            Comenzar Elite
                        </button>
                        <p class="text-xs text-center mt-2" style="color:#9CA3AF;">Soporte premium incluido</p>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <button wire:click="backToSearch" class="text-sm transition-colors" style="color:#6B7280;background:none;border:none;cursor:pointer;text-decoration:underline;">
                        ← Buscar otro restaurante
                    </button>
                </div>
            </div>
        </div>
        @endif

        @if($step === 'payment')
            <div class="rounded-2xl p-8" style="background:#1A1A1A; border:1px solid #2A2A2A; box-shadow:0 4px 20px rgba(0,0,0,0.4);">
                <button
                    wire:click="backToSelectPlan"
                    class="mb-6 font-medium flex items-center" style="color:#D4AF37;"
                >
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </button>

                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold mb-2" style="color:#F5F5F5; font-family:'Playfair Display',serif;">{{ __('app.claim_payment_title') }}</h1>
                    <p style="color:#9CA3AF;">{{ __('app.claim_payment_subtitle') }}</p>
                </div>

                {{-- NOTE: This step is now only reached if selectPlan() fails to redirect.
                     The normal flow redirects directly to Stripe Checkout. --}}

                {{-- Order Summary --}}
                <div class="max-w-2xl mx-auto">
                    <div class="rounded-lg p-6 mb-6" style="background:#111111; border:1px solid #2A2A2A;">
                        <h3 class="text-lg font-semibold mb-4" style="color:#F5F5F5;">{{ __('app.claim_order_summary') }}</h3>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span style="color:#9CA3AF;">{{ __('app.claim_restaurant_label') }}</span>
                                <span class="font-medium" style="color:#F5F5F5;">{{ $selectedRestaurant->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span style="color:#9CA3AF;">{{ __('app.claim_plan_label') }}</span>
                                <span class="font-medium" style="color:#F5F5F5;">{{ ucfirst($selectedPlan) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span style="color:#9CA3AF;">{{ __('app.claim_price_label') }}</span>
                                <span class="font-medium" style="color:#F5F5F5;">
                                    @if($selectedPlan === 'premium')
                                        $9.99 primer mes, después $39{{ __('app.claim_plan_month') }}
                                    @elseif($selectedPlan === 'elite')
                                        $79{{ __('app.claim_plan_month') }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="pt-4" style="border-top:1px solid #2A2A2A;">
                            <div class="flex justify-between text-lg font-bold">
                                <span style="color:#F5F5F5;">{{ __('app.claim_monthly_total') }}</span>
                                <span style="color:#D4AF37;">
                                    @if($selectedPlan === 'premium')
                                        $9.99 <span class='text-sm font-normal' style='color:#9CA3AF;'>(primer mes)</span>
                                    @elseif($selectedPlan === 'elite')
                                        $79
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Coupon Code Section --}}
                    <div class="rounded-lg p-6 mb-6" style="background:#111111; border:1px solid #2A2A2A;" x-data="{ showCoupon: false }">
                        <button type="button" @click="showCoupon = !showCoupon" class="flex items-center gap-2 text-sm font-medium transition-colors" style="color:#CCCCCC;">
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
                                    class="flex-1 px-4 py-3 rounded-lg focus:outline-none uppercase"
                                    style="background:#0B0B0B; border:1px solid #2A2A2A; color:#F5F5F5;"
                                    onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'"
                                >
                                <button
                                    wire:click="applyCoupon"
                                    class="px-6 py-3 rounded-lg font-medium transition-colors" style="background:#2A2A2A; color:#F5F5F5;"
                                >
                                    {{ __('app.claim_coupon_apply') }}
                                </button>
                            </div>

                            @if($couponMessage)
                                <div class="mt-3 p-3 rounded-lg" style="{{ $couponApplied ? 'background:rgba(74,222,128,0.1); border:1px solid rgba(74,222,128,0.3);' : 'background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3);' }}">
                                    <p class="text-sm flex items-center gap-2" style="{{ $couponApplied ? 'color:#4ADE80;' : 'color:#EF4444;' }}">
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

                    {{-- Stripe Checkout redirect button --}}
                    <div class="rounded-lg p-6" style="background:#111111; border:1px solid #2A2A2A;">

                        @if(session('error'))
                            <div class="mb-4 p-3 rounded-lg" style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3);">
                                <p class="text-sm" style="color:#EF4444;">{{ session('error') }}</p>
                            </div>
                        @endif

                        <button
                            wire:click="processPayment"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-70 cursor-not-allowed"
                            class="w-full px-8 py-4 rounded-lg font-semibold transition-all flex items-center justify-center gap-2"
                            style="background:#D4AF37; color:#0B0B0B;"
                        >
                            <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <svg wire:loading class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span wire:loading.remove>{{ __('app.claim_payment_button') }}</span>
                            <span wire:loading>Redirigiendo a Stripe...</span>
                        </button>

                        <div class="mt-4 flex items-center justify-center">
                            <svg class="h-6" viewBox="0 0 468 222.5" xmlns="http://www.w3.org/2000/svg"><path fill="#635BFF" fill-rule="evenodd" d="M414 113.4c0-25.6-12.4-45.8-36.1-45.8-23.8 0-38.2 20.2-38.2 45.6 0 30.1 17 45.3 41.4 45.3 11.9 0 20.9-2.7 27.7-6.5v-20c-6.8 3.4-14.6 5.5-24.5 5.5-9.7 0-18.3-3.4-19.4-15.2h48.9c0-1.3.2-6.5.2-8.9zm-49.4-9.5c0-11.3 6.9-16 13.2-16 6.1 0 12.6 4.7 12.6 16h-25.8zm-63.5-36.3c-9.8 0-16.1 4.6-19.6 7.8l-1.3-6.2h-22v116.6l25-5.3.1-28.3c3.6 2.6 8.9 6.3 17.7 6.3 17.9 0 34.2-14.4 34.2-46.1-.1-29-16.6-44.8-34.1-44.8zm-6 68.9c-5.9 0-9.4-2.1-11.8-4.7l-.1-37.1c2.6-2.9 6.2-4.9 11.9-4.9 9.1 0 15.4 10.2 15.4 23.3 0 13.4-6.2 23.4-15.4 23.4zm-71.3-74.8l25.1-5.4V36l-25.1 5.3v20.4zm0 7.6h25.1v87.5h-25.1v-87.5zm-26.7 7.4l-1.6-7.4h-21.6v87.5h25V97.5c5.9-7.7 15.9-6.3 19-5.2v-23c-3.2-1.2-14.9-3.4-20.8 7.4zm-48.1-39.9l-24.4 5.2-.1 80.1c0 14.8 11.1 25.7 25.9 25.7 8.2 0 14.2-1.5 17.5-3.3V135c-3.2 1.3-19-2.6-19-17.6V89h19V69.3h-19l.1-32.5zm-70.8 66.5c0-3.9 3.2-5.4 8.5-5.4 7.6 0 17.2 2.3 24.8 6.4V72.2c-8.3-3.3-16.5-4.6-24.8-4.6C58.5 67.6 41 81.8 41 103.8c0 34.2 47.1 28.7 47.1 43.4 0 4.6-4 6.1-9.6 6.1-8.3 0-18.9-3.4-27.3-8v23.8c9.3 4 18.7 5.7 27.3 5.7 23.8 0 40.2-11.8 40.2-34.2-.1-36.9-47.4-30.3-47.4-44.1z"/></svg>
                        </div>

                        <p class="text-center text-xs mt-3" style="color:#6B7280;">
                            {{ __('app.claim_payment_secure_notice') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- Support Link --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 text-center">
        <a href="mailto:owners@restaurantesmexicanosfamosos.com?subject={{ urlencode('Problema al reclamar restaurante') }}&body={{ urlencode('Hola, tengo problemas para reclamar mi restaurante. Necesito ayuda con lo siguiente:') }}" class="inline-flex items-center gap-2 text-sm transition-colors" style="color:#6B7280;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            ¿Problemas para reclamar? Contáctenos
        </a>
    </div>

</div>

{{-- famerMountStripe: global function called by Alpine x-data init() on the Stripe section.
     Alpine fires init() automatically when Livewire adds the element to the DOM after morph. --}}
@script
<script>
window.famerMountStripe = function(clientSecret, stripeKey) {
    var mountEl   = document.getElementById('famer-stripe-mount');
    var submitBtn = document.getElementById('famer-stripe-submit');
    var errorDiv  = document.getElementById('famer-stripe-error');
    var errorText = document.getElementById('famer-stripe-error-text');
    var lockIcon  = document.getElementById('famer-stripe-lock-icon');
    var spinner   = document.getElementById('famer-stripe-spinner');
    var btnText   = document.getElementById('famer-stripe-btn-text');

    if (!mountEl || mountEl._stripeInit) return;
    mountEl._stripeInit = true;

    function doMount() {
        if (!window.Stripe) return;

        var stripe   = Stripe(stripeKey);
        var elements = stripe.elements();
        var cardEl   = elements.create('card', {
            style: {
                base: {
                    color:           '#F5F5F5',
                    fontFamily:      'Poppins, system-ui, sans-serif',
                    fontSize:        '16px',
                    fontSmoothing:   'antialiased',
                    '::placeholder': { color: '#6B7280' },
                    iconColor:       '#D4AF37',
                },
                invalid: {
                    color:     '#EF4444',
                    iconColor: '#EF4444',
                }
            },
            hidePostalCode: true,
        });

        cardEl.mount(mountEl);

        cardEl.on('change', function(ev) {
            if (ev.complete) {
                submitBtn.disabled        = false;
                submitBtn.style.opacity   = '1';
                submitBtn.style.cursor    = 'pointer';
            } else {
                submitBtn.disabled        = true;
                submitBtn.style.opacity   = '0.5';
                submitBtn.style.cursor    = 'not-allowed';
            }
            if (ev.error) {
                errorText.textContent  = ev.error.message;
                errorDiv.style.display = 'block';
            } else {
                errorDiv.style.display = 'none';
            }
        });

        submitBtn.addEventListener('click', async function() {
            if (submitBtn.disabled) return;
            submitBtn.disabled       = true;
            submitBtn.style.opacity  = '0.7';
            lockIcon.style.display   = 'none';
            spinner.style.display    = 'block';
            btnText.textContent      = 'Procesando...';
            errorDiv.style.display   = 'none';

            var result = await stripe.confirmCardSetup(clientSecret, {
                payment_method: { card: cardEl }
            });

            if (result.error) {
                errorText.textContent   = result.error.message;
                errorDiv.style.display  = 'block';
                submitBtn.disabled      = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor  = 'pointer';
                lockIcon.style.display  = 'block';
                spinner.style.display   = 'none';
                btnText.textContent     = 'Pagar con Stripe';
            } else if (result.setupIntent && result.setupIntent.status === 'succeeded') {
                $wire.completeSubscriptionPayment(result.setupIntent.id);
            } else {
                errorText.textContent   = 'El pago no pudo completarse. Por favor intenta de nuevo.';
                errorDiv.style.display  = 'block';
                submitBtn.disabled      = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor  = 'pointer';
                lockIcon.style.display  = 'block';
                spinner.style.display   = 'none';
                btnText.textContent     = 'Pagar con Stripe';
            }
        });
    }

    // By the time user reaches step 4, Stripe.js (defer) has long since loaded.
    // Fallback injection just in case.
    if (typeof window.Stripe === 'function') {
        doMount();
    } else {
        var s = document.createElement('script');
        s.src = 'https://js.stripe.com/v3/';
        s.onload = doMount;
        document.head.appendChild(s);
    }
};
</script>
@endscript