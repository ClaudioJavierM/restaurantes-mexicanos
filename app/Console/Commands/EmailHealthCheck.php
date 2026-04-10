<?php

namespace App\Console\Commands;

use App\Models\EmailLog;
use App\Models\EmailSuppression;
use App\Models\NewsletterEvent;
use Illuminate\Console\Command;

class EmailHealthCheck extends Command
{
    /**
     * Signature: famer:email-health
     *
     * Checks bounce rate, complaint rate, suppression list size, and delivery
     * rate for the last 24 hours.  When --alert is passed, it exits silently
     * if everything is within safe thresholds — only outputs when there is a
     * problem (suitable for cron jobs that trigger on any output).
     */
    protected $signature = 'famer:email-health
                            {--alert : Solo muestra output si hay problemas (para cron)}';

    protected $description = 'Email health check — bounce/complaint rates, delivery rate, suppression list.';

    // Thresholds
    const BOUNCE_WARN  = 2.0;   // % — trigger warning above this
    const COMPLAINT_WARN = 0.1; // % — Gmail threshold

    public function handle(): int
    {
        $alertOnly = $this->option('alert');
        $since     = now()->subHours(24);

        // ── Query last-24h stats ──────────────────────────────────────────
        $base = fn() => EmailLog::whereNotNull('from_email')->where('sent_at', '>=', $since);

        $sent      = $base()->count();
        $delivered = $base()->whereNotNull('delivered_at')->count();
        $bounced   = $base()->where('status', 'bounced')->count();
        $complained = NewsletterEvent::where('event_type', 'complained')
                        ->where('occurred_at', '>=', $since)
                        ->count();

        $totalSuppressions = EmailSuppression::count();

        $bounceRate    = $sent > 0 ? round(($bounced / $sent) * 100, 2) : 0.0;
        $complaintRate = $sent > 0 ? round(($complained / $sent) * 100, 3) : 0.0;
        $deliveryRate  = $sent > 0 ? round(($delivered / $sent) * 100, 1) : 0.0;

        // ── Evaluate health ───────────────────────────────────────────────
        $problems = [];

        if ($bounceRate > self::BOUNCE_WARN) {
            $problems[] = sprintf(
                '[CRITICO] Bounce rate: %.2f%% (umbral: %.1f%%) — %d bounces en %d enviados (24h)',
                $bounceRate, self::BOUNCE_WARN, $bounced, $sent
            );
        }

        if ($complaintRate > self::COMPLAINT_WARN) {
            $problems[] = sprintf(
                '[CRITICO] Complaint rate: %.3f%% (umbral Gmail: %.1f%%) — %d complaints en %d enviados (24h)',
                $complaintRate, self::COMPLAINT_WARN, $complained, $sent
            );
        }

        // In alert-only mode, exit silently when healthy
        if ($alertOnly && empty($problems)) {
            return self::SUCCESS;
        }

        // ── Print report ──────────────────────────────────────────────────
        $this->newLine();
        $this->line('╔══════════════════════════════════════════════════════╗');
        $this->line('║       FAMER Email Health — últimas 24 horas          ║');
        $this->line('╚══════════════════════════════════════════════════════╝');
        $this->newLine();

        $this->table(
            ['Métrica', 'Valor', 'Estado'],
            [
                [
                    'Enviados (24h)',
                    number_format($sent),
                    '—',
                ],
                [
                    'Delivery Rate',
                    $deliveryRate . '%',
                    $deliveryRate >= 95 ? '✅ OK' : ($deliveryRate >= 85 ? '⚠️  Revisar' : '❌ Crítico'),
                ],
                [
                    'Bounce Rate',
                    $bounceRate . '%',
                    $bounceRate <= 1.0 ? '✅ OK' : ($bounceRate <= self::BOUNCE_WARN ? '⚠️  Advertencia' : '❌ CRÍTICO'),
                ],
                [
                    'Complaint Rate',
                    $complaintRate . '%',
                    $complaintRate == 0 ? '✅ OK' : ($complaintRate <= self::COMPLAINT_WARN ? '⚠️  Advertencia' : '❌ CRÍTICO'),
                ],
                [
                    'Suppression List',
                    number_format($totalSuppressions),
                    $totalSuppressions > 5000 ? '⚠️  Revisar' : '✅ OK',
                ],
            ]
        );

        if (!empty($problems)) {
            $this->newLine();
            $this->error('  PROBLEMAS DETECTADOS:');
            foreach ($problems as $p) {
                $this->error("  → $p");
            }
            $this->newLine();
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('  Todo OK — email health dentro de rangos seguros.');
        $this->newLine();

        return self::SUCCESS;
    }
}
