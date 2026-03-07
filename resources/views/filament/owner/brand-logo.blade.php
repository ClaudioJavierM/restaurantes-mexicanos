@php
    $logoUrl = null;
    $siteLogo = asset('images/branding/sombrero-icon.png');

    if (auth()->check()) {
        $restaurant = auth()->user()->restaurants()->first();
        if ($restaurant && $restaurant->logo) {
            $logoUrl = asset('storage/' . $restaurant->logo);
        }
    }
@endphp

<div style="display: flex; align-items: center; gap: 0.5rem;">
    <img
        src="{{ $logoUrl ?? $siteLogo }}"
        alt="Logo"
        style="height: 2.25rem; width: 2.25rem; border-radius: 50%; object-fit: cover; border: 2px solid rgba(212,165,74,0.5);"
    />
    <span style="font-weight: 600; font-size: 0.85rem; color: #1f2937;">
        {{ $restaurant->name ?? 'Mi Dashboard' }}
    </span>
</div>