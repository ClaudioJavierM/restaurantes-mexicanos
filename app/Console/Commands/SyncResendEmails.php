<?php

namespace App\Console\Commands;

use App\Models\EmailLog;
use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SyncResendEmails extends Command
{
    protected $signature = "emails:sync-resend 
                           {--full : Sync all emails, not just new ones}
                           {--update-events : Update delivery/open/click events for existing logs}";
                           
    protected $description = "Sync email data from Resend API to email_logs table";

    public function handle()
    {
        $apiKey = config("services.resend.key") ?: env("RESEND_API_KEY");
        
        if (empty($apiKey)) {
            $this->error("RESEND_API_KEY not configured");
            return 1;
        }
        
        $this->info("Fetching emails from Resend API...");
        
        $allEmails = [];
        $hasMore = true;
        $lastId = null;
        $maxPages = 100;
        $page = 0;
        
        $bar = $this->output->createProgressBar(100);
        $bar->start();
        
        while ($hasMore && $page < $maxPages) {
            $params = ["limit" => 100];
            if ($lastId) {
                $params["starting_after"] = $lastId;
            }
            
            $response = Http::withHeaders([
                "Authorization" => "Bearer " . $apiKey,
            ])->timeout(30)->get("https://api.resend.com/emails", $params);
            
            if ($response->successful()) {
                $data = $response->json();
                $emails = $data["data"] ?? [];
                $hasMore = $data["has_more"] ?? false;
                
                if (empty($emails)) {
                    $hasMore = false;
                } else {
                    $allEmails = array_merge($allEmails, $emails);
                    $lastId = end($emails)["id"] ?? null;
                }
            } else {
                $hasMore = false;
                $this->warn("API error: " . $response->status());
            }
            $page++;
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("Found " . count($allEmails) . " emails in Resend");
        
        $synced = 0;
        $updated = 0;
        $skipped = 0;
        
        foreach ($allEmails as $email) {
            $toEmail = $email["to"][0] ?? null;
            $subject = $email["subject"] ?? "";
            $messageId = $email["id"] ?? null;
            $createdAt = $email["created_at"] ?? null;
            $lastEvent = $email["last_event"] ?? "sent";
            
            if (!$toEmail) {
                $skipped++;
                continue;
            }
            
            // Check if this is a claim invitation
            $isClaimInvitation = str_contains($subject, "Reclama tu perfil") || 
                                 str_contains($subject, "claim") ||
                                 str_contains($subject, "Restaurantes Mexicanos");
            
            // Find existing log by message_id or to_email + date
            $existingLog = EmailLog::where("message_id", $messageId)->first();
            
            if (!$existingLog && $createdAt) {
                $existingLog = EmailLog::where("to_email", $toEmail)
                    ->whereDate("sent_at", Carbon::parse($createdAt)->toDateString())
                    ->first();
            }
            
            if ($existingLog) {
                if ($this->option("update-events")) {
                    // Update status and events
                    $this->updateEmailLog($existingLog, $lastEvent, $messageId);
                    $updated++;
                } else {
                    $skipped++;
                }
                continue;
            }
            
            // Create new log
            $restaurant = Restaurant::where("email", $toEmail)
                ->orWhere("owner_email", $toEmail)
                ->first();
            
            $log = EmailLog::create([
                "type" => "campaign",
                "category" => $isClaimInvitation ? "claim_invitation" : "other",
                "to_email" => $toEmail,
                "to_name" => $restaurant?->name,
                "subject" => $subject,
                "status" => $this->mapStatus($lastEvent),
                "sent_at" => $createdAt ? Carbon::parse($createdAt) : now(),
                "message_id" => $messageId,
                "provider" => "resend",
                "restaurant_id" => $restaurant?->id,
            ]);
            
            // Set opened/clicked/bounced timestamps based on last_event
            $this->updateEmailLog($log, $lastEvent, $messageId);
            
            // Also update restaurant claim_invitation_sent_at if it was a claim invitation
            if ($restaurant && $isClaimInvitation && !$restaurant->claim_invitation_sent_at) {
                $restaurant->update(["claim_invitation_sent_at" => $createdAt ? Carbon::parse($createdAt) : now()]);
            }
            
            $synced++;
        }
        
        $this->info("Sync complete:");
        $this->info("  - Synced: $synced new emails");
        $this->info("  - Updated: $updated existing emails");
        $this->info("  - Skipped: $skipped emails");
        
        $this->newLine();
        $this->info("Total in email_logs: " . EmailLog::count());
        
        return 0;
    }
    
    protected function mapStatus(string $lastEvent): string
    {
        return match ($lastEvent) {
            "delivered" => "delivered",
            "opened" => "opened",
            "clicked" => "clicked",
            "bounced" => "bounced",
            "complained" => "complained",
            "delivery_delayed" => "delayed",
            default => "sent",
        };
    }
    
    protected function updateEmailLog(EmailLog $log, string $lastEvent, ?string $messageId): void
    {
        if ($messageId && !$log->message_id) {
            $log->message_id = $messageId;
        }
        
        $log->status = $this->mapStatus($lastEvent);
        
        if (in_array($lastEvent, ["delivered", "opened", "clicked"]) && !$log->delivered_at) {
            $log->delivered_at = $log->sent_at ?? now();
        }
        
        if (in_array($lastEvent, ["opened", "clicked"]) && !$log->opened_at) {
            $log->opened_at = $log->delivered_at ?? $log->sent_at ?? now();
        }
        
        if ($lastEvent === "clicked" && !$log->clicked_at) {
            $log->clicked_at = $log->opened_at ?? $log->sent_at ?? now();
        }
        
        if ($lastEvent === "bounced" && !$log->bounced_at) {
            $log->bounced_at = $log->sent_at ?? now();
        }
        
        $log->save();
    }
}
