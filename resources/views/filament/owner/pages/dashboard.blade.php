<x-filament-panels::page>
    @php
        $restaurant = auth()->user()->restaurants->first();
        $plan = $restaurant->subscription_plan ?? 'free';
        $isPremium = in_array($plan, ['premium', 'elite']);
        
        // Calculate profile completion
        $completionFields = [
            'name' => !empty($restaurant->name),
            'description' => !empty($restaurant->description),
            'address' => !empty($restaurant->address),
            'phone' => !empty($restaurant->phone),
            'website' => !empty($restaurant->website),
            'hours' => !empty($restaurant->hours),
            'logo' => !empty($restaurant->logo),
            'cover_image' => !empty($restaurant->image),
        ];
        $completionPercent = round((count(array_filter($completionFields)) / count($completionFields)) * 100);
        
        // Get stats
        $totalReviews = $restaurant->reviews()->where('status', 'approved')->count();
        $avgRating = $restaurant->reviews()->where('status', 'approved')->avg('rating') ?? 0;
        $pendingResponses = $restaurant->reviews()->where('status', 'approved')->whereNull('response')->count();
        $recentReviews = $restaurant->reviews()->where('status', 'approved')->latest()->take(3)->get();
        $menuItemsCount = $restaurant->menuItems()->count();
        $photosCount = $restaurant->userPhotos()->count();

        // Get visitor stats
        $visitorStats = \Illuminate\Support\Facades\Cache::remember(
            "restaurant_stats_" . $restaurant->id,
            300,
            function () use ($restaurant) {
                $thirtyDaysAgo = now()->subDays(30);
                $totalViews = \App\Models\AnalyticsEvent::where("restaurant_id", $restaurant->id)
                    ->where("event_type", \App\Models\AnalyticsEvent::EVENT_PAGE_VIEW)
                    ->count();
                $monthlyViews = \App\Models\AnalyticsEvent::where("restaurant_id", $restaurant->id)
                    ->where("event_type", \App\Models\AnalyticsEvent::EVENT_PAGE_VIEW)
                    ->where("created_at", ">=", $thirtyDaysAgo)
                    ->count();
                return ["total" => $totalViews, "monthly" => $monthlyViews];
            }
        );
        $adValue = round($visitorStats["total"] * 1.5, 2);

        // Get pending team requests
        $pendingTeamRequests = \App\Models\TeamRequest::where('restaurant_id', $restaurant->id)
            ->where('status', 'pending')
            ->latest()
            ->get();
    @endphp

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        {{-- Welcome Banner --}}
        <div style="background: linear-gradient(135deg, #dc2626 0%, #f97316 100%); border-radius: 1rem; padding: 1.5rem; color: white; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: bold; margin: 0;">Hola, {{ auth()->user()->name }}! 👋</h2>
                    <p style="color: #fed7aa; margin-top: 0.5rem;">Panel de control de <strong>{{ $restaurant->name }}</strong></p>
                </div>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <a href="{{ url('/restaurante/' . $restaurant->slug) }}" target="_blank" 
                       style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 0.5rem; color: white; text-decoration: none; font-size: 0.875rem;">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Ver Perfil
                    </a>
                    <span style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.3); padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600;">
                        @if($plan === 'elite') 🏆 Elite @elseif($plan === 'premium') ⭐ Premium @else 📋 Gratis @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
            <div style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border-radius: 0.75rem; padding: 1.25rem; color: white;">
                <p style="font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; margin: 0;">Resenas</p>
                <p style="font-size: 2rem; font-weight: bold; margin: 0.25rem 0 0 0;">{{ $totalReviews }}</p>
            </div>
            <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 0.75rem; padding: 1.25rem; color: white;">
                <p style="font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; margin: 0;">Calificacion</p>
                <p style="font-size: 2rem; font-weight: bold; margin: 0.25rem 0 0 0;">{{ number_format($avgRating, 1) }} ⭐</p>
            </div>
            <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 0.75rem; padding: 1.25rem; color: white;">
                <p style="font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; margin: 0;">Menu Items</p>
                <p style="font-size: 2rem; font-weight: bold; margin: 0.25rem 0 0 0;">{{ $menuItemsCount }}</p>
            </div>
            <div style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 0.75rem; padding: 1.25rem; color: white;">
                <p style="font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; margin: 0;">Fotos</p>
                <p style="font-size: 2rem; font-weight: bold; margin: 0.25rem 0 0 0;">{{ $photosCount }}</p>
            </div>
        </div>

        {{-- Visitor Stats & Ad Value --}}
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 0.5rem;">
            <div style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border-radius: 0.75rem; padding: 1.25rem; color: white;">
                <p style="font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; margin: 0;">Visitas Este Mes</p>
                <p style="font-size: 2rem; font-weight: bold; margin: 0.25rem 0 0 0;">{{ number_format($visitorStats["monthly"]) }}</p>
            </div>
            <div style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); border-radius: 0.75rem; padding: 1.25rem; color: white;">
                <p style="font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; margin: 0;">Visitas Totales</p>
                <p style="font-size: 2rem; font-weight: bold; margin: 0.25rem 0 0 0;">{{ number_format($visitorStats["total"]) }}</p>
            </div>
            <div style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 0.75rem; padding: 1.25rem; color: white;">
                <p style="font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; margin: 0;">Valor Publicitario</p>
                <p style="font-size: 2rem; font-weight: bold; margin: 0.25rem 0 0 0;">${{ number_format($adValue) }}</p>
                <p style="font-size: 0.625rem; opacity: 0.7; margin: 0.25rem 0 0 0;">Equivalente en Google Ads</p>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #374151;">
            <h3 style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0 0 1rem 0;">Acciones Rapidas</h3>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;">
                <a href="{{ route('filament.owner.resources.my-restaurants.edit', $restaurant) }}" 
                   style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; background-color: #374151; padding: 1rem; border-radius: 0.5rem; text-decoration: none; transition: all 0.2s;">
                    <div style="background: linear-gradient(135deg, #ef4444, #dc2626); width: 3rem; height: 3rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 1.5rem; height: 1.5rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <span style="color: #e5e7eb; font-size: 0.875rem; font-weight: 500;">Editar Info</span>
                </a>
                <a href="{{ route('filament.owner.resources.my-menus.index') }}" 
                   style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; background-color: #374151; padding: 1rem; border-radius: 0.5rem; text-decoration: none;">
                    <div style="background: linear-gradient(135deg, #f97316, #ea580c); width: 3rem; height: 3rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 1.5rem; height: 1.5rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <span style="color: #e5e7eb; font-size: 0.875rem; font-weight: 500;">Menu</span>
                </a>
                <a href="{{ route('filament.owner.resources.my-photos.index') }}" 
                   style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; background-color: #374151; padding: 1rem; border-radius: 0.5rem; text-decoration: none;">
                    <div style="background: linear-gradient(135deg, #ec4899, #db2777); width: 3rem; height: 3rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 1.5rem; height: 1.5rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span style="color: #e5e7eb; font-size: 0.875rem; font-weight: 500;">Fotos</span>
                </a>
                <a href="{{ route('filament.owner.resources.my-reviews.index') }}" 
                   style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; background-color: #374151; padding: 1rem; border-radius: 0.5rem; text-decoration: none; position: relative;">
                    <div style="background: linear-gradient(135deg, #eab308, #ca8a04); width: 3rem; height: 3rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 1.5rem; height: 1.5rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                    <span style="color: #e5e7eb; font-size: 0.875rem; font-weight: 500;">Resenas</span>
                    @if($pendingResponses > 0)
                    <span style="position: absolute; top: 0.5rem; right: 0.5rem; background-color: #ef4444; color: white; font-size: 0.75rem; font-weight: bold; width: 1.25rem; height: 1.25rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center;">{{ $pendingResponses }}</span>
                    @endif
                </a>
            </div>
        </div>

        
        {{-- Certificate Download Section --}}
        <div style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); border-radius: 0.75rem; padding: 1.25rem; border: 2px solid #d4af37;">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 4rem; height: 4rem; background: linear-gradient(135deg, #d4af37, #f4e4a6, #d4af37); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 2rem; height: 2rem; color: #1a1a2e;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1.125rem; font-weight: bold; color: #d4af37; margin: 0;">Certificado FAMER {{ date("Y") }}</h3>
                        <p style="font-size: 0.875rem; color: #94a3b8; margin: 0.25rem 0 0 0;">Descarga tu certificado oficial de Restaurante Mexicano Verificado</p>
                    </div>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="{{ url('/owner/certificate/' . $restaurant->id) }}" target="_blank"
                       style="display: inline-flex; align-items: center; gap: 0.5rem; background-color: #374151; color: #d4af37; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 500; border: 1px solid #d4af37;">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Vista Previa
                    </a>
                    <a href="{{ url('/owner/certificate-pdf/' . $restaurant->id) }}"
                       style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #d4af37, #f4e4a6); color: #1a1a2e; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 600;">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Descargar PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Two Column Layout --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            
            {{-- Left Column --}}
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                {{-- Profile Completion --}}
                <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #374151;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <h3 style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0;">📋 Perfil Completo</h3>
                        <span style="font-size: 1rem; font-weight: bold; color: {{ $completionPercent >= 80 ? '#22c55e' : ($completionPercent >= 50 ? '#eab308' : '#ef4444') }};">{{ $completionPercent }}%</span>
                    </div>
                    <div style="width: 100%; background-color: #374151; border-radius: 9999px; height: 0.5rem; margin-bottom: 0.75rem;">
                        <div style="height: 0.5rem; border-radius: 9999px; background-color: {{ $completionPercent >= 80 ? '#22c55e' : ($completionPercent >= 50 ? '#eab308' : '#ef4444') }}; width: {{ $completionPercent }}%;"></div>
                    </div>
                    @if($completionPercent < 100)
                    <p style="font-size: 0.875rem; color: #9ca3af; margin: 0;">
                        Falta: 
                        @foreach($completionFields as $field => $completed)
                            @if(!$completed)
                                <span style="display: inline-block; background-color: #374151; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; margin-right: 0.25rem;">{{ ucfirst($field) }}</span>
                            @endif
                        @endforeach
                    </p>
                    @else
                    <p style="font-size: 0.875rem; color: #22c55e; margin: 0;">✓ Tu perfil esta completo!</p>
                    @endif
                </div>

                {{-- Recent Reviews --}}
                <div style="background-color: #1f2937; border-radius: 0.75rem; border: 1px solid #374151; overflow: hidden;">
                    <div style="padding: 1rem; border-bottom: 1px solid #374151; display: flex; align-items: center; justify-content: space-between;">
                        <h3 style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0;">💬 Resenas Recientes</h3>
                        <a href="{{ route('filament.owner.resources.my-reviews.index') }}" style="font-size: 0.875rem; color: #818cf8; text-decoration: none;">Ver todas →</a>
                    </div>
                    @forelse($recentReviews as $review)
                    <div style="padding: 1rem; border-bottom: 1px solid #374151;">
                        <div style="display: flex; align-items: start; gap: 0.75rem;">
                            <div style="width: 2.5rem; height: 2.5rem; background-color: #374151; border-radius: 9999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <span style="color: #9ca3af; font-weight: 600;">{{ substr($review->reviewer_name ?? 'A', 0, 1) }}</span>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-weight: 500; color: #ffffff; font-size: 0.875rem;">{{ $review->reviewer_name ?? 'Anonimo' }}</span>
                                    <span style="color: #eab308; font-size: 0.875rem;">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                                </div>
                                <p style="font-size: 0.875rem; color: #9ca3af; margin: 0.25rem 0 0 0; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">{{ $review->comment }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div style="padding: 2rem; text-align: center; color: #6b7280;">
                        <p style="margin: 0;">No hay resenas aun</p>
                    </div>
                    @endforelse
                </div>

                {{-- Team Requests --}}
                @if($pendingTeamRequests->count() > 0)
                <div style="background-color: #1f2937; border-radius: 0.75rem; border: 2px solid #f59e0b; overflow: hidden;">
                    <div style="padding: 1rem; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); display: flex; align-items: center; justify-content: space-between;">
                        <h3 style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0;">👥 Solicitudes de Equipo</h3>
                        <span style="background-color: #ffffff; color: #d97706; font-size: 0.75rem; font-weight: bold; padding: 0.25rem 0.5rem; border-radius: 9999px;">{{ $pendingTeamRequests->count() }} pendiente(s)</span>
                    </div>
                    @foreach($pendingTeamRequests as $request)
                    <div style="padding: 1rem; border-bottom: 1px solid #374151;">
                        <div style="display: flex; align-items: start; gap: 0.75rem;">
                            <div style="width: 2.5rem; height: 2.5rem; {{ $request->request_type === 'ownership_dispute' ? 'background-color: #ef4444;' : 'background-color: #3b82f6;' }} border-radius: 9999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                @if($request->request_type === 'ownership_dispute')
                                <svg style="width: 1.25rem; height: 1.25rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @else
                                <svg style="width: 1.25rem; height: 1.25rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                @endif
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                    <span style="font-weight: 500; color: #ffffff; font-size: 0.875rem;">{{ $request->requester_name }}</span>
                                    @if($request->request_type === 'ownership_dispute')
                                    <span style="background-color: #7f1d1d; color: #fecaca; font-size: 0.625rem; padding: 0.125rem 0.375rem; border-radius: 9999px; font-weight: 600;">DISPUTA</span>
                                    @else
                                    <span style="background-color: #1e3a8a; color: #bfdbfe; font-size: 0.625rem; padding: 0.125rem 0.375rem; border-radius: 9999px; font-weight: 600;">{{ strtoupper(\App\Models\TeamRequest::getRoleLabel($request->requested_role)) }}</span>
                                    @endif
                                </div>
                                <p style="font-size: 0.75rem; color: #9ca3af; margin: 0.25rem 0 0 0;">{{ $request->requester_email }}</p>
                                <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">{{ $request->message }}</p>
                                <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                                    <form action="{{ url('/owner/team-request/' . $request->id . '/approve') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" style="background-color: #22c55e; color: white; font-size: 0.75rem; font-weight: 600; padding: 0.375rem 0.75rem; border-radius: 0.375rem; border: none; cursor: pointer;">
                                            ✓ Aprobar
                                        </button>
                                    </form>
                                    <form action="{{ url('/owner/team-request/' . $request->id . '/reject') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" style="background-color: #ef4444; color: white; font-size: 0.75rem; font-weight: 600; padding: 0.375rem 0.75rem; border-radius: 0.375rem; border: none; cursor: pointer;">
                                            ✗ Rechazar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Right Column --}}
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                {{-- FAMER Benefits --}}
                <div style="background: linear-gradient(135deg, #f97316 0%, #dc2626 100%); border-radius: 0.75rem; padding: 1.25rem;">
                    <h3 style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0 0 0.75rem 0;">🎁 Tus Beneficios FAMER</h3>
                    <p style="font-size: 0.875rem; color: #fed7aa; margin: 0 0 1rem 0;">
                        Como suscriptor {{ ucfirst($plan) }} obtienes 
                        <strong style="color: white;">{{ $plan === 'elite' ? '15%' : ($plan === 'premium' ? '10%' : '5%') }}</strong> 
                        de descuento en todos los negocios afiliados.
                    </p>
                    <a href="{{ url('/owner/my-benefits') }}" 
                       style="display: inline-block; background-color: rgba(255,255,255,0.2); color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                        Ver mis descuentos →
                    </a>
                </div>

                {{-- Tips --}}
                <div style="background-color: #1e3a5f; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #1e40af;">
                    <h3 style="font-size: 1rem; font-weight: 600; color: #93c5fd; margin: 0 0 0.75rem 0;">💡 Consejos para mas visitas</h3>
                    <ul style="margin: 0; padding: 0; list-style: none;">
                        <li style="display: flex; align-items: start; gap: 0.5rem; font-size: 0.875rem; color: #bfdbfe; margin-bottom: 0.5rem;">
                            <span>•</span><span>Agrega fotos de tus platillos mas populares</span>
                        </li>
                        <li style="display: flex; align-items: start; gap: 0.5rem; font-size: 0.875rem; color: #bfdbfe; margin-bottom: 0.5rem;">
                            <span>•</span><span>Responde a todas las resenas (buenas y malas)</span>
                        </li>
                        <li style="display: flex; align-items: start; gap: 0.5rem; font-size: 0.875rem; color: #bfdbfe; margin-bottom: 0.5rem;">
                            <span>•</span><span>Manten tu menu y horarios actualizados</span>
                        </li>
                        <li style="display: flex; align-items: start; gap: 0.5rem; font-size: 0.875rem; color: #bfdbfe;">
                            <span>•</span><span>Pide a clientes satisfechos que dejen resena</span>
                        </li>
                    </ul>
                </div>

                @if($plan === 'free')
                {{-- Upgrade CTA --}}
                <div style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); border-radius: 0.75rem; padding: 1.25rem;">
                    <h3 style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0 0 0.5rem 0;">🚀 Actualiza a Premium</h3>
                    <p style="font-size: 0.875rem; color: #c4b5fd; margin: 0 0 1rem 0;">
                        Analytics, QR para menu, cupones, mas fotos y 10% de descuento FAMER.
                    </p>
                    <a href="{{ url('/owner/upgrade-subscription') }}" 
                       style="display: inline-block; background-color: white; color: #7c3aed; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 600;">
                        Ver Planes →
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Beneficios Exclusivos - Filiales --}}
        <div style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-radius: 0.75rem; padding: 1.5rem; border: 1px solid #fbbf24; margin-top: 1.5rem;">
            <div style="text-align: center; margin-bottom: 1.25rem;">
                <span style="display: inline-block; background-color: #fef3c7; border: 1px solid #f59e0b; color: #b45309; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; margin-bottom: 0.5rem;">Beneficio Exclusivo para Miembros</span>
                <h3 style="font-size: 1.25rem; font-weight: bold; color: #111827; margin: 0.5rem 0;">No olvides aprovechar tus descuentos!</h3>
                <p style="color: #374151; font-size: 0.875rem; max-width: 48rem; margin: 0 auto;">
                    Descuentos exclusivos en muebles, sillas, mesas, booths, platos, decoracion, copas, vasos, 
                    equipo de tortilleria, equipo de paleteria mexicana, food trucks para catering, accesorios 
                    y mas para <span style="color: #dc2626; font-weight: 600;">subir de nivel tu restaurante</span>.
                </p>
            </div>
            
            <div style="border-top: 1px solid #fbbf24; padding-top: 1rem;">
                <p style="text-align: center; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.75rem;">Visita nuestras empresas aliadas:</p>
                
                <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 0.5rem;">
                    <a href="https://mf-imports.com" target="_blank" style="display: inline-flex; align-items: center; padding: 0.5rem 0.75rem; background-color: white; border-radius: 0.5rem; border: 1px solid #e5e7eb; text-decoration: none; color: #374151; font-size: 0.75rem;">MF Imports</a>
                    <a href="https://tormexpro.com" target="_blank" style="display: inline-flex; align-items: center; padding: 0.5rem 0.75rem; background-color: white; border-radius: 0.5rem; border: 1px solid #e5e7eb; text-decoration: none; color: #374151; font-size: 0.75rem;">Tormex Pro</a>
                    <a href="https://mftrailers.com" target="_blank" style="display: inline-flex; align-items: center; padding: 0.5rem 0.75rem; background-color: white; border-radius: 0.5rem; border: 1px solid #e5e7eb; text-decoration: none; color: #374151; font-size: 0.75rem;">MF Trailers</a>
                    <a href="https://refrimexpaleteria.com" target="_blank" style="display: inline-flex; align-items: center; padding: 0.5rem 0.75rem; background-color: white; border-radius: 0.5rem; border: 1px solid #e5e7eb; text-decoration: none; color: #374151; font-size: 0.75rem;">Refrimex</a>
                    <a href="https://muebleyarte.com" target="_blank" style="display: inline-flex; align-items: center; padding: 0.5rem 0.75rem; background-color: white; border-radius: 0.5rem; border: 1px solid #e5e7eb; text-decoration: none; color: #374151; font-size: 0.75rem;">Mueble y Arte</a>
                    <a href="https://decorarmex.com" target="_blank" style="display: inline-flex; align-items: center; padding: 0.5rem 0.75rem; background-color: white; border-radius: 0.5rem; border: 1px solid #e5e7eb; text-decoration: none; color: #374151; font-size: 0.75rem;">Decorarmex</a>
                    <a href="https://mexartandcraft.com" target="_blank" style="display: inline-flex; align-items: center; padding: 0.5rem 0.75rem; background-color: white; border-radius: 0.5rem; border: 1px solid #e5e7eb; text-decoration: none; color: #374151; font-size: 0.75rem;">Mexican Arts</a>
                    <a href="https://mueblesmexicanos.com" target="_blank" style="display: inline-flex; align-items: center; padding: 0.5rem 0.75rem; background-color: white; border-radius: 0.5rem; border: 1px solid #e5e7eb; text-decoration: none; color: #374151; font-size: 0.75rem;">Muebles Mexicanos</a>
                    <a href="https://tododetonala.com" target="_blank" style="display: inline-flex; align-items: center; padding: 0.5rem 0.75rem; background-color: white; border-radius: 0.5rem; border: 1px solid #e5e7eb; text-decoration: none; color: #374151; font-size: 0.75rem;">Todo de Tonala</a>
                </div>
            </div>
        </div>

        {{-- Danger Zone - Unlink Restaurant --}}
        <details style="margin-top: 1.5rem;">
            <summary style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; padding: 1rem; cursor: pointer; list-style: none; display: flex; align-items: center; gap: 0.5rem; color: #991b1b; font-weight: 600;">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                Zona de Peligro
            </summary>
            <div style="background-color: #fef2f2; border: 1px solid #fecaca; border-top: none; border-radius: 0 0 0.5rem 0.5rem; padding: 1.5rem;">
                <h4 style="color: #991b1b; font-weight: 600; margin: 0 0 0.5rem 0;">Desvincular Restaurante de mi Cuenta</h4>
                <p style="color: #7f1d1d; font-size: 0.875rem; margin: 0 0 1rem 0;">
                    Esta accion liberara el restaurante para que otra persona pueda reclamarlo. Perderas acceso a este restaurante y no podras recuperarlo automaticamente.
                </p>
                <form action="{{ url('/owner/restaurant/' . $restaurant->id . '/unlink') }}" method="POST"
                      onsubmit="return confirm('¿Estas seguro de que deseas desvincular {{ $restaurant->name }}? Esta accion no se puede deshacer.');">
                    @csrf
                    <button type="submit" style="background-color: #dc2626; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; border: none; cursor: pointer; font-size: 0.875rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem;">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Desvincular Restaurante
                    </button>
                </form>
            </div>
        </details>

    </div>

        {{-- FAMER Awards Section --}}
        @php
            $monthlyVotes = \App\Models\RestaurantVote::where('restaurant_id', $restaurant->id)
                ->where('month', now()->month)
                ->where('year', now()->year)
                ->count();
            $totalVotes = \App\Models\RestaurantVote::where('restaurant_id', $restaurant->id)->count();

            $cityRanking = \App\Models\RestaurantRanking::where('restaurant_id', $restaurant->id)
                ->where('year', now()->year)
                ->where('ranking_type', 'city')
                ->where('ranking_scope', $restaurant->city)
                ->first();
            $stateRanking = \App\Models\RestaurantRanking::where('restaurant_id', $restaurant->id)
                ->where('year', now()->year)
                ->where('ranking_type', 'state')
                ->first();
            $nationalRanking = \App\Models\RestaurantRanking::where('restaurant_id', $restaurant->id)
                ->where('year', now()->year)
                ->where('ranking_type', 'national')
                ->first();

            $monthlyVotesHistory = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $votes = \App\Models\RestaurantVote::where('restaurant_id', $restaurant->id)
                    ->where('month', $date->month)
                    ->where('year', $date->year)
                    ->count();
                $monthlyVotesHistory[] = ['month' => $date->format('M'), 'votes' => $votes];
            }
            $maxVotes = max(array_column($monthlyVotesHistory, 'votes') ?: [1]);

            $voteUrl = url("/restaurante/{$restaurant->slug}#votar");
            $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=" . urlencode($voteUrl);
            $shareUrl = url("/restaurante/{$restaurant->slug}");
        @endphp

        {{-- FAMER Awards Banner --}}
        <div style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 50%, #d97706 100%); border-radius: 0.75rem; padding: 1.25rem; color: white; border: 2px solid #fbbf24;">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span style="font-size: 2.5rem;">🏆</span>
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: bold; margin: 0;">FAMER Awards {{ date('Y') }}</h3>
                        <p style="font-size: 0.875rem; opacity: 0.9; margin: 0.25rem 0 0 0;">Ranking y votacion de tu restaurante</p>
                    </div>
                </div>
                <div style="text-align: center; background: rgba(0,0,0,0.2); padding: 0.75rem 1.5rem; border-radius: 0.5rem;">
                    <div style="font-size: 2rem; font-weight: bold;">{{ $monthlyVotes }}</div>
                    <div style="font-size: 0.75rem; opacity: 0.8;">Votos este mes</div>
                </div>
            </div>
        </div>

        {{-- FAMER Rankings Grid --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
            <div style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); border-radius: 0.75rem; padding: 1.25rem; color: white; text-align: center;">
                <p style="font-size: 2rem; font-weight: bold; margin: 0;">{{ $cityRanking ? '#' . $cityRanking->rank : '-' }}</p>
                <p style="font-size: 0.75rem; opacity: 0.8; margin: 0.25rem 0 0 0;">Posicion en {{ $restaurant->city }}</p>
            </div>
            <div style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); border-radius: 0.75rem; padding: 1.25rem; color: white; text-align: center;">
                <p style="font-size: 2rem; font-weight: bold; margin: 0;">{{ $stateRanking ? '#' . $stateRanking->rank : '-' }}</p>
                <p style="font-size: 0.75rem; opacity: 0.8; margin: 0.25rem 0 0 0;">Posicion Estatal</p>
            </div>
            <div style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); border-radius: 0.75rem; padding: 1.25rem; color: white; text-align: center;">
                <p style="font-size: 2rem; font-weight: bold; margin: 0;">{{ $nationalRanking ? '#' . $nationalRanking->rank : '-' }}</p>
                <p style="font-size: 0.75rem; opacity: 0.8; margin: 0.25rem 0 0 0;">Posicion Nacional</p>
            </div>
            <div style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); border-radius: 0.75rem; padding: 1.25rem; color: white; text-align: center;">
                <p style="font-size: 2rem; font-weight: bold; margin: 0;">{{ number_format($totalVotes) }}</p>
                <p style="font-size: 0.75rem; opacity: 0.8; margin: 0.25rem 0 0 0;">Votos totales {{ date('Y') }}</p>
            </div>
        </div>

        {{-- Monthly Votes Chart --}}
        <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #374151;">
            <h3 style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0 0 1rem 0;">📊 Historial de Votos</h3>
            <div style="display: flex; align-items: flex-end; justify-content: space-between; height: 120px; gap: 0.5rem;">
                @foreach($monthlyVotesHistory as $data)
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%;">
                    <div style="flex: 1; display: flex; align-items: flex-end; width: 100%;">
                        <div style="width: 100%; background: linear-gradient(180deg, #f59e0b, #d97706); border-radius: 0.25rem 0.25rem 0 0; height: {{ $maxVotes > 0 ? max(8, ($data['votes'] / $maxVotes) * 100) : 8 }}%; min-height: 8px;"></div>
                    </div>
                    <span style="font-size: 0.625rem; color: #9ca3af; margin-top: 0.5rem;">{{ $data['month'] }}</span>
                    <span style="font-size: 0.625rem; color: #f59e0b; font-weight: bold;">{{ $data['votes'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Plan Features Section --}}
        @if(in_array($restaurant->subscription_tier, ['premium', 'elite']))
        <div style="background-color: #1f2937; border-radius: 0.75rem; overflow: hidden; border: 1px solid #374151;">
            <div style="background: linear-gradient(135deg, {{ $restaurant->subscription_tier === 'elite' ? '#b45309, #d97706' : '#dc2626, #ef4444' }}); padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 1.5rem;">{{ $restaurant->subscription_tier === 'elite' ? '🏆' : '⭐' }}</span>
                    <div>
                        <h3 style="font-size: 1rem; font-weight: bold; color: white; margin: 0;">Plan {{ ucfirst($restaurant->subscription_tier) }}</h3>
                        <p style="font-size: 0.75rem; color: rgba(255,255,255,0.8); margin: 0;">Funciones activas de tu suscripcion</p>
                    </div>
                </div>
                <span style="background: rgba(255,255,255,0.2); color: white; font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.75rem; border-radius: 9999px;">Activo</span>
            </div>
            <div style="padding: 1rem 1.25rem;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem;">
                    @foreach(['Aparece en el directorio', 'Info basica editable', 'Integracion Google Maps', 'Verificar propiedad', 'Badge Destacado', 'Top 3 busquedas locales', 'Menu Digital + QR Code', 'Sistema de Reservaciones', 'Dashboard de Analiticas', 'Chatbot AI 24/7'] as $feature)
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background-color: #374151; border-radius: 0.375rem;">
                        <span style="color: #22c55e;">✓</span>
                        <span style="font-size: 0.8125rem; color: #e5e7eb;">{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @else
        {{-- Free Plan Upgrade CTA --}}
        <div style="background-color: #1f2937; border-radius: 0.75rem; overflow: hidden; border: 1px solid #374151;">
            <div style="background: linear-gradient(135deg, #4b5563, #374151); padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 1.5rem;">🔒</span>
                <div>
                    <h3 style="font-size: 1rem; font-weight: bold; color: white; margin: 0;">Plan Gratuito</h3>
                    <p style="font-size: 0.75rem; color: rgba(255,255,255,0.8); margin: 0;">Actualiza para desbloquear mas funciones</p>
                </div>
            </div>
            <div style="padding: 1rem 1.25rem;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem;">
                    @foreach(['Aparece en el directorio', 'Info basica editable', 'Integracion Google Maps', 'Verificar propiedad'] as $feature)
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background-color: #374151; border-radius: 0.375rem;">
                        <span style="color: #22c55e;">✓</span>
                        <span style="font-size: 0.8125rem; color: #e5e7eb;">{{ $feature }}</span>
                    </div>
                    @endforeach
                    @foreach(['Badge Destacado', 'Top 3 busquedas', 'Menu Digital + QR', 'Reservaciones', 'Analiticas', 'Chatbot AI'] as $feature)
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background-color: #374151; border-radius: 0.375rem; opacity: 0.5;">
                        <span style="color: #6b7280;">🔒</span>
                        <span style="font-size: 0.8125rem; color: #6b7280;">{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>
                <div style="text-align: center; margin-top: 1rem;">
                    <a href="/claim" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #dc2626, #ef4444); color: white; padding: 0.625rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem; font-weight: 600;">
                        🚀 Actualizar a Premium
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- QR Code & Social Share --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            {{-- QR Code --}}
            <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #374151;">
                <h3 style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0 0 1rem 0;">📱 QR Code para Votacion</h3>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <img src="{{ $qrCodeUrl }}" alt="QR Code" style="width: 120px; height: 120px; border-radius: 0.5rem; border: 3px solid #f59e0b;">
                    <div>
                        <p style="font-size: 0.8125rem; color: #9ca3af; margin: 0;">Imprime este QR y colocalo en tu restaurante para que tus clientes voten.</p>
                        <a href="{{ url('/owner/qr-print/' . $restaurant->id) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; margin-top: 0.75rem; background-color: #f59e0b; color: #1f2937; padding: 0.5rem 1rem; border-radius: 0.375rem; text-decoration: none; font-size: 0.8125rem; font-weight: 600;">
                            🖨️ Descargar QR
                        </a>
                    </div>
                </div>
            </div>

            {{-- Social Share --}}
            <div style="background: linear-gradient(135deg, #3b82f6 0%, #7c3aed 100%); border-radius: 0.75rem; padding: 1.25rem; color: white;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0 0 0.5rem 0;">📣 Comparte y consigue mas votos!</h3>
                <p style="font-size: 0.8125rem; opacity: 0.8; margin: 0 0 1rem 0;">Invita a tus clientes a votar por tu restaurante</p>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 0.375rem; color: white; text-decoration: none; font-size: 0.8125rem;">
                        Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text=Vota+por+{{ urlencode($restaurant->name) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 0.375rem; color: white; text-decoration: none; font-size: 0.8125rem;">
                        Twitter/X
                    </a>
                    <a href="https://wa.me/?text={{ urlencode('Vota por ' . $restaurant->name . ': ' . $shareUrl) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 0.375rem; color: white; text-decoration: none; font-size: 0.8125rem;">
                        WhatsApp
                    </a>
                </div>
                <p style="font-size: 0.6875rem; opacity: 0.6; margin: 0.75rem 0 0 0;">URL: {{ $shareUrl }}</p>
            </div>
        </div>

</x-filament-panels::page>
