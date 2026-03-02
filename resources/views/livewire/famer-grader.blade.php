<div class="min-h-screen bg-gradient-to-b from-red-50 to-white">
    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-red-600 to-red-700 text-white py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                FAMER Score
            </h1>
            <p class="text-xl text-red-100 mb-2">
                Califica la presencia digital de tu restaurante mexicano
            </p>
            <p class="text-red-200">
                Descubre como mejorar tu visibilidad online y atraer mas clientes
            </p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Messages --}}
        @if ($successMessage)
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $successMessage }}</span>
                </div>
            </div>
        @endif

        @if ($errorMessage)
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $errorMessage }}</span>
                </div>
            </div>
        @endif

        {{-- Analysis Animation Section --}}
        @if ($showAnalysis && $selectedRestaurant)
            <div
                x-data="{
                    currentStep: -1,
                    overallProgress: 0,
                    isComplete: false,
                    cats: [
                        { status: 'Esperando...', progress: 0, score: null },
                        { status: 'Esperando...', progress: 0, score: null },
                        { status: 'Esperando...', progress: 0, score: null },
                        { status: 'Esperando...', progress: 0, score: null },
                        { status: 'Esperando...', progress: 0, score: null },
                        { status: 'Esperando...', progress: 0, score: null }
                    ],
                    actions: [
                        ['Verificando informacion...', 'Analizando fotos...', 'Revisando horarios...', 'Evaluando descripcion...'],
                        ['Buscando en Google...', 'Verificando Yelp...', 'Analizando Facebook...', 'Calculando...'],
                        ['Contando resenas...', 'Analizando ratings...', 'Revisando fotos...', 'Evaluando...'],
                        ['Contando items...', 'Analizando categorias...', 'Verificando precios...', 'Revisando...'],
                        ['Verificando origen...', 'Buscando certificaciones...', 'Analizando...', 'Evaluando...'],
                        ['Verificando sitio web...', 'Analizando pedidos...', 'Revisando reservas...', 'Calculando final...']
                    ],
                    async start() {
                        this.overallProgress = 0;
                        this.currentStep = -1;
                        await new Promise(r => setTimeout(r, 300));
                        for (let i = 0; i < 6; i++) {
                            this.currentStep = i;
                            for (let j = 0; j < 4; j++) {
                                this.cats[i].status = this.actions[i][j];
                                this.cats[i].progress = ((j + 1) / 4) * 100;
                                this.overallProgress = ((i + (j + 1) / 4) / 6) * 100;
                                await new Promise(r => setTimeout(r, 350 + Math.random() * 250));
                            }
                            this.cats[i].score = Math.floor(30 + Math.random() * 60);
                            this.cats[i].status = 'Completado';
                            await new Promise(r => setTimeout(r, 150));
                        }
                        this.currentStep = 6;
                        this.overallProgress = 100;
                        this.isComplete = true;
                    }
                }"
                x-init="start()"
                class="bg-white rounded-xl shadow-lg -mt-12 relative z-10 overflow-hidden"
            >
                {{-- Restaurant Header --}}
                <div class="bg-gray-50 border-b px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $selectedRestaurant['name'] }}</h2>
                            <p class="text-sm text-gray-600">
                                {{ $selectedRestaurant['city'] }}, {{ $selectedRestaurant['state'] }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                            <span class="text-sm font-medium text-gray-600">Analizando...</span>
                        </div>
                    </div>
                </div>

                <div class="p-6 md:p-8">
                    {{-- Analysis Progress --}}
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Analizando Presencia Digital</h3>
                            <span class="text-sm font-medium text-gray-500" x-text="Math.round(overallProgress) + '%'"></span>
                        </div>

                        {{-- Overall Progress Bar --}}
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-8 overflow-hidden">
                            <div
                                class="h-3 rounded-full bg-gradient-to-r from-red-500 to-red-600 transition-all duration-300 ease-out"
                                :style="'width: ' + overallProgress + '%'"
                            ></div>
                        </div>

                        {{-- Analysis Categories --}}
                        <div class="space-y-6">
                            {{-- Profile Analysis --}}
                            <div class="analysis-category" :class="{ 'opacity-50': currentStep < 0, 'opacity-100': currentStep >= 0 }">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-500"
                                            :class="currentStep > 0 ? 'bg-emerald-100' : (currentStep === 0 ? 'bg-red-100 animate-pulse' : 'bg-gray-100')">
                                            <svg x-show="currentStep > 0" class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <svg x-show="currentStep === 0" class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <svg x-show="currentStep < 0" class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Perfil Completo</h4>
                                            <p class="text-xs text-gray-500" x-text="cats[0].status"></p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium" :class="currentStep > 0 ? 'text-emerald-600' : 'text-gray-400'" x-text="cats[0].score !== null ? cats[0].score + '/100' : '--'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full transition-all duration-700 ease-out"
                                        :class="currentStep > 0 ? 'bg-emerald-500' : 'bg-red-500'"
                                        :style="'width: ' + cats[0].progress + '%'"></div>
                                </div>
                            </div>

                            {{-- Online Presence Analysis --}}
                            <div class="analysis-category" :class="{ 'opacity-50': currentStep < 1, 'opacity-100': currentStep >= 1 }">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-500"
                                            :class="currentStep > 1 ? 'bg-emerald-100' : (currentStep === 1 ? 'bg-blue-100 animate-pulse' : 'bg-gray-100')">
                                            <svg x-show="currentStep > 1" class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <svg x-show="currentStep === 1" class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                            </svg>
                                            <svg x-show="currentStep < 1" class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Presencia Online</h4>
                                            <p class="text-xs text-gray-500" x-text="cats[1].status"></p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium" :class="currentStep > 1 ? 'text-emerald-600' : 'text-gray-400'" x-text="cats[1].score !== null ? cats[1].score + '/100' : '--'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full transition-all duration-700 ease-out"
                                        :class="currentStep > 1 ? 'bg-emerald-500' : 'bg-blue-500'"
                                        :style="'width: ' + cats[1].progress + '%'"></div>
                                </div>
                            </div>

                            {{-- Customer Engagement Analysis --}}
                            <div class="analysis-category" :class="{ 'opacity-50': currentStep < 2, 'opacity-100': currentStep >= 2 }">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-500"
                                            :class="currentStep > 2 ? 'bg-emerald-100' : (currentStep === 2 ? 'bg-yellow-100 animate-pulse' : 'bg-gray-100')">
                                            <svg x-show="currentStep > 2" class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <svg x-show="currentStep === 2" class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                                            </svg>
                                            <svg x-show="currentStep < 2" class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Engagement</h4>
                                            <p class="text-xs text-gray-500" x-text="cats[2].status"></p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium" :class="currentStep > 2 ? 'text-emerald-600' : 'text-gray-400'" x-text="cats[2].score !== null ? cats[2].score + '/100' : '--'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full transition-all duration-700 ease-out"
                                        :class="currentStep > 2 ? 'bg-emerald-500' : 'bg-yellow-500'"
                                        :style="'width: ' + cats[2].progress + '%'"></div>
                                </div>
                            </div>

                            {{-- Menu Analysis --}}
                            <div class="analysis-category" :class="{ 'opacity-50': currentStep < 3, 'opacity-100': currentStep >= 3 }">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-500"
                                            :class="currentStep > 3 ? 'bg-emerald-100' : (currentStep === 3 ? 'bg-orange-100 animate-pulse' : 'bg-gray-100')">
                                            <svg x-show="currentStep > 3" class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <svg x-show="currentStep === 3" class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            <svg x-show="currentStep < 3" class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Menu</h4>
                                            <p class="text-xs text-gray-500" x-text="cats[3].status"></p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium" :class="currentStep > 3 ? 'text-emerald-600' : 'text-gray-400'" x-text="cats[3].score !== null ? cats[3].score + '/100' : '--'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full transition-all duration-700 ease-out"
                                        :class="currentStep > 3 ? 'bg-emerald-500' : 'bg-orange-500'"
                                        :style="'width: ' + cats[3].progress + '%'"></div>
                                </div>
                            </div>

                            {{-- Authenticity Analysis --}}
                            <div class="analysis-category" :class="{ 'opacity-50': currentStep < 4, 'opacity-100': currentStep >= 4 }">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-500"
                                            :class="currentStep > 4 ? 'bg-emerald-100' : (currentStep === 4 ? 'bg-red-100 animate-pulse' : 'bg-gray-100')">
                                            <svg x-show="currentStep > 4" class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <svg x-show="currentStep === 4" class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                                            </svg>
                                            <svg x-show="currentStep < 4" class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Autenticidad</h4>
                                            <p class="text-xs text-gray-500" x-text="cats[4].status"></p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium" :class="currentStep > 4 ? 'text-emerald-600' : 'text-gray-400'" x-text="cats[4].score !== null ? cats[4].score + '/100' : '--'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full transition-all duration-700 ease-out"
                                        :class="currentStep > 4 ? 'bg-emerald-500' : 'bg-red-500'"
                                        :style="'width: ' + cats[4].progress + '%'"></div>
                                </div>
                            </div>

                            {{-- Digital Readiness Analysis --}}
                            <div class="analysis-category" :class="{ 'opacity-50': currentStep < 5, 'opacity-100': currentStep >= 5 }">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-500"
                                            :class="currentStep > 5 ? 'bg-emerald-100' : (currentStep === 5 ? 'bg-purple-100 animate-pulse' : 'bg-gray-100')">
                                            <svg x-show="currentStep > 5" class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <svg x-show="currentStep === 5" class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            <svg x-show="currentStep < 5" class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Digital</h4>
                                            <p class="text-xs text-gray-500" x-text="cats[5].status"></p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium" :class="currentStep > 5 ? 'text-emerald-600' : 'text-gray-400'" x-text="cats[5].score !== null ? cats[5].score + '/100' : '--'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full transition-all duration-700 ease-out"
                                        :class="currentStep > 5 ? 'bg-emerald-500' : 'bg-purple-500'"
                                        :style="'width: ' + cats[5].progress + '%'"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Analysis Complete Button --}}
                    <div x-show="isComplete" x-cloak class="text-center">
                        <div class="mb-4 inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-700 rounded-full">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Analisis Completado
                        </div>
                        <button
                            wire:click="completeAnalysis"
                            class="w-full py-4 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold rounded-lg text-lg transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02]"
                        >
                            Ver Resultados Completos
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Search Section (show when no score displayed and not analyzing) --}}
        @if (!$scoreData && !$showAnalysis)
            <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 -mt-12 relative z-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">
                    Busca tu Restaurante
                </h2>

                <div class="space-y-4">
                    <div>
                        <label for="searchName" class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre del Restaurante
                        </label>
                        <input
                            type="text"
                            id="searchName"
                            wire:model="searchName"
                            wire:keydown.enter="search"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-lg"
                            placeholder="Ej: Taqueria El Mexicano"
                            autofocus
                        >
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="searchCity" class="block text-sm font-medium text-gray-700 mb-1">
                                Ciudad
                            </label>
                            <input
                                type="text"
                                id="searchCity"
                                wire:model="searchCity"
                                wire:keydown.enter="search"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                placeholder="Ej: Los Angeles"
                            >
                        </div>

                        <div>
                            <label for="searchState" class="block text-sm font-medium text-gray-700 mb-1">
                                Estado
                            </label>
                            <select
                                id="searchState"
                                wire:model="searchState"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            >
                                <option value="">Todos los estados</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->code }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button
                        type="button"
                        wire:click="search"
                        wire:loading.attr="disabled"
                        class="w-full py-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-lg transition-colors disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="search">
                            Obtener FAMER Score
                        </span>
                        <span wire:loading wire:target="search" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Buscando...
                        </span>
                    </button>
                </div>

                {{-- Search Results --}}
                @if ($hasSearched && !empty($searchResults))
                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Resultados ({{ count($searchResults) }})
                        </h3>

                        <div class="space-y-3">
                            @foreach ($searchResults as $result)
                                <button
                                    wire:click="selectResult('{{ $result['id'] }}')"
                                    wire:loading.attr="disabled"
                                    class="w-full text-left p-4 border rounded-lg hover:border-red-300 hover:shadow-md transition-all {{ $result['source'] === 'famer' ? 'bg-emerald-50 border-emerald-200' : 'bg-white border-gray-200' }}"
                                >
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                                <h4 class="font-semibold text-gray-900 truncate">{{ $result['name'] }}</h4>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $result['source_color'] }}-100 text-{{ $result['source_color'] }}-800">
                                                    {{ $result['source_label'] }}
                                                </span>
                                                @if ($result['is_claimed'] ?? false)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        Verificado
                                                    </span>
                                                @endif
                                                @if ($result['has_score'] ?? false)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                        Score: {{ $result['existing_score'] }} ({{ $result['existing_grade'] }})
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-600 truncate">
                                                {{ $result['address'] }}, {{ $result['city'] }}, {{ $result['state'] }}
                                            </p>
                                            @if ($result['rating'])
                                                <div class="flex items-center mt-1 text-sm">
                                                    <span class="text-yellow-500">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= round($result['rating']))
                                                                <span>&#9733;</span>
                                                            @else
                                                                <span class="text-gray-300">&#9733;</span>
                                                            @endif
                                                        @endfor
                                                    </span>
                                                    <span class="ml-1 text-gray-500">
                                                        {{ number_format($result['rating'], 1) }}
                                                        @if ($result['review_count'])
                                                            ({{ number_format($result['review_count']) }} reviews)
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        @if ($result['image_url'])
                                            <img src="{{ $result['image_url'] }}" alt="{{ $result['name'] }}" class="w-16 h-16 object-cover rounded-lg ml-4 flex-shrink-0">
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Score Display --}}
        @if ($scoreData && !$showAnalysis)
            <div class="bg-white rounded-xl shadow-lg -mt-12 relative z-10 overflow-hidden">
                {{-- Restaurant Header --}}
                <div class="bg-gray-50 border-b px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $selectedRestaurant['name'] }}</h2>
                            <p class="text-sm text-gray-600">
                                {{ $selectedRestaurant['city'] }}, {{ $selectedRestaurant['state'] }}
                            </p>
                        </div>
                        <button
                            wire:click="resetSearch"
                            class="text-gray-500 hover:text-gray-700 text-sm font-medium"
                        >
                            Nueva Busqueda
                        </button>
                    </div>
                </div>

                <div class="p-6 md:p-8">
                    {{-- Partial Score Warning --}}
                    @if ($scoreData['is_partial'] ?? false)
                        <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="font-medium">Score Parcial</p>
                                    <p class="text-sm">{{ $scoreData['message'] ?? 'Este score esta basado en datos publicos. Agrega tu restaurante a FAMER para un analisis completo.' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Score Circle and Grade --}}
                    <div class="text-center mb-8">
                        <div class="relative inline-block">
                            {{-- Circular Progress --}}
                            <svg class="w-48 h-48 transform -rotate-90" viewBox="0 0 200 200">
                                {{-- Background circle --}}
                                <circle
                                    cx="100"
                                    cy="100"
                                    r="88"
                                    stroke-width="12"
                                    stroke="currentColor"
                                    class="text-gray-200"
                                    fill="none"
                                />
                                {{-- Progress circle --}}
                                <circle
                                    cx="100"
                                    cy="100"
                                    r="88"
                                    stroke-width="12"
                                    stroke="currentColor"
                                    class="text-{{ $scoreData['grade_color'] }}-500"
                                    fill="none"
                                    stroke-dasharray="{{ $scoreData['overall_score'] * 5.53 }} 553"
                                    stroke-linecap="round"
                                />
                            </svg>
                            {{-- Score Text --}}
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-5xl font-bold text-gray-900">{{ $scoreData['overall_score'] }}</span>
                                <span class="text-3xl font-bold text-{{ $scoreData['grade_color'] }}-600">{{ $scoreData['letter_grade'] }}</span>
                            </div>
                        </div>

                        <p class="mt-4 text-gray-600 max-w-md mx-auto">
                            {{ $scoreData['score_description'] }}
                        </p>

                        {{-- Comparison --}}
                        @if ($scoreData['percentile'] && !$isExternalRestaurant)
                            <div class="mt-4 inline-flex items-center px-4 py-2 bg-{{ $scoreData['grade_color'] }}-50 rounded-full">
                                <span class="text-{{ $scoreData['grade_color'] }}-700 font-medium">
                                    Top {{ 100 - $scoreData['percentile'] }}% en {{ $selectedRestaurant['city'] }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Category Breakdown (only for full scores) --}}
                    @if (!($scoreData['is_partial'] ?? false) && $scoreData['categories'])
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Desglose por Categoria</h3>

                            <div class="space-y-4">
                                @foreach ($scoreData['categories'] as $category)
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ $category['name'] }}
                                                <span class="text-gray-400 text-xs">({{ $category['weight'] }}%)</span>
                                            </span>
                                            <span class="text-sm font-semibold
                                                @if($category['score'] >= 80) text-emerald-600
                                                @elseif($category['score'] >= 60) text-blue-600
                                                @elseif($category['score'] >= 40) text-yellow-600
                                                @else text-red-600
                                                @endif
                                            ">
                                                {{ $category['score'] }}/100
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3">
                                            <div
                                                class="h-3 rounded-full transition-all duration-500
                                                    @if($category['score'] >= 80) bg-emerald-500
                                                    @elseif($category['score'] >= 60) bg-blue-500
                                                    @elseif($category['score'] >= 40) bg-yellow-500
                                                    @else bg-red-500
                                                    @endif
                                                "
                                                style="width: {{ $category['score'] }}%"
                                            ></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Top Recommendations (Teaser) --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Principales Recomendaciones
                        </h3>

                        <div class="space-y-3">
                            @foreach ($scoreData['top_recommendations'] as $rec)
                                <div class="flex items-start p-4 rounded-lg
                                    @if($rec['priority'] === 'critical') bg-red-50 border border-red-200
                                    @elseif($rec['priority'] === 'high') bg-orange-50 border border-orange-200
                                    @else bg-gray-50 border border-gray-200
                                    @endif
                                ">
                                    <div class="flex-shrink-0 mr-3">
                                        @if($rec['priority'] === 'critical')
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100">
                                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                        @elseif($rec['priority'] === 'high')
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100">
                                                <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v4a1 1 0 102 0V7zm-1 8a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100">
                                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <h4 class="font-semibold text-gray-900">{{ $rec['title'] }}</h4>
                                            @if(isset($rec['impact']))
                                                <span class="text-sm font-medium text-emerald-600">{{ $rec['impact'] }}</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $rec['description'] }}</p>
                                        @if(isset($rec['action_url']))
                                            <a
                                                href="{{ $rec['action_url'] }}"
                                                class="inline-flex items-center mt-2 text-sm font-medium text-red-600 hover:text-red-700"
                                            >
                                                {{ $rec['action_label'] ?? 'Ver mas' }}
                                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- CTA for Full Report --}}
                    @if (!$emailSubmitted)
                        <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-xl p-6 text-center text-white">
                            <h3 class="text-xl font-bold mb-2">Quieres el Reporte Completo?</h3>
                            <p class="text-red-100 mb-4">
                                Recibe todas las recomendaciones, comparaciones detalladas y pasos especificos para mejorar tu score.
                            </p>
                            <button
                                wire:click="requestFullReport"
                                class="px-8 py-3 bg-white text-red-600 font-bold rounded-lg hover:bg-red-50 transition-colors"
                            >
                                Obtener Reporte Gratis
                            </button>
                        </div>
                    @endif

                    {{-- CTA for Claiming --}}
                    @if (!($selectedRestaurant['is_claimed'] ?? true))
                        <div class="mt-6 bg-emerald-50 border border-emerald-200 rounded-xl p-6 text-center">
                            <h3 class="text-lg font-bold text-emerald-900 mb-2">
                                Eres el dueno de {{ $selectedRestaurant['name'] }}?
                            </h3>
                            <p class="text-emerald-700 mb-4">
                                Reclama tu restaurante para editar tu perfil, responder resenas y acceder a herramientas de marketing.
                            </p>
                            <a
                                href="{{ route('claim.restaurant') }}"
                                class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 transition-colors"
                            >
                                Reclamar Restaurante
                                <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- How It Works Section --}}
        @if (!$scoreData && !$showAnalysis)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">Como Funciona?</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-6">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">1. Busca</h3>
                        <p class="text-gray-600 text-sm">
                            Ingresa el nombre de tu restaurante y ubicacion
                        </p>
                    </div>

                    <div class="text-center p-6">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">2. Analiza</h3>
                        <p class="text-gray-600 text-sm">
                            Evaluamos 6 categorias clave de tu presencia digital
                        </p>
                    </div>

                    <div class="text-center p-6">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">3. Mejora</h3>
                        <p class="text-gray-600 text-sm">
                            Recibe recomendaciones personalizadas para subir tu score
                        </p>
                    </div>
                </div>
            </div>

            {{-- Categories Explanation --}}
            <div class="mt-12 bg-white rounded-xl shadow-lg p-6 md:p-8">
                <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">Que Evaluamos?</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Perfil Completo (20%)</h3>
                            <p class="text-sm text-gray-600">Fotos, descripcion, horarios, contacto</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Presencia Online (25%)</h3>
                            <p class="text-sm text-gray-600">Google, Yelp, Facebook</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Engagement (20%)</h3>
                            <p class="text-sm text-gray-600">Resenas, calificaciones, fotos de usuarios</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Menu (15%)</h3>
                            <p class="text-sm text-gray-600">Items, categorias, opciones dieteticas</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Autenticidad (10%)</h3>
                            <p class="text-sm text-gray-600">Region mexicana, certificaciones</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Digital (10%)</h3>
                            <p class="text-sm text-gray-600">Website, pedidos online, reservaciones</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Email Capture Modal --}}
    @if ($showEmailCapture)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Obtener Reporte Completo</h3>
                    <button
                        wire:click="$set('showEmailCapture', false)"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <p class="text-gray-600 mb-6">
                    Ingresa tu email para recibir el reporte completo con todas las recomendaciones.
                </p>

                <div class="space-y-4">
                    <div>
                        <label for="leadEmail" class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-red-600">*</span>
                        </label>
                        <input
                            type="email"
                            id="leadEmail"
                            wire:model="leadEmail"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            placeholder="tu@email.com"
                        >
                        @error('leadEmail')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="leadName" class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre (opcional)
                        </label>
                        <input
                            type="text"
                            id="leadName"
                            wire:model="leadName"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            placeholder="Tu nombre"
                        >
                    </div>

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="isOwner"
                            wire:model="isOwner"
                            class="rounded border-gray-300 text-red-600 focus:ring-red-500"
                        >
                        <label for="isOwner" class="ml-2 text-sm text-gray-700">
                            Soy el dueno/gerente del restaurante
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="marketingConsent"
                            wire:model="marketingConsent"
                            class="rounded border-gray-300 text-red-600 focus:ring-red-500"
                        >
                        <label for="marketingConsent" class="ml-2 text-sm text-gray-700">
                            Acepto recibir tips y promociones de FAMER
                        </label>
                    </div>

                    <button
                        wire:click="submitEmailForReport"
                        wire:loading.attr="disabled"
                        class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-colors disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="submitEmailForReport">
                            Enviar Reporte
                        </span>
                        <span wire:loading wire:target="submitEmailForReport" class="inline-flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Enviando...
                        </span>
                    </button>
                </div>

                <p class="mt-4 text-xs text-gray-500 text-center">
                    No compartimos tu informacion con terceros.
                </p>
            </div>
        </div>
    @endif

    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>
