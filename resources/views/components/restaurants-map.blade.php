@props([
    'restaurants' => collect(),
    'userLatitude' => null,
    'userLongitude' => null,
    'height' => '600px'
])

@php
    // Ensure height has a unit if it's just a number
    $heightStyle = is_numeric($height) ? $height . 'px' : $height;

    $apiKey = config('services.google.maps_api_key');

    // Handle both paginated results and regular collections
    $restaurantItems = $restaurants instanceof \Illuminate\Pagination\LengthAwarePaginator
        ? $restaurants->items()
        : $restaurants;

    $restaurantsData = collect($restaurantItems)->map(function($r, $i) {
        return [
            'id' => $r->id,
            'name' => $r->name,
            'lat' => (float) $r->latitude,
            'lng' => (float) $r->longitude,
            'address' => $r->address ?? '',
            'city' => $r->city ?? '',
            'state' => $r->state?->code ?? '',
            'rating' => round($r->getWeightedRating(), 1),
            'slug' => $r->slug,
            'number' => $i + 1,
            'image' => $r->hasMedia('images') ? $r->getFirstMediaUrl('images') : null,
        ];
    })->filter(fn($r) => $r['lat'] && $r['lng'])->values();
    $restaurantsJson = $restaurantsData->toJson();
    $hasRestaurants = $restaurantsData->count() > 0;
@endphp

@if($apiKey && $hasRestaurants)
{{-- Data bridge: Livewire morphs this on every re-render with fresh restaurant JSON --}}
<div id="restaurants-map-data" data-restaurants="{!! htmlspecialchars($restaurantsJson, ENT_QUOTES) !!}" style="display:none;"></div>

<div
    x-data="restaurantsMap()"
    x-init="initMap()"
    @highlight-marker.window="highlightMarker($event.detail.index)"
    @user-location-updated.window="refreshMapForLocation($event.detail.lat, $event.detail.lng)"
    {{ $attributes->merge(['class' => 'rounded-lg overflow-hidden shadow-lg border border-gray-200 bg-gray-100']) }}
    style="height: {{ $heightStyle }};"
>
    <div wire:ignore id="restaurants-map" style="height: 100%; width: 100%;"></div>
</div>

@push('scripts')
<script>
(function() {
    // Only load Google Maps once
    if (!window.googleMapsLoaded) {
        window.googleMapsLoaded = true;
        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key={{ $apiKey }}&callback=initGoogleMaps';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }

    window.initGoogleMaps = function() {
        window.dispatchEvent(new Event('google-maps-loaded'));
    };
})();

function restaurantsMap() {
    return {
        map: null,
        markers: [],
        infoWindow: null,
        restaurants: {!! $restaurantsJson !!},
        userLat: {{ $userLatitude ?? 'null' }},
        userLng: {{ $userLongitude ?? 'null' }},
        initialized: false,
        _pendingMarkerRefresh: false,

        initMap() {
            if (typeof google === 'undefined' || !google.maps) {
                window.addEventListener('google-maps-loaded', () => this.initMap(), { once: true });
                return;
            }

            if (this.initialized) return;
            this.initialized = true;

            // After Livewire morphs the DOM, check if markers need refreshing
            document.addEventListener('livewire:update', () => {
                if (this._pendingMarkerRefresh) {
                    this._pendingMarkerRefresh = false;
                    this.refreshMarkersFromDom();
                }
            });

            // Calculate map center
            let center = { lat: 39.8283, lng: -98.5795 }; // USA center
            let initialZoom = 4;

            if (this.userLat && this.userLng) {
                center = { lat: this.userLat, lng: this.userLng };
                initialZoom = 11;
            } else if (this.restaurants.length > 0) {
                center = { lat: this.restaurants[0].lat, lng: this.restaurants[0].lng };
                initialZoom = 12;
            }

            this.map = new google.maps.Map(document.getElementById('restaurants-map'), {
                zoom: initialZoom,
                center: center,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                zoomControl: true,
                styles: [
                    { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] },
                    { featureType: 'transit', stylers: [{ visibility: 'off' }] }
                ]
            });

            // Single info window instance
            this.infoWindow = new google.maps.InfoWindow();

            // Create bounds
            const bounds = new google.maps.LatLngBounds();

            // Add markers
            this.restaurants.forEach((restaurant, index) => {
                const marker = this.createMarker(restaurant, index);
                bounds.extend(marker.getPosition());
                this.markers.push(marker);
            });

            // Fit bounds only when user location is NOT known.
            // When userLat/userLng are set, keep the map centered on the user —
            // calling fitBounds would drag the center to the geographic midpoint
            // of all markers (e.g. downtown Dallas) instead of the user's position.
            if (this.restaurants.length > 1 && !(this.userLat && this.userLng)) {
                this.map.fitBounds(bounds);
                // Don't zoom too close
                google.maps.event.addListenerOnce(this.map, 'bounds_changed', () => {
                    if (this.map.getZoom() > 15) {
                        this.map.setZoom(15);
                    }
                });
            }

            // Add user location marker if available
            if (this.userLat && this.userLng) {
                new google.maps.Marker({
                    position: { lat: this.userLat, lng: this.userLng },
                    map: this.map,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 8,
                        fillColor: '#3B82F6',
                        fillOpacity: 1,
                        strokeColor: '#1E40AF',
                        strokeWeight: 2,
                    },
                    title: 'Tu ubicacion',
                    zIndex: 1000
                });
            }
        },

        createMarker(restaurant, index) {
            // Create custom marker with number
            const marker = new google.maps.Marker({
                position: { lat: restaurant.lat, lng: restaurant.lng },
                map: this.map,
                label: {
                    text: String(index + 1),
                    color: 'white',
                    fontWeight: 'bold',
                    fontSize: '12px'
                },
                icon: {
                    path: 'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z',
                    fillColor: '#DC2626',
                    fillOpacity: 1,
                    strokeColor: '#991B1B',
                    strokeWeight: 1,
                    scale: 1.5,
                    anchor: new google.maps.Point(12, 24),
                    labelOrigin: new google.maps.Point(12, 9)
                },
                title: restaurant.name,
                zIndex: index
            });

            // Click handler
            marker.addListener('click', () => {
                const restaurantUrl = '/restaurante/' + restaurant.slug;
                const starsHtml = restaurant.rating > 0
                    ? '<div style="font-size:12px;color:#F59E0B;margin-bottom:8px;">' +
                      '★'.repeat(Math.round(restaurant.rating)) + '☆'.repeat(5 - Math.round(restaurant.rating)) +
                      ' <span style="color:#6B7280;">(' + restaurant.rating + ')</span></div>'
                    : '';
                const content = '<div style="max-width:250px;padding:8px;">' +
                    '<div style="font-weight:bold;font-size:14px;margin-bottom:4px;color:#111827;">' + (index + 1) + '. ' + restaurant.name + '</div>' +
                    '<div style="font-size:12px;color:#6B7280;margin-bottom:4px;">' + restaurant.address + (restaurant.city ? ', ' + restaurant.city : '') + (restaurant.state ? ', ' + restaurant.state : '') + '</div>' +
                    starsHtml +
                    '<a href="' + restaurantUrl + '" style="display:inline-block;background:#DC2626;color:white;padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;">Ver restaurante</a>' +
                    '</div>';
                this.infoWindow.setContent(content);
                this.infoWindow.open(this.map, marker);
            });

            return marker;
        },

        refreshMapForLocation(lat, lng) {
            if (!this.map) return;

            const pos = { lat: parseFloat(lat), lng: parseFloat(lng) };

            // Update internal state so any subsequent initMap() call (e.g. Alpine
            // re-init after Livewire morph) also uses the GPS coords.
            this.userLat = pos.lat;
            this.userLng = pos.lng;

            // setCenter + setZoom is immediate; panTo animates and can be
            // interrupted by concurrent Livewire DOM updates.
            this.map.setCenter(pos);
            this.map.setZoom(13);

            // Update or add user location dot immediately
            if (this._userMarker) {
                this._userMarker.setPosition(pos);
            } else {
                this._userMarker = new google.maps.Marker({
                    position: pos,
                    map: this.map,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 8,
                        fillColor: '#3B82F6',
                        fillOpacity: 1,
                        strokeColor: '#1E40AF',
                        strokeWeight: 2,
                    },
                    title: 'Tu ubicacion',
                    zIndex: 1000
                });
            }

            // Markers refresh AFTER Livewire re-renders with nearby restaurants.
            // The event fires before Livewire morphs the DOM, so we wait for
            // livewire:update (dispatched after DOM morphing is complete).
            this._pendingMarkerRefresh = true;
        },

        refreshMarkersFromDom() {
            const dataEl = document.getElementById('restaurants-map-data');
            if (!dataEl) return;
            try {
                const fresh = JSON.parse(dataEl.dataset.restaurants || '[]');
                if (fresh.length > 0 && JSON.stringify(fresh) !== JSON.stringify(this.restaurants)) {
                    this.restaurants = fresh;
                    this.clearMarkers();
                    this.restaurants.forEach((restaurant, index) => {
                        this.markers.push(this.createMarker(restaurant, index));
                    });
                }
            } catch(e) {}
        },

        clearMarkers() {
            this.markers.forEach(m => m.setMap(null));
            this.markers = [];
            if (this.infoWindow) this.infoWindow.close();
        },

        highlightMarker(index) {
            if (this.markers[index]) {
                // Pan to marker
                this.map.panTo(this.markers[index].getPosition());

                // Bounce animation
                this.markers[index].setAnimation(google.maps.Animation.BOUNCE);
                setTimeout(() => {
                    if (this.markers[index]) {
                        this.markers[index].setAnimation(null);
                    }
                }, 750);

                // Change icon color temporarily
                const originalIcon = this.markers[index].getIcon();
                this.markers[index].setIcon({
                    ...originalIcon,
                    fillColor: '#F59E0B',
                    scale: 2
                });
                setTimeout(() => {
                    if (this.markers[index]) {
                        this.markers[index].setIcon(originalIcon);
                    }
                }, 1500);
            }
        }
    }
}
</script>
@endpush
@else
<div {{ $attributes->merge(['class' => 'rounded-lg overflow-hidden shadow-lg border border-gray-200 bg-gray-100 flex items-center justify-center']) }} style="height: {{ $heightStyle }};">
    <div class="text-center p-8 text-gray-500">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <p class="font-medium">Mapa no disponible</p>
        <p class="text-sm mt-1">No hay restaurantes con ubicacion para mostrar</p>
    </div>
</div>
@endif
