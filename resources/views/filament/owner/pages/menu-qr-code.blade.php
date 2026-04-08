<x-filament-panels::page>
    <div class="space-y-6">
        @if($restaurant)
            {{-- Hero Header --}}
            <div style="background: linear-gradient(135deg, #DC2626 0%, #991B1B 100%); border-radius: 12px; padding: 24px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 4px 0;">Codigos QR de tu Restaurante</h2>
                        <p style="opacity: 0.9; margin: 0; font-size: 14px;">Imprimelos y colocalos en mesas para que tus clientes escaneen y vean tu menu o tu perfil.</p>
                    </div>
                    <div style="font-size: 42px;">&#128241;</div>
                </div>
            </div>

            @if(!$hasMenu)
                <div style="background: #FEF3C7; border: 1px solid #FCD34D; border-radius: 12px; padding: 16px; display: flex; align-items: center; gap: 12px;">
                    <svg style="width: 24px; height: 24px; color: #D97706; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    <div>
                        <p style="font-weight: 600; color: #92400E; margin: 0 0 2px 0; font-size: 14px;">No tienes menu digital cargado</p>
                        <p style="color: #78350F; font-size: 13px; margin: 0;">Carga tu menu en <a href="{{ url('/owner/my-menu/upload') }}" style="color: #DC2626; font-weight: 600;">Menu Digital</a> para que el QR muestre tus platillos.</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- QR Menu --}}
                <div style="background: white; border-radius: 12px; border: 1px solid #E5E7EB; padding: 24px;" class="dark:bg-gray-800 dark:border-gray-700">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                        <h3 style="font-size: 16px; font-weight: 600; margin: 0;" class="text-gray-900 dark:text-white">QR del Menu Digital</h3>
                        <span style="background: #DBEAFE; color: #1E40AF; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 12px;">{{ $menuItemsCount }} platillos</span>
                    </div>
                    <div style="background: #F9FAFB; border-radius: 10px; padding: 24px; text-align: center;" class="dark:bg-gray-900">
                        <div style="background: white; display: inline-block; padding: 16px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <img src="{{ $this->getQrCodeUrl(300) }}" alt="QR Menu" style="width: 240px; height: 240px; display: block;">
                        </div>
                        <p style="margin: 12px 0 4px 0; font-weight: 700; font-size: 16px;" class="text-gray-900 dark:text-white">{{ $restaurant->name }}</p>
                        <p style="margin: 0; font-size: 13px; color: #6B7280;">Escanea para ver el menu</p>
                    </div>
                    <div style="margin-top: 16px;">
                        <p style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6B7280; margin: 0 0 4px 0;">URL</p>
                        <code style="display: block; background: #F3F4F6; padding: 8px 10px; border-radius: 6px; font-size: 12px; word-break: break-all; color: #374151;">{{ $menuUrl }}</code>
                    </div>
                    <div style="display: flex; gap: 8px; margin-top: 16px;">
                        <a href="{{ $this->getQrCodeUrl(600) }}" download="qr-menu-{{ $restaurant->slug }}.png" style="flex: 1; background: #DC2626; color: white; padding: 10px; border-radius: 8px; text-align: center; font-weight: 600; font-size: 13px; text-decoration: none;">Descargar PNG</a>
                        <button onclick="printQrTemplate('menu')" style="flex: 1; background: #374151; color: white; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 13px; border: none; cursor: pointer;">Imprimir</button>
                    </div>
                </div>

                {{-- QR Profile --}}
                <div style="background: white; border-radius: 12px; border: 1px solid #E5E7EB; padding: 24px;" class="dark:bg-gray-800 dark:border-gray-700">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                        <h3 style="font-size: 16px; font-weight: 600; margin: 0;" class="text-gray-900 dark:text-white">QR del Perfil Completo</h3>
                        <span style="background: #DCFCE7; color: #15803D; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 12px;">Resenas y fotos</span>
                    </div>
                    <div style="background: #F9FAFB; border-radius: 10px; padding: 24px; text-align: center;" class="dark:bg-gray-900">
                        <div style="background: white; display: inline-block; padding: 16px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <img src="{{ $this->getRestaurantQrUrl(300) }}" alt="QR Perfil" style="width: 240px; height: 240px; display: block;">
                        </div>
                        <p style="margin: 12px 0 4px 0; font-weight: 700; font-size: 16px;" class="text-gray-900 dark:text-white">{{ $restaurant->name }}</p>
                        <p style="margin: 0; font-size: 13px; color: #6B7280;">Escanea para ver el restaurante</p>
                    </div>
                    <div style="margin-top: 16px;">
                        <p style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6B7280; margin: 0 0 4px 0;">URL</p>
                        <code style="display: block; background: #F3F4F6; padding: 8px 10px; border-radius: 6px; font-size: 12px; word-break: break-all; color: #374151;">{{ $restaurantUrl }}</code>
                    </div>
                    <div style="display: flex; gap: 8px; margin-top: 16px;">
                        <a href="{{ $this->getRestaurantQrUrl(600) }}" download="qr-perfil-{{ $restaurant->slug }}.png" style="flex: 1; background: #DC2626; color: white; padding: 10px; border-radius: 8px; text-align: center; font-weight: 600; font-size: 13px; text-decoration: none;">Descargar PNG</a>
                        <button onclick="printQrTemplate('profile')" style="flex: 1; background: #374151; color: white; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 13px; border: none; cursor: pointer;">Imprimir</button>
                    </div>
                </div>
            </div>

            <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 12px; padding: 20px;">
                <h4 style="font-weight: 700; color: #1E40AF; margin: 0 0 12px 0; font-size: 14px;">Consejos para usar tus codigos QR</h4>
                <ul style="margin: 0; padding-left: 24px; color: #1E3A8A; font-size: 13px; line-height: 1.7;">
                    <li>Coloca el <strong>QR del Menu</strong> en las mesas para que ordenen sin tocar menus fisicos</li>
                    <li>Coloca el <strong>QR del Perfil</strong> en la entrada/vidriera para que vean resenas, fotos y horarios</li>
                    <li>Usa tamanos grandes (600x600) si vas a imprimir posters</li>
                    <li>Tambien compartelo por WhatsApp o redes sociales</li>
                </ul>
            </div>
        @else
            <div style="background: #FEF3C7; border: 1px solid #FCD34D; border-radius: 12px; padding: 24px; text-align: center;">
                <p style="color: #92400E; margin: 0;">No tienes un restaurante asociado. Por favor, reclama tu restaurante primero.</p>
            </div>
        @endif
    </div>

    <script>
        const menuUrl = @json($menuUrl ?? '');
        const profileUrl = @json($restaurantUrl ?? '');
        const restaurantName = @json($restaurant->name ?? '');

        function printQrTemplate(type) {
            const url = type === "menu" ? menuUrl : profileUrl;
            const title = type === "menu" ? "Escanea nuestro menu" : "Visita nuestro perfil";
            const qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=600x600&data=" + encodeURIComponent(url) + "&format=png&margin=10&color=DC2626";

            const html = '<html><head><title>QR ' + restaurantName + '</title><style>' +
                'body { margin: 0; font-family: Arial, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; }' +
                '.page { text-align: center; padding: 40px; }' +
                'h1 { color: #DC2626; font-size: 32px; margin: 0 0 8px 0; }' +
                '.subtitle { color: #6B7280; font-size: 16px; margin: 0 0 24px 0; }' +
                '.qr-box { padding: 20px; border: 4px solid #DC2626; border-radius: 16px; display: inline-block; }' +
                '.qr-box img { width: 320px; height: 320px; display: block; }' +
                '.cta { color: #DC2626; font-size: 20px; font-weight: 700; margin: 24px 0 8px 0; }' +
                '.url { color: #9CA3AF; font-size: 11px; margin: 0; word-break: break-all; }' +
                '.footer { color: #9CA3AF; font-size: 10px; margin-top: 24px; }' +
                '</style></head><body><div class="page">' +
                '<h1>' + restaurantName + '</h1>' +
                '<p class="subtitle">' + title + '</p>' +
                '<div class="qr-box"><img src="' + qrUrl + '"></div>' +
                '<p class="cta">Escanea con tu camara</p>' +
                '<p class="url">' + url + '</p>' +
                '<p class="footer">Powered by FAMER - restaurantesmexicanosfamosos.com</p>' +
                '</div></body></html>';

            const w = window.open("", "_blank");
            w.document.write(html);
            w.document.close();
            setTimeout(function() { w.print(); }, 500);
        }
    </script>
</x-filament-panels::page>
