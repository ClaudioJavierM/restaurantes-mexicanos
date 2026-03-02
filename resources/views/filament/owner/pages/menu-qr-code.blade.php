<x-filament-panels::page>
    <div class="space-y-6">
        @if($restaurant)
            @if(!$isPremium)
            <x-premium-lock feature="Codigo QR del Menu" :benefits="['QR personalizado', 'Multiples tamanos', 'Plantillas imprimibles', 'Link directo al menu']">
            @endif
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
                <h2 class="text-2xl font-bold mb-2">📱 Código QR de tu Menú</h2>
                <p class="text-purple-100">Imprime este código QR y colócalo en tu restaurante. Tus clientes podrán escanear y ver tu menú digital al instante.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- QR Preview -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Vista Previa</h3>
                    
                    <div class="flex flex-col items-center justify-center p-6 bg-gray-50 dark:bg-gray-900 rounded-lg">
                        <!-- QR Code -->
                        <div class="bg-white p-4 rounded-lg shadow-md">
                            <img 
                                id="qr-image"
                                src="{{ $this->getQrCodeUrl(300) }}" 
                                alt="QR Code del Menú"
                                class="w-64 h-64"
                            >
                        </div>
                        
                        <!-- Restaurant Name -->
                        <div class="mt-4 text-center">
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $restaurant->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Escanea para ver el menú</p>
                        </div>
                    </div>

                    <!-- URL Display -->
                    <div class="mt-4 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">URL del menú:</p>
                        <code class="text-sm text-gray-800 dark:text-gray-200 break-all">{{ $menuUrl }}</code>
                    </div>
                </div>

                <!-- Download Options -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Descargar QR</h3>
                    
                    <!-- Size Options -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tamaño</label>
                        <div class="grid grid-cols-3 gap-3">
                            <button 
                                onclick="updateQrSize(200)"
                                class="px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-center hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                            >
                                <span class="block text-lg font-bold text-gray-900 dark:text-white">S</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">200x200</span>
                            </button>
                            <button 
                                onclick="updateQrSize(300)"
                                class="px-4 py-3 border-2 border-purple-600 bg-purple-50 dark:bg-purple-900/30 rounded-lg text-center"
                            >
                                <span class="block text-lg font-bold text-purple-600 dark:text-purple-400">M</span>
                                <span class="text-xs text-purple-600 dark:text-purple-400">300x300</span>
                            </button>
                            <button 
                                onclick="updateQrSize(500)"
                                class="px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-center hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                            >
                                <span class="block text-lg font-bold text-gray-900 dark:text-white">L</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">500x500</span>
                            </button>
                        </div>
                    </div>

                    <!-- Download Buttons -->
                    <div class="space-y-3">
                        <a 
                            id="download-png"
                            href="{{ $this->getQrCodeUrl(500) }}"
                            download="qr-menu-{{ $restaurant->slug }}.png"
                            class="flex items-center justify-center w-full px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Descargar PNG
                        </a>

                        <button 
                            onclick="printQr()"
                            class="flex items-center justify-center w-full px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Imprimir
                        </button>
                    </div>

                    <!-- Tips -->
                    <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg border border-yellow-200 dark:border-yellow-800">
                        <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">💡 Consejos</h4>
                        <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                            <li>• Colócalo en cada mesa o en la entrada</li>
                            <li>• Usa tamaño grande (L) para posters</li>
                            <li>• Asegúrate que haya buena iluminación</li>
                            <li>• Pruébalo antes de imprimir</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Printable Template -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">🖨️ Plantilla Imprimible</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Haz clic en "Imprimir Plantilla" para imprimir una versión lista para usar con el nombre de tu restaurante.</p>
                
                <button 
                    onclick="printTemplate()"
                    class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition"
                >
                    Imprimir Plantilla
                </button>
            </div>
        @if(!$isPremium)
            </x-premium-lock>
            @endif
        @else
            <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 text-center">
                <p class="text-yellow-800 dark:text-yellow-200">No tienes un restaurante asociado. Por favor, reclama tu restaurante primero.</p>
            </div>
        @endif
    </div>

    <!-- Print Template (Hidden) -->
    <div id="print-template" class="hidden">
        <div style="text-align: center; padding: 40px; font-family: Arial, sans-serif;">
            <h1 style="font-size: 28px; margin-bottom: 20px;">{{ $restaurant->name ?? "" }}</h1>
            <img id="print-qr" src="{{ $this->getQrCodeUrl(400) }}" style="width: 300px; height: 300px;">
            <p style="font-size: 18px; margin-top: 20px; color: #666;">📱 Escanea para ver nuestro menú</p>
            <p style="font-size: 12px; margin-top: 10px; color: #999;">{{ $menuUrl }}</p>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        const baseUrl = @json($menuUrl);
        const restaurantSlug = @json($restaurant->slug ?? "menu");
        
        function updateQrSize(size) {
            const qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=" + size + "x" + size + "&data=" + encodeURIComponent(baseUrl) + "&format=png&margin=10";
            document.getElementById('qr-image').src = qrUrl;
            document.getElementById('download-png').href = qrUrl;
            if (document.getElementById('print-qr')) {
                document.getElementById('print-qr').src = qrUrl;
            }
        }
        
        function printQr() {
            const img = document.getElementById('qr-image');
            const printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>QR Code</title></head><body style="text-align: center; padding: 40px;"><img src="' + img.src + '" style="max-width: 400px;"></body></html>');
            printWindow.document.close();
            printWindow.print();
        }
        
        function printTemplate() {
            const template = document.getElementById('print-template').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Menu QR</title></head><body>' + template + '</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>
    
</x-filament-panels::page>
