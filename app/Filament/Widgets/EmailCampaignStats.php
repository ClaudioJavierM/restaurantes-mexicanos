<?php

namespace App\Filament\Widgets;

use App\Models\EmailLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EmailCampaignStats extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = "30s";
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = "full";
    
    protected ?string $heading = "Email Campaigns - Estadísticas Reales";
    protected ?string $description = "Datos basados en email_logs (FAMER + Claim)";

    protected function getStats(): array
    {
        $stats = Cache::remember("email_campaign_stats_v4", 60, function () {
            // Categorías de email a incluir
            $categories = ["claim_invitation", "famer_email_1", "famer_email_2", "famer_email_3"];
            
            // FUENTE ÚNICA: email_logs table
            $totalSent = EmailLog::whereIn("category", $categories)->count();
            
            $delivered = EmailLog::whereIn("category", $categories)
                ->whereIn("status", ["delivered", "opened", "clicked", "sent"])
                ->count();
                
            $opened = EmailLog::whereIn("category", $categories)
                ->whereNotNull("opened_at")
                ->count();
                
            $clicked = EmailLog::whereIn("category", $categories)
                ->whereNotNull("clicked_at")
                ->count();
                
            $bounced = EmailLog::whereIn("category", $categories)
                ->where("status", "bounced")
                ->count();
                
            $failed = EmailLog::whereIn("category", $categories)
                ->where("status", "failed")
                ->count();
            
            // Emails enviados hoy (timezone México)
            $todayMX = now("America/Mexico_City")->toDateString();
            $sentToday = EmailLog::whereIn("category", $categories)
                ->whereDate("sent_at", $todayMX)
                ->count();
            
            // Emails enviados esta semana
            $sentThisWeek = EmailLog::whereIn("category", $categories)
                ->whereBetween("sent_at", [
                    now("America/Mexico_City")->startOfWeek(), 
                    now("America/Mexico_City")->endOfWeek()
                ])
                ->count();
            
            // Restaurantes disponibles para próximas campañas
            $restaurantsAvailable = DB::table("restaurants")
                ->whereNotNull("email")
                ->where("email", "<>", "")
                ->where("status", "approved")
                ->where("is_claimed", false)
                ->whereNull("famer_email_1_sent_at")
                ->count();
            
            // Restaurantes reclamados (con owner)
            $claimed = DB::table("restaurants")
                ->where("is_claimed", true)
                ->count();
            
            // Desglose por tipo
            $famerEmail1 = EmailLog::where("category", "famer_email_1")->count();
            $famerEmail2 = EmailLog::where("category", "famer_email_2")->count();
            $famerEmail3 = EmailLog::where("category", "famer_email_3")->count();
            $claimInvitation = EmailLog::where("category", "claim_invitation")->count();
            
            return [
                "total_sent" => $totalSent,
                "delivered" => $delivered,
                "opened" => $opened,
                "clicked" => $clicked,
                "bounced" => $bounced,
                "failed" => $failed,
                "sent_today" => $sentToday,
                "sent_this_week" => $sentThisWeek,
                "available" => $restaurantsAvailable,
                "claimed" => $claimed,
                "famer_email_1" => $famerEmail1,
                "famer_email_2" => $famerEmail2,
                "famer_email_3" => $famerEmail3,
                "claim_invitation" => $claimInvitation,
                "open_rate" => $totalSent > 0 ? round(($opened / $totalSent) * 100, 1) : 0,
                "click_rate" => $opened > 0 ? round(($clicked / $opened) * 100, 1) : 0,
                "bounce_rate" => $totalSent > 0 ? round(($bounced / $totalSent) * 100, 1) : 0,
            ];
        });

        return [
            Stat::make("Total Enviados", number_format($stats["total_sent"]))
                ->description("Hoy: {$stats["sent_today"]} | Semana: {$stats["sent_this_week"]}")
                ->descriptionIcon("heroicon-m-envelope")
                ->color("primary"),
                
            Stat::make("Entregados", number_format($stats["delivered"]))
                ->description(round(($stats["delivered"] / max($stats["total_sent"], 1)) * 100, 1) . "% tasa de entrega")
                ->descriptionIcon("heroicon-m-check-circle")
                ->color("success"),
                
            Stat::make("Abiertos", number_format($stats["opened"]))
                ->description("{$stats["open_rate"]}% tasa de apertura")
                ->descriptionIcon("heroicon-m-eye")
                ->color("success"),
                
            Stat::make("Clicks", number_format($stats["clicked"]))
                ->description("{$stats["click_rate"]}% tasa de clicks")
                ->descriptionIcon("heroicon-m-cursor-arrow-rays")
                ->color("info"),
                
            Stat::make("Rebotados", number_format($stats["bounced"]))
                ->description("{$stats["failed"]} fallidos | {$stats["bounce_rate"]}% rebote")
                ->descriptionIcon("heroicon-m-x-circle")
                ->color("danger"),
                
            Stat::make("Disponibles", number_format($stats["available"]))
                ->description("{$stats["claimed"]} restaurantes reclamados")
                ->descriptionIcon("heroicon-m-building-storefront")
                ->color("warning"),
        ];
    }
}
