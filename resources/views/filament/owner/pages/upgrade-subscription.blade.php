<x-filament-panels::page>
    <div class="space-y-6">
        
        {{-- Current Plan Banner --}}
        <div style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%); border-radius: 0.75rem; padding: 1.5rem; border: 1px solid #374151;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <p style="font-size: 0.875rem; color: #9ca3af; margin-bottom: 0.25rem;">Tu plan actual</p>
                    <h2 style="font-size: 1.5rem; font-weight: bold; color: #ffffff;">
                        {{ ucfirst($currentPlan) }}
                        @if($currentPlan === 'elite')
                            <span style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: 9999px; margin-left: 0.5rem;">MEJOR PLAN</span>
                        @endif
                    </h2>
                </div>
                <div style="text-align: right;">
                    <p style="font-size: 0.875rem; color: #9ca3af;">Descuento FAMER</p>
                    <p style="font-size: 1.5rem; font-weight: bold; color: #22c55e;">{{ $plans[$currentPlan]['discount'] }}</p>
                </div>
            </div>
        </div>
        
        {{-- Plans Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($plans as $planKey => $plan)
            <div style="border-radius: 0.75rem; overflow: hidden; border: 2px solid {{ $planKey === $currentPlan ? '#f97316' : '#374151' }}; background-color: #1f2937; position: relative;">
                
                @if($planKey === $currentPlan)
                <div style="background-color: #f97316; color: white; text-align: center; padding: 0.5rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">
                    Plan Actual
                </div>
                @elseif($planKey === 'elite')
                <div style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; text-align: center; padding: 0.5rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">
                    Recomendado
                </div>
                @else
                <div style="height: 2rem;"></div>
                @endif
                
                <div style="padding: 1.5rem;">
                    {{-- Plan Header --}}
                    <div style="text-align: center; margin-bottom: 1.5rem;">
                        <h3 style="font-size: 1.5rem; font-weight: bold; color: #ffffff; margin-bottom: 0.5rem;">{{ $plan['name'] }}</h3>
                        <div style="display: flex; align-items: baseline; justify-content: center; gap: 0.25rem;">
                            @if($plan['price'] > 0)
                                <span style="font-size: 2.5rem; font-weight: bold; color: #ffffff;">${{ $plan["price"] }}</span>
                            @if(isset($plan["first_month_price"]))
                                <div style="margin-top: 0.25rem;">
                                    <span style="font-size: 0.875rem; color: #22c55e; font-weight: 600;">Primer mes: ${{ $plan["first_month_price"] }}</span>
                                </div>
                            @elseif(isset($plan["trial_days"]))
                                <div style="margin-top: 0.25rem;">
                                    <span style="font-size: 0.875rem; color: #22c55e; font-weight: 600;">{{ $plan["trial_days"] }} días gratis — luego ${{ $plan["price"] }}/mes</span>
                                </div>
                            @endif
                                <span style="font-size: 1rem; color: #9ca3af;">/mes</span>
                            @else
                                <span style="font-size: 2.5rem; font-weight: bold; color: #22c55e;">Gratis</span>
                            @endif
                        </div>
                        <div style="margin-top: 0.75rem;">
                            <span style="background: linear-gradient(135deg, #f97316, #dc2626); color: white; font-size: 0.875rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 600;">
                                {{ $plan['discount'] }} descuento FAMER
                            </span>
                        </div>
                    </div>
                    
                    {{-- Features List --}}
                    <ul style="list-style: none; padding: 0; margin: 0 0 1.5rem 0;">
                        @foreach($plan['features'] as $feature)
                        <li style="display: flex; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.75rem; font-size: 0.875rem; color: #d1d5db;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: #22c55e; flex-shrink: 0; margin-top: 0.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                    
                    {{-- Action Button --}}
                    @if($planKey === $currentPlan)
                        <button disabled style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; background-color: #374151; color: #9ca3af; font-weight: 600; border: none; cursor: not-allowed;">
                            Plan Actual
                        </button>
                    @elseif($planKey === 'free' && $currentPlan !== 'free')
                        <button wire:click="upgradeToPlan('free')" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; background-color: #4b5563; color: #ffffff; font-weight: 600; border: none; cursor: pointer;">
                            Cambiar a Gratis
                        </button>
                    @else
                        <button wire:click="upgradeToPlan('{{ $planKey }}')" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; background: linear-gradient(135deg, #f97316, #dc2626); color: #ffffff; font-weight: 600; border: none; cursor: pointer;">
                            @if($plan['price'] > $plans[$currentPlan]['price'])
                                Actualizar a {{ $plan['name'] }}
                            @else
                                Cambiar a {{ $plan['name'] }}
                            @endif
                        </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        
        {{-- FAQ Section --}}
        <div style="background-color: #1f2937; border-radius: 0.75rem; border: 1px solid #374151; padding: 1.5rem;">
            <h3 style="font-size: 1.125rem; font-weight: bold; color: #ffffff; margin-bottom: 1rem;">Preguntas Frecuentes</h3>
            
            <div style="space-y: 1rem;">
                <div style="margin-bottom: 1rem;">
                    <h4 style="font-weight: 600; color: #f97316; margin-bottom: 0.25rem;">¿Puedo cambiar de plan en cualquier momento?</h4>
                    <p style="font-size: 0.875rem; color: #9ca3af;">Si, puedes actualizar o cambiar tu plan cuando quieras. Los cambios se aplican inmediatamente.</p>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <h4 style="font-weight: 600; color: #f97316; margin-bottom: 0.25rem;">¿Como funcionan los descuentos FAMER?</h4>
                    <p style="font-size: 0.875rem; color: #9ca3af;">Con tu codigo de suscriptor obtienes descuentos en todos los negocios afiliados: MF Imports, Tormex Pro, MF Trailers, Muebles Mexicanos y Refrimex Paleteria.</p>
                </div>
                
                <div>
                    <h4 style="font-weight: 600; color: #f97316; margin-bottom: 0.25rem;">¿Que pasa si cancelo mi suscripcion?</h4>
                    <p style="font-size: 0.875rem; color: #9ca3af;">Tu restaurante permanecera en el directorio con el plan Gratis y tus descuentos FAMER bajaran al 5%.</p>
                </div>
            </div>
        </div>
        
    </div>
</x-filament-panels::page>
