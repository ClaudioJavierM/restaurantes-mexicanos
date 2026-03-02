<div class="bg-white rounded-lg border border-gray-200 p-6">
    <h3 class="text-xl font-bold text-gray-900 mb-4">Escribe una Reseña</h3>

    @if (session()->has('review_success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('review_success') }}</span>
            </div>
        </div>
    @endif

    <form wire:submit="submit" class="space-y-6">
        <!-- Rating -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Calificación <span class="text-red-600">*</span>
            </label>
            <div class="flex items-center space-x-2">
                @for($i = 1; $i <= 5; $i++)
                    <button
                        type="button"
                        wire:click="$set('rating', {{ $i }})"
                        class="focus:outline-none transition-colors"
                    >
                        <svg class="w-8 h-8 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </button>
                @endfor
                <span class="ml-2 text-sm text-gray-600">{{ $rating }} de 5 estrellas</span>
            </div>
            @error('rating')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                Título de tu reseña <span class="text-red-600">*</span>
            </label>
            <input
                type="text"
                id="title"
                wire:model="title"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                placeholder="Resume tu experiencia en pocas palabras"
            >
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Comment -->
        <div>
            <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">
                Tu reseña <span class="text-red-600">*</span>
            </label>
            <textarea
                id="comment"
                wire:model="comment"
                rows="4"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                placeholder="Cuéntanos sobre tu experiencia: comida, servicio, ambiente, etc."
            ></textarea>
            @error('comment')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Mínimo 10 caracteres</p>
        </div>

        <!-- Guest Info (if not logged in) -->
        @guest
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                <div>
                    <label for="guest_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Tu nombre <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="text"
                        id="guest_name"
                        wire:model="guest_name"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                        placeholder="Tu nombre completo"
                    >
                    @error('guest_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="guest_email" class="block text-sm font-medium text-gray-700 mb-1">
                        Tu email <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="email"
                        id="guest_email"
                        wire:model="guest_email"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                        placeholder="tu@email.com"
                    >
                    @error('guest_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">No será publicado</p>
                </div>
            </div>
        @endguest

        <!-- Submit Button -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                <span class="text-red-600">*</span> Campos requeridos
            </p>
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="submit">Enviar Reseña</span>
                <span wire:loading wire:target="submit">Enviando...</span>
            </button>
        </div>
    </form>

    <!-- Info Notice -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Nota importante</p>
                <p>Tu reseña será revisada antes de ser publicada. Por favor, mantén un lenguaje respetuoso y comparte tu experiencia real en el restaurante.</p>
            </div>
        </div>
    </div>
</div>
