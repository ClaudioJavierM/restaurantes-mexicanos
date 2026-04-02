@extends('layouts.app')

@section('title')
{{ $current ? (($categories[$current] ?? ucfirst($current)) . ' — Blog FAMER') : 'Blog de Cocina Mexicana | FAMER' }}
@endsection

@section('meta_description')
{{ $current
    ? 'Artículos sobre ' . ($categories[$current] ?? $current) . ' de la cocina mexicana. Descubre la cultura, historia y sabores de México en FAMER.'
    : 'El blog definitivo sobre cocina mexicana auténtica. Historia, recetas, cultura, guías de restaurantes y entrevistas con chefs. Todo sobre la gastronomía mexicana en FAMER.' }}
@endsection

@push('meta')
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ url('/blog') }}{{ $current ? '?categoria=' . $current : '' }}">
@if($featured && !$current)
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Article",
    "headline": "{{ addslashes($featured->seo_title ?? $featured->title) }}",
    "description": "{{ addslashes($featured->seo_description ?? $featured->excerpt) }}",
    "author": { "@@type": "Person", "name": "{{ $featured->author }}" },
    "datePublished": "{{ $featured->published_at?->toIso8601String() }}",
    "publisher": {
        "@@type": "Organization",
        "name": "FAMER",
        "logo": { "@@type": "ImageObject", "url": "{{ asset('images/branding/famer55.png') }}" }
    }
    @if($featured->cover_image)
    ,"image": "{{ str_starts_with($featured->cover_image, 'http') ? $featured->cover_image : Storage::url($featured->cover_image) }}"
    @endif
}
</script>
@endif
@endpush

@section('content')
<div class="min-h-screen" style="background:#0B0B0B; color:#F5F5F5;">

    {{-- ── Hero ─────────────────────────────────────────────────────────────── --}}
    <div style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 50%,#0B0B0B 100%); border-bottom:1px solid #2A2A2A; padding:4rem 0 3rem;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <nav style="margin-bottom:1.5rem;">
                <ol style="display:flex; justify-content:center; flex-wrap:wrap; gap:0.5rem; align-items:center; font-size:0.875rem; color:#9CA3AF;">
                    <li><a href="/" style="color:#D4AF37; text-decoration:none;">FAMER</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li style="color:#9CA3AF;">Blog</li>
                    @if($current)
                        <li style="color:#4B5563;">/</li>
                        <li style="color:#9CA3AF;">{{ $categories[$current] ?? ucfirst($current) }}</li>
                    @endif
                </ol>
            </nav>
            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.5rem); font-weight:700; color:#F5F5F5; margin-bottom:1rem; line-height:1.2;">
                Blog <span style="color:#D4AF37;">FAMER</span>
            </h1>
            <p style="color:#9CA3AF; font-size:1.125rem; max-width:600px; margin:0 auto;">
                Cultura, Historia y Sabores de México
            </p>
        </div>
    </div>

    {{-- ── Category Filter Pills ───────────────────────────────────────────── --}}
    <div style="background:#1A1A1A; border-bottom:1px solid #2A2A2A; padding:1rem 0; overflow-x:auto; white-space:nowrap;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="display:flex; gap:0.75rem; align-items:center;">
            <a href="/blog"
               style="display:inline-block; padding:0.5rem 1.25rem; border-radius:9999px; font-size:0.875rem; font-weight:600; text-decoration:none; transition:all 0.2s;
                      {{ !$current ? 'background:#D4AF37; color:#0B0B0B;' : 'background:#2A2A2A; color:#9CA3AF; border:1px solid #3A3A3A;' }}">
                Todos
            </a>
            @foreach($categories as $key => $label)
            <a href="/blog/categoria/{{ $key }}"
               style="display:inline-block; padding:0.5rem 1.25rem; border-radius:9999px; font-size:0.875rem; font-weight:600; text-decoration:none; transition:all 0.2s;
                      {{ $current === $key ? 'background:#D4AF37; color:#0B0B0B;' : 'background:#2A2A2A; color:#9CA3AF; border:1px solid #3A3A3A;' }}"
               onmouseover="{{ $current === $key ? '' : "this.style.borderColor='#D4AF37'; this.style.color='#D4AF37';" }}"
               onmouseout="{{ $current === $key ? '' : "this.style.borderColor='#3A3A3A'; this.style.color='#9CA3AF';" }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- ── Featured Post ───────────────────────────────────────────────── --}}
        @if($featured && !$current)
        <section style="margin-bottom:3.5rem;">
            <a href="/blog/{{ $featured->slug }}"
               style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; overflow:hidden; text-decoration:none; transition:border-color 0.2s;"
               onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                <div style="display:grid; grid-template-columns:1fr; gap:0;">
                    @if($featured->cover_image)
                    <div style="height:320px; overflow:hidden; position:relative;">
                        <img src="{{ str_starts_with($featured->cover_image, 'http') ? $featured->cover_image : Storage::url($featured->cover_image) }}"
                             alt="{{ $featured->title }}"
                             loading="eager"
                             style="width:100%; height:100%; object-fit:cover;">
                        <div style="position:absolute; inset:0; background:linear-gradient(to right, rgba(11,11,11,0.6) 0%, transparent 60%);"></div>
                        <div style="position:absolute; top:1rem; left:1rem;">
                            <span style="background:#D4AF37; color:#0B0B0B; padding:0.25rem 0.75rem; border-radius:9999px; font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">
                                Destacado
                            </span>
                        </div>
                    </div>
                    @endif
                    <div style="padding:2rem;">
                        @if($featured->category)
                        <span style="display:inline-block; background:#2A2A2A; color:#D4AF37; padding:0.2rem 0.6rem; border-radius:4px; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.75rem;">
                            {{ $categories[$featured->category] ?? ucfirst($featured->category) }}
                        </span>
                        @endif
                        <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.5rem,3vw,2rem); font-weight:700; color:#F5F5F5; margin-bottom:0.75rem; line-height:1.3;">
                            {{ $featured->title }}
                        </h2>
                        @if($featured->excerpt)
                        <p style="color:#9CA3AF; line-height:1.7; margin-bottom:1.25rem; font-size:1rem;">
                            {{ $featured->excerpt }}
                        </p>
                        @endif
                        <div style="display:flex; align-items:center; gap:1.25rem; flex-wrap:wrap; font-size:0.8125rem; color:#6B7280;">
                            <span style="display:flex; align-items:center; gap:0.35rem;">
                                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $featured->author }}
                            </span>
                            <span style="display:flex; align-items:center; gap:0.35rem;">
                                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $featured->read_time }} min lectura
                            </span>
                            @if($featured->published_at)
                            <span>{{ $featured->published_at->format('d M Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        </section>
        @endif

        {{-- ── Post Grid ───────────────────────────────────────────────────── --}}
        @if($posts->count())
        <section>
            @if($current)
            <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#F5F5F5; margin-bottom:1.5rem;">
                {{ $categories[$current] ?? ucfirst($current) }}
                <span style="color:#6B7280; font-size:1rem; font-weight:400; font-family:inherit;">— {{ $posts->total() }} artículos</span>
            </h2>
            @endif
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:1.5rem; margin-bottom:3rem;">
                @foreach($posts as $post)
                @if(!$featured || $post->id !== $featured->id || $current)
                <a href="/blog/{{ $post->slug }}"
                   style="display:flex; flex-direction:column; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; overflow:hidden; text-decoration:none; transition:border-color 0.2s;"
                   onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                    {{-- Cover image --}}
                    @if($post->cover_image)
                    <div style="height:200px; overflow:hidden;">
                        <img src="{{ str_starts_with($post->cover_image, 'http') ? $post->cover_image : Storage::url($post->cover_image) }}"
                             alt="{{ $post->title }}"
                             loading="lazy"
                             style="width:100%; height:100%; object-fit:cover; transition:transform 0.3s;"
                             onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                    </div>
                    @else
                    <div style="height:120px; background:#2A2A2A; display:flex; align-items:center; justify-content:center;">
                        <span style="font-size:2.5rem;">🍽️</span>
                    </div>
                    @endif

                    <div style="padding:1.25rem; flex:1; display:flex; flex-direction:column;">
                        {{-- Category badge --}}
                        @if($post->category)
                        <span style="display:inline-block; background:#2A2A2A; color:#D4AF37; padding:0.2rem 0.6rem; border-radius:4px; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.625rem; width:fit-content;">
                            {{ $categories[$post->category] ?? ucfirst($post->category) }}
                        </span>
                        @endif

                        {{-- Title --}}
                        <h3 style="font-weight:700; color:#F5F5F5; margin-bottom:0.5rem; font-size:1rem; line-height:1.4; flex:1;">
                            {{ $post->title }}
                        </h3>

                        {{-- Excerpt (2 lines) --}}
                        @if($post->excerpt)
                        <p style="color:#9CA3AF; font-size:0.875rem; line-height:1.5; margin-bottom:1rem;
                                  display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                            {{ $post->excerpt }}
                        </p>
                        @endif

                        {{-- Meta --}}
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:0.5rem; font-size:0.75rem; color:#6B7280; flex-wrap:wrap; margin-top:auto;">
                            <span>{{ $post->author }}</span>
                            <div style="display:flex; align-items:center; gap:0.75rem;">
                                <span>{{ $post->read_time }} min</span>
                                @if($post->published_at)
                                <span>{{ $post->published_at->format('d M Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
                @endif
                @endforeach
            </div>

            {{-- ── Pagination ──────────────────────────────────────────── --}}
            @if($posts->hasPages())
            <div style="display:flex; justify-content:center; gap:0.5rem; flex-wrap:wrap; padding:2rem 0;">
                {{-- Previous --}}
                @if($posts->onFirstPage())
                    <span style="padding:0.5rem 1rem; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:8px; color:#4B5563; font-size:0.875rem;">← Anterior</span>
                @else
                    <a href="{{ $posts->previousPageUrl() }}" style="padding:0.5rem 1rem; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:8px; color:#9CA3AF; font-size:0.875rem; text-decoration:none; transition:border-color 0.2s;"
                       onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">← Anterior</a>
                @endif

                {{-- Page numbers --}}
                @foreach($posts->getUrlRange(max(1, $posts->currentPage() - 2), min($posts->lastPage(), $posts->currentPage() + 2)) as $page => $url)
                    @if($page == $posts->currentPage())
                        <span style="padding:0.5rem 1rem; background:#D4AF37; color:#0B0B0B; border-radius:8px; font-size:0.875rem; font-weight:700;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="padding:0.5rem 1rem; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:8px; color:#9CA3AF; font-size:0.875rem; text-decoration:none; transition:border-color 0.2s;"
                           onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($posts->hasMorePages())
                    <a href="{{ $posts->nextPageUrl() }}" style="padding:0.5rem 1rem; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:8px; color:#9CA3AF; font-size:0.875rem; text-decoration:none; transition:border-color 0.2s;"
                       onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">Siguiente →</a>
                @else
                    <span style="padding:0.5rem 1rem; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:8px; color:#4B5563; font-size:0.875rem;">Siguiente →</span>
                @endif
            </div>
            @endif
        </section>

        @else
        {{-- Empty state --}}
        <div style="text-align:center; padding:5rem 0; color:#6B7280;">
            <div style="font-size:3rem; margin-bottom:1rem;">📝</div>
            <p style="font-size:1.125rem;">Próximamente — el blog FAMER está en camino.</p>
        </div>
        @endif

    </div>
</div>
@endsection
