<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Subir Menu con IA</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Sube tu menu en PDF o imagen y la IA extraera automaticamente los platillos, precios y categorias
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                Powered by Claude AI
            </span>
        </div>
    </div>

    <!-- Processing Overlay -->
    @if($isProcessing)
        <div class="bg-white rounded-xl shadow-sm border-2 border-purple-200 p-8">
            <div class="text-center space-y-4">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full">
                    <svg class="w-8 h-8 text-purple-600 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $processingStep }}</h3>

                <!-- Progress Bar -->
                <div class="max-w-md mx-auto">
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-3 rounded-full transition-all duration-500"
                             style="width: {{ $processingProgress }}%"></div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">{{ $processingProgress }}% completado</p>
                </div>

                <div class="flex items-center justify-center gap-2 text-sm text-gray-500">
                    <svg class="w-4 h-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11H9v-2h2v2zm0-4H9V5h2v4z"/>
                    </svg>
                    Esto puede tomar entre 15-60 segundos dependiendo del tamano del menu
                </div>
            </div>
        </div>
    @endif

    <!-- Success Result -->
    @if($processingResult)
        <div class="bg-green-50 border-2 border-green-200 rounded-xl p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-green-800">Menu procesado exitosamente</h3>
                    <div class="mt-2 flex gap-6">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-700">{{ $processingResult['items'] }}</p>
                            <p class="text-sm text-green-600">platillos extraidos</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-700">{{ $processingResult['categories'] }}</p>
                            <p class="text-sm text-green-600">categorias creadas</p>
                        </div>
                    </div>
                    <a href="{{ \App\Filament\Owner\Resources\MyMenuResource::getUrl('index') }}"
                       style="display: inline-flex; align-items: center; gap: 8px; margin-top: 16px; padding: 12px 24px; background: #16a34a; color: #ffffff !important; border-radius: 10px; font-size: 16px; font-weight: 700; text-decoration: none; box-shadow: 0 2px 8px rgba(22,163,74,0.3);">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Ver platillos en el menu
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Message -->
    @if($processingError)
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h3 class="font-semibold text-red-800">Error al procesar</h3>
                <p class="text-red-700 text-sm mt-1">{{ $processingError }}</p>
            </div>
        </div>
    @endif

    <!-- Upload Form -->
    @if(!$isProcessing)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form wire:submit.prevent="uploadMenu" class="space-y-6">
                <!-- Upload Type Tabs -->
                <div class="flex space-x-1 bg-gray-100 rounded-lg p-1 max-w-xs">
                    <button
                        type="button"
                        wire:click="$set('uploadType', 'file')"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-md transition {{ $uploadType === 'file' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}"
                    >
                        Archivo
                    </button>
                    <button
                        type="button"
                        wire:click="$set('uploadType', 'url')"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-md transition {{ $uploadType === 'url' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}"
                    >
                        URL
                    </button>
                </div>

                @if($uploadType === 'file')
                    <!-- File Upload -->
                    <div
                        x-data="{ dragover: false }"
                        x-on:dragover.prevent="dragover = true"
                        x-on:dragleave.prevent="dragover = false"
                        x-on:drop.prevent="dragover = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'));"
                        class="border-2 border-dashed rounded-xl p-10 text-center transition-colors cursor-pointer"
                        :class="dragover ? 'border-purple-400 bg-purple-50' : 'border-gray-300 hover:border-gray-400'"
                        x-on:click="$refs.fileInput.click()"
                    >
                        <input
                            type="file"
                            wire:model="menuFile"
                            x-ref="fileInput"
                            class="hidden"
                            accept=".pdf,.jpg,.jpeg,.png,.webp"
                        >

                        <div class="space-y-3">
                            <div class="inline-flex items-center justify-center w-14 h-14 bg-gray-100 rounded-full">
                                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-purple-600 hover:text-purple-700 font-medium">Haz clic para seleccionar</span>
                                <span class="text-gray-500"> o arrastra tu menu aqui</span>
                            </div>
                            <p class="text-xs text-gray-400">PDF, JPG, PNG o WEBP - Maximo 20MB</p>
                        </div>

                        @if($menuFile)
                            <div class="mt-4 p-3 bg-purple-50 rounded-lg text-purple-700 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium text-sm">{{ $menuFile->getClientOriginalName() }}</span>
                            </div>
                        @endif

                        <div wire:loading wire:target="menuFile" class="mt-4">
                            <div class="flex items-center justify-center gap-2 text-purple-600">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span class="text-sm font-medium">Cargando archivo...</span>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- URL Input -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            URL del Menu Digital
                        </label>
                        <input
                            type="url"
                            wire:model="menuUrl"
                            placeholder="https://ejemplo.com/mi-menu"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        >
                        <p class="mt-2 text-sm text-gray-500">
                            Si tienes un menu en linea, pega la URL y la IA extraera la informacion
                        </p>
                    </div>
                @endif

                @error('menuFile') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                @error('menuUrl') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

                <!-- Submit Button -->
                <!-- Submit Button -->
                <div class="pt-4 mt-4 border-t border-gray-200">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="uploadMenu,menuFile"
                        style="display: block; width: 100%; padding: 16px 24px; background: linear-gradient(to right, #7c3aed, #db2777); color: white; font-weight: 700; font-size: 18px; border-radius: 12px; border: none; cursor: pointer; text-align: center;"
                    >
                        <span wire:loading.remove wire:target="uploadMenu">
                            🤖 Procesar Menu con IA
                        </span>
                        <span wire:loading wire:target="uploadMenu">
                            ⏳ Procesando... Por favor espera
                        </span>
                    </button>
                    <p style="text-align: center; font-size: 12px; color: #9ca3af; margin-top: 8px;">Selecciona un archivo o URL primero, luego haz clic para procesar</p>
                </div>
            </form>
        </div>
    @endif

    <!-- How it works -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Como funciona</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center p-4">
                <div class="inline-flex items-center justify-center w-10 h-10 bg-purple-100 text-purple-600 rounded-full font-bold mb-2">1</div>
                <h4 class="font-medium text-gray-900">Sube tu menu</h4>
                <p class="text-sm text-gray-500 mt-1">Foto, PDF o URL de tu menu actual</p>
            </div>
            <div class="text-center p-4">
                <div class="inline-flex items-center justify-center w-10 h-10 bg-purple-100 text-purple-600 rounded-full font-bold mb-2">2</div>
                <h4 class="font-medium text-gray-900">La IA lo analiza</h4>
                <p class="text-sm text-gray-500 mt-1">Claude AI extrae platillos, precios y categorias</p>
            </div>
            <div class="text-center p-4">
                <div class="inline-flex items-center justify-center w-10 h-10 bg-purple-100 text-purple-600 rounded-full font-bold mb-2">3</div>
                <h4 class="font-medium text-gray-900">Menu digital listo</h4>
                <p class="text-sm text-gray-500 mt-1">Revisa y edita los platillos extraidos</p>
            </div>
        </div>
    </div>

    <!-- Previous Uploads -->
    @if(count($uploads) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Historial de uploads</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($uploads as $upload)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <span class="text-2xl">{{ $upload->file_type_icon }}</span>
                            <div>
                                <p class="font-medium text-gray-900 truncate max-w-xs">
                                    {{ $upload->original_name }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ $upload->created_at->diffForHumans() }}
                                    @if($upload->items_extracted > 0)
                                        &middot; {{ $upload->items_extracted }} platillos extraidos
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @if($upload->status === 'completed') bg-green-100 text-green-800
                                @elseif($upload->status === 'processing') bg-blue-100 text-blue-800
                                @elseif($upload->status === 'failed') bg-red-100 text-red-800
                                @elseif($upload->status === 'needs_review') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ $upload->status_badge }}
                            </span>

                            @if($upload->status === 'failed')
                                <button
                                    wire:click="retryUpload({{ $upload->id }})"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                >
                                    Reintentar
                                </button>
                            @endif

                            <button
                                wire:click="deleteUpload({{ $upload->id }})"
                                wire:confirm="Seguro que quieres eliminar este upload?"
                                class="text-red-400 hover:text-red-600 text-sm"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
