<x-filament-panels::page>
@if(!$restaurant)
<p style="color:#9ca3af">Sin restaurante asociado.</p>
@else
<div style="display:flex;flex-direction:column;gap:1.5rem;">

    {{-- Segmentos --}}
    <div style="background-color:#111827;border-radius:0.75rem;border:1px solid #374151;overflow:hidden;">
        <div style="padding:1.25rem;border-bottom:1px solid #374151;">
            <h3 style="font-size:1rem;font-weight:600;color:#fff;margin:0;">👥 Tu Base de Clientes</h3>
            <p style="font-size:0.8rem;color:#9ca3af;margin:0.25rem 0 0;">Clientes registrados por segmento</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0;">
            @php
                $segs = [
                    ['key'=>'total','label'=>'Total Clientes','icon'=>'👤','color'=>'#818cf8'],
                    ['key'=>'subscribed','label'=>'Suscritos Email','icon'=>'✉️','color'=>'#34d399'],
                    ['key'=>'frequent','label'=>'Frecuentes (3+)','icon'=>'⭐','color'=>'#fbbf24'],
                    ['key'=>'new','label'=>'Nuevos (30 días)','icon'=>'🆕','color'=>'#60a5fa'],
                    ['key'=>'inactive','label'=>'Inactivos (90d+)','icon'=>'💤','color'=>'#f87171'],
                    ['key'=>'birthday_month','label'=>'Cumpleaños este mes','icon'=>'🎂','color'=>'#e879f9'],
                ];
            @endphp
            @foreach($segs as $i => $seg)
            <div style="padding:1.25rem;text-align:center;{{ $i > 0 && $i % 3 !== 0 ? 'border-left:1px solid #374151;' : '' }}{{ $i >= 3 ? 'border-top:1px solid #374151;' : '' }}">
                <p style="font-size:1.5rem;margin:0;">{{ $seg['icon'] }}</p>
                <p style="font-size:1.75rem;font-weight:bold;color:{{ $seg['color'] }};margin:0.25rem 0 0;">{{ $segments[$seg['key']] ?? 0 }}</p>
                <p style="font-size:0.7rem;color:#9ca3af;margin:0.25rem 0 0;text-transform:uppercase;">{{ $seg['label'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Auto-Campañas --}}
    <div style="background-color:#111827;border-radius:0.75rem;border:1px solid #374151;overflow:hidden;">
        <div style="padding:1.25rem;border-bottom:1px solid #374151;background:linear-gradient(135deg,#1e3a5f,#111827);">
            <h3 style="font-size:1rem;font-weight:600;color:#fff;margin:0;">🤖 Campañas Automáticas</h3>
            <p style="font-size:0.8rem;color:#93c5fd;margin:0.25rem 0 0;">Se envían automáticamente según las condiciones configuradas</p>
        </div>
        <div style="padding:1.25rem;display:flex;flex-direction:column;gap:1rem;">
            @foreach($autoCampaigns as $type => $config)
            @php $typeLabel = \App\Models\AutoCampaignConfig::$types[$type] ?? ucfirst($type); @endphp
            <div style="background-color:#1f2937;border-radius:0.75rem;padding:1.25rem;border:1px solid #374151;" x-data="{ open: false }">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:1rem;">
                        <div style="width:2.5rem;height:2.5rem;background-color:{{ $config['is_active'] ? '#064e3b' : '#374151' }};border-radius:0.5rem;display:flex;align-items:center;justify-content:center;font-size:1.25rem;">
                            {{ $type === 'birthday' ? '🎂' : ($type === 'reactivation' ? '🔄' : '👋') }}
                        </div>
                        <div>
                            <p style="font-size:0.9rem;font-weight:600;color:#fff;margin:0;">{{ $typeLabel }}</p>
                            <p style="font-size:0.75rem;color:#9ca3af;margin:0;">
                                @if(($config['total_sent'] ?? 0) > 0)
                                    {{ $config['total_sent'] }} enviados · Última: {{ $config['last_run_at'] ? \Carbon\Carbon::parse($config['last_run_at'])->format('d M') : '—' }}
                                @else
                                    Sin envíos aún
                                @endif
                            </p>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <span style="font-size:0.75rem;padding:0.25rem 0.75rem;border-radius:9999px;font-weight:600;background:{{ ($config['is_active'] ?? false) ? '#064e3b' : '#374151' }};color:{{ ($config['is_active'] ?? false) ? '#34d399' : '#9ca3af' }};">
                            {{ ($config['is_active'] ?? false) ? 'Activa' : 'Inactiva' }}
                        </span>
                        <button @click="open = !open" style="background:transparent;border:1px solid #4b5563;color:#9ca3af;padding:0.375rem 0.75rem;border-radius:0.375rem;font-size:0.75rem;cursor:pointer;">
                            Configurar
                        </button>
                    </div>
                </div>

                <div x-show="open" x-cloak style="margin-top:1rem;padding-top:1rem;border-top:1px solid #374151;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:0.75rem;">
                        <div>
                            <label style="font-size:0.75rem;color:#9ca3af;display:block;margin-bottom:0.25rem;">Asunto del Email</label>
                            <input wire:model="autoCampaigns.{{ $type }}.subject" type="text"
                                style="width:100%;background:#111827;border:1px solid #374151;border-radius:0.375rem;padding:0.5rem;color:#fff;font-size:0.875rem;">
                        </div>
                        <div>
                            <label style="font-size:0.75rem;color:#9ca3af;display:block;margin-bottom:0.25rem;">Descuento del cupón (%)</label>
                            <input wire:model="autoCampaigns.{{ $type }}.coupon_discount_percent" type="number"
                                style="width:100%;background:#111827;border:1px solid #374151;border-radius:0.375rem;padding:0.5rem;color:#fff;font-size:0.875rem;"
                                min="5" max="50">
                        </div>
                    </div>
                    <div style="margin-bottom:0.75rem;">
                        <label style="font-size:0.75rem;color:#9ca3af;display:block;margin-bottom:0.25rem;">Mensaje del Email</label>
                        <textarea wire:model="autoCampaigns.{{ $type }}.message" rows="3"
                            style="width:100%;background:#111827;border:1px solid #374151;border-radius:0.375rem;padding:0.5rem;color:#fff;font-size:0.875rem;resize:vertical;"></textarea>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;">
                        <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                            <input wire:model="autoCampaigns.{{ $type }}.is_active" type="checkbox"
                                style="width:1rem;height:1rem;accent-color:#34d399;">
                            <span style="font-size:0.875rem;color:#d1d5db;">Activar campaña automática</span>
                        </label>
                        <button wire:click="saveAutoCampaign('{{ $type }}')"
                            style="background:linear-gradient(135deg,#059669,#047857);color:white;padding:0.5rem 1.25rem;border-radius:0.5rem;border:none;font-size:0.8rem;font-weight:600;cursor:pointer;">
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Campañas Manuales --}}
    <div style="background-color:#111827;border-radius:0.75rem;border:1px solid #374151;overflow:hidden;">
        <div style="padding:1.25rem;border-bottom:1px solid #374151;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div>
                <h3 style="font-size:1rem;font-weight:600;color:#fff;margin:0;">📨 Campañas Manuales</h3>
                <p style="font-size:0.8rem;color:#9ca3af;margin:0.25rem 0 0;">Crea y envía emails a tus segmentos de clientes</p>
            </div>
            <button wire:click="$toggle('showCampaignForm')"
                style="background:linear-gradient(135deg,#7c3aed,#4f46e5);color:white;padding:0.625rem 1.25rem;border-radius:0.5rem;border:none;font-size:0.8rem;font-weight:600;cursor:pointer;">
                + Nueva Campaña
            </button>
        </div>

        @if($showCampaignForm)
        <div style="padding:1.25rem;border-bottom:1px solid #374151;background-color:#1f2937;">
            <h4 style="font-size:0.9rem;font-weight:600;color:#fff;margin:0 0 1rem;">Nueva Campaña</h4>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:0.75rem;">
                <div>
                    <label style="font-size:0.75rem;color:#9ca3af;display:block;margin-bottom:0.25rem;">Nombre interno</label>
                    <input wire:model="campaignName" type="text"
                        style="width:100%;background:#111827;border:1px solid #374151;border-radius:0.375rem;padding:0.5rem;color:#fff;font-size:0.875rem;"
                        placeholder="Promo Semana Santa">
                    @error('campaignName') <p style="color:#f87171;font-size:0.7rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label style="font-size:0.75rem;color:#9ca3af;display:block;margin-bottom:0.25rem;">Tipo</label>
                    <select wire:model="campaignType"
                        style="width:100%;background:#111827;border:1px solid #374151;border-radius:0.375rem;padding:0.5rem;color:#fff;font-size:0.875rem;">
                        @foreach(\App\Models\OwnerCampaign::typeLabels() as $val => $lbl)
                        <option value="{{ $val }}">{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column:1/-1;">
                    <label style="font-size:0.75rem;color:#9ca3af;display:block;margin-bottom:0.25rem;">Asunto del Email</label>
                    <input wire:model="campaignSubject" type="text"
                        style="width:100%;background:#111827;border:1px solid #374151;border-radius:0.375rem;padding:0.5rem;color:#fff;font-size:0.875rem;"
                        placeholder="¡Oferta especial solo para ti!">
                    @error('campaignSubject') <p style="color:#f87171;font-size:0.7rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label style="font-size:0.75rem;color:#9ca3af;display:block;margin-bottom:0.25rem;">Audiencia</label>
                    <select wire:model="campaignAudience"
                        style="width:100%;background:#111827;border:1px solid #374151;border-radius:0.375rem;padding:0.5rem;color:#fff;font-size:0.875rem;">
                        <option value="all">Todos los suscritos ({{ $segments['subscribed'] ?? 0 }})</option>
                        <option value="frequent">Clientes frecuentes ({{ $segments['frequent'] ?? 0 }})</option>
                        <option value="inactive">Inactivos 90+ días ({{ $segments['inactive'] ?? 0 }})</option>
                        <option value="birthday_month">Cumpleaños este mes ({{ $segments['birthday_month'] ?? 0 }})</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom:0.75rem;">
                <label style="font-size:0.75rem;color:#9ca3af;display:block;margin-bottom:0.25rem;">Contenido del Email</label>
                <textarea wire:model="campaignContent" rows="5"
                    style="width:100%;background:#111827;border:1px solid #374151;border-radius:0.375rem;padding:0.5rem;color:#fff;font-size:0.875rem;resize:vertical;"
                    placeholder="Escribe el contenido de tu email..."></textarea>
                @error('campaignContent') <p style="color:#f87171;font-size:0.7rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>
            <div style="display:flex;gap:0.75rem;">
                <button wire:click="createCampaign"
                    style="background:linear-gradient(135deg,#7c3aed,#4f46e5);color:white;padding:0.625rem 1.5rem;border-radius:0.5rem;border:none;font-size:0.8rem;font-weight:600;cursor:pointer;">
                    Crear Borrador
                </button>
                <button wire:click="$set('showCampaignForm', false)"
                    style="background:transparent;border:1px solid #4b5563;color:#9ca3af;padding:0.625rem 1rem;border-radius:0.5rem;font-size:0.8rem;cursor:pointer;">
                    Cancelar
                </button>
            </div>
        </div>
        @endif

        @if(count($campaigns) === 0)
        <div style="padding:3rem;text-align:center;">
            <p style="font-size:2rem;margin:0 0 0.5rem;">📭</p>
            <p style="color:#9ca3af;font-size:0.875rem;">No tienes campañas. Crea tu primera campaña arriba.</p>
        </div>
        @else
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #374151;">
                        <th style="padding:0.75rem 1.25rem;text-align:left;font-size:0.75rem;color:#9ca3af;text-transform:uppercase;">Campaña</th>
                        <th style="padding:0.75rem 1.25rem;text-align:center;font-size:0.75rem;color:#9ca3af;text-transform:uppercase;">Estado</th>
                        <th style="padding:0.75rem 1.25rem;text-align:center;font-size:0.75rem;color:#9ca3af;text-transform:uppercase;">Destinatarios</th>
                        <th style="padding:0.75rem 1.25rem;text-align:center;font-size:0.75rem;color:#9ca3af;text-transform:uppercase;">Apertura</th>
                        <th style="padding:0.75rem 1.25rem;text-align:center;font-size:0.75rem;color:#9ca3af;text-transform:uppercase;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($campaigns as $campaign)
                    @php
                        $statusColor = match($campaign['status']) {
                            'draft' => '#9ca3af', 'scheduled' => '#fbbf24',
                            'sending' => '#60a5fa', 'sent' => '#34d399',
                            default => '#9ca3af',
                        };
                        $statusLabel = match($campaign['status']) {
                            'draft' => 'Borrador', 'scheduled' => 'Programada',
                            'sending' => 'Enviando', 'sent' => 'Enviada',
                            'paused' => 'Pausada', 'cancelled' => 'Cancelada',
                            default => ucfirst($campaign['status']),
                        };
                        $openRate = ($campaign['sent_count'] ?? 0) > 0
                            ? round(($campaign['opened_count'] / $campaign['sent_count']) * 100) . '%'
                            : '—';
                    @endphp
                    <tr style="border-bottom:1px solid #374151;">
                        <td style="padding:1rem 1.25rem;">
                            <p style="color:#fff;font-size:0.875rem;font-weight:500;margin:0;">{{ $campaign['name'] }}</p>
                            <p style="color:#9ca3af;font-size:0.75rem;margin:0.125rem 0 0;">{{ \App\Models\OwnerCampaign::typeLabels()[$campaign['type']] ?? ucfirst($campaign['type']) }}</p>
                        </td>
                        <td style="padding:1rem 1.25rem;text-align:center;">
                            <span style="display:inline-block;padding:0.2rem 0.6rem;border-radius:9999px;font-size:0.7rem;font-weight:600;background:{{ $statusColor }}20;color:{{ $statusColor }};border:1px solid {{ $statusColor }}40;">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td style="padding:1rem 1.25rem;text-align:center;color:#d1d5db;font-size:0.875rem;">
                            {{ $campaign['total_recipients'] ?? '—' }}
                        </td>
                        <td style="padding:1rem 1.25rem;text-align:center;color:#d1d5db;font-size:0.875rem;">
                            {{ $openRate }}
                        </td>
                        <td style="padding:1rem 1.25rem;text-align:center;">
                            @if($campaign['status'] === 'draft')
                            <button wire:click="scheduleCampaign({{ $campaign['id'] }})"
                                style="background:transparent;border:1px solid #4f46e5;color:#818cf8;padding:0.25rem 0.75rem;border-radius:0.375rem;font-size:0.75rem;cursor:pointer;">
                                Programar →
                            </button>
                            @else
                            <span style="color:#9ca3af;font-size:0.75rem;">{{ $campaign['sent_count'] ?? 0 }} enviados</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endif
</x-filament-panels::page>
