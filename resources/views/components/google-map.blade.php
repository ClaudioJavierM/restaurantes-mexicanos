@props([
    'address' => '',
    'name' => '',
    'latitude' => null,
    'longitude' => null,
    'height' => '450',
    'zoom' => '15',
    'mode' => 'place',
])

@php
    $apiKey = config('services.google.maps_api_key');
    $query = urlencode($name . ', ' . $address);
    
    if ($mode === 'place') {
        $embedUrl = "https://www.google.com/maps/embed/v1/place?key={$apiKey}&q={$query}&zoom={$zoom}";
    } else {
        $embedUrl = "https://www.google.com/maps/embed/v1/search?key={$apiKey}&q={$query}&zoom={$zoom}";
    }
    
    // URLs para direcciones
    $googleMapsUrl = "https://www.google.com/maps/search/?api=1&query=" . $query;
    
    // Apple Maps URL (funciona en iOS/macOS)
    if ($latitude && $longitude) {
        $appleMapsUrl = "https://maps.apple.com/?q=" . $query . "&ll={$latitude},{$longitude}";
    } else {
        $appleMapsUrl = "https://maps.apple.com/?q=" . $query;
    }
@endphp

@if($apiKey)
    <div {{ $attributes->merge(['class' => 'relative rounded-xl overflow-hidden shadow-lg border-2 border-gray-200 hover:border-red-500 transition-all duration-300']) }}>
        <iframe
            width="100%"
            height="{{ $height }}"
            style="border:0"
            loading="lazy"
            allowfullscreen
            referrerpolicy="no-referrer-when-downgrade"
            src="{{ $embedUrl }}"
            title="{{ __('app.get_directions') }} - {{ $name }}">
        </iframe>

        <!-- Smart Directions Button -->
        <div class="absolute bottom-4 right-4" x-data="smartMaps()">
            <!-- Botón principal - cambia según dispositivo -->
            <a :href="primaryUrl"
               target="_blank"
               rel="noopener noreferrer"
               class="flex items-center space-x-2 bg-white hover:bg-red-600 text-gray-800 hover:text-white px-4 py-2 rounded-lg shadow-lg font-semibold text-sm transition-all duration-300 group">
                <!-- Apple Maps Icon -->
                <template x-if="isAppleDevice">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </template>
                <!-- Google Maps Icon -->
                <template x-if="!isAppleDevice">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </template>
                <span x-text="primaryLabel"></span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            
            <!-- Link alternativo -->
            <div class="mt-2 text-center">
                <a :href="secondaryUrl"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="text-xs text-gray-500 hover:text-red-600 underline">
                    <span x-text="secondaryLabel"></span>
                </a>
            </div>
        </div>
    </div>

    <script>
        function smartMaps() {
            return {
                isAppleDevice: false,
                googleUrl: '{{ $googleMapsUrl }}',
                appleUrl: '{{ $appleMapsUrl }}',
                
                init() {
                    // Detectar dispositivo Apple (iOS, iPadOS, macOS)
                    const ua = navigator.userAgent || navigator.vendor || window.opera;
                    this.isAppleDevice = /iPad|iPhone|iPod|Macintosh/.test(ua) || 
                                        (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
                },
                
                get primaryUrl() {
                    return this.isAppleDevice ? this.appleUrl : this.googleUrl;
                },
                
                get secondaryUrl() {
                    return this.isAppleDevice ? this.googleUrl : this.appleUrl;
                },
                
                get primaryLabel() {
                    return this.isAppleDevice ? 'Abrir en Apple Maps' : 'Abrir en Google Maps';
                },
                
                get secondaryLabel() {
                    return this.isAppleDevice ? 'Abrir en Google Maps' : 'Abrir en Apple Maps';
                }
            }
        }
    </script>
@else
    <!-- Fallback sin API key -->
    <div {{ $attributes->merge(['class' => 'relative rounded-xl overflow-hidden shadow-lg border-2 border-gray-200 bg-gray-100']) }}>
        <div class="flex items-center justify-center" style="height: {{ $height }}px">
            <div class="text-center p-8 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
                <p class="text-sm font-semibold mb-4">{{ __('app.map_unavailable') }}</p>
                <div class="flex flex-col space-y-2">
                    <a href="{{ $googleMapsUrl }}" target="_blank" class="text-red-600 hover:text-red-700 font-semibold">
                        Abrir en Google Maps
                    </a>
                    <a href="{{ $appleMapsUrl }}" target="_blank" class="text-gray-600 hover:text-gray-700 text-sm">
                        Abrir en Apple Maps
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif
