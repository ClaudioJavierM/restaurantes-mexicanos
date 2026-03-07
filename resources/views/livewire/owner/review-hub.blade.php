<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Review Hub</h1>
                    <p class="text-gray-600">{{ $restaurant->name }}</p>
                </div>
                <a href="{{ url('/owner') }}" class="text-primary-600 hover:text-primary-700">
                    <i class="fas fa-arrow-left mr-2"></i> Volver al panel
                </a>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex space-x-8">
                <button wire:click="setTab('dashboard')" 
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'dashboard' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-chart-line mr-2"></i> Dashboard
                </button>
                <button wire:click="setTab('reviews')" 
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'reviews' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-star mr-2"></i> Reseñas
                    @if($stats['pending_responses'] > 0)
                        <span class="ml-2 bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded-full">{{ $stats['pending_responses'] }}</span>
                    @endif
                </button>
                <button wire:click="setTab('connections')" 
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'connections' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-plug mr-2"></i> Conexiones
                </button>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Dashboard Tab -->
        @if($activeTab === 'dashboard')
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <i class="fas fa-comments text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Reseñas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_reviews'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <i class="fas fa-star text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Rating Promedio</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['average_rating'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 rounded-lg">
                            <i class="fas fa-clock text-orange-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Sin Responder</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_responses'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-100 rounded-lg">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Negativas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['negative_reviews'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Platform Breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Reseñas por Plataforma</h3>
                    <div class="space-y-4">
                        @foreach(['google' => 'Google', 'facebook' => 'Facebook', 'yelp' => 'Yelp', 'tripadvisor' => 'TripAdvisor'] as $key => $name)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    @if($key === 'google')
                                        <i class="fab fa-google text-red-500 w-6"></i>
                                    @elseif($key === 'facebook')
                                        <i class="fab fa-facebook text-blue-600 w-6"></i>
                                    @elseif($key === 'yelp')
                                        <i class="fab fa-yelp text-[#AF0606] w-6"></i>
                                    @else
                                        <i class="fab fa-tripadvisor text-green-600 w-6"></i>
                                    @endif
                                    <span class="ml-3 text-gray-700">{{ $name }}</span>
                                </div>
                                <span class="font-semibold text-gray-900">{{ $stats['by_platform'][$key] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución de Ratings</h3>
                    <div class="space-y-3">
                        @foreach([5, 4, 3, 2, 1] as $rating)
                            @php
                                $count = $stats['rating_distribution'][$rating];
                                $total = $stats['total_reviews'] ?: 1;
                                $percentage = round(($count / $total) * 100);
                            @endphp
                            <div class="flex items-center">
                                <span class="text-sm w-8">{{ $rating }} <i class="fas fa-star text-yellow-400 text-xs"></i></span>
                                <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600 w-12 text-right">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Recent Reviews -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Reseñas Recientes</h3>
                    <button wire:click="setTab('reviews')" class="text-primary-600 hover:text-primary-700 text-sm">
                        Ver todas <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                </div>
                
                @forelse($reviews->take(5) as $review)
                    <div class="border-b border-gray-100 py-4 last:border-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    @if($review->platform === 'google')
                                        <i class="fab fa-google text-red-500 mr-2"></i>
                                    @elseif($review->platform === 'facebook')
                                        <i class="fab fa-facebook text-blue-600 mr-2"></i>
                                    @elseif($review->platform === 'yelp')
                                        <i class="fab fa-yelp text-[#AF0606] mr-2"></i>
                                    @else
                                        <i class="fab fa-tripadvisor text-green-600 mr-2"></i>
                                    @endif
                                    <span class="font-medium text-gray-900">{{ $review->reviewer_name }}</span>
                                    <div class="ml-2 flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-sm text-gray-500">{{ $review->review_date->diffForHumans() }}</span>
                                </div>
                                <p class="mt-1 text-gray-600 text-sm">{{ Str::limit($review->review_text, 150) }}</p>
                            </div>
                            @if(!$review->owner_response)
                                <button wire:click="openResponseModal({{ $review->id }})" 
                                    class="ml-4 px-3 py-1 bg-primary-100 text-primary-700 rounded-lg text-sm hover:bg-primary-200">
                                    Responder
                                </button>
                            @else
                                <span class="ml-4 px-3 py-1 bg-green-100 text-green-700 rounded-lg text-sm">
                                    <i class="fas fa-check mr-1"></i> Respondida
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">No hay reseñas todavía. Conecta tus plataformas para importar reseñas.</p>
                @endforelse
            </div>
        @endif
        
        <!-- Reviews Tab -->
        @if($activeTab === 'reviews')
            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plataforma</label>
                        <select wire:model.live="platformFilter" class="w-full rounded-lg border-gray-300">
                            <option value="">Todas</option>
                            <option value="google">Google</option>
                            <option value="facebook">Facebook</option>
                            <option value="yelp">Yelp</option>
                            <option value="tripadvisor">TripAdvisor</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                        <select wire:model.live="ratingFilter" class="w-full rounded-lg border-gray-300">
                            <option value="">Todos</option>
                            <option value="positive">Positivas (4-5)</option>
                            <option value="neutral">Neutrales (3)</option>
                            <option value="negative">Negativas (1-2)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select wire:model.live="responseFilter" class="w-full rounded-lg border-gray-300">
                            <option value="">Todos</option>
                            <option value="pending">Sin responder</option>
                            <option value="responded">Respondidas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                        <input type="text" wire:model.live.debounce.300ms="searchQuery" 
                            placeholder="Nombre o contenido..." 
                            class="w-full rounded-lg border-gray-300">
                    </div>
                </div>
            </div>
            
            <!-- Reviews List -->
            <div class="space-y-4">
                @forelse($reviews as $review)
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-start">
                            <!-- Platform Icon -->
                            <div class="flex-shrink-0">
                                @if($review->platform === 'google')
                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                        <i class="fab fa-google text-red-500 text-xl"></i>
                                    </div>
                                @elseif($review->platform === 'facebook')
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fab fa-facebook text-blue-600 text-xl"></i>
                                    </div>
                                @elseif($review->platform === 'yelp')
                                    <div class="w-12 h-12 bg-[#AF0606]/10 rounded-full flex items-center justify-center">
                                        <i class="fab fa-yelp text-[#AF0606] text-xl"></i>
                                    </div>
                                @else
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fab fa-tripadvisor text-green-600 text-xl"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Review Content -->
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $review->reviewer_name }}</h4>
                                        <div class="flex items-center mt-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                            @endfor
                                            <span class="ml-2 text-sm text-gray-500">{{ $review->review_date->format('d M, Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($review->external_url)
                                            <a href="{{ $review->external_url }}" target="_blank" 
                                                class="p-2 text-gray-400 hover:text-gray-600" title="Ver en {{ $review->getPlatformLabel() }}">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                
                                <p class="mt-3 text-gray-700">{{ $review->review_text }}</p>
                                
                                @if($review->owner_response)
                                    <div class="mt-4 bg-gray-50 rounded-lg p-4 border-l-4 border-primary-500">
                                        <div class="flex items-center text-sm text-gray-500 mb-2">
                                            <i class="fas fa-reply mr-2"></i>
                                            Respuesta del propietario
                                            @if($review->response_synced)
                                                <span class="ml-2 text-green-600"><i class="fas fa-check-circle"></i> Sincronizada</span>
                                            @endif
                                        </div>
                                        <p class="text-gray-700">{{ $review->owner_response }}</p>
                                    </div>
                                @endif
                                
                                <div class="mt-4 flex items-center space-x-3">
                                    @if(!$review->owner_response)
                                        <button wire:click="openResponseModal({{ $review->id }})" 
                                            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm">
                                            <i class="fas fa-reply mr-2"></i> Responder
                                        </button>
                                    @else
                                        <button wire:click="openResponseModal({{ $review->id }})" 
                                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm">
                                            <i class="fas fa-edit mr-2"></i> Editar respuesta
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                        <i class="fas fa-comments text-gray-300 text-5xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900">No hay reseñas</h3>
                        <p class="text-gray-500 mt-2">Conecta tus plataformas para comenzar a importar reseñas.</p>
                        <button wire:click="setTab('connections')" class="mt-4 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                            Conectar plataformas
                        </button>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($reviews->hasPages())
                <div class="mt-6">
                    {{ $reviews->links() }}
                </div>
            @endif
        @endif
        
        <!-- Connections Tab -->
        @if($activeTab === 'connections')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Google -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fab fa-google text-red-500 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold text-gray-900">Google Business Profile</h3>
                                <p class="text-sm text-gray-500">Sincroniza reseñas y responde desde aqui</p>
                            </div>
                        </div>
                    </div>
                    
                    @php $googleConnection = $connections->where('platform', 'google')->where('is_active', true)->first(); @endphp
                    
                    @if($googleConnection)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center text-green-700">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span class="font-medium">Conectado</span>
                            </div>
                            <p class="text-sm text-green-600 mt-1">
                                Ultima sincronizacion: {{ $googleConnection->last_sync_at ? $googleConnection->last_sync_at->diffForHumans() : 'Nunca' }}
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            <button wire:click="syncPlatform('google')" class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                                <i class="fas fa-sync mr-2"></i> Sincronizar
                            </button>
                            <button wire:click="disconnectPlatform({{ $googleConnection->id }})" class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50">
                                <i class="fas fa-unlink"></i>
                            </button>
                        </div>
                    @else
                        <button wire:click="connectPlatform('google')" class="w-full px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600">
                            <i class="fab fa-google mr-2"></i> Conectar Google Business
                        </button>
                    @endif
                </div>
                
                <!-- Facebook -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fab fa-facebook text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold text-gray-900">Facebook Page</h3>
                                <p class="text-sm text-gray-500">Recomendaciones y resenas de Facebook</p>
                            </div>
                        </div>
                    </div>
                    
                    @php $facebookConnection = $connections->where('platform', 'facebook')->where('is_active', true)->first(); @endphp
                    
                    @if($facebookConnection)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center text-green-700">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span class="font-medium">Conectado</span>
                            </div>
                            <p class="text-sm text-green-600 mt-1">
                                Ultima sincronizacion: {{ $facebookConnection->last_sync_at ? $facebookConnection->last_sync_at->diffForHumans() : 'Nunca' }}
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            <button wire:click="syncPlatform('facebook')" class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                                <i class="fas fa-sync mr-2"></i> Sincronizar
                            </button>
                            <button wire:click="disconnectPlatform({{ $facebookConnection->id }})" class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50">
                                <i class="fas fa-unlink"></i>
                            </button>
                        </div>
                    @else
                        <button wire:click="connectPlatform('facebook')" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fab fa-facebook mr-2"></i> Conectar Facebook Page
                        </button>
                    @endif
                </div>
                
                <!-- Yelp (Read Only) -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fab fa-yelp text-[#AF0606] text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold text-gray-900">Yelp</h3>
                                <p class="text-sm text-gray-500">Solo lectura - Las respuestas deben hacerse en Yelp</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center text-yellow-700">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span class="text-sm">Yelp se sincroniza automaticamente. No permite respuestas via API.</span>
                        </div>
                    </div>
                </div>
                
                <!-- TripAdvisor (Read Only) -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fab fa-tripadvisor text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold text-gray-900">TripAdvisor</h3>
                                <p class="text-sm text-gray-500">Solo lectura - Las respuestas deben hacerse en TripAdvisor</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center text-yellow-700">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span class="text-sm">TripAdvisor se sincroniza automaticamente. No permite respuestas via API.</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Response Modal -->
    @if($showResponseModal && $selectedReview)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Responder a reseña</h3>
                        <button wire:click="closeResponseModal" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <!-- Original Review -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="flex items-center mb-2">
                            <span class="font-medium text-gray-900">{{ $selectedReview->reviewer_name }}</span>
                            <div class="ml-2 flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-sm {{ $i <= $selectedReview->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <p class="text-gray-700">{{ $selectedReview->review_text }}</p>
                    </div>
                    
                    <!-- Template Selector -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Usar plantilla</label>
                        <div class="flex space-x-2">
                            <select wire:model="selectedTemplate" class="flex-1 rounded-lg border-gray-300">
                                <option value="">Seleccionar plantilla...</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                                @endforeach
                            </select>
                            <button wire:click="applyTemplate" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                Aplicar
                            </button>
                        </div>
                    </div>
                    
                    <!-- Response Text -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tu respuesta</label>
                        <textarea wire:model="responseText" rows="5" 
                            class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Escribe tu respuesta..."></textarea>
                        @error('responseText') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    @if($selectedReview->canRespondViaApi())
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                            <div class="flex items-center text-green-700 text-sm">
                                <i class="fas fa-check-circle mr-2"></i>
                                Esta respuesta se publicara automaticamente en {{ $selectedReview->getPlatformLabel() }}
                            </div>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                            <div class="flex items-center text-yellow-700 text-sm">
                                <i class="fas fa-info-circle mr-2"></i>
                                {{ $selectedReview->getPlatformLabel() }} no permite respuestas via API. La respuesta se guardara solo localmente.
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="p-6 border-t bg-gray-50 flex justify-end space-x-3">
                    <button wire:click="closeResponseModal" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">
                        Cancelar
                    </button>
                    <button wire:click="submitResponse" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        <i class="fas fa-paper-plane mr-2"></i> Enviar respuesta
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
