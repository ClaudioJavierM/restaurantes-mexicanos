<x-filament-panels::page>
    <div class="space-y-6">
        
        {{-- Hero Section with Coupon --}}
        @if($subscriberCoupon)
        <div style="background: linear-gradient(to right, #f97316, #dc2626); border-radius: 1rem; padding: 2rem; color: white; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="text-center lg:text-left">
                    <h2 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem;">Tu Codigo de Descuento FAMER</h2>
                    <p style="color: #fed7aa; margin-bottom: 1rem;">
                        Usa este codigo en cualquiera de nuestros negocios afiliados
                    </p>
                    
                    <div style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 0.75rem; padding: 1rem; display: inline-block;">
                        <div class="flex items-center gap-4">
                            <span id="coupon-code" style="font-family: monospace; font-size: 1.875rem; font-weight: bold; letter-spacing: 0.1em;">
                                {{ $subscriberCoupon->code }}
                            </span>
                            <button id="copy-btn" type="button" style="background-color: #ffffff; color: #ea580c; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600; border: none; cursor: pointer;">
                                Copiar
                            </button>
                        </div>
                    </div>
                    
                    <p style="font-size: 0.875rem; color: #fed7aa; margin-top: 0.75rem;">
                        Plan actual: <span style="font-weight: 600; color: white;">{{ ucfirst($subscriberCoupon->tier ?? 'free') }}</span>
                    </p>
                </div>
                
                <div class="text-center">
                    <div style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 9999px; padding: 1.5rem;">
                        <x-heroicon-o-ticket class="w-16 h-16" />
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        @php $currentTier = $subscriberCoupon->tier ?? 'free'; @endphp
        
        {{-- What you can buy Section --}}
        <div style="background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%); border-radius: 0.75rem; padding: 1.5rem; border: 1px solid #334155;">
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="background: linear-gradient(135deg, #f97316, #dc2626); border-radius: 0.5rem; padding: 0.75rem 1rem; text-align: center; flex-shrink: 0;">
                        <span style="font-size: 1.25rem; font-weight: bold; color: white; white-space: nowrap;">5% - 15%</span>
                        <span style="display: block; font-size: 0.625rem; color: rgba(255,255,255,0.8); text-transform: uppercase;">descuento</span>
                    </div>
                    <h3 style="font-size: 1.125rem; font-weight: bold; color: #ffffff; margin: 0;">
                        Obten descuentos exclusivos en:
                    </h3>
                </div>
                <p style="font-size: 0.875rem; color: #94a3b8; line-height: 1.8; margin: 0;">
                    <span style="color: #f97316; font-weight: 500;">Mobiliario:</span> Sillas, mesas, booths, lamparas
                    <span style="color: #475569;">|</span>
                    <span style="color: #f97316; font-weight: 500;">Decoracion:</span> Cuadros, accesorios de mesa
                    <span style="color: #475569;">|</span>
                    <span style="color: #f97316; font-weight: 500;">Vajilla:</span> Platos, vasos, copas
                    <span style="color: #475569;">|</span>
                    <span style="color: #f97316; font-weight: 500;">Equipo:</span> Tortilleria, paleteria mexicana
                    <span style="color: #475569;">|</span>
                    <span style="color: #f97316; font-weight: 500;">Catering:</span> Food trucks
                    <span style="color: #475569;">|</span>
                    <span style="color: #f97316; font-weight: 500;">Publicidad:</span> Signs externos, menus
                    <span style="color: #475569;">|</span>
                    <span style="color: #22c55e; font-weight: 600;">y mucho mas...</span>
                </p>
            </div>
        </div>
        
        {{-- Benefits Grid --}}
        <div style="background-color: #1f2937; border-radius: 0.75rem; border: 1px solid #374151; overflow: hidden;">
            <div style="padding: 1.5rem; border-bottom: 1px solid #374151; background-color: #111827;">
                <h3 style="font-size: 1.25rem; font-weight: bold; color: #ffffff;">Tus Descuentos Disponibles</h3>
                <p style="font-size: 0.875rem; color: #9ca3af; margin-top: 0.25rem;">Beneficios exclusivos para suscriptores {{ ucfirst($currentTier) }}</p>
            </div>
            
            <div style="padding: 1.5rem;">
                @if(count($benefits) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($benefits as $benefit)
                    <div style="border-radius: 0.75rem; overflow: hidden; border: 1px solid #374151; background-color: #111827;">
                        <div style="background-color: #1f2937; padding: 1rem;">
                            <div class="flex items-center gap-3">
                                <div style="width: 3rem; height: 3rem; border-radius: 0.5rem; background: linear-gradient(135deg, #f97316, #dc2626); display: flex; align-items: center; justify-content: center;">
                                    <span style="color: white; font-weight: bold; font-size: 1.125rem;">{{ strtoupper(substr($benefit['business_name'], 0, 2)) }}</span>
                                </div>
                                <div>
                                    <h4 style="font-weight: bold; color: #ffffff; font-size: 1rem;">{{ $benefit['business_name'] }}</h4>
                                    <span style="font-size: 0.75rem; color: #9ca3af;">{{ $benefit['business_code'] }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: linear-gradient(to right, #f97316, #dc2626); color: white; padding: 1.5rem; text-align: center;">
                            <span style="font-size: 2.5rem; font-weight: bold;">
                                @if($benefit['discount_type'] === 'percentage')
                                    {{ number_format($benefit['discount_value'], 0) }}%
                                @else
                                    \${{ number_format($benefit['discount_value'], 0) }}
                                @endif
                            </span>
                            <span style="display: block; font-size: 0.875rem; color: rgba(255,255,255,0.8); margin-top: 0.25rem;">de descuento</span>
                        </div>
                        
                        <div style="padding: 1rem; background-color: #1f2937;">
                            @if(!empty($benefit['description']))
                            <p style="font-size: 0.875rem; color: #d1d5db; margin-bottom: 0.75rem;">{{ $benefit['description'] }}</p>
                            @endif
                            
                            @if(!empty($benefit['is_used']))
                            <div style="text-align: center; background-color: rgba(34, 197, 94, 0.2); color: #4ade80; padding: 0.5rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500;">Ya utilizado</div>
                            @elseif(!empty($benefit['business_url']))
                            <a href="{{ $benefit['business_url'] }}" target="_blank" style="display: block; width: 100%; text-align: center; background-color: #374151; color: #e5e7eb; padding: 0.5rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; text-decoration: none;">Visitar Sitio Web</a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div style="text-align: center; padding: 2rem;">
                    <p style="color: #9ca3af;">No hay beneficios configurados para tu plan actual.</p>
                </div>
                @endif
            </div>
        </div>
        
        @if($currentTier !== 'elite')
        <div style="background: linear-gradient(to right, #9333ea, #4f46e5); border-radius: 0.75rem; padding: 1.5rem; color: white;">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.25rem;">Mejora tu Plan para Mas Beneficios</h3>
                    <p style="color: #c4b5fd;">
                        @if($currentTier === 'free')
                            Actualiza a Premium (10% descuento) o Elite (15% descuento)
                        @else
                            Actualiza a Elite para obtener 15% de descuento en todos los negocios
                        @endif
                    </p>
                </div>
                <a href="{{ url('/owner/upgrade-subscription') }}" style="background-color: white; color: #7c3aed; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; display: inline-block; text-decoration: none; white-space: nowrap;">Ver Planes</a>
            </div>
        </div>
        @endif
        
        <div style="background-color: #1f2937; border-radius: 0.75rem; border: 1px solid #374151; padding: 1.5rem;">
            <h3 style="font-size: 1.125rem; font-weight: bold; color: #ffffff; margin-bottom: 1rem;">Como Usar tu Codigo</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div style="text-align: center;">
                    <div style="background-color: rgba(249, 115, 22, 0.2); width: 3rem; height: 3rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
                        <span style="color: #f97316; font-weight: bold; font-size: 1.25rem;">1</span>
                    </div>
                    <h4 style="font-weight: 600; color: #ffffff; margin-bottom: 0.25rem;">Copia tu Codigo</h4>
                    <p style="font-size: 0.875rem; color: #9ca3af;">Haz clic en el boton Copiar arriba</p>
                </div>
                <div style="text-align: center;">
                    <div style="background-color: rgba(249, 115, 22, 0.2); width: 3rem; height: 3rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
                        <span style="color: #f97316; font-weight: bold; font-size: 1.25rem;">2</span>
                    </div>
                    <h4 style="font-weight: 600; color: #ffffff; margin-bottom: 0.25rem;">Visita el Negocio</h4>
                    <p style="font-size: 0.875rem; color: #9ca3af;">Compra en cualquiera de nuestros sitios afiliados</p>
                </div>
                <div style="text-align: center;">
                    <div style="background-color: rgba(249, 115, 22, 0.2); width: 3rem; height: 3rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
                        <span style="color: #f97316; font-weight: bold; font-size: 1.25rem;">3</span>
                    </div>
                    <h4 style="font-weight: 600; color: #ffffff; margin-bottom: 0.25rem;">Aplica el Descuento</h4>
                    <p style="font-size: 0.875rem; color: #9ca3af;">Ingresa el codigo al momento del pago</p>
                </div>
            </div>
        </div>
        
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var copyBtn = document.getElementById('copy-btn');
            var couponCode = document.getElementById('coupon-code');
            if (copyBtn && couponCode) {
                copyBtn.addEventListener('click', function() {
                    var code = couponCode.textContent.trim();
                    navigator.clipboard.writeText(code).then(function() {
                        copyBtn.textContent = 'Copiado!';
                        copyBtn.style.backgroundColor = '#22c55e';
                        copyBtn.style.color = '#ffffff';
                        setTimeout(function() {
                            copyBtn.textContent = 'Copiar';
                            copyBtn.style.backgroundColor = '#ffffff';
                            copyBtn.style.color = '#ea580c';
                        }, 2000);
                    });
                });
            }
        });
    </script>
</x-filament-panels::page>
