<div style="background:#0B0B0B; color:#E8E8E8; font-family:'Inter',sans-serif; padding:24px; border-radius:12px; min-height:400px;">

    @php
        $avgScore   = $summary['avg_score'] ?? null;
        $scoreInt   = $avgScore !== null ? (int) round($avgScore * 100) : null;
        $labels     = $summary['label_counts'] ?? [];
        $total      = array_sum($labels);
        $posKws     = $summary['top_positive_keywords'] ?? [];
        $negKws     = $summary['top_negative_keywords'] ?? [];
        $catScores  = $summary['category_scores'] ?? [];
        $analyzed   = $summary['total_analyzed'] ?? 0;

        $labelColors = [
            'very_positive' => '#22c55e',
            'positive'      => '#86efac',
            'neutral'       => '#94a3b8',
            'negative'      => '#f87171',
            'very_negative' => '#8B1E1E',
        ];
        $labelNames = [
            'very_positive' => 'Muy Positivo',
            'positive'      => 'Positivo',
            'neutral'       => 'Neutral',
            'negative'      => 'Negativo',
            'very_negative' => 'Muy Negativo',
        ];

        // Circular gauge CSS variables
        $dashOffset = $scoreInt !== null ? (int) round((100 - $scoreInt) * 2.51327) : 251;

        // Category star helper
        $toStars = function(?float $s): string {
            if ($s === null) return '—';
            $stars = (int) round($s * 5);
            return str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
        };

        $categoryIcons = [
            'food'     => '🍽️',
            'service'  => '🤝',
            'ambiance' => '✨',
            'price'    => '💰',
            'location' => '📍',
        ];
        $categoryNames = [
            'food'     => 'Comida',
            'service'  => 'Servicio',
            'ambiance' => 'Ambiente',
            'price'    => 'Precio',
            'location' => 'Ubicación',
        ];
    @endphp

    {{-- ===== HEADER ===== --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:28px;">
        <div>
            <h2 style="margin:0; font-size:20px; font-weight:700; color:#D4AF37; letter-spacing:0.5px;">
                Análisis de Sentimiento
            </h2>
            <p style="margin:4px 0 0; font-size:13px; color:#666;">
                {{ $analyzed }} reseñas analizadas con IA
            </p>
        </div>
        <div style="background:#1A1A1A; border:1px solid #333; border-radius:8px; padding:8px 14px; font-size:12px; color:#888; text-align:center;">
            <div style="color:#D4AF37; font-weight:600;">🤖 GPT-4o-mini</div>
            <div>Actualiza semanalmente</div>
        </div>
    </div>

    @if($analyzed === 0)
        {{-- Empty state --}}
        <div style="text-align:center; padding:60px 20px; background:#111; border-radius:12px; border:1px dashed #333;">
            <div style="font-size:48px; margin-bottom:12px;">🔍</div>
            <p style="font-size:16px; color:#888; margin:0 0 8px;">Sin análisis disponible</p>
            <p style="font-size:13px; color:#555; margin:0;">
                Las reseñas se analizan automáticamente cada semana.<br>
                Ejecuta <code style="background:#1A1A1A; padding:2px 6px; border-radius:4px; color:#D4AF37;">php artisan famer:analyze-sentiment --restaurant={{ $restaurantId }}</code> para analizar ahora.
            </p>
        </div>
    @else

        {{-- ===== TOP ROW: GAUGE + DISTRIBUTION ===== --}}
        <div style="display:grid; grid-template-columns:200px 1fr; gap:20px; margin-bottom:24px;">

            {{-- Circular gauge --}}
            <div style="background:#111; border-radius:12px; border:1px solid #222; padding:20px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <svg width="140" height="140" viewBox="0 0 140 140" style="transform:rotate(-90deg);">
                    <circle cx="70" cy="70" r="56" fill="none" stroke="#1A1A1A" stroke-width="14"/>
                    <circle cx="70" cy="70" r="56" fill="none"
                        stroke="{{ $scoreInt >= 70 ? '#D4AF37' : ($scoreInt >= 50 ? '#86efac' : '#8B1E1E') }}"
                        stroke-width="14"
                        stroke-linecap="round"
                        stroke-dasharray="351.858"
                        stroke-dashoffset="{{ $dashOffset }}"/>
                </svg>
                <div style="margin-top:-90px; text-align:center; position:relative; z-index:2;">
                    <div style="font-size:32px; font-weight:800; color:#D4AF37; line-height:1;">{{ $scoreInt }}%</div>
                    <div style="font-size:11px; color:#888; margin-top:2px;">sentimiento</div>
                </div>
                <div style="margin-top:70px; font-size:12px; color:#aaa; text-align:center; line-height:1.4;">
                    Puntuación promedio<br>
                    <span style="color:#D4AF37; font-weight:600;">{{ number_format($avgScore, 2) }} / 1.00</span>
                </div>
            </div>

            {{-- Distribution bars --}}
            <div style="background:#111; border-radius:12px; border:1px solid #222; padding:20px;">
                <h3 style="margin:0 0 16px; font-size:13px; font-weight:600; color:#aaa; text-transform:uppercase; letter-spacing:1px;">Distribución de Opiniones</h3>
                @foreach(['very_positive','positive','neutral','negative','very_negative'] as $lbl)
                    @php
                        $count = $labels[$lbl] ?? 0;
                        $pct   = $total > 0 ? round(($count / $total) * 100) : 0;
                        $color = $labelColors[$lbl];
                        $name  = $labelNames[$lbl];
                    @endphp
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                        <span style="width:90px; font-size:12px; color:#aaa; flex-shrink:0;">{{ $name }}</span>
                        <div style="flex:1; background:#1A1A1A; border-radius:4px; height:10px; overflow:hidden;">
                            <div style="width:{{ $pct }}%; background:{{ $color }}; height:100%; border-radius:4px; transition:width 0.6s ease;"></div>
                        </div>
                        <span style="width:30px; text-align:right; font-size:12px; color:#666;">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ===== KEYWORDS ROW ===== --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">

            {{-- Positive keywords --}}
            <div style="background:#111; border-radius:12px; border:1px solid #222; padding:20px;">
                <h3 style="margin:0 0 14px; font-size:13px; font-weight:600; color:#22c55e; text-transform:uppercase; letter-spacing:1px;">
                    👍 Lo que dicen de tu comida
                </h3>
                @if(count($posKws) > 0)
                    <div style="display:flex; flex-wrap:wrap; gap:8px;">
                        @foreach($posKws as $kw)
                            <span style="background:#14532d; color:#86efac; border:1px solid #22c55e44; padding:5px 12px; border-radius:20px; font-size:13px; font-weight:500;">
                                {{ $kw }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p style="color:#555; font-size:13px; margin:0;">Sin datos suficientes aún.</p>
                @endif
            </div>

            {{-- Negative keywords --}}
            <div style="background:#111; border-radius:12px; border:1px solid #222; padding:20px;">
                <h3 style="margin:0 0 14px; font-size:13px; font-weight:600; color:#f87171; text-transform:uppercase; letter-spacing:1px;">
                    ⚠️ Áreas de mejora
                </h3>
                @if(count($negKws) > 0)
                    <div style="display:flex; flex-wrap:wrap; gap:8px;">
                        @foreach($negKws as $kw)
                            <span style="background:#450a0a; color:#fca5a5; border:1px solid #8B1E1E; padding:5px 12px; border-radius:20px; font-size:13px; font-weight:500;">
                                {{ $kw }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p style="color:#555; font-size:13px; margin:0;">Sin retroalimentación negativa detectada. ✅</p>
                @endif
            </div>
        </div>

        {{-- ===== CATEGORY SCORES ===== --}}
        <div style="background:#111; border-radius:12px; border:1px solid #222; padding:20px; margin-bottom:24px;">
            <h3 style="margin:0 0 16px; font-size:13px; font-weight:600; color:#aaa; text-transform:uppercase; letter-spacing:1px;">Desglose por Categoría</h3>
            <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:12px;">
                @foreach(['food','service','ambiance','price','location'] as $cat)
                    @php
                        $catScore = $catScores[$cat] ?? null;
                        $stars    = $toStars($catScore);
                        $icon     = $categoryIcons[$cat];
                        $name     = $categoryNames[$cat];
                    @endphp
                    <div style="text-align:center; background:#0B0B0B; border-radius:8px; border:1px solid #222; padding:14px 8px;">
                        <div style="font-size:22px; margin-bottom:4px;">{{ $icon }}</div>
                        <div style="font-size:11px; color:#888; margin-bottom:6px;">{{ $name }}</div>
                        <div style="font-size:16px; color:#D4AF37; letter-spacing:2px;">{{ $stars }}</div>
                        @if($catScore !== null)
                            <div style="font-size:11px; color:#555; margin-top:4px;">{{ number_format($catScore, 2) }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ===== RECENT REVIEWS ===== --}}
        @if($recentReviews->count() > 0)
        <div style="background:#111; border-radius:12px; border:1px solid #222; padding:20px;">
            <h3 style="margin:0 0 16px; font-size:13px; font-weight:600; color:#aaa; text-transform:uppercase; letter-spacing:1px;">
                Reseñas Recientes Analizadas
            </h3>
            @foreach($recentReviews as $review)
                @php
                    $lbl   = $review->sentiment_label ?? 'neutral';
                    $color = $labelColors[$lbl] ?? '#94a3b8';
                    $name  = $labelNames[$lbl] ?? 'Neutral';
                    $pct   = $review->sentiment_score !== null ? (int) round($review->sentiment_score * 100) : null;
                @endphp
                <div style="border-bottom:1px solid #1A1A1A; padding:12px 0; display:flex; align-items:flex-start; gap:12px;">
                    {{-- Sentiment badge --}}
                    <div style="flex-shrink:0; text-align:center; width:70px;">
                        <span style="display:inline-block; background:{{ $color }}22; color:{{ $color }}; border:1px solid {{ $color }}44; border-radius:12px; font-size:10px; font-weight:600; padding:3px 8px; white-space:nowrap;">
                            {{ $name }}
                        </span>
                        @if($pct !== null)
                            <div style="font-size:11px; color:#555; margin-top:3px;">{{ $pct }}%</div>
                        @endif
                    </div>

                    {{-- Review content --}}
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                            <span style="font-size:13px; font-weight:600; color:#D4AF37;">
                                {{ $review->guest_name ?? $review->name ?? 'Anónimo' }}
                            </span>
                            <span style="color:#555; font-size:11px;">
                                {{ $review->sentiment_analyzed_at?->format('d M Y') }}
                            </span>
                        </div>
                        @if($review->title)
                            <div style="font-size:13px; color:#ccc; font-weight:500; margin-bottom:2px;">{{ $review->title }}</div>
                        @endif
                        <div style="font-size:12px; color:#666; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                            {{ $review->comment }}
                        </div>
                        {{-- Keywords for this review --}}
                        @if(!empty($review->sentiment_keywords))
                            <div style="display:flex; flex-wrap:wrap; gap:4px; margin-top:6px;">
                                @foreach(array_slice($review->sentiment_keywords, 0, 5) as $kw)
                                    @php
                                        $kwColor = match($kw['sentiment'] ?? 'neutral') {
                                            'positive' => '#14532d',
                                            'negative' => '#450a0a',
                                            default    => '#1A1A1A',
                                        };
                                        $kwText = match($kw['sentiment'] ?? 'neutral') {
                                            'positive' => '#86efac',
                                            'negative' => '#fca5a5',
                                            default    => '#666',
                                        };
                                    @endphp
                                    <span style="background:{{ $kwColor }}; color:{{ $kwText }}; font-size:10px; padding:2px 7px; border-radius:10px;">
                                        {{ $kw['word'] }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Rating --}}
                    <div style="flex-shrink:0; text-align:center;">
                        <div style="font-size:20px; font-weight:800; color:#D4AF37;">{{ $review->rating }}</div>
                        <div style="font-size:10px; color:#555;">/ 5</div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        {{-- ===== FOOTER NOTE ===== --}}
        <div style="margin-top:16px; text-align:center; font-size:12px; color:#444;">
            El análisis se actualiza automáticamente cada semana. &nbsp;·&nbsp;
            <span style="color:#D4AF37;">GPT-4o-mini</span> analiza texto, tono y categorías de experiencia.
        </div>

    @endif
</div>
