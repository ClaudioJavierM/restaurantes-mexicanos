<?php

namespace App\Console\Commands;

use App\Mail\ClaimInvitationMx;
use App\Models\Restaurant;
use App\Models\EmailLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SendClaimInvitationsMx extends Command
{
    protected $signature = "famer:send-claim-invitations-mx
                            {--limit=300 : Maximum number of emails to send}
                            {--dry-run : Show what would be sent without actually sending}
                            {--delay=1 : Delay in seconds between emails to avoid rate limits}";

    protected $description = "Send Spanish claim invitation emails to unclaimed MX restaurants with email addresses";

    public function handle()
    {
        $limit = (int) $this->option("limit");
        $dryRun = $this->option("dry-run");
        $delay = (int) $this->option("delay");

        $this->info("Buscando restaurantes MX sin reclamar con emails...");

        $query = Restaurant::query()
            ->where("country", "MX")
            ->where("is_claimed", false)
            ->where("status", "approved")
            ->whereNull("famer_email_1_sent_at")
            ->whereNotNull("email")
            ->whereNotIn('email', \App\Models\EmailSuppression::pluck('email'))
            ->where(function ($q) {
                $q->whereNull('owner_newsletter')->orWhere('owner_newsletter', true);
            })
            ->where(function ($q) {
                // Excluir emails verificados como inválidos (sin MX, sintaxis rota, bounced)
                $q->whereNull('email_status')
                  ->orWhere('email_status', 'valid');
            });

        $restaurants = $query->with(["state", "media"])
            ->limit($limit)
            ->get();

        if ($restaurants->isEmpty()) {
            $this->warn("No se encontraron restaurantes MX que cumplan los criterios.");
            return 0;
        }

        $this->info("Se encontraron {$restaurants->count()} restaurantes para invitar.");

        if ($dryRun) {
            $this->warn("DRY RUN — No se enviará ningún email.");
        }

        $this->newLine();

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        $sent = 0;
        $failed = 0;

        foreach ($restaurants as $restaurant) {
            $email = $restaurant->email;

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->newLine();
                $this->warn("  Omitiendo {$restaurant->name}: email inválido — {$email}");
                $bar->advance();
                continue;
            }

            try {
                if (!$dryRun) {
                    $capturedId = null;
                    $mailable = new ClaimInvitationMx($restaurant);

                    $listener = \Illuminate\Support\Facades\Event::listen(
                        \Illuminate\Mail\Events\MessageSent::class,
                        function ($event) use (&$capturedId) {
                            $headers = $event->sent->getOriginalMessage()->getHeaders();
                            if ($headers->has('X-Resend-Email-ID')) {
                                $capturedId = $headers->get('X-Resend-Email-ID')->getBody();
                            }
                        }
                    );

                    Mail::to($email)->send($mailable);

                    \Illuminate\Support\Facades\Event::forget(\Illuminate\Mail\Events\MessageSent::class);

                    // Registrar en email_logs con el Resend message_id capturado
                    EmailLog::create([
                        "type" => "campaign",
                        "category" => "claim_invitation_mx",
                        "to_email" => $email,
                        "to_name" => $restaurant->name,
                        "from_email" => config("mail.from.address"),
                        "from_name" => config("mail.from.name"),
                        "subject" => "¿Es suyo {$restaurant->name}? Reclame su perfil gratis",
                        "mailable_class" => ClaimInvitationMx::class,
                        "status" => "sent",
                        "sent_at" => now(),
                        "provider" => "resend",
                        "message_id" => $capturedId,
                        "restaurant_id" => $restaurant->id,
                        "metadata" => json_encode([
                            "restaurant_name" => $restaurant->name,
                            "restaurant_city" => $restaurant->city,
                            "restaurant_state" => $restaurant->state?->code,
                            "country" => "MX",
                        ]),
                    ]);

                    // Marcar restaurante como invitado (usando columna de la campaña MX)
                    $restaurant->update([
                        "famer_email_1_sent_at" => now(),
                    ]);
                }

                $sent++;
                $bar->advance();

                // Delay entre emails para evitar rate limits
                if (!$dryRun && $delay > 0) {
                    sleep($delay);
                }

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  Falló el envío a {$restaurant->name}: {$e->getMessage()}");

                // Registrar el fallo también
                if (!$dryRun) {
                    EmailLog::create([
                        "type" => "campaign",
                        "category" => "claim_invitation_mx",
                        "to_email" => $email,
                        "to_name" => $restaurant->name,
                        "from_email" => config("mail.from.address"),
                        "subject" => "¿Es suyo {$restaurant->name}? Reclame su perfil gratis",
                        "status" => "failed",
                        "error_message" => $e->getMessage(),
                        "provider" => "resend",
                        "restaurant_id" => $restaurant->id,
                    ]);
                }

                $failed++;
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ["Métrica", "Cantidad"],
            [
                ["Restaurantes encontrados", $restaurants->count()],
                ["Emails enviados" . ($dryRun ? " (dry run)" : ""), $sent],
                ["Fallidos", $failed],
            ]
        );

        if (!$dryRun && $sent > 0) {
            $this->info("¡Invitaciones de reclamación MX enviadas exitosamente!");
        }

        return 0;
    }
}
