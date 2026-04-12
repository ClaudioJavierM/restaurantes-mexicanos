<x-filament-panels::page>
    @php
        $restaurant = auth()->user()->restaurants->first();
        $plan = $restaurant->subscription_tier ?? 'free';
        $famerDiscount = $plan === 'elite' ? '15%' : ($plan === 'premium' ? '10%' : '5%');
    @endphp

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        {{-- Current Plan Hero --}}
        <div style="background: linear-gradient(135deg, {{ $plan === 'elite' ? '#f59e0b, #d97706' : ($plan === 'premium' ? '#ef4444, #dc2626' : '#22c55e, #16a34a') }}); border-radius: 1rem; padding: 2rem; color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            
            <div style="position: relative; z-index: 1;">
                <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.5rem;">
                    <div>
                        <p style="font-size: 0.875rem; opacity: 0.9; margin: 0 0 0.25rem 0; text-transform: uppercase; letter-spacing: 0.05em;">Tu Plan Actual</p>
                        <h1 style="font-size: 2.5rem; font-weight: bold; margin: 0;">
                            @if($plan === 'elite') 🏆 @elseif($plan === 'premium') ⭐ @else 📋 @endif
                            {{ ucfirst($plan) }}
                        </h1>
                        <p style="font-size: 1rem; opacity: 0.9; margin: 0.5rem 0 0 0;">
                            @if($plan === 'free')
                                Gratis para siempre
                            @else
                                Facturacion mensual activa
                            @endif
                        </p>
                    </div>
                    
                    <div style="text-align: center; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 1rem; padding: 1.25rem 2rem;">
                        <p style="font-size: 0.75rem; opacity: 0.9; margin: 0 0 0.25rem 0; text-transform: uppercase;">Descuento FAMER</p>
                        <p style="font-size: 2.5rem; font-weight: bold; margin: 0;">{{ $famerDiscount }}</p>
                        <p style="font-size: 0.75rem; opacity: 0.8; margin: 0.25rem 0 0 0;">en todos los negocios</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
            <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #374151; text-align: center;">
                <p style="font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; margin: 0;">Fotos Permitidas</p>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ffffff; margin: 0.25rem 0 0 0;">
                    @if($plan === 'elite') Ilimitadas @elseif($plan === 'premium') 25 @else 5 @endif
                </p>
            </div>
            <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #374151; text-align: center;">
                <p style="font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; margin: 0;">Menu Digital</p>
                <p style="font-size: 1.5rem; font-weight: bold; color: {{ $plan !== 'free' ? '#22c55e' : '#ef4444' }}; margin: 0.25rem 0 0 0;">
                    @if($plan !== 'free') ✓ Activo @else ✗ No @endif
                </p>
            </div>
            <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #374151; text-align: center;">
                <p style="font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; margin: 0;">Analytics</p>
                <p style="font-size: 1.5rem; font-weight: bold; color: {{ $plan !== 'free' ? '#22c55e' : '#ef4444' }}; margin: 0.25rem 0 0 0;">
                    @if($plan !== 'free') ✓ Completos @else Basicos @endif
                </p>
            </div>
        </div>

        {{-- Features Included --}}
        <div style="background-color: #1f2937; border-radius: 0.75rem; border: 1px solid #374151; overflow: hidden;">
            <div style="padding: 1.25rem; border-bottom: 1px solid #374151; background-color: #111827;">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #ffffff; margin: 0;">✅ Incluido en tu Plan {{ ucfirst($plan) }}</h3>
            </div>
            <div style="padding: 1.25rem;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                    @php
                        $features = [
                            'free' => ['Perfil verificado', 'Editar informacion', 'Responder resenas', 'Horarios y contacto', 'Hasta 5 fotos', '5% descuento FAMER'],
                            'premium' => ['Todo lo del plan Gratis', 'Badge Premium verificado', 'Galeria de fotos ilimitada', 'Menu digital completo', 'Analiticas basicas', '10% descuento en negocios FAMER'],
                            'elite' => ['Todo de Premium', 'Posicion #1 en ciudad', 'Sitio Web Completo', 'Fotografia Profesional', 'Gerente Dedicado', 'Fotos Ilimitadas', '15% descuento FAMER'],
                        ];
                    @endphp
                    @foreach($features[$plan] as $feature)
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: #22c55e; font-size: 1rem;">✓</span>
                        <span style="color: #d1d5db; font-size: 0.875rem;">{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($plan !== 'elite')
        {{-- Upgrade Section --}}
        <div style="background-color: #111827; border-radius: 0.75rem; border: 1px solid #374151; overflow: hidden;">
            <div style="padding: 1.25rem; border-bottom: 1px solid #374151; background: linear-gradient(135deg, #7c3aed, #4f46e5);">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #ffffff; margin: 0;">🚀 Mejora tu Plan</h3>
                <p style="font-size: 0.875rem; color: #c4b5fd; margin: 0.25rem 0 0 0;">Desbloquea mas funciones y aumenta tu descuento FAMER</p>
            </div>
            <div style="padding: 1.5rem;">
                <div style="display: grid; grid-template-columns: repeat({{ $plan === 'free' ? '2' : '1' }}, 1fr); gap: 1.5rem;">
                    
                    @if($plan === 'free')
                    {{-- Premium Plan --}}
                    <div style="border: 2px solid #ef4444; border-radius: 0.75rem; overflow: hidden; position: relative;">
                        <div style="background: linear-gradient(135deg, #ef4444, #dc2626); padding: 0.5rem; text-align: center;">
                            <span style="font-size: 0.75rem; font-weight: 600; color: white; text-transform: uppercase;">Mas Popular</span>
                        </div>
                        <div style="padding: 1.5rem; background-color: #1f2937;">
                            <div style="text-align: center; margin-bottom: 1.5rem;">
                                <h4 style="font-size: 1.5rem; font-weight: bold; color: #ffffff; margin: 0;">⭐ Premium</h4>
                                <div style="margin-top: 0.5rem;">
                                    <span style="font-size: 2.5rem; font-weight: bold; color: #ffffff;">$39</span>
                                    <span style="font-size: 1rem; color: #9ca3af;">/mes</span>
                                </div>
                                <span style="display: inline-block; margin-top: 0.5rem; background: linear-gradient(135deg, #f97316, #dc2626); color: white; font-size: 0.75rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 600;">10% descuento FAMER</span>
                            </div>
                            <ul style="list-style: none; padding: 0; margin: 0 0 1.5rem 0;">
                                <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; font-size: 0.875rem; margin-bottom: 0.5rem;"><span style="color: #22c55e;">✓</span> Insignia Destacada</li>
                                <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; font-size: 0.875rem; margin-bottom: 0.5rem;"><span style="color: #22c55e;">✓</span> Analytics Completos</li>
                                <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; font-size: 0.875rem; margin-bottom: 0.5rem;"><span style="color: #22c55e;">✓</span> Menu Digital + QR</li>
                                <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; font-size: 0.875rem; margin-bottom: 0.5rem;"><span style="color: #22c55e;">✓</span> Chatbot IA Bilingue</li>
                                <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; font-size: 0.875rem;"><span style="color: #22c55e;">✓</span> Reservaciones Online</li>
                            </ul>
                            <a href="{{ url('/owner/upgrade-subscription') }}" 
                               style="display: block; width: 100%; text-align: center; background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 0.75rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600;">
                                Actualizar a Premium
                            </a>
                        </div>
                    </div>
                    @endif

                    {{-- Elite Plan --}}
                    <div style="border: 2px solid #f59e0b; border-radius: 0.75rem; overflow: hidden;">
                        <div style="background: linear-gradient(135deg, #f59e0b, #d97706); padding: 0.5rem; text-align: center;">
                            <span style="font-size: 0.75rem; font-weight: 600; color: white; text-transform: uppercase;">🏆 El Mejor</span>
                        </div>
                        <div style="padding: 1.5rem; background-color: #1f2937;">
                            <div style="text-align: center; margin-bottom: 1.5rem;">
                                <h4 style="font-size: 1.5rem; font-weight: bold; color: #ffffff; margin: 0;">🏆 Elite</h4>
                                <div style="margin-top: 0.5rem;">
                                    <span style="font-size: 2.5rem; font-weight: bold; color: #ffffff;">$79</span>
                                    <span style="font-size: 1rem; color: #9ca3af;">/mes</span>
                                </div>
                                <span style="display: inline-block; margin-top: 0.5rem; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; font-size: 0.75rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 600;">15% descuento FAMER</span>
                            </div>
                            <ul style="list-style: none; padding: 0; margin: 0 0 1.5rem 0;">
                                <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; font-size: 0.875rem; margin-bottom: 0.5rem;"><span style="color: #22c55e;">✓</span> Todo de Premium</li>
                                <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; font-size: 0.875rem; margin-bottom: 0.5rem;"><span style="color: #22c55e;">✓</span> Posicion #1 en tu Ciudad</li>
                                <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; font-size: 0.875rem; margin-bottom: 0.5rem;"><span style="color: #22c55e;">✓</span> Sitio Web Completo</li>
                                <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; font-size: 0.875rem; margin-bottom: 0.5rem;"><span style="color: #22c55e;">✓</span> Fotografia Profesional</li>
                                <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; font-size: 0.875rem;"><span style="color: #22c55e;">✓</span> Gerente Dedicado</li>
                            </ul>
                            <a href="{{ url('/owner/upgrade-subscription') }}" 
                               style="display: block; width: 100%; text-align: center; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 0.75rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600;">
                                Actualizar a Elite
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        {{-- Elite User Message --}}
        <div style="background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 0.75rem; padding: 1.5rem; text-align: center;">
            <h3 style="font-size: 1.25rem; font-weight: bold; color: white; margin: 0 0 0.5rem 0;">🏆 Tienes el Mejor Plan!</h3>
            <p style="font-size: 0.875rem; color: rgba(255,255,255,0.9); margin: 0;">Disfruta de todas las funciones y el maximo descuento FAMER del 15%</p>
        </div>
        @endif

        {{-- FAMER Benefits Reminder --}}
        <div style="background: linear-gradient(135deg, #1e3a5f, #0f172a); border-radius: 0.75rem; padding: 1.5rem; border: 1px solid #1e40af;">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #ffffff; margin: 0 0 0.5rem 0;">🎁 Tus Beneficios FAMER</h3>
                    <p style="font-size: 0.875rem; color: #93c5fd; margin: 0;">
                        Usa tu codigo de descuento en MF Imports, Tormex Pro, MF Trailers, Muebles Mexicanos y Refrimex
                    </p>
                </div>
                <a href="{{ url('/owner/my-benefits') }}" 
                   style="display: inline-block; background-color: rgba(255,255,255,0.1); color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 500; white-space: nowrap;">
                    Ver mis descuentos →
                </a>
            </div>
        </div>

        {{-- Cancel / Manage Subscription --}}
        @if($restaurant && $restaurant->stripe_customer_id && $plan !== 'free')
        <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #374151;">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0 0 0.25rem 0;">Gestionar Suscripción</h3>
                    <p style="font-size: 0.875rem; color: #9ca3af; margin: 0;">Cancela, actualiza tu método de pago o revisa tus facturas desde el portal de Stripe.</p>
                </div>
                <button wire:click="openBillingPortal"
                        style="display: inline-flex; align-items: center; gap: 0.5rem; background-color: #374151; color: #ffffff; padding: 0.625rem 1.25rem; border-radius: 0.5rem; border: 1px solid #4b5563; font-size: 0.875rem; font-weight: 500; cursor: pointer; white-space: nowrap;">
                    Administrar en Stripe →
                </button>
            </div>
        </div>
        @endif

        {{-- Help Section --}}
        <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #374151;">
            <h3 style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0 0 0.75rem 0;">💬 Necesitas Ayuda?</h3>
            <p style="font-size: 0.875rem; color: #9ca3af; margin: 0;">
                Si tienes preguntas sobre tu suscripcion o necesitas hacer cambios, contactanos a
                <a href="mailto:soporte@restaurantesmexicanosfamosos.com" style="color: #818cf8;">soporte@restaurantesmexicanosfamosos.com</a>
            </p>
        </div>

    </div>
</x-filament-panels::page>
