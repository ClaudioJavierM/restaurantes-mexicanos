@props(['review'])

@if ($review->owner_reply)
    <div style="background: linear-gradient(135deg, #111111 0%, #1A1A1A 100%); border: 1px solid rgba(212, 175, 55, 0.3); border-left: 3px solid #D4AF37; border-radius: 0 10px 10px 0; padding: 1.125rem 1.375rem; margin-top: 0.875rem;">

        {{-- Owner badge header --}}
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
            {{-- Gold shield avatar --}}
            <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #D4AF37, #B8973A); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="18" height="18" fill="none" stroke="#0B0B0B" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>

            <div>
                <p style="color: #D4AF37; font-weight: 700; font-size: 0.875rem; margin: 0 0 0.1rem 0; font-family: 'Poppins', sans-serif;">
                    Respuesta del Propietario
                </p>
                @if ($review->owner_replied_at)
                    <p style="color: #6B7280; font-size: 0.75rem; margin: 0; font-family: 'Poppins', sans-serif;">
                        {{ $review->owner_replied_at->diffForHumans() }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Reply text --}}
        <p style="color: #D1D5DB; font-size: 0.9375rem; line-height: 1.65; margin: 0; font-family: 'Poppins', sans-serif; font-style: italic;">
            "{{ $review->owner_reply }}"
        </p>

    </div>
@endif
