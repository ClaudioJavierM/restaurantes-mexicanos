<x-filament-panels::page>
    @if(!$restaurant || !$restaurant->stripe_customer_id)
        <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 3rem; text-align: center; border: 1px solid #374151;">
            <p style="font-size: 3rem; margin: 0 0 1rem;">🧾</p>
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #ffffff; margin: 0 0 0.5rem;">Sin historial de pagos</h3>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">No tienes una suscripcion activa de pago.</p>
        </div>
    @else

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        {{-- Header con acciones --}}
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.5rem; font-weight: bold; color: #ffffff; margin: 0;">Historial de Pagos</h2>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0.25rem 0 0 0;">{{ $restaurant->name }}</p>
            </div>
            <button wire:click="openBillingPortal"
                style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; padding: 0.625rem 1.25rem; border-radius: 0.5rem; border: none; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
                💳 Administrar Facturacion
            </button>
        </div>

        {{-- Proximo cargo --}}
        @if($upcomingInvoice)
        <div style="background: linear-gradient(135deg, #1e3a5f, #0f172a); border-radius: 0.75rem; padding: 1.5rem; border: 1px solid #1e40af;">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
                <div>
                    <p style="font-size: 0.75rem; color: #93c5fd; text-transform: uppercase; margin: 0 0 0.25rem;">Proximo Cargo</p>
                    <p style="font-size: 1.75rem; font-weight: bold; color: #ffffff; margin: 0;">
                        ${{ number_format(($upcomingInvoice['amount_due'] ?? 0) / 100, 2) }} USD
                    </p>
                    <p style="color: #93c5fd; font-size: 0.875rem; margin: 0.25rem 0 0 0;">
                        @if(!empty($upcomingInvoice['next_payment_attempt']))
                            {{ \Carbon\Carbon::createFromTimestamp($upcomingInvoice['next_payment_attempt'])->format('d M, Y') }}
                        @else
                            Proximamente
                        @endif
                    </p>
                </div>
                <div style="text-align: right;">
                    <p style="font-size: 0.75rem; color: #93c5fd; margin: 0 0 0.25rem;">Plan</p>
                    <p style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0;">{{ match($restaurant->subscription_tier) { 'claimed', 'free' => 'Gratuito', 'premium' => 'Premium', 'elite' => 'Elite', default => ucfirst($restaurant->subscription_tier) } }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Lista de facturas --}}
        <div style="background-color: #1f2937; border-radius: 0.75rem; border: 1px solid #374151; overflow: hidden;">
            <div style="padding: 1.25rem; border-bottom: 1px solid #374151; background-color: #111827;">
                <h3 style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0;">Facturas</h3>
            </div>

            @if(count($invoices) === 0)
            <div style="padding: 3rem; text-align: center;">
                <p style="color: #9ca3af;">No hay facturas disponibles aun.</p>
            </div>
            @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid #374151;">
                            <th style="padding: 0.75rem 1.25rem; text-align: left; font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; font-weight: 500;">Fecha</th>
                            <th style="padding: 0.75rem 1.25rem; text-align: left; font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; font-weight: 500;">Descripcion</th>
                            <th style="padding: 0.75rem 1.25rem; text-align: right; font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; font-weight: 500;">Monto</th>
                            <th style="padding: 0.75rem 1.25rem; text-align: center; font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; font-weight: 500;">Estado</th>
                            <th style="padding: 0.75rem 1.25rem; text-align: center; font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; font-weight: 500;">Factura</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                        @php
                            $invStatus = $invoice['status'] ?? 'unknown';
                            $statusColor = match($invStatus) {
                                'paid' => '#22c55e',
                                'open' => '#f59e0b',
                                'void', 'uncollectible' => '#ef4444',
                                default => '#9ca3af',
                            };
                            $statusLabel = match($invStatus) {
                                'paid' => 'Pagado',
                                'open' => 'Pendiente',
                                'void' => 'Cancelado',
                                'uncollectible' => 'No cobrable',
                                'draft' => 'Borrador',
                                default => ucfirst($invStatus),
                            };
                            $description = $invoice['lines']['data'][0]['description']
                                           ?? ($invoice['description'] ?? 'Suscripcion FAMER');
                        @endphp
                        <tr style="border-bottom: 1px solid #374151;">
                            <td style="padding: 1rem 1.25rem; color: #d1d5db; font-size: 0.875rem;">
                                {{ \Carbon\Carbon::createFromTimestamp($invoice['created'])->format('d M, Y') }}
                            </td>
                            <td style="padding: 1rem 1.25rem; color: #d1d5db; font-size: 0.875rem; max-width: 250px;">
                                {{ Str::limit($description, 50) }}
                            </td>
                            <td style="padding: 1rem 1.25rem; color: #ffffff; font-size: 0.875rem; font-weight: 600; text-align: right;">
                                ${{ number_format(($invoice['amount_paid'] ?? 0) / 100, 2) }} USD
                            </td>
                            <td style="padding: 1rem 1.25rem; text-align: center;">
                                <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: {{ $statusColor }}20; color: {{ $statusColor }}; border: 1px solid {{ $statusColor }}40;">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td style="padding: 1rem 1.25rem; text-align: center;">
                                @if(!empty($invoice['invoice_pdf']))
                                <a href="{{ $invoice['invoice_pdf'] }}" target="_blank"
                                    style="display: inline-flex; align-items: center; gap: 0.25rem; color: #818cf8; font-size: 0.75rem; text-decoration: none;">
                                    ⬇ PDF
                                </a>
                                @elseif(!empty($invoice['hosted_invoice_url']))
                                <a href="{{ $invoice['hosted_invoice_url'] }}" target="_blank"
                                    style="display: inline-flex; align-items: center; gap: 0.25rem; color: #818cf8; font-size: 0.75rem; text-decoration: none;">
                                    Ver →
                                </a>
                                @else
                                <span style="color: #6b7280; font-size: 0.75rem;">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Help --}}
        <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #374151;">
            <p style="font-size: 0.875rem; color: #9ca3af; margin: 0;">
                ¿Problemas con un pago? Contacta a
                <a href="mailto:soporte@restaurantesmexicanosfamosos.com" style="color: #818cf8;">soporte@restaurantesmexicanosfamosos.com</a>
                o usa el portal de facturacion para actualizar tu metodo de pago.
            </p>
        </div>

    </div>
    @endif
</x-filament-panels::page>
