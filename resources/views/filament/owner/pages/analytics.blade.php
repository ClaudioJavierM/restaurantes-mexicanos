<x-filament-panels::page>
    <div class="space-y-6">
        @if($restaurant)
            @if(!$isPremium)
            {{-- Premium Lock Overlay --}}
            <div style="position: relative;">
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(17, 24, 39, 0.85); z-index: 50; display: flex; align-items: center; justify-content: center; border-radius: 0.75rem; min-height: 500px;">
                    <div style="text-align: center; padding: 2rem;">
                        <div style="width: 5rem; height: 5rem; background: linear-gradient(135deg, #d4af37, #f4e4a6); border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                            <svg style="width: 2.5rem; height: 2.5rem; color: #1a1a2e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: bold; color: #ffffff; margin-bottom: 0.5rem;">Analytics Premium</h3>
                        <p style="color: #9ca3af; margin-bottom: 1.5rem; max-width: 400px;">
                            Accede a estadisticas detalladas, comparacion con la competencia y reportes de rendimiento actualizando a Premium o Elite.
                        </p>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem; align-items: center;">
                            <a href="{{ url("/owner/upgrade-subscription") }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #d4af37, #f4e4a6); color: #1a1a2e; padding: 0.75rem 2rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600; font-size: 1rem;">
                                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Actualizar Plan
                            </a>
                            <span style="font-size: 0.875rem; color: #6b7280;">Desde $29/mes</span>
                        </div>
                        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #374151;">
                            <p style="font-size: 0.875rem; color: #9ca3af; margin-bottom: 0.75rem;">Con Premium obtendras:</p>
                            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 0.5rem;">
                                <span style="background-color: #374151; color: #d1d5db; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem;">Estadisticas detalladas</span>
                                <span style="background-color: #374151; color: #d1d5db; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem;">Comparacion con competencia</span>
                                <span style="background-color: #374151; color: #d1d5db; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem;">Reportes semanales</span>
                                <span style="background-color: #374151; color: #d1d5db; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem;">Menu QR</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Blurred preview content --}}
                <div style="filter: blur(4px); pointer-events: none; opacity: 0.5;">
            @endif
            
            <!-- Header with Period Selector -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">📊 Analytics de {{ $restaurant->name }}</h2>
                    <p class="text-gray-600 dark:text-gray-400">Métricas de rendimiento de tu restaurante</p>
                </div>
                <select 
                    wire:model.live="period"
                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white"
                >
                    <option value="7">Últimos 7 días</option>
                    <option value="30">Últimos 30 días</option>
                    <option value="90">Últimos 90 días</option>
                </select>
            </div>

            <!-- Main Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Views -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Vistas del Perfil</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['views']['current']) }}</p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-lg">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Phone Clicks -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Clicks al Teléfono</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['phone_clicks']['current']) }}</p>
                        </div>
                        <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-lg">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Website Clicks -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Clicks al Sitio Web</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['website_clicks']['current']) }}</p>
                        </div>
                        <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-lg">
                            <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.083 9h1.946c.089-1.546.383-2.97.837-4.118A6.004 6.004 0 004.083 9zM10 2a8 8 0 100 16 8 8 0 000-16zm0 2c-.076 0-.232.032-.465.262-.238.234-.497.623-.737 1.182-.389.907-.673 2.142-.766 3.556h3.936c-.093-1.414-.377-2.649-.766-3.556-.24-.56-.5-.948-.737-1.182C10.232 4.032 10.076 4 10 4zm3.971 5c-.089-1.546-.383-2.97-.837-4.118A6.004 6.004 0 0115.917 9h-1.946zm-2.003 2H8.032c.093 1.414.377 2.649.766 3.556.24.56.5.948.737 1.182.233.23.389.262.465.262.076 0 .232-.032.465-.262.238-.234.498-.623.737-1.182.389-.907.673-2.142.766-3.556zm1.166 4.118c.454-1.147.748-2.572.837-4.118h1.946a6.004 6.004 0 01-2.783 4.118zm-6.268 0C6.412 13.97 6.118 12.546 6.03 11H4.083a6.004 6.004 0 002.783 4.118z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Reviews -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nuevas Reseñas</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['reviews']['current'] }}</p>
                            @if($stats['reviews']['change'] != 0)
                                <p class="text-sm {{ $stats['reviews']['change'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $stats['reviews']['change'] > 0 ? '+'.'':''.'' }}{{ $stats['reviews']['change'] }}% vs período anterior
                                </p>
                            @endif
                        </div>
                        <div class="bg-yellow-100 dark:bg-yellow-900/30 p-3 rounded-lg">
                            <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating Distribution and Average -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Average Rating -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">⭐ Calificación Promedio</h3>
                    <div class="flex items-center justify-center py-8">
                        <div class="text-center">
                            <div class="text-6xl font-bold text-gray-900 dark:text-white">{{ $stats['avg_rating'] }}</div>
                            <div class="text-2xl text-yellow-500 mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($stats['avg_rating']))
                                        ⭐
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 mt-2">Basado en {{ $stats['total_reviews'] }} reseñas</p>
                        </div>
                    </div>
                </div>

                <!-- Rating Distribution -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📊 Distribución de Calificaciones</h3>
                    <div class="space-y-3">
                        @for($rating = 5; $rating >= 1; $rating--)
                            @php
                                $count = $stats['reviews_by_rating'][$rating] ?? 0;
                                $total = $stats['total_reviews'] ?: 1;
                                $percentage = round(($count / $total) * 100);
                            @endphp
                            <div class="flex items-center">
                                <span class="w-12 text-sm text-gray-600 dark:text-gray-400">{{ $rating }} ⭐</span>
                                <div style="flex: 1; margin: 0 0.75rem; background-color: #374151; border-radius: 9999px; height: 1rem;">
                                    <div style="background-color: #eab308; height: 1rem; border-radius: 9999px; transition: all 0.5s; width: {{ $percentage }}%"></div>
                                </div>
                                <span class="w-16 text-sm text-gray-600 dark:text-gray-400 text-right">{{ $count }} ({{ $percentage }}%)</span>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Actions Summary -->
            <div style="background: linear-gradient(135deg, #1e1b4b, #312e81); border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                <h3 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 1rem; color: #ffffff;">Resumen de Engagement</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div style="text-align: center;">
                        <div style="font-size: 2.25rem; font-weight: bold; color: #ffffff;">{{ number_format($stats['views']['current']) }}</div>
                        <div style="color: #c7d2fe;">Personas vieron tu perfil</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 2.25rem; font-weight: bold; color: #ffffff;">{{ number_format($stats['phone_clicks']['current'] + $stats['website_clicks']['current']) }}</div>
                        <div style="color: #c7d2fe;">Interacciones totales</div>
                    </div>
                    <div style="text-align: center;">
                        @php
                            $conversionRate = $stats['views']['current'] > 0
                                ? round((($stats['phone_clicks']['current'] + $stats['website_clicks']['current']) / $stats['views']['current']) * 100, 1)
                                : 0;
                        @endphp
                        <div style="font-size: 2.25rem; font-weight: bold; color: #ffffff;">{{ $conversionRate }}%</div>
                        <div style="color: #c7d2fe;">Tasa de conversion</div>
                    </div>
                </div>
            </div>

            
            {{-- Competition Comparison Section --}}
            @if(!empty($comparison) && !isset($comparison["no_data"]))
            <div style="background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%); border-radius: 0.75rem; padding: 1.5rem; border: 1px solid #1e40af; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: bold; color: #ffffff; margin: 0;">📊 Comparacion con Competencia</h3>
                        <p style="font-size: 0.875rem; color: #94a3b8; margin: 0.25rem 0 0 0;">
                            Tu restaurante vs {{ $comparison["local_count"] }} restaurantes en {{ $comparison["city"] }}
                        </p>
                    </div>
                    <div style="background: linear-gradient(135deg, #22c55e, #16a34a); padding: 0.5rem 1rem; border-radius: 0.5rem;">
                        <span style="color: white; font-size: 0.75rem; font-weight: 600;">EN VIVO</span>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem;">
                    @foreach($comparison["metrics"] as $metric)
                    <div style="background-color: rgba(255,255,255,0.05); border-radius: 0.75rem; padding: 1rem; text-align: center;">
                        <div style="display: flex; justify-content: center; margin-bottom: 0.5rem;">
                            @if($metric["icon"] === "star")
                                <svg style="width: 1.5rem; height: 1.5rem; color: #eab308;" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            @elseif($metric["icon"] === "chat-bubble-left")
                                <svg style="width: 1.5rem; height: 1.5rem; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            @elseif($metric["icon"] === "eye")
                                <svg style="width: 1.5rem; height: 1.5rem; color: #22c55e;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            @elseif($metric["icon"] === "clipboard-document-list")
                                <svg style="width: 1.5rem; height: 1.5rem; color: #f97316;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            @elseif($metric["icon"] === "photo")
                                <svg style="width: 1.5rem; height: 1.5rem; color: #ec4899;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @endif
                        </div>
                        <p style="font-size: 0.75rem; color: #94a3b8; margin: 0 0 0.5rem 0;">{{ $metric["name"] }}</p>
                        <p style="font-size: 1.5rem; font-weight: bold; color: #ffffff; margin: 0;">{{ $metric["your_value"] }}{{ $metric["unit"] }}</p>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.25rem; margin-top: 0.5rem;">
                            @if($metric["is_better"])
                                <svg style="width: 1rem; height: 1rem; color: #22c55e;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                <span style="font-size: 0.75rem; color: #22c55e;">+{{ abs($metric["difference"]) }} vs local</span>
                            @else
                                <svg style="width: 1rem; height: 1rem; color: #ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                <span style="font-size: 0.75rem; color: #ef4444;">{{ $metric["difference"] }} vs local</span>
                            @endif
                        </div>
                        <p style="font-size: 0.625rem; color: #64748b; margin: 0.25rem 0 0 0;">Promedio: {{ $metric["local_avg"] }}{{ $metric["unit"] }}</p>
                        @if(isset($metric["percentile"]))
                        <div style="margin-top: 0.5rem; background-color: rgba(255,255,255,0.1); border-radius: 9999px; padding: 0.25rem 0.5rem;">
                            <span style="font-size: 0.625rem; color: {{ $metric["percentile"] >= 50 ? "#22c55e" : "#f97316" }}; font-weight: 600;">
                                Top {{ 100 - $metric["percentile"] }}%
                            </span>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                
                {{-- Summary --}}
                @php
                    $betterCount = collect($comparison["metrics"])->filter(fn($m) => $m["is_better"])->count();
                    $totalMetrics = count($comparison["metrics"]);
                @endphp
                <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            @if($betterCount >= 4)
                                <span style="font-size: 1.5rem;">🏆</span>
                                <span style="color: #22c55e; font-weight: 600;">Excelente! Superas el promedio en {{ $betterCount }}/{{ $totalMetrics }} metricas</span>
                            @elseif($betterCount >= 3)
                                <span style="font-size: 1.5rem;">👍</span>
                                <span style="color: #3b82f6; font-weight: 600;">Buen trabajo! Superas el promedio en {{ $betterCount }}/{{ $totalMetrics }} metricas</span>
                            @else
                                <span style="font-size: 1.5rem;">💪</span>
                                <span style="color: #f97316; font-weight: 600;">Hay oportunidad de mejorar en algunas areas</span>
                            @endif
                        </div>
                        <a href="{{ url("/owner/upgrade-subscription") }}" style="background: linear-gradient(135deg, #7c3aed, #4f46e5); color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                            Mejorar Visibilidad →
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tips -->
            <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                <h3 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">💡 Consejos para Mejorar tus Métricas</h3>
                <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                    <li>• Responde a todas las reseñas para mejorar tu engagement</li>
                    <li>• Mantén actualizada la información de tu restaurante</li>
                    <li>• Sube fotos de alta calidad de tus platillos</li>
                    <li>• Comparte el código QR de tu menú con tus clientes</li>
                </ul>
            </div>
        @if(!$isPremium)
                </div>
            </div>
            @endif
        @else
            <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 text-center">
                <p class="text-yellow-800 dark:text-yellow-200">No tienes un restaurante asociado.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
