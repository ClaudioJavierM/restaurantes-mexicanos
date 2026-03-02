{{-- What's the Vibe / Amenities Section - Yelp Style --}}
@php
    // Safely get array values (some might be stored as strings)
    $transactions = is_array($restaurant->yelp_transactions) ? $restaurant->yelp_transactions : [];
    $specialFeatures = is_array($restaurant->special_features) ? $restaurant->special_features : [];
    $atmosphere = is_array($restaurant->atmosphere) ? $restaurant->atmosphere : [];
    $amenities = is_array($restaurant->amenities) ? $restaurant->amenities : [];
    
    // Define all possible amenities with icons
    $allAmenities = [
        // From Yelp transactions
        'delivery' => ['icon' => '🚗', 'label' => 'Delivery', 'positive' => in_array('delivery', $transactions)],
        'pickup' => ['icon' => '📦', 'label' => 'Para Llevar', 'positive' => in_array('pickup', $transactions)],
        'restaurant_reservation' => ['icon' => '📅', 'label' => 'Reservaciones', 'positive' => in_array('restaurant_reservation', $transactions) || $restaurant->accepts_reservations],
        
        // From special_features
        'outdoor_patio' => ['icon' => '🌳', 'label' => 'Patio al Aire Libre', 'positive' => in_array('outdoor_patio', $specialFeatures)],
        'live_music' => ['icon' => '🎵', 'label' => 'Musica en Vivo', 'positive' => in_array('live_music', $specialFeatures)],
        'mariachi' => ['icon' => '🎺', 'label' => 'Mariachi', 'positive' => in_array('mariachi', $specialFeatures)],
        'full_bar' => ['icon' => '🍹', 'label' => 'Bar Completo', 'positive' => in_array('full_bar', $specialFeatures)],
        'wifi' => ['icon' => '📶', 'label' => 'WiFi Gratis', 'positive' => in_array('wifi', $specialFeatures)],
        'parking' => ['icon' => '🅿️', 'label' => 'Estacionamiento', 'positive' => in_array('parking', $specialFeatures)],
        'catering' => ['icon' => '🍴', 'label' => 'Catering', 'positive' => in_array('catering', $specialFeatures)],
        'private_events' => ['icon' => '🎉', 'label' => 'Eventos Privados', 'positive' => in_array('private_events', $specialFeatures)],
        
        // From atmosphere
        'family_friendly' => ['icon' => '👨‍👩‍👧‍👦', 'label' => 'Familiar', 'positive' => in_array('family_friendly', $atmosphere)],
        'romantic' => ['icon' => '💕', 'label' => 'Romantico', 'positive' => in_array('romantic', $atmosphere)],
        'casual' => ['icon' => '😊', 'label' => 'Casual', 'positive' => in_array('casual', $atmosphere)],
        
        // Additional amenities
        'fresh_tortillas' => ['icon' => '🫓', 'label' => 'Tortillas Frescas', 'positive' => $restaurant->has_fresh_tortillas ?? false],
        'cafe_de_olla' => ['icon' => '☕', 'label' => 'Cafe de Olla', 'positive' => $restaurant->has_cafe_de_olla ?? false],
        'online_ordering' => ['icon' => '📱', 'label' => 'Pedidos Online', 'positive' => $restaurant->online_ordering ?? false],
    ];
    
    // Filter to only show amenities that are available
    $activeAmenities = collect($allAmenities)->filter(fn($a) => $a['positive'])->all();
    
    // Get price range
    $priceRange = $restaurant->price_range ?? '40932';
    $priceLabels = [
        '$' => 'Economico',
        '40932' => 'Moderado',
        '40932$' => 'Caro',
        '4093240932' => 'Muy Caro',
    ];
@endphp

@if(count($activeAmenities) > 0 || $priceRange)
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Sobre este Restaurante</h2>
    
    {{-- Price & Basic Info --}}
    <div class="flex flex-wrap items-center gap-4 mb-6 pb-6 border-b border-gray-200">
        @if($priceRange)
        <div class="flex items-center bg-gray-100 rounded-full px-4 py-2">
            <span class="font-bold text-green-600 mr-2">{{ $priceRange }}</span>
            <span class="text-gray-600 text-sm">{{ $priceLabels[$priceRange] ?? 'Moderado' }}</span>
        </div>
        @endif
        
        @if($restaurant->category)
        <div class="flex items-center bg-red-50 rounded-full px-4 py-2">
            <span class="text-red-600">🌮</span>
            <span class="text-gray-700 text-sm ml-2">{{ $restaurant->category->name }}</span>
        </div>
        @endif
        
        @if($restaurant->menu_url)
        <a href="{{ $restaurant->menu_url }}" target="_blank" 
           class="flex items-center bg-blue-50 hover:bg-blue-100 rounded-full px-4 py-2 transition-colors">
            <span>📋</span>
            <span class="text-blue-600 text-sm ml-2 font-medium">Ver Menu</span>
            <svg class="w-3 h-3 ml-1 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
        </a>
        @endif
    </div>
    
    {{-- Amenities Grid --}}
    @if(count($activeAmenities) > 0)
    <div>
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Servicios y Amenidades</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($activeAmenities as $key => $amenity)
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-xl mr-3">{{ $amenity['icon'] }}</span>
                <span class="text-gray-700 text-sm font-medium">{{ $amenity['label'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    
    {{-- Hours Section --}}
    @php
        $hours = is_array($restaurant->hours) ? $restaurant->hours : [];
    @endphp
    @if(count($hours) > 0)
    <div class="mt-6 pt-6 border-t border-gray-200">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Horario</h3>
        @php
            $dayNames = [
                'monday' => 'Lunes',
                'tuesday' => 'Martes',
                'wednesday' => 'Miercoles',
                'thursday' => 'Jueves',
                'friday' => 'Viernes',
                'saturday' => 'Sabado',
                'sunday' => 'Domingo',
            ];
            $today = strtolower(now()->format('l'));
        @endphp
        <div class="space-y-2">
            @foreach($dayNames as $dayKey => $dayLabel)
                @php
                    $dayHours = $hours[$dayKey] ?? null;
                    $isToday = $today === $dayKey;
                @endphp
                <div class="flex justify-between items-center py-2 {{ $isToday ? 'bg-red-50 -mx-2 px-2 rounded-lg' : '' }}">
                    <span class="text-gray-700 {{ $isToday ? 'font-bold' : '' }}">
                        {{ $dayLabel }}
                        @if($isToday)
                        <span class="text-red-600 text-xs ml-1">(Hoy)</span>
                        @endif
                    </span>
                    <span class="text-gray-600 {{ $isToday ? 'font-semibold' : '' }}">
                        @if($dayHours && is_array($dayHours) && !($dayHours['closed'] ?? false))
                            {{ $dayHours['open'] ?? '?' }} - {{ $dayHours['close'] ?? '?' }}
                        @else
                            <span class="text-gray-400">Cerrado</span>
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif
