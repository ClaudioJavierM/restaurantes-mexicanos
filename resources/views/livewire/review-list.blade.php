<div>
    {{-- ── Anti-fake Alert Banner ─────────────────────────────────────────── --}}
    @if(!empty($suspiciousAlerts))
        <div class="mb-6 bg-amber-50 border border-amber-300 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-amber-800">
                        {{ app()->getLocale() === 'en' ? 'Notice: Unusual review activity detected' : 'Aviso: Se detectó actividad inusual en las reseñas' }}
                    </p>
                    <ul class="mt-1 space-y-1">
                        @foreach($suspiciousAlerts as $alert)
                            <li class="text-sm text-amber-700">• {{ $alert['message'] }}</li>
                        @endforeach
                    </ul>
                    <p class="text-xs text-amber-600 mt-2">
                        {{ app()->getLocale() === 'en'
                            ? 'Our system has flagged some patterns. Reviews are being reviewed by our team.'
                            : 'Nuestro sistema detectó patrones sospechosos. Las reseñas están siendo revisadas por nuestro equipo.' }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Review Policy Section ──────────────────────────────────────────── --}}
    <div class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4" x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between text-left">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-sm font-semibold text-gray-700">
                    {{ app()->getLocale() === 'en' ? 'Our Review Integrity Policy' : 'Nuestra Política de Integridad de Reseñas' }}
                </span>
            </div>
            <svg class="w-4 h-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open" x-transition class="mt-3 text-sm text-gray-600 space-y-2">
            @if(app()->getLocale() === 'en')
                <p>We are committed to authentic, trustworthy reviews. Here is how we protect them:</p>
                <ul class="space-y-1 list-none">
                    <li class="flex items-start gap-2"><span class="text-green-600 font-bold mt-0.5">✓</span> All reviews are analyzed by our anti-fraud system before publication.</li>
                    <li class="flex items-start gap-2"><span class="text-green-600 font-bold mt-0.5">✓</span> Reviews from verified accounts receive a trust badge.</li>
                    <li class="flex items-start gap-2"><span class="text-green-600 font-bold mt-0.5">✓</span> Suspicious patterns (IP clusters, duplicate content, review bursts) are automatically flagged.</li>
                    <li class="flex items-start gap-2"><span class="text-green-600 font-bold mt-0.5">✓</span> New accounts cannot auto-publish reviews — they go through a brief review queue.</li>
                    <li class="flex items-start gap-2"><span class="text-green-600 font-bold mt-0.5">✓</span> You can report any review using the flag button.</li>
                </ul>
            @else
                <p>Nos comprometemos con reseñas auténticas y confiables. Así las protegemos:</p>
                <ul class="space-y-1 list-none">
                    <li class="flex items-start gap-2"><span class="text-green-600 font-bold mt-0.5">✓</span> Todas las reseñas son analizadas por nuestro sistema anti-fraude antes de publicarse.</li>
                    <li class="flex items-start gap-2"><span class="text-green-600 font-bold mt-0.5">✓</span> Las reseñas de cuentas verificadas reciben una insignia de confianza.</li>
                    <li class="flex items-start gap-2"><span class="text-green-600 font-bold mt-0.5">✓</span> Patrones sospechosos (misma IP, contenido duplicado, ráfagas de reseñas) se detectan automáticamente.</li>
                    <li class="flex items-start gap-2"><span class="text-green-600 font-bold mt-0.5">✓</span> Las cuentas nuevas no pueden auto-publicar reseñas — pasan por una revisión breve.</li>
                    <li class="flex items-start gap-2"><span class="text-green-600 font-bold mt-0.5">✓</span> Puedes reportar cualquier reseña usando el botón de bandera.</li>
                </ul>
            @endif
        </div>
    </div>

    {{-- ── Filters and Sorting ────────────────────────────────────────────── --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-gray-700">
                    {{ app()->getLocale() === 'en' ? 'Sort by:' : 'Ordenar por:' }}
                </span>
                <div class="flex gap-2 flex-wrap">
                    <button wire:click="setSortBy('recent')"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $sortBy === 'recent' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ app()->getLocale() === 'en' ? 'Most Recent' : 'Más Recientes' }}
                    </button>
                    <button wire:click="setSortBy('helpful')"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $sortBy === 'helpful' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ app()->getLocale() === 'en' ? 'Most Helpful' : 'Más Útiles' }}
                    </button>
                    <button wire:click="setSortBy('rating_high')"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $sortBy === 'rating_high' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ app()->getLocale() === 'en' ? 'Highest Rated' : 'Mejor Calificadas' }}
                    </button>
                    <button wire:click="setSortBy('rating_low')"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $sortBy === 'rating_low' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ app()->getLocale() === 'en' ? 'Lowest Rated' : 'Peor Calificadas' }}
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-gray-700">
                    {{ app()->getLocale() === 'en' ? 'Filter:' : 'Filtrar:' }}
                </span>
                <div class="flex gap-2">
                    @for($i = 5; $i >= 1; $i--)
                        <button wire:click="setFilterRating({{ $i }})"
                                class="px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-1 {{ $filterRating === $i ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $i }}
                            <svg class="w-4 h-4 {{ $filterRating === $i ? 'text-white' : 'text-yellow-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            @if(isset($ratingDistribution[$i]))
                                <span class="text-xs">({{ $ratingDistribution[$i] }})</span>
                            @endif
                        </button>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    {{-- ── Flash Messages ─────────────────────────────────────────────────── --}}
    @if(session()->has('vote-success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            {{ session('vote-success') }}
        </div>
    @endif
    @if(session()->has('vote-error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            {{ session('vote-error') }}
        </div>
    @endif

    {{-- ── Reviews List ───────────────────────────────────────────────────── --}}
    <div class="space-y-6">
        @forelse($reviews as $review)
            <div class="bg-white rounded-lg shadow p-6 {{ $review->flagged_suspicious ? 'border border-amber-200' : '' }}">

                {{-- Review Header --}}
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        {{-- Avatar --}}
                        <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                            {{ strtoupper(substr($review->reviewer_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-semibold text-gray-900">{{ $review->reviewer_name }}</h4>

                                {{-- Trust Badge --}}
                                @if($review->is_verified)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ app()->getLocale() === 'en' ? 'Verified' : 'Verificado' }}
                                    </span>
                                @elseif($review->trust_score >= 70)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        {{ app()->getLocale() === 'en' ? 'Trusted' : 'Confiable' }}
                                    </span>
                                @endif

                                {{-- Edited badge --}}
                                @if($review->edit_count > 0)
                                    <span class="text-xs text-gray-400 italic" title="{{ app()->getLocale() === 'en' ? 'Edited ' . $review->edit_count . ' time(s)' : 'Editado ' . $review->edit_count . ' vez/veces' }}">
                                        ({{ app()->getLocale() === 'en' ? 'edited' : 'editado' }})
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600 flex-wrap mt-0.5">
                                <span>{{ $review->created_at->diffForHumans() }}</span>
                                @if($review->visit_date)
                                    <span>•</span>
                                    <span>{{ app()->getLocale() === 'en' ? 'Visited' : 'Visitó' }}: {{ \Carbon\Carbon::parse($review->visit_date)->format('M Y') }}</span>
                                @endif
                                @if($review->visit_type)
                                    <span>•</span>
                                    <span>
                                        @if($review->visit_type === 'dine_in')
                                            {{ app()->getLocale() === 'en' ? 'Dine In' : 'En el Restaurante' }}
                                        @elseif($review->visit_type === 'takeout')
                                            {{ app()->getLocale() === 'en' ? 'Takeout' : 'Para Llevar' }}
                                        @else
                                            {{ app()->getLocale() === 'en' ? 'Delivery' : 'Entrega a Domicilio' }}
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Rating Stars --}}
                    <div class="flex gap-1 flex-shrink-0">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                </div>

                {{-- Sub-ratings --}}
                @if($review->food_rating || $review->service_rating || $review->ambiance_rating)
                    <div class="flex flex-wrap gap-3 mb-3">
                        @if($review->food_rating)
                            <div class="flex items-center gap-1 text-xs text-gray-600">
                                <span class="font-medium">{{ app()->getLocale() === 'en' ? 'Food:' : 'Comida:' }}</span>
                                <div class="flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3 h-3 {{ $i <= $review->food_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                        @endif
                        @if($review->service_rating)
                            <div class="flex items-center gap-1 text-xs text-gray-600">
                                <span class="font-medium">{{ app()->getLocale() === 'en' ? 'Service:' : 'Servicio:' }}</span>
                                <div class="flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3 h-3 {{ $i <= $review->service_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                        @endif
                        @if($review->ambiance_rating)
                            <div class="flex items-center gap-1 text-xs text-gray-600">
                                <span class="font-medium">{{ app()->getLocale() === 'en' ? 'Ambiance:' : 'Ambiente:' }}</span>
                                <div class="flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3 h-3 {{ $i <= $review->ambiance_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Review Title --}}
                @if($review->title)
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $review->title }}</h3>
                @endif

                {{-- Review Content --}}
                <p class="text-gray-700 mb-4 leading-relaxed">{{ $review->comment }}</p>

                {{-- Review Photos --}}
                @if($review->photos->count() > 0)
                    <div class="flex gap-2 mb-4 flex-wrap">
                        @foreach($review->photos as $photo)
                            <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                 alt="Review photo"
                                 class="w-32 h-32 object-cover rounded-lg hover:scale-105 transition-transform cursor-pointer">
                        @endforeach
                    </div>
                @endif

                {{-- Owner Response --}}
                @if($review->owner_response)
                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border-l-4 border-red-600">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            <span class="font-semibold text-gray-900">
                                {{ app()->getLocale() === 'en' ? 'Response from the owner' : 'Respuesta del dueño' }}
                            </span>
                            @if($review->owner_response_at)
                                <span class="text-sm text-gray-600">• {{ $review->owner_response_at->diffForHumans() }}</span>
                            @endif
                        </div>
                        <p class="text-gray-700">{{ $review->owner_response }}</p>
                    </div>
                @endif

                {{-- Footer: Helpful + Trust Score + Report --}}
                <div class="flex items-center justify-between pt-4 border-t flex-wrap gap-3">
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">
                            {{ app()->getLocale() === 'en' ? 'Was this review helpful?' : '¿Te fue útil esta reseña?' }}
                        </span>
                        <div class="flex gap-2">
                            <button wire:click="voteHelpful({{ $review->id }})"
                                    class="flex items-center gap-1 px-3 py-1.5 rounded-lg border {{ $review->getUserVote()?->is_helpful === true ? 'border-green-500 bg-green-900/30 text-green-400' : 'border-black bg-black text-white hover:bg-gray-900' }} transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                </svg>
                                <span class="text-sm font-medium">
                                    {{ app()->getLocale() === 'en' ? 'Helpful' : 'Útil' }} ({{ $review->helpful_count }})
                                </span>
                            </button>
                            <button wire:click="voteNotHelpful({{ $review->id }})"
                                    class="flex items-center gap-1 px-3 py-1.5 rounded-lg border {{ $review->getUserVote()?->is_helpful === false ? 'border-red-500 bg-red-900/30 text-red-400' : 'border-black bg-black text-white hover:bg-gray-900' }} transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"/>
                                </svg>
                                <span class="text-sm font-medium">
                                    {{ app()->getLocale() === 'en' ? 'Not Helpful' : 'No Útil' }} ({{ $review->not_helpful_count }})
                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- Trust Score Indicator --}}
                    <div class="flex items-center gap-2">
                        @php $trustLevel = $review->trust_level; @endphp
                        <div class="flex items-center gap-1 text-xs text-gray-500">
                            <svg class="w-3.5 h-3.5 {{ $trustLevel === 'high' ? 'text-green-500' : ($trustLevel === 'medium' ? 'text-blue-400' : 'text-gray-400') }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>
                                @if($trustLevel === 'high')
                                    {{ app()->getLocale() === 'en' ? 'High trust' : 'Alta confianza' }}
                                @elseif($trustLevel === 'medium')
                                    {{ app()->getLocale() === 'en' ? 'Verified' : 'Verificada' }}
                                @else
                                    {{ app()->getLocale() === 'en' ? 'Unverified' : 'No verificada' }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    {{ app()->getLocale() === 'en' ? 'No reviews yet' : 'Aún no hay reseñas' }}
                </h3>
                <p class="text-gray-600">
                    {{ app()->getLocale() === 'en' ? 'Be the first to write a review!' : '¡Sé el primero en escribir una reseña!' }}
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($reviews->hasPages())
        <div class="mt-8">
            {{ $reviews->links() }}
        </div>
    @endif
</div>
