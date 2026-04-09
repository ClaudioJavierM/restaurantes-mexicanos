<?php

namespace App\Filament\Widgets;

use App\Models\Restaurant;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class DataCompletenessWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = true;

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = null;

    protected ?string $heading = 'Calidad de Datos';

    protected ?string $description = 'Completitud del directorio de 26K+ restaurantes';

    protected function getStats(): array
    {
        try {
            $approvedTotal = max(Restaurant::where('status', 'approved')->count(), 1);
        } catch (\Throwable $e) {
            $approvedTotal = 1;
        }

        // --- Con Fotos ---
        try {
            $withPhotos = DB::table('restaurants')
                ->where('status', 'approved')
                ->whereNull('deleted_at')
                ->where(function ($q) {
                    $q->whereRaw("JSON_LENGTH(yelp_photos) > 0")
                      ->orWhereExists(function ($sub) {
                          $sub->from('media')
                              ->whereColumn('media.model_id', 'restaurants.id')
                              ->where('media.model_type', 'App\\Models\\Restaurant');
                      });
                })
                ->count();
        } catch (\Throwable $e) {
            $withPhotos = 0;
        }
        $photosPct = round(($withPhotos / $approvedTotal) * 100, 1);
        $photosColor = $photosPct > 50 ? 'success' : ($photosPct > 25 ? 'warning' : 'danger');

        // --- Con Horarios ---
        try {
            $withHours = DB::table('restaurants')
                ->where('status', 'approved')
                ->whereNull('deleted_at')
                ->whereNotNull('yelp_hours')
                ->whereRaw("JSON_LENGTH(yelp_hours) > 0")
                ->count();
        } catch (\Throwable $e) {
            $withHours = 0;
        }
        $hoursPct = round(($withHours / $approvedTotal) * 100, 1);
        $hoursColor = $hoursPct > 50 ? 'success' : ($hoursPct > 25 ? 'warning' : 'danger');

        // --- Con Coordenadas ---
        try {
            $withCoords = DB::table('restaurants')
                ->where('status', 'approved')
                ->whereNull('deleted_at')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->count();
        } catch (\Throwable $e) {
            $withCoords = 0;
        }
        $coordsPct = round(($withCoords / $approvedTotal) * 100, 1);
        $coordsColor = $coordsPct > 50 ? 'success' : ($coordsPct > 25 ? 'warning' : 'danger');

        // --- Con Rating ---
        try {
            $withRating = DB::table('restaurants')
                ->where('status', 'approved')
                ->whereNull('deleted_at')
                ->where(function ($q) {
                    $q->where('average_rating', '>', 0)
                      ->orWhere('yelp_rating', '>', 0);
                })
                ->count();
        } catch (\Throwable $e) {
            $withRating = 0;
        }
        $ratingPct = round(($withRating / $approvedTotal) * 100, 1);
        $ratingColor = $ratingPct > 50 ? 'success' : ($ratingPct > 25 ? 'warning' : 'danger');

        // --- Con Descripción IA ---
        try {
            $withAiDesc = DB::table('restaurants')
                ->where('status', 'approved')
                ->whereNull('deleted_at')
                ->whereNotNull('ai_description')
                ->where('ai_description', '!=', '')
                ->count();
        } catch (\Throwable $e) {
            $withAiDesc = 0;
        }
        $aiDescPct = round(($withAiDesc / $approvedTotal) * 100, 1);
        $aiDescColor = $aiDescPct > 50 ? 'success' : ($aiDescPct > 25 ? 'warning' : 'danger');

        // --- Enriquecidos Yelp ---
        try {
            $yelpEnriched = DB::table('restaurants')
                ->where('status', 'approved')
                ->whereNull('deleted_at')
                ->whereNotNull('yelp_enriched_at')
                ->count();
        } catch (\Throwable $e) {
            $yelpEnriched = 0;
        }
        $yelpPct = round(($yelpEnriched / $approvedTotal) * 100, 1);

        // Sparklines (last 7 days daily counts for each metric)
        $photosSparkline = $this->getSparkline('restaurants', 'yelp_enriched_at', 7); // approximate reuse
        $enrichedSparkline = $this->getSparklineForColumn('yelp_enriched_at', 7);

        return [
            Stat::make('Con Fotos', number_format($withPhotos))
                ->description($photosPct . '% de ' . number_format($approvedTotal) . ' aprobados')
                ->descriptionIcon('heroicon-m-photo')
                ->color($photosColor),

            Stat::make('Con Horarios', number_format($withHours))
                ->description($hoursPct . '% tienen yelp_hours')
                ->descriptionIcon('heroicon-m-clock')
                ->color($hoursColor),

            Stat::make('Con Coordenadas', number_format($withCoords))
                ->description($coordsPct . '% con lat/lng')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color($coordsColor),

            Stat::make('Con Rating', number_format($withRating))
                ->description($ratingPct . '% tienen calificación')
                ->descriptionIcon('heroicon-m-star')
                ->color($ratingColor),

            Stat::make('Con Descripción IA', number_format($withAiDesc))
                ->description($aiDescPct . '% generados por AI')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color($aiDescColor),

            Stat::make('Enriquecidos Yelp', number_format($yelpEnriched))
                ->description($yelpPct . '% — Con datos completos de Yelp')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('info')
                ->chart($enrichedSparkline),
        ];
    }

    /**
     * Returns a 7-day sparkline for restaurants that had yelp_enriched_at set that day.
     */
    protected function getSparklineForColumn(string $column, int $days): array
    {
        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            try {
                $count = DB::table('restaurants')
                    ->whereDate($column, $date)
                    ->where('status', 'approved')
                    ->whereNull('deleted_at')
                    ->count();
            } catch (\Throwable $e) {
                $count = 0;
            }
            $trend[] = $count;
        }
        return $trend;
    }

    /**
     * Generic daily sparkline for any restaurants column date.
     */
    protected function getSparkline(string $table, string $dateCol, int $days): array
    {
        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            try {
                $trend[] = DB::table($table)->whereDate($dateCol, $date)->count();
            } catch (\Throwable $e) {
                $trend[] = 0;
            }
        }
        return $trend;
    }
}
