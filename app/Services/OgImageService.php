<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OgImageService
{
    // Canvas dimensions (standard OG size)
    private const WIDTH  = 1200;
    private const HEIGHT = 630;

    // Photo panel width
    private const PHOTO_WIDTH = 400;

    // Color palette
    private const COLOR_BG       = [11, 11, 11];       // #0B0B0B
    private const COLOR_GOLD     = [212, 175, 55];      // #D4AF37
    private const COLOR_WHITE    = [255, 255, 255];
    private const COLOR_GRAY     = [156, 163, 175];     // #9CA3AF
    private const COLOR_DARK_BG  = [20, 20, 20];        // slightly lighter for panels

    // TTF font paths (try in order)
    private const TTF_FONTS = [
        '/usr/share/fonts/liberation/LiberationSans-Bold.ttf',
        '/usr/share/fonts/liberation-sans/LiberationSans-Bold.ttf',
        '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
        '/usr/share/fonts/liberation-fonts/LiberationSans-Bold.ttf',
        '/usr/share/fonts/google-droid/DroidSans-Bold.ttf',
        '/usr/share/fonts/urw-base35/NimbusSans-Bold.ttf',
        '/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-Bold.ttf',
        '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
        '/System/Library/Fonts/Helvetica.ttc', // macOS fallback
    ];

    private const TTF_FONTS_REGULAR = [
        '/usr/share/fonts/liberation/LiberationSans-Regular.ttf',
        '/usr/share/fonts/liberation-sans/LiberationSans-Regular.ttf',
        '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
        '/usr/share/fonts/dejavu-sans-fonts/DejaVuSans.ttf',
        '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        '/System/Library/Fonts/Helvetica.ttc',
    ];

    /**
     * Generate OG image for a restaurant and return the public URL.
     * Skips generation if file already exists (unless $force = true).
     */
    public function generate(Restaurant $restaurant, bool $force = false): string
    {
        $storagePath = $this->getStoragePath($restaurant);

        // Return cached URL if file already exists and not forced
        if (! $force && Storage::disk('public')->exists($storagePath)) {
            return Storage::disk('public')->url($storagePath);
        }

        try {
            return $this->buildImage($restaurant, $storagePath);
        } catch (\Throwable $e) {
            Log::warning("OgImageService: failed for restaurant {$restaurant->id} ({$restaurant->slug}): " . $e->getMessage());

            // Fallback: return the restaurant's existing photo or default OG
            $fallback = $restaurant->getDisplayImageUrl();
            return $fallback ?? asset('images/branding/og-default.jpg');
        }
    }

    /**
     * Return the storage-relative path (relative to the 'public' disk).
     */
    public function getStoragePath(Restaurant $restaurant): string
    {
        return 'og-images/' . $restaurant->slug . '.jpg';
    }

    /**
     * Return the absolute filesystem path inside storage/app/public/.
     */
    public function getPath(Restaurant $restaurant): string
    {
        return Storage::disk('public')->path($this->getStoragePath($restaurant));
    }

    /**
     * Check whether the OG image already exists on disk.
     */
    public function exists(Restaurant $restaurant): bool
    {
        return Storage::disk('public')->exists($this->getStoragePath($restaurant));
    }

    // -------------------------------------------------------------------------
    // Private: image generation
    // -------------------------------------------------------------------------

    private function buildImage(Restaurant $restaurant, string $storagePath): string
    {
        // Ensure target directory exists
        Storage::disk('public')->makeDirectory('og-images');

        $canvas = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        if (! $canvas) {
            throw new \RuntimeException('imagecreatetruecolor failed');
        }

        try {
            // Resolve fonts
            $boldFont    = $this->resolveFontPath(self::TTF_FONTS);
            $regularFont = $this->resolveFontPath(self::TTF_FONTS_REGULAR);

            // ── Background ─────────────────────────────────────────────────
            $colorBg = imagecolorallocate($canvas, ...self::COLOR_BG);
            imagefill($canvas, 0, 0, $colorBg);

            // ── Left panel: restaurant photo ────────────────────────────────
            $this->renderPhotoPanel($canvas, $restaurant);

            // ── Right panel: text content ───────────────────────────────────
            $this->renderTextPanel($canvas, $restaurant, $boldFont, $regularFont);

            // ── Bottom bar ──────────────────────────────────────────────────
            $this->renderBottomBar($canvas, $regularFont);

            // ── Save to disk ────────────────────────────────────────────────
            $tmpPath = tempnam(sys_get_temp_dir(), 'og_') . '.jpg';
            imagejpeg($canvas, $tmpPath, 90);

            Storage::disk('public')->put($storagePath, file_get_contents($tmpPath));
            @unlink($tmpPath);

            return Storage::disk('public')->url($storagePath);

        } finally {
            imagedestroy($canvas);
        }
    }

    // ── Photo panel (left 400 px) ─────────────────────────────────────────────

    private function renderPhotoPanel(\GdImage $canvas, Restaurant $restaurant): void
    {
        $photoUrl = $this->resolvePhotoUrl($restaurant);
        $photo    = null;

        if ($photoUrl) {
            $photo = $this->loadRemoteImage($photoUrl);
        }

        if ($photo) {
            // Crop/scale to fill 400×630
            $srcW = imagesx($photo);
            $srcH = imagesy($photo);

            [$cropX, $cropY, $cropW, $cropH] = $this->coverCrop(
                $srcW, $srcH,
                self::PHOTO_WIDTH, self::HEIGHT
            );

            imagecopyresampled(
                $canvas, $photo,
                0, 0,
                $cropX, $cropY,
                self::PHOTO_WIDTH, self::HEIGHT,
                $cropW, $cropH
            );

            imagedestroy($photo);
        } else {
            // Solid dark-gold gradient replacement when no photo available
            $this->drawSolidPhotoPlaceholder($canvas);
        }

        // Dark gradient overlay on right edge of photo (fade to background)
        $this->drawPhotoGradient($canvas);
    }

    private function resolvePhotoUrl(Restaurant $restaurant): ?string
    {
        // 1. image column — CDN URL
        if ($restaurant->image && str_starts_with($restaurant->image, 'http')) {
            return $restaurant->image;
        }

        // 2. yelp_photos — CDN array
        if (! empty($restaurant->yelp_photos) && is_array($restaurant->yelp_photos)) {
            return $restaurant->yelp_photos[0] ?? null;
        }

        // 3. photos JSON array
        $photos = $restaurant->photos;
        if (is_array($photos) && ! empty($photos)) {
            $first = $photos[0];
            if (is_string($first) && str_starts_with($first, 'http')) {
                return $first;
            }
            if (is_array($first) && isset($first['url'])) {
                return $first['url'];
            }
        }

        // 4. local image path
        if ($restaurant->image) {
            return Storage::disk('public')->url($restaurant->image);
        }

        return null;
    }

    private function loadRemoteImage(string $url): ?\GdImage
    {
        try {
            $ctx = stream_context_create([
                'http' => [
                    'timeout'       => 8,
                    'user_agent'    => 'FAMER-OGImage/1.0',
                    'ignore_errors' => true,
                ],
                'ssl' => ['verify_peer' => false],
            ]);

            $data = @file_get_contents($url, false, $ctx);
            if (! $data || strlen($data) < 100) {
                return null;
            }

            $img = @imagecreatefromstring($data);
            return ($img instanceof \GdImage) ? $img : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function coverCrop(int $srcW, int $srcH, int $dstW, int $dstH): array
    {
        $scaleW = $dstW / $srcW;
        $scaleH = $dstH / $srcH;
        $scale  = max($scaleW, $scaleH);

        $scaledW = (int) round($srcW * $scale);
        $scaledH = (int) round($srcH * $scale);

        $cropX = (int) round(($scaledW - $dstW) / 2 / $scale);
        $cropY = (int) round(($scaledH - $dstH) / 2 / $scale);
        $cropW = (int) round($dstW / $scale);
        $cropH = (int) round($dstH / $scale);

        return [$cropX, $cropY, $cropW, $cropH];
    }

    private function drawSolidPhotoPlaceholder(\GdImage $canvas): void
    {
        // Deep charcoal with subtle gold tint
        $c = imagecolorallocate($canvas, 26, 22, 10);
        imagefilledrectangle($canvas, 0, 0, self::PHOTO_WIDTH - 1, self::HEIGHT - 1, $c);

        // A simple decorative pattern: faint gold "F" monogram area
        $gold = imagecolorallocatealpha($canvas, 212, 175, 55, 90);
        imagefilledellipse($canvas, (int)(self::PHOTO_WIDTH / 2), (int)(self::HEIGHT / 2), 180, 180, $gold);
    }

    private function drawPhotoGradient(\GdImage $canvas): void
    {
        // Left-to-right fade from transparent to #0B0B0B over the rightmost ~160px of the photo panel
        $gradStart = self::PHOTO_WIDTH - 160;
        $gradEnd   = self::PHOTO_WIDTH + 20; // slight bleed into text area

        for ($x = $gradStart; $x <= $gradEnd; $x++) {
            $progress = ($x - $gradStart) / ($gradEnd - $gradStart); // 0.0 → 1.0
            $alpha    = (int) round(127 * (1 - $progress));           // 127=transparent, 0=opaque
            $color    = imagecolorallocatealpha($canvas, 11, 11, 11, $alpha);
            imageline($canvas, $x, 0, $x, self::HEIGHT, $color);
        }
    }

    // ── Text panel (right side) ───────────────────────────────────────────────

    private function renderTextPanel(\GdImage $canvas, Restaurant $restaurant, ?string $boldFont, ?string $regularFont): void
    {
        $xStart = self::PHOTO_WIDTH + 50; // left edge of text block
        $xEnd   = self::WIDTH - 40;       // right margin
        $textW  = $xEnd - $xStart;

        // Allocate colors
        $cGold    = imagecolorallocate($canvas, ...self::COLOR_GOLD);
        $cWhite   = imagecolorallocate($canvas, ...self::COLOR_WHITE);
        $cGray    = imagecolorallocate($canvas, ...self::COLOR_GRAY);

        $y = 55;

        // ── "FAMER" brand label (top-right) ──────────────────────────────
        $brandText = 'FAMER';
        if ($boldFont) {
            $bbox = imagettfbbox(20, 0, $boldFont, $brandText);
            $bW   = $bbox[2] - $bbox[0];
            imagettftext($canvas, 20, 0, $xEnd - $bW, $y, $cGold, $boldFont, $brandText);
        } else {
            imagestring($canvas, 5, $xEnd - 60, $y - 15, $brandText, $cGold);
        }

        $y += 50;

        // ── Restaurant name ───────────────────────────────────────────────
        $name = $this->sanitizeText($restaurant->name);

        if ($boldFont) {
            $y = $this->renderWrappedText($canvas, $name, $boldFont, 42, $xStart, $y, $textW, $cWhite, 2, 54);
        } else {
            $y = $this->renderWrappedTextBitmap($canvas, $name, $xStart, $y, $textW, $cWhite, 5, 20);
        }

        $y += 24;

        // ── City, State ───────────────────────────────────────────────────
        $cityState = trim(
            ($restaurant->city ?? '') .
            ($restaurant->city && $restaurant->state ? ', ' : '') .
            ($restaurant->state ? $restaurant->state->name : '')
        );

        if ($cityState) {
            if ($regularFont) {
                imagettftext($canvas, 22, 0, $xStart, $y, $cGray, $regularFont, $this->sanitizeText($cityState));
            } else {
                imagestring($canvas, 4, $xStart, $y - 15, $this->sanitizeAscii($cityState), $cGray);
            }
            $y += 36;
        }

        $y += 16;

        // ── Stars + rating ─────────────────────────────────────────────────
        $rating      = (float) ($restaurant->average_rating ?? 0);
        $reviewCount = (int)   ($restaurant->total_reviews  ?? 0);

        if ($rating > 0) {
            $stars    = $this->buildStarString($rating);
            $ratingTx = number_format($rating, 1);

            if ($boldFont && $regularFont) {
                // Stars in gold
                imagettftext($canvas, 28, 0, $xStart, $y, $cGold, $boldFont, $stars);
                $starsW = $this->ttfWidth($boldFont, 28, $stars) + 14;

                // Numeric rating next to stars
                imagettftext($canvas, 30, 0, $xStart + $starsW, $y, $cGold, $boldFont, $ratingTx);
                $y += 44;

                // Review count below
                if ($reviewCount > 0) {
                    $reviewTx = '(' . number_format($reviewCount) . ' reviews)';
                    imagettftext($canvas, 18, 0, $xStart, $y, $cGray, $regularFont, $reviewTx);
                    $y += 30;
                }
            } else {
                // Bitmap fallback
                imagestring($canvas, 4, $xStart, $y, $this->buildStarAscii($rating) . ' ' . $ratingTx, $cGold);
                $y += 22;
                if ($reviewCount > 0) {
                    imagestring($canvas, 3, $xStart, $y, '(' . number_format($reviewCount) . ' reviews)', $cGray);
                    $y += 18;
                }
            }
        }

        // ── Category badge ─────────────────────────────────────────────────
        $category = $restaurant->category?->name;
        if ($category && $boldFont) {
            $y += 12;
            $badgeText = $this->sanitizeText($category);
            $badgeW    = $this->ttfWidth($boldFont, 14, $badgeText) + 24;
            $badgeH    = 30;
            $badgeY    = $y - 22;

            // Gold border rectangle
            $cGoldBorder = imagecolorallocate($canvas, ...self::COLOR_GOLD);
            imagerectangle($canvas, $xStart, $badgeY, $xStart + $badgeW, $badgeY + $badgeH, $cGoldBorder);

            imagettftext($canvas, 14, 0, $xStart + 12, $y, $cGold, $boldFont, $badgeText);
        }
    }

    private function renderBottomBar(\GdImage $canvas, ?string $regularFont): void
    {
        $cGold = imagecolorallocate($canvas, ...self::COLOR_GOLD);
        $cGray = imagecolorallocate($canvas, ...self::COLOR_GRAY);

        $lineY = self::HEIGHT - 52;

        // Thin gold horizontal rule from photo edge to right
        imagesetthickness($canvas, 1);
        imageline($canvas, self::PHOTO_WIDTH + 10, $lineY, self::WIDTH - 20, $lineY, $cGold);

        $domainText = 'restaurantesmexicanosfamosos.com.mx';
        $textY      = self::HEIGHT - 26;

        if ($regularFont) {
            $bbox = imagettfbbox(14, 0, $regularFont, $domainText);
            $textW = $bbox[2] - $bbox[0];
            $textX = self::WIDTH - 30 - $textW;
            imagettftext($canvas, 14, 0, $textX, $textY, $cGray, $regularFont, $domainText);
        } else {
            imagestring($canvas, 2, self::WIDTH - 340, $textY - 10, $domainText, $cGray);
        }
    }

    // ── Text utilities ────────────────────────────────────────────────────────

    /**
     * Render text with word-wrap, respecting $maxLines.
     * Returns the new Y position after the text block.
     */
    private function renderWrappedText(
        \GdImage $canvas,
        string $text,
        string $font,
        int $size,
        int $x,
        int $y,
        int $maxWidth,
        int $color,
        int $maxLines,
        int $lineHeight
    ): int {
        $words = explode(' ', $text);
        $lines = [];
        $line  = '';

        foreach ($words as $word) {
            $test = $line === '' ? $word : $line . ' ' . $word;
            $w    = $this->ttfWidth($font, $size, $test);

            if ($w <= $maxWidth) {
                $line = $test;
            } else {
                if ($line !== '') {
                    $lines[] = $line;
                }
                $line = $word;
                if (count($lines) >= $maxLines) {
                    break;
                }
            }
        }
        if ($line !== '' && count($lines) < $maxLines) {
            $lines[] = $line;
        }

        // Truncate last line with ellipsis if needed
        if (count($lines) === $maxLines) {
            $last = end($lines);
            while ($this->ttfWidth($font, $size, $last . '…') > $maxWidth && strlen($last) > 1) {
                $last = mb_substr($last, 0, -1);
            }
            $lines[count($lines) - 1] = $last . (str_ends_with($last, ' ') ? '…' : '…');
        }

        foreach ($lines as $ln) {
            imagettftext($canvas, $size, 0, $x, $y, $color, $font, $ln);
            $y += $lineHeight;
        }

        return $y;
    }

    /**
     * Bitmap font fallback for renderWrappedText.
     */
    private function renderWrappedTextBitmap(
        \GdImage $canvas,
        string $text,
        int $x,
        int $y,
        int $maxWidth,
        int $color,
        int $fontId,
        int $lineHeight
    ): int {
        $charW   = imagefontwidth($fontId);
        $maxChars = (int) floor($maxWidth / $charW);

        $asciiText = $this->sanitizeAscii($text);
        $lines     = array_slice(
            array_map('trim', explode("\n", wordwrap($asciiText, $maxChars, "\n", true))),
            0, 2
        );

        foreach ($lines as $ln) {
            imagestring($canvas, $fontId, $x, $y - 15, $ln, $color);
            $y += $lineHeight;
        }

        return $y;
    }

    private function ttfWidth(string $font, int $size, string $text): int
    {
        $bbox = imagettfbbox($size, 0, $font, $text);
        return abs($bbox[2] - $bbox[0]);
    }

    private function buildStarString(float $rating): string
    {
        $full  = (int) floor($rating);
        $half  = ($rating - $full) >= 0.4 ? 1 : 0;
        $empty = 5 - $full - $half;

        return str_repeat('★', $full) . ($half ? '½' : '') . str_repeat('☆', $empty);
    }

    private function buildStarAscii(float $rating): string
    {
        $full  = (int) round($rating);
        $empty = 5 - $full;
        return str_repeat('*', $full) . str_repeat('-', $empty);
    }

    /**
     * Strip characters that GD/TTF can't render cleanly (control chars, etc.).
     * Keeps Unicode for TTF rendering.
     */
    private function sanitizeText(string $text): string
    {
        // Remove null bytes and control characters
        $clean = preg_replace('/[\x00-\x1F\x7F]/u', '', $text);
        return mb_substr(trim($clean ?? $text), 0, 120);
    }

    /**
     * Strip to ASCII for bitmap (imagestring) rendering.
     */
    private function sanitizeAscii(string $text): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        return mb_substr(trim($ascii ?: $text), 0, 120);
    }

    /**
     * Find the first readable font file from a list of candidates.
     */
    private function resolveFontPath(array $candidates): ?string
    {
        foreach ($candidates as $path) {
            if (is_readable($path)) {
                return $path;
            }
        }
        return null;
    }
}
