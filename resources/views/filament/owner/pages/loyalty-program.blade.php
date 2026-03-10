<x-filament-panels::page>
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="text-center max-w-2xl mx-auto px-6">
            {{-- Icon --}}
            <div class="mx-auto w-24 h-24 rounded-full bg-yellow-500/10 flex items-center justify-center mb-8">
                <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path>
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
                Programa de Lealtad
            </h2>

            {{-- Description --}}
            <p class="text-lg text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
                Muy pronto podras crear un programa de lealtad para recompensar a tus clientes mas fieles.
                Aumenta la retencion y las visitas recurrentes a tu restaurante.
            </p>

            {{-- Features grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Puntos por Visita</h3>
                    <p class="text-xs text-gray-500 mt-1">Recompensa cada visita y compra de tus clientes</p>
                </div>

                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                    <div class="w-10 h-10 rounded-lg bg-green-500/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14.25l3-3m0 0l3 3m-3-3v8.25M15 3.75H9m6 0a2.25 2.25 0 012.25 2.25v.75m-9-3a2.25 2.25 0 00-2.25 2.25v.75m0 0h13.5m-13.5 0v10.5A2.25 2.25 0 006.75 20.25h10.5A2.25 2.25 0 0019.5 17.25V6.75m-13.5 0h13.5"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Recompensas Personalizadas</h3>
                    <p class="text-xs text-gray-500 mt-1">Crea premios unicos para tu restaurante</p>
                </div>

                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.58-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Aumenta Retencion</h3>
                    <p class="text-xs text-gray-500 mt-1">Clientes que regresan gastan hasta 67% mas</p>
                </div>
            </div>

            {{-- CTA --}}
            <p class="text-sm text-gray-400">
                Estamos trabajando para ofrecerte el mejor programa de lealtad para restaurantes.
            </p>
        </div>
    </div>
</x-filament-panels::page>
