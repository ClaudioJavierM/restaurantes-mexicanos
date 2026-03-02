<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-sparkles class="w-5 h-5 text-yellow-500" />
                <span>Asistente de Menu con IA</span>
            </div>
        </x-slot>
        
        <x-slot name="description">
            Sugerencias gastronomicas personalizadas para tu restaurante
        </x-slot>

        @if(!$restaurant)
            <div class="text-center py-8 text-gray-500">
                <x-heroicon-o-building-storefront class="w-12 h-12 mx-auto mb-4 text-gray-400" />
                <p>No tienes restaurantes registrados aun.</p>
                <p class="text-sm">Reclama tu restaurante para acceder a las sugerencias de IA.</p>
            </div>
        @else
            <div class="space-y-6">
                {{-- Suggestion Type Tabs --}}
                <div class="flex flex-wrap gap-2">
                    @foreach($this->getSuggestionTypes() as $type => $label)
                        <button 
                            wire:click="changeSuggestionType({{ json_encode($type) }})"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ $suggestionType === $type 
                                    ? bg-primary-600 text-white 
                                    : bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                {{-- Loading State --}}
                @if($loading)
                    <div class="flex items-center justify-center py-8">
                        <x-filament::loading-indicator class="w-8 h-8" />
                        <span class="ml-3 text-gray-500">Generando sugerencias...</span>
                    </div>
                @elseif($error)
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <p class="text-red-600 dark:text-red-400">{{ $error }}</p>
                    </div>
                @else
                    {{-- Suggestions Grid --}}
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @forelse($suggestions as $suggestion)
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                                <div class="flex items-start justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">
                                        {{ $suggestion["name"] ?? "Sin nombre" }}
                                    </h4>
                                    @if(isset($suggestion["price_suggestion"]))
                                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs rounded-full">
                                            ${{ $suggestion["price_suggestion"] }}
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                    {{ $suggestion["description"] ?? "" }}
                                </p>
                                
                                @if(isset($suggestion["tip"]) && $suggestion["tip"])
                                    <div class="flex items-start gap-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-2">
                                        <x-heroicon-o-light-bulb class="w-4 h-4 text-yellow-500 mt-0.5 flex-shrink-0" />
                                        <p class="text-xs text-yellow-700 dark:text-yellow-400">
                                            {{ $suggestion["tip"] }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8 text-gray-500">
                                <p>No hay sugerencias disponibles.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Refresh Button --}}
                    <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                        <x-filament::button 
                            wire:click="refreshSuggestions"
                            wire:loading.attr="disabled"
                            icon="heroicon-o-arrow-path"
                            size="sm"
                            color="gray"
                        >
                            <span wire:loading.remove wire:target="refreshSuggestions">Generar Nuevas Sugerencias</span>
                            <span wire:loading wire:target="refreshSuggestions">Generando...</span>
                        </x-filament::button>
                    </div>
                @endif
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
