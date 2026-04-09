<?php

namespace App\Filament\Widgets;

use App\Models\Restaurant;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RestaurantGrowthWidget extends ChartWidget
{
    protected static bool $isLazy = true;

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Crecimiento de Restaurantes';

    protected static ?string $description = 'Importaciones y aprobaciones en los últimos 30 días';

    protected static ?string $pollingInterval = null;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $labels = [];
        $totalData = [];
        $approvedData = [];

        try {
            // Build a date range for the last 30 days
            $dates = [];
            for ($i = 29; $i >= 0; $i--) {
                $dates[] = now()->subDays($i)->toDateString();
            }

            // All restaurants created in last 30 days grouped by date
            $totalByDate = DB::table('restaurants')
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->where('created_at', '>=', now()->subDays(29)->startOfDay())
                ->whereNull('deleted_at')
                ->groupBy('date')
                ->pluck('total', 'date')
                ->toArray();

            // Approved restaurants created in last 30 days grouped by date
            $approvedByDate = DB::table('restaurants')
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->where('created_at', '>=', now()->subDays(29)->startOfDay())
                ->where('status', 'approved')
                ->whereNull('deleted_at')
                ->groupBy('date')
                ->pluck('total', 'date')
                ->toArray();

            foreach ($dates as $date) {
                $labels[] = Carbon::parse($date)->format('d/m');
                $totalData[] = $totalByDate[$date] ?? 0;
                $approvedData[] = $approvedByDate[$date] ?? 0;
            }
        } catch (\Throwable $e) {
            // Return empty chart on error
            for ($i = 29; $i >= 0; $i--) {
                $labels[] = now()->subDays($i)->format('d/m');
                $totalData[] = 0;
                $approvedData[] = 0;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Importados',
                    'data' => $totalData,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 3,
                ],
                [
                    'label' => 'Aprobados',
                    'data' => $approvedData,
                    'borderColor' => '#22c55e',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 3,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
