<div>

    {{-- Summary Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-bottom:1.5rem;">

        <div style="display:flex; align-items:center; gap:1rem;">
            {{-- Total & Average --}}
            <div>
                <span style="color:#F5F5F5; font-family:'Poppins',sans-serif; font-size:1.1rem; font-weight:600;">
                    {{ $totalReviews }} {{ $totalReviews === 1 ? 'reseña' : 'reseñas' }} de platillos
                </span>
                @if($totalReviews > 0)
                    <div style="display:flex; align-items:center; gap:0.375rem; margin-top:0.25rem;">
                        <span style="color:#D4AF37; font-size:1.1rem;">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($avgRating))★@else☆@endif
                            @endfor
                        </span>
                        <span style="color:#9CA3AF; font-family:'Poppins',sans-serif; font-size:0.875rem;">
                            {{ number_format($avgRating, 1) }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sort Buttons --}}
        @if($totalReviews > 0)
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            @foreach([
                'recent'  => 'Recientes',
                'highest' => 'Mejor calificados',
                'helpful' => 'Más útiles',
            ] as $key => $label)
                <button
                    wire:click="$set('sortBy', '{{ $key }}')"
                    style="
                        background: {{ $sortBy === $key ? '#D4AF37' : '#1A1A1A' }};
                        color: {{ $sortBy === $key ? '#0B0B0B' : '#9CA3AF' }};
                        border: 1px solid {{ $sortBy === $key ? '#D4AF37' : '#2A2A2A' }};
                        border-radius: 0.5rem;
                        padding: 0.4rem 0.875rem;
                        font-size: 0.8125rem;
                        font-weight: {{ $sortBy === $key ? '600' : '400' }};
                        font-family: 'Poppins', sans-serif;
                        cursor: pointer;
                        transition: all 0.2s ease;
                    "
                >{{ $label }}</button>
            @endforeach
        </div>
        @endif

    </div>

    {{-- Empty State --}}
    @if($totalReviews === 0)
        <div style="text-align:center; padding:3rem 1rem; border:1px dashed #2A2A2A; border-radius:12px;">
            <div style="font-size:2rem; margin-bottom:0.75rem; opacity:0.4;">🍽️</div>
            <p style="color:#6B7280; font-family:'Poppins',sans-serif; font-size:0.9375rem; margin:0;">
                Sé el primero en calificar un platillo
            </p>
        </div>

    @else
        {{-- Review Cards --}}
        <div style="display:flex; flex-direction:column; gap:1rem;">
            @foreach($reviews as $review)
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1.25rem;">

                    {{-- Card Header --}}
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:0.75rem; flex-wrap:wrap; gap:0.5rem;">
                        <div>
                            {{-- Reviewer Name --}}
                            <span style="color:#F5F5F5; font-family:'Poppins',sans-serif; font-size:0.9375rem; font-weight:600;">
                                {{ $review->display_name }}
                            </span>
                            {{-- Verified Badge --}}
                            @if($review->is_verified_purchase)
                                <span style="
                                    display:inline-block;
                                    margin-left:0.5rem;
                                    background:rgba(74,222,128,0.15);
                                    color:#4ADE80;
                                    border:1px solid rgba(74,222,128,0.3);
                                    border-radius:0.375rem;
                                    padding:0.125rem 0.5rem;
                                    font-size:0.6875rem;
                                    font-weight:600;
                                    font-family:'Poppins',sans-serif;
                                    letter-spacing:0.04em;
                                    text-transform:uppercase;
                                    vertical-align:middle;
                                ">✓ Compra verificada</span>
                            @endif
                        </div>

                        {{-- Date --}}
                        <span style="color:#6B7280; font-family:'Poppins',sans-serif; font-size:0.8125rem;">
                            {{ $review->created_at->diffForHumans() }}
                        </span>
                    </div>

                    {{-- Dish Name --}}
                    <div style="margin-bottom:0.5rem;">
                        <span style="color:#D4AF37; font-family:'Poppins',sans-serif; font-size:0.875rem; font-weight:500;">
                            🍴 {{ $review->dish_name }}
                        </span>
                    </div>

                    {{-- Star Rating --}}
                    <div style="margin-bottom:0.75rem;">
                        <span style="color:#D4AF37; font-size:1rem; letter-spacing:2px;">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)★@else<span style="color:#3A3A3A;">★</span>@endif
                            @endfor
                        </span>
                        <span style="color:#6B7280; font-family:'Poppins',sans-serif; font-size:0.8125rem; margin-left:0.375rem;">
                            {{ $review->rating }}/5
                        </span>
                    </div>

                    {{-- Comment --}}
                    <p style="color:#D1D5DB; font-family:'Poppins',sans-serif; font-size:0.9375rem; line-height:1.6; margin:0 0 1rem;">
                        {{ $review->comment }}
                    </p>

                    {{-- Helpful Button --}}
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <button
                            wire:click="markHelpful({{ $review->id }})"
                            style="
                                background:transparent;
                                color:#6B7280;
                                border:1px solid #2A2A2A;
                                border-radius:0.5rem;
                                padding:0.3rem 0.75rem;
                                font-size:0.8125rem;
                                font-family:'Poppins',sans-serif;
                                cursor:pointer;
                                display:flex;
                                align-items:center;
                                gap:0.375rem;
                                transition:all 0.2s ease;
                            "
                            onmouseover="this.style.borderColor='rgba(212,175,55,0.4)'; this.style.color='#D4AF37';"
                            onmouseout="this.style.borderColor='#2A2A2A'; this.style.color='#6B7280';"
                        >
                            👍 ¿Útil?
                            @if($review->helpful_count > 0)
                                <span style="
                                    background:#2A2A2A;
                                    color:#9CA3AF;
                                    border-radius:0.25rem;
                                    padding:0.1rem 0.375rem;
                                    font-size:0.75rem;
                                ">{{ $review->helpful_count }}</span>
                            @endif
                        </button>
                    </div>

                </div>
            @endforeach
        </div>

    @endif

</div>
