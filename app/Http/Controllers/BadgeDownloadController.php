<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\MonthlyRanking;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BadgeDownloadController extends Controller
{
    /**
     * Download a winner badge SVG file.
     * GET /owner/badge/download?restaurant_id=X&year=Y&month=M&scope=city&scope_value=Dallas
     */
    public function download(Request $request): Response
    {
        $request->validate([
            'restaurant_id' => 'required|integer|exists:restaurants,id',
            'year'          => 'required|integer|min:2020|max:2099',
            'month'         => 'required|integer|min:1|max:12',
            'scope'         => 'nullable|string|in:city,state,national',
            'scope_value'   => 'nullable|string|max:100',
        ]);

        $restaurant = Restaurant::with('state:id,code,name')->findOrFail($request->restaurant_id);

        // Verify authenticated user owns this restaurant
        if (!Auth::check() || (int) $restaurant->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para descargar este badge.');
        }

        $year       = (int) $request->year;
        $month      = (int) $request->month;
        $scope      = $request->scope ?? 'city';
        $scopeValue = $request->scope_value;

        // Determine display label
        $scopeLabel = match ($scope) {
            'national' => 'NACIONAL',
            'state'    => strtoupper($scopeValue ?? $restaurant->state?->code ?? 'ESTADO'),
            default    => strtoupper($scopeValue ?? $restaurant->city ?? 'CIUDAD'),
        };

        $monthName   = $this->monthName($month);
        $restaurantName = mb_strtoupper(mb_substr($restaurant->name, 0, 34));

        $svg = $this->buildSvg($scopeLabel, $monthName, $year, $restaurantName);

        $filename = sprintf('famer-badge-%s-%d-%02d.svg',
            strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $scopeLabel)),
            $year,
            $month
        );

        return response($svg, 200, [
            'Content-Type'        => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
        ]);
    }

    private function buildSvg(string $scopeLabel, string $monthName, int $year, string $restaurantName): string
    {
        $monthUpper = strtoupper($monthName);

        return <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="600" height="600" viewBox="0 0 600 600">
  <defs>
    <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#D4AF37;stop-opacity:1"/>
      <stop offset="100%" style="stop-color:#F0C040;stop-opacity:1"/>
    </linearGradient>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="0%" y2="100%">
      <stop offset="0%" style="stop-color:#1A1A1A;stop-opacity:1"/>
      <stop offset="100%" style="stop-color:#0B0B0B;stop-opacity:1"/>
    </linearGradient>
  </defs>

  <!-- Background -->
  <rect width="600" height="600" fill="url(#bgGrad)" rx="24"/>

  <!-- Gold border outer -->
  <rect x="16" y="16" width="568" height="568" fill="none"
        stroke="url(#goldGrad)" stroke-width="3" rx="20"/>

  <!-- Dark inner border -->
  <rect x="28" y="28" width="544" height="544" fill="none"
        stroke="#2A2A2A" stroke-width="1.5" rx="14"/>

  <!-- Star polygon -->
  <polygon points="300,82 316,130 364,130 326,158 342,206 300,178 258,206 274,158 236,130 284,130"
           fill="url(#goldGrad)"/>

  <!-- FAMER -->
  <text x="300" y="248" text-anchor="middle"
        font-family="'Playfair Display', Georgia, 'Times New Roman', serif"
        font-size="56" font-weight="700" letter-spacing="12"
        fill="url(#goldGrad)">FAMER</text>

  <!-- Separator -->
  <line x1="100" y1="272" x2="500" y2="272" stroke="#D4AF37" stroke-width="1.5" opacity="0.6"/>

  <!-- VOTADO #1 -->
  <text x="300" y="330" text-anchor="middle"
        font-family="'Playfair Display', Georgia, 'Times New Roman', serif"
        font-size="46" font-weight="700" letter-spacing="4"
        fill="#FFFFFF">VOTADO #1</text>

  <!-- EN [SCOPE] -->
  <text x="300" y="376" text-anchor="middle"
        font-family="Arial, Helvetica, sans-serif"
        font-size="26" font-weight="600" letter-spacing="6"
        fill="#D4AF37">EN {$scopeLabel}</text>

  <!-- Month + Year -->
  <text x="300" y="416" text-anchor="middle"
        font-family="Arial, Helvetica, sans-serif"
        font-size="22" letter-spacing="2"
        fill="#AAAAAA">{$monthUpper} {$year}</text>

  <!-- Separator -->
  <line x1="140" y1="448" x2="460" y2="448" stroke="#2A2A2A" stroke-width="1.5"/>

  <!-- Restaurant name -->
  <text x="300" y="488" text-anchor="middle"
        font-family="'Playfair Display', Georgia, 'Times New Roman', serif"
        font-size="18" font-weight="600" letter-spacing="1"
        fill="#888888">{$restaurantName}</text>

  <!-- URL -->
  <text x="300" y="536" text-anchor="middle"
        font-family="Arial, Helvetica, sans-serif"
        font-size="13" letter-spacing="1"
        fill="#555555">restaurantesmexicanosfamosos.com.mx</text>
</svg>
SVG;
    }

    private function monthName(int $month): string
    {
        $names = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];
        return $names[$month] ?? 'Mes';
    }
}
