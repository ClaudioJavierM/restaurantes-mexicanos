<div>
    @section('title', __("Categorías de Restaurantes Mexicanos"))
    @section('meta_description', __("Explora todas las categorías de restaurantes mexicanos: tacos, birria, mariscos, carnitas y más."))

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-red-600 to-orange-500 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-display font-bold mb-4">
                {{ app()->getLocale() === 'en' ? 'Explore by Category' : 'Explora por Categoría' }}
            </h1>
            <p class="text-xl text-red-100 max-w-2xl mx-auto">
                {{ app()->getLocale() === 'en' ? 'Find your favorite Mexican food' : 'Encuentra tu comida mexicana favorita' }}
            </p>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}" 
                   class="group bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 p-6 text-center border border-gray-100 hover:border-red-200 hover:-translate-y-1">
                    
                    <!-- Emoji Icon -->
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform">
                        {{ $category->icon ?? '🍽️' }}
                    </div>
                    
                    <!-- Category Name -->
                    <h3 class="font-bold text-gray-900 group-hover:text-red-600 transition-colors text-lg mb-2">
                        {{ $category->name }}
                    </h3>
                    
                    <!-- Restaurant Count -->
                    <p class="text-sm text-gray-500">
                        {{ number_format($category->restaurants_count) }} 
                        {{ $category->restaurants_count === 1 ? 'lugar' : 'lugares' }}
                    </p>
                </a>
            @endforeach
        </div>

        <!-- Empty State -->
        @if($categories->isEmpty())
            <div class="text-center py-16">
                <div class="text-6xl mb-4">🌮</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No hay categorías disponibles</h3>
                <p class="text-gray-600">Pronto agregaremos más categorías.</p>
            </div>
        @endif
    </div>

    <!-- Stats Section -->
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-3xl font-bold text-red-600">{{ $categories->count() }}</div>
                    <div class="text-gray-600">{{ app()->getLocale() === 'en' ? 'Categories' : 'Categorías' }}</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-red-600">{{ number_format($categories->sum('restaurants_count')) }}</div>
                    <div class="text-gray-600">{{ app()->getLocale() === 'en' ? 'Restaurants' : 'Restaurantes' }}</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-red-600">50+</div>
                    <div class="text-gray-600">{{ app()->getLocale() === 'en' ? 'States' : 'Estados' }}</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-red-600">24/7</div>
                    <div class="text-gray-600">{{ app()->getLocale() === 'en' ? 'Online' : 'En Línea' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
