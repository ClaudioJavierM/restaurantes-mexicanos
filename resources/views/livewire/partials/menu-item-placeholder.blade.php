@php
    $categoryName = strtolower($item->category?->name ?? "");
    $itemName = strtolower($item->name ?? "");
    
    $emoji = match(true) {
        str_contains($categoryName, "taco") || str_contains($itemName, "taco") => "🌮",
        str_contains($categoryName, "burrito") || str_contains($itemName, "burrito") => "🌯",
        str_contains($categoryName, "quesadilla") || str_contains($itemName, "quesadilla") => "🧀",
        str_contains($categoryName, "enchilada") || str_contains($itemName, "enchilada") => "🫔",
        str_contains($itemName, "churro") || str_contains($categoryName, "postre") => "🍮",
        str_contains($categoryName, "bebida") || str_contains($categoryName, "drink") => "🥤",
        str_contains($itemName, "pollo") || str_contains($itemName, "chicken") => "🍗",
        str_contains($itemName, "carne") || str_contains($itemName, "asada") => "🥩",
        str_contains($itemName, "pastor") => "🌮",
        str_contains($itemName, "carnitas") => "🐷",
        default => "🍽️",
    };
    
    $gradients = [
        "🌮" => "linear-gradient(135deg, #FBBF24 0%, #F97316 100%)",
        "🌯" => "linear-gradient(135deg, #FB923C 0%, #EF4444 100%)",
        "🧀" => "linear-gradient(135deg, #FDE047 0%, #EAB308 100%)",
        "🫔" => "linear-gradient(135deg, #F87171 0%, #DC2626 100%)",
        "🍮" => "linear-gradient(135deg, #FCD34D 0%, #F59E0B 100%)",
        "🥤" => "linear-gradient(135deg, #60A5FA 0%, #06B6D4 100%)",
        "🍗" => "linear-gradient(135deg, #FBBF24 0%, #F59E0B 100%)",
        "🥩" => "linear-gradient(135deg, #F87171 0%, #E11D48 100%)",
        "🐷" => "linear-gradient(135deg, #F472B6 0%, #F43F5E 100%)",
        "🍽️" => "linear-gradient(135deg, #9CA3AF 0%, #6B7280 100%)",
    ];
    
    $gradient = $gradients[$emoji] ?? "linear-gradient(135deg, #F87171 0%, #F97316 100%)";
@endphp
<div style="position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100% !important; height: 100% !important; max-height: 160px !important; background: {{ $gradient }} !important; display: flex !important; align-items: center !important; justify-content: center !important; z-index: 1 !important;">
    <span style="font-size: 3rem; filter: drop-shadow(0 4px 3px rgb(0 0 0 / 0.1));">{{ $emoji }}</span>
</div>
