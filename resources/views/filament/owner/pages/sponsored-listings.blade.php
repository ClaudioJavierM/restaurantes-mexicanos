<x-filament-panels::page>
@if(!$restaurant)
<p style="color:#9ca3af">Sin restaurante asociado.</p>
@else
<div style="display:flex;flex-direction:column;gap:1.5rem;">

    {{-- Hero Banner --}}
    <div style="background:linear-gradient(135deg,#7c2d12,#1f2937);border-radius:0.75rem;padding:2rem;text-align:center;border:1px solid #374151;">
        <p style="font-size:2rem;margin:0 0 0.5rem;">📣</p>
        <h2 style="font-size:1.5rem;font-weight:700;color:#fff;margin:0 0 0.5rem;">Destaca tu Restaurante</h2>
        <p style="color:#d1d5db;font-size:0.875rem;margin:0;">Aparece en posiciones premium en búsquedas, homepage y guías de ciudad. Más visibilidad = más clientes.</p>
    </div>

    {{-- Configurar solicitud --}}
    <div style="background:#1f2937;border-radius:0.75rem;border:1px solid #374151;overflow:hidden;">
        <div style="padding:1.25rem;border-bottom:1px solid #374151;">
            <h3 style="font-size:1rem;font-weight:600;color:#fff;margin:0;">Solicitar Listado Patrocinado</h3>
        </div>
        <div style="padding:1.5rem;display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;" x-data>

            {{-- Placement --}}
            <div>
                <label style="font-size:0.8rem;color:#9ca3af;display:block;margin-bottom:0.75rem;font-weight:600;">Posición</label>
                <div style="display:flex;flex-direction:column;gap:0.5rem;">
                    @foreach(\App\Models\SponsoredListing::$placements as $key => $label)
                    @php $price = \App\Models\SponsoredListing::$prices[$key]; @endphp
                    <label style="display:flex;align-items:center;gap:0.75rem;background:{{ $selectedPlacement === $key ? '#374151' : '#111827' }};border:1px solid {{ $selectedPlacement === $key ? '#dc2626' : '#374151' }};border-radius:0.5rem;padding:0.75rem;cursor:pointer;">
                        <input type="radio" wire:model.live="selectedPlacement" value="{{ $key }}" style="accent-color:#dc2626;">
                        <div style="flex:1;">
                            <p style="color:#fff;font-size:0.875rem;font-weight:600;margin:0;">{{ $label }}</p>
                        </div>
                        <span style="color:#fca5a5;font-weight:700;font-size:0.875rem;">${{ $price }}/sem</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Duration + Summary --}}
            <div>
                <label style="font-size:0.8rem;color:#9ca3af;display:block;margin-bottom:0.75rem;font-weight:600;">Duración</label>
                <div style="display:flex;flex-direction:column;gap:0.5rem;margin-bottom:1.5rem;">
                    @foreach([1 => '1 semana', 2 => '2 semanas', 4 => '1 mes', 8 => '2 meses'] as $weeks => $label)
                    <label style="display:flex;align-items:center;gap:0.75rem;background:{{ $selectedWeeks === $weeks ? '#374151' : '#111827' }};border:1px solid {{ $selectedWeeks === $weeks ? '#dc2626' : '#374151' }};border-radius:0.5rem;padding:0.75rem;cursor:pointer;">
                        <input type="radio" wire:model.live="selectedWeeks" value="{{ $weeks }}" style="accent-color:#dc2626;">
                        <span style="color:#fff;font-size:0.875rem;flex:1;">{{ $label }}</span>
                        @php $p = \App\Models\SponsoredListing::$prices[$selectedPlacement] ?? 49; @endphp
                        <span style="color:#9ca3af;font-size:0.8rem;">${{ $p * $weeks }}</span>
                    </label>
                    @endforeach
                </div>

                {{-- Quote --}}
                @php $q = $this->quote; @endphp
                <div style="background:#111827;border-radius:0.75rem;padding:1.25rem;border:1px solid #374151;">
                    <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 0.5rem;">Resumen de solicitud</p>
                    <p style="font-size:0.875rem;color:#d1d5db;margin:0 0 0.25rem;">{{ $q['placement_label'] }}</p>
                    <p style="font-size:0.875rem;color:#d1d5db;margin:0 0 0.25rem;">{{ $q['weeks'] }} semana(s) → hasta {{ $q['ends'] }}</p>
                    <p style="font-size:1.5rem;font-weight:700;color:#fff;margin:0.5rem 0;">${{ $q['total'] }}</p>
                    <p style="font-size:0.75rem;color:#9ca3af;margin:0 0 1rem;">*Activación manual tras confirmación de pago</p>
                    <button wire:click="requestSponsorship"
                        style="width:100%;background:linear-gradient(135deg,#dc2626,#991b1b);color:white;padding:0.75rem;border-radius:0.5rem;border:none;font-weight:700;font-size:0.875rem;cursor:pointer;">
                        📣 Solicitar Listado
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial --}}
    @if(count($listings) > 0)
    <div style="background:#111827;border-radius:0.75rem;border:1px solid #374151;overflow:hidden;">
        <div style="padding:1.25rem;border-bottom:1px solid #374151;">
            <h3 style="font-size:1rem;font-weight:600;color:#fff;margin:0;">Mis Listados</h3>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #374151;">
                        <th style="padding:0.75rem 1.25rem;text-align:left;font-size:0.75rem;color:#9ca3af;font-weight:600;">Posición</th>
                        <th style="padding:0.75rem;text-align:left;font-size:0.75rem;color:#9ca3af;font-weight:600;">Inicio</th>
                        <th style="padding:0.75rem;text-align:left;font-size:0.75rem;color:#9ca3af;font-weight:600;">Fin</th>
                        <th style="padding:0.75rem;text-align:left;font-size:0.75rem;color:#9ca3af;font-weight:600;">Monto</th>
                        <th style="padding:0.75rem;text-align:left;font-size:0.75rem;color:#9ca3af;font-weight:600;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($listings as $listing)
                    @php
                        $statusColor = match($listing['status']) {
                            'active'    => ['bg'=>'#064e3b','text'=>'#34d399','label'=>'✅ Activo'],
                            'pending'   => ['bg'=>'#713f12','text'=>'#fde68a','label'=>'⏳ Pendiente'],
                            'expired'   => ['bg'=>'#1f2937','text'=>'#9ca3af','label'=>'Vencido'],
                            'cancelled' => ['bg'=>'#7f1d1d','text'=>'#fca5a5','label'=>'Cancelado'],
                            default     => ['bg'=>'#1f2937','text'=>'#9ca3af','label'=>ucfirst($listing['status'])],
                        };
                        $placementLabel = \App\Models\SponsoredListing::$placements[$listing['placement']] ?? $listing['placement'];
                    @endphp
                    <tr style="border-bottom:1px solid #1f2937;">
                        <td style="padding:0.75rem 1.25rem;color:#d1d5db;font-size:0.875rem;">{{ $placementLabel }}</td>
                        <td style="padding:0.75rem;color:#d1d5db;font-size:0.875rem;">{{ \Carbon\Carbon::parse($listing['starts_at'])->format('d M, Y') }}</td>
                        <td style="padding:0.75rem;color:#d1d5db;font-size:0.875rem;">{{ \Carbon\Carbon::parse($listing['ends_at'])->format('d M, Y') }}</td>
                        <td style="padding:0.75rem;color:#fff;font-weight:600;font-size:0.875rem;">${{ number_format($listing['amount_paid'], 2) }}</td>
                        <td style="padding:0.75rem;">
                            <span style="font-size:0.7rem;padding:0.2rem 0.6rem;border-radius:9999px;background:{{ $statusColor['bg'] }};color:{{ $statusColor['text'] }};">
                                {{ $statusColor['label'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Benefits --}}
    <div style="background:#1f2937;border-radius:0.75rem;padding:1.5rem;border:1px solid #374151;">
        <h3 style="font-size:0.95rem;font-weight:600;color:#fff;margin:0 0 1rem;">¿Por qué destacar tu restaurante?</h3>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
            @foreach([
                ['🏠','Homepage Featured','Aparece en la sección destacada de la página principal. Máxima visibilidad.'],
                ['🔍','Top en Búsquedas','Aparece primero en búsquedas de tu ciudad y tipo de cocina.'],
                ['🌆','City Spotlight','Destacado en guías de ciudad y páginas de ranking.'],
            ] as [$icon,$title,$desc])
            <div style="background:#111827;border-radius:0.75rem;padding:1rem;border:1px solid #374151;">
                <p style="font-size:1.5rem;margin:0 0 0.5rem;">{{ $icon }}</p>
                <p style="font-weight:600;color:#fff;font-size:0.875rem;margin:0 0 0.375rem;">{{ $title }}</p>
                <p style="color:#9ca3af;font-size:0.75rem;margin:0;">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endif
</x-filament-panels::page>
