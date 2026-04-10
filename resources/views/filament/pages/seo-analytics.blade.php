<x-filament-panels::page>
<style>
    .seo-dash { background: #0B0B0B; min-height: 100vh; color: #F5F5F5; }
    .seo-card { background: #1A1A1A; border-radius: 12px; border: 1px solid #2A2A2A; padding: 1.25rem; }
    .seo-card-sm { background: #1A1A1A; border-radius: 10px; border: 1px solid #2A2A2A; padding: 1rem; }
    .gold { color: #D4AF37; }
    .gold-border { border-color: #D4AF37; }
    .sec-title { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #D4AF37; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .sec-title::after { content: ''; flex: 1; height: 1px; background: linear-gradient(to right, #D4AF37 0%, transparent 100%); opacity: 0.3; }
    .stat-label { font-size: 0.65rem; color: #888; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.25rem; }
    .stat-value-lg { font-size: 2rem; font-weight: 800; color: #F5F5F5; line-height: 1; }
    .stat-sub { font-size: 0.7rem; color: #666; margin-top: 0.2rem; }
    .prog-bar-wrap { height: 6px; background: #2A2A2A; border-radius: 3px; overflow: hidden; margin-top: 0.5rem; }
    .prog-bar-fill { height: 100%; border-radius: 3px; transition: width 0.6s ease; }
    .bg-gold { background: #D4AF37; }
    .bg-green { background: #22c55e; }
    .bg-yellow { background: #f59e0b; }
    .bg-red { background: #ef4444; }
    .bg-blue { background: #3b82f6; }
    .bg-purple { background: #8b5cf6; }
    .bg-cyan { background: #06b6d4; }
    .score-ring-wrap { position: relative; display: flex; align-items: center; justify-content: center; width: 160px; height: 160px; flex-shrink: 0; }
    .score-ring-inner { position: absolute; inset: 0; }
    .score-label-wrap { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .score-num { font-size: 2.5rem; font-weight: 900; line-height: 1; }
    .score-denom { font-size: 0.75rem; color: #666; }
    .cov-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; }
    @media (max-width: 900px) { .cov-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .cov-grid { grid-template-columns: 1fr; } }
    .cov-card { background: #111; border-radius: 10px; border: 1px solid #222; padding: 1rem; }
    .cov-pct { font-size: 1.5rem; font-weight: 800; }
    .indexable-row { display: flex; align-items: center; gap: 0.75rem; padding: 0.4rem 0; border-bottom: 1px solid #1E1E1E; }
    .indexable-row:last-child { border-bottom: none; }
    .indexable-label { color: #aaa; font-size: 0.75rem; flex: 1; }
    .indexable-count { color: #F5F5F5; font-size: 0.75rem; font-weight: 600; width: 80px; text-align: right; }
    .indexable-pct { color: #666; font-size: 0.65rem; width: 40px; text-align: right; }
    .indexable-bar-wrap { width: 120px; height: 4px; background: #2A2A2A; border-radius: 2px; overflow: hidden; }
    .indexable-bar-fill { height: 100%; border-radius: 2px; }
    .blog-card { background: #111; border-radius: 10px; border: 1px solid #222; padding: 1rem; text-align: center; }
    .blog-num { font-size: 1.8rem; font-weight: 800; color: #F5F5F5; }
    .blog-label { font-size: 0.65rem; color: #888; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 0.25rem; }
    .state-table { width: 100%; border-collapse: collapse; font-size: 0.72rem; }
    .state-table th { text-align: left; color: #D4AF37; font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.1em; padding: 0.5rem 0.75rem; border-bottom: 1px solid #2A2A2A; }
    .state-table td { padding: 0.45rem 0.75rem; border-bottom: 1px solid #1A1A1A; color: #ccc; }
    .state-table tr:hover td { background: #1E1E1E; }
    .state-table td:first-child { color: #F5F5F5; font-weight: 600; }
    .state-table td:last-child { text-align: right; }
    .mini-bar { display: inline-block; height: 4px; border-radius: 2px; vertical-align: middle; }
    .review-card { background: #111; border-radius: 10px; border: 1px solid #222; padding: 1rem; }
    .stars { color: #D4AF37; font-size: 1.1rem; }
    .main-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    @media (max-width: 900px) { .main-grid { grid-template-columns: 1fr; } }
    .score-section { display: flex; align-items: center; gap: 2rem; }
    @media (max-width: 700px) { .score-section { flex-direction: column; align-items: flex-start; } }
    .score-breakdown { flex: 1; }
    .score-breakdown-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.72rem; }
    .score-breakdown-label { color: #aaa; }
    .score-breakdown-val { color: #F5F5F5; font-weight: 600; margin-left: 0.75rem; white-space: nowrap; }
    .score-breakdown-bar { flex: 1; height: 4px; background: #2A2A2A; border-radius: 2px; overflow: hidden; margin: 0 0.75rem; }
    .score-breakdown-fill { height: 100%; border-radius: 2px; }
    /* GSC styles */
    .gsc-grid-4 { display: grid; grid-template-columns: repeat(4,1fr); gap: 0.75rem; margin-bottom: 1rem; }
    @media (max-width: 900px) { .gsc-grid-4 { grid-template-columns: repeat(2,1fr); } }
    .kw-table { width: 100%; border-collapse: collapse; font-size: 0.72rem; }
    .kw-table th { text-align: left; color: #D4AF37; font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.1em; padding: 0.5rem 0.75rem; border-bottom: 1px solid #2A2A2A; }
    .kw-table td { padding: 0.4rem 0.75rem; border-bottom: 1px solid #1A1A1A; color: #ccc; }
    .kw-table tr:hover td { background: #1E1E1E; }
    .kw-table td:first-child { color: #F5F5F5; }
    .pos-badge { display: inline-block; min-width: 36px; text-align: center; border-radius: 4px; padding: 1px 4px; font-weight: 700; font-size: 0.68rem; }
    .pos-top3  { background: rgba(34,197,94,0.15); color: #22c55e; }
    .pos-4to10 { background: rgba(245,158,11,0.15); color: #f59e0b; }
    .pos-11p   { background: rgba(239,68,68,0.12); color: #ef4444; }
</style>

<div class="seo-dash">

    {{-- ===== GSC: GOOGLE SEARCH CONSOLE ===== --}}
    @if($this->hasGscData)

    <div class="sec-title">Google Search Console — Últimos 30 días</div>

    {{-- 4 KPIs --}}
    <div class="gsc-grid-4">
        @php
            $gscKpis = [
                ['label' => 'Clics', 'val' => number_format($this->totalClicks), 'color' => '#22c55e', 'icon' => '👆'],
                ['label' => 'Impresiones', 'val' => number_format($this->totalImpressions), 'color' => '#3b82f6', 'icon' => '👁'],
                ['label' => 'CTR Promedio', 'val' => $this->avgCtr . '%', 'color' => '#D4AF37', 'icon' => '📊'],
                ['label' => 'Posición Media', 'val' => $this->avgPosition, 'color' => '#8b5cf6', 'icon' => '📍'],
            ];
        @endphp
        @foreach ($gscKpis as $kpi)
        <div class="seo-card-sm" style="text-align: center;">
            <div style="font-size: 1.3rem; margin-bottom: 0.25rem;">{{ $kpi['icon'] }}</div>
            <div style="font-size: 1.6rem; font-weight: 900; color: {{ $kpi['color'] }}; line-height: 1;">{{ $kpi['val'] }}</div>
            <div style="font-size: 0.65rem; color: #888; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 0.3rem;">{{ $kpi['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Top keywords + Oportunidades --}}
    <div class="main-grid" style="margin-bottom: 1rem;">

        {{-- Top 20 Keywords --}}
        <div class="seo-card">
            <div class="sec-title">Top 20 Keywords</div>
            <div style="overflow-x: auto; max-height: 360px; overflow-y: auto;">
                <table class="kw-table">
                    <thead><tr><th>Query</th><th>Clics</th><th>Impr.</th><th>CTR</th><th>Pos.</th></tr></thead>
                    <tbody>
                        @foreach ($this->topKeywords as $kw)
                            @php
                                $pos = $kw['position'];
                                $posClass = $pos <= 3 ? 'pos-top3' : ($pos <= 10 ? 'pos-4to10' : 'pos-11p');
                            @endphp
                            <tr>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $kw['query'] }}</td>
                                <td style="color: #22c55e; font-weight: 700;">{{ $kw['clicks'] }}</td>
                                <td style="color: #3b82f6;">{{ number_format($kw['impressions']) }}</td>
                                <td>{{ $kw['ctr'] }}</td>
                                <td><span class="pos-badge {{ $posClass }}">{{ $pos }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Oportunidades (pos 4-10) --}}
        <div class="seo-card">
            <div class="sec-title" style="color: #f59e0b;">Oportunidades — Posición 4–10</div>
            <p style="font-size: 0.7rem; color: #666; margin-bottom: 0.75rem;">Keywords con alto potencial — pequeña mejora = salto a top 3</p>
            <div style="overflow-x: auto; max-height: 360px; overflow-y: auto;">
                <table class="kw-table">
                    <thead><tr><th>Query</th><th>Impr.</th><th>Clics</th><th>CTR</th><th>Pos.</th></tr></thead>
                    <tbody>
                        @foreach ($this->opportunities as $opp)
                            <tr>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $opp['query'] }}</td>
                                <td style="color: #3b82f6;">{{ number_format($opp['impressions']) }}</td>
                                <td style="color: #22c55e; font-weight: 700;">{{ $opp['clicks'] }}</td>
                                <td>{{ $opp['ctr'] }}</td>
                                <td><span class="pos-badge pos-4to10">{{ $opp['position'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Top páginas + Dispositivos --}}
    <div class="main-grid" style="margin-bottom: 1rem;">
        {{-- Top páginas --}}
        <div class="seo-card">
            <div class="sec-title">Top 10 Páginas</div>
            <div style="overflow-x: auto;">
                <table class="kw-table">
                    <thead><tr><th>Página</th><th>Clics</th><th>Impr.</th><th>Pos.</th></tr></thead>
                    <tbody>
                        @foreach ($this->topPages as $pg)
                            @php $slug = ltrim(parse_url($pg['page'], PHP_URL_PATH) ?? $pg['page'], '/'); @endphp
                            <tr>
                                <td style="max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #D4AF37;">
                                    <a href="{{ $pg['page'] }}" target="_blank" style="color: #D4AF37; text-decoration: none;">{{ $slug ?: 'Inicio' }}</a>
                                </td>
                                <td style="color: #22c55e; font-weight: 700;">{{ $pg['clicks'] }}</td>
                                <td style="color: #3b82f6;">{{ number_format($pg['impressions']) }}</td>
                                <td>{{ $pg['position'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Por dispositivo --}}
        <div class="seo-card">
            <div class="sec-title">Clics por Dispositivo</div>
            @php $totalDevClicks = array_sum(array_column($this->byDevice, 'clicks')) ?: 1; @endphp
            @foreach ($this->byDevice as $dev)
                @php
                    $devPct = round($dev['clicks'] / $totalDevClicks * 100, 1);
                    $devColor = match(strtolower($dev['device'])) { 'mobile' => '#22c55e', 'desktop' => '#3b82f6', default => '#f59e0b' };
                @endphp
                <div style="margin-bottom: 0.75rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.72rem; margin-bottom: 0.3rem;">
                        <span style="color: #ccc;">{{ $dev['device'] }}</span>
                        <span style="color: {{ $devColor }}; font-weight: 700;">{{ number_format($dev['clicks']) }} clics ({{ $devPct }}%)</span>
                    </div>
                    <div class="prog-bar-wrap">
                        <div class="prog-bar-fill" style="width: {{ $devPct }}%; background: {{ $devColor }};"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <hr style="border: none; border-top: 1px solid #2A2A2A; margin: 1.5rem 0;">
    @endif

    {{-- ===== SECTION 1: SEO HEALTH SCORE ===== --}}
    @php
        $total = $this->totalPages ?: 1;
        $score = $this->seoScore;
        $scoreColor = $score >= 75 ? '#22c55e' : ($score >= 50 ? '#f59e0b' : '#ef4444');
        $scoreLabel = $score >= 75 ? 'Excelente' : ($score >= 50 ? 'Bueno' : 'Necesita Atención');
        $scoreCircumference = 2 * M_PI * 60;
        $scoreDashoffset = $scoreCircumference - ($score / 100) * $scoreCircumference;
    @endphp

    <div class="seo-card" style="margin-bottom: 1rem;">
        <div class="sec-title">Salud SEO General</div>
        <div class="score-section">
            {{-- Ring --}}
            <div class="score-ring-wrap">
                <svg class="score-ring-inner" viewBox="0 0 140 140" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="70" cy="70" r="60" fill="none" stroke="#2A2A2A" stroke-width="10"/>
                    <circle cx="70" cy="70" r="60" fill="none"
                        stroke="{{ $scoreColor }}"
                        stroke-width="10"
                        stroke-linecap="round"
                        stroke-dasharray="{{ $scoreCircumference }}"
                        stroke-dashoffset="{{ $scoreDashoffset }}"
                        transform="rotate(-90 70 70)"/>
                </svg>
                <div class="score-label-wrap">
                    <div class="score-num" style="color: {{ $scoreColor }};">{{ $score }}</div>
                    <div class="score-denom">/100</div>
                    <div style="font-size: 0.6rem; color: {{ $scoreColor }}; margin-top: 0.2rem; font-weight: 700;">{{ $scoreLabel }}</div>
                </div>
            </div>

            {{-- Breakdown --}}
            <div class="score-breakdown">
                @php
                    $breakdownItems = [
                        ['label' => 'Descripciones IA (ES)', 'val' => $this->withAiDescription, 'weight' => 25, 'color' => '#D4AF37'],
                        ['label' => 'Con Fotos', 'val' => $this->withPhotos, 'weight' => 20, 'color' => '#3b82f6'],
                        ['label' => 'Con Rating', 'val' => $this->withRating, 'weight' => 20, 'color' => '#22c55e'],
                        ['label' => 'Con Coordenadas', 'val' => $this->withCoordinates, 'weight' => 15, 'color' => '#8b5cf6'],
                        ['label' => 'Con Dirección', 'val' => $this->withAddress, 'weight' => 10, 'color' => '#06b6d4'],
                        ['label' => 'Con Horarios', 'val' => $this->withHours, 'weight' => 10, 'color' => '#f59e0b'],
                    ];
                @endphp
                @foreach ($breakdownItems as $item)
                    @php
                        $itemPct = $total > 0 ? round($item['val'] / $total * 100, 1) : 0;
                        $contribution = round($itemPct / 100 * $item['weight'], 1);
                    @endphp
                    <div class="score-breakdown-row">
                        <span class="score-breakdown-label">{{ $item['label'] }}</span>
                        <div class="score-breakdown-bar">
                            <div class="score-breakdown-fill" style="width: {{ $itemPct }}%; background: {{ $item['color'] }};"></div>
                        </div>
                        <span class="score-breakdown-val">{{ $itemPct }}% <span style="color:#555;">(×{{ $item['weight'] }}pts)</span></span>
                    </div>
                @endforeach

                <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #2A2A2A; font-size: 0.7rem; color: #666;">
                    <strong style="color: #F5F5F5;">{{ number_format($this->totalPages) }}</strong> páginas indexables totales
                </div>
            </div>
        </div>
    </div>

    {{-- ===== SECTION 2: CONTENT COVERAGE GRID ===== --}}
    <div class="sec-title" style="margin-top: 1.25rem;">Cobertura de Contenido</div>
    @php
        $coverageCards = [
            ['label' => 'Descripciones ES', 'val' => $this->withAiDescription, 'icon' => '🇲🇽', 'color' => '#D4AF37', 'bg' => 'rgba(212,175,55,0.08)'],
            ['label' => 'Descripciones EN', 'val' => $this->withAiDescriptionEn, 'icon' => '🇺🇸', 'color' => '#3b82f6', 'bg' => 'rgba(59,130,246,0.08)'],
            ['label' => 'Con Fotos', 'val' => $this->withPhotos, 'icon' => '📸', 'color' => '#22c55e', 'bg' => 'rgba(34,197,94,0.08)'],
            ['label' => 'Con Rating', 'val' => $this->withRating, 'icon' => '⭐', 'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.08)'],
            ['label' => 'Con Coordenadas', 'val' => $this->withCoordinates, 'icon' => '📍', 'color' => '#8b5cf6', 'bg' => 'rgba(139,92,246,0.08)'],
            ['label' => 'Con Horarios', 'val' => $this->withHours, 'icon' => '🕐', 'color' => '#06b6d4', 'bg' => 'rgba(6,182,212,0.08)'],
        ];
    @endphp
    <div class="cov-grid" style="margin-bottom: 1rem;">
        @foreach ($coverageCards as $card)
            @php
                $pct = $total > 0 ? round($card['val'] / $total * 100, 1) : 0;
            @endphp
            <div class="cov-card" style="border-color: {{ $card['color'] }}22; background: {{ $card['bg'] }};">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                    <div style="font-size: 0.65rem; color: #888; text-transform: uppercase; letter-spacing: 0.08em;">{{ $card['label'] }}</div>
                    <div style="font-size: 1.1rem;">{{ $card['icon'] }}</div>
                </div>
                <div class="cov-pct" style="color: {{ $card['color'] }};">{{ $pct }}%</div>
                <div style="font-size: 0.7rem; color: #666; margin-top: 0.1rem;">{{ number_format($card['val']) }} restaurantes</div>
                <div class="prog-bar-wrap" style="margin-top: 0.6rem;">
                    <div class="prog-bar-fill" style="width: {{ $pct }}%; background: {{ $card['color'] }};"></div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ===== SECTION 3: INDEXABLE PAGES BREAKDOWN ===== --}}
    <div class="seo-card" style="margin-bottom: 1rem;">
        <div class="sec-title">Páginas Indexables</div>
        @php
            $noContent = max(0, $total - max($this->withAiDescription, $this->withPhotos));
            $indexRows = [
                ['label' => 'Con descripción IA (ES)', 'val' => $this->withAiDescription, 'color' => '#D4AF37'],
                ['label' => 'Con descripción IA (EN)', 'val' => $this->withAiDescriptionEn, 'color' => '#3b82f6'],
                ['label' => 'Con fotos Yelp', 'val' => $this->withPhotos, 'color' => '#22c55e'],
                ['label' => 'Con rating de clientes', 'val' => $this->withRating, 'color' => '#f59e0b'],
                ['label' => 'Con dirección completa', 'val' => $this->withAddress, 'color' => '#8b5cf6'],
                ['label' => 'Con horarios de atención', 'val' => $this->withHours, 'color' => '#06b6d4'],
                ['label' => 'Sin descripción (necesitan IA)', 'val' => max(0, $total - $this->withAiDescription), 'color' => '#ef4444'],
            ];
        @endphp

        <div style="display: flex; align-items: baseline; gap: 0.75rem; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid #2A2A2A;">
            <span style="font-size: 2rem; font-weight: 900; color: #F5F5F5;">{{ number_format($this->totalPages) }}</span>
            <span style="font-size: 0.75rem; color: #888;">páginas indexables totales</span>
        </div>

        @foreach ($indexRows as $row)
            @php $p = $total > 0 ? round($row['val'] / $total * 100, 1) : 0; @endphp
            <div class="indexable-row">
                <span class="indexable-label">{{ $row['label'] }}</span>
                <span class="indexable-count">{{ number_format($row['val']) }}</span>
                <div class="indexable-bar-wrap">
                    <div class="indexable-bar-fill" style="width: {{ $p }}%; background: {{ $row['color'] }};"></div>
                </div>
                <span class="indexable-pct">{{ $p }}%</span>
            </div>
        @endforeach
    </div>

    {{-- ===== SECTION 4: BLOG CONTENT ===== --}}
    <div class="sec-title">Blog & Contenido Editorial</div>
    @php
        $blogTotal = $this->blogPostsPublished + $this->blogPostsDraft;
    @endphp
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1rem;">
        <div class="blog-card">
            <div class="blog-num" style="color: #22c55e;">{{ number_format($this->blogPostsPublished) }}</div>
            <div class="blog-label">Posts Publicados</div>
        </div>
        <div class="blog-card">
            <div class="blog-num" style="color: #f59e0b;">{{ number_format($this->blogPostsDraft) }}</div>
            <div class="blog-label">Borradores</div>
        </div>
        <div class="blog-card">
            <div class="blog-num" style="color: #D4AF37;">{{ $blogTotal > 0 ? number_format($this->blogPostsPublished / $blogTotal * 100, 0) : 0 }}%</div>
            <div class="blog-label">Tasa Publicación</div>
        </div>
    </div>

    {{-- ===== SECTION 5: COVERAGE BY STATE ===== --}}
    <div class="seo-card" style="margin-bottom: 1rem;">
        <div class="sec-title">Cobertura por Estado (Top 20)</div>
        <div style="overflow-x: auto;">
            <table class="state-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Estado</th>
                        <th>Restaurantes</th>
                        <th>Con Desc.</th>
                        <th>Con Fotos</th>
                        <th>Cobertura</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->coverageByState as $i => $row)
                        @php
                            $coverPct = $row->count > 0 ? round($row->with_description / $row->count * 100) : 0;
                            $barColor = $coverPct >= 75 ? '#22c55e' : ($coverPct >= 40 ? '#f59e0b' : '#ef4444');
                        @endphp
                        <tr>
                            <td style="color: #555;">{{ $i + 1 }}</td>
                            <td>{{ $row->state_name }}</td>
                            <td style="color: #D4AF37; font-weight: 700;">{{ number_format($row->count) }}</td>
                            <td>{{ number_format($row->with_description) }}</td>
                            <td>{{ number_format($row->with_photos) }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="width: 60px; height: 4px; background: #2A2A2A; border-radius: 2px; overflow: hidden;">
                                        <div style="width: {{ $coverPct }}%; height: 100%; background: {{ $barColor }}; border-radius: 2px;"></div>
                                    </div>
                                    <span style="color: {{ $barColor }}; font-weight: 700;">{{ $coverPct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== SECTION 6: REVIEW SIGNALS ===== --}}
    @php
        try {
            $reviewsThisWeek = \App\Models\Review::where('status','approved')->where('created_at','>=', now()->subDays(7))->count();
            $reviewsThisMonth = \App\Models\Review::where('status','approved')->where('created_at','>=', now()->subDays(30))->count();
        } catch (\Throwable $e) {
            $reviewsThisWeek = 0;
            $reviewsThisMonth = 0;
        }
        $avgStars = round($this->avgRating);
    @endphp

    <div class="sec-title">Señales de Reviews para SEO</div>
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.5rem;">
        <div class="review-card">
            <div class="stat-label">Total Reviews</div>
            <div class="stat-value-lg" style="color: #D4AF37;">{{ number_format($this->totalReviews) }}</div>
            <div class="stat-sub">Aprobadas</div>
        </div>
        <div class="review-card">
            <div class="stat-label">Rating Promedio</div>
            <div class="stat-value-lg" style="color: #f59e0b;">{{ number_format($this->avgRating, 1) }}</div>
            <div class="stars">
                @for ($s = 1; $s <= 5; $s++)
                    <span style="opacity: {{ $s <= $avgStars ? '1' : '0.25' }};">★</span>
                @endfor
            </div>
        </div>
        <div class="review-card">
            <div class="stat-label">Esta Semana</div>
            <div class="stat-value-lg" style="color: #22c55e;">{{ number_format($reviewsThisWeek) }}</div>
            <div class="stat-sub">Últimos 7 días</div>
        </div>
        <div class="review-card">
            <div class="stat-label">Este Mes</div>
            <div class="stat-value-lg" style="color: #3b82f6;">{{ number_format($reviewsThisMonth) }}</div>
            <div class="stat-sub">Últimos 30 días</div>
        </div>
    </div>

</div>
</x-filament-panels::page>
