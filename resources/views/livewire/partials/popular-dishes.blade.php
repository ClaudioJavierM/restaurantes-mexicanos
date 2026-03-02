{{-- Popular Dishes Section - Yelp Style --}}
@if(isset($popularMenuItems) && $popularMenuItems && $popularMenuItems->count() > 0)
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Platillos Populares</h2>
            <p class="text-gray-500 text-sm mt-1">Los favoritos de nuestros clientes</p>
        </div>
        @if(isset($menuItems) && $menuItems->count() > 0)
        <button wire:click="switchTab('menu')" class="text-red-600 hover:text-red-700 font-medium text-sm flex items-center">
            Ver menu completo
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        @endif
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($popularMenuItems->take(6) as $item)
        <div wire:click="showMenuItem({{ $item->id }})" 
             class="group cursor-pointer bg-gray-50 rounded-xl overflow-hidden hover:shadow-lg transition-all border border-gray-100 hover:border-red-200">
            {{-- Image --}}
            <div class="relative h-32 overflow-hidden">
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" 
                         alt="{{ $item->name }}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                        <span class="text-4xl">🍽️</span>
                    </div>
                @endif
                
                {{-- Popular Badge --}}
                <div class="absolute top-2 left-2">
                    <span class="inline-flex items-center px-2 py-1 bg-yellow-400 text-yellow-900 text-xs font-bold rounded-full">
                        🔥 Popular
                    </span>
                </div>
                
                {{-- Price Badge --}}
                <div class="absolute bottom-2 right-2">
                    <span class="inline-flex items-center px-2 py-1 bg-white/90 backdrop-blur text-gray-900 text-sm font-bold rounded-lg shadow">
                        {{ $item->display_price }}
                    </span>
                </div>
            </div>
            
            {{-- Content --}}
            <div class="p-4">
                <h3 class="font-bold text-gray-900 group-hover:text-red-600 transition-colors">
                    {{ $item->name }}
                </h3>
                @if($item->description)
                <p class="text-gray-500 text-sm mt-1 line-clamp-2">
                    {{ Str::limit($item->description, 80) }}
                </p>
                @endif
                
                {{-- Dietary Tags --}}
                @php
                    $dietaryTags = is_array($item->dietary_tags) ? $item->dietary_tags : [];
                @endphp
                @if(count($dietaryTags) > 0)
                <div class="flex flex-wrap gap-1 mt-2">
                    @foreach($dietaryTags as $tag)
                        @php
                            $tagIcons = [
                                'vegetarian' => '🥬',
                                'vegan' => '🌱',
                                'gluten-free' => '🌾',
                                'spicy' => '🌶️',
                                'dairy-free' => '🥛',
                            ];
                        @endphp
                        <span class="text-xs px-2 py-0.5 bg-gray-100 rounded-full text-gray-600">
                            {{ $tagIcons[$tag] ?? '' }} {{ ucfirst($tag) }}
                        </span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
