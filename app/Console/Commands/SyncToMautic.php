<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;

class SyncToMautic extends Command
{
    protected $signature = "restaurants:sync-mautic {--dry-run : Show what would be synced}";
    protected $description = "Sync new restaurant emails to Mautic";

    private $mauticPdo;

    public function handle()
    {
        $dryRun = $this->option("dry-run");
        
        $this->info("Connecting to Mautic database...");
        
        try {
            $this->mauticPdo = new PDO(
                "mysql:host=172.27.0.3;dbname=mautic;charset=utf8mb4",
                "mautic",
                "mautic_s3cur3_2024",
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (\Exception $e) {
            $this->error("Could not connect to Mautic: " . $e->getMessage());
            return 1;
        }

        // Get existing emails in Mautic
        $stmt = $this->mauticPdo->query("SELECT email FROM leads WHERE email IS NOT NULL");
        $existingEmails = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $existingEmails = array_map("strtolower", $existingEmails);
        
        $this->info("Found " . count($existingEmails) . " existing contacts in Mautic");

        // Get restaurants with email not in Mautic
        $restaurants = Restaurant::with("state")
            ->where("status", "approved")
            ->whereNotNull("email")
            ->where("email", "!=", "")
            ->whereNotIn(DB::raw("LOWER(email)"), $existingEmails)
            ->get();

        $this->info("Found " . $restaurants->count() . " new restaurants to sync");

        if ($restaurants->isEmpty()) {
            $this->info("No new contacts to sync.");
            return 0;
        }

        $synced = 0;
        $errors = 0;

        foreach ($restaurants as $restaurant) {
            try {
                $email = strtolower(trim($restaurant->email));
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                if ($dryRun) {
                    $this->line("[DRY] Would sync: {$restaurant->name} - {$email}");
                    $synced++;
                    continue;
                }

                // Build tags
                $tags = ["restaurant", "scraped"];
                if ($restaurant->state) {
                    $tags[] = "state_" . strtolower($restaurant->state->code ?? "");
                }

                $stmt = $this->mauticPdo->prepare("
                    INSERT INTO leads (email, firstname, company, phone, city, state, date_added, date_modified, points)
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW(), 0)
                ");

                $stmt->execute([
                    $email,
                    $restaurant->name,
                    $restaurant->name,
                    $restaurant->phone,
                    $restaurant->city,
                    $restaurant->state->code ?? null
                ]);

                $leadId = $this->mauticPdo->lastInsertId();

                // Add tags
                foreach ($tags as $tag) {
                    $this->addTag($leadId, $tag);
                }

                $synced++;
                
            } catch (\Exception $e) {
                $errors++;
                Log::warning("Mautic sync error", [
                    "restaurant_id" => $restaurant->id,
                    "error" => $e->getMessage()
                ]);
            }
        }

        $this->info("=== Results ===");
        $this->info("Synced: {$synced}");
        $this->info("Errors: {$errors}");

        return 0;
    }

    private function addTag($leadId, $tagName)
    {
        // Get or create tag
        $stmt = $this->mauticPdo->prepare("SELECT id FROM lead_tags WHERE tag = ?");
        $stmt->execute([$tagName]);
        $tagId = $stmt->fetchColumn();

        if (!$tagId) {
            $stmt = $this->mauticPdo->prepare("INSERT INTO lead_tags (tag) VALUES (?)");
            $stmt->execute([$tagName]);
            $tagId = $this->mauticPdo->lastInsertId();
        }

        // Link tag to lead
        try {
            $stmt = $this->mauticPdo->prepare("INSERT IGNORE INTO lead_tags_xref (lead_id, tag_id) VALUES (?, ?)");
            $stmt->execute([$leadId, $tagId]);
        } catch (\Exception $e) {
            // Ignore duplicates
        }
    }
}
