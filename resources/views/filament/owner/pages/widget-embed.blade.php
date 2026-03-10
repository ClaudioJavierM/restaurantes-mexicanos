<x-filament-panels::page>
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="text-center max-w-2xl mx-auto px-6">
            {{-- Icon --}}
            <div class="mx-auto w-24 h-24 rounded-full bg-yellow-500/10 flex items-center justify-center mb-8">
                <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"></path>
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
                Widget para tu Sitio Web
            </h2>

            {{-- Description --}}
            <p class="text-lg text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
                Muy pronto podras insertar un widget en tu sitio web para mostrar tus resenas, 
                menu y boton de reservaciones directamente a tus visitantes.
            </p>

            {{-- Features grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Resenas en tu Web</h3>
                    <p class="text-xs text-gray-500 mt-1">Muestra tus mejores resenas automaticamente</p>
                </div>

                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                    <div class="w-10 h-10 rounded-lg bg-green-500/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Facil de Instalar</h3>
                    <p class="text-xs text-gray-500 mt-1">Copia y pega una linea de codigo</p>
                </div>

                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Personalizable</h3>
                    <p class="text-xs text-gray-500 mt-1">Adapta colores y estilo a tu marca</p>
                </div>
            </div>

            {{-- CTA --}}
            <p class="text-sm text-gray-400">
                Estamos trabajando para ofrecerte el mejor widget embebible para tu restaurante.
            </p>
        </div>
    </div>
</x-filament-panels::page>
