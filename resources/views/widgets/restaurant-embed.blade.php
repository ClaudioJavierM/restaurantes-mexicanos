<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #1f2937; color: #fff; padding: 20px; }
        .widget { max-width: 100%; }
        .header { display: flex; gap: 15px; margin-bottom: 20px; }
        .logo { width: 80px; height: 80px; border-radius: 12px; object-fit: cover; background: #374151; }
        .info h2 { font-size: 1.25rem; margin-bottom: 4px; }
        .rating { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
        .stars { color: #fbbf24; }
        .rating-text { font-size: 0.875rem; color: #9ca3af; }
        .location { font-size: 0.875rem; color: #9ca3af; }
        .reviews { margin-bottom: 20px; }
        .reviews h3 { font-size: 0.875rem; color: #9ca3af; margin-bottom: 10px; }
        .review { background: #374151; padding: 12px; border-radius: 8px; margin-bottom: 10px; }
        .review-header { display: flex; justify-content: space-between; margin-bottom: 6px; }
        .review-author { font-weight: 500; font-size: 0.875rem; }
        .review-date { font-size: 0.75rem; color: #9ca3af; }
        .review-text { font-size: 0.875rem; color: #d1d5db; line-height: 1.4; }
        .buttons { display: flex; gap: 10px; }
        .btn { flex: 1; padding: 12px; border-radius: 8px; text-align: center; text-decoration: none; font-weight: 500; font-size: 0.875rem; }
        .btn-primary { background: #f97316; color: white; }
        .btn-secondary { background: #374151; color: white; }
        .powered { text-align: center; margin-top: 15px; font-size: 0.75rem; color: #6b7280; }
        .powered a { color: #f97316; text-decoration: none; }
    </style>
</head>
<body>
    <div class="widget">
        <div class="header">
            @if($restaurant->main_image)
            <img src="{{ $restaurant->main_image }}" alt="{{ $restaurant->name }}" class="logo">
            @else
            <div class="logo"></div>
            @endif
            <div class="info">
                <h2>{{ $restaurant->name }}</h2>
                <div class="rating">
                    <span class="stars">@for($i = 0; $i < 5; $i++){{ $i < round($restaurant->rating ?? 0) ? '★' : '☆' }}@endfor</span>
                    <span class="rating-text">{{ number_format($restaurant->rating ?? 0, 1) }} ({{ $restaurant->reviews_count ?? 0 }} resenas)</span>
                </div>
                <p class="location">{{ $restaurant->city }}, {{ $restaurant->state->code ?? '' }}</p>
            </div>
        </div>
        
        @if($reviews->count() > 0 && ($settings['show_reviews'] ?? true))
        <div class="reviews">
            <h3>Resenas Recientes</h3>
            @foreach($reviews as $review)
            <div class="review">
                <div class="review-header">
                    <span class="review-author">{{ $review->author_name ?? 'Cliente' }}</span>
                    <span class="review-date">{{ $review->created_at->diffForHumans() }}</span>
                </div>
                <p class="review-text">{{ Str::limit($review->comment, 120) }}</p>
            </div>
            @endforeach
        </div>
        @endif
        
        <div class="buttons">
            <a href="{{ config('app.url') }}/restaurante/{{ $restaurant->slug }}" target="_blank" class="btn btn-primary">Ver Menu</a>
            <a href="{{ config('app.url') }}/restaurante/{{ $restaurant->slug }}#reservar" target="_blank" class="btn btn-secondary">Reservar</a>
        </div>
        
        <p class="powered">Powered by <a href="{{ config('app.url') }}" target="_blank">FAMER</a></p>
    </div>
</body>
</html>
