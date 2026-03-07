<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <!-- Write Review Button -->
    @if(!$showForm)
        <button wire:click="toggleForm" class="w-full bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            {{ app()->getLocale() === 'en' ? 'Write a Review' : 'Escribir una Reseña' }}
        </button>
    @endif

    <!-- Review Form -->
    @if($showForm)
        <div>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">
                    {{ app()->getLocale() === 'en' ? 'Write Your Review' : 'Escribe tu Reseña' }}
                </h3>
                <button wire:click="toggleForm" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="submitReview" class="space-y-6">
                <!-- Rating Stars -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        {{ app()->getLocale() === 'en' ? 'Your Rating' : 'Tu Calificación' }} *
                    </label>
                    <div class="flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" wire:click="setRating({{ $i }})" class="focus:outline-none transition-transform hover:scale-110">
                                <svg class="w-10 h-10 {{ $rating >= $i ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    @error('rating') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror

                @include('livewire.partials.separate-ratings')
                </div>

                <!-- Title -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        {{ app()->getLocale() === 'en' ? 'Title (Optional)' : 'Título (Opcional)' }}
                    </label>
                    <input type="text" wire:model="title"
                           placeholder="{{ app()->getLocale() === 'en' ? 'Summarize your experience' : 'Resume tu experiencia' }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    @error('title') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Comment -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        {{ app()->getLocale() === 'en' ? 'Your Review' : 'Tu Reseña' }} *
                    </label>
                    <textarea wire:model="comment" rows="6"
                              placeholder="{{ app()->getLocale() === 'en' ? 'Share your experience... What did you like? What could be improved?' : 'Comparte tu experiencia... ¿Qué te gustó? ¿Qué podría mejorar?' }}"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                    <p class="text-sm text-gray-500 mt-1">{{ app()->getLocale() === 'en' ? 'Minimum 10 characters' : 'Mínimo 10 caracteres' }}</p>
                    @error('comment') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Visit Details Row -->
                <div class="grid md:grid-cols-2 gap-4">
                    <!-- Visit Date -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            {{ app()->getLocale() === 'en' ? 'Visit Date (Optional)' : 'Fecha de Visita (Opcional)' }}
                        </label>
                        <input type="date" wire:model="visitDate" max="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        @error('visitDate') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Visit Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            {{ app()->getLocale() === 'en' ? 'Visit Type (Optional)' : 'Tipo de Visita (Opcional)' }}
                        </label>
                        <select wire:model="visitType" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="dine_in">{{ app()->getLocale() === 'en' ? 'Dine In' : 'En el Restaurante' }}</option>
                            <option value="takeout">{{ app()->getLocale() === 'en' ? 'Takeout' : 'Para Llevar' }}</option>
                            <option value="delivery">{{ app()->getLocale() === 'en' ? 'Delivery' : 'Entrega a Domicilio' }}</option>
                        </select>
                        @error('visitType') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Photos Upload -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        {{ app()->getLocale() === 'en' ? 'Add Photos (Optional)' : 'Agregar Fotos (Opcional)' }}
                    </label>
                    <input type="file" wire:model="photos" multiple accept="image/*"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <p class="text-sm text-gray-500 mt-1">{{ app()->getLocale() === 'en' ? 'Max 5MB per photo' : 'Máximo 5MB por foto' }}</p>
                    @error('photos.*') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror

                    @if($photos)
                        <div class="mt-3 flex gap-2 flex-wrap">
                            @foreach($photos as $photo)
                                <div class="relative">
                                    <img src="{{ $photo->temporaryUrl() }}" class="w-20 h-20 object-cover rounded-lg">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Guest Info (if not logged in) -->
                @guest
                    <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                        <h4 class="font-semibold text-gray-900">{{ app()->getLocale() === 'en' ? 'Your Information' : 'Tu Información' }}</h4>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ app()->getLocale() === 'en' ? 'Your Name' : 'Tu Nombre' }} *
                            </label>
                            <input type="text" wire:model="guestName"
                                   placeholder="{{ app()->getLocale() === 'en' ? 'Enter your name' : 'Ingresa tu nombre' }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            @error('guestName') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ app()->getLocale() === 'en' ? 'Your Email' : 'Tu Email' }} *
                            </label>
                            <input type="email" wire:model="guestEmail"
                                   placeholder="{{ app()->getLocale() === 'en' ? 'Enter your email' : 'Ingresa tu email' }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            @error('guestEmail') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endguest

                <!-- Submit Buttons -->
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                        {{ app()->getLocale() === 'en' ? 'Submit Review' : 'Enviar Reseña' }}
                    </button>
                    <button type="button" wire:click="toggleForm" class="px-6 py-3 border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        {{ app()->getLocale() === 'en' ? 'Cancel' : 'Cancelar' }}
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
