@extends('layouts.app')

@section('title')
{{ $post->seo_title ?? $post->title . ' | Blog FAMER' }}
@endsection

@section('meta_description')
{{ $post->seo_description ?? $post->excerpt ?? 'Lee este artículo en el blog de FAMER sobre cocina mexicana auténtica.' }}
@endsection

@push('meta')
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ url('/blog/' . $post->slug) }}">
<meta property="og:title" content="{{ $post->seo_title ?? $post->title }}">
<meta property="og:description" content="{{ $post->seo_description ?? $post->excerpt }}">
<meta property="og:type" content="article">
<meta property="og:url" content="{{ url('/blog/' . $post->slug) }}">
@if($post->cover_image)
<meta property="og:image" content="{{ str_starts_with($post->cover_image, 'http') ? $post->cover_image : Storage::url($post->cover_image) }}">
@endif
<meta property="article:author" content="{{ $post->author }}">
@if($post->published_at)
<meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
@endif
@if($post->category)
<meta property="article:section" content="{{ ucfirst($post->category) }}">
@endif

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Article",
    "headline": "{{ addslashes($post->seo_title ?? $post->title) }}",
    "description": "{{ addslashes($post->seo_description ?? $post->excerpt ?? '') }}",
    "author": {
        "@@type": "Person",
        "name": "{{ $post->author }}"
    },
    "datePublished": "{{ $post->published_at?->toIso8601String() }}",
    "dateModified": "{{ $post->updated_at->toIso8601String() }}",
    "publisher": {
        "@@type": "Organization",
        "name": "FAMER",
        "logo": {
            "@@type": "ImageObject",
            "url": "{{ asset('images/branding/famer55.png') }}"
        }
    },
    "url": "{{ url('/blog/' . $post->slug) }}"
    @if($post->cover_image)
    ,"image": {
        "@@type": "ImageObject",
        "url": "{{ str_starts_with($post->cover_image, 'http') ? $post->cover_image : Storage::url($post->cover_image) }}"
    }
    @endif
}
</script>

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        { "@@type": "ListItem", "position": 1, "name": "FAMER", "item": "{{ url('/') }}" },
        { "@@type": "ListItem", "position": 2, "name": "Blog", "item": "{{ url('/blog') }}" }
        @if($post->category)
        ,{ "@@type": "ListItem", "position": 3, "name": "{{ ucfirst($post->category) }}", "item": "{{ url('/blog/categoria/' . $post->category) }}" }
        ,{ "@@type": "ListItem", "position": 4, "name": "{{ $post->title }}", "item": "{{ url('/blog/' . $post->slug) }}" }
        @else
        ,{ "@@type": "ListItem", "position": 3, "name": "{{ $post->title }}", "item": "{{ url('/blog/' . $post->slug) }}" }
        @endif
    ]
}
</script>
@endpush

@section('content')
<div class="min-h-screen" style="background:#0B0B0B; color:#F5F5F5;">

    {{-- ── Hero with cover image ──────────────────────────────────────────── --}}
    @if($post->cover_image)
    <div style="position:relative; height:400px; overflow:hidden;">
        <img src="{{ str_starts_with($post->cover_image, 'http') ? $post->cover_image : Storage::url($post->cover_image) }}"
             alt="{{ $post->title }}"
             style="width:100%; height:100%; object-fit:cover;">
        <div style="position:absolute; inset:0; background:linear-gradient(to bottom, rgba(11,11,11,0.3) 0%, rgba(11,11,11,0.85) 100%);"></div>
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8" style="position:absolute; bottom:0; left:0; right:0; padding-bottom:2rem;">
            {{-- Breadcrumb --}}
            <nav style="margin-bottom:1rem;">
                <ol style="display:flex; flex-wrap:wrap; gap:0.4rem; align-items:center; font-size:0.8125rem; color:#9CA3AF;">
                    <li><a href="/" style="color:#D4AF37; text-decoration:none;">FAMER</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li><a href="/blog" style="color:#D4AF37; text-decoration:none;">Blog</a></li>
                    @if($post->category)
                    <li style="color:#4B5563;">/</li>
                    <li><a href="/blog/categoria/{{ $post->category }}" style="color:#D4AF37; text-decoration:none;">{{ ucfirst($post->category) }}</a></li>
                    @endif
                    <li style="color:#4B5563;">/</li>
                    <li style="color:#9CA3AF; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $post->title }}</li>
                </ol>
            </nav>
        </div>
    </div>
    @else
    {{-- No cover image — minimal hero --}}
    <div style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 50%,#0B0B0B 100%); border-bottom:1px solid #2A2A2A; padding:3rem 0 2rem;">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav style="margin-bottom:1rem;">
                <ol style="display:flex; flex-wrap:wrap; gap:0.4rem; align-items:center; font-size:0.8125rem; color:#9CA3AF;">
                    <li><a href="/" style="color:#D4AF37; text-decoration:none;">FAMER</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li><a href="/blog" style="color:#D4AF37; text-decoration:none;">Blog</a></li>
                    @if($post->category)
                    <li style="color:#4B5563;">/</li>
                    <li><a href="/blog/categoria/{{ $post->category }}" style="color:#D4AF37; text-decoration:none;">{{ ucfirst($post->category) }}</a></li>
                    @endif
                    <li style="color:#4B5563;">/</li>
                    <li style="color:#9CA3AF;">{{ Str::limit($post->title, 40) }}</li>
                </ol>
            </nav>
        </div>
    </div>
    @endif

    {{-- ── Article Content ─────────────────────────────────────────────────── --}}
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- Category badge --}}
        @if($post->category)
        <a href="/blog/categoria/{{ $post->category }}"
           style="display:inline-block; background:#2A2A2A; color:#D4AF37; padding:0.25rem 0.75rem; border-radius:4px; font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; text-decoration:none; margin-bottom:1.25rem; transition:background 0.2s;"
           onmouseover="this.style.background='#3A3A3A'" onmouseout="this.style.background='#2A2A2A'">
            {{ ucfirst($post->category) }}
        </a>
        @endif

        {{-- H1 Title --}}
        <h1 style="font-family:'Playfair Display',serif; font-size:clamp(1.75rem,4vw,2.75rem); font-weight:700; color:#F5F5F5; line-height:1.25; margin-bottom:1.5rem;">
            {{ $post->title }}
        </h1>

        {{-- Meta row --}}
        <div style="display:flex; align-items:center; gap:1.25rem; flex-wrap:wrap; font-size:0.875rem; color:#6B7280; padding-bottom:1.5rem; border-bottom:1px solid #2A2A2A; margin-bottom:2.5rem;">
            <span style="display:flex; align-items:center; gap:0.35rem; color:#9CA3AF;">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ $post->author }}
            </span>
            @if($post->published_at)
            <span style="display:flex; align-items:center; gap:0.35rem;">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ $post->published_at->format('d \d\e F, Y') }}
            </span>
            @endif
            <span style="display:flex; align-items:center; gap:0.35rem;">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $post->read_time }} min lectura
            </span>
            <span style="display:flex; align-items:center; gap:0.35rem;">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                {{ number_format($post->view_count) }} lecturas
            </span>
        </div>

        {{-- Article Body --}}
        <div class="famer-article-content" style="color:#D1D5DB; line-height:1.85; font-size:1.0625rem;">
            {!! $post->content !!}
        </div>

        {{-- Tags --}}
        @if($post->tags && count($post->tags))
        <div style="display:flex; flex-wrap:wrap; gap:0.5rem; margin-top:2.5rem; padding-top:1.5rem; border-top:1px solid #2A2A2A;">
            @foreach($post->tags as $tag)
            <span style="background:#1A1A1A; border:1px solid #2A2A2A; color:#9CA3AF; padding:0.3rem 0.75rem; border-radius:9999px; font-size:0.8125rem;">
                #{{ $tag }}
            </span>
            @endforeach
        </div>
        @endif

    </div>

    {{-- ── CTA ─────────────────────────────────────────────────────────────── --}}
    <div style="background:#1A1A1A; border-top:1px solid #2A2A2A; border-bottom:1px solid #2A2A2A; padding:3rem 0; margin:2rem 0;">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p style="font-size:1.25rem; font-weight:600; color:#F5F5F5; margin-bottom:0.5rem;">
                ¿Tienes un restaurante mexicano?
            </p>
            <p style="color:#9CA3AF; margin-bottom:1.5rem;">Únete a la red FAMER y llega a millones de amantes de la cocina mexicana.</p>
            <a href="/for-owners"
               style="display:inline-block; background:#D4AF37; color:#0B0B0B; padding:0.875rem 2.5rem; border-radius:9999px; font-weight:700; font-size:1rem; text-decoration:none; transition:opacity 0.2s;"
               onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                Únete a FAMER
            </a>
        </div>
    </div>

    {{-- ── Related Posts ───────────────────────────────────────────────────── --}}
    @if($related->count())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#F5F5F5; margin-bottom:1.5rem;">
            Artículos Relacionados
        </h2>
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1.25rem;">
            @foreach($related as $rel)
            <a href="/blog/{{ $rel->slug }}"
               style="display:flex; flex-direction:column; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; overflow:hidden; text-decoration:none; transition:border-color 0.2s;"
               onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                @if($rel->cover_image)
                <div style="height:160px; overflow:hidden;">
                    <img src="{{ str_starts_with($rel->cover_image, 'http') ? $rel->cover_image : Storage::url($rel->cover_image) }}"
                         alt="{{ $rel->title }}"
                         loading="lazy"
                         style="width:100%; height:100%; object-fit:cover;">
                </div>
                @else
                <div style="height:80px; background:#2A2A2A; display:flex; align-items:center; justify-content:center;">
                    <span style="font-size:2rem;">🍽️</span>
                </div>
                @endif
                <div style="padding:1rem; flex:1; display:flex; flex-direction:column;">
                    @if($rel->category)
                    <span style="display:inline-block; background:#2A2A2A; color:#D4AF37; padding:0.15rem 0.5rem; border-radius:4px; font-size:0.7rem; font-weight:700; text-transform:uppercase; margin-bottom:0.5rem; width:fit-content;">
                        {{ ucfirst($rel->category) }}
                    </span>
                    @endif
                    <h3 style="font-weight:600; color:#F5F5F5; font-size:0.9375rem; line-height:1.4; margin-bottom:0.5rem; flex:1;">
                        {{ $rel->title }}
                    </h3>
                    <div style="font-size:0.75rem; color:#6B7280; display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:auto;">
                        <span>{{ $rel->read_time }} min</span>
                        @if($rel->published_at)
                        <span>{{ $rel->published_at->format('d M Y') }}</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>

@push('styles')
<style>
/* Article typography */
.famer-article-content h2 {
    font-family: 'Playfair Display', serif;
    font-size: 1.625rem;
    font-weight: 700;
    color: #D4AF37;
    margin: 2.5rem 0 1rem;
    line-height: 1.3;
}
.famer-article-content h3 {
    font-family: 'Playfair Display', serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: #F5F5F5;
    margin: 2rem 0 0.75rem;
    line-height: 1.4;
}
.famer-article-content h4 {
    font-size: 1.0625rem;
    font-weight: 700;
    color: #E5E7EB;
    margin: 1.5rem 0 0.5rem;
}
.famer-article-content p {
    margin-bottom: 1.5rem;
}
.famer-article-content a {
    color: #D4AF37;
    text-decoration: underline;
    text-underline-offset: 3px;
}
.famer-article-content a:hover {
    color: #F5C842;
}
.famer-article-content ul,
.famer-article-content ol {
    margin: 1rem 0 1.5rem 1.5rem;
    color: #D1D5DB;
}
.famer-article-content li {
    margin-bottom: 0.5rem;
    line-height: 1.7;
}
.famer-article-content ul li {
    list-style-type: disc;
}
.famer-article-content ol li {
    list-style-type: decimal;
}
.famer-article-content blockquote {
    border-left: 3px solid #D4AF37;
    padding: 0.75rem 1.25rem;
    margin: 2rem 0;
    background: #1A1A1A;
    border-radius: 0 8px 8px 0;
    color: #9CA3AF;
    font-style: italic;
}
.famer-article-content img {
    max-width: 100%;
    border-radius: 8px;
    margin: 1.5rem 0;
}
.famer-article-content strong {
    color: #F5F5F5;
    font-weight: 700;
}
.famer-article-content em {
    color: #D4AF37;
}
.famer-article-content hr {
    border: none;
    border-top: 1px solid #2A2A2A;
    margin: 2.5rem 0;
}
.famer-article-content pre {
    background: #1A1A1A;
    border: 1px solid #2A2A2A;
    border-radius: 8px;
    padding: 1.25rem;
    overflow-x: auto;
    font-size: 0.875rem;
    margin: 1.5rem 0;
}
.famer-article-content code {
    background: #2A2A2A;
    color: #D4AF37;
    padding: 0.15rem 0.4rem;
    border-radius: 4px;
    font-size: 0.875em;
}
</style>
@endpush
@endsection
