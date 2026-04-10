<?php

namespace App\Console\Commands;

use App\Mail\FamerIntroduction;
use App\Mail\FamerHowItWorks;
use App\Mail\FamerReminder;
use App\Models\EmailLog;
use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendFamerEmails extends Command
{
    protected $signature = "famer:send-emails 
                            {--email1 : Send Email 1 (Introduction) to new contacts}
                            {--email2 : Send Email 2 (How It Works) to 7-day follow-up}
                            {--email3 : Send Email 3 (Reminder) to final follow-up}
                            {--all : Process all email sequences}
                            {--limit=50 : Maximum emails to send per run}
                            {--dry-run : Show what would be sent without sending}";

    protected $description = "Send FAMER Awards email sequence to restaurants";

    private int $email1Count = 0;
    private int $email2Count = 0;
    private int $email3Count = 0;

    /**
     * Send a mailable and capture the Resend email_id from the X-Resend-Email-ID header.
     * Returns the Resend message ID or null if not captured.
     */
    private function sendAndCaptureId(\Illuminate\Mail\Mailable $mailable, string $email): ?string
    {
        $capturedId = null;

        Event::listen(MessageSent::class, function ($event) use (&$capturedId) {
            $headers = $event->sent->getOriginalMessage()->getHeaders();
            if ($headers->has('X-Resend-Email-ID')) {
                $capturedId = $headers->get('X-Resend-Email-ID')->getBody();
            }
        });

        Mail::to($email)->send($mailable);

        Event::forget(MessageSent::class);

        return $capturedId;
    }

    public function handle(): int
    {
        $limit = (int) $this->option("limit");
        $dryRun = $this->option("dry-run");
        $all = $this->option("all");

        if ($dryRun) {
            $this->warn("DRY RUN - No emails will be sent");
        }

        if ($all || $this->option("email1")) {
            $this->sendEmail1($limit, $dryRun);
        }

        if ($all || $this->option("email2")) {
            $this->sendEmail2($limit, $dryRun);
        }

        if ($all || $this->option("email3")) {
            $this->sendEmail3($limit, $dryRun);
        }

        $this->newLine();
        $this->info("Resumen:");
        $this->line("  Email 1 (Introduction): {$this->email1Count}");
        $this->line("  Email 2 (How It Works): {$this->email2Count}");
        $this->line("  Email 3 (Reminder): {$this->email3Count}");

        return 0;
    }

    private function sendEmail1(int $limit, bool $dryRun): void
    {
        $this->info("Procesando Email 1 (Introduction)...");

        $restaurants = Restaurant::query()
            ->where("country", "US")
            ->where("status", "approved")
            ->where(function ($q) {
                $q->whereNotNull("email")->where("email", "<>", "");
            })
            ->where("is_claimed", false)
            ->whereNull("famer_email_1_sent_at")
            ->whereNotExists(function ($q) {
                $q->from('email_suppressions')->whereColumn('email_suppressions.email', 'restaurants.email');
            })
            ->limit($limit)
            ->get();

        foreach ($restaurants as $restaurant) {
            $email = $restaurant->email;
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            if ($dryRun) {
                $this->line("  [DRY] Would send Email 1 to: {$email} ({$restaurant->name})");
            } else {
                try {
                    $resendId = $this->sendAndCaptureId(new FamerIntroduction($restaurant), $email);
                    $restaurant->update(["famer_email_1_sent_at" => now()]);
                    EmailLog::create([
                        'type'           => 'campaign',
                        'category'       => 'famer_email_1',
                        'to_email'       => $email,
                        'to_name'        => $restaurant->name,
                        'from_email'     => config('mail.from.address'),
                        'from_name'      => config('mail.from.name'),
                        'subject'        => "Bienvenido a FAMER — {$restaurant->name}",
                        'mailable_class' => FamerIntroduction::class,
                        'status'         => 'sent',
                        'sent_at'        => now(),
                        'provider'       => 'resend',
                        'message_id'     => $resendId,
                        'restaurant_id'  => $restaurant->id,
                    ]);
                    $this->line("  Sent Email 1 to: {$email}");
                } catch (\Exception $e) {
                    $this->error("  Failed: {$email} - {$e->getMessage()}");
                    continue;
                }
            }
            $this->email1Count++;
            
            if (!$dryRun) {
                usleep(200000);
            }
        }
    }

    private function sendEmail2(int $limit, bool $dryRun): void
    {
        $this->info("Procesando Email 2 (How It Works)...");

        $tenDaysAgo = Carbon::now()->subDays(10);

        $restaurants = Restaurant::query()
            ->where("country", "US")
            ->where("status", "approved")
            ->where("is_claimed", false)
            ->whereNotNull("famer_email_1_sent_at")
            ->where("famer_email_1_sent_at", "<", $tenDaysAgo)
            ->whereNull("famer_email_2_sent_at")
            ->whereNotExists(function ($q) {
                $q->from('email_suppressions')->whereColumn('email_suppressions.email', 'restaurants.email');
            })
            ->limit($limit)
            ->get();

        foreach ($restaurants as $restaurant) {
            $email = $restaurant->email;
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            if ($dryRun) {
                $this->line("  [DRY] Would send Email 2 to: {$email}");
            } else {
                try {
                    $resendId = $this->sendAndCaptureId(new FamerHowItWorks($restaurant), $email);
                    $restaurant->update(["famer_email_2_sent_at" => now()]);
                    EmailLog::create([
                        'type'           => 'campaign',
                        'category'       => 'famer_email_2',
                        'to_email'       => $email,
                        'to_name'        => $restaurant->name,
                        'from_email'     => config('mail.from.address'),
                        'from_name'      => config('mail.from.name'),
                        'subject'        => "Cómo funciona FAMER — {$restaurant->name}",
                        'mailable_class' => FamerHowItWorks::class,
                        'status'         => 'sent',
                        'sent_at'        => now(),
                        'provider'       => 'resend',
                        'message_id'     => $resendId,
                        'restaurant_id'  => $restaurant->id,
                    ]);
                    $this->line("  Sent Email 2 to: {$email}");
                } catch (\Exception $e) {
                    $this->error("  Failed: {$email} - {$e->getMessage()}");
                    continue;
                }
            }
            $this->email2Count++;

            if (!$dryRun) {
                usleep(200000);
            }
        }
    }

    private function sendEmail3(int $limit, bool $dryRun): void
    {
        $this->info("Procesando Email 3 (Reminder)...");

        $tenDaysAgo = Carbon::now()->subDays(10);

        $restaurants = Restaurant::query()
            ->where("country", "US")
            ->where("status", "approved")
            ->where("is_claimed", false)
            ->whereNotNull("famer_email_2_sent_at")
            ->where("famer_email_2_sent_at", "<", $tenDaysAgo)
            ->whereNull("famer_email_3_sent_at")
            ->whereNotExists(function ($q) {
                $q->from('email_suppressions')->whereColumn('email_suppressions.email', 'restaurants.email');
            })
            ->limit($limit)
            ->get();

        foreach ($restaurants as $restaurant) {
            $email = $restaurant->email;
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            if ($dryRun) {
                $this->line("  [DRY] Would send Email 3 to: {$email}");
            } else {
                try {
                    $resendId = $this->sendAndCaptureId(new FamerReminder($restaurant), $email);
                    $restaurant->update(["famer_email_3_sent_at" => now()]);
                    EmailLog::create([
                        'type'           => 'campaign',
                        'category'       => 'famer_email_3',
                        'to_email'       => $email,
                        'to_name'        => $restaurant->name,
                        'from_email'     => config('mail.from.address'),
                        'from_name'      => config('mail.from.name'),
                        'subject'        => "Última invitación — {$restaurant->name} en FAMER",
                        'mailable_class' => FamerReminder::class,
                        'status'         => 'sent',
                        'sent_at'        => now(),
                        'provider'       => 'resend',
                        'message_id'     => $resendId,
                        'restaurant_id'  => $restaurant->id,
                    ]);
                    $this->line("  Sent Email 3 to: {$email}");
                } catch (\Exception $e) {
                    $this->error("  Failed: {$email} - {$e->getMessage()}");
                    continue;
                }
            }
            $this->email3Count++;

            if (!$dryRun) {
                usleep(200000);
            }
        }
    }
}
