<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Cache;

class OgImageController extends Controller
{
    private const WIDTH = 1200;
    private const HEIGHT = 630;
    private const FONT_REGULAR = '/usr/share/fonts/dejavu-sans-fonts/DejaVuSans.ttf';
    private const FONT_BOLD = '/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-Bold.ttf';

    public function restaurant(string $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();

        $cacheKey = "og_img_{$restaurant->id}_v4";
        $cachedPath = Cache::get($cacheKey);

        if ($cachedPath && file_exists(public_path($cachedPath))) {
            return response()->file(public_path($cachedPath), [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        $imagePath = $this->generate($restaurant);

        if (!$imagePath) {
            $fallback = $restaurant->image
                ? (str_starts_with($restaurant->image, 'http') ? $restaurant->image : asset('storage/' . $restaurant->image))
                : asset('images/branding/og-default.jpg');
            return redirect($fallback);
        }

        Cache::put($cacheKey, $imagePath, now()->addHours(24));

        return response()->file(public_path($imagePath), [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    private function generate(Restaurant $restaurant): ?string
    {
        $w = self::WIDTH;
        $h = self::HEIGHT;

        $canvas = imagecreatetruecolor($w, $h);
        if (!$canvas) return null;

        // Enable alpha blending
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);

        // --- Background: restaurant cover image ---
        $this->drawCoverImage($canvas, $restaurant, $w, $h);

        // --- Dark gradient overlay (stronger at bottom) ---
        for ($y = 0; $y < $h; $y++) {
            $ratio = $y / $h;
            // Top: 30% opacity, Bottom: 85% opacity
            $alpha = (int)(127 - (127 * (0.30 + $ratio * 0.55)));
            $overlay = imagecolorallocatealpha($canvas, 0, 0, 0, $alpha);
            imageline($canvas, 0, $y, $w, $y, $overlay);
        }

        // --- Get ranking data ---
        $bestRanking = $restaurant->rankings()
            ->where('year', now()->year - 1)
            ->where('position', '<=', 25)
            ->where('is_published', true)
            ->orderBy('position')
            ->first();

        // --- Colors ---
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $gold = imagecolorallocate($canvas, 212, 175, 55);
        $goldLight = imagecolorallocate($canvas, 245, 208, 96);
        $lightGray = imagecolorallocate($canvas, 180, 180, 180);
        $darkBg = imagecolorallocatealpha($canvas, 10, 10, 10, 30);
        $goldBorder = imagecolorallocatealpha($canvas, 212, 175, 55, 60);

        // --- Gold Award Banner (full width, top) ---
        if ($bestRanking) {
            $bannerH = 90;

            // Gold gradient background
            $goldDark = imagecolorallocate($canvas, 184, 134, 11);   // #B8860B
            $goldMid = imagecolorallocate($canvas, 212, 175, 55);    // #D4AF37
            $goldBright = imagecolorallocate($canvas, 245, 208, 96); // #F5D060
            for ($y = 0; $y < $bannerH; $y++) {
                $ratio = $y / $bannerH;
                $r = (int)(184 + (245 - 184) * $ratio);
                $g = (int)(134 + (208 - 134) * $ratio);
                $b = (int)(11 + (96 - 11) * $ratio);
                $lineColor = imagecolorallocate($canvas, $r, $g, $b);
                imageline($canvas, 0, $y, $w, $y, $lineColor);
            }
            // Subtle bottom border
            $borderLine = imagecolorallocate($canvas, 160, 120, 10);
            imageline($canvas, 0, $bannerH - 1, $w, $bannerH - 1, $borderLine);
            imageline($canvas, 0, $bannerH, $w, $bannerH, $borderLine);

            $darkText = imagecolorallocate($canvas, 26, 26, 46); // #1a1a2e navy
            $darkTextSoft = imagecolorallocate($canvas, 60, 50, 20);

            // Trophy circle (navy background)
            $circleX = 70;
            $circleY = (int)($bannerH / 2);
            $navy = imagecolorallocate($canvas, 26, 26, 46);
            imagefilledellipse($canvas, $circleX, $circleY, 60, 60, $navy);
            // Trophy icon as text (gold on navy)
            $trophyColor = imagecolorallocate($canvas, 245, 208, 96);
            imagettftext($canvas, 20, 0, $circleX - 10, $circleY + 8, $trophyColor, self::FONT_BOLD, chr(0xF0) !== false ? '#' : '#');

            // Position number (large)
            $posText = '#' . $bestRanking->position;
            $posFontSize = 38;
            imagettftext($canvas, $posFontSize, 0, 120, 58, $darkText, self::FONT_BOLD, $posText);

            // Scope name next to position
            $scopeText = match ($bestRanking->ranking_type) {
                'city' => mb_strtoupper($bestRanking->ranking_scope, 'UTF-8'),
                'state' => mb_strtoupper($bestRanking->ranking_scope, 'UTF-8'),
                'national' => 'USA',
                default => mb_strtoupper($bestRanking->ranking_scope, 'UTF-8'),
            };
            $posBox = imagettfbbox($posFontSize, 0, self::FONT_BOLD, $posText);
            $posW = $posBox[2] - $posBox[0];
            imagettftext($canvas, 24, 0, 120 + $posW + 15, 55, $darkText, self::FONT_BOLD, $scopeText);

            // FAMER Awards year (right-aligned)
            $awardText = 'FAMER AWARDS ' . $bestRanking->year;
            $awardBox = imagettfbbox(14, 0, self::FONT_BOLD, $awardText);
            $awardW = $awardBox[2] - $awardBox[0];
            imagettftext($canvas, 14, 0, $w - $awardW - 40, 38, $darkTextSoft, self::FONT_REGULAR, $awardText);

            // Stars + rating (right side, below FAMER Awards)
            $rating = number_format($restaurant->google_rating ?? $restaurant->average_rating ?? 0, 1);
            $totalReviews = ($restaurant->google_reviews_count ?? 0) + ($restaurant->yelp_reviews_count ?? 0);
            $starsText = str_repeat('★', (int) round((float) $rating)) . str_repeat('☆', 5 - (int) round((float) $rating));
            $ratingLine = $starsText . ' ' . $rating;
            if ($totalReviews > 0) {
                $ratingLine .= '  (' . number_format($totalReviews) . ' reviews)';
            }
            $ratingBox = imagettfbbox(12, 0, self::FONT_REGULAR, $ratingLine);
            $ratingW = $ratingBox[2] - $ratingBox[0];
            imagettftext($canvas, 12, 0, $w - $ratingW - 40, 65, $darkTextSoft, self::FONT_REGULAR, $ratingLine);
        }

        // --- Restaurant name (large, bottom-left) ---
        $name = $restaurant->name;
        $nameFontSize = mb_strlen($name) > 30 ? 32 : (mb_strlen($name) > 20 ? 38 : 44);
        imagettftext($canvas, $nameFontSize, 0, 45, $h - 95, $white, self::FONT_BOLD, $name);

        // --- City, State below name ---
        $location = $restaurant->city . ', ' . ($restaurant->state?->name ?? '');
        imagettftext($canvas, 18, 0, 47, $h - 55, $lightGray, self::FONT_REGULAR, $location);

        // --- FAMER branding (bottom-right) ---
        $brand = 'restaurantesmexicanosfamosos.com';
        $brandBox = imagettfbbox(12, 0, self::FONT_REGULAR, $brand);
        $brandW = $brandBox[2] - $brandBox[0];
        imagettftext($canvas, 12, 0, $w - $brandW - 40, $h - 30, $lightGray, self::FONT_REGULAR, $brand);

        // --- Save ---
        $dir = public_path('images/og');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'images/og/restaurant-' . $restaurant->slug . '.jpg';
        imagejpeg($canvas, public_path($filename), 92);
        imagedestroy($canvas);

        return $filename;
    }

    private function drawCoverImage($canvas, Restaurant $restaurant, int $w, int $h): void
    {
        $coverUrl = null;

        if ($restaurant->image) {
            $coverUrl = str_starts_with($restaurant->image, 'http')
                ? $restaurant->image
                : public_path('storage/' . $restaurant->image);
        } elseif ($restaurant->getFirstMediaUrl('images')) {
            $coverUrl = $restaurant->getFirstMediaUrl('images');
        } elseif (is_array($restaurant->yelp_photos) && count($restaurant->yelp_photos) > 0) {
            $coverUrl = $restaurant->yelp_photos[0];
        }

        if (!$coverUrl) {
            $dark = imagecolorallocate($canvas, 25, 30, 40);
            imagefill($canvas, 0, 0, $dark);
            return;
        }

        $imageData = @file_get_contents($coverUrl);
        if (!$imageData) {
            $dark = imagecolorallocate($canvas, 25, 30, 40);
            imagefill($canvas, 0, 0, $dark);
            return;
        }

        $source = @imagecreatefromstring($imageData);
        if (!$source) {
            $dark = imagecolorallocate($canvas, 25, 30, 40);
            imagefill($canvas, 0, 0, $dark);
            return;
        }

        // Cover-fit: scale and crop to fill
        $srcW = imagesx($source);
        $srcH = imagesy($source);
        $scale = max($w / $srcW, $h / $srcH);
        $newW = (int) ($srcW * $scale);
        $newH = (int) ($srcH * $scale);
        $offsetX = (int) (($w - $newW) / 2);
        $offsetY = (int) (($h - $newH) / 2);

        imagecopyresampled($canvas, $source, $offsetX, $offsetY, 0, 0, $newW, $newH, $srcW, $srcH);
        imagedestroy($source);
    }
}
