<?php

namespace App\Console\Commands;

use App\Models\EmailLog;
use Illuminate\Console\Command;

class CampaignReport extends Command
{
    protected $signature = 'famer:campaign-report
                            {--period=7 : Días a reportar (7, 30, 90)}
                            {--format=text : Formato de salida (text, json, markdown)}
                            {--alert : Solo mostrar si hay alertas (bounce >2%, open <10%)}';

    protected $description = 'Reporte de métricas de campañas de email para monitoreo por agentes';

    public function handle(): int
    {
        $days      = (int) $this->option('period');
        $format    = $this->option('format');
        $alertOnly = $this->option('alert');

        $since = now()->subDays($days);

        // Datos de email_logs (campañas FAMER, excluye SDV — SDV llega con from_email=NULL)
        $logs = EmailLog::whereNotNull('from_email')
            ->where('sent_at', '>=', $since);

        $sent      = (clone $logs)->count();
        $delivered = (clone $logs)->where('status', 'delivered')->count();
        $opened    = (clone $logs)->where('status', 'opened')->count();
        $clicked   = (clone $logs)->where('status', 'clicked')->count();
        $bounced   = (clone $logs)->where('status', 'bounced')->count();
        $failed    = (clone $logs)->where('status', 'failed')->count();

        $deliveryRate = $sent > 0 ? round(($delivered / $sent) * 100, 1) : 0;
        $openRate     = $sent > 0 ? round(($opened   / $sent) * 100, 1) : 0;
        $clickRate    = $sent > 0 ? round(($clicked  / $sent) * 100, 1) : 0;
        $bounceRate   = $sent > 0 ? round(($bounced  / $sent) * 100, 2) : 0;

        // Por categoría
        $byCategory = EmailLog::whereNotNull('from_email')
            ->where('sent_at', '>=', $since)
            ->selectRaw("category, COUNT(*) as sent,
                         SUM(status='opened') as opened,
                         SUM(status='clicked') as clicked,
                         SUM(status='bounced') as bounced")
            ->groupBy('category')
            ->orderByDesc('sent')
            ->get();

        // Pendientes en pipeline (aprobados, no reclamados, sin invitación enviada, con email)
        $pendingEmailCount = \App\Models\Restaurant::where('status', 'approved')
            ->where('is_claimed', false)
            ->whereNull('claim_invitation_sent_at')
            ->where(function ($q) {
                $q->whereNotNull('owner_email')->orWhereNotNull('email');
            })->count();

        // Alerts
        $alerts = [];
        if ($bounceRate > 3)               $alerts[] = "CRITICO: Bounce rate {$bounceRate}% supera el 3%";
        if ($bounceRate > 1 && $bounceRate <= 3) $alerts[] = "AVISO: Bounce rate {$bounceRate}% — monitorear";
        if ($openRate < 10 && $sent > 50)  $alerts[] = "AVISO: Open rate {$openRate}% es bajo (benchmark: 20-25%)";
        if ($openRate > 25)                $alerts[] = "EXCELENTE: Open rate {$openRate}% supera el benchmark";
        if ($failed > 0)                   $alerts[] = "AVISO: {$failed} emails fallaron en el envío";

        // Si --alert y no hay alertas, salir silenciosamente
        if ($alertOnly && empty($alerts)) {
            return 0;
        }

        // ── JSON ─────────────────────────────────────────────────────────────
        if ($format === 'json') {
            $this->line(json_encode([
                'period_days'      => $days,
                'generated_at'     => now()->toISOString(),
                'sent'             => $sent,
                'delivered'        => $delivered,
                'opened'           => $opened,
                'clicked'          => $clicked,
                'bounced'          => $bounced,
                'failed'           => $failed,
                'delivery_rate'    => $deliveryRate,
                'open_rate'        => $openRate,
                'click_rate'       => $clickRate,
                'bounce_rate'      => $bounceRate,
                'pending_pipeline' => $pendingEmailCount,
                'alerts'           => $alerts,
                'by_category'      => $byCategory->toArray(),
            ], JSON_PRETTY_PRINT));

            return 0;
        }

        // ── MARKDOWN ─────────────────────────────────────────────────────────
        if ($format === 'markdown') {
            $this->line("# Reporte de Campañas FAMER — Últimos {$days} días");
            $this->line("**Generado:** " . now()->format('Y-m-d H:i') . " CST");
            $this->newLine();
            $this->line("## Métricas Globales");
            $this->line("| Métrica | Valor |");
            $this->line("|---------|-------|");
            $this->line("| Emails enviados | {$sent} |");
            $this->line("| Tasa de entrega | {$deliveryRate}% |");
            $this->line("| Tasa de apertura | {$openRate}% |");
            $this->line("| Tasa de clicks | {$clickRate}% |");
            $this->line("| Tasa de rebote | {$bounceRate}% |");
            $this->line("| Pipeline pendiente | {$pendingEmailCount} restaurantes |");
            $this->newLine();
            $this->line("## Por Campaña");
            $this->line("| Campaña | Enviados | Abiertos | % Apertura | Clicks |");
            $this->line("|---------|----------|----------|------------|--------|");
            foreach ($byCategory as $cat) {
                $catRate = $cat->sent > 0 ? round(($cat->opened / $cat->sent) * 100, 1) : 0;
                $this->line("| {$cat->category} | {$cat->sent} | {$cat->opened} | {$catRate}% | {$cat->clicked} |");
            }
            if (!empty($alerts)) {
                $this->newLine();
                $this->line("## Alertas");
                foreach ($alerts as $alert) {
                    $this->line("- {$alert}");
                }
            }

            return 0;
        }

        // ── TEXT (default) ───────────────────────────────────────────────────
        $this->info("=== FAMER Campaign Report — Últimos {$days} días ===");
        $this->table(
            ['Métrica', 'Valor', 'Rate'],
            [
                ['Enviados',   $sent,      '—'],
                ['Entregados', $delivered, $deliveryRate . '%'],
                ['Abiertos',   $opened,    $openRate . '%'],
                ['Clicks',     $clicked,   $clickRate . '%'],
                ['Rebotados',  $bounced,   $bounceRate . '%'],
                ['Fallidos',   $failed,    '—'],
            ]
        );
        $this->line("Pipeline pendiente: {$pendingEmailCount} restaurantes sin invitar");
        if (!empty($alerts)) {
            $this->newLine();
            foreach ($alerts as $alert) {
                if (str_starts_with($alert, 'CRITICO'))    $this->error($alert);
                elseif (str_starts_with($alert, 'EXCELENTE')) $this->info($alert);
                else                                           $this->warn($alert);
            }
        }

        return 0;
    }
}
