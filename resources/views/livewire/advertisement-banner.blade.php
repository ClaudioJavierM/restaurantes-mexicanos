<div>
    @if($advertisement)
        <a
            href="{{ $advertisement->link_url }}"
            target="_blank"
            wire:click.prevent="trackClick"
            class="block rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow cursor-pointer"
        >
            <!-- Imagen del anuncio -->
            @if($advertisement->getFirstMediaUrl('image'))
                <img
                    src="{{ $advertisement->getFirstMediaUrl('image') }}"
                    alt="{{ $advertisement->title }}"
                    class="w-full h-auto object-cover"
                />
            @else
                <!-- Fallback si no hay imagen -->
                <div class="bg-gradient-to-br from-purple-500 to-pink-500 p-6 text-white text-center">
                    <h3 class="font-bold text-lg mb-2">{{ $advertisement->title }}</h3>
                    @if($advertisement->description)
                        <p class="text-sm text-white/90 mb-3">{{ $advertisement->description }}</p>
                    @endif
                    <span class="inline-block bg-white text-purple-600 px-4 py-2 rounded-md font-semibold text-sm">
                        {{ $advertisement->button_text }}
                    </span>
                </div>
            @endif

            <!-- Overlay con información (opcional, se muestra al hacer hover) -->
            <div class="bg-gray-900/80 text-white p-3 hover:bg-gray-900/90 transition-colors">
                <p class="font-semibold text-sm truncate">{{ $advertisement->title }}</p>
                @if($advertisement->description)
                    <p class="text-xs text-gray-300 truncate">{{ $advertisement->description }}</p>
                @endif
            </div>
        </a>
    @endif
</div>
