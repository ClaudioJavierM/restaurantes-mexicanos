@props([
    'url' => url()->current(),
    'title' => '',
    'size' => 200,
    'showDownload' => true,
])

<div {{ $attributes->merge(['class' => 'qr-code-container']) }}>
    <div class="bg-white rounded-lg p-4 inline-block shadow-lg">
        <div id="qr-code-{{ md5($url) }}" data-url="{{ $url }}" class="flex items-center justify-center" style="width: {{ $size }}px; height: {{ $size }}px;">
            <div class="animate-pulse bg-gray-200 rounded" style="width: {{ $size }}px; height: {{ $size }}px;"></div>
        </div>
        @if($title)
            <p class="text-center text-sm text-gray-600 mt-2 font-medium">{{ $title }}</p>
        @endif
    </div>
    
    @if($showDownload)
        <div class="mt-3 flex flex-col gap-2">
            <button type="button" 
                    onclick="downloadQRCode('qr-code-{{ md5($url) }}', '{{ Str::slug($title ?: 'qr-code') }}')"
                    class="flex items-center justify-center gap-2 px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg transition-colors text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                {{ app()->getLocale() === 'en' ? 'Download QR' : 'Descargar QR' }}
            </button>
            <button type="button"
                    onclick="printQRCode('qr-code-{{ md5($url) }}', '{{ $title }}')"
                    class="flex items-center justify-center gap-2 px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-colors text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                {{ app()->getLocale() === 'en' ? 'Print QR' : 'Imprimir QR' }}
            </button>
        </div>
    @endif
</div>

@once
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[id^="qr-code-"]').forEach(function(container) {
                const url = container.dataset.url;
                if (url) {
                    generateQRCode(container.id, url);
                }
            });
        });

        function generateQRCode(containerId, url, size = 200) {
            const container = document.getElementById(containerId);
            if (!container) return;
            container.innerHTML = '';
            const canvas = document.createElement('canvas');
            container.appendChild(canvas);
            QRCode.toCanvas(canvas, url, {
                width: size,
                margin: 2,
                color: { dark: '#000000', light: '#FFFFFF' },
                errorCorrectionLevel: 'M'
            }, function(error) {
                if (error) {
                    console.error('QR Code generation error:', error);
                    container.innerHTML = '<p class="text-red-500 text-sm">Error</p>';
                }
            });
        }

        function downloadQRCode(containerId, filename) {
            const container = document.getElementById(containerId);
            const canvas = container.querySelector('canvas');
            if (canvas) {
                const link = document.createElement('a');
                link.download = filename + '-qr.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            }
        }

        function printQRCode(containerId, title) {
            const container = document.getElementById(containerId);
            const canvas = container.querySelector('canvas');
            if (canvas) {
                const printWindow = window.open('', '_blank');
                const imgSrc = canvas.toDataURL('image/png');
                printWindow.document.write('<html><head><title>QR - ' + title + '</title>');
                printWindow.document.write('<style>body{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px;font-family:Arial,sans-serif}.qr-container{text-align:center;padding:30px;border:2px solid #eee;border-radius:12px}h1{color:#333;margin-bottom:20px;font-size:24px}img{max-width:300px}.instructions{margin-top:20px;color:#666;font-size:14px}.logo{margin-bottom:15px;font-size:28px;font-weight:bold;color:#dc2626}@media print{body{padding:0}.qr-container{border:none}}</style>');
                printWindow.document.write('</head><body><div class="qr-container"><div class="logo">Restaurantes Mexicanos</div><h1>' + title + '</h1><img src="' + imgSrc + '" alt="QR Code" /><p class="instructions">Escanea para ver el menu</p></div></body></html>');
                printWindow.document.close();
                printWindow.onload = function() { printWindow.print(); };
            }
        }
    </script>
    @endpush
@endonce
