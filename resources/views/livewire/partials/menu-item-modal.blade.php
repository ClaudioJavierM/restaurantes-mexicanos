<!-- Modal Overlay -->
<div class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.5);">
    <!-- Background Overlay -->
    <div class="fixed inset-0" wire:click="closeMenuItemModal"></div>

    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-xl shadow-2xl max-w-4xl w-full overflow-hidden">
            <!-- Close Button -->
            <button
                wire:click="closeMenuItemModal"
                class="absolute top-4 right-4 z-10 bg-white/90 hover:bg-white rounded-full p-2 shadow-lg transition-colors"
            >
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-2">
                <!-- Left Side - Image -->
                <div class="relative h-64 md:h-auto bg-gray-200">
                    @if($selectedMenuItem->image)
                        <img
                            src="{{ Storage::url($selectedMenuItem->image) }}"
                            alt="{{ $selectedMenuItem->name }}"
                            class="w-full h-full object-cover"
                        >
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-red-400 to-orange-400 flex items-center justify-center">
                            <svg class="w-32 h-32 text-white opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif

                    <!-- Badges Overlay -->
                    <div class="absolute top-4 left-4 space-y-2">
                        @if($selectedMenuItem->is_popular)
                        <span class="inline-block bg-yellow-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                            ⭐ Popular
                        </span>
                        @endif

                        @if($selectedMenuItem->category)
                        <span class="block bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                            {{ $selectedMenuItem->category->name ?? "" }}
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Right Side - Details -->
                <div class="p-8 overflow-y-auto max-h-[600px]">
                    <!-- Name & Price -->
                    <div class="mb-6">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $selectedMenuItem->name }}</h2>

                        @if($selectedMenuItem->name_en && $selectedMenuItem->name_en !== $selectedMenuItem->name)
                        <p class="text-gray-500 italic mb-3">{{ $selectedMenuItem->name_en }}</p>
                        @endif

                        @if($selectedMenuItem->price)
                        <p class="text-3xl font-bold text-red-600">${{ number_format($selectedMenuItem->price, 2) }}</p>
                        @endif
                    </div>

                    <!-- Spice Indicator -->
                    @if(in_array("spicy", $selectedMenuItem->dietary_tags ?? []))
                    <div class="mb-6">
                        <p class="text-sm font-medium text-gray-700 mb-2">🌶️ Platillo Picante</p>
                    </div>
                    @endif
                    <!-- Description -->
                    @if($selectedMenuItem->description)
                    <div class="mb-6">
                        <p class="text-sm font-medium text-gray-700 mb-2">Descripción:</p>
                        <p class="text-gray-700 leading-relaxed">{{ $selectedMenuItem->description }}</p>

                        @if($selectedMenuItem->description_en && $selectedMenuItem->description_en !== $selectedMenuItem->description)
                        <p class="text-gray-600 italic mt-3 text-sm">{{ $selectedMenuItem->description_en }}</p>
                        @endif
                    </div>
                    @endif

                    <!-- Ingredients -->
                    @if($selectedMenuItem->ingredients && count($selectedMenuItem->ingredients) > 0)
                    <div class="mb-6">
                        <p class="text-sm font-medium text-gray-700 mb-2">Ingredientes:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($selectedMenuItem->ingredients as $ingredient)
                            <span class="inline-block bg-gray-100 text-gray-700 text-xs px-3 py-1.5 rounded-full">
                                {{ $ingredient }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Dietary Options -->
                    @if($selectedMenuItem->dietary_options && count($selectedMenuItem->dietary_options) > 0)
                    <div class="mb-6">
                        <p class="text-sm font-medium text-gray-700 mb-2">Opciones Dietéticas:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($selectedMenuItem->dietary_options as $option)
                            <span class="inline-block bg-green-100 text-green-700 text-xs font-medium px-3 py-1.5 rounded-full">
                                {{ \App\Models\MenuItem::getDietaryOptions()[$option] ?? ucfirst($option) }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Share Buttons -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <p class="text-sm font-medium text-gray-700 mb-3">Compartir este platillo:</p>
                        <div class="flex space-x-3">
                            <!-- WhatsApp -->
                            <a
                                href="https://wa.me/?text={{ urlencode($selectedMenuItem->name . ' - ' . $restaurant->name . ' ' . url()->current()) }}"
                                target="_blank"
                                class="inline-flex items-center justify-center w-10 h-10 bg-green-500 text-white rounded-full hover:bg-green-600 transition-colors"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </a>

                            <!-- Facebook -->
                            <a
                                href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                target="_blank"
                                class="inline-flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>

                            <!-- Twitter -->
                            <a
                                href="https://twitter.com/intent/tweet?text={{ urlencode($selectedMenuItem->name . ' - ' . $restaurant->name) }}&url={{ urlencode(url()->current()) }}"
                                target="_blank"
                                class="inline-flex items-center justify-center w-10 h-10 bg-sky-500 text-white rounded-full hover:bg-sky-600 transition-colors"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Call to Action -->
                    <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-800 text-center">
                            💡 <strong>¿Te antojó?</strong> Llama al restaurante para ordenar:
                        </p>
                        <a
                            href="tel:{{ $restaurant->phone }}"
                            class="mt-3 block w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg text-center transition-colors"
                        >
                            📞 {{ $restaurant->phone }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
