<x-filament-panels::page>
<style>
    .ops-dash { background: #0B0B0B; min-height: 100vh; color: #F5F5F5; }
    .ops-card { background: #1A1A1A; border-radius: 12px; border: 1px solid #2A2A2A; padding: 1.25rem; margin-bottom: 1rem; }
    .ops-card-sm { background: #111; border-radius: 10px; border: 1px solid #222; padding: 1rem; }
    .sec-title { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #D4AF37; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .sec-title::after { content: ''; flex: 1; height: 1px; background: linear-gradient(to right, #D4AF37 0%, transparent 100%); opacity: 0.3; }

    /* Status Bar */
    .status-bar { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem; }
    .status-pill { display: flex; align-items: center; gap: 0.5rem; background: #1A1A1A; border: 1px solid #2A2A2A; border-radius: 999px; padding: 0.4rem 0.9rem; font-size: 0.72rem; }
    .status-pill.ok { border-color: rgba(34,197,94,0.4); }
    .status-pill.warn { border-color: rgba(245,158,11,0.4); }
    .status-pill.err { border-color: rgba(239,68,68,0.4); }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .dot-green { background: #22c55e; box-shadow: 0 0 6px rgba(34,197,94,0.6); }
    .dot-yellow { background: #f59e0b; box-shadow: 0 0 6px rgba(245,158,11,0.6); }
    .dot-red { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,0.6); }
    .status-label { color: #aaa; }
    .status-val { color: #F5F5F5; font-weight: 700; margin-left: 0.2rem; }

    /* Priority Queue */
    .priority-section { margin-bottom: 0.75rem; }
    .priority-header { font-size: 0.65rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; padding: 0.35rem 0.75rem; border-radius: 6px 6px 0 0; }
    .priority-header.urgent { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.25); }
    .priority-header.week { background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.25); }
    .priority-header.auto { background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.25); }
    .priority-items { border: 1px solid #2A2A2A; border-top: none; border-radius: 0 0 6px 6px; overflow: hidden; }
    .priority-item { display: flex; align-items: center; justify-content: space-between; padding: 0.55rem 0.75rem; border-bottom: 1px solid #1E1E1E; transition: background 0.15s; }
    .priority-item:last-child { border-bottom: none; }
    .priority-item:hover { background: #1E1E1E; }
    .priority-item-label { font-size: 0.73rem; color: #ccc; display: flex; align-items: center; gap: 0.5rem; }
    .priority-item-right { display: flex; align-items: center; gap: 0.75rem; }
    .priority-count { font-size: 0.75rem; font-weight: 800; }
    .count-red { color: #f87171; }
    .count-yellow { color: #fbbf24; }
    .count-blue { color: #60a5fa; }
    .count-green { color: #4ade80; }
    .action-link { font-size: 0.62rem; color: #D4AF37; text-decoration: none; padding: 0.15rem 0.5rem; border: 1px solid rgba(212,175,55,0.3); border-radius: 4px; transition: all 0.15s; white-space: nowrap; }
    .action-link:hover { background: rgba(212,175,55,0.1); }

    /* API cards */
    .api-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; }
    @media (max-width: 900px) { .api-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .api-grid { grid-template-columns: 1fr; } }
    .api-card { background: #111; border-radius: 10px; border: 1px solid #222; padding: 0.875rem; }
    .api-card-label { font-size: 0.6rem; color: #666; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.3rem; }
    .api-card-val { font-size: 1.4rem; font-weight: 800; color: #F5F5F5; line-height: 1; }
    .api-card-sub { font-size: 0.62rem; color: #555; margin-top: 0.2rem; }

    /* Progress bars */
    .enrich-row { display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0; border-bottom: 1px solid #1E1E1E; }
    .enrich-row:last-child { border-bottom: none; }
    .enrich-label { color: #aaa; font-size: 0.72rem; width: 200px; flex-shrink: 0; }
    .enrich-bar-wrap { flex: 1; height: 8px; background: #2A2A2A; border-radius: 4px; overflow: hidden; }
    .enrich-bar-fill { height: 100%; border-radius: 4px; transition: width 0.6s ease; }
    .enrich-pct { font-size: 0.7rem; font-weight: 700; width: 40px; text-align: right; }
    .enrich-count { font-size: 0.62rem; color: #555; width: 110px; text-align: right; }

    /* Email section */
    .email-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; }
    @media (max-width: 700px) { .email-grid { grid-template-columns: 1fr; } }
    .email-card { background: #111; border-radius: 10px; border: 1px solid #222; padding: 1rem; text-align: center; }
    .email-num { font-size: 1.6rem; font-weight: 800; }
    .email-label { font-size: 0.62rem; color: #666; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 0.25rem; }

    /* Bottom layout */
    .bottom-split { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    @media (max-width: 900px) { .bottom-split { grid-template-columns: 1fr; } }
</style>

@php
    $approvedTotal = $this->totalApproved ?: 1;
    $yelpEnriched = $approvedTotal - ($this->enrichmentQueue ?: 0);
    $yelpEnrichPct = min(100, round($yelpEnriched / $approvedTotal * 100));
    $descPct = $approvedTotal > 0 ? round(($approvedTotal - $this->restaurantsWithoutDescription) / $approvedTotal * 100) : 0;
    $coordPct = $approvedTotal > 0 ? round(($approvedTotal - $this->restaurantsWithoutCoords) / $approvedTotal * 100) : 0;
    $photoPct = $approvedTotal > 0 ? round(($approvedTotal - $this->restaurantsWithoutPhotos) / $approvedTotal * 100) : 0;

    // Compute AI EN as proxy
    try {
        $withDescEn = \App\Models\Restaurant::where('status','approved')->whereNotNull('ai_description_en')->count();
        $descEnPct = $approvedTotal > 0 ? round($withDescEn / $approvedTotal * 100) : 0;
    } catch (\Throwable $e) {
        $withDescEn = 0; $descEnPct = 0;
    }

    $yelpBudgetPct = min(100, round($this->yelpBudgetRemaining / 35000 * 100));
    $yelpStatus = $this->yelpBudgetRemaining > 5000 ? 'ok' : ($this->yelpBudgetRemaining > 1000 ? 'warn' : 'err');

    try {
        $totalCalls24h = \App\Models\ApiCallLog::where('called_at', '>=', now()->subDay())->count();
    } catch (\Throwable $e) { $totalCalls24h = 0; }
@endphp

<div class="ops-dash">

    {{-- ===== SECTION 1: SYSTEM STATUS BAR ===== --}}
    <div class="sec-title">Estado del Sistema</div>
    <div class="status-bar" style="margin-bottom: 1.25rem;">
        {{-- Scheduler --}}
        <div class="status-pill {{ $this->schedulerActive ? 'ok' : 'warn' }}">
            <div class="status-dot {{ $this->schedulerActive ? 'dot-green' : 'dot-yellow' }}"></div>
            <span class="status-label">Scheduler:</span>
            <span class="status-val">{{ $this->schedulerActive ? 'Activo' : 'Inactivo' }}</span>
            @if ($this->schedulerLastRun)
                <span style="color: #555; font-size: 0.62rem; margin-left: 0.3rem;">({{ $this->schedulerLastRun }})</span>
            @endif
        </div>

        {{-- Yelp API --}}
        <div class="status-pill {{ $yelpStatus }}">
            <div class="status-dot {{ $yelpStatus === 'ok' ? 'dot-green' : ($yelpStatus === 'warn' ? 'dot-yellow' : 'dot-red') }}"></div>
            <span class="status-label">API Yelp:</span>
            <span class="status-val">{{ number_format($this->yelpBudgetRemaining) }} restantes</span>
        </div>

        {{-- Queue --}}
        <div class="status-pill {{ $this->enrichmentQueue > 1000 ? 'warn' : 'ok' }}">
            <div class="status-dot {{ $this->enrichmentQueue > 1000 ? 'dot-yellow' : 'dot-green' }}"></div>
            <span class="status-label">Cola:</span>
            <span class="status-val">{{ number_format($this->enrichmentQueue) }} pendientes</span>
        </div>

        {{-- Errors --}}
        <div class="status-pill {{ $this->recentErrors > 0 ? 'err' : 'ok' }}">
            <div class="status-dot {{ $this->recentErrors > 0 ? 'dot-red' : 'dot-green' }}"></div>
            <span class="status-label">Errores (24h):</span>
            <span class="status-val">{{ number_format($this->recentErrors) }}</span>
            @if ($this->recentErrors > 0)
                <span style="color: #f87171; font-size: 0.62rem;">({{ $this->errorRate24h }}%)</span>
            @endif
        </div>
    </div>

    {{-- ===== SECTION 2: CONTENT QUEUE ===== --}}
    <div class="ops-card">
        <div class="sec-title">Cola de Contenido — Acciones Requeridas</div>

        {{-- URGENT --}}
        <div class="priority-section">
            <div class="priority-header urgent">🔴 URGENTE</div>
            <div class="priority-items">
                <div class="priority-item">
                    <div class="priority-item-label">
                        <span>🏪</span> Restaurantes pendientes de aprobación
                    </div>
                    <div class="priority-item-right">
                        <span class="priority-count count-red">{{ number_format($this->pendingRestaurants) }}</span>
                        <a href="{{ route('filament.admin.resources.restaurants.index', ['tableFilters[status][value]' => 'pending']) }}" class="action-link">Revisar →</a>
                    </div>
                </div>
                <div class="priority-item">
                    <div class="priority-item-label">
                        <span>💬</span> Reviews pendientes de moderación
                    </div>
                    <div class="priority-item-right">
                        <span class="priority-count count-red">{{ number_format($this->pendingReviews) }}</span>
                        <a href="{{ route('filament.admin.resources.reviews.index') }}" class="action-link">Revisar →</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- THIS WEEK --}}
        <div class="priority-section">
            <div class="priority-header week">🟡 ESTA SEMANA</div>
            <div class="priority-items">
                <div class="priority-item">
                    <div class="priority-item-label">
                        <span>📍</span> Restaurantes sin coordenadas GPS
                    </div>
                    <div class="priority-item-right">
                        <span class="priority-count count-yellow">{{ number_format($this->restaurantsWithoutCoords) }}</span>
                        <a href="{{ route('filament.admin.resources.restaurants.index') }}" class="action-link">Ver →</a>
                    </div>
                </div>
                <div class="priority-item">
                    <div class="priority-item-label">
                        <span>📸</span> Restaurantes sin fotos
                    </div>
                    <div class="priority-item-right">
                        <span class="priority-count count-yellow">{{ number_format($this->restaurantsWithoutPhotos) }}</span>
                        <a href="{{ route('filament.admin.resources.restaurants.index') }}" class="action-link">Ver →</a>
                    </div>
                </div>
                <div class="priority-item">
                    <div class="priority-item-label">
                        <span>📝</span> Restaurantes sin descripción IA
                    </div>
                    <div class="priority-item-right">
                        <span class="priority-count count-yellow">{{ number_format($this->restaurantsWithoutDescription) }}</span>
                        <a href="{{ route('filament.admin.resources.restaurants.index') }}" class="action-link">Ver →</a>
                    </div>
                </div>
                @if ($this->duplicatesEstimate > 0)
                <div class="priority-item">
                    <div class="priority-item-label">
                        <span>🔁</span> Duplicados estimados
                    </div>
                    <div class="priority-item-right">
                        <span class="priority-count count-yellow">~{{ number_format($this->duplicatesEstimate) }}</span>
                        <span style="font-size: 0.62rem; color: #555;">Detección: Dom 4am</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- IN PROGRESS --}}
        <div class="priority-section" style="margin-bottom: 0;">
            <div class="priority-header auto">🟢 EN PROCESO (automático)</div>
            <div class="priority-items">
                <div class="priority-item">
                    <div class="priority-item-label"><span>⚡</span> Enriquecimiento Yelp</div>
                    <div class="priority-item-right">
                        <span class="priority-count count-green">{{ number_format($this->enrichmentQueue) }} en cola</span>
                    </div>
                </div>
                <div class="priority-item">
                    <div class="priority-item-label"><span>🤖</span> Generación descripciones IA</div>
                    <div class="priority-item-right">
                        <span style="font-size: 0.7rem; color: #4ade80;">Activo</span>
                    </div>
                </div>
                <div class="priority-item">
                    <div class="priority-item-label"><span>🔍</span> Detección duplicados</div>
                    <div class="priority-item-right">
                        <span style="font-size: 0.7rem; color: #666;">Domingo 4am</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== SECTION 3: API USAGE ===== --}}
    <div class="ops-card">
        <div class="sec-title">Uso de APIs — Hoy</div>
        <div class="api-grid">
            <div class="api-card" style="border-color: rgba(239,68,68,0.3);">
                <div class="api-card-label">Yelp — Hoy</div>
                <div class="api-card-val" style="color: #f87171;">{{ number_format($this->yelpCallsToday) }}</div>
                <div class="api-card-sub">llamadas API</div>
            </div>
            <div class="api-card" style="border-color: rgba(239,68,68,0.2);">
                <div class="api-card-label">Yelp — Este Mes</div>
                <div class="api-card-val" style="color: #f87171;">{{ number_format($this->yelpCallsThisMonth) }}</div>
                <div class="api-card-sub">de 35,000 incluidas</div>
                <div style="margin-top: 0.5rem; height: 4px; background: #2A2A2A; border-radius: 2px; overflow: hidden;">
                    <div style="width: {{ 100 - $yelpBudgetPct }}%; height: 100%; background: {{ $yelpStatus === 'ok' ? '#22c55e' : ($yelpStatus === 'warn' ? '#f59e0b' : '#ef4444') }}; border-radius: 2px;"></div>
                </div>
            </div>
            <div class="api-card" style="border-color: rgba(212,175,55,0.3);">
                <div class="api-card-label">Budget Restante</div>
                <div class="api-card-val" style="color: #D4AF37;">{{ number_format($this->yelpBudgetRemaining) }}</div>
                <div class="api-card-sub">llamadas disponibles</div>
            </div>
            <div class="api-card" style="border-color: rgba(34,197,94,0.3);">
                <div class="api-card-label">Google — Hoy</div>
                <div class="api-card-val" style="color: #4ade80;">{{ number_format($this->googleCallsToday) }}</div>
                <div class="api-card-sub">llamadas API</div>
            </div>
            <div class="api-card" style="border-color: rgba(239,68,68,{{ $this->recentErrors > 0 ? '0.5' : '0.1' }});">
                <div class="api-card-label">Errores (24h)</div>
                <div class="api-card-val" style="color: {{ $this->recentErrors > 0 ? '#f87171' : '#4ade80' }};">{{ number_format($this->recentErrors) }}</div>
                <div class="api-card-sub">{{ $totalCalls24h > 0 ? $this->errorRate24h . '% tasa de error' : 'sin actividad' }}</div>
            </div>
            <div class="api-card">
                <div class="api-card-label">Total Calls (24h)</div>
                <div class="api-card-val" style="color: #60a5fa;">{{ number_format($totalCalls24h) }}</div>
                <div class="api-card-sub">todas las APIs</div>
            </div>
        </div>
    </div>

    {{-- ===== SECTION 4: EMAIL OPERATIONS ===== --}}
    <div class="ops-card">
        <div class="sec-title">Operaciones de Email</div>
        <div class="email-grid">
            <div class="email-card">
                <div class="email-num" style="color: #60a5fa;">{{ number_format($this->emailsSentToday) }}</div>
                <div class="email-label">Enviados Hoy</div>
            </div>
            <div class="email-card">
                <div class="email-num" style="color: #D4AF37;">{{ number_format($this->emailsSentThisWeek) }}</div>
                <div class="email-label">Esta Semana</div>
            </div>
            <div class="email-card" style="{{ $this->claimEmailsPending > 0 ? 'border-color: rgba(245,158,11,0.4);' : '' }}">
                <div class="email-num" style="color: {{ $this->claimEmailsPending > 0 ? '#fbbf24' : '#4ade80' }};">{{ number_format($this->claimEmailsPending) }}</div>
                <div class="email-label">Invitaciones Pendientes</div>
                @if ($this->claimEmailsPending > 0)
                    <div style="margin-top: 0.5rem;">
                        <span style="font-size: 0.62rem; color: #f59e0b; background: rgba(245,158,11,0.1); padding: 0.15rem 0.5rem; border-radius: 4px;">
                            Restaurantes con email sin contactar
                        </span>
                    </div>
                @endif
            </div>
        </div>
        @if ($this->claimEmailsPending > 0)
            <div style="margin-top: 0.75rem; padding: 0.6rem 0.75rem; background: rgba(245,158,11,0.05); border: 1px solid rgba(245,158,11,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 0.72rem; color: #fbbf24;">
                    ⚠️ {{ number_format($this->claimEmailsPending) }} propietarios no han sido contactados todavía
                </span>
                <a href="{{ route('filament.admin.resources.restaurants.index') }}" class="action-link">
                    Enviar invitaciones →
                </a>
            </div>
        @endif
    </div>

    {{-- ===== SECTION 5: DATA ENRICHMENT PROGRESS ===== --}}
    <div class="ops-card">
        <div class="sec-title">Progreso de Enriquecimiento</div>
        @php
            $enrichRows = [
                ['label' => 'Enriquecimiento Yelp', 'pct' => $yelpEnrichPct, 'val' => $yelpEnriched, 'color' => '#ef4444'],
                ['label' => 'Descripciones IA (ES)', 'pct' => $descPct, 'val' => ($approvedTotal - $this->restaurantsWithoutDescription), 'color' => '#D4AF37'],
                ['label' => 'Descripciones IA (EN)', 'pct' => $descEnPct, 'val' => $withDescEn, 'color' => '#3b82f6'],
                ['label' => 'Con Fotos', 'pct' => $photoPct, 'val' => ($approvedTotal - $this->restaurantsWithoutPhotos), 'color' => '#22c55e'],
                ['label' => 'Con Coordenadas', 'pct' => $coordPct, 'val' => ($approvedTotal - $this->restaurantsWithoutCoords), 'color' => '#8b5cf6'],
            ];
        @endphp

        @foreach ($enrichRows as $row)
            <div class="enrich-row">
                <span class="enrich-label">{{ $row['label'] }}</span>
                <div class="enrich-bar-wrap">
                    <div class="enrich-bar-fill" style="width: {{ $row['pct'] }}%; background: {{ $row['color'] }};"></div>
                </div>
                <span class="enrich-pct" style="color: {{ $row['color'] }};">{{ $row['pct'] }}%</span>
                <span class="enrich-count">{{ number_format($row['val']) }}/{{ number_format($approvedTotal) }}</span>
            </div>
        @endforeach

        <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #2A2A2A; display: flex; justify-content: space-between; font-size: 0.65rem; color: #555;">
            <span>Base total: <strong style="color: #F5F5F5;">{{ number_format($approvedTotal) }}</strong> restaurantes aprobados</span>
            <span>Cola enriquecimiento: <strong style="color: #fbbf24;">{{ number_format($this->enrichmentQueue) }}</strong></span>
        </div>
    </div>

</div>
</x-filament-panels::page>
