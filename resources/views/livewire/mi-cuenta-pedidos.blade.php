<div style="min-height:100vh; background:#0B0B0B; font-family:'Poppins',sans-serif; padding:2rem 1rem;">
    <div style="max-width:900px; margin:0 auto;">

        {{-- Back link --}}
        <a href="/mi-cuenta" style="display:inline-flex; align-items:center; gap:0.4rem; color:#9CA3AF; font-size:0.875rem; text-decoration:none; margin-bottom:1.75rem; transition:color 0.2s;"
           onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#9CA3AF'">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Mi Cuenta
        </a>

        {{-- Page header --}}
        <div style="margin-bottom:2rem;">
            <h1 style="font-family:'Playfair Display',serif; font-size:2rem; font-weight:700; color:#F5F5F5; margin:0 0 0.4rem;">
                Mis Pedidos
            </h1>
            <p style="color:#9CA3AF; font-size:0.9rem; margin:0;">
                Historial completo de tus órdenes en FAMER
            </p>
        </div>

        {{-- Status filter tabs --}}
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-bottom:1.75rem;">
            @php
                $filters = [
                    '' => 'Todos',
                    'pending' => 'Pendientes',
                    'confirmed' => 'Confirmados',
                    'preparing' => 'En preparación',
                    'ready' => 'Listos',
                    'out_for_delivery' => 'En camino',
                    'completed' => 'Completados',
                    'cancelled' => 'Cancelados',
                ];
            @endphp
            @foreach($filters as $value => $label)
                <button
                    wire:click="$set('statusFilter', '{{ $value }}')"
                    style="
                        padding:0.45rem 1rem;
                        border-radius:2rem;
                        border:1px solid {{ $statusFilter === $value ? '#D4AF37' : '#2A2A2A' }};
                        background:{{ $statusFilter === $value ? 'rgba(212,175,55,0.15)' : '#1A1A1A' }};
                        color:{{ $statusFilter === $value ? '#D4AF37' : '#9CA3AF' }};
                        font-family:'Poppins',sans-serif;
                        font-size:0.8rem;
                        cursor:pointer;
                        transition:all 0.2s;
                        white-space:nowrap;
                    "
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Orders list --}}
        @if($orders->count() > 0)
            <div style="display:flex; flex-direction:column; gap:1rem;">
                @foreach($orders as $order)
                    @php
                        $badgeColors = [
                            'pending'          => 'background:rgba(212,175,55,0.15); color:#D4AF37; border:1px solid rgba(212,175,55,0.3);',
                            'confirmed'        => 'background:rgba(59,130,246,0.15); color:#93C5FD; border:1px solid rgba(59,130,246,0.3);',
                            'preparing'        => 'background:rgba(249,115,22,0.15); color:#FDBA74; border:1px solid rgba(249,115,22,0.3);',
                            'ready'            => 'background:rgba(74,222,128,0.15); color:#4ADE80; border:1px solid rgba(74,222,128,0.3);',
                            'out_for_delivery' => 'background:rgba(167,139,250,0.15); color:#C4B5FD; border:1px solid rgba(167,139,250,0.3);',
                            'completed'        => 'background:rgba(74,222,128,0.1); color:#86EFAC; border:1px solid rgba(74,222,128,0.2);',
                            'cancelled'        => 'background:rgba(239,68,68,0.15); color:#FCA5A5; border:1px solid rgba(239,68,68,0.3);',
                        ];
                        $badgeStyle = $badgeColors[$order->status] ?? 'background:rgba(107,114,128,0.15); color:#9CA3AF; border:1px solid rgba(107,114,128,0.3);';

                        $statusLabels = [
                            'pending'          => 'Pendiente',
                            'confirmed'        => 'Confirmado',
                            'preparing'        => 'Preparando',
                            'ready'            => 'Listo',
                            'out_for_delivery' => 'En camino',
                            'completed'        => 'Completado',
                            'cancelled'        => 'Cancelado',
                        ];
                        $statusLabel = $statusLabels[$order->status] ?? ucfirst($order->status);

                        $paymentLabels = [
                            'cash'         => 'Efectivo',
                            'card'         => 'Tarjeta',
                            'stripe'       => 'Tarjeta',
                            'online'       => 'Pago en línea',
                            'credit_card'  => 'Tarjeta de crédito',
                            'debit_card'   => 'Tarjeta de débito',
                        ];
                        $paymentLabel = $paymentLabels[$order->payment_method] ?? ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A'));

                        $isDelivery = $order->order_type === 'delivery';

                        $itemsSummary = $order->items->map(fn($item) => $item->name . ' x' . $item->quantity)->implode(', ');
                        if (strlen($itemsSummary) > 80) {
                            $itemsSummary = substr($itemsSummary, 0, 77) . '...';
                        }

                        $formattedDate = $order->created_at
                            ? $order->created_at->locale('es')->isoFormat('D MMM YYYY [·] h:mm A')
                            : '—';
                    @endphp

                    <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.75rem; padding:1.25rem 1.5rem; transition:border-color 0.2s;"
                         onmouseover="this.style.borderColor='rgba(212,175,55,0.3)'" onmouseout="this.style.borderColor='#2A2A2A'">

                        {{-- Top row: restaurant + status --}}
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:0.75rem; flex-wrap:wrap;">
                            <div>
                                <h3 style="font-family:'Playfair Display',serif; font-size:1.15rem; font-weight:700; color:#D4AF37; margin:0 0 0.2rem;">
                                    {{ $order->restaurant?->name ?? 'Restaurante' }}
                                </h3>
                                <span style="font-size:0.75rem; color:#6B7280; font-family:'Poppins',sans-serif;">
                                    #{{ $order->order_number }}
                                </span>
                            </div>
                            <span style="{{ $badgeStyle }} padding:0.3rem 0.85rem; border-radius:2rem; font-size:0.75rem; font-weight:600; white-space:nowrap;">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        {{-- Divider --}}
                        <div style="height:1px; background:#2A2A2A; margin-bottom:0.75rem;"></div>

                        {{-- Middle row: date + type + items --}}
                        <div style="display:flex; flex-direction:column; gap:0.5rem; margin-bottom:0.875rem;">

                            {{-- Date & order type --}}
                            <div style="display:flex; align-items:center; gap:1.25rem; flex-wrap:wrap;">
                                <div style="display:flex; align-items:center; gap:0.4rem; color:#9CA3AF; font-size:0.8rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $formattedDate }}
                                </div>

                                <div style="display:flex; align-items:center; gap:0.4rem; color:#9CA3AF; font-size:0.8rem;">
                                    @if($isDelivery)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                        </svg>
                                        Entrega a domicilio
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        Recoger en restaurante
                                    @endif
                                </div>
                            </div>

                            {{-- Items summary --}}
                            @if($itemsSummary)
                                <p style="color:#9CA3AF; font-size:0.82rem; margin:0; line-height:1.4;">
                                    <span style="color:#6B7280;">Artículos:</span>
                                    {{ $itemsSummary }}
                                </p>
                            @endif
                        </div>

                        {{-- Bottom row: total + payment --}}
                        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem;">
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <span style="color:#6B7280; font-size:0.8rem;">Pago:</span>
                                <span style="background:#2A2A2A; color:#9CA3AF; padding:0.2rem 0.65rem; border-radius:0.625rem; font-size:0.75rem; border:1px solid #3A3A3A;">
                                    {{ $paymentLabel }}
                                </span>
                            </div>
                            <div style="text-align:right;">
                                <span style="color:#6B7280; font-size:0.75rem; display:block; margin-bottom:0.1rem;">Total</span>
                                <span style="font-family:'Playfair Display',serif; font-size:1.35rem; font-weight:700; color:#D4AF37;">
                                    ${{ number_format($order->total, 2) }}
                                </span>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($orders->hasPages())
                <div style="margin-top:2rem; display:flex; justify-content:center;">
                    <div style="color:#9CA3AF; font-size:0.85rem;">
                        {{ $orders->links() }}
                    </div>
                </div>
            @endif

        @else
            {{-- Empty state --}}
            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.75rem; padding:4rem 2rem; text-align:center;">
                <div style="width:72px; height:72px; background:rgba(212,175,55,0.08); border:1px solid rgba(212,175,55,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 style="font-family:'Playfair Display',serif; font-size:1.4rem; font-weight:700; color:#F5F5F5; margin:0 0 0.6rem;">
                    Aún no tienes pedidos
                </h3>
                <p style="color:#9CA3AF; font-size:0.9rem; max-width:360px; margin:0 auto 2rem; line-height:1.6;">
                    @if($statusFilter)
                        No hay pedidos con ese estatus. Prueba otro filtro o explora más restaurantes.
                    @else
                        Cuando realices tu primer pedido, aparecerá aquí con todos los detalles.
                    @endif
                </p>
                <a href="/restaurantes"
                   style="display:inline-block; background:#D4AF37; color:#0B0B0B; font-weight:700; font-size:0.9rem; padding:0.75rem 2rem; border-radius:0.625rem; text-decoration:none; font-family:'Poppins',sans-serif; transition:background 0.2s;"
                   onmouseover="this.style.background='#C09B2A'" onmouseout="this.style.background='#D4AF37'">
                    Explorar restaurantes
                </a>
            </div>
        @endif

    </div>
</div>
