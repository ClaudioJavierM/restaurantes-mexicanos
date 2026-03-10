<x-filament-panels::page>
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="text-center max-w-2xl mx-auto px-6">
            {{-- Icon --}}
            <div class="mx-auto w-24 h-24 rounded-full bg-yellow-500/10 flex items-center justify-center mb-8">
                <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75H16.5v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75H16.5v-.75z"></path>
                </svg>
            </div>

            {{-- Badge --}}
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-yellow-500/10 text-yellow-500 text-sm font-semibold mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Pronto
            </span>

            {{-- Title --}}
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Codigo QR del Menu
            </h2>

            {{-- Description --}}
            <p class="text-lg text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
                Muy pronto podras generar codigos QR personalizados para tu menu digital.
                Tus clientes podran escanear y ver tu menu completo desde su celular.
            </p>

            {{-- Features grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Escaneo Instantaneo</h3>
                    <p class="text-xs text-gray-500 mt-1">Tus clientes acceden al menu con una foto</p>
                </div>

                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                    <div class="w-10 h-10 rounded-lg bg-green-500/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M9.75 8.25h4.5"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Listo para Imprimir</h3>
                    <p class="text-xs text-gray-500 mt-1">Descarga e imprime para tus mesas</p>
                </div>

                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Diseno Personalizado</h3>
                    <p class="text-xs text-gray-500 mt-1">QR con tu logo y colores de marca</p>
                </div>
            </div>

            {{-- CTA --}}
            <p class="text-sm text-gray-400">
                Estamos trabajando para ofrecerte los mejores codigos QR para tu restaurante.
            </p>
        </div>
    </div>
</x-filament-panels::page>
