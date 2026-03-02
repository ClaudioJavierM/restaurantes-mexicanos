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
    
    protected ?string $heading = "Restaurantes Reclamados";
    protected ?string $description = "Ownership y contacto con dueños";

    protected function getStats(): array
    {
        // Total claimed (is_claimed = true OR has user_id)
        $totalClaimed = DB::table("restaurants")
            ->where("status", "approved")
            ->where(function($q) {
                $q->where("is_claimed", true)
                   ->orWhereNotNull("user_id");
            })
            ->count();
            
        // Claimed this month
        $claimedThisMonth = DB::table("restaurants")
            ->whereNotNull("claimed_at")
            ->whereMonth("claimed_at", Carbon::now()->month)
            ->whereYear("claimed_at", Carbon::now()->year)
            ->count();
            
        // Claimed this week
        $claimedThisWeek = DB::table("restaurants")
            ->whereNotNull("claimed_at")
            ->where("claimed_at", ">=", Carbon::now()->startOfWeek())
            ->count();
        
        // ACTUALIZADO: Invitaciones enviadas desde email_logs
        $invitationsSent = DB::table("email_logs")
            ->whereIn("category", ["famer_email_1", "famer_email_2", "famer_email_3", "claim_invitation"])
            ->distinct("restaurant_id")
            ->count("restaurant_id");
            
        // Total restaurants
        $totalRestaurants = DB::table("restaurants")
            ->where("status", "approved")
            ->count();
            
        // Claim rate
        $claimRate = $totalRestaurants > 0 ? round(($totalClaimed / $totalRestaurants) * 100, 1) : 0;
        
        // With owner contact info
        $withOwnerInfo = DB::table("restaurants")
            ->where("status", "approved")
            ->where(function($q) {
                $q->whereNotNull("owner_email")
                   ->where("owner_email", "<>", "");
            })
            ->count();
            
        // Sin email enviado (disponibles)
        $sinEmail = DB::table("restaurants")
            ->where("status", "approved")
            ->whereNotNull("email")
            ->where("email", "<>", "")
            ->where("is_claimed", false)
            ->whereNull("famer_email_1_sent_at")
            ->count();

        return [
            Stat::make("Total Reclamados", number_format($totalClaimed))
                ->description($claimRate . "% de restaurantes")
                ->descriptionIcon("heroicon-m-flag")
                ->color("success")
                ->chart($this->getClaimTrend()),
                
            Stat::make("Este Mes", number_format($claimedThisMonth))
                ->description("Nuevos reclamos")
                ->descriptionIcon("heroicon-m-calendar")
                ->color("primary"),
                
            Stat::make("Esta Semana", number_format($claimedThisWeek))
                ->description("Reclamos recientes")
                ->descriptionIcon("heroicon-m-clock")
                ->color("info"),
                
            Stat::make("Emails Enviados", number_format($invitationsSent))
                ->description("Restaurantes contactados")
                ->descriptionIcon("heroicon-m-envelope")
                ->color($invitationsSent > 0 ? "warning" : "gray"),
                
            Stat::make("Con Info de Dueño", number_format($withOwnerInfo))
                ->description("Email de contacto")
                ->descriptionIcon("heroicon-m-user-circle")
                ->color("info"),
                
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
                ->whereNotNull("claimed_at")
                ->whereMonth("claimed_at", $date->month)
                ->whereYear("claimed_at", $date->year)
                ->count();
            $trend[] = $count;
        }
        return $trend;
    }
}
