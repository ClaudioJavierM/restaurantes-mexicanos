<x-filament-panels::page>
    <div class="space-y-6">
        @if($hasRestaurant)
            @if(!$isPremium)
            <div style="position: relative; min-height: 400px;">
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(17, 24, 39, 0.9); z-index: 50; display: flex; align-items: center; justify-content: center; border-radius: 0.75rem;">
                    <div style="text-align: center; padding: 2rem; max-width: 500px;">
                        <div style="width: 4rem; height: 4rem; background: linear-gradient(135deg, #d4af37, #f4e4a6); border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                            <svg style="width: 2rem; height: 2rem; color: #1a1a2e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: bold; color: #ffffff; margin-bottom: 0.5rem;">Chatbot IA Bilingue</h3>
                        <p style="color: #9ca3af; margin-bottom: 1.25rem; font-size: 0.9375rem;">
                            Actualiza tu plan para acceder al chatbot IA y mejorar los resultados de tu restaurante.
                        </p>
                        <div style="margin-bottom: 1.25rem;">
                            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 0.5rem;">
                                <span style="background-color: #374151; color: #d1d5db; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem;">Asistente 24/7</span>
                                <span style="background-color: #374151; color: #d1d5db; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem;">Respuestas en espanol e ingles</span>
                                <span style="background-color: #374151; color: #d1d5db; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem;">Responde sobre tu menu</span>
                                <span style="background-color: #374151; color: #d1d5db; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem;">Maneja reservaciones</span>
                            </div>
                        </div>
                        <a href="{{ url('/owner/upgrade-subscription') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #d4af37, #f4e4a6); color: #1a1a2e; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600;">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Ver Planes Disponibles
                        </a>
                        <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.75rem;">Desde $39/mes - Primer mes $9.99</p>
                    </div>
                </div>
                <div style="filter: blur(4px); pointer-events: none; opacity: 0.4;">
            @endif

            <!-- Header with Preview -->
            <div style="background: linear-gradient(135deg, #1e1b4b, #4338ca); border-radius: 0.75rem; padding: 1.5rem; color: #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem; color: #ffffff;">Chatbot IA Bilingue</h2>
                        <p style="color: #c7d2fe;">Tu asistente virtual 24/7 que responde en espanol e ingles</p>
                    </div>
                    <div>
                        <svg style="width: 4rem; height: 4rem; color: #a5b4fc;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Conversaciones</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $chatStats['conversations'] ?? 0 }}</p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-lg">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Mensajes</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $chatStats['messages'] ?? 0 }}</p>
                        </div>
                        <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-lg">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                                <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Satisfaccion</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $chatStats['satisfaction'] ?? 0 }}%</p>
                        </div>
                        <div class="bg-yellow-100 dark:bg-yellow-900/30 p-3 rounded-lg">
                            <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-.464 5.535a1 1 0 10-1.415-1.414 3 3 0 01-4.242 0 1 1 0 00-1.415 1.414 5 5 0 007.072 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Configuracion</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Personaliza como funciona tu chatbot</p>
                </div>

                <form wire:submit="saveSettings" class="p-6">
                    {{ $this->form }}

                    <div class="mt-6 flex justify-end">
                        <x-filament::button type="submit" icon="heroicon-o-check">
                            Guardar Configuracion
                        </x-filament::button>
                    </div>
                </form>
            </div>

            <!-- Chat Preview -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Vista Previa del Chat</h3>

                <div class="max-w-sm mx-auto bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden shadow-lg">
                    <!-- Chat Header -->
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-4 text-white">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="font-semibold">{{ $restaurantName }}</p>
                                <p class="text-xs text-indigo-200">En linea</p>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Messages -->
                    <div class="p-4 space-y-3 h-48">
                        <div class="flex">
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow max-w-xs">
                                <p class="text-sm text-gray-800 dark:text-gray-200">{{ $data['chatbot_welcome_es'] ?? 'Hola! En que puedo ayudarte?' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Input -->
                    <div class="p-3 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center bg-gray-50 dark:bg-gray-800 rounded-full px-4 py-2">
                            <input type="text" placeholder="Escribe un mensaje..." class="flex-1 bg-transparent text-sm text-gray-700 dark:text-gray-300 focus:outline-none" disabled>
                            <button class="ml-2 text-indigo-600" disabled>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-6">
                <h3 class="font-semibold text-green-800 dark:text-green-200 mb-2">Caracteristicas del Chatbot</h3>
                <ul class="text-sm text-green-700 dark:text-green-300 space-y-1">
                    <li>&bull; <strong>Bilingue:</strong> Detecta automaticamente el idioma del cliente</li>
                    <li>&bull; <strong>24/7:</strong> Responde incluso cuando estas cerrado</li>
                    <li>&bull; <strong>Inteligente:</strong> Usa IA para entender preguntas naturales</li>
                    <li>&bull; <strong>Personalizable:</strong> Configura respuestas especificas para tu restaurante</li>
                </ul>
            </div>

            @if(!$isPremium)
                </div>
            </div>
            @endif
        @else
            <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 text-center">
                <p class="text-yellow-800 dark:text-yellow-200">No tienes un restaurante asociado.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
