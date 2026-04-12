@if($isVisible && $currentStep >= 1 && $currentStep <= 5)
<div>
    {{-- Dark overlay --}}
    <div style="position:fixed;inset:0;background:rgba(0,0,0,0.80);z-index:9000;pointer-events:all;"></div>

    {{-- Tour card — centered, above overlay --}}
    <div style="position:fixed;bottom:2rem;left:50%;transform:translateX(-50%);z-index:9001;width:100%;max-width:420px;padding:0 1rem;">
        <div style="background:#1A1A1A;border:1px solid #D4AF37;border-radius:1rem;padding:1.5rem;box-shadow:0 20px 60px rgba(0,0,0,0.6);">

            {{-- Step indicator --}}
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                <div style="display:flex;gap:0.375rem;">
                    @for($i = 1; $i <= 5; $i++)
                        <div style="width:0.5rem;height:0.5rem;border-radius:50%;background:{{ $i <= $currentStep ? '#D4AF37' : '#2A2A2A' }};transition:background 0.3s;"></div>
                    @endfor
                </div>
                <span style="color:#9CA3AF;font-size:0.75rem;">{{ $currentStep }}/5</span>
            </div>

            {{-- Icon + Title --}}
            <div style="margin-bottom:0.75rem;">
                <span style="font-size:1.75rem;">{{ $steps[$currentStep]['icon'] }}</span>
                <h3 style="color:#F5F5F5;font-size:1.125rem;font-weight:700;margin:0.375rem 0 0;font-family:'Playfair Display',serif;">
                    {{ $steps[$currentStep]['title'] }}
                </h3>
            </div>

            {{-- Body --}}
            <p style="color:#9CA3AF;font-size:0.9rem;line-height:1.6;margin-bottom:1.25rem;">
                {{ $steps[$currentStep]['body'] }}
            </p>

            {{-- Actions --}}
            <div style="display:flex;gap:0.75rem;align-items:center;justify-content:space-between;">
                <button
                    wire:click="skipOnboarding"
                    style="color:#6B7280;font-size:0.8rem;background:none;border:none;cursor:pointer;text-decoration:underline;padding:0;"
                >
                    Saltar tutorial
                </button>
                <div style="display:flex;gap:0.5rem;">
                    <a
                        href="{{ $steps[$currentStep]['cta_url'] }}"
                        style="padding:0.5rem 1rem;border-radius:0.5rem;font-size:0.85rem;font-weight:600;color:#0B0B0B;background:#D4AF37;text-decoration:none;display:inline-block;"
                    >
                        {{ $steps[$currentStep]['cta'] }}
                    </a>
                    <button
                        wire:click="nextStep"
                        style="padding:0.5rem 1rem;border-radius:0.5rem;font-size:0.85rem;font-weight:600;color:#F5F5F5;background:#2A2A2A;border:none;cursor:pointer;"
                    >
                        {{ $currentStep < 5 ? 'Siguiente →' : '¡Terminar!' }}
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
@endif
