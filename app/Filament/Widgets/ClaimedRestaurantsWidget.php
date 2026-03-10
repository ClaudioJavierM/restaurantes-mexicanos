<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClaimedRestaurantsWidget extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = "60s";
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = "full";

    protected ?string $heading = "Claims y Suscripciones";
    protected ?string $description = "Restaurantes reclamados y suscripciones activas";

    protected function getStats(): array
    {
        // Total claimed (is_claimed = true OR has user_id)
        $totalClaimed = DB::table("restaurants")
            ->where("status", "approved")
            ->whereNull("deleted_at")
            ->where(function($q) {
                $q->where("is_claimed", true)
                   ->orWhereNotNull("user_id");
            })
            ->count();

        // Claimed this month
        $claimedThisMonth = DB::table("restaurants")
            ->whereNull("deleted_at")
            ->whereNotNull("claimed_at")
            ->whereMonth("claimed_at", Carbon::now()->month)
            ->whereYear("claimed_at", Carbon::now()->year)
            ->count();

        // Claimed this week
        $claimedThisWeek = DB::table("restaurants")
            ->whereNull("deleted_at")
            ->whereNotNull("claimed_at")
            ->where("claimed_at", ">=", Carbon::now()->startOfWeek())
            ->count();

        // Subscription tiers (from restaurants table directly)
        $premiumCount = DB::table("restaurants")
            ->whereNull("deleted_at")
            ->where("subscription_tier", "premium")
            ->count();

        $eliteCount = DB::table("restaurants")
            ->whereNull("deleted_at")
            ->where("subscription_tier", "elite")
            ->count();

        $totalPaid = $premiumCount + $eliteCount;

        // ACTUALIZADO: Invitaciones enviadas desde email_logs
        $invitationsSent = DB::table("email_logs")
            ->whereIn("category", ["famer_email_1", "famer_email_2", "famer_email_3", "claim_invitation"])
            ->distinct("restaurant_id")
            ->count("restaurant_id");

        // Total restaurants
        $totalRestaurants = DB::table("restaurants")
            ->where("status", "approved")
            ->whereNull("deleted_at")
            ->count();

        // Claim rate
        $claimRate = $totalRestaurants > 0 ? round(($totalClaimed / $totalRestaurants) * 100, 1) : 0;

        // Sin email enviado (disponibles)
        $sinEmail = DB::table("restaurants")
            ->where("status", "approved")
            ->whereNull("deleted_at")
            ->whereNotNull("email")
            ->where("email", "<>", "")
            ->where("is_claimed", false)
            ->whereNull("famer_email_1_sent_at")
            ->count();

        return [
            Stat::make("Total Reclamados", number_format($totalClaimed))
                ->description($claimRate . "% de " . number_format($totalRestaurants) . " restaurantes")
                ->descriptionIcon("heroicon-m-flag")
                ->color("success")
                ->chart($this->getClaimTrend()),

            Stat::make("Suscripciones de Pago", number_format($totalPaid))
                ->description($premiumCount . " Premium · " . $eliteCount . " Elite")
                ->descriptionIcon("heroicon-m-star")
                ->color($totalPaid > 0 ? "warning" : "gray"),

            Stat::make("Este Mes", number_format($claimedThisMonth))
                ->description("Nuevos claims")
                ->descriptionIcon("heroicon-m-calendar")
                ->color("primary"),

            Stat::make("Esta Semana", number_format($claimedThisWeek))
                ->description("Claims recientes")
                ->descriptionIcon("heroicon-m-clock")
                ->color("info"),

            Stat::make("Emails Enviados", number_format($invitationsSent))
                ->description("Restaurantes contactados")
                ->descriptionIcon("heroicon-m-envelope")
                ->color($invitationsSent > 0 ? "warning" : "gray"),

            Stat::make("Sin Contactar", number_format($sinEmail))
                ->description("Pendientes de email")
                ->descriptionIcon("heroicon-m-building-storefront")
                ->color("gray"),
        ];
    }

    protected function getClaimTrend(): array
    {
        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = DB::table("restaurants")
                ->whereNull("deleted_at")
                ->whereNotNull("claimed_at")
                ->whereMonth("claimed_at", $date->month)
                ->whereYear("claimed_at", $date->year)
                ->count();
            $trend[] = $count;
        }
        return $trend;
    }
}
