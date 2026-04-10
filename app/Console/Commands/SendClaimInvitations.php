<?php

namespace App\Console\Commands;

use App\Mail\ClaimInvitation;
use App\Models\Restaurant;
use App\Models\EmailLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SendClaimInvitations extends Command
{
    protected $signature = "restaurants:send-claim-invitations
                            {--limit=150 : Maximum number of emails to send}
                            {--state= : Filter by state code (e.g., TX, CA)}
                            {--city= : Filter by city name}
                            {--dry-run : Show what would be sent without actually sending}
                            {--delay=1 : Delay in seconds between emails to avoid rate limits}";

    protected $description = "Send claim invitation emails to unclaimed restaurants with email addresses";

    public function handle()
    {
        $limit = (int) $this->option("limit");
        $state = $this->option("state");
        $city = $this->option("city");
        $dryRun = $this->option("dry-run");
        $delay = (int) $this->option("delay");

        $this->info("Searching for unclaimed restaurants with emails...");

        $query = Restaurant::query()
            ->where("country", "US")
            ->where("is_claimed", false)
            ->where("status", "approved")
            ->whereNull("claim_invitation_sent_at")
            ->where(function ($q) {
                $q->whereNotNull("owner_email")
                  ->orWhereNotNull("email");
            });

        if ($state) {
            $query->whereHas("state", function ($q) use ($state) {
                $q->where("code", strtoupper($state));
            });
        }

        if ($city) {
            $query->where("city", "like", "%{$city}%");
        }

        $restaurants = $query->with(["state", "media"])
            ->limit($limit)
            ->get();

        if ($restaurants->isEmpty()) {
            $this->warn("No restaurants found matching criteria.");
            return 0;
        }

        $this->info("Found {$restaurants->count()} restaurants to invite.");

        if ($dryRun) {
            $this->warn("DRY RUN - No emails will be sent.");
        }

        $this->newLine();

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        $sent = 0;
        $failed = 0;

        foreach ($restaurants as $restaurant) {
            $email = $restaurant->owner_email ?? $restaurant->email;

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->newLine();
                $this->warn("  Skipping {$restaurant->name}: Invalid email - {$email}");
                $bar->advance();
                continue;
            }

            try {
                if (!$dryRun) {
                    // Capturar el Resend email_id via header X-Resend-Email-ID
                    // que el transport agrega al mensaje Symfony después de enviarlo
                    $resendEmailId = null;
                    $mailable = new ClaimInvitation($restaurant);
                    $mailable->withSymfonyMessage(function (\Symfony\Component\Mime\Email $msg) use (&$resendEmailId) {
                        // Capturar referencia al objeto — el ID se leerá post-send
                        // usando el evento MessageSent
                    });

                    $capturedId = null;
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
                        "category" => "claim_invitation",
                        "to_email" => $email,
                        "to_name" => $restaurant->name,
                        "from_email" => config("mail.from.address"),
                        "from_name" => config("mail.from.name"),
                        "subject" => "{$restaurant->name} — Tu perfil ya está en el directorio de restaurantes mexicanos",
                        "mailable_class" => ClaimInvitation::class,
                        "status" => "sent",
                        "sent_at" => now(),
                        "provider" => "resend",
                        "message_id" => $capturedId,
                        "restaurant_id" => $restaurant->id,
                        "metadata" => json_encode([
                            "restaurant_name" => $restaurant->name,
                            "restaurant_city" => $restaurant->city,
                            "restaurant_state" => $restaurant->state?->code,
                        ]),
                    ]);

                    // Marcar restaurante como invitado
                    $restaurant->update([
                        "claim_invitation_sent_at" => now(),
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
                $this->error("  Failed to send to {$restaurant->name}: {$e->getMessage()}");
                
                // Registrar el fallo también
                if (!$dryRun) {
                    EmailLog::create([
                        "type" => "campaign",
                        "category" => "claim_invitation",
                        "to_email" => $email,
                        "to_name" => $restaurant->name,
                        "from_email" => config("mail.from.address"),
                        "subject" => "{$restaurant->name} — Tu perfil ya está en el directorio de restaurantes mexicanos",
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
            ["Metric", "Count"],
            [
                ["Restaurants found", $restaurants->count()],
                ["Emails sent" . ($dryRun ? " (dry run)" : ""), $sent],
                ["Failed", $failed],
            ]
        );

        if (!$dryRun && $sent > 0) {
            $this->info("Claim invitations sent successfully!");
        }

        return 0;
    }
}
