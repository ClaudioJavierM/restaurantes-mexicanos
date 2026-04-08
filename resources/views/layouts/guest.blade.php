<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'FAMER') }}</title>
        <link rel="canonical" href="{{ strtok(url()->current(), '?') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body style="background:#0B0B0B; font-family:'Poppins',sans-serif; min-height:100vh; margin:0;">

        {{-- Background photo --}}
        <div style="position:fixed; inset:0; z-index:0; pointer-events:none;">
            <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1600&q=80"
                 alt="" aria-hidden="true"
                 style="width:100%; height:100%; object-fit:cover; opacity:0.08;">
            <div style="position:absolute; inset:0; background:radial-gradient(ellipse 80% 60% at 50% 30%, rgba(212,175,55,0.06) 0%, transparent 70%);"></div>
        </div>

        {{-- Page --}}
        <div style="position:relative; z-index:1; min-height:100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:2rem 1rem;">

            {{-- Logo --}}
            <div style="text-align:center; margin-bottom:1.75rem;">
                <a href="/" wire:navigate style="text-decoration:none; display:inline-flex; flex-direction:column; align-items:center; gap:0.5rem;">
                    <img src="/images/branding/famer55.png" alt="FAMER"
                         style="width:64px; height:64px; object-fit:contain;">
                    <span style="font-family:'Playfair Display',serif; font-size:1.375rem; font-weight:700; color:#F5F5F5; line-height:1;">FAMER</span>
                    <span style="font-size:0.6875rem; font-weight:700; color:#D4AF37; letter-spacing:0.12em; text-transform:uppercase;">Famous Mexican Restaurants</span>
                </a>
            </div>

            {{-- Card --}}
            <div style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.2); border-radius:1.25rem; width:100%; max-width:460px; padding:2rem 2rem 1.75rem; color:#F5F5F5;">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <p style="margin-top:1.5rem; font-size:0.75rem; color:#4B5563; text-align:center;">
                &copy; {{ date('Y') }} FAMER &mdash; Encuentra los mejores restaurantes mexicanos
            </p>
        </div>

    </body>
</html>
