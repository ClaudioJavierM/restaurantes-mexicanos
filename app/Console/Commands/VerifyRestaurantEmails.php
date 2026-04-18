<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyRestaurantEmails extends Command
{
    protected $signature = 'famer:verify-emails
                            {--dry-run : Mostrar resultados sin guardar}
                            {--re-verify : Re-verificar emails que ya tienen status}
                            {--limit=0 : Limitar número de dominios a verificar (0 = todos)}';

    protected $description = 'Verifica emails de restaurantes: sintaxis + MX record del dominio. Marca inválidos para excluirlos de campañas.';

    public function handle(): int
    {
        $dryRun    = $this->option('dry-run');
        $reVerify  = $this->option('re-verify');
        $limit     = (int) $this->option('limit');

        $this->info('Cargando emails a verificar...');

        // Build base query
        $query = DB::table('restaurants')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where('country', 'US') // solo pipeline activo
            ->select('id', 'email', 'email_status');

        if (!$reVerify) {
            $query->whereNull('email_status');
        }

        $restaurants = $query->get();

        if ($restaurants->isEmpty()) {
            $this->info('No hay emails pendientes de verificar.');
            $this->line('Tip: usa --re-verify para re-verificar todos.');
            return Command::SUCCESS;
        }

        $this->info("Emails a verificar: {$restaurants->count()}");

        // ── Paso 1: Agrupar por dominio ─────────────────────────
        $byDomain = [];
        foreach ($restaurants as $r) {
            $email = strtolower(trim($r->email));

            // Validar sintaxis primero
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $byDomain['__invalid_syntax__'][] = $r->id;
                continue;
            }

            $domain = substr($email, strpos($email, '@') + 1);
            $byDomain[$domain][] = $r->id;
        }

        // ── Paso 2: MX lookup por dominio ──────────────────────
        $this->info('Verificando MX records...');

        // Dominios grandes conocidos — siempre válidos, skip DNS
        $knownValid = [
            'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com',
            'icloud.com', 'me.com', 'aol.com', 'msn.com', 'live.com',
            'comcast.net', 'att.net', 'verizon.net', 'sbcglobal.net',
            'cox.net', 'charter.net', 'earthlink.net', 'protonmail.com',
        ];

        $results = [
            'valid'          => [],
            'no_mx'          => [],
            'invalid_syntax' => [],
        ];

        $results['invalid_syntax'] = $byDomain['__invalid_syntax__'] ?? [];
        unset($byDomain['__invalid_syntax__']);

        $domains  = array_keys($byDomain);
        $total    = count($domains);
        $checked  = 0;
        $limited  = $limit > 0 ? min($limit, $total) : $total;

        $bar = $this->output->createProgressBar($limited);
        $bar->start();

        foreach ($domains as $domain) {
            if ($limit > 0 && $checked >= $limit) break;

            if (in_array($domain, $knownValid)) {
                $results['valid'] = array_merge($results['valid'], $byDomain[$domain]);
            } else {
                $hasMx = $this->domainHasMx($domain);
                if ($hasMx) {
                    $results['valid'] = array_merge($results['valid'], $byDomain[$domain]);
                } else {
                    $results['no_mx'] = array_merge($results['no_mx'], $byDomain[$domain]);
                }
            }

            $checked++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // ── Paso 3: También marcar bounceados desde email_logs ──
        $bouncedEmails = DB::table('email_logs')
            ->where('status', 'bounced')
            ->whereNotNull('from_email') // solo FAMER
            ->pluck('to_email')
            ->map(fn($e) => strtolower(trim($e)))
            ->unique()
            ->toArray();

        if (!empty($bouncedEmails)) {
            $bouncedIds = DB::table('restaurants')
                ->whereIn(DB::raw('LOWER(TRIM(email))'), $bouncedEmails)
                ->pluck('id')
                ->toArray();

            // Bounced overrides valid — es más específico
            $results['valid']  = array_diff($results['valid'],  $bouncedIds);
            $results['no_mx']  = array_diff($results['no_mx'],  $bouncedIds);
            $results['bounced'] = $bouncedIds;
        } else {
            $results['bounced'] = [];
        }

        // ── Reporte ─────────────────────────────────────────────
        $this->table(
            ['Estado',          'Cantidad', 'Descripción'],
            [
                ['✅ Válido',       count($results['valid']),          'Tienen MX record — seguros para enviar'],
                ['❌ Sin MX',       count($results['no_mx']),          'Dominio sin servidor de email — rebotarán'],
                ['⛔ Sintaxis',     count($results['invalid_syntax']), 'Email malformado — nunca enviar'],
                ['🔴 Bounced',      count($results['bounced']),        'Ya rebotaron en envíos previos'],
            ]
        );

        $totalInvalid = count($results['no_mx']) + count($results['invalid_syntax']) + count($results['bounced']);
        $this->line("Total a excluir de campañas: <fg=red>{$totalInvalid}</>");

        if ($dryRun) {
            $this->warn('[dry-run] No se guardaron cambios.');

            // Mostrar muestra de dominios sin MX
            if (!empty($results['no_mx'])) {
                $sample = DB::table('restaurants')
                    ->whereIn('id', array_slice($results['no_mx'], 0, 10))
                    ->pluck('email')->toArray();
                $this->line('Muestra de emails sin MX:');
                foreach ($sample as $e) $this->line("  · {$e}");
            }

            return Command::SUCCESS;
        }

        // ── Paso 4: Guardar en DB ────────────────────────────────
        $this->info('Guardando resultados...');

        foreach ([
            'valid'          => $results['valid'],
            'no_mx'          => $results['no_mx'],
            'invalid_syntax' => $results['invalid_syntax'],
            'bounced'        => $results['bounced'],
        ] as $status => $ids) {
            if (empty($ids)) continue;
            foreach (array_chunk($ids, 500) as $chunk) {
                DB::table('restaurants')
                    ->whereIn('id', $chunk)
                    ->update(['email_status' => $status]);
            }
        }

        $this->info('✓ Verificación completada.');
        $this->line('Los emails con status <fg=red>no_mx, invalid_syntax, bounced</> serán ignorados automáticamente por las campañas.');

        return Command::SUCCESS;
    }

    private function domainHasMx(string $domain): bool
    {
        // Try MX records first
        $mx = @dns_get_record($domain, DNS_MX);
        if (!empty($mx)) return true;

        // Some domains use A records only (no explicit MX but still receive email)
        $a = @dns_get_record($domain, DNS_A);
        return !empty($a);
    }
}
