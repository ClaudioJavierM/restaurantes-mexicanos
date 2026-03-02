<div>
    <!-- Photo Gallery Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Fotos</h2>
                <p class="text-gray-600">{{ $photosCount }} {{ $photosCount === 1 ? 'foto' : 'fotos' }} de este restaurante</p>
            </div>
            <button
                wire:click="toggleUploadForm"
                class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Agregar Fotos
            </button>
        </div>
    </div>

    <!-- Success Message -->
    @if(session()->has('photo-success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-green-700">{{ session('photo-success') }}</p>
            </div>
        </div>
    @endif

    <!-- Error Message -->
    @if(session()->has('photo-error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <p class="text-red-700">{{ session('photo-error') }}</p>
            </div>
        </div>
    @endif

    <!-- Upload Form -->
    @if($showUploadForm)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Subir Fotos</h3>

            <form wire:submit="uploadPhotos">
                <!-- Photo Upload -->
                <div class="mb-4"
                     x-data="{
                         isDragging: false,
                         previews: [],
                         handleDrop(e) {
                             this.isDragging = false;
                             const files = e.dataTransfer.files;
                             if (files.length > 0) {
                                 const input = document.getElementById('photo-upload');
                                 const dataTransfer = new DataTransfer();
                                 this.previews = [];
                                 for (let i = 0; i < files.length; i++) {
                                     if (files[i].type.startsWith('image/')) {
                                         dataTransfer.items.add(files[i]);
                                         this.previews.push(URL.createObjectURL(files[i]));
                                     }
                                 }
                                 input.files = dataTransfer.files;
                                 input.dispatchEvent(new Event('change', { bubbles: true }));
                             }
                         },
                         handleFileSelect(e) {
                             const files = e.target.files;
                             this.previews = [];
                             for (let i = 0; i < files.length; i++) {
                                 if (files[i].type.startsWith('image/')) {
                                     this.previews.push(URL.createObjectURL(files[i]));
                                 }
                             }
                         }
                     }"
                >
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Seleccionar fotos (max 10MB cada una)
                    </label>
                    <div
                        class="border-2 border-dashed rounded-lg p-8 text-center transition-all duration-200 cursor-pointer"
                        :class="isDragging ? 'border-red-500 bg-red-50 scale-[1.02]' : 'border-gray-300 hover:border-red-400 bg-white'"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop($event)"
                        @click="$refs.fileInput.click()"
                    >
                        <input
                            type="file"
                            wire:model.live="photos"
                            multiple
                            accept="image/png,image/jpeg,image/jpg,image/webp"
                            class="hidden"
                            id="photo-upload"
                            x-ref="fileInput"
                            @change="handleFileSelect($event)"
                        >
                        <div class="pointer-events-none">
                            <svg class="mx-auto h-12 w-12 text-gray-400" :class="isDragging ? 'text-red-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-sm" :class="isDragging ? 'text-red-600 font-medium' : 'text-gray-600'">
                                <span x-show="!isDragging">Haz clic para seleccionar fotos o arrastra y suelta</span>
                                <span x-show="isDragging">Suelta las fotos aqui</span>
                            </p>
                            <p class="mt-1 text-xs text-gray-500">PNG, JPG, WEBP hasta 10MB</p>
                        </div>
                    </div>

                    <!-- Loading indicator -->
                    <div wire:loading wire:target="photos" class="mt-3 flex items-center justify-center gap-2 text-gray-600">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Cargando fotos...</span>
                    </div>

                    @error('photos.*') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror

                    <!-- Preview using Alpine.js local URLs -->
                    <template x-if="previews.length > 0">
                        <div>
                            <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3" wire:loading.remove wire:target="photos">
                                <template x-for="(preview, index) in previews" :key="index">
                                    <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-100 shadow-sm">
                                        <img :src="preview" class="w-full h-full object-cover" :alt="'Preview ' + (index + 1)">
                                        <div class="absolute top-1 left-1 bg-black/60 text-white text-xs px-2 py-0.5 rounded" x-text="index + 1"></div>
                                    </div>
                                </template>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">
                                <span x-text="previews.length"></span>
                                <span x-text="previews.length === 1 ? 'foto seleccionada' : 'fotos seleccionadas'"></span>
                            </p>
                        </div>
                    </template>
                </div>

                <!-- Photo Type -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de foto</label>
                    <select
                        wire:model="photoType"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    >
                        @foreach($photoTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Caption -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripcion (opcional)</label>
                    <textarea
                        wire:model="caption"
                        rows="2"
                        maxlength="500"
                        placeholder="Agrega una descripcion para tus fotos..."
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    ></textarea>
                    @error('caption') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        wire:click="toggleUploadForm"
                        class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="uploadPhotos">Subir Fotos</span>
                        <span wire:loading wire:target="uploadPhotos">Subiendo...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Filters -->
    @if($photosCount > 0)
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="flex flex-wrap gap-2">
                <button
                    wire:click="filterByType('all')"
                    class="px-3 py-1.5 rounded-full text-sm font-medium transition-colors {{ $filterType === 'all' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    Todas
                </button>
                @foreach($photoTypes as $value => $label)
                    <button
                        wire:click="filterByType('{{ $value }}')"
                        class="px-3 py-1.5 rounded-full text-sm font-medium transition-colors {{ $filterType === $value ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Photo Grid -->
    @if($photosCount > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            {{-- Yelp Photos --}}
            @foreach($yelpPhotos as $index => $yelpPhoto)
                <a
                    href="{{ $yelpPhoto }}"
                    target="_blank"
                    class="relative aspect-square rounded-lg overflow-hidden bg-gray-100 cursor-pointer group"
                >
                    <img
                        src="{{ $yelpPhoto }}"
                        alt="Foto de Yelp {{ $index + 1 }}"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                        loading="lazy"
                    >
                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors">
                        <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="flex items-center justify-end text-white text-xs">
                                <span class="px-2 py-0.5 bg-red-600 rounded-full flex items-center gap-1">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M20.16 12.594l-4.995 1.433c-.96.276-1.74-.8-1.176-1.63l2.905-4.308a1.072 1.072 0 011.596-.206 9.194 9.194 0 011.67 4.711z"/>
                                    </svg>
                                    Yelp
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach

            {{-- User-uploaded Photos --}}
            @foreach($galleryPhotos as $photo)
                <div
                    wire:click="openLightbox({{ $photo->id }})"
                    class="relative aspect-square rounded-lg overflow-hidden bg-gray-100 cursor-pointer group"
                >
                    <img
                        src="{{ $photo->getThumbnailUrl() }}"
                        alt="{{ $photo->caption ?: 'Foto del restaurante' }}"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                        loading="lazy"
                    >
                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors">
                        <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="flex items-center justify-between text-white text-xs">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $photo->likes_count }}
                                </span>
                                <span class="px-2 py-0.5 bg-white/20 rounded-full">
                                    {{ $photo->getPhotoTypeLabel() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Owner badge -->
                    @if($photo->user_id === $restaurant->user_id)
                        <div class="absolute top-2 left-2">
                            <span class="px-2 py-1 bg-yellow-500 text-white text-xs font-semibold rounded-full">
                                Del Dueno
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No hay fotos aun</h3>
            <p class="mt-2 text-gray-500">Se el primero en compartir fotos de este restaurante.</p>
            <button
                wire:click="toggleUploadForm"
                class="mt-4 inline-flex items-center px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Agregar la primera foto
            </button>
        </div>
    @endif

    <!-- Lightbox Modal -->
    @if($showLightbox && $selectedPhoto)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
            wire:click.self="closeLightbox"
        >
            <!-- Close button -->
            <button
                wire:click="closeLightbox"
                class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-10"
            >
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Navigation arrows -->
            <button
                wire:click="previousPhoto"
                class="absolute left-4 text-white hover:text-gray-300 transition-colors"
            >
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <button
                wire:click="nextPhoto"
                class="absolute right-4 text-white hover:text-gray-300 transition-colors"
            >
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Photo container -->
            <div class="max-w-5xl max-h-[85vh] px-16">
                <img
                    src="{{ $selectedPhoto->getPhotoUrl() }}"
                    alt="{{ $selectedPhoto->caption ?: 'Foto del restaurante' }}"
                    class="max-w-full max-h-[75vh] object-contain rounded-lg"
                >

                <!-- Photo info -->
                <div class="mt-4 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <!-- Like button -->
                            <button
                                wire:click="toggleLike({{ $selectedPhoto->id }})"
                                class="flex items-center gap-1 hover:text-red-400 transition-colors {{ $selectedPhoto->isLikedBy(auth()->user()) ? 'text-red-500' : '' }}"
                            >
                                <svg class="w-6 h-6" fill="{{ $selectedPhoto->isLikedBy(auth()->user()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span>{{ $selectedPhoto->likes_count }}</span>
                            </button>

                            <!-- Views -->
                            <span class="flex items-center gap-1 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                {{ $selectedPhoto->views_count }}
                            </span>
                        </div>

                        <div class="flex items-center gap-4">
                            <!-- Type badge -->
                            <span class="px-3 py-1 bg-white/20 rounded-full text-sm">
                                {{ $selectedPhoto->getPhotoTypeLabel() }}
                            </span>

                            <!-- Report button -->
                            @auth
                                @if($selectedPhoto->user_id !== auth()->id())
                                    <button
                                        wire:click="openReportModal({{ $selectedPhoto->id }})"
                                        class="flex items-center gap-1 text-gray-400 hover:text-red-400 transition-colors text-sm"
                                        title="Reportar foto"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                                        </svg>
                                        Reportar
                                    </button>
                                @endif
                            @endauth

                            <!-- User info -->
                            @if($selectedPhoto->user)
                                <span class="text-sm text-gray-400">
                                    Por {{ $selectedPhoto->user->name }}
                                    @if($selectedPhoto->user_id === $restaurant->user_id)
                                        <span class="ml-1 px-2 py-0.5 bg-yellow-500 text-white text-xs rounded-full">Dueno</span>
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($selectedPhoto->caption)
                        <p class="mt-2 text-gray-300">{{ $selectedPhoto->caption }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Report Modal -->
    @if($showReportModal)
        <div
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/70"
            wire:click.self="closeReportModal"
        >
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
                <!-- Header -->
                <div class="bg-red-600 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Reportar Foto</h3>
                    <button
                        wire:click="closeReportModal"
                        class="text-white/80 hover:text-white transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <form wire:submit="submitReport" class="p-6">
                    <p class="text-gray-600 mb-4">
                        Selecciona el motivo por el cual deseas reportar esta foto. Nuestro equipo revisara el reporte.
                    </p>

                    <!-- Report Reason -->
                    <div class="space-y-2 mb-4">
                        @foreach($this->reportReasons as $value => $label)
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors {{ $reportReason === $value ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                                <input
                                    type="radio"
                                    wire:model="reportReason"
                                    value="{{ $value }}"
                                    class="w-4 h-4 text-red-600 focus:ring-red-500"
                                >
                                <span class="ml-3 text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('reportReason')
                        <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
                    @enderror

                    <!-- Additional Description -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Detalles adicionales (opcional)
                        </label>
                        <textarea
                            wire:model="reportDescription"
                            rows="3"
                            maxlength="500"
                            placeholder="Proporciona mas detalles sobre el problema..."
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                        ></textarea>
                        @error('reportDescription')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            wire:click="closeReportModal"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="submitReport">Enviar Reporte</span>
                            <span wire:loading wire:target="submitReport">Enviando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
