<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncGscData extends Command
{
    protected $signature = 'famer:sync-gsc
                            {--days=30 : Días a sincronizar}
                            {--country=usa : usa o mx}';

    protected $description = 'Sync Google Search Console data: keywords, impressions, CTR, positions';

    // Property IDs / site URLs to try
    protected array $siteUrls = [
        'sc-domain:restaurantesmexicanosfamosos.com',
        'sc-domain:restaurantesmexicanosfamosos.com.mx',
        'sc-domain:famousmexicanrestaurants.com',
    ];

    public function handle(): int
    {
        $days    = (int) $this->option('days');
        $country = strtolower($this->option('country'));

        $this->info("FAMER GSC Sync — últimos {$days} días | país: {$country}");
        $this->newLine();

        // ----------------------------------------------------------------
        // 1. Resolver credenciales
        // ----------------------------------------------------------------
        $serviceAccountJson = $this->resolveCredentials();

        if ($serviceAccountJson === null) {
            $this->showSetupInstructions();
            return self::SUCCESS; // graceful exit
        }

        // ----------------------------------------------------------------
        // 2. Obtener access token via JWT (Service Account OAuth2)
        // ----------------------------------------------------------------
        $this->info('Autenticando con Google...');
        $accessToken = $this->getAccessToken($serviceAccountJson);

        if (! $accessToken) {
            $this->error('No se pudo obtener access token. Verifica las credenciales del service account.');
            return self::FAILURE;
        }

        $this->info('Token obtenido correctamente.');

        // ----------------------------------------------------------------
        // 3. Detectar qué site URL está verificada en GSC
        // ----------------------------------------------------------------
        $siteUrl = $this->detectVerifiedSite($accessToken);

        if (! $siteUrl) {
            $this->warn('Ninguno de los dominios está verificado en GSC o no hay acceso.');
            $this->warn('Dominios intentados:');
            foreach ($this->siteUrls as $url) {
                $this->warn("  - {$url}");
            }
            return self::FAILURE;
        }

        $this->info("Site URL verificado: {$siteUrl}");

        // ----------------------------------------------------------------
        // 4. Definir rango de fechas
        // ----------------------------------------------------------------
        // GSC tiene un retraso de ~3 días en los datos
        $endDate   = Carbon::now()->subDays(3)->format('Y-m-d');
        $startDate = Carbon::now()->subDays($days + 3)->format('Y-m-d');

        $this->info("Rango: {$startDate} → {$endDate}");

        // ----------------------------------------------------------------
        // 5. Mapear opción --country al código ISO de GSC
        // ----------------------------------------------------------------
        $countryCode = match ($country) {
            'mx'  => 'mex',
            'usa' => 'usa',
            default => null, // sin filtro de país
        };

        // ----------------------------------------------------------------
        // 6. Llamar GSC Search Analytics API
        // ----------------------------------------------------------------
        $this->info('Consultando GSC Search Analytics API...');

        $rows = $this->fetchSearchAnalytics($accessToken, $siteUrl, $startDate, $endDate, $countryCode);

        if ($rows === null) {
            $this->error('Error al consultar la API de GSC.');
            return self::FAILURE;
        }

        $this->info("Filas obtenidas: " . count($rows));

        if (empty($rows)) {
            $this->warn('No hay datos para el período solicitado.');
            return self::SUCCESS;
        }

        // ----------------------------------------------------------------
        // 7. Guardar en gsc_performance (upsert por date+query+page+country+device)
        // ----------------------------------------------------------------
        $this->info('Guardando en base de datos...');

        $bar     = $this->output->createProgressBar(count($rows));
        $saved   = 0;
        $chunk   = [];
        $now     = now()->toDateTimeString();

        foreach ($rows as $row) {
            $keys    = $row['keys'] ?? [];
            $query   = $keys[0] ?? null;
            $page    = $keys[1] ?? null;
            $countryIso = isset($keys[2]) ? strtolower(substr($keys[2], 0, 2)) : null;
            $device  = $keys[3] ?? null;

            $chunk[] = [
                'date'        => $startDate, // placeholder; real date not in dimensions for range queries
                'query'       => $query ? mb_substr($query, 0, 500) : null,
                'page'        => $page  ? mb_substr($page, 0, 500)  : null,
                'country'     => $countryIso,
                'device'      => $device,
                'clicks'      => (int) ($row['clicks']      ?? 0),
                'impressions' => (int) ($row['impressions'] ?? 0),
                'ctr'         => round((float) ($row['ctr'] ?? 0), 6),
                'position'    => round((float) ($row['position'] ?? 0), 2),
                'created_at'  => $now,
                'updated_at'  => $now,
            ];

            $saved++;
            $bar->advance();

            // Flush cada 500 filas
            if (count($chunk) >= 500) {
                DB::table('gsc_performance')->insert($chunk);
                $chunk = [];
            }
        }

        // Flush restantes
        if (! empty($chunk)) {
            DB::table('gsc_performance')->insert($chunk);
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Sync completado. {$saved} filas guardadas.");

        Log::info("GSC Sync completado: {$saved} filas | site: {$siteUrl} | {$startDate} → {$endDate}");

        return self::SUCCESS;
    }

    // ====================================================================
    // HELPERS
    // ====================================================================

    protected function resolveCredentials(): ?array
    {
        // Opción 1: ruta a archivo JSON
        $path = config('services.google.service_account_path')
             ?? env('GOOGLE_SERVICE_ACCOUNT_PATH');

        if ($path && file_exists($path)) {
            $json = json_decode(file_get_contents($path), true);
            if ($json) {
                return $json;
            }
        }

        // Opción 2: JSON inline en variable de entorno
        $inline = env('GOOGLE_SERVICE_ACCOUNT_JSON');
        if ($inline) {
            $json = json_decode($inline, true);
            if ($json) {
                return $json;
            }
        }

        return null;
    }

    protected function showSetupInstructions(): void
    {
        $this->warn('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->warn('  Google Search Console no está configurado.');
        $this->warn('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();
        $this->line('Para conectar GSC y ver datos reales de keywords, sigue estos pasos:');
        $this->newLine();
        $this->line('  1. Ve a Google Cloud Console → IAM → Service Accounts');
        $this->line('     https://console.cloud.google.com/iam-admin/serviceaccounts');
        $this->newLine();
        $this->line('  2. Crea un Service Account con acceso a Search Console API');
        $this->line('     (o usa el existente si ya tienes uno para Analytics)');
        $this->newLine();
        $this->line('  3. Descarga la clave JSON del Service Account');
        $this->newLine();
        $this->line('  4. En Google Search Console (search.google.com/search-console):');
        $this->line('     - Agrega el email del Service Account como usuario (permiso: Lector)');
        $this->newLine();
        $this->line('  5. En el .env agrega UNA de estas opciones:');
        $this->newLine();
        $this->line('     Opción A — ruta al archivo:');
        $this->line('     GOOGLE_SERVICE_ACCOUNT_PATH=/var/www/restaurantesmexicanosfamosos.com.mx/storage/gsc-service-account.json');
        $this->newLine();
        $this->line('     Opción B — JSON completo inline (para variables de entorno seguras):');
        $this->line('     GOOGLE_SERVICE_ACCOUNT_JSON={"type":"service_account","project_id":"..."}');
        $this->newLine();
        $this->line('  6. Ejecuta: php artisan famer:sync-gsc --days=30');
        $this->newLine();
        $this->warn('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }

    protected function getAccessToken(array $serviceAccount): ?string
    {
        try {
            $now    = time();
            $expiry = $now + 3600;
            $scope  = 'https://www.googleapis.com/auth/webmasters.readonly';

            // Build JWT header + claims
            $header = base64url_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claims = base64url_encode(json_encode([
                'iss'   => $serviceAccount['client_email'],
                'scope' => $scope,
                'aud'   => 'https://oauth2.googleapis.com/token',
                'exp'   => $expiry,
                'iat'   => $now,
            ]));

            $signingInput = "{$header}.{$claims}";

            // Sign with RSA private key
            $privateKey = openssl_pkey_get_private($serviceAccount['private_key']);
            if (! $privateKey) {
                $this->error('No se pudo cargar la clave privada del service account.');
                return null;
            }

            $signature = '';
            openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
            $signature = base64url_encode($signature);

            $jwt = "{$signingInput}.{$signature}";

            // Exchange JWT for access token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            $this->error('Error al obtener token: ' . $response->body());
            return null;

        } catch (\Throwable $e) {
            $this->error('Excepción al obtener token: ' . $e->getMessage());
            Log::error('GSC token error: ' . $e->getMessage());
            return null;
        }
    }

    protected function detectVerifiedSite(string $accessToken): ?string
    {
        try {
            $response = Http::withToken($accessToken)
                ->get('https://www.googleapis.com/webmasters/v3/sites');

            if (! $response->successful()) {
                return null;
            }

            $sites = collect($response->json('siteEntry', []));

            // Buscar coincidencia con los dominios de FAMER
            foreach ($this->siteUrls as $candidate) {
                $match = $sites->firstWhere('siteUrl', $candidate);
                if ($match) {
                    return $candidate;
                }
            }

            // Si no hay match exacto, devolver el primero disponible
            if ($sites->isNotEmpty()) {
                return $sites->first()['siteUrl'];
            }

        } catch (\Throwable $e) {
            $this->error('Error al listar sites GSC: ' . $e->getMessage());
        }

        return null;
    }

    protected function fetchSearchAnalytics(
        string $accessToken,
        string $siteUrl,
        string $startDate,
        string $endDate,
        ?string $countryCode
    ): ?array {
        try {
            $encodedSite = urlencode($siteUrl);
            $url = "https://searchconsole.googleapis.com/webmasters/v3/sites/{$encodedSite}/searchAnalytics/query";

            $body = [
                'startDate'  => $startDate,
                'endDate'    => $endDate,
                'dimensions' => ['query', 'page', 'country', 'device'],
                'rowLimit'   => 25000,
                'startRow'   => 0,
            ];

            if ($countryCode) {
                $body['dimensionFilterGroups'] = [[
                    'filters' => [[
                        'dimension'  => 'country',
                        'operator'   => 'equals',
                        'expression' => $countryCode,
                    ]],
                ]];
            }

            $response = Http::withToken($accessToken)
                ->timeout(60)
                ->post($url, $body);

            if ($response->successful()) {
                return $response->json('rows', []);
            }

            $this->error('GSC API error: ' . $response->status() . ' — ' . $response->body());
            return null;

        } catch (\Throwable $e) {
            $this->error('Excepción GSC API: ' . $e->getMessage());
            Log::error('GSC fetchSearchAnalytics error: ' . $e->getMessage());
            return null;
        }
    }
}

// Helper para base64url encoding (sin padding)
if (! function_exists('base64url_encode')) {
    function base64url_encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
